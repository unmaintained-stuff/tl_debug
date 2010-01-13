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

$GLOBALS['TL_LANG']['tl_debug']['enableDebug']          = array('Debugger aktivieren', 'Wählen Sie diese Option, um den TYPOlight debugger zu aktivieren. Die Performance der Seite wird durch den Debugger etwas beeinträchtigt.');
$GLOBALS['TL_LANG']['tl_debug']['enableDebugMember']    = array('Frontend-Benutzerauswahl', 'Wählen Sie bitte alle Frontend-Benutzer aus, für die der TYPOlight debugger aktiviert werden soll.');
$GLOBALS['TL_LANG']['tl_debug']['enableDebugUser']      = array('Backend-Benutzerauswahl', 'Wählen Sie bitte alle Benutzer aus, für die der TYPOlight debugger aktiviert werden soll.');

$GLOBALS['TL_LANG']['tl_debug']['hideCoreNotices']      = array('Fehlermeldungen und Notices aus dem Core verstecken', 'Wählen Sie diese Option, um alle Meldungen, welche aus dem core generiert werden im Log zu unterdrücken.');
$GLOBALS['TL_LANG']['tl_debug']['showNotices']          = array('Notices finetuning', 'Hier können Sie spezielle Arten von Notices explizit an oder abschalten.');

$GLOBALS['TL_LANG']['tl_debug']['logErrors']            = array('Fehleranzeige konfigurieren', 'Bitte wählen Sie hier die gewünschten errorlevel aus, die in der Protokollierung enthalten sein sollen.');
$GLOBALS['TL_LANG']['tl_debug']['logHooks']             = array('Hooks verfolgen', 'Wählen Sie diese Option, um HOOKs zu verfolgen.');
$GLOBALS['TL_LANG']['tl_debug']['logHookSelection']     = array('aktive Hooks', 'Wählen Sie die HOOKs aus, die verfolgt werden sollen.');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_debug']['base_legend']          = 'Allgemeine Einstellungen';
$GLOBALS['TL_LANG']['tl_debug']['debugdata_legend']     = 'Zu protokollierende Daten';

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_debug']['edit']                 = 'Debugger einstellen.';
$GLOBALS['TL_LANG']['tl_debug']['severity']             = array(
	E_WARNING=>'Warnungen (E_WARNING)',
	E_NOTICE=>'Hinweise (E_NOTICE)',
	E_PARSE=>'Parse Fehler (E_PARSE)',
	E_CORE_ERROR=>'PHP Core Fehler (E_CORE_ERROR)',
	E_CORE_WARNING=>'PHP Core Warnungen (E_CORE_WARNING)',
	E_COMPILE_ERROR=>'PHP Compiler Fehler (E_COMPILE_ERROR)',
	E_COMPILE_WARNING=>'PHP Compiler Warnungen (E_COMPILE_WARNING)',
	E_USER_ERROR=>'Benutzerdefinierte Fehler (E_USER_ERROR)',
	E_USER_WARNING=>'Benutzerdefinierte Warnungen (E_USER_WARNING)',
	E_USER_NOTICE=>'Benutzerdefinierte Hinweise (E_USER_NOTICE)',
	E_STRICT=>'PHP strict Meldungen (E_STRICT)',
	E_RECOVERABLE_ERROR=>'behebare Fehler (E_RECOVERABLE_ERROR)',
	E_DEPRECATED=>'Veraltete Funktionen (E_DEPRECATED)',
	E_USER_DEPRECATED=>'Benutzerdefinierte veraltete Funktionen (E_USER_DEPRECATED)',
);

$GLOBALS['TL_LANG']['tl_debug']['undefinedIndex']      = 'Notices anzeigen, die sich auf undefinierte indizes beziehen (Undefined index: ...)';
$GLOBALS['TL_LANG']['tl_debug']['undefinedOffset']      = 'Notices anzeigen, die sich auf undefinierte indizes beziehen (Undefined index: ...)';
$GLOBALS['TL_LANG']['tl_debug']['propertyNonObject']   = 'Notices anzeigen, die sich auf das auslesen einer property eines Nicht-Objekts beziehen (Trying to get property of non-object)';

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
	'validateFormField'		=> 'validateFormField (triggered when a form field is submitted)'
);



?>