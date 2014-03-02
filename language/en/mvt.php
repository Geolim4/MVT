<?php
/*
 @package MVT
 @copyright (c) 2014 phpBB-fr MOD Team
 @license http://opensource.org/licenses/gpl-license.php GNU Public License
 @author Georges.L (Geolim4)
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB_MVT'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
// Some characters you may want to copy&paste:
// ’ « » “ ” …
// Use: <strong style="color:green">Texte</strong>',
// For add Color

$lang = array_merge($lang, array(
	'MVT_LANG' => 'en',
	'MVT_DATE_FORMAT' => 'D M d, Y g:i a',
	'MVT_CFG_PHP_SYNTAX' => 'PHP syntax checker',
	'MVT_DRAG_BUTTON' => '░',
	'MVT_BASE64_DECODE' => 'Decode base64 code',
	'MVT_INVALID_BASE64' => 'Invalid base64 string!',
	'MVT_INSERT_FILENAME' => 'Insert filename',
	'MVT_EXIT_HANDLER' => 'Exit alert handler',
	'MVT_FILESTATS' => 'Get file statistics',
	'MVT_NO_XML' => 'Any valid XML file found!',
	'MVT_NO_MOD' => 'The specified MOD does not exists in <strong>/mods</strong> directory.',
	'MVT_NO_MODS' => 'Any MOD found inside <strong>/mods</strong> directory.',
	'MVT_MOD_ALTERNATIVE' => 'You can also unzip a MOD inside the <strong>/mods</strong> directory.',
	'MVT_MOD_DELETED' => 'The MOD has been deleted.',
	'MVT_MOD_DELETE_WARN' => 'That will remove the MOD from the <strong>/mods</strong> directory. Are you sure to continue?',
	'MVT_MOD_DELETE_FAILED' => 'Cannot remove <strong>%s</strong> directory.',
	'MVT_MOD_FAILED' => 'Connection to the specified URL has failed.',
	'MVT_MOD_ALREADY_PRESENT' => 'It seem the MOD you tried to import is already present in the <strong>/mods</strong> directory.',
	'MVT_CFG_PHP_BINARY_PATH' => 'PHP binary path',
	'MVT_CFG_PHP_BINARY_PATH_EXPLAIN' => 'Type the PHP command-line interface path.
		<br /><strong>Windows users</strong>: <em>C:\php_path\php.exe</em>.
			<br />You can also define PHP as an <em>Execution Environments</em>, <a href="http://windows.fyicenter.com/view.php?ID=60">see more</a>.
		<br /><strong>Linux users</strong>: <em>php</em>',
	'MVT_ADD_MOD' => 'Add a MOD',
	'MVT_ADD_MOD_URL' => 'Type the URL of the remote file. To specify several different URL enter each on a new line.',
	'MVT_LANGUAGE' => 'Language',
	'MVT_VERSION' => 'Version',
	'MVT_CFG_TAB_STR_LEN' => 'Max chars count of MOD tabs',
	'MVT_DIRECTION' => 'ltr',
	'MVT_INVALID_PHPBIN' => '<strong class="error">Invalid PHP binary path specified!</strong>',
	'MVT_INTERNAL' => '@Internal',
	'MVT_PURGE' => 'Purge notepad',
	'MVT_PURGE_CONFIRM' => 'Are you sure you want to purge the notepad?',
	'MVT_NO_FILE' => 'No file found!',
	'MVT_EMPTY_MESSAGE' => 'Empty message!',
	'MVT_INTERNAL_EXPLAIN' => 'Join a message to the validation Team',
	'MVT_TOOLS' => 'Tools',
	'MVT_SELECT_ALL' => 'Select all',
	'MVT_CLOSE_WINDOW' => 'Close window',
	'MVT_INFORMATION' => 'Information',
	'MVT_DESCRIBE' => 'Describe the reason to report that part of code:',
	'MVT_CANCEL' => 'Cancel',
	'MVT_ADD_TO_REPORT' => 'Add to report',
	'MVT_HOME'	=> 'MVT home',
	'MVT_FILE_BROWSER' => 'File browser',
	'MVT_OPEN_NOTEPAD' => 'Open validation notepad',
	'MVT_PHP_SYNTAX' => 'PHP syntax checking',
	'MVT_OK' => 'Ok',
	'MVT_CONTINUE' => 'Continue',
	'MVT_NOTEPAD_TITLE' => 'Validation editor',
	'MVT_SETTINGS' => 'Settings',
	'MVT_SETTINGS_SAVED' => 'Settings saved.',
	'MVT_NEW_TAB' => 'Open MODs link in new tab',
	'MVT_SELECT_ERROR' => 'Please select a part of code!',
	'MVT_SCROLL_DOWN' => 'Scroll down',
	'MVT_SCROLL_TOP' => 'Scroll top',
	'MVT_SCROLL_LEFT' => 'Scroll left',
	'MVT_SCROLL_RIGHT' => 'Scroll right',
	'MVT_TAG_NOTICE' => 'NOTICE',
	'MVT_TAG_WARNING' => 'WARNING',
	'MVT_TAG_FAIL' => 'FAIL',
	'MVT_VERSION_ERROR' => 'Cannot retrieve version from server, error message: %s',
	'MVT_LATEST_VERSION' => 'Latest version: %1$s, <a href="%1$s">read more</a>.',
	'MVT_EXPAND_ALL' => 'Expand all',
	'MVT_COLLAPSE_ALL' => 'Collapse all',
	'MVT_SEARCH_ENGINE' => 'Search on %s',
	'MVT_CFG_SEARCH_ENGINE' => 'Search engine name',
	'MVT_CFG_SEARCH_ENGINE_URL' => 'Search engine address',
	'MVT_CFG_SEARCH_ENGINE_URL_EXPLAIN' => 'Type the search engine address as using the <em>%CODE%</em> var which will be replaced by the code you are looking for.',
	'MVT_CFG_SEARCH_ENGINE_IMG' => 'Search engine image filename',
	'MVT_EXIT_ALERT' => 'Your validation notepad is not empty, if you leave without saving it, you will lose those datas !',
));