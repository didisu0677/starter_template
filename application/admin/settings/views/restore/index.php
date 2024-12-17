<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<?php if($menu_access['access_additional']) { ?>
		<div class="float-right">
			<button type="button" class="btn btn-primary btn-sm btn-act-import"><i class="fa-upload"></i><?php echo lang('unggah'); ?></button>
		</div>
		<?php } ?>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true);
		thead();
			tr();
				th(lang('backup'));
				th('&nbsp;','','width="30"');
		tbody();
			if(count($backup) > 0) {
				foreach($backup as $b) {
					$button = '';
					$button .= '<button type="button" class="btn btn-warning btn-restore" data-key="restore" data-id="'.$b.'" title="'.lang('restore').'"><i class="fa-redo"></i></button>';
					tr();
						td($b);
						td($button,'button');
				}
			} else {
				tr();
					td(lang('tidak_ada_data'),'','colspan="2"');
			}
	table_close();
	?>
</div>
<?php
modal_open('modal-import',lang('impor'));
	modal_body();
		form_open(base_url('settings/restore/import'),'post','form-import','data-callback="reload"');
			col_init(3,9);
			fileupload('File Zip','fileimport','required','data-accept="zip"');
			form_button(lang('impor'),lang('batal'));
		form_close();
modal_close();
modal_open('modal-form',lang('restore'));
	modal_body();
		form_open(base_url('settings/restore/proccess'),'post','form','data-callback="reload"');
			col_init(0,12);
			input('hidden','file','file');
			echo '<div class="custom-control custom-checkbox mb-3">
				<input type="checkbox" class="custom-control-input" id="all" name="example1">
				<label class="custom-control-label" for="all"><strong>'.lang('semua').'</strong></label>
			  </div>';
			echo '<div id="edit-restore" class="row mb-3"></div>';
			form_button(lang('restore'),lang('batal'));
		form_close();
modal_close();
?>
<script>
$(document).on('click','.btn-restore',function(e){
	e.preventDefault();
	cLoader.open(lang.memuat_data);
	var file = $(this).attr('data-id');
	$.ajax({
		url : base_url + 'settings/restore/get_file',
		data : {file : file},
		type : 'post',
		dataType : 'json',
		success : function(response) {
			if(Object.keys(response).length > 0) {
				konten = '';
				$.each(response,function(k,v){
					konten += '<div class="col-sm-6 mb-1">' +
							'<div class="custom-control custom-checkbox">' +
								'<input type="checkbox" class="custom-control-input" id="value-'+k+'" name="value[]" value="'+v+'">' +
								'<label class="custom-control-label" for="value-'+k+'">'+v+'</label>' +
							'</div>' +
						'</div>';
				});
				$('#file').val(file);
				$('#edit-restore').html(konten);
				$('#modal-form').modal();
			} else {
				cAlert.open(lang.tidak_ada_data);
			}
			cLoader.close();
		}
	});
});
$('#all').click(function(){
	if($(this).is(':checked')) {
		$('#edit-restore .custom-control-input').prop('checked',true);
	} else {
		$('#edit-restore .custom-control-input').prop('checked',false);
	}
});
$(document).on('click','#edit-restore .custom-control-input', function(){
	if($('#edit-restore .custom-control-input:checked').length == 0) {
		$('#all').prop('indeterminate',false);
		$('#all').prop('checked',false);
	} else if($('#edit-restore .custom-control-input:checked').length == $('#edit-restore .custom-control-input').length) {
		$('#all').prop('indeterminate',false);
		$('#all').prop('checked',true);
	} else {
		$('#all').prop('checked',false);
		$('#all').prop('indeterminate',true);
	}
});
</script>