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

//utf8_normalize_nfc() not working properly here :|
if (!($dir = to_utf8(urldecode(request_var('dir', '', true)))))
{
	$dir = to_utf8(urldecode(current(request_var('dir', array(''), true))));
}

//request_var request_var does not handle correctly recursively arrays
$post_data = (isset($_POST['post_data'])? $_POST['post_data'] : false);
$ext_ary = $version_ary = array();

if($post_data)
{
	foreach($post_data AS $key => $post_data_)
	{
		if(strpos($post_data_, 'ext-') === 0)
		{
			$ext_ary[] = trim(str_replace('ext-', '', $post_data_));
		}
		if(strpos($post_data_, 'version-') === 0)
		{
			$version_ary[] = trim(str_replace('version-', '', $post_data_)). SLASH;
			//$version_ary[] = strstr(trim(str_replace($mods_root_path, '', $dir)), '/', true);
		}
	}
}

if (file_exists($mods_root_path . $dir) && strpos($dir, '..') === false) 
{
	$echo = '';
	$files = scandir($mods_root_path . $dir);
	natcasesort($files);

	if (count($files) > 2) 
	{ 
		// All dirs
		foreach ($files AS $file) 
		{
			if (file_exists($mods_root_path . $dir . $file) && $file != '.' && $file != '..' && is_dir($mods_root_path . $dir . $file)) 
			{
				if (!empty($ext_ary) && !sizeof(directory_to_array($mods_root_path . $dir . $file, true, false, true, '', $ext_ary)))
				{
					continue;
				}

				$echo .= "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($dir . $file) . "/\">" . htmlentities($file) . "</a></li>";
			}
		}

		// All files
		foreach ($files AS $file) 
		{
			if (file_exists($mods_root_path . $dir . $file) && $file != '.' && $file != '..' && !is_dir($mods_root_path . $dir . $file)) 
			{
				if(!empty($ext_ary) && !in_array(substr(strrchr($file, '.'), 1), $ext_ary))
				{
					continue;
				}
				if ($version_ary)
				{
					$diff_unmodified = true;
					foreach($version_ary AS $update)
					{
						$update .= substr(strstr($dir, SLASH), 1);
						if($diff_unmodified && file_exists($mods_root_path . $update . $file))
						{
							if(sha1_file($mods_root_path . $update . $file) == sha1_file($mods_root_path . $dir . $file))
							{
								$diff_unmodified = false;
							}
						}
					}
					if(!$diff_unmodified)
					{
						continue;
					}
				}
				$file_path = substr(strstr(str_replace($mods_root_path , '', $dir . $file), '/'), 1);
				$ext = preg_replace('/^.*\./', '', $file);
				$echo .= "<li class=\"file ext_$ext\"><a class=\"tree-link\" href=\"#$file_path\" rel=\"" . htmlentities($dir . $file) . "\">" . htmlentities($file) . "&nbsp;&nbsp;<span class=\"tree-external\"></span></a></li>";
			}
		}
	}
	if(isset($version_file_ary))
	{
		//print_r($version_file_ary);
	}
	if($echo)
	{
		echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">{$echo}</ul>";
	}
}
else
{
	echo $user->lang['MVT_NO_FILE'];
}
//No garbage here
exit_handler();
?>