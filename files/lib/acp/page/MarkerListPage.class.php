<?php
namespace usermap\acp\page;
use wcf\data\user\group\UserGroupList;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;

/**
 * Shows the marker list page.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class MarkerListPage extends AbstractPage {
	/**
	 * marker data
	 */
	protected $markers = [];
	
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'usermap.acp.menu.link.usermap.marker.list';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.usermap.canManage'];
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_USERMAP'];
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		// used markers
		$usedMarkers = $markerToGroup = [];
		$groupList = new UserGroupList();
		$groupList->readObjects();
		$groups = $groupList->getObjects();
		foreach ($groups as $group) {
			$usedMarkers[] = $group->usermapMarker;
			if (!isset($markerToGroup[$group->usermapMarker])) {
				$markerToGroup[$group->usermapMarker] = WCF::getLanguage()->get($group->groupName);
			}
			else {
				$markerToGroup[$group->usermapMarker] .= ', ' . WCF::getLanguage()->get($group->groupName);
			}
		}
		$usedMarkers = array_unique($usedMarkers);
		
		// available markers
		$files = DirectoryUtil::getInstance(USERMAP_DIR.'images/markers/')->getFiles(SORT_ASC);
		$path = WCF::getPath('usermap').'images/markers/';
		foreach ($files as $file) {
			if (is_dir($file)) continue;
			$name = basename($file);
			$link = '<img src="'. $path . $name . '" alt="' . $name . '">';
			$temp = getimagesize($file);
			$size = $temp[0] . ' x ' . $temp[1];
			
			$used = 0;
			$groups = '';
			if (in_array($name, $usedMarkers)) {
				$used = 1;
				$groups = $markerToGroup[$name];
			}
			
			$this->markers[] = [
					'name' => $name,
					'link' => $link,
					'used' => $used,
					'size' => $size,
					'groups' => $groups
			];
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'markers' => $this->markers
		]);
	}
}
