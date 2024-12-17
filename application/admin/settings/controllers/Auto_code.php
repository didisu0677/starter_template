<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auto_code extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$unset_table		= array('tbl_kode','tbl_menu','tbl_notifikasi','tbl_setting','tbl_user','tbl_user_cabang','tbl_user_akses','tbl_user_group','tbl_master','tbl_master_setting','tbl_pengumuman');
		$data['list_table']	= db_list_table();
		foreach($data['list_table']	as $k => $v) {
			foreach($unset_table as $u) {
				if($u == $v) unset($data['list_table'][$k]);
			}
		}
		render($data);
	}

	function get_kolom() {
		$data['field']		= get_field(post('value'),'name');
		$result['kolom']	= $this->load->view('settings/auto_code/option',$data,true);
		render($result,'json');
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data 				= get_data('tbl_kode','id',post('id'))->row_array();
		$opt['field']		= get_field($data['tabel'],'name');
		$data['opt_kolom']	= $this->load->view('settings/auto_code/option',$opt,true);
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_kode',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_kode','id',post('id'));
		render($response,'json');
	}

	function help() {
		render(array(),'layout:false');
	}

	function check_table() {
		$kolom 	= get_data('tbl_kode',array('where_array'=>array(
			'tabel'		=> post('table'),
			'is_active'	=> 1
		)))->result_array();
		render($kolom,'json');
	}

}