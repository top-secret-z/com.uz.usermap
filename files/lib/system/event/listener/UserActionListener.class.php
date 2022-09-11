<?php
namespace usermap\system\event\listener;
use usermap\data\usermap\geocache\Geocache;
use usermap\system\cache\builder\UsermapCacheBuilder;
use usermap\system\usermap\geocoder\GoogleMapsGeocoder;
use usermap\data\usermap\log\Log;
use usermap\data\usermap\log\LogEditor;
use wcf\data\user\User;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;

/**
 * Listens to User actions to update Usermap
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UserActionListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// check module
		if (!MODULE_USERMAP) return;
		
		// user deletion
		if ($eventObj->getActionName() == 'delete') {
			// reset cache
			UsermapCacheBuilder::getInstance()->reset();
		}
		
		// user update
		if ($eventObj->getActionName() == 'update') {
			// get data
			$objects = $eventObj->getObjects();
			$params = $eventObj->getParameters();
			
			// skip if UsermapUserInputForm
			if (isset($params['skipGeocoding']) && $params['skipGeocoding']) return;
			
			// skip if no relevant user action
			$userOptionID = User::getUserOptionID('usermapField');
			if ($userOptionID === null) return;
			if (!isset($params['options'][$userOptionID])) return;
			
			// user
			if (empty($objects)) return;
			$userEditor = $objects[0];
			$user = $userEditor->getDecoratedObject();
			
			// skip if user itself without permission
			if (WCF::getUser()->userID == $user->userID && !WCF::getSession()->getPermission('user.usermap.canUseUsermap')) return;
			
			// new value,commit changes
			$newUserMap = $params['options'][$userOptionID];
			
			if ($newUserMap != $user->usermapLocation) {
				if (empty($newUserMap)) {
					// delete location
					$userEditor->update([
							'usermapLatitude' => 0.0,
							'usermapLongitude' => 0.0,
							'usermapLocation' => '',
							'usermapTime' => 0
					]);
					
					if (USERMAP_LOG_ENABLE) {
						LogEditor::create([
								'log' => 'usermap.acp.log.sync.delete',
								'userID' => $user->userID,
								'username' => $user->username
						]);
					}
				}
				else {
					// geocode with google
					$geocoder = new GoogleMapsGeocoder(false);
					$geocache = $geocoder->geocode($newUserMap, $user);
					if (!$geocache) return;
					
					// update user and log
					$userEditor->update([
							'usermapLatitude' => $geocache->lat,
							'usermapLongitude' => $geocache->lng,
							'usermapLocation' => $newUserMap,
							'usermapTime' => TIME_NOW
					]);
					
					if (USERMAP_LOG_ENABLE) {
						LogEditor::create([
								'log' => 'usermap.acp.log.sync.new',
								'remark' => $newUserMap,
								'userID' => $user->userID,
								'username' => $user->username
						]);
					}
				}
				
				// reset cache
				UsermapCacheBuilder::getInstance()->reset();
			}
		}
	}
}
