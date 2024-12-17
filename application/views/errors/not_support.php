<!DOCTYPE html>
<html lang="en">
<head>
	<title>Browser Tidak Didukung</title>
	<link href="<?php echo base_url(dir_upload('setting').setting('favicon')); ?>" rel="shortcut icon" />
	<style type="text/css">
		* {
			padding: 0;
			margin: 0;
			font-family: sans-serif;
			color: #484848;
		}
		.logo {
			display: block;
			width: 200px;
			margin: 20px auto;
		}
		.message {
			font-size: 16px;
			font-weight: 600;
			padding: 15px;
			text-align: center;
		}
		.text-center {
			text-align: center;
		}
		a {
			border: 0 none;
			outline: none;
			text-decoration: none;
		}
		a img {
			border: 0 none;
		}
	</style>
</head>
<body>
	<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" class="logo">
	<div class="message">
		Browser yang anda gunakan belum mendukung untuk menjalankan aplikasi ini.
	</div>
	<div class="text-center">
		Aplikasi ini bekerja baik dengan menggunakan browser berikut:
	</div>
	<div class="text-center" style="margin-top: 20px;">
		<a href="https://www.mozilla.org/id/firefox/new/" target="_blank">
			<img src="<?php echo base_url('assets/images/firefox.png'); ?>" width="100" alt="Firefox" />
		</a>
		<a href="https://www.google.com/intl/id_id/chrome/" target="_blank" style="margin: 0 10px;">
			<img src="<?php echo base_url('assets/images/chrome.png'); ?>" width="100" alt="Chrome" />
		</a>
		<a href="https://www.opera.com/id" target="_blank">
			<img src="<?php echo base_url('assets/images/opera.png'); ?>" width="100" alt="Opera" />
		</a>
	</div>
</body>
</html>