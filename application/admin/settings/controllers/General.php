<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['setting']	= get_data('tbl_master_setting')->result_array();
		$data['master']		= get_data('tbl_master','parent_id',0)->result_array();
		render($data);
	}

	function master($encode_id='') {
		$id 		= decode_id($encode_id)[0];
		$master 	= get_data('tbl_master','id',$id)->row();
		if(isset($master->id)) {
			$data['title']		= $master->konten;
			$data['parent_id']	= $id;
			$data['tipe']		= $master->tipe;
			render($data);
		} else {
			redirect('settings/general');
		}
	}

	function data($parent_id=0) {
		$config['where']['parent_id'] 	= $parent_id;
		if($parent_id == 0) {
			$config['button']			= button_serverside('btn-info',base_url('settings/general/master/'),array('fa-list','List',true));
		} else {
			$master 					= get_data('tbl_master','id',$parent_id)->row();
			if(isset($master->id) && $master->tipe == 'Integer') {
				$config['to_int']		= 'konten';
			}
		}
		$data 							= data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_master','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_master',post(),post(':validation'));
		render($response,'json');
	}

	function save_setting() {
		$data = post('setting');
		$sort = post('sort');
		foreach($data as $k => $v) {
			update_data('tbl_master_setting',array('id_master'=>$v,'tipe'=>$sort[$k]),'id',$k);
		}
		$response = array(
			'message'	=> lang('data_berhasil_disimpan'),
			'status'	=> 'success'
		);
		render($response,'json');
	}

	function delete() {
		$child	= array(
			'parent_id'	=> 'tbl_master'
		);
		$response = destroy_data('tbl_master','id',post('id'),$child);
		render($response,'json');
	}

}