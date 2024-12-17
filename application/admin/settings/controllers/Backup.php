<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backup extends BE_Controller {
	
	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$data['backup_db']		= scandir(FCPATH . 'assets/backup/');
		$data['backup_file']	= [];
		foreach($data['backup_db'] as $k => $f) {
			if(substr($f,0,1) == '.') unset($data['backup_db'][$k]);
			else {
				if(!is_dir(FCPATH . 'assets/backup/'.$f)) {
					$data['backup_file'][] = $data['backup_db'][$k];
					unset($data['backup_db'][$k]);
				}
			}
		}
		$data['fileupload']	= scandir(FCPATH . 'assets/uploads/');
		foreach($data['fileupload'] as $k => $f) {
			if(substr($f,0,1) == '.' || $f == 'temp') unset($data['fileupload'][$k]);
		}
		rsort($data['fileupload']);
		$data['table']	= db_list_table();
		render($data);
	}
	
	function process() {
		ini_set('memory_limit', '-1');
		if(get_access('backup')['access_input'] && post('x') == 'x') {
			$backupdir = FCPATH . 'assets/backup/backup_db_'.date('Y_m_d_h_i');
			if(!is_dir($backupdir)) mkdir($backupdir, 0777, true);
			
			$table = post('table');
			$this->load->dbutil();
			$this->load->helper('file');
			foreach($table as $t) {
				$prefs 	= array(
					'tables'      => array($t),
					'format'      => 'sql',
					'filename'    => $t.'.sql'
				);
				$backup		= $this->dbutil->backup($prefs);
				$db_name 	= $t.'.sql';
				$save 		= $backupdir.'/'.$db_name;
				write_file($save, $backup);
			}

			$dir 		= post('files');
			if(is_array($dir) && count($dir) > 0) {
				$conf 		= [
					'src' 		=> FCPATH . 'assets/uploads/',
					'dst' 		=> FCPATH . 'assets/backup/',
					'filename'	=> 'backup_file_'.date('Y_m_d_h_i'),
					'dir'		=> $dir
				];
				$this->load->library('Rzip',$conf);
				$this->rzip->compress();
			}
			
			$response 	= [
				'status'	=> 'success',
				'message'	=> lang('data_berhasil_dibackup')
			];
		} else {
			$response = [
				'status'	=> 'failed',
				'message'	=> lang('izin_ditolak')
			];	
		}
		render($response,'json');
	}

	function delete() {
		$backup = post('backup');
		$response 	= [
			'status'	=> 'failed',
			'message'	=> lang('izin_ditolak')
		];
		if(get_access('backup')['access_delete']) {
			$del_source 	= FCPATH . 'assets/backup/'.$backup;
			if(is_dir($del_source)) {
				delete_dir($del_source);
			} else {
				@unlink($del_source);
			}
			$response 		= [
				'status'	=> 'success',
				'message'	=> lang('data_berhasil_dihapus')
			];
		}
		render($response,'json');
	}

	function download() {
		ini_set('memory_limit', '-1');
		if(get('b') && get_access('backup')['access_additional']) {
			if(is_dir(FCPATH . 'assets/backup/'.get('b'))) {
				$this->load->library('zip');
				$path = 'assets/backup/'.get('b');
				$this->zip->read_dir($path,false);
				$this->zip->download(get('b').'.zip');
			} else {
				$this->load->helper('download');
				force_download(FCPATH . 'assets/backup/' . get('b'), NULL);
			}
		} else {
			flash_message('error',lang('izin_ditolak'));
			redirect('settings/backup');
		}
	}
	
}