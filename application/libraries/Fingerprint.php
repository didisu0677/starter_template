<?php

class Fingerprint{
    
    private $ip     = "";
    private $key    = "0";
    private $port   = "80";
    private $tgl    = '';
    private $all    = false;
    
    function __construct($config=[]) {
        if(isset($config['ip']) && $config['ip'])           $this->ip   = $config['ip'];
        if(isset($config['key']) && $config['key'])         $this->key  = $config['key'];
        if(isset($config['port']) && $config['port'])       $this->port = $config['port'];
        if(isset($config['tanggal']) && $config['tanggal']) $this->tgl  = $config['tanggal'];
    }
    
    function set_ip($ip = '') {
        if($ip) $this->ip = $ip;
    }
    
    function set_key($key = '') {
        if($key) $this->key = $key;
    }
    
    function set_port($port = '') {
        if($port) $this->port = $port;
    }
    
    function set_tanggal($tanggal = '') {
        if($tanggal) $this->tgl = $tanggal;
    }
    
    function set_all($all = '') {
        if($all) $this->all = $all;
    }
    
    function get($ip='',$key='', $port='') {
        if($ip)     $this->ip   = $ip;
        if($key)    $this->key  = $key;
        if($port)   $this->port = $port;
        
        $con = fsockopen($this->ip, $this->port, $errno, $errstr, 1);
        if ($con) {
            $soap_request = "<GetAttLog>
            <ArgComKey xsi:type=\"xsd:integer\">".$this->key."</ArgComKey>
            <Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg>
            </GetAttLog>";
            
            $newLine = "\r\n";
            fputs($con, "POST /iWsService HTTP/1.0".$newLine);
            fputs($con, "Content-Type: text/xml".$newLine);
            fputs($con, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
            fputs($con, $soap_request.$newLine);
            $buffer = "";
            while($Response = fgets($con, 1024)) {
                $buffer = $buffer.$Response;
            }
        } else { 
            echo "Koneksi Gagal"; die();
        }
        
        $buffer = $this->parse_data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
        $buffer = explode("\r\n",$buffer);
        
        $export = [];
        $temp   = [];
        $i      = 0;
        for ($a=0; $a<count($buffer); $a++) {
            $data           = $this->parse_data($buffer[$a],"<Row>","</Row>");
            $waktu          = $this->parse_data($data,"<DateTime>","</DateTime>");
            $pin            = $this->parse_data($data,"<PIN>","</PIN>");
            $status         = $this->parse_data($data,"<Status>","</Status>");
            $e_waktu        = explode(' ',$waktu);
            if($this->tgl) {
                if($this->all) {
                    if(isset($e_waktu[0]) && $e_waktu[0] == $this->tgl) {
                        $export[$i]['pin']      = $pin;
                        $export[$i]['tanggal']  = isset($e_waktu[0]) ? $e_waktu[0] : '';
                        $export[$i]['jam']      = isset($e_waktu[1]) ? $e_waktu[1] : '';
                        $export[$i]['status']   = $status == 0 ? 'masuk' : 'pulang';
                        $i++;
                    }
                } else {
                    if(isset($e_waktu[0]) && $e_waktu[0] == $this->tgl) {
                        if(!isset($temp[$e_waktu[0]][$pin][$status])) {
                            $export[$i]['pin']      = $pin;
                            $export[$i]['tanggal']  = isset($e_waktu[0]) ? $e_waktu[0] : '';
                            $export[$i]['jam']      = isset($e_waktu[1]) ? $e_waktu[1] : '';
                            $export[$i]['status']   = $status == 0 ? 'masuk' : 'pulang';
                            $temp[$e_waktu[0]][$pin][$status]   = $i;
                            $i++;
                        } else {
                            if($status == 1) {
                                $export[$temp[$e_waktu[0]][$pin][$status]]['jam'] = isset($e_waktu[1]) ? $e_waktu[1] : '';
                            }
                        }
                    }
                }
            } else {
                if($pin) {
                    if($this->all) {
                        $export[$i]['pin']      = $pin;
                        $export[$i]['tanggal']  = isset($e_waktu[0]) ? $e_waktu[0] : '';
                        $export[$i]['jam']      = isset($e_waktu[1]) ? $e_waktu[1] : '';
                        $export[$i]['status']   = $status == 0 ? 'masuk' : 'pulang';
                        $i++;    
                    } else {
                        if(!isset($temp[$e_waktu[0]][$pin][$status])) {
                            $export[$i]['pin']      = $pin;
                            $export[$i]['tanggal']  = isset($e_waktu[0]) ? $e_waktu[0] : '';
                            $export[$i]['jam']      = isset($e_waktu[1]) ? $e_waktu[1] : '';
                            $export[$i]['status']   = $status == 0 ? 'masuk' : 'pulang';
                            $temp[$e_waktu[0]][$pin][$status]   = $i;
                            $i++;
                        } else {
                            if($status == 1) {
                                $export[$temp[$e_waktu[0]][$pin][$status]]['jam'] = isset($e_waktu[1]) ? $e_waktu[1] : '';
                            }
                        }
                    }
                }
            }
        }
        return $export;
    }
    
    private function parse_data($data,$p1,$p2) {
        $data = " ".$data;
        $hasil = "";
        $awal = strpos($data,$p1);
        if ($awal != "") {
            $akhir = strpos(strstr($data,$p1),$p2);
            if ($akhir != ""){
                $hasil=substr($data,$awal+strlen($p1),$akhir-strlen($p1));
            }
        }
        return $hasil;    
    }
}