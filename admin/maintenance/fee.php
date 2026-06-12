<div class="card card-outline card-primary card-glass">
	<div class="card-header">
		<h3 class="card-title">List of Charges/Fees</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Add New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-hovered table-striped table-responsive-card">
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Amount From</th>
						<th>Amount To</th>
						<th>Charge</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
							$qry = $conn->query("SELECT * from `fee_list` order by `amount_from` asc, `amount_to` asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center" data-label="#"><?php echo $i++; ?></td>
							<td data-label="Date"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td class="text-right" data-label="From"><?php echo number_format($row['amount_from'],2) ?></td>
							<td class="text-right" data-label="To"><?php echo number_format($row['amount_to'],2) ?></td>
							<td class="text-right" data-label="Charge"><?php echo number_format($row['fee'],2) ?></td>
							<td class="text-center" data-label="Status">
								<input type="checkbox" class="toggle_fee" data-id="<?php echo $row['id'] ?>" <?php echo isset($row['status']) && $row['status'] == 1 ? 'checked' : '' ?> />
							</td>
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
			_conf("Are you sure to delete this Fee/Charge permanently?","delete_category",[$(this).attr('data-id')])
		})
		$('#create_new').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Fee/Charge","maintenance/manage_fee.php","mid-large")
		})
		$('.edit_data').click(function(){
			uni_modal("<i class='fa fa-plus'></i> Add New Fee/Charge","maintenance/manage_fee.php?id="+$(this).attr('data-id'),"mid-large")
		})
			$(document).on('change','.toggle_fee',function(){
				var id = $(this).data('id');
				var status = $(this).is(':checked') ? 1 : 0;
				start_loader();
				$.ajax({
					url:_base_url_+"classes/Master_api.php?f=toggle_fee",
					method:"POST",
					data:{id: id, status: status},
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
			})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').DataTable({responsive: true});
	})
	function delete_category($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master_api.php?f=delete_fee",
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
