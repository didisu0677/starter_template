<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Web_setting extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function save() {
		$p_color	= '#404E67';
		$h_color	= '#586884';
		$t_color	= '#484848';
		$s_color	= '#22C2DC';
		$b_color 	= '#20B9CE';
		$n_color	= '#ABCDEF';

		// btn-primary
		$p_bg 		= '#00B5B8';
		$p_bo		= '#00A5A8';
		$p_co 		= '#FEFFFF';
		$p_bg_h 	= '#26C0C3';
		$p_bo_h		= '#00AEB1';
		$p_co_h 	= '#FFFEFF';
		$p_bg_f 	= '#009DA0';
		$p_bo_f		= '#00A5A9';
		$p_co_f 	= '#FFFFFE';

		// btn-info
		$i_bg 		= '#2DCEE4';
		$i_bo		= '#22C2DD';
		$i_co 		= '#FDFFFF';
		$i_bg_h 	= '#4DD5E8';
		$i_bo_h		= '#28C9E1';
		$i_co_h 	= '#FFFDFF';
		$i_bg_f 	= '#1CBCD9';
		$i_bo_f		= '#22C2DE';
		$i_co_f 	= '#FFFFFD';

		// btn-success
		$s_bg 		= '#16D39A';
		$s_bo		= '#10C888';
		$s_co 		= '#FCFFFF';
		$s_bg_h 	= '#39DAA9';
		$s_bo_h		= '#13CE92';
		$s_co_h 	= '#FFFCFF';
		$s_bg_f 	= '#0CC27E';
		$s_bo_f		= '#10C888';
		$s_co_f 	= '#FFFFFC';

		// btn-warning
		$w_bg 		= '#FFA87D';
		$w_bo		= '#FF976A';
		$w_co 		= '#FBFFFF';
		$w_bg_h 	= '#FFB591';
		$w_bo_h		= '#FFA075';
		$w_co_h 	= '#FFFBFF';
		$w_bg_f 	= '#FF8D60';
		$w_bo_f		= '#FF976A';
		$w_co_f 	= '#FFFFFB';

		// btn-danger
		$d_bg 		= '#FF7588';
		$d_bo		= '#FF6275';
		$d_co 		= '#FAFFFF';
		$d_bg_h 	= '#FF8A9A';
		$d_bo_h		= '#FF6D80';
		$d_co_h 	= '#FFFAFF';
		$d_bg_f 	= '#FF586B';
		$d_bo_f		= '#FF6275';
		$d_co_f 	= '#FFFFFA';

        if(!is_dir(FCPATH . 'assets/uploads/setting/')){
            $oldmask = umask(0);
        	mkdir(FCPATH . 'assets/uploads/setting/',0777);
            umask($oldmask);
        }
		foreach(post() as $k => $v) {
			if($k == 'favicon' || $k == 'logo' || $k == 'logo_perusahaan') {
				$img 		= basename($v);
				$temp_dir	= str_replace($img, '', $v);
				$e 			= explode('.', $img);
				$ext 		= $e[count($e)-1];
				$new_name	= md5(uniqid()).'.'.$ext;
                $dest 		= dir_upload('setting').$new_name;
                if(!@copy($v,$dest))
                   $v = '';
                else {
                    delete_dir(FCPATH . $temp_dir);
                    @unlink(dir_upload('setting').setting($k));
                    $v = $new_name;
					update_data('tbl_setting',array('_value'=>$v),'_key',$k);
                }
			} else {
				$check 	= get_data('tbl_setting','_key',$k)->row();
				if(isset($check->_key)) {
					update_data('tbl_setting',array('_value'=>$v),'_key',$k);
				} else {
					insert_data('tbl_setting',array('_value'=>$v,'_key'=>$k));
				}
			}
		}
		$css_path	= FCPATH . 'assets/css/';
		@unlink($css_path.'template.css');
		if(file_exists($css_path.'template.css.tpl')) {
			$content = file_get_contents($css_path.'template.css.tpl');
			if($content) {
				$content 	= str_replace($p_color, strtoupper(post()['warna_primary']), $content);
				$content 	= str_replace($h_color, strtoupper(post()['warna_primary_hover']), $content);
				$content 	= str_replace($s_color, strtoupper(post()['warna_secondary']), $content);
				$content 	= str_replace($b_color, strtoupper(post()['warna_border']), $content);
				$content 	= str_replace($t_color, strtoupper(post()['warna_text_header']), $content);
				$content 	= str_replace($n_color, strtoupper(post()['warna_notifikasi']), $content);

				// btn-primary-replace
				$content 	= str_replace($p_bg, strtoupper(post()['bg_btn_primary']), $content);
				$content 	= str_replace($p_bo, strtoupper(post()['border_btn_primary']), $content);
				$content 	= str_replace($p_co, strtoupper(post()['color_btn_primary']), $content);
				$content 	= str_replace($p_bg_h, strtoupper(post()['hover_bg_btn_primary']), $content);
				$content 	= str_replace($p_bo_h, strtoupper(post()['hover_border_btn_primary']), $content);
				$content 	= str_replace($p_co_h, strtoupper(post()['hover_color_btn_primary']), $content);
				$content 	= str_replace($p_bg_f, strtoupper(post()['focus_bg_btn_primary']), $content);
				$content 	= str_replace($p_bo_f, strtoupper(post()['focus_border_btn_primary']), $content);
				$content 	= str_replace($p_co_f, strtoupper(post()['focus_color_btn_primary']), $content);

				// btn-info-replace
				$content 	= str_replace($i_bg, strtoupper(post()['bg_btn_info']), $content);
				$content 	= str_replace($i_bo, strtoupper(post()['border_btn_info']), $content);
				$content 	= str_replace($i_co, strtoupper(post()['color_btn_info']), $content);
				$content 	= str_replace($i_bg_h, strtoupper(post()['hover_bg_btn_info']), $content);
				$content 	= str_replace($i_bo_h, strtoupper(post()['hover_border_btn_info']), $content);
				$content 	= str_replace($i_co_h, strtoupper(post()['hover_color_btn_info']), $content);
				$content 	= str_replace($i_bg_f, strtoupper(post()['focus_bg_btn_info']), $content);
				$content 	= str_replace($i_bo_f, strtoupper(post()['focus_border_btn_info']), $content);
				$content 	= str_replace($i_co_f, strtoupper(post()['focus_color_btn_info']), $content);

				// btn-success-replace
				$content 	= str_replace($s_bg, strtoupper(post()['bg_btn_success']), $content);
				$content 	= str_replace($s_bo, strtoupper(post()['border_btn_success']), $content);
				$content 	= str_replace($s_co, strtoupper(post()['color_btn_success']), $content);
				$content 	= str_replace($s_bg_h, strtoupper(post()['hover_bg_btn_success']), $content);
				$content 	= str_replace($s_bo_h, strtoupper(post()['hover_border_btn_success']), $content);
				$content 	= str_replace($s_co_h, strtoupper(post()['hover_color_btn_success']), $content);
				$content 	= str_replace($s_bg_f, strtoupper(post()['focus_bg_btn_success']), $content);
				$content 	= str_replace($s_bo_f, strtoupper(post()['focus_border_btn_success']), $content);
				$content 	= str_replace($s_co_f, strtoupper(post()['focus_color_btn_success']), $content);

				// btn-warning-replace
				$content 	= str_replace($w_bg, strtoupper(post()['bg_btn_warning']), $content);
				$content 	= str_replace($w_bo, strtoupper(post()['border_btn_warning']), $content);
				$content 	= str_replace($w_co, strtoupper(post()['color_btn_warning']), $content);
				$content 	= str_replace($w_bg_h, strtoupper(post()['hover_bg_btn_warning']), $content);
				$content 	= str_replace($w_bo_h, strtoupper(post()['hover_border_btn_warning']), $content);
				$content 	= str_replace($w_co_h, strtoupper(post()['hover_color_btn_warning']), $content);
				$content 	= str_replace($w_bg_f, strtoupper(post()['focus_bg_btn_warning']), $content);
				$content 	= str_replace($w_bo_f, strtoupper(post()['focus_border_btn_warning']), $content);
				$content 	= str_replace($w_co_f, strtoupper(post()['focus_color_btn_warning']), $content);

				// btn-danger-replace
				$content 	= str_replace($d_bg, strtoupper(post()['bg_btn_danger']), $content);
				$content 	= str_replace($d_bo, strtoupper(post()['border_btn_danger']), $content);
				$content 	= str_replace($d_co, strtoupper(post()['color_btn_danger']), $content);
				$content 	= str_replace($d_bg_h, strtoupper(post()['hover_bg_btn_danger']), $content);
				$content 	= str_replace($d_bo_h, strtoupper(post()['hover_border_btn_danger']), $content);
				$content 	= str_replace($d_co_h, strtoupper(post()['hover_color_btn_danger']), $content);
				$content 	= str_replace($d_bg_f, strtoupper(post()['focus_bg_btn_danger']), $content);
				$content 	= str_replace($d_bo_f, strtoupper(post()['focus_border_btn_danger']), $content);
				$content 	= str_replace($d_co_f, strtoupper(post()['focus_color_btn_danger']), $content);

				$filename 	= $css_path.'template.css';
				$handle 	= fopen ($filename, "wb");
				if($handle) {
					fwrite ( $handle, $content );
				}
				fclose($handle);
				$oldmask = umask(0);
				chmod($filename, 0777);
				umask($oldmask);				
			}
		}
		$response 	= array(
			'status'	=> 'success',
			'message'	=> lang('pengaturan_berhasil_diperbaharui')
		);
		render($response,'json');
	}

	function check_email() {
		$data = array(
			'subject'	=> 'Check email',
			'message'	=> post('message'),
			'to'		=> post('email')
		);
		$response = send_mail($data);
		render($response,'json');
	}

}