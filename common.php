<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/

require($phpbb_root_path . 'includes/startup.' . $phpEx);
require($phpbb_root_path . 'includes/functions.' . $phpEx);
require($phpbb_root_path . 'includes/constants_mvt.' . $phpEx);
require($phpbb_root_path . 'includes/functions_mvt.' . $phpEx);
require($phpbb_root_path . 'includes/template.' . $phpEx);
require($phpbb_root_path . 'includes/functions_template.' . $phpEx);
require($phpbb_root_path . 'includes/user.' . $phpEx);
require($phpbb_root_path . 'includes/utf/utf_tools.' . $phpEx);

// Set PHP error handler to ours
set_error_handler(defined('PHPBB_MSG_HANDLER') ? PHPBB_MSG_HANDLER : 'mvt_msg_handler');

//Check up the cache directory
check_cache_directory();

$config = mvt_get_config();
//In case the config file has corrupted for an unknown reason, we keep the hardcoded version of the MVT config 
$config['load_tplcompile']			= isset($config['load_tplcompile'])			? $config['load_tplcompile']		: true;
$config['tpl_allow_php']			= isset($config['tpl_allow_php'])			? $config['tpl_allow_php']			: false;
$config['mvt_lang']					= isset($config['mvt_lang'])				? $config['mvt_lang']				: 'en';
$config['mvt_version_check']		= isset($config['mvt_version_check'])		? $config['mvt_version_check']		: '';
$config['mvt_php_syntax']			= isset($config['mvt_php_syntax'])			? $config['mvt_php_syntax']			: true;
$config['mvt_php_binary_path']		= isset($config['mvt_php_binary_path'])		? $config['mvt_php_binary_path']	: 'php';
$config['mvt_new_tab']				= isset($config['mvt_new_tab'])				? $config['mvt_new_tab']			: false;
$config['mvt_search_engine']		= isset($config['mvt_search_engine'])		? $config['mvt_search_engine']		: 'Github';
$config['mvt_search_engine_img']	= isset($config['mvt_search_engine_img'])	? $config['mvt_search_engine_img']	: 'search_engine.png';
$config['mvt_search_engine_url']	= isset($config['mvt_search_engine_url'])	? $config['mvt_search_engine_url']	: 'https://github.com/search?type=Code&q=%CODE%';
$config['mvt_tab_str_len']			= isset($config['mvt_tab_str_len'])			? $config['mvt_tab_str_len']		: 25;
$config['mvt_exit_handler']			= isset($config['mvt_exit_handler'])		? $config['mvt_exit_handler']		: true;

//Only the first one is important, other will be auto-set automatically
$config['force_server_vars']		= isset($config['force_server_vars'])		? $config['force_server_vars']		: false;
$config['server_protocol']			= isset($config['server_protocol'])			? $config['server_protocol']		: false;
$config['cookie_secure']			= isset($config['cookie_secure'])			? $config['cookie_secure']			: false;
$config['server_name']				= isset($config['server_name'])				? $config['server_name']			: false;
$config['server_port']				= isset($config['server_port'])				? $config['server_port']			: false;
$config['script_path']				= isset($config['script_path'])				? $config['script_path']			: false;
$config['cookie_secure']			= isset($config['cookie_secure'])			? $config['cookie_secure']			: false;

$directories = array('cache' => 0600, 'mods' => 0700);
//Check directories
foreach($directories AS $directory => $dir_chmod)
{
	if(!is_dir($phpbb_root_path . $directory))
	{
		mkdir($phpbb_root_path . $directory, $dir_chmod);
		$fp = fopen($phpbb_root_path . $directory . "index.htm", "a+b");
		fwrite($fp, '');
		fclose($fp);
	}
}
//Global routines
$template = new template();
$template->set_custom_template($phpbb_root_path . 'style', 'mvt');

$user = new user();
$_SID = $_EXTRA_URL = '';