/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: CA (Catalan; català)
 */
$.extend( $.validator.messages, {
	required: "Aquest camp &eacute;s obligatori.",
	remote: "Si us plau, omple aquest camp.",
	email: "Si us plau, escriu una adreça de correu-e vàlida",
	url: "Si us plau, escriu una URL vàlida.",
	date: "Si us plau, escriu una data vàlida.",
	dateISO: "Si us plau, escriu una data (ISO) vàlida.",
	number: "Si us plau, escriu un n&uacute;mero enter vàlid.",
	digits: "Si us plau, escriu nom&eacute;s d&iacute;gits.",
	creditcard: "Si us plau, escriu un n&uacute;mero de tarjeta vàlid.",
	equalTo: "Si us plau, escriu el mateix valor de nou.",
	extension: "Si us plau, escriu un valor amb una extensi&oacute; acceptada.",
	maxlength: $.validator.format( "Si us plau, no escriguis m&eacute;s de {0} caracters." ),
	minlength: $.validator.format( "Si us plau, no escriguis menys de {0} caracters." ),
	rangelength: $.validator.format( "Si us plau, escriu un valor entre {0} i {1} caracters." ),
	range: $.validator.format( "Si us plau, escriu un valor entre {0} i {1}." ),
	max: $.validator.format( "Si us plau, escriu un valor menor o igual a {0}." ),
	min: $.validator.format( "Si us plau, escriu un valor major o igual a {0}." )
} );
