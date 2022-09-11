<?php
namespace usermap\data\usermap;
use wcf\data\user\group\UserGroup;
use wcf\data\user\group\UserGroupList;

/**
 * Utility functions for Usermap
 *
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapUtils {
	/**
	 * Get groupIDs to be displayed
	 */
	public static function getDisplayGroupIDs() {
		$list = new UserGroupList();
		$list->getConditionBuilder()->add('usermapShow = ?', [1]);
		$list->getConditionBuilder()->add('groupID != ?', [Usergroup::EVERYONE]);
		$list->getConditionBuilder()->add('groupID != ?', [Usergroup::GUESTS]);
		$list->readObjectIDs();
		return $list->getObjectIDs();
	}
}
