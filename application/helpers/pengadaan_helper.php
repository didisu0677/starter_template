<?php defined('BASEPATH') OR exit('No direct script access allowed');

function cek_approval($no_pengajuan) {
	$pengajuan 	= get_data('tbl_pengajuan','nomor_pengajuan',$no_pengajuan)->row_array();
	if(isset($pengajuan['id']) && $pengajuan['no_hps'] && $pengajuan['nomor_rks'] && $pengajuan['nomor_inisiasi'] && !$pengajuan['is_pos_approve'] && in_array($pengajuan['approve'],[0,8])) {
		$inisiasi 	= get_data('tbl_inisiasi_pengadaan','nomor_inisiasi',$pengajuan['nomor_inisiasi'])->row_array();
		if(isset($inisiasi['id'])) {
			if($inisiasi['tipe_pengadaan']) {
				$persetujuan_pengadaan	= [];
				$persetujuan_terdekat 	= get_data('tbl_m_penyetuju_pengadaan',[
					'where' 			=> [
						'is_active'				=> 1,
						'limit_persetujuan >='	=> $inisiasi['hps_panitia']
					],
					'sort_by'			=> 'limit_persetujuan','sort'=>'ASC',
					'limit'				=> 1
				])->row();
				$limit_persetujuan		= isset($persetujuan_terdekat->id) ? $persetujuan_terdekat->limit_persetujuan : $inisiasi['hps_panitia'];
				$persetujuan_pengadaan	= get_data('tbl_m_penyetuju_pengadaan',[
					'where' 		=> [
						'is_active'				=> 1,
						'limit_persetujuan <='	=> $limit_persetujuan
					],
					'sort_by'		=>'limit_persetujuan','sort'=>'ASC'
				])->result();
				delete_data('tbl_alur_persetujuan',[
					'nomor_pengajuan'	=> $pengajuan['nomor_pengajuan'],
					'jenis_approval'	=> 'PENGADAAN'
				]);
				$i = 1;
				foreach($persetujuan_pengadaan as $m) {
					$data_p = [
						'id_pengajuan'		=> $pengajuan['id'],
						'nomor_pengajuan'	=> $pengajuan['nomor_pengajuan'],
						'level_persetujuan'	=> $i,
						'nama_persetujuan'	=> $m->nama_persetujuan,
						'jenis_approval'	=> 'PENGADAAN',
						'id_user'			=> $m->id_user,
						'username'			=> $m->username,
						'nama_user'			=> $m->nama_lengkap
					];					
					insert_data('tbl_alur_persetujuan',$data_p);
					$i++;
				}

				$next_persetujuan  = get_data('tbl_alur_persetujuan',[
					'where'=> [
						'nomor_pengajuan'		=> $pengajuan['nomor_pengajuan'],
						'tanggal_persetujuan' 	=> '0000-00-00 00:00:00',
						'jenis_approval'		=> 'PENGADAAN'
					],
					'sort_by'=>'level_persetujuan','sort'=>'ASC'  
				])->row();
					
				if(isset($next_persetujuan->id)){
					update_data('tbl_pengajuan',[
						'id_user_persetujuan'	=> $next_persetujuan->id_user,
						'nama_persetujuan' 		=> $next_persetujuan->nama_persetujuan,
						'tanggal_pengajuan'		=> date('Y-m-d'),
						'is_pos_approve'		=> 1,
						'approve'				=> 0,
						'status_desc'			=> 'Persetujuan Pengadaan (Menunggu : '.$next_persetujuan->nama_user.')'
					],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);

					// kirim notifikasi ke approver
					$usr 				= get_data('tbl_user','id',$next_persetujuan->id_user)->row();
					if(isset($usr->id)) {
						$link				= base_url().'pengadaan/approval_pengadaan?i='.encode_id([$pengajuan['id'],rand()]);
						$desctiption 		= 'Pengadaan dengan nomor pengajuan. <strong>'.$pengajuan['nomor_pengajuan'].'</strong> membutuhkan persetujuan anda';
						$data_notifikasi 	= [
							'title'			=> 'Persetujuan Pengadaan',
							'description'	=> $desctiption,
							'notif_link'	=> $link,
							'notif_date'	=> date('Y-m-d H:i:s'),
							'notif_type'	=> 'info',
							'notif_icon'	=> 'fa-file-alt',
							'id_user'		=> $usr->id,
							'transaksi'		=> 'pengajuan',
							'id_transaksi'	=> $pengajuan['id']
						];
						insert_data('tbl_notifikasi',$data_notifikasi);

						if(setting('email_notification') && $usr->email) {
							send_mail([
								'subject'		=> 'Persetujuan Pengadaan #'.$pengajuan['nomor_pengajuan'],
								'to'			=> 'bayu.ramadhan.92.93@gmail.com',
								'nama_user'		=> $usr->nama,
								'description'	=> $desctiption,
								'url'			=> $link,
								'view'			=> 'pengadaan/pengajuan/mailer_save'
							]);
						}
					}
					update_data('tbl_m_hps',['status'=>1],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
					update_data('tbl_inisiasi_pengadaan',['status'=>1],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
					update_data('tbl_rks',['status'=>1],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
				}
			} else {
				$cek_notif	= get_data('tbl_notifikasi',[
					'where'		=> [
						'date(notif_date)'	=> date('Y-m-d'),
						'transaksi'			=> 'notif_inisiasi',
						'id_transaksi'		=> $pengajuan['id']
					]
				])->row();
				if(!isset($cek_notif->id)) {
					$get_panitia 	= get_data('tbl_panitia_pelaksana',[
						'where'		=> [
							'nomor_pengajuan'	=> $no_pengajuan,
							'id_m_panitia'		=> $inisiasi['id_panitia']
						]
					])->result_array();
					foreach($get_panitia as $p) {
						$data_notifikasi 	= [
							'title'			=> 'Pemberitahuan',
							'description'	=> 'Pengajuan <strong>'.$no_pengajuan.'</strong> belum bisa lanjutkan ke proses persetujuan dikarenakan Metode Pengadaan belum dilengkapi',
							'notif_link'	=> base_url('pengadaan/inisiasi_pengadaan?i='.encode_id([$inisiasi['id'],rand()])),
							'notif_date'	=> date('Y-m-d H:i:s'),
							'notif_type'	=> 'info',
							'notif_icon'	=> 'fa-info',
							'id_user'		=> $p['userid'],
							'transaksi'		=> 'notif_inisiasi',
							'id_transaksi'	=> $pengajuan['id']
						];
						insert_data('tbl_notifikasi',$data_notifikasi);	
					}
				}
			}
		} else {
			update_data('tbl_pengajuan',[
				'id_user_persetujuan'	=> '',
				'nama_persetujuan' 		=> '',
				'tanggal_pengajuan'		=> '0000-00-00',
				'is_pos_approve'		=> 0,
				'approve'				=> 0,
				'status_desc'			=> 'Inisiasi Pengadaan'
			],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
			update_data('tbl_m_hps',['status'=>0],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
			update_data('tbl_inisiasi_pengadaan',['status'=>0],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
			update_data('tbl_rks',['status'=>0],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);			
		}
	} else {
		update_data('tbl_pengajuan',[
			'id_user_persetujuan'	=> '',
			'nama_persetujuan' 		=> '',
			'tanggal_pengajuan'		=> '0000-00-00',
			'is_pos_approve'		=> 0,
			'approve'				=> 0,
			'status_desc'			=> 'Inisiasi Pengadaan'
		],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
		update_data('tbl_m_hps',['status'=>0],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
		update_data('tbl_inisiasi_pengadaan',['status'=>0],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
		update_data('tbl_rks',['status'=>0],'nomor_pengajuan',$pengajuan['nomor_pengajuan']);
	}
}

function id_by_nomor($nomor='',$tipe='pengajuan',$tipe_rks='pengadaan') {
	$result 	= 0;
	if($tipe == 'pengajuan') {
		$q		= get_data('tbl_pengajuan','nomor_pengajuan',$nomor)->row();
		if(isset($q->id)) $result = $q->id;
	} else if($tipe == 'hps') {
		$q		= get_data('tbl_m_hps','nomor_hps',$nomor)->row();
		if(isset($q->id)) $result = $q->id;
	} else if($tipe == 'rks') {
		$q		= get_data('tbl_rks',[
			'where' 	=> [
				'nomor_pengajuan'	=> $nomor,
				'tipe_rks'			=> $tipe_rks
			],
			'limit'		=> 1,
			'sort_by'	=> 'id',
			'sort'		=> 'DESC'
		])->row();
		if(isset($q->id)) $result = $q->id;
	}
	return $result;
}