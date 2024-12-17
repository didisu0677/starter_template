<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgot extends BE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['title']  = lang('lupa_kata_sandi');
        $data['layout'] = 'auth';
        render($data);
    }

	public function do_forgot() {
		$email			= post('email');
		$check 			= get_data('tbl_user','email',$email)->row();
		$response 		= array();
		if(isset($check->id)) {
	        $this->load->library('hashid');
	        $next_hour			= strtotime('+1 hour',strtotime(date('Y-m-d H:i:00')));
	        $encode_attr 		= array($check->id,$next_hour);
	        $data['user']		= $check;
	        $data['exp']		= date('d/m/Y H:i:00',$next_hour);
	        $data['encode']		= $this->hashid->encode($encode_attr);
	        $data['to']			= $check->email;
	        $data['subject']	= 'Reset Kata Sandi';
	        $response			= send_mail($data);
	    } else {
	    	$response 	= array(
	    		'status'	=> 'failed',
	    		'message'	=> lang('msg_unregistered_email')
	    	);
	    }
	    render($response,'json');
	}

	public function reset($hash='') {
        $this->load->library('hashid');
        $decode	= $this->hashid->decode($hash);
        if(count($decode) == 2) {
	        $data['title']  = lang('reset_kata_sandi');
	        $data['layout'] = 'auth';
        	$data['id']		= $decode[0];
        	$data['user']	= get_data('tbl_user','id',$data['id'])->row();
        	if(isset($data['user']->id)) {
	        	$data['exp']	= $decode[1] < strtotime(date('Y-m-d H:i:s')) ? true : false;
	        	render($data);
	        } else {
	        	render([],'view:errors/page_not_found layout:false');
	        }
        } else {
        	render([],'view:errors/page_not_found layout:false');
        }
	}

	public function do_reset() {
		$user 		= get_data('tbl_user','id',post('id'))->row();
		$password_pass 	= true;
		if(isset($user->id) && setting('jumlah_history_password')) {
			$last_password 	= get_data('tbl_history_password',[
				'where'		=> [
					'id_user'	=> $user->id
				],
				'sort_by'	=> 'tanggal',
				'sort'		=> 'DESC',
				'limit'		=> setting('jumlah_history_password')
			])->result();
			foreach($last_password as $l) {
				if($l->password == md5(post('password'))) $password_pass = false;
			}
		}
		if($password_pass) {
			$response 	= save_data('tbl_user',post(),post(':validation'));
			if($response['status'] == 'success') {
				update_data('tbl_user',[
					'change_password_by'    => $user->nama,
					'change_password_at'    => date('Y-m-d H:i:s')
				],'id',$user->id);
				insert_data('tbl_history_password',[
					'id_user'   => $user->id,
					'password'  => md5(post('password')),
					'tanggal'   => date('Y-m-d H:i:s')
				]);
			}
			render($response,'json');
		} else {
			render([
				'status'	=> 'failed',
				'message'	=> lang('anda_tidak_bisa_menggunakan_kata_sandi_yang_sama_dengan').' '.setting('jumlah_history_password').' '.lang('kata_sandi_sebelumnya')
			],'json');
		}
	}
}
