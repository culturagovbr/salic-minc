/* Slovak initialisation for the jQuery UI date picker plugin. */
/* Written by Vojtech Rinik (vojto@hmm.sk). */
jQuery(function($){
	$.datepicker.regional['sk'] = {
		closeText: 'Zavrieť',
		prevText: '&#x3c;Predch&aacute;dzaj&uacute;ci',
		nextText: 'Nasleduj&uacute;ci&#x3e;',
		currentText: 'Dnes',
		monthNames: ['Janu&aacute;r','Febru&aacute;r','Marec','Apr&iacute;l','M&aacute;j','J&uacute;n',
		'J&uacute;l','August','September','Okt&oacute;ber','November','December'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','M&aacute;j','J&uacute;n',
		'J&uacute;l','Aug','Sep','Okt','Nov','Dec'],
		dayNames: ['Nedel\'a','Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota'],
		dayNamesShort: ['Ned','Pon','Uto','Str','Štv','Pia','Sob'],
		dayNamesMin: ['Ne','Po','Ut','St','Št','Pia','So'],
		weekHeader: 'Ty',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['sk']);
});
