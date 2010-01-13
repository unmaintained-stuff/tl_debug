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

// an array for setting up debugging in certain files for database queries.
// This is an example for logging all database queries from within index.php
$GLOBALS['TL_DEBUGGER']['DATABASE'][]='/index.php';
$GLOBALS['TL_DEBUGGER']['DATABASE'][]='/system/modules/frontend/PageRegular.php';
$GLOBALS['TL_DEBUGGER']['DATABASE'][]='/system/libraries/User.php';

$GLOBALS['TL_DEBUGGER']['TRACE'][]='/index.php';

///////////////////////////////////////////////////////////////////////////////
// no changes below here. We need to have the startup here.
///////////////////////////////////////////////////////////////////////////////


TYPOlightDebug::startUp();

?>