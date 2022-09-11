<?php
namespace usermap\system\option;
use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\AbstractOptionType;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;

/**
 * Option type implementation for single marker selection.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class MarkerSelectOptionType extends AbstractOptionType {
	/**
	 * @inheritDoc
	 */
	public function getFormElement(Option $option, $value) {
		// get stored markers
		$files = DirectoryUtil::getInstance(USERMAP_DIR.'images/markers/')->getFiles(SORT_ASC);
		$icons = [];
		$path = WCF::getPath('usermap').'images/markers/';
		foreach ($files as $file) {
			if (is_dir($file)) continue;
			
			$name = basename($file);
			$link = '<img src="'. $path . $name . '" >';
			$icons[$name] = $link;
		}
		
		WCF::getTPL()->assign([
				'icons' => $icons,
				'option' => $option,
				'value' => $value
		]);
		return WCF::getTPL()->fetch('markerSelectOptionType', 'usermap');
	}
	
	/**
	 * @inheritDoc
	 */
	public function validate(Option $option, $newValue) {
		if (!empty($newValue)) {
			if (!file_exists(USERMAP_DIR.'images/markers/'.$newValue)) {
				throw new UserInputException($option->optionName, 'validationFailed');
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function getData(Option $option, $newValue) {
		return $newValue;
	}
}
