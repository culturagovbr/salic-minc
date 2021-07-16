/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Icelandic language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['is'] =
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
	source			: 'K&oacute;ði',
	newPage			: 'Ný s&iacute;ða',
	save			: 'Vista',
	preview			: 'Forskoða',
	cut				: 'Klippa',
	copy			: 'Afrita',
	paste			: 'L&iacute;ma',
	print			: 'Prenta',
	underline		: 'Undirstrikað',
	bold			: 'Feitletrað',
	italic			: 'Sk&aacute;letrað',
	selectAll		: 'Velja allt',
	removeFormat	: 'Fjarlægja snið',
	strike			: 'Yfirstrikað',
	subscript		: 'Niðurskrifað',
	superscript		: 'Uppskrifað',
	horizontalrule	: 'L&oacute;ðr&eacute;tt l&iacute;na',
	pagebreak		: 'Setja inn s&iacute;ðuskil',
	unlink			: 'Fjarlægja stiklu',
	undo			: 'Afturkalla',
	redo			: 'Hætta við afturköllun',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Fletta &iacute; skjalasafni',
		url				: 'Vefsl&oacute;ð',
		protocol		: 'Samskiptastaðall',
		upload			: 'Senda upp',
		uploadSubmit	: 'Hlaða upp',
		image			: 'Setja inn mynd',
		flash			: 'Flash',
		form			: 'Setja inn innsl&aacute;ttarform',
		checkbox		: 'Setja inn hökunarreit',
		radio			: 'Setja inn valhnapp',
		textField		: 'Setja inn textareit',
		textarea		: 'Setja inn textasvæði',
		hiddenField		: 'Setja inn falið svæði',
		button			: 'Setja inn hnapp',
		select			: 'Setja inn lista',
		imageButton		: 'Setja inn myndahnapp',
		notSet			: '<ekkert valið>',
		id				: 'Auðkenni',
		name			: 'Nafn',
		langDir			: 'Lesstefna',
		langDirLtr		: 'Fr&aacute; vinstri til hægri (LTR)',
		langDirRtl		: 'Fr&aacute; hægri til vinstri (RTL)',
		langCode		: 'Tungum&aacute;lak&oacute;ði',
		longDescr		: 'N&aacute;nari lýsing',
		cssClass		: 'St&iacute;lsniðsflokkur',
		advisoryTitle	: 'Titill',
		cssStyle		: 'St&iacute;ll',
		ok				: '&iacute; lagi',
		cancel			: 'Hætta við',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: 'Almennt',
		advancedTab		: 'Tæknilegt',
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
		toolbar		: 'Setja inn merki',
		title		: 'Velja t&aacute;kn',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Stofna/breyta stiklu',
		other 		: '<annar>',
		menu		: 'Breyta stiklu',
		title		: 'Stikla',
		info		: 'Almennt',
		target		: 'Mark',
		upload		: 'Senda upp',
		advanced	: 'Tæknilegt',
		type		: 'Stikluflokkur',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'B&oacute;kamerki &aacute; þessari s&iacute;ðu',
		toEmail		: 'Netfang',
		targetFrame		: '<rammi>',
		targetPopup		: '<sprettigluggi>',
		targetFrameName	: 'Nafn markglugga',
		targetPopupName	: 'Nafn sprettiglugga',
		popupFeatures	: 'Eigindi sprettiglugga',
		popupResizable	: 'Resizable', // MISSING
		popupStatusBar	: 'Stöðustika',
		popupLocationBar: 'Fangl&iacute;na',
		popupToolbar	: 'Verkfærastika',
		popupMenuBar	: 'Vall&iacute;na',
		popupFullScreen	: 'Heilskj&aacute;r (IE)',
		popupScrollBars	: 'Skrunstikur',
		popupDependent	: 'H&aacute;ð venslum (Netscape)',
		popupWidth		: 'Breidd',
		popupLeft		: 'Fjarlægð fr&aacute; vinstri',
		popupHeight		: 'Hæð',
		popupTop		: 'Fjarlægð fr&aacute; efri br&uacute;n',
		id				: 'Id', // MISSING
		langDir			: 'Lesstefna',
		langDirLTR		: 'Fr&aacute; vinstri til hægri (LTR)',
		langDirRTL		: 'Fr&aacute; hægri til vinstri (RTL)',
		acccessKey		: 'Skammvalshnappur',
		name			: 'Nafn',
		langCode		: 'Lesstefna',
		tabIndex		: 'Raðn&uacute;mer innsl&aacute;ttarreits',
		advisoryTitle	: 'Titill',
		advisoryContentType	: 'Tegund innihalds',
		cssClasses		: 'St&iacute;lsniðsflokkur',
		charset			: 'T&aacute;knr&oacute;f',
		styles			: 'St&iacute;ll',
		selectAnchor	: 'Veldu akkeri',
		anchorName		: 'Eftir akkerisnafni',
		anchorId		: 'Eftir auðkenni einingar',
		emailAddress	: 'Netfang',
		emailSubject	: 'Efni',
		emailBody		: 'Meginm&aacute;l',
		noAnchors		: '<Engin b&oacute;kamerki &aacute; skr&aacute;>',
		noUrl			: 'Sl&aacute;ðu inn veffang stiklunnar!',
		noEmail			: 'Sl&aacute;ðu inn netfang!'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Stofna/breyta kaflamerki',
		menu		: 'Eigindi kaflamerkis',
		title		: 'Eigindi kaflamerkis',
		name		: 'Nafn b&oacute;kamerkis',
		errorName	: 'Sl&aacute;ðu inn nafn b&oacute;kamerkis!'
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
		title				: 'Finna og skipta',
		find				: 'Leita',
		replace				: 'Skipta &uacute;t',
		findWhat			: 'Leita að:',
		replaceWith			: 'Skipta &uacute;t fyrir:',
		notFoundMsg			: 'Leitartexti fannst ekki!',
		matchCase			: 'Gera greinarmun &aacute;¡ h&aacute;¡- og l&aacute;gstöfum',
		matchWord			: 'Aðeins heil orð',
		matchCyclic			: 'Match cyclic', // MISSING
		replaceAll			: 'Skipta &uacute;t allsstaðar',
		replaceSuccessMsg	: '%1 occurrence(s) replaced.' // MISSING
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tafla',
		title		: 'Eigindi töflu',
		menu		: 'Eigindi töflu',
		deleteTable	: 'Fella töflu',
		rows		: 'Raðir',
		columns		: 'D&aacute;lkar',
		border		: 'Breidd ramma',
		align		: 'Jöfnun',
		alignLeft	: 'Vinstrijafnað',
		alignCenter	: 'Miðjað',
		alignRight	: 'Hægrijafnað',
		width		: 'Breidd',
		widthPx		: 'myndeindir',
		widthPc		: 'pr&oacute;sent',
		widthUnit	: 'width unit', // MISSING
		height		: 'Hæð',
		cellSpace	: 'Bil milli reita',
		cellPad		: 'Reitasp&aacute;ss&iacute;a',
		caption		: 'Titill',
		summary		: '&aacute;fram',
		headers		: 'Fyrirsagnir',
		headersNone		: 'Engar',
		headersColumn	: 'Fyrsti d&aacute;lkur',
		headersRow		: 'Fyrsta röð',
		headersBoth		: 'Hvort tveggja',
		invalidRows		: 'Number of rows must be a number greater than 0.', // MISSING
		invalidCols		: 'Number of columns must be a number greater than 0.', // MISSING
		invalidBorder	: 'Border size must be a number.', // MISSING
		invalidWidth	: 'Table width must be a number.', // MISSING
		invalidHeight	: 'Table height must be a number.', // MISSING
		invalidCellSpacing	: 'Cell spacing must be a number.', // MISSING
		invalidCellPadding	: 'Cell padding must be a number.', // MISSING

		cell :
		{
			menu			: 'Reitur',
			insertBefore	: 'Skj&oacute;ta inn reiti fyrir aftan',
			insertAfter		: 'Skj&oacute;ta inn reiti fyrir framan',
			deleteCell		: 'Fella reit',
			merge			: 'Sameina reiti',
			mergeRight		: 'Sameina til hægri',
			mergeDown		: 'Sameina niður &aacute; við',
			splitHorizontal	: 'Klj&uacute;fa reit l&aacute;r&eacute;tt',
			splitVertical	: 'Klj&uacute;fa reit l&oacute;ðr&eacute;tt',
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
			menu			: 'Röð',
			insertBefore	: 'Skj&oacute;ta inn röð fyrir ofan',
			insertAfter		: 'Skj&oacute;ta inn röð fyrir neðan',
			deleteRow		: 'Eyða röð'
		},

		column :
		{
			menu			: 'D&aacute;lkur',
			insertBefore	: 'Skj&oacute;ta inn d&aacute;lki vinstra megin',
			insertAfter		: 'Skj&oacute;ta inn d&aacute;lki hægra megin',
			deleteColumn	: 'Fella d&aacute;lk'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Eigindi hnapps',
		text		: 'Texti',
		type		: 'Gerð',
		typeBtn		: 'Hnappur',
		typeSbm		: 'Staðfesta',
		typeRst		: 'Hreinsa'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Eigindi markreits',
		radioTitle	: 'Eigindi valhnapps',
		value		: 'Gildi',
		selected	: 'Valið'
	},

	// Form Dialog.
	form :
	{
		title		: 'Eigindi innsl&aacute;ttarforms',
		menu		: 'Eigindi innsl&aacute;ttarforms',
		action		: 'Aðgerð',
		method		: 'Aðferð',
		encoding	: 'Encoding' // MISSING
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Eigindi lista',
		selectInfo	: 'Upplýsingar',
		opAvail		: 'Kostir',
		value		: 'Gildi',
		size		: 'Stærð',
		lines		: 'l&iacute;nur',
		chkMulti	: 'Leyfa fleiri kosti',
		opText		: 'Texti',
		opValue		: 'Gildi',
		btnAdd		: 'Bæta við',
		btnModify	: 'Breyta',
		btnUp		: 'Upp',
		btnDown		: 'Niður',
		btnSetValue : 'Merkja sem valið',
		btnDelete	: 'Eyða'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Eigindi textasvæðis',
		cols		: 'D&aacute;lkar',
		rows		: 'L&iacute;nur'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Eigindi textareits',
		name		: 'Nafn',
		value		: 'Gildi',
		charWidth	: 'Breidd (leturt&aacute;kn)',
		maxChars	: 'H&aacute;marksfjöldi leturt&aacute;kna',
		type		: 'Gerð',
		typeText	: 'Texti',
		typePass	: 'Lykilorð'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Eigindi falins svæðis',
		name	: 'Nafn',
		value	: 'Gildi'
	},

	// Image Dialog.
	image :
	{
		title		: 'Eigindi myndar',
		titleButton	: 'Eigindi myndahnapps',
		menu		: 'Eigindi myndar',
		infoTab		: 'Almennt',
		btnUpload	: 'Hlaða upp',
		upload		: 'Hlaða upp',
		alt			: 'Baklægur texti',
		width		: 'Breidd',
		height		: 'Hæð',
		lockRatio	: 'Festa stærðarhlutfall',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Reikna stærð',
		border		: 'Rammi',
		hSpace		: 'Vinstri bil',
		vSpace		: 'Hægri bil',
		align		: 'Jöfnun',
		alignLeft	: 'Vinstri',
		alignRight	: 'Hægri',
		alertUrl	: 'Sl&aacute;ðu inn sl&oacute;ðina að myndinni',
		linkTab		: 'Stikla',
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
		properties		: 'Eigindi Flash',
		propertiesTab	: 'Properties', // MISSING
		title			: 'Eigindi Flash',
		chkPlay			: 'Sj&aacute;lfvirk spilun',
		chkLoop			: 'Endurtekning',
		chkMenu			: 'Sýna Flash-valmynd',
		chkFull			: 'Allow Fullscreen', // MISSING
 		scale			: 'Skali',
		scaleAll		: 'Sýna allt',
		scaleNoBorder	: '&aacute;n ramma',
		scaleFit		: 'Fella skala að stærð',
		access			: 'Script Access', // MISSING
		accessAlways	: 'Always', // MISSING
		accessSameDomain: 'Same domain', // MISSING
		accessNever		: 'Never', // MISSING
		align			: 'Jöfnun',
		alignLeft		: 'Vinstri',
		alignAbsBottom	: 'Abs neðst',
		alignAbsMiddle	: 'Abs miðjuð',
		alignBaseline	: 'Grunnl&iacute;na',
		alignBottom		: 'Neðst',
		alignMiddle		: 'Miðjuð',
		alignRight		: 'Hægri',
		alignTextTop	: 'Efri br&uacute;n texta',
		alignTop		: 'Efst',
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
		bgcolor			: 'Bakgrunnslitur',
		width			: 'Breidd',
		height			: 'Hæð',
		hSpace			: 'Vinstri bil',
		vSpace			: 'Hægri bil',
		validateSrc		: 'Sl&aacute;ðu inn veffang stiklunnar!',
		validateWidth	: 'Width must be a number.', // MISSING
		validateHeight	: 'Height must be a number.', // MISSING
		validateHSpace	: 'HSpace must be a number.', // MISSING
		validateVSpace	: 'VSpace must be a number.' // MISSING
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Villuleit',
		title			: 'Spell Check', // MISSING
		notAvailable	: 'Sorry, but service is unavailable now.', // MISSING
		errorLoading	: 'Error loading application service host: %s.', // MISSING
		notInDic		: 'Ekki &iacute; orðab&oacute;kinni',
		changeTo		: 'Tillaga',
		btnIgnore		: 'Hunsa',
		btnIgnoreAll	: 'Hunsa allt',
		btnReplace		: 'Skipta',
		btnReplaceAll	: 'Skipta öllu',
		btnUndo			: 'Til baka',
		noSuggestions	: '- engar tillögur -',
		progress		: 'Villuleit &iacute; gangi...',
		noMispell		: 'Villuleit lokið: Engin villa fannst',
		noChanges		: 'Villuleit lokið: Engu orði breytt',
		oneChange		: 'Villuleit lokið: Einu orði breytt',
		manyChanges		: 'Villuleit lokið: %1 orðum breytt',
		ieSpellDownload	: 'Villuleit ekki sett upp.<br>Viltu setja hana upp?'
	},

	smiley :
	{
		toolbar	: 'Svipur',
		title	: 'Velja svip',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : '%1 element' // MISSING
	},

	numberedlist	: 'N&uacute;meraður listi',
	bulletedlist	: 'Punktalisti',
	indent			: 'Minnka inndr&aacute;tt',
	outdent			: 'Auka inndr&aacute;tt',

	justify :
	{
		left	: 'Vinstrijöfnun',
		center	: 'Miðja texta',
		right	: 'Hægrijöfnun',
		block	: 'Jafna b&aacute;ðum megin'
	},

	blockquote : 'Inndr&aacute;ttur',

	clipboard :
	{
		title		: 'L&iacute;ma',
		cutError	: 'Öryggisstillingar vafrans þ&iacute;ns leyfa ekki klippingu texta með m&uacute;saraðgerð. Notaðu lyklaborðið &iacute; klippa (Ctrl/Cmd+X).',
		copyError	: 'Öryggisstillingar vafrans þ&iacute;ns leyfa ekki afritun texta með m&uacute;saraðgerð. Notaðu lyklaborðið &iacute; afrita (Ctrl/Cmd+C).',
		pasteMsg	: 'L&iacute;mdu &iacute; svæðið h&eacute;r að neðan og (<STRONG>Ctrl/Cmd+V</STRONG>) og smelltu &aacute; <STRONG>OK</STRONG>.',
		securityMsg	: 'Vegna öryggisstillinga &iacute; vafranum þ&iacute;num fær ritillinn ekki beinan aðgang að klippuborðinu. Þ&uacute; verður að l&iacute;ma innihaldið aftur inn &iacute; þennan glugga.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'The text you want to paste seems to be copied from Word. Do you want to clean it before pasting?', // MISSING
		toolbar			: 'L&iacute;ma &uacute;r Word',
		title			: 'L&iacute;ma &uacute;r Word',
		error			: 'It was not possible to clean up the pasted data due to an internal error' // MISSING
	},

	pasteText :
	{
		button	: 'L&iacute;ma sem &oacute;sniðinn texta',
		title	: 'L&iacute;ma sem &oacute;sniðinn texta'
	},

	templates :
	{
		button			: 'Sniðm&aacute;t',
		title			: 'Innihaldssniðm&aacute;t',
		options : 'Template Options', // MISSING
		insertOption	: 'Skipta &uacute;t raunverulegu innihaldi',
		selectPromptMsg	: 'Veldu sniðm&aacute;t til að opna &iacute; ritlinum.<br>(N&uacute;verandi innihald v&iacute;kur fyrir þv&iacute;!):',
		emptyListMsg	: '(Ekkert sniðm&aacute;t er skilgreint!)'
	},

	showBlocks : 'Sýna blokkir',

	stylesCombo :
	{
		label		: 'St&iacute;lflokkur',
		panelTitle	: 'Formatting Styles', // MISSING
		panelTitle1	: 'Block Styles', // MISSING
		panelTitle2	: 'Inline Styles', // MISSING
		panelTitle3	: 'Object Styles' // MISSING
	},

	format :
	{
		label		: 'St&iacute;lsnið',
		panelTitle	: 'St&iacute;lsnið',

		tag_p		: 'Venjulegt letur',
		tag_pre		: 'Forsniðið',
		tag_address	: 'Vistfang',
		tag_h1		: 'Fyrirsögn 1',
		tag_h2		: 'Fyrirsögn 2',
		tag_h3		: 'Fyrirsögn 3',
		tag_h4		: 'Fyrirsögn 4',
		tag_h5		: 'Fyrirsögn 5',
		tag_h6		: 'Fyrirsögn 6',
		tag_div		: 'Venjulegt (DIV)'
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
		label		: 'Leturgerð ',
		voiceLabel	: 'Font', // MISSING
		panelTitle	: 'Leturgerð '
	},

	fontSize :
	{
		label		: 'Leturstærð ',
		voiceLabel	: 'Font Size', // MISSING
		panelTitle	: 'Leturstærð '
	},

	colorButton :
	{
		textColorTitle	: 'Litur texta',
		bgColorTitle	: 'Bakgrunnslitur',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Sj&aacute;lfval',
		more			: 'Fleiri liti...'
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
