<?php
namespace usermap\system\menu\user;
use wcf\system\menu\user\DefaultUserMenuItemProvider;
use wcf\system\WCF;

/**
 * UserMenuItemProvider for Usermap
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapUserMenuItemProvider extends DefaultUserMenuItemProvider {
	/**
	 * @inheritDoc
	 */
	public function isVisible() {
		if (WCF::getSession()->getPermission('user.usermap.canUseUsermap')) return true;
		return false;
	}
}
