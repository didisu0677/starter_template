<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Obrolan extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$access = get_access('obrolan');
		$config = [];
		$config['access_view'] = false;
		if($access['access_additional']) {
			$config['button'][] = button_serverside('btn-info','btn-export',['fa-download',lang('ekspor'),true],'act-export');
		}
		if($access['access_edit']) {
			$config['access_edit'] = false;
			$config['button'][] = button_serverside('btn-warning','btn-input',['fa-edit',lang('ubah'),true],'act-edit',[
				'is_group'	=> 1
			]);
		}
		$data 	= data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_chat_key','id',post('id'))->row_array();
		$data['detail']	= get_data('tbl_chat_anggota a',[
			'select'	=> 'a.*,b.nama',
			'join'		=> 'tbl_user b ON a.id_user = b.id TYPE LEFT',
			'where'		=> 'key_id = '.post('id'),
			'sort_by'	=> 'id'
		])->result_array();
		render($data,'json');
	}

	function save() {
		$data 		= post();
		$id_anggota = post('id_anggota');
		$user 		= get_data('tbl_user','id',$id_anggota)->result();
		$anggota 	= [];
		foreach($user as $u) $anggota[] = $u->nama;
		$data['anggota']	= implode(', ',$anggota);
		$data['is_group']	= 1;
		$response 	= save_data('tbl_chat_key',$data,post(':validation'));
		if($response['status'] == 'success') {
			delete_data('tbl_chat_anggota','key_id',$response['id']);
			foreach($id_anggota as $i) {
				insert_data('tbl_chat_anggota',[
					'id_user'	=> $i,
					'key_id'	=> $response['id']
				]);
			}
		}
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_chat_key','id',post('id'));
		if($response['status'] == 'success') {
			delete_data('tbl_chat','key_id',post('id'));
			delete_data('tbl_chat_anggota','key_id',post('id'));
		}
		render($response,'json');
	}

	function get_user() {
		$query 	= get('query');
		$user 	= get_data('tbl_user','is_active=1 AND nama LIKE "%'.$query.'%"')->result();
		$data['suggestions'] = [];
		foreach($user as $u) {
			$data['suggestions'][] = [
				'value'	=> $u->nama,
				'data'	=> $u->id
			];
		}
		render($data,'json');
	}

	function export() {
		if(post('id_export')) {
			$where = [
				'a.key_id'	=> post('id_export')
			];
			if(post('periode')) {
				$periode = post('::periode');
				$where['DATE(a.tanggal) >='] = $periode[0];
				$where['DATE(a.tanggal) <='] = $periode[1];
			}
			$data['info']		= get_data('tbl_chat_key','id',post('id_export'))->row_array();
			$data['periode']	= isset($periode) && count($periode) == 2 ? c_date($periode[0]).' s/d '.c_date($periode[1]) : 'Sepanjang Waktu';
			$data['data']		= get_data('tbl_chat a',[
				'select'	=> 'a.*,b.nama',
				'join'		=> 'tbl_user b ON a.id_pengirim = b.id TYPE LEFT',
				'where' 	=> $where,
				'sort_by'	=> 'tanggal'
			])->result_array();
			render($data,'pdf');
		} else {
			render('404');
		}
	}

}