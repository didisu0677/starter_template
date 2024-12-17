<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		$vendor 						= [];
		if(user('id_vendor')) $vendor 	= get_data('tbl_vendor','id',user('id_vendor'))->row_array();
		if(isset($vendor['id'])) {
			include_lang('auth');
			$data 						= $vendor;
			$data['title']				= lang('profil');
			$data['kategori_rekanan']	= get_data('tbl_m_kategori_rekanan','is_active=1')->result_array();
			$data['bentuk_badan_usaha']	= get_data('tbl_m_bentuk_badan_usaha','is_active=1')->result_array();
			$data['status_perusahaan']	= get_data('tbl_m_status_perusahaan','is_active=1')->result_array();
			$data['kualifikasi']		= get_data('tbl_m_kualifikasi','is_active=1')->result_array();
			$data['asosiasi']			= get_data('tbl_m_asosiasi','is_active=1')->result_array();
			$data['unit']				= get_data('tbl_m_unit','is_active=1')->result_array();
			$data['negara']				= get_data('tbl_m_negara')->result_array();
			$data['provinsi']			= $data['id_negara'] == '101' ? get_data('tbl_m_wilayah','parent_id=0')->result_array() : [];
			$data['kota']				= get_data('tbl_m_wilayah','parent_id="'.$data['id_provinsi'].'"')->result_array();
			$data['kecamatan']			= get_data('tbl_m_wilayah','parent_id="'.$data['id_kota'].'"')->result_array();
			$data['kelurahan']			= get_data('tbl_m_wilayah','parent_id="'.$data['id_kecamatan'].'"')->result_array();

			render($data,'view:account/profile/vendor');
		} else {
			$data['title']				= lang('profil');
			render($data);			
		}
	}

	function save() {
		$response = save_data('tbl_user', post(), post(':validation'));
		render($response,'json');
	}
	
	function save_vendor() {
		$data					= post();
		$data['id']				= user('id_vendor');
		$bentuk_badan_usaha 	= get_data('tbl_m_bentuk_badan_usaha','id',post('id_bentuk_badan_usaha'))->row_array();
		$status_perusahaan 		= get_data('tbl_m_status_perusahaan','id',post('id_status_perusahaan'))->row_array();
		$kualifikasi 			= get_data('tbl_m_kualifikasi','id',post('id_kualifikasi'))->row_array();
		$asosiasi 				= get_data('tbl_m_asosiasi','id',post('id_asosiasi'))->row_array();
		$unit_daftar 			= get_data('tbl_m_unit','id',post('id_unit_daftar'))->row_array();
		$negara 				= get_data('tbl_m_negara','id',post('id_negara'))->row_array();

		$data['bentuk_badan_usaha']	= isset($bentuk_badan_usaha['bentuk_badan_usaha']) ? $bentuk_badan_usaha['bentuk_badan_usaha'] : '';
		$data['status_perusahaan']	= isset($status_perusahaan['status_perusahaan']) ? $status_perusahaan['status_perusahaan'] : '';
		$data['kualifikasi']		= isset($kualifikasi['kualifikasi']) ? $kualifikasi['kualifikasi'] : '';
		$data['asosiasi']			= isset($asosiasi['asosiasi']) ? $asosiasi['asosiasi'] : '';
		$data['unit_daftar']		= isset($unit_daftar['unit']) ? $unit_daftar['unit'] : '';
		$data['nama_negara']		= isset($negara['nama']) ? $negara['nama'] : '';

		if(is_array(post('id_kategori_rekanan')) && count(post('id_kategori_rekanan')) > 0) {
			$kategori_rekanan 			= get_data('tbl_m_kategori_rekanan','id',post('id_kategori_rekanan'))->result_array();
			$data['id_kategori_rekanan']= json_encode(post('id_kategori_rekanan'));
			$_kategori 					= [];
			foreach($kategori_rekanan as $k) {
				$_kategori[] 			= $k['kategori'];
			}
			$data['kategori_rekanan']	= implode(', ',$_kategori);
		}

		$response 	= save_data('tbl_vendor',$data,post(':validation'));
		if($response['status'] == 'success') {
			delete_data('tbl_vendor_kategori','id_vendor',$response['id']);
			$r = [];
			foreach(post('id_kategori_rekanan') as $k) {
				$r[] = [
					'id_vendor'				=> $response['id'],
					'id_kategori_rekanan'	=> $k
				];
			}
			insert_batch("tbl_vendor_kategori",$r);
			$vendor 	= get_data('tbl_vendor','id',user('id_vendor'))->row();
			$user 		= [
				'nama'			=> $vendor->nama,
				'email'			=> $vendor->email_cp,
				'telepon'		=> $vendor->hp_cp,
				'update_at'		=> date('Y-m-d H:i:s'),
				'update_by'		=> user('nama')
			];
			update_data('tbl_user',$user,'id_vendor',$vendor->id);

			$vendor = get_data('tbl_vendor','id',user('id_vendor'))->row();
			if($vendor->laporan_kunjungan == 9 && post('kunjungan_ulang')) {
				update_data('tbl_vendor',['laporan_kunjungan'=>0,'kunjungan'=>0,'nomor_kunjungan'=>''],'id',$vendor->id);
				$user_persetujuan = get_data('tbl_user',[
                    'where'     => [
                        'is_active' => 1,
                        'id_group'  => id_group_access('kunjungan_langsung','additional')
                    ]
                ])->result();
                $email_notifikasi = [];
                foreach($user_persetujuan as $u) {
                    $link               = base_url().'manajemen_rekanan/kunjungan_langsung?i='.encode_id([$vendor->id,rand()]);
                    $desctiption        = $vendor->nama.' mengajukan kunjungan ulang';
                    $data_notifikasi    = [
                        'title'         => 'Kunjungan Langsung',
                        'description'   => $desctiption,
                        'notif_link'    => $link,
                        'notif_date'    => date('Y-m-d H:i:s'),
                        'notif_type'    => 'info',
                        'notif_icon'    => 'fa-map-marker',
                        'id_user'       => $u->id,
                        'transaksi'     => 'kunjungan_langsung',
                        'id_transaksi'  => $vendor->id
                    ];
                    insert_data('tbl_notifikasi',$data_notifikasi);
                    $email_notifikasi[] = $u->email;
                }
                if(setting('email_notification') && count($email_notifikasi) ) {
					send_mail([
						'subject'		=> 'Kunjungan Langsung',
						'to'			=> $email_notifikasi,
						'description'	=> $desctiption,
						'url'			=> $link
					]);
				}
			} elseif($vendor->status_drm == 9 && post('drm_ulang')) {
				update_data('tbl_vendor',['status_drm'=>0],'id',$vendor->id);
				update_data('tbl_m_rekomendasi_vendor',['approval'=>0],'id_vendor',$vendor->id);
				$user_persetujuan = get_data('tbl_user',[
                    'where'     => [
                        'is_active' => 1,
                        'id_group'  => id_group_access('persetujuan_drm','additional')
                    ]
                ])->result();
                $email_notifikasi = [];
                foreach($user_persetujuan as $u) {
                    $link               = base_url().'manajemen_rekanan/persetujuan_drm?i='.encode_id([$vendor->id,rand()]);
                    $desctiption        = $vendor->nama.' mengajukan Persetujuan DRM ulang';
                    $data_notifikasi    = [
                        'title'         => 'Persetujuan DRM',
                        'description'   => $desctiption,
                        'notif_link'    => $link,
                        'notif_date'    => date('Y-m-d H:i:s'),
                        'notif_type'    => 'info',
                        'notif_icon'    => 'fa-check',
                        'id_user'       => $u->id,
                        'transaksi'     => 'persetujuan_drm',
                        'id_transaksi'  => $vendor->id
                    ];
                    insert_data('tbl_notifikasi',$data_notifikasi);
                    $email_notifikasi[] = $u->email;
                }
                if(setting('email_notification') && count($email_notifikasi) ) {
					send_mail([
						'subject'		=> 'Persetujuan DRM',
						'to'			=> $email_notifikasi,
						'description'	=> $desctiption,
						'url'			=> $link
					]);
				}
			}
		}
		render($response,'json');
	}
}