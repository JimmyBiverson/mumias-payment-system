<?php require_once('./config.php') ?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container-fluid">
    <form action="" id="transaction_form">
        <fieldset id="information">
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
                                // populate gateways from admin-controlled table: only active gateways
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
        <fieldset id="pay-field" class="d-none">
            <h1 class="text-center text-info" id="payable_amount">0.00</h1>
            <hr class="border-light">
            <div class="form-group">
                <dl class="row">
                    <dt class='text-info col-4'>Amount to Pay</dt>
                    <dd class="col-8 text-right" id="pay_amount"></dd>
                    <dt class='text-info col-4'>Service Fee</dt>
                    <dd class="col-8 text-right"id="fee"></dd>
                    <input type="hidden" name="fee" value='0'>
                    <input type="hidden" name="payable_amount" value='0'>
                    <input type="hidden" name="payment_code" value=''>
                </dl>
            </div>
            <div class="form-group text-center">
                <span id="paypal-button" ></span>
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
</script>
<script>
// Initialize gateway behavior after modal content is loaded
$(function(){
    // When gateway selection changes, handle manual vs automatic gateways
    $(document).on('change','#gateway_select',function(){
        var _t = $(this).find('option:selected');
        var gtype = _t.data('type') || '';
        var gid = _t.val();
        var gcode = _t.data('code') || '';
        var gsettings = _t.data('settings') || '';
        $('[name="gateway_id"]').val(gid);
        // Hide all gateway-specific areas
        $('#gateway_manual_fields').addClass('d-none');
        $('#paypal-button').closest('.form-group').addClass('d-none');
        if(gtype == 'manual'){
            $('#gateway_manual_fields').removeClass('d-none');
        }else if(gtype == 'automatic'){
            // If paypal, initialize paypal button using settings if provided
            if(gcode == 'paypal'){
                try{
                    var s = {};
                    if(gsettings && gsettings != '') s = JSON.parse(gsettings);
                    var clientId = s.sandbox_client_id || s.client_id || '';
                    if(clientId != ''){
                        // show paypal button container
                        $('#paypal-button').closest('.form-group').removeClass('d-none');
                        // render paypal button (legacy SDK usage) if paypal object exists
                        if(typeof paypal !== 'undefined' && typeof paypal.Button !== 'undefined' && typeof paypal.Button.render === 'function'){
                            try{ $('#paypal-button').empty(); }catch(e){}
                            paypal.Button.render({
                                env: s.env || 'sandbox',
                                client: { sandbox: clientId },
                                commit: true,
                                payment: function(data, actions){
                                    return actions.payment.create({
                                        payment: { transactions:[{ amount:{ total: $('fieldset#pay-field').find('[name="payable_amount"]').val(), currency: s.currency || 'PHP' } }] }
                                    })
                                },
                                onAuthorize: function(data, actions){
                                    return actions.payment.execute().then(function(payment){
                                        var tracking_code = data.paymentID || '';
                                        $('fieldset#pay-field').find('[name="payment_code"]').val(tracking_code);
                                        $('#transaction_form').submit();
                                    })
                                },
                                onError: function(err){ console.error('paypal err',err); alert('Payment Error.'); }
                            }, '#paypal-button');
                        }
                    }
                }catch(e){console.log('paypal init err',e)}
            }
        }
    })
    $('#uni_modal .select2').select2({
        placeholder:"Please Select Here",
        dropdownParent: $("#uni_modal")
    })
    $('#next').click(function(){
        var check = new Promise((resolve,reject)=>{
            $('fieldset#information').find('input,select').each(function(){
                if($(this).val() == ''){
                    alert_toast(" All fields are required.","warning")
                    reject();
                }
            })
            resolve()
        })
        check.then(function(){

            $('#next').addClass('d-none')
            $("#back").removeClass('d-none')
            $("fieldset#information").addClass('d-none')
            $("fieldset#pay-field").removeClass('d-none')
        })

    })
    $('#back').click(function(){
        $(this).addClass('d-none')
        $("#next").removeClass('d-none')
        $("fieldset#information").removeClass('d-none')
        $("fieldset#pay-field").addClass('d-none')
    })
    $('#amount_to_pay').on('input',function(){
        var amount = $(this).val() > 0 ? $(this).val() :0;
        $.ajax({
            url:_base_url_+"classes/Master_api.php?f=get_fee",
            method:'POST',
            data:{amount : amount },
            dataType:'json',
            error:err=>{
                console.log(err)
                start_loader()
                alert("An error occured. Try to refresh the page");
            },
            success:function(resp){
                if(resp.status == 'success'){
                    $('#pay_amount').text(parseFloat(amount).toLocaleString('en-US'))
                    $('#fee').text(parseFloat(resp.fee).toLocaleString('en-US'))
                    $('[name="fee"]').val(parseFloat(resp.fee))
                    $('#payable_amount').text(parseFloat(resp.payable).toLocaleString('en-US'))
                    $('[name="payable_amount"]').val(parseFloat(resp.payable))
                }else{
                    // no active fee found; ensure no fee is charged
                    $('#pay_amount').text(parseFloat(amount).toLocaleString('en-US'))
                    $('#fee').text('0.00')
                    $('[name="fee"]').val(0)
                    $('#payable_amount').text(parseFloat(amount).toLocaleString('en-US'))
                    $('[name="payable_amount"]').val(parseFloat(amount))
                }
            }
        })
    })
    $('#transaction_form').submit(function(e){
        e.preventDefault();
        var _this = $(this)
        $('.err-msg').remove();
        // Ensure gateway_id is set from select if not already
        try{
            var gid = $('[name="gateway_id"]').val();
            if(!gid || gid == ''){
                var sel = $('#gateway_select');
                if(sel.length) gid = sel.val();
                if(gid) $('[name="gateway_id"]').val(gid);
            }
        }catch(e){}

        // Validate manual gateway requires payment_code
        try{
            var selOpt = $('#gateway_select').find('option:selected');
            var stype = selOpt.data('type') || '';
            if(stype == 'manual'){
                var pcode = $('[name="payment_code"]').val() || $('#manual_reference').val() || '';
                if($.trim(pcode) == ''){
                    var el = $('<div>').addClass('alert alert-danger err-msg').text('Payment reference is required for the selected manual gateway.');
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body,.modal").animate({ scrollTop: 0 }, "fast");
                    return false;
                }else{
                    $('[name="payment_code"]').val(pcode);
                }
            }
        }catch(e){}

        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master_api.php?f=save_transaction",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error:err=>{
                console.log(err)
                var msg = 'An error occured';
                try{ if(err && err.responseText) msg = err.responseText; }catch(e){}
                alert_toast(msg,'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp =='object' && resp.status == 'success'){
                    location.reload();
                }else if(resp.status == 'failed' && !!resp.msg){
                    var el = $('<div>')
                        el.addClass("alert alert-danger err-msg").text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        $("html, body,.modal").animate({ scrollTop: 0 }, "fast");
                        end_loader()
                }else{
                    alert_toast("An error occured",'error');
                    end_loader();
                    console.log(resp)
                }
            }
        })
    })
    // handle manual send button
    $(document).on('click','#send_manual',function(){
        var ref = $('#manual_reference').val();
        if($.trim(ref) == ''){
            alert_toast('Please enter reference or mobile number','warning');
            return;
        }
        $('fieldset#pay-field').find('[name="payment_code"]').val(ref);
        $('#transaction_form').submit();
    })
})
</script>