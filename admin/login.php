<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page  dark-mode">
  <script>
    start_loader()
  </script>
  <style>
    body{
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size:cover;
      background-repeat:no-repeat;
    }
  </style>
  <h1 class="text-center py-5 login-title"><b><?php echo $_settings->info('name') ?></b></h1>
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary glass-card">
    <div class="card-header text-center">
      <a href="./" class="h1"><b>Login</b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="login-frm" action="" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo hash('sha256', session_id() . '::admin_login'); ?>">
        <div class="input-group mb-3">
          <input type="text" class="form-control" autofocus name="username" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <a href="<?php echo base_url ?>">Go to Website</a>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <!-- /.social-auth-links -->

      <!-- <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p> -->
      
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
    $('#login-frm').submit(function(e){
      e.preventDefault();
      start_loader();
      if($('.err_msg').length > 0) $('.err_msg').remove();
      $.ajax({
        url: _base_url_ + 'classes/Login.php?f=login',
        method: 'POST',
        data: $(this).serialize(),
        error: function(err) {
          end_loader();
          var msg = 'An error occurred';
          try { if(err && err.responseText) msg = err.responseText; } catch(e) {}
          var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> " + msg + "</div>";
          $('#login-frm').prepend(_msg);
        },
        success: function(resp) {
          if(resp) {
            resp = JSON.parse(resp);
            if(resp.status == 'success'){
              location.replace(_base_url_ + 'admin');
            } else if(resp.status == 'incorrect'){
              var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> Incorrect username or password</div>";
              $('#login-frm').prepend(_msg);
              $('#login-frm').find('input').addClass('is-invalid');
              $('[name="username"]').focus();
            } else if(resp.status == 'failed' && resp.msg) {
              var _msg = "<div class='alert alert-danger text-white err_msg'><i class='fa fa-exclamation-triangle'></i> " + resp.msg + "</div>";
              $('#login-frm').prepend(_msg);
            }
          }
          end_loader();
        }
      });
    });
  });
</script>
</body>
</html>
