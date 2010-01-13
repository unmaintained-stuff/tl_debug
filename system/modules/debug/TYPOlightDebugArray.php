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
		if(is_array($value))
		{
			$traces=debug_backtrace();
			while(($file=='' || $file=='/system/libraries/Database.php') && $i++<count($traces))
			{
				$trace=&$traces[$i];
				if(array_key_exists('file', $trace))
				{
					// are we still in debugging files?
					if(strpos($this->path($trace['file']),'libraries/Database.php'))
						$file=str_replace(TL_ROOT, '', $this->path($traces[$i+1]['file']));
				}
			}
			// only log database queries that are allowed, according to our settings.
			if(in_array($file, $GLOBALS['TL_DEBUGGER']['DATABASE']))
			{
				TYPOlightDebug::info($value, 'Database Query: ' . ($key ? $key : substr($value[0], 0, 80).'...'), true);
			}
			// exit if it was a database query, no matter if we handled it or not.
			if($file !== '')
				return true;
		}
		TYPOlightDebug::log($value, ($key ? $key : ''));
	}
	public function append($value)
	{
		$this->addLine($value);
	}

	public function offsetGet ($index)
	{
		$this->channel=$index;
		return $this;
	}

	public function offsetSet ($p_key, $p_value)
	{
		if($p_key==NULL && $this->channel)
		{
			$p_key=$this->channel;
			$this->channel=NULL;
		}
		$this->addLine($p_value, $p_key);
	}
}

?>