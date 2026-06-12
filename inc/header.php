<?php
  require_once('sess_auth.php');
  
?>
<head>
  <style>
    :root{
      --bg-img:url('<?php echo validate_image($_settings->info('cover')) ?>');
    }
  </style>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  	<title><?php echo $_settings->info('title') != false ? $_settings->info('title').' | ' : '' ?><?php echo $_settings->info('name') ?></title>
    <link rel="icon" href="<?php echo validate_image($_settings->info('logo')) ?>" />
    <link rel="manifest" href="<?php echo base_url ?>manifest.php">
    <meta name="theme-color" content="#007bff" />
    <?php if(is_file(base_app.'assets/img/app-icon-192.png')): ?>
      <link rel="apple-touch-icon" href="<?php echo base_url ?>assets/img/app-icon-192.png">
    <?php elseif(is_file(base_app.'assets/img/app-icon-192.svg')): ?>
      <link rel="apple-touch-icon" href="<?php echo base_url ?>assets/img/app-icon-192.svg">
    <?php endif; ?>
    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback"> -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
      <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
   <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/adminlte.css">
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/custom.css">
    <link rel="stylesheet" href="<?php echo base_url ?>assets/css/styles.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.css">
     <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo base_url ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <style type="text/css">/* Chart.js */
      @keyframes chartjs-render-animation{from{opacity:.99}to{opacity:1}}.chartjs-render-monitor{animation:chartjs-render-animation 1ms}.chartjs-size-monitor,.chartjs-size-monitor-expand,.chartjs-size-monitor-shrink{position:absolute;direction:ltr;left:0;top:0;right:0;bottom:0;overflow:hidden;pointer-events:none;visibility:hidden;z-index:-1}.chartjs-size-monitor-expand>div{position:absolute;width:1000000px;height:1000000px;left:0;top:0}.chartjs-size-monitor-shrink>div{position:absolute;width:200%;height:200%;left:0;top:0}
    </style>

     <!-- jQuery -->
    <script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?php echo base_url ?>plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="<?php echo base_url ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <!-- Toastr -->
    <script src="<?php echo base_url ?>plugins/toastr/toastr.min.js"></script>
    <script>
        var _base_url_ = '<?php echo base_url ?>';
    </script>
    <script src="<?php echo base_url ?>dist/js/script.js"></script>
    <script src="<?php echo base_url ?>assets/js/scripts.js"></script>
    <script>
      // Register service worker
      if('serviceWorker' in navigator){
        navigator.serviceWorker.register(_base_url_ + 'sw.js').then(function(){
          console.log('Service Worker registered');
        }).catch(function(err){
          console.error('SW registration failed: ', err);
        });
      }
      // Handle install prompt
      let deferredPrompt;
      window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        var btn = document.getElementById('install-app');
        if(btn) btn.style.display = 'inline-block';
      });

      // Update install button state (show installed / open behavior)
      function updateInstallButtonState(){
        var btn = document.getElementById('install-app');
        if(!btn) return;
        var isStandalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;
        if(isStandalone){
          btn.innerHTML = '<i class="fa fa-check"></i> App Installed';
          btn.classList.remove('btn-success');
          btn.classList.add('btn-secondary');
          // clicking opens the app (navigates to start_url)
          btn.onclick = function(){ window.location.href = _base_url_; };
          // ensure visible on desktop
          btn.style.display = 'inline-block';
        } else {
          // default behavior: ensure visible on desktop sizes (md+ via CSS)
          if(window.innerWidth >= 768){
            // show by default on desktop/tablet
            btn.style.display = 'inline-block';
          }
          // leave click behavior to document click handler which handles deferredPrompt and fallback
        }
      }
      // Run after DOM loads and when appinstalled event fires
      document.addEventListener('DOMContentLoaded', updateInstallButtonState);
      window.addEventListener('appinstalled', function(){
        updateInstallButtonState();
      });
      function hideInstallButton(){
        var btn = document.getElementById('install-app');
        if(btn) btn.style.display = 'none';
      }
      document.addEventListener('click', function(e){
        if(e.target && (e.target.id === 'install-app' || e.target.closest('#install-app'))){
          if(deferredPrompt){
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function(choiceResult){
              if(choiceResult.outcome === 'accepted'){
                console.log('PWA install accepted');
              }
              deferredPrompt = null;
              hideInstallButton();
            });
          } else if(/iphone|ipad|ipod/i.test(navigator.userAgent.toLowerCase())){
            Swal.fire({title:'Install App',html:'Tap the <b>Share</b> button in Safari, then choose <b>Add to Home Screen</b> to install this app.',icon:'info'});
          } else {
            // Fallback for browsers without automatic install prompt: download the APK hosted on the server
            Swal.fire({
              title: 'Download App',
              html: 'Your device will download the APK file. On Android open the file to install it and allow <b>Install Unknown Apps</b> if prompted.',
              icon: 'info',
              showCancelButton: true,
              confirmButtonText: 'Download APK',
              cancelButtonText: 'Cancel'
            }).then((result)=>{
              if(result.isConfirmed){
                window.open(_base_url_ + 'inc/mmspyt.biz.apk', '_blank', 'noopener');
                hideInstallButton();
              }
            });
          }
        }
      });

    // APK download guidance for users clicking the explicit APK button
    $(document).on('click', '#download-apk', function(ev){
      ev.preventDefault();
      var apkUrl = $(this).attr('href');
      Swal.fire({
        title: 'Download & Install App',
        html: 'This will download the APK to your device. <br><br><b>Android:</b> Open the downloaded file to install. You may need to enable <i>Install unknown apps</i>. <br><br><b>iOS:</b> APKs are not supported on iOS; please use the PWA install option.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Download APK'
      }).then((res)=>{
        if(res.isConfirmed){
          // Trigger download
          window.location.href = apkUrl;
        }
      });
    });
    </script>
    <style>
    #main-header{
        position:relative;
        background: rgb(0,0,0)!important;
        background: radial-gradient(circle, rgba(0,0,0,0.48503151260504207) 22%, rgba(0,0,0,0.39539565826330536) 49%, rgba(0,212,255,0) 100%)!important;
    }
    #main-header:before{
        content:"";
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background-image:url(<?php echo base_url.$_settings->info('cover') ?>);
        background-repeat: no-repeat;
        background-size: cover;
        filter: drop-shadow(0px 7px 6px black);
        z-index:-1;
    }

 </style>
 <!-- the updated app -->
  </head>
