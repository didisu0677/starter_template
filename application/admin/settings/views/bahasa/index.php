<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<?php if($menu_access['access_input']) { ?>
		<div class="float-right">
			<button type="button" class="btn btn-primary btn-sm btn-add-lang"><i class="fa-plus"></i><?php echo lang('tambah'); ?></button>
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
				th(lang('bahasa'));
				th(lang('bendera'),'','width="30"');
				th('&nbsp;','','width="30"');
		tbody();
			foreach($bahasa as $b) {
				$button = '';
				if($menu_access['access_edit']) $button .= '<a href="'.base_url('settings/bahasa/d/'.$b).'" title="'.lang('detil').'" class="btn btn-info"><i class="fa-search"></i></a> ';
				if($menu_access['access_delete']) $button .= '<button type="button" class="btn btn-danger btn-delete-lang" data-key="delete" data-id="'.$b.'" title="'.lang('hapus').'"><i class="fa-trash-alt"></i></button>';
				tr();
					td(strtoupper($b));
					td('<img src="'.base_url('assets/lang/'.$b.'/_flag.png').'" alt="'.$b.'" width="24" />','text-center');
					td($button,'button');
			}
	table_close();
?>
</div>
<?php 
	modal_open('modal-form',lang('tambah'));
		modal_body();
			form_open(base_url('settings/bahasa/save'),'post','form','data-callback="reload"');
				col_init(3,9);
				input('text',lang('bahasa'),'bahasa','required|min-length:2|max-length:2');
				imageupload(lang('bendera'),'flag',48,48);
				form_button(lang('simpan'),lang('batal'));
			form_close();
	modal_close();
?>
<script>
	var del_lang = '';
	$('.btn-add-lang').click(function(e){
		e.preventDefault();
		$('#bahasa').val('');
		$('[name="flag"]').parent().find('img').attr('src',$('[name="flag"]').parent().find('img').attr('data-origin'));
		$('#modal-form').modal();
	});
	$(document).on('click','.btn-delete-lang',function(e){
		e.preventDefault();
		del_lang = $(this).attr('data-id');
		cConfirm.open(lang.anda_yakin_menghapus_data_ini+'?','deleteLang');
	});
	function deleteLang(){
		$.ajax({
			url : base_url + 'settings/bahasa/delete',
			data : {bahasa: del_lang},
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
</script>