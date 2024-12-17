<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container">
		<?php if (count($quick_link) > 0) { foreach($quick_link as $q) { ?>
		<a href="<?php echo base_url(uri_segment(1).'/'.$q->target); ?>" class="quick-link" title="<?php echo $q->nama; ?>">
			<span class="icon"><i class="<?php echo $q->icon ? $q->icon : 'fa-database'; ?>"></i></span>
			<span class="text"><?php echo lang($q->target,$q->nama); ?></span>
		</a>
		<?php }} else { ?>
		<div class="alert alert-warning">
			<div class="alert-icon"><i class="fa-exclamation"></i></div>
			<div class="alert-description">Tidak ditemukan sub-menu, dikarenakan akun ini tidak mempunyai akses untuk membuka sub-menu dari menu ini. Silahkan hubungi administrator untuk keterangan lebih lanjut.</div>
		</div>
		<?php } ?>
	</div>
</div>