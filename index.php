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

// The acp template is never stored in the database
$user->theme['template_storedb'] = false;
$user->add_lang('mvt');
$submit = !empty($_POST['submit']) ? true : false;
$mode = request_var('mode', 'validation');
$mod = $user_mod = utf8_normalize_nfc(request_var('mod', '', true));
$file = utf8_normalize_nfc(request_var('file', '', true));
$tpl_file = 'mvt_body.html';
$ignored_exts = array('gif', 'png', 'jpg', 'jpeg', 'bmp', 'svg', 'psd');
$mod_versions = $block_vars = array();
$s_current_mod_name = '';

// Check up the MOD directory status
check_mods_directory();
// Now search for MOD install files...
foreach (explode(',', BASE_INSTALL_FILE_EXT) AS $install_ext)
{
	// Variables variable are so convenient  !!
	${$install_ext . '_mapping'} = glob("mods/*/*." . $install_ext);
	$temp_sorting = array();
	if (!empty(${$install_ext . '_mapping'}))
	{
		foreach (${$install_ext . '_mapping'} AS $key => $value)
		{
			$filename = substr(strrchr($value, SLASH), 1);
			// 3.0.x hack
			if (isset($temp_sorting[str_replace($filename, '', $value)]) && strpos($temp_sorting[str_replace($filename, '', $value)], 'install') !== false )
			{
				continue;
			}
			$temp_sorting[str_replace($filename, '', $value)] = $filename;
		}
		${$install_ext . '_mapping'} = $temp_sorting;
	}
}

$template->assign_vars(array(
	// Links
	'PAGE_TITLE'	=> $user->lang['MVT_HOME'],
	'S_NO_MODS'		=> true,
	'S_TITLE'		=> '',
	'U_MODS_PATH'	=> $phpbb_root_path . 'mods/',
	'U_INDEX'		=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mode' => 'validation')),
	'U_FILE_TREE'	=> append_sid($phpbb_root_path . 'file_tree.' . $phpEx, array()),
	'U_CONFIG'		=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mode' => 'config')),
	'U_AJAX'		=> append_sid($phpbb_root_path . 'ajax.' . $phpEx, array()),
	'U_GIT_REPOSITORY' => MVT_GIT_REPOSITORY,
));

foreach (explode(',', MVT_SUPPORTED_VERSIONS) AS $supported_versions)
{
	$template->assign_block_vars('supported_versions', array(
		'REAL_BRANCH' => substr($supported_versions, 0, strpos($supported_versions, ':')),
		'SHORT_BRANCH' => substr(strrchr($supported_versions, ':'), 1),
	));
}

$dh = @opendir($phpbb_root_path . 'mods');
if ($dh)
{
	while (($mod_dir = readdir($dh)) !== false)
	{
		$vmode = ''; 
		$base_30x_file = BASE_30X_FILE; 
		$base_31x_file = BASE_31X_FILE;

		if (is_dir($phpbb_root_path .'mods/' . $mod_dir) && !in_array($mod_dir, array('.', '..')))
		{
			$mod_subfolder = directory_to_array($phpbb_root_path . 'mods/' . $mod_dir . SLASH, false, true, true);

			if (isset($xml_mapping['mods/' . $mod_dir . SLASH]))
			{
				$base_30x_file = $xml_mapping['mods/' . $mod_dir . SLASH];
			}
			else
			{
				$base_30x_file = 'install_mod.xml';
			}
			if (isset($json_mapping['mods/' . $mod_dir . SLASH]))
			{
				$base_31x_file = $json_mapping['mods/' . $mod_dir . SLASH];
			}
			else
			{
				$base_31x_file = BASE_31X_FILE;
			}

			$mod_name = $mod_dir;

			if (file_exists($phpbb_root_path .'mods/' . $mod_dir . SLASH . $base_30x_file))
			{
				$vmode = '3.0.x';
			}
			else if (file_exists($phpbb_root_path .'mods/' . $mod_dir . SLASH . $base_31x_file))
			{
				$vmode = '3.1.x';
			}
			// Not file found in the main MOD directory, try subdirectory
			if (empty($vmode))
			{
				switch (true)
				{
					case file_exists($mod_subfolder[0] . SLASH . $base_30x_file):
						$base_30x_file = substr(strrchr(str_replace('/' . $base_30x_file, '', $mod_subfolder[0] . SLASH . $base_30x_file), '/'), 1) . strrchr($mod_subfolder[0] . SLASH . $base_30x_file, '/');
						$vmode = '3.0.x';

					case file_exists($mod_subfolder[0] . SLASH . $base_31x_file):
						$base_31x_file = substr(strrchr(str_replace('/' . $base_31x_file, '', $mod_subfolder[0] . SLASH . $base_31x_file), '/'), 1) . strrchr($mod_subfolder[0] . SLASH . $base_31x_file, '/');
						$vmode = '3.1.x';
				}
			}
			if ($vmode)
			{
				switch ($vmode)
				{
					case '3.0.x':
						$parser = new parser('xml');
						$parser->set_file($phpbb_root_path .'mods/' . $mod_dir . SLASH . $base_30x_file);
						$mod_details = $parser->get_details();
						$mod_name = isset($mod_details['MOD_NAME'][$user->data['user_lang']]) ? $mod_details['MOD_NAME'][$user->data['user_lang']] : current($mod_details['MOD_NAME']);
						$mod_name_versioned = "$mod_name {$mod_details['MOD_VERSION']}";
						$mod_version = $mod_details['MOD_VERSION'];
					break;

					case '3.1.x':
						$mod_details = json_decode(file_get_contents($phpbb_root_path .'mods/' . $mod_dir . SLASH . $base_31x_file), true);
						$mod_name = $mod_details['extra']['display-name'];
						$mod_name_versioned = "$mod_name {$mod_details['version']}";
						$mod_version = $mod_details['version'];
					break;
				}

				// Set a default one if no one was selected
				if (empty($mod) && $mode == 'validation')
				{
					$mod = $mod_dir;
				}
				if (!isset($mod_versions[$mod_name]))
				{
					$block_vars[$mod_name] = array(
						'S_VMODE'	=> $vmode,
						'L_TITLE'	=> strlen($mod_name_versioned) > $config['mvt_tab_str_len'] ? utf8_substr($mod_name_versioned, 0, $config['mvt_tab_str_len'] - 3) . '...' : $mod_name_versioned,
						'U_TITLE'	=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mod' => $mod_dir)),
						'S_MOD_DIR'	=> $mod_dir,
						'S_SELECTED'=> $mod == $mod_dir ? true : false,
					);
				}

				if($user_mod == $mod_dir)
				{
					$block_vars[$mod_name] = array(
						'S_VMODE'	=> $vmode,
						'L_TITLE'	=> strlen($mod_name_versioned) > $config['mvt_tab_str_len'] ? utf8_substr($mod_name_versioned, 0, $config['mvt_tab_str_len'] - 3) . '...' : $mod_name_versioned,
						'U_TITLE'	=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mod' => $mod_dir)),
						'S_MOD_DIR'	=> $mod_dir,
						'S_SELECTED'=> $mod == $mod_dir ? true : false,
					);
				}

				// Store versions of each MODs
				$mod_versions[$mod_name][$mod_version] = array(
					'mod_name_version' => $mod_dir,
					'mod_phpbb_version' => $vmode,
				);

				if ($mod == $mod_dir)
				{
					$template->assign_vars(array(
						'S_NO_MODS'		=> false,
						'S_CURRENT_MOD_NAME'	=> $mod_name,
						'S_CURRENT_MOD' => $mod_dir
					));
					$mod_mapping = array();
					$s_current_mod_name = $mod_name;
					$s_current_mod_version = $mod_version;
					$mod_directory = directory_to_array($phpbb_root_path . 'mods/' . $mod_dir);
					foreach ($mod_directory AS $mod_directory_)
					{
						$mod_directory_ = str_replace($phpbb_root_path .'mods/', '', $mod_directory_);
						$file_ext = substr(strrchr($mod_directory_, '.'), 1);
						if (in_array($file_ext, $ignored_exts))
						{
							continue;
						}
						if (empty($file))
						{
							$file = $mod_directory_;
						}
						$template->assign_block_vars('mods_files', array(
							'FULL_FILE_PATH'	=> $mod_directory_,
							'SHORT_FILE_PATH'	=> str_replace($mod_dir, '', $mod_directory_),
							'U_TITLE'			=> append_sid($phpbb_root_path . 'index.' . $phpEx, array('mod' => $mod_dir, 'file' => $mod_directory_)),
							'S_SELECTED'		=> $file == $mod_directory_ ? true : false,
						));
						// We automatically handle $base_3xx_file if present.
						if ((strpos($mod_directory_, $base_30x_file) || strpos($mod_directory_, $base_31x_file))|| $file == $mod_directory_)
						{
							switch ($file_ext)
							{
								case 'html':
								case 'htm':
									$file_ext = 'html4strict';
								break;

								case 'yml':
									$file_ext = 'yaml';
								break;

								case 'js':
								case 'json':
									$file_ext = 'javascript';
								break;

								default:
									$file_ext = substr(strrchr($mod_directory_, '.'), 1);
								break;
							}
							$s_current_file = str_replace($mod_dir . SLASH, '', $mod_directory_);
							$template->assign_vars(array(
								'S_CURRENT_FILE' => str_replace($mod_dir . SLASH, '', $mod_directory_),
								'S_CURRENT_REAL_MOD' => $mod_name,
								'S_CURRENT_MOD_VERSION' => $mod_version,
								'S_BREADCRUMB'	=> $user->lang['MVT'] . ' » ' . $mod_name . ' » ' . $s_current_file,
								'S_CURRENT_FILE_EXT' => substr(strrchr(str_replace($mod_dir . SLASH, '', $mod_directory_), '.'), 1),
							));
						}
					}
				}
			}
		}
	}

	foreach($block_vars AS $block_vars_)
	{
		$template->assign_block_vars('mods_blocks', $block_vars_);
	}

	// Display only the selector if we have 2 versions at least !
	if (isset($mod_versions[$s_current_mod_name]) && sizeof($mod_versions[$s_current_mod_name]) > 1)
	{
		foreach(($mod_versions[$s_current_mod_name]) AS $version_id => $mod_versions_)
		{
			$template->assign_block_vars('version_selector', array(
				'VERSION_ID' => $version_id,
				'VERSION_DIR' => $mod_versions_['mod_name_version'],
				'PHPBB_VERSION' => $mod_versions_['mod_phpbb_version'],
				'S_SELECTED' => ($s_current_mod_version == $version_id) ? true : false,
			));
		}
	}
	closedir($dh);
	// Error: No valid MOD selected, back to the index.
	if (empty($s_current_file) && $mode == 'validation' && !isset($mod_name))
	{
		trigger_error('MVT_NO_MODS');
	}
	if (empty($s_current_file) && $mode == 'validation')
	{
		trigger_error('MVT_NO_MOD');
	}
}
if ($mode == 'config')
{
	$template->assign_vars(array(
		'S_IN_CONFIG'	=> true,
		'PAGE_TITLE'	=> $user->lang['MVT_SETTINGS'],
	));
	// Get current and latest version
	$errstr = '';
	$errno = 0;
	if ($config['mvt_version_check'] < time())
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

	if ($submit)
	{
		$settings = array (
			'mvt_lang' => request_var('mvt_lang', ''),
		);
		if(!MVT_DEMO_MODE)
		{
			$settings += array (
				'mvt_php_syntax' => request_var('mvt_php_syntax', 1),
				'mvt_php_binary_path' => request_var('mvt_php_binary_path', ''),
				'mvt_new_tab' => request_var('mvt_lang', 1),
				'mvt_search_engine' => request_var('mvt_search_engine', ''),
				'mvt_search_engine_url' => request_var('mvt_search_engine_url', ''),
				'mvt_search_engine_img' => request_var('mvt_search_engine_img', ''),
				'mvt_tab_str_len' => request_var('mvt_tab_str_len', 25),
				'mvt_exit_handler' => request_var('mvt_exit_handler', 1),
			);
		}
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
	foreach ($user->languages AS $iso => $lang)
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
	'S_DEMO_MODE' => MVT_DEMO_MODE,
));

$template->set_filenames(array(
	'index'	=> 'mvt_body.html',
));

set_default_template_vars();
$template->display('index');