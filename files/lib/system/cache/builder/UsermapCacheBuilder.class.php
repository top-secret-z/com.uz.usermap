<?php
namespace usermap\system\cache\builder;
use usermap\data\usermap\UsermapUtils;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\data\user\group\UserGroup;
use wcf\data\user\group\UserGroupList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches Usermap entries; i.e. all users with usermapLocation.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected $maxLifetime = 600;
	
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [];
		$data[0] = [];
		
		// get prio ordered groups
		$list = new UserGroupList();
		$list->getConditionBuilder()->add('usermapShow = ?', [1]);
		$list->sqlOrderBy = 'priority DESC';
		$list->readObjectIDs();
		$prioIDs = $list->getObjectIDs();
		
		// get groups forbidden as filter
		$list = new UserGroupList();
		$list->getConditionBuilder()->add('usermapFilter = ?', [0]);
		$list->readObjectIDs();
		$forbiddenIDs = $list->getObjectIDs();
		
		// groups to be displayed
		$showIDs = UsermapUtils::getDisplayGroupIDs();
		if (empty($showIDs)) return $data;
		
		// get users, their groups to incl. highst prio group
		// get matching userIDs
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('groupID IN (?)', [$showIDs]);
		$sql = "SELECT	userID
				FROM	wcf".WCF_N."_user_to_group
				".$conditionBuilder;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		$userIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
		$userIDs = array_unique($userIDs);
		
		if (empty($userIDs)) return $data;
		
		$count = count($userIDs);
		
		for ($i = 0; $i < $count; $i += 2500) {
			$ids = array_slice($userIDs, $i, 2500);
			
			$userList = new UserList();
			if (!USERMAP_USER_FILTER_BANNED) {
				$userList->getConditionBuilder()->add('user_table.banned = ?', [0]);
			}
			if (!USERMAP_USER_FILTER_DISABLED) {
				$userList->getConditionBuilder()->add('user_table.activationCode = ?', [0]);
			}
			if (USERMAP_USER_FILTER_INACTIVE > 0) {
				$userList->getConditionBuilder()->add('user_table.lastActivityTime > ?', [TIME_NOW - USERMAP_USER_FILTER_INACTIVE * 86400]);
			}
			$userList->getConditionBuilder()->add('user_table.usermapLocation <> ?', ['']);
			$userList->getConditionBuilder()->add('user_table.usermapLatitude <> ?', ['']);
			$userList->getConditionBuilder()->add('user_table.usermapLongitude <> ?', ['']);
			$userList->getConditionBuilder()->add('user_table.userID in (?)', [$ids]);
			$userList->readObjects();
			$users = $userList->getObjects();
			if (count($users)) {
				foreach ($users as $user) {
					// skip upon privacy setting
					$allow = $user->usermapAllowEntry;
					
					if ($allow === null || $allow) {
						// get groupIDs for filter and remove everyone / forbidden
						$groups = $user->getGroupIDs();
						$groups = array_flip($groups);
						unset($groups[Usergroup::EVERYONE]);
						
						// get highest prio group
						$prio = Usergroup::USERS;
						foreach ($prioIDs as $id) {
							if (isset($groups[$id])) {
								$prio = $id;
								break;
							}
						}
						
						// set groups iaw allowed filter groups
						if (count($forbiddenIDs)) {
							foreach ($groups as $key => $value) {
								if (in_array($key, $forbiddenIDs)) {
									unset($groups[$key]);
								}
							}
						}
						
						// store users
						$data[0][] = [
								'userID' => $user->userID,
								'username' => $user->username,
								'usermapLatitude' => $user->usermapLatitude,
								'usermapLongitude' => $user->usermapLongitude,
								'usermapLocation' => $user->usermapLocation,
								'link' => $user->getLink(),
								'groupIDs' => $groups,
								'prioGroup' => $prio
						];
					}
				}
			}
		}
		
		return $data;
	}
}
