/* Vietnamese initialisation for the jQuery UI date picker plugin. */
/* Translated by Le Thanh Huy (lthanhhuy@cit.ctu.edu.vn). */
jQuery(function($){
	$.datepicker.regional['vi'] = {
		closeText: 'Đ&oacute;ng',
		prevText: '&#x3c;Trước',
		nextText: 'Tiếp&#x3e;',
		currentText: 'Hôm nay',
		monthNames: ['Th&aacute;ng Một', 'Th&aacute;ng Hai', 'Th&aacute;ng Ba', 'Th&aacute;ng Tư', 'Th&aacute;ng Năm', 'Th&aacute;ng S&aacute;u',
		'Th&aacute;ng Bảy', 'Th&aacute;ng T&aacute;m', 'Th&aacute;ng Ch&iacute;n', 'Th&aacute;ng Mười', 'Th&aacute;ng Mười Một', 'Th&aacute;ng Mười Hai'],
		monthNamesShort: ['Th&aacute;ng 1', 'Th&aacute;ng 2', 'Th&aacute;ng 3', 'Th&aacute;ng 4', 'Th&aacute;ng 5', 'Th&aacute;ng 6',
		'Th&aacute;ng 7', 'Th&aacute;ng 8', 'Th&aacute;ng 9', 'Th&aacute;ng 10', 'Th&aacute;ng 11', 'Th&aacute;ng 12'],
		dayNames: ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ S&aacute;u', 'Thứ Bảy'],
		dayNamesShort: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
		dayNamesMin: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
		weekHeader: 'Tu',
		dateFormat: 'dd/mm/yy',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['vi']);
});
