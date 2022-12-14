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
namespace usermap\system\usermap\geocoder;

use Exception;
use usermap\data\usermap\log\Log;
use usermap\data\usermap\log\LogEditor;
use wcf\util\JSON;

/**
 * Geocoder implementation for Nominatim.
 */
class NominatimGeocoder extends AbstractGeocoder
{
    /**
     * Geocoder data
     */
    protected $gecodingUrl = 'https://nominatim.openstreetmap.org/search?format=jsonv2&q=%s&limit=%d';

    protected $requestsPerSecond = 1;

    protected $limit = 1;

    protected $wait = true;

    public function __construct($wait = true)
    {
        $this->wait = $wait;
    }

    /**
     * Geocode a location
     */
    public function geocode($location, $user = null)
    {
        // try cache first
        $cache = $this->checkCache($location);
        if ($cache->geocacheID) {
            return $cache;
        }

        // request
        $url = \sprintf($this->gecodingUrl, \rawurlencode($location), $this->limit);
        $reply = $this->executeRequest($url);
        if (empty($reply)) {
            if (USERMAP_LOG_ENABLE) {
                LogEditor::create([
                    'log' => 'usermap.acp.log.notFound',
                    'remark' => $location,
                    'status' => Log::STATUS_WARNING,
                    'userID' => !$user ? '' : $user->userID,
                    'username' => !$user ? '' : $user->username,
                ]);
            }

            return null;
        }

        // analyse
        $result = JSON::decode($reply);
        try {
            if (!isset($result[0])) {
                if (USERMAP_LOG_ENABLE) {
                    LogEditor::create([
                        'log' => 'usermap.acp.log.notFound',
                        'remark' => $location,
                        'status' => Log::STATUS_WARNING,
                        'userID' => !$user ? '' : $user->userID,
                        'username' => !$user ? '' : $user->username,
                    ]);
                }

                return null;
            } else {
                $hash = \md5($location);
                $data = [
                    'hash' => $hash,
                    'lat' => \round(\floatval($result[0]['lat']), 6),
                    'lng' => \round(\floatval($result[0]['lon']), 6),
                    'location' => $location,
                    'time' => TIME_NOW,
                    'type' => 0,
                ];
                $geoLocation = $result[0]['display_name'] ?? $location;
            }
        } catch (Exception $e) {
            if (USERMAP_LOG_ENABLE) {
                LogEditor::create([
                    'log' => 'usermap.acp.log.error.unknown',
                    'remark' => $e->getMessage(),
                    'status' => Log::STATUS_ERROR,
                ]);
            }

            return null;
        }

        $result = $this->setCache($data);

        // extend cache by display_name
        if ($location != $geoLocation) {
            $data['location'] = $geoLocation;
            $data['hash'] = \md5($geoLocation);

            $this->setCache($data);
        }

        // wait if required
        if ($this->wait) {
            $this->waitAfter($this->requestsPerSecond);
        }

        // finally
        return $result;
    }
}
