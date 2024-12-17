<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends BE_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('securimage');
    }

    public function index() {
        $d = decode_id(get('token'));
        if(isset($d[0]) && $d[0] > strtotime('now')) {
            $data['layout']             = 'register';
            $data['title']              = lang('pendaftaran_rekanan');
            $data['kategori_rekanan']   = get_data('tbl_m_kategori_rekanan','is_active=1')->result_array();
            $data['bentuk_badan_usaha'] = get_data('tbl_m_bentuk_badan_usaha','is_active=1')->result_array();
            $data['status_perusahaan']  = get_data('tbl_m_status_perusahaan','is_active=1')->result_array();
            $data['kualifikasi']        = get_data('tbl_m_kualifikasi','is_active=1')->result_array();
            $data['asosiasi']           = get_data('tbl_m_asosiasi','is_active=1')->result_array();
            $data['unit']               = get_data('tbl_m_unit','is_active=1')->result_array();
            $data['negara']             = get_data('tbl_m_negara')->result_array();
            $data['provinsi']           = get_data('tbl_m_wilayah','parent_id=0')->result_array();
            $data['captcha']            = Securimage::getCaptchaHtml(array('input_name' => 'ct_captcha', 'placeholder' => lang('tuliskan_teks_diatas')));;
            render($data);
        } else {
            redirect();
        }
    }

    function generate_password() {
        $data = [
            substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz'), 0, 6),
            substr(str_shuffle('1234567890'), 0, 1),
            substr(str_shuffle('!@#$%&?=+-_'), 0, 1)
        ];
        shuffle($data);
        return $data[0].$data[1].$data[2];
    }

    function do_reg() {
        $img                    = new Securimage();
        $response               = [];
        $data                   = post();
        $bentuk_badan_usaha     = get_data('tbl_m_bentuk_badan_usaha','id',post('id_bentuk_badan_usaha'))->row_array();
        $status_perusahaan      = get_data('tbl_m_status_perusahaan','id',post('id_status_perusahaan'))->row_array();
        $kualifikasi            = get_data('tbl_m_kualifikasi','id',post('id_kualifikasi'))->row_array();
        $asosiasi               = get_data('tbl_m_asosiasi','id',post('id_asosiasi'))->row_array();
        $unit_daftar            = get_data('tbl_m_unit','id',post('id_unit_daftar'))->row_array();
        $negara                 = get_data('tbl_m_negara','id',post('id_negara'))->row_array();

        $data['bentuk_badan_usaha']     = isset($bentuk_badan_usaha['bentuk_badan_usaha']) ? $bentuk_badan_usaha['bentuk_badan_usaha'] : '';
        $data['status_perusahaan']      = isset($status_perusahaan['status_perusahaan']) ? $status_perusahaan['status_perusahaan'] : '';
        $data['kualifikasi']            = isset($kualifikasi['kualifikasi']) ? $kualifikasi['kualifikasi'] : '';
        $data['asosiasi']               = isset($asosiasi['asosiasi']) ? $asosiasi['asosiasi'] : '';
        $data['unit_daftar']            = isset($unit_daftar['unit']) ? $unit_daftar['unit'] : '';
        $data['nama_negara']            = isset($negara['nama']) ? $negara['nama'] : '';
        if(is_array(post('id_kategori_rekanan')) && count(post('id_kategori_rekanan')) > 0) { 
            $kategori_rekanan           = get_data('tbl_m_kategori_rekanan','id',post('id_kategori_rekanan'))->result_array();
            $data['id_kategori_rekanan']= json_encode(post('id_kategori_rekanan'));
            $_kategori                  = [];
            foreach($kategori_rekanan as $k) {
                $_kategori[]            = $k['kategori'];
            }
            $data['kategori_rekanan']   = implode(', ',$_kategori);
        }
        $data['create_at']              = date('Y-m-d H:i:s');
        $data['is_active']              = 1;
        $data['terdaftar_sejak']        = date('Y-m-d H:i:s');
        $data['is_pendaftar']           = 1;

        if($img->check(post('ct_captcha'))) {
            $cek_npwp1  = get_data('tbl_vendor','npwp',$data['npwp'])->row();
            if(isset($cek_npwp1->id)) {
                $response   = [
                    'status'    => 'failed',
                    'message'   => lang('rekanan_dengan_npwp_ini_sudah_pernah_mendaftar')
                ];
            } else {
                $cek_email_cp1  = get_data('tbl_vendor','email_cp',$data['email_cp'])->row();
                if(isset($cek_email_cp1->id)) {
                    $response   = [
                        'status'    => 'failed',
                        'message'   => lang('email_kontak_person_sudah_digunakan')
                    ];
                } else {
                    $res            = save_data('tbl_vendor',$data,post(':validation'),true);
                    $password       = $this->generate_password();
                    if($res['status'] == 'success') {
                        $vendor     = get_data('tbl_vendor','id',$res['id'])->row_array();
                        $user       = [
                            'id_group'              => 99,
                            'kode'                  => $vendor['kode_rekanan'],
                            'nama'                  => $vendor['nama'],
                            'username'              => $vendor['kode_rekanan'],
                            'password'              => c_password($password),
                            'email'                 => $vendor['email_cp'],
                            'telepon'               => $vendor['hp_cp'],
                            'jabatan'               => 'Rekanan',
                            'id_vendor'             => $vendor['id'],
                            'is_active'             => 1,
                            'create_at'             => date('Y-m-d H:i:s'),
                            'change_password_at'    => date('Y-m-d H:i:s'),
                            'create_by'             => 'Pendaftaran'
                        ];
                        $check      = get_data('tbl_user','kode',$vendor['kode_rekanan'])->row();
                        if(isset($check->id)) {
                            update_data('tbl_user',$user,'id',$check->id);
                            $user_id    = $check->id;
                        } else {
                            $user_id    = insert_data('tbl_user',$user);
                        }
                        $username   = $vendor['kode_rekanan'];

                        $r = [];
                        foreach(post('id_kategori_rekanan') as $k) {
                            $r[] = [
                                'id_vendor'             => $res['id'],
                                'email'                 => $vendor['email_cp'],
                                'id_kategori_rekanan'   => $k,
                                'id_user'               => $user_id,
                                'is_active'             => 1
                            ];
                        }
                        insert_batch("tbl_vendor_kategori",$r);

                        // kirim email ke calon vendor
                        send_mail([
                            'to'        => $data['email_cp'],
                            'subject'   => 'Pendaftaran '.setting('title').' '.setting('company'),
                            'vendor'    => $vendor,
                            'username'  => $username,
                            'password'  => $password
                        ]);

                        // kirim notifikasi ke user persetujuan
                        $user_persetujuan = get_data('tbl_user',[
                            'where'     => [
                                'is_active' => 1,
                                'id_group'  => id_group_access('calon_rekanan','additional')
                            ]
                        ])->result();
                        foreach($user_persetujuan as $u) {
                            $link               = base_url().'manajemen_rekanan/calon_rekanan?i='.encode_id([$res['id'],rand()]);
                            $desctiption        = 'Pendaftaran rekanan baru atas nama <strong>'.$data['nama'].'</strong>';
                            $data_notifikasi    = [
                                'title'         => 'Pendaftaran Rekanan',
                                'description'   => $desctiption,
                                'notif_link'    => $link,
                                'notif_date'    => date('Y-m-d H:i:s'),
                                'notif_type'    => 'info',
                                'notif_icon'    => 'fa-users',
                                'id_user'       => $u->id,
                                'transaksi'     => 'pendaftaran_rekanan',
                                'id_transaksi'  => $res['id']
                            ];
                            insert_data('tbl_notifikasi',$data_notifikasi);
                        }

                        $response = [
                            'status'    => 'success',
                            'message'   => lang('pendaftaran_rekanan_berhasil')
                        ];
                    }else{
                        $response = $res;
                    }
                }
            }
        } else {
            $response   = [
                'status'    => 'failed',
                'message'   => lang('captcha_tidak_valid')
            ];
        }
        render($response,'json');
    }

     function securimage() {
        $img = new Securimage();
        $img->show();
    }

    function securimage_audio() {
        $bahasa             = setting('language') == 'id' ? 'id' : 'en';
        $img                = new Securimage();
        $img->audio_path    = $img->securimage_path . '/audio/'.$bahasa.'/';
        $img->outputAudioFile(null);
    }

}