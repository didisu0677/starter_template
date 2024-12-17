<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kebijakan extends FE_Controller {

	public function index() {
		$data['title']		= 'Kebijakan';
		render($data);
	}
}