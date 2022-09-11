<?php
namespace usermap\data\usermap\log;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of log entries.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.log
 */
class LogList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = Log::class;
}
