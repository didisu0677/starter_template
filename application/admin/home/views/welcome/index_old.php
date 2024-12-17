<div class="content-body body-home">
	<div class="body-bg color-bg">
		<div class="main-container p-4 text-center">
			<div class="mb-sm-4 mt-sm-4 pt-sm-4 pb-sm-2">
				<img src="<?php echo user('foto'); ?>" class="rounded-circle" alt="avatar">
			</div>
			<h4 class="pt-sm-2 pt-4"><?php echo user('nama'); ?></h4>
			<h6 class="mb-sm-4"><?php echo user('email'); ?></h6>
		</div>
	</div>
	<div class="main-container p-4 mb-sm-4 text-center">
		<a href="<?php echo base_url('home/welcome/info'); ?>" class="quick-link cInfo" title="<?php echo lang('informasi'); ?>">
			<span class="icon"><i class="fa-info"></i></span>
			<span class="text"><?php echo lang('informasi'); ?></span>
			<?php if(count($pengumuman) > 0) { ?>
			<div class="notif"></div>
			<?php } ?>
		</a>
		<a href="<?php echo base_url('account/profile'); ?>" class="quick-link" title="<?php echo lang('profil'); ?>">
			<span class="icon"><i class="fa-user-edit"></i></span>
			<span class="text"><?php echo lang('profil'); ?></span>
		</a>
		<a href="<?php echo base_url('account/changepwd'); ?>" class="quick-link" title="<?php echo lang('ubah_kata_sandi'); ?>">
			<span class="icon"><i class="fa-key"></i></span>
			<span class="text"><?php echo lang('ubah_kata_sandi'); ?></span>
		</a>
		<a href="<?php echo base_url('auth/logout'); ?>" class="quick-link" title="<?php echo lang('keluar'); ?>">
			<span class="icon"><i class="fa-sign-out-alt"></i></span>
			<span class="text"><?php echo lang('keluar'); ?></span>
		</a>
	</div>
	<div class="main-container p-4 text-center">
		<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="logo">
		<div class="version"><?php echo lang('versi') . ' ' . APP_VERSION; ?></div>
	</div>
</div> 