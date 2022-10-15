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
namespace usermap\page;

use usermap\system\cache\builder\StatsCacheBuilder;
use wcf\data\user\group\UserGroup;
use wcf\page\AbstractPage;
use wcf\system\WCF;

/**
 * Shows the Usermap page.
 */
class UsermapPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'usermap.header.menu.usermap';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.usermap.canSeeUsermap'];

    /**
     * @inheritDoc
     */
    public $enableTracking = true;

    /**
     * usermap statistics
     */
    public $stats = [];

    /**
     * available usergroups
     */
    public $availableGroups = [];

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        // stats
        if (USERMAP_INDEX_ENABLE_STATS) {
            $this->stats = StatsCacheBuilder::getInstance()->getData();
        }

        // groups
        $this->availableGroups = UserGroup::getGroupsByType([], [UserGroup::EVERYONE, UserGroup::GUESTS]);
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'stats' => $this->stats,
            'allowSpidersToIndexThisPage' => true,
            'groups' => $this->availableGroups,
        ]);
    }
}
