<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb($title); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
<?php
	table_open('',true);
		thead();
			tr();
				th(lang('direktori'));
				th('&nbsp;','','width="30"');
		tbody();
            tr();
                td('_js');
                td('<button type="button" class="btn btn-warning btn-edit-lang" data-key="edit" data-id="_js" title="'.lang('ubah').'"><i class="fa-edit"></i></button>','button');
            foreach($file as $b) {
                if(strpos($b,'.json') != false) {
                    tr();
                        td(str_replace('.json','',$b));
                        td('<button type="button" class="btn btn-warning btn-edit-lang" data-key="edit" data-id="'.str_replace('.json','',$b).'" title="'.lang('ubah').'"><i class="fa-edit"></i></button>','button');
                }
			}
    table_close();
?>
</div>
<?php 
	modal_open('modal-form',lang('ubah'),'modal-lg');
		modal_body();
            form_open(base_url('settings/bahasa/update'),'post','form','data-callback="reload"');
                col_init(3,9);
                input('hidden','bahasa','bahasa','',strtolower($title));
                input('hidden','file','file');
                echo '<div id="edit-lang" class="mb-2"></div>';
				form_button(lang('perbaharui'),lang('batal'));
			form_close();
	modal_close();
?>
<script>
    $(document).on('click','.btn-edit-lang',function(e){
        e.preventDefault();
        cLoader.open(lang.memuat_data);
        var file = $(this).attr('data-id');
        $.ajax({
            url : base_url + 'settings/bahasa/get_directory',
            data : {file : file, bahasa : $('#bahasa').val()},
            type : 'post',
            dataType : 'json',
            success : function(response) {
                konten = '';
                $.each(response,function(k,v){
                    konten += '<div class="form-group row">' +
                                '<label class="col-form-label col-sm-3" for="bahasa">'+k.replace(/\_/g,' ')+'</label>' +
                                '<div class="col-sm-9">' +
                                    '<input type="hidden" name="key[]" value="'+k+'">' +
                                    '<input type="text" name="value[]" autocomplete="off" class="form-control" value="'+v+'">' +
                                '</div>' +
                            '</div>';
                });
                $('#file').val(file);
                $('#edit-lang').html(konten);
                $('#modal-form').modal();
                cLoader.close();
            }
        });
    });
</script>