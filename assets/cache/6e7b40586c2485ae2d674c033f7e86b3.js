
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
