<?php defined('BASEPATH') OR exit('No direct script access allowed');

function breadcrumb($last='',$multiple_child=false) {
    $CI     = get_instance();
    $br     = array();
    $br[]   = array(
        'link'  => 'home',
        'title' => 'Home'
    );
	$module     = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : $CI->uri->segment(1);
	if($CI->session->userdata('as_access')) $module = $CI->session->userdata('as_access');
    $cur_menu   = get_data('tbl_menu','target',$module)->row();
    if(!isset($cur_menu->id) && $module == $CI->uri->segment(2)) {
    	$module = $CI->uri->segment(1);
	    $cur_menu   = get_data('tbl_menu','target',$module)->row();
    }
    if(isset($cur_menu->id)) {
        if($cur_menu->level2) {
            $menu_l1    = get_data('tbl_menu','id',$cur_menu->level1)->row();
            if(isset($menu_l1->id)) {
            	$module = $menu_l1->target;
                $br[]   = array(
                    'link'  => $module == $menu_l1->target ? $module : $module . '/' . $menu_l1->target,
                    'title' => lang($menu_l1->target,$menu_l1->nama)
                );
            }
        }
        if($cur_menu->level3) {
            $menu_l2    = get_data('tbl_menu','id',$cur_menu->level2)->row();
            if(isset($menu_l2->id)) {
                $br[]   = array(
                    'link'  => $module == $menu_l2->target ? $module : $module . '/' . $menu_l2->target,
                    'title' => lang($menu_l2->target,$menu_l2->nama)
                );
            }
        }
        if($cur_menu->level4) {
            $menu_l3    = get_data('tbl_menu','id',$cur_menu->level3)->row();
            if(isset($menu_l3->id)) {
                $br[]   = array(
                    'link'  => $module == $menu_l3->target ? $module : $module . '/' . $menu_l3->target,
                    'title' => lang($menu_l3->target,$menu_l3->nama)
                );
            }
		}
		$show_last_br = true;
		if($CI->session->userdata('as_access')) {
			$access = get_data('tbl_user_akses',array(
				'where_array'   => array(
					'id_group'  => user('id_group'),
					'id_menu'   => $cur_menu->id
				)
			))->row();
			$show_last_br = isset($access->id) ? $access->act_view : 0;
		}

		if($show_last_br) {
			$br[]   = array(
				'link'  => $module == $cur_menu->target ? $module : $module . '/' . $cur_menu->target,
				'title' => lang($cur_menu->target,$cur_menu->nama)
			);
		}
    }
    if($last) {
    	if($multiple_child) {
	    	$last_breadcrumb = $CI->session->userdata('last_breadcrumb');
	    	$lastest = false;
	    	if(count($last_breadcrumb) > 0){
	    		foreach($last_breadcrumb as $kl => $l) {
	    			if($lastest) {
	    				unset($last_breadcrumb[$kl]);
	    			} else {
				        if($kl == uri_string()) {
				        	$lastest = true;
				        	unset($last_breadcrumb[$kl]);
				        } else {
					        $br[]   = array(
					            'link'  => $l['link'],
					            'title' => $l['title']
					        );
					    }
				    }
			    }
	    	}
	        if(!isset($last_breadcrumb['title'])) {
	        	$last_breadcrumb[uri_string()] = array(
		            'link'  => uri_string(),
		            'title' => $last
		        );
		        $CI->session->set_userdata('last_breadcrumb',$last_breadcrumb);
	        }
	    }
        $br[]   = array(
            'link'  => uri_string(),
            'title' => $last
        );
    } else {
        $CI->session->unset_userdata('last_breadcrumb');
    }
    $breadcrumb = '<nav aria-label="breadcrumb">' . PHP_EOL;
    $breadcrumb .= '<ol class="breadcrumb">' . PHP_EOL;
    foreach($br as $k => $b) {
        $title = $b['title'] == 'Home' ? '<i class="fa-home"></i>' : $b['title'];
        if($k == count($br) - 1) {
            $breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">'.$title.'</li>' . PHP_EOL;
        } else {
            $breadcrumb .= '<li class="breadcrumb-item"><a href="'.base_url($b['link']).'">'.$title.'</a></li>' . PHP_EOL;
        }
    }
    $breadcrumb .= '</ol>' . PHP_EOL;
    $breadcrumb .= '</nav>' . PHP_EOL;
    return $breadcrumb;
}

function access_button($button='',$link_add='') {
	$delete 	= false;
	$active		= false;
	$inactive 	= false;
	$export		= false;
	$import		= false;
	$input 		= false;
	$setting	= false;
	$menu 		= menu('access');
	$link		= str_replace('index','',current_url());
	if(substr($link, -1) != '/') $link .= '/';
	if($menu['access_input']) $input = true;
	if($button) {
		$exp = explode(',', $button);
		foreach($exp as $e) {
			if($e == 'delete' && $menu['access_delete']) $delete = true;
			else if($e == 'active' && $menu['access_edit']) $active = true;
			else if($e == 'inactive' && $menu['access_edit']) $inactive = true;
			else if($e == 'export') $export = true;
			else if($e == 'import' && $menu['access_input']) $import = true;
			else if($e == 'setting') $setting = true;
		}
	}
	$a_button = '';
	if(is_array($link_add) && count($link_add) > 0) {
		$additional	= '';
		foreach($link_add as $l) {
			if(isset($l[0]) && isset($l[1]) && $l[0] && $l[1]) {
				$add_icon	= isset($l[2]) ? $l[2] : 'fa-list';
				$add_access	= isset($l[3]) && in_array($l[3], ['view','input','edit','delete']) ? 'access_'.$l[3] : 'access_view';
				if($menu[$add_access]) {
					if(strpos($l[0], '/')) {
						$additional .= '<a class="dropdown-item" href="'.$l[0].'"><i class="'.$add_icon.'"></i>'.$l[1].'</a>' . PHP_EOL;
					} else {
						$additional .= '<a class="dropdown-item '.$l[0].'" href="javascript:;"><i class="'.$add_icon.'"></i>'.$l[1].'</a>' . PHP_EOL;
					}
				}
			}
		}
	}
	if($delete || $active || $inactive || $export || $import || (isset($additional) && $additional)) {
		$add_class = $input ? ' caret-only' : '';
		$a_button .= '<div class="btn-group" role="group" aria-label="Access Button">' . PHP_EOL;
		if($input) {
			if($link_add && !is_array($link_add)) {
				$a_button .= '<a href="'.$link_add.'" class="btn btn-primary btn-sm"><i class="fa-plus"></i>'.lang('tambah').'</a>' . PHP_EOL;
			} else {
				$a_button .= '<button type="button" class="btn btn-primary btn-sm btn-input" data-id="0"><i class="fa-plus"></i>'.lang('tambah').'</button>' . PHP_EOL;
			}
		}
		$a_button .= '<div class="btn-group" role="group">' . PHP_EOL;
		$a_button .= '<button id="btnAccess" type="button" class="btn btn-sm btn-primary dropdown-toggle'.$add_class.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
		$a_button .= $input ? '' : lang('tombol_aksi');
		$a_button .= '</button>' . PHP_EOL;
		$a_button .= '<div class="dropdown-menu" aria-labelledby="btnAccess">' . PHP_EOL;
		if(isset($additional) && $additional) {
			$a_button .= $additional;
			$a_button .= '<div class="dropdown-divider"></div>' . PHP_EOL;
		}
		if($setting) $a_button .= '<a class="dropdown-item btn-act-setting" href="javascript:;"><i class="fa-cog"></i>'.lang('pengaturan').'</a>' . PHP_EOL;
		if($export) $a_button .= '<a class="dropdown-item btn-act-export" href="'.$link.'export" target="_blank"><i class="fa-download"></i>'.lang('ekspor').'</a>' . PHP_EOL;
		if($import) {
			$a_button .= '<a class="dropdown-item btn-act-import" href="javascript:;"><i class="fa-upload"></i>'.lang('impor').'</a>' . PHP_EOL;
			$a_button .= '<a class="dropdown-item btn-act-template" href="'.$link.'template" target="_blank"><i class="fa-file-alt"></i>'.lang('templat_impor').'</a>' . PHP_EOL;
		}
		if(($export || $import || $setting) && ($delete || $active || $inactive)) $a_button .= '<div class="dropdown-divider"></div>' . PHP_EOL;
		if($active) $a_button .= '<a class="dropdown-item btn-act-active" data-value="1" href="javascript:;"><i class="fa-toggle-on"></i>'.lang('aktif').'</a>' . PHP_EOL;
		if($inactive) $a_button .= '<a class="dropdown-item btn-act-active" data-value="0" href="javascript:;"><i class="fa-toggle-off"></i>'.lang('tidak_aktif').'</a>' . PHP_EOL;
		if(($active || $inactive) && $delete) $a_button .= '<div class="dropdown-divider"></div>' . PHP_EOL;
		if($delete) $a_button .= '<a class="dropdown-item btn-act-delete" href="javascript:;"><i class="fa-trash"></i>'.lang('hapus').'</a>' . PHP_EOL;
		$a_button .= '</div>' . PHP_EOL;
		$a_button .= '</div>' . PHP_EOL;
		$a_button .= '</div>' . PHP_EOL;
	} else {
		if($input) {
			if($link_add && !is_array($link_add)) {
				$a_button .= '<a href="'.$link_add.'" class="btn btn-primary btn-sm"><i class="fa-plus"></i>'.lang('tambah').'</a>' . PHP_EOL;
			} else {
				$a_button = '<button type="button" class="btn btn-primary btn-sm btn-input" data-id="0"><i class="fa-plus"></i>'.lang('tambah').'</button>' . PHP_EOL;
			}
		}
	}
	return $a_button;
}

function table_open($class='',$fixed=false,$serverside=false,$table='',$attr='') {
	if($class == '') $class = 'table table-striped table-bordered table-app table-hover';
	$CI = get_instance();
	$CI->session->set_userdata('g_table',true);
	$ss 	= $serverside && $table ? ' data-serverside="'.$serverside.'" data-table="'.$table.'"' : '';
	$fs 	= $fixed ? ' data-fixed="true"' : '';
	$attr 	= $attr ? ' '.$attr : '';
	$html = '<table class="'.$class.'"'.$fs.$ss.$attr.'>' . PHP_EOL;
	echo $html;
}

function table_close() {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_tr')) {
		$html .= '</tr>' . PHP_EOL;
	}
	if($CI->session->userdata('g_thead')) {
		$html .= '</thead>' . PHP_EOL;
	}
	if($CI->session->userdata('g_tbody')) {
		$html .= '</tbody>' . PHP_EOL;
	}
	if($CI->session->userdata('g_tfoot')) {
		$html .= '</tfoot>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_table');
	$CI->session->unset_userdata('g_thead');
	$CI->session->unset_userdata('g_tbody');
	$CI->session->unset_userdata('g_tfoot');
	$CI->session->unset_userdata('g_tr');
	$html .= '</table>' . PHP_EOL;
	echo $html;
}

function thead($class='') {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_tr')) {
		$html .= '</tr>' . PHP_EOL;
	}
	if($CI->session->userdata('g_tbody')) {
		$html .= '</tbody>' . PHP_EOL;
	}
	if($CI->session->userdata('g_tfoot')) {
		$html .= '</tfoot>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_tbody');
	$CI->session->unset_userdata('g_tfoot');
	$CI->session->unset_userdata('g_tr');
	if($CI->session->userdata('g_table') && !$CI->session->userdata('g_thead')) {
		$CI->session->set_userdata('g_thead',true);
		$cls = $class ? ' class="'.$class.'"' : '';
		$html .= '<thead'.$cls.'>' . PHP_EOL;
		echo $html;
	}
}

function tbody($class='') {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_tr')) {
		$html .= '</tr>' . PHP_EOL;
	}
	if($CI->session->userdata('g_thead')) {
		$html .= '</thead>' . PHP_EOL;
	}
	if($CI->session->userdata('g_tfoot')) {
		$html .= '</tfoot>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_thead');
	$CI->session->unset_userdata('g_tfoot');
	$CI->session->unset_userdata('g_tr');
	if($CI->session->userdata('g_table') && !$CI->session->userdata('g_tbody')) {
		$CI->session->set_userdata('g_tbody',true);
		$cls = $class ? ' class="'.$class.'"' : '';
		$html .= '<tbody'.$cls.'>' . PHP_EOL;
		echo $html;
	}
}

function tfoot($class='') {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_tr')) {
		$html .= '</tr>' . PHP_EOL;
	}
	if($CI->session->userdata('g_thead')) {
		$html .= '</thead>' . PHP_EOL;
	}
	if($CI->session->userdata('g_tbody')) {
		$html .= '</tbody>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_thead');
	$CI->session->unset_userdata('g_tbody');
	$CI->session->unset_userdata('g_tr');
	if($CI->session->userdata('g_table') && !$CI->session->userdata('g_tfoot')) {
		$CI->session->set_userdata('g_tfoot',true);
		$cls = $class ? ' class="'.$class.'"' : '';
		$html .= '<tfoot'.$cls.'>' . PHP_EOL;
		echo $html;
	}
}

function tr($class='') {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_tr')) {
		$html .= '</tr>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_tr');
	if($CI->session->userdata('g_table') && !$CI->session->userdata('g_tr')) {
		$CI->session->set_userdata('g_tr',true);
		$cls = $class ? ' class="'.$class.'"' : '';
		$html .= '<tr'.$cls.'>' . PHP_EOL;
		echo $html;
	}
}

function th($text='',$class='',$attr='') {
	$CI = get_instance();
	if($CI->session->userdata('g_table')) {
		if($text == 'checkbox') {
			$rand = rand(111111111,999999999);
			$text = '<div class="custom-checkbox custom-control">';
			$text .= '<input class="custom-control-input" type="checkbox" id="chk-th-'.$rand.'">';
			$text .= '<label class="custom-control-label" for="chk-th-'.$rand.'">&nbsp;</label>';
			$text .= '</div>';
		}
		$cls = $class ? ' class="'.$class.'"' : '';
		$attr = $attr ? ' '.$attr : '';
		$html = '<th'.$cls.$attr.'>'.$text.'</th>' . PHP_EOL;
		echo $html;
	}
}

function td($text='',$class='',$attr='') {
	$CI = get_instance();
	if($CI->session->userdata('g_table')) {
		$cls = $class ? ' class="'.$class.'"' : '';
		$attr = $attr ? ' '.$attr : '';
		$html = '<td'.$cls.$attr.'>'.$text.'</td>' . PHP_EOL;
		echo $html;
	}
}

function modal_open($id='',$title="Modal",$modal_tipe='',$attr='') {
	$CI = get_instance();
	$CI->session->set_userdata('g_modal',true);
	$cls = '';
	if($id != '') $id = ' id="'.$id.'"';
	if($attr) {
		$e = explode('=',$attr);
		if(count($e) == 1) {
			$cls = ' '.$attr;
			$attr = '';
		}
	}
	$html = '<div class="modal fade'.$cls.'" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true"'.$id.' '.$attr.'>' . PHP_EOL;
	$html .= '<div class="modal-dialog '.$modal_tipe.'">' . PHP_EOL;
	$html .= '<div class="modal-content">' . PHP_EOL;
	$html .= '<div class="modal-header">' . PHP_EOL;
	$html .= '<h5 class="modal-title">'.$title.'</h5>' . PHP_EOL;
	$html .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' . PHP_EOL;
	$html .= '</div>' . PHP_EOL;
	echo $html;
}
function modal_body($a_class='',$attr='') {
	$CI = get_instance();
	if($CI->session->userdata('g_modal') && !$CI->session->userdata('g_modal_body')) {
		$CI->session->set_userdata('g_modal_body',true);
		$html = '<div class="modal-body '.$a_class.'" '.$attr.'>' . PHP_EOL;
		echo $html;
	}
}
function modal_footer() {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_modal_body')) {
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_modal_body');
	if($CI->session->userdata('g_modal') && !$CI->session->userdata('g_modal_footer')) {
		$CI->session->set_userdata('g_modal_footer',true);
		$html .= '<div class="modal-footer">' . PHP_EOL;
		echo $html;
	}
}
function modal_close() {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_modal_body') || $CI->session->userdata('g_modal_footer')) {
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_modal_body');
	$CI->session->unset_userdata('g_modal_footer');
	$CI->session->unset_userdata('g_modal');
	$html .= '</div>' . PHP_EOL;
	$html .= '</div>' . PHP_EOL;
	$html .= '</div>' . PHP_EOL;
	echo $html;
}

function form_open($action='#',$method='post',$id='form',$attr='') {
	$CI = get_instance();
	$CI->session->set_userdata('g_form',true);
	$CI->session->set_userdata('col_label',3);
	$CI->session->set_userdata('col_input',9);
	if($id != '') $id = ' id="'.$id.'"';
	if($attr) $attr = ' '.$attr;
	$html = '<form method="'.$method.'" action="'.$action.'"'.$id.$attr.'>' . PHP_EOL;
	echo $html;
}
function col_init($label=3,$input=9) {
	$CI = get_instance();
	$CI->session->set_userdata('col_label',$label);
	$CI->session->set_userdata('col_input',$input);
}
function form_close() {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html = '</div>' . PHP_EOL;
		$html = '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_form');
	$CI->session->unset_userdata('col_label');
	$CI->session->unset_userdata('col_input');
	$CI->session->unset_userdata('g_checkbox');
	$CI->session->unset_userdata('g_inputgroup');
	$CI->session->unset_userdata('sub_form');
	$html .= '</form>' . PHP_EOL;
	echo $html;
}
function sub_open($tipe='1') {
	$CI = get_instance();
	$CI->session->set_userdata('sub_form',$tipe);
}
function sub_close() {
	$CI = get_instance();
	$CI->session->unset_userdata('sub_form');
}
function label($label='',$cls='',$attr='') {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	$html .= '<div class="row">' . PHP_EOL;
	$html .= '<h4 class="col-form-label col-12 '.$cls.'" '.$attr.'>'.$label.'</h4>' . PHP_EOL;
	$html .= '</div>' . PHP_EOL;
	echo $html;
}
function input($tipe='',$label='',$nama='',$validation='',$value='',$attr='',$label_group='') {
	$CI = get_instance();
	if($attr) $attr = ' '.$attr;
	$val = explode('|', $validation);
	$required = '';
	$_id = str_replace(['[',']'], '', $nama);
	foreach($val as $v) {
		if($v == 'required') {
			$required = ' required';
		}
	}
	$label_prepend = $label_append = '';
	if($label_group) {
		$e = explode('|',$label_group);
		if(count($e) == 1) {
			$e2 			= explode(':',$label_group);
			if(count($e2) == 2) {
				if($e2[0] == 'append') $label_append = $e2[1];
				else if($e2[0] == 'prepend') $label_prepend = $e2[1];
				else $label_append = $label_group;
			} else {
				$label_append	= $label_group;
			}
		} else {
			$label_prepend 	= $e[0];
			$label_append 	= $e[1];
		}
	}
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0 ? $CI->session->userdata('col_label') : 0;
	$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12 ? $CI->session->userdata('col_input') : 12;
	if($tipe == 'hidden') {
		if(!$CI->session->userdata('g_inputgroup')) {
			$html .= '<input type="hidden" name="'.$nama.'" id="'.$_id.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
		}
	} else {
		if(!$CI->session->userdata('g_inputgroup')) {
			$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
			$html .= '<div class="form-group row">' . PHP_EOL;
			if($c1) {
				$html .= '<label class="col-form-label col-sm-'.$c1.$required.$sub.'" for="'.$_id.'">'.$label.'</label>' . PHP_EOL;
			}
			$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		}
		if($tipe == 'icon') {
			if(!$CI->session->userdata('g_inputgroup')) {
				$html .= '<div class="input-group">' . PHP_EOL;
				$html .= '<input type="text" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="form-control icp" data-placement="bottomRight" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
				$html .= '<div class="input-group-append">' . PHP_EOL;
				$html .= '<span class="input-group-text"></span>' . PHP_EOL;
				$html .= '</div>' . PHP_EOL;
				$html .= '</div>' . PHP_EOL;
			}
		} elseif($tipe == 'range') {
			if(!$CI->session->userdata('g_inputgroup')) {
				$html .= '<input type="'.$tipe.'" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="custom-range" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
			}
		} elseif($tipe == 'money') {
			if($label_group) {
				$html .= '<div class="input-group">' . PHP_EOL;
				if($label_prepend) {
					$html .= '<div class="input-group-prepend">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_prepend.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
			}
			$html .= '<input type="text" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="form-control money" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
			if($label_group) {
				if($label_append) {
					$html .= '<div class="input-group-append">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_append.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
				$html .= '</div>' . PHP_EOL;
			}
		} elseif($tipe == 'percent') {
			$html .= '<div class="input-group">' . PHP_EOL;
			$html .= '<input type="text" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="form-control percent" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
			$html .= '<div class="input-group-append">' . PHP_EOL;
			$html .= '<span class="input-group-text">%</span>' . PHP_EOL;
			$html .= '</div>' . PHP_EOL;
			$html .= '</div>' . PHP_EOL;
		} elseif($tipe == 'date') {
			if($label_group) {
				$html .= '<div class="input-group">' . PHP_EOL;
				if($label_prepend) {
					$html .= '<div class="input-group-prepend">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_prepend.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
			}
			$html .= '<input type="text" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="form-control dp" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
			if($label_group) {
				if($label_append) {
					$html .= '<div class="input-group-append">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_append.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
				$html .= '</div>' . PHP_EOL;
			}
		} elseif($tipe == 'daterange') {
			if($label_group) {
				$html .= '<div class="input-group">' . PHP_EOL;
				if($label_prepend) {
					$html .= '<div class="input-group-prepend">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_prepend.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
			}
			$html .= '<input type="text" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="form-control drp" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
			if($label_group) {
				if($label_append) {
					$html .= '<div class="input-group-append">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_append.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
				$html .= '</div>' . PHP_EOL;
			}
		} elseif($tipe == 'datetime') {
			if($label_group) {
				$html .= '<div class="input-group">' . PHP_EOL;
				if($label_prepend) {
					$html .= '<div class="input-group-prepend">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_prepend.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
			}
			$html .= '<input type="text" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="form-control dtp" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
			if($label_group) {
				if($label_append) {
					$html .= '<div class="input-group-append">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_append.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
				$html .= '</div>' . PHP_EOL;
			}
		} elseif($tipe == 'autocomplete') {
			if(!$CI->session->userdata('g_inputgroup')) {
				if($label_group) {
					$html .= '<div class="input-group">' . PHP_EOL;
					if($label_prepend) {
						$html .= '<div class="input-group-prepend">' . PHP_EOL;
						$html .= '<span class="input-group-text">'.$label_prepend.'</span>' . PHP_EOL;
						$html .= '</div>' . PHP_EOL;
					}
				}
				$html .= '<input type="text" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="form-control autocomplete" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
				if($label_group) {
					if($label_append) {
						$html .= '<div class="input-group-append">' . PHP_EOL;
						$html .= '<span class="input-group-text">'.$label_append.'</span>' . PHP_EOL;
						$html .= '</div>' . PHP_EOL;
					}
					$html .= '</div>' . PHP_EOL;
				}
			}
		} else {
			if($label_group) {
				$html .= '<div class="input-group">' . PHP_EOL;
				if($label_prepend) {
					$html .= '<div class="input-group-prepend">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_prepend.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
			}
			$html .= '<input type="'.$tipe.'" name="'.$nama.'" id="'.$_id.'" autocomplete="off" class="form-control" data-validation="'.$validation.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
			if($label_group) {
				if($label_append) {
					$html .= '<div class="input-group-append">' . PHP_EOL;
					$html .= '<span class="input-group-text">'.$label_append.'</span>' . PHP_EOL;
					$html .= '</div>' . PHP_EOL;
				}
				$html .= '</div>' . PHP_EOL;
			}
		}
		if(!$CI->session->userdata('g_inputgroup')) {
			$html .= '</div>' . PHP_EOL;
			$html .= '</div>' . PHP_EOL;
		}
	}
	echo $html;
}
function select($label='',$nama='',$validation='',$data=array(),$opt_key="",$opt_val="",$value='',$attr='') {
	$CI = get_instance();
	if($attr) $attr = ' '.$attr;
	if(!is_array($data)) $data = array();
	$nama_id 	= str_replace(['[',']'], '', $nama);;
	$val = explode('|', $validation);
	$required = '';
	foreach($val as $v) {
		if($v == 'required') {
			$required = ' required';
		}
	}
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	if(!$CI->session->userdata('g_inputgroup')) {
		$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
		$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
		$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.$required.$sub.'" for="'.$nama_id.'">'.$label.'</label>' . PHP_EOL;
		}
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
	}
	$html .= '<select name="'.$nama.'" id="'.$nama_id.'" class="form-control custom-select" data-validation="'.$validation.'"'.$attr.'>' . PHP_EOL;
	$i = 0;
	foreach($data as $k => $d) {
		if($opt_key == '' || $opt_val == '') {
			$_val = $opt_key == '_key' ? $k : $d;
			if($_val == $value || strtoupper($_val) == $value) {
				$html .= '<option value="'.$_val.'" selected>'.$d.'</option>' . PHP_EOL;
			} else {
				$html .= '<option value="'.$_val.'">'.$d.'</option>' . PHP_EOL;
			}
		} else {
			if($i == 0) {
				if (strpos($attr, 'multiple') !== false) {
				    $html .= '<option value="all">All</option>' . PHP_EOL;
				} else {
					if(strpos($validation, 'all') !== FALSE) {
						$html .= '<option value="all">Semua '.$label.'</option>' . PHP_EOL;
					} else {
						$html .= '<option value=""></option>' . PHP_EOL;
					}
				}
			}
			$opt_label = '';
			$spl = preg_split( "/[:,\[,\],\-,\(,\),\s]+/",$opt_val, -1);
			if(count($spl) > 1) {
				$opt_label = '';
				foreach($spl as $k_sp => $sp) {
					if($sp) {
						$opt_label .= $d[$sp];
					}
					if(isset($spl[$k_sp + 1])) {
						$opt_label .= preg_match('/'.$sp.'(.*?)'.$spl[$k_sp + 1].'/', $opt_val, $match) == 1 ? $match[1] : ' ';
					}
				}
			} else {
				$opt_label = $d[$opt_val];
			}
			if($d[$opt_key] == $value) {
				$html .= '<option value="'.$d[$opt_key].'" selected>'.$opt_label.'</option>' . PHP_EOL;
			} else {
				$html .= '<option value="'.$d[$opt_key].'">'.$opt_label.'</option>' . PHP_EOL;
			}
		}
		$i++;
	}
	$html .= '</select>' . PHP_EOL;
	if(!$CI->session->userdata('g_inputgroup')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	echo $html;
}
function select2($label='',$nama='',$validation='',$data=array(),$opt_key="",$opt_val="",$value='',$attr='') {
	$CI 		= get_instance();
	if($attr) $attr = ' '.$attr;
	if(!is_array($data)) $data = array();
	$nama_id 	= str_replace(['[',']'], '', $nama);
	$infinity 	= false;
	$val 		= explode('|', $validation);
	$required 	= '';
	$arr_val	= array();
	foreach($val as $v) {
		$arr_val[$v] = $v;
		if($v == 'required') {
			$required = ' required';
		} elseif($v == 'infinity') {
			$infinity = true;
			$validation = str_replace('||', '|', str_replace('infinity', '', $validation));
			unset($arr_val[$v]);
		}
	}
	$validation = implode('|', $arr_val);
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	if(!$CI->session->userdata('g_inputgroup')) {
		$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
		$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
		$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.$required.$sub.'" for="'.$nama_id.'">'.$label.'</label>' . PHP_EOL;
		}
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		if($infinity) {
			$html .= '<select name="'.$nama.'" id="'.$nama_id.'" class="form-control select2 infinity" data-validation="'.$validation.'"'.$attr.'>' . PHP_EOL;
		} else {
			$html .= '<select name="'.$nama.'" id="'.$nama_id.'" class="form-control select2" data-validation="'.$validation.'"'.$attr.'>' . PHP_EOL;
		}
		$i = 0;
		foreach($data as $k => $d) {
			if($opt_key == '' || $opt_val == '') {
				if($i == 0) {
					if (strpos($attr, 'multiple') !== false) {
					    $html .= '<option value="all">All</option>' . PHP_EOL;
					} else {
						if(strpos($validation, 'all') !== FALSE) {
							$html .= '<option value="all">Semua '.$label.'</option>' . PHP_EOL;
						} else {
							$html .= '<option value=""></option>' . PHP_EOL;
						}
					}
				}
				$_val = $opt_key == '_key' ? $k : $d;
				if(is_array($value)) {
					foreach($value as $vl) {
						if($_val == $vl || strtoupper($_val) == $vl) {
							$html .= '<option value="'.$_val.'" selected>'.$d.'</option>' . PHP_EOL;
						} else {
							$html .= '<option value="'.$_val.'">'.$d.'</option>' . PHP_EOL;
						}
					}
				} else {
					if($_val == $value || strtoupper($_val) == $value) {
						$html .= '<option value="'.$_val.'" selected>'.$d.'</option>' . PHP_EOL;
					} else {
						$html .= '<option value="'.$_val.'">'.$d.'</option>' . PHP_EOL;
					}
				}
			} else {
				if($i == 0) {
					if (strpos($attr, 'multiple') !== false) {
						if(strpos($validation, 'all') !== FALSE) {
							$html .= '<option value="all">All</option>' . PHP_EOL;
						}
					} else {
						if(strpos($validation, 'all') !== FALSE) {
							$html .= '<option value="all">Semua '.$label.'</option>' . PHP_EOL;
						} else {
							$html .= '<option value=""></option>' . PHP_EOL;
						}
					}
				}
				$opt_label = '';
				$spl = preg_split( "/[:,\[,\],\-,\(,\),\s]+/",$opt_val, -1);
				if(count($spl) > 1) {
					$opt_label = '';
					foreach($spl as $k_sp => $sp) {
						if($sp) {
							$opt_label .= $d[$sp];
						}
						if(isset($spl[$k_sp + 1])) {
							$opt_label .= preg_match('/'.$sp.'(.*?)'.$spl[$k_sp + 1].'/', $opt_val, $match) == 1 ? $match[1] : ' ';
						}
					}
				} else {
					$opt_label = $d[$opt_val];
				}
				if(is_array($value)) {
					foreach($value as $vl) {
						if($d[$opt_key] == $vl || strtoupper($d[$opt_key]) == $vl) {
							$html .= '<option value="'.$d[$opt_key].'" selected>'.$opt_label.'</option>' . PHP_EOL;
						} else {
							$html .= '<option value="'.$d[$opt_key].'">'.$opt_label.'</option>' . PHP_EOL;
						}
					}
				} else {
					if($d[$opt_key] == $value || strtoupper($d[$opt_key]) == $value) {
						$html .= '<option value="'.$d[$opt_key].'" selected>'.$opt_label.'</option>' . PHP_EOL;
					} else {
						$html .= '<option value="'.$d[$opt_key].'">'.$opt_label.'</option>' . PHP_EOL;
					}
				}
			}
			$i++;
		}
		$html .= '</select>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		echo $html;
	}
}
function textarea($label='',$nama='',$validation='',$value='',$attr='') {
	$CI = get_instance();
	if($attr) $attr = ' '.$attr;
	$val = explode('|', $validation);
	$required = '';
	$_id = str_replace(['[',']'], '', $nama);
	foreach($val as $v) {
		if($v == 'required') {
			$required = ' required';
		}
	}
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	if(!$CI->session->userdata('g_inputgroup')) {
		$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
		$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
		$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.$required.$sub.'" for="'.$_id.'">'.$label.'</label>' . PHP_EOL;
		}
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		$html .= '<textarea name="'.$nama.'" id="'.$_id.'" class="form-control" data-validation="'.$validation.'" rows="4"'.$attr.'>'.$value.'</textarea>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		echo $html;
	}
}
function checkbox($label='',$nama='',$value='',$attr='') {
	$CI = get_instance();
	if($attr) $attr = ' '.$attr;
	$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
	$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
	if(!$CI->session->userdata('g_inputgroup')) {
		$html = '';
		if(!$CI->session->userdata('g_checkbox')) {
			$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
			$html .= '<div class="form-group row">' . PHP_EOL;
			if($c1) {
				$html .= '<label class="col-form-label col-sm-'.$c1.$sub.'" for="'.$nama.'">'.$label.'</label>' . PHP_EOL;
			}
			$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		}
		$html .= '<div class="custom-checkbox custom-control custom-control-inline">' . PHP_EOL;
		$html .= '<input class="custom-control-input" type="checkbox" id="'.$nama.'" name="'.$nama.'" value="'.$value.'"'.$attr.'>' . PHP_EOL;
		if($CI->session->userdata('g_checkbox')) {
			$html .= '<label class="custom-control-label" for="'.$nama.'">'.$label.'</label>' . PHP_EOL;
		} else {
			$html .= '<label class="custom-control-label" for="'.$nama.'">&nbsp;</label>' . PHP_EOL;
		}
		$html .= '</div>' . PHP_EOL;
		if(!$CI->session->userdata('g_checkbox')) {
			$html .= '</div>' . PHP_EOL;
			$html .= '</div>' . PHP_EOL;
		}
		echo $html;
	}
}
function checkbox_group($label='') {
	$CI = get_instance();
	if(!$CI->session->userdata('g_inputgroup')) {
		$CI->session->set_userdata('g_checkbox',true);
		$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
		$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
		$html = '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.'">'.$label.'</label>' . PHP_EOL;
		}
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		echo $html;
	}
}
function toggle($label='',$nama='',$checked=true,$attr='') {
	$CI = get_instance();
	if($attr) $attr = ' '.$attr;
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$chk 	= $checked ? ' checked ' : '';
	$CI->session->unset_userdata('g_checkbox');
	if(!$CI->session->userdata('g_inputgroup')) {
		$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
		$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
		$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.$sub.'" for="'.$nama.'">'.$label.'</label>' . PHP_EOL;
		}
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		$html .= '<label class="switch">';
		$html .= '<input type="checkbox" value="1" name="'.$nama.'"'.$chk.'id="'.$nama.'">' . PHP_EOL;
		$html .= '<span class="slider"></span>' . PHP_EOL;
		$html .= '</label>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		echo $html;
	}
}
function fileupload($label='',$nama='',$validation='',$attr='') {
	$CI = get_instance();
	if($attr) $attr = ' '.$attr;
	$val = explode('|', $validation);
	$required = '';
	foreach($val as $v) {
		if($v == 'required') {
			$required = ' required';
		}
	}
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	if(!$CI->session->userdata('g_inputgroup')) {
		$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
		$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
		$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.$required.$sub.'" for="'.$nama.'">'.$label.'</label>' . PHP_EOL;
		}
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		$html .= '<input type="hidden" name="'.$nama.'" id="'.$nama.'" data-validation="'.$validation.'" data-action="'.base_url('upload/file/datetime').'" data-token="'.encode_id([user('id'),(time() + 900)]).'" autocomplete="off" class="input-file" '.$attr.'>' . PHP_EOL;
		$html .= '<div class="input-group">' . PHP_EOL;
		$html .= '<div class="form-control fileupload-preview"></div>' . PHP_EOL;
		$html .= '<div class="input-group-append">' . PHP_EOL;
		$html .= '<button class="btn btn-secondary btn-file" type="button">'.lang('unggah').'</button>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		echo $html;
	}
}
function imageupload($label='',$nama='',$width=256,$height=256,$tipe='',$attr='',$img_link='',$img_pos='') {
	$CI = get_instance();
	if($attr) $attr = ' '.$attr;
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$length = strlen($width.'x'.$height);
	if($width >= 1000) {
		$font_size = 60;
		$toleransi_height = 30;
		$toleransi_width  = 21.75;
	}elseif($width >= 300) {
		$font_size = 40;
		$toleransi_height = 20;
		$toleransi_width  = 14.5;
	}elseif($width > 100) {
		$font_size = 20;
		$toleransi_height = 10;
		$toleransi_width  = 7.25;
	}elseif($width <= 100) {
		$font_size = 10;
		$toleransi_height = 5;
		$toleransi_width  = 3.25;
	}
	$svg 	= 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22'.$width.'%22%20height%3D%22'.$height.'%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20'.$width.'%20'.$height.'%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164ad9cacfe%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A'.$font_size.'pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164ad9cacfe%22%3E%3Crect%20width%3D%22'.$width.'%22%20height%3D%22'.$height.'%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22'.(($width * 0.5) - ($length * $toleransi_width)).'%22%20y%3D%22'.(($height * 0.5) + $toleransi_height).'%22%3E'.$width.'x'.$height.'%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';
	if($img_link) $svg = $img_link;
	$CI->session->unset_userdata('g_checkbox');
	if(!$CI->session->userdata('g_inputgroup')) {
		$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
		$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
		$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.$sub.'" for="'.$nama.'">'.$label.'</label>' . PHP_EOL;
		}
		$small = $width <= 100 ? ' small' : '';
		$rekomendasi = $width <= 100 ? '' : lang('rekomendasi_ukuran').' ';
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		$html .= '<div class="image-upload'.$small.'"'.$attr.'>' . PHP_EOL;
		$html .= '<div class="image-content">' . PHP_EOL;
		$html .= '<img src="'.$svg.'" data-origin="'.$svg.'" alt="'.$label.'" data-action="'.base_url('upload/image/'.$width.'/'.$height.'/'.$tipe).'" data-token="'.encode_id([user('id'),(time() + 900)]).'">' . PHP_EOL;
		$html .= '<input type="hidden" name="'.$nama.'" data-validation="image" value="'.$img_pos.'">' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '<div class="image-description">'.$rekomendasi.$width.' x '.$height.' (px)</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		echo $html;
	}
}
function imageupload2($label='',$nama='',$validation='',$width=256,$height=256,$tipe='',$value='',$attr='') {
	$CI = get_instance();
	if($attr) $attr = ' '.$attr;
	$val = explode('|', $validation);
	$required = '';
	foreach($val as $v) {
		if($v == 'required') {
			$required = ' required';
		}
	}
	$validation = $validation ? $validation . '|image' : 'image';
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	if(!$CI->session->userdata('g_inputgroup')) {
		$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
		$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
		$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.$required.$sub.'" for="'.$nama.'">'.$label.'</label>' . PHP_EOL;
		}
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		$html .= '<div class="input-group">' . PHP_EOL;
		$html .= '<input type="text" name="'.$nama.'" id="'.$nama.'" data-validation="'.$validation.'" data-action="'.base_url('upload/image/'.$width.'/'.$height.'/'.$tipe).'" data-token="'.encode_id([user('id'),(time() + 900)]).'" value="'.$value.'" class="form-control input-image" placeholder="Rekomendasi ukuran '.$width.'x'.$height.' (px)" readonly '.$attr.'>' . PHP_EOL;
		$html .= '<div class="input-group-append">' . PHP_EOL;
		$html .= '<button class="btn btn-info btn-view-imageupload" type="button"><span class="fa-eye"></span></button>' . PHP_EOL;
		$html .= '<button class="btn btn-secondary btn-image" type="button">'.lang('unggah').'</button>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		echo $html;
	}
}
function radio($label='',$nama='',$value='',$attr='') {
	$CI = get_instance();
	if(!is_array($value)) {
		$value = array(
			$value => '&nbsp;'
		);
	}
	if($attr) $attr = ' '.$attr;
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
	$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
	if(!$CI->session->userdata('g_inputgroup')) {
		$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<label class="col-form-label col-sm-'.$c1.$sub.'" for="'.$nama.'">'.$label.'</label>' . PHP_EOL;
		}
		$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		foreach($value as $k => $v) {
			$html .= '<div class="custom-radio custom-control custom-control-inline">' . PHP_EOL;
			$html .= '<input class="custom-control-input" type="radio" id="'.$nama.'_'.$k.'" name="'.$nama.'" value="'.$k.'"'.$attr.'>' . PHP_EOL;
			$html .= '<label class="custom-control-label" for="'.$nama.'_'.$k.'">'.$v.'</label>' . PHP_EOL;
			$html .= '</div>' . PHP_EOL;
		}
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	echo $html;
}
function inputgroup_open($label='',$required='') {
	$CI = get_instance();
	$CI->session->set_userdata('g_inputgroup',true);
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
	$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
	$sub = $CI->session->userdata('sub_form') ? ' sub-' . $CI->session->userdata('sub_form') : '';
	$html .= '<div class="form-group row">' . PHP_EOL;
	if($c1) {
		$html .= '<label class="col-form-label col-sm-'.$c1.$sub.' '.$required.'">'.$label.'</label>' . PHP_EOL;
	}
	$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
	$html .= '<div class="input-group">' . PHP_EOL;
	echo $html;
}
function inputgroup_close() {
	$CI = get_instance();
	$html = '';
	$html .= '</div>' . PHP_EOL;
	$html .= '</div>' . PHP_EOL;
	$html .= '</div>' . PHP_EOL;
	$CI->session->unset_userdata('g_inputgroup');
	echo $html;
}
function form_button($submit='Submit',$reset='Reset') {
	$CI = get_instance();
	$html = '';
	if($CI->session->userdata('g_checkbox')) {
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	$CI->session->unset_userdata('g_checkbox');
	$c1 = $CI->session->userdata('col_label') ? $CI->session->userdata('col_label') : 0;
	$c2 = $CI->session->userdata('col_input') ? $CI->session->userdata('col_input') : 12;
	if(!$CI->session->userdata('g_inputgroup')) {
		$html .= '<div class="form-group row">' . PHP_EOL;
		if($c1) {
			$html .= '<div class="col-sm-'.$c2.' offset-sm-'.$c1.'">' . PHP_EOL;
		} else {
			$html .= '<div class="col-sm-'.$c2.'">' . PHP_EOL;
		}
		$html .= '<button type="submit" class="btn btn-info">'.$submit.'</button>' . PHP_EOL;
		if($reset) {
			$html .= '<button type="reset" class="btn btn-secondary">'.$reset.'</button>' . PHP_EOL;
		}
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
	}
	echo $html;
}
function card_open($title='',$class='') {
	$html = '<div class="card '.$class.'">' . PHP_EOL;
	if($title) $html .= '<div class="card-header">'.$title.'</div>' . PHP_EOL;
	$html .= '<div class="card-body">' . PHP_EOL;
	echo $html;
}
function card_close() {
	$html = '</div>' . PHP_EOL;
	$html .= '</div>' . PHP_EOL;
	echo $html;
}
function alert($msg='',$type='info',$class='') {
	echo '<div class="alert alert-'.$type.' '.$class.'">'.$msg.'</div>'.PHP_EOL;
}