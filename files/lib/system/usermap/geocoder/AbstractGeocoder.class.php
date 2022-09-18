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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use usermap\data\usermap\geocache\Geocache;
use usermap\data\usermap\log\Log;
use usermap\data\usermap\log\LogEditor;
use wcf\system\io\HttpFactory;

/**
 * Abstract implementation of a geocoder.
 */
abstract class AbstractGeocoder
{
    /**
     * URL for geocoding
     */
    protected $gecodingUrl = '';

    /**
     * Allowed requests per second
     */
    protected $requestsPerSecond = 1;

    /**
     * limit per request, ufn always 1
     */
    protected $limit = 1;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * Executes HTTP request
     */
    protected function executeRequest($url)
    {
        try {
            $request = new Request('GET', $url);
            $response = $this->getHttpClient()->send($request);
        } catch (ClientExceptionInterface $e) {
            if (USERMAP_LOG_ENABLE) {
                LogEditor::create([
                    'log' => 'usermap.acp.log.connection.error',
                    'remark' => $e->getMessage(),
                    'status' => Log::STATUS_ERROR,
                ]);
            }

            return null;
        }

        if ($response->getStatusCode() != 200) {
            if (USERMAP_LOG_ENABLE) {
                LogEditor::create([
                    'log' => 'usermap.acp.log.connection.error',
                    'remark' => $reply['statusCode'],
                    'status' => Log::STATUS_ERROR,
                ]);
            }

            return null;
        }

        return (string)$response->getBody();
    }

    /**
     *
     * @param string $location
     */
    protected function checkCache($location)
    {
        $hash = \md5($location);

        return Geocache::getCacheLocation($hash);
    }

    /**
     * Add location result to cache
     */
    protected function setCache($result)
    {
        return Geocache::setCacheLocation($result);
    }

    /**
     * Delay execution iaw $requestsPerSecond
     */
    protected function waitAfter($requestsPerSecond)
    {
        $microSec = \ceil(1000000 / $requestsPerSecond) + 50000;
        \usleep($microSec);
    }

    /**
     * getHttpClient
     */
    private function getHttpClient(): ClientInterface
    {
        if (!$this->httpClient) {
            $this->httpClient = HttpFactory::makeClientWithTimeout(5);
        }

        return $this->httpClient;
    }
}
