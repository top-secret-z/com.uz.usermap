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
namespace wcf\system\option;

use PDO;
use wcf\data\option\Option;
use wcf\system\WCF;

/**
 * Option type implementation for usermap location fields selection.
 */

/**
 * Sligthly modified copy of:
 */
class UsermapOptionType extends AbstractOptionType
{
    /**
     * list of available user options
     * @var    string[]
     */
    protected static $userOptions;

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue)
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }

        foreach ($newValue as $optionName) {
            if (!\in_array($optionName, self::getUserOptions())) {
                throw new UserInputException($option->optionName, 'validationFailed');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue)
    {
        if (!\is_array($newValue)) {
            return '';
        }

        return \implode(',', $newValue);
    }

    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        $userOptions = self::getUserOptions();
        if ($option->issortable && $value) {
            $sortedOptions = \explode(',', $value);

            // remove old options
            $sortedOptions = \array_intersect($sortedOptions, $userOptions);

            // append the non-checked options after the checked and sorted options
            $userOptions = \array_merge($sortedOptions, \array_diff($userOptions, $sortedOptions));
        }

        WCF::getTPL()->assign([
            'option' => $option,
            'value' => \explode(',', $value),
            'availableOptions' => $userOptions,
        ]);

        return WCF::getTPL()->fetch('useroptionsOptionType');
    }

    /**
     * Returns the list of available user options.
     *
     * @return    string[]
     */
    protected static function getUserOptions()
    {
        if (self::$userOptions === null) {
            self::$userOptions = [];
            $sql = "SELECT    optionName
                FROM    wcf" . WCF_N . "_user_option
                WHERE    categoryName IN (
                        SELECT    categoryName
                        FROM    wcf" . WCF_N . "_user_option_category
                        WHERE    parentCategoryName = 'profile'
                    )
                    AND optionType <> 'boolean' AND optionName <> 'usermapField'";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute();
            self::$userOptions = $statement->fetchAll(PDO::FETCH_COLUMN);
        }

        return self::$userOptions;
    }
}
