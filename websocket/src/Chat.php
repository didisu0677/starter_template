<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require(__DIR__ . '/../Database.php');
require(__DIR__ . '/../Emoji.php');

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        date_default_timezone_set('Asia/Jakarta');
        $this->clients = new \SplObjectStorage;

        echo "Server Started";
    }

    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $data = json_decode($msg,true);
        $self = false;
        if(isset($data['type']) && $data['type'] == 'chat') {

            // menyimpan chat ke database
            $tanggal    = date('Y-m-d H:i:s');
            $rows = [
                'id_pengirim'   => $data['id_pengirim'],
                'id_penerima'   => $data['id_penerima'],
                'pesan'         => \Emoji::Encode($data['pesan']),
                'tanggal'       => $tanggal,
                'key_id'        => $data['chat_key']
            ];
            $db     = new \Database();
            $db->save_chat($rows);

            // jika id_penerima = 0 maka dia chat group, ambil data dari chat_anggota
            if(!$data['id_penerima']) {
                $db                     = new \Database();
                $data['id_penerima']    = $db->get_penerima($data['chat_key']);

                $db                     = new \Database();
                $nama_group             = $db->get_nama_group($data['chat_key']);
            }

            // baca pesan
            $db     = new \Database();
            $db->read_chat($data['id_pengirim'],$data['chat_key']);

            // mendapatkan foto dan nama user pengirim
            $db     = new \Database();
            $user   = $db->get_user($data['id_pengirim']);
            $data['foto_pengirim']  = $user['foto'];
            $data['nama_pengirim']  = $user['nama'];
            if(isset($nama_group) && $nama_group) {
                $data['nama_pengirim'] .= ' @ '.$nama_group;
            }
            $data['tanggal']        = $tanggal;
            $msg            = json_encode($data);
        } elseif(isset($data['type']) && $data['type'] == 'user_online') {
            $self           = true;
            $db             = new \Database();
            $data['data']   = $db->get_user_online($data['id'],$data['keyword']);
            $msg            = json_encode($data);
        } elseif(isset($data['type']) && $data['type'] == 'list_chat') {
            $self           = true;
            $db             = new \Database();
            $data['data']   = $db->get_list_chat($data['id']);
            foreach($data['data'] as $k => $v) {
                $data['data'][$k]['pesan'] = \Emoji::Decode($v['pesan']);
            }
            $msg            = json_encode($data);
        } elseif(isset($data['type']) && $data['type'] == 'grup') {
            $self           = true;
            $db             = new \Database();
            $data['data']   = $db->get_group_chat($data['id']);
            $msg            = json_encode($data);
        }  elseif(isset($data['type']) && $data['type'] == 'unread') {
            $self           = true;
            $db             = new \Database();
            $data['unread'] = $db->get_not_read_chat($data['id']);
            $msg            = json_encode($data);
        } elseif(isset($data['type']) && $data['type'] == 'chat_content') {
            $self           = true;
            if(!$data['chat_key']) {
                $db                     = new \Database();
                $data['chat_key']       = $db->get_chat_key($data['id_user1'],$data['id_user2']);
            }
            $last_id    = $data['last_id'];
            $data['last_id'] = -1;
            if($data['chat_key']) {
                $db                     = new \Database();
                $data['data']           = $db->get_chat($data['chat_key'],$last_id);
                foreach($data['data'] as $k => $v) {
                    $data['data'][$k]['pesan'] = \Emoji::Decode($v['pesan']);
                }
                if(count($data['data']) > 0) {
                    $data['last_id'] = $data['data'][count($data['data'])-1]['id'];
                }
            }
            if($last_id == 0) {
                $db     = new \Database();
                $db->read_chat($data['id_user1'],$data['chat_key']);
            }
            $msg            = json_encode($data);
        }

        foreach ($this->clients as $client) {
            if($self) {
                if ($from === $client) {
                    $client->send($msg);
                }
            } else {
                if ($from !== $client) {
                    // The sender is not the receiver, send to each client connected
                    $client->send($msg);
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}