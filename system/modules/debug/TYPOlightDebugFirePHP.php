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

require_once(TL_ROOT . '/system/modules/debug/FirePHPCore/FirePHP.class.php');

/**
 * Sends the given data to the FirePHP Firefox Extension.
 * The data can be displayed in the Firebug Console or in the
 * "Server" request tab.
 * 
 * For more information see: http://www.firephp.org/
 * 
 * @copyright   Copyright (C) 2007-2009 Christoph Dorn
 * @author      Christoph Dorn <christoph@christophdorn.com>
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @package     FirePHP
 */
class TYPOlightDebugFirePHP extends FirePHP
{
  
	/**
	 * FirePHP version
	 *
	 * @var string
	 */
	const VERSION = '0.3typolight';
	
	/**
	 * Singleton instance of FirePHP
	 *
	 * @var FirePHP
	 */
	protected static $instance = null;

	/**
	 * Counter for the amount of data we are sending.
	 *
	 * @var FirePHP
	 */
	protected $headersizes = array();

	/**
	 * The object constructor
	 */
	function __construct() {
		$this->skipClasses=array();
		$this->skipFiles=array();
		parent::__construct();
	}

	/**
	 * Gets singleton instance of FirePHP
	 *
	 * @param boolean $AutoCreate
	 * @return FirePHP
	 */
	public static function getInstance($AutoCreate=false) {
		if($AutoCreate===true && !self::$instance) {
			self::init();
		}
		return self::$instance;
	}

	/**
	 * Creates FirePHP object and stores it for singleton access
	 *
	 * @return FirePHP
	 */
	public static function init() {
		return self::$instance = new self();
	}

	public function skipClassInTrace($classname)
	{
		$this->skipClasses[]=$classname;
	}

	public function skipFileInTrace($filename)
	{
		$this->skipFiles[]=$filename;
	}

	protected function cleanTrace($trace, $Object='')
	{
		$ret=array();
		$idx=0;
		/*
		for($i=0;$i<sizeof($trace);$i++)
		{
			if(
				($trace[$i]['function']=='fb'
        		   || $trace[$i]['function']=='trace'
        		   || $trace[$i]['function']=='send') 
				|| !(
					(array_key_exists('class', $trace[$i])
					&& (
						in_array($trace[$i]['class'], $this->skipClasses)
						|| (
							array_key_exists('function', $trace[$i])
							&& in_array($trace[$i]['class'].'->'.$trace[$i]['function'], $this->skipClasses))
						)
					)
					|| (array_key_exists('file', $trace[$i]) && in_array($trace[$i]['file'], $this->skipFiles))
					)
				)
			{
				$ret[$idx++]=&$trace[$i];
			}
		}
		*/
		for($i=0;$i<count($trace);$i++)
		{
			// check if it is some firePHP file we are issuing.
			// check if it is the TYPOlight debugger.
			if(isset($trace[$i]['file'])
					&& ((substr($trace[$i]['file'],-18,18)=='TYPOlightDebug.php')
						|| (substr($trace[$i]['file'],-23,23)=='TYPOlightDebugArray.php' && $trace[$i]['function']!='offsetSet')
						|| (substr($trace[$i]['file'],-24,24)=='TYPOlightDebugConfig.php' && $trace[$i]['function']!='offsetSet'))
			)
			continue;
			if(isset($trace[$i]['class']) && ($trace[$i]['class']=='TYPOlightDebug'))
			{
				if($trace[$i]['function']=='errorHandler')
				{
					$trace[$i]['function']='--HERE--';
					unset($trace[$i]['class']);
					$trace[$i]['args']=array();
				} else if(substr($trace[$i]['file'],-14,14)=='TYPOlightDebug')
					continue;
				else {
					unset($trace[$i]['class']);
					if(isset($trace[$i]['args']) && count($trace[$i]['args'])>=2)
					$trace[$i]['args']=array($trace[$i]['args'][0],$trace[$i]['args'][1]);
				}
			}
			if(isset($trace[$i]['class']) && $trace[$i]['class']=='TYPOlightDebugArray')
			{
				if($trace[$i]['function']=='offsetSet')
				{
					$trace[$i]['function']='TL_DEBUG';
					$trace[$i]['args']=array_splice($trace[$i]['args'],1);
					unset($trace[$i]['class']);
				} else
					continue;
			}
			if(isset($trace[$i]['class']) && $trace[$i]['class']=='TYPOlightDebugConfig')
			{
				if($trace[$i]['function']=='offsetSet')
				{
					$trace[$i]['function']='$GLOBALS[\'TL_CONFIG\'][\''.$trace[$i]['args'][0].'\']=';
					unset($trace[$i]['object']);
					$trace[$i]['args']=array_splice($trace[$i]['args'],1);
					unset($trace[$i]['class']);
				} else
					continue;
			}
			// make output smaller by huge means.
			unset($trace[$i]['object']);
			// keep this entry in the stack.
			$ret[$idx++]=$trace[$i];
		}
		//return $trace;
		return $ret;
	}

	/**
	 * Log varible to Firebug
	 * 
	 * @see http://www.firephp.org/Wiki/Reference/Fb
	 * @param mixed $Object The variable to be logged
	 * @return true Return TRUE if message was added to headers, FALSE otherwise
	 * @throws Exception
	 */
  public function fb($Object)
  {
	if(!$this->enabled) {
		return false;
	}
	if (headers_sent($filename, $linenum))
	{
		// If we are logging from within the exception handler we cannot throw another exception
		if($this->inExceptionHandler)
		{
			// Simply echo the error out to the page
			echo '<div style="border: 2px solid red; font-family: Arial; font-size: 12px; background-color: lightgray; padding: 5px;"><span style="color: red; font-weight: bold;">FirePHP ERROR:</span> Headers already sent in <b>'.$filename.'</b> on line <b>'.$linenum.'</b>. Cannot send log data to FirePHP. You must have Output Buffering enabled via ob_start() or output_buffering ini directive.</div>';
		} else {
			throw $this->newException('Headers already sent in '.$filename.' on line '.$linenum.'. Cannot send log data to FirePHP. You must have Output Buffering enabled via ob_start() or output_buffering ini directive.');
		}
	}
  
	$Type = null;
	$Label = null;
	$Options = array();
	
	if(func_num_args()==1)
	{
	} else
	if(func_num_args()==2)
	{
		switch(func_get_arg(1))
		{
			case self::LOG:
			case self::INFO:
			case self::WARN:
			case self::ERROR:
			case self::DUMP:
			case self::TRACE:
			case self::EXCEPTION:
			case self::TABLE:
			case self::GROUP_START:
			case self::GROUP_END:
				$Type = func_get_arg(1);
				break;
			default:
				$Label = func_get_arg(1);
				break;
		}
	} else if(func_num_args()==3)
	{
		$Type = func_get_arg(2);
		$Label = func_get_arg(1);
	} else if(func_num_args()==4)
	{
		$Type = func_get_arg(2);
		$Label = func_get_arg(1);
		$Options = func_get_arg(3);
	} else
	{
		throw $this->newException('Wrong number of arguments to fb() function!');
	}
  
	if(!$this->detectClientExtension())
	{
		return false;
	}

	$meta = array();
	$skipFinalObjectEncode = false;
    if($Object instanceof Exception)
	{
		$meta['file'] = $this->_escapeTraceFile($Object->getFile());
		$meta['line'] = $Object->getLine();
		$trace = $Object->getTrace();
		$trace=$this->cleanTrace($trace);
		if($Object instanceof ErrorException
			&& isset($trace[0]['function'])
			&& $trace[0]['function']=='errorHandler'
			&& isset($trace[0]['class'])
			&& $trace[0]['class']=='FirePHP')
		{
				$severity = false;
				switch($Object->getSeverity())
				{
					case E_WARNING: $severity = 'E_WARNING'; break;
					case E_NOTICE: $severity = 'E_NOTICE'; break;
					case E_USER_ERROR: $severity = 'E_USER_ERROR'; break;
					case E_USER_WARNING: $severity = 'E_USER_WARNING'; break;
					case E_USER_NOTICE: $severity = 'E_USER_NOTICE'; break;
					case E_STRICT: $severity = 'E_STRICT'; break;
					case E_RECOVERABLE_ERROR: $severity = 'E_RECOVERABLE_ERROR'; break;
					case E_DEPRECATED: $severity = 'E_DEPRECATED'; break;
					case E_USER_DEPRECATED: $severity = 'E_USER_DEPRECATED'; break;
				}
           
        $Object = array('Class'=>get_class($Object),
                        'Message'=>$severity.': '.$Object->getMessage(),
                        'File'=>$this->_escapeTraceFile($Object->getFile()),
                        'Line'=>$Object->getLine(),
                        'Type'=>'trigger',
                        'Trace'=>$this->_escapeTrace(array_splice($trace,2)));
        $skipFinalObjectEncode = true;
      } else {
        $Object = array('Class'=>get_class($Object),
                        'Message'=>$Object->getMessage(),
                        'File'=>$this->_escapeTraceFile($Object->getFile()),
                        'Line'=>$Object->getLine(),
                        'Type'=>'throw',
                        'Trace'=>$this->_escapeTrace($trace));
        $skipFinalObjectEncode = true;
      }
      $Type = self::EXCEPTION;
      
    } else
    if($Type==self::TRACE)
	{
		$trace = @debug_backtrace(false);
		$fromTrace=(isset($trace[1]['class']) && isset($trace[1]['file']) && $trace[1]['class']=='FirePHP' && ($trace[1]['function']=='trace'));
		$trace=$this->cleanTrace($trace, $Object);
		if(!$trace) return false;
		$i=1;
		$Object = array('Class'=>isset($trace[$i]['class'])?$trace[$i]['class']:'',
						'Type'=>isset($trace[$i]['type'])?$trace[$i]['type']:'',
						'Function'=>isset($trace[$i]['function'])?$trace[$i]['function']:'',
						'Message'=> $fromTrace ? $Object : (isset($trace[$i]['args'])&& count($trace[$i]['args']) ? $trace[$i]['args'][0] : '...'),
						'File'=>isset($trace[$i]['file'])?$this->_escapeTraceFile($trace[$i]['file']):'',
						'Line'=>isset($trace[$i]['line'])?$trace[$i]['line']:'',
						'Args'=>isset($trace[$i]['args'])?$this->encodeObject($trace[$i]['args']):'',
						'Trace'=>$this->_escapeTrace(array_splice($trace,$i+1)));
		$skipFinalObjectEncode = true;
		$meta['file'] = isset($trace[$i]['file'])?$this->_escapeTraceFile($trace[$i]['file']):'';
		$meta['line'] = isset($trace[$i]['line'])?$trace[$i]['line']:'';
	} else if($Type==self::TABLE)
	{
		if(isset($Object[0]) && is_string($Object[0]))
		{
			$Object[1] = $this->encodeTable($Object[1]);
		} else
		{
			$Object = $this->encodeTable($Object);
		}
		$skipFinalObjectEncode = true;
    } else if($Type==self::GROUP_START)
	{
		if(!$Label)
		{
			throw $this->newException('You must specify a label for the group!');
		}
	} else {
		if($Type===null)
		{
			$Type = self::LOG;
		}
	}
	if($this->options['includeLineNumbers'])
	{
		if(!isset($meta['file']) || !isset($meta['line']))
		{
			$trace = @debug_backtrace(false);
			$trace=$this->cleanTrace($trace);
			$meta['file'] = isset($trace[1]['file'])?$this->_escapeTraceFile($trace[1]['file']):'';
			$meta['line'] = isset($trace[1]['line'])?$trace[1]['line']:'';
		}
	} else {
		unset($meta['file']);
		unset($meta['line']);
	}
	$this->setHeader('X-Wf-Protocol-1','http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
	$this->setHeader('X-Wf-1-Plugin-1','http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/'.self::VERSION);
	$structure_index = 1;
	if($Type==self::DUMP)
	{
		$structure_index = 2;
		$this->setHeader('X-Wf-1-Structure-2','http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1');
	} else {
		$this->setHeader('X-Wf-1-Structure-1','http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
	}
	if($Type==self::DUMP) {
		$msg = '{"'.$Label.'":'.$this->jsonEncode($Object, $skipFinalObjectEncode).'}';
	} else {
		$msg_meta = $Options;
		$msg_meta['Type'] = $Type;
		if($Label!==null)
		{
			$msg_meta['Label'] = $Label;
		}
		if(isset($meta['file']) && !isset($msg_meta['File']))
		{
			$msg_meta['File'] = $meta['file'];
		}
		if(isset($meta['line']) && !isset($msg_meta['Line']))
		{
			$msg_meta['Line'] = $meta['line'];
		}
		// experimental logging of stack trace for every log attempt
		if(false)
		{
			if(!isset($trace))
				$trace = $this->cleanTrace(@debug_backtrace());
			if($trace)
				$msg_meta['Trace'] = $this->_escapeTrace(array_splice($trace,2));
		}
		$msg = '['.$this->jsonEncode($msg_meta).','.$this->jsonEncode($Object, $skipFinalObjectEncode).']';
	}
	$parts = explode("\n",chunk_split($msg, 5000, "\n"));
	for( $i=0 ; $i<count($parts) ; $i++)
	{
		$part = $parts[$i];
		if ($part)
		{
			if(count($parts)>2) {
				// Message needs to be split into multiple parts
				$this->setHeader('X-Wf-1-'.$structure_index.'-'.'1-'.$this->messageIndex,
							(($i==0)?strlen($msg):'')
							. '|' . $part . '|'
							. (($i<count($parts)-2)?'\\':''));
			} else {
			$this->setHeader('X-Wf-1-'.$structure_index.'-'.'1-'.$this->messageIndex,
							strlen($part) . '|' . $part . '|');
			}
			$this->messageIndex++;
			if ($this->messageIndex > 99999)
			{
				throw $this->newException('Maximum number (99,999) of messages reached!');             
			}
		}
	}
  	$this->setHeader('X-Wf-1-Index',$this->messageIndex-1);
    return true;
  }
  
  /**
   * Encode an object into a JSON string
   * 
   * Uses PHP's jeson_encode() if available
   * 
   * @param object $Object The object to be encoded
   * @return string The JSON string
   */
	public function jsonEncode($Object, $skipObjectEncode=false)
	{
		if(!$skipObjectEncode) {
			$Object = $this->encodeObject($Object);
		}
		
		if(function_exists('json_encode')
			&& $this->options['useNativeJsonEncode']!=false)
		{
			return @json_encode($Object);
		} else {
			return parent::jsonEncode($Object, $skipObjectEncode);
		}
	}

	/**
	 * Send header
	 *
	 * @param string $Name
	 * @param string_type $Value
	 */
	protected function setHeader($Name, $Value)
	{
		$size=strlen($Name.': '.$Value);
		$oldsize=(array_key_exists($Name, $this->headersizes)) ? $this->headersizes[$Name] : 0;
		$this->headerSize+=$size-$oldsize;
		$this->headersizes[$Name]=$size;
		return parent::setHeader($Name, $Value);
	}

	public function getSize()
	{
		return array_sum($this->headersizes);
	}

  /**
   * Encodes an object including members with
   * protected and private visibility
   * 
   * @param Object $Object The object to be encoded
   * @param int $Depth The current traversal depth
   * @return array All members of the object
   */
  protected function encodeObject($Object, $ObjectDepth = 1, $ArrayDepth = 1)
  {
    $return = array();

    if (is_resource($Object)) {

      return '** '.(string)$Object.' **';

    } else    
    if (is_object($Object)) {

        if ($ObjectDepth > $this->options['maxObjectDepth']) {
          return '** Max Object Depth ('.$this->options['maxObjectDepth'].') **';
        }
        
        foreach ($this->objectStack as $refVal) {
            if ($refVal === $Object) {
                return '** Recursion ('.get_class($Object).') **';
            }
        }
        array_push($this->objectStack, $Object);
                
        $return['__className'] = $class = get_class($Object);
        $class_lower = strtolower($class);

        $reflectionClass = new ReflectionClass($class);  
        $properties = array();
        foreach( $reflectionClass->getProperties() as $property) {
          $properties[$property->getName()] = $property;
        }
            
        $members = (array)$Object;
            
        foreach( $properties as $raw_name => $property ) {
          
          $name = $raw_name;
          if($property->isStatic()) {
            $name = 'static:'.$name;
          }
          if($property->isPublic()) {
            $name = 'public:'.$name;
          } else
          if($property->isPrivate()) {
            $name = 'private:'.$name;
            $raw_name = "\0".$class."\0".$raw_name;
          } else
          if($property->isProtected()) {
            $name = 'protected:'.$name;
            $raw_name = "\0".'*'."\0".$raw_name;
          }
          
          if(!(isset($this->objectFilters[$class_lower])
               && is_array($this->objectFilters[$class_lower])
			   && (in_array($raw_name,$this->objectFilters[$class_lower])
                 || in_array('*',$this->objectFilters[$class_lower])))) {

            if(array_key_exists($raw_name,$members)
               && !$property->isStatic()) {
              
              $return[$name] = $this->encodeObject($members[$raw_name], $ObjectDepth + 1, 1);      
            
            } else {
              if(method_exists($property,'setAccessible')) {
                $property->setAccessible(true);
                $return[$name] = $this->encodeObject($property->getValue($Object), $ObjectDepth + 1, 1);
              } else
              if($property->isPublic()) {
                $return[$name] = $this->encodeObject($property->getValue($Object), $ObjectDepth + 1, 1);
              } else {
                $return[$name] = '** Need PHP 5.3 to get value **';
              }
            }
          } else {
            $return[$name] = '** Excluded by Filter **';
          }
        }
        
        // Include all members that are not defined in the class
        // but exist in the object
        foreach( $members as $raw_name => $value ) {
          
          $name = $raw_name;
          
          if ($name{0} == "\0") {
            $parts = explode("\0", $name);
            $name = $parts[2];
          }
          
          if(!isset($properties[$name])) {
            $name = 'undeclared:'.$name;
              
            if(!(isset($this->objectFilters[$class_lower])
                 && is_array($this->objectFilters[$class_lower])
                 && in_array($raw_name,$this->objectFilters[$class_lower]))) {
              
              $return[$name] = $this->encodeObject($value, $ObjectDepth + 1, 1);
            } else {
              $return[$name] = '** Excluded by Filter **';
            }
          }
        }
        
        array_pop($this->objectStack);
        
    } elseif (is_array($Object)) {

        if ($ArrayDepth > $this->options['maxArrayDepth']) {
          return '** Max Array Depth ('.$this->options['maxArrayDepth'].') **';
        }
      
        foreach ($Object as $key => $val) {
          
          // Encoding the $GLOBALS PHP array causes an infinite loop
          // if the recursion is not reset here as it contains
          // a reference to itself. This is the only way I have come up
          // with to stop infinite recursion in this case.
          if($key=='GLOBALS'
             && is_array($val)
             && array_key_exists('GLOBALS',$val)) {
            $val['GLOBALS'] = '** Recursion (GLOBALS) **';
          }
          
          $return[$key] = $this->encodeObject($val, 1, $ArrayDepth + 1);
        }
    } else {
      if(self::is_utf8($Object)) {
        return $Object;
      } else {
        return utf8_encode($Object);
      }
    }
    return $return;
  }

}
?>