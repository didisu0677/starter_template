var _ajax_last_uri = '';
var xhr_delete = null;
var xhr_save = null;
function ellipsisLoader() {
	return '<div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>';
}
$.ajaxSetup({
    beforeSend: function(xhr, settings) {
		var _url = settings.url.split('/');
		_ajax_last_uri = _url[_url.length - 1];
		if(_ajax_last_uri == '') _ajax_last_uri = _url[_url.length - 2];
        if (typeof $('meta[name="csrf-token"]').attr('content') != 'undefined') {
			var hashids = new Hashids(encode_key);
			var x = hashids.decode($('meta[name="csrf-token"]').attr('content'));
            if (x.length == 3) {
                xhr.setRequestHeader("X-CSRF-Token", x[0])
            }
		}
		if(settings.type == 'POST') {
			if(!is_autocomplete) {
				$('[type="submit"],[type="reset"]').attr('disabled', true);
			}
			if(readonly_ajax && !is_autocomplete) {
				$('form :input').attr('readonly', true)
			}
		}
    },
    complete: function(xhr, stat) {
		$('[type="submit"],[type="reset"]').removeAttr('disabled');
        $('form :input').removeAttr('readonly');
        $('[data-readonly],[data-edit-readonly]').attr('readonly',true);
		if($('[data-editor]').length > 0 && typeof window.CKEDITOR != 'undefined' && _ajax_last_uri != 'data' && _ajax_last_uri != 'check_table') {
			$('[data-editor]').each(function(){
				if(typeof CKEDITOR.instances[$(this).attr('id')] != 'undefined') {
					CKEDITOR.instances[$(this).attr('id')].setReadOnly(false);
				}
			});
		}
		readonly_ajax = true;
		xhr_delete = null;
		xhr_save = null;
    },
    error: function(jqXHR, textStatus, errorThrown) {
		var input_active = $('.btn-input .fa-spin');
		input_active.attr('class',input_active.attr('data-class')).removeAttr('data-class');
        if (jqXHR.status) {
            if (jqXHR.status == '200') {
                cAlert.open(lang.respon_data_tidak_valid, 'error');
            } else if (jqXHR.status == '500') {
                cAlert.open(lang.kesalahan_server_dari_dalam, 'error');
            } else if (jqXHR.status == '403') {
                cAlert.open(lang.akses_terlarang, 'error');
            } else if (jqXHR.status == '404') {
                cAlert.open(lang.halaman_tidak_ditemukan, 'error');
            } else {
                cAlert.open(lang.kesalahan_tidak_diketahui + ' (Err:' + jqXHR.status + ')', 'error');
			}
        }
		cLoader.close();
		cLoading.close();
		xhr_delete = null;
		xhr_save = null;
    }
});
var cAlert = cAlert || (function($) {
    'use strict';
    return {
        open: function(message, status, onClick) {
            if (typeof message === 'undefined') {
                message = 'Halo Dunia';
            }
            var title = lang.pemberitahuan;
            var type = 'info';
            var button = 'btn-info';
            if (typeof status !== 'undefined') {
                if (status == 'error') {
                    title = lang.kesalahan;
                    type = 'error';
                    button = 'btn-danger';
                } else if (status == 'failed') {
                    title = lang.gagal;
                    type = 'error';
                    button = 'btn-danger';
                } else if (status == 'success') {
                    title = lang.berhasil;
                    type = 'success';
                    button = 'btn-success';
                }
            }
            swal({
            	title: title,
            	text: message,
            	icon: type,
            	button: lang.ok,
            	closeOnClickOutside: false,
            	closeOnEsc: false,
            }).then((value) => {
	            if (typeof onClick !== 'undefined') {
                    var act = window[onClick];
                    act();
	            }
            });
        }
    };
})(jQuery);
var cConfirm = cConfirm || (function($) {
    'use strict';
    return {
        open: function(message, onConfirm, tipe) {
            if (typeof message === 'undefined') {
                message = lang.apakah_anda_yakin + '?';
            }
            var title = lang.konfirmasi;
            var type = 'question';
			if (typeof tipe !== 'undefined') type = tipe;
			if (tipe == 'warning') title = lang.peringatan;
            swal({
            	title: title,
            	text: message,
            	icon: type,
            	buttons: {
            		cancel: lang.batalkan,
            		catch: {
            			text: lang.lanjutkan,
            			value: "catch",
            			closeModal: false
            		}
            	},
            	closeOnClickOutside: false,
            	closeOnEsc: false,
            }).then((value) => {
	            if(value == "catch") {
	            	$('.swal-button--cancel,.swal-button--catch').attr('disabled',true);
	            	setTimeout(function () {
			            if (typeof onConfirm !== 'undefined') {
		                    var act = window[onConfirm];
		                    act();
			            } else {
			            	cAlert.open(lang.lanjutkan,'success');
			            }
			        }, 1000);
	            } else {
	            	swal.close();
	            }
            });
        },
        close: function() {
        	swal.close();
        }
    };
})(jQuery);
var cInfo = cInfo || (function($) {
    'use strict';
    var $dialog = $('<div class="modal modal-info fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title"></h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">'+lang.tutup+'</button></div></div></div></div>');
    return {
        open: function(title, message, options) {
            if (typeof options === 'undefined') {
                options = {};
            }
            if (typeof title === 'undefined') {
                message = 'Title';
            }
            if (typeof message === 'undefined') {
                message = lang.memuat;
            }
            var settings = $.extend({
                onHide: null,
                modal_lg: true
            }, options);
            $dialog.find('.modal-body').html(message);
            $dialog.find('.modal-header').children('h5').text(title);
            if (typeof settings.modal_lg !== 'undefined' && settings.modal_lg == false) {
                $dialog.find('.modal-lg').removeClass('modal-lg');
            }
            if (typeof settings.headerbg !== 'undefined') {
            	if(settings.headerbg == false) {
            		$dialog.find('.modal-header').removeAttr('style');
            	} else {
	                $dialog.find('.modal-header').css({'background' : settings.headerbg});
	            }
            } else {
        		$dialog.find('.modal-header').removeAttr('style');            	
            }
            if (typeof settings.modal_footer !== 'undefined' && settings.modal_footer == false) {
                $dialog.find('.modal-footer').remove();
            }
            if (typeof settings.onHide !== 'undefined') {
                $dialog.off('hidden.bs.modal').on('hidden.bs.modal', function(e) {
                    var act = window[settings.onHide];
                    if(typeof act == 'function') {
	                    act();
	                }
                });
            }
            $dialog.off('shown.bs.modal').on('shown.bs.modal', function() {
                $dialog.find('.btn').focus()
            });
            $dialog.modal();
            $dialog.on('shown.bs.modal',function(){
            	if($('.modal-backdrop').length > 1) {
            		$('.modal-backdrop').last().css({'z-index' : '1055'});
            	}
            });
        }
    };
})(jQuery);
var cLoading = cLoading || (function($) {
    'use strict';
    var $dialog = $('<div class="overlay"><div class="loading"><div class="position-relative"><div class="spinner"></div><div class="text"></div></div></div></div>');
    return {
        open: function(message) {
            if (typeof message === 'undefined') {
                message = lang.memuat + '...';
            }
            $dialog.find('.text').html(message);
            $('body').append($dialog);
        },
        close: function() {
            if ($('.overlay .loading').length > 0) {
                $('.overlay').remove();
            }
        }
    };
})(jQuery);
var cLoader = cLoader || (function($) {
    'use strict';
    var $dialog = $('<div class="loader"><div class="position-relative"><div class="spinner"></div><div class="text"></div></div></div>');
    return {
        open: function(message) {
            if (typeof message === 'undefined') {
                message = lang.memuat + '...';
            }
            $dialog.find('.text').html(message);
            $('body').append($dialog);
        },
        close: function() {
            if ($('.loader').length > 0) {
                $('.loader').remove();
            }
        }
    };
})(jQuery);
$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
	if (!$(this).next().hasClass('show')) {
		$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
	}
	var $subMenu = $(this).next(".dropdown-menu");
	$subMenu.toggleClass('show');
	$(this).toggleClass('show');
	$(this).parents('li.nav-item.show').on('hidden.bs.dropdown', function(e) {
		$('.dropdown-submenu .show').removeClass("show");
	});
	return false;
});
$('.dropdown-menu a').on('mouseenter', function(e) {
	if($(this).hasClass('dropdown-toggle')) {
		if (!$(this).next().hasClass('show')) {
			$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
		}
		var $subMenu = $(this).next(".dropdown-menu");
		$subMenu.toggleClass('show');
		$(this).toggleClass('show');
		$(this).parents('li.nav-item.show').on('hidden.bs.dropdown', function(e) {
			$('.dropdown-submenu .show').removeClass("show");
		});
	} else {
		$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
	}
	return false;
});
$.sidebarMenu = function(menu) {
	var animationSpeed = 300;
	
	$(menu).on('click', 'li a', function(e) {
		if(!$(this).closest('.sidebar-menu').hasClass('disabled-collapse') || parseInt($(window).width()) < 768 ) {
			var $this = $(this);
			var checkElement = $this.next();

			if (checkElement.is('.treeview-menu') && checkElement.is(':visible')) {
				checkElement.attr('style','display: block');
				checkElement.slideUp(animationSpeed, function() {
					checkElement.removeClass('menu-open');
				});
				checkElement.parent("li").removeClass("active");
			}

			else if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
				var parent = $this.parents('ul').first();
				var ul = parent.find('ul:visible').slideUp(animationSpeed);
				ul.removeClass('menu-open');
				var parent_li = $this.parent("li");
				parent_li.addClass('open');

				checkElement.slideDown(animationSpeed, function() {
					parent.find('li.active').removeClass('active');
					parent_li.addClass('active').removeClass('open');
					checkElement.addClass('menu-open');
					checkElement.removeAttr('style');					
				});
			}
			if (checkElement.is('.treeview-menu')) {
				e.preventDefault();
			}
		} else {
			if($(this).parent().children('ul').length > 0) {
				return false;
			}
		}
	});
}
function modalInputFocus(e) {
	if( $(e).find('form').find(':input:visible').length > 0) {
		var inputs = $(e).find('form').find(':input:not([disabled]):visible');
		inputs.eq( inputs.index(this)+ 1 ).focus();
	}
}
function check_mobile() {
	if($(window).width() <= 767)  {
		$('#navbar-mobile').addClass('collapse');
	} else {
		$('#navbar-mobile').removeClass('collapse');
		$('.sidebar-panel').removeClass('active');
		$('.sidebar-overlay').remove();
	}
}
function generate_mobile_header_tab() {
	var item = '';
	if($('.content-header .nav-tabs .nav-item').length > 1) {
		$('.content-header .nav-tabs .nav-item').each(function(){
			var cls = ' class="dropdown-item"';
			if($(this).find('.nav-link').hasClass('active')) {
				var cls = ' class="dropdown-item active"';
			}
			var href = ' href="'+$(this).find('.nav-link').attr('href')+'"';
			item += '<a' + cls + href + '>' + $(this).find('.nav-link').text() + '</a>';
		});
		var add_item = '';
		if(item != '') {
			add_item += '<div class="btn-group btn-group-sm btn-substitute btn-mobile btn-alias" id="tabs-alias" role="group">';
			add_item += '<button type="button" class="btn btn-default dropdown-toggle btn-icon-only" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-bars"></i></button>';
			add_item += '<div class="dropdown-menu">';
			add_item += item;
			add_item += '</div></div>';
		}
		$('.content-action .float-right').append(add_item);
	}
}
function menuMore() {
	if($(window).width() < 768) {
		var notif_count = $('.dropdown-notification .tag.tag-pill.tag-up').length == 1 ? $('.dropdown-notification .tag.tag-pill.tag-up').text() : '';
		if( notif_count != '' ) {
			notif_count = '<span class="tag tag-pill tag-up">'+notif_count+'</span>';
		}
		konten = '<ul class="more-menu">';
		konten += '<li><a href="'+base_url +'home/notification"><i class="fa-bell"></i> '+lang.pemberitahuan+notif_count+'</a></li>';
		konten += '<li class="info-user">'+$('.dropdown-user-link').html()+'</li>';
		$('.dropdown-user .dropdown-menu a').each(function(){
			konten += '<li><a href="'+$(this).attr('href')+'">'+$(this).html()+'</a></li>';
		});
		konten += '</ul>';
		if($('.more-menu').length == 0) {
			$('body').append(konten);
			const ps_menu_more = new PerfectScrollbar('.more-menu');
		}
	} else {
		$('.more-menu').remove();
	}
}
function closeModal() {
	if($('.modal.show').length > 0) {
		$('.modal').modal('hide');
	}
}
function refreshData() {
	closeModal();
	if($('table[data-serverside]').length > 0) {
		serverside.refresh();
	} else if(typeof getData === 'function') {
		getData();
	} else {
		location.reload();
	}
	$('table input[type="checkbox"]').prop('checked', false).prop('indeterminate', false);
}
function gridTable() {	
	if($('.table-grid').length == 1) {	
		if($(window).width() < 768) {	
			var h = $(window).height();	
			var t = $('nav.navbar').outerHeight();	
			var u = $('.content-header').outerHeight();	
			var toleransi = 0;
			$('.table-grid').parent().next().each(function(){
				toleransi += $(this).outerHeight();
			});
			toleransi += parseInt($('.table-grid').parent().parent().css('padding-bottom').replace('px',''));
			toleransi += parseInt($('.table-grid').parent().parent().css('padding-top').replace('px',''));
			var grid_h = h - (t + u + toleransi);	
			$('.table-grid').parent().css({'max-height':grid_h+'px'});	
		} else {	
			var x = $('.table-grid').parent().offset().top;	
			var h = $(window).height();	
			var t = $('nav.navbar').outerHeight();	
			var u = $('.content-header').outerHeight();	
			var toleransi = 0;
			$('.table-grid').parent().next().each(function(){
				toleransi += $(this).outerHeight();
			});
			toleransi += parseInt($('.table-grid').parent().parent().css('padding-bottom').replace('px',''));
			toleransi += parseInt($('.table-grid').parent().parent().css('padding-top').replace('px',''));
			var grid_h = h - (x + toleransi);
			$('.table-grid').parent().css({'max-height':grid_h+'px'});	
		}	
		$('.table-grid').children('thead').addClass('sticky-top');	
	}	
}
$('#nav-menu-more').click(function(e){
	e.preventDefault();
	$('.more-menu').addClass('show');
	$('#sidebar-panel').removeClass('active');
	$('.sidebar-overlay').remove();
	$('body').append('<div class="menu-overlay"></div>');
});
$(document).on('click','.menu-overlay',function(){
	$('.more-menu').removeClass('show');
	$(this).remove();
});
$(document).on('click','.menu-overlay',function(){
	$('.more-menu').removeClass('show');
	$(this).remove();
});
$(document).on('click','.sidebar-overlay',function(){
	$('.sidebar-panel').removeClass('active');
	$(this).remove();
});
$(document).on('change','[data-validation]',function(){
	if($(this).hasClass('is-invalid') && $(this).val() != '') {
		$(this).removeClass('is-invalid');
		if($(this).parent().hasClass('input-group')) {
			$(this).parent().parent().find('span.error').remove();
		} else {
			$(this).parent().find('span.error').remove();
			if($(this).parent().children('span.select2').length == 1) {
				$(this).parent().children('span.select2').removeClass('is-invalid');
			}
		}
	}
});
$(document).on('keyup','[data-validation]',function(){
	$(this).trigger('change');
});
$(document).on('focus',':input[readonly]',function(){
	if($(this).next().find(':input').length > 0) {
		$(this).next().find(':input:first').focus();
	} else {
		if($(this).closest('.form-group').nextAll('.form-group:first').length > 0) {
			$(this).closest('.form-group').nextAll('.form-group:first').find(':input:first').focus();
		} else {
			if($(this).closest('.card').length == 1 && $(this).closest('.card').nextAll(':first').find(':input:first').length > 0) {
				$(this).closest('.card').nextAll(':first').find(':input:first').focus();
			} else {
				$(this).blur();
			}
		}
	}
});
$(document).on('click','a.disabled,a[disabled],a[data-disabled]',function(e){
	e.preventDefault();
});
$(function(){
	if($('.floating-alert').length == 1) {
		$('.floating-alert').css({'top':'50px'});
		setTimeout(function(){
			$('.floating-alert').css({'top':'-300px'});
			setTimeout(function(){
				$('.floating-alert').remove();
			},1000);
		},3000);
	}
	if($.cookie('menu-minimize') == 'minimize') {
		$('body').addClass('body-minimize');
		$('.main-menu').addClass('disabled-collapse');
	}
	if(typeof $('#sidebar-panel').attr('data-pos') != 'undefined') {
		setTimeout(function(){
			$('#sidebar-panel').scrollTop(parseInt($('#sidebar-panel').attr('data-pos')));
		},300);
		$.removeCookie('sidebar_pos', { path: '/' });
	}
	if($('.filter-panel').length > 0 && $('.filter-header').length == 0) {
		$('.filter-panel').prepend('<div class="filter-header">'+lang.filter+'<button type="button" class="filter-close btn-filter-panel-hide">&times;</button></div>');
	}
	if($('.filter-panel').length > 0 && $('.filter-header').length > 0 && $('.filter-close').length == 0) {
		$('.filter-header').append('<button type="button" class="filter-close btn-filter-panel-hide">&times;</button>');
	}
	if($('.filter-panel').length > 0 && $('.content-header').length > 0 && $('.content-header .float-right').length == 0) {
		$('<div class="float-right"></div>').insertAfter('.content-header .main-container .header-info');
	}
	if($('.filter-panel').length > 0) {
		$('.content-header .float-right').append('<button class="btn btn-info btn-sm btn-icon-only btn-filter-panel-show" type="button" title="'+lang.filter+'"><i class="fa-sliders-v"></i> '+lang.filter+'</button>');
		const ps_filter = new PerfectScrollbar('.filter-panel',{
			suppressScrollX : true
		});
	}
	$(document).on('click','.btn-filter-panel-show',function(){
		$('.filter-panel').addClass('active');
	});
	$(document).on('click','.btn-filter-panel-hide',function(){
		$('.filter-panel').removeClass('active');
	});
	$('[data-toggle="tooltip"],[data-tooltip]').tooltip();
	check_mobile();
	generate_mobile_header_tab();
	$.sidebarMenu($('.sidebar-menu'));
	$(window).resize(function(){
		check_mobile();
	});
	$('.navbar-header .menu-toggle').click(function(){
		$('.sidebar-panel').toggleClass('active');
		if($('.sidebar-panel').hasClass('active')) {
			$('body').append('<div class="sidebar-overlay"></div>');
		} else {
			$('.sidebar-overlay').remove();
		}
		return false;
	});
	$('.toggle-menu-bar .menu-toggle').click(function(){
		$($(this).attr('data-target')).toggleClass('show');
		return false;
	});
	var $body 	= $('body');
	var $window	= $(window);
	$('#btn-minimize').click(function(){
		if($body.hasClass('body-minimize')) {
			$body.removeClass('body-minimize');
			$('.main-menu').removeClass('disabled-collapse');
			$.removeCookie('menu-minimize', { path: '/' });
		} else {
			$body.addClass('body-minimize');
			$('.main-menu').addClass('disabled-collapse');
			$.cookie('menu-minimize', 'minimize', { path: '/' });
		}
		return false;
	});
	var hideFloatingMenu = false;
	var floatingMenu = '';
	$("#sidebar-panel").on("mouseenter.sidebar-menu", "li", function() {
		$('#sidebar-panel .ps__rail-y').addClass('hide-scroll');
		if($(this).parent().hasClass('main-menu') && $body.hasClass("body-minimize")) {
			if($('#sidebar-panel .floating-menu').length > 0) {
				$('#sidebar-panel .floating-menu').remove();
			}
			$('#sidebar-panel li').removeClass('hover');
			$(this).addClass('hover');
			if ($body.hasClass("body-minimize") && $window.width() > 767 && !$(this).hasClass('header')) {
				var fromTopOffset = 49;
				if($('.header-navbar').height() == 40) fromTopOffset = 40;
				var fromTop = ($(this).position().top + fromTopOffset) - parseInt($('#sidebar-panel').scrollTop());
				var maxHeigh = $window.height() - (fromTop + 60);
				maxHeigh = maxHeigh > 0 ? maxHeigh : 0;
				floatingMenu = '<div class="floating-menu" style="top:'+fromTop+'px;">';
				floatingMenu += '<div class="floating-title">'+$(this).find('span').first().text()+'</div>';
				if($(this).children('ul').length > 0) {
					floatingMenu += '<ul class="treeview-menu sidebar-menu" style="max-height: '+maxHeigh+'px">';
					floatingMenu += $(this).children('ul:first').html();
					floatingMenu += '</ul>';
				}
				floatingMenu += '</div>';
				$("#sidebar-panel .sidebar-container").append(floatingMenu);
				if($('.floating-menu .sidebar-menu').length > 0) {
					$.sidebarMenu($('.floating-menu .sidebar-menu'));
					const ps = new PerfectScrollbar('.floating-menu .sidebar-menu');
				}
			}
		}
	});
	$("#sidebar-panel").on("mouseleave",function(){
		$('#sidebar-panel .ps__rail-y').removeClass('hide-scroll');
		if($('#sidebar-panel .floating-menu').length > 0) {
			$('#sidebar-panel .floating-menu').remove();
		}
		$('#sidebar-panel li').removeClass('hover');
	});
	$(document).on('click','.cInfo',function(){
		var url = '';
		if ( typeof $(this).attr('href') != 'undefined' ) {
			url = $(this).attr('href');
		} else if( typeof $(this).attr('data-target') != 'undefined' ) {
			url = $(this).attr('data-target');
		}
		var modal_lg = true;
		if( typeof $(this).attr('data-smallmodal') != 'undefined' ) {
			modal_lg = false;
		}
		var title = $(this).text();
		if(typeof $(this).attr('aria-label') != 'undefined') {
			title = $(this).attr('aria-label');
		}
		if(url) {
			readonly_ajax = false;
			$.get(url,function(res){
				cInfo.open(title,res,{'modal_lg':modal_lg});
			});
		}
		return false;
	});
	$('.switch').click(function(){
		if(typeof $(this).children('input').attr('readonly') !== 'undefined') return false;
		return true;
	});
	$('.change-language').click(function(){
		if(!$(this).hasClass('current')) {
			$.ajax({
				url : base_url + 'account/change_language',
				data : {'lang':$(this).attr('data-value')},
				type : 'post',
				success : function() {
					location.reload();
				}
			});
		}
	});
	$('.custom-control-input').click(function(){
		if(typeof $(this).parent().children('input').attr('readonly') !== 'undefined') return false;
		return true;
	});

	menuMore();
	gridTable();
	$(window).resize(menuMore);	
	$(window).resize(gridTable);

	$('.modal').on('shown.bs.modal', function() {
		modalInputFocus(this);
		if($(this).find('[data-editor]').length > 0 && typeof window.CKEDITOR != 'undefined') {
			$(this).find('[data-editor]').each(function(){
				if(typeof CKEDITOR.instances[$(this).attr('id')] != 'undefined') {
					CKEDITOR.instances[$(this).attr('id')].setReadOnly(false);
				}
			});
		}
	});
	$('.modal [type="reset"]').click(function(){
		$(this).closest('.modal').modal('hide');
	});

	$(':input[data-prefix],:input[data-suffix],:input[data-append],:input[data-prepend]').each(function(){
		if(!$(this).hasClass('jq-range')) {
			$(this).wrap('<div class="input-group"></div>');
			if(typeof $(this).attr('data-prefix') != 'undefined' || typeof $(this).attr('data-prepend') != 'undefined') {
				var label = typeof $(this).attr('data-prefix') != 'undefined' ? $(this).attr('data-prefix') : $(this).attr('data-prepend');
				var icon_label = label.split('icon:');
				if(icon_label.length == 2) {
					label = '<i class="'+icon_label[1]+'"></i>';
				}
				$(this).parent().prepend('<div class="input-group-prepend"><span class="input-group-text">'+label+'</span></div>');
			}
			if(typeof $(this).attr('data-suffix') != 'undefined' || typeof $(this).attr('data-append') != 'undefined') {
				var label = typeof $(this).attr('data-suffix') != 'undefined' ? $(this).attr('data-suffix') : $(this).attr('data-append');
				var icon_label = label.split('icon:');
				if(icon_label.length == 2) {
					label = '<i class="'+icon_label[1]+'"></i>';
				}
				$(this).parent().append('<div class="input-group-append"><span class="input-group-text">'+label+'</span></div>');
			}
		}
	});

	$('form :input').each(function(){
		if(typeof $(this).attr('name') != 'undefined' && $(this).attr('name') != 'id' && typeof $(this).attr('disabled') == 'undefined') {
			var name = $(this).attr('name');
			var n = name.replace('[]','');
			var nn = n.split('[');
			n = nn[0];
			if($(this).closest('form').find('[name="field_'+n+'"]').length == 0) {
				if($(this).hasClass('dp')) {
					$(this).closest('form').prepend('<input type="hidden" name="field_'+n+'" value="date">');
				}if($(this).hasClass('dtp')) {
					$(this).closest('form').prepend('<input type="hidden" name="field_'+n+'" value="datetime">');
				} else {
					$(this).closest('form').prepend('<input type="hidden" name="field_'+n+'" value="1">');
				}
				if(typeof $(this).attr('data-validation') != 'undefined' && $(this).attr('data-validation') != '') {
					$(this).closest('form').prepend('<input type="hidden" name="validation_name_'+n+'" value="'+$(this).closest('.form-group').children('label').first().text()+'">');
					$(this).closest('form').prepend('<input type="hidden" name="validation_'+n+'" value="'+$(this).attr('data-validation')+'">');
				}
			}
		}
	});
	$('textarea[data-limit]').each(function(){
		$(this).parent().append('<div class="counter"><span class="count">'+$(this).val().length+'</span> / <span class="limit">'+$(this).attr('data-limit')+'</span> Karakter</div>');
	});
	$('textarea[data-limit]').on('keyup change',function(){
		var limit = parseInt($(this).parent().find('.limit').text());
		var length = parseInt($(this).val().length);
		if(length <= limit) {
			$(this).parent().find('.count').text(length);
		} else {
			$(this).val($(this).val().substring(0, limit));
			$(this).parent('div').find('.count').text(limit);
		}
		return true;
	});
	$('[readonly]').each(function(){
		$(this).attr('data-readonly',true);
	});
});
$(document).on('keypress','input.text-number',function(e){
	var wh 			= e.which;
	if (e.shiftKey) {
		if(wh == 0) return true;
	}
	if(e.metaKey || e.ctrlKey) {
		if(wh == 86 || wh == 118) {
			$(this)[0].onchange = function(){
				$(this)[0].value = $(this)[0].value.replace(/[^0-9]/g, '');
			}
		}
		return true;
	}
	if(wh == 0 || wh == 8 || wh == 13 || wh == 32 || wh == 46 || (48 <= wh && wh <= 57)) 
		return true;
	return false;
});

if ( window.self !== window.top ) {
    window.top.location.href=window.location.href;
}

$('body').on('hidden.bs.modal', function () {
	if($('.modal.show').length > 0) {
		$('body').addClass('modal-open');
	}
});

$('#sidebar-panel a').click(function(){
	if($('#sidebar-panel').length == 1) {
		$.cookie('sidebar_pos', $('#sidebar-panel').scrollTop(), { path: '/' });	
	} else {
		$.cookie('sidebar_pos', 0, { path: '/' });	
	}
});

$('#sidebar-panel a').click(function(){
	if($('#sidebar-panel').length == 1 && $('#sidebar-panel').scrollTop() > 0) {
		$.cookie('sidebar_pos', $('#sidebar-panel').scrollTop(), { path: '/' });	
	}
});
$(window).bind('beforeunload', function(){
	if($('#sidebar-panel').length == 1 && $('#sidebar-panel').scrollTop() > 0) {
		$.cookie('sidebar_pos', $('#sidebar-panel').scrollTop(), { path: '/' });	
	}
});