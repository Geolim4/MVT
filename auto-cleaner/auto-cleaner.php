<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/
$time = microtime(true);
$script_version = '0.0.2';
if(strtolower(php_sapi_name()) != 'cli')
{
	die('Fatal error: This script must then be run from the command line interface (CLI).');
}
else
{
	echo PHP_EOL . " \e[0;36m" . str_repeat('*', 15) . " Mod Validation Tool (MVT) MOD cleaner v{$script_version} " . str_repeat('*', 15) . "\033[0m" . PHP_EOL . PHP_EOL;
}

define('IN_PHPBB', true);
define('IN_PHPBB_MVT', true);
chdir(dirname(__FILE__));
clearstatcache(true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$autocleaner_root_path = $phpbb_root_path . 'auto-cleaner/';
$mods_root_path = $phpbb_root_path . 'mods/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'includes/functions_mvt.' . $phpEx);

$rmvd_directories = $crtd_directories = $ign_directories = 0;
$rem_dir_list = $crea_dir_list = $ign_dir_list = array();
$force_delete = (isset($argv[1]) && $argv[1] == 'force') ? true : false;
$no_email = (isset($argv[2]) && $argv[2] == 'no-email') ? true : false;
//Remove mods created in /mods directory
$dh = opendir($mods_root_path);
$days_age = 3;

echo PHP_EOL . "\e[1;34mStep 1: Cleaning /mods directory" . PHP_EOL;
while (($mod_dir = readdir($dh)) !== false)
{
	if (is_dir($mods_root_path . $mod_dir) && !in_array($mod_dir, array('.', '..')))
	{
		$creation_date = filectime($mods_root_path . $mod_dir);
		if (($creation_date < (time() - (86400 * $days_age)) || $force_delete) && destroy_dir($mods_root_path . $mod_dir))
		{
			$rem_dir_list[] = $mod_dir;
			echo "\e[0;32mSuccessfully destroyed '{$mod_dir}' /mods directory . \033[0m" . PHP_EOL;
			$rmvd_directories++;
		}
		else if ($creation_date > (time() - (86400 * $days_age)) && !$force_delete)
		{
			$ign_dir_list[] = $mod_dir;
			echo "\e[1;33mIgnored '{$mod_dir}': Directory too recent (" . date("F d Y H:i:s", $creation_date) . "). Use 'force' argument to force the deletion. \033[0m" . PHP_EOL;
			$ign_directories++;
		}
		else
		{
			echo "\e[0;31mFailed destroying '{$mod_dir}' /mods directory. \033[0m" . PHP_EOL;
		}
	}
}
echo PHP_EOL . "\e[1;34mStep 2: Restoring default MODs in /mods directory" . PHP_EOL;
closedir($dh);

//Restore default mods in /mods directory
$dh = @opendir($autocleaner_root_path . 'default-mods');
while (($mod_dir = readdir($dh)) !== false)
{
	if (is_dir($autocleaner_root_path . 'default-mods/' . $mod_dir) && !in_array($mod_dir, array('.', '..')))
	{
		if (file_exists($mods_root_path . $mod_dir))
		{
			echo "\e[0;31mFailed copying '{$mod_dir}' in /mods directory, the directory already exists. Use 'force' argument to force the deletion of target directory.\033[0m" . PHP_EOL;
			continue;
		}

		recurse_copy($autocleaner_root_path . 'default-mods/' . $mod_dir, $mods_root_path . $mod_dir);

		if (is_dir($mods_root_path . $mod_dir))
		{
			$crea_dir_list[] = $mod_dir;
			echo "\e[0;32mSuccessfully created '{$mod_dir}' /mods directory. \033[0m" . PHP_EOL;
			$crtd_directories++;
			if(chmodr($mods_root_path . $mod_dir, 0777))
			{
				echo "	\e[1;37mSuccessfully applied chmod 0777 to '{$mod_dir}' directory. \033[0m" . PHP_EOL;
			}
			else
			{
				echo "	\e[1;31mFailed applying chmod 0777 to '{$mod_dir}' directory. \033[0m" . PHP_EOL;
			}
		}
		else
		{
			echo "\e[0;31mFailed creating '{$mod_dir}' in /mods directory. \033[0m" . PHP_EOL;
		}
	}
}
closedir($dh);

if($rmvd_directories > 1)
{
	$rmvd_directories = "$rmvd_directories directory";
}
else
{
	$rmvd_directories = "$rmvd_directories directory";
}

if($crtd_directories > 1)
{
	$crtd_directories = "$crtd_directories directory";
}
else
{
	$crtd_directories = "$crtd_directories directory";
}

if($ign_directories > 1)
{
	$ign_directories = "$ign_directories directory";
}
else
{
	$ign_directories = "$ign_directories directory";
}
echo PHP_EOL . "\e[1;35m$rmvd_directories were removed, $crtd_directories were created and $ign_directories were ignored.\033[0m" . PHP_EOL;

$rem_dir_list = '<br />' . implode('<br />', $rem_dir_list) . '<br /><br />';
$crea_dir_list = '<br />' . implode('<br />', $crea_dir_list) . '<br /><br />';
$ign_dir_list = '<br />' . implode('<br />', $ign_dir_list) . '<br /><br />';
$recipient = 'geolim4@gmail.com, zoddo.ino@gmail.com';
$sujet = "Cleaning report of the Mod Validation Tool";
$message = <<<EOF
<html>
<head>
   <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
   <title>Cleaning report of the Mod Validation Tool</title>
</head>
<body>
   <p>Hi my friend,<br />
that email confirm that the Mod Validation Tool has been cleaned successfully:
$rmvd_directories were removed, $crtd_directories were created and $ign_directories were ignored.</p>
List of removed directories: $rem_dir_list
List of created directories: $crea_dir_list
List of ignored directories: $ign_dir_list
<em>Your devoted and loved server.</em><br />
<p>Email sent by the Mod Validation Tool (MVT) MOD cleaner v{$script_version}</p>
</body>
</html>
EOF;

$headers = 
'Content-type: text/html; charset=utf-8' . "\r\n" .
'From: Your devoted server <root@geolim4.com>' . "\r\n" .
'Reply-To: Your devoted server <root@geolim4.com>' . "\r\n" .
'X-Mailer: PHP/' . phpversion();

if (!$no_email)
{
	$envoi = mail($recipient, $sujet, wordwrap($message, 70, "\r\n"), $headers);
	if (!empty($envoi))
	{
		echo "\e[1;35mA report has been sent to $recipient.\033[0m" . PHP_EOL . PHP_EOL;
	}
	else
	{
		echo "\e[0;31mFailed while sending report to $recipient. \033[0m" . PHP_EOL . PHP_EOL;
	}
}
else
{
	echo "\e[0;31mIgnored email sending as requested. \033[0m" . PHP_EOL . PHP_EOL;
}


echo PHP_EOL . " \e[0;36m" . str_repeat('*', 15) . " Script terminated in " . round(microtime(true) - $time, 3) ."s " . str_repeat('*', 15) . "\033[0m" . PHP_EOL . PHP_EOL;
exit;

/**
* CLI functions
*/
function chownr($path, $owner)
{
	if (!is_dir($path))
	{
		return chown($path, $owner);
	}

	$dh = @opendir($path);
	while (($file = readdir($dh)) !== false)
	{
		if($file != '.' && $file != '..')
		{
			$fullpath = $path.'/'.$file;
			if(is_link($fullpath))
			{
				return false;
			}
			elseif(!is_dir($fullpath) && !chown($fullpath, $owner))
			{
				return false;
			}
			elseif(!chownr($fullpath, $owner))
			{
				return false;
			}
		}
	}

	closedir($dh);

	if(chown($path, $owner))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function chmodr($path, $mode)
{
	if (!is_dir($path))
	{
		return chmod($path, $mode);
	}

	$dh = @opendir($path);
	while (($file = readdir($dh)) !== false)
	{
		if($file != '.' && $file != '..')
		{
			$fullpath = $path.'/'.$file;
			if(is_link($fullpath))
			{
				return false;
			}
			elseif(!is_dir($fullpath) && !chmod($fullpath, $mode))
			{
				return false;
			}
			elseif(!chmodr($fullpath, $mode))
			{
				return false;
			}
		}
	}

	closedir($dh);

	if(chmod($path, $mode))
	{
		return true;
	}
	else
	{
		return false;
	}
}