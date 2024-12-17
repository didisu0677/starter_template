<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Option extends BE_Controller {

	function wilayah() {
		$child 			= post('target');
		$data[$child] 	= post('value') ? select_option(get_data('tbl_m_wilayah','parent_id',post('value'))->result_array(),'id','nama') : option('','','','return');
		render($data,'json');
	}

	function floor() {
		$child 			= post('target');
		$floor 			= get_data('tbl_m_lantai',array('where'=>array('id_lokasi'=>post('value'),'is_active'=>1)))->result_array();
		$data[$child] 	= post('value') ? select_option($floor,'id','nama') : option('','','','return');
		render($data,'json');
	}
	
}