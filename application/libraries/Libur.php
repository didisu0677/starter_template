<?php

class Libur {

	private $source_url		= 'https://www.liburnasional.com/kalender-'; // source : https://www.liburnasional.com/kalender-{tahun}/
	private $tahun			= '';

	function __construct($config=[]) {
		$this->tahun = isset($config['tahun']) ? $config['tahun'] : date('Y');
	}

	public function get($tahun=false) {
		if($tahun)
			$this->tahun 	= $tahun;
		$source_url			= $this->source_url . $this->tahun . '/';
		$source 			= $this->httpsCurl($source_url);
		$s1 				= preg_replace('!\s+!', ' ', $this->get_between('libnas-content">','<footer',$source));
		preg_match_all('/libnas-holiday-calendar-detail">.*?>(.*?)<\/time>/si', $s1, $res);
		$result				= [];
		foreach($res[1] as $r) {
			$keterangan		= $this->get_between('itemprop="url">','</a>',$r);
			if($keterangan == 'Jumat Agung') 		$keterangan = 'Wafat Isa Al Masih';
			else if($keterangan == 'Isra Miraj') 	$keterangan = 'Isra Mi\'raj Nabi Muhammad SAW';
			else if($keterangan == 'Maulid Nabi')	$keterangan = 'Maulid Nabi Muhammad SAW';
			$tanggal        = $this->get_between('datetime="','"',$r);
			$e_tanggal      = explode('datetime="'.$tanggal.'">', $r);
			$str_tanggal    = $e_tanggal[1];
			$e_str_tanggal  = explode('-', $str_tanggal);
			if(count($e_str_tanggal) == 1) {
				$result[]		= [
					'tanggal'		=> date('Y-m-d',strtotime($tanggal)),
					'keterangan'	=> $keterangan
				];
			} else {
				$str_start	= substr(trim($e_str_tanggal[0]),0,2);
				$str_end	= substr(trim($e_str_tanggal[1]),0,2);

				$end_month	= $str_start < $str_end ? $tanggal : date('Y-m-d',strtotime('+1 month',strtotime($tanggal)));

				$start_date	= date('Y-m-',strtotime($tanggal)) . sprintf('%02d',$str_start);
				$end_date	= date('Y-m-',strtotime($end_month)) . sprintf('%02d',$str_end);

				$begin 		= new DateTime($start_date);
				$end 		= new DateTime(date('Y-m-d',strtotime('+1 day',strtotime($end_date))));

				$interval 	= DateInterval::createFromDateString('1 day');
				$period 	= new DatePeriod($begin, $interval, $end);

				foreach ($period as $dt) {
					$result[]		= [
						'tanggal'		=> $dt->format('Y-m-d'),
						'keterangan'	=> $keterangan
					];
				}
			}
		}
		return $result;
	}

	private function httpsCurl($url,$urlref=false,$fields=false) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		if($urlref) {
			curl_setopt($ch, CURLOPT_REFERER, $urlref);
		}
		if($fields) {
			$fields_string = '';
			foreach($fields as $key=>$value) {
				$fields_string .= $key.'='.$value.'&';
			}
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
			curl_setopt($ch,CURLOPT_POST,count($fields));
		} else {
			curl_setopt($ch,CURLOPT_HTTPGET, TRUE);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$html = curl_exec($ch);
		return $html;
	}

	private function get_between($var1="",$var2="",$pool){
		$temp1 		= strpos($pool,$var1)+strlen($var1);
		$result 	= substr($pool,$temp1,strlen($pool));
		$dd			= strpos($result,$var2);
		if($dd == 0){
			$dd 	= strlen($result);
		}

		return substr($result,0,$dd);
	}

}
