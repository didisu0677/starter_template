<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery.sortable.css'); ?>" />
<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="float-right">
		<?php if($menu_access['access_edit']) { ?>
			<button type="button" class="btn btn-success btn-sm btn-sort"><i class="fa-align-right"></i><?php echo lang('atur_posisi'); ?></button>
		<?php } ?>
		<?php if($menu_access['access_input'] && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
			<button type="button" class="btn btn-primary btn-sm btn-input" data-id="0"><i class="fa-plus"></i><?php echo lang('tambah'); ?></button>
		<?php } ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<?php
	table_open('',true);
		thead();
			tr();
				th(lang('nama_menu'));
				th(lang('target'));
				th(lang('jalan_pintas'),'text-center','width="120px"');
				th('#','text-center','width="50px"');
				th(lang('aktif').'?','text-center','width="100px"');
				th('&nbsp;','','width="30"');
		tbody();
	table_close();
	?>
</div>
<?php 
	modal_open('modal-form','','modal-lg');
		modal_body();
			form_open(base_url('settings/menu/save'),'post','form');
				col_init(3,9);
				input('hidden','id','id');
				select2(lang('sub_dari'),'parent_id');
				input('text',lang('nama_menu'),'nama','required|min-length:3');
				input('text',lang('target'),'target','required|min-length:3|unique');
				checkbox_group(lang('akses'));
					checkbox(lang('lihat'),'akses_view',1, 'disabled checked');
					checkbox(lang('tambah'),'akses_input',1);
					checkbox(lang('ubah'),'akses_edit',1);
					checkbox(lang('hapus'),'akses_delete',1);
					checkbox(lang('tambahan'),'akses_additional',1);
				label(lang('alias_akses'));
				sub_open(1);
					input('text',lang('tambah'),'alias_input');
					input('text',lang('ubah'),'alias_edit');
					input('text',lang('hapus'),'alias_delete');
					input('text',lang('tambahan'),'alias_additional');
				sub_close();
				col_init(3,3);
				input('icon',lang('ikon'),'icon','required');
				input('text',lang('urutan'),'urutan','required|number');
				inputgroup_open(lang('jalan_pintas'));
					select(lang('jalan_pintas'),'shortcut_key','',array('Alt+','Alt+Shift+'));
					input('text',lang('jalan_pintas'),'shortcut','max-length:1|alphanumeric|unique');
				inputgroup_close();
				toggle(lang('aktif').'?','is_active');
				form_button(lang('simpan'),lang('batal'));
			form_close();
		modal_footer();
	modal_close();
	modal_open('modal-sort',lang('atur_posisi'),'modal-lg','modal-info');
		modal_body();
		modal_footer();
			echo '<form><button type="submit" class="btn btn-success" id="save-posisi">'.lang('simpan').'</button></form>';
	modal_close();
?>
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.sortable.min.js'); ?>"></script>
<script type="text/javascript">
	function getData() {
		cLoader.open(lang.memuat_data + '...');
		$.ajax({
			url 	: base_url + 'settings/menu/data',
			data 	: {},
			type	: 'get',
			dataType: 'json',
			success	: function(response) {
				$('.table-app tbody').html(response.table);
				$('#parent_id').html(response.option);
				cLoader.close();
				fixedTable();
				var item_act	= {};
				if($('.table-app tbody .btn-input').length > 0) {
					item_act['active'] 		= {name : lang.aktif, icon : "toggle-on"};
					item_act['inactive'] 	= {name : lang.tidak_aktif, icon : "toggle-off"};
					item_act["sep2"] 		= "---------";
					item_act['edit'] 		= {name : lang.ubah, icon : "edit"};					
				}
				if($('.table-app tbody .btn-input').length > 0) {
					item_act['delete'] 		= {name : lang.hapus, icon : "delete"};					
				}
				var act_count = 0;
				for (var c in item_act) {
					act_count = act_count + 1;
				}
				if(act_count > 0) {
					$.contextMenu({
				        selector: '.table-app tbody tr', 
				        callback: function(key, options) {
				        	if($(this).find('[data-key="'+key+'"]').length > 0) {
					        	if(typeof $(this).find('[data-key="'+key+'"]').attr('href') != 'undefined') {
					        		window.location = $(this).find('[data-key="'+key+'"]').attr('href');
					        	} else {
						        	$(this).find('[data-key="'+key+'"]').trigger('click');
						        }
						    } else if(key == 'active') {
						    	var data_id = $(this).find('.btn-input').attr('data-id');
						    	if(typeof active_inactive  === 'function') {
						    		active_inactive(data_id,'1');
						    	} else {
						    		cAlert.open(lang.fungsi_aktif_tidak_tersedia);
						    	}
						    } else if(key == 'inactive') {
						    	var data_id = $(this).find('.btn-input').attr('data-id');
						    	if(typeof active_inactive  === 'function') {
						    		active_inactive(data_id,'0');
						    	} else {
						    		cAlert.open(lang.fungsi_tidak_aktif_tidak_tersedia);
						    	}
						    }
				        },
				        items: item_act
				    });
				}
			}
		});
	}
	$(function(){
		getData();
	});
	$('.btn-sort').click(function(){
		$('#modal-sort .modal-body').html('');
		$.ajax({
			url : base_url + 'settings/menu/data/sortable',
			type : 'get',
			dataType : 'json',
			success : function(response) {
				$('#modal-sort .modal-body').html(response.content);
				$('#modal-sort').modal();
				$('ol.sortable').nestedSortable({
					forcePlaceholderSize: true,
					handle: 'div',
					helper:	'clone',
					items: 'li',
					opacity: .6,
					placeholder: 'placeholder',
					revert: 250,
					tabSize: 25,
					tolerance: 'pointer',
					toleranceElement: '> div',
					maxLevels: 4,
					isTree: true,
					expandOnHover: 700,
					isAllowed: function(item, parent, dragItem) {
						var x = true;
						if(dragItem.hasClass('module')) {
							if(typeof parent != 'undefined') x = false;
						} else {
							if(typeof parent == 'undefined') x = false;
							if(x && parent.closest('.module').attr('data-module') != dragItem.attr('data-module')) x = false;
						}
						return x;
					}
				});
			}
		});
	});
	$(document).on('dblclick','.table-app tbody td .badge',function(){
		if($(this).closest('tr').find('.btn-input').length == 1) {
			var badge_status 	= '0';
			var data_id 		= $(this).closest('tr').find('.btn-input').attr('data-id');
			if( $(this).hasClass('badge-danger') ) {
				badge_status = '1';
			}
			active_inactive(data_id,badge_status);
		}
	});
	$('#save-posisi').click(function(e){
		e.preventDefault();
		var serialized = $('ol.sortable').nestedSortable('serialize');
		$.ajax({
			url : base_url + 'settings/menu/save_sortable',
			type : 'post',
			data : serialized,
			dataType : 'json',
			success : function(response) {
				if(response.status == 'success') {
					cAlert.open(response.message,response.status,'refreshData');
				} else {
					cAlert.open(response.message,response.status);
				}
			}
		});
	});
</script>