<?php defined('BASEPATH') OR exit('No direct script access allowed');

function render($data=array(),$tipe='',$force_view=false) {
    if(is_array($data) || $data == '404') {
        $init_page = '';
        if(!is_array($data)) {
            $data = [];
            $init_page = '404';
        }
        $CI         = get_instance();
        $f_segment  = $CI->uri->segment(1);
        $class      = $CI->router->fetch_class();
        $method     = $CI->router->fetch_method();
        if(setting('interface') == 'admin') {
            $view       = $f_segment == $class ? $class . '/' . $method : $f_segment . '/' . $class . '/' . $method;
            $str_view   = $f_segment == $class ? $class . '_' . $method : $f_segment . '_' . $class . '_' . $method;
            $menu       = isset($data['as_access']) ? menu('all',$data['as_access']) : menu();
            if(isset($data['as_access'])) {
                $CI->session->set_userdata('as_access',$data['as_access']);
            } else {
                $CI->session->unset_userdata('as_access');
            }
        } else {
            $view       = $class . '/' . $method;
            $str_view   = $class . '_' . $method;
        }
        if(strtolower($tipe) == 'json') {
            if(setting('interface') == 'admin') {
                if(!$menu['access_view'] && $menu['target'] != 'auto_code' && post('special') != 'special') {
                    $data   = array(
                        'status'    => 'error',
                        'message'   => lang('izin_ditolak')
                    );
                    update_data('tbl_user_log',['respon'=>403],'id',setting('last_id_log'));
                }
            }
            header('Content-Type: application/json');
            echo json_encode($data);
        } elseif(strtolower($tipe) == 'pdf' || substr(strtolower($tipe),0,4) == 'pdf:') {
            $view                           = str_replace($method, 'pdf_'.$method, $view);
            if(isset($data['view'])) $view  = $data['view'];
            $data['view_content']           = isset($data['html']) ? $data['html'] : $CI->load->view($view,$data,true);
            $html                           = $CI->load->view('layout/pdf',$data,true);
            $html                           = preg_replace('/>\s+</', '><', $html);
            $title                          = isset($data['title']) ? $data['title'] : $method;
            $parsing_pdf                    = explode(':', strtolower($tipe));
            $pdf_orientation                = isset($parsing_pdf[1]) && $parsing_pdf[1] == 'landscape' ? 'landscape' : 'portrait';
            $CI->load->library('pdfgenerator');
            $CI->pdfgenerator->generate($html,$title,true,'A4',$pdf_orientation);
        } else {
            $CI->load->library('asset');
            $CI->load->helper('bootstrap');
            $CI->session->unset_userdata('additionalSelect');
            if(setting('interface') == 'admin') {
                $data['menu_access']    = $menu;
                $data['notifikasi']     = notifikasi();
                $jml_pesan              = get_data('tbl_chat_anggota a',[
                    'select'            => 'COUNT(DISTINCT(a.key_id)) AS jml',
                    'join'              => 'tbl_chat_key b ON a.key_id = b.id TYPE LEFT',
                    'where'             => [
                        'a.id_user'     => user('id'),
                        'a.is_read'     => 0,
                        'b.is_active'   => 1
                    ]
                ])->row();
                $data['jml_pesan']      = $jml_pesan->jml;
            } else {
                $data['cur_menu']       = uri_segment(1);
                $data['cur_sub']        = uri_segment(2);
            }
            $language = scandir(FCPATH . 'assets/lang/');
            foreach($language as $k => $v) {
                if(substr($v,0,1) == '.') unset($language[$k]);
            }
            $data['bahasa']         = $language;
            $layout                 = true;
            $force_access           = false;
            if($tipe != '' && $tipe != 'view') {
                $attr_view = explode(' ', $tipe);
                foreach($attr_view as $av) {
                    $attr_av = explode(':', $av);
                    if(count($attr_av) == 2) {
                        if($attr_av[0] == 'view') {
                            $view       = $attr_av[1];
                            $str_view   = str_replace('/', '_', $view);
                        }
                        else if($attr_av[0] == 'layout' && $attr_av[1] == 'false') $layout = false;
                        else if($attr_av[0] == 'access' && $attr_av[1] == 'true') $force_access = true;
                    }
                }
            }
            if($init_page == '404') {
                $view = 'errors/page_not_found';
                if(setting('interface') != 'public') $layout = false;
                update_data('tbl_user_log',['respon'=>404],'id',setting('last_id_log'));
            }
            if(!isset($data['title']) || !$data['title']) {
                $data['title']  = isset($data['menu_access']['title']) && $data['menu_access']['title'] ? $data['menu_access']['title'] : 'Unknown';
            }
            $data['uri_string']	= $CI->uri->uri_string();
            $access             = setting('interface') == 'public' ? true : $menu['access_view'];
            if($force_access) $access = true;
            if(strpos(base_url(),'assets') === false) {
                if($access || $force_view) {
                    if($layout) {
                        $content                = preg_replace('/<!--(.|\s)*?-->/', '',$CI->load->view($view,$data,true));
                        $data['view_content']   = rm_js(rm_css($content));
                        $data['css_content']    = render_css($content,$str_view);
                        $data['js_content']     = render_js($content,$str_view);
                        $data['file_upload_max_size']   = file_upload_max_size();
                        if(!isset($data['layout'])) {
                            $data['layout'] = setting('default_layout');
                        }
                        $CI->load->view('layout/'.$data['layout'],$data);
                    } else {
                        $CI->load->view($view,$data);
                    }
                } else {
                    update_data('tbl_user_log',['respon'=>403],'id',setting('last_id_log'));
                    $CI->load->view('errors/forbidden',$data);
                }
            }
        }
    } else {
        header('Content-Type: text/plain');
        echo $data;
    }
}

function menu($type="all",$segment_f=""){
    $menu                       = array();
    $cur_menu                   = array();
    if(user('id_group')) {
        $segment = $cur_segment = uri_segment(2) ? uri_segment(2) : uri_segment(1);
        if($segment_f) {
            $cur_segment        = $segment_f;
        }
        $cur_menu               = get_data('tbl_menu','target',$cur_segment)->row();
        if($cur_segment != uri_segment(1) && !$segment_f) {
            $parent_menu        = get_data('tbl_menu','target',uri_segment(1))->row();
            if(!isset($parent_menu->id)) {
                $cur_menu       = new stdClass();
            }
        }
        if($type == 'all' || $type == '') {
            $menu[0]                = get_menu( 'tbl_user_akses', 'tbl_menu', user('id_group') );
            foreach( $menu[0] as $m ){
                $menu[$m->id]       = get_menu( 'tbl_user_akses', 'tbl_menu', user('id_group') , $m->id );
                foreach($menu[$m->id] as $s) {
                    $menu[$s->id]   = get_menu( 'tbl_user_akses', 'tbl_menu', user('id_group') , $s->id );
                    foreach($menu[$s->id] as $e) {
                        $menu[$e->id]   = get_menu( 'tbl_user_akses', 'tbl_menu', user('id_group') , $e->id );
                    }
                }
            }
        }
    }
    if(isset($cur_menu->id)) {
        $access = get_data('tbl_user_akses',array(
            'where_array'   => array(
                'id_group'  => user('id_group'),
                'id_menu'   => $cur_menu->id
            )
        ))->row();
        if($segment_f && isset($access->id)) $access->act_view = 1;
    } else {
        $is_cur = false;
        if(isset($segment) && $segment == uri_segment(2)) {
            $segment = uri_segment(1);
            $cur_menu   = get_data('tbl_menu','target',$segment)->row();
            if(isset($cur_menu->id)) {
                $access = get_data('tbl_user_akses',array(
                    'where_array'   => array(
                        'id_group'  => user('id_group'),
                        'id_menu'   => $cur_menu->id
                    )
                ))->row();
                $is_cur = true;
            }
        }
        // karena jika menu tidak terdaftar di database berarti menu ini bebas digunakan semua role
        if(!$is_cur) {
            $access = new stdClass();
            $access->id             = 999;
            $access->act_view       = 1;
            $access->act_input      = 1;
            $access->act_edit       = 1;
            $access->act_delete     = 1;
            $access->act_additional = 1;
        }
    }
    if(isset($cur_menu->id) && !$cur_menu->is_active) {
        $access->act_view       = 0;
        $access->act_input      = 0;
        $access->act_edit       = 0;
        $access->act_delete     = 0;
        $access->act_additional = 0;
    }
    return array(
        'menu'              => $menu,
        'active_l1'         => isset($cur_menu->id) ? $cur_menu->level1 : 0,
        'active_l2'         => isset($cur_menu->id) ? $cur_menu->level2 : 0,
        'active_l3'         => isset($cur_menu->id) ? $cur_menu->level3 : 0,
        'active_l4'         => isset($cur_menu->id) ? $cur_menu->level4 : 0,
        'title'             => isset($cur_menu->id) ? lang($cur_menu->target,$cur_menu->nama) : '',
        'target'            => isset($cur_menu->id) ? $cur_menu->target : '',
        'access_view'       => isset($access->id) ? $access->act_view : 0,
        'access_input'      => isset($access->id) ? $access->act_input : 0,
        'access_edit'       => isset($access->id) ? $access->act_edit : 0,
        'access_delete'     => isset($access->id) ? $access->act_delete : 0,
        'access_additional' => isset($access->id) ? $access->act_additional : 0,
    );
}

function menu_tab($segment_f=""){
    $segment = $cur_segment = uri_segment(2) ? uri_segment(2) : uri_segment(1);
    if($segment_f) {
        $cur_segment        = $segment_f;
    }
    $cur_menu = get_data('tbl_menu','target',$cur_segment)->row();
    $menu = get_menu('tbl_user_akses', 'tbl_menu', user('id_group') , $cur_menu->parent_id);
    return $menu;
}

function get_access($target='') {
    $result         = [
        'access_view'       => 0,
        'access_input'      => 0,
        'access_edit'       => 0,
        'access_delete'     => 0,
        'access_additional' => 0
    ];
    $cur_menu       = get_data('tbl_menu','target',$target)->row();
    if(isset($cur_menu->id)) {
        $access = get_data('tbl_user_akses',array(
            'where_array'   => array(
                'id_group'  => user('id_group'),
                'id_menu'   => $cur_menu->id
            )
        ))->row();
        $result         = [
            'access_view'       => isset($access->id) ? $access->act_view : 0,
            'access_input'      => isset($access->id) ? $access->act_input : 0,
            'access_edit'       => isset($access->id) ? $access->act_edit : 0,
            'access_delete'     => isset($access->id) ? $access->act_delete : 0,
            'access_additional' => isset($access->id) ? $access->act_additional : 0,
        ];
    }
    return $result;
}

function denied() {
    $data   = array(
        'status'    => 'error',
        'message'   => lang('izin_ditolak')
    );
    update_data('tbl_user_log',['respon'=>403],'id',setting('last_id_log'));
    return $data;
}

function notifikasi() {
    $jml            = get_data('tbl_notifikasi',array('select'=>'count(id) AS jml','where_array'=>array('id_user'=>user('id'),'is_read'=>0)))->row();
    $data['count']  = isset($jml->jml) && $jml->jml ? $jml->jml : 0;
    $data['list']   = get_data('tbl_notifikasi',array(
        'where_array'   => array(
            'id_user'   => user('id')
        ),
        'limit'         => 5,
        'sort_by'       => 'notif_date',
        'sort'          => 'DESC'
    ))->result_array();
    return $data;
}

function render_css($content='',$str_view='') {
    $return_css  = '';
    $css         = '';
    preg_match_all('/<link.*?(.*?)>/si', $content, $res);
    if(isset($res[0])) {
        foreach($res[0] as $r) {
            $return_css .= $r.PHP_EOL;
        }
    }

    preg_match_all('/<style.*?>(.*?)<\/style>/si', $content, $res);
    if(isset($res[1])) {
        foreach($res[1] as $k => $r) {
            $buffer = preg_replace('!\s+!',' ',preg_replace('@(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|((?<!:)//.*)|[\t\r\n]|@i','',$r));
            // $css    .= ENVIRONMENT == 'production' ? $buffer : $r;
            $css    .= $r;
        }
    }
    $filename   = 'assets/cache/' . md5($str_view) . '.css';
    if($css) {
        $render = false;
        if(file_exists( $filename )) {
            $str_file   = file_get_contents($filename);
            if($str_file != $css) $render = true;
        } else $render = true;
        if($render) {
            $handle = fopen ($filename, "wb");
            if($handle) {
                fwrite ( $handle, $css );
            }
            fclose($handle);
        }
        $return_css .= file_exists( $filename ) ? '<link rel="stylesheet" type="text/css" href="' . base_url($filename) . '?v='.APP_VERSION.'" />' : '<style type="text/css">' . $css . '</style>';
    }
    return $return_css;
}

function render_js($content='',$str_view='') {
    $return_js  = '';
    $js         = '';
    preg_match_all('/<script.*?>(.*?)<\/script>/si', $content, $res);
    if(isset($res[1])) {
        foreach($res[1] as $k => $r) {
            if(strpos($res[0][$k], ' src=') !== false) {
                $return_js .= $res[0][$k].PHP_EOL;
            } else {
                $buffer = preg_replace('!\s+!',' ',preg_replace('@(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|((?<!:)//.*)|[\t\r\n]@i','',$r));
                // $js     .= ENVIRONMENT == 'production' ? $buffer : $r;
                $js     .= $r;
            }
        }
    }
    $filename   = 'assets/cache/' . md5($str_view) . '.js';
    if($js) {
        $render = false;
        if(file_exists( $filename )) {
            $str_file   = file_get_contents($filename);
            if($str_file != $js) $render = true;
        } else $render = true;
        if($render) {
            $handle = fopen ($filename, "wb");
            if($handle) {
                fwrite ( $handle, $js );
            }
            fclose($handle);
        }
        $return_js .= file_exists( $filename ) ? '<script type="text/javascript" src="' . base_url($filename) . '?v='.APP_VERSION.'"></script>' : '<script type="text/javascript">' . $js . '</script>';
    }
    return $return_js;
}

function rm_css($content='') {
    $content = preg_replace('/<link.*?(.*?)>/is','', $content);
    $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content);
    return $html;
}

function rm_js($content='') {
    $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
    return $html;
}

function setting($key='') {
    $CI     = get_instance();
    return $CI->config->item('setting_'.$key) ? $CI->config->item('setting_'.$key) : '';
}

function user($key='') {
    $CI     = get_instance();
    return $CI->config->item('user_'.$key) ? $CI->config->item('user_'.$key) : '';
}

function lang($key='',$default='') {
    $CI     = get_instance();
    $default_label = ENVIRONMENT == 'production' ? ucwords(strtolower(str_replace('_',' ',$key))) : '';
    if($default) $default_label = $default;
    return $CI->config->item('lang_'.$key) ? $CI->config->item('lang_'.$key) : $default_label;
}

function csrf_token($ajax=true,$tipe='echo') {
    $CI         = get_instance();
    $cur_page   = $CI->uri->uri_string();
    $last_page  = $CI->session->userdata('last_page');
    $CI->session->set_userdata('last_page',$CI->uri->uri_string());
    if(!$CI->session->userdata('csrf_token') || ($ajax.$cur_page == $last_page && !$CI->session->userdata('csrf_form'))) {
        $token      = rand();
        $CI->session->set_userdata('csrf_token',$token);
        if($ajax == false) $CI->session->set_userdata('csrf_form',true);
        if($tipe == 'echo') {
            echo $ajax ? encode_id([$token,rand(),strtotime('now')]) : $token;
        } else {
            return $ajax ? encode_id([$token,rand(),strtotime('now')]) : $token;
        }
    } else {
        $CI->session->unset_userdata('csrf_form');
        if($tipe == 'echo') {
            echo $ajax ? encode_id([$CI->session->userdata('csrf_token'),rand(),strtotime('now')]) : $CI->session->userdata('csrf_token');
        } else {
            return $ajax ? encode_id([$CI->session->userdata('csrf_token'),rand(),strtotime('now')]) : $CI->session->userdata('csrf_token');
        }
    }
}

function csrf_match() {
    $CI         = get_instance();
    $headers    = $CI->input->request_headers();
    if((isset($headers['X-Requested-With']) && $headers['X-Requested-With'] == 'XMLHttpRequest') || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        if((isset($headers['X-CSRF-Token']) && $headers['X-CSRF-Token'] == $CI->session->userdata('csrf_token')) || (isset($_SERVER['HTTP_X_CSRF_TOKEN']) && $_SERVER['HTTP_X_CSRF_TOKEN'] == $CI->session->userdata('csrf_token'))) {
            return true;
        } else {
            return false;
        }
    } else {
        if(post('csrf_token') == $CI->session->userdata('csrf_token')) {
            return true;
        } else {
            return false;
        }
    }
}

function flash_message($status='',$message='') {
    $CI         = get_instance();
    $CI->session->set_flashdata('message',$message);
    $CI->session->set_flashdata('status',$status);
}

function flash_body() {
    $CI         = get_instance();
    if($CI->session->flashdata('message') && $CI->session->flashdata('status')){
        echo ' data-status-open="'.$CI->session->flashdata('status').'" data-message-open="'.$CI->session->flashdata('message').'"';
    }
}

function include_view($view='',$data=[]) {
    if($view) {
        $CI         = get_instance();
        return $CI->load->view($view,$data,true);
    }
}

function send_mail($data=array(),$preview=false) {
    $CI                 = get_instance();
    $f_segment          = $CI->uri->segment(1);
    $class              = $CI->router->fetch_class();
    $method             = $CI->router->fetch_method();
    if(isset($data['view'])) {
        $view           = $data['view'];
    } else {
        $view           = $f_segment == $class ? $class . '/mailer_' . $method : $f_segment . '/' . $class . '/mailer_' . $method;
    }
    $data['content']    = $CI->load->view($view,$data,true);
    $message            = $CI->load->view('layout/mailer',$data,true);
    if($preview) {
        echo $message;
    } else {
        if(setting('smtp_server') && setting('smtp_port') && setting('smtp_password')) {
            $config = array(
                'protocol'     => 'smtp',
                'smtp_host'    => setting('smtp_server'),
                'smtp_port'    => setting('smtp_port'),
                'smtp_user'    => setting('smtp_email'),
                'smtp_pass'    => setting('smtp_password'),
                'mailtype'     => 'html',
                'charset'      => 'iso-8859-1',
                'wordwrap'     => FALSE
            );
        } else {
            $config        = array(
                'protocol'      => 'mail',
                'mailtype'      => 'html',
                'wordwrap'      => FALSE
            );
        }
        $email_sender       = setting('alias_email') ? setting('alias_email') : setting('smtp_email');
        $email_sender_name  = setting('nama_alias_email') ? setting('nama_alias_email') : setting('title');
        try {
            $CI->load->library('email', $config);
            $CI->email->set_newline("\r\n");
            $CI->email->from($email_sender,$email_sender_name);
            $CI->email->to($data['to']);
            if(isset($data['cc'])) {
                $CI->email->cc($data['cc']);
            }
            if(isset($data['bcc'])) {
                $CI->email->bcc($data['bcc']);
            }
            $CI->email->subject($data['subject']);
            $CI->email->message($message);
            if($CI->email->send()) {
                $response = array(
                    'status'    => 'success',
                    'message'   => lang('email_berhasil_terkirim')
                );
            } else {
                $response = array(
                    'status'    => 'failed',
                    'message'   => lang('email_gagal_terkirim')
                );
            }
        } catch (Exception $e) {
            $response = array(
                'status'    => 'failed',
                'message'   => lang('email_gagal_terkirim')
            );
        }
        return $response;
    }
}

function send_notif($notif_id='',$title='',$message='',$url=''){
    $content = array(
        "en" => $message
    );
    $heading = array(
        "en" => $title
    );

    if(is_array($notif_id)) {
        $player_id = $notif_id;
    } else {
        $player_id = array($notif_id);
    }

    $fields = array(
        'app_id'                => setting('onesignal_app_id'),
        'include_player_ids'    => $player_id,
        'data'                  => array('foo'=>'bar'), // harus ada isinya
        'contents'              => $content,
        'headings'              => $heading,
        'url'                   => $url
    );

    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                               'Authorization: Basic '.setting('onesignal_api_key')));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function uri_segment($segment=0) {
    $CI     = get_instance();
    return $CI->uri->segment($segment);
}

function dir_upload($dir='') {
    return 'assets/uploads/'.$dir.'/';
}
function delete_dir($directory, $empty = false) {
    if(substr($directory,-1) == "/") {
        $directory = substr($directory,0,-1);
    }

    if(!file_exists($directory) || !is_dir($directory)) {
        return false;
    } elseif(!is_readable($directory)) {
        return false;
    } else {
        $directoryHandle = opendir($directory);

        while ($contents = readdir($directoryHandle)) {
            if($contents != '.' && $contents != '..') {
                $path = $directory . "/" . $contents;

                if(is_dir($path)) {
                    delete_dir($path);
                } else {
                    @unlink($path);
                }
            }
        }

        closedir($directoryHandle);

        if($empty == false) {
            if(!rmdir($directory)) {
                return false;
            }
        }

        return true;
    }
}

function c_scandir($dir) {
    $handle = opendir($dir);
    if ( !$handle ) return array();
    $contents = array();
    while ( $entry = readdir($handle) ) {
        if ( $entry=='.' || $entry=='..' ) continue;
        $entry = $dir.DIRECTORY_SEPARATOR.$entry;
        if ( is_file($entry) ) {
            $contents[] = $entry;
        }else if ( is_dir($entry) ) {
            $contents = array_merge($contents, c_scandir($entry));
        }
    }
    closedir($handle);
    return $contents;
}

function debug($array=array()){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function c_password($string='') {
    if($string) {
        $string = password_hash(md5(xss_clean(trim($string,' '))),PASSWORD_DEFAULT,array('cost'=>COST));
    }
    return $string;
}

function post($post="",$pass_type="password_hash"){
    $CI     = get_instance();
    if($post && ($post != ':field' && $post != ':validation' && $post != ':upper')) {
        $is_daterange   = false;
        if(substr($post, 0, 2) == '::') {
            $check_post     = explode(' - ', $CI->input->post(str_replace('::', '', $post)));
            if(count($check_post) == 2 && strlen($check_post[0]) == 10 && strlen($check_post[1]) == 10) $is_daterange = true;
            else $post = str_replace('::', '', $post);
        }
        if($is_daterange) {
            return [
                date('Y-m-d',strtotime(str_replace('/', '-', $check_post[0]))),
                date('Y-m-d',strtotime(str_replace('/', '-', $check_post[1])))
            ];
        } else {
            if($pass_type == 'html') {
                return html_escape($CI->input->post($post, FALSE));
            } else {
                $str_post = $CI->input->post($post);
                if(!is_array($str_post)) {
                    $check_date = explode('/', $str_post);
                    if(count($check_date) == 3 && is_numeric($check_date[0]) && is_numeric($check_date[1]) && is_numeric($check_date[2]) && strlen($check_date[0]) == 2 && strlen($check_date[1]) == 2 && strlen($check_date[2]) == 4) {
                        $str_post = $check_date[2].'-'.$check_date[1].'-'.$check_date[0];
                    } elseif(count($check_date) == 3 && strlen($check_date[0]) == 2 && strlen($check_date[1]) == 2 && strlen($check_date[2]) > 4) {
                        $check_time = explode(' ', $check_date[2]);
                        if(count($check_time) == 2) {
                            $str_post = $check_time[0].'-'.$check_date[1].'-'.$check_date[0].' '.$check_time[1];
                        }
                    }
                }
                return xss_clean($str_post);
            }
        }
    } else {
        $value  = $CI->input->post();
        $data = array();
        $field = array();
        foreach($value as $key => $val){
            if($post == ':field') {
                $parse_key = explode('field_', $key);
                if(count($parse_key) == 2) {
                    $data[$parse_key[1]] = $val;
                }
            } elseif($post == ':validation') {
                $parse_key = explode('validation_', $key);
                if(count($parse_key) == 2) {
                    $parse_name = explode('name_', $parse_key[1]);
                    if(count($parse_name) == 2) {
                        $data[$parse_name[1]]['name'] = $val;
                    } else {
                        $data[$parse_key[1]]['validation'] = $val;
                    }
                }
            } else {
                if( $key != 'csrf_token' && (!is_array($val) || $key == 'id')){
                    if(is_array($val)) {
                        $data[$key] = $val;
                    } else {
                        if($key == "password"){
                            if($val) {
                                if($pass_type == 'password_hash') {
                                    $data[$key] = password_hash(md5(xss_clean(trim($val,' '))),PASSWORD_DEFAULT,array('cost'=>COST));
                                } elseif($pass_type == 'md5') {
                                    $data[$key] = md5(xss_clean(trim($val,' ')));
                                } else {
                                    $data[$key] = xss_clean(trim($val,' '));
                                }
                            }
                        } else {
                            $parse_key1 = explode('field_', $key);
                            $parse_key2 = explode('validation_', $key);
                            if(count($parse_key1) == 1 && count($parse_key2) == 1) {
                                $data[$key] = xss_clean(trim($val,' '));
                                if($post == ':upper') {
                                    $check_string1  = explode('/', $data[$key]);
                                    $check_string2  = explode('.', $data[$key]);
                                    $is_file        = false;
                                    if(count($check_string1) > 1 && count($check_string2) == 2) {
                                        $is_file    = true;
                                    }
                                    if(!$is_file && !filter_var($data[$key], FILTER_VALIDATE_EMAIL)) {
                                        $data[$key] = strtoupper($data[$key]);
                                    }
                                }
                                $check_date = explode('/', $data[$key]);
                                if(count($check_date) == 3 && is_numeric($check_date[0]) && is_numeric($check_date[1]) && is_numeric($check_date[2]) && strlen($check_date[0]) == 2 && strlen($check_date[1]) == 2 && strlen($check_date[2]) == 4) {
                                    $data[$key] = $check_date[2].'-'.$check_date[1].'-'.$check_date[0];
                                } elseif(count($check_date) == 3 && strlen($check_date[0]) == 2 && strlen($check_date[1]) == 2 && strlen($check_date[2]) > 4) {
                                    $check_time = explode(' ', $check_date[2]);
                                    if(count($check_time) == 2) {
                                        $data[$key] = $check_time[0].'-'.$check_date[1].'-'.$check_date[0].' '.$check_time[1];
                                    }
                                }
                                if(strpos($data[$key],'.') !== false && ctype_digit(str_replace('-','',str_replace('.','',$data[$key])))) {
                                    $c_exp = explode('.', $data[$key]);
                                    if(strlen($c_exp[count($c_exp)-1]) <= 3) {
                                        $data[$key] = str_replace('.','',$data[$key]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if(!$post || $post == ':upper') {
            foreach($value as $key => $val){
                $parse_key = explode('field_', $key);
                if(count($parse_key) == 2 && $parse_key[1] != 'csrf_token' && !is_array($CI->input->post($parse_key[1]))) {
                    $field[$parse_key[1]] = $val;
                }
            }
            foreach($field as $k => $v) {
                if(!isset($data[$k])) {
                    $data[$k] = false;
                } else {
                    if($v == 'date') {
                        if(count(explode('-', $data[$k])) == 0) {
                            if($data[$k]) {
                                $parseDate = explode('/', $data[$k]);
                                if(count($parseDate) == 3) {
                                    $data[$k] = $parseDate[2].'-'.$parseDate[1].'-'.$parseDate[0];
                                } else {
                                    $data[$k] = date('Y-m-d');
                                }
                            } else {
                                $data[$k] = date('Y-m-d');
                            }
                        }
                    } elseif($v == 'datetime') {
                        if(count(explode('-', $data[$k])) == 0) {
                            if($data[$k]) {
                                $parseDatetime = explode(' ', $data[$k]);
                                $parseDate = explode('/', $parseDatetime[0]);
                                if(count($parseDate) == 3) {
                                    $data[$k] = $parseDate[2].'-'.$parseDate[1].'-'.$parseDate[0];
                                    if(isset($parseDatetime[1])) {
                                        $data[$k] .= ' '.$parseDatetime[1];
                                    }
                                } else {
                                    $data[$k] = date('Y-m-d H:i:s');
                                }
                            } else {
                                $data[$k] = date('Y-m-d H:i:s');
                            }
                        }
                    } else {
                        if($data[$k]) {
                            if(strpos($data[$k],'.') !== false && ctype_digit(str_replace('-','',str_replace('.','',$data[$k])))) {
                                $c_exp = explode('.', $data[$k]);
                                if(strlen($c_exp[count($c_exp)-1]) <= 3) {
                                    $data[$k] = str_replace('.','',$data[$k]);
                                }
                            } if(strpos($data[$k],',') !== false && ctype_digit(str_replace(',','',$data[$k]))) {
                                $c_exp = explode(',', $data[$k]);
                                if(count($c_exp) == 2) {
                                    $data[$k] = str_replace(',','.',$data[$k]);
                                }
                            }
                        }
                    }
                }
            }
        } elseif($post == ':validation') {
            $data2 = $data3 = array();
            foreach($data as $k => $d) {
                $data2[] = $k;
            }
            for($i = count($data2) - 1; $i >= 0; $i-- ) {
                $data3[$data2[$i]] = $data[$data2[$i]];
            }
            $data = $data3;
        }
        return $data;
    }
}

function get($get=""){
    $CI     = get_instance();
    if( $get ) return $CI->input->get($get);
    else return $CI->input->get();
}

function add_tab($i=0) {
    $tab = '';
    for($j=0; $j<$i; $j++) {
        $tab .= "\t";
    }
    return $tab;
}

function save_data($tabel='',$data=array(),$validation=array(),$force=false) {
    $valid      = true;
    $message    = '';
    $id         = isset($data['id']) && $data['id'] ? $data['id'] : 0;
    $status     = 'failed';
    $menu       = isset($data['as_access']) ? menu('access',$data['as_access']) : menu('access');
    if(isset($data['as_access'])) {
        unset($data['as_access']);
    }
    if( (!$id && $menu['access_input']) || ($id && $menu['access_edit']) || $force == true || post('special') == 'special' ) {
        if($validation && is_array($validation)) {
            $unique_group   = [];
            $ug_name        = [];
            foreach($validation as $k => $v) {
                if(isset($data[$k])) {
                    $field2 = !is_array($data[$k]) ? xss_clean(trim($data[$k],' ')) : $data[$k];
                    $field = post($k) && !is_array(post($k)) ? xss_clean(trim(post($k),' ')) : '';
                    if(!$field && is_array(post($k)) && isset($data[$k])) {
                        $field = $k;
                    }
                    if(isset($v['name']) && isset($v['validation'])) {
                        $s = explode('|', $v['validation']);
                        foreach($s as $z) {
                            $p = explode(':', $z);
                            $l = isset($p[1]) && $p[1] ? $p[1] : 0;
                            if($p[0] == 'unique_group' && $field != '') {
                                $unique_group[$k]   = $field2;
                                $ug_name[$k]        = $v['name'];
                            }
                            if($message == '') {
                                if($p[0] == 'required' && $field == '') {
                                    $message = $v['name'] . ' '.lang('harus_diisi');
                                    $valid = false;
                                } elseif($p[0] == 'length' && strlen($field) != $l && $field != '') {
                                    $message = $v['name'] . ' '.lang('harus').' ' . $l .' '.lang('karakter');
                                    $valid = false;
                                } elseif($p[0] == 'max-length' && strlen($field) > $l && $field != '') {
                                    $message = $v['name'] . ' '.lang('maksimal').' ' . $l .' '.lang('karakter');
                                    $valid = false;
                                } elseif($p[0] == 'min-length' && strlen($field) < $l && $field != '') {
                                    $message = $v['name'] . ' '.lang('minimal').' ' . $l .' '.lang('karakter');
                                    $valid = false;
                                } elseif($p[0] == 'number' && !is_numeric(str_replace('.', '', $field)) && $field != '') {
                                    $message = $v['name'] . ' '.lang('harus_diisi_format_angka');
                                    $valid = false;
                                } elseif($p[0] == 'letter' && !ctype_alpha($field) && $field != '') {
                                    $message = $v['name'] . ' '.lang('harus_diisi_format_huruf');
                                    $valid = false;
                                } elseif($p[0] == 'alphanumeric' && !ctype_alnum($field) && $field != '') {
                                    $message = $v['name'] . ' '.lang('harus_diisi_format_huruf_atau_angka');
                                    $valid = false;
                                } elseif($p[0] == 'email' && !filter_var($field, FILTER_VALIDATE_EMAIL) && $field != '') {
                                    $message = $v['name'] . ' '.lang('harus_diisi_format_email').' (ex@email.xx)';
                                    $valid = false;
                                // } elseif($p[0] == 'phone' && !preg_match("/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/", $field) && $field != '') {
                                //     $message = $v['name'] . ' '.lang('harus_diisi_format_nomor_telepon');
                                //     $valid = false;
                                } elseif($p[0] == 'equal' && $field != post($l)) {
                                    $message = $v['name'] . ' ' . lang('tidak_cocok');
                                    $valid = false;
                                } elseif($p[0] == 'min' && is_numeric($field) && $field < $l) {
                                    $message = $v['name'] . ' '.lang('tidak_boleh_kurang_dari').' ' . $l;
                                    $valid = false;
                                } elseif($p[0] == 'max' && is_numeric($field) && $field > $l) {
                                    $message = $v['name'] . ' '.lang('tidak_boleh_lebih_dari').' ' . $l;
                                    $valid = false;
                                } elseif($p[0] == 'unique' && $field != '') {
                                    $arr = array(
                                        $k => $field2
                                    );
                                    if($id) $arr['id !='] = $id;
                                    $check = get_data($tabel,array('where_array'=>$arr))->row();
                                    if(isset($check->id)) {
                                        $desc       = isset($p[1]) && $p[1] == 'nodesc' ? ' ' : ' "' . $field2 . '" ';
                                        $message    = $v['name'] . $desc . lang('sudah_ada');
                                        $status     = 'info';
                                        $valid      = false;
                                    }
                                } elseif($p[0] == 'image' && $valid) {
                                    if($data[$k] || is_array($data[$k])) {
                                        if(is_array($data[$k])) {
                                            $l_image = array();
                                            if($id) {
                                                $last = get_data($tabel,'id',$id)->row_array();
                                                if(isset($last[$k]) && $last[$k]) {
                                                    $image_l = json_decode($last[$k],true);
                                                }
                                            } else {
                                                $image_l = array();
                                            }
                                            foreach($data[$k] as $kk => $dk) {
                                                if($dk) {
                                                    if(strpos($dk, 'temp') !== FALSE) {
                                                        $img        = basename($dk);
                                                        $temp_dir   = str_replace($img, '', $dk);
                                                        $e          = explode('.', $img);
                                                        $ext        = $e[count($e)-1];
                                                        $new_name   = md5(uniqid()).'.'.$ext;
                                                        $dest       = dir_upload(str_replace('tbl_', '', $tabel)).$new_name;
                                                        if(@copy($dk,$dest)) {
                                                            delete_dir(FCPATH . $temp_dir);
                                                            if(isset($image_l[$kk])) {
                                                                @unlink(dir_upload(str_replace('tbl_', '', $tabel)).$image_l[$kk]);
                                                            }
                                                            $l_image[$kk] = $new_name;
                                                        }
                                                    } else {
                                                        $l_image[$kk] = basename($dk);
                                                    }
                                                }
                                            }
                                            unset($data[$k]);
                                            $data[$k]   = json_encode($l_image);
                                        } else {
                                            $img        = basename($data[$k]);
                                            $temp_dir   = str_replace($img, '', $data[$k]);
                                            $e          = explode('.', $img);
                                            $ext        = $e[count($e)-1];
                                            $new_name   = md5(uniqid()).'.'.$ext;
                                            $dest       = dir_upload(str_replace('tbl_', '', $tabel)).$new_name;
                                            if(!@copy($data[$k],$dest))
                                            unset($data[$k]);
                                            else {
                                                delete_dir(FCPATH . $temp_dir);
                                                if($id) {
                                                    $last = get_data($tabel,'id',$id)->row_array();
                                                    if(isset($last[$k]) && $last[$k]) {
                                                        @unlink(dir_upload(str_replace('tbl_', '', $tabel)).$last[$k]);
                                                    }
                                                }
                                                $data[$k] = $new_name;
                                            }
                                        }
                                    } else {
                                        unset($data[$k]);
                                    }
                                } elseif($p[0] == 'file' && $valid) {
                                    if($data[$k]) {
                                        if(!is_dir(dir_upload(str_replace('tbl_', '', $tabel)))){
                                            $oldmask = umask(0);
                                            mkdir(dir_upload(str_replace('tbl_', '', $tabel)),0777);
                                            umask($oldmask);
                                        }
                                        $file       = basename($data[$k]);
                                        $temp_dir   = str_replace($file, '', $data[$k]);
                                        $e          = explode('.', $file);
                                        $ext        = $e[count($e)-1];
                                        $new_name   = md5(uniqid()).'.'.$ext;
                                        $dest       = dir_upload(str_replace('tbl_', '', $tabel)).$new_name;
                                        if(!@copy($data[$k],$dest))
                                            unset($data[$k]);
                                        else {
                                            delete_dir(FCPATH . $temp_dir);
                                            if($id) {
                                                $last = get_data($tabel,'id',$id)->row_array();
                                                if(isset($last[$k]) && $last[$k]) {
                                                    @unlink(dir_upload(str_replace('tbl_', '', $tabel)).$last[$k]);
                                                }
                                            }
                                            $data[$k] = $new_name;
                                        }
                                    } else {
                                        unset($data[$k]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if(!$message && count($unique_group) > 0) {
                if($id) $unique_group['id !='] = $id;
                $check = get_data($tabel,array('where_array'=>$unique_group))->row();
                if(isset($check->id)) {
                    $message = lang('kombinasi').' (' . implode(', ',$ug_name) . ') '.lang('sudah_ada');
                    $status = 'info';
                    $valid = false;
                }
            }
        }
        if(isset($data['password']) && $data['password'] == '') {
            unset($data['password']);
        }

        if($valid) {
            $create_by      = $create_at = $update_by = $update_at = false;
            $field_tabel    = get_field($tabel,'name');
            $data_save      = array();
            foreach($field_tabel as $f) {
                if($f == 'create_at') $create_at = true;
                else if($f == 'create_by') $create_by = true;
                else if($f == 'update_at') $update_at = true;
                else if($f == 'update_by') $update_by = true;
                if(isset($data[$f]) && !is_array($data[$f])) $data_save[$f] = $data[$f];
            }
            if($id || is_array($id)) {
                $autocode = get_data('tbl_kode',array('where_array'=>array(
                    'is_active' => 1,
                    'tabel'     => $tabel
                )))->result();
                foreach($autocode as $a) {
                    if(isset($data_save[$a->kolom])) {
                        if($data_save[$a->kolom]) unset($data_save[$a->kolom]);
                        else $data_save[$a->kolom] = generate_code($tabel,$a->kolom,$data_save);
                    } else {
                        $check_autocode = get_data($tabel,'id',$id)->row_array();
                        if(isset($check_autocode[$a->kolom]) && !$check_autocode[$a->kolom]) {
                            $data_save[$a->kolom] = generate_code($tabel,$a->kolom,$data_save);
                        }
                    }
                }
                if($update_at) $data_save['update_at'] = date('Y-m-d H:i:s');
                if($update_by) $data_save['update_by'] = user('nama');
                if(!is_array($id)) {
                    $save = update_data($tabel,$data_save,'id',$id);
                    if($save) {
                        $message = lang('data_berhasil_diperbaharui');
                        $status = 'success';
                    } else {
                        $message = lang('data_gagal_diperbaharui');
                    }
                } else {
                    $jml_update = 0;
                    foreach($id as $i) {
                        $save = update_data($tabel,$data_save,'id',$i);
                        if($save) $jml_update++;
                    }
                    if($jml_update == 0) $message = lang('tidak_ada_data_yang_diperbaharui');
                    else {
                        if(count($id) == 1) {
                            $message = lang('data_berhasil_diperbaharui');
                            $status = 'success';
                        } else {
                            $message    = $jml_update . ' '.lang('data_berhasil_diperbaharui');
                            $status     = 'success';
                        }
                    }
                }
            } else {
                $autocode = get_data('tbl_kode',array('where_array'=>array(
                    'is_active' => 1,
                    'tabel'     => $tabel
                )))->result();
                foreach($autocode as $a) {
                    $data_save[$a->kolom]    = generate_code($tabel,$a->kolom,$data_save);
                }
                if($create_at) $data_save['create_at'] = date('Y-m-d H:i:s');
                if($create_by) $data_save['create_by'] = user('nama');
                $save = insert_data($tabel,$data_save);
                if($save) {
                    $id = $save;
                    $message = lang('data_berhasil_disimpan');
                    $status = 'success';
                } else {
                    $message = lang('data_gagal_disimpan');
                }
            }
        }
    } else {
        $message = lang('izin_ditolak');
        update_data('tbl_user_log',['respon'=>403],'id',setting('last_id_log'));
    }

    $response = array(
        'status'    => $status,
        'message'   => $message,
        'id'        => $id
    );
    return $response;
}

function destroy_data($tabel='',$field='',$id='',$child=array(),$del_file='',$as_access='') {
    $status     = 'failed';
    $menu       = $as_access ? menu('access',$as_access) : menu('access');
    if( $menu['access_delete'] ) {
        $delete = false;
        if(is_array($field)) {
            $delete = delete_data($tabel,$field);
        } else {
            if(is_array($id)) {
                $jml_del = 0;
                foreach($id as $j) {
                    if( ($tabel == 'tbl_user' || $tabel == 'tbl_user_group') && $j == 1 ) {
                        $message = lang('izin_ditolak');
                        update_data('tbl_user_log',['respon'=>403],'id',setting('last_id_log'));
                    } elseif($tabel == 'tbl_user' && $j == user('id')) {
                        $message = lang('dilarang_menghapus_akun_sendiri');
                    } elseif($tabel == 'tbl_user_group' && $j == user('id_group')) {
                        $message = lang('hak_akses_ini_digunakan_akun_anda');
                    } else {
                        $last_data = get_data($tabel,$field,$j)->row_array();
                        $del = delete_data($tabel,$field,$j);
                        if($del && is_array($child) && count($child) > 0) {
                            foreach($child as $k => $c) {
                                if(is_array($c)) {
                                    foreach($c as $c1) {
                                        delete_data($c1,$k,$j);
                                    }
                                } else {
                                    delete_data($c,$k,$j);
                                }
                            }
                        }
                        if(is_array($del_file)) {
                            foreach($del_file as $df) {
                                if($del && isset($last_data[$df]) && $last_data[$df]) {
                                    @unlink(dir_upload(str_replace('tbl_', '', $tabel)).$last_data[$df]);
                                }
                            }
                        } else {
                            if($del && isset($last_data[$del_file]) && $last_data[$del_file]) {
                                @unlink(dir_upload(str_replace('tbl_', '', $tabel)).$last_data[$del_file]);
                            }
                        }
                        if($del) $jml_del++;
                    }
                }
                if($jml_del == 0) $message = lang('tidak_ada_data_yang_dihapus');
                else {
                    $message    = $jml_del . ' '.lang('data_berhasil_dihapus');
                    $status     = 'success';
                }
            } else {
                if( ($tabel == 'tbl_user' || $tabel == 'tbl_user_group') && $id == 1 ) {
                    $message = lang('izin_ditolak');
                    update_data('tbl_user_log',['respon'=>403],'id',setting('last_id_log'));
                } elseif($tabel == 'tbl_user' && $id == user('id')) {
                    $message = lang('dilarang_menghapus_akun_sendiri');
                } elseif($tabel == 'tbl_user_group' && $id == user('id_group')) {
                    $message = lang('hak_akses_ini_digunakan_akun_anda');
                } else {
                    $last_data  = get_data($tabel,$field,$id)->row_array();
                    $delete     = delete_data($tabel,$field,$id);
                    if($delete && is_array($child) && count($child) > 0) {
                        foreach($child as $k => $c) {
                            if(is_array($c)) {
                                foreach($c as $c1) {
                                    delete_data($c1,$k,$id);
                                }
                            } else {
                                delete_data($c,$k,$id);
                            }
                        }
                    }
                    if(is_array($del_file)) {
                        foreach($del_file as $df) {
                            if($delete && isset($last_data[$df]) && $last_data[$df]) {
                                @unlink(dir_upload(str_replace('tbl_', '', $tabel)).$last_data[$df]);
                            }
                        }
                    } else {
                        if($delete && isset($last_data[$del_file]) && $last_data[$del_file]) {
                            @unlink(dir_upload(str_replace('tbl_', '', $tabel)).$last_data[$del_file]);
                        }
                    }
                }
            }
        }
        if($delete) {
            $message    = lang('data_berhasil_dihapus');
            $status     = 'success';
        }
    } else {
        $message = lang('izin_ditolak');
        update_data('tbl_user_log',['respon'=>403],'id',setting('last_id_log'));
    }
    $response = array(
        'status'    => $status,
        'message'   => $message
    );
    return $response;
}

function button_serverside($class='btn-primary',$link='btn-detail',$label='fi-eye',$unique='act_detail',$condition=array()) {
    if(is_array($label)) $label = implode($label,'<<');
    $cond = '';
    if(is_array($condition) && count($condition) > 0) {
        $CI         = get_instance();
        if($CI->session->userdata('additionalSelect')) {
            $add_select = $CI->session->userdata('additionalSelect');
        } else {
            $add_select = array();
        }
        foreach($condition as $k => $c) {
            $e = explode(' ', $k);
            $add_select[$e[0]] = $e[0];
            if($cond != '') $cond .= '&&';
            if(is_array($c)) {
                $cond .= $k . '<<' . implode('||', $c);
            } else {
                $cond .= $k . '<<' . $c;
            }
        }
        $CI->session->set_userdata('additionalSelect',$add_select);
    }
    $str = str_replace(' ','',str_replace('.', '', $class)) . '>>' . str_replace(' ','',$link) . '>>' . trim($label,' ') . '>>' . trim($unique,' ') . '>>' . $cond;
    return base64_encode($str);
}

function data_serverside($config=array()) {
    $CI         = get_instance();
    $field      = post('field');
    $alias      = post('alias');
    $filter     = post('filter');
    $f_val      = post('f_val');
    $table      = post('table');
    $limit      = post('limit');
    $offset     = post('offset');
    $sort_by    = post('order_by');
    $sort       = post('order_type');
    $join       = array();
    $final_join = array();
    $select     = array();
    $like       = array();
    $where      = array();
    $action_button  = isset($config['action_button'])   ? $config['action_button'] : true;
    $is_filter      = false;
    $f_table        = get_field($table,'name');
    $field_length   = count($f_table);
    $select_length  = count($field);
    $uri_string     = str_replace('/','_',$CI->uri->uri_string()).'_'.$table;
    if(uri_segment(4)) {
        $uri_string = str_replace('_'.uri_segment(4), '', $uri_string);
    }
    $get_arr_cache  = [];
    if(file_exists(FCPATH . 'assets/json/cache_table.json') && ENVIRONMENT == 'production') {
        $get_content = json_decode(file_get_contents(FCPATH . 'assets/json/cache_table.json'),true);
        if(is_array($get_content)) $get_arr_cache = $get_content;
    }
    if(isset($get_arr_cache[$uri_string]) && is_array($get_arr_cache[$uri_string]) && ENVIRONMENT == 'production') {
        $final_select   = $get_arr_cache[$uri_string]['select'];
        if(isset($get_arr_cache[$uri_string]['join']) && $get_arr_cache[$uri_string]['join']) {
            $fix_join   = $get_arr_cache[$uri_string]['join'];
        }
    } else {
        foreach($field as $k) {
            $e = explode('.', $k);
            if($e[0] != $table) {
                $join[] = $e[0];
            }
        }
        if(count($join) > 0) {
            foreach($join as $j) {
                foreach($f_table as $ft) {
                    $e      = explode('id_', $ft, 2);
                    $f_j    = '';
                    if(count($e) == 2 && $e[0] == '' && $e[1] != '') {
                        if(strpos($j, $e[1]) !== FALSE) {
                            $f_j = $ft;
                        }
                    }
                    if($f_j) {
                        $final_join[$j] = $f_j;
                    }
                }
            }
        }
        foreach($field as $k => $f) {
            $ff = explode(' ', $f);
            if(count($ff) == 2) $f = $ff[1];
            if(isset($config['to_int'])) {
                $to_int = array();
                if(!is_array($config['to_int'])) {
                    $to_int = array($config['to_int']);
                } else {
                    $to_int = $config['to_int'];
                }
                $c_f = explode('.', $f);
                foreach($to_int as $ti) {
                    if($c_f[1] == $ti) {
                        $f = 'CONVERT('.$f.', UNSIGNED INTEGER)';
                    }
                }
            }
            $select[] = $f.' AS '.$alias[$k];
        }
        $final_select   = implode(', ', $select);
        if($CI->session->userdata('additionalSelect')) {
            $add_select = implode(',', $CI->session->userdata('additionalSelect'));
            $final_select .= ','.$add_select;
            $CI->session->unset_userdata('additionalSelect');
        }
        if(count($final_join) > 0) {
            $fix_join = array();
            foreach($final_join as $t => $o) {
                $on_join    = explode(' ', $t);
                $o_join     = count($on_join) == 2 ? $on_join[1] : $on_join[0];
                $fix_join[] = $t .' on ' . $table.'.'.$o.' = '.$o_join.'.id' . ' type ' . 'left';
            }
        }
        $component      = [
            'select'    => $final_select,
            'join'      => []
        ];
        if(isset($fix_join)) {
            $component['join']  = $fix_join;
        }
        if(ENVIRONMENT == 'production') {
            $arr_cache = $get_arr_cache;
            $arr_cache[$uri_string]['select']   = $component['select'];
            $arr_cache[$uri_string]['join']     = [];
            foreach($component['join'] as $j) {
                $arr_cache[$uri_string]['join'][] = $j;
            }

            $filename = FCPATH . 'assets/json/cache_table.json';
            $handle = fopen ($filename, "wb");
            if($handle) {
                fwrite ( $handle, json_encode($arr_cache,JSON_PRETTY_PRINT) );
            }
            fclose($handle);
            $oldmask = umask(0);
			chmod($filename, 0777);
			umask($oldmask);
        }
    }
    if($filter && is_array($filter)) {
        foreach($filter as $k => $f) {
            if(trim($f_val[$k],' ') != '') {
                $like[$f] = $f_val[$k];
                $check_daterange = explode(' - ', $like[$f]);
                if(count($check_daterange) == 2) {
                    $check_date1 = explode('/', $check_daterange[0]);
                    $check_date2 = explode('/', $check_daterange[1]);
                    if(count($check_date1) == 3 && is_numeric($check_date1[0]) && is_numeric($check_date1[1]) && is_numeric($check_date1[2]) && strlen($check_date1[0]) == 2 && strlen($check_date1[1]) == 2 && strlen($check_date1[2]) == 4 && count($check_date2) == 3 && is_numeric($check_date2[0]) && is_numeric($check_date2[1]) && is_numeric($check_date2[2]) && strlen($check_date2[0]) == 2 && strlen($check_date2[1]) == 2 && strlen($check_date2[2]) == 4) {
                        $where['DATE('.$f.') >='] = $check_date1[2].'-'.$check_date1[1].'-'.$check_date1[0];
                        $where['DATE('.$f.') <='] = $check_date2[2].'-'.$check_date2[1].'-'.$check_date2[0];
                        unset($like[$f]);
                    }
                }
                if(isset($like[$f])) {
                    $check_date = explode('/', $like[$f]);
                    if(count($check_date) == 3 && is_numeric($check_date[0]) && is_numeric($check_date[1]) && is_numeric($check_date[2]) && strlen($check_date[0]) == 2 && strlen($check_date[1]) == 2 && strlen($check_date[2]) == 4) {
                        $where['DATE('.$f.')'] = $check_date[2].'-'.$check_date[1].'-'.$check_date[0];
                        unset($like[$f]);
                    }
                }
            }
        }
    }
    
    $arr_q = array(
        'select'    => $final_select,
        'limit'     => $limit,
        'offset'    => $offset
    );
    if(isset($config['select'])) {
        $arr_q['select']    .= ','.$config['select'];
    }
    if(count($like) > 0) {
        $arr_q['like']  = $like;
        $is_filter      = true;
    }
    if(count($where) > 0) {
        $arr_q['where'] = $where;
        $is_filter      = true;
    }
    $join_q = array();
    if(isset($fix_join) > 0) {
        $join_q = $fix_join;
    }
    if(isset($config['join'])) {
        foreach($config['join'] as $cj) {
            $join_q[] = $cj;
        }
    }
    $arr_a = array('select'=>'COUNT('.$field[0].') AS jml','join');
    $arr_a['join'] = $join_q;
    if(($table == 'tbl_user' || $table == 'tbl_user_group') && user('id') > 1) {
        $arr_q['where'][$table.'.id >'] = 1;
        $arr_a['where'][$table.'.id >'] = 1;
    }
    if(isset($config['where']) && is_array($config['where'])) {
        foreach($config['where'] as $key_where => $val_where) {
            $arr_q['where'][$key_where] = $val_where;
            $arr_a['where'][$key_where] = $val_where;
        }
    }
    if(isset($config['where_in']) && is_array($config['where_in'])) {
        foreach($config['where_in'] as $key_where => $val_where) {
            $arr_q['where_in'][$key_where] = $val_where;
            $arr_a['where_in'][$key_where] = $val_where;
        }
    }
    if(count($join_q) > 0) {
        $arr_q['join'] = $join_q;
    }
    if($sort && $sort_by) {
        $arr_q['sort_by']   = $sort_by;
        $arr_q['sort']      = $sort;
    } elseif(isset($config['sort_by'])) {
        $csort_by   = $config['sort_by'];
        $csort      = isset($config['sort']) ? $config['sort'] : 'ASC';
        if($csort && $csort_by) {
            $arr_q['sort_by']   = $csort_by;
            $arr_q['sort']      = $csort;
        }
    }
    if(isset($config['group_by'])) {
        $arr_q['group_by']  = $config['group_by'];
        $arr_a['select']    = 'COUNT(DISTINCT(`'.$config['group_by'].'`)) AS jml';
    }
    $query = get_data($table,$arr_q)->result_array();

    if($is_filter) {
        if(isset($arr_q['limit'])) unset($arr_q['limit']);
        if(isset($arr_q['offset'])) unset($arr_q['offset']);
        if(isset($arr_q['sort_by'])) unset($arr_q['sort_by']);
        if(isset($arr_q['sort'])) unset($arr_q['sort']);
        $arr_q['select']    = 'COUNT('.$field[0].') AS jml';
        $query_jml          = get_data($table,$arr_q)->row_array();
        $jml_filter         = $query_jml['jml'] ? $query_jml['jml'] : 0;
    }
    $query_all          = get_data($table,$arr_a)->row_array();
    $jml_all            = $query_all['jml'] ? $query_all['jml'] : 0;

    $rm_tbl             = str_replace('tbl_', '', $table);

    $menu           = isset($config['as_access']) ? menu('access',$config['as_access']) : menu('access');
    $link_view      = isset($config['link_view']) ? $config['link_view'] : '';
    $link_edit      = isset($config['link_edit']) ? $config['link_edit'] : '';
    $link_delete    = isset($config['link_delete']) ? $config['link_delete'] : '';

    if(($field_length - 5) <= $select_length) {
        $menu['access_view']    = false;
    }
    if(isset($config['access_view'])) {
        $menu['access_view']    = $config['access_view'] ? true : false;
    }
    if(isset($config['access_edit'])) {
        $menu['access_edit']    = $config['access_edit'] ? true : false;
    }
    if(isset($config['access_delete'])) {
        $menu['access_delete']  = $config['access_delete'] ? true : false;
    }
    if(!$action_button) {
        $menu['access_edit']    = 0;
        $menu['access_delete']  = 0;
    }

    $data = array(
        'status'        => 'success',
        'data'          => $query,
        'jmlShow'       => count($query),
        'jmlAll'        => $jml_all,
        'accessView'    => $menu['access_view'],
        'accessEdit'    => $menu['access_edit'],
        'accessDelete'  => $menu['access_delete'],
        'linkView'      => $link_view,
        'linkEdit'      => $link_edit,
        'linkDelete'    => $link_delete,
        'dirUpload'     => base_url(dir_upload($rm_tbl))
    );
    
    if(isset($config['button'])) {
        if(is_array($config['button'])) {
            $data['additionalButton'] = $config['button'];
        } else {
            $data['additionalButton'] = array($config['button']);
        }
    }
    if(isset($jml_filter)) $data['jmlFilter'] = $jml_filter;
    return $data;
}

function generate_type_code($str="",$data_field=array()){
    preg_match_all('/{(.*?)}/', $str, $res);
    $i  = $res[1];

    $m_romawi   = array('01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI','07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII');
    $m_id       = array('01'=>'JAN','02'=>'PEB','03'=>'MAR','04'=>'APR','05'=>'MEI','06'=>'JUN','07'=>'JUL','08'=>'AGU','09'=>'SEP','10'=>'OKT','11'=>'NOP','12'=>'DES');
    $m_idfull   = array('01'=>'JANUARI','02'=>'PEBRUARI','03'=>'MARET','04'=>'APRIL','05'=>'MEI','06'=>'JUNI','07'=>'JULI','08'=>'AGUSTUS','09'=>'SEPTEMBER','10'=>'OKTOBER','11'=>'NOPEMBER','12'=>'DESEMBER');
    $string     = $str;
    $result     = '';
    if(count($i) == 0){
        $result = $str;
    }else{
        foreach($i as $j => $k){
            if($k == 'Y')                           $rs[$j] = date('Y');
            else if($k == 'y')                      $rs[$j] = date('y');
            else if($k == 'm')                      $rs[$j] = date('m');
            else if(strtolower($k) == 'r')          $rs[$j] = $m_romawi[date('m')];
            else if($k == 'M')                      $rs[$j] = strtoupper(date('M'));
            else if(strtolower($k) == 'month')      $rs[$j] = strtoupper(date('F'));
            else if(strtolower($k) == 'bln')        $rs[$j] = $m_id[date('m')];
            else if(strtolower($k) == 'bulan')      $rs[$j] = $m_idfull[date('m')];
            else if(strtolower($k) == 'd')          $rs[$j] = date('d');
            else if(strpos($k, 'field_') !== false) {
                $f = str_replace('field_', '', $k);
                if(isset($data_field[$f])) $rs[$j] = $data_field[$f];
                else $rs[$j] = '{'.$k.'}';
            }
            else $rs[$j] = '{'.$k.'}';

            $m          = explode('{'.$k.'}',$string,2);
            $result    .= $m[0] . $rs[$j];
            if(isset($m[1]) && strlen($m[1]) == 1){
                $result.= $m[1];
            }
            $string     = $m[1];
        }
    }
    return $result;
}

function generate_code($table="",$column="",$data_field=array()){
    $data           = get_data('tbl_kode',array('where_array'=>array('tabel'=>$table,'kolom'=>$column)))->row();
    if(isset($data->id)) {
        $jumlah_digit   = $data->panjang;
        $prefix         = generate_type_code($data->awalan,$data_field);
        $suffix         = generate_type_code($data->akhiran,$data_field);

        if($jumlah_digit) {
            $result   = get_code($table,$prefix,$suffix,$jumlah_digit,$column)->row();
            $code_max   = $result->k;
            $code       = (int) $code_max;
            $new_code   = $code + 1;
            if($jumlah_digit == 1)
                return $prefix . $new_code .$suffix;
            else
                return $prefix . sprintf("%0".$jumlah_digit."s",$new_code) . $suffix;
        } else
            return $prefix . $suffix;
    } else
        return 'undefined';
}

function option($val='',$label='',$value='',$tipe='echo') {
    $html = $val == $value || strtoupper($val) == $value ? '<option value="'.$val.'" selected>'.$label.'</option>' . PHP_EOL : '<option value="'.$val.'">'.$label.'</option>' . PHP_EOL;
    if($tipe == 'echo') {
        echo $html;
    } else {
        return $html;
    }
}

function select_option($data=array(),$value='',$label='',$val='',$all=false,$lbl='') {
    $html = $all ? option('all','Semua '.$lbl,'','return') : option('','','','return');
    foreach($data as $d) {
        $html .= option($d[$value],$d[$label],$val,'return');
    }
    return $html;
}

function encode_string($input) {
    $ci = get_instance();
    $ci->load->library('encryption');
    $enc_username=$ci->encryption->encrypt($input);
    return str_replace(array('+', '/', '='), array('-', '_', 'b4yU'), $enc_username);
}

function decode_string($input) {
    $ci = get_instance();
    $ci->load->library('encryption');
    $dec_username=str_replace(array('-', '_', 'b4yU'), array('+', '/', '='), $input);
    return $ci->encryption->decrypt($dec_username);
}

function encode_id($id=0) {
    $CI = get_instance();
    $CI->load->library('hashid');
    if(!is_array($id)) {
        $id = [$id,rand()];
    }
    return $CI->hashid->encode($id);
}

function decode_id($encode_id='') {
    $CI = get_instance();
    $CI->load->library('hashid');
    return $CI->hashid->decode($encode_id);
}

function date_indo($date="",$full_date=true) {
    if(strlen($date) == 8) {
        $date = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' .substr($date, 6, 2);
    }
    if(!$date)  $date = date('Y-m-d H:i:s');
    $arrayBulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
    $result     = '-';
    if($date != '0000-00-00' && $date != '0000-00-00 00:00:00') {
        $tahun      = date('Y',strtotime($date));
        $bulan      = date('m',strtotime($date));
        $tgl        = date('d',strtotime($date));
        $result     = $tgl . " " . $arrayBulan[(int)$bulan-1] . " ". $tahun;
        if($date == date('Y-m-d H:i:s',strtotime($date)) && $full_date){
            $result .= ' Jam '.date('H:i',strtotime($date));
        }
    }
    return($result);
}

function bulan($bln='') {
    $bln = $bln == '' ? date('m') : sprintf('%02s',$bln);

    $arr_bln = array(
        '01'    => 'Januari',
        '02'    => 'Februari',
        '03'    => 'Maret',
        '04'    => 'April',
        '05'    => 'Mei',
        '06'    => 'Juni',
        '07'    => 'Juli',
        '08'    => 'Agustus',
        '09'    => 'September',
        '10'    => 'Oktober',
        '11'    => 'November',
        '12'    => 'Desember'
    );
    return $arr_bln[$bln];
}

function date_lang($date="",$full_date=true) {
    if(strlen($date) == 8) {
        $date = substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' .substr($date, 6, 2);
    }
    if(!$date)  $date = date('Y-m-d H:i:s');
    $arrayBulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
    $result     = '-';
    if($date != '0000-00-00' && $date != '0000-00-00 00:00:00') {
        $tahun      = date('Y',strtotime($date));
        $bulan      = date('m',strtotime($date));
        $tgl        = date('d',strtotime($date));
        $result     = $tgl . " " . lang(strtolower($arrayBulan[(int)$bulan-1])) . " ". $tahun;
        if($date == date('Y-m-d H:i:s',strtotime($date)) && $full_date){
            $result .= ' '.date('H:i',strtotime($date));
        }
    }
    return($result);
}

function month_lang($bln='') {
    $bln = $bln == '' ? date('m') : sprintf('%02s',$bln);

    $arr_bln = array(
        '01'    => 'Januari',
        '02'    => 'Februari',
        '03'    => 'Maret',
        '04'    => 'April',
        '05'    => 'Mei',
        '06'    => 'Juni',
        '07'    => 'Juli',
        '08'    => 'Agustus',
        '09'    => 'September',
        '10'    => 'Oktober',
        '11'    => 'November',
        '12'    => 'Desember'
    );
    return lang(strtolower($arr_bln[$bln]));
}

function timeago($date,$timeago_only=false) {
    $timestamp = strtotime($date);

    $strTime = array(lang('detik'), lang('menit'), lang('jam'), lang('hari'), lang('bulan'), lang('tahun'));
    $length = array("60","60","24","30","12","10");

    $currentTime = time();
    if($currentTime >= $timestamp) {
        if(($timestamp + (60*60*24*6)) > $currentTime || $timeago_only) {
            $diff     = time()- $timestamp;
            for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
                $diff = $diff / $length[$i];
            }

            $diff = round($diff);
            return $diff . " " . strtolower($strTime[$i] . " " . lang('yang_lalu'));
        } else {
            return date_indo($date);
        }
    }
}
function c_date($date,$full=true) {
    if($date) {
        if(strlen($date) == 10 || !$full) {
            return $date == '0000-00-00' ? '' : date('d/m/Y',strtotime($date));
        } else {
            return $date == '0000-00-00 00:00:00' ? '' : date('d/m/Y H:i',strtotime($date));
        }
    } else return '';
}
function custom_format($value,$absolute=false,$decimal=0) {
    if($value) {
        if($absolute) $value = abs($value);
        return $value < 0 ? '<span class="currency-negative">('.number_format(abs($value),$decimal,',','.').')</span>' : number_format($value,$decimal,',','.');
    } else {
        return $value == 0 ? 0 : '';
    }
}
function c_upper($data='') {
    if(is_array($data)) {
        $res = array();
        foreach($data as $k => $v) {
            $res[$k]    = strtoupper($v);
        }
        return $res;
    } else {
        return strtoupper($data);
    }
}
function terbilang($angka="0") {
    $angka = (float)$angka;
    $bilangan = array('','Satu','Dua','Tiga','Empat','Lima','Enam','Tujuh','Delapan','Sembilan','Sepuluh','Sebelas');
    if ($angka < 12) {
        return $bilangan[$angka];
    } else if ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } else if ($angka < 100) {
        $hasil_bagi = (int)($angka / 10);
        $hasil_mod = $angka % 10;
        return trim(sprintf('%s Puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
    } else if ($angka < 200) { return sprintf('Seratus %s', terbilang($angka - 100));
    } else if ($angka < 1000) { $hasil_bagi = (int)($angka / 100); $hasil_mod = $angka % 100; return trim(sprintf('%s Ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
    } else if ($angka < 2000) { return trim(sprintf('Seribu %s', terbilang($angka - 1000)));
    } else if ($angka < 1000000) { $hasil_bagi = (int)($angka / 1000); $hasil_mod = $angka % 1000; return sprintf('%s Ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod));
    } else if ($angka < 1000000000) { $hasil_bagi = (int)($angka / 1000000); $hasil_mod = $angka % 1000000; return trim(sprintf('%s Juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000) { $hasil_bagi = (int)($angka / 1000000000); $hasil_mod = fmod($angka, 1000000000); return trim(sprintf('%s Milyar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else if ($angka < 1000000000000000) { $hasil_bagi = $angka / 1000000000000; $hasil_mod = fmod($angka, 1000000000000); return trim(sprintf('%s Triliun %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
    } else {
        return 'Data Salah';
    }
}

function string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function template_pdf($data=[],$key='',$periode='') {
    $konten         = '<h3 style="text-align: center; padding: 20px;">Template belum diatur</h3>';
    $template 		= get_data('tbl_template_cetak',[
        'where'     => [
            'key'   => $key,
            'periode <='    => $periode
        ],
        'sort_by'   => 'periode',
        'sort'      => 'DESC'
    ])->row();
    if(!isset($template->id)) {
        $template       = get_data('tbl_template_cetak',[
            'where'     => [
                'key'   => $key,
                'periode >='    => $periode
            ],
            'sort_by'   => 'periode',
            'sort'      => 'ASC'
        ])->row();
    }
    if(isset($template->id) && is_array($data)) {
        $konten 	= html_entity_decode($template->konten);
        foreach(json_decode($template->variabel,true) as $v) {
            if(isset($data[$v])) {
                $konten = str_replace([
                    '{{'.$v.'}}',
                    '{{'.strtoupper($v).'}}'
                ],[
                    html_entity_decode($data[$v]),
                    strtoupper(html_entity_decode($data[$v]))
                ],$konten);
            }
        }
        $konten         = str_replace(['<p>---new_page---</p>','---new_page---'],'<div class="new-page"></div>',$konten);
        $konten         = preg_replace( "/\r|\n/", "", $konten);
        $konten         = str_replace('<p><p>','<p>',$konten);
        $konten         = str_replace('<p><p ','<p ',$konten);
        $konten         = str_replace('<p><h ','<h ',$konten);
        $konten         = str_replace(['</p></p></p>','</p></p>'],'</p>',$konten);
        $konten         = str_replace(['<p>&nbsp;</p>','<p> </p>','<p></p>'],'<br />',$konten);
        $replace_string = string_between($konten, '<img', 'assets');
        $konten         = str_replace($replace_string,' src="',$konten);
    }
    return $konten;
}
function pdf_img($konten='') {
    $replace_string = string_between($konten, '<img', 'assets');
    $konten         = str_replace($replace_string,' src="',$konten);
    return $konten;
}
function to_roman($number) {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}
function c_percent($decimal) {
    return $decimal == floor($decimal) ? floor($decimal) : str_replace('.', ',', $decimal);
}
function hari($date) {
    $arr = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu'
    ];
    return $arr[date('l',strtotime($date))];
}
function include_lang($_l) {
    $CI     = get_instance();
    if(file_exists(FCPATH . 'assets/lang/'.setting('language').'/'.$_l.'.json')) {
        $json_to_array   = json_decode(file_get_contents(FCPATH . 'assets/lang/'.setting('language').'/'.$_l.'.json'),true);
        if(is_array($json_to_array)) {
            foreach($json_to_array as $jk => $jv) {
                $CI->config->set_item('lang_'.$jk,$jv);
            }
        }
    }
}

function file_upload_max_size() {
    static $max_size = -1;

    if ($max_size < 0) {
        $post_max_size = parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        $upload_max = parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}
function parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

function KonDecRomawi($angka)
{
    $hsl = "";
    if ($angka < 1 || $angka > 5000) { 
        // Statement di atas buat nentuin angka ngga boleh dibawah 1 atau di atas 5000
        $hsl = "Batas Angka 1 s/d 5000";
    } else {
        while ($angka >= 1000) {
            // While itu termasuk kedalam statement perulangan
            // Jadi misal variable angka lebih dari sama dengan 1000
            // Kondisi ini akan di jalankan
            $hsl .= "M"; 
            // jadi pas di jalanin , kondisi ini akan menambahkan M ke dalam
            // Varible hsl
            $angka -= 1000;
            // Lalu setelah itu varible angka di kurangi 1000 ,
            // Kenapa di kurangi
            // Karena statment ini mengambil 1000 untuk di konversi menjadi M
        }
    }


    if ($angka >= 500) {
        // statement di atas akan bernilai true / benar
        // Jika var angka lebih dari sama dengan 500
        if ($angka > 500) {
            if ($angka >= 900) {
                $hsl .= "CM";
                $angka -= 900;
            } else {
                $hsl .= "D";
                $angka-=500;
            }
        }
    }
    while ($angka>=100) {
        if ($angka>=400) {
            $hsl .= "CD";
            $angka -= 400;
        } else {
            $angka -= 100;
        }
    }
    if ($angka>=50) {
        if ($angka>=90) {
            $hsl .= "XC";
            $angka -= 90;
        } else {
            $hsl .= "L";
            $angka-=50;
        }
    }
    while ($angka >= 10) {
        if ($angka >= 40) {
            $hsl .= "XL";
            $angka -= 40;
        } else {
            $hsl .= "X";
            $angka -= 10;
        }
    }
    if ($angka >= 5) {
        if ($angka == 9) {
            $hsl .= "IX";
            $angka-=9;
        } else {
            $hsl .= "V";
            $angka -= 5;
        }
    }
    while ($angka >= 1) {
        if ($angka == 4) {
            $hsl .= "IV"; 
            $angka -= 4;
        } else {
            $hsl .= "I";
            $angka -= 1;
        }
    }

    return ($hsl);
}

function m_number($n) {
    // first strip any formatting;
    $n = (0+str_replace(",", "", $n));

    // is this a number?
    if (!is_numeric($n)) return false;

    // now filter it;
    if ($n > 1000000 or $n < -1000000) return round(($n/1000000), 0);

    return number_format($n);
}

function insert_view_report($value){
    if(!$value): $value = 0; endif;
    $setting = setting('report_view');
    if($setting):
        $x      = (float) $setting;
        $value  = (float) $value;
        $value  = $value * $x;
    endif;
    return $value;
}

function view_report($value){
    if(!$value): $value = 0; endif;
    $setting = setting('report_view');
    if($setting):
        $x      = (float) $setting;
        $value  = (float) $value;
        $value  = $value / $x;
    endif;
    return $value;
}

function get_view_report(){
    $setting = setting('report_view');
    $value = '';
    if($setting):
        $x      = (int) $setting;
        if($x<1000000):
            $value = lang('ribuan');
        else:
            $value = lang('jutaan');
        endif;
    endif;
    return $value;
}

function insert_view_report_arr($arr){
   if(!$arr):
    $arr = array();
    $result = array();
   endif;
    
   $setting = setting('report_view');
   if($setting):
    $x = (float) $setting;
        foreach ($arr as $k => $v) {
       $value  = (float) $v;
           $value  = $value * $x;
           $result[$k] = $value;
        }
    endif; 
    return $result;
}

function checkInputNumber($input){
    $val = 0;
    if($input): 
        $input  = str_replace('.', '', $input); 
        $val    = str_replace(',', '.', $input); 
    endif;
    return $val;
}

function multidimensional_search($parents, $searched) {
  if (empty($searched) || empty($parents)) {
    return false;
  }

  foreach ($parents as $key => $value) {
    $exists = true;
    foreach ($searched as $skey => $svalue) {
      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
    }
    if($exists){ return $key; }
  }

  return false;
}

function data_cabang($access=""){
    $segment = $cur_segment = uri_segment(2) ? uri_segment(2) : uri_segment(1);
    if($access) {
        $cur_segment        = $access;
    }
    $cabang_user  = get_data('tbl_user',[
        'where' => [
            'is_active' => 1,
            'id_group'  => id_group_access($cur_segment)
        ]
    ])->result();

    $kode_cabang          = [];
    foreach($cabang_user as $c) $kode_cabang[] = $c->kode_cabang;

    $cab = get_data('tbl_m_cabang','id',user('id_struktur'))->row();

    $id = user('id_struktur');
    if($id){
        $cab = get_data('tbl_m_cabang','id',$id)->row();
    }else{
        $id = user('kode_cabang');
        $cab = get_data('tbl_m_cabang','kode_cabang',$id)->row();
    }

    if(isset($cab->id)){ 
        $x ='';
        for ($i = 1; $i <= 4; $i++) { 
            $field = 'level' . $i ;

            if($cab->id == $cab->$field) {
                $x = $field ; 
            }    
        }    
    }
         
 
    $data['cabang']            = get_data('tbl_m_cabang a',[
        'select'    => 'distinct a.kode_cabang,a.nama_cabang',
        'where'     => [
            'a.is_active' => 1,
            'a.'.$x => $cab->id,
            'a.kode_cabang' => $kode_cabang
        ]
    ])->result_array();

    $data['cabang_input'] = get_data('tbl_m_cabang a',[
        'select'    => 'distinct a.kode_cabang,a.nama_cabang',
        'where'     => [
            'a.is_active' => 1,
            'a.kode_cabang' => user('kode_cabang')
        ]
    ])->result_array();

    $data['tahun'] = get_data('tbl_tahun_anggaran','kode_anggaran',user('kode_anggaran'))->result();
    return $data;
}

function weekOfMonth($when) {
    if ($when === null) $when = time();
    $week = date('W', strtotime($when)); // note that ISO weeks start on Monday
    $firstWeekOfMonth = date('W', strtotime(date('Y-m-01', strtotime($when))));
    return 1 + ($week < $firstWeekOfMonth ? $week : $week - $firstWeekOfMonth);
}

function weekOfMonth2($p1,$p2){
    $t1 = strtotime($p1);
    $t2 = strtotime($p2);
    $out = array();
    $res = array();
    while ($t1 <= $t2) {
        $week = date('W', $t1);
        if(!in_array($week,$out)):
            $out[] = $week;
            $res[$week] = date("W-m-Y", $t1);
        endif;
        $t1 = strtotime('+1 day', $t1);
    }
    return ['out' => $out, 'res' => $res];
}
function month_week_number($year,$weeks){
    $data = [];
    foreach ($weeks as $v) {
        $int = explode("-", $v);
        $month = (new DateTime())->setISODate($year, $int[0])->format('m');
        if(isset($data[$month])):
            $total = ($data[$month]) + 1;
        else:
            $total = 1;
        endif;
        $data[$month] = $total;
    }
    return $data;
}
function arrWeekOfMonth($year){
    $date_string = $year . 'W' . sprintf('%02d', "01");
    $first_day = sprintf('%02d', date('j', strtotime($date_string)));
    $weekOfMonth2   = weekOfMonth2($year.'-01-'.$first_day, $year.'-12-31');
    $arrMonth       = month_week_number($year,$weekOfMonth2['out']);
    
    $data = array(
        'week'      => $weekOfMonth2['out'],
        'detail'    => $weekOfMonth2['res'],
        'month'     => $arrMonth,
    );
    return $data;
}

function arrNpl(){
    return [
        "1" => "NPL Kredit Produktif",
        "2" => "NPL Kredit Konsumtif",
    ];
}

function save_kolektibilitas_detail($data,$status){
    if($status):
        foreach ($data as $id => $v) {
            update_data('tbl_kolektibilitas_detail',$v,'id',$id);
        }
    endif;
}

function kali_minus($value,$type){
    if($type == 1):
        $value = (float) $value * (-1);
    endif;
    return $value;
}