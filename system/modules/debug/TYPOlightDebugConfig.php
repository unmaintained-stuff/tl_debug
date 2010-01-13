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

class TYPOlightDebugConfig extends ArrayObject
{
	/**
	* Standardizes path for windows systems.
	*
	* @param string $Path
	* @return string
	*/
	protected function path($Path) {
		return preg_replace('/\\\\+/','/',$Path);    
	}

	public function offsetGet ($index)
	{
		
		return $this->offsetexists($index) ? parent::offsetGet($index) : NULL;
	}

	public function offsetSet ($p_key, $p_value) {
		// prevent debugmode from being unset.
		if($p_key=='debugMode' && !$p_value && TYPOlightDebug::isActive())
		{
			// work around the fact, that TYPOlight is including /system/config/localconfig.php twice.
			$traces=debug_backtrace();
			if(!(substr($this->path($traces[1]['file']),-28,28)=='/system/libraries/Config.php'))
				TYPOlightDebug::warn($traces[1]['file'].':'.$traces[1]['line'].' tried to set debugMode to false, but I need it to remain true in debug mode.', true);
		} else {
			$old=$this->offsetGet($p_key);
			if($old!=$p_value)
			{
				TYPOlightDebug::info(array('old' => $old, 'new' => $p_value), 'Configuration value ' . $p_key . ' changed', true);
				return parent::offsetSet($p_key, $p_value);
			}
		}
	}
}

?>