$(document).ready(function() {
	$('.submit').click(function (event) {
		event.preventDefault();
		event.stopPropagation();
		$(this).parents('form').submit();
		return false;
	});
});