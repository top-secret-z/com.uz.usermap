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
namespace usermap\system;

use usermap\page\UsermapPage;
use wcf\system\application\AbstractApplication;
use wcf\system\page\PageLocationManager;
use wcf\system\WCF;

/**
 * This class extends the main WCF class by Usermap specific functions.
 */
class USERMAPCore extends AbstractApplication
{
    /**
     * @inheritDoc
     */
    protected $primaryController = UsermapPage::class;

    /**
     * Sets the location data.
     */
    public function setLocation(array $categories = [])
    {
        $categories = \array_reverse($categories);
        foreach ($categories as $category) {
            PageLocationManager::getInstance()->addParentLocation('com.uz.usermap.Usermap', $category->categoryID, $category);
        }

        PageLocationManager::getInstance()->addParentLocation('com.uz.usermap.Usermap');
    }
}
