$(document).ready(function() {
	var
		  browser   = navigator.appName
		, b_version = navigator.appVersion
	;
	if (-1 == b_version.indexOf('MSIE 6.0') && -1 == b_version.indexOf('MSIE 7.0') && -1 == browser.indexOf('Opera')) {
		$('#menu_group_main a').blend();
	}
	$('.column').disableSelection();
	$('.portlet-header .ui-icon').click(function() {
		$(this)
			.parents('.portlet:first')
				.find('.portlet-content')
					.slideToggle('fast')
				.end()
			.end()
			.toggleClass('ui-icon-triangle-1-s')
		;
		return false;
	});
	$('.info').click(function() {
		$(this).slideUp('fast');
	});
	$('#tabs .more').click(function() {
		$('#hidden_submenu').slideToggle('fast');
		$(this).toggleClass('current');
		return false;
	});
});

$('.allbox').click(function () {
	var checked = this.checked;
	$(this).parents('.list').find('input[type=checkbox]').each(function (e) {
		if ($(this).hasClass('allbox')) {
			return;
		}
		this.checked = checked;
	});
});
