/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Faroese language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['fo'] =
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
	source			: 'Kelda',
	newPage			: 'Nýggj s&iacute;ða',
	save			: 'Goym',
	preview			: 'Frumsýning',
	cut				: 'Kvett',
	copy			: 'Avrita',
	paste			: 'Innrita',
	print			: 'Prenta',
	underline		: 'Undirstrikað',
	bold			: 'Feit skrift',
	italic			: 'Skr&aacute;skrift',
	selectAll		: 'Markera alt',
	removeFormat	: 'Strika sniðgeving',
	strike			: 'Yvirstrikað',
	subscript		: 'Lækkað skrift',
	superscript		: 'Hækkað skrift',
	horizontalrule	: 'Ger vatnrætta linju',
	pagebreak		: 'Ger s&iacute;ðuskift',
	unlink			: 'Strika tilknýti',
	undo			: 'Angra',
	redo			: 'Vend aftur',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Ambætarakagi',
		url				: 'URL',
		protocol		: 'Protokoll',
		upload			: 'Send til ambætaran',
		uploadSubmit	: 'Send til ambætaran',
		image			: 'Myndir',
		flash			: 'Flash',
		form			: 'Formur',
		checkbox		: 'Flugubein',
		radio			: 'Radioknøttur',
		textField		: 'Tekstteigur',
		textarea		: 'Tekstumr&aacute;ði',
		hiddenField		: 'Fjaldur teigur',
		button			: 'Knøttur',
		select			: 'Valskr&aacute;',
		imageButton		: 'Myndaknøttur',
		notSet			: '<ikki sett>',
		id				: 'Id',
		name			: 'Navn',
		langDir			: 'Tekstk&oacute;s',
		langDirLtr		: 'Fr&aacute; vinstru til høgru (LTR)',
		langDirRtl		: 'Fr&aacute; høgru til vinstru (RTL)',
		langCode		: 'M&aacute;lkoda',
		longDescr		: 'V&iacute;ðkað URL fr&aacute;greiðing',
		cssClass		: 'Typografi klassar',
		advisoryTitle	: 'Vegleiðandi heiti',
		cssStyle		: 'Typografi',
		ok				: 'G&oacute;ðkent',
		cancel			: 'Avlýst',
		close			: 'Lat aftur',
		preview			: 'Frumsýn',
		generalTab		: 'Generelt',
		advancedTab		: 'Fjølbroytt',
		validateNumberFailed : 'Hetta er ikki eitt tal.',
		confirmNewPage	: 'Allar ikki goymdar broytingar &iacute; hesum innihaldi hvørva. Skal nýggj s&iacute;ða lesast kortini?',
		confirmCancel	: 'Nakrir valmøguleikar eru broyttir. Ert t&uacute; v&iacute;sur &iacute;, at dialogurin skal latast aftur?',
		options			: 'Options', // MISSING
		target			: 'Target', // MISSING
		targetNew		: 'Nýtt vindeyga (_blank)',
		targetTop		: 'Vindeyga ovast (_top)',
		targetSelf		: 'Sama vindeyga (_self)',
		targetParent	: 'Upphavligt vindeyga (_parent)',

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, ikki tøkt</span>'
	},

	contextmenu :
	{
		options : 'Context Menu Options' // MISSING
	},

	// Special char dialog.
	specialChar		:
	{
		toolbar		: 'Set inn sertekn',
		title		: 'Vel sertekn',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Ger/broyt tilknýti',
		other 		: '<other>', // MISSING
		menu		: 'Broyt tilknýti',
		title		: 'Tilknýti',
		info		: 'Tilknýtis upplýsingar',
		target		: 'Target', // MISSING
		upload		: 'Send til ambætaran',
		advanced	: 'Fjølbroytt',
		type		: 'Tilknýtisslag',
		toUrl		: 'URL',
		toAnchor	: 'Tilknýti til marknastein &iacute; tekstinum',
		toEmail		: 'Teldupostur',
		targetFrame		: '<ramma>',
		targetPopup		: '<popup vindeyga>',
		targetFrameName	: 'V&iacute;s navn vindeygans',
		targetPopupName	: 'Popup vindeygans navn',
		popupFeatures	: 'Popup vindeygans v&iacute;ðkaðu eginleikar',
		popupResizable	: 'Resizable', // MISSING
		popupStatusBar	: 'Støðufr&aacute;greiðingarbj&aacute;lki',
		popupLocationBar: 'Adressulinja',
		popupToolbar	: 'Amboðsbj&aacute;lki',
		popupMenuBar	: 'Skr&aacute;bj&aacute;lki',
		popupFullScreen	: 'Fullur skermur (IE)',
		popupScrollBars	: 'Rullibj&aacute;lki',
		popupDependent	: 'Bundið (Netscape)',
		popupWidth		: 'Breidd',
		popupLeft		: 'Fr&aacute;støða fr&aacute; vinstru',
		popupHeight		: 'Hædd',
		popupTop		: 'Fr&aacute;støða fr&aacute; &iacute;erva',
		id				: 'Id', // MISSING
		langDir			: 'Tekstk&oacute;s',
		langDirLTR		: 'Fr&aacute; vinstru til høgru (LTR)',
		langDirRTL		: 'Fr&aacute; høgru til vinstru (RTL)',
		acccessKey		: 'Snarvegisknappur',
		name			: 'Navn',
		langCode		: 'Tekstk&oacute;s',
		tabIndex		: 'Inntriv indeks',
		advisoryTitle	: 'Vegleiðandi heiti',
		advisoryContentType	: 'Vegleiðandi innihaldsslag',
		cssClasses		: 'Typografi klassar',
		charset			: 'Atknýtt teknsett',
		styles			: 'Typografi',
		selectAnchor	: 'Vel ein marknastein',
		anchorName		: 'Eftir navni &aacute; marknasteini',
		anchorId		: 'Eftir element Id',
		emailAddress	: 'Teldupost-adressa',
		emailSubject	: 'Evni',
		emailBody		: 'Breyðtekstur',
		noAnchors		: '(Eingir marknasteinar eru &iacute; hesum dokumentið)',
		noUrl			: 'Vinarliga skriva tilknýti (URL)',
		noEmail			: 'Vinarliga skriva teldupost-adressu'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Ger/broyt marknastein',
		menu		: 'Eginleikar fyri marknastein',
		title		: 'Eginleikar fyri marknastein',
		name		: 'Heiti marknasteinsins',
		errorName	: 'Vinarliga rita marknasteinsins heiti'
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
		title				: 'Finn og broyt',
		find				: 'Leita',
		replace				: 'Yvirskriva',
		findWhat			: 'Finn:',
		replaceWith			: 'Yvirskriva við:',
		notFoundMsg			: 'Leititeksturin varð ikki funnin',
		matchCase			: 'Munur &aacute; st&oacute;rum og sm&aacute;um b&oacute;kstavum',
		matchWord			: 'Bert heil orð',
		matchCyclic			: 'Match cyclic', // MISSING
		replaceAll			: 'Yvirskriva alt',
		replaceSuccessMsg	: '%1 &uacute;rslit broytt.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tabell',
		title		: 'Eginleikar fyri tabell',
		menu		: 'Eginleikar fyri tabell',
		deleteTable	: 'Strika tabell',
		rows		: 'Røðir',
		columns		: 'Kolonnur',
		border		: 'Bordabreidd',
		align		: 'Justering',
		alignLeft	: 'Vinstrasett',
		alignCenter	: 'Miðsett',
		alignRight	: 'Høgrasett',
		width		: 'Breidd',
		widthPx		: 'pixels',
		widthPc		: 'prosent',
		widthUnit	: 'breiddar unit',
		height		: 'Hædd',
		cellSpace	: 'Fjarstøða millum meskar',
		cellPad		: 'Meskubreddi',
		caption		: 'Tabellfr&aacute;greiðing',
		summary		: 'Samandr&aacute;ttur',
		headers		: 'Headers', // MISSING
		headersNone		: 'Eingin',
		headersColumn	: 'Fyrsta kolonna',
		headersRow		: 'Fyrsta rað',
		headersBoth		: 'B&aacute;ðir',
		invalidRows		: 'Talið av røðum m&aacute; vera eitt tal størri enn 0.',
		invalidCols		: 'Talið av kolonnum m&aacute; vera eitt tal størri enn 0.',
		invalidBorder	: 'Borda-stødd m&aacute; vera eitt tal.',
		invalidWidth	: 'Tabell-breidd m&aacute; vera eitt tal.',
		invalidHeight	: 'Tabell-hædd m&aacute; vera eitt tal.',
		invalidCellSpacing	: 'Cell spacing m&aacute; vera eitt tal.',
		invalidCellPadding	: 'Cell padding m&aacute; vera eitt tal.',

		cell :
		{
			menu			: 'Meski',
			insertBefore	: 'Set meska inn &aacute;ðrenn',
			insertAfter		: 'Set meska inn aftan&aacute;',
			deleteCell		: 'Strika meskar',
			merge			: 'Flætta meskar',
			mergeRight		: 'Flætta meskar til høgru',
			mergeDown		: 'Flætta saman',
			splitHorizontal	: 'Kloyv meska vatnrætt',
			splitVertical	: 'Kloyv meska loddrætt',
			title			: 'Mesku eginleikar',
			cellType		: 'Mesku slag',
			rowSpan			: 'Ræð spenni',
			colSpan			: 'Kolonnu spenni',
			wordWrap		: 'Word Wrap', // MISSING
			hAlign			: 'Horisontal plasering',
			vAlign			: 'Loddrøtt plasering',
			alignTop		: 'Top',
			alignMiddle		: 'Miðja',
			alignBottom		: 'Botnur',
			alignBaseline	: 'Basislinja',
			bgColor			: 'Bakgrundslitur',
			borderColor		: 'Bordalitur',
			data			: 'Data',
			header			: 'Header',
			yes				: 'Ja',
			no				: 'Nei',
			invalidWidth	: 'Meskubreidd m&aacute; vera eitt tal.',
			invalidHeight	: 'Meskuhædd m&aacute; vera eitt tal.',
			invalidRowSpan	: 'Raðspennið m&aacute; vera eitt heiltal.',
			invalidColSpan	: 'Kolonnuspennið m&aacute; vera eitt heiltal.',
			chooseColor		: 'Vel'
		},

		row :
		{
			menu			: 'Rað',
			insertBefore	: 'Set rað inn &aacute;ðrenn',
			insertAfter		: 'Set rað inn aftan&aacute;',
			deleteRow		: 'Strika røðir'
		},

		column :
		{
			menu			: 'Kolonna',
			insertBefore	: 'Set kolonnu inn &aacute;ðrenn',
			insertAfter		: 'Set kolonnu inn aftan&aacute;',
			deleteColumn	: 'Strika kolonnur'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Eginleikar fyri knøtt',
		text		: 'Tekstur',
		type		: 'Slag',
		typeBtn		: 'Knøttur',
		typeSbm		: 'Send',
		typeRst		: 'Nullstilla'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Eginleikar fyri flugubein',
		radioTitle	: 'Eginleikar fyri radioknøtt',
		value		: 'Virði',
		selected	: 'Valt'
	},

	// Form Dialog.
	form :
	{
		title		: 'Eginleikar fyri Form',
		menu		: 'Eginleikar fyri Form',
		action		: 'Hending',
		method		: 'H&aacute;ttur',
		encoding	: 'Encoding' // MISSING
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Eginleikar fyri valskr&aacute;',
		selectInfo	: 'Upplýsingar',
		opAvail		: 'Tøkir møguleikar',
		value		: 'Virði',
		size		: 'Stødd',
		lines		: 'Linjur',
		chkMulti	: 'Loyv fleiri valmøguleikum samstundis',
		opText		: 'Tekstur',
		opValue		: 'Virði',
		btnAdd		: 'Legg afturat',
		btnModify	: 'Broyt',
		btnUp		: 'Upp',
		btnDown		: 'Niður',
		btnSetValue : 'Set sum valt virði',
		btnDelete	: 'Strika'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Eginleikar fyri tekstumr&aacute;ði',
		cols		: 'kolonnur',
		rows		: 'røðir'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Eginleikar fyri tekstteig',
		name		: 'Navn',
		value		: 'Virði',
		charWidth	: 'Breidd (sj&oacute;nlig tekn)',
		maxChars	: 'Mest loyvdu tekn',
		type		: 'Slag',
		typeText	: 'Tekstur',
		typePass	: 'Loyniorð'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Eginleikar fyri fjaldan teig',
		name	: 'Navn',
		value	: 'Virði'
	},

	// Image Dialog.
	image :
	{
		title		: 'Myndaeginleikar',
		titleButton	: 'Eginleikar fyri myndaknøtt',
		menu		: 'Myndaeginleikar',
		infoTab		: 'Myndaupplýsingar',
		btnUpload	: 'Send til ambætaran',
		upload		: 'Send',
		alt			: 'Alternativur tekstur',
		width		: 'Breidd',
		height		: 'Hædd',
		lockRatio	: 'Læs lutfallið',
		unlockRatio	: 'Lutfallið ikki læst',
		resetSize	: 'Upprunastødd',
		border		: 'Bordi',
		hSpace		: 'Høgri breddi',
		vSpace		: 'Vinstri breddi',
		align		: 'Justering',
		alignLeft	: 'Vinstra',
		alignRight	: 'Høgra',
		alertUrl	: 'Rita sl&oacute;ðina til myndina',
		linkTab		: 'Tilknýti',
		button2Img	: 'Do you want to transform the selected image button on a simple image?', // MISSING
		img2Button	: 'Do you want to transform the selected image on a image button?', // MISSING
		urlMissing	: 'URL til mynd manglar.',
		validateWidth	: 'Breidd m&aacute; vera eitt heiltal.',
		validateHeight	: 'Hædd m&aacute; vera eitt heiltal.',
		validateBorder	: 'Bordi m&aacute; vera eitt heiltal.',
		validateHSpace	: 'HSpace m&aacute; vera eitt heiltal.',
		validateVSpace	: 'VSpace m&aacute; vera eitt heiltal.'
	},

	// Flash Dialog
	flash :
	{
		properties		: 'Flash eginleikar',
		propertiesTab	: 'Eginleikar',
		title			: 'Flash eginleikar',
		chkPlay			: 'Avspælingin byrjar sj&aacute;lv',
		chkLoop			: 'Endurspæl',
		chkMenu			: 'Ger Flash skr&aacute; virkna',
		chkFull			: 'Loyv fullan skerm',
 		scale			: 'Skalering',
		scaleAll		: 'V&iacute;s alt',
		scaleNoBorder	: 'Eingin bordi',
		scaleFit		: 'Neyv skalering',
		access			: 'Script atgongd',
		accessAlways	: 'Alt&iacute;ð',
		accessSameDomain: 'Sama navnaøki',
		accessNever		: 'Ongant&iacute;ð',
		align			: 'Justering',
		alignLeft		: 'Vinstra',
		alignAbsBottom	: 'Abs botnur',
		alignAbsMiddle	: 'Abs miðja',
		alignBaseline	: 'Basislinja',
		alignBottom		: 'Botnur',
		alignMiddle		: 'Miðja',
		alignRight		: 'Høgra',
		alignTextTop	: 'Tekst toppur',
		alignTop		: 'Ovast',
		quality			: 'G&oacute;ðska',
		qualityBest		: 'Besta',
		qualityHigh		: 'Høg',
		qualityAutoHigh	: 'Auto høg',
		qualityMedium	: 'Meðal',
		qualityAutoLow	: 'Auto L&aacute;g',
		qualityLow		: 'L&aacute;g',
		windowModeWindow: 'Window', // MISSING
		windowModeOpaque: 'Ikki transparent',
		windowModeTransparent : 'Transparent',
		windowMode		: 'Window mode', // MISSING
		flashvars		: 'Variablar fyri Flash',
		bgcolor			: 'Bakgrundslitur',
		width			: 'Breidd',
		height			: 'Hædd',
		hSpace			: 'Høgri breddi',
		vSpace			: 'Vinstri breddi',
		validateSrc		: 'Vinarliga skriva tilknýti (URL)',
		validateWidth	: 'Breidd m&aacute; vera eitt tal.',
		validateHeight	: 'Hædd m&aacute; vera eitt tal.',
		validateHSpace	: 'HSpace m&aacute; vera eitt tal.',
		validateVSpace	: 'VSpace m&aacute; vera eitt tal.'
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Kanna stavseting',
		title			: 'Kanna stavseting',
		notAvailable	: 'T&iacute;verri, ikki tøkt &iacute; løtuni.',
		errorLoading	: 'Feilur við innlesing av application service host: %s.',
		notInDic		: 'Finst ikki &iacute; orðab&oacute;kini',
		changeTo		: 'Broyt til',
		btnIgnore		: 'Forfj&oacute;na',
		btnIgnoreAll	: 'Forfj&oacute;na alt',
		btnReplace		: 'Yvirskriva',
		btnReplaceAll	: 'Yvirskriva alt',
		btnUndo			: 'Angra',
		noSuggestions	: '- Einki uppskot -',
		progress		: 'Rættstavarin arbeiðir...',
		noMispell		: 'Rættstavarain liðugur: Eingin feilur funnin',
		noChanges		: 'Rættstavarain liðugur: Einki orð varð broytt',
		oneChange		: 'Rættstavarain liðugur: Eitt orð er broytt',
		manyChanges		: 'Rættstavarain liðugur: %1 orð broytt',
		ieSpellDownload	: 'Rættstavarin er ikki tøkur &iacute; tekstviðgeranum. Vilt t&uacute; heinta hann n&uacute;?'
	},

	smiley :
	{
		toolbar	: 'Smiley',
		title	: 'Vel Smiley',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Sl&oacute;ð til elementir',
		eleTitle : '%1 element'
	},

	numberedlist	: 'Talmerktur listi',
	bulletedlist	: 'Punktmerktur listi',
	indent			: 'Økja reglubrotarinntriv',
	outdent			: 'Minka reglubrotarinntriv',

	justify :
	{
		left	: 'Vinstrasett',
		center	: 'Miðsett',
		right	: 'Høgrasett',
		block	: 'Javnir tekstkantar'
	},

	blockquote : 'Blockquote',

	clipboard :
	{
		title		: 'Innrita',
		cutError	: 'Trygdaruppseting aln&oacute;tskagans forðar tekstviðgeranum &iacute; at kvetta tekstin. Vinarliga nýt knappaborðið til at kvetta tekstin (Ctrl/Cmd+X).',
		copyError	: 'Trygdaruppseting aln&oacute;tskagans forðar tekstviðgeranum &iacute; at avrita tekstin. Vinarliga nýt knappaborðið til at avrita tekstin (Ctrl/Cmd+C).',
		pasteMsg	: 'Vinarliga koyr tekstin &iacute; hendan r&uacute;tin við knappaborðinum (<strong>Ctrl/Cmd+V</strong>) og klikk &aacute; <strong>G&oacute;ðtak</strong>.',
		securityMsg	: 'Trygdaruppseting aln&oacute;tskagans forðar tekstviðgeranum &iacute; beinleiðis atgongd til avritingarminnið. Tygum mugu royna aftur &iacute; hesum r&uacute;tinum.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'Teksturin, t&uacute; roynir at seta inn, sýnist at stava fr&aacute; Word. Skal teksturin reinsast fyrst?',
		toolbar			: 'Innrita fr&aacute; Word',
		title			: 'Innrita fr&aacute; Word',
		error			: 'Tað eyðnaðist ikki at reinsa tekstin vegna ein internan feil'
	},

	pasteText :
	{
		button	: 'Innrita som reinan tekst',
		title	: 'Innrita som reinan tekst'
	},

	templates :
	{
		button			: 'Skabel&oacute;nir',
		title			: 'Innihaldsskabel&oacute;nir',
		options : 'Template Options', // MISSING
		insertOption	: 'Yvirskriva n&uacute;verandi innihald',
		selectPromptMsg	: 'Vinarliga vel ta skabel&oacute;n, ið skal opnast &iacute; tekstviðgeranum<br>(Hetta yvirskrivar n&uacute;verandi innihald):',
		emptyListMsg	: '(Ongar skabel&oacute;nir tøkar)'
	},

	showBlocks : 'V&iacute;s blokkar',

	stylesCombo :
	{
		label		: 'Typografi',
		panelTitle	: 'Formatterings st&iacute;lir',
		panelTitle1	: 'Blokk st&iacute;lir',
		panelTitle2	: 'Inline st&iacute;lir',
		panelTitle3	: 'Object st&iacute;lir'
	},

	format :
	{
		label		: 'Skriftsnið',
		panelTitle	: 'Skriftsnið',

		tag_p		: 'Vanligt',
		tag_pre		: 'Sniðgivið',
		tag_address	: 'Adressa',
		tag_h1		: 'Yvirskrift 1',
		tag_h2		: 'Yvirskrift 2',
		tag_h3		: 'Yvirskrift 3',
		tag_h4		: 'Yvirskrift 4',
		tag_h5		: 'Yvirskrift 5',
		tag_h6		: 'Yvirskrift 6',
		tag_div		: 'Normal (DIV)' // MISSING
	},

	div :
	{
		title				: 'Ger Div Container',
		toolbar				: 'Ger Div Container',
		cssClassInputLabel	: 'Stylesheet Classes',
		styleSelectLabel	: 'Style',
		IdInputLabel		: 'Id',
		languageCodeInputLabel	: ' Language Code',
		inlineStyleInputLabel	: 'Inline Style',
		advisoryTitleInputLabel	: 'Advisory Title',
		langDirLabel		: 'Language Direction',
		langDirLTRLabel		: 'Vinstru til høgru (LTR)',
		langDirRTLLabel		: 'Høgru til vinstru (RTL)',
		edit				: 'Redigera Div',
		remove				: 'Strika Div'
  	},

	font :
	{
		label		: 'Skrift',
		voiceLabel	: 'Font', // MISSING
		panelTitle	: 'Skrift'
	},

	fontSize :
	{
		label		: 'Skriftstødd',
		voiceLabel	: 'Font Size', // MISSING
		panelTitle	: 'Skriftstødd'
	},

	colorButton :
	{
		textColorTitle	: 'Tekstlitur',
		bgColorTitle	: 'Bakgrundslitur',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Automatiskt',
		more			: 'Fleiri litir...'
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
		title			: 'Kanna stavseting, meðan t&uacute; skrivar',
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'Enable SCAYT',
		disable			: 'Disable SCAYT',
		about			: 'Um SCAYT',
		toggle			: 'Toggle SCAYT',
		options			: 'Uppseting',
		langs			: 'Tungum&aacute;l',
		moreSuggestions	: 'Fleiri tilr&aacute;ðingar',
		ignore			: 'Ignorera',
		ignoreAll		: 'Ignorera alt',
		addWord			: 'Legg orð afturat',
		emptyDic		: 'Heiti &aacute; orðab&oacute;k eigur ikki at vera t&oacute;mt.',

		optionsTab		: 'Uppseting',
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'Tungum&aacute;l',

		dictionariesTab	: 'Orðabøkur',
		dic_field_name	: 'Dictionary name', // MISSING
		dic_create		: 'Create', // MISSING
		dic_restore		: 'Restore', // MISSING
		dic_delete		: 'Delete', // MISSING
		dic_rename		: 'Rename', // MISSING
		dic_info		: 'Initially the User Dictionary is stored in a Cookie. However, Cookies are limited in size. When the User Dictionary grows to a point where it cannot be stored in a Cookie, then the dictionary may be stored on our server. To store your personal dictionary on our server you should specify a name for your dictionary. If you already have a stored dictionary, please type it\'s name and click the Restore button.', // MISSING

		aboutTab		: 'Um'
	},

	about :
	{
		title		: 'Um CKEditor',
		dlgTitle	: 'Um CKEditor',
		moreInfo	: 'Licens upplýsingar finnast &aacute; heimas&iacute;ðu okkara:',
		copy		: 'Copyright &copy; $1. All rights reserved.' // MISSING
	},

	maximize : 'Maksimera',
	minimize : 'Minimera',

	fakeobjects :
	{
		anchor	: 'Anchor', // MISSING
		flash	: 'Flash Animation', // MISSING
		div		: 'S&iacute;ðuskift',
		unknown	: '&oacute;kent Object'
	},

	resize : 'Drag fyri at broyta stødd',

	colordialog :
	{
		title		: 'Vel lit',
		options	:	'Color Options', // MISSING
		highlight	: 'Highlight', // MISSING
		selected	: 'Selected Color', // MISSING
		clear		: 'Clear' // MISSING
	},

	toolbarCollapse	: 'Collapse Toolbar', // MISSING
	toolbarExpand	: 'Expand Toolbar' // MISSING
};
