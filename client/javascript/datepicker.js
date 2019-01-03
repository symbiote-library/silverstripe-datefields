(function ($) {
		if ($.entwine) {
			$('input.js-datepicker').entwine({
				onmatch: function () {
					$(this).datepicker({
						format: "dd/mm/yyyy",
						clearBtn: true
					});
				}
			})
		}
})(jQuery);
