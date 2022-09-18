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
namespace usermap\system\event\listener;

use usermap\data\usermap\log\Log;
use usermap\data\usermap\log\LogEditor;
use usermap\system\cache\builder\UsermapCacheBuilder;
use usermap\system\usermap\geocoder\GoogleMapsGeocoder;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\system\event\listener\IParameterizedEventListener;

/**
 * Listens to User add / registration
 */
class UserAddListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        // check module
        if (!MODULE_USERMAP) {
            return;
        }

        // get user with actual data
        $user = User::getUserByUsername($eventObj->username);

        // try usermap field first
        $location = $user->getUserOption('usermapField');

        if (empty($location)) {
            // no synchronization, no map entry
            if (!USERMAP_DATA_SYNCHRONIZE_FIELDS_ENABLE || empty(USERMAP_DATA_SYNCHRONIZE_WITH)) {
                return;
            }

            $options = \explode(',', USERMAP_DATA_SYNCHRONIZE_WITH);
            $temp = [];
            foreach ($options as $option) {
                $value = $user->getUserOption(\trim($option));
                if (!empty($value)) {
                    $temp[] = $value;
                }
            }
            if (!\count($temp)) {
                return;
            }

            $location = \implode(', ', $temp);
        }

        $geocoder = new GoogleMapsGeocoder(false);
        $geocache = $geocoder->geocode($location, $user);
        if (!$geocache) {
            return;
        }

        // update user and log
        $userEditor = new UserEditor($user);
        $userEditor->update([
            'usermapLatitude' => $geocache->lat,
            'usermapLongitude' => $geocache->lng,
            'usermapLocation' => $location,
            'usermapTime' => TIME_NOW,
        ]);

        $optionID = User::getUserOptionID('usermapField');
        $userEditor->updateUserOptions([$optionID => $location]);

        if (USERMAP_LOG_ENABLE) {
            LogEditor::create([
                'log' => 'usermap.acp.log.sync.new',
                'remark' => $location,
            ]);
        }

        // reset cache
        UsermapCacheBuilder::getInstance()->reset();
    }
}
