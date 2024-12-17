<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends BE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        if(file_exists(FCPATH.'assets/json/'.get('t').'.json')) {
            $data['attr']  = json_decode(file_get_contents(FCPATH.'assets/json/'.get('t').'.json'),true);
        }
        $data['detail'] = get_data(get('t'),'id',get('i'))->row_array();
        render($data,'layout:false');
    }
}