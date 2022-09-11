<?php
namespace usermap\page;
use usermap\system\cache\builder\StatsCacheBuilder;
use wcf\data\user\group\UserGroup;
use wcf\page\AbstractPage;
use wcf\system\WCF;

/**
 * Shows the Usermap page.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapPage extends AbstractPage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'usermap.header.menu.usermap';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['user.usermap.canSeeUsermap'];
	
	/**
	 * @inheritDoc
	 */
	public $enableTracking = true;
	
	/**
	 * usermap statistics
	 */
	public $stats = [];
	
	/**
	 * available usergroups
	 */
	public $availableGroups = [];
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		// stats
		if (USERMAP_INDEX_ENABLE_STATS) {
			$this->stats = StatsCacheBuilder::getInstance()->getData();
		}
		
		// groups
		$this->availableGroups = UserGroup::getGroupsByType([], [UserGroup::EVERYONE, UserGroup::GUESTS]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'stats' => $this->stats,
				'allowSpidersToIndexThisPage' => true,
				'groups' => $this->availableGroups
		]);
	}
}
