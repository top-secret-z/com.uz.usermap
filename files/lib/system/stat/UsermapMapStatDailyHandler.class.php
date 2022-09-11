<?php
namespace usermap\system\stat;
use wcf\system\stat\AbstractStatDailyHandler;
use wcf\system\WCF;

/**
 * Stat handler implementation for usermap user entries.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapMapStatDailyHandler extends AbstractStatDailyHandler {
	/**
	 * @inheritDoc
	 */
	public function getData($date) {
		return [
				'counter' => $this->getCounter($date, 'wcf'.WCF_N.'_user', 'usermapTime'),
				'total' => $this->getTotal($date, 'wcf'.WCF_N.'_user', 'usermapTime')
		];
	}
}
