<?php
namespace usermap\data\usermap\log;
use wcf\data\DatabaseObjectEditor;
use wcf\system\WCF;

/**
 * Provides functions to edit log entries.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class LogEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = Log::class;
	
	/**
	 * @inheritDoc
	 */
	public static function create(array $data = []) {
		$userID = isset($data['userID']) ? $data['userID'] : WCF::getUser()->userID;
		if ($userID == 0) $userID = null;
		
		$parameters = [
				'time' => TIME_NOW,
				'log' => isset($data['log']) ? $data['log'] : 'usermap.acp.log.none',
				'remark' => isset($data['remark']) ? $data['remark'] : '',
				'status' => isset($data['status']) ? $data['status'] : Log::STATUS_OK,
				'userID' => $userID,
				'username' => isset($data['username']) ? $data['username'] : WCF::getUser()->username,
		];
		
		parent::create($parameters);
	}
}
