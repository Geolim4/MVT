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
define('MVT_VERSION', '0.0.3');
define('MVT_GIT_REPOSITORY', 'https://github.com/Geolim4/MVT');
define('BASE_30X_FILE', 'install_mod.xml');
define('BASE_31X_FILE', 'composer.json');
define('BASE_INSTALL_FILE_EXT', 'json,xml');
define('MVT_SUPPORTED_VERSIONS', '3.0.x:30x,3.1.x:31x');
define('MVT_DEMO_MODE', false);

/**
* Imported phpBB constants
**/

// phpbb_chmod() permissions
@define('CHMOD_ALL', 7);
@define('CHMOD_READ', 4);
@define('CHMOD_WRITE', 2);
@define('CHMOD_EXECUTE', 1);