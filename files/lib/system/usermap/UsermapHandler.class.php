<?php
namespace usermap\system\usermap;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\SingletonFactory;

/**
 * Core functions for usermap.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapHandler extends SingletonFactory {
	/**
	 * Returns user object.
	 */
	public function getUserById($userID) {
		return UserRuntimeCache::getInstance()->getObject($userID);
	}
}
