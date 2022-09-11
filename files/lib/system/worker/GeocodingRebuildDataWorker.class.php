<?php
namespace usermap\system\worker;
use usermap\data\usermap\UsermapUtils;
use usermap\data\usermap\geocache\Geocache;
use usermap\system\cache\builder\StatsCacheBuilder;
use usermap\system\cache\builder\UsermapCacheBuilder;
use usermap\system\usermap\geocoder\GoogleMapsGeocoder;
use usermap\system\usermap\geocoder\NominatimGeocoder;
use usermap\data\usermap\log\Log;
use usermap\data\usermap\log\LogEditor;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\user\UserList;
use wcf\system\worker\AbstractRebuildDataWorker;

/**
 * Worker implementation for updating geocoding.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class GeocodingRebuildDataWorker extends AbstractRebuildDataWorker {
	/**
	 * @inheritDoc
	 */
	protected $objectListClassName = UserList::class;
	
	/**
	 * @inheritDoc
	 */
	protected $limit = 50;
	
	// google limit reached
	protected $googleLimit = false;
	
	/**
	 * groups to be displayed
	 */
	public $groupIDs = [];
	
	/**
	 * @inheritDoc
	 */
	public function __construct(array $parameters) {
		parent::__construct($parameters);
		
		$this->groupIDs = UsermapUtils::getDisplayGroupIDs();
	}
	
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		$this->objectList->sqlOrderBy = 'user_table.userID';
	}
	
	/**
	 * @inheritDoc
	 */
	public function execute() {
		parent::execute();
		
		// only if configured and if count
		if (!USERMAP_DATA_SYNCHRONIZE_FIELDS_ENABLE || empty(USERMAP_DATA_SYNCHRONIZE_WITH)) return;
		if (!count($this->objectList)) return;
		
		// log start
		if (USERMAP_LOG_ENABLE) {
			LogEditor::create([
					'log' => 'usermap.acp.log.rebuild.start'
			]);
		}
		
		// step through users
		$options = explode(',', USERMAP_DATA_SYNCHRONIZE_WITH);
		$success = 0;
		foreach ($this->objectList as $user) {
			// respect privacy settings
			$allow = $user->usermapAllowEntry;
			if ($allow !== null && !$allow) continue;
			
			// user field 'usermap' must be empty
			if (!empty($user->getUserOption('usermapField'))) continue;
			
			// must be in groups to be displayed
			if (empty(array_intersect($this->groupIDs, $user->getGroupIDs()))) continue;
			
			$profileUsermap = [];
			foreach ($options as $option) {
				$value = $user->getUserOption(trim($option));
				if (!empty($value)) $profileUsermap[] = $value;
			}
			if (!count($profileUsermap)) continue;
			
			$profileLocation = implode(', ', $profileUsermap);
			
			// first try nominatim, then google
			$geocache = null;
			if (USERMAP_DATA_SYNCHRONIZE_NOMINATIM) {
				$geocoder = new NominatimGeocoder(true);
				$geocache = $geocoder->geocode($profileLocation, $user);
			}
			
			if (empty($geocache)) {
				$geocoder = new GoogleMapsGeocoder(true);
				$geocache = $geocoder->geocode($profileLocation, $user);
			}
			
			// nothing found
			if (empty($geocache)) continue;
			
			// update user data
			$editor = new UserEditor($user);
			$editor->update([
					'usermapLatitude' => $geocache->lat,
					'usermapLongitude' => $geocache->lng,
					'usermapLocation' => $profileLocation,
					'usermapTime' => TIME_NOW
			]);
			$optionID = User::getUserOptionID('usermapField');
			$editor->updateUserOptions([$optionID => $profileLocation]);
			
			$success ++;
		}
		
		if (USERMAP_LOG_ENABLE) {
			LogEditor::create([
					'log' => 'usermap.acp.log.rebuild.end',
					'remark' => $success
			]);
		}
		
		// reset cache
		if ($success) {
			StatsCacheBuilder::getInstance()->reset();
			UsermapCacheBuilder::getInstance()->reset();
		}
	}
}
