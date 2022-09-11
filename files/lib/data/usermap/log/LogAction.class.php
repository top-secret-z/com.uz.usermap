<?php
namespace usermap\data\usermap\log;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes log related actions.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class LogAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = LogEditor::class;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.usermap.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['admin.usermap.canManage'];
	
	/**
	 * @inheritDoc
	 */
	protected $requireACP = ['delete', 'update'];
	
	/**
	 * Validates the clearAll action.
	 */
	public function validateClearAll() {
		// do nothing
	}
	
	/**
	 * Executes the clearAll action.
	 */
	public function clearAll() {
		$sql = "DELETE FROM	usermap".WCF_N."_log";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
	}
}
