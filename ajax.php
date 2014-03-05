<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/
define('IN_PHPBB', true);
define('IN_PHPBB_MVT', true);
ini_set('auto_detect_line_endings', true);

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

				case 'yml':
					$file_ext = 'yaml';
				break;

				case 'js':
				case 'json':
					$file_ext = 'javascript';
				break;
			}
			$geshi = new GeSHi(file_get_contents($mods_root_path . $mod . SLASH . $file), $file_ext);
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

			echo preg_replace("#(\\t)#siU", '<s class="tab">\\1</s>', $geshi->parse_code());

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
					$vmode = ''; 
					$base_30x_file = BASE_30X_FILE; 
					$base_31x_file = BASE_31X_FILE;

					$mod_subfolder = directory_to_array($phpbb_root_path . 'mods/' . $mod_dir . SLASH, false, true, true);

					if(isset($xml_mapping[$mod_dir . SLASH]))
					{
						$base_30x_file = $xml_mapping[$mod_dir . SLASH];
					}
					else
					{
						$base_30x_file = 'install_mod.xml';
					}
					if(file_exists($phpbb_root_path . $mod_dir . SLASH . $base_30x_file))
					{
						$vmode = '3.0.x';
					}
					else if (file_exists($phpbb_root_path . $mod_dir . SLASH . $base_31x_file))
					{
						$vmode = '3.1.x';
					}
					//Not file found in the main directory, try second-level directory
					if(empty($vmode))
					{
						switch(true)
						{
							case file_exists($mod_subfolder[0] . SLASH . $base_30x_file):
								$base_30x_file = substr(strrchr(str_replace('/' . $base_30x_file, '', $mod_subfolder[0] . SLASH . $base_30x_file), '/'), 1) . strrchr($mod_subfolder[0] . SLASH . $base_30x_file, '/');
								$vmode = '3.0.x';

							case file_exists($mod_subfolder[0] . SLASH . $base_31x_file):
								$base_31x_file = substr(strrchr(str_replace('/' . $base_31x_file, '', $mod_subfolder[0] . SLASH . $base_31x_file), '/'), 1) . strrchr($mod_subfolder[0] . SLASH . $base_31x_file, '/');
								$vmode = '3.1.x';
						}
					}

					if($vmode)
					{
/*						$parser = new parser('xml');
						$parser->set_file($phpbb_root_path . $mod_dir . SLASH . $base_30x_file);
						$mod_details = $parser->get_details();
						$mod_name = isset($mod_details['MOD_NAME'][$user->data['user_lang']]) ? $mod_details['MOD_NAME'][$user->data['user_lang']] : current($mod_details['MOD_NAME']);
						$mod_name_versioned = "$mod_name {$mod_details['MOD_VERSION']}"; */
						switch($vmode)
						{
							case '3.0.x':
								$parser = new parser('xml');
								$parser->set_file($phpbb_root_path . $mod_dir . SLASH . $base_30x_file);
								$mod_details = $parser->get_details();
								$mod_name = isset($mod_details['MOD_NAME'][$user->data['user_lang']]) ? $mod_details['MOD_NAME'][$user->data['user_lang']] : current($mod_details['MOD_NAME']);
								$mod_name_versioned = "$mod_name {$mod_details['MOD_VERSION']}";
							break;

							case '3.1.x':
								$mod_details = json_decode(file_get_contents($phpbb_root_path . $mod_dir . SLASH . $base_31x_file), true);
								$mod_name = $mod_details['extra']['display-name'];
								$mod_name_versioned = "$mod_name {$mod_details['version']}";
							break;
						}
						

						$json = array(
							'status' => true, 
							'eval' => 'add_mod_tab("' . (strlen($mod_name_versioned) > $config['mvt_tab_str_len'] ? substr($mod_name_versioned, 0, $config['mvt_tab_str_len'] - 3) . '...' : $mod_name_versioned) . '", "' . append_sid($phpbb_root_path . 'index.' . $phpEx, array('mod' => substr(strrchr($mod_dir, '/'), 1))) . '", "' . str_replace('mods' . SLASH, '', $mod_dir) . '", "' . $vmode . '")'
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

	case 'file_encoding':
		if($mod && $file && file_exists($mods_root_path . $mod . SLASH . $file))
		{
			$filename = $mods_root_path . $mod . SLASH . $file;
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			if(function_exists('mb_detect_encoding'))
			{
				echo json_encode(array(
						'eval' => "file_encoding('{$mod}', '{$file}', '" . mvt_detect_encoding($contents, '') . "')",
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

	case 'file_eol':
			$filename = $mods_root_path . $mod . SLASH . $file;
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			echo json_encode(array(
					'eval' => "file_eol('{$mod}', '{$file}', '" . detect_eol($contents) . "')",
					'status' => true,
				)
			);
	break;
}

/**
 * Detects the charset of a string.
 * @param string $str The string to check.
  * @param string $def_charset The default charset to return if encoding not detected
 */
function mvt_detect_encoding($str, $def_charset)
{
	global $user;

	$encoding = mb_detect_encoding($str, $def_charset);
	if(stripos($encoding, 'UTF-8') !== false)
	{
		$utf8_bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
		$first3 = substr($str, 0, 3);
		if ($first3 == $utf8_bom)
		{
			return $encoding;
		}
		else
		{
			return "UTF-8 ({$user->lang['MVT_NO_BOM']})";
		}
	}
	else
	{
		return $encoding;
	}
}

/**
 * Detects the end-of-line character of a string.
 * @param string $str The string to check.
 */
function detect_eol($str)
{
	$cr = "\r";	// Carriage Return: Mac
	$lf = "\n";	// Line Feed: Unix
	$crlf = "\r\n";	// Carriage Return and Line Feed: Windows
	if(strpos($str, $crlf) !== false)
	{
		return 'Dos/Windows (CR+LF)';
	}
	else if(strpos($str, $lf) !== false)
	{
		return 'UNIX (LF)';
	}
	else if(strpos($str, $cr) !== false)
	{
		return 'MAC (CR)';
	}
}
//No garbage here
exit_handler();