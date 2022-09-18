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
namespace usermap\data\usermap\geocache;

use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a geocache entry and provides geocoding functions
 */
class Geocache extends DatabaseObject
{
    /**
     * Returns geocode cache by hash
     */
    public static function getCacheLocation($hash)
    {
        $sql = "SELECT    *
                FROM        usermap" . WCF_N . "_geocache
                WHERE        hash = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$hash]);
        $row = $statement->fetchArray();
        if (!$row) {
            $row = [];
        }

        return new self(null, $row);
    }

    /**
     * Store data in cache
     */
    public static function setCacheLocation($data)
    {
        // limit length of location
        if (\mb_strlen($data['location']) > 255) {
            $data['location'] = \mb_substr($data['location'], 0, 255);
        }

        $sql = "INSERT INTO    usermap" . WCF_N . "_geocache
                            (hash, location, lat, lng, time, type)
                VALUES        (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY
                UPDATE        lat = ?, lng = ?, time = ?, type = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $data['hash'],
            $data['location'],
            $data['lat'],
            $data['lng'],
            $data['time'],
            $data['type'],
            $data['lat'],
            $data['lng'],
            $data['time'],
            $data['type'],
        ]);

        return self::getCacheLocation($data['hash']);
    }
}
