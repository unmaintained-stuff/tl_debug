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

$GLOBALS['TL_DCA']['tl_debug'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'File',
		'closed'                      => true
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array(),
		'default'                     => '{base_legend},enableDebug,enableDebugMember,enableDebugUser;{debugdata_legend},hideCoreNotices,logErrors,showNotices,logHooks,logHookSelection,logDatabase,logDatabaseModules'
	),

	// Subpalettes
	'subpalettes' => array
	(
	),

	// Fields
	'fields' => array
	(
		'enableDebug' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['enableDebug'],
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'')
		),
		'enableDebugMember' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['enableDebugMember'],
			'inputType'               => 'checkbox',
			'foreignKey'              => 'tl_member.username',
			'eval'                    => array('multiple' => true, 'tl_class'=>'')
		),
		'enableDebugUser' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['enableDebugUser'],
			'inputType'               => 'checkbox',
			'foreignKey'              => 'tl_user.name',
			'eval'                    => array('multiple' => true, 'tl_class'=>'')
		),

		'hideCoreNotices' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['hideCoreNotices'],
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'')
		),
		'showNotices' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['showNotices'],
			'inputType'               => 'checkbox',
			'options'                 => array('undefinedIndex', 'propertyNonObject', 'undefinedOffset'),
			'eval'                    => array('multiple' => true, 'tl_class'=>'')
		),
		'logErrors' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['logErrors'],
			'inputType'               => 'checkbox',
			'options'                 => array(E_WARNING,E_NOTICE,E_PARSE,E_CORE_ERROR,E_CORE_WARNING,E_COMPILE_ERROR,E_COMPILE_WARNING,E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE,E_STRICT,E_RECOVERABLE_ERROR,E_DEPRECATED,E_USER_DEPRECATED),
			'reference'               => $GLOBALS['TL_LANG']['tl_debug']['severity'],
			'eval'                    => array('multiple' => true, 'tl_class'=>'clr')
		),
		'logHooks' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['logHooks'],
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'')
		),
		'logHookSelection' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['logHookSelection'],
			'inputType'               => 'checkbox',
			'options_callback'         => array('TYPOlightDebugHookCatcher','getHooks'),
			'eval'                    => array('multiple' => true, 'tl_class'=>'')
		),
		'logDatabase' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['logDatabase'],
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class'=>'')
		),

		'logDatabaseModules' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['logDatabaseModules'],
			'inputType'               => 'checkbox',
			'options_callback'        => array('tl_debug', 'getModules'),
			'eval'                    => array('multiple'=>true)
		),
		'adminEmail' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_debug']['adminEmail'],
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'rgxp'=>'friendly', 'decodeEntities'=>true, 'tl_class'=>'w50')
		),
	)
);

/**
 * Class tl_debug
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Cyberspectrum 2010
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @package    Controller
 */
class tl_debug extends Backend
{
	/**
	 * Return all modules as array
	 * @return array
	 */
	public function getModules()
	{
		$arrReturn = array();
		$arrInactiveModules = deserialize($GLOBALS['TL_CONFIG']['inactiveModules']);
		$blnCheckInactiveModules = is_array($arrInactiveModules);
		$arrModules = scan(TL_ROOT . '/system/modules');
		foreach ($arrModules as $strModule)
		{
			if((substr($strModule, 0, 1) == '.')
				|| (substr($strModule, -5, 5) == 'debug')
				|| (!is_dir(TL_ROOT . '/system/modules/' . $strModule))
				|| ($blnCheckInactiveModules && in_array($strModule, $arrInactiveModules)))
			{
				continue;
			}
			$label = 
				(array_key_exists($strModule, $GLOBALS['TL_LANG']['MOD']) && is_array($GLOBALS['TL_LANG']['MOD'][$strModule]))
				? $GLOBALS['TL_LANG']['MOD'][$strModule][0]
				: ((array_key_exists($strModule, $GLOBALS['TL_LANG']['MOD']) && $GLOBALS['TL_LANG']['MOD'][$strModule]) ? $GLOBALS['TL_LANG']['MOD'][$strModule] : '');
			$arrReturn[$strModule] = strlen($label)	? $label : $strModule;
		}
		natcasesort($arrReturn);
		$arrReturn=array('core' => 'Core')+$arrReturn;
		return $arrReturn;
	}
}

?>