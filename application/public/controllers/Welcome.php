<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends FE_Controller {

	public function index() {
		$data['title']		= setting('title');
		render($data);
	}

	function get_token() {
		echo encode_id([strtotime('+1 hour',strtotime('now')),rand()]);
	}
}