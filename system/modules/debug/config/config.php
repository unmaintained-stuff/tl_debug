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

// since PHP5
if (!defined('E_STRICT'))define('E_STRICT', 2048);
// since PHP 5.2
if (!defined('E_RECOVERABLE_ERROR'))define('E_RECOVERABLE_ERROR', 4096);
// since PHP 5.3
if (!defined('E_DEPRECATED'))define('E_DEPRECATED', 8192);
if (!defined('E_USER_DEPRECATED'))define('E_USER_DEPRECATED', 16384);

// hook us as module
$GLOBALS['BE_MOD']['development']['debugger'] = array
	(
		'tables'			=> array('tl_debug'),
		'icon'				=> 'system/modules/debug/html/bug.png', 
	);
?>