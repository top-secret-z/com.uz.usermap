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
namespace usermap\system\condition;

use wcf\data\condition\Condition;
use wcf\data\DatabaseObjectList;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\system\condition\AbstractSingleFieldCondition;
use wcf\system\condition\IContentCondition;
use wcf\system\condition\IObjectListCondition;
use wcf\system\condition\IUserCondition;
use wcf\system\condition\TObjectListUserCondition;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Condition implementation for the state (map entry yes/no) of a user.
 */
class UsermapStateCondition extends AbstractSingleFieldCondition implements IContentCondition, IObjectListCondition, IUserCondition
{
    use TObjectListUserCondition;

    /**
     * @inheritDoc
     */
    protected $label = 'wcf.user.condition.usermap.state';

    /**
     * true if the the user has entry / no entry
     */
    protected $userHasEntry = 0;

    protected $userHasNotEntry = 0;

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof UserList)) {
            throw new ParentClassException(\get_class($objectList), UserList::class);
        }

        if (isset($conditionData['userHasEntry'])) {
            $objectList->getConditionBuilder()->add('user_table.usermapLocation > ?', ['']);
        }
        if (isset($conditionData['userHasNotEntry'])) {
            $objectList->getConditionBuilder()->add('user_table.usermapLocation = ?', ['']);
        }
    }

    /**
     * @inheritDoc
     */
    public function checkUser(Condition $condition, User $user)
    {
        if ($condition->userHasEntry !== null && $user->usermapLocation == '') {
            return false;
        }

        if ($condition->userHasNotEntry !== null && $user->usermapLocation != '') {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = [];

        if ($this->userHasEntry) {
            $data['userHasEntry'] = 1;
        }
        if ($this->userHasNotEntry) {
            $data['userHasNotEntry'] = 1;
        }

        if (!empty($data)) {
            return $data;
        }

        return null;
    }

    /**
     * Returns the "checked" attribute for an input element.
     */
    protected function getCheckedAttribute($propertyName)
    {
        if ($this->{$propertyName}) {
            return ' checked';
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    protected function getFieldElement()
    {
        $userHasNotEntry = WCF::getLanguage()->get('wcf.user.condition.usermap.state.hasNotEntry');
        $userHasEntry = WCF::getLanguage()->get('wcf.user.condition.usermap.state.hasEntry');

        return <<<HTML
<label><input type="checkbox" name="userHasEntry" value="1"{$this->getCheckedAttribute('userHasEntry')}> {$userHasEntry}</label>
<label><input type="checkbox" name="userHasNotEntry" value="1"{$this->getCheckedAttribute('userHasNotEntry')}> {$userHasNotEntry}</label>
HTML;
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        if (isset($_POST['userHasEntry'])) {
            $this->userHasEntry = 1;
        }
        if (isset($_POST['userHasNotEntry'])) {
            $this->userHasNotEntry = 1;
        }
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->userHasEntry = 0;
        $this->userHasNotEntry = 0;
    }

    /**
     * @inheritDoc
     */
    public function setData(Condition $condition)
    {
        if ($condition->userHasEntry !== null) {
            $this->userHasEntry = $condition->userHasEntry;
        }

        if ($condition->userHasNotEntry !== null) {
            $this->userHasNotEntry = $condition->userHasNotEntry;
        }
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        if ($this->userHasEntry && $this->userHasNotEntry) {
            $this->errorMessage = 'wcf.user.condition.usermap.state.error.conflict';

            throw new UserInputException('userHasEntry', 'conflict');
        }
    }

    /**
     * @inheritDoc
     */
    public function showContent(Condition $condition)
    {
        if (!WCF::getUser()->userID) {
            return false;
        }

        return $this->checkUser($condition, WCF::getUser());
    }
}
