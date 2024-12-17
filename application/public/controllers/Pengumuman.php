<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengumuman extends FE_Controller {

	public function index() {
        $this->load->helper('text');
        $data['pengadaan']  = get_data('tbl_pengadaan','tipe_pengadaan = "Lelang"')->result_array();
		$data['title']		= 'Pengumuman Pengadaan';
		render($data);
    }

    function pemenang() {
        $this->load->helper('text');
        $data['pengadaan']  = get_data('tbl_pemenang_pengadaan','tipe_pengadaan = "Lelang" AND approve = 1')->result_array();
        $data['title']      = 'Pengumuman Pemenang';
        render($data);
    }
    
    function detail($id='') {
        $id = decode_id($id);
        $id = isset($id[0]) ? $id[0] : 0;
        $data = get_data('tbl_pengadaan','id = '.$id)->row_array();
        if(isset($data['id'])) {
            $data['aanwijzing'] = get_data('tbl_jadwal_pengadaan',[
                'where'         => [
                    'kata_kunci'        => 'aanwijzing',
                    'nomor_pengajuan'   => $data['nomor_pengajuan']
                ]
            ])->row();
            $data['pendaftaran'] = get_data('tbl_jadwal_pengadaan',[
                'where'         => [
                    'kata_kunci'        => 'pendaftaran_pengadaan',
                    'nomor_pengajuan'   => $data['nomor_pengajuan']
                ]
            ])->row();
            $data['title']      = $data['nama_pengadaan'];
            render($data);
        } else render('404');
    }

    function download($encode_id='') {
        $id             = decode_id($encode_id);
        $id             = isset($id[0]) ? $id[0] : 0;
        $data           = get_data('tbl_pengadaan','status_pengadaan = "BIDDING" AND id = '.$id)->row_array();
        if(isset($data['id'])) {
            $aanwijzing                 = get_data('tbl_jadwal_pengadaan',[
                'where'                 => [
                    'kata_kunci'        => 'aanwijzing',
                    'nomor_pengajuan'   => $data['nomor_pengajuan']
                ]
            ])->row();
            $pendaftaran                = get_data('tbl_jadwal_pengadaan',[
                'where'                 => [
                    'kata_kunci'        => 'pendaftaran_pengadaan',
                    'nomor_pengajuan'   => $data['nomor_pengajuan']
                ]
            ])->row();
            $pengumuman                 = get_data('tbl_jadwal_pengadaan',[
                'where'                 => [
                    'kata_kunci'        => 'spph',
                    'nomor_pengajuan'   => $data['nomor_pengajuan']
                ]
            ])->row();
            $tanggal_pengumuman         = isset($pengumuman->id) ? $pengumuman->tanggal_awal : date('Y-m-d H:i:s');
            $rks                        = get_data('tbl_rks','nomor_pengajuan = "'.$data['nomor_pengajuan'].'" AND tipe_rks = "pengadaan"')->row();

            $data['tanggal_aanwijzing']     = isset($aanwijzing->id) ? date_indo($aanwijzing->tanggal_awal,false) : '-';
            $data['jam_aanwijzing']         = isset($aanwijzing->id) ? date('H:i',strtotime($aanwijzing->tanggal_awal)).' '.$aanwijzing->zona_waktu.' s/d Selesai' : '-';
            $data['lokasi_aanwijzing']      = isset($aanwijzing->id) ? str_replace("\n", '<br />', $aanwijzing->lokasi) : '-';
            $data['tanggal_pendaftaran']    = $data['jam_pendaftaran'] = $data['lokasi_pendaftaran'] = '-';
            if(isset($pendaftaran->id)) {
                if(date('Y-m-d',strtotime($pendaftaran->tanggal_awal)) == date('Y-m-d',strtotime($pendaftaran->tanggal_akhir))) {
                    $data['tanggal_pendaftaran']    = date_indo($pendaftaran->tanggal_awal,false);
                } else {
                    $data['tanggal_pendaftaran']    = date_indo($pendaftaran->tanggal_awal,false).' s/d '.date_indo($pendaftaran->tanggal_akhir,false);
                }
                $data['jam_pendaftaran']            = date('H:i',strtotime($pendaftaran->tanggal_awal)).' '.$pendaftaran->zona_waktu.' s/d '.date('H:i',strtotime($pendaftaran->tanggal_akhir)).' '.$pendaftaran->zona_waktu;
                $data['lokasi_pendaftaran']         = str_replace("\n", '<br />', $pendaftaran->lokasi);
            }
            $data['tanggal_pengumuman']     = date_indo($tanggal_pengumuman,false);
            $data['syarat_umum']            = isset($rks->id) ? $rks->syarat_umum : '-';
            $data['html']                   = template_pdf($data,'pengumuman_lelang',$tanggal_pengumuman);
            render($data,'pdf');
        } else render('404');
    }
}