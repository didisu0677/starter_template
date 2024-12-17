<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require __DIR__.'/PHPExcel.php';

class Rzip {

	private $src = '';
	private $dst = '';
	private $fnm = '';
	private $chs = [];

	function __construct($opt) {
		$this->src 		= $opt['src'];
		$this->dst 		= $opt['dst'];
		if(isset($opt['dir'])) {
			$this->chs 	= $opt['dir'];
		}
		if(isset($opt['filename'])) {
			$this->fnm 	= $opt['filename'];
		}
	}

	private function recurse_zip($src, &$zip, $path) {
		$is_root = $src == rtrim($this->src,'/') ? true : false;
		$dir = opendir($src);
		while (false !== ($file = readdir($dir))) {
			$__proses = true;
			if($is_root) {
				if(is_array($this->chs)) {
					if(count($this->chs) > 0 && !in_array($file,$this->chs)) $__proses = false;
				} else {
					if($this->chs != $file) $__proses = false;
				}
			}
			if (($file != '.') && ($file != '..') && $__proses) {
				if (is_dir($src . '/' . $file)) {
					$this->recurse_zip($src . '/' . $file, $zip, $path);
				} else {
					$zip->addFile($src . '/' . $file, substr($src . '/' . $file, $path));
				}
			}
		}
		closedir($dir);
	}

	private function run($src, $dst = '') {
		if (substr($src, -1) === '/') {
			$src 	= substr($src, 0, -1);
		}
		if (substr($dst, -1) === '/') {
			$dst 	= substr($dst, 0, -1);
		}
		$path  		= strlen(dirname($src) . '/');
		$filename 	= $this->fnm ? $this->fnm . '.zip' : substr($src, strrpos($src, '/') + 1) . '.zip';
		$dst  		= empty($dst) ? $filename : $dst . '/' . $filename;
		@unlink($dst);
		$zip 		= new ZipArchive;
		$res 		= $zip->open($dst, ZipArchive::CREATE);
		if ($res !== TRUE) {
			echo 'Error: Unable to create zip file';
			exit;
		}
		if (is_file($src)) {
			$zip->addFile($src, substr($src, $path));
		} else {
			if (!is_dir($src)) {
				$zip->close();
				@unlink($dst);
				echo 'Error: File not found';
				exit;
			}
			$this->recurse_zip($src, $zip, $path);
		}
		$zip->close();
		return $dst;
	}

	public function compress() {
		return $this->run($this->src, $this->dst);
	}

}