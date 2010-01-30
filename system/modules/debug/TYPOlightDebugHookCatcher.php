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

class TYPOlightDebugHookCatcher
{
	public function activateAccount()
	{
		$params = func_get_args();
		$this->ProcessHook('activateAccount', $params);
	}
	
	public function activateRecipient()
	{
		$params = func_get_args();
		$this->ProcessHook('activateRecipient', $params);
	}

	public function addCustomRegexp()
	{
		$params = func_get_args();
		$this->ProcessHook('addCustomRegexp', $params);
		return false;
	}

	public function addLogEntry()
	{
		$params = func_get_args();
		$this->ProcessHook('addLogEntry', $params);
		return false;
	}

	public function checkCredentials()
	{
		$params = func_get_args();
		// TODO: add flag to make them visible?
		$params[1]='***PASSWORD PROTECTED***';
		$this->ProcessHook('checkCredentials', $params);
		return false;
	}

	public function createNewUser()
	{
		$params = func_get_args();
		$this->ProcessHook('createNewUser', $params);
	}

	public function executePreActions()
	{
		$params = func_get_args();
		$this->ProcessHook('executePreActions', $params);
	}
	
	public function executePostActions()
	{
		$params = func_get_args();
		$this->ProcessHook('executePostActions', $params);
	}

	public function generateFrontendUrl($arrPage, $strParams, $strUrl)
	{
		$params = func_get_args();
		$this->ProcessHook('generateFrontendUrl', $params);
		return $strUrl;
	}

	public function generatePage()
	{
		$params = func_get_args();
		$this->ProcessHook('generatePage', $params);
	}

	public function getAllEvents($arrEvents, $arrCalendars, $intStart, $intEnd)
	{
		$params = func_get_args();
		$this->ProcessHook('getAllEvents', $params);
		return $arrEvents;
	}

	public function getPageIdFromUrl($arrFragments)
	{
		$params = func_get_args();
		$this->ProcessHook('getPageIdFromUrl', $params);
		return $arrFragments;
	}

	public function getSearchablePages($arrPages, $intRoot)
	{
		$params = func_get_args();
		$this->ProcessHook('getSearchablePages', $params);
		return $arrPages;
	}

	public function importUser()
	{
		$params = func_get_args();
		$this->ProcessHook('importUser', $params);
		return false;
	}

	public function listComments()
	{
		$params = func_get_args();
		$this->ProcessHook('listComments', $params);
		return '';
	}

	public function loadFormField(Widget $objWidget)
	{
		$params = func_get_args();
		$this->ProcessHook('loadFormField', $params);
		return $objWidget;
	}

	public function loadLanguageFile()
	{
		$params = func_get_args();
		$this->ProcessHook('loadLanguageFile', $params);
	}

	public function outputBackendTemplate($strContent)
	{
		$params = func_get_args();
		$this->ProcessHook('outputBackendTemplate', $params);
		return $strContent;
	}

	public function outputFrontendTemplate($strContent)
	{
		$params = func_get_args();
		$this->ProcessHook('outputFrontendTemplate', $params);
		return $strContent;
	}

	public function parseBackendTemplate($strContent)
	{
		$params = func_get_args();
		$this->ProcessHook('parseBackendTemplate', $params);
		return $strContent;
	}

	public function parseFrontendTemplate($strContent)
	{
		$params = func_get_args();
		$this->ProcessHook('parseFrontendTemplate', $params);
		return $strContent;
	}

	public function postDownload()
	{
		$params = func_get_args();
		$this->ProcessHook('postDownload', $params);
	}

	public function postLogin()
	{
		$params = func_get_args();
		$this->ProcessHook('postLogin', $params);
	}


	public function postLogout()
	{
		$params = func_get_args();
		$this->ProcessHook('postLogout', $params);
	}

	public function postUpload()
	{
		$params = func_get_args();
		$this->ProcessHook('postUpload', $params);
	}

	public function printArticleAsPdf()
	{
		// TODO: unsure about this one here as we are not exiting as we can not as we do not create a pdf.
		// we have to check if TYPOlight will call the other hooks after this one "fails".
		$params = func_get_args();
		$this->ProcessHook('printArticleAsPdf', $params);
	}

	public function removeOldFeeds()
	{
		$params = func_get_args();
		$this->ProcessHook('removeOldFeeds', $params);
		return array();
	}

	public function removeRecipient()
	{
		$params = func_get_args();
		$this->ProcessHook('removeRecipient', $params);
	}

	public function replaceInsertTags()
	{
		$params = func_get_args();
		$this->ProcessHook('replaceInsertTags', $params);
		return false;
	}

	public function reviseTable($strTable, $ids, $foo, $bar)
	{
		$params = func_get_args();
		$this->ProcessHook('reviseTable', $params);
		return;
	}

	public function setNewPassword()
	{
		$params = func_get_args();
		$this->ProcessHook('setNewPassword', $params);
		return false;
	}

	public function validateFormField(Widget $objWidget)
	{
		$params = func_get_args();
		$this->ProcessHook('validateFormField', $params);
		return $objWidget;
	}

	public function loadDataContainer()
	{
		$params = func_get_args();
		$this->ProcessHook('loadDataContainer', $params);
	}

	/*
	 * other hooks from here on.
	*/
	public function dispatchAjax()
	{
		$params = func_get_args();
		$this->ProcessHook('dispatchAjax', $params);
		return;
	}

////////////////////////////////////////
// Meta methods and internal use only from here on.
////////////////////////////////////////

	/*
	 * Handle unknown hooks in here
	 */
	public function __call($strMethod, $params)
	{
		throw new Exception('TYPOlight Debugger error: UNKNOWN HOOK called: '.$strMethod);
	}


	protected static $hookstack=array();

	/*
	 * generic hook logging facility.
	*/
	protected function ProcessHook($hookname, $params)
	{
		$cnt=count($GLOBALS['TL_HOOKS'][$hookname])-2;
		if($cnt>0)
		{
			$lasthook=end(self::$hookstack);
			if($lasthook===$hookname)
			{
				TYPOlightDebug::info('EXIT HOOK::'.$hookname);
				TYPOlightDebug::groupEnd();
				array_pop(self::$hookstack);
			} else {
				array_push(self::$hookstack, $hookname);
				TYPOlightDebug::group('HOOK::'.$hookname . ' (' . $cnt . ' handler' . ($cnt-1 ? 's' :''). ' registered)' );
				TYPOlightDebug::info($params, 'ENTER HOOK::'.$hookname);
			}
		} else {
			TYPOlightDebug::info($params, 'HOOK::'.$hookname);
		}
	}
	
}

?>