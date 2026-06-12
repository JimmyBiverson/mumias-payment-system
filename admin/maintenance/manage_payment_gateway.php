<?php
require_once('../../config.php');
if(isset($_GET['id']) && intval($_GET['id']) > 0){
    $gid = intval($_GET['id']);
    $qry = $conn->query("SELECT * FROM payment_gateways where id = '{$gid}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_assoc();
        foreach($res as $k=>$v)
            $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form id="gateway-form">
        <input type="hidden" name="id" value="<?php echo isset($id)? $id : '' ?>">
        <div class="form-group">
            <label class="control-label">Name</label>
            <input name="name" required class="form-control form-control-border" value="<?php echo isset($name)? $name : '' ?>">
        </div>
        <div class="form-group">
            <label class="control-label">Code (identifier)</label>
            <input name="code" required class="form-control form-control-border" value="<?php echo isset($code)? $code : '' ?>">
        </div>
        <div class="form-group">
            <label class="control-label">Type</label>
            <select name="type" class="form-control form-control-border">
                <option value="manual" <?php echo (isset($type) && $type=='manual')? 'selected' : '' ?>>Manual</option>
                <option value="automatic" <?php echo (isset($type) && $type=='automatic')? 'selected' : '' ?>>Automatic</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">Status</label>
            <select name="status" class="form-control form-control-border">
                <option value="1" <?php echo (!isset($status) || $status==1)? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo (isset($status) && $status==0)? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label">Settings (JSON)</label>
            <textarea name="settings" class="form-control form-control-border" rows="4"><?php echo isset($settings)? $settings : '' ?></textarea>
        </div>
        <div class="form-group text-right">
            <button class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
    </form>
</div>
<script>
    $('#gateway-form').submit(function(e){
        e.preventDefault();
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master_api.php?f=save_gateway',
            data: new FormData($(this)[0]),
            cache:false,
            contentType:false,
            processData:false,
            method:'POST',
            type:'POST',
            dataType:'json',
            error:err=>{
                console.log(err);
                alert_toast('An error occured.','error');
                end_loader();
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast(resp.msg || 'An error occured.','error');
                    end_loader();
                }
            }
        })
    })
</script>
