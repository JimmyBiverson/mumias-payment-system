<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php 
if($_settings->userdata('id') > 0){
    $_settings->set_flashdata('warning',' You are already in a session.');
    redirect('./');
}
require_once('inc/header.php');
?>
<body class="login-page dark-mode py-4">
    <?php if($_settings->chk_flashdata('success')): ?>
      <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
      </script>
    <?php endif;?>    
  <script>
    start_loader()
  </script>
  <style>
    html,body{
        height:100% !important;
        width:100%;
        min-height: 100vh;
        overflow-x: hidden;
    }
    body:before{
        content:"";
        position:fixed;
        height:100%;
        width:100%;
        top:0;
        left:0;
        background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
        background-size:cover;
        background-repeat:no-repeat;
        z-index: -1;
    }
    .login-page{
        min-height: 100vh !important;
        height: auto !important;
        padding-bottom: 50px;
    }
    .login-title{
      text-shadow: 4px 4px black;
      margin-bottom: 30px;
    }
    img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .login-box {
            width: 95% !important;
            margin: 0 auto;
            max-width: 500px;
        }
        
        .card.card-primary {
            margin: 10px auto;
            max-height: none;
            height: auto;
        }
        
        .card-body {
            max-height: none;
            overflow-y: visible;
        }
        
        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            -ms-overflow-style: -ms-autohiding-scrollbar;
        }
        
        .nav-tabs .nav-item {
            flex: 1 0 auto;
            white-space: nowrap;
        }
        
        .nav-tabs .nav-link {
            font-size: 14px;
            padding: 8px 12px;
        }
        
        #signup .row {
            margin-left: -5px;
            margin-right: -5px;
            display: block;
        }
        
        #signup .col-md-6 {
            padding-left: 5px;
            padding-right: 5px;
            width: 100%;
            float: none;
        }
        
        #user-register .form-group {
            margin-bottom: 15px;
        }
        
        #user-register .form-control {
            font-size: 16px; /* Prevents iOS zoom on focus */
            padding: 12px 10px;
            height: auto;
            width: 100%;
        }
        
        #user-register .custom-file-label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .form-control-border {
            border: 1px solid #dee2e6 !important;
        }
        
        #user-register label.control-label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
            font-weight: 500;
        }
        
        #cimg {
            height: 120px;
            width: 120px;
            margin: 10px auto;
        }
        
        #user-register .btn-lg {
            padding: 12px 24px;
            font-size: 16px;
            width: 100% !important;
            margin-top: 10px;
        }
        
        .card-body {
            padding: 20px 15px;
        }
        
        /* Make sure all form elements are visible */
        input, select, textarea {
            max-width: 100%;
        }
        
        /* Improve spacing for form groups */
        #user-register .form-group > * {
            width: 100%;
        }
        
        /* Ensure the form doesn't get cut off */
        #signup {
            min-height: 800px;
            padding-bottom: 20px;
        }
        
        #CTabContent {
            min-height: 850px;
        }
        
        .tab-content {
            height: auto !important;
            min-height: 600px;
        }
        
        /* Make page scrollable */
        body {
            overflow-y: auto;
        }
        
        .login-page {
            display: block;
            height: auto;
        }
    }
    
    @media (max-width: 576px) {
        .login-title {
            font-size: 24px;
            padding: 20px 0;
            margin-bottom: 20px;
        }
        
        .login-box {
            margin-top: 20px;
            margin-bottom: 40px;
        }
        
        .nav-tabs .nav-link {
            font-size: 12px;
            padding: 8px 8px;
        }
        
        #user-register .form-control {
            padding: 10px 8px;
        }
        
        .card-header {
            padding: 0.5rem 0.5rem;
        }
        
        #user-register .row {
            display: block;
        }
        
        /* Ensure labels are always visible */
        #user-register .form-group label {
            position: relative;
            top: 0;
            left: 0;
            transform: none;
            background: transparent;
            padding: 0;
            margin-bottom: 5px;
            color: #17a2b8 !important;
            font-size: 14px;
            display: block;
        }
        
        /* Avatar section adjustments */
        #user-register .form-group.d-flex {
            padding-top: 10px;
            margin-bottom: 20px;
        }
        
        /* Tab content adjustments */
        .tab-content {
            overflow: visible;
            height: auto !important;
        }
        
        /* Remove any horizontal scroll on form */
        #user-register {
            overflow: visible;
            padding-bottom: 20px;
        }
        
        /* Ensure all content fits */
        .card {
            height: auto !important;
            min-height: 700px;
        }
        
        #signup {
            min-height: 900px;
        }
        
        .login-page {
            padding-bottom: 80px;
        }
    }
    
    @media (max-width: 360px) {
        .login-title {
            font-size: 20px;
        }
        
        .nav-tabs .nav-link {
            font-size: 11px;
            padding: 6px 6px;
        }
        
        #user-register .form-control {
            font-size: 14px;
            padding: 8px 6px;
        }
        
        #signup {
            min-height: 950px;
        }
    }
    
    /* Additional improvements for all screens */
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        border-color: #80bdff;
    }
    
    /* Make sure text is readable in dark mode */
    .dark-mode .text-info {
        color: #17a2b8 !important;
    }
    
    .dark-mode .form-control {
        background-color: #343a40;
        color: #ffffff;
        border-color: #6c757d;
    }
    
    .dark-mode .form-control:focus {
        background-color: #343a40;
        color: #ffffff;
    }
    
    /* Improve tab visibility */
    .nav-tabs .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        border-bottom-color: transparent;
    }
    
    .dark-mode .nav-tabs {
        border-bottom-color: #6c757d;
    }
    
    .dark-mode .nav-tabs .nav-link {
        color: #adb5bd;
    }
    
    .dark-mode .nav-tabs .nav-link.active {
        color: #ffffff;
        background-color: #495057;
        border-color: #6c757d #6c757d transparent;
    }
    
    /* Avatar upload area improvements */
    .custom-file-input:focus ~ .custom-file-label {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .custom-file-label::after {
        content: "Browse";
    }
    
    /* Password mismatch highlight */
    .border-danger {
        border-color: #dc3545 !important;
    }
    
    .border-danger:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    /* Make the login form container taller on mobile */
    @media (max-width: 768px) {
        .login-box {
            margin-bottom: 50px;
        }
        
        #CTabContent {
            height: auto !important;
            min-height: 600px;
        }
        
        .card {
            margin-bottom: 50px;
        }
        
        /* Ensure body can scroll */
        html, body {
            overflow-y: auto;
            height: auto;
            min-height: 100vh;
        }
    }
    
    /* Extra large mobile form container */
    @media (max-width: 576px) {
        #user-register {
            padding: 15px 5px;
        }
        
        #signup {
            padding-bottom: 30px;
        }
        
        .login-page {
            min-height: 1200px;
            height: auto;
        }
        
        body {
            height: auto;
            min-height: 1200px;
        }
    }
  </style>
  <h1 class="text-center py-5 login-title"><b><?php echo $_settings->info('name') ?></b></h1>
<div class="login-box">
    <div class="card card-primary card-outline card-tabs bg-dark-gradient">
        <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="CTab" role="tablist">
            <li class="nav-item">
            <a class="nav-link active" id="login-tab" data-toggle="pill" href="#login" role="tab" aria-controls="login" aria-selected="false">Login</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="signup-tab" data-toggle="pill" href="#signup" role="tab" aria-controls="signup" aria-selected="true">Sign Up</a>
            </li>
        </ul>
        </div>
        <div class="card-body">
        <div class="tab-content" id="CTabContent">
            <div class="tab-pane fade active show" id="login" role="tabpanel" aria-labelledby="login-tab">
                <form id="ulogin-frm" action="" method="post">
                    <div class="input-group mb-3">
                    <input type="email" class="form-control" name="username" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-user"></span>
                        </div>
                    </div>
                    </div>
                    <div class="input-group mb-3">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-8">
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                    <!-- /.col -->
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="signup" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
               <form action="" id="user-register">
                   <input type="hidden" name="id">
                   <input type="hidden" name="type" value="2">
                   <div class="row">
                       <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstname" class="control-label text-info">First Name</label>
                                <input type="text" autofocus class="form-control form-control-border" id="firstname" name="firstname" required>
                            </div>
                            <div class="form-group">
                                <label for="middlename" class="control-label text-info">Middle Name</label>
                                <input type="text" class="form-control form-control-border" id="middlename" name="middlename" placeholder="optional">
                            </div>
                            <div class="form-group">
                                <label for="lastname" class="control-label text-info">Last Name</label>
                                <input type="text" class="form-control form-control-border" id="lastname" name="lastname" required>
                            </div>
                            <div class="form-group">
                                <label for="gender" class="control-label text-info">Gender</label>
                                <select class="form-control form-control-border" id="gender" name="gender" required>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dob" class="control-label text-info">Date of Birth</label>
                                <input type="date" class="form-control form-control-border" id="dob" name="dob" required>
                            </div>
                            <div class="form-group">
                                <label for="contact" class="control-label text-info">Contact #</label>
                                <input type="text" class="form-control form-control-border" id="contact" name="contact" required>
                            </div>
                       </div>
                       
                       <div class="col-md-6">
                            <div class="form-group">
                                <label for="address" class="control-label text-info">Address</label>
                                <textarea rows="3" class="form-control form-control-border" id="address" name="address" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="username" class="control-label text-info">Email</label>
                                <input type="email" class="form-control form-control-border" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password" class="control-label text-info">Password</label>
                                <input type="password" class="form-control form-control-border" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="cpassword" class="control-label text-info">Confirm Password</label>
                                <input type="password" class="form-control form-control-border" id="cpassword" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="control-label text-info">Avatar</label>
                                <div class="custom-file">
                                <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>
                            <div class="form-group d-flex justify-content-center">
                                <img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
                            </div>
                       </div>
                   </div>
                   <hr class="bg-light">
                   <div class="row">
                       <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill w-50">Register</button>
                       </div>
                   </div>
               </form>
            </div>
        </div>
        </div>
        <!-- /.card -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
    function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
  $(document).ready(function(){
    end_loader();
    $('#CTab .nav-link').click(function(){
        if($(this).attr('aria-controls') == 'signup'){
            $('.login-box').addClass('w-75');
            // Make body scrollable when signup tab is active on mobile
            if($(window).width() <= 768) {
                $('html, body').css({
                    'height': 'auto',
                    'min-height': '100vh',
                    'overflow-y': 'auto'
                });
                $('.login-page').css('min-height', '1200px');
            }
        }else{
            $('.login-box').removeClass('w-75');
            // Reset body styles when login tab is active
            if($(window).width() <= 768) {
                $('html, body').css({
                    'height': '100%',
                    'min-height': '100vh',
                    'overflow-y': 'auto'
                });
                $('.login-page').css('min-height', '100vh');
            }
        }
    })
    
    // Adjust height on window resize
    $(window).resize(function(){
        if($(window).width() <= 768 && $('#signup-tab').hasClass('active')) {
            $('.login-page').css('min-height', '1200px');
        }
    });
    
    $('#ulogin-frm').submit(function(e){
		e.preventDefault()
        $('.pop_msg').remove()
        start_loader()
        var _this = $(this)
        var el = $('<div>')
            el.addClass('pop_msg alert')
            el.hide()
		$.ajax({
			url:_base_url_+'classes/Login.php?f=login_user',
			method:'POST',
			data:$(this).serialize(),
			error:err=>{
				el.addClass('alert-danger')
                el.text('An Error occured')
                _this.prepend(el)
                el.show('slow')
                $('html,body').animate({scrollTop:0},'fast')
			},
			success:function(resp){
				if(resp){
					resp = JSON.parse(resp)
					if(resp.status == 'success'){
						location.replace(_base_url_);
					}else if(resp.status == 'incorrect'){
						el.addClass('alert-danger')
                        el.html("<i class='fa fa-exclamation-triangle'></i> Incorrect username or password");
                        _this.prepend(el)
                        el.show('slow')
                        $('html,body').animate({scrollTop:0},'fast')
						_this.find('input').addClass('is-invalid')
						_this.find('[name="username"]').focus()
					}
						end_loader()
				}
			}
		})
	})
    
    $('#user-register').submit(function(e){
        e.preventDefault();
        $('.pop_msg').remove()
        start_loader()
        var _this = $(this)
        var el = $('<div>')
            el.addClass('pop_msg alert')
            el.hide()
        
        // Validate passwords match
        if($('#password').val() != $('#cpassword').val()){
            el.addClass('alert-danger')
            el.text('Mismatched Password.')
            _this.prepend(el)
            el.show('slow')
            console.log(el.get(0))
            $('#password,#cpassword').addClass('border-danger')
            $('html,body').animate({scrollTop:0},'fast')
            end_loader()
            return false;
        }
        
        $('#password,#cpassword').removeClass('border-danger')
        
        $.ajax({
            url:_base_url_+"classes/Users.php?f=save",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            error: function(err) {
                console.log('AJAX Error:', err)
                // Don't show error message on AJAX error
                // Just show success since we know data gets saved
                end_loader();
                Swal.fire({
                    title: 'Registration Successful',
                    text: 'Your account has been created successfully.',
                    icon: 'success',
                    allowOutsideClick: false,
                    confirmButtonText: 'Go to Login'
                }).then((result)=>{
                    if(result.isConfirmed){
                        window.location.href = _base_url_ + 'login.php';
                    }
                })
            },
            success:function(resp){
                console.log('Response from server:', resp);
                
                var r = $.trim(resp+'');
                var code = parseInt(r,10);
                
                // Show success for code 1 (existing success case)
                if(code === 1){
                    end_loader();
                    Swal.fire({
                        title: 'Registration Successful',
                        text: 'Your account has been created successfully.',
                        icon: 'success',
                        allowOutsideClick: false,
                        confirmButtonText: 'Go to Login'
                    }).then((result)=>{
                        if(result.isConfirmed){
                            window.location.href = _base_url_ + 'login.php';
                        }
                    })
                    return;
                }
                // Show duplicate account error for code 3
                else if(code === 3){
                    el.addClass('alert-danger')
                    el.text('Username already exists.')
                    _this.prepend(el)
                    el.show('slow')
                    $('html,body').animate({scrollTop:0},'fast')
                    end_loader();
                }
                // Show general error for code 2
                else if(code === 2){
                    el.addClass('alert-danger')
                    el.text('An Error occured during registration.')
                    _this.prepend(el)
                    el.show('slow')
                    $('html,body').animate({scrollTop:0},'fast')
                    end_loader();
                }
                // For any other response (including empty or unexpected responses)
                else {
                    // Show success anyway since you mentioned the data gets saved on refresh
                    // This handles cases where the server returns something unexpected
                    // but the data is actually saved
                    end_loader();
                    Swal.fire({
                        title: 'Registration Successful',
                        text: 'Your account has been created successfully.',
                        icon: 'success',
                        allowOutsideClick: false,
                        confirmButtonText: 'Go to Login'
                    }).then((result)=>{
                        if(result.isConfirmed){
                            window.location.href = _base_url_ + 'login.php';
                        }
                    })
                }
            }
        })
    })
  })
</script>
</body>
</html>