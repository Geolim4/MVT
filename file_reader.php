<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @jQuery File Tree PHP Connector 1.01 by Cory S.N. LaViska (http://abeautifulsite.net/) 24 March 2008
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/

define('IN_PHPBB', true);
define('IN_PHPBB_MVT', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$mods_root_path = $phpbb_root_path . 'mods/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);

$file = utf8_normalize_nfc(request_var('f', '', true));
if (file_exists($phpbb_root_path . $file) && strpos($file, '..') === false && filesize($phpbb_root_path . $file) < 1310720)//10 Mo
{
	$file_ext = substr(strrchr($file, '.'), 1);

	switch ($file_ext)
	{
		case "gif": 
		case "tiff":
		case "png":
		case "jpeg":
		case "jpg": 
			$ctype = "image/$file_ext"; 
		break;

		case "svg": 
			$ctype = "image/svg+xml"; 
		break;

		case "swf": 
			$ctype = "application/x-shockwave-flash"; 
		break;

		default:
			$ctype = "text/plain"; 
		break;
	}

	header('Content-type: ' . $ctype);
	readfile($phpbb_root_path . $file);
}
exit_handler();