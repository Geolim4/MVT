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
include($phpbb_root_path . 'includes/geshi/geshi.' . $phpEx);
$user->add_lang('mvt');
$dir = urldecode(request_var('dir', ''));
if(file_exists($phpbb_root_path . $dir) && strpos($dir, $mods_root_path) === 0 && strpos($dir, '../') === false) 
{
	$files = scandir($phpbb_root_path . $dir);
	natcasesort($files);
	if( count($files) > 2 ) 
	{ 
		/* The 2 accounts for . and .. */
		echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
		// All dirs
		foreach( $files AS $file ) 
		{
			if( file_exists($phpbb_root_path . $dir . $file) && $file != '.' && $file != '..' && is_dir($phpbb_root_path . $dir . $file) ) 
			{
				echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($dir . $file) . "/\">" . htmlentities($file) . "</a></li>";
			}
		}
		// All files
		foreach( $files AS $file ) {
			if( file_exists($phpbb_root_path . $dir . $file) && $file != '.' && $file != '..' && !is_dir($phpbb_root_path . $dir . $file) ) 
			{
				$file_path = substr(strstr(str_replace($mods_root_path , '', $dir . $file), '/'), 1);
				$ext = preg_replace('/^.*\./', '', $file);
				echo "<li class=\"file ext_$ext\"><a href=\"#$file_path\" rel=\"" . htmlentities($dir . $file) . "\">" . htmlentities($file) . "&nbsp;&nbsp;<span class=\"tree-external\"></span></a></li>";
			}
		}
		echo "</ul>";
	}
}
else
{
	echo $user->lang['MVT_NO_FILE'];
}
exit_handler();
?>