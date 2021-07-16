/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * French language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['fr'] =
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
	save			: 'Enregistrer',
	preview			: 'Aperçu',
	cut				: 'Couper',
	copy			: 'Copier',
	paste			: 'Coller',
	print			: 'Imprimer',
	underline		: 'Soulign&eacute;',
	bold			: 'Gras',
	italic			: 'Italique',
	selectAll		: 'Tout s&eacute;lectionner',
	removeFormat	: 'Supprimer la mise en forme',
	strike			: 'Barr&eacute;',
	subscript		: 'Indice',
	superscript		: 'Exposant',
	horizontalrule	: 'Ligne horizontale',
	pagebreak		: 'Saut de page',
	unlink			: 'Supprimer le lien',
	undo			: 'Annuler',
	redo			: 'R&eacute;tablir',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Explorer le serveur',
		url				: 'URL',
		protocol		: 'Protocole',
		upload			: 'Envoyer',
		uploadSubmit	: 'Envoyer sur le serveur',
		image			: 'Image',
		flash			: 'Flash',
		form			: 'Formulaire',
		checkbox		: 'Case à cocher',
		radio			: 'Bouton Radio',
		textField		: 'Champ texte',
		textarea		: 'Zone de texte',
		hiddenField		: 'Champ cach&eacute;',
		button			: 'Bouton',
		select			: 'Liste d&eacute;roulante',
		imageButton		: 'Bouton image',
		notSet			: '<non d&eacute;fini>',
		id				: 'Id',
		name			: 'Nom',
		langDir			: 'Sens d\'&eacute;criture',
		langDirLtr		: 'Gauche à droite (LTR)',
		langDirRtl		: 'Droite à gauche (RTL)',
		langCode		: 'Code de langue',
		longDescr		: 'URL de description longue (longdesc => malvoyant)',
		cssClass		: 'Classe CSS',
		advisoryTitle	: 'Description (title)',
		cssStyle		: 'Style',
		ok				: 'OK',
		cancel			: 'Annuler',
		close			: 'Close', // MISSING
		preview			: 'Preview', // MISSING
		generalTab		: 'G&eacute;n&eacute;ral',
		advancedTab		: 'Avanc&eacute;',
		validateNumberFailed : 'Cette valeur n\'est pas un nombre.',
		confirmNewPage	: 'Les changements non sauvegard&eacute;s seront perdus. Etes-vous sûr de vouloir charger une nouvelle page?',
		confirmCancel	: 'Certaines options ont &eacute;t&eacute; modifi&eacute;es. Etes-vous sûr de vouloir fermer?',
		options			: 'Options', // MISSING
		target			: 'Target', // MISSING
		targetNew		: 'New Window (_blank)', // MISSING
		targetTop		: 'Topmost Window (_top)', // MISSING
		targetSelf		: 'Same Window (_self)', // MISSING
		targetParent	: 'Parent Window (_parent)', // MISSING

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, Indisponible</span>'
	},

	contextmenu :
	{
		options : 'Context Menu Options' // MISSING
	},

	// Special char dialog.
	specialChar		:
	{
		toolbar		: 'Ins&eacute;rer un caractère sp&eacute;cial',
		title		: 'S&eacute;lectionnez un caractère',
		options : 'Special Character Options' // MISSING
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Lien',
		other 		: '<autre>',
		menu		: 'Editer le lien',
		title		: 'Lien',
		info		: 'Infos sur le lien',
		target		: 'Cible',
		upload		: 'Envoyer',
		advanced	: 'Avanc&eacute;',
		type		: 'Type de lien',
		toUrl		: 'URL', // MISSING
		toAnchor	: 'Transformer le lien en ancre dans le texte',
		toEmail		: 'E-mail',
		targetFrame		: '<cadre>',
		targetPopup		: '<fenêtre popup>',
		targetFrameName	: 'Nom du Cadre destination',
		targetPopupName	: 'Nom de la fenêtre popup',
		popupFeatures	: 'Options de la fenêtre popup',
		popupResizable	: 'Redimensionnable',
		popupStatusBar	: 'Barre de status',
		popupLocationBar: 'Barre d\'adresse',
		popupToolbar	: 'Barre d\'outils',
		popupMenuBar	: 'Barre de menu',
		popupFullScreen	: 'Plein &eacute;cran (IE)',
		popupScrollBars	: 'Barres de d&eacute;filement',
		popupDependent	: 'D&eacute;pendante (Netscape)',
		popupWidth		: 'Largeur',
		popupLeft		: 'Position gauche',
		popupHeight		: 'Hauteur',
		popupTop		: 'Position haute',
		id				: 'Id',
		langDir			: 'Sens d\'&eacute;criture',
		langDirLTR		: 'Gauche à droite',
		langDirRTL		: 'Droite à gauche',
		acccessKey		: 'Touche d\'accessibilit&eacute;',
		name			: 'Nom',
		langCode		: 'Code de langue',
		tabIndex		: 'Index de tabulation',
		advisoryTitle	: 'Description (title)',
		advisoryContentType	: 'Type de contenu (ex: text/html)',
		cssClasses		: 'Classe du CSS',
		charset			: 'Charset de la cible',
		styles			: 'Style',
		selectAnchor	: 'S&eacute;lectionner l\'ancre',
		anchorName		: 'Par nom d\'ancre',
		anchorId		: 'Par ID d\'&eacute;l&eacute;ment',
		emailAddress	: 'Adresse E-Mail',
		emailSubject	: 'Sujet du message',
		emailBody		: 'Corps du message',
		noAnchors		: '(Aucune ancre disponible dans ce document)',
		noUrl			: 'Veuillez entrer l\'adresse du lien',
		noEmail			: 'Veuillez entrer l\'adresse e-mail'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Ancre',
		menu		: 'Editer l\'ancre',
		title		: 'Propri&eacute;t&eacute;s de l\'ancre',
		name		: 'Nom de l\'ancre',
		errorName	: 'Veuillez entrer le nom de l\'ancre'
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
		title				: 'Trouver et remplacer',
		find				: 'Trouver',
		replace				: 'Remplacer',
		findWhat			: 'Expression à trouver: ',
		replaceWith			: 'Remplacer par: ',
		notFoundMsg			: 'Le texte sp&eacute;cifi&eacute; ne peut être trouv&eacute;.',
		matchCase			: 'Respecter la casse',
		matchWord			: 'Mot entier uniquement',
		matchCyclic			: 'Boucler',
		replaceAll			: 'Remplacer tout',
		replaceSuccessMsg	: '%1 occurrence(s) replac&eacute;e(s).'
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
		align		: 'Alignement du contenu',
		alignLeft	: 'Gauche',
		alignCenter	: 'Centr&eacute;',
		alignRight	: 'Droite',
		width		: 'Largeur',
		widthPx		: 'pixels',
		widthPc		: '% pourcents',
		widthUnit	: 'width unit', // MISSING
		height		: 'Hauteur',
		cellSpace	: 'Espacement des cellules',
		cellPad		: 'Marge interne des cellules',
		caption		: 'Titre du tableau',
		summary		: 'R&eacute;sum&eacute; (description)',
		headers		: 'En-Têtes',
		headersNone		: 'Aucunes',
		headersColumn	: 'Première colonne',
		headersRow		: 'Première ligne',
		headersBoth		: 'Les deux',
		invalidRows		: 'Le nombre de lignes doit être sup&eacute;rieur à 0.',
		invalidCols		: 'Le nombre de colonnes doit être sup&eacute;rieur à 0.',
		invalidBorder	: 'La taille de la bordure doit être un nombre.',
		invalidWidth	: 'La largeur du tableau doit être un nombre.',
		invalidHeight	: 'La hauteur du tableau doit être un nombre.',
		invalidCellSpacing	: 'L\'espacement des cellules doit être un nombre.',
		invalidCellPadding	: 'La marge int&eacute;rieure des cellules doit être un nombre.',

		cell :
		{
			menu			: 'Cellule',
			insertBefore	: 'Ins&eacute;rer une cellule avant',
			insertAfter		: 'Ins&eacute;rer une cellule après',
			deleteCell		: 'Supprimer les cellules',
			merge			: 'Fusionner les cellules',
			mergeRight		: 'Fusionner à droite',
			mergeDown		: 'Fusionner en bas',
			splitHorizontal	: 'Fractionner horizontalement',
			splitVertical	: 'Fractionner verticalement',
			title			: 'Propri&eacute;t&eacute;s de Cellule',
			cellType		: 'Type de Cellule',
			rowSpan			: 'Fusion de Lignes',
			colSpan			: 'Fusion de Colonnes',
			wordWrap		: 'Word Wrap', // MISSING
			hAlign			: 'Alignement Horizontal',
			vAlign			: 'Alignement Vertical',
			alignTop		: 'Haut',
			alignMiddle		: 'Milieu',
			alignBottom		: 'Bas',
			alignBaseline	: 'Bas du texte',
			bgColor			: 'Couleur d\'arrière-plan',
			borderColor		: 'Couleur de Bordure',
			data			: 'Donn&eacute;es',
			header			: 'Entête',
			yes				: 'Oui',
			no				: 'Non',
			invalidWidth	: 'La Largeur de Cellule doit être un nombre.',
			invalidHeight	: 'La Hauteur de Cellule doit être un nombre.',
			invalidRowSpan	: 'La fusion de lignes doit être un nombre entier.',
			invalidColSpan	: 'La fusion de colonnes doit être un nombre entier.',
			chooseColor		: 'Choose' // MISSING
		},

		row :
		{
			menu			: 'Ligne',
			insertBefore	: 'Ins&eacute;rer une ligne avant',
			insertAfter		: 'Ins&eacute;rer une ligne après',
			deleteRow		: 'Supprimer les lignes'
		},

		column :
		{
			menu			: 'Colonnes',
			insertBefore	: 'Ins&eacute;rer une colonne avant',
			insertAfter		: 'Ins&eacute;rer une colonne après',
			deleteColumn	: 'Supprimer les colonnes'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Propri&eacute;t&eacute;s du bouton',
		text		: 'Texte (Value)',
		type		: 'Type',
		typeBtn		: 'Bouton',
		typeSbm		: 'Validation (submit)',
		typeRst		: 'Remise à z&eacute;ro'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Propri&eacute;t&eacute;s de la case à cocher',
		radioTitle	: 'Propri&eacute;t&eacute;s du bouton Radio',
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
		encoding	: 'Encodage'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Propri&eacute;t&eacute;s du menu d&eacute;roulant',
		selectInfo	: 'Informations sur le menu d&eacute;roulant',
		opAvail		: 'Options disponibles',
		value		: 'Valeur',
		size		: 'Taille',
		lines		: 'Lignes',
		chkMulti	: 'Permettre les s&eacute;lections multiples',
		opText		: 'Texte',
		opValue		: 'Valeur',
		btnAdd		: 'Ajouter',
		btnModify	: 'Modifier',
		btnUp		: 'Haut',
		btnDown		: 'Bas',
		btnSetValue : 'D&eacute;finir comme valeur s&eacute;lectionn&eacute;e',
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
		charWidth	: 'Taille des caractères',
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
		upload		: 'Envoyer',
		alt			: 'Texte de remplacement',
		width		: 'Largeur',
		height		: 'Hauteur',
		lockRatio	: 'Garder les proportions',
		unlockRatio	: 'Unlock Ratio', // MISSING
		resetSize	: 'Taille d\'origine',
		border		: 'Bordure',
		hSpace		: 'Espacement horizontal',
		vSpace		: 'Espacement vertical',
		align		: 'Alignement',
		alignLeft	: 'Gauche',
		alignRight	: 'Droite',
		alertUrl	: 'Veuillez entrer l\'adresse de l\'image',
		linkTab		: 'Lien',
		button2Img	: 'Voulez-vous transformer le bouton image s&eacute;lectionn&eacute; en simple image?',
		img2Button	: 'Voulez-vous transformer l\'image en bouton image?',
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
		properties		: 'Propri&eacute;t&eacute;s du Flash',
		propertiesTab	: 'Propri&eacute;t&eacute;s',
		title			: 'Propri&eacute;t&eacute;s du Flash',
		chkPlay			: 'Jouer automatiquement',
		chkLoop			: 'Boucle',
		chkMenu			: 'Activer le menu Flash',
		chkFull			: 'Permettre le plein &eacute;cran',
 		scale			: 'Echelle',
		scaleAll		: 'Afficher tout',
		scaleNoBorder	: 'Pas de bordure',
		scaleFit		: 'Taille d\'origine',
		access			: 'Accès aux scripts',
		accessAlways	: 'Toujours',
		accessSameDomain: 'Même domaine',
		accessNever		: 'Jamais',
		align			: 'Alignement',
		alignLeft		: 'Gauche',
		alignAbsBottom	: 'Bas absolu',
		alignAbsMiddle	: 'Milieu absolu',
		alignBaseline	: 'Bas du texte',
		alignBottom		: 'Bas',
		alignMiddle		: 'Milieu',
		alignRight		: 'Droite',
		alignTextTop	: 'Haut du texte',
		alignTop		: 'Haut',
		quality			: 'Qualit&eacute;',
		qualityBest		: 'Meilleure',
		qualityHigh		: 'Haute',
		qualityAutoHigh	: 'Haute Auto',
		qualityMedium	: 'Moyenne',
		qualityAutoLow	: 'Basse Auto',
		qualityLow		: 'Basse',
		windowModeWindow: 'Fenêtre',
		windowModeOpaque: 'Opaque',
		windowModeTransparent : 'Transparent',
		windowMode		: 'Mode fenêtre',
		flashvars		: 'Variables du Flash',
		bgcolor			: 'Couleur d\'arrière-plan',
		width			: 'Largeur',
		height			: 'Hauteur',
		hSpace			: 'Espacement horizontal',
		vSpace			: 'Espacement vertical',
		validateSrc		: 'L\'adresse ne doit pas être vide.',
		validateWidth	: 'La largeur doit être un nombre.',
		validateHeight	: 'La hauteur doit être un nombre.',
		validateHSpace	: 'L\'espacement horizontal doit être un nombre.',
		validateVSpace	: 'L\'espacement vertical doit être un nombre.'
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'V&eacute;rifier l\'orthographe',
		title			: 'V&eacute;rifier l\'orthographe',
		notAvailable	: 'D&eacute;sol&eacute;, le service est indisponible actuellement.',
		errorLoading	: 'Erreur du chargement du service depuis l\'hôte : %s.',
		notInDic		: 'N\'existe pas dans le dictionnaire',
		changeTo		: 'Modifier pour',
		btnIgnore		: 'Ignorer',
		btnIgnoreAll	: 'Ignorer tout',
		btnReplace		: 'Remplacer',
		btnReplaceAll	: 'Remplacer tout',
		btnUndo			: 'Annuler',
		noSuggestions	: '- Aucune suggestion -',
		progress		: 'V&eacute;rification de l\'orthographe en cours...',
		noMispell		: 'V&eacute;rification de l\'orthographe termin&eacute;e : aucune erreur trouv&eacute;e',
		noChanges		: 'V&eacute;rification de l\'orthographe termin&eacute;e : Aucun mot corrig&eacute;',
		oneChange		: 'V&eacute;rification de l\'orthographe termin&eacute;e : Un seul mot corrig&eacute;',
		manyChanges		: 'V&eacute;rification de l\'orthographe termin&eacute;e : %1 mots corrig&eacute;s',
		ieSpellDownload	: 'La v&eacute;rification d\'orthographe n\'est pas install&eacute;e. Voulez-vous la t&eacute;l&eacute;charger maintenant?'
	},

	smiley :
	{
		toolbar	: 'Emoticon',
		title	: 'Ins&eacute;rer un &eacute;moticon',
		options : 'Smiley Options' // MISSING
	},

	elementsPath :
	{
		eleLabel : 'Elements path', // MISSING
		eleTitle : '%1 &eacute;l&eacute;ments'
	},

	numberedlist	: 'Ins&eacute;rer/Supprimer la liste num&eacute;rot&eacute;e',
	bulletedlist	: 'Ins&eacute;rer/Supprimer la liste à puces',
	indent			: 'Augmenter le retrait (tabulation)',
	outdent			: 'Diminuer le retrait (tabulation)',

	justify :
	{
		left	: 'Aligner à gauche',
		center	: 'Centrer',
		right	: 'Aligner à droite',
		block	: 'Justifier'
	},

	blockquote : 'Citation',

	clipboard :
	{
		title		: 'Coller',
		cutError	: 'Les paramètres de s&eacute;curit&eacute; de votre navigateur ne permettent pas à l\'&eacute;diteur d\'ex&eacute;cuter automatiquement l\'op&eacute;ration "couper". Veuillez utiliser le raccourci clavier (Ctrl/Cmd+X).',
		copyError	: 'Les paramètres de s&eacute;curit&eacute; de votre navigateur ne permettent pas à l\'&eacute;diteur d\'ex&eacute;cuter automatiquement des op&eacute;rations de copie. Veuillez utiliser le raccourci clavier (Ctrl/Cmd+C).',
		pasteMsg	: 'Veuillez coller le texte dans la zone suivante en utilisant le raccourci clavier (<strong>Ctrl/Cmd+V</strong>) et cliquez sur OK',
		securityMsg	: 'A cause des paramètres de s&eacute;curit&eacute; de votre navigateur, l\'&eacute;diteur n\'est pas en mesure d\'acc&eacute;der directement à vos donn&eacute;es contenues dans le presse-papier. Vous devriez r&eacute;essayer de coller les donn&eacute;es dans la fenêtre.',
		pasteArea	: 'Paste Area' // MISSING
	},

	pastefromword :
	{
		confirmCleanup	: 'Le texte à coller semble provenir de Word. D&eacute;sirez-vous le nettoyer avant de coller?',
		toolbar			: 'Coller depuis Word',
		title			: 'Coller depuis Word',
		error			: 'It was not possible to clean up the pasted data due to an internal error' // MISSING
	},

	pasteText :
	{
		button	: 'Coller comme texte sans mise en forme',
		title	: 'Coller comme texte sans mise en forme'
	},

	templates :
	{
		button			: 'Modèles',
		title			: 'Contenu des modèles',
		options : 'Template Options', // MISSING
		insertOption	: 'Remplacer le contenu actuel',
		selectPromptMsg	: 'Veuillez s&eacute;lectionner le modèle pour l\'ouvrir dans l\'&eacute;diteur',
		emptyListMsg	: '(Aucun modèle disponible)'
	},

	showBlocks : 'Afficher les blocs',

	stylesCombo :
	{
		label		: 'Styles',
		panelTitle	: 'Formatting Styles', // MISSING
		panelTitle1	: 'Styles de blocs',
		panelTitle2	: 'Styles en ligne',
		panelTitle3	: 'Styles d\'objet'
	},

	format :
	{
		label		: 'Format',
		panelTitle	: 'Format de paragraphe',

		tag_p		: 'Normal',
		tag_pre		: 'Format&eacute;',
		tag_address	: 'Adresse',
		tag_h1		: 'Titre 1',
		tag_h2		: 'Titre 2',
		tag_h3		: 'Titre 3',
		tag_h4		: 'Titre 4',
		tag_h5		: 'Titre 5',
		tag_h6		: 'Titre 6',
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
		voiceLabel	: 'Police',
		panelTitle	: 'Style de police'
	},

	fontSize :
	{
		label		: 'Taille',
		voiceLabel	: 'Taille de police',
		panelTitle	: 'Taille de police'
	},

	colorButton :
	{
		textColorTitle	: 'Couleur de texte',
		bgColorTitle	: 'Couleur d\'arrière plan',
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
		title			: 'V&eacute;rification d\'Orthographe en Cours de Frappe (SCAYT: Spell Check As You Type)',
		opera_title		: 'Not supported by Opera', // MISSING
		enable			: 'Activer SCAYT',
		disable			: 'D&eacute;sactiver SCAYT',
		about			: 'A propos de SCAYT',
		toggle			: 'Activer/D&eacute;sactiver SCAYT',
		options			: 'Options',
		langs			: 'Langues',
		moreSuggestions	: 'Plus de suggestions',
		ignore			: 'Ignorer',
		ignoreAll		: 'Ignorer Tout',
		addWord			: 'Ajouter le mot',
		emptyDic		: 'Le nom du dictionnaire ne devrait pas être vide.',

		optionsTab		: 'Options',
		allCaps			: 'Ignore All-Caps Words', // MISSING
		ignoreDomainNames : 'Ignore Domain Names', // MISSING
		mixedCase		: 'Ignore Words with Mixed Case', // MISSING
		mixedWithDigits	: 'Ignore Words with Numbers', // MISSING

		languagesTab	: 'Langues',

		dictionariesTab	: 'Dictionnaires',
		dic_field_name	: 'Dictionary name', // MISSING
		dic_create		: 'Create', // MISSING
		dic_restore		: 'Restore', // MISSING
		dic_delete		: 'Delete', // MISSING
		dic_rename		: 'Rename', // MISSING
		dic_info		: 'Initially the User Dictionary is stored in a Cookie. However, Cookies are limited in size. When the User Dictionary grows to a point where it cannot be stored in a Cookie, then the dictionary may be stored on our server. To store your personal dictionary on our server you should specify a name for your dictionary. If you already have a stored dictionary, please type it\'s name and click the Restore button.', // MISSING

		aboutTab		: 'A propos de'
	},

	about :
	{
		title		: 'A propos de CKEditor',
		dlgTitle	: 'A propos de CKEditor',
		moreInfo	: 'Pour les informations de licence, veuillez visiter notre site web:',
		copy		: 'Copyright &copy; $1. Tous droits r&eacute;serv&eacute;s.'
	},

	maximize : 'Agrandir',
	minimize : 'Minimize', // MISSING

	fakeobjects :
	{
		anchor	: 'Ancre',
		flash	: 'Animation Flash',
		div		: 'Saut de Page',
		unknown	: 'Objet Inconnu'
	},

	resize : 'Glisser pour modifier la taille',

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
