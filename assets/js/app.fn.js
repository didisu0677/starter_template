var encode_key			= '1234567890987654321234567890';
var proccess 			= true;
var serverside 			= null;
var readonly_ajax 		= true;
var is_autocomplete		= false;
var id_user_login		= 0;
var id_group_login		= 0;
var websocket_active	= false;

$(document).ready(function(){
	if($('.wizard [data-editor]').length > 0) {
		$.fn.modal.Constructor.prototype._enforceFocus = function() {
			var _this4 = this;
			$(document).off('focusin.bs.modal').on('focusin.bs.modal', $.proxy((function(event) {
				if (
					document !== event.target
					&& _this4._element !== event.target
					&& $(_this4._element).has(event.target).length === 0
					&& !$(event.target.parentNode).hasClass('cke_dialog_ui_input_select')
					&& !$(event.target.parentNode).hasClass('cke_dialog_ui_input_text')
				) {
					_this4._element.focus();
				}
			}), this));
		};
	}
});
function decodeEntities(encodedString) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = encodedString;
    return textArea.value;
}
function validation(e) {
	if(typeof e == 'undefined') e = 'form';
	var i 				= 0;
	var valid 			= true;
	var f 				= '';
	$('#' + e).find('[data-validation]:enabled').each(function(){
		var $t 			= $(this);
		var validate	= $t.attr('data-validation');
		var spl 		= validate.split('|');
		var m 			= '';
		if($(this).closest('.hidden').length == 0) {
			$.each(spl,function(i,d){
				var v 		= d.split(':');
				var t 		= v[0];
				var l 		= typeof v[1] == 'undefined' ? 0 : parseInt(v[1]);
				var n 		= 'Field ini';
				var vl 		= $t.val();
				var int_v 	= vl == null || $.isArray(vl) ? '' : vl.replace('.','').replace(',','.');
				var int_val	= parseFloat(int_v);
				if (isNaN(l)) l = v[1];
				if(typeof $t.attr('aria-label') == 'undefined') {
					if(typeof $t.attr('placeholder') == 'undefined' || $t.hasClass('dp') || $t.hasClass('drp') || $().hasClass('dtp')) {
						if(typeof $t.attr('id') != 'undefined' && typeof $('[for="' + $t.attr('id') + '"]').text() != 'undefined') {
							n 		= $('[for="' + $t.attr('id') + '"]').text();
						} 
						if($t.closest('.form-group').children('label').length == 1 && (n == '' || n == 'Field ini')) {
							n 		= $t.closest('.form-group').children('label').text();
						}
					} else {
						n = $t.attr('placeholder');
					}
				} else {
					n = $t.attr('aria-label');
				}
				var req_val = typeof $t.val() == 'string' ? $t.val().trim() : $t.val();
				if(t == 'required' && (($t.val() != null && req_val.length == 0) || $t.val() == null) && m == '') {
					m 		= n + ' ' + lang.harus_diisi;
				}
				else if(t == 'strong_password' && m == '' && ($t.val() != null && $t.val().length != 0)) {
					var re 	= /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])/;
					if(!re.test($t.val())) {
						m 	= n + ' ' + lang.msg_strong_password;
					}
				}
				else if(t == 'email' && m == '' && ($t.val() != null && $t.val().length != 0)) {
					var re 	= /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					if(!re.test($t.val())) {
						m 	= n + ' ' + lang.harus_diisi_format_email + ' (ex@email.xx)';
					}
				}
				else if(t == 'phone' && m == '' && ($t.val() != null && $t.val().length != 0)) {
					var re = /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/;
					if(!re.test($t.val())) {
						m 	= n + ' ' + lang.harus_diisi_format_nomor_telepon;
					}
				}
				else if(t == 'number' && m == '' && ($t.val() != null && $t.val().length != 0)) {
					var re 	= /^[0-9.,]+$/;
					if(!re.test($t.val())) {
						m 	= n + ' ' + lang.harus_diisi_format_angka;
					}
				}
				else if(t == 'letter' && m == '' && ($t.val() != null && $t.val().length != 0)) {
					var re = /^[a-zA-Z]+$/;
					if(!re.test($t.val())) {
						m 	= n + ' ' + lang.harus_diisi_format_huruf;
					}
				}
				else if(t == 'alphanumeric' && m == '' && ($t.val() != null && $t.val().length != 0)) {
					var re = /^[0-9a-zA-Z]+$/;
					if(!re.test($t.val())) {
						m 	= n + ' ' + lang.harus_diisi_format_huruf_atau_angka;
					}
				}
				else if(t == 'length' && $t.val().length != l && m == '' && ($t.val() != null && $t.val().length != 0)) {
					m 		= n + ' ' + lang.harus + ' ' + l + ' ' + lang.karakter;
				}
				else if(t == 'min-length' && $t.val().length < l && m == '' && ($t.val() != null && $t.val().length != 0)) {
					m 		= n + ' ' + lang.minimal + ' ' + l + ' ' + lang.karakter;
				}
				else if(t == 'max-length' && $t.val().length > l && m == '' && ($t.val() != null && $t.val().length != 0)) {
					m 		= n + ' ' + lang.maksimal + ' ' + l + ' ' + lang.karakter;
				}
				else if(t == 'equal' && m == '' && $t.closest('form').find('[name="'+l+'"]').length == 1 && $t.val() != $t.closest('form').find('[name="'+l+'"]').val() ) {
					if(typeof $t.closest('form').find('[name="'+l+'"]').closest('.form-group').attr('class') != 'undefined') {
						m 		= n + ' ' + lang.tidak_cocok_dengan + ' ' + $t.closest('form').find('[name="'+l+'"]').closest('.form-group').children('label').text();
					} else if(typeof $t.closest('form').find('[name="'+l+'"]').attr('aria-label') != 'undefined'){
						m 		= n + ' ' + lang.tidak_cocok_dengan + ' ' + $t.closest('form').find('[name="'+l+'"]').attr('aria-label');
					} else if(typeof $('label[for="'+l+'"]').attr('for') != 'undefined') {
						m 		= n + ' ' + lang.tidak_cocok_dengan + ' ' + $('label[for="'+l+'"]').text();
					} else {
						m 		= n + ' ' + lang.tidak_cocok_dengan + ' ' + l;					
					}
				}
				else if(t == 'min' && !isNaN(int_val) && int_val < l) {
					m 		= n + ' ' + lang.tidak_boleh_kurang_dari + ' ' + l;
				}
				else if(t == 'max' && !isNaN(int_val) && int_val > l) {
					m 		= n + ' ' + lang.tidak_boleh_lebih_dari + ' ' + l;
				}
			});
			if(m) {
				valid 		= false;
				if($t.parent().hasClass('input-group')) {
					if($t.parent().parent().find('span.error').length == 0) {
						$t.addClass('is-invalid');
						$t.parent().parent().append('<span class="error">' + m + '</span>');
					}
				} else {
					if($t.parent().find('span.error').length == 0) {
						$t.addClass('is-invalid');
						$t.parent().append('<span class="error">' + m + '</span>');
						if($t.parent().children('span.select2').length == 1) {
							$t.parent().children('span.select2').addClass('is-invalid');
						}
					}
				}
			}
		}
	});
	if(!valid) {
		$('.is-invalid').first().focus();
		return false;
	} else {
		return true;
	}
}
function rand() {
	return Math.floor(Math.random() * (9999 - 1000 + 1)) + 100;
}
function reload() {
	window.location.reload();
}
function numberFormat(e, c, d, t, z){
	var n = e, 
	c = isNaN(c = Math.abs(c)) ? 2 : c, 
	d = d == undefined ? "." : d, 
	t = t == undefined ? "," : t, 
	s = n < 0 ? "-" : "", 
	i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
	j = (j = i.length) > 3 ? j % 3 : 0;
	x = z == 'negatif' ? false : true;
	var result = s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	if(s == '-' && x == true) {
		result = '(' + result.replace('-','') + ')';
	}
	return result;
}
function customFormat(str) {
	return numberFormat(str,0,'.','.','negatif');
}
// function customFormat(str,comma) {
// 	if(typeof comma != undefined) comma = 2;
// 	else comma = 0;
// 	return numberFormat(str,comma,',','.');
// }
function moneyToNumber(str) {
	if(typeof str == 'undefined') {
		return 0;
	} else {
		if(str == '') {
			return 0;
		} else {
			return parseFloat(str.replace(/\./g,'').replace(/\,/g,'.'));
		}
	}
}
function toNumber(str) {
	if(typeof str != 'undefined') {
		str = str.replace(',','.');
		if(str == '' || isNaN(str)) {
			return 0;
		} else {
			return parseFloat(str);
		}
	} else {
		return 0;
	}
}
function curDate() {
	var d = new Date,
	dformat = [d.getFullYear(),
		      (d.getMonth()+1).padLeft(),
               d.getDate().padLeft()].join('-') +' ' +
              [d.getHours().padLeft(),
               d.getMinutes().padLeft(),
			   d.getSeconds().padLeft()].join(':');
	return dformat;
}
Number.prototype.padLeft = function(base,chr){
    var  len = (String(base || 10).length - String(this).length)+1;
    return len > 0? new Array(len).join(chr || '0')+this : this;
}
function cDate(e,r_sec) {
	var dt = e;
	if(e.length == 10) {
		if(e == '0000-00-00') {
			dt = '';
		} else {
			var x = dt.split('-');
			if(x.length == 3) {
				dt = x[2]+'/'+x[1]+'/'+x[0];
			}
		}
	} else if(e.length == 19){
		if(e == '0000-00-00 00:00:00') {
			dt = '';
		} else {
			var x = dt.split(' ');
			if(x.length == 2) {
				var y = x[0].split('-');
				if(y.length == 3) {
					if(typeof r_sec != 'undefined' && r_sec == true) {
						tt = x[1].split(':');
						dt = y[2]+'/'+y[1]+'/'+y[0]+' '+tt[0]+':'+tt[1];
					} else {
						dt = y[2]+'/'+y[1]+'/'+y[0]+' '+x[1];
					}
				}
			}
		}
	}
	return dt;
}
function cPercent(str) {
	var x = str.split('.');
	var result = '';
	if(x.length == 2) {
		if(parseInt(x[1]) == 0) {
			result = x[0];
		} else {
			result = str.replace('.',',');
		}
	}
	return result;
}
function encodeId(e) {
	var hashids_init = new Hashids(encode_key);
	var id = parseInt(e);	
	var encode_id = hashids_init.encode(id, Math.floor(Math.random() * (9999 - 1000 + 1)) + 100 );
	return encode_id;
}
function decodeId(e) {
	var hashids_init = new Hashids(encode_key);
	var decode_id = hashids_init.decode(e);
	return decode_id;
}
$(function(){
	var user_login = decodeId(user_key);
	if(user_login.length == 3) {
		id_user_login	= user_login[0];
		id_group_login	= user_login[1];
	}
});
function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}