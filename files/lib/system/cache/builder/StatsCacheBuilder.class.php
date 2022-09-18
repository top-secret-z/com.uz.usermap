<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace usermap\system\cache\builder;

use PDO;
use usermap\data\usermap\UsermapUtils;
use wcf\data\user\UserList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Caches Usermap stats.
 */
class StatsCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $maxLifetime = 600;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
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
        $sql = "SELECT    userID
                FROM    wcf" . WCF_N . "_user_to_group
                " . $conditionBuilder;
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditionBuilder->getParameters());
        $userIDs = $statement->fetchAll(PDO::FETCH_COLUMN);
        $userIDs = \array_unique($userIDs);
        $count = \count($userIDs);
        $data['usersTotal'] = $count;

        if (!$count) {
            $data['usageMap'] = $data['usersMap'] = 0;

            return $data;
        }

        // users with entry
        $data['usersMap'] = 0;
        for ($i = 0; $i < $count; $i += 2500) {
            $ids = \array_slice($userIDs, $i, 2500);

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
            $data['usersMap'] += \count($userList->getObjectIDs());
        }

        // usage
        $data['usageMap'] = \round($data['usersMap'] / $data['usersTotal'] * 100, 1);

        // days since install for further purposes
        $days = \ceil((TIME_NOW - USERMAP_INSTALL_DATE) / 86400);
        if ($days <= 0) {
            $days = 1;
        }

        return $data;
    }
}
