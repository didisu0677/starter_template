<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete,active,inactive'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/pengumuman/data'),'tbl_pengumuman');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('informasi'),'','data-content="pengumuman"');
				th(lang('tanggal_terbit'),'','data-content="tanggal_publish" data-type="daterange"');
				th(lang('tanggal_selesai'),'','data-content="tanggal_selesai" data-type="daterange"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/pengumuman/save'),'post','form');
			col_init(4,8);
			input('hidden','id','id');
			input('hidden','id_user','id_user','',user('id'));
			textarea(lang('informasi'),'pengumuman','required');
			input('datetime',lang('tanggal_terbit'),'tanggal_publish','required');
			input('datetime',lang('tanggal_selesai'),'tanggal_selesai','required');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
?>
