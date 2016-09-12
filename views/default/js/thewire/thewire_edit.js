define(function(require)
{
    var $ = require('jquery');
    var elgg = require('elgg');
		elgg.provide('elgg.thewire_edit');

		elgg.thewire_edit.init = function() {
			var callback = function() {
				var maxLength = $(this).data('max-length');
				if (maxLength) {
					elgg.thewire_edit.textCounter(this, $("#thewire-characters-remaining span"), maxLength);
				}
			};

			$("#thewire-edit-textarea").on({
				input: callback,
				onpropertychange: callback
			});
		};

		/**
		 * Update the number of characters left with every keystroke
		 *
		 * @param {Object}  textarea
		 * @param {Object}  status
		 * @param {integer} limit
		 * @return void
		 */
		elgg.thewire_edit.textCounter = function(textarea, status, limit) {

			var remaining_chars = limit - $(textarea).val().length;
			status.html(remaining_chars);

			if (remaining_chars < 0) {
				status.parent().addClass("thewire-characters-remaining-warning");
				$("#thewire-submit-button").attr('disabled', 'disabled');
				$("#thewire-submit-button").addClass('elgg-state-disabled');
			} else {
				status.parent().removeClass("thewire-characters-remaining-warning");
				$("#thewire-submit-button").removeAttr('disabled', 'disabled');
				$("#thewire-submit-button").removeClass('elgg-state-disabled');
			}
		};

		elgg.register_hook_handler('init', 'system', elgg.thewire_edit.init);
});
