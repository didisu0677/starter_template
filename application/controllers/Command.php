<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Command extends BE_Controller {

	function __construct() {
		parent::__construct();
	}

	function index() {
		if(ENVIRONMENT == 'development' && user('id_group') == 1) {
			$data['title']	= 'Command';
			render($data);
		} else {
			$this->load->view('errors/page_not_found');
		}
	}

	function help() {
		if(ENVIRONMENT == 'development' && user('id_group') == 1) {
			$this->load->view('command/help');
		} else {
			$this->load->view('errors/page_not_found');
		}
	}

	function process() {
		$cmd	= post('command');
		$c 		= explode(' ', preg_replace('!\s+!', ' ', $cmd));
		if($c[0] == '-g') {
			if(isset($c[1]) && $c[1]) {
				if($c[1] == 'module') {
					if(isset($c[2]) && $c[2]) {
						$dir_module	= FCPATH . 'application/admin/';
						if(preg_match('/^[a-z_]+$/', $c[2])) {
							$nama_module = strtolower($c[2]);
							if(!file_exists($dir_module.$nama_module)) {
								$check	= get_data('tbl_menu',array('where_array'=>array(
									'target'	=> $nama_module,
									'is_active'	=> 1,
									'parent_id'	=> 0
								)))->row();
								if(isset($check->id)) {
									$oldmask = umask(0);
									if(mkdir($dir_module.$nama_module,0777)) {
										umask($oldmask);
										$oldmask = umask(0);
										if(mkdir($dir_module.$nama_module.'/controllers',0777)) {
											umask($oldmask);
											$i 		= 0;
											$php 	= '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . PHP_EOL . PHP_EOL;
											$php 	.= 'class '.ucfirst($nama_module).' extends BE_Controller {' . PHP_EOL . PHP_EOL;
											$i++;
											$php 	.= add_tab($i) . 'function __construct() {' . PHP_EOL; $i++;
											$php 	.= add_tab($i) . 'parent::__construct();' . PHP_EOL; $i--;
											$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
											$php 	.= add_tab($i) . 'function index() {' . PHP_EOL; $i++;
											$php 	.= add_tab($i) . '$data = array();' . PHP_EOL;
											$php 	.= add_tab($i) . '$module = uri_segment(1);' . PHP_EOL;
											$php 	.= add_tab($i) . '$m_name = get_data(\'tbl_menu\',\'target\',$module)->row();' . PHP_EOL;
											$php 	.= add_tab($i) . 'if(isset($m_name->id)) {' . PHP_EOL; $i++;
											$php 	.= add_tab($i) . '$access = get_data(\'tbl_user_akses\',array(\'where_array\'=>array(\'act_view\'=>1,\'id_group\'=>user(\'id_group\'))))->result();' . PHP_EOL;
											$php 	.= add_tab($i) . '$id_menu = array(0);' . PHP_EOL;
											$php 	.= add_tab($i) . 'foreach($access as $a) {' . PHP_EOL; $i++;
											$php 	.= add_tab($i) . '$id_menu[] = $a->id_menu;' . PHP_EOL; $i--;
											$php 	.= add_tab($i) . '}' . PHP_EOL;
											$php 	.= add_tab($i) . '$data[\'quick_link\'] = get_data(\'tbl_menu\',array(\'where_array\'=>array(\'is_active\'=>1,\'parent_id\'=>$m_name->id),\'where_in\'=>array(\'id\'=>$id_menu),\'sort_by\'=>\'urutan\',\'sort\'=>\'ASC\'))->result();' . PHP_EOL; $i--;
											$php 	.= add_tab($i) . '}' . PHP_EOL;
											$php 	.= add_tab($i) . 'render($data,\'view:home/welcome/quick_link\');' . PHP_EOL; $i--;
											$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
											$php 	.= '}';
											$filename = $dir_module.$nama_module.'/controllers/'.ucfirst($nama_module).'.php';
											$handle = fopen ($filename, "wb");
											if($handle) {
												fwrite ( $handle, $php );
											}
											fclose($handle);
											$oldmask = umask(0);
											chmod($filename, 0777);
											umask($oldmask);
										}
										$oldmask = umask(0);
										mkdir($dir_module.$nama_module.'/views');
										umask($oldmask);
										$response = array(
											'status'	=> 'success',
											'message'	=> 'Module "'.$nama_module.'" berhasil di generate'
										);
									} else {
										$response = array(
											'status'	=> 'failed',
											'message'	=> 'Module "'.$nama_module.'" gagal di generate'
										);										
									}
								} else {
									$response = array(
										'status'	=> 'error',
										'message'	=> 'Module "'.$nama_module.'" tidak terdaftar di menu'
									);
								}
							} else {
								$response = array(
									'status'	=> 'info',
									'message'	=> 'Module '.$c[2].' sudah ada'
								);
							}
						} else {
							$response = array(
								'status'	=> 'error',
								'message'	=> 'Nama module tidak boleh ada angka dan karakter simbol'
							);
						}
					} else {
						$response = array(
							'status'	=> 'error',
							'message'	=> 'Nama module tidak boleh kosong'
						);
					}
				} elseif($c[1] == 'menu') {
					if(isset($c[2]) && $c[2]) {
						$nama_menu	= $c[2];
						$check			= get_data('tbl_menu',array('where_array'=>array(
							'target'	=> $nama_menu,
							'is_active'	=> 1
						)))->row();
						if(isset($check->id)) {
							$module 	= get_data('tbl_menu',array('where_array'=>array(
								'id'		=> $check->level1
							)))->row();
							$dir_module	= FCPATH . 'application/admin/';
							if(file_exists($dir_module.$module->target)) {
								if(!file_exists($dir_module.$module->target.'/controllers')) {
									$oldmask = umask(0);
									mkdir($dir_module.$module->target.'/controllers',0777);
									umask($oldmask);
								}
								if(!file_exists($dir_module.$module->target.'/views')) {
									$oldmask = umask(0);
									mkdir($dir_module.$module->target.'/views',0777);
									umask($oldmask);
								}
								$list_string 	= preg_split('/\r\n|[\r\n]/', $cmd);
								$_cmd			= $list_string[0];
								$c 				= explode(' ', preg_replace('!\s+!', ' ', $_cmd));
								if(!file_exists($dir_module.$module->target.'/controllers/'.$nama_menu.'.php') || (count($c) == 4 && $c[3] == '--force') || (count($c) == 6 && $c[5] == '--force') ) {
									$child	= get_data('tbl_menu','parent_id',$check->id)->result();
									if(count($child) > 0) {
										$i 		= 0;
										$php 	= '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . PHP_EOL . PHP_EOL;
										$php 	.= 'class '.ucfirst($nama_menu).' extends BE_Controller {' . PHP_EOL . PHP_EOL;
										$i++;
										$php 	.= add_tab($i) . 'function __construct() {' . PHP_EOL; $i++;
										$php 	.= add_tab($i) . 'parent::__construct();' . PHP_EOL; $i--;
										$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
										$php 	.= add_tab($i) . 'function index() {' . PHP_EOL; $i++;
										$php 	.= add_tab($i) . '$data = array();' . PHP_EOL;
										$php 	.= add_tab($i) . '$t_menu = uri_segment(2);' . PHP_EOL;
										$php 	.= add_tab($i) . '$m_name = get_data(\'tbl_menu\',\'target\',$t_menu)->row();' . PHP_EOL;
										$php 	.= add_tab($i) . 'if(isset($m_name->id)) {' . PHP_EOL; $i++;
										$php 	.= add_tab($i) . '$access = get_data(\'tbl_user_akses\',array(\'where_array\'=>array(\'act_view\'=>1,\'id_group\'=>user(\'id_group\'))))->result();' . PHP_EOL;
										$php 	.= add_tab($i) . '$id_menu = array(0);' . PHP_EOL;
										$php 	.= add_tab($i) . 'foreach($access as $a) {' . PHP_EOL; $i++;
										$php 	.= add_tab($i) . '$id_menu[] = $a->id_menu;' . PHP_EOL; $i--;
										$php 	.= add_tab($i) . '}' . PHP_EOL;
										$php 	.= add_tab($i) . '$data[\'quick_link\'] = get_data(\'tbl_menu\',array(\'where_array\'=>array(\'is_active\'=>1,\'parent_id\'=>$m_name->id),\'where_in\'=>array(\'id\'=>$id_menu),\'sort_by\'=>\'urutan\',\'sort\'=>\'ASC\'))->result();' . PHP_EOL; $i--;
										$php 	.= add_tab($i) . '}' . PHP_EOL;
										$php 	.= add_tab($i) . 'render($data,\'view:home/welcome/quick_link\');' . PHP_EOL; $i--;
										$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
										$php 	.= '}';
										$filename = $dir_module.$module->target.'/controllers/'.ucfirst($nama_menu).'.php';
										$handle = fopen ($filename, "wb");
										if($handle) {
											fwrite ( $handle, $php );
										}
										fclose($handle);
										$oldmask = umask(0);
										chmod($filename, 0777);
										umask($oldmask);
										$response = array(
											'status'	=> 'success',
											'message'	=> 'Menu "'.$nama_menu.'" berhasil di generate'
										);
									} else {
										if(!file_exists($dir_module.$module->target.'/views/'.$nama_menu)) {
											$oldmask = umask(0);
											mkdir($dir_module.$module->target.'/views/'.$nama_menu,0777);
											umask($oldmask);
										}

										if(!isset($c[3]) || (isset($c[3]) && $c[3] == '--force')) {
											$i 		= 0;
											$php 	= '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . PHP_EOL . PHP_EOL;
											$php 	.= 'class '.ucfirst($nama_menu).' extends BE_Controller {' . PHP_EOL . PHP_EOL;
											$i++;
											$php 	.= add_tab($i) . 'function __construct() {' . PHP_EOL; $i++;
											$php 	.= add_tab($i) . 'parent::__construct();' . PHP_EOL; $i--;
											$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
											$php 	.= add_tab($i) . 'function index() {' . PHP_EOL; $i++;
											$php 	.= add_tab($i) . 'render();' . PHP_EOL; $i--;
											$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
											$php 	.= '}';
											$filename = $dir_module.$module->target.'/controllers/'.ucfirst($nama_menu).'.php';
											$handle = fopen ($filename, "wb");
											if($handle) {
												fwrite ( $handle, $php );
											}
											fclose($handle);
											$oldmask = umask(0);
											chmod($filename, 0777);
											umask($oldmask);

											$i 		= 0;
											$html 	= '<div class="content-header">' . PHP_EOL; $i++;
											$html 	.= add_tab($i) . '<div class="main-container position-relative">' . PHP_EOL; $i++;
											$html 	.= add_tab($i) . '<div class="header-info">' . PHP_EOL; $i++;
											$html 	.= add_tab($i) . '<div class="content-title"><?php echo $title; ?></div>' . PHP_EOL;
											$html 	.= add_tab($i) . '<?php echo breadcrumb(); ?>' . PHP_EOL; $i--;
											$html 	.= add_tab($i) . '</div>' . PHP_EOL;
											$html 	.= add_tab($i) . '<div class="clearfix"></div>' . PHP_EOL; $i--;
											$html 	.= add_tab($i) . '</div>' . PHP_EOL; $i--;
											$html 	.= add_tab($i) . '</div>' . PHP_EOL;
											$html 	.= add_tab($i) . '<div class="content-body">' . PHP_EOL; $i++;
											$html 	.= add_tab($i) . '<div class="main-container">' . PHP_EOL; $i++;
											$html 	.= add_tab($i) . 'Ini adalah menu <?php echo $title; ?> hasil generate' . PHP_EOL; $i--;
											$html 	.= add_tab($i) . '</div>' . PHP_EOL; $i--;
											$html 	.= add_tab($i) . '</div>' . PHP_EOL;
											$filename = $dir_module.$module->target.'/views/'.$nama_menu.'/index.php';
											$handle = fopen ($filename, "wb");
											if($handle) {
												fwrite ( $handle, $html );
											}
											fclose($handle);
											$oldmask = umask(0);
											chmod($filename, 0777);
											umask($oldmask);
											$response = array(
												'status'	=> 'success',
												'message'	=> 'Menu "'.$nama_menu.'" berhasil di generate'
											);
										} else {
											if(isset($c[4]) && $c[3] == '-crud' && $c[4]) {
												if(table_exists($c[4])) {
													$field 		= get_field($c[4]);
													$fields		= [];
													$label		= [];
													$validation	= [];
													$unique_v	= [];
													$alias		= [];
													foreach($field as $f) {
														if($f->name != 'create_by' && $f->name != 'create_at' && $f->name != 'update_by' && $f->name != 'update_at') {
															if($f->name == 'id' && $f->primary_key == 1) {
																$fields[$f->name] = 'primary_key';
															} elseif($f->type == 'tinyint' && $f->max_length == 1) {
																$fields[$f->name] = 'boolean';
															} elseif(strpos($f->name,'password') !== false) {
																$fields[$f->name] = 'password';
															} else {
																$fields[$f->name] = $f->type;
															}
															if($f->type == 'int' && $f->type == 'bigint') {
																$validation[$f->name] = 'number';
															}
															if(strpos($f->name,'email') !== false) {
																$validation[$f->name] = 'email';
															}
															$label[$f->name]	= $f->name == 'is_active' ? 'Aktif' : ucwords(str_replace('_',' ',$f->name));
														}
													}
													if(count($list_string) > 0) {
														foreach($list_string as $kl => $_vl) {
															if($kl > 0) {
																$vl = explode(' ', preg_replace('!\s+!', ' ', $_vl));
																if(isset($fields[$vl[0]])) {
																	foreach($vl as $_l) {
																		$__l = explode('=',$_l);
																		if(count($__l) == 2) {
																			if($__l[0] == 'l') {
																				$label[$vl[0]] = str_replace('+',' ',$__l[1]);
																			} elseif($__l[0] == 't' && strpos($_vl,' a=') === false) {
																				$fields[$vl[0]] = $__l[1];
																			} elseif($__l[0] == 'a' && strpos($vl[0],'id_') !== false) {
																				$_a = explode('.',$__l[1]);
																				if(count($_a) == 2 && table_exists($_a[0])) {
																					$fa = get_field($_a[0],'name');
																					$is_exist = false;
																					foreach($fa as $fa) {
																						if($fa == $_a[1]) $is_exist = true;
																					}
																					if($is_exist){
																						$alias[$vl[0]] = $__l[1];
																						$fields[$vl[0]] = 'select';
																					}
																				}
																			} elseif($__l[0] == 'v') {
																				if(isset($validation[$vl[0]])) {
																					if(strpos($__l[1],$validation[$vl[0]]) !== false) {
																						$validation[$vl[0]] = $__l[1];
																					} else {
																						$validation[$vl[0]] .= '|'.$__l[1];
																					}
																				} else {
																					$validation[$vl[0]] = $__l[1];
																				}
																				if(strpos($__l[1],'unique') !== false) {
																					$unique_v[]	= $vl[0];
																				}
																			}
																		}
																	}
																}
															}
														}
													}

													$lang_field = [];
													if(file_exists(FCPATH . 'assets/lang/id/'.$module->target.'.json')) {
														$get_content = file_get_contents(FCPATH . 'assets/lang/id/'.$module->target.'.json');
														$lang_field = json_decode($get_content,true);
													}

													$i 		= 0;
													$php 	= '<?php if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . PHP_EOL . PHP_EOL;
													$php 	.= 'class '.ucfirst($nama_menu).' extends BE_Controller {' . PHP_EOL . PHP_EOL;
													$i++;
													$php 	.= add_tab($i) . 'function __construct() {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . 'parent::__construct();' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= add_tab($i) . 'function index() {' . PHP_EOL; $i++;
													if(count($alias) > 0) {
														foreach($alias as $ka => $va) {
															$_va = explode('.',$va);
															$_fa = get_field($_va[0],'name');
															$is_active = false;
															foreach($_fa as $_fa) {
																if($_fa == 'is_active') $is_active = true;
															}
															if($is_active) {
																$php .= add_tab($i) . '$data[\'opt_'.$ka.'\'] = get_data(\''.$_va[0].'\',\'is_active\',1)->result_array();' . PHP_EOL;
															} else {
																$php .= add_tab($i) . '$data[\'opt_'.$ka.'\'] = get_data(\''.$_va[0].'\')->result_array();' . PHP_EOL;
															}
														}
														$php 	.= add_tab($i) . 'render($data);' . PHP_EOL;
													} else {
														$php 	.= add_tab($i) . 'render();' . PHP_EOL;
													}
													$i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= add_tab($i) . 'function data() {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . '$data = data_serverside();' . PHP_EOL;
													$php 	.= add_tab($i) . 'render($data,\'json\');' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= add_tab($i) . 'function get_data() {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . '$data = get_data(\''.$c[4].'\',\'id\',post(\'id\'))->row_array();' . PHP_EOL;
													$php 	.= add_tab($i) . 'render($data,\'json\');' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= add_tab($i) . 'function save() {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . '$response = save_data(\''.$c[4].'\',post(),post(\':validation\'));' . PHP_EOL;
													$php 	.= add_tab($i) . 'render($response,\'json\');' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= add_tab($i) . 'function delete() {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . '$response = destroy_data(\''.$c[4].'\',\'id\',post(\'id\'));' . PHP_EOL;
													$php 	.= add_tab($i) . 'render($response,\'json\');' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= add_tab($i) . 'function template() {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . 'ini_set(\'memory_limit\', \'-1\');' . PHP_EOL;
													$php 	.= add_tab($i) . '$arr = [';
													$_arr 	= '';
													foreach($label as $kl => $vl) {
														if($kl != 'id') {
															$_arr .= '\''.$kl.'\' => \''.$kl.'\',';
														}
													}
													$php 	.= substr($_arr,0,strlen($_arr)-1) . '];' . PHP_EOL;
													$php 	.= add_tab($i) . '$config[] = [' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . '\'title\' => \'template_import_'.$nama_menu.'\',' . PHP_EOL;
													$php 	.= add_tab($i) . '\'header\' => $arr,' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '];' . PHP_EOL;
													foreach($alias as $ka => $va) {
														$_va 	= explode('.',$va);
														$_fa = get_field($_va[0],'name');
														$is_active = false;
														foreach($_fa as $_fa) {
															if($_fa == 'is_active') $is_active = true;
														}
														if($is_active) {
															$php 	.= add_tab($i) . '$'.$ka.' = get_data(\''.$_va[0].'\',[' . PHP_EOL; $i++;
															$php 	.= add_tab($i) . '\'select\' => \'id,'.$_va[1].'\',' . PHP_EOL;
															$php 	.= add_tab($i) . '\'where\' => \'is_active = 1\'' . PHP_EOL; $i--;
															$php 	.= add_tab($i) . '])->result_array();' . PHP_EOL;
														} else {
															$php 	.= add_tab($i) . '$'.$ka.' = get_data(\''.$_va[0].'\',[' . PHP_EOL; $i++;
															$php 	.= add_tab($i) . '\'select\' => \'id,'.$_va[1].'\,' . PHP_EOL; $i--;
															$php 	.= add_tab($i) . '])->result_array();' . PHP_EOL;
														}
														$php 	.= add_tab($i) . '$config[] = [' . PHP_EOL; $i++;
														$php 	.= add_tab($i) . '\'title\' => \'data_'.str_replace('tbl_','',$_va[0]).'\',' . PHP_EOL;
														$php 	.= add_tab($i) . '\'data\' => $'.$ka.',' . PHP_EOL; $i--;
														$php 	.= add_tab($i) . '];' . PHP_EOL;
													}
													$php	.= add_tab($i) . '$this->load->library(\'simpleexcel\',$config);' . PHP_EOL;
													$php 	.= add_tab($i) . '$this->simpleexcel->export();' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= add_tab($i) . 'function import() {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . 'ini_set(\'memory_limit\', \'-1\');' . PHP_EOL;
													$php 	.= add_tab($i) . '$file = post(\'fileimport\');' . PHP_EOL;
													$_arr 	= '';
													foreach($label as $kl => $vl) {
														if($kl != 'id') {
															$_arr .= '\''.$kl.'\',';
														}
													}
													$php 	.= add_tab($i) . '$col = ['.substr($_arr,0,strlen($_arr)-1).'];' . PHP_EOL;
													$php 	.= add_tab($i) . '$this->load->library(\'simpleexcel\');' . PHP_EOL;
													$php 	.= add_tab($i) . '$this->simpleexcel->define_column($col);' . PHP_EOL;
													$php 	.= add_tab($i) . '$jml = $this->simpleexcel->read($file);' . PHP_EOL;
													$php 	.= add_tab($i) . '$c = 0;' . PHP_EOL;
													if(count($unique_v) > 0) {
														$php 	.= add_tab($i) . '$u = 0;' . PHP_EOL;
													}
													$php 	.= add_tab($i) . 'foreach($jml as $i => $k) {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . 'if($i==0) {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . 'for($j = 2; $j <= $k; $j++) {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . '$data = $this->simpleexcel->parsing($i,$j);' . PHP_EOL;
													if(count($unique_v) > 0) {
														$arr_check = [];
														foreach($unique_v as $uv) {
															$php 	.= add_tab($i) . '$check_'.$uv.' = get_data(\''.$c[4].'\',\''.$uv.'\',$data[\''.$uv.'\'])->row();' . PHP_EOL;
															$arr_check[] = 'isset($check_'.$uv.'->'.$uv.')';
														}
														$php 	.= add_tab($i) . 'if('.implode(' || ',$arr_check).') {' . PHP_EOL; $i++;
														if(count($unique_v) == 1) {
															$php .= add_tab($i) . '$id = $check_'.$unique_v[0].'->id;' . PHP_EOL;
														} else {
															$php .= add_tab($i) . '$id = 0;' . PHP_EOL;
															foreach($unique_v as $uv) {
																$php .= add_tab($i) . '$id = isset($check_'.$uv.'->id) ? $check_'.$uv.'->id : $id;' . PHP_EOL;
															}
														}
														$php 	.= add_tab($i) . '$data[\'update_at\'] = date(\'Y-m-d H:i:s\');' . PHP_EOL;
														$php 	.= add_tab($i) . '$data[\'update_by\'] = user(\'nama\');' . PHP_EOL;
														$php 	.= add_tab($i) . '$save = update_data(\''.$c[4].'\',$data,\'id\',$id);' . PHP_EOL;
														$php 	.= add_tab($i) . 'if($save) $u++;' . PHP_EOL; $i--;
														$php 	.= add_tab($i) . '} else {' . PHP_EOL; $i++;
													}
													$php 	.= add_tab($i) . '$data[\'create_at\'] = date(\'Y-m-d H:i:s\');' . PHP_EOL;
													$php 	.= add_tab($i) . '$data[\'create_by\'] = user(\'nama\');' . PHP_EOL;
													$php 	.= add_tab($i) . '$save = insert_data(\''.$c[4].'\',$data);' . PHP_EOL;
													$php 	.= add_tab($i) . 'if($save) $c++;' . PHP_EOL; $i--;
													if(count($unique_v) > 0) {
														$php 	.= add_tab($i) . '}' . PHP_EOL; $i--;
													}
													$php 	.= add_tab($i) . '}' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL;
													$php 	.= add_tab($i) . '$response = [' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . '\'status\' => \'success\',' . PHP_EOL;
													if(count($unique_v) > 0) {
														$php 	.= add_tab($i) . '\'message\' => $c.\' \'.lang(\'data_berhasil_disimpan\').\'. \'.$u.\' \'.lang(\'data_berhasil_diperbaharui\').\'.\''. PHP_EOL; $i--;
													} else {
														$php 	.= add_tab($i) . '\'message\' => $c.\' \'.lang(\'data_berhasil_disimpan\').\'.\''. PHP_EOL; $i--;
													}
													$php 	.= add_tab($i) . '];' . PHP_EOL;
													$php 	.= add_tab($i) . '@unlink($file);' . PHP_EOL;
													$php 	.= add_tab($i) . 'render($response,\'json\');' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= add_tab($i) . 'function export() {' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . 'ini_set(\'memory_limit\', \'-1\');' . PHP_EOL;
													$php 	.= add_tab($i) . '$arr = [';
													$_arr 	= '';
													foreach($label as $kl => $vl) {
														if($kl != 'id') {
															if($fields[$kl] == 'money') {
																$_arr .= '\''.$kl.'\' => \'-c'.$vl.'\',';
															} elseif($fields[$kl] == 'date' || $fields[$kl] == 'datetime') {
																$_arr .= '\''.$kl.'\' => \'-d'.$vl.'\',';
															} else {
																$_arr .= '\''.$kl.'\' => \''.$vl.'\',';
															}
															if($vl != 'Aktif') {
																$lang_field[strtolower(str_replace(' ','_',$vl))] = $vl;
															}
														}
													}
													foreach($alias as $ka => $va) {
														$_va 	= explode('.',$va);
														$_arr 	= str_replace($ka,$ka.'_'.$_va[1],$_arr);
													}
													$php 	.= substr($_arr,0,strlen($_arr)-1) . '];' . PHP_EOL;
													if(count($alias) == 0) {
														$php 	.= add_tab($i) . '$data = get_data(\''.$c[4].'\')->result_array();' . PHP_EOL;
													} else {
														$select_j	= '';
														foreach($alias as $ka => $va) {
															$_va 	= explode('.',$va);
															$select_j .= ','.$va.' AS '.$ka.'_'.$_va[1];
														}
														$php 	.= add_tab($i) . '$data = get_data(\''.$c[4].'\',[' . PHP_EOL; $i++;
														$php 	.= add_tab($i) . '\'select\' => \''.$c[4].'.*'.$select_j.'\',' . PHP_EOL;
														$php 	.= add_tab($i) . '\'join\' => [' . PHP_EOL; $i++;
														foreach($alias as $ka => $va) {
															$_va 	= explode('.',$va);
															$php 	.= add_tab($i) . '\''.$_va[0].' on '.$c[4].'.'.$ka.' = '.$_va[0].'.id type left\',' . PHP_EOL;
														}
														$i--;
														$php 	.= add_tab($i) . ']' . PHP_EOL; $i--;
														$php 	.= add_tab($i) . '])->result_array();' . PHP_EOL;
													}
													$php 	.= add_tab($i) . '$config = [' . PHP_EOL; $i++;
													$php 	.= add_tab($i) . '\'title\' => \'data_'.$nama_menu.'\',' . PHP_EOL;
													$php 	.= add_tab($i) . '\'data\' => $data,' . PHP_EOL;
													$php 	.= add_tab($i) . '\'header\' => $arr,' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '];' . PHP_EOL;
													$php	.= add_tab($i) . '$this->load->library(\'simpleexcel\',$config);' . PHP_EOL;
													$php 	.= add_tab($i) . '$this->simpleexcel->export();' . PHP_EOL; $i--;
													$php 	.= add_tab($i) . '}' . PHP_EOL . PHP_EOL;
													$php 	.= '}';
													$filename = $dir_module.$module->target.'/controllers/'.ucfirst($nama_menu).'.php';
													$handle = fopen ($filename, "wb");
													if($handle) {
														fwrite ( $handle, $php );
													}
													fclose($handle);
													$oldmask = umask(0);
													chmod($filename, 0777);
													umask($oldmask);

													$i 		= 0;
													$html 	= '<div class="content-header">' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . '<div class="main-container position-relative">' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . '<div class="header-info">' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . '<div class="content-title"><?php echo $title; ?></div>' . PHP_EOL;
													$html 	.= add_tab($i) . '<?php echo breadcrumb(); ?>' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . '</div>' . PHP_EOL;
													$html 	.= add_tab($i) . '<div class="float-right">' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . '<?php echo access_button(\'delete,active,inactive,export,import\'); ?>' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . '</div>' . PHP_EOL;
													$html 	.= add_tab($i) . '<div class="clearfix"></div>' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . '</div>' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . '</div>' . PHP_EOL;
													$html 	.= add_tab($i) . '<div class="content-body">' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . '<?php' . PHP_EOL;
													$html 	.= add_tab($i) . 'table_open(\'\',true,base_url(\''.$module->target.'/'.$nama_menu.'/data\'),\''.$c[4].'\');' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . 'thead();' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . 'tr();' . PHP_EOL; $i++;
													foreach($fields as $kf => $vf) {
														if($vf == 'primary_key') {
															$html .= add_tab($i) . 'th(\'checkbox\',\'text-center\',\'width="30" data-content="id"\');' . PHP_EOL;
														} elseif($vf == 'boolean') {
															$label_boolean = 'lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\')';
															if($label[$kf] == 'Aktif') $label_boolean .= '.\'?\'';
															$html .= add_tab($i) . 'th('.$label_boolean.',\'text-center\',\'data-content="'.$kf.'" data-type="boolean"\');' . PHP_EOL;
														} elseif($vf == 'date' || $vf == 'datetime') {
															$html .= add_tab($i) . 'th(lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\'\',\'data-content="'.$kf.'" data-type="daterange"\');' . PHP_EOL;
														} elseif($vf == 'image') {
															$html .= add_tab($i) . 'th(lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\'\',\'data-content="'.$kf.'" data-type="image"\');' . PHP_EOL;
														} elseif($vf == 'money') {
															$html .= add_tab($i) . 'th(lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\'text-right\',\'data-content="'.$kf.'" data-type="currency"\');' . PHP_EOL;
														} elseif($vf == 'select' && isset($alias[$kf])) {
															$_a = explode('.',$alias[$kf]);
															$html .= add_tab($i) . 'th(lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\'\',\'data-content="'.$_a[1].'" data-table="'.$_a[0].' '.str_replace('id_','',$kf).'"\');' . PHP_EOL;
														} else {
															$html .= add_tab($i) . 'th(lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\'\',\'data-content="'.$kf.'"\');' . PHP_EOL;
														}
													}
													$html 	.= add_tab($i) . 'th(\'&nbsp;\',\'\',\'width="30" data-content="action_button"\');' . PHP_EOL; $i -= 3;
													$html 	.= add_tab($i) . 'table_close();' . PHP_EOL;
													$html 	.= add_tab($i) . '?>' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . '</div>' . PHP_EOL;
													$html 	.= add_tab($i) . '<?php ' . PHP_EOL;
													$html 	.= add_tab($i) . 'modal_open(\'modal-form\');' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . 'modal_body();' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . 'form_open(base_url(\''.$module->target.'/'.$nama_menu.'/save\'),\'post\',\'form\');' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . 'col_init(3,9);' . PHP_EOL;
													foreach($fields as $kf => $vf) {
														if($vf == 'primary_key') {
															$html 	.= add_tab($i) . 'input(\'hidden\',\'id\',\'id\');' . PHP_EOL;
														} elseif($vf == 'boolean') {
															$label_boolean = 'lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\')';
															if($label[$kf] == 'Aktif') $label_boolean .= '.\'?\'';
															$html .= add_tab($i) . 'toggle('.$label_boolean.',\''.$kf.'\');' . PHP_EOL;
														} elseif($vf == 'text') {
															$html .= add_tab($i) . 'textarea(lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\''.$kf.'\'';
															if(isset($validation[$kf])) {
																$html .= ',\''.$validation[$kf].'\'';
															}
															$html .= ');' . PHP_EOL;
														} elseif($vf == 'int' || $vf == 'bigint') {
															$html .= add_tab($i) . 'input(\'text\',lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\''.$kf.'\'';
															if(isset($validation[$kf])) {
																$html .= ',\''.$validation[$kf].'\'';
															}
															$html .= ');' . PHP_EOL;
														} elseif($vf == 'date') {
															$html .= add_tab($i) . 'input(\'date\',lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\''.$kf.'\'';
															if(isset($validation[$kf])) {
																$html .= ',\''.$validation[$kf].'\'';
															}
															$html .= ');' . PHP_EOL;
														} elseif($vf == 'datetime') {
															$html .= add_tab($i) . 'input(\'datetime\',lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\''.$kf.'\'';
															if(isset($validation[$kf])) {
																$html .= ',\''.$validation[$kf].'\'';
															}
															$html .= ');' . PHP_EOL;
														} elseif($vf == 'money') {
															$html .= add_tab($i) . 'input(\'money\',lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\''.$kf.'\'';
															if(isset($validation[$kf])) {
																$html .= ',\''.$validation[$kf].'\'';
															}
															$html .= ');' . PHP_EOL;
														} elseif($vf == 'password') {
															$html .= add_tab($i) . 'input(\'password\',lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\''.$kf.'\'';
															if(isset($validation[$kf])) {
																$html .= ',\''.$validation[$kf].'\'';
															}
															$html .= ');' . PHP_EOL;
														}  elseif($vf == 'select' && isset($alias[$kf])) {
															$_a = explode('.',$alias[$kf]);
															$html .= add_tab($i) . 'select2(lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\''.$kf.'\'';
															if(isset($validation[$kf])) {
																$html .= ',\''.$validation[$kf].'\'';
															} else {
																$html .= ',\'\'';
															}
															$html .= ',$opt_'.$kf.',\'id\',\''.$_a[1].'\');' . PHP_EOL;
														} else {
															$html .= add_tab($i) . 'input(\'text\',lang(\''.strtolower(str_replace(' ','_',$label[$kf])).'\'),\''.$kf.'\'';
															if(isset($validation[$kf])) {
																$html .= ',\''.$validation[$kf].'\'';
															}
															$html .= ');' . PHP_EOL;
														}
													}
													$html 	.= add_tab($i) . 'form_button(lang(\'simpan\'),lang(\'batal\'));' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . 'form_close();' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . 'modal_footer();' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . 'modal_close();' . PHP_EOL;
													$html 	.= add_tab($i) . 'modal_open(\'modal-import\',lang(\'impor\'));' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . 'modal_body();' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . 'form_open(base_url(\''.$module->target.'/'.$nama_menu.'/import\'),\'post\',\'form-import\');' . PHP_EOL; $i++;
													$html 	.= add_tab($i) . 'col_init(3,9);' . PHP_EOL;
													$html 	.= add_tab($i) . 'fileupload(\'File Excel\',\'fileimport\',\'required\',\'data-accept="xls|xlsx"\');' . PHP_EOL;
													$html 	.= add_tab($i) . 'form_button(lang(\'impor\'),lang(\'batal\'));' . PHP_EOL; $i--;
													$html 	.= add_tab($i) . 'form_close();' . PHP_EOL; $i--; $i--;
													$html 	.= add_tab($i) . 'modal_close();' . PHP_EOL;
													$html 	.= add_tab($i) . '?>' . PHP_EOL;
													$filename = $dir_module.$module->target.'/views/'.$nama_menu.'/index.php';
													$handle = fopen ($filename, "wb");
													if($handle) {
														fwrite ( $handle, $html );
													}
													fclose($handle);
													$oldmask = umask(0);
													chmod($filename, 0777);
													umask($oldmask);
													$jdata = [
														'field'	=> $fields,
														'label'	=> $label,
														'validation' => $validation,
														'alias'	=> $alias
													];
													$filename = FCPATH . 'assets/json/' . $c[4] . '.json';
													$handle = fopen ($filename, "wb");
													if($handle) {
														fwrite ( $handle, json_encode($jdata, JSON_PRETTY_PRINT) );
													}													
													fclose($handle);
													$oldmask = umask(0);
													chmod($filename, 0777);
													umask($oldmask);

													$lang_json = json_encode($lang_field,JSON_PRETTY_PRINT);
													$filename = FCPATH . 'assets/lang/id/'.$module->target.'.json';
													@unlink($filename);
													$handle = fopen ($filename, "wb");
													if($handle) {
														fwrite ( $handle, $lang_json );
													}
													fclose($handle);
													$oldmask = umask(0);
													chmod($filename, 0777);
													umask($oldmask);

													$response = array(
														'status'	=> 'success',
														'message'	=> 'Menu "'.$nama_menu.'" berhasil di generate'
													);
												} else {
													$response = array(
														'status'	=> 'error',
														'message'	=> 'Tabel "'.$c[4].'" tidak ada'
													);
												}
											} else {
												$response = array(
													'status'	=> 'error',
													'message'	=> 'Perintah tidak valid'
												);
											}
										}
									}
								} else {
									$response = array(
										'status'	=> 'error',
										'message'	=> 'Menu "'.$nama_menu.'" sudah ada'
									);
								}
							} else {
								$response = array(
									'status'	=> 'error',
									'message'	=> 'Module "'.$module->target.'" belum di create'
								);
							}
						} else {
							$response = array(
								'status'	=> 'error',
								'message'	=> 'Menu "'.$nama_menu.'" tidak terdaftar'
							);
						}
					} else {
						$response = array(
							'status'	=> 'error',
							'message'	=> 'Nama menu tidak boleh kosong'
						);						
					}
				} elseif($c[1] == 'table') {
					if(isset($c[2]) && $c[2]) {
						$table 			= $c[2];
						$list_string 	= preg_split('/\r\n|[\r\n]/', $cmd);
						$field 			= array();
						foreach($list_string as $k => $l) {
							if($k > 0) {
								$d 		= explode(' ', preg_replace('!\s+!', ' ', $l));
								$f_name	= trim($d[0],' ');
								if($f_name && $f_name != 'id' && $f_name != 'create_by' && $f_name != 'create_at' && $f_name != 'update_by' && $f_name != 'update_at' && $f_name != 'is_active') {
									$field[$k]['field']	= $f_name;
									if(isset($d[1])) {
										$e = explode('_', $d[1]);
										if($e[0] == 'i' || $e[0] == 'int') {
											$field[$k]['type']		= 'int';
											$field[$k]['length']	= count($e) == 2 && is_numeric($e[1]) && $e[1] > 0 ? $e[1] : 11;
										} elseif($e[0] == 'v' || $e[0] == 'varchar') {
											$field[$k]['type']		= 'varchar';
											$field[$k]['length']	= count($e) == 2 && is_numeric($e[1]) && $e[1] > 0 ? $e[1] : 100;
										} elseif($e[0] == 't' || $e[0] == 'text') {
											$field[$k]['type']		= 'text';
											$field[$k]['length']	= '';
										} elseif($e[0] == 'bi' || $e[0] == 'bigint') {
											$field[$k]['type']		= 'bigint';
											$field[$k]['length']	= count($e) == 2 && is_numeric($e[1]) && $e[1] > 0 ? $e[1] : 20;
										} elseif($e[0] == 'b' || $e[0] == 'boolean') {
											$field[$k]['type']		= 'tinyint';
											$field[$k]['length']	= '1';
										} elseif($e[0] == 'd' || $e[0] == 'date') {
											$field[$k]['type']		= 'date';
											$field[$k]['length']	= '';
										} elseif($e[0] == 'dt' || $e[0] == 'datetime') {
											$field[$k]['type']		= 'datetime';
											$field[$k]['length']	= '';
										} else {
											$field[$k]['type']		= 'varchar';
											$field[$k]['length']	= 100;
										}
									}
								}
							}
						}
						if(count($field) > 0) {
							if(!table_exists($table)) {
								$fields['id']	= array(
									'type'				=> 'int',
									'constraint'		=> 11,
									'auto_increment' 	=> TRUE
								);
								foreach($field as $f) {
									$fields[$f['field']] = array(
										'type'				=> $f['type'],
										'constraint'		=> $f['length'],
										'null'				=> FALSE
									);
								}
								$fields['is_active'] = array(
									'type'				=> 'tinyint',
									'constraint'		=> 1,
									'null'				=> FALSE
								);
								$fields['create_at'] = array(
									'type'				=> 'datetime',
									'null'				=> FALSE
								);
								$fields['create_by'] = array(
									'type'				=> 'varchar',
									'constraint'		=> 100,
									'null'				=> FALSE
								);
								$fields['update_at'] = array(
									'type'				=> 'datetime',
									'null'				=> FALSE
								);
								$fields['update_by'] = array(
									'type'				=> 'varchar',
									'constraint'		=> 100,
									'null'				=> FALSE
								);
								$this->load->dbforge();
								$this->dbforge->add_field($fields);
								$this->dbforge->add_key('id', TRUE);
								$attributes = array('ENGINE' => 'MyISAM');
								if($this->dbforge->create_table($table, FALSE, $attributes)) {
									$response = array(
										'status'	=> 'success',
										'message'	=> 'Tabel "'.$table.'" berhasil digenerate'
									);
								} else {
									$response = array(
										'status'	=> 'failed',
										'message'	=> 'Tabel "'.$table.'" gagal digenerate'
									);
								}
							} else {
								$response = array(
									'status'	=> 'error',
									'message'	=> 'Tabel "'.$table.'" sudah ada'
								);
							}
						} else {
							$response = array(
								'status'	=> 'error',
								'message'	=> 'Field table belum di definisikan'
							);
						}
					} else {
						$response = array(
							'status'	=> 'error',
							'message'	=> 'Nama table tidak boleh kosong'
						);
					}
				}  elseif($c[1] == 'dummy') {
					$table 		= isset($c[2]) ? $c[2] : 'tbl_example'.rand(100,999);
					$looping 	= isset($c[3]) && is_numeric($c[3]) ? $c[3] : 1;

					if(table_exists($table)) {
						$list_string 		= preg_split('/\r\n|[\r\n]/', $cmd);
						$list_type 			= ['text','name','email','address','randomNumber','randomDate','randomRange','randomChoose','currentDate'];
						$jml_save 	= 0;
						for($i=0;$i<$looping;$i++) {
							$data 			= [];
							$name 			= $this->dummy('name');
							foreach($list_string as $k => $l) {
								if($k > 0) {
									$m 		= explode(' ', $l, 3);
									$field 	= $m[0];
									$type 	= isset($m[1]) ? $m[1] : '';
									$length = isset($m[2]) ? $m[2] : '';
									if($type && in_array($type, $list_type)) {
										if($type == 'text') {
											$data[$field] = $this->dummy('text',$length);
										} elseif($type == 'name') {
											$data[$field] = $name['name'];
										} elseif($type == 'email') {
											$data[$field] = $name['email'];
										} elseif($type == 'address') {
											$data[$field] = $this->dummy('address');
										} elseif($type == 'randomNumber') {
											$o 		= $length && is_numeric($length) ? $length : 1;
											$start 	= '';
											$end 	= '';
											for($j=0;$j<$o;$j++) {
												$start 	.= 1;
												$end 	.= 9;
											}
											$data[$field] = rand($start,$end);
										} elseif($type == 'randomDate') {
											$timestamp	= mt_rand(1, time());
											$data[$field] = date("Y-m-d H:i:s", $timestamp);
										} elseif($type == 'randomRange' && $length) {
											$range 		= explode('-', $length);
											$res 		= '';
											if(count($range) == 2 && is_numeric(trim($range[0])) && is_numeric(trim($range[1]))) {
												$min = trim($range[0]);
												$max = trim($range[1]);
												if($max >= $min) {
													$arr_range = [];
													for($p=$min;$p<=$max;$p++) {
														$arr_range[] = $p;
													}
													shuffle($arr_range);
													$res = $arr_range[0];
												}
											}
											$data[$field] 	= $res;
										} elseif($type == 'randomChoose' && $length) {
											$choose = explode(',', $length);
											shuffle($choose);
											$data[$field] 	= trim($choose[0]);
										} elseif($type == 'currentDate') {
											$data[$field] 	= date('Y-m-d H:i:s');
										}
									}
								}
							}
							if(count($data)) {
								$save = save_data($table,$data,[],true);
								if($save['status'] == 'success') $jml_save++;
							}
						}
						$response = array(
							'status'	=> 'success',
							'message'	=> $jml_save.' data dummy berhasil ditambahkan ke tabel '.$table
						);
					} else {
						$response = array(
							'status'	=> 'error',
							'message'	=> 'Tabel '.$table.' tidak ada'
						);
					}
				} else {
					$response = array(
						'status'	=> 'error',
						'message'	=> 'Generate '.$c[1].' tidak tersedia'
					);
				}
			} else {
				$response = array(
					'status'	=> 'error',
					'message'	=> 'Perintah tidak lengkap'
				);
			}
		} elseif($c[0] == '-a') {
			if($c[1] == 'table') {
				if(isset($c[2]) && $c[2]) {
					$table 			= $c[2];
					$list_string 	= preg_split('/\r\n|[\r\n]/', $cmd);
					$field 			= array();
					$last_field 	= get_field($table,'name');
					foreach($list_string as $k => $l) {
						if($k > 0) {
							$d 		= explode(' ', preg_replace('!\s+!', ' ', $l));
							$f_name	= trim($d[0],' ');
							if($f_name && $f_name != 'id' && $f_name != 'create_by' && $f_name != 'create_at' && $f_name != 'update_by' && $f_name != 'update_at' && $f_name != 'is_active') {
								$field[$k]['field']	= $f_name;
								if(isset($d[1])) {
									$e = explode('_', $d[1]);
									if($e[0] == 'i' || $e[0] == 'int') {
										$field[$k]['type']		= 'int';
										$field[$k]['length']	= count($e) == 2 && is_numeric($e[1]) && $e[1] > 0 ? $e[1] : 11;
									} elseif($e[0] == 'v' || $e[0] == 'varchar') {
										$field[$k]['type']		= 'varchar';
										$field[$k]['length']	= count($e) == 2 && is_numeric($e[1]) && $e[1] > 0 ? $e[1] : 100;
									} elseif($e[0] == 't' || $e[0] == 'text') {
										$field[$k]['type']		= 'text';
										$field[$k]['length']	= '';
									} elseif($e[0] == 'bi' || $e[0] == 'bigint') {
										$field[$k]['type']		= 'bigint';
										$field[$k]['length']	= count($e) == 2 && is_numeric($e[1]) && $e[1] > 0 ? $e[1] : 20;
									} elseif($e[0] == 'b' || $e[0] == 'boolean') {
										$field[$k]['type']		= 'tinyint';
										$field[$k]['length']	= '1';
									} elseif($e[0] == 'd' || $e[0] == 'date') {
										$field[$k]['type']		= 'date';
										$field[$k]['length']	= '';
									} elseif($e[0] == 'dt' || $e[0] == 'datetime') {
										$field[$k]['type']		= 'datetime';
										$field[$k]['length']	= '';
									} else {
										$field[$k]['type']		= 'varchar';
										$field[$k]['length']	= 100;
									}
								}
								foreach($last_field as $l) {
									if($l == $field[$k]['field']) {
										unset($field[$k]);
									}
								}
							}
						}
					}
					if(count($field) > 0) {
						if(table_exists($table)) {
							$after 			= '';
							foreach($last_field as $l) {
								if($l != 'is_active' && $l != 'create_at' && $l != 'create_by' && $l != 'update_at' && $l != 'update_by') {
									$after 	= $l;
								}
							}
							foreach($field as $f) {
								$fields[$f['field']] = array(
									'type'				=> $f['type'],
									'constraint'		=> $f['length'],
									'null'				=> FALSE,
									'after'				=> $after
								);
								$after 	= $f['field'];
							}
							$this->load->dbforge();
							if($this->dbforge->add_column($table,$fields)) {
								$response = array(
									'status'	=> 'success',
									'message'	=> 'Penambahan field pada Tabel "'.$table.'" berhasil'
								);
							} else {
								$response = array(
									'status'	=> 'failed',
									'message'	=> 'Penambahan field pada Tabel "'.$table.'" gagal'
								);
							}
						} else {
							$response = array(
								'status'	=> 'error',
								'message'	=> 'Tabel "'.$table.'" belum ada'
							);
						}
					} else {
						$response = array(
							'status'	=> 'error',
							'message'	=> 'Field table belum di definisikan'
						);
					}
				} else {
					$response = array(
						'status'	=> 'error',
						'message'	=> 'Nama table tidak boleh kosong'
					);
				}
			} else {
				$response = array(
					'status'	=> 'error',
					'message'	=> 'Perintah -a hanya untuk tabel saja'
				);				
			}
		} else {
			$response = array(
				'status'	=> 'error',
				'message'	=> 'Perintah tidak dikenali'
			);
		}
		render($response,'json');
	}

	function dummy($tipe='text',$length=0) {
		$dummy_text = file_get_contents(FCPATH . 'assets/json/dummy.json');
		$dummy_db	= json_decode($dummy_text,true);
		$result 	= '';
		if($tipe == 'text') {
			$length = $length && is_numeric($length) ? $length : 40;
			shuffle($dummy_db['text']);
			for($i=0;$i<$length;$i++) {
				$result .= $dummy_db['text'][$i].' ';
			}
			$result = trim($result);
		} elseif($tipe == 'name') {
			$length = $length && is_numeric($length) && $length < 4 ? $length : rand(1,3);
			$gender = ['male','female'];
			$email_provider = ['example.com','email.com','gogle.com','yaho.com'];
			shuffle($email_provider);
			shuffle($gender);
			shuffle($dummy_db[$gender[0]]);
			for($i=0;$i<$length;$i++) {
				$result .= $dummy_db[$gender[0]][$i].' ';
			}
			$delimiter = ['','_'];
			shuffle($delimiter);
			$result = trim($result);
			$nama 	= $result;
			$email	= str_replace(' ',$delimiter[0],strtolower($nama)).rand(10,999).'@'.$email_provider[0];
			$result = [
				'name'	=> $nama,
				'email'	=> $email
			];
		} elseif($tipe == 'address') {
			shuffle($dummy_db['state']);
			shuffle($dummy_db['street']);
			$result = $dummy_db['street'][0].' No. '.rand(1,300).' '.$dummy_db['state'][0]['kota'].', '.$dummy_db['state'][0]['provinsi'].' - '.rand(11111,99999);
		}
		return $result;
	}
}