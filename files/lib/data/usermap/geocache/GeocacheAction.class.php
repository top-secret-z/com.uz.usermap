<?php
namespace usermap\data\usermap\geocache;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes geocache related actions.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
class GeocacheAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = GeocacheEditor::class;
}
