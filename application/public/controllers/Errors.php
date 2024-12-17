<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends FE_Controller {

	public function page_not_found() {
		$data['title']	= "Halaman Tidak Ditemukan";
		render($data);
	}
}
