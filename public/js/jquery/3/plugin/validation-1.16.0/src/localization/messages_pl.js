/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: PL (Polish; język polski, polszczyzna)
 */
$.extend( $.validator.messages, {
	required: "To pole jest wymagane.",
	remote: "Proszę o wypełnienie tego pola.",
	email: "Proszę o podanie prawidłowego adresu email.",
	url: "Proszę o podanie prawidłowego URL.",
	date: "Proszę o podanie prawidłowej daty.",
	dateISO: "Proszę o podanie prawidłowej daty (ISO).",
	number: "Proszę o podanie prawidłowej liczby.",
	digits: "Proszę o podanie samych cyfr.",
	creditcard: "Proszę o podanie prawidłowej karty kredytowej.",
	equalTo: "Proszę o podanie tej samej wartości ponownie.",
	extension: "Proszę o podanie wartości z prawidłowym rozszerzeniem.",
	maxlength: $.validator.format( "Proszę o podanie nie więcej niż {0} znak&oacute;w." ),
	minlength: $.validator.format( "Proszę o podanie przynajmniej {0} znak&oacute;w." ),
	rangelength: $.validator.format( "Proszę o podanie wartości o długości od {0} do {1} znak&oacute;w." ),
	range: $.validator.format( "Proszę o podanie wartości z przedziału od {0} do {1}." ),
	max: $.validator.format( "Proszę o podanie wartości mniejszej bądź r&oacute;wnej {0}." ),
	min: $.validator.format( "Proszę o podanie wartości większej bądź r&oacute;wnej {0}." ),
	pattern: $.validator.format( "Pole zawiera niedozwolone znaki." )
} );
