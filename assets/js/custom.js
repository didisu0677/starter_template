$(document).on('change','[data-provinsi="active"]',function(e){
	e.preventDefault();
	var val  		= $(this).val();
	var temp_id 	= $(this).attr('data-temp_id');
	opt_kecamatan(val,'id_kota',temp_id);
})

var xhr_ajax_kecamatan 	= null;
var select2_readonly 	= false;
function opt_kecamatan(parent,to_id,temp_id){
	if(!parent){
		return '';
	}

	if( xhr_ajax_kecamatan != null ) {
        xhr_ajax_kecamatan.abort();
        xhr_ajax_kecamatan = null;
    }

	xhr_ajax_kecamatan = $.ajax({
		url 	: base_url+'api/kecamatan_option',
		data 	: {
			parent 	: parent,
		},
		type	: 'post',
		dataType: 'json',
		success	: function(response) {
			console.log(temp_id);
			xhr_ajax_kecamatan = null;
			$(document).find('#'+to_id).html(response.data);
			if(typeof temp_id != 'undefined' && temp_id && $(document).find("#"+to_id+" option[value='"+temp_id+"']").length>0){
				$(document).find('#'+to_id).val(temp_id).trigger('change');
			}

			select2_readonly = true;
		}
	});
}

setInterval(function(){
	if(select2_readonly){
		$(document).find('.select2-search__field').attr('readonly',false);
		select2_readonly = false;
	}
}, 3000);