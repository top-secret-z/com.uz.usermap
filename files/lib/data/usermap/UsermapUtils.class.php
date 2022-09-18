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
namespace usermap\data\usermap;

use wcf\data\user\group\UserGroup;
use wcf\data\user\group\UserGroupList;

/**
 * Utility functions for Usermap
 */
class UsermapUtils
{
    /**
     * Get groupIDs to be displayed
     */
    public static function getDisplayGroupIDs()
    {
        $list = new UserGroupList();
        $list->getConditionBuilder()->add('usermapShow = ?', [1]);
        $list->getConditionBuilder()->add('groupID != ?', [Usergroup::EVERYONE]);
        $list->getConditionBuilder()->add('groupID != ?', [Usergroup::GUESTS]);
        $list->readObjectIDs();

        return $list->getObjectIDs();
    }
}
