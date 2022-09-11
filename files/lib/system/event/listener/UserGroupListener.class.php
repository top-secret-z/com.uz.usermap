<?php
namespace usermap\system\event\listener;
use usermap\system\cache\builder\StatsCacheBuilder;
use usermap\system\cache\builder\UsermapCacheBuilder;
use wcf\acp\form\UserGroupEditForm;
use wcf\data\user\group\UserGroup;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;
use wcf\util\StringUtil;

/**
 * Listen to Group add / edit
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UserGroupListener implements IParameterizedEventListener {
	/**
	 * instance of UserGroupAddForm
	 */
	protected $eventObj;
	
	/**
	 * marker data
	 */
	protected $markers = [];
	protected $usermapMarker = USERMAP_MAP_MAP_MARKER;
	
	/**
	 * allow as filter / show on map
	 */
	protected $usermapFilter = 1;
	protected $usermapShow = 1;
	
	/**
	 * disabled switch for certain groups
	 */
	private $disable = false;
	
	/**
	 * @inheritDoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		$this->eventObj = $eventObj;
		
		// disable for everyone and guests
		if ($this->eventObj instanceof UserGroupEditForm && is_object($this->eventObj->group)) {
			switch ($this->eventObj->group->groupType) {
				case UserGroup::EVERYONE:
				case UserGroup::GUESTS:
					$this->disable = true;
				break;
			}
		}
		
		if ($this->disable) return;
		$this->$eventName();
	}
	
	/**
	 * Handles the assignVariables event.
	 */
	protected function assignVariables() {
		$files = DirectoryUtil::getInstance(USERMAP_DIR.'images/markers/')->getFiles(SORT_ASC);
		$path = WCF::getPath('usermap').'images/markers/';
		foreach ($files as $file) {
			if (is_dir($file)) continue;
			$name = basename($file);
			$link = '<img src="'. $path . $name . '" height="25" alt="' . $name . '">';
			$this->markers[$name] = $link;
		}
		
		WCF::getTPL()->assign([
				'markers' => $this->markers,
				'usermapMarker' => $this->usermapMarker,
				'usermapFilter' => $this->usermapFilter,
				'usermapShow' => $this->usermapShow
		]);
	}
	
	/**
	 * Handles the readData event (UserGroupEditForm only).
	 */
	protected function readData() {
		if (empty($_POST)) {
			$files = DirectoryUtil::getInstance(USERMAP_DIR.'images/markers/')->getFiles(SORT_ASC);
			$path = WCF::getPath('usermap').'images/markers/';
			foreach ($files as $file) {
				if (is_dir($file)) continue;
				
				$name = basename($file);
				$link = '<img src="'. $path . $name . '" height="25">';
				$this->markers[$name] = $link;
			}
			
			$this->usermapMarker = $this->eventObj->group->usermapMarker;
			$this->usermapFilter = $this->eventObj->group->usermapFilter;
			$this->usermapShow = $this->eventObj->group->usermapShow;
		}
	}
	
	/**
	 * Handles the readFormParameters event.
	 */
	protected function readFormParameters() {
		if (isset($_POST['usermapMarker'])) $this->usermapMarker = StringUtil::trim($_POST['usermapMarker']);
		$this->usermapFilter = $this->usermapShow = 0;
		if (isset($_POST['usermapFilter'])) $this->usermapFilter = 1;
		if (isset($_POST['usermapShow'])) $this->usermapShow = 1;
	}
	
	/**
	 * Handles the save event.
	 */
	protected function save() {
		$this->eventObj->additionalFields = array_merge($this->eventObj->additionalFields, [
				'usermapMarker' => $this->usermapMarker,
				'usermapFilter' => $this->usermapFilter,
				'usermapShow' => $this->usermapShow
		]);
		
		if (!$this->eventObj instanceof UserGroupEditForm) {
			$this->usermapMarker = USERMAP_MAP_MAP_MARKER;
			$this->usermapFilter = 1;
			$this->usermapShow = 1;
		}
		
		// reset cache
		StatsCacheBuilder::getInstance()->reset();
		UsermapCacheBuilder::getInstance()->reset();
	}
	
	/**
	 * Handles the validate event.
	 */
	protected function validate() {
		// simply set to default
		if (!file_exists(USERMAP_DIR.'images/markers/'.$this->usermapMarker)) {
			$this->usermapMarker = USERMAP_MAP_MAP_MARKER;
		}
		
		// switch off filter unless show on map
		if (!$this->usermapShow) $this->usermapFilter = 0;
	}
}
