/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Czech language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['cs'] =
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
	save			: 'Uložit',
	preview			: 'N&aacute;hled',
	cut				: 'Vyjmout',
	copy			: 'Kop&iacute;rovat',
	paste			: 'Vložit',
	print			: 'Tisk',
	underline		: 'Podtržen&eacute;',
	bold			: 'Tučn&eacute;',
	italic			: 'Kurz&iacute;va',
	selectAll		: 'Vybrat vše',
	removeFormat	: 'Odstranit form&aacute;tov&aacute;n&iacute;',
	strike			: 'Přeškrtnut&eacute;',
	subscript		: 'Doln&iacute; index',
	superscript		: 'Horn&iacute; index',
	horizontalrule	: 'Vložit vodorovnou linku',
	pagebreak		: 'Vložit konec str&aacute;nky',
	unlink			: 'Odstranit odkaz',
	undo			: 'Zpět',
	redo			: 'Znovu',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Vybrat na serveru',
		url				: 'URL',
		protocol		: 'Protokol',
		upload			: 'Odeslat',
		uploadSubmit	: 'Odeslat na server',
		image			: 'Obr&aacute;zek',
		flash			: 'Flash',
		form			: 'Formul&aacute;ř',
		checkbox		: 'Zaškrt&aacute;vac&iacute; pol&iacute;čko',
		radio			: 'Přep&iacute;nač',
		textField		: 'Textov&eacute; pole',
		textarea		: 'Textov&aacute; oblast',
		hiddenField		: 'Skryt&eacute; pole',
		button			: 'Tlač&iacute;tko',
		select			: 'Seznam',
		imageButton		: 'Obr&aacute;zkov&eacute; tlač&iacute;tko',
		notSet			: '<nenastaveno>',
		id				: 'Id',
		name			: 'Jm&eacute;no',
		langDir			: 'Orientace jazyka',
		langDirLtr		: 'Zleva do prava (LTR)',
		langDirRtl		: 'Zprava do leva (RTL)',
		langCode		: 'K&oacute;d jazyka',
		longDescr		: 'Dlouhý popis URL',
		cssClass		: 'Tř&iacute;da stylu',
		advisoryTitle	: 'Pomocný titulek',
		cssStyle		: 'Styl',
		ok				: 'OK',
		cancel			: 'Storno',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: 'Obecn&eacute;',
		advancedTab		: 'Rozš&iacute;řen&eacute;',
		validateNumberFailed : 'Zadan&aacute; hodnota nen&iacute; č&iacute;seln&aacute;.',
		confirmNewPage	: 'Jak&eacute;koliv neuložen&eacute; změny obsahu budou ztraceny. Skutečně chete otevr&iacute;t novou str&aacute;nku?',
		confirmCancel	: 'Někter&aacute; z nastaven&iacute; byla změněna. Skutečně chete zavř&iacute;t dialogov&eacute; okno?',
		options			: 'Options', // MISSING
		target			: 'Target', // MISSING
		targetNew		: 'New Window (_blank)', // MISSING
		targetTop		: 'Topmost Window (_top)', // MISSING
		targetSelf		: 'Same Window (_self)', // MISSING
		targetParent	: 'Parent Window (_parent)', // MISSING

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, nedostupn&eacute;</span>'
	},

	contextmenu :
	{
		options : 'Context Menu Options' // MISSING
	},

	// Special char dialog.
	specialChar		:
	{
		toolbar		: 'Vložit speci&aacute;ln&iacute; znaky',
		title		: 'Výběr speci&aacute;ln&iacute;ho znaku',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Vložit/změnit odkaz',
		other 		: '<jiný>',
		menu		: 'Změnit odkaz',
		title		: 'Odkaz',
		info		: 'Informace o odkazu',
		target		: 'C&iacute;l',
		upload		: 'Odeslat',
		advanced	: 'Rozš&iacute;řen&eacute;',
		type		: 'Typ odkazu',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'Kotva v t&eacute;to str&aacute;nce',
		toEmail		: 'E-Mail',
		targetFrame		: '<r&aacute;mec>',
		targetPopup		: '<vyskakovac&iacute; okno>',
		targetFrameName	: 'N&aacute;zev c&iacute;lov&eacute;ho r&aacute;mu',
		targetPopupName	: 'N&aacute;zev vyskakovac&iacute;ho okna',
		popupFeatures	: 'Vlastnosti vyskakovac&iacute;ho okna',
		popupResizable	: 'Umožňuj&iacute;c&iacute; měnit velikost',
		popupStatusBar	: 'Stavový ř&aacute;dek',
		popupLocationBar: 'Panel um&iacute;stěn&iacute;',
		popupToolbar	: 'Panel n&aacute;strojů',
		popupMenuBar	: 'Panel nab&iacute;dky',
		popupFullScreen	: 'Cel&aacute; obrazovka (IE)',
		popupScrollBars	: 'Posuvn&iacute;ky',
		popupDependent	: 'Z&aacute;vislost (Netscape)',
		popupWidth		: 'Š&iacute;řka',
		popupLeft		: 'Levý okraj',
		popupHeight		: 'Výška',
		popupTop		: 'Horn&iacute; okraj',
		id				: 'Id',
		langDir			: 'Orientace jazyka',
		langDirLTR		: 'Zleva do prava (LTR)',
		langDirRTL		: 'Zprava do leva (RTL)',
		acccessKey		: 'Př&iacute;stupový kl&iacute;č',
		name			: 'Jm&eacute;no',
		langCode		: 'Orientace jazyka',
		tabIndex		: 'Pořad&iacute; prvku',
		advisoryTitle	: 'Pomocný titulek',
		advisoryContentType	: 'Pomocný typ obsahu',
		cssClasses		: 'Tř&iacute;da stylu',
		charset			: 'Přiřazen&aacute; znakov&aacute; sada',
		styles			: 'Styl',
		selectAnchor	: 'Vybrat kotvu',
		anchorName		: 'Podle jm&eacute;na kotvy',
		anchorId		: 'Podle Id objektu',
		emailAddress	: 'E-Mailov&aacute; adresa',
		emailSubject	: 'Předmět zpr&aacute;vy',
		emailBody		: 'Tělo zpr&aacute;vy',
		noAnchors		: '(Ve str&aacute;nce nen&iacute; definov&aacute;na ž&aacute;dn&aacute; kotva!)',
		noUrl			: 'Zadejte pros&iacute;m URL odkazu',
		noEmail			: 'Zadejte pros&iacute;m e-mailovou adresu'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Vlož&iacute;t/změnit z&aacute;ložku',
		menu		: 'Vlastnosti z&aacute;ložky',
		title		: 'Vlastnosti z&aacute;ložky',
		name		: 'N&aacute;zev z&aacute;ložky',
		errorName	: 'Zadejte pros&iacute;m n&aacute;zev z&aacute;ložky'
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
		title				: 'Naj&iacute;t a nahradit',
		find				: 'Hledat',
		replace				: 'Nahradit',
		findWhat			: 'Co hledat:',
		replaceWith			: 'Č&iacute;m nahradit:',
		notFoundMsg			: 'Hledaný text nebyl nalezen.',
		matchCase			: 'Rozlišovat velikost p&iacute;sma',
		matchWord			: 'Pouze cel&aacute; slova',
		matchCyclic			: 'Proch&aacute;zet opakovaně',
		replaceAll			: 'Nahradit vše',
		replaceSuccessMsg	: '%1 nahrazen&iacute;.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tabulka',
		title		: 'Vlastnosti tabulky',
		menu		: 'Vlastnosti tabulky',
		deleteTable	: 'Smazat tabulku',
		rows		: 'Ř&aacute;dky',
		columns		: 'Sloupce',
		border		: 'Ohraničen&iacute;',
		align		: 'Zarovn&aacute;n&iacute;',
		alignLeft	: 'Vlevo',
		alignCenter	: 'Na střed',
		alignRight	: 'Vpravo',
		width		: 'Š&iacute;řka',
		widthPx		: 'bodů',
		widthPc		: 'procent',
		widthUnit	: 'width unit', // MISSING
		height		: 'Výška',
		cellSpace	: 'Vzd&aacute;lenost buněk',
		cellPad		: 'Odsazen&iacute; obsahu v buňce',
		caption		: 'Popis',
		summary		: 'Souhrn',
		headers		: 'Z&aacute;hlav&iacute;',
		headersNone		: 'Ž&aacute;dn&eacute;',
		headersColumn	: 'Prvn&iacute; sloupec',
		headersRow		: 'Prvn&iacute; ř&aacute;dek',
		headersBoth		: 'Oboj&iacute;',
		invalidRows		: 'Počet ř&aacute;dků mus&iacute; být č&iacute;slo větš&iacute; než 0.',
		invalidCols		: 'Počet sloupců mus&iacute; být č&iacute;slo větš&iacute; než 0.',
		invalidBorder	: 'Zdan&aacute; velikost okraje mus&iacute; být č&iacute;seln&aacute;.',
		invalidWidth	: 'Zadan&aacute; š&iacute;řka tabulky mus&iacute; být č&iacute;seln&aacute;.',
		invalidHeight	: 'zadan&aacute; výška tabulky mus&iacute; být č&iacute;seln&aacute;.',
		invalidCellSpacing	: 'Zadan&aacute; vzd&aacute;lenost buněk mus&iacute; být č&iacute;seln&aacute;.',
		invalidCellPadding	: 'Zadan&eacute; odsazen&iacute; obsahu v buňce mus&iacute; být č&iacute;seln&eacute;.',

		cell :
		{
			menu			: 'Buňka',
			insertBefore	: 'Vložit buňku před',
			insertAfter		: 'Vložit buňku za',
			deleteCell		: 'Smazat buňky',
			merge			: 'Sloučit buňky',
			mergeRight		: 'Sloučit doprava',
			mergeDown		: 'Sloučit dolů',
			splitHorizontal	: 'Rozdělit buňky vodorovně',
			splitVertical	: 'Rozdělit buňky svisle',
			title			: 'Vlastnosti buňky',
			cellType		: 'Typ buňky',
			rowSpan			: 'Spojit ř&aacute;dky',
			colSpan			: 'Spojit sloupce',
			wordWrap		: 'Zalamov&aacute;n&iacute;',
			hAlign			: 'Vodorovn&eacute; zarovn&aacute;n&iacute;',
			vAlign			: 'Svisl&eacute; zarovn&aacute;n&iacute;',
			alignTop		: 'Nahoru',
			alignMiddle		: 'Doprostřed',
			alignBottom		: 'Dolů',
			alignBaseline	: 'Na &uacute;čař&iacute;',
			bgColor			: 'Barva pozad&iacute;',
			borderColor		: 'Barva okraje',
			data			: 'Data',
			header			: 'Hlavička',
			yes				: 'Ano',
			no				: 'Ne',
			invalidWidth	: 'Zadan&aacute; š&iacute;řka buňky mus&iacute; být č&iacute;slen&aacute;.',
			invalidHeight	: 'Zadan&aacute; výška buňky mus&iacute; být č&iacute;slen&aacute;.',
			invalidRowSpan	: 'Zadaný počet sloučených ř&aacute;dků mus&iacute; být cel&eacute; č&iacute;slo.',
			invalidColSpan	: 'Zadaný počet sloučených sloupců mus&iacute; být cel&eacute; č&iacute;slo.',
			chooseColor		: 'Výběr'
		},

		row :
		{
			menu			: 'Ř&aacute;dek',
			insertBefore	: 'Vložit ř&aacute;dek před',
			insertAfter		: 'Vložit ř&aacute;dek za',
			deleteRow		: 'Smazat ř&aacute;dky'
		},

		column :
		{
			menu			: 'Sloupec',
			insertBefore	: 'Vložit sloupec před',
			insertAfter		: 'Vložit sloupec za',
			deleteColumn	: 'Smazat sloupec'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Vlastnosti tlač&iacute;tka',
		text		: 'Popisek',
		type		: 'Typ',
		typeBtn		: 'Tlač&iacute;tko',
		typeSbm		: 'Odeslat',
		typeRst		: 'Obnovit'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Vlastnosti zaškrt&aacute;vac&iacute;ho pol&iacute;čka',
		radioTitle	: 'Vlastnosti přep&iacute;nače',
		value		: 'Hodnota',
		selected	: 'Zaškrtnuto'
	},

	// Form Dialog.
	form :
	{
		title		: 'Vlastnosti formul&aacute;ře',
		menu		: 'Vlastnosti formul&aacute;ře',
		action		: 'Akce',
		method		: 'Metoda',
		encoding	: 'K&oacute;dov&aacute;n&iacute;'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Vlastnosti seznamu',
		selectInfo	: 'Info',
		opAvail		: 'Dostupn&aacute; nastaven&iacute;',
		value		: 'Hodnota',
		size		: 'Velikost',
		lines		: 'Ř&aacute;dků',
		chkMulti	: 'Povolit mnohon&aacute;sobn&eacute; výběry',
		opText		: 'Text',
		opValue		: 'Hodnota',
		btnAdd		: 'Přidat',
		btnModify	: 'Změnit',
		btnUp		: 'Nahoru',
		btnDown		: 'Dolů',
		btnSetValue : 'Nastavit jako vybranou hodnotu',
		btnDelete	: 'Smazat'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Vlastnosti textov&eacute; oblasti',
		cols		: 'Sloupců',
		rows		: 'Ř&aacute;dků'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Vlastnosti textov&eacute;ho pole',
		name		: 'N&aacute;zev',
		value		: 'Hodnota',
		charWidth	: 'Š&iacute;řka ve znac&iacute;ch',
		maxChars	: 'Maxim&aacute;ln&iacute; počet znaků',
		type		: 'Typ',
		typeText	: 'Text',
		typePass	: 'Heslo'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Vlastnosti skryt&eacute;ho pole',
		name	: 'N&aacute;zev',
		value	: 'Hodnota'
	},

	// Image Dialog.
	image :
	{
		title		: 'Vlastnosti obr&aacute;zku',
		titleButton	: 'Vlastnost&iacute; obr&aacute;zkov&eacute;ho tlač&iacute;tka',
		menu		: 'Vlastnosti obr&aacute;zku',
		infoTab		: 'Informace o obr&aacute;zku',
		btnUpload	: 'Odeslat na server',
		upload		: 'Odeslat',
		alt			: 'Alternativn&iacute; text',
		width		: 'Š&iacute;řka',
		height		: 'Výška',
		lockRatio	: 'Z&aacute;mek',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Původn&iacute; velikost',
		border		: 'Okraje',
		hSpace		: 'H-mezera',
		vSpace		: 'V-mezera',
		align		: 'Zarovn&aacute;n&iacute;',
		alignLeft	: 'Vlevo',
		alignRight	: 'Vpravo',
		alertUrl	: 'Zadejte pros&iacute;m URL obr&aacute;zku',
		linkTab		: 'Odkaz',
		button2Img	: 'Skutečně chcete přev&eacute;st zvolen&eacute; obr&aacute;zkov&eacute; tlač&iacute;tko na obyčejný obr&aacute;zek?',
		img2Button	: 'Skutečně chcete přev&eacute;st zvolený obr&aacute;zek na obr&aacute;zkov&eacute; tlač&iacute;tko?',
		urlMissing	: 'Zadan&eacute; URL zdroje obr&aacute;zku nebylo nalezeno.',
		validateWidth	: 'Width must be a whole number.', // MISSING
		validateHeight	: 'Height must be a whole number.', // MISSING
		validateBorder	: 'Border must be a whole number.', // MISSING
		validateHSpace	: 'HSpace must be a whole number.', // MISSING
		validateVSpace	: 'VSpace must be a whole number.' // MISSING
	},

	// Flash Dialog
	flash :
	{
		properties		: 'Vlastnosti Flashe',
		propertiesTab	: 'Vlastnosti',
		title			: 'Vlastnosti Flashe',
		chkPlay			: 'Automatick&eacute; spuštěn&iacute;',
		chkLoop			: 'Opakov&aacute;n&iacute;',
		chkMenu			: 'Nab&iacute;dka Flash',
		chkFull			: 'Povolit celoobrazovkový režim',
 		scale			: 'Zobrazit',
		scaleAll		: 'Zobrazit vše',
		scaleNoBorder	: 'Bez okraje',
		scaleFit		: 'Přizpůsobit',
		access			: 'Př&iacute;stup ke skriptu',
		accessAlways	: 'Vždy',
		accessSameDomain: 'Ve stejn&eacute; dom&eacute;ně',
		accessNever		: 'Nikdy',
		align			: 'Zarovn&aacute;n&iacute;',
		alignLeft		: 'Vlevo',
		alignAbsBottom	: 'Zcela dolů',
		alignAbsMiddle	: 'Doprostřed',
		alignBaseline	: 'Na &uacute;čař&iacute;',
		alignBottom		: 'Dolů',
		alignMiddle		: 'Na střed',
		alignRight		: 'Vpravo',
		alignTextTop	: 'Na horn&iacute; okraj textu',
		alignTop		: 'Nahoru',
		quality			: 'Kvalita',
		qualityBest		: 'Nejlepš&iacute;',
		qualityHigh		: 'Vysok&aacute;',
		qualityAutoHigh	: 'Vysok&aacute; - auto',
		qualityMedium	: 'Středn&iacute;',
		qualityAutoLow	: 'N&iacute;zk&aacute; - auto',
		qualityLow		: 'Nejnižš&iacute;',
		windowModeWindow: 'Okno',
		windowModeOpaque: 'Neprůhledn&eacute;',
		windowModeTransparent : 'Průhledn&eacute;',
		windowMode		: 'Režim okna',
		flashvars		: 'Proměnn&eacute; pro Flash',
		bgcolor			: 'Barva pozad&iacute;',
		width			: 'Š&iacute;řka',
		height			: 'Výška',
		hSpace			: 'H-mezera',
		vSpace			: 'V-mezera',
		validateSrc		: 'Zadejte pros&iacute;m URL odkazu',
		validateWidth	: 'Zadan&aacute; š&iacute;řka mus&iacute; být č&iacute;slo.',
		validateHeight	: 'Zadan&aacute; výška mus&iacute; být č&iacute;slo.',
		validateHSpace	: 'Zadan&aacute; H-mezera mus&iacute; být č&iacute;slo.',
		validateVSpace	: 'Zadan&aacute; V-mezera mus&iacute; být č&iacute;slo.'
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Zkontrolovat pravopis',
		title			: 'Kontrola pravopisu',
		notAvailable	: 'Omlouv&aacute;me se, ale služba nyn&iacute; nen&iacute; dostupn&aacute;.',
		errorLoading	: 'Chyba nahr&aacute;v&aacute;n&iacute; služby aplikace z: %s.',
		notInDic		: 'Nen&iacute; ve slovn&iacute;ku',
		changeTo		: 'Změnit na',
		btnIgnore		: 'Přeskočit',
		btnIgnoreAll	: 'Přeskakovat vše',
		btnReplace		: 'Zaměnit',
		btnReplaceAll	: 'Zaměňovat vše',
		btnUndo			: 'Zpět',
		noSuggestions	: '- ž&aacute;dn&eacute; n&aacute;vrhy -',
		progress		: 'Prob&iacute;h&aacute; kontrola pravopisu...',
		noMispell		: 'Kontrola pravopisu dokončena: Ž&aacute;dn&eacute; pravopisn&eacute; chyby nenalezeny',
		noChanges		: 'Kontrola pravopisu dokončena: Beze změn',
		oneChange		: 'Kontrola pravopisu dokončena: Jedno slovo změněno',
		manyChanges		: 'Kontrola pravopisu dokončena: %1 slov změněno',
		ieSpellDownload	: 'Kontrola pravopisu nen&iacute; nainstalov&aacute;na. Chcete ji nyn&iacute; st&aacute;hnout?'
	},

	smiley :
	{
		toolbar	: 'Smajl&iacute;ky',
		title	: 'Vkl&aacute;d&aacute;n&iacute; smajl&iacute;ků',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : '%1 objekt'
	},

	numberedlist	: 'Č&iacute;slov&aacute;n&iacute;',
	bulletedlist	: 'Odr&aacute;žky',
	indent			: 'Zvětšit odsazen&iacute;',
	outdent			: 'Zmenšit odsazen&iacute;',

	justify :
	{
		left	: 'Zarovnat vlevo',
		center	: 'Zarovnat na střed',
		right	: 'Zarovnat vpravo',
		block	: 'Zarovnat do bloku'
	},

	blockquote : 'Citace',

	clipboard :
	{
		title		: 'Vložit',
		cutError	: 'Bezpečnostn&iacute; nastaven&iacute; Vašeho prohl&iacute;žeče nedovoluj&iacute; editoru spustit funkci pro vyjmut&iacute; zvolen&eacute;ho textu do schr&aacute;nky. Pros&iacute;m vyjměte zvolený text do schr&aacute;nky pomoc&iacute; kl&aacute;vesnice (Ctrl/Cmd+X).',
		copyError	: 'Bezpečnostn&iacute; nastaven&iacute; Vašeho prohl&iacute;žeče nedovoluj&iacute; editoru spustit funkci pro kop&iacute;rov&aacute;n&iacute; zvolen&eacute;ho textu do schr&aacute;nky. Pros&iacute;m zkop&iacute;rujte zvolený text do schr&aacute;nky pomoc&iacute; kl&aacute;vesnice (Ctrl/Cmd+C).',
		pasteMsg	: 'Do n&aacute;sleduj&iacute;c&iacute;ho pole vložte požadovaný obsah pomoc&iacute; kl&aacute;vesnice (<STRONG>Ctrl/Cmd+V</STRONG>) a stiskněte <STRONG>OK</STRONG>.',
		securityMsg	: 'Z důvodů nastaven&iacute; bezpečnosti Vašeho prohl&iacute;žeče nemůže editor přistupovat př&iacute;mo do schr&aacute;nky. Obsah schr&aacute;nky pros&iacute;m vložte znovu do tohoto okna.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'Jak je vidět, vkl&aacute;daný text je kop&iacute;rov&aacute;n z Wordu. Chcete jej před vložen&iacute;m vyčistit?',
		toolbar			: 'Vložit z Wordu',
		title			: 'Vložit z Wordu',
		error			: 'It was not possible to clean up the pasted data due to an internal error' // MISSING
	},

	pasteText :
	{
		button	: 'Vložit jako čistý text',
		title	: 'Vložit jako čistý text'
	},

	templates :
	{
		button			: 'Šablony',
		title			: 'Šablony obsahu',
		options : 'Template Options', // MISSING
		insertOption	: 'Nahradit aktu&aacute;ln&iacute; obsah',
		selectPromptMsg	: 'Pros&iacute;m zvolte šablonu pro otevřen&iacute; v editoru<br>(aktu&aacute;ln&iacute; obsah editoru bude ztracen):',
		emptyListMsg	: '(Nen&iacute; definov&aacute;na ž&aacute;dn&aacute; šablona)'
	},

	showBlocks : 'Uk&aacute;zat bloky',

	stylesCombo :
	{
		label		: 'Styl',
		panelTitle	: 'Formatting Styles', // MISSING
		panelTitle1	: 'Blokov&eacute; styly',
		panelTitle2	: 'Ř&aacute;dkov&eacute; styly',
		panelTitle3	: 'Objektov&eacute; styly'
	},

	format :
	{
		label		: 'Form&aacute;t',
		panelTitle	: 'Form&aacute;t',

		tag_p		: 'Norm&aacute;ln&iacute;',
		tag_pre		: 'Naform&aacute;tov&aacute;no',
		tag_address	: 'Adresa',
		tag_h1		: 'Nadpis 1',
		tag_h2		: 'Nadpis 2',
		tag_h3		: 'Nadpis 3',
		tag_h4		: 'Nadpis 4',
		tag_h5		: 'Nadpis 5',
		tag_h6		: 'Nadpis 6',
		tag_div		: 'Norm&aacute;ln&iacute; (DIV)'
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
		voiceLabel	: 'P&iacute;smo',
		panelTitle	: 'P&iacute;smo'
	},

	fontSize :
	{
		label		: 'Velikost',
		voiceLabel	: 'Velikost p&iacute;sma',
		panelTitle	: 'Velikost'
	},

	colorButton :
	{
		textColorTitle	: 'Barva textu',
		bgColorTitle	: 'Barva pozad&iacute;',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Automaticky',
		more			: 'V&iacute;ce barev...'
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
		title			: 'Kontrola pravopisu během psan&iacute; (SCAYT)',
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'Zapnout SCAYT',
		disable			: 'Vypnout SCAYT',
		about			: 'O aplikaci SCAYT',
		toggle			: 'Vyp&iacute;nač SCAYT',
		options			: 'Nastaven&iacute;',
		langs			: 'Jazyky',
		moreSuggestions	: 'V&iacute;ce n&aacute;vrhů',
		ignore			: 'Přeskočit',
		ignoreAll		: 'Přeskočit vše',
		addWord			: 'Přidat slovo',
		emptyDic		: 'N&aacute;zev slovn&iacute;ku nesm&iacute; být pr&aacute;zdný.',

		optionsTab		: 'Nastaven&iacute;',
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'Jazyky',

		dictionariesTab	: 'Slovn&iacute;ky',
		dic_field_name	: 'Dictionary name', // MISSING
		dic_create		: 'Create', // MISSING
		dic_restore		: 'Restore', // MISSING
		dic_delete		: 'Delete', // MISSING
		dic_rename		: 'Rename', // MISSING
		dic_info		: 'Initially the User Dictionary is stored in a Cookie. However, Cookies are limited in size. When the User Dictionary grows to a point where it cannot be stored in a Cookie, then the dictionary may be stored on our server. To store your personal dictionary on our server you should specify a name for your dictionary. If you already have a stored dictionary, please type it\'s name and click the Restore button.', // MISSING

		aboutTab		: 'O aplikaci'
	},

	about :
	{
		title		: 'O aplikaci CKEditor',
		dlgTitle	: 'O aplikaci CKEditor',
		moreInfo	: 'Pro informace o lincenci navštivte naši webovou str&aacute;nku:',
		copy		: 'Copyright &copy; $1. All rights reserved.'
	},

	maximize : 'Maximalizovat',
	minimize : 'Minimalizovat',

	fakeobjects :
	{
		anchor	: 'Z&aacute;ložka',
		flash	: 'Flash animace',
		div		: 'Zalomen&iacute; str&aacute;nky',
		unknown	: 'Nezn&aacute;mý objekt'
	},

	resize : 'Uchopit pro změnu velikosti',

	colordialog :
	{
		title		: 'Výběr barvy',
		options	:	'Color Options', // MISSING
		highlight	: 'Zvýraznit',
		selected	: 'Vybr&aacute;no',
		clear		: 'Vyčistit'
	},

	toolbarCollapse	: 'Collapse Toolbar', // MISSING
	toolbarExpand	: 'Expand Toolbar' // MISSING
};
