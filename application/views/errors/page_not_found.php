<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo setting('title') . ' &raquo; '.lang('halaman_tidak_ditemukan'); ?></title>
	<link href="<?php echo base_url(dir_upload('setting').setting('favicon')); ?>" rel="shortcut icon" />
	<?php 
	Asset::css('bootstrap.min.css', true);
	Asset::css('bootstrap.color.min.css', true);
	Asset::css('roboto.css', true);
	Asset::css('fontawesome.css', true);
	Asset::css('style.css', true);
	if(setting('ukuran_tampilan') == 'small') {
		Asset::css('small-style.css', true);
	}
	echo Asset::render();
	?>
</head>
<body class="bg-grey">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col col-sm-7 col-md-5 mt-4 mt-md-6 mb-4">
				<div class="text-center pt-2 pb-4">
					<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="<?php echo setting('title'); ?>" width="200">
				</div>
				<div class="p-4 text-center">
					<h3 class="error-number">404</h3>
					<h6 class="pb-4 error-message"><?php echo lang('halaman_tidak_ditemukan');?></h6>
					<a href="<?php echo base_url('home'); ?>" class="btn btn-info"><i class="fa-home"></i><?php echo lang('halaman_utama'); ?></a>
				</div>
			</div>
		</div>
	</div>
<?php 
Asset::js('jquery.min.js', true);
Asset::js('popper.min.js', true);
Asset::js('bootstrap.min.js', true);
Asset::js('main.js', true);
echo Asset::render();
?>
</body>
</html>