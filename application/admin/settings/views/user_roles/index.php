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
	table_open('',true,base_url('settings/user_roles/data'),'tbl_user_group');
		thead();
			tr();
				th('checkbox','text-center','width="30" data-content="id"');
				th(lang('grup'),'','width="250" data-content="nama"');
				th(lang('keterangan'),'','data-content="keterangan"');
				th(lang('aktif').'?','text-center','width="120" data-content="is_active" data-type="boolean"');
				th('&nbsp;','','width="30" data-content="action_button"');
	table_close();
	?>
</div>
<?php 
	modal_open('modal-form','','modal-lg','data-manual="true"');
		modal_body();
?>
<form method="post" action="<?php echo base_url('settings/user_roles/save'); ?>" id="form">
	<input type="hidden" name="id">
	<div class="form-group tab-app">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true"><?php echo lang('informasi'); ?></a>
			</li>
			<?php foreach($menu[0] as $mn) { ?>
			<li class="nav-item">
				<a class="nav-link" id="<?php echo $mn->target; ?>-tab" data-toggle="tab" href="#<?php echo $mn->target; ?>" role="tab" aria-controls="<?php echo $mn->target; ?>" aria-selected="false"><?php echo $mn->nama; ?></a>
			</li>
			<?php } ?>
		</ul>
		<div class="tab-content" id="myTabContent">
			<div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
				<div class="form-group row">
					<label class="col-form-label col-sm-3 required" for="nama"><?php echo lang('grup'); ?></label>
					<div class="col-sm-9">
						<input type="text" name="nama" id="nama" autocomplete="off" class="form-control" data-validation="required|min-length:3|unique">
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3" for="keterangan"><?php echo lang('keterangan'); ?></label>
					<div class="col-sm-9">
						<textarea name="keterangan" id="keterangan" rows="4" class="form-control"></textarea>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-form-label col-sm-3" for="is_active"><?php echo lang('aktif'); ?>?</label>
					<div class="col-sm-9">
						<label class="switch"><input type="checkbox" value="1" name="is_active" id="is_active" checked>
							<span class="slider"></span>
						</label>
					</div>
				</div>
			</div>
			<?php foreach($menu[0] as $mn) { ?>
			<div class="tab-pane fade" id="<?php echo $mn->target; ?>" role="tabpanel" aria-labelledby="<?php echo $mn->target; ?>-tab">
				<div class="form-group row">
					<input type="hidden" name="id_menu[]" value="<?php echo $mn->id; ?>">
					<label class="col-form-label col-sm-4"><?php echo $mn->nama; ?></label>
					<div class="col-sm-1 col-2">
						<div class="custom-checkbox custom-control custom-control-inline">
							<input class="custom-control-input chk-all" type="checkbox" id="all-<?php echo $mn->id; ?>" value="1">
							<label class="custom-control-label" for="all-<?php echo $mn->id; ?>">&nbsp;</label>
						</div>
					</div>
					<div class="col-sm-7 col-10">
						<div class="custom-checkbox custom-control custom-control-inline">
							<input class="custom-control-input chk-child" type="checkbox" id="act_view-<?php echo $mn->id; ?>" name="act_view[<?php echo $mn->id; ?>]" data-parent="act_view-0" value="1">
							<label class="custom-control-label" for="act_view-<?php echo $mn->id; ?>"><?php echo lang('lihat') ?></label>
						</div>
						<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn->akses_input) echo ' v-hidden'; ?>">
							<input class="custom-control-input chk-child" type="checkbox" id="act_input-<?php echo $mn->id; ?>" name="act_input[<?php echo $mn->id; ?>]" data-parent="act_view-<?php echo $mn->id; ?>" value="1"<?php if(!$mn->akses_input) echo ' disabled'; ?>>
							<label class="custom-control-label" for="act_input-<?php echo $mn->id; ?>"><?php echo $mn->alias_input ? $mn->alias_input : lang('tambah'); ?></label>
						</div>
						<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn->akses_edit) echo ' v-hidden' ;?>">
							<input class="custom-control-input chk-child" type="checkbox" id="act_edit-<?php echo $mn->id; ?>" name="act_edit[<?php echo $mn->id; ?>]" data-parent="act_view-<?php echo $mn->id; ?>" value="1"<?php if(!$mn->akses_edit) echo ' disabled' ?>>
							<label class="custom-control-label" for="act_edit-<?php echo $mn->id; ?>"><?php echo $mn->alias_edit ? $mn->alias_edit : lang('ubah'); ?></label>
						</div>
						<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn->akses_delete) echo ' v-hidden'; ?>">
							<input class="custom-control-input chk-child" type="checkbox" id="act_delete-<?php echo $mn->id; ?>" name="act_delete[<?php echo $mn->id; ?>]" data-parent="act_view-<?php echo $mn->id; ?>" value="1"<?php if(!$mn->akses_delete) echo ' disabled'; ?>>
							<label class="custom-control-label" for="act_delete-<?php echo $mn->id; ?>"><?php echo $mn->alias_delete ? $mn->alias_delete : lang('hapus'); ?></label>
						</div>
						<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn->akses_additional) echo ' v-hidden'; ?>">
							<input class="custom-control-input chk-child" type="checkbox" id="act_additional-<?php echo $mn->id; ?>" name="act_additional[<?php echo $mn->id; ?>]" data-parent="act_view-<?php echo $mn->id; ?>" value="1"<?php if(!$mn->akses_additional) echo ' disabled'; ?>>
							<label class="custom-control-label" for="act_additional-<?php echo $mn->id; ?>"><?php echo $mn->alias_additional ? lang(str_replace(' ','_',strtolower($mn->alias_additional)),$mn->alias_additional) : lang('tambahan'); ?></label>
						</div>
					</div>
				</div>
				<?php foreach($menu[$mn->id] as $mn1) { ?>
				<div class="form-group row">
					<input type="hidden" name="id_menu[]" value="<?php echo $mn1->id; ?>">
					<label class="col-form-label col-sm-4 sub-1"><?php echo $mn1->nama; ?></label>
					<div class="col-sm-1 col-2">
						<div class="custom-checkbox custom-control custom-control-inline">
							<input class="custom-control-input chk-all" type="checkbox" id="all-<?php echo $mn1->id; ?>" value="1">
							<label class="custom-control-label" for="all-<?php echo $mn1->id; ?>">&nbsp;</label>
						</div>
					</div>
					<div class="col-sm-7 col-10">
						<div class="custom-checkbox custom-control custom-control-inline">
							<input class="custom-control-input chk-child" type="checkbox" id="act_view-<?php echo $mn1->id; ?>" name="act_view[<?php echo $mn1->id; ?>]" data-parent="act_view-<?php echo $mn->id; ?>" value="1">
							<label class="custom-control-label" for="act_view-<?php echo $mn1->id; ?>"><?php echo lang('lihat') ?></label>
						</div>
						<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn1->akses_input) echo ' v-hidden'; ?>">
							<input class="custom-control-input chk-child" type="checkbox" id="act_input-<?php echo $mn1->id; ?>" name="act_input[<?php echo $mn1->id; ?>]" data-parent="act_view-<?php echo $mn1->id; ?>" value="1"<?php if(!$mn1->akses_input) echo ' disabled'; ?>>
							<label class="custom-control-label" for="act_input-<?php echo $mn1->id; ?>"><?php echo $mn1->alias_input ? $mn1->alias_input : lang('tambah'); ?></label>
						</div>
						<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn1->akses_edit) echo ' v-hidden' ;?>">
							<input class="custom-control-input chk-child" type="checkbox" id="act_edit-<?php echo $mn1->id; ?>" name="act_edit[<?php echo $mn1->id; ?>]" data-parent="act_view-<?php echo $mn1->id; ?>" value="1"<?php if(!$mn1->akses_edit) echo ' disabled' ?>>
							<label class="custom-control-label" for="act_edit-<?php echo $mn1->id; ?>"><?php echo $mn1->alias_edit ? $mn1->alias_edit : lang('ubah'); ?></label>
						</div>						
						<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn1->akses_delete) echo ' v-hidden'; ?>">
							<input class="custom-control-input chk-child" type="checkbox" id="act_delete-<?php echo $mn1->id; ?>" name="act_delete[<?php echo $mn1->id; ?>]" data-parent="act_view-<?php echo $mn1->id; ?>" value="1"<?php if(!$mn1->akses_delete) echo ' disabled'; ?>>
							<label class="custom-control-label" for="act_delete-<?php echo $mn1->id; ?>"><?php echo $mn1->alias_delete ? $mn1->alias_delete : lang('hapus'); ?></label>
						</div>
						<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn1->akses_additional) echo ' v-hidden'; ?>">
							<input class="custom-control-input chk-child" type="checkbox" id="act_additional-<?php echo $mn1->id; ?>" name="act_additional[<?php echo $mn1->id; ?>]" data-parent="act_view-<?php echo $mn1->id; ?>" value="1"<?php if(!$mn1->akses_additional) echo ' disabled'; ?>>
							<label class="custom-control-label" for="act_additional-<?php echo $mn1->id; ?>"><?php echo $mn1->alias_additional ? lang(str_replace(' ','_',strtolower($mn1->alias_additional)),$mn1->alias_additional) : lang('tambahan'); ?></label>
						</div>
					</div>
				</div>
					<?php foreach($menu[$mn1->id] as $mn2) { ?>
					<div class="form-group row">
						<input type="hidden" name="id_menu[]" value="<?php echo $mn2->id; ?>">
						<label class="col-form-label col-sm-4 sub-2"><?php echo $mn2->nama; ?></label>
						<div class="col-sm-1 col-2">
							<div class="custom-checkbox custom-control custom-control-inline">
								<input class="custom-control-input chk-all" type="checkbox" id="all-<?php echo $mn2->id; ?>" value="1">
								<label class="custom-control-label" for="all-<?php echo $mn2->id; ?>">&nbsp;</label>
							</div>
						</div>
						<div class="col-sm-7 col-10">
							<div class="custom-checkbox custom-control custom-control-inline">
								<input class="custom-control-input chk-child" type="checkbox" id="act_view-<?php echo $mn2->id; ?>" name="act_view[<?php echo $mn2->id; ?>]" data-parent="act_view-<?php echo $mn1->id; ?>" value="1">
								<label class="custom-control-label" for="act_view-<?php echo $mn2->id; ?>"><?php echo lang('lihat') ?></label>
							</div>
							<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn2->akses_input) echo ' v-hidden'; ?>">
								<input class="custom-control-input chk-child" type="checkbox" id="act_input-<?php echo $mn2->id; ?>" name="act_input[<?php echo $mn2->id; ?>]" data-parent="act_view-<?php echo $mn2->id; ?>" value="1"<?php if(!$mn2->akses_input) echo ' disabled'; ?>>
								<label class="custom-control-label" for="act_input-<?php echo $mn2->id; ?>"><?php echo $mn2->alias_input ? $mn2->alias_input : lang('tambah'); ?></label>
							</div>
							<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn2->akses_edit) echo ' v-hidden' ;?>">
								<input class="custom-control-input chk-child" type="checkbox" id="act_edit-<?php echo $mn2->id; ?>" name="act_edit[<?php echo $mn2->id; ?>]" data-parent="act_view-<?php echo $mn2->id; ?>" value="1"<?php if(!$mn2->akses_edit) echo ' disabled' ?>>
								<label class="custom-control-label" for="act_edit-<?php echo $mn2->id; ?>"><?php echo $mn2->alias_edit ? $mn2->alias_edit : lang('ubah'); ?></label>
							</div>						
							<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn2->akses_delete) echo ' v-hidden'; ?>">
								<input class="custom-control-input chk-child" type="checkbox" id="act_delete-<?php echo $mn2->id; ?>" name="act_delete[<?php echo $mn2->id; ?>]" data-parent="act_view-<?php echo $mn2->id; ?>" value="1"<?php if(!$mn2->akses_delete) echo ' disabled'; ?>>
								<label class="custom-control-label" for="act_delete-<?php echo $mn2->id; ?>"><?php echo $mn2->alias_delete ? $mn2->alias_delete : lang('hapus'); ?></label>
							</div>
							<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn2->akses_additional) echo ' v-hidden'; ?>">
								<input class="custom-control-input chk-child" type="checkbox" id="act_additional-<?php echo $mn2->id; ?>" name="act_additional[<?php echo $mn2->id; ?>]" data-parent="act_view-<?php echo $mn2->id; ?>" value="1"<?php if(!$mn2->akses_additional) echo ' disabled'; ?>>
								<label class="custom-control-label" for="act_additional-<?php echo $mn2->id; ?>"><?php echo $mn2->alias_additional ? lang(str_replace(' ','_',strtolower($mn2->alias_additional)),$mn2->alias_additional) : lang('tambahan'); ?></label>
							</div>
						</div>
					</div>
						<?php foreach($menu[$mn2->id] as $mn3) { ?>
						<div class="form-group row">
							<input type="hidden" name="id_menu[]" value="<?php echo $mn3->id; ?>">
							<label class="col-form-label col-sm-4 sub-3"><?php echo $mn3->nama; ?></label>
							<div class="col-sm-1 col-2">
								<div class="custom-checkbox custom-control custom-control-inline">
									<input class="custom-control-input chk-all" type="checkbox" id="all-<?php echo $mn3->id; ?>" value="1">
									<label class="custom-control-label" for="all-<?php echo $mn3->id; ?>">&nbsp;</label>
								</div>
							</div>
							<div class="col-sm-7 col-10">
								<div class="custom-checkbox custom-control custom-control-inline">
									<input class="custom-control-input chk-child" type="checkbox" id="act_view-<?php echo $mn3->id; ?>" name="act_view[<?php echo $mn3->id; ?>]" data-parent="act_view-<?php echo $mn2->id; ?>" value="1">
									<label class="custom-control-label" for="act_view-<?php echo $mn3->id; ?>"><?php echo lang('lihat') ?></label>
								</div>
								<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn3->akses_input) echo ' v-hidden'; ?>">
									<input class="custom-control-input chk-child" type="checkbox" id="act_input-<?php echo $mn3->id; ?>" name="act_input[<?php echo $mn3->id; ?>]" data-parent="act_view-<?php echo $mn3->id; ?>" value="1"<?php if(!$mn3->akses_input) echo ' disabled'; ?>>
									<label class="custom-control-label" for="act_input-<?php echo $mn3->id; ?>"><?php echo $mn3->alias_input ? $mn3->alias_input : lang('tambah'); ?></label>
								</div>
								<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn3->akses_edit) echo ' v-hidden' ;?>">
									<input class="custom-control-input chk-child" type="checkbox" id="act_edit-<?php echo $mn3->id; ?>" name="act_edit[<?php echo $mn3->id; ?>]" data-parent="act_view-<?php echo $mn3->id; ?>" value="1"<?php if(!$mn3->akses_edit) echo ' disabled' ?>>
									<label class="custom-control-label" for="act_edit-<?php echo $mn3->id; ?>"><?php echo $mn3->alias_edit ? $mn3->alias_edit : lang('ubah'); ?></label>
								</div>						
								<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn3->akses_delete) echo ' v-hidden'; ?>">
									<input class="custom-control-input chk-child" type="checkbox" id="act_delete-<?php echo $mn3->id; ?>" name="act_delete[<?php echo $mn3->id; ?>]" data-parent="act_view-<?php echo $mn3->id; ?>" value="1"<?php if(!$mn3->akses_delete) echo ' disabled'; ?>>
									<label class="custom-control-label" for="act_delete-<?php echo $mn3->id; ?>"><?php echo $mn3->alias_delete ? $mn3->alias_delete : lang('hapus'); ?></label>
								</div>
								<div class="custom-checkbox custom-control custom-control-inline<?php if(!$mn3->akses_additional) echo ' v-hidden'; ?>">
									<input class="custom-control-input chk-child" type="checkbox" id="act_additional-<?php echo $mn3->id; ?>" name="act_additional[<?php echo $mn3->id; ?>]" data-parent="act_view-<?php echo $mn3->id; ?>" value="1"<?php if(!$mn3->akses_additional) echo ' disabled'; ?>>
									<label class="custom-control-label" for="act_additional-<?php echo $mn3->id; ?>"><?php echo $mn3->alias_additional ? lang(str_replace(' ','_',strtolower($mn3->alias_additional)),$mn3->alias_additional) : lang('tambahan'); ?></label>
								</div>
							</div>
						</div>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-sm-12">
			<button type="submit" class="btn  btn-info"><?php echo lang('simpan'); ?></button>
			<button type="reset" class="btn  btn-secondary"><?php echo lang('batal'); ?></button>
		</div>
	</div>
</form>
<?php
		modal_footer();
	modal_close();
?>
<script type="text/javascript">
$(document).on('click','.btn-input',function(){
	$('#modal-form form')[0].reset();
	$('#modal-form .modal-footer').addClass('hidden').html('');
	$('#modal-form [name="id"]').val(0);
	$('.tab-pane').removeClass('show').removeClass('active');
	$('.tab-pane').first().addClass('show').addClass('active');
	$('.nav-tabs .nav-link').removeClass('active');
	$('.nav-tabs .nav-link').first().addClass('active');
	$('.chk-all').prop('indeterminate',false).prop('checked',false);
	$('#modal-form form .is-invalid').each(function(){
		$(this).removeClass('is-invalid');
		$(this).closest('.form-group').find('.error').remove();
	});
	if($(this).data('id') == 0) {
		$('#modal-form .modal-title').html('Tambah');
		$('#modal-form [type="submit"]').text('Simpan');
		$('#modal-form').modal();
	} else {
		$('#modal-form .modal-title').html('Edit');
		$('#modal-form [type="submit"]').text('Update');
		var getUrl = '';
		if(typeof $('#modal-form form').attr('data-edit') != 'undefined') {
			getUrl = $('#modal-form form').attr('data-edit');
		} else {
			var curUrl = $('#modal-form form').attr('action');
			var parseUrl = curUrl.split('/');
			var lastPath = parseUrl[parseUrl.length - 1];
			if(lastPath == '') lastPath = parseUrl[parseUrl.length - 2];
			getUrl = curUrl.replace(lastPath,'get_data');
		}
		$.ajax({
			url			: getUrl,
			data 		: {'id':$(this).attr('data-id')},
			type		: 'post',
			cache		: false,
			dataType	: 'json',
			success		: function(response) {
				if(typeof response['status'] == 'undefined' && typeof response['message'] == 'undefined') {
					$('#modal-form [name="id"]').val(response.id);
					$('#modal-form [name="nama"]').val(response.nama);
					$('#modal-form [name="keterangan"]').val(response.keterangan);
					if(response.is_active == '0') {
						$('#modal-form [name="is_active"]').prop('checked',false);
					}
					$.each(response.access,function(k,v){
						if(response.access[k].act_view == '1') $('#modal-form [name="act_view['+response.access[k].id_menu+']"]').prop('checked',true);
						if(response.access[k].act_input == '1') $('#modal-form [name="act_input['+response.access[k].id_menu+']"]').prop('checked',true);
						if(response.access[k].act_edit == '1') $('#modal-form [name="act_edit['+response.access[k].id_menu+']"]').prop('checked',true);
						if(response.access[k].act_delete == '1') $('#modal-form [name="act_delete['+response.access[k].id_menu+']"]').prop('checked',true);
						if(response.access[k].act_additional == '1') $('#modal-form [name="act_additional['+response.access[k].id_menu+']"]').prop('checked',true);
						var f = $('#modal-form [name="act_view['+response.access[k].id_menu+']"]').closest('.form-group');
						if(f.find('.chk-child:enabled:checked').length > 0) {
							if(f.find('.chk-child:enabled:checked').length == f.find('.chk-child:enabled').length) {
								f.find('.chk-all').prop('checked',true);
							} else {
								f.find('.chk-all').prop('indeterminate',true);
							}
						}
					});
					$('#modal-form').modal();
					$('#modal-form .modal-footer').html('');
					var footer_text = '';
					var create_info = '';
					var update_info = '';
					if(typeof response['create_by'] != 'undefined' && typeof response['create_at'] != 'undefined') {
						if(response['create_at'] != '0000-00-00 00:00:00') {
							var create_by = response['create_by'] == '' ? 'Unknown' : response['create_by'];
							var create_at = response['create_at'].split(' ');
							var tanggal_c = create_at[0].split('-');
							var waktu_c = create_at[1].split(':');
							var date_c = tanggal_c[2]+'/'+tanggal_c[1]+'/'+tanggal_c[0]+' '+waktu_c[0]+':'+waktu_c[1];
							create_info += '<small>Dibuat oleh <strong>' + create_by + ' </strong> @ ' + date_c + '</small>';
						}
					}
					if(typeof response['update_by'] != 'undefined' && typeof response['update_at'] != 'undefined') {
						if(response['update_at'] != '0000-00-00 00:00:00') {
							var update_by = response['update_by'] == '' ? 'Unknown' : response['update_by'];
							var update_at = response['update_at'].split(' ');
							var tanggal_u = update_at[0].split('-');
							var waktu_u = update_at[1].split(':');
							var date_u = tanggal_u[2]+'/'+tanggal_u[1]+'/'+tanggal_u[0]+' '+waktu_u[0]+':'+waktu_u[1];
							update_info += '<small>Diupdate oleh <strong>' + update_by + ' </strong> @ ' + date_u + '</small>';
						}
					}
					if(create_info || update_info) {
						footer_text += '<div class="w-100">';
						footer_text += create_info;
						footer_text += update_info;
						footer_text += '</div>';
					}
					if(footer_text) {
						$('#modal-form .modal-footer').html(footer_text).removeClass('hidden');
					}
				} else {
					cAlert.open(response['message'],response['status']);
				}
			}
		});
	}
});
$('.chk-all').click(function(){
	if($(this).is(':checked')) {
		$(this).closest('.form-group').find('.chk-child:enabled').prop('checked',true);
		var c0 = $(this).closest('.form-group').find('.chk-child').last().attr('data-parent');
		var c1 = $('#' + c0).attr('data-parent');
		var c2 = $('#' + c1).attr('data-parent');
		var c3 = $('#' + c2).attr('data-parent');
		$('#' + c0).prop('checked',true);
		$('#' + c1).prop('checked',true);
		$('#' + c2).prop('checked',true);
		$('#' + c3).prop('checked',true);
		if($('#' + c0).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c0).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c0).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c0).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c1).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c1).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c1).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c1).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c2).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c2).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c2).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c2).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c3).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c3).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c3).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c3).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
	} else {
		$(this).closest('.form-group').find('.chk-child:enabled').prop('checked',false);
		$('[data-parent="' + $(this).closest('.form-group').find('.chk-child').first().attr('id') + '"]').each(function(){
			$(this).prop('checked',false);
			$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
			$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
				$(this).prop('checked',false);
				$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
				$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
					$(this).prop('checked',false);
					$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
					$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
						$(this).prop('checked',false);
						$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
					});
				});
			});
		});
	}
});
$('.chk-child').click(function(){
	if($(this).is(':checked')) {
		var c0 = $(this).attr('data-parent');
		var c1 = $('#' + c0).attr('data-parent');
		var c2 = $('#' + c1).attr('data-parent');
		var c3 = $('#' + c2).attr('data-parent');
		$('#' + c0).prop('checked',true);
		$('#' + c1).prop('checked',true);
		$('#' + c2).prop('checked',true);
		$('#' + c3).prop('checked',true);
		if($('#' + c0).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c0).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c0).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c0).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c1).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c1).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c1).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c1).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c2).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c2).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c2).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c2).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
		if($('#' + c3).closest('.form-group').find('.chk-child:enabled:checked').length == $('#' + c3).closest('.form-group').find('.chk-child:enabled').length) {
			$('#' + c3).closest('.form-group').find('.chk-all').prop('checked',true); 
		} else {
			$('#' + c3).closest('.form-group').find('.chk-all').prop('indeterminate',true); 
		}
	} else {
		$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
			$(this).prop('checked',false);
			$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
			$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
				$(this).prop('checked',false);
				$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
				$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
					$(this).prop('checked',false);
					$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
					$('[data-parent="' + $(this).attr('id') + '"]').each(function(){
						$(this).prop('checked',false);
						$(this).closest('.form-group').find('.chk-all').prop('checked',false).prop('indeterminate',false);
					});
				});
			});
		});
	}
	if($(this).closest('.form-group').find('.chk-child:enabled:checked').length > 0) {
		if($(this).closest('.form-group').find('.chk-child:enabled').length == $(this).closest('.form-group').find('.chk-child:enabled:checked').length) {
			$(this).closest('.form-group').find('.chk-all').prop('indeterminate',false).prop('checked',true);
		} else {
			$(this).closest('.form-group').find('.chk-all').prop('indeterminate',true).prop('checked',false);
		}
	} else {
		$(this).closest('.form-group').find('.chk-all').prop('indeterminate',false).prop('checked',false);
	}
});
</script>