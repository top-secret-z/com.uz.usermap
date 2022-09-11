<?php
namespace usermap\data\usermap\geocache;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of geocache entries.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class GeocacheList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = Geocache::class;
}
