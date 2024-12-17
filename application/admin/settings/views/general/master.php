<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb($title); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('setting,delete,active,inactive'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/general/data/'.$parent_id),'tbl_master');
		thead();
			tr();
				th('checkbox','text-center','width="30px" data-content="id"');
				th($title,'','data-content="konten"');
				th(lang('aktif').'?','text-center',' width="120" data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
modal_open('modal-form');
	modal_body();
		form_open(base_url('settings/general/save'),'post','form');
			col_init(3,9);
			input('hidden','id','id');
			input('hidden','parent_id','parent_id','',$parent_id);
			if($tipe == 'Integer') {
				input('text',$title,'konten','required|number|max-length:11');
			} else {
				input('text',$title,'konten','required|max-length:100');				
			}
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
?>
