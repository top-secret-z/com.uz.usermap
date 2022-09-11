<?php
namespace usermap\system\menu\user\profile\content;
use wcf\system\menu\user\profile\content\IUserProfileMenuContent;
use wcf\data\user\User;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles user profile usrmap content.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapUserProfileMenuContent extends SingletonFactory implements IUserProfileMenuContent {
	/**
	 * @inheritDoc
	 */
	public function getContent($userID) {
		$user = new User($userID);
		
		// assign map data
		WCF::getTPL()->assign(array(
				'userID' => $user->userID,
				'username' => $user->username,
				'latitude' => $user->usermapLatitude,
				'longitude' => $user->usermapLongitude,
				'location' => $user->usermapLocation,
				'own' => (WCF::getUser()->userID == $userID ? 1 : 0)
		));
		
		return WCF::getTPL()->fetch('userProfileUsermap', 'usermap');
	}
	
	/**
	 * @inheritDoc
	 */
	public function isVisible($userID) {
		return true;
	}
}
