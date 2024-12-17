<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_lists extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['group']            = get_data('tbl_user_group','is_active = 1 AND id != 7')->result_array();
		render($data);
	}

	function data() {
		$config['access_view'] = false;
		if(setting('jumlah_salah_password')) {
			$config['button']	= button_serverside('btn-success','btn-unlock',['fa-unlock',lang('buka_kunci'),true],'act-unlock',['invalid_password >=' => setting('jumlah_salah_password')]);
		}
		$data 			= data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data 					= get_data('tbl_user','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$data 		= post();

		$response 	= save_data('tbl_user',$data,post(':validation'));
		if($response['status'] == 'success' && post('password')) {
			update_data('tbl_user',[
				'change_password_by'    => user('nama'),
				'change_password_at'    => date('Y-m-d H:i:s')
			],'id',$response['id']);
			$check  = get_data('tbl_history_password',[
				'where' => [
					'id_user'   => $response['id'],
					'password'  => md5(post('password'))
				]
			])->row();
			if(isset($check->id)) {
				update_data('tbl_history_password',['tanggal'=>date('Y-m-d H:i:s')],'id',$check->id);
			} else {
				insert_data('tbl_history_password',[
					'id_user'   => $response['id'],
					'password'  => md5(post('password')),
					'tanggal'   => date('Y-m-d H:i:s')
				]);
			}
		}
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_user','id',post('id'),'','foto');
		render($response,'json');
	}

	function template() {
		$arr            = array('kode'=>'Kode (Jika tidak diisi tidak akan disave)','nama'=>'Nama','email'=>'Email','telepon'=>'Telepon','jabatan'=>'Jabatan','username'=>'Username (Jika tidak diisi akan otomatis No Anggota menjadi username)','password'=>'Password','role'=>'Role');
		$config[]		= array(
			'title'		=> 'template_import_user',
			'header'	=> $arr
		);
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function import() {
		ini_set('memory_limit', '-1');
		$this->load->library('PHPExcel');
		$file   = post('fileimport');
		try {
			$objPHPExcel = PHPExcel_IOFactory::load($file);
		} catch(Exception $e) {
			die('Error loading file :' . $e->getMessage());
		}
		$worksheet  = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$numRows    = count($worksheet);
		$tambah     = 0;
		$edit       = 0;
		for ($i=1; $i < ($numRows+1) ; $i++) {
			if(strtoupper($worksheet[$i]["A"]) !== "KODE" && strtoupper($worksheet[$i]["B"]) !== "NAMA") {
				$data = array(
					"kode"          => strtoupper(trim($worksheet[$i]["A"]," ")),
					"nama"          => trim($worksheet[$i]["B"]," "),
					"email"         => trim($worksheet[$i]["C"]," "),
					"telepon"     	=> trim($worksheet[$i]["D"]," "),
					"jabatan"       => trim($worksheet[$i]["E"]," "),
					"username"      => trim($worksheet[$i]["F"]," "),
					"password"      => trim($worksheet[$i]["G"]," "),
					"id_group"      => trim($worksheet[$i]["H"]," "),
					"is_active"  	=> 1
				);
				if($data['kode']) {
					$data['username']   = $data['username'] ? $data['username'] : $data['kode'];
					$group              = get_data('tbl_user_group',array('where_array'=>array('nama'=>$data['id_group'])))->row();
					$data['id_group']   = isset($group->id) ? $group->id : 0;
					$data['password']   = $data['password'] ? 
					password_hash(md5($data['password']),PASSWORD_DEFAULT,array('cost'=>COST)) : 
					password_hash(md5($data['kode']),PASSWORD_DEFAULT,array('cost'=>COST));
					$check  = get_data('tbl_user',array('where_array'=>array('kode'=>$data['kode'])))->row();
					if(isset($check->id)) {
						unset($data['username']);
						$data['update_by']	= user('nama');
						$data['update_at']	= date('Y-m-d H:i:s');
						$save   = update_data('tbl_user',$data,'id',$check->id);
						if($save) {
							$edit++;
							$save = $check->id;
						}
					} else {
						$username   = get_data('tbl_user',array('where_array'=>array('username'=>$data['username'])))->row();
						if(isset($username->id)) $data['username'] = $data['kode'];
						$data['create_by'] 	= user('nama');
						$data['create_at']	= date('Y-m-d H:i:s');
						$save   = insert_data('tbl_user',$data);
						if($save) {
							$tambah++;
						}
					}
				}
			}
		}
		delete_dir(FCPATH . "assets/uploads/temp/".md5(user('id').'fileimport'));
		$response = array(
			'status' 	=> 'success', 
			'message' 	=> $tambah.' '.lang('data_berhasil_disimpan').', '.$edit.' '.lang('data_berhasil_diperbaharui')
		);
		render($response,'json');
	}

	function export() {
		$header = array('kode'=>'Kode','nama'=>'Nama','email'=>'Email','telepon'=>'Telepon','jabatan'=>'Jabatan','username'=>'Username','grp'=>'Role');
		$user   = get_data('tbl_user a',array(
			'select'    => 'a.*,b.nama AS grp',
			'join'      => array(
				'tbl_user_group b on a.id_group = b.id type left',
			)
		))->result_array();
		$config			= array(
			'title'		=> 'data_user',
			'header'	=> $header,
			'data'		=> $user
		);
		$this->load->library('simpleexcel',$config);
		$this->simpleexcel->export();
	}

	function unlock() {
		$data = [
			'id'				=> post('id'),
			'invalid_password'	=> 0
		];
		$res = save_data('tbl_user',$data,[],true);
		render($res,'json');
	}
}