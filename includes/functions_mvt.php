<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB_MVT'))
{
	exit;
}

// Common global functions

/**
* Get an array that represents directory tree
* @param string $directory		Directory path
* @param bool $recursive		Include sub directories
* @param bool $listDirs		Include directories on listing
* @param bool $listFiles		Include files on listing
* @param regex $exclude		Exclude paths that matches this regex
* @param array $ext_group	Include only file which have this extension
*/
function directory_to_array($directory, $recursive = true, $list_dirs = false, $list_files = true, $exclude = '', $ext_group = array()) 
{
	$array_items = array();
	$skip_by_exclude = false;
	//Prevent opendir warnings.
	if(!is_dir($directory))
	{
		return false;
	}
	$handle = opendir($directory);
	if ($handle ) 
	{
		while (false !== ($file = readdir($handle))) 
		{
			preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
			if ($exclude)
			{
				preg_match($exclude, $file, $skip_by_exclude);
			}
			if (!$skip && !$skip_by_exclude ) 
			{
				if (is_dir($directory. '/' . $file) ) 
				{
					if ($recursive ) 
					{
						$array_items = array_merge($array_items, directory_to_array($directory. '/' . $file, $recursive, $list_dirs, $list_files, $exclude, $ext_group));
					}
					if ($list_dirs )
					{
						$file = $directory . '/' . $file;
						$array_items[] = $file;
					}
				} 
				else 
				{
					if ($list_files)
					{
						if(!empty($ext_group) && !in_array(substr(strrchr($file, '.'), 1), $ext_group))
						{
							continue;
						}
						$file = $directory . '/' . $file;
						$array_items[] = $file;
					}
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}

function set_default_template_vars()
{
	global $template, $user, $phpbb_root_path;

	$template->assign_vars(array(
		'S_CONTENT_DIRECTION'	=> $user->lang['MVT_DIRECTION'],
		'S_CONTENT_ENCODING'	=> 'UTF-8',
		'S_USER_LANG'			=> $user->lang['MVT_LANG'],
		
/* 		'ICON_MOVE_UP'				=> '<img src="' . $phpbb_root_path . 'style/images/icon_up.gif" alt="' . $user->lang['MVT_MOVE_UP'] . '" title="' . $user->lang['MVT_MOVE_UP'] . '" />',
		'ICON_MOVE_UP_DISABLED'		=> '<img src="' . $phpbb_root_path . 'style/images/icon_up_disabled.gif" alt="' . $user->lang['MVT_MOVE_UP'] . '" title="' . $user->lang['MVT_MOVE_UP'] . '" />',
		'ICON_MOVE_DOWN'			=> '<img src="' . $phpbb_root_path . 'style/images/icon_down.gif" alt="' . $user->lang['MVT_MOVE_DOWN'] . '" title="' . $user->lang['MVT_MOVE_DOWN'] . '" />',
		'ICON_MOVE_DOWN_DISABLED'	=> '<img src="' . $phpbb_root_path . 'style/images/icon_down_disabled.gif" alt="' . $user->lang['MVT_MOVE_DOWN'] . '" title="' . $user->lang['MVT_MOVE_DOWN'] . '" />',
		'ICON_EDIT'					=> '<img src="' . $phpbb_root_path . 'style/images/icon_edit.gif" alt="' . $user->lang['MVT_EDIT'] . '" title="' . $user->lang['MVT_EDIT'] . '" />',
		'ICON_EDIT_DISABLED'		=> '<img src="' . $phpbb_root_path . 'style/images/icon_edit_disabled.gif" alt="' . $user->lang['MVT_EDIT'] . '" title="' . $user->lang['MVT_EDIT'] . '" />',
		'ICON_DELETE'				=> '<img src="' . $phpbb_root_path . 'style/images/icon_delete.gif" alt="' . $user->lang['MVT_DELETE'] . '" title="' . $user->lang['MVT_DELETE'] . '" />',
		'ICON_DELETE_DISABLED'		=> '<img src="' . $phpbb_root_path . 'style/images/icon_delete_disabled.gif" alt="' . $user->lang['MVT_DELETE'] . '" title="' . $user->lang['MVT_DELETE'] . '" />',
		'ICON_SYNC'					=> '<img src="' . $phpbb_root_path . 'style/images/icon_sync.gif" alt="' . $user->lang['MVT_RESYNC'] . '" title="' . $user->lang['MVT_RESYNC'] . '" />',
		'ICON_SYNC_DISABLED'		=> '<img src="' . $phpbb_root_path . 'style/images/icon_sync_disabled.gif" alt="' . $user->lang['MVT_RESYNC'] . '" title="' . $user->lang['MVT_RESYNC'] . '" />', */
	));
}

function mvt_get_config()
{
	global $phpbb_root_path, $phpEx;
	$config_file = $phpbb_root_path . 'bin/config.json';

	if(file_exists($config_file))
	{
		return json_decode(file_get_contents($config_file), true);
	}
}

function mvt_set_config($name, $value = '')
{
	global $phpbb_root_path, $phpEx, $config;
	$config_file = $phpbb_root_path . 'bin/config.json';
	$config[$name] = $value;
	$cfg = fopen($config_file, 'wb');
	fwrite($cfg, json_encode($config)); // We need to be sure that we do not remove unupdated config values!!
	fclose($cfg);
}

/**
* Generate back link for acp pages
*/
function mvt_back_link($u_action)
{
	global $user;
	return '<br /><br /><a href="' . $u_action . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
}

function mvt_check_php_syntax($file)
{
	global $config, $user;
	if(empty($config['mvt_php_syntax']))
	{
		return $user->lang['MVT_DISABLED_PHPBIN'];
	}
	if(empty($config['mvt_php_binary_path']))
	{
		return $user->lang['MVT_INVALID_PHPBIN'];
	}
	exec($config['mvt_php_binary_path'] . ' -lf ' . $file, $output, $result);
	return implode('<br />', $output);
}

/**
* Error and message handler, call with trigger_error if reqd
*/
function mvt_msg_handler($errno, $msg_text, $errfile, $errline)
{
	global $template, $config, $user;
	global $phpEx, $phpbb_root_path, $msg_title, $msg_long_text;

	// Do not display notices if we suppress them via @
	if (error_reporting() == 0 && $errno != E_USER_ERROR && $errno != E_USER_WARNING && $errno != E_USER_NOTICE)
	{
		return;
	}

	// Message handler is stripping text. In case we need it, we are possible to define long text...
	if (isset($msg_long_text) && $msg_long_text && !$msg_text)
	{
		$msg_text = $msg_long_text;
	}

	if (!defined('E_DEPRECATED'))
	{
		define('E_DEPRECATED', 8192);
	}

	switch ($errno)
	{
		case E_NOTICE:
		case E_WARNING:

			// Check the error reporting level and return if the error level does not match
			// If DEBUG is defined the default level is E_ALL
			if (($errno & ((defined('DEBUG')) ? E_ALL : error_reporting())) == 0)
			{
				return;
			}

			if (strpos($errfile, 'cache') === false && strpos($errfile, 'template.') === false)
			{
				$errfile = phpbb_filter_root_path($errfile);
				$msg_text = phpbb_filter_root_path($msg_text);
				$error_name = ($errno === E_WARNING) ? 'PHP Warning' : 'PHP Notice';
				echo '<b>[phpBB Debug] ' . $error_name . '</b>: in file <b>' . $errfile . '</b> on line <b>' . $errline . '</b>: <b>' . $msg_text . '</b><br />' . "\n";

				// we are writing an image - the user won't see the debug, so let's place it in the log
				if (defined('IMAGE_OUTPUT') || defined('IN_CRON'))
				{
					add_log('critical', 'LOG_IMAGE_GENERATION_ERROR', $errfile, $errline, $msg_text);
				}
				// echo '<br /><br />BACKTRACE<br />' . get_backtrace() . '<br />' . "\n";
			}

			return;

		break;

		case E_USER_ERROR:

			if (!empty($user) && !empty($user->lang))
			{
				$msg_text = (!empty($user->lang[$msg_text])) ? $user->lang[$msg_text] : $msg_text;
				$msg_title = (!isset($msg_title)) ? $user->lang['GENERAL_ERROR'] : ((!empty($user->lang[$msg_title])) ? $user->lang[$msg_title] : $msg_title);

				$l_return_index = sprintf($user->lang['RETURN_INDEX'], '<a href="' . $phpbb_root_path . '">', '</a>');
				$l_notify = '';

				if (!empty($config['board_contact']))
				{
					$l_notify = '<p>' . sprintf($user->lang['NOTIFY_ADMIN_EMAIL'], $config['board_contact']) . '</p>';
				}
			}
			else
			{
				$msg_title = 'General Error';
				$l_return_index = '<a href="' . $phpbb_root_path . '">Return to index page</a>';
				$l_notify = '';

				if (!empty($config['board_contact']))
				{
					$l_notify = '<p>Please notify the board administrator or webmaster: <a href="mailto:' . $config['board_contact'] . '">' . $config['board_contact'] . '</a></p>';
				}
			}

			$log_text = $msg_text;
			$backtrace = get_backtrace();
			if ($backtrace)
			{
				$log_text .= '<br /><br />BACKTRACE<br />' . $backtrace;
			}

			if (defined('IN_INSTALL') || defined('DEBUG_EXTRA') || isset($auth) && $auth->acl_get('a_'))
			{
				$msg_text = $log_text;
			}

			if ((defined('DEBUG') || defined('IN_CRON') || defined('IMAGE_OUTPUT')) && isset($db))
			{
				// let's avoid loops
				$db->sql_return_on_error(true);
				add_log('critical', 'LOG_GENERAL_ERROR', $msg_title, $log_text);
				$db->sql_return_on_error(false);
			}

			// Do not send 200 OK, but service unavailable on errors
			send_status_line(503, 'Service Unavailable');

			garbage_collection();

			// Try to not call the adm page data...

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
			echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">';
			echo '<head>';
			echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
			echo '<title>' . $msg_title . '</title>';
			echo '<style type="text/css">' . "\n" . '/* <![CDATA[ */' . "\n";
			echo '* { margin: 0; padding: 0; } html { font-size: 100%; height: 100%; margin-bottom: 1px; background-color: #E4EDF0; } body { font-family: "Lucida Grande", Verdana, Helvetica, Arial, sans-serif; color: #536482; background: #E4EDF0; font-size: 62.5%; margin: 0; } ';
			echo 'a:link, a:active, a:visited { color: #006699; text-decoration: none; } a:hover { color: #DD6900; text-decoration: underline; } ';
			echo '#wrap { padding: 0 20px 15px 20px; min-width: 615px; } #page-header { text-align: right; height: 40px; } #page-footer { clear: both; font-size: 1em; text-align: center; } ';
			echo '.panel { margin: 4px 0; background-color: #FFFFFF; border: solid 1px  #A9B8C2; } ';
			echo '#errorpage #page-header a { font-weight: bold; line-height: 6em; } #errorpage #content { padding: 10px; } #errorpage #content h1 { line-height: 1.2em; margin-bottom: 0; color: #DF075C; } ';
			echo '#errorpage #content div { margin-top: 20px; margin-bottom: 5px; border-bottom: 1px solid #CCCCCC; padding-bottom: 5px; color: #333333; font: bold 1.2em "Lucida Grande", Arial, Helvetica, sans-serif; text-decoration: none; line-height: 120%; text-align: left; } ';
			echo "\n" . '/* ]]> */' . "\n";
			echo '</style>';
			echo '</head>';
			echo '<body id="errorpage">';
			echo '<div id="wrap">';
			echo '	<div id="page-header">';
			echo '		' . $l_return_index;
			echo '	</div>';
			echo '	<div id="acp">';
			echo '	<div class="panel">';
			echo '		<div id="content">';
			echo '			<h1>' . $msg_title . '</h1>';

			echo '			<div>' . $msg_text . '</div>';

			echo $l_notify;

			echo '		</div>';
			echo '	</div>';
			echo '	</div>';
			echo '	<div id="page-footer">';
			echo '		Powered by <a href="https://www.phpbb.com/">phpBB</a>&reg; Forum Software &copy; phpBB Group';
			echo '	</div>';
			echo '</div>';
			echo '</body>';
			echo '</html>';

			exit_handler();

			// On a fatal error (and E_USER_ERROR *is* fatal) we never want other scripts to continue and force an exit here.
			exit;
		break;

		case E_USER_WARNING:
		case E_USER_NOTICE:

			define('IN_ERROR_HANDLER', true);

			if ($msg_text == 'ERROR_NO_ATTACHMENT' || $msg_text == 'NO_FORUM' || $msg_text == 'NO_TOPIC' || $msg_text == 'NO_USER')
			{
				send_status_line(404, 'Not Found');
			}

			$msg_text = (!empty($user->lang[$msg_text])) ? $user->lang[$msg_text] : $msg_text;
			$msg_title = (!isset($msg_title)) ? $user->lang['INFORMATION'] : ((!empty($user->lang[$msg_title])) ? $user->lang[$msg_title] : $msg_title);


			$template->set_filenames(array(
				'body' => 'message_body.html')
			);

			$template->assign_vars(array(
				'MESSAGE_TITLE'		=> $msg_title,
				'MESSAGE_TEXT'		=> $msg_text,
				'S_USER_WARNING'	=> ($errno == E_USER_WARNING) ? true : false,
				'S_USER_NOTICE'		=> ($errno == E_USER_NOTICE) ? true : false)
			);

			set_default_template_vars();
			$template->display('body');
			exit_handler();
		break;

		// PHP4 compatibility
		case E_DEPRECATED:
			return true;
		break;
	}

	// If we notice an error not handled here we pass this back to PHP by returning false
	// This may not work for all php versions
	return false;
}

/***
** Imported functions from functions_admin.php
***/

/**
* Retrieve contents from remotely stored file
*/
function get_remote_file($host, $directory, $filename, &$errstr, &$errno, $port = 80, $timeout = 6)
{
	global $user;

	if ($fsock = @fsockopen($host, $port, $errno, $errstr, $timeout))
	{
		@fputs($fsock, "GET $directory/$filename HTTP/1.0\r\n");
		@fputs($fsock, "HOST: $host\r\n");
		@fputs($fsock, "Connection: close\r\n\r\n");

		$timer_stop = time() + $timeout;
		stream_set_timeout($fsock, $timeout);

		$file_info = '';
		$get_info = false;

		while (!@feof($fsock))
		{
			if ($get_info)
			{
				$file_info .= @fread($fsock, 1024);
			}
			else
			{
				$line = @fgets($fsock, 1024);
				if ($line == "\r\n")
				{
					$get_info = true;
				}
				else if (stripos($line, '404 not found') !== false)
				{
					$errstr = $user->lang['FILE_NOT_FOUND'] . ': ' . $filename;
					return false;
				}
			}

			$stream_meta_data = stream_get_meta_data($fsock);

			if (!empty($stream_meta_data['timed_out']) || time() >= $timer_stop)
			{
				$errstr = $user->lang['FSOCK_TIMEOUT'];
				return false;
			}
		}
		@fclose($fsock);
	}
	else
	{
		if ($errstr)
		{
			$errstr = utf8_convert_message($errstr);
			return false;
		}
		else
		{
			$errstr = $user->lang['FSOCK_DISABLED'];
			return false;
		}
	}

	return $file_info;
}

function bertix_id()
{
	return substr(str_shuffle(sha1(md5(uniqid(time(), true)))), 0, 20);
}

function stream_copy($src, $dest, $meta_data = array())
{
	$fsrc = fopen($src, 'rb');
	$filename = $length = false;

	if(!$fsrc)
	{
		return false;
	}
	if(empty($meta_data))
	{
		$meta_data = stream_get_meta_data($fsrc);
	}
	if (isset($meta_data["uri"], $meta_data['wrapper_data']))
	{
		$filename = substr(strrchr($meta_data["uri"], '/'), 1);
		if(is_array($meta_data['wrapper_data']))
		{
			$array_name = preg_grep("#([0-9a-zA-Z_-])*\.zip#", $meta_data['wrapper_data']);
		}
		else
		{
			$array_name = array($meta_data['wrapper_data']);
		}

		preg_match("#([0-9a-zA-Z\._-]*\.zip)#", current($array_name), $matche);
		if($matche)
		{
			$filename = $matche[1];
		}
		$fdest = fopen($dest . $filename, 'w+b');
		$length = stream_copy_to_stream($fsrc, $fdest);
		fclose($fdest);
	}
	fclose($fsrc);
	return array('filename' => $filename, 'length' => $length);
} 

function strongify($str)
{
	return '<strong>' . $str . '</strong>';
}

function destroy_dir($dir) 
{ 
	if (!is_dir($dir) || is_link($dir)) 
	{
		return @unlink($dir); 
	}
	foreach (scandir($dir) as $file) 
	{ 
		if ($file == '.' || $file == '..')
		{
			continue; 
		}
		if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) 
		{ 
			chmod($dir . DIRECTORY_SEPARATOR . $file, 0777); 
			if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file))
			{
				return false; 
			}
		}; 
	} 
	return rmdir($dir); 
} 

function recurse_copy($src, $dst) 
{ 
	$dir = opendir($src); 
	@mkdir($dst); 
	while(false !== ( $file = readdir($dir)) ) 
	{ 
		if (( $file != '.' ) && ( $file != '..' )) 
		{ 
			if ( is_dir($src . '/' . $file) ) 
			{ 
				recurse_copy($src . '/' . $file,$dst . '/' . $file); 
			} 
			else 
			{ 
				copy($src . '/' . $file,$dst . '/' . $file); 
			} 
		} 
	} 
	closedir($dir); 
}

function check_mods_directory()
{
	global $phpbb_root_path;
	if (!is_dir($phpbb_root_path . 'mods/')) 
	{
		if(!mkdir($phpbb_root_path . 'mods/'))
		{
			trigger_error('Cannot create MODS directory.', E_USER_ERROR);
		}
	}
}


function check_cache_directory()
{
	global $phpbb_root_path;
	if (!is_dir($phpbb_root_path . 'cache/')) 
	{
		if(!mkdir($phpbb_root_path . 'cache/'))
		{
			trigger_error('Cannot create cache directory.', E_USER_ERROR);
		}
	}
}

/**
 * Detects the charset of a string.
 * @param string $str The string to check.
  * @param string $encoding_list The supported encoding list
 */
function mvt_detect_encoding($str, $encoding_list = 'auto')
{
	global $user;

	//Limit to 32768 to prevent unexpected CPU overload
	$encoding = @mb_detect_encoding(utf8_substr($str, 0, 32768), $encoding_list);
	if (stripos($encoding, 'UTF-8') !== false)
	{
		$utf8_bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
		$first3 = utf8_substr($str, 0, 3);
		if ($first3 == $utf8_bom)
		{
			return $encoding;
		}
		else
		{
			return "UTF-8<span class=\"eol-enc\"> ({$user->lang['MVT_NO_BOM']})</span>";
		}
	}
	else if($encoding)
	{
		return $encoding;
	}
	else
	{
		return "BIN<span class=\"eol-enc\"> ({$user->lang['MVT_BINARY']})</span>";
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
	if (strpos($str, $crlf) !== false)
	{
		return 'Dos/Windows<span class="eol-enc"> (CR+LF)</span>';
	}
	else if (strpos($str, $lf) !== false)
	{
		return 'UNIX<span class="eol-enc"> (LF)</span>';
	}
	else if (strpos($str, $cr) !== false)
	{
		return 'MAC<span class="eol-enc"> (CR)</span>';
	}
}

function mvt_syntaxify($content, $file_ext = 'txt')
{
	$uid = "\t" . bertix_id() . "\t";

	$content = str_replace(' ', $uid, $content);

	$geshi = new GeSHi($content, $file_ext);
	$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

	$content = $geshi->parse_code();
	$content = str_replace($uid, '<s class="spc"> </s>', $content);

	return  preg_replace("#(\\t)#siU", '<s class="tab">\\1</s>', $content);
}

/**
 * utf8_normalize_nfc() complement
 * @param string $string The string to utf8tise.
 */
function to_utf8($string) 
{
// From http://w3.org/International/questions/qa-forms-utf-8.html
	$reg = '%^(?:
	[\x09\x0A\x0D\x20-\x7E]              # ASCII
	| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	| \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
	| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	| \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
	| \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
	| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	| \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
	)*$%xs';

	if (preg_match($reg, $string)) 
	{
		return $string;
	} 
	else 
	{
		return iconv('CP1252', 'UTF-8', $string);
	}
}

class mvt_sha1
{
	public $xml = null;
	public $cached = array();
	public $max_record = 2500;
	public $default_array = array(
		'desc' => null,
		'status' => null,
		'name' => null
	);

	public function __construct($sha1_list_file, $cache_now = true)
	{
		if(file_exists($sha1_list_file))
		{
			$this->xml = simplexml_load_file($sha1_list_file);
			if($cache_now)
			{
				$this->_cache();
			}
		}
		else
		{
			return false;
		}
	}

	public function get($sha1, $filename = false)
	{
		if(isset($this->cached[$sha1]) && (!$filename || trim($this->cached[$sha1]->name) == trim($filename)))
		{
			return (object) $this->cached[$sha1];
		}
		else
		{
			return (object) ($this->default_array += array($sha1 => false));
		}
	}

	
	private function _cache()
	{
		$i = 0;
		foreach($this->xml AS $sha1_data)
		{
			$this->cached[(string) $sha1_data] = $sha1_data->attributes();
			if(++$i > $this->max_record)
			{
				break;
			}
		}
	}
}
function is_protected_file($mod_dir, $file)
{
	global $phpbb_root_path;
	$mods_root_path = $phpbb_root_path . 'mods/';

	if (in_array(substr(strrchr($file, '.'), 1), explode(',', BASE_INSTALL_FILE_EXT)))
	{
		$file_ext = substr(strrchr($file, '.'), 1);
		if($file_ext == 'xml')
		{
			$parser = new parser('xml');
			$parser->set_file($mods_root_path . $mod_dir . SLASH . $file);
			$mod_details = $parser->get_details();
			$mod_name = isset($mod_details['MOD_NAME'][$user->data['user_lang']]) ? $mod_details['MOD_NAME'][$user->data['user_lang']] : current($mod_details['MOD_NAME']);
			$mod_version = $mod_details['MOD_VERSION'];
		}
		else if($file_ext == 'json')
		{
			$mod_details = json_decode(file_get_contents($mods_root_path . $mod_dir . SLASH . $file), true);
			$mod_name = $mod_details['extra']['display-name'];
			$mod_version = $mod_details['version'];
		}
	}
	return (!empty($mod_details) && !empty($mod_name) && !empty($mod_version));
}