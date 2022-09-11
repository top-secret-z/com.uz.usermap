<?php
namespace usermap\system\event\listener;
use wcf\system\event\listener\IParameterizedEventListener;

/**
 * Exports user data iaw Gdpr.
 *
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UserDataExportListener implements IParameterizedEventListener {
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// exclude one option and add location data in user
		$eventObj->skipUserOptions[] = 'usermapAllowEntry';
		$eventObj->exportUserProperties[] = 'usermapLatitude';
		$eventObj->exportUserProperties[] = 'usermapLongitude';
		$eventObj->exportUserProperties[] = 'usermapLocation';
		$eventObj->exportUserProperties[] = 'usermapTime';
	}
}
