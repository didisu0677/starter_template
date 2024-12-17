<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function version() {
		if(file_exists(FCPATH . 'changelog.md')) {
			$content = file_get_contents(FCPATH . 'changelog.md');
			$this->load->library('parsedown');
			$text = $this->parsedown->text($content);
			echo '<div class="md-info">';
			echo $text;
			echo '</div>';
		} else {
			echo 'file changelog.md tidak diada';
		}
	}

}