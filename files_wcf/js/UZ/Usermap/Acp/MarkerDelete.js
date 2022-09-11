/**
 * Handles deletion of a marker.
 * 
 * @author		2014-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.usermap
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Ajax", "WoltLabSuite/Core/Language", "WoltLabSuite/Core/Ui/Notification", "WoltLabSuite/Core/Ui/Confirmation"], function (require, exports, tslib_1, Ajax, Language, UiNotification, UiConfirmation) {
	"use strict";
	Object.defineProperty(exports, "__esModule", { value: true });
	exports.init = void 0;
	
	Ajax = tslib_1.__importStar(Ajax);
	Language = tslib_1.__importStar(Language);
	UiNotification = tslib_1.__importStar(UiNotification);
	UiConfirmation = tslib_1.__importStar(UiConfirmation);
	
	class UsermapAcpMarkerDelete {
		constructor() {
			var buttons = document.querySelectorAll('.jsDeleteButton');
			for (var i = 0, length = buttons.length; i < length; i++) {
				buttons[i].addEventListener("click", (ev) => this._click(ev));
			}
		}
		
		_click(event) {
			event.preventDefault();
			
			var filename = event.currentTarget.id;
			
			UiConfirmation.show({
				confirm: function() {
					Ajax.apiOnce({
						data: {
							actionName: 'deleteMarker',
							className: 'usermap\\data\\usermap\\UsermapAction',
							parameters: {
								filename: filename
							}
						},
						success: function() {
							UiNotification.show();
							window.location.reload();
						}
					});
				},
				message: Language.get('usermap.acp.marker.delete.sure')
			});
		}
	}
	
	let usermapAcpMarkerDelete;
	function init() {
		if (!usermapAcpMarkerDelete) {
			usermapAcpMarkerDelete = new UsermapAcpMarkerDelete();
		}
	}
	exports.init = init;
});
