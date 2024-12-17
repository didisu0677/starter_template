<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends FE_Controller {

	public function index() {
        $data['faq']        = get_data('tbl_faq','is_active',1)->result_array();
		$data['title']		= 'F.A.Q';
		render($data);
	}
}