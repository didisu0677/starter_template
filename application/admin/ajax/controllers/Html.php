<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Html extends MY_Controller {

    function chat_anggota($key_id=0) {
        $user = get_data('tbl_chat_anggota a',[
            'select'    => 'a.*,b.nama,b.foto',
            'join'      => 'tbl_user b ON a.id_user = b.id',
            'where'     => 'a.key_id = '.$key_id
        ])->result();
        foreach($user as $u) {
            $foto = $u->foto ? $u->foto : 'default.png';
            echo '<div class="chat-list-item"><img src="'.base_url(dir_upload('user').$foto).'"><span class="single-line">'.$u->nama.'</span></div>';
        }
    }

}