<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/

define('IN_PHPBB', true);
define('IN_PHPBB_MVT', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_mods.' . $phpEx);
include($phpbb_root_path . 'includes/mod_parser.' . $phpEx);
include($phpbb_root_path . 'includes/geshi/geshi.' . $phpEx);

$template->set_custom_template($phpbb_root_path . 'style', 'mvt');
$template->assign_var('T_TEMPLATE_PATH', $phpbb_root_path . 'style');
// the acp template is never stored in the database
$user->theme['template_storedb'] = false;
$user->add_lang('mvt');
$submit = !empty($_POST['submit']) ? true : false;
$mode = request_var('mode', 'validation');
$mod = request_var('mod', '');
$file = request_var('file', '');
$tpl_file = 'mvt_body.html';
$ignored_exts = array('gif', 'png', 'jpg', 'jpeg', 'bmp', 'svg', 'psd');

//Check up the MOD directory status
check_mods_directory();

// Now search for mods...
$xml_mapping = glob("mods/*/*.xml");
$temp_sorting = array();
foreach ($xml_mapping AS $key => $value)
{
	$filename = substr(strrchr($value, SLASH), 1);
	$temp_sorting[str_replace($filename, '', $value)] = $filename;
}
$xml_mapping = $temp_sorting;

$template->assign_vars(array(
	//Links
	'S_NO_MODS'		=> true,
	'S_TITLE'		=> '',
	'U_MODS_PATH'	=> $phpbb_root_path . 'mods/',
	'U_INDEX'		=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mode' => 'validation')),
	'U_FILE_TREE'	=> append_sid($phpbb_root_path . 'file_tree.' . $phpEx, array()),
	'U_CONFIG'		=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mode' => 'config')),
	'U_AJAX'		=> append_sid($phpbb_root_path . 'ajax.' . $phpEx, array()),
));

$dh = @opendir($phpbb_root_path . 'mods');
if ($dh)
{
	while (($mod_dir = readdir($dh)) !== false)
	{
		if (is_dir($phpbb_root_path .'mods/' . $mod_dir) && !in_array($mod_dir, array('.', '..')))
		{
			if(isset($xml_mapping['mods/' . $mod_dir . SLASH]))
			{
				$xml_main_file = $xml_mapping['mods/' . $mod_dir . SLASH];
			}
			else
			{
				$xml_main_file = 'install_mod.xml';
			}
			$mod_name = $mod_dir;
			if(file_exists($phpbb_root_path .'mods/' . $mod_dir . SLASH . $xml_main_file))
			{
				//Set a default one if no one was selected
				if(empty($mod) && $mode == 'validation')
				{
					$mod = $mod_dir;
				}
				$parser = new parser('xml');
				$parser->set_file($phpbb_root_path .'mods/' . $mod_dir . SLASH . $xml_main_file);
				$mod_details = $parser->get_details();
				$mod_name = isset($mod_details['MOD_NAME'][$user->data['user_lang']]) ? $mod_details['MOD_NAME'][$user->data['user_lang']] : current($mod_details['MOD_NAME']);
				$mod_name_versioned = "$mod_name {$mod_details['MOD_VERSION']}";

				$template->assign_block_vars('mods_blocks', array(
					'L_TITLE'	=> strlen($mod_name_versioned) > $config['mvt_tab_str_len'] ? substr($mod_name_versioned, 0, $config['mvt_tab_str_len'] - 3) . '...' : $mod_name_versioned,
					'U_TITLE'	=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mod' => $mod_dir)),
					'S_MOD_DIR'	=> $mod_dir,
					'S_SELECTED'=> $mod == $mod_dir ? true : false,
				));
				if($mod == $mod_dir)
				{
					$template->assign_vars(array(
						'S_NO_MODS'		=> false,
						'S_CURRENT_MOD_NAME'	=> $mod_name,
						'S_CURRENT_MOD' => $mod_dir
					));
					$mod_mapping = array();
					$mod_directory = directory_to_array($phpbb_root_path . 'mods/' . $mod_dir);
					foreach($mod_directory AS $mod_directory_)
					{
						$mod_directory_ = str_replace($phpbb_root_path .'mods/', '', $mod_directory_);
						$file_ext = substr(strrchr($mod_directory_, '.'), 1);
						if(in_array($file_ext, $ignored_exts))
						{
							continue;
						}
						if(empty($file))
						{
							$file = $mod_directory_;
						}
						$template->assign_block_vars('mods_files', array(
							'FULL_FILE_PATH'	=> $mod_directory_,
							'SHORT_FILE_PATH'	=> str_replace($mod_dir, '', $mod_directory_),
							'U_TITLE'			=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mod' => $mod_dir, 'file' => $mod_directory_)),
							'S_SELECTED'		=> $file == $mod_directory_ ? true : false,
						));
						//We automatically handle $xml_main_file if present.
						if(strpos($mod_directory_, $xml_main_file) || $file == $mod_directory_)
						{
							switch($file_ext)
							{
								case 'html':
								case 'htm':
									$file_ext = 'html4strict';
								break;

								case 'js':
									$file_ext = 'javascript';
								break;

								default:
									$file_ext = substr(strrchr($mod_directory_, '.'), 1);
								break;
							}
							$geshi = new GeSHi(file_get_contents($phpbb_root_path .'mods/' . $mod_directory_), $file_ext);
							$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
							$template->assign_vars(array(
								'S_FILE_CODE_CONTENT'	=> preg_replace("#(\\t)#siU", '<s class="tab">\\1</s>', $geshi->parse_code()),
								'S_CURRENT_FILE' => str_replace($mod_dir . SLASH, '', $mod_directory_),
								'S_CURRENT_FILE_EXT' => substr(strrchr(str_replace($mod_dir . SLASH, '', $mod_directory_), '.'), 1),
							));
						}
					}
				}
			}
		}
	}
	closedir($dh);
	//Error: No valid MOD selected, back to the index.
	if(empty($geshi) && $mode == 'validation' && !isset($mod_name))
	{
		trigger_error('MVT_NO_MODS');
	}
	if(empty($geshi) && $mode == 'validation')
	{
		trigger_error('MVT_NO_MOD');
	}
}
if($mode == 'config')
{
	$template->assign_var('S_IN_CONFIG', true);
	// Get current and latest version
	$errstr = '';
	$errno = 0;
	if($config['mvt_version_check'] < time())
	{
		$info = get_remote_file('gl4.fr', '', 'mvt.txt', $errstr, $errno);
		if ($info !== false)
		{
			//Cache it for 24 hours
			mvt_set_config('mvt_version_check', time() + 86400);
		}
	}
	else
	{
		$info = array(MVT_VERSION, '');
	}

	if ($info === false)
	{
		$template->assign_var('MVT_VERSION_ERROR_MSG', $user->lang('MVT_VERSION_ERROR', $errstr));
	}
	else
	{
		$template->assign_vars(array(
			'S_LATEST_VERSION' => $info[0],
			'S_LATEST_UPDATE' => $info[1],
			'S_UP_TO_DATE' => phpbb_version_compare(MVT_VERSION, $info[0], '<') ? false : true,
			'S_UPDATE_MSG' => $user->lang('MVT_LATEST_VERSION', $info[0], $info[1])
		));
	}

	if($submit)
	{
		$settings = array (
			'mvt_php_syntax' => request_var('mvt_php_syntax', 1),
			'mvt_php_binary_path' => request_var('mvt_php_binary_path', ''),
			'mvt_lang' => request_var('mvt_lang', ''),
			'mvt_new_tab' => request_var('mvt_lang', 1),
			'mvt_search_engine' => request_var('mvt_search_engine', ''),
			'mvt_search_engine_url' => request_var('mvt_search_engine_url', ''),
			'mvt_search_engine_img' => request_var('mvt_search_engine_img', ''),
			'mvt_tab_str_len' => request_var('mvt_tab_str_len', 25),
			'mvt_exit_handler' => request_var('mvt_exit_handler', 1),
		);
		foreach ($settings as $config_name => $config_value)
		{
			if (!isset($config[$config_name]) || $config_value != $config[$config_name])
			{
				mvt_set_config($config_name, $config_value);
			}
		}
		trigger_error($user->lang['MVT_SETTINGS_SAVED'] . mvt_back_link(append_sid($phpbb_root_path . 'index.' . $phpEx, array('mode' => 'config'))));
	}
	$language = '';
	foreach($user->languages AS $iso => $lang)
	{
		$language .= '<option value="' . $iso . '"' . (($config['mvt_lang'] == $iso) ? ' selected="selected"' : '' ) . '>' . $lang . '</option>';
	}
	$template->assign_var('S_CFG_LANG', $language);
}

$template->assign_vars(array(
	//Config
	'S_CFG_VERSION' => MVT_VERSION,
	'S_CFG_SYNTAX' => $config['mvt_php_syntax'],
	'S_CFG_PHP_BINARY_PATH' => $config['mvt_php_binary_path'],
	'S_CFG_NEW_TAB' => $config['mvt_new_tab'],
	'S_CFG_SEARCH_ENGINE' => $config['mvt_search_engine'],
	'S_CFG_SEARCH_ENGINE_IMG' => $config['mvt_search_engine_img'],
	'S_CFG_SEARCH_ENGINE_URL' => $config['mvt_search_engine_url'],
	'S_CFG_TAB_STR_LEN' => $config['mvt_tab_str_len'],
	'S_CFG_EXIT_HANDLER' => $config['mvt_exit_handler'],

	//Misc
	'L_MVT_SEARCH_ENGINE' => $user->lang('MVT_SEARCH_ENGINE', $config['mvt_search_engine']),
	'PAGE_TITLE'	=> $user->lang['MVT_HOME'],
));

$template->set_filenames(array(
	'index'	=> 'mvt_body.html',
));
set_default_template_vars();
$template->display('index');

exit;//For now