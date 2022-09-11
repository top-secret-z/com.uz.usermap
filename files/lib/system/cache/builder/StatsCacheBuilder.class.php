<?php
namespace usermap\system\cache\builder;
use usermap\data\usermap\UsermapUtils;
use wcf\data\user\UserList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches Usermap stats.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class StatsCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @inheritDoc
	 */
	protected $maxLifetime = 600;
	
	/**
	 * @inheritDoc
	 */
	protected function rebuild(array $parameters) {
		$data = [];
		
		// groups to be displayed
		$groupIDs = UsermapUtils::getDisplayGroupIDs();
		if (empty($groupIDs)) {
			$data['usersTotal'] = $data['usageMap'] = $data['usersMap'] = 0;
			return $data;
		}
		
		// total users
		$conditionBuilder = new PreparedStatementConditionBuilder();
		$conditionBuilder->add('groupID IN (?)', [$groupIDs]);
		$sql = "SELECT	userID
				FROM	wcf".WCF_N."_user_to_group
				".$conditionBuilder;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditionBuilder->getParameters());
		$userIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
		$userIDs = array_unique($userIDs);
		$count = count($userIDs);
		$data['usersTotal'] = $count;
		
		if (!$count) {
			$data['usageMap'] = $data['usersMap'] = 0;
			return $data;
		}
		
		// users with entry
		$data['usersMap'] = 0;
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
			$userList->readObjectIDs();
			$data['usersMap'] += count($userList->getObjectIDs());
		}
		
		// usage
		$data['usageMap'] = round($data['usersMap'] / $data['usersTotal'] * 100, 1);
		
		// days since install for further purposes
		$days = ceil((TIME_NOW - USERMAP_INSTALL_DATE) / 86400);
		if ($days <= 0) $days = 1;
		
		return $data;
	}
}
