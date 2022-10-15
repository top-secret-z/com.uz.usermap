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
namespace usermap\acp\form;

use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the marker list page.
 */
class MarkerAddForm extends AbstractForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'usermap.acp.menu.link.usermap.marker.add';

    /**
     * @inheritDoc
     */
    public $templateName = 'markerAdd';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.usermap.canManage'];

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_USERMAP'];

    /**
     * data of the uploaded marker file
     * @var    array
     */
    public $fileUpload = [];

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        parent::readFormParameters();

        if (isset($_FILES['fileUpload'])) {
            $this->fileUpload = $_FILES['fileUpload'];
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        parent::save();

        $this->saved();

        // show success message
        WCF::getTPL()->assign('success', true);
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        parent::validate();

        // uploaded?
        if (empty($this->fileUpload['name'])) {
            throw new UserInputException('fileUpload');
        }

        // ASCII, no space
        if (!StringUtil::isASCII($this->fileUpload['name']) || \strpos($this->fileUpload['name'], ' ') !== false) {
            throw new UserInputException('fileUpload', 'name');
        }

        // basic marker check
        $size = \getimagesize($this->fileUpload['tmp_name']);

        if (!$size) {
            throw new UserInputException('fileUpload', 'noImage');
        }

        // size check; max 100 / 200 (0 = width, 1 = height)
        if ($size[0] > 100 || $size[1] > 200) {
            throw new UserInputException('fileUpload', 'tooBig');
        }

        // exists?
        if (\file_exists(USERMAP_DIR . 'images/markers/' . $this->fileUpload['name'])) {
            throw new UserInputException('fileUpload', 'exists');
        }

        // move
        if (!@\move_uploaded_file($this->fileUpload['tmp_name'], USERMAP_DIR . 'images/markers/' . $this->fileUpload['name'])) {
            throw new UserInputException('fileUpload', 'uploadFailed');
        }
    }
}
