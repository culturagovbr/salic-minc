/* Hungarian initialisation for the jQuery UI date picker plugin. */
/* Written by Istvan Karaszi (jquery@spam.raszi.hu). */
jQuery(function($){
	$.datepicker.regional['hu'] = {
		closeText: 'bez&aacute;r&aacute;s',
		prevText: '&laquo;&nbsp;vissza',
		nextText: 'előre&nbsp;&raquo;',
		currentText: 'ma',
		monthNames: ['Janu&aacute;r', 'Febru&aacute;r', 'M&aacute;rcius', '&aacute;prilis', 'M&aacute;jus', 'J&uacute;nius',
		'J&uacute;lius', 'Augusztus', 'Szeptember', 'Okt&oacute;ber', 'November', 'December'],
		monthNamesShort: ['Jan', 'Feb', 'M&aacute;r', '&aacute;pr', 'M&aacute;j', 'J&uacute;n',
		'J&uacute;l', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
		dayNames: ['Vas&aacute;rnap', 'H&eacute;tfö', 'Kedd', 'Szerda', 'Csütörtök', 'P&eacute;ntek', 'Szombat'],
		dayNamesShort: ['Vas', 'H&eacute;t', 'Ked', 'Sze', 'Csü', 'P&eacute;n', 'Szo'],
		dayNamesMin: ['V', 'H', 'K', 'Sze', 'Cs', 'P', 'Szo'],
		weekHeader: 'H&eacute;',
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['hu']);
});
