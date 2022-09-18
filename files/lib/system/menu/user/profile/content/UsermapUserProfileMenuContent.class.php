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
namespace usermap\system\menu\user\profile\content;

use wcf\data\user\User;
use wcf\system\menu\user\profile\content\IUserProfileMenuContent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles user profile usrmap content.
 */
class UsermapUserProfileMenuContent extends SingletonFactory implements IUserProfileMenuContent
{
    /**
     * @inheritDoc
     */
    public function getContent($userID)
    {
        $user = new User($userID);

        // assign map data
        WCF::getTPL()->assign([
            'userID' => $user->userID,
            'username' => $user->username,
            'latitude' => $user->usermapLatitude,
            'longitude' => $user->usermapLongitude,
            'location' => $user->usermapLocation,
            'own' => (WCF::getUser()->userID == $userID ? 1 : 0),
        ]);

        return WCF::getTPL()->fetch('userProfileUsermap', 'usermap');
    }

    /**
     * @inheritDoc
     */
    public function isVisible($userID)
    {
        return true;
    }
}
