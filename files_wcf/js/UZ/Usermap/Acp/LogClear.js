/**
 * Clears the log.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Confirmation"], function (require, exports, tslib_1, Ajax, Language, UiConfirmation) {
	"use strict";
	Object.defineProperty(exports, "__esModule", { value: true });
	exports.init = void 0;
	
	Ajax = tslib_1.__importStar(Ajax);
	Language = tslib_1.__importStar(Language);
	UiConfirmation = tslib_1.__importStar(UiConfirmation);
	
	class UsermapAcpLogClear {
		constructor() {
			var button = document.querySelector('.jsLogClear');
			if (button) {
				button.addEventListener("click", (ev) => this._click(ev));
			}
		}
		
		_click(event) {
			UiConfirmation.show({
				confirm: function() {
					Ajax.apiOnce({
						data: {
							actionName: 'clearAll',
							className: 'usermap\\data\\usermap\\log\\LogAction'
						},
						success: function() {
							window.location.reload();
						}
					});
				},
				message: Language.get('usermap.acp.log.clear.confirm')
			});
		}
	}
	
	let usermapAcpLogClear;
	function init() {
		if (!usermapAcpLogClear) {
			usermapAcpLogClear = new UsermapAcpLogClear();
		}
	}
	exports.init = init;
});
