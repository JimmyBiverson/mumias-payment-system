<div class="card card-outline card-primary card-glass">
    <div class="card-header">
        <h3 class="card-title">List of Payment Gateways</h3>
        <div class="card-tools">
            <a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
        <div class="container-fluid">
            <table class="table table-bordered table-striped table-responsive-card">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Created</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $qry = $conn->query("SELECT * from `payment_gateways` order by `date_created` desc ");
                    while($row = $qry->fetch_assoc()):
                    ?>
                        <tr>
                            <td class="text-center" data-label="#"><?php echo $i++; ?></td>
                            <td data-label="Date"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                            <td data-label="Name"><?php echo $row['name'] ?></td>
                            <td data-label="Code"><?php echo $row['code'] ?></td>
                            <td data-label="Type"><?php echo $row['type'] ?></td>
                            <td align="center" data-label="Action">
                                <a class="btn btn-sm btn-glass edit_data mr-1" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="Edit"><i class="fa fa-edit text-primary"></i></a>
                                <a class="btn btn-sm btn-glass delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="Delete"><i class="fa fa-trash text-danger"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this gateway permanently?","delete_gateway",[$(this).attr('data-id')])
        })
        $('#create_new').click(function(){
            uni_modal("<i class='fa fa-plus'></i> Add New Gateway","maintenance/manage_payment_gateway.php","mid-large")
        })
        $('.edit_data').click(function(){
            uni_modal("<i class='fa fa-edit'></i> Edit Gateway","maintenance/manage_payment_gateway.php?id="+$(this).attr('data-id'),"mid-large")
        })
        $('.table td,.table th').addClass('py-1 px-2 align-middle')
        $('.table').DataTable({responsive: true});
    })
    function delete_gateway($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master_api.php?f=delete_gateway",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occured.",'error');
                    end_loader();
                }
            }
        })
    }
</script>
