<!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
        <!-- Brand Logo -->
        <a href="<?php echo base_url ?>admin" class="brand-link bg-primary text-sm">
        <img src="<?php echo validate_image($_settings->info('logo'))?>" alt="Store Logo" class="brand-image img-circle elevation-3 bg-black" style="width: 1.8rem;height: 1.8rem;max-height: unset">
        <span class="brand-text font-weight-light"><?php echo $_settings->info('short_name') ?></span>
        </a>
        <!-- Sidebar -->
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="./" class="nav-link nav-home">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=transaction" class="nav-link nav-transaction">
                            <i class="nav-icon fas fa-th-list"></i>
                            <p>Transaction</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=clients" class="nav-link nav-clients">
                            <i class="nav-icon fas fa-user-friends"></i>
                            <p>Clients</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=reports" class="nav-link nav-reports">
                            <i class="nav-icon fas fa-file"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                    <?php if($_settings->userdata('type') == 1): ?>
                    <li class="nav-header">Maintenance</li>
                    <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=maintenance/company" class="nav-link nav-maintenance_company">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Company List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=maintenance/fee" class="nav-link nav-maintenance_fee">
                            <i class="nav-icon fas fa-table"></i>
                            <p>Fee Table</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=maintenance/payment_gateways" class="nav-link nav-maintenance_payment_gateways">
                            <i class="nav-icon fas fa-credit-card"></i>
                            <p>Payment Gateways</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user_list">
                            <i class="nav-icon fas fa-users"></i>
                            <p>User List</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo base_url ?>admin/?page=system_info" class="nav-link nav-system_info">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Settings</p>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <!-- /.sidebar -->
      </aside>
      <script>
        var page;
    $(document).ready(function(){
      page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
      page = page.replace(/\//gi,'_');

      if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
        if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
          $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
        }
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

      }
      
		$('#receive-nav').click(function(){
      $('#uni_modal').on('shown.bs.modal',function(){
        $('#find-transaction [name="tracking_code"]').focus();
      })
			uni_modal("Enter Tracking Number","transaction/find_transaction.php");
		})
    })
  </script>
