<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function sortable() {
		render();
	}

	function data($tipe = 'table') {
		$menu = menu();
		if($menu['access_view']) {
			$data['menu'][0] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>0),'sort_by'=>'urutan'))->result();
			foreach($data['menu'][0] as $m0) {
				$data['menu'][$m0->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m0->id),'sort_by'=>'urutan'))->result();
				foreach($data['menu'][$m0->id] as $m1) {
					$data['menu'][$m1->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m1->id),'sort_by'=>'urutan'))->result();
					foreach($data['menu'][$m1->id] as $m2) {
						$data['menu'][$m2->id] = get_data('tbl_menu',array('where_array'=>array('parent_id'=>$m2->id),'sort_by'=>'urutan'))->result();
					}
				}
			}
			if($tipe == 'sortable') {
				$response	= array(
					'content' => $this->load->view('settings/menu/sortable',$data,true)
				);
			} else {
				$data['access_edit']	= $menu['access_edit'];
				$data['access_delete']	= $menu['access_delete'];
				$response	= array(
					'table'		=> $this->load->view('settings/menu/table',$data,true),
					'option'	=> $this->load->view('settings/menu/option',$data,true)
				);
			}
		} else {
			$response	= array(
				'status'	=> 'error',
				'message'	=> 'Permission Denied'
			);
		}
		render($response,'json');
	}

	function get_data() {
		$data = get_data('tbl_menu','id',post('id'))->row_array();
		if(isset($data['id']) && $data['shortcut']) {
			$data['shortcut_key'] = substr($data['shortcut'], 0, strlen($data['shortcut']) - 1);
			$data['shortcut'] = substr($data['shortcut'],-1,1);
		}
		render($data,'json');
	}

	function save() {
		$data 		= post();
		$validation	= post(':validation');
		if(isset($data['shortcut']) && $data['shortcut']) {
			$data['shortcut'] = $data['shortcut_key'] . strtoupper($data['shortcut']);
		}
		$response = save_data('tbl_menu',$data,$validation);
		if($response['status'] == 'success') {
			$data_user_akses = [];
			if(!$data['akses_input']) 		$data_user_akses['act_input'] 		= 0;
			if(!$data['akses_edit']) 		$data_user_akses['act_edit'] 		= 0;
			if(!$data['akses_delete']) 		$data_user_akses['act_delete'] 		= 0;
			if(!$data['akses_additional']) 	$data_user_akses['act_additional'] 	= 0;
			if(count($data_user_akses)) update_data('tbl_user_akses',$data_user_akses,'id_menu',$response['id']);

			$mn = get_data('tbl_menu','id',$response['id'])->row_array();
			if($mn['parent_id'] == 0) {
				update_data('tbl_menu',array('level1'=>$mn['id']),'id',$mn['id']);
			} else {
				$parent = get_data('tbl_menu','id',$mn['parent_id'])->row_array();
				$data_update = array(
					'level1' => $parent['level1'],
					'level2' => $parent['level2'],
					'level3' => $parent['level3'],
					'level4' => $parent['level4']
				);
				if(!$parent['level2']) $data_update['level2'] = $mn['id'];
				else if(!$parent['level3']) $data_update['level3'] = $mn['id'];
				else if(!$parent['level4']) $data_update['level4'] = $mn['id'];
				update_data('tbl_menu',$data_update,'id',$mn['id']);
			}
		}
		render($response,'json');
	}

	function delete() {
		$child	= array(
			'level1'	=> 'tbl_menu',
			'level2'	=> 'tbl_menu',
			'level3'	=> 'tbl_menu',
			'id_menu'	=> 'tbl_user_akses'
		);
		$response = destroy_data('tbl_menu','id',post('id'),$child);
		render($response,'json');
	}

	function save_sortable() {
		$data = post('menuItem');
		update_data('tbl_menu',['urutan'=>0]);
		foreach($data as $id => $parent_id) {
			if(!$parent_id || $parent_id == null || $parent_id == 'null') $parent_id = 0;
			$get_urutan	= get_data('tbl_menu',[
				'select'	=> 'MAX(urutan) urutan',
				'where'		=> [
					'parent_id'	=> $parent_id
				]
			])->row();
			$urutan 	= $get_urutan->urutan ? $get_urutan->urutan + 1 : 1;
			$save 		= update_data('tbl_menu',['parent_id'=>$parent_id,'urutan'=>$urutan],'id',$id);
			if($save) {
				$mn = get_data('tbl_menu','id',$id)->row_array();
				if($mn['parent_id'] == 0) {
					update_data('tbl_menu',array('level1'=>$mn['id']),'id',$mn['id']);
				} else {
					$parent = get_data('tbl_menu','id',$mn['parent_id'])->row_array();
					$data_update = array(
						'level1' => $parent['level1'],
						'level2' => $parent['level2'],
						'level3' => $parent['level3'],
						'level4' => $parent['level4']
					);
					if(!$parent['level2']) $data_update['level2'] = $mn['id'];
					else if(!$parent['level3']) $data_update['level3'] = $mn['id'];
					else if(!$parent['level4']) $data_update['level4'] = $mn['id'];
					update_data('tbl_menu',$data_update,'id',$mn['id']);
					
				}
			}
		}
		render([
			'status'	=> 'success',
			'message'	=> lang('data_berhasil_diperbaharui')
		],'json');
	}

}