<div class="card card-outline card-primary card-glass">
	<div class="card-header">
		<h3 class="card-title">List of Transaction</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="table-responsive">
			<table class="table table-bordered table-stripped table-responsive-card">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date Created</th>
                            <th>Transaction Code</th>
                            <th>Client</th>
                            <th>Gateway</th>
                            <th>Information</th>
                            <th>Payable Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $qry = $conn->query("SELECT t.*,c.name as company,concat(lastname,', ', firstname,' ',middlename) as user, pg.name as gateway FROM `transaction_list` t 
                            INNER JOIN company_list c on t.company_id = c.id 
                            LEFT JOIN payment_gateways pg on pg.id = t.gateway_id 
                            INNER JOIN users u on t.user_id = u.id 
                            order by unix_timestamp(t.`date_created`) desc ");
                        while($row = $qry->fetch_assoc()):
                        ?>
                            <tr>
                                <td class="text-center" data-label="#"><?php echo $i++; ?></td>
                                <td data-label="Date"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                                <td data-label="Code"><?php echo $row['tracking_code'] ?></td>
                                <td data-label="Client"><?php echo $row['user'] ?></td>
                                <td data-label="Gateway"><?php echo isset($row['gateway']) ? $row['gateway'] : 'N/A' ?></td>
                                <td data-label="Details">
                                    <dl class="lh-1 mb-0">
                                        <dt class="my-0 py-0 text-info" style="font-size:0.8rem">Account Name:</dt>
                                        <dd class="my-0 py-0 pl-3 mb-0" style="font-size:0.85rem"><?php echo $row['account_name'] ?></dd>
                                        <dt class="my-0 py-0 text-info" style="font-size:0.8rem">Account #:</dt>
                                        <dd class="my-0 py-0 pl-3 mb-0" style="font-size:0.85rem"><?php echo $row['account_number'] ?></dd>
                                    </dl>
                                </td>
                                <td class="text-right" data-label="Amount"><?php echo number_format($row['payable_amount'],2) ?></td>
                                <td class="text-center" data-label="Status">
                                    <?php if(isset($row['status']) && $row['status'] == 'pending'): ?>
                                        <span class="badge badge-glass badge-warning"><i class="fa fa-clock"></i> Pending</span>
                                    <?php elseif(isset($row['status']) && $row['status'] == 'failed'): ?>
                                        <span class="badge badge-glass badge-danger"><i class="fa fa-times"></i> Failed</span>
                                    <?php else: ?>
                                        <span class="badge badge-glass badge-success"><i class="fa fa-check"></i> Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td align="center" data-label="Action">
                                    <a class="btn btn-sm btn-glass view_details" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="View"><i class="fa fa-eye"></i></a>
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
			_conf("Are you sure to delete this transaction permanently?","delete_transaction",[$(this).attr('data-id')])
		})
		$('.view_details').click(function(){
			uni_modal("Payment Details","transaction/view_payment.php?id="+$(this).attr('data-id'),'mid-large')
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').DataTable({responsive: true});
	})
	function delete_transaction($id){
		start_loader();
		$.ajax({
                url:_base_url_+"classes/Master_api.php?f=delete_transaction",
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

