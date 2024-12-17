<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
			<?php echo access_button('setting,delete,active,inactive'); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true,base_url('settings/general/data'),'tbl_master');
		thead();
			tr();
				th('checkbox','text-center','width="30px" data-content="id"');
				th(lang('master'),'','data-content="konten"');
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
			input('text',lang('master'),'konten','required|max-length:100');
			select2(lang('tipe'),'tipe','required|infinity',array('String','Integer'));
			toggle(lang('aktif').'?','is_active');
			form_button(lang('simpan'),lang('batal'));
		form_close();
	modal_footer();
modal_close();
modal_open('modal-setting',lang('pengaturan'),'modal-lg');
	modal_body();
		form_open(base_url('settings/general/save_setting'),'post','form-setting');
			foreach($setting as $s) { ?>
			<div class="form-group row">
				<label class="col-form-label col-sm-3 required" for="setting[1]"><?php echo $s['label']; ?></label>
				<div class="col-sm-6 col-7">
					<select name="setting[<?php echo $s['id']; ?>]" id="setting[<?php echo $s['id']; ?>]" class="form-control select2 infinity" data-validation="required">
						<option value=""></option>
						<?php foreach($master as $m) { ?>
						<option value="<?php echo $m['id']; ?>"<?php if($m['id'] == $s['id_master']) echo ' selected'; ?>><?php echo $m['konten']; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-3 col-5">
					<select name="sort[<?php echo $s['id']; ?>]" id="sort[<?php echo $s['id']; ?>]" class="form-control select2 infinity">
						<option value=""></option>
						<option value="asc"<?php echo $s['tipe'] == 'asc' ? ' selected' : ''; ?>>Ascending</option>
						<option value="desc"<?php echo $s['tipe'] == 'desc' ? ' selected' : ''; ?>>Descending</option>
					</select>
				</div>
			</div>
			<?php }
			form_button(lang('simpan'),lang('batal'));
		form_close();
modal_close();
if(count($setting) > 0) {
?>
<script type="text/javascript">
	$('.btn-act-setting').click(function(){
		$('#modal-setting').modal();
	});
	$('#form-setting').submit(function(e){
		e.preventDefault();
		if(validation($(this).attr('id'))) {
			$.ajax({
				url : $(this).attr('action'),
				data : $(this).serialize(),
				type : 'post',
				dataType : 'json',
				success : function(response) {
					cAlert.open(response.message,response.status);
					if(response.status == 'success') {
						$('#modal-setting').modal('hide');
					}
				}
			});
		}
	});
</script>
<?php } ?>