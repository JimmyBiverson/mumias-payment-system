<?php require_once('./config.php') ?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
    #paypal-loading {
        padding: 20px;
        text-align: center;
    }
    #paypal-loading .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #17a2b8;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        margin: 0 auto 10px;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    #paypal-error {
        color: #dc3545;
        text-align: center;
        padding: 15px;
    }
</style>
<div class="container-fluid fade-in">
    <form action="" id="transaction_form">
        <input type="hidden" name="csrf_token" value="<?php echo hash('sha256', session_id() . '::payment_form'); ?>">
        <fieldset id="information" class="fade-in-up">
            <legend class="text-info">Payment Information</legend>
            <div class="form-group">
                <label for="company_id" class="control-label text-info">Company</label>
                <select name="company_id" id="company_id" class="form-control form-control-border select2" required>
                    <option value="" disabled selected></option>
                    <?php
                        $company = $conn->query("SELECT * FROM `company_list` where status = 1 order by `name` asc");
                        while($row = $company->fetch_assoc()):
                    ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="account_name" class="control-label text-info">Account Name</label>
                <input name="account_name" id="account_name" class="form-control form-control-border" required/>
            </div>
            <div class="form-group">
                <label for="account_number" class="control-label text-info">Account Number</label>
                <input name="account_number" id="account_number" class="form-control form-control-border" required/>
            </div>
            <div class="form-group">
                <label for="amount_to_pay" class="control-label text-info">Amount to Pay</label>
                <input name="amount_to_pay" pattern="[0-9.]+" id="amount_to_pay" class="form-control form-control-border text-right" required/>
            </div>
            <div class="form-group">
                <label for="gateway_select" class="control-label text-info">Payment Gateway</label>
                <select id="gateway_select" class="form-control form-control-border" required>
                    <option value="" disabled selected>Select gateway</option>
                    <?php
                        $gq = $conn->query("SELECT * FROM `payment_gateways` WHERE `status` = 1 ORDER BY `name` ASC");
                        while($gr = $gq->fetch_assoc()):
                            $gsettings = htmlspecialchars($gr['settings'], ENT_QUOTES);
                    ?>
                        <option value="<?php echo $gr['id'] ?>" data-code="<?php echo $gr['code'] ?>" data-type="<?php echo $gr['type'] ?>" data-settings="<?php echo $gsettings ?>"><?php echo $gr['name'] ?> (<?php echo $gr['type'] ?>)</option>
                    <?php endwhile; ?>
                </select>
                <input type="hidden" name="gateway_id" value="">
            </div>
        </fieldset>
        <fieldset id="pay-field" class="d-none glass-card p-3 fade-in-up">
            <h1 class="text-center text-info" id="payable_amount">0.00</h1>
            <hr class="border-light">
            <div class="form-group">
                <dl class="row">
                    <dt class='text-info col-4'>Amount to Pay</dt>
                    <dd class="col-8 text-right" id="pay_amount"></dd>
                    <dt class='text-info col-4'>Service Fee</dt>
                    <dd class="col-8 text-right" id="fee"></dd>
                    <input type="hidden" name="fee" value='0'>
                    <input type="hidden" name="payable_amount" value='0'>
                    <input type="hidden" name="payment_code" value=''>
                </dl>
            </div>
            <div class="form-group text-center">
                <div id="paypal-loading" class="d-none">
                    <div class="spinner"></div>
                    <p class="text-info">Loading PayPal...</p>
                </div>
                <div id="paypal-error" class="d-none">
                    <p>PayPal failed to load.</p>
                    <button type="button" class="btn btn-sm btn-warning" id="retry-paypal">Retry</button>
                </div>
                <span id="paypal-button"></span>
            </div>
            <div id="gateway_manual_fields" class="d-none">
                <div class="form-group">
                    <label class="control-label text-info">Transaction Reference / Mobile #</label>
                    <input type="text" id="manual_reference" class="form-control form-control-border" placeholder="Enter reference or mobile number" />
                </div>
                <div class="form-group text-center">
                    <button type="button" id="send_manual" class="btn btn-success">Send</button>
                </div>
            </div>
        </fieldset>
        <div class="form-group">
            <div class="col-12">
                <div class="d-flex justify-content-end align-items-center">
                    <button class="btn btn-primary btn-flat mr-2 d-none" type="button" id="back">Back</button>
                    <button class="btn btn-primary btn-flat mr-2" type="button" id="next">Next</button>
                    <button class="btn btn-light btn-flat" type="button" id="cancel" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
var paypalSdkLoaded = false;
var paypalSdkLoading = false;
var pendingPaypalRender = false;

function loadPaypalSdk(clientId, env) {
    if (paypalSdkLoaded) {
        renderPaypalButton(clientId, env);
        return;
    }
    if (paypalSdkLoading) {
        pendingPaypalRender = { clientId: clientId, env: env };
        return;
    }
    paypalSdkLoading = true;
    $('#paypal-loading').removeClass('d-none');
    $('#paypal-error').addClass('d-none');
    $('#paypal-button').empty();

    var script = document.createElement('script');
    script.src = 'https://www.paypalobjects.com/api/checkout.js';
    script.async = true;
    script.onload = function() {
        paypalSdkLoaded = true;
        paypalSdkLoading = false;
        $('#paypal-loading').addClass('d-none');
        renderPaypalButton(clientId, env);
        if (pendingPaypalRender) {
            renderPaypalButton(pendingPaypalRender.clientId, pendingPaypalRender.env);
            pendingPaypalRender = null;
        }
    };
    script.onerror = function() {
        paypalSdkLoading = false;
        $('#paypal-loading').addClass('d-none');
        $('#paypal-error').removeClass('d-none');
    };
    document.head.appendChild(script);
}

function renderPaypalButton(clientId, env) {
    if (typeof paypal === 'undefined' || typeof paypal.Button === 'undefined') {
        $('#paypal-loading').addClass('d-none');
        $('#paypal-error').removeClass('d-none');
        return;
    }
    try {
        $('#paypal-button').empty();
        var payable = $('fieldset#pay-field').find('[name="payable_amount"]').val() || '0';
        paypal.Button.render({
            env: env || 'sandbox',
            client: { sandbox: clientId },
            commit: true,
            payment: function(data, actions) {
                return actions.payment.create({
                    payment: { transactions: [{ amount: { total: payable, currency: 'PHP' } }] }
                });
            },
            onAuthorize: function(data, actions) {
                return actions.payment.execute().then(function(payment) {
                    $('fieldset#pay-field').find('[name="payment_code"]').val(data.paymentID || '');
                    $('#transaction_form').submit();
                });
            },
            onCancel: function() {
                alert_toast('Payment was cancelled.', 'warning');
            },
            onError: function(err) {
                console.error('paypal err', err);
                alert_toast('PayPal encountered an error. Please try again.', 'error');
            }
        }, '#paypal-button');
    } catch (e) {
        console.log('paypal render err', e);
        $('#paypal-error').removeClass('d-none');
    }
}

$(function(){
    $(document).on('change', '#gateway_select', function() {
        var _t = $(this).find('option:selected');
        var gtype = _t.data('type') || '';
        var gid = _t.val();
        var gcode = _t.data('code') || '';
        var gsettings = _t.data('settings') || '';
        $('[name="gateway_id"]').val(gid);
        $('#gateway_manual_fields').addClass('d-none');
        $('#paypal-button').closest('.form-group').addClass('d-none');
        $('#paypal-loading').addClass('d-none');
        $('#paypal-error').addClass('d-none');

        if (gtype == 'manual') {
            $('#gateway_manual_fields').removeClass('d-none');
        } else if (gtype == 'automatic' && gcode == 'paypal') {
            try {
                var s = {};
                if (gsettings && gsettings != '') s = JSON.parse(gsettings);
                var clientId = s.sandbox_client_id || s.client_id || '';
                if (clientId != '') {
                    $('#paypal-button').closest('.form-group').removeClass('d-none');
                    loadPaypalSdk(clientId, s.env || 'sandbox');
                }
            } catch (e) {
                console.log('paypal init err', e);
            }
        }
    });

    $(document).on('click', '#retry-paypal', function() {
        var sel = $('#gateway_select').find('option:selected');
        var gsettings = sel.data('settings') || '';
        var gcode = sel.data('code') || '';
        if (gcode == 'paypal') {
            try {
                var s = JSON.parse(gsettings);
                var clientId = s.sandbox_client_id || s.client_id || '';
                if (clientId) {
                    paypalSdkLoaded = false;
                    paypalSdkLoading = false;
                    loadPaypalSdk(clientId, s.env || 'sandbox');
                }
            } catch (e) {}
        }
    });

    $('#uni_modal .select2').select2({
        placeholder: "Please Select Here",
        dropdownParent: $("#uni_modal")
    });

    $('#next').click(function() {
        var valid = true;
        $('fieldset#information').find('input,select').each(function() {
            if ($(this).val() == '') {
                alert_toast("All fields are required.", "warning");
                valid = false;
                return false;
            }
        });
        if (!valid) return;
        var amount = $('#amount_to_pay').val();
        if (!amount || parseFloat(amount) <= 0) {
            alert_toast("Please enter a valid amount.", "warning");
            return;
        }
        // Fetch fee before moving to review
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master_api.php?f=get_fee",
            method: 'POST',
            data: { amount: amount },
            dataType: 'json',
            error: function(err) {
                end_loader();
                alert_toast("Could not calculate fee. Check your connection.", "error");
            },
            success: function(resp) {
                end_loader();
                var fee = 0;
                var payable = parseFloat(amount);
                if (resp && resp.status == 'success') {
                    fee = parseFloat(resp.fee) || 0;
                    payable = payable + fee;
                }
                $('#pay_amount').text(parseFloat(amount).toLocaleString('en-US'));
                $('#fee').text(fee.toLocaleString('en-US'));
                $('[name="fee"]').val(fee);
                $('#payable_amount').text(payable.toLocaleString('en-US'));
                $('[name="payable_amount"]').val(payable);
                $('#next').addClass('d-none');
                $("#back").removeClass('d-none');
                $("fieldset#information").addClass('d-none');
                $("fieldset#pay-field").removeClass('d-none');
            }
        });
    });

    $('#back').click(function() {
        $(this).addClass('d-none');
        $("#next").removeClass('d-none');
        $("fieldset#information").removeClass('d-none');
        $("fieldset#pay-field").addClass('d-none');
    });

    $('#transaction_form').submit(function(e) {
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        try {
            var gid = $('[name="gateway_id"]').val();
            if (!gid || gid == '') {
                var sel = $('#gateway_select');
                if (sel.length) gid = sel.val();
                if (gid) $('[name="gateway_id"]').val(gid);
            }
        } catch (e) {}
        try {
            var selOpt = $('#gateway_select').find('option:selected');
            var stype = selOpt.data('type') || '';
            if (stype == 'manual') {
                var pcode = $('[name="payment_code"]').val() || $('#manual_reference').val() || '';
                if ($.trim(pcode) == '') {
                    var el = $('<div>').addClass('alert alert-danger err-msg').text('Payment reference is required for the selected manual gateway.');
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body,.modal").animate({ scrollTop: 0 }, "fast");
                    return false;
                } else {
                    $('[name="payment_code"]').val(pcode);
                }
            }
        } catch (e) {}

        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master_api.php?f=save_transaction",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error: function(err) {
                end_loader();
                var msg = 'An error occurred';
                try { if (err && err.responseText) msg = err.responseText; } catch (e) {}
                alert_toast(msg, 'error');
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    end_loader();
                    alert_toast('Payment successful!', 'success');
                    $('#uni_modal').modal('hide');
                    setTimeout(function() { location.reload(); }, 1500);
                } else if (resp.status == 'failed' && !!resp.msg) {
                    var el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body,.modal").animate({ scrollTop: 0 }, "fast");
                    end_loader();
                } else {
                    alert_toast("An error occurred", 'error');
                    end_loader();
                }
            }
        });
    });

    $(document).on('click', '#send_manual', function() {
        var ref = $('#manual_reference').val();
        if ($.trim(ref) == '') {
            alert_toast('Please enter reference or mobile number', 'warning');
            return;
        }
        $('fieldset#pay-field').find('[name="payment_code"]').val(ref);
        $('#transaction_form').submit();
    });
});
</script>
