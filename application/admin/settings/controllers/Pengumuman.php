<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengumuman extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		render();
	}

	function data() {
		$config['where']['id_user'] = user('id');
		$data 	= data_serverside($config);
		render($data,'json');
	}

	function get_data() {
		$data = get_data('tbl_pengumuman','id',post('id'))->row_array();
		render($data,'json');
	}

	function save() {
		$response = save_data('tbl_pengumuman',post(),post(':validation'));
		render($response,'json');
	}

	function delete() {
		$response = destroy_data('tbl_pengumuman','id',post('id'));
		render($response,'json');
	}

}