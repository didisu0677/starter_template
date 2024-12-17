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
	<?php
	table_open('',true,base_url('settings/log/data'),'tbl_user_log');
		thead();
			tr();
				th(lang('no'),'text-center','width="30" data-content="id"');
				th(lang('ip_address'),'','data-content="ip_address"');
				th(lang('nama_user'),'','data-content="nama_user"');
				th(lang('tanggal'),'','data-content="tanggal" data-type="daterange"');
				th(lang('keterangan'),'','data-content="keterangan"');
				th(lang('metode'),'','data-content="metode"');
				th(lang('data'),'','data-content="data"');
				th(lang('respon'),'','data-content="respon" data-replace="200:'.lang('sukses').'|400:'.lang('gagal_masuk').'|403:'.lang('akses_dilarang').'|404:'.lang('halaman_tidak_ditemukan').'"');
	table_close();
	?>
</div>