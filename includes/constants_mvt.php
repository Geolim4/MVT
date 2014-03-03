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
if (!defined('IN_PHPBB'))
{
	exit;
}

define('SLASH', '/');
define('MVT_VERSION', '0.0.2');
define('MVT_GIT_REPOSITORY', 'https://github.com/Geolim4/MVT');

/**
* Imported phpBB constants
**/

// phpbb_chmod() permissions
@define('CHMOD_ALL', 7);
@define('CHMOD_READ', 4);
@define('CHMOD_WRITE', 2);
@define('CHMOD_EXECUTE', 1);