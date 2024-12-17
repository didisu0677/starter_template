<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
<title><?php echo $title; ?></title>
<link rel="shortcut icon" href="<?php echo base_url(dir_upload('setting').setting('favicon')); ?>" />
<?php
Asset::set_path('core_public');
Asset::css('bootstrap.min.css', true);
Asset::css('bootstrap.color.min.css', true);
Asset::css('roboto.css', true);
Asset::css('fontawesome.css', true);
Asset::css('linear-icons.css', true);
Asset::css('daterangepicker.css', true);
Asset::css('style.css', true);
echo Asset::render();
?>
<?php echo $css_content; ?>
</head>
<body>
<div class="header fixed-top">
    <nav class="navbar navbar-expand-lg navbar-light main-container">
        <a class="logo-brand" href="<?php echo base_url(); ?>">
            <img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="<?php echo setting('title'); ?>" />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-main" aria-controls="navbar-main" aria-expanded="false" aria-label="Toggle navigation">
            <i class="li-menu"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbar-main">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item<?php if($cur_menu == '') echo ' active'; ?>">
                    <a class="nav-link" href="<?php echo base_url(); ?>">Beranda</a>
                </li>
                <li class="nav-item<?php if($cur_menu == 'kebijakan') echo ' active'; ?>">
                    <a class="nav-link" href="<?php echo base_url('kebijakan'); ?>">Kebijakan</a>
                </li>
                <li class="nav-item<?php if($cur_menu == 'informasi') echo ' active'; ?>">
                    <a class="nav-link" href="<?php echo base_url('informasi'); ?>">Informasi</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle<?php if($cur_menu == 'pengumuman') echo ' active'; ?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Pengumuman
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item<?php if($cur_menu == 'pengumuman' && ($cur_sub == '' || $cur_sub == 'index')) echo ' active'; ?>" href="<?php echo base_url('pengumuman'); ?>">Pengumuman Pengadaan</a>
                        <a class="dropdown-item<?php if($cur_menu == 'pengumuman' && $cur_sub == 'pemenang') echo ' active'; ?>" href="<?php echo base_url('pengumuman/pemenang'); ?>">Pengumuman Pemenang</a>
                    </div>
                </li>
                <li class="nav-item<?php if($cur_menu == 'faq') echo ' active'; ?>">
                    <a class="nav-link" href="<?php echo base_url('faq'); ?>">F.A.Q</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn-daftar" href="javascript:;">Pendaftaran Rekanan</a>
                </li>
            </ul>
            <div class="my-2 my-lg-0">
                <a href="<?php echo base_url('auth/login'); ?>" class="btn btn-app d-block d-lg-inline-block" id="btn-login">Masuk</a>
            </div>
        </div>
    </nav>
</div>
<div id="content" class="main-container">
<?php echo $view_content; ?>
</div>
<?php 
modal_open('modal-disclaimer','Syarat &amp; Ketentuan','modal-lg','modal-info');
    modal_body();
        echo html_entity_decode(setting('disclaimer'));
    modal_footer();
        echo '<button type="button" class="btn btn-app" id="btn-daftar">Saya Setuju</button>';
modal_close();
echo '<script>var base_url = "'.base_url().'";</script>';
Asset::set_path('core_public');
Asset::js('jquery.min.js', true);
Asset::js('moment.min.js', true);
Asset::js('popper.min.js', true);
Asset::js('bootstrap.min.js', true);
Asset::js('daterangepicker.js', true);
Asset::js('main.js', true);
echo Asset::render();
?>
<?php echo $js_content; ?>
</body>
</html>