<?php
namespace usermap\form;
use usermap\data\usermap\geocache\Geocache;
use usermap\data\usermap\log\Log;
use usermap\data\usermap\log\LogEditor;
use usermap\system\cache\builder\StatsCacheBuilder;
use usermap\system\cache\builder\UsermapCacheBuilder;
use wcf\form\AbstractForm;
use wcf\data\user\UserAction;
use wcf\system\menu\user\UserMenu;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;

/**
 * Shows the own location input form.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class UsermapUserInputForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.user.menu.usermap.input';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['user.usermap.canUseUsermap'];
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_USERMAP'];
	
	/**
	 * @inheritDoc
	 */
	public $enableTracking = true;
	
	/**
	 * geocode data
	 */
	public $delete = 0;
	public $geocode = '';
	public $latitude = 0.0;
	public $longitude = 0.0;
	
	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		
		$user = WCF::getUser();
		$this->latitude = $user->usermapLatitude;
		$this->longitude = $user->usermapLongitude;
		$this->geocode = $user->usermapLocation;
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'latitude' => $this->latitude,
				'longitude' => $this->longitude,
				'geocode' => $this->geocode,
				'delete' => 0
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function show() {
		// set active tab
		UserMenu::getInstance()->setActiveMenuItem('usermap.user.account.menu.input');
		
		parent::show();
	}
	
	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		$this->delete = 0;
		if (isset($_POST['delete'])) $this->delete = intval($_POST['delete']);
		if (isset($_POST['geocode'])) $this->geocode = StringUtil::trim($_POST['geocode']);
		if (isset($_POST['latitude'])) $this->latitude = floatval($_POST['latitude']);
		if (isset($_POST['longitude'])) $this->longitude = floatval($_POST['longitude']);
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->geocode)) {
			$this->latitude = 0.0;
			$this->longitude = 0.0;
		}
		
		// limit length of location
		if (mb_strlen($this->geocode) > 255) {
			$this->geocode = mb_substr($this->geocode, 0, 255);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		
		if (!$this->delete) {
			if (!empty($this->geocode)) {
				// update user
				$data = [
						'usermapLatitude' => $this->latitude,
						'usermapLongitude' => $this->longitude,
						'usermapLocation' => $this->geocode,
						'usermapTime' => TIME_NOW
				];
				
				$userAction = new UserAction([WCF::getUser()->userID], 'update', [
						'data' => $data,
						'options' => [
								WCF::getUser()->getUserOptionID('usermapField') => !empty($this->geocode) ? $this->geocode : null
						],
						'skipGeocoding' => 1
				]);
				$userAction->executeAction();
				
				// log
				if (USERMAP_LOG_ENABLE) {
					LogEditor::create([
							'log' => 'usermap.acp.log.input.new',
							'remark' => $this->geocode
					]);
				}
				
				// update geo cache 
				$data = [
						'hash' => md5($this->geocode),
						'lat' => $this->latitude,
						'lng' => $this->longitude,
						'location' => $this->geocode,
						'time' => TIME_NOW,
						'type' => 1
				];
				Geocache::setCacheLocation($data);
			}
		}
		else {
			$userAction = new UserAction([WCF::getUser()->userID], 'update', [
					'data' => [
							'usermapLatitude' => 0.0,
							'usermapLongitude' => 0.0,
							'usermapLocation' => '',
							'usermapTime' => 0
					],
					'options' => [
							WCF::getUser()->getUserOptionID('usermapField') => null
					],
					'skipGeocoding' => 1
			]);
			$userAction->executeAction();
			
			// log
			if (USERMAP_LOG_ENABLE) {
				LogEditor::create([
						'log' => 'usermap.acp.log.input.deleted'
				]);
			}
		}
		
		// reset cache
		StatsCacheBuilder::getInstance()->reset();
		UsermapCacheBuilder::getInstance()->reset();
		
		// ready, reload form
		$this->saved();
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink('UsermapUserInput', ['application' => 'usermap']));
		exit;
	}
}
