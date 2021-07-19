/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Canadian French language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['fr-ca'] =
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
	source			: 'Source',
	newPage			: 'Nouvelle page',
	save			: 'Sauvegarder',
	preview			: 'Previsualiser',
	cut				: 'Couper',
	copy			: 'Copier',
	paste			: 'Coller',
	print			: 'Imprimer',
	underline		: 'Soulign&eacute;',
	bold			: 'Gras',
	italic			: 'Italique',
	selectAll		: 'Tout s&eacute;lectionner',
	removeFormat	: 'Supprimer le formatage',
	strike			: 'Barrer',
	subscript		: 'Indice',
	superscript		: 'Exposant',
	horizontalrule	: 'Ins&eacute;rer un s&eacute;parateur',
	pagebreak		: 'Ins&eacute;rer un saut de page',
	unlink			: 'Supprimer le lien',
	undo			: 'Annuler',
	redo			: 'Refaire',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Parcourir le serveur',
		url				: 'URL',
		protocol		: 'Protocole',
		upload			: 'T&eacute;l&eacute;charger',
		uploadSubmit	: 'Envoyer sur le serveur',
		image			: 'Image',
		flash			: 'Animation Flash',
		form			: 'Formulaire',
		checkbox		: 'Case à cocher',
		radio			: 'Bouton radio',
		textField		: 'Champ texte',
		textarea		: 'Zone de texte',
		hiddenField		: 'Champ cach&eacute;',
		button			: 'Bouton',
		select			: 'Champ de s&eacute;lection',
		imageButton		: 'Bouton image',
		notSet			: '<Par d&eacute;faut>',
		id				: 'Id',
		name			: 'Nom',
		langDir			: 'Sens d\'&eacute;criture',
		langDirLtr		: 'De gauche à droite (LTR)',
		langDirRtl		: 'De droite à gauche (RTL)',
		langCode		: 'Code langue',
		longDescr		: 'URL de description longue',
		cssClass		: 'Classes de feuilles de style',
		advisoryTitle	: 'Titre',
		cssStyle		: 'Style',
		ok				: 'OK',
		cancel			: 'Annuler',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: 'G&eacute;n&eacute;ral',
		advancedTab		: 'Avanc&eacute;e',
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
		toolbar		: 'Ins&eacute;rer un caractère sp&eacute;cial',
		title		: 'Ins&eacute;rer un caractère sp&eacute;cial',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Ins&eacute;rer/modifier le lien',
		other 		: '<other>', // MISSING
		menu		: 'Modifier le lien',
		title		: 'Propri&eacute;t&eacute;s du lien',
		info		: 'Informations sur le lien',
		target		: 'Destination',
		upload		: 'T&eacute;l&eacute;charger',
		advanced	: 'Avanc&eacute;e',
		type		: 'Type de lien',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'Ancre dans cette page',
		toEmail		: 'E-Mail',
		targetFrame		: '<Cadre>',
		targetPopup		: '<fenêtre popup>',
		targetFrameName	: 'Nom du cadre de destination',
		targetPopupName	: 'Nom de la fenêtre popup',
		popupFeatures	: 'Caract&eacute;ristiques de la fenêtre popup',
		popupResizable	: 'Resizable', // MISSING
		popupStatusBar	: 'Barre d\'&eacute;tat',
		popupLocationBar: 'Barre d\'adresses',
		popupToolbar	: 'Barre d\'outils',
		popupMenuBar	: 'Barre de menu',
		popupFullScreen	: 'Plein &eacute;cran (IE)',
		popupScrollBars	: 'Barres de d&eacute;filement',
		popupDependent	: 'D&eacute;pendante (Netscape)',
		popupWidth		: 'Largeur',
		popupLeft		: 'Position à partir de la gauche',
		popupHeight		: 'Hauteur',
		popupTop		: 'Position à partir du haut',
		id				: 'Id', // MISSING
		langDir			: 'Sens d\'&eacute;criture',
		langDirLTR		: 'De gauche à droite (LTR)',
		langDirRTL		: 'De droite à gauche (RTL)',
		acccessKey		: '&eacute;quivalent clavier',
		name			: 'Nom',
		langCode		: 'Sens d\'&eacute;criture',
		tabIndex		: 'Ordre de tabulation',
		advisoryTitle	: 'Titre',
		advisoryContentType	: 'Type de contenu',
		cssClasses		: 'Classes de feuilles de style',
		charset			: 'Encodage de caractère',
		styles			: 'Style',
		selectAnchor	: 'S&eacute;lectionner une ancre',
		anchorName		: 'Par nom',
		anchorId		: 'Par id',
		emailAddress	: 'Adresse E-Mail',
		emailSubject	: 'Sujet du message',
		emailBody		: 'Corps du message',
		noAnchors		: '(Pas d\'ancre disponible dans le document)',
		noUrl			: 'Veuillez saisir l\'URL',
		noEmail			: 'Veuillez saisir l\'adresse e-mail'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Ins&eacute;rer/modifier l\'ancre',
		menu		: 'Propri&eacute;t&eacute;s de l\'ancre',
		title		: 'Propri&eacute;t&eacute;s de l\'ancre',
		name		: 'Nom de l\'ancre',
		errorName	: 'Veuillez saisir le nom de l\'ancre'
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
		title				: 'Chercher et Remplacer',
		find				: 'Chercher',
		replace				: 'Remplacer',
		findWhat			: 'Rechercher:',
		replaceWith			: 'Remplacer par:',
		notFoundMsg			: 'Le texte indiqu&eacute; est introuvable.',
		matchCase			: 'Respecter la casse',
		matchWord			: 'Mot entier',
		matchCyclic			: 'Match cyclic', // MISSING
		replaceAll			: 'Tout remplacer',
		replaceSuccessMsg	: '%1 occurrence(s) replaced.' // MISSING
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tableau',
		title		: 'Propri&eacute;t&eacute;s du tableau',
		menu		: 'Propri&eacute;t&eacute;s du tableau',
		deleteTable	: 'Supprimer le tableau',
		rows		: 'Lignes',
		columns		: 'Colonnes',
		border		: 'Taille de la bordure',
		align		: 'Alignement',
		alignLeft	: 'Gauche',
		alignCenter	: 'Centr&eacute;',
		alignRight	: 'Droite',
		width		: 'Largeur',
		widthPx		: 'pixels',
		widthPc		: 'pourcentage',
		widthUnit	: 'width unit', // MISSING
		height		: 'Hauteur',
		cellSpace	: 'Espacement',
		cellPad		: 'Contour',
		caption		: 'Titre',
		summary		: 'R&eacute;sum&eacute;',
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
			menu			: 'Cellule',
			insertBefore	: 'Ins&eacute;rer une cellule avant',
			insertAfter		: 'Ins&eacute;rer une cellule après',
			deleteCell		: 'Supprimer des cellules',
			merge			: 'Fusionner les cellules',
			mergeRight		: 'Fusionner à droite',
			mergeDown		: 'Fusionner en bas',
			splitHorizontal	: 'Scinder la cellule horizontalement',
			splitVertical	: 'Scinder la cellule verticalement',
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
			menu			: 'Ligne',
			insertBefore	: 'Ins&eacute;rer une ligne avant',
			insertAfter		: 'Ins&eacute;rer une ligne après',
			deleteRow		: 'Supprimer des lignes'
		},

		column :
		{
			menu			: 'Colonne',
			insertBefore	: 'Ins&eacute;rer une colonne avant',
			insertAfter		: 'Ins&eacute;rer une colonne après',
			deleteColumn	: 'Supprimer des colonnes'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Propri&eacute;t&eacute;s du bouton',
		text		: 'Texte (Valeur)',
		type		: 'Type',
		typeBtn		: 'Bouton',
		typeSbm		: 'Soumettre',
		typeRst		: 'R&eacute;initialiser'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Propri&eacute;t&eacute;s de la case à cocher',
		radioTitle	: 'Propri&eacute;t&eacute;s du bouton radio',
		value		: 'Valeur',
		selected	: 'S&eacute;lectionn&eacute;'
	},

	// Form Dialog.
	form :
	{
		title		: 'Propri&eacute;t&eacute;s du formulaire',
		menu		: 'Propri&eacute;t&eacute;s du formulaire',
		action		: 'Action',
		method		: 'M&eacute;thode',
		encoding	: 'Encoding' // MISSING
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Propri&eacute;t&eacute;s de la liste/du menu',
		selectInfo	: 'Info',
		opAvail		: 'Options disponibles',
		value		: 'Valeur',
		size		: 'Taille',
		lines		: 'lignes',
		chkMulti	: 'S&eacute;lection multiple',
		opText		: 'Texte',
		opValue		: 'Valeur',
		btnAdd		: 'Ajouter',
		btnModify	: 'Modifier',
		btnUp		: 'Monter',
		btnDown		: 'Descendre',
		btnSetValue : 'Valeur s&eacute;lectionn&eacute;e',
		btnDelete	: 'Supprimer'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Propri&eacute;t&eacute;s de la zone de texte',
		cols		: 'Colonnes',
		rows		: 'Lignes'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Propri&eacute;t&eacute;s du champ texte',
		name		: 'Nom',
		value		: 'Valeur',
		charWidth	: 'Largeur en caractères',
		maxChars	: 'Nombre maximum de caractères',
		type		: 'Type',
		typeText	: 'Texte',
		typePass	: 'Mot de passe'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Propri&eacute;t&eacute;s du champ cach&eacute;',
		name	: 'Nom',
		value	: 'Valeur'
	},

	// Image Dialog.
	image :
	{
		title		: 'Propri&eacute;t&eacute;s de l\'image',
		titleButton	: 'Propri&eacute;t&eacute;s du bouton image',
		menu		: 'Propri&eacute;t&eacute;s de l\'image',
		infoTab		: 'Informations sur l\'image',
		btnUpload	: 'Envoyer sur le serveur',
		upload		: 'T&eacute;l&eacute;charger',
		alt			: 'Texte de remplacement',
		width		: 'Largeur',
		height		: 'Hauteur',
		lockRatio	: 'Garder les proportions',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Taille originale',
		border		: 'Bordure',
		hSpace		: 'Espacement horizontal',
		vSpace		: 'Espacement vertical',
		align		: 'Alignement',
		alignLeft	: 'Gauche',
		alignRight	: 'Droite',
		alertUrl	: 'Veuillez saisir l\'URL de l\'image',
		linkTab		: 'Lien',
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
		properties		: 'Propri&eacute;t&eacute;s de l\'animation Flash',
		propertiesTab	: 'Properties', // MISSING
		title			: 'Propri&eacute;t&eacute;s de l\'animation Flash',
		chkPlay			: 'Lecture automatique',
		chkLoop			: 'Boucle',
		chkMenu			: 'Activer le menu Flash',
		chkFull			: 'Allow Fullscreen', // MISSING
 		scale			: 'Affichage',
		scaleAll		: 'Par d&eacute;faut (tout montrer)',
		scaleNoBorder	: 'Sans bordure',
		scaleFit		: 'Ajuster aux dimensions',
		access			: 'Script Access', // MISSING
		accessAlways	: 'Always', // MISSING
		accessSameDomain: 'Same domain', // MISSING
		accessNever		: 'Never', // MISSING
		align			: 'Alignement',
		alignLeft		: 'Gauche',
		alignAbsBottom	: 'Abs Bas',
		alignAbsMiddle	: 'Abs Milieu',
		alignBaseline	: 'Bas du texte',
		alignBottom		: 'Bas',
		alignMiddle		: 'Milieu',
		alignRight		: 'Droite',
		alignTextTop	: 'Haut du texte',
		alignTop		: 'Haut',
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
		bgcolor			: 'Couleur de fond',
		width			: 'Largeur',
		height			: 'Hauteur',
		hSpace			: 'Espacement horizontal',
		vSpace			: 'Espacement vertical',
		validateSrc		: 'Veuillez saisir l\'URL',
		validateWidth	: 'Width must be a number.', // MISSING
		validateHeight	: 'Height must be a number.', // MISSING
		validateHSpace	: 'HSpace must be a number.', // MISSING
		validateVSpace	: 'VSpace must be a number.' // MISSING
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Orthographe',
		title			: 'Spell Check', // MISSING
		notAvailable	: 'Sorry, but service is unavailable now.', // MISSING
		errorLoading	: 'Error loading application service host: %s.', // MISSING
		notInDic		: 'Pas dans le dictionnaire',
		changeTo		: 'Changer en',
		btnIgnore		: 'Ignorer',
		btnIgnoreAll	: 'Ignorer tout',
		btnReplace		: 'Remplacer',
		btnReplaceAll	: 'Remplacer tout',
		btnUndo			: 'Annuler',
		noSuggestions	: '- Pas de suggestion -',
		progress		: 'V&eacute;rification d\'orthographe en cours...',
		noMispell		: 'V&eacute;rification d\'orthographe termin&eacute;e: pas d\'erreur trouv&eacute;e',
		noChanges		: 'V&eacute;rification d\'orthographe termin&eacute;e: Pas de modifications',
		oneChange		: 'V&eacute;rification d\'orthographe termin&eacute;e: Un mot modifi&eacute;',
		manyChanges		: 'V&eacute;rification d\'orthographe termin&eacute;e: %1 mots modifi&eacute;s',
		ieSpellDownload	: 'Le Correcteur d\'orthographe n\'est pas install&eacute;. Souhaitez-vous le t&eacute;l&eacute;charger maintenant?'
	},

	smiley :
	{
		toolbar	: 'Emoticon',
		title	: 'Ins&eacute;rer un Emoticon',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : '%1 element' // MISSING
	},

	numberedlist	: 'Liste num&eacute;rot&eacute;e',
	bulletedlist	: 'Liste à puces',
	indent			: 'Augmenter le retrait',
	outdent			: 'Diminuer le retrait',

	justify :
	{
		left	: 'Aligner à gauche',
		center	: 'Centrer',
		right	: 'Aligner à Droite',
		block	: 'Texte justifi&eacute;'
	},

	blockquote : 'Citation',

	clipboard :
	{
		title		: 'Coller',
		cutError	: 'Les paramètres de s&eacute;curit&eacute; de votre navigateur empêchent l\'&eacute;diteur de couper automatiquement vos donn&eacute;es. Veuillez utiliser les &eacute;quivalents claviers (Ctrl/Cmd+X).',
		copyError	: 'Les paramètres de s&eacute;curit&eacute; de votre navigateur empêchent l\'&eacute;diteur de copier automatiquement vos donn&eacute;es. Veuillez utiliser les &eacute;quivalents claviers (Ctrl/Cmd+C).',
		pasteMsg	: 'Veuillez coller dans la zone ci-dessous en utilisant le clavier (<STRONG>Ctrl/Cmd+V</STRONG>) et appuyer sur <STRONG>OK</STRONG>.',
		securityMsg	: 'A cause des paramètres de s&eacute;curit&eacute; de votre navigateur, l\'&eacute;diteur ne peut acc&eacute;der au presse-papier directement. Vous devez coller à nouveau le contenu dans cette fenêtre.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'The text you want to paste seems to be copied from Word. Do you want to clean it before pasting?', // MISSING
		toolbar			: 'Coller en tant que Word (format&eacute;)',
		title			: 'Coller en tant que Word (format&eacute;)',
		error			: 'It was not possible to clean up the pasted data due to an internal error' // MISSING
	},

	pasteText :
	{
		button	: 'Coller comme texte',
		title	: 'Coller comme texte'
	},

	templates :
	{
		button			: 'Modèles',
		title			: 'Modèles de contenu',
		options : 'Template Options', // MISSING
		insertOption	: 'Remplacer tout le contenu actuel',
		selectPromptMsg	: 'S&eacute;lectionner le modèle à ouvrir dans l\'&eacute;diteur<br>(le contenu actuel sera remplac&eacute;):',
		emptyListMsg	: '(Aucun modèle disponible)'
	},

	showBlocks : 'Afficher les blocs',

	stylesCombo :
	{
		label		: 'Style',
		panelTitle	: 'Formatting Styles', // MISSING
		panelTitle1	: 'Block Styles', // MISSING
		panelTitle2	: 'Inline Styles', // MISSING
		panelTitle3	: 'Object Styles' // MISSING
	},

	format :
	{
		label		: 'Format',
		panelTitle	: 'Format',

		tag_p		: 'Normal',
		tag_pre		: 'Format&eacute;',
		tag_address	: 'Adresse',
		tag_h1		: 'En-tête 1',
		tag_h2		: 'En-tête 2',
		tag_h3		: 'En-tête 3',
		tag_h4		: 'En-tête 4',
		tag_h5		: 'En-tête 5',
		tag_h6		: 'En-tête 6',
		tag_div		: 'Normal (DIV)'
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
		label		: 'Police',
		voiceLabel	: 'Font', // MISSING
		panelTitle	: 'Police'
	},

	fontSize :
	{
		label		: 'Taille',
		voiceLabel	: 'Font Size', // MISSING
		panelTitle	: 'Taille'
	},

	colorButton :
	{
		textColorTitle	: 'Couleur de caractère',
		bgColorTitle	: 'Couleur de fond',
		panelTitle		: 'Colors', // MISSING
		auto			: 'Automatique',
		more			: 'Plus de couleurs...'
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
