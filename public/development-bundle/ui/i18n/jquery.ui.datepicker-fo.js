/* Faroese initialisation for the jQuery UI date picker plugin */
/* Written by Sverri Mohr Olsen, sverrimo@gmail.com */
jQuery(function($){
	$.datepicker.regional['fo'] = {
		closeText: 'Lat aftur',
		prevText: '&#x3c;Fyrra',
		nextText: 'Næsta&#x3e;',
		currentText: '&iacute; dag',
		monthNames: ['Januar','Februar','Mars','Apr&iacute;l','Mei','Juni',
		'Juli','August','September','Oktober','November','Desember'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Mei','Jun',
		'Jul','Aug','Sep','Okt','Nov','Des'],
		dayNames: ['Sunnudagur','M&aacute;nadagur','Týsdagur','Mikudagur','H&oacute;sdagur','Fr&iacute;ggjadagur','Leyardagur'],
		dayNamesShort: ['Sun','M&aacute;n','Týs','Mik','H&oacute;s','Fr&iacute;','Ley'],
		dayNamesMin: ['Su','M&aacute;','Tý','Mi','H&oacute;','Fr','Le'],
		weekHeader: 'Vk',
		dateFormat: 'dd-mm-yy',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['fo']);
});
