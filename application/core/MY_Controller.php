<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        if(ENVIRONMENT == 'production' && is_writable(FCPATH . 'application/admin')) {
            render('Warning!!! Folder admin masih writeable. Demi keamanan, aplikasi tidak dapat digunakan sebelum folder admin dibuat read only'); die;
        }

        $setting    = get_data('tbl_setting')->result();
        foreach($setting as $s) {
            if(!$this->config->item('setting_'.$s->_key)) {
                $this->config->set_item('setting_'.$s->_key,$s->_value);
            }
        }

        if($this->agent->is_browser()) {
            if(!in_array($this->agent->browser(),['Chrome','Firefox','Opera'])) {
                #echo $this->load->view('errors/not_support',[],true); die;
            }
        }

        $lang = get_cookie('lang');
        if(!$lang || !is_dir(FCPATH . 'assets/lang/'.$lang)) $lang = 'id';
        $this->config->set_item('setting_language',$lang);

        $_page  = uri_segment(1);
        if($_page == 'home_detail' && get('das')) $_page = get('das');
        $_lang  = ['_main','_menu',$_page];

        foreach($_lang as $_l) {
            if(file_exists(FCPATH . 'assets/lang/'.$lang.'/'.$_l.'.json')) {
                $json_to_array   = json_decode(file_get_contents(FCPATH . 'assets/lang/'.$lang.'/'.$_l.'.json'),true);
                if(is_array($json_to_array)) {
                    foreach($json_to_array as $jk => $jv) {
                        $this->config->set_item('lang_'.$jk,$jv);
                    }
                }
            }
        }

        $post       = $this->input->post();
        if(count($post) > 0 && strpos(base_url(),'assets') === false) {
            if(!csrf_match()){
                if((isset($headers['X-Requested-With']) && $headers['X-Requested-With'] == 'XMLHttpRequest') || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    $res    = array(
                        'status'    => 'error',
                        'message'   => lang('token_keamanan_tidak_valid')
                    );
                    render($res,'json'); die;
                } else {
                    $this->session->set_flashdata('message',lang('token_keamanan_tidak_valid'));
                    $this->session->set_flashdata('status','error');
                    redirect($_SERVER['HTTP_REFERER']); die;
                }
            }
        }

    }
}

class BE_Controller extends MY_Controller {
        
    public function __construct(){
        parent::__construct();
        $this->config->set_item('setting_default_layout','admin_default');
        $this->config->set_item('setting_interface','admin');
        $module     = $this->uri->segment(1);
        $class      = $this->router->fetch_class();
        $method     = $this->router->fetch_method();
        if(!$this->session->userdata('id')){
            $check = get_cookie('id');
            if($check){
                $data = array(
                    'id'            => $check
                );
                $this->session->set_userdata($data);
            }
        }
        if($this->session->userdata('id') && setting('single_login')){
            $_user      = get_data('tbl_user','id',$this->session->userdata('id'))->row();
            if($module != 'auth' && isset($_user->id) && $_user->ip_address != $this->input->ip_address()) {
                $this->load->library('asset');
                $data       = get_data('tbl_user','id',$this->session->userdata('id'))->row_array();
                echo $this->load->view('errors/force_logout',$data,true);
                die;
            }
        }
        if(!get_cookie('x-token-app')) {
           $this->generate_token();
           redirect($this->uri->uri_string(), 'refresh');
        }
        if($module == 'auth' || $class == 'info'){
            if($class == 'login' || $class == 'forgot') {
                if($method == 'index' || $method == 'reset') {
                    if($this->session->userdata('id')){
                        redirect('home/welcome');
                    }
                }
            }
            $this->config->set_item('user_key_id',encode_id([0,0,rand()]));
        } else {
            if(!$this->session->userdata('id')){
                $this->session->set_userdata('last_url',current_url());
                redirect('auth/login');
            }
            $user   = get_data('tbl_user a',array(
                'select'        => 'a.*,b.nama AS grp',
                'join'          => [
                    'tbl_user_group b ON a.id_group = b.id TYPE LEFT'
                ],
                'where_array'   => array(
                    'a.id'      => $this->session->userdata('id')
                )
            ))->row_array();
            if(isset($user['id'])) {
                $foto       = base_url(dir_upload('user').'default.png');
                if($user['foto'] && file_exists(FCPATH . dir_upload('user') . $user['foto'])) {
                    $foto   = base_url(dir_upload('user') . $user['foto']);
                }
                if(get_cookie('osuid') != $user['notification_id']) {
                    update_data('tbl_user',array('notification_id'=>get_cookie('osuid')),'id',$user['id']);
                }
                foreach($user as $ku => $vu) {
                    $this->config->set_item('user_'.$ku,$vu);
                }
                $this->config->set_item('user_foto',$foto);
                $this->config->set_item('user_key_id',encode_id([$user['id'],$user['id_group'],rand()]));
            }
        }
        if(user('id') && setting('masa_aktif_password')){
            $masa_aktif_password    = setting('masa_aktif_password');
            $date                   = strtotime(date('Y-m-d H:i:s'));
            $exp                    = strtotime(date('Y-m-d H:i:s',strtotime('+'.$masa_aktif_password.' days',strtotime(user('change_password_at')))));
            if($date >= $exp && ($module != 'auth' && $module != 'account')) {
                redirect('account/changepwd');
            }
        }

        if(setting('log_aktif')) {
            $dt_log     = '';
            $metode     = 'GET';
            if(count(post())) {
                $dt_log = serialize(post());
                $metode = 'POST';
            } elseif(count(get())) {
                $dt_log = serialize(get());
            }
            $data_log   = [
                'ip_address'    => $this->input->ip_address(),
                'tanggal'       => date('Y-m-d H:i:s'),
                'id_user'       => user('id'),
                'nama_user'     => user('nama'),
                'keterangan'    => 'Mengakses ' . base_url($this->uri->uri_string()),
                'data'          => $dt_log,
                'metode'        => $metode,
                'respon'        => 200
            ];
            $save_log   = insert_data('tbl_user_log',$data_log);
            $this->config->set_item('setting_last_id_log',$save_log);
        }
    }

    private function session_exp() {
        $this->session->unset_userdata('id');
        delete_cookie('id');
        redirect('auth/login');
    }

    private function generate_token() {
        $length     = 72;
        $token      = encode_id([rand(),rand(),strtotime('now')]);
        $expire     = (int) time() + (10 * 365 * 24 * 60 * 60);
        $cookie     = array(
            'name'      => 'x-token-app',
            'value'     => $token,
            'expire'    => $expire
        );
        set_cookie( $cookie );
    }
        
}

class FE_Controller extends MY_Controller {
        
    public function __construct(){
        parent::__construct();
        $this->config->set_item('setting_default_layout','public_default');
        $this->config->set_item('setting_interface','public');
    }
}
