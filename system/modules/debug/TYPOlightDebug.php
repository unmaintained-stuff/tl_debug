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


class TYPOlightDebug
{
	/*
	 * pointer to the firebug/firephp instance
	 */
	protected static $fb=NULL;
	/*
	 * amount of debug data assembled so far.
	 */
	protected static $size=0;
	/*
	 * maximum amount of debug data allowed to be sent to the client.
	 * this is not required but suggested, as many proxies and anti virus software can not process large HTTP headers.
	 */
	protected static $maxsize=80000000; // apache default: 65535 - keeping reserve here as we can not know how much each object encoded will "eat".
	/*
	 * logic flag if the size has been exceeded.
	 */
	protected static $sizeexceeded=false;
	/*
	 * counters for the different supressed messages.
	 */
	protected static $supressed=array(E_WARNING=>0, E_NOTICE=>0, E_USER_NOTICE=>0, E_USER_WARNING=>0,  E_ERROR=>0,  E_USER_ERROR=>0,  E_RECOVERABLE_ERROR=>0);
	/*
	 * files to be skipped in error messages, if not mentioned within here, the error messages will get logged.
	 */
	protected static $skipFiles=array();
	/*
	 * internal map for mapping severity names to constants.
	 */
	protected static $severity=array(
							E_ERROR => 'E_ERROR',
							E_WARNING=>'E_WARNING',
							E_NOTICE=>'E_NOTICE',
							E_PARSE=>'E_PARSE',
							E_CORE_ERROR=>'E_CORE_ERROR',
							E_CORE_WARNING=>'E_CORE_WARNING',
							E_COMPILE_ERROR=>'E_COMPILE_ERROR',
							E_COMPILE_WARNING=>'E_COMPILE_WARNING',
							E_USER_ERROR=>'E_USER_ERROR',
							E_USER_WARNING=>'E_USER_WARNING',
							E_USER_NOTICE=>'E_USER_NOTICE',
							E_STRICT=>'E_STRICT',
							E_RECOVERABLE_ERROR=>'E_RECOVERABLE_ERROR',
							E_DEPRECATED=>'E_DEPRECATED',
							E_USER_DEPRECATED=>'E_USER_DEPRECATED',
							);
	/*
	 * combined bitmask of errors we want to handle.
	 */
	protected static $log_severity=0;

	/*
	 * logic flag to keep track if we activated output buffering or not.
	 */
	protected static $ob_started=false;

	/*
	 * combined bitmask of errors we want to handle.
	 */
	protected static $showNotices=array();

	/*
	 * combined bitmask of errors we want to handle.
	 */
	protected static $inGroup=false;

	/*
	 * combined bitmask of errors we want to handle.
	 */
	protected static $ticks=0;

	/*
	 * function to implode an array using name:value pairs in logging instead of just using the value.
	 */
	protected static function implodeWithKey($glue, $arr)
	{
		$ret='';
		foreach($arr as $k=>$v)
		{
			$ret .= (strlen($ret) ? $glue : '') . $k.': '.$v;
		}
		return $ret;
	}

	/*
	 * internal wrapper function for finding out about all user information.
	 */
	protected static function getLoginStatus($strCookie)
	{
		$hash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? Environment::getInstance()->ip : '') . $strCookie);
		if (Input::getInstance()->cookie($strCookie) == $hash)
		{
			$objSession = Database::getInstance()->prepare("SELECT * FROM tl_session WHERE hash=? AND name=?")
										 ->limit(1)
										 ->execute($hash, $strCookie);
			if ($objSession->numRows && $objSession->sessionID == session_id() && ($GLOBALS['TL_CONFIG']['disableIpCheck'] || $objSession->ip == Environment::getInstance()->ip) && ($objSession->tstamp + $GLOBALS['TL_CONFIG']['sessionTimeout']) > time())
			{
				return $objSession->pid;
			}
		}
		return false;
	}

	public static function isActive()
	{
		return self::$fb ? true : false;
	}

	/*
	 * this function will get called right before TYPOlight exits. We have to "cleanup" 
	 * (iow: prevent TYPOlight from dumping the debugdata on its own) in here.
	 */
	public function ProcessDebugData($strBuffer, $strTemplate)
	{
		$GLOBALS['TL_CONFIG']=(array)$GLOBALS['TL_CONFIG'];
		$GLOBALS['TL_CONFIG']['debugMode']=false;
		unset($GLOBALS['TL_DEBUG']);
		return $strBuffer;
	}

	/*
	 * this function will get called right before PHP exits. We check if there was a fatal error 
	 * and display the error screen. We also log some statistics to firebug.
	 */
	public static function shutDown()
	{
		if ($error = error_get_last())
		{
			switch($error['type'])
			{
				case E_ERROR:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					// we definately want to prevent the error message from being sent to the browser.
					if(self::$ob_started)
					{
						ob_end_clean();
						ob_start();
					}
					self::error(self::$severity[$error['type']].' ' . $error['message'] . ' in file: ' . $error['file'] . ' on line ' . $error['line']);
					self::error('active modules: ' . implode(', ',Config::getInstance()->getActiveModules()));
					ini_set('display_errors', false);
					show_help_message();
					break;
				default:;
			}
		}
		try {
			if(function_exists('getrusage'))
			{
				$dat=getrusage();
				$executiontime=(($dat['ru_utime.tv_sec']*1e6+$dat['ru_utime.tv_usec'])/1e6) . ' seconds';
			} else {
				$executiontime='N/A';
			}
			self::$fb->info('TYPOlight debugger exiting. Max. mem used: ' . memory_get_peak_usage() . ' bytes. Execution time: '. $executiontime);
			self::$fb->info('Supressed: ' . self::implodeWithKey(', ', array(
				'Notices'=>(self::$supressed[E_NOTICE]+self::$supressed[E_USER_NOTICE]),
				'Warnings'=>(self::$supressed[E_WARNING]+self::$supressed[E_USER_WARNING])
				)));
			self::$fb->info('Executed: ' . self::$ticks . ' statements.');
		} catch (Exception $e) {
			// we can not log via headers anymore, echo the exception out then.
			echo $e->getMessage();
		}
		if(self::$ob_started)
			ob_end_flush();
	}
	/*
	 * This is where the whole magic starts.
	 * We initialize firePHP and ourselves to be the logging destination for all debug data.
	 * We also read our configuration and bring everything into a defined state.
	 */
	public static function startUp()
	{
		if($GLOBALS['TL_CONFIG']['enableDebug'] && !self::$fb)
		{
			$mayUseDebugger=false;
			// pre checks if debugging is allowed.
			if(BE_USER_LOGGED_IN && array_key_exists('enableDebugUser', $GLOBALS['TL_CONFIG']) && strlen($GLOBALS['TL_CONFIG']['enableDebugUser']))
			{
				$uid=self::getLoginStatus('BE_USER_AUTH');
				if($uid && count(array_intersect(deserialize($GLOBALS['TL_CONFIG']['enableDebugUser']), array($uid))))
					$mayUseDebugger=true;
			}
			if($mayUseDebugger || (FE_USER_LOGGED_IN && array_key_exists('enableDebugMember', $GLOBALS['TL_CONFIG']) && strlen($GLOBALS['TL_CONFIG']['enableDebugMember'])))
			{
				$uid=self::getLoginStatus('FE_USER_AUTH');
				if($uid && count(array_intersect(deserialize($GLOBALS['TL_CONFIG']['enableDebugMember']), array($uid))))
					$mayUseDebugger=true;
			}
			if(!$mayUseDebugger)
				return;

			// starting up.
			require_once(TL_ROOT . '/system/modules/debug/FirePHPCore/FirePHP.class.php');
			$fb=FirePHP::getInstance(true);
			self::$fb=$fb;
			if($fb->detectClientExtension())
			{
				// native encoding dumps way too many notices. Very bad when within error handler, as we can not capture it then.
				$fb->setOptions(array('useNativeJsonEncode'=>false));
				$fb->setEnabled(true);
				set_error_handler(array('TYPOlightDebug','errorHandler'));
				error_reporting(E_ALL);
				ini_set('display_errors', true);
				register_shutdown_function('debug_shutdown');
				$fb->registerExceptionHandler();
				if(array_key_exists('logErrors', $GLOBALS['TL_CONFIG']))
				{
					foreach(deserialize($GLOBALS['TL_CONFIG']['logErrors']) as $k=>$v)
						self::$log_severity=self::$log_severity + $v;
				}
				$GLOBALS['TL_DEBUG'] = new TYPOlightDebugArray((array_key_exists('TL_DEBUG', $GLOBALS) ? $GLOBALS['TL_DEBUG'] : array()));
				$GLOBALS['TL_CONFIG']['debugMode']=true;
				$GLOBALS['TL_CONFIG'] = new TYPOlightDebugConfig((array_key_exists('TL_CONFIG', $GLOBALS) ? $GLOBALS['TL_CONFIG'] : array()));

				// check if gZip is active and usable (will get used by Template class). If it is not in use, we have to put an output handler into place to prevent headers from being sent too early.
				if (!($GLOBALS['TL_CONFIG']['enableGZip'] && (in_array('gzip', $arrEncoding) || in_array('x-gzip', $arrEncoding)) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')))
				{
					ob_start();
					self::$ob_started=true;
				}

				//$fb->skipClassInTrace('Database_Statement->debugQuery');
	
				if(array_key_exists('hideCoreNotices', $GLOBALS['TL_CONFIG']) && $GLOBALS['TL_CONFIG']['hideCoreNotices'])
				{
					self::skipNoticesInFile(TL_ROOT.'/system/functions.php');
					self::skipNoticesInFile(TL_ROOT.'/system/initialize.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/Controller.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/Database.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/Input.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/Session.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/Search.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/System.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/Template.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/Widget.php');
					self::skipNoticesInFile(TL_ROOT.'/system/libraries/Environment.php');
					self::skipNoticesInFile(TL_ROOT.'/system/modules/backend');
					self::skipNoticesInFile(TL_ROOT.'/system/modules/frontend');
					self::skipNoticesInFile(TL_ROOT.'/system/drivers/DC_File.php');
					self::skipNoticesInFile(TL_ROOT.'/system/drivers/DC_Table.php');
					
					self::skipNoticesInFile(TL_ROOT.'/system/modules/registration/ModuleRegistration.php');
	
					self::skipNoticesInFile(TL_ROOT.'/system/modules/memberlist/dca/tl_member.php');
				}

				if(array_key_exists('showNotices', $GLOBALS['TL_CONFIG']) && $GLOBALS['TL_CONFIG']['showNotices'])
				{
					self::$showNotices=deserialize($GLOBALS['TL_CONFIG']['showNotices']);
				}

				$fb->setObjectFilter('DB_Mysqli_Result', array('resResult'));
				//$fb->setObjectFilter('Environment', array('*'));
				
				if(array_key_exists('logHooks', $GLOBALS['TL_CONFIG']) && $GLOBALS['TL_CONFIG']['logHooks'])
				{
					$hooks = (array_key_exists('logHookSelection', $GLOBALS['TL_CONFIG']) && $GLOBALS['TL_CONFIG']['logHookSelection']) ? deserialize($GLOBALS['TL_CONFIG']['logHookSelection']) : array();
					foreach(array_merge(array_keys($GLOBALS['TL_HOOKS']), $hooks) as $k)
					{
						$GLOBALS['TL_HOOKS'][$k][]=array('TYPOlightDebugHookCatcher', $k);
					}
				}
				// add the clean up hooks
				$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][]=array('TYPOlightDebug', 'ProcessDebugData');
				$GLOBALS['TL_HOOKS']['outputBackendTemplate'][]=array('TYPOlightDebug', 'ProcessDebugData');

				self::group('Execution evironment');
				$processUser = posix_getpwuid(posix_geteuid());$processUser=$processUser['name'];
				$scriptUser = get_current_user();
				if($processUser != $scriptUser)
					self::warn('Script owner: ' . $scriptUser . ' executed as: ' . $processUser );
				else
					self::info('Script owner: ' . $scriptUser . ' executed as: ' . $processUser);
				self::info((isset($_GET) && count($_GET) ? $_GET : NULL), '$_GET data');
				self::info((isset($_POST) && count($_POST) ? $_POST : NULL), '$_POST data');
				self::info((isset($_SESSION) && count($_SESSION) ? $_SESSION : NULL), '$_SESSION data');
				$const=get_defined_constants(true);
				self::info($const['user'], 'CONST(app context)');
				self::groupEnd();
				self::log('TYPOlight debugger active');
				// finally set up the tick counter.
				self::$ticks=0;
				register_tick_function(array('TYPOlightDebug', 'tick_handler'));
				declare(ticks=1);
			} else {
				// no firePHP present, hide the debug output, in future we will need a workaround for this (display debugdata in an iframe or something like that).
				$GLOBALS['TL_CONFIG']['debugMode']=false;
			}
		}
	}

	public static function tick_handler()
	{
		 self::$ticks++;
	}

	public static function skipClassInTrace($classname)
	{
		self::$fb->skipClassInTrace($classname);
	}

	public static function skipFileInTrace($filename)
	{
		self::$fb->skipFileInTrace($filename);
	}

	public static function skipNoticesInFile($filename)
	{
		self::$skipFiles[]=$filename;
	}

	protected static function checkSize()
	{
		if(self::$sizeexceeded)
			return false;
		$hdr=headers_list();
		$size=0;
		foreach($hdr as $v)
		{
			$size+=strlen($v);
		}
		self::$size=$size;
		if(self::$size>=self::$maxsize)
		{
			self::$fb->warn('Debug data too big, used '.self::$size.' of '.self::$maxsize.' allowed, logging stopped.');
			self::$sizeexceeded=true;
		}
		return !self::$sizeexceeded;
	}

	public static function dump($key, $data)
	{
		if(self::checkSize())
		{
			self::$fb->dump($key, $data);
		}
	}

	protected static function dispatch($method, $message, $label=NULL, $addtrace=false)
	{
		if(self::checkSize())
		{
			if(!in_array($method, array('log', 'info', 'warn', 'error')))
				$method = 'log';
			if(is_bool($label))
			{
				$addtrace=$label;
				$label=NULL;
			}
			if($addtrace && !(self::$inGroup))
				self::$fb->group(($label ? $label : $message), array('Collapsed'=>true));
			self::$fb->info($message, $label);
			if($addtrace)
			{
				self::$fb->trace('{{trace}}');
				if(!(self::$inGroup))
				self::$fb->groupEnd(($label ? $label : $message));
			}
		}
	}

	public static function group($name, $options=false)
	{
		if(self::$inGroup)
			return;
		if($name)
		{
			if(!$options)
				$options=array('Collapsed' => true);
			self::$fb->group($name, $options);
			self::$inGroup=$name;
		}
	}

	public static function groupEnd()
	{
		if(self::$inGroup)
		{
			self::$fb->groupEnd();
			self::$inGroup=false;
		}
	}

	public static function log($message, $label=NULL, $addtrace=false)
	{
		self::dispatch('log', $message, $label, $addtrace);
	}

	public static function info($message, $label=NULL, $addtrace=false)
	{
		self::dispatch('info', $message, $label, $addtrace);
	}

	public static function warn($message, $label=NULL, $addtrace=false)
	{
		self::dispatch('warn', $message, $label, $addtrace);
	}

	public static function error($message, $label=NULL, $addtrace=false)
	{
		self::dispatch('error', $message, $label, $addtrace);
	}


	public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
	{
		// Don't log if error reporting is switched off
		if (error_reporting() == 0)
		{
			return;
		}
		// as PHP stops logging when a custom error handler is active, we force the logging here if it is active.
		if (ini_get('log_errors'))
			error_log(sprintf("PHP %s:  %s in %s on line %d", $errno, $errstr, $errfile, $errline));


		// if we want to filter this error, increment the according counter.
		if(self::filterError($errno, $errstr, $errfile, $errline, $errcontext) || ((self::$log_severity & $errno)===0))
		{
			self::$supressed[$errno]+=1;
			return true;
		}

		// Only log for errors we are asking for.
		if (error_reporting() & $errno)
		{
			$location= ' in ' . $errfile.'::'.$errline;
			switch($errno)
			{
				case E_NOTICE:
				case E_USER_NOTICE:
				case E_STRICT:
					self::log(self::$severity[$errno] . ':' . $errstr . $location, true);
					break;
				case E_CORE_WARNING:
				case E_COMPILE_WARNING: 
				case E_WARNING: 
				case E_USER_WARNING:
				case E_DEPRECATED: 
				case E_USER_DEPRECATED: 
					if(strpos($errstr, 'json_encode()')===false && strpos($errstr, 'recursion detected in '.TL_ROOT.'/plugins/FirePHPCore-0.3.1/lib/FirePHPCore/FirePHP.class.php')===false)
						self::warn(self::$severity[$errno] . ':' . $errstr . $location, true);
					break;
				case E_ERROR:
				case E_USER_ERROR:
				case E_RECOVERABLE_ERROR:
				case E_PARSE:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
					self::error(self::$severity[$errno] . ':' . $errstr . $location, true);
					break;
				default:
					$exception = new ErrorException($errstr, 0, $errno, $errfile, $errline);
					if(self::$fb->throwErrorExceptions) {
						throw $exception;
					} else {
						self::$fb->fb($exception);
					}
			}
			return true;
		}
	}

	protected static $noticeLookup=array(
		'undefinedIndex'=>'Undefined index:', 
		'undefinedOffset'=>'Undefined offset:',
		'propertyNonObject'=>'Trying to get property of non-object',
	);

	protected static function filterError($errno, $errstr, $errfile, $errline, $errcontext)
	{
		$logit=!in_array($errno, array(E_NOTICE,E_USER_NOTICE,));
		if(!$logit && count(self::$showNotices))
		{
			$logit=false;
			// check if this notice type is disabled in the extended notice configuration.
			foreach(self::$showNotices as $v)
			{
				if(strpos($errstr, self::$noticeLookup[$v])!==false)
				{
					$logit=true;
					break;
				}
			}
		}
		return ((!$logit) || in_array($errfile, self::$skipFiles) || in_array(dirname($errfile), self::$skipFiles));
	}
}

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
		$this->ProcessHook('reviseTable ' . $strTable, $params);
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

	/*
	 * generic hook logging facility.
	*/
	protected function ProcessHook($hookname, $params)
	{
		TYPOlightDebug::info($params, 'HOOK::'.$hookname);
	}
	
	/*
	 * generic hook logging facility.
	*/
	public function getHooks()
	{
		$tmp= new TYPOlightDebugDummy();
		$tmp->loadDataContainer('tl_debug');
		$res=array();
		foreach(get_class_methods('TYPOlightDebugHookCatcher') as $v)
		{
			if(!in_array($v, array('__call','ProcessHook','getHooks')))
			{
				$res[$v]=((array_key_exists($v, $GLOBALS['TL_LANG']['tl_debug']['logHookNames']) && $GLOBALS['TL_LANG']['tl_debug']['logHookNames'][$v]) ? $GLOBALS['TL_LANG']['tl_debug']['logHookNames'][$v] : $v);
			}
		}
		return $res;
	}
}

class TYPOlightDebugDummy extends Backend
{
	public function __construct()
	{
		parent::__construct();
	}
	public function loadDataContainer($name)
	{
		parent::loadDataContainer($name);
	}
}

function debug_shutdown()
{
	TYPOlightDebug::shutDown();
}

?>