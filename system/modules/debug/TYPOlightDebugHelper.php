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

class TYPOlightDebugHelper extends Backend
{
	/*
	 * return an array of all known hooks.
	*/
	public function getHooks()
	{
		$this->loadDataContainer('tl_debug');
		$res=array();
		foreach(get_class_methods('TYPOlightDebugHookCatcher') as $v)
		{
			if(!in_array($v, array('__call','ProcessHook','getHooks')))
			{
				$res[$v]=((array_key_exists($v, $GLOBALS['TL_LANG']['tl_debug']['logHookNames']) && $GLOBALS['TL_LANG']['tl_debug']['logHookNames'][$v]) ? $GLOBALS['TL_LANG']['tl_debug']['logHookNames'][$v] : 'non core HOOK: ' . $v);
			}
		}
		return $res;
	}

	const DEBUGCONFIG_STRING = "/*### BEGIN: TYPOlight Debugger startup - do not move! ###*/\nTYPOlightDebug::startUp();\n/*### END: TYPOlight Debugger startup - do not move! ###*/\n\n";
	const DEBUGCONFIG_INJECT = " * @filesource\n */\n";

	/*
	 * this is a rather nasty method, I really admit.
	 * we need to inject ourselves into system/config/initconfig.php, as we have no other way of telling where we are in the system.
	 * We can not use our configuration file, as it get's loaded from Config::__construct();
	 * We can not use a hook either, as it is "too late" in the process and we will loose precious debug information.
	 * We should also not rely upon the user to inject ourselves as this is not "as nice" :D
	 * We MIGHT use spl_autoload and check if BackendUser and FrontendUser are already loaded and if so, start ourselves up then.
	 * There is also a pending ticket on allowing custom loader classes but until this is done, we will need to stick to this initconfig 
	 * approach or work via spl_autoload.
	 */
	public function saveDebuggerstate($varValue, $dc)
	{
		$objFile=new File('system/config/initconfig.php');
		$strContent=$objFile->getContent();
		if($varValue)
		{
			if(!strpos($strContent,self::DEBUGCONFIG_STRING))
			{
				// enable debugger.
				$strContent=str_replace(self::DEBUGCONFIG_INJECT,self::DEBUGCONFIG_INJECT.self::DEBUGCONFIG_STRING,$strContent);
			}
		} else {
			if(strpos($strContent,self::DEBUGCONFIG_STRING))
			{
				// disable debugger.
				$strContent=str_replace(self::DEBUGCONFIG_STRING,'',$strContent);
			}
		}
		$objFile->write($strContent);
		$objFile->close();
		return $varValue;
	}
	public function loadDebuggerstate($varValue, $dc)
	{
		$objFile=new File('system/config/initconfig.php');
		$strContent=$objFile->getContent();
		$objFile->close();
		return $varValue && strpos($strContent,self::DEBUGCONFIG_STRING);
	}
}

?>