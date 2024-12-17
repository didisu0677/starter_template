
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
