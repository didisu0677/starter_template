<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends BE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        update_data('tbl_user',array(
            'is_login'          => 0,
            'token_app'         => '',
            'notification_id'   => ''
        ),'id',$this->session->userdata('id'));
        $this->session->unset_userdata('id');
        $this->session->unset_userdata('last_url');
        delete_cookie('id');
        delete_cookie('osuid');
        redirect('auth/login');
    }
}