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
namespace usermap\system\option;

use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\AbstractOptionType;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;

use const SORT_ASC;

/**
 * Option type implementation for single marker selection.
 */
class MarkerSelectOptionType extends AbstractOptionType
{
    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        // get stored markers
        $files = DirectoryUtil::getInstance(USERMAP_DIR . 'images/markers/')->getFiles(SORT_ASC);
        $icons = [];
        $path = WCF::getPath('usermap') . 'images/markers/';
        foreach ($files as $file) {
            if (\is_dir($file)) {
                continue;
            }

            $name = \basename($file);
            $link = '<img src="' . $path . $name . '" >';
            $icons[$name] = $link;
        }

        WCF::getTPL()->assign([
            'icons' => $icons,
            'option' => $option,
            'value' => $value,
        ]);

        return WCF::getTPL()->fetch('markerSelectOptionType', 'usermap');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue)
    {
        if (!empty($newValue)) {
            if (!\file_exists(USERMAP_DIR . 'images/markers/' . $newValue)) {
                throw new UserInputException($option->optionName, 'validationFailed');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue)
    {
        return $newValue;
    }
}
