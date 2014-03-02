<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/
define('IN_PHPBB', true);
define('IN_PHPBB_MVT', true);

$level = E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED;
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$mods_root_path = $phpbb_root_path . 'mods/';
$absolute_mod_path = __DIR__ . '/mods/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/geshi/geshi.' . $phpEx);

$user->add_lang('mvt');
$picture_exts = array('gif', 'png', 'jpg', 'jpeg', 'bmp', 'svg');

$mod = request_var('mod', '');
$url = request_var('url', '');
$file = request_var('file', '');
$mode = request_var('mode', 'geshi');

switch($mode)
{
	case 'geshi':
		if(file_exists($mods_root_path . $mod . SLASH . $file) && !in_array(substr(strrchr($file, '.'), 1), $picture_exts))
		{
			$file_ext = substr(strrchr($file, '.'), 1);
			switch($file_ext)
			{
				case 'html':
				case 'htm':
					$file_ext = 'html4strict';
				break;
				
				case 'js':
					$file_ext = 'javascript';
				break;
			}
			$geshi = new GeSHi(file_get_contents($mods_root_path . $mod . SLASH . $file), $file_ext);
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

			echo $geshi->parse_code();

		}
		else if (in_array(substr(strrchr($file, '.'), 1), $picture_exts))
		{
			if(substr(strrchr($file, '.'), 1) == 'svg')
			{
				echo '<object type="image/svg+xml" data="' . $phpbb_root_path . 'mods/' . $mod . SLASH . $file . '"></object>';
			}
			else
			{
				echo '<img alt="' . $file . '" src="' . $phpbb_root_path . 'mods/' . $mod . SLASH . $file . '" />';
			}
		}
		else
		{
			echo $user->lang['MVT_NO_FILE'];
		}
	break;

	case 'syntax':
		if(file_exists($mods_root_path . $mod . SLASH . $file) && !in_array(substr(strrchr($file, '.'), 1), $picture_exts))
		{
			$file_ext = substr(strrchr($file, '.'), 1);
			if($file_ext == 'php')
			{
				$result = mvt_check_php_syntax(str_replace(SLASH, DIRECTORY_SEPARATOR, $absolute_mod_path . $mod . SLASH . $file));
				if(strpos($result, 'No syntax errors detected') === 0)
				{
					echo $user->lang['MVT_PHP_SYNTAX'] . ': <span class="success">' . $user->lang['MVT_OK'] . "</span>";
				}
				else
				{
					echo $user->lang['MVT_PHP_SYNTAX'] . ': <span class="error">' . $result . "</span>";
				}
			}
		}
	break;
	
	case 'tree_all':
		if(substr($mod, -1) == SLASH)
		{
			$mod = substr($mod, 0, -1);
		}
		$file_mapping = directory_to_array($phpbb_root_path . 'mods/' . $mod , true, true, false);
		foreach($file_mapping AS &$file_)
		{
			$file_ .= SLASH;
		}
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($file_mapping);
	break;

	case 'add_mod':
		include($phpbb_root_path . 'includes/functions_compress.' . $phpEx);
		include($phpbb_root_path . 'includes/functions_mods.' . $phpEx);
		include($phpbb_root_path . 'includes/mod_parser.' . $phpEx);

		$stream = stream_copy($url, $phpbb_root_path . 'mods/');
		if($stream)
		{	
			if(substr(strrchr($stream['filename'], '.'), 1) == 'zip')
			{
				$before_extracting = directory_to_array($phpbb_root_path . 'mods', false, true, false);
				$compress = new compress_zip('r', $phpbb_root_path . 'mods/' . $stream['filename']);
				$compress->extract($phpbb_root_path . 'mods/');
				$compress->close();
				$after_extracting = directory_to_array($phpbb_root_path . 'mods', false, true, false);
				if(file_exists($phpbb_root_path . 'mods/' . $stream['filename']))
				{
					unlink($phpbb_root_path . 'mods/' . $stream['filename']);
				}
				$xml_mapping = glob("mods/*/*.xml");
				$temp_sorting = array();
				foreach ($xml_mapping AS $key => $value)
				{
					$filename = substr(strrchr($value, SLASH), 1);
					$temp_sorting[str_replace($filename, '', $value)] = $filename;
				}
				$xml_mapping = $temp_sorting;
				$mod_dir = str_replace($phpbb_root_path, '', current(array_diff($after_extracting, $before_extracting)));
				if($mod_dir)
				{
					if(isset($xml_mapping[$mod_dir . SLASH]))
					{
						$xml_main_file = $xml_mapping[$mod_dir . SLASH];
					}
					else
					{
						$xml_main_file = 'install_mod.xml';
					}
					if(file_exists($phpbb_root_path . $mod_dir . SLASH . $xml_main_file))
					{
						$parser = new parser('xml');
						$parser->set_file($phpbb_root_path . $mod_dir . SLASH . $xml_main_file);
						$mod_details = $parser->get_details();
						$mod_name = isset($mod_details['MOD_NAME'][$user->data['user_lang']]) ? $mod_details['MOD_NAME'][$user->data['user_lang']] : current($mod_details['MOD_NAME']);
						$mod_name_versioned = "$mod_name {$mod_details['MOD_VERSION']}";

						$json = array(
							'status' => true, 
							'eval' => 'add_mod_tab("' . (strlen($mod_name_versioned) > $config['mvt_tab_str_len'] ? substr($mod_name_versioned, 0, $config['mvt_tab_str_len'] - 3) . '...' : $mod_name_versioned) . '", "' . append_sid($phpbb_root_path . 'index.' . $phpEx, array('mod' => substr(strrchr($mod_dir, '/'), 1))) . '", "' . str_replace('mods' . SLASH, '', $mod_dir) . '")'
						);
					}
					else
					{
						$json = array(
							'status' => false, 
							'eval' => 'mvt_info("' . $user->lang['MVT_INFORMATION'] . '", "' . $user->lang['MVT_NO_XML'] . '")'
						);
					}
				}
				else
				{
					$json = array(
						'status' => false, 
						'eval' => 'mvt_info("' . $user->lang['MVT_INFORMATION'] . '", "' . $user->lang['MVT_MOD_ALREADY_PRESENT'] . '")'
					);
				}
			}
			else
			{
				unlink($phpbb_root_path . 'mods/' . $stream['filename']);
				$json = array(
					'eval' => 'mvt_info("' . $user->lang['MVT_INFORMATION'] . '", "' . $user->lang('AVATAR_DISALLOWED_EXTENSION', substr(strrchr($stream['filename'], '.'), 1)) . '")',
					'status' => false,
				);
			}
		}
		else
		{
			$json = array(
				'eval' => 'mvt_info("' . $user->lang['MVT_INFORMATION'] . '", "' . $user->lang['MVT_MOD_FAILED'] . '")',
				'status' => false,
			);
		}
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($json);
	break;
	
	case 'filestats':
		if(file_exists($mods_root_path . $mod . SLASH . $file))
		{
			$stats = array();
			$stats[] = strongify("MD5: ") . md5_file($mods_root_path . $mod . SLASH . $file);
			$stats[] = strongify("SHA1: ") . sha1_file($mods_root_path . $mod . SLASH . $file);
			$stats[] = strongify($user->lang['FILESIZE'] . ': ') . get_formatted_filesize(filesize($mods_root_path . $mod . SLASH . $file));
			$stats[] = strongify($user->lang['LAST_UPDATED'] . ': ') . $user->format_date(filemtime($mods_root_path . $mod . SLASH . $file));

			echo json_encode(array(
				'eval' => 'mvt_info("' . $user->lang['MVT_INFORMATION'] . '", "' . implode('<br />', $stats) . '")',
				'status' => true,
			));
		}
		else
		{
			echo json_encode(array(
				'eval' => 'mvt_info("' . $user->lang['MVT_INFORMATION'] . '", "' . $user->lang['MVT_MOD_FAILED'] . '")',
				'status' => false,
			));
		}
	break;
	
	case 'delete_mod':
		if($mod && strpos($mod, '..') === false)
		{
			header('Content-Type: application/json; charset=UTF-8');
			if (destroy_dir($mods_root_path . $mod . SLASH))
			{
				echo json_encode(array(
						'eval' => '$("a[href=\'' . append_sid($phpbb_root_path . 'index.' . $phpEx, array('mod' => $mod)) .'\']").parent().remove(); mvt_info("' . $user->lang['MVT_INFORMATION'] . '", "' . $user->lang['MVT_MOD_DELETED'] . '", function(){(mod_to_delete == current_mod) ? location.assign(index) : false;})',
						'status' => true,
					)
				);
			}
			else
			{
				echo json_encode(array(
						'eval' => 'mvt_info("' . $user->lang['MVT_INFORMATION'] . '", "' . $user->lang('MVT_MOD_DELETE_FAILED', $mod) . '")',
						'status' => false,
					)
				);
			}
		}
	break;
}
//No garbage here
exit_handler();