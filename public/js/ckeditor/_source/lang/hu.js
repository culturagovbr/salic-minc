/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Hungarian language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['hu'] =
{
	/**
	 * The language reading direction. Possible values are "rtl" for
	 * Right-To-Left languages (like Arabic) and "ltr" for Left-To-Right
	 * languages (like English).
	 * @default 'ltr'
	 */
	dir : 'ltr',

	/*
	 * Screenreader titles. Please note that screenreaders are not always capable
	 * of reading non-English words. So be careful while translating it.
	 */
	editorTitle : 'Rich text editor, %1, press ALT 0 for help.', // MISSING

	// ARIA descriptions.
	toolbar	: 'Toolbar', // MISSING
	editor	: 'Rich Text Editor', // MISSING

	// Toolbar buttons without dialogs.
	source			: 'Forr&aacute;sk&oacute;d',
	newPage			: '&uacute;j oldal',
	save			: 'Ment&eacute;s',
	preview			: 'Előn&eacute;zet',
	cut				: 'Kiv&aacute;g&aacute;s',
	copy			: 'M&aacute;sol&aacute;s',
	paste			: 'Beilleszt&eacute;s',
	print			: 'Nyomtat&aacute;s',
	underline		: 'Al&aacute;h&uacute;zott',
	bold			: 'F&eacute;lköv&eacute;r',
	italic			: 'Dőlt',
	selectAll		: 'Mindent kijelöl',
	removeFormat	: 'Form&aacute;z&aacute;s elt&aacute;vol&iacute;t&aacute;sa',
	strike			: '&aacute;th&uacute;zott',
	subscript		: 'Als&oacute; index',
	superscript		: 'Felső index',
	horizontalrule	: 'Elv&aacute;laszt&oacute;vonal beilleszt&eacute;se',
	pagebreak		: 'Oldaltör&eacute;s beilleszt&eacute;se',
	unlink			: 'Hivatkoz&aacute;s törl&eacute;se',
	undo			: 'Visszavon&aacute;s',
	redo			: 'Ism&eacute;tl&eacute;s',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Böng&eacute;sz&eacute;s a szerveren',
		url				: 'Hivatkoz&aacute;s',
		protocol		: 'Protokoll',
		upload			: 'Feltölt&eacute;s',
		uploadSubmit	: 'Küld&eacute;s a szerverre',
		image			: 'K&eacute;p',
		flash			: 'Flash',
		form			: 'Űrlap',
		checkbox		: 'Jelölőn&eacute;gyzet',
		radio			: 'V&aacute;laszt&oacute;gomb',
		textField		: 'Szövegmező',
		textarea		: 'Szövegterület',
		hiddenField		: 'Rejtettmező',
		button			: 'Gomb',
		select			: 'Legördülő lista',
		imageButton		: 'K&eacute;pgomb',
		notSet			: '<nincs be&aacute;ll&iacute;tva>',
		id				: 'Azonos&iacute;t&oacute;',
		name			: 'N&eacute;v',
		langDir			: '&iacute;r&aacute;s ir&aacute;nya',
		langDirLtr		: 'Balr&oacute;l jobbra',
		langDirRtl		: 'Jobbr&oacute;l balra',
		langCode		: 'Nyelv k&oacute;dja',
		longDescr		: 'R&eacute;szletes le&iacute;r&aacute;s webc&iacute;me',
		cssClass		: 'St&iacute;lusk&eacute;szlet',
		advisoryTitle	: 'S&uacute;g&oacute;cimke',
		cssStyle		: 'St&iacute;lus',
		ok				: 'Rendben',
		cancel			: 'M&eacute;gsem',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: '&aacute;ltal&aacute;nos',
		advancedTab		: 'Tov&aacute;bbi opci&oacute;k',
		validateNumberFailed : 'A mezőbe csak sz&aacute;mokat &iacute;rhat.',
		confirmNewPage	: 'Minden nem mentett v&aacute;ltoz&aacute;s el fog veszni! Biztosan be szeretn&eacute; tölteni az oldalt?',
		confirmCancel	: 'Az űrlap tartalma megv&aacute;ltozott, &aacute;m a v&aacute;ltoz&aacute;sokat nem rögz&iacute;tette. Biztosan be szeretn&eacute; z&aacute;rni az űrlapot?',
		options			: 'Options', // MISSING
		target			: 'Target', // MISSING
		targetNew		: 'New Window (_blank)', // MISSING
		targetTop		: 'Topmost Window (_top)', // MISSING
		targetSelf		: 'Same Window (_self)', // MISSING
		targetParent	: 'Parent Window (_parent)', // MISSING

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, unavailable</span>' // MISSING
	},

	contextmenu :
	{
		options : 'Context Menu Options' // MISSING
	},

	// Special char dialog.
	specialChar		:
	{
		toolbar		: 'Speci&aacute;lis karakter beilleszt&eacute;se',
		title		: 'Speci&aacute;lis karakter v&aacute;laszt&aacute;sa',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Hivatkoz&aacute;s beilleszt&eacute;se/m&oacute;dos&iacute;t&aacute;sa',
		other 		: '<m&aacute;s>',
		menu		: 'Hivatkoz&aacute;s m&oacute;dos&iacute;t&aacute;sa',
		title		: 'Hivatkoz&aacute;s tulajdons&aacute;gai',
		info		: 'Alaptulajdons&aacute;gok',
		target		: 'Tartalom megjelen&iacute;t&eacute;se',
		upload		: 'Feltölt&eacute;s',
		advanced	: 'Tov&aacute;bbi opci&oacute;k',
		type		: 'Hivatkoz&aacute;s t&iacute;pusa',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'Horgony az oldalon',
		toEmail		: 'E-Mail',
		targetFrame		: '<keretben>',
		targetPopup		: '<felugr&oacute; ablakban>',
		targetFrameName	: 'Keret neve',
		targetPopupName	: 'Felugr&oacute; ablak neve',
		popupFeatures	: 'Felugr&oacute; ablak jellemzői',
		popupResizable	: '&aacute;tm&eacute;retez&eacute;s',
		popupStatusBar	: '&aacute;llapotsor',
		popupLocationBar: 'C&iacute;msor',
		popupToolbar	: 'Eszközt&aacute;r',
		popupMenuBar	: 'Menü sor',
		popupFullScreen	: 'Teljes k&eacute;pernyő (csak IE)',
		popupScrollBars	: 'Görd&iacute;tős&aacute;v',
		popupDependent	: 'Szülőhöz kapcsolt (csak Netscape)',
		popupWidth		: 'Sz&eacute;less&eacute;g',
		popupLeft		: 'Bal poz&iacute;ci&oacute;',
		popupHeight		: 'Magass&aacute;g',
		popupTop		: 'Felső poz&iacute;ci&oacute;',
		id				: 'Id',
		langDir			: '&iacute;r&aacute;s ir&aacute;nya',
		langDirLTR		: 'Balr&oacute;l jobbra',
		langDirRTL		: 'Jobbr&oacute;l balra',
		acccessKey		: 'Billentyűkombin&aacute;ci&oacute;',
		name			: 'N&eacute;v',
		langCode		: '&iacute;r&aacute;s ir&aacute;nya',
		tabIndex		: 'Tabul&aacute;tor index',
		advisoryTitle	: 'S&uacute;g&oacute;cimke',
		advisoryContentType	: 'S&uacute;g&oacute; tartalomt&iacute;pusa',
		cssClasses		: 'St&iacute;lusk&eacute;szlet',
		charset			: 'Hivatkozott tartalom k&oacute;dlapja',
		styles			: 'St&iacute;lus',
		selectAnchor	: 'Horgony v&aacute;laszt&aacute;sa',
		anchorName		: 'Horgony n&eacute;v szerint',
		anchorId		: 'Azonos&iacute;t&oacute; szerint',
		emailAddress	: 'E-Mail c&iacute;m',
		emailSubject	: 'Üzenet t&aacute;rgya',
		emailBody		: 'Üzenet',
		noAnchors		: '(Nincs horgony a dokumentumban)',
		noUrl			: 'Adja meg a hivatkoz&aacute;s webc&iacute;m&eacute;t',
		noEmail			: 'Adja meg az E-Mail c&iacute;met'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Horgony beilleszt&eacute;se/szerkeszt&eacute;se',
		menu		: 'Horgony tulajdons&aacute;gai',
		title		: 'Horgony tulajdons&aacute;gai',
		name		: 'Horgony neve',
		errorName	: 'K&eacute;rem adja meg a horgony nev&eacute;t'
	},

	// List style dialog
	list:
	{
		numberedTitle		: 'Numbered List Properties', // MISSING
		bulletedTitle		: 'Bulleted List Properties', // MISSING
		type				: 'Type', // MISSING
		start				: 'Start', // MISSING
		circle				: 'Circle', // MISSING
		disc				: 'Disc', // MISSING
		square				: 'Square', // MISSING
		none				: 'None', // MISSING
		notset				: '<not set>', // MISSING
		armenian			: 'Armenian numbering', // MISSING
		georgian			: 'Georgian numbering (an, ban, gan, etc.)', // MISSING
		lowerRoman			: 'Lower Roman (i, ii, iii, iv, v, etc.)', // MISSING
		upperRoman			: 'Upper Roman (I, II, III, IV, V, etc.)', // MISSING
		lowerAlpha			: 'Lower Alpha (a, b, c, d, e, etc.)', // MISSING
		upperAlpha			: 'Upper Alpha (A, B, C, D, E, etc.)', // MISSING
		lowerGreek			: 'Lower Greek (alpha, beta, gamma, etc.)', // MISSING
		decimal				: 'Decimal (1, 2, 3, etc.)', // MISSING
		decimalLeadingZero	: 'Decimal leading zero (01, 02, 03, etc.)' // MISSING
	},

	// Find And Replace Dialog
	findAndReplace :
	{
		title				: 'Keres&eacute;s &eacute;s csere',
		find				: 'Keres&eacute;s',
		replace				: 'Csere',
		findWhat			: 'Keresett szöveg:',
		replaceWith			: 'Csere erre:',
		notFoundMsg			: 'A keresett szöveg nem tal&aacute;lhat&oacute;.',
		matchCase			: 'kis- &eacute;s nagybetű megkülönböztet&eacute;se',
		matchWord			: 'csak ha ez a teljes sz&oacute;',
		matchCyclic			: 'Ciklikus keres&eacute;s',
		replaceAll			: 'Az összes cser&eacute;je',
		replaceSuccessMsg	: '%1 egyezős&eacute;g cser&eacute;lve.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'T&aacute;bl&aacute;zat',
		title		: 'T&aacute;bl&aacute;zat tulajdons&aacute;gai',
		menu		: 'T&aacute;bl&aacute;zat tulajdons&aacute;gai',
		deleteTable	: 'T&aacute;bl&aacute;zat törl&eacute;se',
		rows		: 'Sorok',
		columns		: 'Oszlopok',
		border		: 'Szeg&eacute;lym&eacute;ret',
		align		: 'Igaz&iacute;t&aacute;s',
		alignLeft	: 'Balra',
		alignCenter	: 'Köz&eacute;pre',
		alignRight	: 'Jobbra',
		width		: 'Sz&eacute;less&eacute;g',
		widthPx		: 'k&eacute;ppont',
		widthPc		: 'sz&aacute;zal&eacute;k',
		widthUnit	: 'width unit', // MISSING
		height		: 'Magass&aacute;g',
		cellSpace	: 'Cella t&eacute;rköz',
		cellPad		: 'Cella belső marg&oacute;',
		caption		: 'Felirat',
		summary		: 'Le&iacute;r&aacute;s',
		headers		: 'Fejl&eacute;cek',
		headersNone		: 'Nincsenek',
		headersColumn	: 'Első oszlop',
		headersRow		: 'Első sor',
		headersBoth		: 'Mindkettő',
		invalidRows		: 'A sorok sz&aacute;m&aacute;nak nagyobbnak kell lenni mint 0.',
		invalidCols		: 'Az oszlopok sz&aacute;m&aacute;nak nagyobbnak kell lenni mint 0.',
		invalidBorder	: 'A szeg&eacute;lym&eacute;ret mezőbe csak sz&aacute;mokat &iacute;rhat.',
		invalidWidth	: 'A sz&eacute;less&eacute;g mezőbe csak sz&aacute;mokat &iacute;rhat.',
		invalidHeight	: 'A magass&aacute;g mezőbe csak sz&aacute;mokat &iacute;rhat.',
		invalidCellSpacing	: 'A cella t&eacute;rköz mezőbe csak sz&aacute;mokat &iacute;rhat.',
		invalidCellPadding	: 'A cella belső marg&oacute; mezőbe csak sz&aacute;mokat &iacute;rhat.',

		cell :
		{
			menu			: 'Cella',
			insertBefore	: 'Besz&uacute;r&aacute;s balra',
			insertAfter		: 'Besz&uacute;r&aacute;s jobbra',
			deleteCell		: 'Cell&aacute;k törl&eacute;se',
			merge			: 'Cell&aacute;k egyes&iacute;t&eacute;se',
			mergeRight		: 'Cell&aacute;k egyes&iacute;t&eacute;se jobbra',
			mergeDown		: 'Cell&aacute;k egyes&iacute;t&eacute;se lefel&eacute;',
			splitHorizontal	: 'Cell&aacute;k sz&eacute;tv&aacute;laszt&aacute;sa v&iacute;zszintesen',
			splitVertical	: 'Cell&aacute;k sz&eacute;tv&aacute;laszt&aacute;sa függőlegesen',
			title			: 'Cella tulajdons&aacute;gai',
			cellType		: 'Cella t&iacute;pusa',
			rowSpan			: 'Függőleges egyes&iacute;t&eacute;s',
			colSpan			: 'V&iacute;zszintes egyes&iacute;t&eacute;s',
			wordWrap		: 'Hossz&uacute; sorok tör&eacute;se',
			hAlign			: 'V&iacute;zszintes igaz&iacute;t&aacute;s',
			vAlign			: 'Függőleges igaz&iacute;t&aacute;s',
			alignTop		: 'Fel',
			alignMiddle		: 'Köz&eacute;pre',
			alignBottom		: 'Le',
			alignBaseline	: 'Alapvonalra',
			bgColor			: 'H&aacute;tt&eacute;r sz&iacute;ne',
			borderColor		: 'Keret sz&iacute;ne',
			data			: 'Adat',
			header			: 'Fejl&eacute;c',
			yes				: 'Igen',
			no				: 'Nem',
			invalidWidth	: 'A sz&eacute;less&eacute;g mezőbe csak sz&aacute;mokat &iacute;rhat.',
			invalidHeight	: 'A magass&aacute;g mezőbe csak sz&aacute;mokat &iacute;rhat.',
			invalidRowSpan	: 'A függőleges egyes&iacute;t&eacute;s mezőbe csak sz&aacute;mokat &iacute;rhat.',
			invalidColSpan	: 'A v&iacute;zszintes egyes&iacute;t&eacute;s mezőbe csak sz&aacute;mokat &iacute;rhat.',
			chooseColor		: 'Choose' // MISSING
		},

		row :
		{
			menu			: 'Sor',
			insertBefore	: 'Besz&uacute;r&aacute;s föl&eacute;',
			insertAfter		: 'Besz&uacute;r&aacute;s al&aacute;',
			deleteRow		: 'Sorok törl&eacute;se'
		},

		column :
		{
			menu			: 'Oszlop',
			insertBefore	: 'Besz&uacute;r&aacute;s balra',
			insertAfter		: 'Besz&uacute;r&aacute;s jobbra',
			deleteColumn	: 'Oszlopok törl&eacute;se'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Gomb tulajdons&aacute;gai',
		text		: 'Szöveg (&eacute;rt&eacute;k)',
		type		: 'T&iacute;pus',
		typeBtn		: 'Gomb',
		typeSbm		: 'Küld&eacute;s',
		typeRst		: 'Alaphelyzet'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Jelölőn&eacute;gyzet tulajdons&aacute;gai',
		radioTitle	: 'V&aacute;laszt&oacute;gomb tulajdons&aacute;gai',
		value		: '&eacute;rt&eacute;k',
		selected	: 'Kiv&aacute;lasztott'
	},

	// Form Dialog.
	form :
	{
		title		: 'Űrlap tulajdons&aacute;gai',
		menu		: 'Űrlap tulajdons&aacute;gai',
		action		: 'Adatfeldolgoz&aacute;st v&eacute;gző hivatkoz&aacute;s',
		method		: 'Adatküld&eacute;s m&oacute;dja',
		encoding	: 'K&oacute;dol&aacute;s'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Legördülő lista tulajdons&aacute;gai',
		selectInfo	: 'Alaptulajdons&aacute;gok',
		opAvail		: 'El&eacute;rhető opci&oacute;k',
		value		: '&eacute;rt&eacute;k',
		size		: 'M&eacute;ret',
		lines		: 'sor',
		chkMulti	: 'több sor is kiv&aacute;laszthat&oacute;',
		opText		: 'Szöveg',
		opValue		: '&eacute;rt&eacute;k',
		btnAdd		: 'Hozz&aacute;ad',
		btnModify	: 'M&oacute;dos&iacute;t',
		btnUp		: 'Fel',
		btnDown		: 'Le',
		btnSetValue : 'Legyen az alap&eacute;rtelmezett &eacute;rt&eacute;k',
		btnDelete	: 'Töröl'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Szövegterület tulajdons&aacute;gai',
		cols		: 'Karakterek sz&aacute;ma egy sorban',
		rows		: 'Sorok sz&aacute;ma'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Szövegmező tulajdons&aacute;gai',
		name		: 'N&eacute;v',
		value		: '&eacute;rt&eacute;k',
		charWidth	: 'Megjelen&iacute;tett karakterek sz&aacute;ma',
		maxChars	: 'Maxim&aacute;lis karaktersz&aacute;m',
		type		: 'T&iacute;pus',
		typeText	: 'Szöveg',
		typePass	: 'Jelsz&oacute;'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Rejtett mező tulajdons&aacute;gai',
		name	: 'N&eacute;v',
		value	: '&eacute;rt&eacute;k'
	},

	// Image Dialog.
	image :
	{
		title		: 'K&eacute;p tulajdons&aacute;gai',
		titleButton	: 'K&eacute;pgomb tulajdons&aacute;gai',
		menu		: 'K&eacute;p tulajdons&aacute;gai',
		infoTab		: 'Alaptulajdons&aacute;gok',
		btnUpload	: 'Küld&eacute;s a szerverre',
		upload		: 'Feltölt&eacute;s',
		alt			: 'Bubor&eacute;k szöveg',
		width		: 'Sz&eacute;less&eacute;g',
		height		: 'Magass&aacute;g',
		lockRatio	: 'Ar&aacute;ny megtart&aacute;sa',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Eredeti m&eacute;ret',
		border		: 'Keret',
		hSpace		: 'V&iacute;zsz. t&aacute;v',
		vSpace		: 'Függ. t&aacute;v',
		align		: 'Igaz&iacute;t&aacute;s',
		alignLeft	: 'Bal',
		alignRight	: 'Jobbra',
		alertUrl	: 'Töltse ki a k&eacute;p webc&iacute;m&eacute;t',
		linkTab		: 'Hivatkoz&aacute;s',
		button2Img	: 'Do you want to transform the selected image button on a simple image?', // MISSING
		img2Button	: 'Do you want to transform the selected image on a image button?', // MISSING
		urlMissing	: 'Image source URL is missing.', // MISSING
		validateWidth	: 'Width must be a whole number.', // MISSING
		validateHeight	: 'Height must be a whole number.', // MISSING
		validateBorder	: 'Border must be a whole number.', // MISSING
		validateHSpace	: 'HSpace must be a whole number.', // MISSING
		validateVSpace	: 'VSpace must be a whole number.' // MISSING
	},

	// Flash Dialog
	flash :
	{
		properties		: 'Flash tulajdons&aacute;gai',
		propertiesTab	: 'Tulajdons&aacute;gok',
		title			: 'Flash tulajdons&aacute;gai',
		chkPlay			: 'Automata lej&aacute;tsz&aacute;s',
		chkLoop			: 'Folyamatosan',
		chkMenu			: 'Flash menü enged&eacute;lyez&eacute;se',
		chkFull			: 'Teljes k&eacute;pernyő enged&eacute;lyez&eacute;se',
 		scale			: 'M&eacute;retez&eacute;s',
		scaleAll		: 'Mindent mutat',
		scaleNoBorder	: 'Keret n&eacute;lkül',
		scaleFit		: 'Teljes kitölt&eacute;s',
		access			: 'Szkript hozz&aacute;f&eacute;r&eacute;s',
		accessAlways	: 'Mindig',
		accessSameDomain: 'Azonos domainről',
		accessNever		: 'Soha',
		align			: 'Igaz&iacute;t&aacute;s',
		alignLeft		: 'Bal',
		alignAbsBottom	: 'Legalj&aacute;ra',
		alignAbsMiddle	: 'Közep&eacute;re',
		alignBaseline	: 'Alapvonalhoz',
		alignBottom		: 'Alj&aacute;ra',
		alignMiddle		: 'Köz&eacute;pre',
		alignRight		: 'Jobbra',
		alignTextTop	: 'Szöveg tetej&eacute;re',
		alignTop		: 'Tetej&eacute;re',
		quality			: 'Minős&eacute;g',
		qualityBest		: 'Legjobb',
		qualityHigh		: 'J&oacute;',
		qualityAutoHigh	: 'Automata j&oacute;',
		qualityMedium	: 'Közepes',
		qualityAutoLow	: 'Automata gyenge',
		qualityLow		: 'Gyenge',
		windowModeWindow: 'Window',
		windowModeOpaque: 'Opaque',
		windowModeTransparent : 'Transparent',
		windowMode		: 'Ablak m&oacute;d',
		flashvars		: 'Flash v&aacute;ltoz&oacute;k',
		bgcolor			: 'H&aacute;tt&eacute;rsz&iacute;n',
		width			: 'Sz&eacute;less&eacute;g',
		height			: 'Magass&aacute;g',
		hSpace			: 'V&iacute;zsz. t&aacute;v',
		vSpace			: 'Függ. t&aacute;v',
		validateSrc		: 'Adja meg a hivatkoz&aacute;s webc&iacute;m&eacute;t',
		validateWidth	: 'A sz&eacute;less&eacute;g mezőbe csak sz&aacute;mokat &iacute;rhat.',
		validateHeight	: 'A magass&aacute;g mezőbe csak sz&aacute;mokat &iacute;rhat.',
		validateHSpace	: 'A v&iacute;zszintes t&aacute;volsű&aacute;g mezőbe csak sz&aacute;mokat &iacute;rhat.',
		validateVSpace	: 'A függőleges t&aacute;volsű&aacute;g mezőbe csak sz&aacute;mokat &iacute;rhat.'
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Helyes&iacute;r&aacute;s-ellenőrz&eacute;s',
		title			: 'Helyes&iacute;r&aacute;s ellenörző',
		notAvailable	: 'Sajn&aacute;lom, de a szolg&aacute;ltat&aacute;s jelenleg nem el&eacute;rhető.',
		errorLoading	: 'Hiba a szolg&aacute;ltat&aacute;s host betölt&eacute;se közben: %s.',
		notInDic		: 'Nincs a sz&oacute;t&aacute;rban',
		changeTo		: 'M&oacute;dos&iacute;t&aacute;s',
		btnIgnore		: 'Kihagyja',
		btnIgnoreAll	: 'Mindet kihagyja',
		btnReplace		: 'Csere',
		btnReplaceAll	: 'Összes cser&eacute;je',
		btnUndo			: 'Visszavon&aacute;s',
		noSuggestions	: 'Nincs javaslat',
		progress		: 'Helyes&iacute;r&aacute;s-ellenőrz&eacute;s folyamatban...',
		noMispell		: 'Helyes&iacute;r&aacute;s-ellenőrz&eacute;s k&eacute;sz: Nem tal&aacute;ltam hib&aacute;t',
		noChanges		: 'Helyes&iacute;r&aacute;s-ellenőrz&eacute;s k&eacute;sz: Nincs v&aacute;ltoztatott sz&oacute;',
		oneChange		: 'Helyes&iacute;r&aacute;s-ellenőrz&eacute;s k&eacute;sz: Egy sz&oacute; cser&eacute;lve',
		manyChanges		: 'Helyes&iacute;r&aacute;s-ellenőrz&eacute;s k&eacute;sz: %1 sz&oacute; cser&eacute;lve',
		ieSpellDownload	: 'A helyes&iacute;r&aacute;s-ellenőrző nincs telep&iacute;tve. Szeretn&eacute; letölteni most?'
	},

	smiley :
	{
		toolbar	: 'Hangulatjelek',
		title	: 'Hangulatjel besz&uacute;r&aacute;sa',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : '%1 elem'
	},

	numberedlist	: 'Sz&aacute;moz&aacute;s',
	bulletedlist	: 'Felsorol&aacute;s',
	indent			: 'Beh&uacute;z&aacute;s növel&eacute;se',
	outdent			: 'Beh&uacute;z&aacute;s csökkent&eacute;se',

	justify :
	{
		left	: 'Balra',
		center	: 'Köz&eacute;pre',
		right	: 'Jobbra',
		block	: 'Sorkiz&aacute;rt'
	},

	blockquote : 'Id&eacute;zet blokk',

	clipboard :
	{
		title		: 'Beilleszt&eacute;s',
		cutError	: 'A böng&eacute;sző biztons&aacute;gi be&aacute;ll&iacute;t&aacute;sai nem enged&eacute;lyezik a szerkesztőnek, hogy v&eacute;grehajtsa a kiv&aacute;g&aacute;s műveletet. Haszn&aacute;lja az al&aacute;bbi billentyűkombin&aacute;ci&oacute;t (Ctrl/Cmd+X).',
		copyError	: 'A böng&eacute;sző biztons&aacute;gi be&aacute;ll&iacute;t&aacute;sai nem enged&eacute;lyezik a szerkesztőnek, hogy v&eacute;grehajtsa a m&aacute;sol&aacute;s műveletet. Haszn&aacute;lja az al&aacute;bbi billentyűkombin&aacute;ci&oacute;t (Ctrl/Cmd+X).',
		pasteMsg	: 'M&aacute;solja be az al&aacute;bbi mezőbe a <STRONG>Ctrl/Cmd+V</STRONG> billentyűk lenyom&aacute;s&aacute;val, majd nyomjon <STRONG>Rendben</STRONG>-t.',
		securityMsg	: 'A böng&eacute;sző biztons&aacute;gi be&aacute;ll&iacute;t&aacute;sai miatt a szerkesztő nem k&eacute;pes hozz&aacute;f&eacute;rni a v&aacute;g&oacute;lap adataihoz. Illeszd be &uacute;jra ebben az ablakban.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'The text you want to paste seems to be copied from Word. Do you want to clean it before pasting?', // MISSING
		toolbar			: 'Beilleszt&eacute;s Word-ből',
		title			: 'Beilleszt&eacute;s Word-ből',
		error			: 'It was not possible to clean up the pasted data due to an internal error' // MISSING
	},

	pasteText :
	{
		button	: 'Beilleszt&eacute;s form&aacute;zatlan szövegk&eacute;nt',
		title	: 'Beilleszt&eacute;s form&aacute;zatlan szövegk&eacute;nt'
	},

	templates :
	{
		button			: 'Sablonok',
		title			: 'El&eacute;rhető sablonok',
		options : 'Template Options', // MISSING
		insertOption	: 'Kicser&eacute;li a jelenlegi tartalmat',
		selectPromptMsg	: 'V&aacute;lassza ki melyik sablon ny&iacute;ljon meg a szerkesztőben<br>(a jelenlegi tartalom elveszik):',
		emptyListMsg	: '(Nincs sablon megadva)'
	},

	showBlocks : 'Blokkok megjelen&iacute;t&eacute;se',

	stylesCombo :
	{
		label		: 'St&iacute;lus',
		panelTitle	: 'Formatting Styles', // MISSING
		panelTitle1	: 'Blokk st&iacute;lusok',
		panelTitle2	: 'Inline st&iacute;lusok',
		panelTitle3	: 'Objektum st&iacute;lusok'
	},

	format :
	{
		label		: 'Form&aacute;tum',
		panelTitle	: 'Form&aacute;tum',

		tag_p		: 'Norm&aacute;l',
		tag_pre		: 'Form&aacute;zott',
		tag_address	: 'C&iacute;msor',
		tag_h1		: 'Fejl&eacute;c 1',
		tag_h2		: 'Fejl&eacute;c 2',
		tag_h3		: 'Fejl&eacute;c 3',
		tag_h4		: 'Fejl&eacute;c 4',
		tag_h5		: 'Fejl&eacute;c 5',
		tag_h6		: 'Fejl&eacute;c 6',
		tag_div		: 'Bekezd&eacute;s (DIV)'
	},

	div :
	{
		title				: 'Create Div Container', // MISSING
		toolbar				: 'Create Div Container', // MISSING
		cssClassInputLabel	: 'Stylesheet Classes', // MISSING
		styleSelectLabel	: 'Style', // MISSING
		IdInputLabel		: 'Id', // MISSING
		languageCodeInputLabel	: ' Language Code', // MISSING
		inlineStyleInputLabel	: 'Inline Style', // MISSING
		advisoryTitleInputLabel	: 'Advisory Title', // MISSING
		langDirLabel		: 'Language Direction', // MISSING
		langDirLTRLabel		: 'Left to Right (LTR)', // MISSING
		langDirRTLLabel		: 'Right to Left (RTL)', // MISSING
		edit				: 'Edit Div', // MISSING
		remove				: 'Remove Div' // MISSING
  	},

	font :
	{
		label		: 'Betűt&iacute;pus',
		voiceLabel	: 'Betűt&iacute;pus',
		panelTitle	: 'Betűt&iacute;pus'
	},

	fontSize :
	{
		label		: 'M&eacute;ret',
		voiceLabel	: 'Betűm&eacute;ret',
		panelTitle	: 'M&eacute;ret'
	},

	colorButton :
	{
		textColorTitle	: 'Betűsz&iacute;n',
		bgColorTitle	: 'H&aacute;tt&eacute;rsz&iacute;n',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Automatikus',
		more			: 'Tov&aacute;bbi sz&iacute;nek...'
	},

	colors :
	{
		'000' : 'Fekete',
		'800000' : 'Bord&oacute;',
		'8B4513' : 'Barna',
		'2F4F4F' : 'Söt&eacute;t türkiz',
		'008080' : 'Türkiz',
		'000080' : 'Kir&aacute;ly k&eacute;k',
		'4B0082' : 'Indig&oacute; k&eacute;k',
		'696969' : 'Szürke',
		'B22222' : 'T&eacute;gla vörös',
		'A52A2A' : 'Vörös',
		'DAA520' : 'Arany s&aacute;rga',
		'006400' : 'Söt&eacute;t zöld',
		'40E0D0' : 'Türkiz',
		'0000CD' : 'K&eacute;k',
		'800080' : 'Lila',
		'808080' : 'Szürke',
		'F00' : 'Piros',
		'FF8C00' : 'Söt&eacute;t narancs',
		'FFD700' : 'Arany',
		'008000' : 'Zöld',
		'0FF' : 'Türkiz',
		'00F' : 'K&eacute;k',
		'EE82EE' : 'R&oacute;zsasz&iacute;n',
		'A9A9A9' : 'Söt&eacute;t szürke',
		'FFA07A' : 'Lazac',
		'FFA500' : 'Narancs',
		'FFFF00' : 'Citroms&aacute;rga',
		'00FF00' : 'Neon zöld',
		'AFEEEE' : 'Vil&aacute;gos türkiz',
		'ADD8E6' : 'Vil&aacute;gos k&eacute;k',
		'DDA0DD' : 'Vil&aacute;gos lila',
		'D3D3D3' : 'Vil&aacute;gos szürke',
		'FFF0F5' : 'Lavender Blush',
		'FAEBD7' : 'Törtfeh&eacute;r',
		'FFFFE0' : 'Vil&aacute;gos s&aacute;rga',
		'F0FFF0' : 'Menta',
		'F0FFFF' : 'Az&uacute;r k&eacute;k',
		'F0F8FF' : 'Halv&aacute;ny k&eacute;k',
		'E6E6FA' : 'Lavender',
		'FFF' : 'Feh&eacute;r'
	},

	scayt :
	{
		title			: 'Helyes&iacute;r&aacute;s ellenőrz&eacute;s g&eacute;pel&eacute;s közben',
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'SCAYT enged&eacute;lyez&eacute;se',
		disable			: 'SCAYT letilt&aacute;sa',
		about			: 'SCAYT n&eacute;vjegy',
		toggle			: 'SCAYT kapcsol&aacute;sa',
		options			: 'Be&aacute;ll&iacute;t&aacute;sok',
		langs			: 'Nyelvek',
		moreSuggestions	: 'Tov&aacute;bbi javaslatok',
		ignore			: 'Kihagy',
		ignoreAll		: 'Összes kihagy&aacute;sa',
		addWord			: 'Sz&oacute; hozz&aacute;ad&aacute;sa',
		emptyDic		: 'A sz&oacute;t&aacute;r nev&eacute;t meg kell adni.',

		optionsTab		: 'Be&aacute;ll&iacute;t&aacute;sok',
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'Nyelvek',

		dictionariesTab	: 'Sz&oacute;t&aacute;r',
		dic_field_name	: 'Dictionary name', // MISSING
		dic_create		: 'Create', // MISSING
		dic_restore		: 'Restore', // MISSING
		dic_delete		: 'Delete', // MISSING
		dic_rename		: 'Rename', // MISSING
		dic_info		: 'Initially the User Dictionary is stored in a Cookie. However, Cookies are limited in size. When the User Dictionary grows to a point where it cannot be stored in a Cookie, then the dictionary may be stored on our server. To store your personal dictionary on our server you should specify a name for your dictionary. If you already have a stored dictionary, please type it\'s name and click the Restore button.', // MISSING

		aboutTab		: 'N&eacute;vjegy'
	},

	about :
	{
		title		: 'CKEditor n&eacute;vjegy',
		dlgTitle	: 'CKEditor n&eacute;vjegy',
		moreInfo	: 'Licenszel&eacute;si inform&aacute;ci&oacute;k&eacute;rt k&eacute;rjük l&aacute;togassa meg weboldalunkat:',
		copy		: 'Copyright &copy; $1. Minden jog fenntartva.'
	},

	maximize : 'Teljes m&eacute;ret',
	minimize : 'Kis m&eacute;ret',

	fakeobjects :
	{
		anchor	: 'Horgony',
		flash	: 'Flash anim&aacute;ci&oacute;',
		div		: 'Oldaltör&eacute;s',
		unknown	: 'Ismeretlen objektum'
	},

	resize : 'H&uacute;zza az &aacute;tm&eacute;retez&eacute;shez',

	colordialog :
	{
		title		: 'V&aacute;lasszon sz&iacute;nt',
		options	:	'Color Options', // MISSING
		highlight	: 'Nagy&iacute;t&aacute;s',
		selected	: 'Kiv&aacute;lasztott',
		clear		: 'Ür&iacute;t&eacute;s'
	},

	toolbarCollapse	: 'Collapse Toolbar', // MISSING
	toolbarExpand	: 'Expand Toolbar' // MISSING
};
