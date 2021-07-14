﻿/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Catalan language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['ca'] =
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
	editorTitle : 'Editor de text enriquit, %1, prem ALT 0 per obtenir ajuda.',

	// ARIA descriptions.
	toolbar	: 'Barra d\'eines',
	editor	: 'Editor de text enriquit',

	// Toolbar buttons without dialogs.
	source			: 'Codi font',
	newPage			: 'Nova pàgina',
	save			: 'Desa',
	preview			: 'Visualitzaci&oacute; prèvia',
	cut				: 'Retalla',
	copy			: 'Copia',
	paste			: 'Enganxa',
	print			: 'Imprimeix',
	underline		: 'Subratllat',
	bold			: 'Negreta',
	italic			: 'Cursiva',
	selectAll		: 'Selecciona-ho tot',
	removeFormat	: 'Elimina Format',
	strike			: 'Barrat',
	subscript		: 'Sub&iacute;ndex',
	superscript		: 'Super&iacute;ndex',
	horizontalrule	: 'Insereix l&iacute;nia horitzontal',
	pagebreak		: 'Insereix salt de pàgina',
	unlink			: 'Elimina l\'enllaç',
	undo			: 'Desf&eacute;s',
	redo			: 'Ref&eacute;s',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Veure servidor',
		url				: 'URL',
		protocol		: 'Protocol',
		upload			: 'Puja',
		uploadSubmit	: 'Envia-la al servidor',
		image			: 'Imatge',
		flash			: 'Flash',
		form			: 'Formulari',
		checkbox		: 'Casella de verificaci&oacute;',
		radio			: 'Bot&oacute; d\'opci&oacute;',
		textField		: 'Camp de text',
		textarea		: 'Àrea de text',
		hiddenField		: 'Camp ocult',
		button			: 'Bot&oacute;',
		select			: 'Camp de selecci&oacute;',
		imageButton		: 'Bot&oacute; d\'imatge',
		notSet			: '<no definit>',
		id				: 'Id',
		name			: 'Nom',
		langDir			: 'Direcci&oacute; de l\'idioma',
		langDirLtr		: 'D\'esquerra a dreta (LTR)',
		langDirRtl		: 'De dreta a esquerra (RTL)',
		langCode		: 'Codi d\'idioma',
		longDescr		: 'Descripci&oacute; llarga de la URL',
		cssClass		: 'Classes del full d\'estil',
		advisoryTitle	: 'T&iacute;tol consultiu',
		cssStyle		: 'Estil',
		ok				: 'D\'acord',
		cancel			: 'Cancel·la',
		close			: 'Tanca',
		preview			: 'Previsualitza',
		generalTab		: 'General',
		advancedTab		: 'Avançat',
		validateNumberFailed : 'Aquest valor no &eacute;s un n&uacute;mero.',
		confirmNewPage	: 'Els canvis en aquest contingut que no es desin es perdran. Esteu segur que voleu carregar una pàgina nova?',
		confirmCancel	: 'Algunes opcions s\'han canviat. Esteu segur que voleu tancar la finestra de diàleg?',
		options			: 'Opcions',
		target			: 'Dest&iacute;',
		targetNew		: 'Nova finestra (_blank)',
		targetTop		: 'Finestra major (_top)',
		targetSelf		: 'Mateixa finestra (_self)',
		targetParent	: 'Finestra pare (_parent)',

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, no disponible</span>'
	},

	contextmenu :
	{
		options : 'Context Menu Options' // MISSING
	},

	// Special char dialog.
	specialChar		:
	{
		toolbar		: 'Insereix caràcter especial',
		title		: 'Selecciona el caràcter especial',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Insereix/Edita enllaç',
		other 		: '<altre>',
		menu		: 'Edita l\'enllaç',
		title		: 'Enllaç',
		info		: 'Informaci&oacute; de l\'enllaç',
		target		: 'Dest&iacute;',
		upload		: 'Puja',
		advanced	: 'Avançat',
		type		: 'Tipus d\'enllaç',
		toUrl		: 'URL',
		toAnchor	: 'Àncora en aquesta pàgina',
		toEmail		: 'Correu electrònic',
		targetFrame		: '<marc>',
		targetPopup		: '<finestra emergent>',
		targetFrameName	: 'Nom del marc de dest&iacute;',
		targetPopupName	: 'Nom finestra popup',
		popupFeatures	: 'Caracter&iacute;stiques finestra popup',
		popupResizable	: 'Redimensionable',
		popupStatusBar	: 'Barra d\'estat',
		popupLocationBar: 'Barra d\'adreça',
		popupToolbar	: 'Barra d\'eines',
		popupMenuBar	: 'Barra de men&uacute;',
		popupFullScreen	: 'Pantalla completa (IE)',
		popupScrollBars	: 'Barres d\'scroll',
		popupDependent	: 'Depenent (Netscape)',
		popupWidth		: 'Amplada',
		popupLeft		: 'Posici&oacute; esquerra',
		popupHeight		: 'Alçada',
		popupTop		: 'Posici&oacute; dalt',
		id				: 'Id',
		langDir			: 'Direcci&oacute; de l\'idioma',
		langDirLTR		: 'D\'esquerra a dreta (LTR)',
		langDirRTL		: 'De dreta a esquerra (RTL)',
		acccessKey		: 'Clau d\'acc&eacute;s',
		name			: 'Nom',
		langCode		: 'Direcci&oacute; de l\'idioma',
		tabIndex		: 'Index de Tab',
		advisoryTitle	: 'T&iacute;tol consultiu',
		advisoryContentType	: 'Tipus de contingut consultiu',
		cssClasses		: 'Classes del full d\'estil',
		charset			: 'Conjunt de caràcters font enllaçat',
		styles			: 'Estil',
		selectAnchor	: 'Selecciona una àncora',
		anchorName		: 'Per nom d\'àncora',
		anchorId		: 'Per Id d\'element',
		emailAddress	: 'Adreça de correu electrònic',
		emailSubject	: 'Assumpte del missatge',
		emailBody		: 'Cos del missatge',
		noAnchors		: '(No hi ha àncores disponibles en aquest document)',
		noUrl			: 'Si us plau, escrigui l\'enllaç URL',
		noEmail			: 'Si us plau, escrigui l\'adreça correu electrònic'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Insereix/Edita àncora',
		menu		: 'Propietats de l\'àncora',
		title		: 'Propietats de l\'àncora',
		name		: 'Nom de l\'àncora',
		errorName	: 'Si us plau, escriviu el nom de l\'ancora'
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
		title				: 'Cerca i reemplaça',
		find				: 'Cerca',
		replace				: 'Reemplaça',
		findWhat			: 'Cerca:',
		replaceWith			: 'Remplaça amb:',
		notFoundMsg			: 'El text especificat no s\'ha trobat.',
		matchCase			: 'Distingeix maj&uacute;scules/min&uacute;scules',
		matchWord			: 'Nom&eacute;s paraules completes',
		matchCyclic			: 'Match cyclic',
		replaceAll			: 'Reemplaça-ho tot',
		replaceSuccessMsg	: '%1 ocurrència/es reemplaçada/es.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Taula',
		title		: 'Propietats de la taula',
		menu		: 'Propietats de la taula',
		deleteTable	: 'Suprimeix la taula',
		rows		: 'Files',
		columns		: 'Columnes',
		border		: 'Mida vora',
		align		: 'Alineaci&oacute;',
		alignLeft	: 'Esquerra',
		alignCenter	: 'Centre',
		alignRight	: 'Dreta',
		width		: 'Amplada',
		widthPx		: 'p&iacute;xels',
		widthPc		: 'percentatge',
		widthUnit	: 'unitat d\'amplada',
		height		: 'Alçada',
		cellSpace	: 'Espaiat de cel·les',
		cellPad		: 'Encoixinament de cel·les',
		caption		: 'T&iacute;tol',
		summary		: 'Resum',
		headers		: 'Capçaleres',
		headersNone		: 'Cap',
		headersColumn	: 'Primera columna',
		headersRow		: 'Primera fila',
		headersBoth		: 'Ambdues',
		invalidRows		: 'El nombre de files ha de ser un nombre major que 0.',
		invalidCols		: 'El nombre de columnes ha de ser un nombre major que 0.',
		invalidBorder	: 'El gruix de la vora ha de ser un nombre.',
		invalidWidth	: 'L\'amplada de la taula  ha de ser un nombre.',
		invalidHeight	: 'L\'alçada de la taula  ha de ser un nombre.',
		invalidCellSpacing	: 'L\'espaiat de cel·la  ha de ser un nombre.',
		invalidCellPadding	: 'L\'encoixinament de cel·la  ha de ser un nombre.',

		cell :
		{
			menu			: 'Cel·la',
			insertBefore	: 'Insereix cel·la abans de',
			insertAfter		: 'Insereix cel·la darrera',
			deleteCell		: 'Suprimeix les cel·les',
			merge			: 'Fusiona les cel·les',
			mergeRight		: 'Fusiona cap a la dreta',
			mergeDown		: 'Fusiona cap avall',
			splitHorizontal	: 'Divideix la cel·la horitzontalment',
			splitVertical	: 'Divideix la cel·la verticalment',
			title			: 'Propertiat de la cel·la',
			cellType		: 'Tipus de cel·la',
			rowSpan			: 'Expansi&oacute; de files',
			colSpan			: 'Expansi&oacute; de columnes',
			wordWrap		: 'Ajustar al contingut',
			hAlign			: 'Aliniaci&oacute; Horizontal',
			vAlign			: 'Aliniaci&oacute; Vertical',
			alignTop		: 'A dalt',
			alignMiddle		: 'Al mig',
			alignBottom		: 'A baix',
			alignBaseline	: 'A la l&iacute;nia base',
			bgColor			: 'Color de fons',
			borderColor		: 'Color de la vora',
			data			: 'Data',
			header			: 'Capçalera',
			yes				: 'S&iacute;',
			no				: 'No',
			invalidWidth	: 'L\'amplada de cel·la ha de ser un nombre.',
			invalidHeight	: 'L\'alçada de cel·la ha de ser un nombre.',
			invalidRowSpan	: 'L\'expansi&oacute; de files ha de ser un nombre enter.',
			invalidColSpan	: 'L\'expansi&oacute; de columnes ha de ser un nombre enter.',
			chooseColor		: 'Trieu'
		},

		row :
		{
			menu			: 'Fila',
			insertBefore	: 'Insereix fila abans de',
			insertAfter		: 'Insereix fila darrera',
			deleteRow		: 'Suprimeix una fila'
		},

		column :
		{
			menu			: 'Columna',
			insertBefore	: 'Insereix columna abans de',
			insertAfter		: 'Insereix columna darrera',
			deleteColumn	: 'Suprimeix una columna'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Propietats del bot&oacute;',
		text		: 'Text (Valor)',
		type		: 'Tipus',
		typeBtn		: 'Bot&oacute;',
		typeSbm		: 'Transmet formulari',
		typeRst		: 'Reinicia formulari'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Propietats de la casella de verificaci&oacute;',
		radioTitle	: 'Propietats del bot&oacute; d\'opci&oacute;',
		value		: 'Valor',
		selected	: 'Seleccionat'
	},

	// Form Dialog.
	form :
	{
		title		: 'Propietats del formulari',
		menu		: 'Propietats del formulari',
		action		: 'Acci&oacute;',
		method		: 'Mètode',
		encoding	: 'Codificaci&oacute;'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Propietats del camp de selecci&oacute;',
		selectInfo	: 'Info',
		opAvail		: 'Opcions disponibles',
		value		: 'Valor',
		size		: 'Mida',
		lines		: 'L&iacute;nies',
		chkMulti	: 'Permet m&uacute;ltiples seleccions',
		opText		: 'Text',
		opValue		: 'Valor',
		btnAdd		: 'Afegeix',
		btnModify	: 'Modifica',
		btnUp		: 'Amunt',
		btnDown		: 'Avall',
		btnSetValue : 'Selecciona per defecte',
		btnDelete	: 'Elimina'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Propietats de l\'àrea de text',
		cols		: 'Columnes',
		rows		: 'Files'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Propietats del camp de text',
		name		: 'Nom',
		value		: 'Valor',
		charWidth	: 'Amplada',
		maxChars	: 'Nombre màxim de caràcters',
		type		: 'Tipus',
		typeText	: 'Text',
		typePass	: 'Contrasenya'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Propietats del camp ocult',
		name	: 'Nom',
		value	: 'Valor'
	},

	// Image Dialog.
	image :
	{
		title		: 'Propietats de la imatge',
		titleButton	: 'Propietats del bot&oacute; d\'imatge',
		menu		: 'Propietats de la imatge',
		infoTab		: 'Informaci&oacute; de la imatge',
		btnUpload	: 'Envia-la al servidor',
		upload		: 'Puja',
		alt			: 'Text alternatiu',
		width		: 'Amplada',
		height		: 'Alçada',
		lockRatio	: 'Bloqueja les proporcions',
		unlockRatio	: 'Desbloqueja el ràtio',
		resetSize	: 'Restaura la mida',
		border		: 'Vora',
		hSpace		: 'Espaiat horit.',
		vSpace		: 'Espaiat vert.',
		align		: 'Alineaci&oacute;',
		alignLeft	: 'Ajusta a l\'esquerra',
		alignRight	: 'Ajusta a la dreta',
		alertUrl	: 'Si us plau, escriviu la URL de la imatge',
		linkTab		: 'Enllaç',
		button2Img	: 'Voleu transformar el bot&oacute; d\'imatge seleccionat en una simple imatge?',
		img2Button	: 'Voleu transformar la imatge seleccionada en un bot&oacute; d\'imatge?',
		urlMissing	: 'Falta la URL de la imatge.',
		validateWidth	: 'L\'amplada ha de ser un nombre enter.',
		validateHeight	: 'L\'alçada ha de ser un nombre enter.',
		validateBorder	: 'La vora ha de ser un nombre enter.',
		validateHSpace	: 'HSpace ha de ser un nombre enter.',
		validateVSpace	: 'VSpace ha de ser un nombre enter.'
	},

	// Flash Dialog
	flash :
	{
		properties		: 'Propietats del Flash',
		propertiesTab	: 'Propietats',
		title			: 'Propietats del Flash',
		chkPlay			: 'Reproduci&oacute; automàtica',
		chkLoop			: 'Bucle',
		chkMenu			: 'Habilita men&uacute; Flash',
		chkFull			: 'Permetre la pantalla completa',
 		scale			: 'Escala',
		scaleAll		: 'Mostra-ho tot',
		scaleNoBorder	: 'Sense vores',
		scaleFit		: 'Mida exacta',
		access			: 'Acc&eacute;s a scripts',
		accessAlways	: 'Sempre',
		accessSameDomain: 'El mateix domini',
		accessNever		: 'Mai',
		align			: 'Alineaci&oacute;',
		alignLeft		: 'Ajusta a l\'esquerra',
		alignAbsBottom	: 'Abs Bottom',
		alignAbsMiddle	: 'Abs Middle',
		alignBaseline	: 'Baseline',
		alignBottom		: 'Bottom',
		alignMiddle		: 'Middle',
		alignRight		: 'Ajusta a la dreta',
		alignTextTop	: 'Text Top',
		alignTop		: 'Top',
		quality			: 'Qualitat',
		qualityBest		: 'La millor',
		qualityHigh		: 'Alta',
		qualityAutoHigh	: 'Alta automàtica',
		qualityMedium	: 'Mitjana',
		qualityAutoLow	: 'Baixa automàtica',
		qualityLow		: 'Baixa',
		windowModeWindow: 'Finestra',
		windowModeOpaque: 'Opaca',
		windowModeTransparent : 'Transparent',
		windowMode		: 'Mode de la finestra',
		flashvars		: 'Variables de Flash',
		bgcolor			: 'Color de Fons',
		width			: 'Amplada',
		height			: 'Alçada',
		hSpace			: 'Espaiat horit.',
		vSpace			: 'Espaiat vert.',
		validateSrc		: 'Si us plau, escrigui l\'enllaç URL',
		validateWidth	: 'L\'amplada ha de ser un nombre.',
		validateHeight	: 'L\'alçada ha de ser un nombre.',
		validateHSpace	: 'L\'espaiat horitzonatal ha de ser un nombre.',
		validateVSpace	: 'L\'espaiat vertical ha de ser un nombre.'
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Revisa l\'ortografia',
		title			: 'Comprova l\'ortografia',
		notAvailable	: 'El servei no es troba disponible ara.',
		errorLoading	: 'Error carregant el servidor: %s.',
		notInDic		: 'No &eacute;s al diccionari',
		changeTo		: 'Reemplaça amb',
		btnIgnore		: 'Ignora',
		btnIgnoreAll	: 'Ignora-les totes',
		btnReplace		: 'Canvia',
		btnReplaceAll	: 'Canvia-les totes',
		btnUndo			: 'Desf&eacute;s',
		noSuggestions	: 'Cap suggeriment',
		progress		: 'Verificaci&oacute; ortogràfica en curs...',
		noMispell		: 'Verificaci&oacute; ortogràfica acabada: no hi ha cap paraula mal escrita',
		noChanges		: 'Verificaci&oacute; ortogràfica: no s\'ha canviat cap paraula',
		oneChange		: 'Verificaci&oacute; ortogràfica: s\'ha canviat una paraula',
		manyChanges		: 'Verificaci&oacute; ortogràfica: s\'han canviat %1 paraules',
		ieSpellDownload	: 'Verificaci&oacute; ortogràfica no instal·lada. Voleu descarregar-ho ara?'
	},

	smiley :
	{
		toolbar	: 'Icona',
		title	: 'Insereix una icona',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path',
		eleTitle : '%1 element'
	},

	numberedlist	: 'Llista numerada',
	bulletedlist	: 'Llista de pics',
	indent			: 'Augmenta el sagnat',
	outdent			: 'Redueix el sagnat',

	justify :
	{
		left	: 'Alinia a l\'esquerra',
		center	: 'Centrat',
		right	: 'Alinia a la dreta',
		block	: 'Justificat'
	},

	blockquote : 'Bloc de cita',

	clipboard :
	{
		title		: 'Enganxa',
		cutError	: 'La seguretat del vostre navegador no permet executar automàticament les operacions de retallar. Si us plau, utilitzeu el teclat (Ctrl+X).',
		copyError	: 'La seguretat del vostre navegador no permet executar automàticament les operacions de copiar. Si us plau, utilitzeu el teclat (Ctrl+C).',
		pasteMsg	: 'Si us plau, enganxeu dins del següent camp utilitzant el teclat (<STRONG>Ctrl+V</STRONG>) i premeu <STRONG>OK</STRONG>.',
		securityMsg	: 'A causa de la configuraci&oacute; de seguretat del vostre navegador, l\'editor no pot accedir al porta-retalls directament. Enganxeu-ho un altre cop en aquesta finestra.',
		pasteArea	: 'Àrea d\'enganxat'
	},

	pastefromword :
	{
		confirmCleanup	: 'El text que voleu enganxar sembla provenir de Word. Voleu netejar aquest text abans que sigui enganxat?',
		toolbar			: 'Enganxa des del Word',
		title			: 'Enganxa des del Word',
		error			: 'No ha estat possible netejar les dades enganxades degut a un error intern'
	},

	pasteText :
	{
		button	: 'Enganxa com a text no formatat',
		title	: 'Enganxa com a text no formatat'
	},

	templates :
	{
		button			: 'Plantilles',
		title			: 'Contingut plantilles',
		options : 'Template Options', // MISSING
		insertOption	: 'Reemplaça el contingut actual',
		selectPromptMsg	: 'Si us plau, seleccioneu la plantilla per obrir a l\'editor<br>(el contingut actual no serà enregistrat):',
		emptyListMsg	: '(No hi ha plantilles definides)'
	},

	showBlocks : 'Mostra els blocs',

	stylesCombo :
	{
		label		: 'Estil',
		panelTitle	: 'Estils de format',
		panelTitle1	: 'Estils de bloc',
		panelTitle2	: 'Estils incrustats',
		panelTitle3	: 'Estils d\'objecte'
	},

	format :
	{
		label		: 'Format',
		panelTitle	: 'Format',

		tag_p		: 'Normal',
		tag_pre		: 'Formatejat',
		tag_address	: 'Adreça',
		tag_h1		: 'Encapçalament 1',
		tag_h2		: 'Encapçalament 2',
		tag_h3		: 'Encapçalament 3',
		tag_h4		: 'Encapçalament 4',
		tag_h5		: 'Encapçalament 5',
		tag_h6		: 'Encapçalament 6',
		tag_div		: 'Normal (DIV)'
	},

	div :
	{
		title				: 'Crea un contenidor Div',
		toolbar				: 'Crea un contenidor Div',
		cssClassInputLabel	: 'Classes de la fulla d\'estils',
		styleSelectLabel	: 'Estil',
		IdInputLabel		: 'Id',
		languageCodeInputLabel	: ' Codi d\'idioma',
		inlineStyleInputLabel	: 'Estil en l&iacute;nia',
		advisoryTitleInputLabel	: 'T&iacute;tol de guia',
		langDirLabel		: 'Direcci&oacute; de l\'idioma',
		langDirLTRLabel		: 'D\'esquerra a dreta (LTR)',
		langDirRTLLabel		: 'De dreta a esquerra (RTL)',
		edit				: 'Edita Div',
		remove				: 'Elimina Div'
  	},

	font :
	{
		label		: 'Tipus de lletra',
		voiceLabel	: 'Tipus de lletra',
		panelTitle	: 'Tipus de lletra'
	},

	fontSize :
	{
		label		: 'Mida',
		voiceLabel	: 'Mida de la lletra',
		panelTitle	: 'Mida'
	},

	colorButton :
	{
		textColorTitle	: 'Color de Text',
		bgColorTitle	: 'Color de Fons',
		panelTitle		: 'Colors',
		auto			: 'Automàtic',
		more			: 'M&eacute;s colors...'
	},

	colors :
	{
		'000' : 'Negre',
		'800000' : 'Granat',
		'8B4513' : 'Marr&oacute; sella',
		'2F4F4F' : 'Gris pissarra fosca',
		'008080' : 'Blau xarxet',
		'000080' : 'Blau mar&iacute;',
		'4B0082' : 'Indi',
		'696969' : 'Gris intens',
		'B22222' : 'Ma&oacute;',
		'A52A2A' : 'Marr&oacute; (web)',
		'DAA520' : 'Solidago',
		'006400' : 'Verd fosc',
		'40E0D0' : 'Turquesa',
		'0000CD' : 'Atzur',
		'800080' : 'Lila',
		'808080' : 'Gris',
		'F00' : 'Vermell',
		'FF8C00' : 'Taronja fosc',
		'FFD700' : 'Or',
		'008000' : 'Verd',
		'0FF' : 'Cian',
		'00F' : 'Blau',
		'EE82EE' : 'Lavanda rosat',
		'A9A9A9' : 'Gris clar',
		'FFA07A' : 'Salm&oacute; clar',
		'FFA500' : 'Taronja',
		'FFFF00' : 'Groc',
		'00FF00' : 'Verd llima',
		'AFEEEE' : 'Blau pàlid',
		'ADD8E6' : 'Blau clar',
		'DDA0DD' : 'Pruna',
		'D3D3D3' : 'Gris clar',
		'FFF0F5' : 'Lavanda rosat',
		'FAEBD7' : 'Blanc antic',
		'FFFFE0' : 'Groc clar',
		'F0FFF0' : 'Verd pàlid',
		'F0FFFF' : 'Blau cel pàlid',
		'F0F8FF' : 'Cian pàlid',
		'E6E6FA' : 'Lavanda',
		'FFF' : 'Blanc'
	},

	scayt :
	{
		title			: 'Spell Check As You Type',
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'Habilitat l\'SCAYT',
		disable			: 'Deshabilita SCAYT',
		about			: 'Quant a l\'SCAYT',
		toggle			: 'Commuta l\'SCAYT',
		options			: 'Opcions',
		langs			: 'Idiomes',
		moreSuggestions	: 'M&eacute;s suggerències',
		ignore			: 'Ignora',
		ignoreAll		: 'Ignora\'ls tots',
		addWord			: 'Afegeix una paraula',
		emptyDic		: 'El nom del diccionari no hauria d\'estar buit.',

		optionsTab		: 'Opcions',
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'Idiomes',

		dictionariesTab	: 'Diccionaris',
		dic_field_name	: 'Dictionary name', // MISSING
		dic_create		: 'Create', // MISSING
		dic_restore		: 'Restore', // MISSING
		dic_delete		: 'Delete', // MISSING
		dic_rename		: 'Rename', // MISSING
		dic_info		: 'Initially the User Dictionary is stored in a Cookie. However, Cookies are limited in size. When the User Dictionary grows to a point where it cannot be stored in a Cookie, then the dictionary may be stored on our server. To store your personal dictionary on our server you should specify a name for your dictionary. If you already have a stored dictionary, please type it\'s name and click the Restore button.', // MISSING

		aboutTab		: 'Quant a'
	},

	about :
	{
		title		: 'Quan al CKEditor',
		dlgTitle	: 'Quan al CKEditor',
		moreInfo	: 'Per informaci&oacute; sobre llicències visiteu el web:',
		copy		: 'Copyright &copy; $1. All rights reserved.'
	},

	maximize : 'Maximitza',
	minimize : 'Minimitza',

	fakeobjects :
	{
		anchor	: 'Àncora',
		flash	: 'Animaci&oacute; Flash',
		div		: 'Salt de pàgina',
		unknown	: 'Objecte desconegut'
	},

	resize : 'Arrossegueu per redimensionar',

	colordialog :
	{
		title		: 'Selecciona el color',
		options	:	'Color Options', // MISSING
		highlight	: 'Destacat',
		selected	: 'Seleccionat',
		clear		: 'Neteja'
	},

	toolbarCollapse	: 'Redueix la barra d\'eines',
	toolbarExpand	: 'Amplia la barra d\'eines'
};
