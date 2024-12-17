<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_roles extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$data['menu'][0] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>0,'is_active'=>1),'sort_by'=>'urutan'))->result();
		foreach($data['menu'][0] as $m0) {
			$data['menu'][$m0->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m0->id,'is_active'=>1),'sort_by'=>'urutan'))->result();
			foreach($data['menu'][$m0->id] as $m1) {
				$data['menu'][$m1->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m1->id,'is_active'=>1),'sort_by'=>'urutan'))->result();
				foreach($data['menu'][$m1->id] as $m2) {
					$data['menu'][$m2->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m2->id,'is_active'=>1),'sort_by'=>'urutan'))->result();
				}
			}
		}
		render($data);
	}

	function data() {
		$data = data_serverside();
		render($data,'json');
	}

	function get_data() {
		$data 			= get_data('tbl_user_group','id',post('id'))->row_array();
		$data['access']	= get_data('tbl_user_akses','id_group',post('id'))->result_array();
		render($data,'json');
	}

	function save() {
		$id_menu	= post('id_menu');
		$view		= post('act_view');
		$input		= post('act_input');
		$edit		= post('act_edit');
		$delete		= post('act_delete');
		$additional	= post('act_additional');
		$response 	= save_data('tbl_user_group',post(),post(':validation'));
		if($response['status'] == 'success' && !is_array($response['id']) && count($id_menu) > 0) {
			foreach($id_menu as $m) {
				$data = array(
					'id_menu'		=> $m,
					'id_group'		=> $response['id'],
					'act_view'		=> isset($view[$m]) ? 1 : 0,
					'act_input'		=> isset($input[$m]) ? 1 : 0,
					'act_edit'		=> isset($edit[$m]) ? 1 : 0,
					'act_delete'	=> isset($delete[$m]) ? 1 : 0,
					'act_additional'=> isset($additional[$m]) ? 1 : 0,
				);
				$check = get_data('tbl_user_akses',array('where_array'=>array('id_menu'=>$m,'id_group'=>$data['id_group'])))->row();
				if(isset($check->id)) {
					update_data('tbl_user_akses',$data,array('id_menu'=>$m,'id_group'=>$data['id_group']));
				} else {
					insert_data('tbl_user_akses',$data);
				}
			}
		}
		render($response,'json');
	}

	function delete() {
		$child		= array(
			'id_group'	=> 'tbl_user_akses'
		);
		$response 	= destroy_data('tbl_user_group','id',post('id'),$child);
		render($response,'json');
	}
}