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
use wcf\system\exception\NamedUserException;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;
use wcf\util\StringUtil;

use const SORT_ASC;

/**
 * Display additional category data.
 */
class CategoryFormListener implements IParameterizedEventListener
{
    /**
     * instance of CategoryAddForm
     */
    protected $eventObj;

    /**
     * marker data
     */
    protected $markers = [];

    protected $selectedMarker = '';

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        $this->eventObj = $eventObj;

        $this->{$eventName}();
    }

    /**
     * Handles the assignVariables event.
     */
    protected function assignVariables()
    {
        $files = DirectoryUtil::getInstance(USERMAP_DIR . 'images/markers_poi/')->getFiles(SORT_ASC);
        if (empty($files)) {
            throw new NamedUserException(WCF::getLanguage()->get('usermap.acp.error.category.noIcons'));
        }

        $path = WCF::getPath('usermap') . 'images/markers_poi/';
        $first = '';
        foreach ($files as $file) {
            if (\is_dir($file)) {
                continue;
            }
            $name = \basename($file);
            if (empty($first)) {
                $first = $name;
            }
            $link = '<img src="' . $path . $name . '" height="30" alt="' . $name . '">';
            $this->markers[$name] = $link;
        }

        // selected marker is either stored or first in row
        if (!empty($this->eventObj->additionalData['marker']) && isset($this->markers[$this->eventObj->additionalData['marker']])) {
            $this->selectedMarker = $this->eventObj->additionalData['marker'];
        } else {
            $this->selectedMarker = $first;
        }

        WCF::getTPL()->assign([
            'markers' => $this->markers,
            'selectedMarker' => $this->selectedMarker,
        ]);
    }

    /**
     * Handles the readData event (CategoryEditForm only).
     */
    protected function readData()
    {
        if (empty($_POST)) {
            $this->selectedMarker = $this->eventObj->category->additionalData['marker'];
        }
    }

    /**
     * Handles the readFormParameters event.
     */
    protected function readFormParameters()
    {
        if (isset($_POST['selectedMarker'])) {
            $this->selectedMarker = StringUtil::trim($_POST['selectedMarker']);
        }
    }

    /**
     * Handles the save event.
     */
    protected function save()
    {
        $this->eventObj->additionalData['marker'] = $this->selectedMarker;
    }
}
