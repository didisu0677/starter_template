<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Json extends MY_Controller {

	function user() {
		$data['suggestions'] = get_data('tbl_user',array(
			'select'	=> 'kode AS data, nama AS value',
			'like'		=> array('nama'=>get('query')),
			'where'		=> array('is_active'=>1)
		))->result_array();
		render($data,'json');
	}

	function wilayah($id=0) {
		$wilayah 	= get_data('tbl_m_wilayah','parent_id',$id)->result_array();
		render($wilayah,'json');
	}

	function online_user() {
		$last_activity	= date('Y-m-d H:i:s',strtotime('-30 minutes',strtotime('now')));
		$arr = [
			'select' => 'id,nama,foto,is_login,last_activity',
			'where'	=> [
				'is_active'			=> 1,
				'id !='				=> $this->session->userdata('id')
			],
			'sort_by'	=> 'nama'
		];
		if(post('keyword')) {
			$arr['like'] = [
				'nama'				=> post('keyword')
			];
		} else {
			$arr['where']['is_login'] 			= 1;
			$arr['where']['last_activity >']	= $last_activity;
		}
		$data = get_data('tbl_user',$arr)->result_array();
		foreach($data as $k => $v) {
			$l_activity     = strtotime('now') - strtotime($v['last_activity']);
			if($l_activity > 1800) $data[$k]['is_login'] = 0;
		}
		render($data,'json');
	}

	function list_chat() {
		$this->load->library('emoji');

		$res	= db_query("SELECT `a`.`id` FROM `tbl_chat_key` `a` LEFT JOIN `tbl_chat_anggota` `b` ON `b`.`key_id` = `a`.`id` WHERE `b`.`id_user` = ".$this->session->userdata('id')." AND `a`.`is_active` = 1 AND ((aktif_mulai <= '".date('Y-m-d H:i:s')."' AND aktif_selesai >= '".date('Y-m-d H:i:s')."') OR (aktif_mulai = '0000-00-00 00:00:00' AND aktif_selesai = '0000-00-00 00:00:00'))")->result_array();
		$k_id = ['-1'];
        foreach($res as $row) {
			$k_id[] = $row['id'];
		}
		$key_id = implode(',',$k_id);

		$data 	= db_query('SELECT `a`.*, `b`.`nama`, `b`.`foto`, `c`.`nama` AS `nama_group`, `c`.`is_group`, `d`.`nama` AS `nama_pengirim`, `e`.`is_read` AS `is_read2` FROM `tbl_chat` `a` LEFT JOIN `tbl_user` `b` ON (`a`.`id_pengirim` = `b`.`id` AND `a`.`id_pengirim` != '.$this->session->userdata('id').') OR (`a`.`id_penerima` = `b`.`id` AND `a`.`id_penerima` != '.$this->session->userdata('id').') LEFT JOIN `tbl_chat_key` `c` ON `a`.`key_id` = `c`.`id` LEFT JOIN `tbl_user` `d` ON `a`.`id_pengirim` = `d`.`id` LEFT JOIN `tbl_chat_anggota` `e` ON `a`.`key_id` = `e`.`key_id` AND `e`.`id_user` = '.$this->session->userdata('id').' WHERE `a`.`id` IN (SELECT MAX(`id`) FROM `tbl_chat` WHERE `key_id` IN ('.$key_id.') GROUP BY `key_id`) ORDER BY `tanggal` DESC')->result_array();
		foreach($data as $k => $v) {
			$data[$k]['pesan'] = $this->emoji->decode($v['pesan']);
		}
		render($data,'json');
	}

	function list_group() {
		$users 	= get_data('tbl_chat_anggota','id_user',$this->session->userdata('id'))->result();
		$key_id = [0];
		foreach($users as $u) {
			$key_id[] = $u->key_id;
		}
		$data 	= get_data('tbl_chat_key',[
			'where'	=> "id IN (".implode(',',$key_id).") AND is_active = 1 AND is_group = 1 AND ((aktif_mulai <= '".date('Y-m-d H:i:s')."' AND aktif_selesai >= '".date('Y-m-d H:i:s')."') OR (aktif_mulai = '0000-00-00 00:00:00' AND aktif_selesai = '0000-00-00 00:00:00'))"
		])->result_array();
		render($data,'json');
	}

	function get_chat() {
		$this->load->library('emoji');
		$p = post();
		$response = [];
		if(!$p['chat_key']) {
			$get_chat_key = get_data('tbl_chat',[
				'select'	=> 'key_id',
				'where'		=> '(id_penerima = '.$p['id_user1'].' AND id_pengirim = '.$p['id_user2'].') OR (id_penerima = '.$p['id_user2'].' AND id_pengirim = '.$p['id_user1'].')'
			])->row();
			if(isset($get_chat_key->key_id)) {
				$response['chat_key'] = $get_chat_key->key_id;
			} else {
				$key = [
					$p['id_user1'].'___'.$p['id_user2'],
					$p['id_user2'].'___'.$p['id_user1']
				];
				$get_chat_key = get_data('tbl_chat_key',[
					'select'	=> 'id',
					'where'		=> [
						'_key'	=> $key
					]
				])->row();
				if(isset($get_chat_key->id)) {
					$response['chat_key'] = $get_chat_key->id;
				} else {
					$user = get_data('tbl_user',[
						'where'	=> [
							'id'		=> [$p['id_user1'],$p['id_user2']],
							'is_active'	=> 1
						]
					])->result();
                    $nama       = [];
                    foreach($user as $u) {
                        $nama[] = $u->nama;
                    }
                    $anggota    = implode(', ' , $nama);
					$a          = implode(' dan ', $nama);
					$save_key 	= insert_data('tbl_chat_key',[
						'nama'		=> 'Obrolan Privat '.$a,
						'anggota'	=> $anggota,
						'_key'		=> $key[0],
						'is_active'	=> 1
					]);
					$response['chat_key'] = $save_key;
					foreach([$p['id_user1'],$p['id_user2']] as $us) {
						insert_data('tbl_chat_anggota',[
							'key_id'	=> $save_key,
							'id_user'	=> $us
						]);
					}
				}
			}
		} else {
			$response['chat_key']	= $p['chat_key'];
		}
		$response['last_id'] = -1;
		if(isset($response['chat_key'])) {
			$arr 	= [
				'select'	=> 'a.*, b.nama AS nama_pengirim',
				'join'		=> 'tbl_user b ON a.id_pengirim = b.id TYPE LEFT',
				'where'		=> [
					'a.key_id'	=> $response['chat_key']
				],
				'sort_by'		=> 'a.id',
				'sort'			=> 'desc',
				'limit'			=> 20
			];
			if($p['last_id']) $arr['where']['a.id <'] = $p['last_id'];
			$response['data'] = get_data('tbl_chat a',$arr)->result_array();
			foreach($response['data'] as $k => $v) {
				$response['data'][$k]['pesan'] = $this->emoji->decode($v['pesan']);
			}
			if(count($response['data']) > 0) {
				$response['last_id'] = $response['data'][count($response['data'])-1]['id'];
			}
		}
		if($p['last_id'] == 0) {
			update_data('tbl_chat',['is_read'=>1],[
				'id_penerima' 	=> $p['id_user1'],
				'key_id'		=> $response['chat_key']
			]);
			update_data('tbl_chat_anggota',['is_read'=>1],[
				'id_user' 		=> $p['id_user1'],
				'key_id'		=> $response['chat_key']
			]);
		}
		render($response,'json');
	}

	function send_chat() {
		$this->load->library('emoji');
		$p = post();

		$data = [
			'id_pengirim'	=> $p['id_pengirim'],
			'id_penerima'	=> $p['id_penerima'],
			'key_id'		=> $p['chat_key'],
			'tanggal'		=> date('Y-m-d H:i:s'),
			'pesan'			=> $this->emoji->encode($p['pesan'])
		];
		insert_data('tbl_chat',$data);
		update_data('tbl_chat',['is_read'=>1],[
			'id_penerima' 	=> $p['id_pengirim'],
			'key_id'		=> $p['chat_key']
		]);
		update_data('tbl_chat_anggota',['is_read'=>0],[
			'key_id'		=> $p['chat_key']
		]);
		update_data('tbl_chat_anggota',['is_read'=>1],[
			'id_user' 		=> $p['id_pengirim'],
			'key_id'		=> $p['chat_key']
		]);
	}

}