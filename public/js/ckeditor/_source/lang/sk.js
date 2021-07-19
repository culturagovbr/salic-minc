/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Slovak language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['sk'] =
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
	source			: 'Zdroj',
	newPage			: 'Nov&aacute; str&aacute;nka',
	save			: 'Uložiť',
	preview			: 'N&aacute;hľad',
	cut				: 'Vystrihn&uacute;ť',
	copy			: 'Kop&iacute;rovať',
	paste			: 'Vložiť',
	print			: 'Tlač',
	underline		: 'Podčiarknut&eacute;',
	bold			: 'Tučn&eacute;',
	italic			: 'Kurz&iacute;va',
	selectAll		: 'Vybrať všetko',
	removeFormat	: 'Odstr&aacute;niť form&aacute;tovanie',
	strike			: 'Prečiarknut&eacute;',
	subscript		: 'Dolný index',
	superscript		: 'Horný index',
	horizontalrule	: 'Vložiť vodorovn&uacute; čiaru',
	pagebreak		: 'Vložiť oddeľovač str&aacute;nky',
	unlink			: 'Odstr&aacute;niť odkaz',
	undo			: 'Späť',
	redo			: 'Znovu',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Prech&aacute;dzať server',
		url				: 'URL',
		protocol		: 'Protokol',
		upload			: 'Odoslať',
		uploadSubmit	: 'Odoslať na server',
		image			: 'Obr&aacute;zok',
		flash			: 'Flash',
		form			: 'Formul&aacute;r',
		checkbox		: 'Zaškrt&aacute;vacie pol&iacute;čko',
		radio			: 'Prep&iacute;nač',
		textField		: 'Textov&eacute; pole',
		textarea		: 'Textov&aacute; oblasť',
		hiddenField		: 'Skryt&eacute; pole',
		button			: 'Tlačidlo',
		select			: 'Rozbaľovac&iacute; zoznam',
		imageButton		: 'Obr&aacute;zkov&eacute; tlačidlo',
		notSet			: '<nenastaven&eacute;>',
		id				: 'Id',
		name			: 'Meno',
		langDir			: 'Orient&aacute;cia jazyka',
		langDirLtr		: 'Zľava doprava (LTR)',
		langDirRtl		: 'Sprava doľava (RTL)',
		langCode		: 'K&oacute;d jazyka',
		longDescr		: 'Dlhý popis URL',
		cssClass		: 'Trieda štýlu',
		advisoryTitle	: 'Pomocný titulok',
		cssStyle		: 'Štýl',
		ok				: 'OK',
		cancel			: 'Zrušiť',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: 'Hlavn&eacute;',
		advancedTab		: 'Rozš&iacute;ren&eacute;',
		validateNumberFailed : 'This value is not a number.', // MISSING
		confirmNewPage	: 'Any unsaved changes to this content will be lost. Are you sure you want to load new page?', // MISSING
		confirmCancel	: 'Some of the options have been changed. Are you sure to close the dialog?', // MISSING
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
		toolbar		: 'Vložiť špeci&aacute;lne znaky',
		title		: 'Výber špeci&aacute;lneho znaku',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Vložiť/zmeniť odkaz',
		other 		: '<iný>',
		menu		: 'Zmeniť odkaz',
		title		: 'Odkaz',
		info		: 'Inform&aacute;cie o odkaze',
		target		: 'Cieľ',
		upload		: 'Odoslať',
		advanced	: 'Rozš&iacute;ren&eacute;',
		type		: 'Typ odkazu',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'Kotva v tejto str&aacute;nke',
		toEmail		: 'E-Mail',
		targetFrame		: '<r&aacute;mec>',
		targetPopup		: '<vyskakovacie okno>',
		targetFrameName	: 'Meno r&aacute;mu cieľa',
		targetPopupName	: 'N&aacute;zov vyskakovacieho okna',
		popupFeatures	: 'Vlastnosti vyskakovacieho okna',
		popupResizable	: 'Resizable', // MISSING
		popupStatusBar	: 'Stavový riadok',
		popupLocationBar: 'Panel umiestnenia',
		popupToolbar	: 'Panel n&aacute;strojov',
		popupMenuBar	: 'Panel ponuky',
		popupFullScreen	: 'Cel&aacute; obrazovka (IE)',
		popupScrollBars	: 'Posuvn&iacute;ky',
		popupDependent	: 'Z&aacute;vislosť (Netscape)',
		popupWidth		: 'Š&iacute;rka',
		popupLeft		: 'Ľavý okraj',
		popupHeight		: 'Výška',
		popupTop		: 'Horný okraj',
		id				: 'Id', // MISSING
		langDir			: 'Orient&aacute;cia jazyka',
		langDirLTR		: 'Zľava doprava (LTR)',
		langDirRTL		: 'Sprava doľava (RTL)',
		acccessKey		: 'Pr&iacute;stupový kľ&uacute;č',
		name			: 'Meno',
		langCode		: 'Orient&aacute;cia jazyka',
		tabIndex		: 'Poradie prvku',
		advisoryTitle	: 'Pomocný titulok',
		advisoryContentType	: 'Pomocný typ obsahu',
		cssClasses		: 'Trieda štýlu',
		charset			: 'Priraden&aacute; znakov&aacute; sada',
		styles			: 'Štýl',
		selectAnchor	: 'Vybrať kotvu',
		anchorName		: 'Podľa mena kotvy',
		anchorId		: 'Podľa Id objektu',
		emailAddress	: 'E-Mailov&aacute; adresa',
		emailSubject	: 'Predmet spr&aacute;vy',
		emailBody		: 'Telo spr&aacute;vy',
		noAnchors		: '(V str&aacute;nke nie je definovan&aacute; žiadna kotva)',
		noUrl			: 'Zadajte pros&iacute;m URL odkazu',
		noEmail			: 'Zadajte pros&iacute;m e-mailov&uacute; adresu'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Vložiť/zmeniť kotvu',
		menu		: 'Vlastnosti kotvy',
		title		: 'Vlastnosti kotvy',
		name		: 'Meno kotvy',
		errorName	: 'Zadajte pros&iacute;m meno kotvy'
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
		title				: 'N&aacute;jsť a nahradiť',
		find				: 'Hľadať',
		replace				: 'Nahradiť',
		findWhat			: 'Čo hľadať:',
		replaceWith			: 'Č&iacute;m nahradiť:',
		notFoundMsg			: 'Hľadaný text nebol n&aacute;jdený.',
		matchCase			: 'Rozlišovať mal&eacute;/veľk&eacute; p&iacute;smen&aacute;',
		matchWord			: 'Len cel&eacute; slov&aacute;',
		matchCyclic			: 'Match cyclic', // MISSING
		replaceAll			: 'Nahradiť všetko',
		replaceSuccessMsg	: '%1 occurrence(s) replaced.' // MISSING
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tabuľka',
		title		: 'Vlastnosti tabuľky',
		menu		: 'Vlastnosti tabuľky',
		deleteTable	: 'Vymazať tabuľku',
		rows		: 'Riadky',
		columns		: 'Stĺpce',
		border		: 'Ohraničenie',
		align		: 'Zarovnanie',
		alignLeft	: 'Vľavo',
		alignCenter	: 'Na stred',
		alignRight	: 'Vpravo',
		width		: 'Š&iacute;rka',
		widthPx		: 'pixelov',
		widthPc		: 'percent',
		widthUnit	: 'width unit', // MISSING
		height		: 'Výška',
		cellSpace	: 'Vzdialenosť buniek',
		cellPad		: 'Odsadenie obsahu',
		caption		: 'Popis',
		summary		: 'Prehľad',
		headers		: 'Headers', // MISSING
		headersNone		: 'None', // MISSING
		headersColumn	: 'First column', // MISSING
		headersRow		: 'First Row', // MISSING
		headersBoth		: 'Both', // MISSING
		invalidRows		: 'Number of rows must be a number greater than 0.', // MISSING
		invalidCols		: 'Number of columns must be a number greater than 0.', // MISSING
		invalidBorder	: 'Border size must be a number.', // MISSING
		invalidWidth	: 'Table width must be a number.', // MISSING
		invalidHeight	: 'Table height must be a number.', // MISSING
		invalidCellSpacing	: 'Cell spacing must be a number.', // MISSING
		invalidCellPadding	: 'Cell padding must be a number.', // MISSING

		cell :
		{
			menu			: 'Bunka',
			insertBefore	: 'Vložiť bunku pred',
			insertAfter		: 'Vložiť bunku za',
			deleteCell		: 'Vymazať bunky',
			merge			: 'Zl&uacute;čiť bunky',
			mergeRight		: 'Zl&uacute;čiť doprava',
			mergeDown		: 'Zl&uacute;čiť dole',
			splitHorizontal	: 'Rozdeliť bunky horizont&aacute;lne',
			splitVertical	: 'Rozdeliť bunky vertik&aacute;lne',
			title			: 'Cell Properties', // MISSING
			cellType		: 'Cell Type', // MISSING
			rowSpan			: 'Rows Span', // MISSING
			colSpan			: 'Columns Span', // MISSING
			wordWrap		: 'Word Wrap', // MISSING
			hAlign			: 'Horizontal Alignment', // MISSING
			vAlign			: 'Vertical Alignment', // MISSING
			alignTop		: 'Top', // MISSING
			alignMiddle		: 'Middle', // MISSING
			alignBottom		: 'Bottom', // MISSING
			alignBaseline	: 'Baseline', // MISSING
			bgColor			: 'Background Color', // MISSING
			borderColor		: 'Border Color', // MISSING
			data			: 'Data', // MISSING
			header			: 'Header', // MISSING
			yes				: 'Yes', // MISSING
			no				: 'No', // MISSING
			invalidWidth	: 'Cell width must be a number.', // MISSING
			invalidHeight	: 'Cell height must be a number.', // MISSING
			invalidRowSpan	: 'Rows span must be a whole number.', // MISSING
			invalidColSpan	: 'Columns span must be a whole number.', // MISSING
			chooseColor		: 'Choose' // MISSING
		},

		row :
		{
			menu			: 'Riadok',
			insertBefore	: 'Vložiť riadok za',
			insertAfter		: 'Vložiť riadok pred',
			deleteRow		: 'Vymazať riadok'
		},

		column :
		{
			menu			: 'Stĺpec',
			insertBefore	: 'Vložiť stĺpec za',
			insertAfter		: 'Vložiť stĺpec pred',
			deleteColumn	: 'Zmazať stĺpec'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Vlastnosti tlačidla',
		text		: 'Text',
		type		: 'Typ',
		typeBtn		: 'Tlačidlo',
		typeSbm		: 'Odoslať',
		typeRst		: 'Vymazať'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Vlastnosti zaškrt&aacute;vacieho pol&iacute;čka',
		radioTitle	: 'Vlastnosti prep&iacute;nača',
		value		: 'Hodnota',
		selected	: 'Vybran&eacute;'
	},

	// Form Dialog.
	form :
	{
		title		: 'Vlastnosti formul&aacute;ra',
		menu		: 'Vlastnosti formul&aacute;ra',
		action		: 'Akcie',
		method		: 'Met&oacute;da',
		encoding	: 'Encoding' // MISSING
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Vlastnosti rozbaľovacieho zoznamu',
		selectInfo	: 'Info',
		opAvail		: 'Dostupn&eacute; možnosti',
		value		: 'Hodnota',
		size		: 'Veľkosť',
		lines		: 'riadkov',
		chkMulti	: 'Povoliť viacn&aacute;sobný výber',
		opText		: 'Text',
		opValue		: 'Hodnota',
		btnAdd		: 'Pridať',
		btnModify	: 'Zmeniť',
		btnUp		: 'Hore',
		btnDown		: 'Dole',
		btnSetValue : 'Nastaviť ako vybran&uacute; hodnotu',
		btnDelete	: 'Zmazať'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Vlastnosti textovej oblasti',
		cols		: 'Stĺpce',
		rows		: 'Riadky'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Vlastnosti textov&eacute;ho poľa',
		name		: 'N&aacute;zov',
		value		: 'Hodnota',
		charWidth	: 'Š&iacute;rka pola (znakov)',
		maxChars	: 'Maxim&aacute;lny počet znakov',
		type		: 'Typ',
		typeText	: 'Text',
		typePass	: 'Heslo'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Vlastnosti skryt&eacute;ho poľa',
		name	: 'N&aacute;zov',
		value	: 'Hodnota'
	},

	// Image Dialog.
	image :
	{
		title		: 'Vlastnosti obr&aacute;zku',
		titleButton	: 'Vlastnosti obr&aacute;zkov&eacute;ho tlačidla',
		menu		: 'Vlastnosti obr&aacute;zku',
		infoTab		: 'Inform&aacute;cie o obr&aacute;zku',
		btnUpload	: 'Odoslať na server',
		upload		: 'Odoslať',
		alt			: 'Alternat&iacute;vny text',
		width		: 'Š&iacute;rka',
		height		: 'Výška',
		lockRatio	: 'Z&aacute;mok',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Pôvodn&aacute; veľkosť',
		border		: 'Okraje',
		hSpace		: 'H-medzera',
		vSpace		: 'V-medzera',
		align		: 'Zarovnanie',
		alignLeft	: 'Vľavo',
		alignRight	: 'Vpravo',
		alertUrl	: 'Zadajte pros&iacute;m URL obr&aacute;zku',
		linkTab		: 'Odkaz',
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
		properties		: 'Vlastnosti Flashu',
		propertiesTab	: 'Properties', // MISSING
		title			: 'Vlastnosti Flashu',
		chkPlay			: 'Automatick&eacute; prehr&aacute;vanie',
		chkLoop			: 'Opakovanie',
		chkMenu			: 'Povoliť Flash Menu',
		chkFull			: 'Allow Fullscreen', // MISSING
 		scale			: 'Mierka',
		scaleAll		: 'Zobraziť mierku',
		scaleNoBorder	: 'Bez okrajov',
		scaleFit		: 'Roztiahnuť na cel&eacute;',
		access			: 'Script Access', // MISSING
		accessAlways	: 'Always', // MISSING
		accessSameDomain: 'Same domain', // MISSING
		accessNever		: 'Never', // MISSING
		align			: 'Zarovnanie',
		alignLeft		: 'Vľavo',
		alignAbsBottom	: '&uacute;plne dole',
		alignAbsMiddle	: 'Do stredu',
		alignBaseline	: 'Na z&aacute;kladňu',
		alignBottom		: 'Dole',
		alignMiddle		: 'Na stred',
		alignRight		: 'Vpravo',
		alignTextTop	: 'Na horný okraj textu',
		alignTop		: 'Nahor',
		quality			: 'Quality', // MISSING
		qualityBest		: 'Best', // MISSING
		qualityHigh		: 'High', // MISSING
		qualityAutoHigh	: 'Auto High', // MISSING
		qualityMedium	: 'Medium', // MISSING
		qualityAutoLow	: 'Auto Low', // MISSING
		qualityLow		: 'Low', // MISSING
		windowModeWindow: 'Window', // MISSING
		windowModeOpaque: 'Opaque', // MISSING
		windowModeTransparent : 'Transparent', // MISSING
		windowMode		: 'Window mode', // MISSING
		flashvars		: 'Variables for Flash', // MISSING
		bgcolor			: 'Farba pozadia',
		width			: 'Š&iacute;rka',
		height			: 'Výška',
		hSpace			: 'H-medzera',
		vSpace			: 'V-medzera',
		validateSrc		: 'Zadajte pros&iacute;m URL odkazu',
		validateWidth	: 'Width must be a number.', // MISSING
		validateHeight	: 'Height must be a number.', // MISSING
		validateHSpace	: 'HSpace must be a number.', // MISSING
		validateVSpace	: 'VSpace must be a number.' // MISSING
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Kontrola pravopisu',
		title			: 'Spell Check', // MISSING
		notAvailable	: 'Sorry, but service is unavailable now.', // MISSING
		errorLoading	: 'Error loading application service host: %s.', // MISSING
		notInDic		: 'Nie je v slovn&iacute;ku',
		changeTo		: 'Zmeniť na',
		btnIgnore		: 'Ignorovať',
		btnIgnoreAll	: 'Ignorovať všetko',
		btnReplace		: 'Prep&iacute;sat',
		btnReplaceAll	: 'Prep&iacute;sat všetko',
		btnUndo			: 'Späť',
		noSuggestions	: '- Žiadny n&aacute;vrh -',
		progress		: 'Prebieha kontrola pravopisu...',
		noMispell		: 'Kontrola pravopisu dokončen&aacute;: bez chýb',
		noChanges		: 'Kontrola pravopisu dokončen&aacute;: žiadne slov&aacute; nezmenen&eacute;',
		oneChange		: 'Kontrola pravopisu dokončen&aacute;: zmenen&eacute; jedno slovo',
		manyChanges		: 'Kontrola pravopisu dokončen&aacute;: zmenených %1 slov',
		ieSpellDownload	: 'Kontrola pravopisu nie je naištalovan&aacute;. Chcete ju hneď stiahnuť?'
	},

	smiley :
	{
		toolbar	: 'Smajl&iacute;ky',
		title	: 'Vkladanie smajl&iacute;kov',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : '%1 element' // MISSING
	},

	numberedlist	: 'Č&iacute;slovanie',
	bulletedlist	: 'Odr&aacute;žky',
	indent			: 'Zväčšiť odsadenie',
	outdent			: 'Zmenšiť odsadenie',

	justify :
	{
		left	: 'Zarovnať vľavo',
		center	: 'Zarovnať na stred',
		right	: 'Zarovnať vpravo',
		block	: 'Zarovnať do bloku'
	},

	blockquote : 'Cit&aacute;cia',

	clipboard :
	{
		title		: 'Vložiť',
		cutError	: 'Bezpečnostn&eacute; nastavenia V&aacute;šho prehliadača nedovoľuj&uacute; editoru spustiť funkciu pre vystrihnutie zvolen&eacute;ho textu do schr&aacute;nky. Pros&iacute;m vystrihnite zvolený text do schr&aacute;nky pomocou kl&aacute;vesnice (Ctrl/Cmd+X).',
		copyError	: 'Bezpečnostn&eacute; nastavenia V&aacute;šho prehliadača nedovoľuj&uacute; editoru spustiť funkciu pre kop&iacute;rovanie zvolen&eacute;ho textu do schr&aacute;nky. Pros&iacute;m skop&iacute;rujte zvolený text do schr&aacute;nky pomocou kl&aacute;vesnice (Ctrl/Cmd+C).',
		pasteMsg	: 'Pros&iacute;m vložte nasledovný r&aacute;mček použit&iacute;m kl&aacute;vesnice (<STRONG>Ctrl/Cmd+V</STRONG>) a stlačte <STRONG>OK</STRONG>.',
		securityMsg	: 'Bezpečnostn&eacute; nastavenia V&aacute;šho prehliadača nedovoľuj&uacute; editoru pristupovať priamo k dat&aacute;m v schr&aacute;nke. Mus&iacute;te ich vložiť znovu do tohto okna.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'The text you want to paste seems to be copied from Word. Do you want to clean it before pasting?', // MISSING
		toolbar			: 'Vložiť z Wordu',
		title			: 'Vložiť z Wordu',
		error			: 'It was not possible to clean up the pasted data due to an internal error' // MISSING
	},

	pasteText :
	{
		button	: 'Vložiť ako čistý text',
		title	: 'Vložiť ako čistý text'
	},

	templates :
	{
		button			: 'Šabl&oacute;ny',
		title			: 'Šabl&oacute;ny obsahu',
		options : 'Template Options', // MISSING
		insertOption	: 'Nahradiť aktu&aacute;lny obsah',
		selectPromptMsg	: 'Pros&iacute;m vyberte šabl&oacute;ny na otvorenie v editore<br>(s&uacute;šasný obsah bude stratený):',
		emptyListMsg	: '(žiadne šabl&oacute;ny nen&aacute;jden&eacute;)'
	},

	showBlocks : 'Uk&aacute;zať bloky',

	stylesCombo :
	{
		label		: 'Štýl',
		panelTitle	: 'Formatting Styles', // MISSING
		panelTitle1	: 'Block Styles', // MISSING
		panelTitle2	: 'Inline Styles', // MISSING
		panelTitle3	: 'Object Styles' // MISSING
	},

	format :
	{
		label		: 'Form&aacute;t',
		panelTitle	: 'Form&aacute;t',

		tag_p		: 'Norm&aacute;lny',
		tag_pre		: 'Form&aacute;tovaný',
		tag_address	: 'Adresa',
		tag_h1		: 'Nadpis 1',
		tag_h2		: 'Nadpis 2',
		tag_h3		: 'Nadpis 3',
		tag_h4		: 'Nadpis 4',
		tag_h5		: 'Nadpis 5',
		tag_h6		: 'Nadpis 6',
		tag_div		: 'Odsek (DIV)'
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
		label		: 'P&iacute;smo',
		voiceLabel	: 'Font', // MISSING
		panelTitle	: 'P&iacute;smo'
	},

	fontSize :
	{
		label		: 'Veľkosť',
		voiceLabel	: 'Font Size', // MISSING
		panelTitle	: 'Veľkosť'
	},

	colorButton :
	{
		textColorTitle	: 'Farba textu',
		bgColorTitle	: 'Farba pozadia',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Automaticky',
		more			: 'Viac farieb...'
	},

	colors :
	{
		'000' : 'Black', // MISSING
		'800000' : 'Maroon', // MISSING
		'8B4513' : 'Saddle Brown', // MISSING
		'2F4F4F' : 'Dark Slate Gray', // MISSING
		'008080' : 'Teal', // MISSING
		'000080' : 'Navy', // MISSING
		'4B0082' : 'Indigo', // MISSING
		'696969' : 'Dim Gray', // MISSING
		'B22222' : 'Fire Brick', // MISSING
		'A52A2A' : 'Brown', // MISSING
		'DAA520' : 'Golden Rod', // MISSING
		'006400' : 'Dark Green', // MISSING
		'40E0D0' : 'Turquoise', // MISSING
		'0000CD' : 'Medium Blue', // MISSING
		'800080' : 'Purple', // MISSING
		'808080' : 'Gray', // MISSING
		'F00' : 'Red', // MISSING
		'FF8C00' : 'Dark Orange', // MISSING
		'FFD700' : 'Gold', // MISSING
		'008000' : 'Green', // MISSING
		'0FF' : 'Cyan', // MISSING
		'00F' : 'Blue', // MISSING
		'EE82EE' : 'Violet', // MISSING
		'A9A9A9' : 'Dark Gray', // MISSING
		'FFA07A' : 'Light Salmon', // MISSING
		'FFA500' : 'Orange', // MISSING
		'FFFF00' : 'Yellow', // MISSING
		'00FF00' : 'Lime', // MISSING
		'AFEEEE' : 'Pale Turquoise', // MISSING
		'ADD8E6' : 'Light Blue', // MISSING
		'DDA0DD' : 'Plum', // MISSING
		'D3D3D3' : 'Light Grey', // MISSING
		'FFF0F5' : 'Lavender Blush', // MISSING
		'FAEBD7' : 'Antique White', // MISSING
		'FFFFE0' : 'Light Yellow', // MISSING
		'F0FFF0' : 'Honeydew', // MISSING
		'F0FFFF' : 'Azure', // MISSING
		'F0F8FF' : 'Alice Blue', // MISSING
		'E6E6FA' : 'Lavender', // MISSING
		'FFF' : 'White' // MISSING
	},

	scayt :
	{
		title			: 'Spell Check As You Type', // MISSING
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'Enable SCAYT', // MISSING
		disable			: 'Disable SCAYT', // MISSING
		about			: 'About SCAYT', // MISSING
		toggle			: 'Toggle SCAYT', // MISSING
		options			: 'Options', // MISSING
		langs			: 'Languages', // MISSING
		moreSuggestions	: 'More suggestions', // MISSING
		ignore			: 'Ignore', // MISSING
		ignoreAll		: 'Ignore All', // MISSING
		addWord			: 'Add Word', // MISSING
		emptyDic		: 'Dictionary name should not be empty.', // MISSING

		optionsTab		: 'Options', // MISSING
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'Languages', // MISSING

		dictionariesTab	: 'Dictionaries', // MISSING
		dic_field_name	: 'Dictionary name', // MISSING
		dic_create		: 'Create', // MISSING
		dic_restore		: 'Restore', // MISSING
		dic_delete		: 'Delete', // MISSING
		dic_rename		: 'Rename', // MISSING
		dic_info		: 'Initially the User Dictionary is stored in a Cookie. However, Cookies are limited in size. When the User Dictionary grows to a point where it cannot be stored in a Cookie, then the dictionary may be stored on our server. To store your personal dictionary on our server you should specify a name for your dictionary. If you already have a stored dictionary, please type it\'s name and click the Restore button.', // MISSING

		aboutTab		: 'About' // MISSING
	},

	about :
	{
		title		: 'About CKEditor', // MISSING
		dlgTitle	: 'About CKEditor', // MISSING
		moreInfo	: 'For licensing information please visit our web site:', // MISSING
		copy		: 'Copyright &copy; $1. All rights reserved.' // MISSING
	},

	maximize : 'Maximize', // MISSING
	minimize : 'Minimize', // MISSING

	fakeobjects :
	{
		anchor	: 'Anchor', // MISSING
		flash	: 'Flash Animation', // MISSING
		div		: 'Page Break', // MISSING
		unknown	: 'Unknown Object' // MISSING
	},

	resize : 'Drag to resize', // MISSING

	colordialog :
	{
		title		: 'Select color', // MISSING
		options	:	'Color Options', // MISSING
		highlight	: 'Highlight', // MISSING
		selected	: 'Selected Color', // MISSING
		clear		: 'Clear' // MISSING
	},

	toolbarCollapse	: 'Collapse Toolbar', // MISSING
	toolbarExpand	: 'Expand Toolbar' // MISSING
};
