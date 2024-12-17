<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Restore extends BE_Controller {
	
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$data['backup']	= scandir(FCPATH . 'assets/backup/');
		foreach($data['backup'] as $k => $f) {
			if(substr($f,0,1) == '.' || is_dir(FCPATH . 'assets/backup/'.$f)) unset($data['backup'][$k]);
		}
		rsort($data['backup']);
		render($data);
	}
	
	function import() {
		ini_set('memory_limit', '-1');
		$response 	= [
			'status'	=> 'failed',
			'message'	=> lang('file_gagal_diunggah')
		];
		$backup	= scandir(FCPATH . 'assets/backup/');
		$backup_key = [];
		foreach($backup as $b) {
			$backup_key[$b] = $b;
		}
		
		$file_zip 	= post('fileimport');
		$zip 		= new ZipArchive;
		if ($zip->open($file_zip) === TRUE) {
			$zip->extractTo(FCPATH . 'assets/backup/');
			$zip->close();
			$file    	= basename(post('fileimport'));
			$temp_dir   = str_replace($file, '', post('fileimport'));
			delete_dir(FCPATH . $temp_dir);
			
			$backup2	= scandir(FCPATH . 'assets/backup/');
			foreach($backup2 as $k => $v) {
				if(isset($backup_key[$v])) unset($backup2[$k]);
			}
			foreach($backup2 as $b) {
				$value		= scandir(FCPATH . 'assets/backup/'.$b.'/');
				foreach($value as $k => $f) {
					$x = explode('.',$f);
					$ext = $x[count($x)-1];
					if($ext && $ext != 'sql') @unlink(FCPATH . 'assets/backup/'.$b.'/'.$f);
				}
			}
			
			$response 	= [
				'status'	=> 'success',
				'message'	=> lang('file_berhasil_diunggah')
			];
		}
		render($response,'json');
	}
	
	function get_file() {
		$dir = FCPATH . 'assets/backup/'.post('file').'/';
		$data	= scandir($dir);
		foreach($data as $k => $f) {
			if(substr($f,0,1) == '.') unset($data[$k]);
			else {
				if(strpos($f,'.sql') != false) {
					$data[$k] = str_replace('.sql','',$f);
				} else unset($data[$k]);
			}
		}
		render($data,'json');
	}
	
	function proccess() {
		$value = post('value');
		foreach($value as $v) {
			if(table_exists($v)) {
				db_query('DROP TABLE '.$v);
			}
			if(file_exists(FCPATH . 'assets/backup/' . post('file') . '/' . $v . '.sql')) {
				$isi_file = file_get_contents(FCPATH . 'assets/backup/' . post('file') . '/' . $v . '.sql');
				$string_query = rtrim( $isi_file, "\n;" );
				$array_query = explode(";", $string_query);
				foreach($array_query as $query){
					db_query($query);
				}
			}
		}
		$response 	= [
			'status'	=> 'success',
			'message'	=> lang('data_berhasil_direstore')
		];
		render($response,'json');
	}
	
}