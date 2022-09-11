<?php
namespace usermap\data\usermap\geocache;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a geocache entry and provides geocoding functions
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class Geocache extends DatabaseObject {
	/**
	 * Returns geocode cache by hash
	 */
	public static function getCacheLocation($hash) {
		$sql = "SELECT	*
				FROM		usermap".WCF_N."_geocache
				WHERE		hash = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$hash]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		
		return new Geocache(null, $row);
	}
	
	/**
	 * Store data in cache
	 */
	public static function setCacheLocation($data) {
		// limit length of location
		if (mb_strlen($data['location']) > 255) {
			$data['location'] = mb_substr($data['location'], 0, 255);
		}
		
		$sql = "INSERT INTO	usermap".WCF_N."_geocache
							(hash, location, lat, lng, time, type)
				VALUES		(?, ?, ?, ?, ?, ?)
				ON DUPLICATE KEY
				UPDATE		lat = ?, lng = ?, time = ?, type = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([
				$data['hash'],
				$data['location'],
				$data['lat'],
				$data['lng'],
				$data['time'],
				$data['type'],
				$data['lat'],
				$data['lng'],
				$data['time'],
				$data['type']
		]);
		
		return self::getCacheLocation($data['hash']);
	}
}
