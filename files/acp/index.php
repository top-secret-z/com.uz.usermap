<?php
/**
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */

require_once('./global.php');
wcf\system\request\RequestHandler::getInstance()->handle('usermap', true);
