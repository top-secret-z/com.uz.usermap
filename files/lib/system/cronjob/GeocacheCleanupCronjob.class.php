<?php
namespace usermap\system\cronjob;
use wcf\data\cronjob\Cronjob;
use wcf\system\cronjob\AbstractCronjob;
use wcf\system\WCF;

/**
 * Deletes expired geocache entries.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class GeocacheCleanupCronjob extends AbstractCronjob {
	/**
	 * @inheritDoc
	 */
	public function execute(Cronjob $cronjob) {
		parent::execute($cronjob);
		
		if (MODULE_USERMAP) {
			$sql = "DELETE FROM usermap".WCF_N."_geocache
					WHERE	time < ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([TIME_NOW - USERMAP_DATA_CACHE_GEO * 86400]);
		}
	}
}
