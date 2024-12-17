<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['asset_paths'] = array(
	'core' 			=> 'assets/',
	'core_public'	=> 'assets/public/'
);
$config['asset_js_dir'] = 'js/';
$config['asset_css_dir'] = 'css/';
$config['asset_img_dir'] = 'img/';
$config['asset_cache_path'] = 'assets/cache/';
$config['asset_min'] = true;
$config['asset_combine'] = ENVIRONMENT == 'production' ? true : false;
$config['asset_show_files'] = false;
$config['asset_show_files_inline'] = false;
$config['asset_deps_max_depth'] = 2;
$config['asset_post_load_callback'] = null;
$config['asset_groups'] = array();
$config['asset_symlinks'] = array();