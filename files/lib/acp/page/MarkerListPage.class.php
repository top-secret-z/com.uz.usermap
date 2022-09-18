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
namespace usermap\acp\page;

use wcf\data\user\group\UserGroupList;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;

use const SORT_ASC;

/**
 * Shows the marker list page.
 */
class MarkerListPage extends AbstractPage
{
    /**
     * marker data
     */
    protected $markers = [];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'usermap.acp.menu.link.usermap.marker.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.usermap.canManage'];

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_USERMAP'];

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        // used markers
        $usedMarkers = $markerToGroup = [];
        $groupList = new UserGroupList();
        $groupList->readObjects();
        $groups = $groupList->getObjects();
        foreach ($groups as $group) {
            $usedMarkers[] = $group->usermapMarker;
            if (!isset($markerToGroup[$group->usermapMarker])) {
                $markerToGroup[$group->usermapMarker] = WCF::getLanguage()->get($group->groupName);
            } else {
                $markerToGroup[$group->usermapMarker] .= ', ' . WCF::getLanguage()->get($group->groupName);
            }
        }
        $usedMarkers = \array_unique($usedMarkers);

        // available markers
        $files = DirectoryUtil::getInstance(USERMAP_DIR . 'images/markers/')->getFiles(SORT_ASC);
        $path = WCF::getPath('usermap') . 'images/markers/';
        foreach ($files as $file) {
            if (\is_dir($file)) {
                continue;
            }
            $name = \basename($file);
            $link = '<img src="' . $path . $name . '" alt="' . $name . '">';
            $temp = \getimagesize($file);
            $size = $temp[0] . ' x ' . $temp[1];

            $used = 0;
            $groups = '';
            if (\in_array($name, $usedMarkers)) {
                $used = 1;
                $groups = $markerToGroup[$name];
            }

            $this->markers[] = [
                'name' => $name,
                'link' => $link,
                'used' => $used,
                'size' => $size,
                'groups' => $groups,
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'markers' => $this->markers,
        ]);
    }
}
