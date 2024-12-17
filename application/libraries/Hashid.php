<?php

require (__DIR__.'/Hashids/Hashids.php');

class Hashid {

	protected $key = '1234567890987654321234567890';

	function encode($id='') {
		$hashids 	= new Hashids\Hashids($this->key);
		$encode_id	= $hashids->encode($id);
		return $encode_id;
	}

	function decode($str='') {
		$hashids 	= new Hashids\Hashids($this->key);
		$decode_id	= $hashids->decode($str);
		return $decode_id;
	}
}