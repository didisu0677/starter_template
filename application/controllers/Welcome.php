<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcomes extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function index() {
		// redirect('public');
	}

	function tes() {
		delete_data('tbl_aset');
		$data = get_data('tbl_aset_old')->result();
		foreach($data as $d) {
			$row = [
				'kode_barang'	=> $d->kode_barang,
				'no_aset'		=> $d->no_aset,
				'barcode'		=> $d->barcode,
				'nama_aset'		=> $d->nama_aset,
				'merk'			=> $d->merk,
				'no_serial'		=> $d->no_serial,
				'no_produk'		=> $d->no_produk
			];
			insert_data('tbl_aset',$row);
		}
	}
}
