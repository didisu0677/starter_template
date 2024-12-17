<?php
class Database {
    private $conn;
    
    function __construct() {
        require(__DIR__ . '/../db.php');
        date_default_timezone_set('Asia/Jakarta');
        $key        = hash('sha256','260992');
        $hostname   = openssl_decrypt($__db['hostname'],'AES-256-CBC',$key,0,substr($key, 0, 16));
        $username   = openssl_decrypt($__db['username'],'AES-256-CBC',$key,0,substr($key, 0, 16));
        $password   = openssl_decrypt($__db['password'],'AES-256-CBC',$key,0,substr($key, 0, 16));
        $database   = openssl_decrypt($__db['database'],'AES-256-CBC',$key,0,substr($key, 0, 16));
        $this->conn = mysqli_connect($hostname, $username, $password, $database);
        if($this->conn === false){
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }
    }

    public function get_user($id=0) {
        $row        = [];
        if($this->conn) {
            $sql    = "SELECT * FROM tbl_user WHERE id = $id";
            $result = mysqli_query($this->conn, $sql);
            $row    = mysqli_fetch_assoc($result);
            mysqli_close($this->conn);
        }
        return $row;
    }

    public function get_user_online($id=0,$keyword="") {
        $last_activity	= date('Y-m-d H:i:s',strtotime('-30 minutes',strtotime('now')));
        $result         = [];
        if($this->conn) {
            if($keyword) {
                $sql    = "SELECT `id`,`nama`,`foto`,`is_login`,`last_activity` FROM `tbl_user` WHERE `id` != $id AND `is_active` = 1 AND `nama` LIKE '%$keyword%' ORDER BY nama";
            } else {
                $sql    = "SELECT `id`,`nama`,`foto`,`is_login`,`last_activity` FROM `tbl_user` WHERE `id` != $id AND `is_active` = 1 AND `is_login` = 1 AND `last_activity` > '$last_activity' ORDER BY nama LIMIT 20";
            }
            $res = mysqli_query($this->conn, $sql);

            while( $row = mysqli_fetch_assoc($res)){
                $l_activity     = strtotime('now') - strtotime($row['last_activity']);
                if($l_activity > 1800) $row['is_login'] = 0;
                $result[] = $row;
            }
            mysqli_close($this->conn);
        }
        return $result;
    }

    public function get_group_chat($id=0) {
        $result         = [];
        if($this->conn) {
            $sql    = "SELECT `key_id` FROM `tbl_chat_anggota` WHERE `id_user` = $id";
            $res    = mysqli_query($this->conn, $sql);
            $k_id   = [0];
            while( $row = mysqli_fetch_assoc($res)){
                $k_id[] = $row['key_id'];
            }

            $key_id = implode(',',$k_id);
            $sql    = "SELECT * FROM `tbl_chat_key` WHERE `id` IN ($key_id) AND is_active = 1 AND is_group = 1 AND ((aktif_mulai <= '".date('Y-m-d H:i:s')."' AND aktif_selesai >= '".date('Y-m-d H:i:s')."') OR (aktif_mulai = '0000-00-00 00:00:00' AND aktif_selesai = '0000-00-00 00:00:00'))";
            $res    = mysqli_query($this->conn, $sql);
            while( $row = mysqli_fetch_assoc($res)){
                $result[] = $row;
            }
            mysqli_close($this->conn);
        }
        return $result;        
    }

    public function get_list_chat($id=0) {
        $result         = [];
        if($this->conn) {
            $sql    = "SELECT `a`.`id` FROM `tbl_chat_key` `a` LEFT JOIN `tbl_chat_anggota` `b` ON `b`.`key_id` = `a`.`id` WHERE `b`.`id_user` = $id AND `a`.`is_active` = 1 AND ((aktif_mulai <= '".date('Y-m-d H:i:s')."' AND aktif_selesai >= '".date('Y-m-d H:i:s')."') OR (aktif_mulai = '0000-00-00 00:00:00' AND aktif_selesai = '0000-00-00 00:00:00'))";
            $res = mysqli_query($this->conn, $sql);

            $k_id = ['-1'];
            while( $row = mysqli_fetch_assoc($res)){
                $k_id[] = $row['id'];
            }
            $key_id = implode(',',$k_id);

            $sql    = 'SELECT `a`.*, `b`.`nama`, `b`.`foto`, `c`.`nama` AS `nama_group`, `c`.`is_group`, `d`.`nama` AS `nama_pengirim`, `e`.`is_read` AS `is_read2` FROM `tbl_chat` `a` LEFT JOIN `tbl_user` `b` ON (`a`.`id_pengirim` = `b`.`id` AND `a`.`id_pengirim` != '.$id.') OR (`a`.`id_penerima` = `b`.`id` AND `a`.`id_penerima` != '.$id.') LEFT JOIN `tbl_chat_key` `c` ON `a`.`key_id` = `c`.`id` LEFT JOIN `tbl_user` `d` ON `a`.`id_pengirim` = `d`.`id` LEFT JOIN `tbl_chat_anggota` `e` ON `a`.`key_id` = `e`.`key_id` AND `e`.`id_user` = '.$id.' WHERE `a`.`id` IN (SELECT MAX(`id`) FROM `tbl_chat` WHERE `key_id` IN ('.$key_id.') GROUP BY `key_id`) ORDER BY `tanggal` DESC';
            $res = mysqli_query($this->conn, $sql);

            while( $row = mysqli_fetch_assoc($res)){
                $result[] = $row;
            }
            mysqli_close($this->conn);
        }
        return $result;
    }

    public function get_chat_key($id_pengirim=0,$id_penerima=0) {
        $result = '';
        if($this->conn) {
            $sql    = "SELECT `key_id` FROM `tbl_chat` WHERE (`id_penerima` = $id_penerima AND `id_pengirim` = $id_pengirim) OR (`id_penerima` = $id_pengirim AND `id_pengirim` = $id_penerima)";
            $res    = mysqli_query($this->conn, $sql);
            $row    = mysqli_fetch_assoc($res);
            if(isset($row['key_id'])) {
                $result = $row['key_id'];
            } else {
                $key1       = $id_pengirim . '___' . $id_penerima;
                $key2       = $id_penerima . '___' . $id_pengirim;
                $sql        = "SELECT `id` FROM `tbl_chat_key` WHERE `_key` IN ('$key1','$key2')";
                $res        = mysqli_query($this->conn, $sql);
                $row        = mysqli_fetch_assoc($res);
                if(isset($row['id'])) {
                    $result = $row['id'];
                } else {
                    $sql        = "SELECT `nama` FROM `tbl_user` WHERE `id` IN ($id_pengirim,$id_penerima) AND is_active = 1";
                    $res        = mysqli_query($this->conn, $sql);
                    $nama       = [];
                    while($row  = mysqli_fetch_assoc($res)){
                        $nama[] = $row['nama'];
                    }
                    $anggota    = implode(', ' , $nama);
                    $a          = implode(' dan ', $nama);
                    $sql        = "INSERT INTO tbl_chat_key (nama, anggota, _key, is_active) VALUES ('Obrolan Privat $a', '$anggota', '$key1', 1)";
                    mysqli_query($this->conn, $sql);
                    $result = mysqli_insert_id($this->conn);
                    foreach([$id_pengirim,$id_penerima] as $i) {
                        $sql        = "INSERT INTO tbl_chat_anggota (key_id, id_user) VALUES ($result, $i)";
                        mysqli_query($this->conn, $sql);    
                    }
                }
            }            
            mysqli_close($this->conn);
        }
        return $result;
    }

    public function get_penerima($key_id=0) {
        $result = [];
        if($this->conn) {
            $sql    = "SELECT id_user FROM tbl_chat_anggota WHERE key_id = $key_id";
            $res = mysqli_query($this->conn, $sql);

            while( $row = mysqli_fetch_assoc($res)){
                $result[] = $row['id_user'];
            }
            mysqli_close($this->conn);
        }
        return $result;
    }

    public function get_nama_group($key_id=0) {
        $result = '';
        if($this->conn) {
            $sql    = "SELECT nama FROM tbl_chat_key WHERE id = $key_id";
            $res    = mysqli_query($this->conn, $sql);

            while( $row = mysqli_fetch_assoc($res)){
                $result = $row['nama'];
            }
            mysqli_close($this->conn);
        }
        return $result;
    }

    public function get_not_read_chat($id_penerima=0) {
        $result     = 0;
        if($this->conn) {
            $sql    = "SELECT COUNT(DISTINCT(`key_id`)) AS jml FROM `tbl_chat_anggota` WHERE `id_user` = $id_penerima AND `is_read` = 0";
            $res    = mysqli_query($this->conn, $sql);
            $row    = mysqli_fetch_assoc($res);
            mysqli_close($this->conn);
            if(isset($row['jml'])) {
                $result = $row['jml'];
            }
        }
        return $result;
    }
    
    public function save_chat($data = []) {
        if($this->conn) {
            $id_pengirim    = isset($data['id_pengirim']) ? mysqli_real_escape_string($this->conn, $data['id_pengirim']) : 0;
            $id_penerima    = isset($data['id_penerima']) ? mysqli_real_escape_string($this->conn, $data['id_penerima']) : 0;
            $pesan          = isset($data['pesan']) ? mysqli_real_escape_string($this->conn, $data['pesan']) : '';
            $tanggal        = isset($data['tanggal']) ? mysqli_real_escape_string($this->conn, $data['tanggal']) : date('Y-m-d H:i:s');
            $key_id         = isset($data['key_id']) ? mysqli_real_escape_string($this->conn, $data['key_id']) : 0;
            if($id_pengirim && $pesan) {
                $sql = "INSERT INTO tbl_chat (id_pengirim, id_penerima, pesan, tanggal, key_id) VALUES ('$id_pengirim', '$id_penerima', '$pesan', '$tanggal', '$key_id')";
                mysqli_query($this->conn, $sql);

                $sql = "UPDATE tbl_chat_anggota SET is_read = 0 WHERE key_id = $key_id AND id_user != $id_pengirim";
                mysqli_query($this->conn, $sql);
            }
            mysqli_close($this->conn);
        }
    }

    public function get_chat($key_id='',$last_id=0) {
        $result         = [];
        if($this->conn) {
            if($last_id) {
                $sql    = 'SELECT `a`.*, `b`.`nama` AS `nama_pengirim` FROM `tbl_chat` `a` LEFT JOIN `tbl_user` `b` ON `a`.`id_pengirim` = `b`.`id` WHERE `key_id` = "'.$key_id.'" AND `a`.`id` < '.$last_id.' ORDER BY `a`.`id` DESC LIMIT 20';
            } else {
                $sql    = 'SELECT `a`.*, `b`.`nama` AS `nama_pengirim` FROM `tbl_chat` `a` LEFT JOIN `tbl_user` `b` ON `a`.`id_pengirim` = `b`.`id` WHERE `key_id` = "'.$key_id.'" ORDER BY `a`.`id` DESC LIMIT 20';
            }
            $res = mysqli_query($this->conn, $sql);
            $result = [];
            while( $row = mysqli_fetch_assoc($res)){
                $result[] = $row;
            }
            mysqli_close($this->conn);
        }
        return $result;
    }

    public function read_chat($id_penerima=0, $key_id=0) {
        if($this->conn) {
            if($key_id && $id_penerima) {
                $sql = "UPDATE tbl_chat SET is_read = 1 WHERE id_penerima = $id_penerima AND key_id = $key_id";
                mysqli_query($this->conn, $sql);
                $sql = "UPDATE tbl_chat_anggota SET is_read = 1 WHERE id_user = $id_penerima AND key_id = $key_id";
                mysqli_query($this->conn, $sql);
            }
            mysqli_close($this->conn);
        }
    }
}