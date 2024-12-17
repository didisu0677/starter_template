<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

	public function index() {
		redirect('account/profile');
	}

	function change_language() {
		$cookie	= array(
			'name'          => 'lang',
			'value'         => post('lang'),
			'expire'        => '86500'
		);
		set_cookie( $cookie );
	}
}
