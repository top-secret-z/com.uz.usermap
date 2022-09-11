<?php
namespace usermap\acp\page;
use usermap\data\usermap\log\LogList;
use wcf\page\SortablePage;

/**
 * Shows the log list page.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class LogListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'usermap.acp.menu.link.usermap.log.list';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.usermap.canManage'];
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_USERMAP'];
	
	/**
	 * number of items shown per page
	 */
	public $itemsPerPage = 20;
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'logID';
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['logID', 'time', 'status', 'log', 'username', 'remark'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = LogList::class;
}
