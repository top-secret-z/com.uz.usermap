<?php
namespace usermap\system;
use usermap\page\UsermapPage;
use wcf\system\application\AbstractApplication;
use wcf\system\page\PageLocationManager;
use wcf\system\WCF;

/**
 * This class extends the main WCF class by Usermap specific functions.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class USERMAPCore extends AbstractApplication {
	/**
	 * @inheritDoc
	 */
	protected $primaryController = UsermapPage::class;
	
	/**
	 * Sets the location data.
	 */
	public function setLocation(array $categories = []) {
		
		$categories = array_reverse($categories);
		foreach ($categories as $category) {
			PageLocationManager::getInstance()->addParentLocation('com.uz.usermap.Usermap', $category->categoryID, $category);
		}
		
		PageLocationManager::getInstance()->addParentLocation('com.uz.usermap.Usermap');
	}
}
