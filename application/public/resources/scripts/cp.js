$(document).ready(function() {
	$('.submit').click(function (event) {
		event.preventDefault();
		event.stopPropagation();
		$(this).parents('form').submit();
		return false;
	});

	$('a.confirm').click(function(event) {
		if (true === confirm($(this).attr('confirm'))) {
			return true;
		}
		event.preventDefault();
		event.stopPropagation();
		return false;
	});
});