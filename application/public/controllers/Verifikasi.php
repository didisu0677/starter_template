<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verifikasi extends FE_Controller {

	public function index() {
		$data['valid']		= false;
		$ids 				= decode_id(get('i'));
		if(isset($ids[0])) {
	        $data['vendor'] = get_data('tbl_vendor','id',$ids[0])->row_array();
	        if(isset($data['vendor']['id'])) $data['valid'] = true;
	    }
		render($data);
	}
}