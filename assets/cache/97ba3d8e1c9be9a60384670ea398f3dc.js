
var del_backup;
$('.btn-backup').click(function(e){
	e.preventDefault();
	$('#modal-backup form')[0].reset();
	$('#modal-backup').modal();
});
$(document).on('click','.btn-delete-backup',function(e){
	e.preventDefault();
	del_backup = $(this).attr('data-id');
	cConfirm.open(lang.anda_yakin_menghapus_data_ini+'?','deleteBackup');
});
function deleteBackup(){
	$.ajax({
		url : base_url + 'settings/backup/delete',
		data : {backup: del_backup},
		type : 'post',
		dataType : 'json',
		success : function(response) {
			if(response.status == 'success') {
				cAlert.open(response.message,response.status,'reload');
			} else {
				cAlert.open(response.message,response.status);
			}
		}
	});
}
$('#all').click(function(){
	if($(this).is(':checked')) {
		$('#chk-backup .custom-control-input').prop('checked',true);
	} else {
		$('#chk-backup .custom-control-input').prop('checked',false);
	}
});
$(document).on('click','#chk-backup .custom-control-input', function(){
	if($('#chk-backup .custom-control-input:checked').length == 0) {
		$('#all').prop('indeterminate',false);
		$('#all').prop('checked',false);
	} else if($('#chk-backup .custom-control-input:checked').length == $('#chk-backup .custom-control-input').length) {
		$('#all').prop('indeterminate',false);
		$('#all').prop('checked',true);
	} else {
		$('#all').prop('checked',false);
		$('#all').prop('indeterminate',true);
	}
});

$('#all-file').click(function(){
	if($(this).is(':checked')) {
		$('#chk-file-backup .custom-control-input').prop('checked',true);
	} else {
		$('#chk-file-backup .custom-control-input').prop('checked',false);
	}
});
$(document).on('click','#chk-file-backup .custom-control-input', function(){
	if($('#chk-file-backup .custom-control-input:checked').length == 0) {
		$('#all-file').prop('indeterminate',false);
		$('#all-file').prop('checked',false);
	} else if($('#chk-file-backup .custom-control-input:checked').length == $('#chk-file-backup .custom-control-input').length) {
		$('#all-file').prop('indeterminate',false);
		$('#all-file').prop('checked',true);
	} else {
		$('#all-file').prop('checked',false);
		$('#all-file').prop('indeterminate',true);
	}
});

