<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errors extends MY_Controller {

	public function page_not_found($tipe='backend') {
		$id_user 	= 0;
		$nama_user	= '';
		if($this->session->userdata('id')){
			$usr 	= get_data('tbl_user','id',$this->session->userdata('id'))->row();
			if(isset($usr->id)) {
				$id_user 	= $usr->id;
				$nama_user	= $usr->nama;
			}
        }
		$data_log   = [
            'ip_address'    => $this->input->ip_address(),
            'tanggal'       => date('Y-m-d H:i:s'),
            'id_user'       => $id_user,
            'nama_user'     => $nama_user,
            'keterangan'    => 'Mengakses ' . base_url($this->uri->uri_string()),
            'data'          => '',
            'metode'        => 'GET',
            'respon'        => 404
        ];
        $save_log   = insert_data('tbl_user_log',$data_log);
		$this->load->library('asset');
		$this->load->view('errors/page_not_found');
	}
}
