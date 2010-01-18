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

class TYPOlightDebugArray extends ArrayObject
{
	protected $channel='';
	protected $method='';

	/**
	* Standardizes path for windows systems.
	*
	* @param string $Path
	* @return string
	*/
	protected function path($Path) {
		return preg_replace('/\\\\+/','/',$Path);    
	}

	protected function addLine($value, $key='')
	{
		$file='';
		$i=0;
		if(is_array($value) && (count($value)>=2) && 
									  ((isset($value[1]) && 
														 ((strpos($value[1],'rows returned')!==false) || (strpos($value[1], 'rows affected')!==false)))))
		{
			// logging of statements disabled? exit!
			if(!(array_key_exists('logDatabase', $GLOBALS['TL_CONFIG']) && $GLOBALS['TL_CONFIG']['logDatabase']))
				return true;
			$traces=debug_backtrace(false);
			while(($file=='' || $file=='/system/libraries/Database.php') && $i++<count($traces)-2)
			{
				if(!($trace=$traces[$i]))
					break;
				if(array_key_exists('file', $trace))
				{
					// are we still in debugging files?
					if(strpos($this->path($trace['file']),'libraries/Database.php'))
						$file=str_replace(TL_ROOT, '', $this->path($traces[$i+1]['file']));
				}
			}
			if($file !== '')
			{
				// only log database queries that are allowed, according to our settings.
				if(preg_match('#/system/modules/([^/]+)/#', $file, $names))
				{
					if(!(array_key_exists('logDatabaseModules', $GLOBALS['TL_CONFIG']) && $GLOBALS['TL_CONFIG']['logDatabaseModules'] && in_array($names[1],deserialize($GLOBALS['TL_CONFIG']['logDatabaseModules']))))
					return true;
				} else {
					// not from module directory, should be core then
					if(!(array_key_exists('logDatabaseModules', $GLOBALS['TL_CONFIG']) && $GLOBALS['TL_CONFIG']['logDatabaseModules'] && in_array('core',deserialize($GLOBALS['TL_CONFIG']['logDatabaseModules']))))
					return true;
				}
				// log and exit.
				TYPOlightDebug::info($value, 'Database Query: ' . ($key ? $key : substr($value[0], 0, 80).'...'), true);
				return true;
			}
		}
		$label=$key;
		if($this->method)
		{
			$label=$key;
			$key=$this->method;
			$this->method='';
		}
		if(in_array($key, array('log', 'info', 'warn', 'error')))
			TYPOlightDebug::$key($value, $label);
		else
			TYPOlightDebug::log($value, ($key ? $key : ''));
	}
	public function append($value)
	{
		$this->addLine($value);
	}

	public function offsetGet ($index)
	{
		if(in_array($index, array('log', 'info', 'warn', 'error')))
			$this->method=$index;
		else
			$this->channel=$index;
		return $this;
	}

	public function offsetSet ($p_key, $p_value)
	{
		if($p_key===NULL && $this->channel!==NULL)
		{
			$p_key=$this->channel;
			$this->channel=NULL;
		}
		$this->addLine($p_value, $p_key);
	}
}

?>