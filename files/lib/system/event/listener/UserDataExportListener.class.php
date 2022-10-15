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

use wcf\system\event\listener\IParameterizedEventListener;

/**
 * Exports user data iaw Gdpr.
 */
class UserDataExportListener implements IParameterizedEventListener
{
    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        // exclude one option and add location data in user
        $eventObj->skipUserOptions[] = 'usermapAllowEntry';
        $eventObj->exportUserProperties[] = 'usermapLatitude';
        $eventObj->exportUserProperties[] = 'usermapLongitude';
        $eventObj->exportUserProperties[] = 'usermapLocation';
        $eventObj->exportUserProperties[] = 'usermapTime';
    }
}
