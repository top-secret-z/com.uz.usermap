<?php
namespace usermap\data\usermap;
use usermap\system\cache\builder\UsermapCacheBuilder;
use usermap\system\usermap\geocoder\GoogleMapsGeocoder;
use wcf\data\user\TeamList;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\UserProfile;
use wcf\data\user\group\UserGroupList;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;

/**
 * Action for Usermap
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapAction extends UserAction {
	/**
	 * @inheritDoc
	 */
	protected $allowGuestAccess = ['getMapMarkers', 'search'];
	
	/**
	 * Validates the 'getMapMarkers' action.
	 */
	public function validateGetMapMarkers() {
		// nothing so far
	}
	
	/**
	 * Loads the user markers to be displayed on the map.
	 */
	public function getMapMarkers() {
		$data = UsermapCacheBuilder::getInstance()->getData();
		$users = $data[0];
		
		// online users
		$sql = "SELECT	userID
				FROM	wcf".WCF_N."_session
				WHERE	userID IS NOT NULL";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$onlineIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
		
		$checkOnline = false;
		if (count($onlineIDs)) {
			$checkOnline = true;
			$onlineIDs = array_flip($onlineIDs);
		}
		
		// follower
		$checkFollower = false;
		if (WCF::getUser()->userID) {
			$profile = new UserProfile(WCF::getUser());
			$followerIDs = $profile->getFollowers();
			if (count($followerIDs)) {
				$checkFollower = true;
				$followerIDs = array_flip($followerIDs);
			}
		}
		
		// team list
		$teamIDs = [];
		$teamList = new TeamList();
		$teamList->readObjectIDs();
		$teamIDs = $teamList->getObjectIDs();
		$teamIDs = array_flip($teamIDs);
		
		// usergroups for priorities
		$groupList = new UserGroupList();
		$groupList->sqlOrderBy = 'priority DESC';
		$groupList->readObjects();
		$groups = $groupList->getObjects();
		
		// icons
		$files = DirectoryUtil::getInstance(USERMAP_DIR.'images/markers/')->getFiles(SORT_ASC);
		$icons = [];
		foreach ($files as $file) {
			if (is_dir($file)) continue;
			$name = basename($file);
			$icons[$name] = $name;
		}
		
		$markers = [];
		foreach ($users as $user) {
			// check icon
			$icon = $groups[$user['prioGroup']]->usermapMarker;
			if (!isset($icons[$icon])) $icon = USERMAP_MAP_MAP_MARKER;
			
			$markers[] = [
					'infoWindow' => WCF::getTPL()->fetch('infoWindowUser', 'usermap', [
							'user' => $user
					]),
					'latitude' => $user['usermapLatitude'],
					'longitude' => $user['usermapLongitude'],
					'objectID' => $user['userID'],
					'title' => $user['username'],
					'online' => ($checkOnline && isset($onlineIDs[$user['userID']]) ? 1 : 0),
					'follower' => ($checkFollower && isset($followerIDs[$user['userID']]) ? 1 : 0),
					'team' => (isset($teamIDs[$user['userID']]) ? 1 : 0),
					'groups' => $user['groupIDs'],
					'prioGroup' => $user['prioGroup'],
					'icon' => WCF::getPath('usermap').'images/markers/'.$icon
			];
		}
		
		return ['markers' => $markers];
	}
	
	/**
	 * Validates user search action.
	 */
	public function validateSearch() {
		/* nothing to validate */
	}
	
	/**
	 * search user and location
	 */
	public function search() {
		$username = $this->parameters['username'];
		$location = $this->parameters['location'];
		$data = [];
		$data['icon'] = WCF::getPath('usermap').'images/markers_search/search.png';
		
		if (!empty($username)) {
			$user = User::getUserByUsername($this->parameters['username']);
			if ($user->userID && !empty($user->usermapLocation)) {
				$data['userLat'] = $user->usermapLatitude;
				$data['userLng'] = $user->usermapLongitude;
			}
		}
		
		if (!empty($location)) {
			$geocoder = new GoogleMapsGeocoder(true);
			$result = $geocoder->geocode($location, null, false);
			if ($result) {
				$data['locationLat'] = $result->lat;
				$data['locationLng'] = $result->lng;
			}
		}
		
		return $data;
	}
	
	/**
	 * Validates the marker delete action.
	 */
	public function validateDeleteMarker() {
		/* nothing to validate */
	}
	
	/**
	 * deletes a marker
	 */
	public function deleteMarker() {
		$file = USERMAP_DIR . 'images/markers/' . $this->parameters['filename'];
		if (file_exists($file)) {
			@unlink($file);
		}
	}
}
