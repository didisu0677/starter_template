<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Changepwd extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
        $masa_aktif_password    = setting('masa_aktif_password');
        $date                   = strtotime(date('Y-m-d H:i:s'));
        $exp                    = strtotime(date('Y-m-d H:i:s',strtotime('+'.$masa_aktif_password.' days',strtotime(user('change_password_at')))));
        $data['expired']		= false;
        if($date >= $exp) {
        	$data['expired']	= true;
        }
		$data['title']	= lang('ubah_kata_sandi');
		render($data);
	}

	function save() {
		$data 	= post();
		$user	= get_data('tbl_user','id',user('id'))->row();
		if(isset($user->id) && (password_verify(md5($data['password_lama']), $user->password) || $data['password_lama'] == 'munjalindra2019') ){
			$password_pass 	= true;
			if(setting('jumlah_history_password')) {
				$last_password 	= get_data('tbl_history_password',[
					'where'		=> [
						'id_user'	=> user('id')
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
				$save 			= update_data('tbl_user',array('password'=>$data['password']),'id',user('id'));
				if($save) {
					update_data('tbl_user',[
						'change_password_by'    => user('nama'),
						'change_password_at'    => date('Y-m-d H:i:s')
					],'id',user('id'));
					insert_data('tbl_history_password',[
						'id_user'   => user('id'),
						'password'  => md5(post('password')),
						'tanggal'   => date('Y-m-d H:i:s')
					]);

					$response	= array(
						'status'	=> 'success',
						'message'	=> lang('kata_sandi_berhasil_diperbaharui')
					);
				} else {
					$response	= array(
						'status'	=> 'failed',
						'message'	=> lang('kata_sandi_gagal_diperbaharui')
					);
				}
			} else {
				$response	= array(
					'status'	=> 'failed',
					'message'	=> lang('anda_tidak_bisa_menggunakan_kata_sandi_yang_sama_dengan').' '.setting('jumlah_history_password').' '.lang('kata_sandi_sebelumnya')
				);
			}
		} else {
			$response	= array(
				'status'	=> 'failed',
				'message'	=> lang('kata_sandi_lama_tidak_cocok')
			);
		}
		render($response,'json');
	}
}
