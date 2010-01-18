<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Cyberspectrum 2010
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package    Debugger 
 * @license    LGPL 
 * @filesource
 */


/**
 * Fields
 */

$GLOBALS['TL_LANG']['tl_debug']['enableDebug']          = array('Debugger active', 'Please select this to enable the TYPOlight debugger. This will have an impact on the performance of the site for enabled users.');
$GLOBALS['TL_LANG']['tl_debug']['enableDebugMember']    = array('Frontend-user selection', 'Please select all frontend users (members) for who the debugger shall be active.');
$GLOBALS['TL_LANG']['tl_debug']['enableDebugUser']      = array('Backend-user selection', 'Please select all backend users for who the debugger shall be active.');

$GLOBALS['TL_LANG']['tl_debug']['hideCoreNotices']      = array('Hide notices issued from the core', 'Please select this option to hide all notices coming from core files from the log.');
$GLOBALS['TL_LANG']['tl_debug']['showNotices']          = array('Notice finetuning', 'Here you can select what notices you want to be shown.');

$GLOBALS['TL_LANG']['tl_debug']['logErrors']            = array('Error level selection', 'Please select the errors you want to have in your debug log.');
$GLOBALS['TL_LANG']['tl_debug']['logHooks']             = array('Trace HOOKs', 'Select this option to enable the tracing of HOOKs.');
$GLOBALS['TL_LANG']['tl_debug']['logHookSelection']     = array('Selected HOOKs for tracing', 'Please select all HOOKs, you want to have traced.');

$GLOBALS['TL_LANG']['tl_debug']['logDatabase']          = array('Log Database statements', 'Select this option to enable the logging of database statements.');
$GLOBALS['TL_LANG']['tl_debug']['logDatabaseModules']   = array('Log Database statements from the following modules.', 'Please select all modules you want the database statements traced from.');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_debug']['base_legend']          = 'Common settings';
$GLOBALS['TL_LANG']['tl_debug']['debugdata_legend']     = 'Data selection';

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_debug']['edit']                 = 'Configure the debugger.';
$GLOBALS['TL_LANG']['tl_debug']['severity']             = array(
	E_WARNING=>'Warnings (E_WARNING)',
	E_NOTICE=>'Notices (E_NOTICE)',
	E_PARSE=>'Parse errors (E_PARSE)',
	E_CORE_ERROR=>'PHP core errors (E_CORE_ERROR)',
	E_CORE_WARNING=>'PHP core warnings (E_CORE_WARNING)',
	E_COMPILE_ERROR=>'PHP compiler errors (E_COMPILE_ERROR)',
	E_COMPILE_WARNING=>'PHP compiler warnings (E_COMPILE_WARNING)',
	E_USER_ERROR=>'User generated errors (E_USER_ERROR)',
	E_USER_WARNING=>'User generated warnings (E_USER_WARNING)',
	E_USER_NOTICE=>'User generated notices (E_USER_NOTICE)',
	E_STRICT=>'PHP strict messages (E_STRICT)',
	E_RECOVERABLE_ERROR=>'recoverable errors (E_RECOVERABLE_ERROR)',
	E_DEPRECATED=>'Notifies for deprecated functions (E_DEPRECATED)',
	E_USER_DEPRECATED=>'User generated notifies for deprecated functions (E_USER_DEPRECATED)',
);

$GLOBALS['TL_LANG']['tl_debug']['undefinedIndex']         = 'Show notices resulting from undefined index (Undefined index: ...)';
$GLOBALS['TL_LANG']['tl_debug']['undefinedOffset']        = 'Show notices resulting from undefined offset (Undefined offset: ...)';
$GLOBALS['TL_LANG']['tl_debug']['propertyNonObject']      = 'Show notices resulting from property retrival of non objects (Trying to get property of non-object)';
$GLOBALS['TL_LANG']['tl_debug']['constantAlreadyDefined'] = 'Show notices resulting from defining an already defined constant (Constant XY already defined)';

$GLOBALS['TL_LANG']['tl_debug']['logHookNames']     = array(
	'activateAccount'		=> 'activateAccount (triggered when a new front end account is activated)',
	'activateRecipient'		=> 'activateRecipient (triggered when a new newsletter recipient is added)',
	'addCustomRegexp'		=> 'addCustomRegexp (triggered when an unknown regular expression is found)',
	'addLogEntry'			=> 'addLogEntry (triggered when a new log entry is added)',
	'checkCredentials'		=> 'checkCredentials (triggered when a login attempt fails due to a wrong password)',
	'createNewUser'			=> 'createNewUser (triggered when a new front end user registers on the website)',
	'executePreActions'		=> 'executePreActions (triggered on Ajax requests that do not expect a response)',
	'executePostActions'	=> 'executePostActions (triggered on Ajax requests that expect a response)',
	'generateFrontendUrl'	=> 'generateFrontendUrl (triggered when a front end URL is recreated)',
	'generatePage'			=> 'generatePage (triggered before the main layout (fe_page) is compiled',
	'getAllEvents'			=> 'getAllEvents (allows to modify the result sets of calendar and event modules)',
	'getPageIdFromUrl'		=> 'getPageIdFromUrl (triggered when the URL fragments are evaluated)',
	'getSearchablePages'	=> 'getSearchablePages (triggered when the the search index is rebuilt',
	'importUser'			=> 'importUser (triggered when a username cannot be found in the database)',
	'listComments'			=> 'listComments (triggered when comments are listed in the back end)',
	'loadFormField'			=> 'loadFormField (triggered when a form field is loaded)',
	'loadLanguageFile'		=> 'loadLanguageFile (triggered when a language file is loaded)',
	'outputBackendTemplate'	=> 'outputBackendTemplate (triggered when a back end template is printed to the screen)',
	'outputFrontendTemplate' => 'outputFrontendTemplate (triggered when a front end template is printed to the screen)',
	'parseBackendTemplate'	=> 'parseBackendTemplate (triggered when a back end template is parsed)',
	'parseFrontendTemplate'	=> 'parseFrontendTemplate (triggered when a front end template is parsed)',
	'postDownload'			=> 'postDownload (triggered after a file has been downloaded with the download(s) element)',
	'postLogin'				=> 'postLogin (triggered after a user has logged into the front end)',
	'postLogout'			=> 'postLogout (triggered after a user has logged out from the front end)',
	'postUpload'			=> 'postUpload (triggered after a user has uploaded one or more file in the back end)',
	'printArticleAsPdf'		=> 'printArticleAsPdf (triggered when an article is exported as PDF)',
	'processFormData'		=> 'processFormData (triggered after a form has been submitted)',
	'removeOldFeeds'		=> 'removeOldFeeds (triggered when old XML files are being removed from the TYPOlight directory)',
	'removeRecipient'		=> 'removeRecipient (triggered when a newsletter recipient is removed)',
	'replaceInsertTags'		=> 'replaceInsertTags (triggered when an unknown insert tag is found)',
	'reviseTable'			=> 'reviseTable (triggered when TYPOlight removes orphan records from a table)',
	'setNewPassword'		=> 'setNewPassword (triggered after a new password has been set)',
	'validateFormField'		=> 'validateFormField (triggered when a form field is submitted)',
	'loadDataContainer'		=> 'loadDataContainer (triggered when a data container get\'s loaded)',
	'dispatchAjax'			=> 'dispatchAjax (triggered when a ajax request is issued)',
);



?>