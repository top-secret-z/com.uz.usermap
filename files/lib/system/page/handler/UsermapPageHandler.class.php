<?php
namespace usermap\system\page\handler;
use wcf\system\page\handler\AbstractMenuPageHandler;
use wcf\system\WCF;

/**
 * Page handler for Usermap.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapPageHandler extends AbstractMenuPageHandler {
	/**
	 * @inheritDoc
	 */
	public function getOutstandingItemCount($objectID = null) {
		return 0;
	}
	
	/**
	 * @inheritDoc
	 */
	public function isVisible($objectID = null) {
		if (!MODULE_USERMAP) return false;
		
		return WCF::getSession()->getPermission('user.usermap.canSeeUsermap') ? true : false;
	}
}
