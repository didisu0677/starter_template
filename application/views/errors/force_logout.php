<!DOCTYPE html>
<html lang="en">
<head>
<meta name="appname" content="<?php echo setting('title'); ?>">
<meta name="description" content="<?php echo setting('deskripsi'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
<meta name="push-userid" content="">
<title><?php echo setting('title') . ' &raquo; ' . lang('keluar'); ?></title>
<link rel="shortcut icon" href="<?php echo base_url(dir_upload('setting').setting('favicon')); ?>" />
<?php
Asset::css('bootstrap.min.css', true);
Asset::css('bootstrap.color.min.css', true);
Asset::css('roboto.css', true);
Asset::css('fontawesome.css', true);
Asset::css('select2.min.css', true);
Asset::css('daterangepicker.css', true);
Asset::css('style.css', true);
if(setting('ukuran_tampilan') == 'small') {
	Asset::css('small-style.css', true);
}
if(setting('custom_template') && file_exists(FCPATH . 'assets/css/template.css')) {
	Asset::css('template.css', true);
}
echo Asset::render();
?>
</head>
<body class="bg-grey <?php echo ' '.setting('warna_dropdown'); ?>">
	<?php flash_message();  ?>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col col-sm-8 mt-2 mb-2">
				<div class="card">
					<div class="card-body p-4">
						<div class="text-center pt-2 pb-2 mb-4">
							<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="<?php setting('title'); ?>" width="200">
						</div>
						<h5 class="text-center mb-4"><?php echo lang('akun_ini_sedang_digunakan_oleh_alamat_ip').' <strong>'.$ip_address.'</strong>'; ?></h5>
						<div class="text-center">
							<a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-success"><?php echo lang('keluar'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
var base_url = '<?php echo base_url(); ?>';
var user_key = '<?php echo user('key_id'); ?>';
var ws_server = '<?php if(setting('websocket') && setting('ws_server')) echo base64_encode(setting('ws_server')); ?>';
</script>
<?php
Asset::js('jquery.min.js', true);
Asset::js('jquery.mask.min.js', true);
Asset::js('popper.min.js', true);
Asset::js('moment.min.js', true);
Asset::js('hashids.min.js', true);
Asset::js('push.min.js', true);
Asset::js('other.bundle.js', true);
Asset::js('bootstrap.min.js', true);
Asset::js('sweetalert.min.js', true);
Asset::js('select2.min.js', true);
Asset::js('daterangepicker.js', true);
Asset::js('_'.setting('language').'.js', true);
Asset::js('app.fn.js', true);
Asset::js('app.js', true);
if(setting('websocket')) Asset::js('realtime.js', true);
echo Asset::render();
?>
<?php if(setting('realtime_notification')) { ?>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
	var OneSignal = window.OneSignal || [];
	OneSignal.push(function() {
		OneSignal.init({
			appId: "<?php echo setting('onesignal_app_id'); ?>",
			autoRegister: true,
			notifyButton: {
				enable: false,
			},
			welcomeNotification: {
				title: "Terima Kasih Telah Mengaktifkan Notifikasi",
				message: "Anda akan mendapatkan pemberitahuan secara realtime ketika anda login."
			}
		});
		OneSignal.getUserId(function(userId) {
			$('form').prepend('<input type="hidden" name="notification_id" value="'+userId+'">');
		});
	});
</script>
<?php } ?>
</body>
</html>
