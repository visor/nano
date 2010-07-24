$(document).ready(function() {
	var browser=navigator.appName;
	var b_version=navigator.appVersion;
	var version=parseFloat(b_version);
	if (b_version.indexOf('MSIE 6.0')==-1 && browser.indexOf('Opera')==-1 && b_version.indexOf('MSIE 7.0')==-1) {
		$('#menu_group_main a').blend();
	}
	if ($('.column').length > 0) {
		$('.column').disableSelection();
	}
	$('.portlet-header .ui-icon').click(function() {
		$(this).parents('.portlet:first').find('.portlet-content').slideToggle('fast');
		$(this).toggleClass('ui-icon-triangle-1-s'); 
		return false;	
	});
	$('.info').click(function() {
		$(this).slideUp('fast');
	});
	if ($('.approve_icon').length > 0) {
		$('.approve_icon').click(function() {
			$(this).parents('tr').css({ 'background-color' : '#e1fbcd' }, 'fast');
			alert('this is approved');
		});
	}
	if ($('.reject_icon').length > 0) {
		$('.reject_icon').click(function() {
			$(this).parents('tr').css({ 'background-color' : '#fbcdcd' }, 'fast');
			alert('this is rejected');
		});
	}
	if ($('.delete_icon').length > 0) {
		$('.delete_icon').click(function() {
			$(this).parents('tr').css({ 'background-color' : '#fbcdcd' }, 'fast');
			alert('this is deleted!');
			$(this).parents('tr').fadeOut('fast');
		});
	}
	if ($('.more').length > 0) {
		$('#tabs .more').click(function() {
			$('#hidden_submenu').slideToggle('fast');
			$(this).toggleClass('current');
			return false;
		});
	}
	if ($('.hidden_calendar').length > 0) {
		$('.hidden_calendar').datepicker();
		$('.inline_calendar').click(function() {
			$('.hidden_calendar').toggle('fast');
		});
	}
	if ($('.inline_tip').length > 0) {
		$('.inline_tip').click(function() {
			$('#inline_example2').dialog('open');
		});
	}
});

if ($('#allbox').length > 0) {
	function checkAll() {
		for (var i=0;i<document.forms[0].elements.length;i++) {
			var e=document.forms[0].elements[i];
			if ((e.name != 'allbox') && (e.type=='checkbox')) {
				e.checked=document.forms[0].allbox.checked;
			}
		}
	}
}