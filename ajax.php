<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/
define('IN_PHPBB', true);
define('IN_PHPBB_MVT', true);
@ini_set('auto_detect_line_endings', true);

$level = E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED;
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$mods_root_path = $phpbb_root_path . 'mods/';
$absolute_mod_path = __DIR__ . '/mods/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/geshi/geshi.' . $phpEx);

$user->add_lang('mvt');
$picture_exts = array('gif', 'png', 'jpg', 'jpeg', 'bmp', 'svg', 'swf');

$mod = utf8_normalize_nfc(request_var('mod', '', true));
$url = utf8_normalize_nfc(request_var('url', '', true));
$file = utf8_normalize_nfc(request_var('file', '', true));
$mode = request_var('mode', 'geshi');

switch ($mode)
{
	case 'geshi':
		if (file_exists($mods_root_path . $mod . SLASH . $file) && !in_array(substr(strrchr($file, '.'), 1), $picture_exts))
		{
			$file_ext = substr(strrchr($file, '.'), 1);
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
			}
			$uid = bertix_id();
			$content = file_get_contents($mods_root_path . $mod . SLASH . $file);
			$content = str_replace(' ', "SPC{$uid}", $content);
			$geshi = new GeSHi($content, $file_ext);
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
			$content = $geshi->parse_code();
			$content = str_replace("SPC{$uid}", '<s class="spc"> </s>', $content);
			echo preg_replace("#(\\t)#siU", '<s class="tab">\\1</s>', $content);

		}
		else if (in_array(substr(strrchr($file, '.'), 1), $picture_exts))
		{
			if (substr(strrchr($file, '.'), 1) == 'svg')
			{
				echo '<object type="image/svg+xml" data="' . "{$phpbb_root_path}file_reader.{$phpEx}?f=mods/" . $mod . SLASH . $file . '"></object>';
			}
			else if (substr(strrchr($file, '.'), 1) == 'swf')
			{
				echo '<object type="application/x-shockwave-flash" data="' . "{$phpbb_root_path}file_reader.{$phpEx}?f=mods/" . $mod . SLASH . $file . '">
					<param name="movie" value="' . "{$phpbb_root_path}file_reader.{$phpEx}?f=mods/" . $mod . SLASH . $file . '">
					<param name="loop" value="' . "{$phpbb_root_path}file_reader.{$phpEx}?f=mods/" . $mod . SLASH . $file . '">
					alt : <a href="' . "{$phpbb_root_path}file_reader.{$phpEx}?f=mods/" . $mod . SLASH . $file . '">test.swf</a>
				</object>';
			}
			else
			{
				echo '<img alt="' . $file . '" src="' . "{$phpbb_root_path}file_reader.{$phpEx}?f=mods/" . $mod . SLASH . $file . '" />';
			}
		}
		else
		{
			echo $user->lang['MVT_NO_FILE'];
		}
	break;

	case 'syntax':
		if (file_exists($mods_root_path . $mod . SLASH . $file) && !in_array(substr(strrchr($file, '.'), 1), $picture_exts))
		{
			$file_ext = substr(strrchr($file, '.'), 1);
			if ($file_ext == 'php')
			{
				$result = mvt_check_php_syntax(str_replace(SLASH, DIRECTORY_SEPARATOR, $absolute_mod_path . $mod . SLASH . $file));
				if (strpos($result, 'No syntax errors detected') === 0)
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
	
	case 'compare':
		//File to compare to
		$mod_to = utf8_normalize_nfc(request_var('mod_to', '', true));
		$file_to = utf8_normalize_nfc(request_var('file_to', '', true));
		$render_mode = request_var('render_mode', '');

		include($phpbb_root_path . 'includes/diff/diff.' . $phpEx);
		include($phpbb_root_path . 'includes/diff/engine.' . $phpEx);
		include($phpbb_root_path . 'includes/diff/renderer.' . $phpEx);

		if(file_exists($mods_root_path . $mod . SLASH . $file) && file_exists($mods_root_path . $mod_to . SLASH . $file_to))
		{
			$from_text = file_get_contents($mods_root_path . $mod . SLASH . $file);
			$to_text = file_get_contents($mods_root_path . $mod_to . SLASH . $file_to);
			$preserbe_cr = true;

			// Now the correct renderer
			$render_class = 'diff_renderer_side_by_side';//inline,unified,side_by_side,raw
			$diff = new diff($from_text, $to_text, $preserbe_cr);
			$renderer = new $render_class();
			//$renderer->get_diff_content($diff)
		}
	break;

	case 'tree_all':
		if (substr($mod, -1) == SLASH)
		{
			$mod = substr($mod, 0, -1);
		}
		$file_mapping = directory_to_array($phpbb_root_path . 'mods/' . $mod , true, true, false);
		foreach ($file_mapping AS &$file_)
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
		if ($stream)
		{	
			if (substr(strrchr($stream['filename'], '.'), 1) == 'zip')
			{
				$before_extracting = directory_to_array($phpbb_root_path . 'mods', false, true, false);
				$compress = new compress_zip('r', $phpbb_root_path . 'mods/' . $stream['filename']);
				$compress->extract($phpbb_root_path . 'mods/');
				$compress->close();
				$after_extracting = directory_to_array($phpbb_root_path . 'mods', false, true, false);
				if (file_exists($phpbb_root_path . 'mods/' . $stream['filename']))
				{
					unlink($phpbb_root_path . 'mods/' . $stream['filename']);
				}

				// Now search for MOD install files...
				foreach (explode(',', BASE_INSTALL_FILE_EXT) AS $install_ext)
				{

					//Variables variable are so convenient  !!
					${$install_ext . '_mapping'} = glob("mods/*/*." . $install_ext);
					$temp_sorting = array();
					foreach (${$install_ext . '_mapping'} AS $key => $value)
					{
						$filename = substr(strrchr($value, SLASH), 1);
						$temp_sorting[str_replace($filename, '', $value)] = $filename;
					}
					${$install_ext . '_mapping'} = $temp_sorting;
				}

				$mod_dir = str_replace($phpbb_root_path, '', current(array_diff($after_extracting, $before_extracting)));
				if ($mod_dir)
				{
					$vmode = ''; 
					$base_30x_file = BASE_30X_FILE; 
					$base_31x_file = BASE_31X_FILE;

					$mod_subfolder = directory_to_array($mod_dir . SLASH, false, true, true);

					if (isset($xml_mapping[$mod_dir . SLASH]))
					{
						$base_30x_file = $xml_mapping[$mod_dir . SLASH];
					}
					else
					{
						$base_30x_file = 'install_mod.xml';
					}
					if (isset($json_mapping[$mod_dir . SLASH]))
					{
						$base_31x_file = $json_mapping[$mod_dir . SLASH];
					}
					else
					{
						$base_31x_file = BASE_31X_FILE;
					}
					if (file_exists($phpbb_root_path . $mod_dir . SLASH . $base_30x_file))
					{
						$vmode = '3.0.x';
					}
					else if (file_exists($phpbb_root_path . $mod_dir . SLASH . $base_31x_file))
					{
						$vmode = '3.1.x';
					}

					//Not file found in the main MOD directory, try subdirectory
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
								$parser->set_file($phpbb_root_path . $mod_dir . SLASH . $base_30x_file);
								$mod_details = $parser->get_details();
								$mod_name = isset($mod_details['MOD_NAME'][$user->data['user_lang']]) ? $mod_details['MOD_NAME'][$user->data['user_lang']] : current($mod_details['MOD_NAME']);
								$mod_name_versioned = addslashes("$mod_name {$mod_details['MOD_VERSION']}");
							break;

							case '3.1.x':
								$mod_details = json_decode(file_get_contents($phpbb_root_path . $mod_dir . SLASH . $base_31x_file), true);
								$mod_name = $mod_details['extra']['display-name'];
								$mod_name_versioned = addslashes("$mod_name {$mod_details['version']}");
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
		if (file_exists($mods_root_path . $mod . SLASH . $file))
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
		if ($mod && strpos($mod, '..') === false)
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
		if ($mod && $file && file_exists($mods_root_path . $mod . SLASH . $file))
		{
			$filename = $mods_root_path . $mod . SLASH . $file;
			$handle = fopen($filename, "r");
			$contents = fread($handle, filesize($filename));
			fclose($handle);
			if (function_exists('mb_detect_encoding'))
			{
				echo json_encode(array(
						'eval' => "file_encoding('{$mod}', '{$file}', '" . mvt_detect_encoding($contents, array('UTF-8', 'UTF-7', 'ASCII','EUC-JP,SJIS', 'eucJP-win', 'SJIS-win', 'JIS', 'iso-8859-1', 'iso-8859-15', 'ISO-2022-JP', 'Windows-1250', 'Windows-1251', 'Windows-1252')) . "')",
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

//No garbage here
exit_handler();