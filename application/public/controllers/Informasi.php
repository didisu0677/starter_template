<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Informasi extends FE_Controller {

	public function index() {
        $this->load->helper('text');
        $data['informasi']  = get_data('tbl_informasi','is_active',1)->result_array();
		$data['title']		= 'Informasi';
		render($data);
    }
    
    function read($id='') {
        $id = decode_id($id);
        $id = isset($id[0]) ? $id[0] : 0;
        $info = get_data('tbl_informasi','is_active = 1 AND id = '.$id)->row();
        if(isset($info->id)) {
            $data['title']      = $info->judul;
            $data['informasi']  = $info;
            render($data);
        } else render('404');
    }
}