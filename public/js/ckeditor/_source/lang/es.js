﻿/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Spanish language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['es'] =
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
	editorTitle : 'Editor de texto, %1, pulse ALT 0 para ayuda.',

	// ARIA descriptions.
	toolbar	: 'Barra de herramientas',
	editor	: 'Editor de texto enriquecido',

	// Toolbar buttons without dialogs.
	source			: 'Fuente HTML',
	newPage			: 'Nueva P&aacute;gina',
	save			: 'Guardar',
	preview			: 'Vista Previa',
	cut				: 'Cortar',
	copy			: 'Copiar',
	paste			: 'Pegar',
	print			: 'Imprimir',
	underline		: 'Subrayado',
	bold			: 'Negrita',
	italic			: 'Cursiva',
	selectAll		: 'Seleccionar Todo',
	removeFormat	: 'Eliminar Formato',
	strike			: 'Tachado',
	subscript		: 'Sub&iacute;ndice',
	superscript		: 'Super&iacute;ndice',
	horizontalrule	: 'Insertar L&iacute;nea Horizontal',
	pagebreak		: 'Insertar Salto de P&aacute;gina',
	unlink			: 'Eliminar V&iacute;nculo',
	undo			: 'Deshacer',
	redo			: 'Rehacer',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Ver Servidor',
		url				: 'URL',
		protocol		: 'Protocolo',
		upload			: 'Cargar',
		uploadSubmit	: 'Enviar al Servidor',
		image			: 'Imagen',
		flash			: 'Flash',
		form			: 'Formulario',
		checkbox		: 'Casilla de Verificaci&oacute;n',
		radio			: 'Botones de Radio',
		textField		: 'Campo de Texto',
		textarea		: 'Area de Texto',
		hiddenField		: 'Campo Oculto',
		button			: 'Bot&oacute;n',
		select			: 'Campo de Selecci&oacute;n',
		imageButton		: 'Bot&oacute;n Imagen',
		notSet			: '<No definido>',
		id				: 'Id',
		name			: 'Nombre',
		langDir			: 'Orientaci&oacute;n',
		langDirLtr		: 'Izquierda a Derecha (LTR)',
		langDirRtl		: 'Derecha a Izquierda (RTL)',
		langCode		: 'C&oacute;d. de idioma',
		longDescr		: 'Descripci&oacute;n larga URL',
		cssClass		: 'Clases de hojas de estilo',
		advisoryTitle	: 'T&iacute;tulo',
		cssStyle		: 'Estilo',
		ok				: 'Aceptar',
		cancel			: 'Cancelar',
		close			: 'Cerrar',
		preview			: 'Previsualizaci&oacute;n',
		generalTab		: 'General',
		advancedTab		: 'Avanzado',
		validateNumberFailed : 'El valor no es un n&uacute;mero.',
		confirmNewPage	: 'Cualquier cambio que no se haya guardado se perder&aacute;.\r\n¿Est&aacute; seguro de querer crear una nueva p&aacute;gina?',
		confirmCancel	: 'Algunas de las opciones se han cambiado.\r\n¿Est&aacute; seguro de querer cerrar el di&aacute;logo?',
		options			: 'Opciones',
		target			: 'Destino',
		targetNew		: 'Nueva ventana (_blank)',
		targetTop		: 'Ventana principal (_top)',
		targetSelf		: 'Misma ventana (_self)',
		targetParent	: 'Ventana padre (_parent)',

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, no disponible</span>'
	},

	contextmenu :
	{
		options : 'Opciones del men&uacute; contextual'
	},

	// Special char dialog.
	specialChar		:
	{
		toolbar		: 'Insertar Caracter Especial',
		title		: 'Seleccione un caracter especial',
		options : 'Opciones de caracteres especiales'
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Insertar/Editar V&iacute;nculo',
		other 		: '<otro>',
		menu		: 'Editar V&iacute;nculo',
		title		: 'V&iacute;nculo',
		info		: 'Informaci&oacute;n de V&iacute;nculo',
		target		: 'Destino',
		upload		: 'Cargar',
		advanced	: 'Avanzado',
		type		: 'Tipo de v&iacute;nculo',
		toUrl		: 'URL',
		toAnchor	: 'Referencia en esta p&aacute;gina',
		toEmail		: 'E-Mail',
		targetFrame		: '<marco>',
		targetPopup		: '<ventana emergente>',
		targetFrameName	: 'Nombre del Marco Destino',
		targetPopupName	: 'Nombre de Ventana Emergente',
		popupFeatures	: 'Caracter&iacute;sticas de Ventana Emergente',
		popupResizable	: 'Redimensionable',
		popupStatusBar	: 'Barra de Estado',
		popupLocationBar: 'Barra de ubicaci&oacute;n',
		popupToolbar	: 'Barra de Herramientas',
		popupMenuBar	: 'Barra de Men&uacute;',
		popupFullScreen	: 'Pantalla Completa (IE)',
		popupScrollBars	: 'Barras de desplazamiento',
		popupDependent	: 'Dependiente (Netscape)',
		popupWidth		: 'Anchura',
		popupLeft		: 'Posici&oacute;n Izquierda',
		popupHeight		: 'Altura',
		popupTop		: 'Posici&oacute;n Derecha',
		id				: 'Id',
		langDir			: 'Orientaci&oacute;n',
		langDirLTR		: 'Izquierda a Derecha (LTR)',
		langDirRTL		: 'Derecha a Izquierda (RTL)',
		acccessKey		: 'Clave de Acceso',
		name			: 'Nombre',
		langCode		: 'C&oacute;digo idioma',
		tabIndex		: 'Indice de tabulaci&oacute;n',
		advisoryTitle	: 'T&iacute;tulo',
		advisoryContentType	: 'Tipo de Contenido',
		cssClasses		: 'Clases de hojas de estilo',
		charset			: 'Fuente de caracteres vinculado',
		styles			: 'Estilo',
		selectAnchor	: 'Seleccionar una referencia',
		anchorName		: 'Por Nombre de Referencia',
		anchorId		: 'Por ID de elemento',
		emailAddress	: 'Direcci&oacute;n de E-Mail',
		emailSubject	: 'T&iacute;tulo del Mensaje',
		emailBody		: 'Cuerpo del Mensaje',
		noAnchors		: '(No hay referencias disponibles en el documento)',
		noUrl			: 'Por favor tipee el v&iacute;nculo URL',
		noEmail			: 'Por favor tipee la direcci&oacute;n de e-mail'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Referencia',
		menu		: 'Propiedades de Referencia',
		title		: 'Propiedades de Referencia',
		name		: 'Nombre de la Referencia',
		errorName	: 'Por favor, complete el nombre de la Referencia'
	},

	// List style dialog
	list:
	{
		numberedTitle		: 'Propiedades de lista numerada',
		bulletedTitle		: 'Propiedades de viñetas',
		type				: 'Tipo',
		start				: 'Inicio',
		circle				: 'C&iacute;rculo',
		disc				: 'Disco',
		square				: 'Cuadrado',
		none				: 'Ninguno',
		notset				: '<sin establecer>',
		armenian			: 'Numeraci&oacute;n armenia',
		georgian			: 'Numeraci&oacute;n georgiana (an, ban, gan, etc.)',
		lowerRoman			: 'N&uacute;meros romanos en min&uacute;sculas (i, ii, iii, iv, v, etc.)',
		upperRoman			: 'N&uacute;meros romanos en may&uacute;sculas (I, II, III, IV, V, etc.)',
		lowerAlpha			: 'Alfabeto en min&uacute;sculas (a, b, c, d, e, etc.)',
		upperAlpha			: 'Alfabeto en may&uacute;sculas  (A, B, C, D, E, etc.)',
		lowerGreek			: 'Letras griegas (alpha, beta, gamma, etc.)',
		decimal				: 'Decimal (1, 2, 3, etc.)',
		decimalLeadingZero	: 'Decimal con cero inicial (01, 02, 03, etc.)'
	},

	// Find And Replace Dialog
	findAndReplace :
	{
		title				: 'Buscar y Reemplazar',
		find				: 'Buscar',
		replace				: 'Reemplazar',
		findWhat			: 'Texto a buscar:',
		replaceWith			: 'Reemplazar con:',
		notFoundMsg			: 'El texto especificado no ha sido encontrado.',
		matchCase			: 'Coincidir may/min',
		matchWord			: 'Coincidir toda la palabra',
		matchCyclic			: 'Buscar en todo el contenido',
		replaceAll			: 'Reemplazar Todo',
		replaceSuccessMsg	: 'La expresi&oacute;n buscada ha sido reemplazada %1 veces.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Tabla',
		title		: 'Propiedades de Tabla',
		menu		: 'Propiedades de Tabla',
		deleteTable	: 'Eliminar Tabla',
		rows		: 'Filas',
		columns		: 'Columnas',
		border		: 'Tamaño de Borde',
		align		: 'Alineaci&oacute;n',
		alignLeft	: 'Izquierda',
		alignCenter	: 'Centrado',
		alignRight	: 'Derecha',
		width		: 'Anchura',
		widthPx		: 'pixeles',
		widthPc		: 'porcentaje',
		widthUnit	: 'unidad de la anchura',
		height		: 'Altura',
		cellSpace	: 'Esp. e/celdas',
		cellPad		: 'Esp. interior',
		caption		: 'T&iacute;tulo',
		summary		: 'S&iacute;ntesis',
		headers		: 'Encabezados',
		headersNone		: 'Ninguno',
		headersColumn	: 'Primera columna',
		headersRow		: 'Primera fila',
		headersBoth		: 'Ambas',
		invalidRows		: 'El n&uacute;mero de filas debe ser un n&uacute;mero mayor que 0.',
		invalidCols		: 'El n&uacute;mero de columnas debe ser un n&uacute;mero mayor que 0.',
		invalidBorder	: 'El tamaño del borde debe ser un n&uacute;mero.',
		invalidWidth	: 'La anchura de tabla debe ser un n&uacute;mero.',
		invalidHeight	: 'La altura de tabla debe ser un n&uacute;mero.',
		invalidCellSpacing	: 'El espaciado entre celdas debe ser un n&uacute;mero.',
		invalidCellPadding	: 'El espaciado interior debe ser un n&uacute;mero.',

		cell :
		{
			menu			: 'Celda',
			insertBefore	: 'Insertar celda a la izquierda',
			insertAfter		: 'Insertar celda a la derecha',
			deleteCell		: 'Eliminar Celdas',
			merge			: 'Combinar Celdas',
			mergeRight		: 'Combinar a la derecha',
			mergeDown		: 'Combinar hacia abajo',
			splitHorizontal	: 'Dividir la celda horizontalmente',
			splitVertical	: 'Dividir la celda verticalmente',
			title			: 'Propiedades de celda',
			cellType		: 'Tipo de Celda',
			rowSpan			: 'Expandir filas',
			colSpan			: 'Expandir columnas',
			wordWrap		: 'Ajustar al contenido',
			hAlign			: 'Alineaci&oacute;n Horizontal',
			vAlign			: 'Alineaci&oacute;n Vertical',
			alignTop		: 'Arriba',
			alignMiddle		: 'Medio',
			alignBottom		: 'Abajo',
			alignBaseline	: 'Linea de base',
			bgColor			: 'Color de fondo',
			borderColor		: 'Color de borde',
			data			: 'Datos',
			header			: 'Encabezado',
			yes				: 'S&iacute;',
			no				: 'No',
			invalidWidth	: 'La anchura de celda debe ser un n&uacute;mero.',
			invalidHeight	: 'La altura de celda debe ser un n&uacute;mero.',
			invalidRowSpan	: 'La expansi&oacute;n de filas debe ser un n&uacute;mero entero.',
			invalidColSpan	: 'La expansi&oacute;n de columnas debe ser un n&uacute;mero entero.',
			chooseColor		: 'Elegir'
		},

		row :
		{
			menu			: 'Fila',
			insertBefore	: 'Insertar fila en la parte superior',
			insertAfter		: 'Insertar fila en la parte inferior',
			deleteRow		: 'Eliminar Filas'
		},

		column :
		{
			menu			: 'Columna',
			insertBefore	: 'Insertar columna a la izquierda',
			insertAfter		: 'Insertar columna a la derecha',
			deleteColumn	: 'Eliminar Columnas'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Propiedades de Bot&oacute;n',
		text		: 'Texto (Valor)',
		type		: 'Tipo',
		typeBtn		: 'Boton',
		typeSbm		: 'Enviar',
		typeRst		: 'Reestablecer'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Propiedades de Casilla',
		radioTitle	: 'Propiedades de Bot&oacute;n de Radio',
		value		: 'Valor',
		selected	: 'Seleccionado'
	},

	// Form Dialog.
	form :
	{
		title		: 'Propiedades de Formulario',
		menu		: 'Propiedades de Formulario',
		action		: 'Acci&oacute;n',
		method		: 'M&eacute;todo',
		encoding	: 'Codificaci&oacute;n'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Propiedades de Campo de Selecci&oacute;n',
		selectInfo	: 'Informaci&oacute;n',
		opAvail		: 'Opciones disponibles',
		value		: 'Valor',
		size		: 'Tamaño',
		lines		: 'Lineas',
		chkMulti	: 'Permitir m&uacute;ltiple selecci&oacute;n',
		opText		: 'Texto',
		opValue		: 'Valor',
		btnAdd		: 'Agregar',
		btnModify	: 'Modificar',
		btnUp		: 'Subir',
		btnDown		: 'Bajar',
		btnSetValue : 'Establecer como predeterminado',
		btnDelete	: 'Eliminar'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Propiedades de Area de Texto',
		cols		: 'Columnas',
		rows		: 'Filas'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Propiedades de Campo de Texto',
		name		: 'Nombre',
		value		: 'Valor',
		charWidth	: 'Caracteres de ancho',
		maxChars	: 'M&aacute;ximo caracteres',
		type		: 'Tipo',
		typeText	: 'Texto',
		typePass	: 'Contraseña'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Propiedades de Campo Oculto',
		name	: 'Nombre',
		value	: 'Valor'
	},

	// Image Dialog.
	image :
	{
		title		: 'Propiedades de Imagen',
		titleButton	: 'Propiedades de Bot&oacute;n de Imagen',
		menu		: 'Propiedades de Imagen',
		infoTab		: 'Informaci&oacute;n de Imagen',
		btnUpload	: 'Enviar al Servidor',
		upload		: 'Cargar',
		alt			: 'Texto Alternativo',
		width		: 'Anchura',
		height		: 'Altura',
		lockRatio	: 'Proporcional',
		unlockRatio	: 'Desbloquear el proporcional',
		resetSize	: 'Tamaño Original',
		border		: 'Borde',
		hSpace		: 'Esp.Horiz',
		vSpace		: 'Esp.Vert',
		align		: 'Alineaci&oacute;n',
		alignLeft	: 'Izquierda',
		alignRight	: 'Derecha',
		alertUrl	: 'Por favor escriba la URL de la imagen',
		linkTab		: 'V&iacute;nculo',
		button2Img	: '¿Desea convertir el bot&oacute;n de imagen en una simple imagen?',
		img2Button	: '¿Desea convertir la imagen en un bot&oacute;n de imagen?',
		urlMissing	: 'Debe indicar la URL de la imagen.',
		validateWidth	: 'La anchura debe ser un n&uacute;mero.',
		validateHeight	: 'La altura debe ser un n&uacute;mero.',
		validateBorder	: 'El borde debe ser un n&uacute;mero.',
		validateHSpace	: 'El espaciado horizontal debe ser un n&uacute;mero.',
		validateVSpace	: 'El espaciado vertical debe ser un n&uacute;mero.'
	},

	// Flash Dialog
	flash :
	{
		properties		: 'Propiedades de Flash',
		propertiesTab	: 'Propiedades',
		title			: 'Propiedades de Flash',
		chkPlay			: 'Autoejecuci&oacute;n',
		chkLoop			: 'Repetir',
		chkMenu			: 'Activar Men&uacute; Flash',
		chkFull			: 'Permitir pantalla completa',
 		scale			: 'Escala',
		scaleAll		: 'Mostrar todo',
		scaleNoBorder	: 'Sin Borde',
		scaleFit		: 'Ajustado',
		access			: 'Acceso de scripts',
		accessAlways	: 'Siempre',
		accessSameDomain: 'Mismo dominio',
		accessNever		: 'Nunca',
		align			: 'Alineaci&oacute;n',
		alignLeft		: 'Izquierda',
		alignAbsBottom	: 'Abs inferior',
		alignAbsMiddle	: 'Abs centro',
		alignBaseline	: 'L&iacute;nea de base',
		alignBottom		: 'Pie',
		alignMiddle		: 'Centro',
		alignRight		: 'Derecha',
		alignTextTop	: 'Tope del texto',
		alignTop		: 'Tope',
		quality			: 'Calidad',
		qualityBest		: 'La mejor',
		qualityHigh		: 'Alta',
		qualityAutoHigh	: 'Auto Alta',
		qualityMedium	: 'Media',
		qualityAutoLow	: 'Auto Baja',
		qualityLow		: 'Baja',
		windowModeWindow: 'Ventana',
		windowModeOpaque: 'Opaco',
		windowModeTransparent : 'Transparente',
		windowMode		: 'WindowMode',
		flashvars		: 'Opciones',
		bgcolor			: 'Color de Fondo',
		width			: 'Anchura',
		height			: 'Altura',
		hSpace			: 'Esp.Horiz',
		vSpace			: 'Esp.Vert',
		validateSrc		: 'Por favor escriba el v&iacute;nculo URL',
		validateWidth	: 'Anchura debe ser un n&uacute;mero.',
		validateHeight	: 'Altura debe ser un n&uacute;mero.',
		validateHSpace	: 'Esp.Horiz debe ser un n&uacute;mero.',
		validateVSpace	: 'Esp.Vert debe ser un n&uacute;mero.'
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Ortograf&iacute;a',
		title			: 'Comprobar ortograf&iacute;a',
		notAvailable	: 'Lo sentimos pero el servicio no est&aacute; disponible.',
		errorLoading	: 'Error cargando la aplicaci&oacute;n del servidor: %s.',
		notInDic		: 'No se encuentra en el Diccionario',
		changeTo		: 'Cambiar a',
		btnIgnore		: 'Ignorar',
		btnIgnoreAll	: 'Ignorar Todo',
		btnReplace		: 'Reemplazar',
		btnReplaceAll	: 'Reemplazar Todo',
		btnUndo			: 'Deshacer',
		noSuggestions	: '- No hay sugerencias -',
		progress		: 'Control de Ortograf&iacute;a en progreso...',
		noMispell		: 'Control finalizado: no se encontraron errores',
		noChanges		: 'Control finalizado: no se ha cambiado ninguna palabra',
		oneChange		: 'Control finalizado: se ha cambiado una palabra',
		manyChanges		: 'Control finalizado: se ha cambiado %1 palabras',
		ieSpellDownload	: 'M&oacute;dulo de Control de Ortograf&iacute;a no instalado.\r\n¿Desea descargarlo ahora?'
	},

	smiley :
	{
		toolbar	: 'Emoticonos',
		title	: 'Insertar un Emoticon',
		options : 'Opciones de emoticonos'
	},

	elementsPath :
	{
		eleLabel : 'Ruta de los elementos',
		eleTitle : '%1 elemento'
	},

	numberedlist	: 'Numeraci&oacute;n',
	bulletedlist	: 'Viñetas',
	indent			: 'Aumentar Sangr&iacute;a',
	outdent			: 'Disminuir Sangr&iacute;a',

	justify :
	{
		left	: 'Alinear a Izquierda',
		center	: 'Centrar',
		right	: 'Alinear a Derecha',
		block	: 'Justificado'
	},

	blockquote : 'Cita',

	clipboard :
	{
		title		: 'Pegar',
		cutError	: 'La configuraci&oacute;n de seguridad de este navegador no permite la ejecuci&oacute;n autom&aacute;tica de operaciones de cortado.\r\nPor favor use el teclado (Ctrl/Cmd+X).',
		copyError	: 'La configuraci&oacute;n de seguridad de este navegador no permite la ejecuci&oacute;n autom&aacute;tica de operaciones de copiado.\r\nPor favor use el teclado (Ctrl/Cmd+C).',
		pasteMsg	: 'Por favor pegue dentro del cuadro utilizando el teclado (<STRONG>Ctrl/Cmd+V</STRONG>);\r\nluego presione <STRONG>Aceptar</STRONG>.',
		securityMsg	: 'Debido a la configuraci&oacute;n de seguridad de su navegador, el editor no tiene acceso al portapapeles.\r\nEs necesario que lo pegue de nuevo en esta ventana.',
		pasteArea	: 'Zona de pegado'
	},

	pastefromword :
	{
		confirmCleanup	: 'El texto que desea parece provenir de Word.\r\n¿Desea depurarlo antes de pegarlo?',
		toolbar			: 'Pegar desde Word',
		title			: 'Pegar desde Word',
		error			: 'No ha sido posible limpiar los datos debido a un error interno'
	},

	pasteText :
	{
		button	: 'Pegar como Texto Plano',
		title	: 'Pegar como Texto Plano'
	},

	templates :
	{
		button			: 'Plantillas',
		title			: 'Contenido de Plantillas',
		options : 'Opciones de plantillas',
		insertOption	: 'Reemplazar el contenido actual',
		selectPromptMsg	: 'Por favor selecciona la plantilla a abrir en el editor<br>(el contenido actual se perder&aacute;):',
		emptyListMsg	: '(No hay plantillas definidas)'
	},

	showBlocks : 'Mostrar bloques',

	stylesCombo :
	{
		label		: 'Estilo',
		panelTitle	: 'Estilos para formatear',
		panelTitle1	: 'Estilos de p&aacute;rrafo',
		panelTitle2	: 'Estilos de car&aacute;cter',
		panelTitle3	: 'Estilos de objeto'
	},

	format :
	{
		label		: 'Formato',
		panelTitle	: 'Formato',

		tag_p		: 'Normal',
		tag_pre		: 'Con formato',
		tag_address	: 'Direcci&oacute;n',
		tag_h1		: 'Encabezado 1',
		tag_h2		: 'Encabezado 2',
		tag_h3		: 'Encabezado 3',
		tag_h4		: 'Encabezado 4',
		tag_h5		: 'Encabezado 5',
		tag_h6		: 'Encabezado 6',
		tag_div		: 'Normal (DIV)'
	},

	div :
	{
		title				: 'Crear contenedor DIV',
		toolbar				: 'Crear contenedor DIV',
		cssClassInputLabel	: 'Clase de hoja de estilos',
		styleSelectLabel	: 'Estilo',
		IdInputLabel		: 'Id',
		languageCodeInputLabel	: ' Codigo de idioma',
		inlineStyleInputLabel	: 'Estilo',
		advisoryTitleInputLabel	: 'T&iacute;tulo',
		langDirLabel		: 'Orientaci&oacute;n',
		langDirLTRLabel		: 'Izquierda a Derecha (LTR)',
		langDirRTLLabel		: 'Derecha a Izquierda (RTL)',
		edit				: 'Editar Div',
		remove				: 'Quitar Div'
  	},

	font :
	{
		label		: 'Fuente',
		voiceLabel	: 'Fuente',
		panelTitle	: 'Fuente'
	},

	fontSize :
	{
		label		: 'Tamaño',
		voiceLabel	: 'Tamaño de fuente',
		panelTitle	: 'Tamaño'
	},

	colorButton :
	{
		textColorTitle	: 'Color de Texto',
		bgColorTitle	: 'Color de Fondo',
		panelTitle		: 'Colores',
		auto			: 'Autom&aacute;tico',
		more			: 'M&aacute;s Colores...'
	},

	colors :
	{
		'000' : 'Negro',
		'800000' : 'Marr&oacute;n oscuro',
		'8B4513' : 'Marr&oacute;n tierra',
		'2F4F4F' : 'Pizarra Oscuro',
		'008080' : 'Azul verdoso',
		'000080' : 'Azul marino',
		'4B0082' : 'Añil',
		'696969' : 'Gris oscuro',
		'B22222' : 'Ladrillo',
		'A52A2A' : 'Marr&oacute;n',
		'DAA520' : 'Oro oscuro',
		'006400' : 'Verde oscuro',
		'40E0D0' : 'Turquesa',
		'0000CD' : 'Azul medio-oscuro',
		'800080' : 'P&uacute;rpura',
		'808080' : 'Gris',
		'F00' : 'Rojo',
		'FF8C00' : 'Naranja oscuro',
		'FFD700' : 'Oro',
		'008000' : 'Verde',
		'0FF' : 'Cian',
		'00F' : 'Azul',
		'EE82EE' : 'Violeta',
		'A9A9A9' : 'Gris medio',
		'FFA07A' : 'Salm&oacute;n claro',
		'FFA500' : 'Naranja',
		'FFFF00' : 'Amarillo',
		'00FF00' : 'Lima',
		'AFEEEE' : 'Turquesa claro',
		'ADD8E6' : 'Azul claro',
		'DDA0DD' : 'Violeta claro',
		'D3D3D3' : 'Gris claro',
		'FFF0F5' : 'Lavanda rojizo',
		'FAEBD7' : 'Blanco antiguo',
		'FFFFE0' : 'Amarillo claro',
		'F0FFF0' : 'Miel',
		'F0FFFF' : 'Azul celeste',
		'F0F8FF' : 'Azul p&aacute;lido',
		'E6E6FA' : 'Lavanda',
		'FFF' : 'Blanco'
	},

	scayt :
	{
		title			: 'Comprobar Ortograf&iacute;a Mientras Escribe',
		opera_title		: 'No soportado en Opera',
		enable			: 'Activar Corrector',
		disable			: 'Desactivar Corrector',
		about			: 'Acerca de Corrector',
		toggle			: 'Cambiar Corrector',
		options			: 'Opciones',
		langs			: 'Idiomas',
		moreSuggestions	: 'M&aacute;s sugerencias',
		ignore			: 'Ignorar',
		ignoreAll		: 'Ignorar Todas',
		addWord			: 'Añadir palabra',
		emptyDic		: 'El nombre del diccionario no puede estar en blanco.',

		optionsTab		: 'Opciones',
		allCaps			: 'Omitir palabras en MAY&uacute;SCULAS',
		ignoreDomainNames : 'Omitir nombres de dominio',
		mixedCase		: 'Ignorar palabras con combinaci&oacute;n de may&uacute;sculas y min&uacute;sculas',
		mixedWithDigits	: 'Omitir palabras con n&uacute;meros',

		languagesTab	: 'Idiomas',

		dictionariesTab	: 'Diccionarios',
		dic_field_name	: 'Nombre del diccionario',
		dic_create		: 'Crear',
		dic_restore		: 'Recuperar',
		dic_delete		: 'Borrar',
		dic_rename		: 'Renombrar',
		dic_info		: 'Inicialmente el Diccionario de usuario se guarda en una Cookie. Sin embargo, las cookies est&aacute;n limitadas en tamaño. Cuando el diccionario crece a un punto en el que no se puede guardar en una Cookie, el diccionario puede ser almacenado en nuestro servidor. Para almacenar su diccionario personalizado en nuestro servidor debe especificar un nombre para su diccionario. Si ya ha guardado un diccionaro, por favor, escriba su nombre y pulse el bot&oacute;n Recuperar',

		aboutTab		: 'Acerca de'
	},

	about :
	{
		title		: 'Acerca de CKEditor',
		dlgTitle	: 'Acerca de CKEditor',
		moreInfo	: 'Para informaci&oacute;n de licencia, por favor visite nuestro sitio web:',
		copy		: 'Copyright &copy; $1. Todos los derechos reservados.'
	},

	maximize : 'Maximizar',
	minimize : 'Minimizar',

	fakeobjects :
	{
		anchor	: 'Ancla',
		flash	: 'Animaci&oacute;n flash',
		div		: 'Salto de p&aacute;gina',
		unknown	: 'Objeto desconocido'
	},

	resize : 'Arrastre para redimensionar',

	colordialog :
	{
		title		: 'Elegir color',
		options	:	'Opciones de colores',
		highlight	: 'Muestra',
		selected	: 'Elegido',
		clear		: 'Borrar'
	},

	toolbarCollapse	: 'Contraer barra de herramientas',
	toolbarExpand	: 'Expandir barra de herramientas'
};
