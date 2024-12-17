<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('delete,active,inactive'); ?>
			<button type="button" class="btn btn-info btn-sm btn-icon-only cInfo" data-target="<?php echo base_url('settings/auto_code/help'); ?>" aria-label="<?php echo lang('bantuan'); ?>"><i class="fa-question-circle"></i></button>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/auto_code/data'),'tbl_kode');
		thead();
			tr();
				th('checkbox','text-center','width="30px" data-content="id"');
				th(lang('tabel'),'','data-content="tabel"');
				th(lang('kolom'),'','data-content="kolom"');
				th(lang('awalan'),'','data-content="awalan"');
				th(lang('panjang'),'','data-content="panjang"');
				th(lang('akhiran'),'','data-content="akhiran"');
				th(lang('aktif').'?','text-center','data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/auto_code/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			select2(lang('tabel'),'tabel','required',$list_table,'','','','data-child="kolom"');
			select2(lang('kolom'),'kolom','required');
			input('text',lang('awalan'),'awalan','max-length:50');
			input('text',lang('panjang'),'panjang','required|number|max-length:11');
			input('text',lang('akhiran'),'akhiran','max-length:50');
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
?>
