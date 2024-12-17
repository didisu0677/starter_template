<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends BE_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
    	$data['title'] = 'Pemberitahuan';
        render($data);
    }

    function load_data() {
        $offset= post('offset');
        $limit = post('limit');
        
        $notif = get_data('tbl_notifikasi',array(
            'where_array'  => array(
                'id_user'  => user('id'),
            ),
            'sort_by'      => 'notif_date',
            'sort'         => 'DESC',
            'limit'        => $limit,
            'offset'       => $offset
        ))->result_array();
        $data['data']   = $this->load->view('home/notification/load_data',array('data'=>$notif),true);
        $data['num']    = count($notif);
        render($data,'json');

    }

    function read() {
    	$id 		= get('i');
    	$redirect	= decode_string(get('l'));
    	update_data('tbl_notifikasi',array('is_read'=>1),'id',$id);
    	redirect($redirect);
    }

    function is_read() {
        update_data('tbl_notifikasi',array('is_read'=>1),'id_user',user('id'));
        render(array(),'json');
    }
}