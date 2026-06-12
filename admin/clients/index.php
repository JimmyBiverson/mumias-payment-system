<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .img-avatar{
        width:45px;
        height:45px;
        object-fit:cover;
        object-position:center center;
        border-radius:100%;
    }
</style>
<div class="card card-outline card-primary card-glass">
	<div class="card-header">
		<h3 class="card-title">List of System Users</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-responsive-card">
				<thead>
					<tr>
						<th>#</th>
						<th>Date Registered</th>
						<th>Avatar</th>
						<th>Name</th>
						<th>Email</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$qry = $conn->query("SELECT *,concat(lastname,', ',firstname,' ', middlename) as name from `users` where  `type` =2 order by concat(lastname,', ',firstname,' ', middlename) asc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center" data-label="#"><?php echo $i++; ?></td>
							<td data-label="Date Registered"><?php echo date("Y-m-d H:i",strtotime($row['date_added'])) ?></td>
							<td class="text-center" data-label="Avatar"><img src="<?php echo validate_image($row['avatar']) ?>" class="img-avatar img-thumbnail p-0 border-2" alt="user_avatar"></td>
							<td data-label="Name"><?php echo ucwords($row['name']) ?></td>
							<td data-label="Email"><p class="m-0 truncate-1"><?php echo $row['username'] ?></p></td>
							<td align="center" data-label="Action">
                                <a class="btn btn-sm btn-glass view_details mr-1" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" title="View"><i class="fa fa-eye"></i></a>
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
			_conf("Are you sure to delete this User permanently?","delete_user",[$(this).attr('data-id')])
		})
		$('.view_details').click(function(){
			uni_modal("Client Details","clients/view_details.php?id="+$(this).attr('data-id'))
		})
		$('.table td,.table th').addClass('py-1 px-2 align-middle')
		$('.table').DataTable({responsive: true});
	})
	function delete_user($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Users.php?f=delete",
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
					branch.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>
