<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', TRUE);
ini_set('memory_limit', "64M");
date_default_timezone_set ("Europe/Ljubljana");

$config = array(
	'url'					=> 'http://www.domain.com',
	'db_dsn' 				=> 'mysqli://user:pass@localhost/name',
	'db_charset'			=> 'utf8',
	'db_prefix'				=> '',
	'db_systables_prefix'	=> '_',
	'db_display_errors' 	=> false,
	'php_display_errors'	=> false,
	'compact_css'			=> true,
	'compact_js'			=> true,
	'def_img_dir'			=> 'media/dsg/',
	'max_img_size'			=> 4194304,
	'image_jpeg_quality'	=> 80,
	'image_png_quality'		=> 8,
	'image_resize_method'	=> 'im',	//	im or gd
 	'imagemagick_path'		=> '/usr/local/bin',
	'img_cache_dir'			=> 'media/cache/',
	'translation_table'		=> '_translation',
	'default_page'			=> array('id' => 1),
	'url_space'				=> '-',
	'use_ul_pagination'		=> true,
	'bootstrap_version'		=> 3,
	'admin_mail'			=> 'admin@domain.com',

	'mail_charset'			=> 'utf-8',
	'force_url_lang'		=> false,

	'wireframe_dir'			=> 'wireframes/',
	'template_dir'			=> 'templates/',
	'module_dir'			=> 'modules/',
	'css_dir'				=> 'media/css/',
	'js_dir'				=> 'media/js/',

	'cookie_expire'			=> 31536000,
	'cookie_domain'			=> '.domain.com',

	'memcache_timeout'		=> 0,
	'memcache_servers'		=> array(
// 								array('127.0.0.1'=>'11211')
							),
	'ignore_home'			=> true,

//	dwoo
	'template_cache_time'	=> 0,
	'template_compile_dir'	=> 'templates/compiled/',
	'template_cache_dir'	=> 'templates/cache/',

	'default_css'			=> array (
									'bootstrap.min.css',
									'menu.css',
									'base.css'
							),
	'default_js'			=> array (
									'bootstrap.min.js',
									'plugins.jquery/jquery.history.js',
									'core.js'
							),
	'packages'				=> array (
								'slick'		=> array (
										'js'	=> array ('plugins.jquery/slick.min.js'),
										'css'	=> array ('slick.css')
								),
							)
);


define ('ORDER_STATUS_COMPLETE', 	2);
define ('ORDER_STATUS_NEW', 		1);

//	Form constants
define ('FH_DEFAULT_ROW_MASK', '<div class="form-group"><label for="%name%" class="col-md-4 control-label">%title%</label><div class="col-md-8">%field%%help% %error%</div></div>' . "\n");
define ('FH_USE_TABLE', false);
define ('FH_SET_FOCUS', false);
define ('FH_DEFAULT_LANGUAGE', 'en');
define ('FH_AUTO_DETECT_LANGUAGE', false);
define ('FH_DEFAULT_DISABLE_SUBMIT_BTN', false);
define ('FH_HELP_MASK', '<span class="help-block">%helptext%</span>');
define ('FH_ERROR_MASK', '<span class="help-inline error" id="error_%s">%s</span>');
define ('FH_USE_OVERLIB', false);
