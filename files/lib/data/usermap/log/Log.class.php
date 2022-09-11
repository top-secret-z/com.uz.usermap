<?php
namespace usermap\data\usermap\log;
use wcf\data\DatabaseObject;

/**
 * Represents a log entry
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class Log extends DatabaseObject {
	const STATUS_OK = 0;
	const STATUS_WARNING = 1;
	const STATUS_ERROR = 2;
}
