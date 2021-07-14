/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @fileOverview Defines the {@link CKEDITOR.lang} object, for the
 * Vietnamese language.
 */

/**#@+
   @type String
   @example
*/

/**
 * Constains the dictionary of language entries.
 * @namespace
 */
CKEDITOR.lang['vi'] =
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
	editorTitle : 'Bộ soạn thảo, %1, nhấn ALT + 0 để xem hướng dẫn.',

	// ARIA descriptions.
	toolbar	: 'Thanh công cụ',
	editor	: 'Bộ soạn thảo',

	// Toolbar buttons without dialogs.
	source			: 'Mã HTML',
	newPage			: 'Trang mới',
	save			: 'Lưu',
	preview			: 'Xem trước',
	cut				: 'Cắt',
	copy			: 'Sao ch&eacute;p',
	paste			: 'D&aacute;n',
	print			: 'In',
	underline		: 'Gạch chân',
	bold			: 'Đậm',
	italic			: 'Nghiêng',
	selectAll		: 'Chọn tất cả',
	removeFormat	: 'Xo&aacute; định dạng',
	strike			: 'Gạch xuyên ngang',
	subscript		: 'Chỉ số dưới',
	superscript		: 'Chỉ số trên',
	horizontalrule	: 'Chèn đường phân c&aacute;ch ngang',
	pagebreak		: 'Chèn ngắt trang',
	unlink			: 'Xo&aacute; liên kết',
	undo			: 'Khôi phục thao t&aacute;c',
	redo			: 'Làm lại thao t&aacute;c',

	// Common messages and labels.
	common :
	{
		browseServer	: 'Duyệt trên m&aacute;y chủ',
		url				: 'URL',
		protocol		: 'Giao thức',
		upload			: 'Tải lên',
		uploadSubmit	: 'Tải lên m&aacute;y chủ',
		image			: 'Hình ảnh',
		flash			: 'Flash',
		form			: 'Biểu mẫu',
		checkbox		: 'N&uacute;t kiểm',
		radio			: 'N&uacute;t chọn',
		textField		: 'Trường văn bản',
		textarea		: 'Vùng văn bản',
		hiddenField		: 'Trường ẩn',
		button			: 'N&uacute;t',
		select			: 'Ô chọn',
		imageButton		: 'N&uacute;t hình ảnh',
		notSet			: '<không thiết lập>',
		id				: 'Định danh',
		name			: 'Tên',
		langDir			: 'Hướng ngôn ngữ',
		langDirLtr		: 'Tr&aacute;i sang phải (LTR)',
		langDirRtl		: 'Phải sang tr&aacute;i (RTL)',
		langCode		: 'Mã ngôn ngữ',
		longDescr		: 'Mô tả URL',
		cssClass		: 'Lớp Stylesheet',
		advisoryTitle	: 'Nhan đề hướng dẫn',
		cssStyle		: 'Kiểu (style)',
		ok				: 'Đồng ý',
		cancel			: 'Bỏ qua',
		close			: 'Đ&oacute;ng',
		preview			: 'Xem trước',
		generalTab		: 'Tab chung',
		advancedTab		: 'Tab mở rộng',
		validateNumberFailed : 'Gi&aacute; trị này không phải là số.',
		confirmNewPage	: 'Mọi thay đổi không được lưu lại, nội dung này sẽ bị mất. Bạn c&oacute; chắc chắn muốn tải một trang mới?',
		confirmCancel	: 'Một vài tùy chọn đã bị thay đổi. Bạn c&oacute; chắc chắn muốn đ&oacute;ng hộp thoại?',
		options			: 'Tùy chọn',
		target			: 'Đ&iacute;ch đến',
		targetNew		: 'Cửa sổ mới (_blank)',
		targetTop		: 'Cửa sổ trên cùng (_top)',
		targetSelf		: 'Tại trang (_self)',
		targetParent	: 'Cửa sổ cha (_parent)',

		// Put the voice-only part of the label in the span.
		unavailable		: '%1<span class="cke_accessibility">, không c&oacute;</span>'
	},

	contextmenu :
	{
		options : 'Tùy chọn menu bổ xung'
	},

	// Special char dialog.
	specialChar		:
	{
		toolbar		: 'Chèn ký tự đặc biệt',
		title		: 'Hãy chọn ký tự đặc biệt',
		options : 'Tùy chọn c&aacute;c ký tự đặc biệt'
	},

	// Link dialog.
	link :
	{
		toolbar		: 'Chèn/Sửa liên kết',
		other 		: '<kh&aacute;c>',
		menu		: 'Sửa liên kết',
		title		: 'Liên kết',
		info		: 'Thông tin liên kết',
		target		: 'Đ&iacute;ch',
		upload		: 'Tải lên',
		advanced	: 'Mở rộng',
		type		: 'Kiểu liên kết',
		toUrl		: 'URL',
		toAnchor	: 'Neo trong trang này',
		toEmail		: 'Thư điện tử',
		targetFrame		: '<khung>',
		targetPopup		: '<cửa sổ popup>',
		targetFrameName	: 'Tên khung đ&iacute;ch',
		targetPopupName	: 'Tên cửa sổ Popup',
		popupFeatures	: 'Đặc điểm của cửa sổ Popup',
		popupResizable	: 'C&oacute; thể thay đổi k&iacute;ch cỡ',
		popupStatusBar	: 'Thanh trạng th&aacute;i',
		popupLocationBar: 'Thanh vị tr&iacute;',
		popupToolbar	: 'Thanh công cụ',
		popupMenuBar	: 'Thanh Menu',
		popupFullScreen	: 'Toàn màn hình (IE)',
		popupScrollBars	: 'Thanh cuộn',
		popupDependent	: 'Phụ thuộc (Netscape)',
		popupWidth		: 'Rộng',
		popupLeft		: 'Vị tr&iacute; bên tr&aacute;i',
		popupHeight		: 'Cao',
		popupTop		: 'Vị tr&iacute; ph&iacute;a trên',
		id				: 'Định danh',
		langDir			: 'Hướng ngôn ngữ',
		langDirLTR		: 'Tr&aacute;i sang phải (LTR)',
		langDirRTL		: 'Phải sang tr&aacute;i (RTL)',
		acccessKey		: 'Ph&iacute;m hỗ trợ truy cập',
		name			: 'Tên',
		langCode		: 'Mã ngôn ngữ',
		tabIndex		: 'Chỉ số của Tab',
		advisoryTitle	: 'Nhan đề hướng dẫn',
		advisoryContentType	: 'Nội dung hướng dẫn',
		cssClasses		: 'Lớp Stylesheet',
		charset			: 'Bảng mã của tài nguyên được liên kết đến',
		styles			: 'Kiểu (style)',
		selectAnchor	: 'Chọn một điểm neo',
		anchorName		: 'Theo tên điểm neo',
		anchorId		: 'Theo định danh thành phần',
		emailAddress	: 'Thư điện tử',
		emailSubject	: 'Tiêu đề thông điệp',
		emailBody		: 'Nội dung thông điệp',
		noAnchors		: '(Không c&oacute; điểm neo nào trong tài liệu)',
		noUrl			: 'Hãy đưa vào đường dẫn liên kết (URL)',
		noEmail			: 'Hãy đưa vào địa chỉ thư điện tử'
	},

	// Anchor dialog
	anchor :
	{
		toolbar		: 'Chèn/Sửa điểm neo',
		menu		: 'Thuộc t&iacute;nh điểm neo',
		title		: 'Thuộc t&iacute;nh điểm neo',
		name		: 'Tên của điểm neo',
		errorName	: 'Hãy nhập vào tên của điểm neo'
	},

	// List style dialog
	list:
	{
		numberedTitle		: 'Thuộc t&iacute;nh danh s&aacute;ch c&oacute; thứ tự',
		bulletedTitle		: 'Thuộc t&iacute;nh danh s&aacute;ch không thứ tự',
		type				: 'Kiểu loại',
		start				: 'Bắt đầu',
		circle				: 'Khuyên tròn',
		disc				: 'Hình đĩa',
		square				: 'Hình vuông',
		none				: 'Không gì cả',
		notset				: '<không thiết lập>',
		armenian			: 'Số theo kiểu Armenian',
		georgian			: 'Số theo kiểu Georgian (an, ban, gan...)',
		lowerRoman			: 'Số La Mã kiểu thường (i, ii, iii, iv, v...)',
		upperRoman			: 'Số La Mã kiểu HOA (I, II, III, IV, V...)',
		lowerAlpha			: 'Kiểu abc thường (a, b, c, d, e...)',
		upperAlpha			: 'Kiểu ABC HOA (A, B, C, D, E...)',
		lowerGreek			: 'Kiểu Hy Lạp (alpha, beta, gamma...)',
		decimal				: 'Kiểu số (1, 2, 3 ...)',
		decimalLeadingZero	: 'Kiểu số (01, 02, 03...)'
	},

	// Find And Replace Dialog
	findAndReplace :
	{
		title				: 'Tìm kiếm và thay thế',
		find				: 'Tìm kiếm',
		replace				: 'Thay thế',
		findWhat			: 'Tìm chuỗi:',
		replaceWith			: 'Thay bằng:',
		notFoundMsg			: 'Không tìm thấy chuỗi cần tìm.',
		matchCase			: 'Phân biệt chữ hoa/thường',
		matchWord			: 'Giống toàn bộ từ',
		matchCyclic			: 'Giống một phần',
		replaceAll			: 'Thay thế tất cả',
		replaceSuccessMsg	: '%1 vị tr&iacute; đã được thay thế.'
	},

	// Table Dialog
	table :
	{
		toolbar		: 'Bảng',
		title		: 'Thuộc t&iacute;nh bảng',
		menu		: 'Thuộc t&iacute;nh bảng',
		deleteTable	: 'X&oacute;a bảng',
		rows		: 'Số hàng',
		columns		: 'Số cột',
		border		: 'K&iacute;ch thước đường viền',
		align		: 'Canh lề',
		alignLeft	: 'Tr&aacute;i',
		alignCenter	: 'Giữa',
		alignRight	: 'Phải',
		width		: 'Rộng',
		widthPx		: 'Điểm ảnh (px)',
		widthPc		: 'Phần trăm (%)',
		widthUnit	: 'Đơn vị',
		height		: 'Chiều cao',
		cellSpace	: 'Khoảng c&aacute;ch giữa c&aacute;c ô',
		cellPad		: 'Khoảng đệm giữ ô và nội dung',
		caption		: 'Đầu đề',
		summary		: 'T&oacute;m lược',
		headers		: 'Đầu đề',
		headersNone		: 'Không c&oacute;',
		headersColumn	: 'Cột đầu tiên',
		headersRow		: 'Hàng đầu tiên',
		headersBoth		: 'Cả hai',
		invalidRows		: 'Số lượng hàng phải là một số lớn hơn 0.',
		invalidCols		: 'Số lượng cột phải là một số lớn hơn 0.',
		invalidBorder	: 'K&iacute;ch cỡ của đường biên phải là một số nguyên.',
		invalidWidth	: 'Chiều rộng của bảng phải là một số nguyên.',
		invalidHeight	: 'Chiều cao của bảng phải là một số nguyên.',
		invalidCellSpacing	: 'Khoảng c&aacute;ch giữa c&aacute;c ô phải là một số nguyên.',
		invalidCellPadding	: 'Khoảng đệm giữa ô và nội dung phải là một số nguyên.',

		cell :
		{
			menu			: 'Ô',
			insertBefore	: 'Chèn ô Ph&iacute;a trước',
			insertAfter		: 'Chèn ô Ph&iacute;a sau',
			deleteCell		: 'Xo&aacute; ô',
			merge			: 'Kết hợp ô',
			mergeRight		: 'Kết hợp sang phải',
			mergeDown		: 'Kết hợp xuống dưới',
			splitHorizontal	: 'Phân t&aacute;ch ô theo chiều ngang',
			splitVertical	: 'Phân t&aacute;ch ô theo chiều dọc',
			title			: 'Thuộc t&iacute;nh của ô',
			cellType		: 'Kiểu của ô',
			rowSpan			: 'Kết hợp hàng',
			colSpan			: 'Kết hợp cột',
			wordWrap		: 'Chữ liền hàng',
			hAlign			: 'Canh lề ngang',
			vAlign			: 'Canh lề dọc',
			alignTop		: 'Trên cùng',
			alignMiddle		: 'Ch&iacute;nh giữa',
			alignBottom		: 'Dưới cùng',
			alignBaseline	: 'Đường cơ sở',
			bgColor			: 'Màu nền',
			borderColor		: 'Màu viền',
			data			: 'Dữ liệu',
			header			: 'Đầu đề',
			yes				: 'C&oacute;',
			no				: 'Không',
			invalidWidth	: 'Chiều rộng của ô phải là một số nguyên.',
			invalidHeight	: 'Chiều cao của ô phải là một số nguyên.',
			invalidRowSpan	: 'Số hàng kết hợp phải là một số nguyên.',
			invalidColSpan	: 'Số cột kết hợp phải là một số nguyên.',
			chooseColor		: 'Chọn màu'
		},

		row :
		{
			menu			: 'Hàng',
			insertBefore	: 'Chèn hàng ph&iacute;a trước',
			insertAfter		: 'Chèn hàng ph&iacute;a sau',
			deleteRow		: 'Xo&aacute; hàng'
		},

		column :
		{
			menu			: 'Cột',
			insertBefore	: 'Chèn cột ph&iacute;a trước',
			insertAfter		: 'Chèn cột ph&iacute;a sau',
			deleteColumn	: 'Xo&aacute; cột'
		}
	},

	// Button Dialog.
	button :
	{
		title		: 'Thuộc t&iacute;nh của n&uacute;t',
		text		: 'Chuỗi hiển thị (gi&aacute; trị)',
		type		: 'Kiểu',
		typeBtn		: 'N&uacute;t bấm',
		typeSbm		: 'N&uacute;t gửi',
		typeRst		: 'N&uacute;t nhập lại'
	},

	// Checkbox and Radio Button Dialogs.
	checkboxAndRadio :
	{
		checkboxTitle : 'Thuộc t&iacute;nh n&uacute;t kiểm',
		radioTitle	: 'Thuộc t&iacute;nh n&uacute;t chọn',
		value		: 'Gi&aacute; trị',
		selected	: 'Được chọn'
	},

	// Form Dialog.
	form :
	{
		title		: 'Thuộc t&iacute;nh biểu mẫu',
		menu		: 'Thuộc t&iacute;nh biểu mẫu',
		action		: 'Hành động',
		method		: 'Phương thức',
		encoding	: 'Bảng mã'
	},

	// Select Field Dialog.
	select :
	{
		title		: 'Thuộc t&iacute;nh ô chọn',
		selectInfo	: 'Thông tin',
		opAvail		: 'C&aacute;c tùy chọn c&oacute; thể sử dụng',
		value		: 'Gi&aacute; trị',
		size		: 'K&iacute;ch cỡ',
		lines		: 'dòng',
		chkMulti	: 'Cho ph&eacute;p chọn nhiều',
		opText		: 'Văn bản',
		opValue		: 'Gi&aacute; trị',
		btnAdd		: 'Thêm',
		btnModify	: 'Thay đổi',
		btnUp		: 'Lên',
		btnDown		: 'Xuống',
		btnSetValue : 'Gi&aacute; trị được chọn',
		btnDelete	: 'N&uacute;t xo&aacute;'
	},

	// Textarea Dialog.
	textarea :
	{
		title		: 'Thuộc t&iacute;nh vùng văn bản',
		cols		: 'Số cột',
		rows		: 'Số hàng'
	},

	// Text Field Dialog.
	textfield :
	{
		title		: 'Thuộc t&iacute;nh trường văn bản',
		name		: 'Tên',
		value		: 'Gi&aacute; trị',
		charWidth	: 'Độ rộng của ký tự',
		maxChars	: 'Số ký tự tối đa',
		type		: 'Kiểu',
		typeText	: 'Ký tự',
		typePass	: 'Mật khẩu'
	},

	// Hidden Field Dialog.
	hidden :
	{
		title	: 'Thuộc t&iacute;nh trường ẩn',
		name	: 'Tên',
		value	: 'Gi&aacute; trị'
	},

	// Image Dialog.
	image :
	{
		title		: 'Thuộc t&iacute;nh của ảnh',
		titleButton	: 'Thuộc t&iacute;nh n&uacute;t của ảnh',
		menu		: 'Thuộc t&iacute;nh của ảnh',
		infoTab		: 'Thông tin của ảnh',
		btnUpload	: 'Tải lên m&aacute;y chủ',
		upload		: 'Tải lên',
		alt			: 'Ch&uacute; th&iacute;ch ảnh',
		width		: 'Chiều rộng',
		height		: 'chiều cao',
		lockRatio	: 'Giữ nguyên tỷ lệ',
		unlockRatio	: 'Ph&aacute; bỏ tỷ lệ',
		resetSize	: 'K&iacute;ch thước gốc',
		border		: 'Đường viền',
		hSpace		: 'Khoảng đệm ngang',
		vSpace		: 'Khoảng đệm dọc',
		align		: 'Vị tr&iacute;',
		alignLeft	: 'Bên tr&aacute;i',
		alignRight	: 'Bên phải',
		alertUrl	: 'Hãy đưa vào đường dẫn của ảnh',
		linkTab		: 'Tab liên kết',
		button2Img	: 'Bạn c&oacute; muốn chuyển n&uacute;t bấm bằng ảnh được chọn thành ảnh?',
		img2Button	: 'Bạn c&oacute; muốn chuyển đổi ảnh được chọn thành n&uacute;t bấm bằng ảnh?',
		urlMissing	: 'Thiếu đường dẫn hình ảnh',
		validateWidth	: 'Chiều rộng của ảnh phải là một số nguyên dương',
		validateHeight	: 'Chiều cao của ảnh phải là một số nguyên dương',
		validateBorder	: 'Chiều rộng của đường viền phải là một số nguyên dương',
		validateHSpace	: 'Khoảng đệm ngang phải là một số nguyên dương',
		validateVSpace	: 'Khoảng đệm dọc phải là một số nguyên dương'
	},

	// Flash Dialog
	flash :
	{
		properties		: 'Thuộc t&iacute;nh Flash',
		propertiesTab	: 'Thuộc t&iacute;nh',
		title			: 'Thuộc t&iacute;nh Flash',
		chkPlay			: 'Tự động chạy',
		chkLoop			: 'Lặp',
		chkMenu			: 'Cho ph&eacute;p bật menu của Flash',
		chkFull			: 'Cho ph&eacute;p toàn màn hình',
 		scale			: 'Tỷ lệ',
		scaleAll		: 'Hiển thị tất cả',
		scaleNoBorder	: 'Không đường viền',
		scaleFit		: 'Vừa vặn',
		access			: 'Truy cập mã',
		accessAlways	: 'Luôn luôn',
		accessSameDomain: 'Cùng tên miền',
		accessNever		: 'Không bao giờ',
		align			: 'Vị tr&iacute;',
		alignLeft		: 'Tr&aacute;i',
		alignAbsBottom	: 'Dưới tuyệt đối',
		alignAbsMiddle	: 'Giữa tuyệt đối',
		alignBaseline	: 'Đường cơ sở',
		alignBottom		: 'Dưới',
		alignMiddle		: 'Giữa',
		alignRight		: 'Phải',
		alignTextTop	: 'Ph&iacute;a trên chữ',
		alignTop		: 'Trên',
		quality			: 'Chất lượng',
		qualityBest		: 'Tốt nhất',
		qualityHigh		: 'Cao',
		qualityAutoHigh	: 'Cao tự động',
		qualityMedium	: 'Trung bình',
		qualityAutoLow	: 'Thấp tự động',
		qualityLow		: 'Thấp',
		windowModeWindow: 'Cửa sổ',
		windowModeOpaque: 'Mờ đục',
		windowModeTransparent : 'Trong suốt',
		windowMode		: 'Chế độ cửa sổ',
		flashvars		: 'C&aacute;c biến số dành cho Flash',
		bgcolor			: 'Màu nền',
		width			: 'Rộng',
		height			: 'Cao',
		hSpace			: 'Khoảng đệm ngang',
		vSpace			: 'Khoảng đệm dọc',
		validateSrc		: 'Hãy đưa vào đường dẫn liên kết',
		validateWidth	: 'Chiều rộng phải là số nguyên.',
		validateHeight	: 'Chiều cao phải là số nguyên.',
		validateHSpace	: 'Khoảng đệm ngang phải là số nguyên.',
		validateVSpace	: 'Khoảng đệm dọc phải là số nguyên.'
	},

	// Speller Pages Dialog
	spellCheck :
	{
		toolbar			: 'Kiểm tra ch&iacute;nh tả',
		title			: 'Kiểm tra ch&iacute;nh tả',
		notAvailable	: 'Xin lỗi, dịch vụ này hiện tại không c&oacute;.',
		errorLoading	: 'Lỗi khi đang nạp dịch vụ ứng dụng: %s.',
		notInDic		: 'Không c&oacute; trong từ điển',
		changeTo		: 'Chuyển thành',
		btnIgnore		: 'Bỏ qua',
		btnIgnoreAll	: 'Bỏ qua tất cả',
		btnReplace		: 'Thay thế',
		btnReplaceAll	: 'Thay thế tất cả',
		btnUndo			: 'Phục hồi lại',
		noSuggestions	: '- Không đưa ra gợi ý về từ -',
		progress		: 'Đang tiến hành kiểm tra ch&iacute;nh tả...',
		noMispell		: 'Hoàn tất kiểm tra ch&iacute;nh tả: Không c&oacute; lỗi ch&iacute;nh tả',
		noChanges		: 'Hoàn tất kiểm tra ch&iacute;nh tả: Không c&oacute; từ nào được thay đổi',
		oneChange		: 'Hoàn tất kiểm tra ch&iacute;nh tả: Một từ đã được thay đổi',
		manyChanges		: 'Hoàn tất kiểm tra ch&iacute;nh tả: %1 từ đã được thay đổi',
		ieSpellDownload	: 'Chức năng kiểm tra ch&iacute;nh tả chưa được cài đặt. Bạn c&oacute; muốn tải về ngay bây giờ?'
	},

	smiley :
	{
		toolbar	: 'Hình biểu lộ cảm x&uacute;c (mặt cười)',
		title	: 'Chèn hình biểu lộ cảm x&uacute;c (mặt cười)',
		options : 'Tùy chọn hình  biểu lộ cảm x&uacute;c'
	},

	elementsPath :
	{
		eleLabel : 'Nhãn thành phần',
		eleTitle : '%1 thành phần'
	},

	numberedlist	: 'Danh s&aacute;ch c&oacute; thứ tự',
	bulletedlist	: 'Danh s&aacute;ch không thứ tự',
	indent			: 'Dịch vào trong',
	outdent			: 'Dịch ra ngoài',

	justify :
	{
		left	: 'Canh tr&aacute;i',
		center	: 'Canh giữa',
		right	: 'Canh phải',
		block	: 'Canh đều'
	},

	blockquote : 'Khối tr&iacute;ch dẫn',

	clipboard :
	{
		title		: 'D&aacute;n',
		cutError	: 'C&aacute;c thiết lập bảo mật của trình duyệt không cho ph&eacute;p trình biên tập tự động thực thi lệnh cắt. Hãy sử dụng bàn ph&iacute;m cho lệnh này (Ctrl/Cmd+X).',
		copyError	: 'C&aacute;c thiết lập bảo mật của trình duyệt không cho ph&eacute;p trình biên tập tự động thực thi lệnh sao ch&eacute;p. Hãy sử dụng bàn ph&iacute;m cho lệnh này (Ctrl/Cmd+C).',
		pasteMsg	: 'Hãy d&aacute;n nội dung vào trong khung bên dưới, sử dụng tổ hợp ph&iacute;m (<STRONG>Ctrl/Cmd+V</STRONG>) và nhấn vào n&uacute;t <STRONG>Đồng ý</STRONG>.',
		securityMsg	: 'Do thiết lập bảo mật của trình duyệt nên trình biên tập không thể truy cập trực tiếp vào nội dung đã sao ch&eacute;p. Bạn cần phải d&aacute;n lại nội dung vào cửa sổ này.',
		pasteArea	: 'Khu vực d&aacute;n'
	},

	pastefromword :
	{
		confirmCleanup	: 'Văn bản bạn muốn d&aacute;n c&oacute; kèm định dạng của Word. Bạn c&oacute; muốn loại bỏ định dạng Word trước khi d&aacute;n?',
		toolbar			: 'D&aacute;n với định dạng Word',
		title			: 'D&aacute;n với định dạng Word',
		error			: 'It was not possible to clean up the pasted data due to an internal error' // MISSING
	},

	pasteText :
	{
		button	: 'D&aacute;n theo định dạng văn bản thuần',
		title	: 'D&aacute;n theo định dạng văn bản thuần'
	},

	templates :
	{
		button			: 'Mẫu dựng sẵn',
		title			: 'Nội dung Mẫu dựng sẵn',
		options : 'Tùy chọn mẫu dựng sẵn',
		insertOption	: 'Thay thế nội dung hiện tại',
		selectPromptMsg	: 'Hãy chọn mẫu dựng sẵn để mở trong trình biên tập<br>(nội dung hiện tại sẽ bị mất):',
		emptyListMsg	: '(Không c&oacute; mẫu dựng sẵn nào được định nghĩa)'
	},

	showBlocks : 'Hiển thị c&aacute;c khối',

	stylesCombo :
	{
		label		: 'Kiểu',
		panelTitle	: 'Phong c&aacute;ch định dạng',
		panelTitle1	: 'Kiểu khối',
		panelTitle2	: 'Kiểu trực tiếp',
		panelTitle3	: 'Kiểu đối tượng'
	},

	format :
	{
		label		: 'Định dạng',
		panelTitle	: 'Định dạng',

		tag_p		: 'Bình thường (P)',
		tag_pre		: 'Đã thiết lập',
		tag_address	: 'Address',
		tag_h1		: 'Heading 1',
		tag_h2		: 'Heading 2',
		tag_h3		: 'Heading 3',
		tag_h4		: 'Heading 4',
		tag_h5		: 'Heading 5',
		tag_h6		: 'Heading 6',
		tag_div		: 'Bình thường (DIV)'
	},

	div :
	{
		title				: 'Tạo khối c&aacute;c thành phần',
		toolbar				: 'Tạo khối c&aacute;c thành phần',
		cssClassInputLabel	: 'C&aacute;c lớp CSS',
		styleSelectLabel	: 'Kiểu (style)',
		IdInputLabel		: 'Định danh (id)',
		languageCodeInputLabel	: 'Mã ngôn ngữ',
		inlineStyleInputLabel	: 'Kiểu nội dòng',
		advisoryTitleInputLabel	: 'Nhan đề hướng dẫn',
		langDirLabel		: 'Hướng ngôn ngữ',
		langDirLTRLabel		: 'Tr&aacute;i sang phải (LTR)',
		langDirRTLLabel		: 'Phải qua tr&aacute;i (RTL)',
		edit				: 'Chỉnh sửa',
		remove				: 'X&oacute;a bỏ'
  	},

	font :
	{
		label		: 'Phông',
		voiceLabel	: 'Phông',
		panelTitle	: 'Phông'
	},

	fontSize :
	{
		label		: 'Cỡ chữ',
		voiceLabel	: 'K&iacute;ch cỡ phông',
		panelTitle	: 'Cỡ chữ'
	},

	colorButton :
	{
		textColorTitle	: 'Màu chữ',
		bgColorTitle	: 'Màu nền',
		panelTitle		: 'Màu sắc',
		auto			: 'Tự động',
		more			: 'Màu kh&aacute;c...'
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
		title			: 'Kiểm tra ch&iacute;nh tả ngay khi gõ chữ (SCAYT)',
		opera_title		: 'Không hỗ trợ trên trình duyệt Opera',
		enable			: 'Bật SCAYT',
		disable			: 'Tắt SCAYT',
		about			: 'Thông tin về SCAYT',
		toggle			: 'Bật tắt SCAYT',
		options			: 'Tùy chọn',
		langs			: 'Ngôn ngữ',
		moreSuggestions	: 'Đề xuất thêm',
		ignore			: 'Bỏ qua',
		ignoreAll		: 'Bỏ qua tất cả',
		addWord			: 'Thêm từ',
		emptyDic		: 'Tên của từ điển không được để trống.',

		optionsTab		: 'Tùy chọn',
		allCaps			: 'Không phân biệt chữ HOA chữ thường',
		ignoreDomainNames : 'Bỏ qua tên miền',
		mixedCase		: 'Không phân biệt loại chữ',
		mixedWithDigits	: 'Không phân biệt chữ và số',

		languagesTab	: 'Tab ngôn ngữ',

		dictionariesTab	: 'Từ điển',
		dic_field_name	: 'Tên từ điển',
		dic_create		: 'Tạo',
		dic_restore		: 'Phục hồi',
		dic_delete		: 'X&oacute;a',
		dic_rename		: 'Thay tên',
		dic_info		: 'Initially the User Dictionary is stored in a Cookie. However, Cookies are limited in size. When the User Dictionary grows to a point where it cannot be stored in a Cookie, then the dictionary may be stored on our server. To store your personal dictionary on our server you should specify a name for your dictionary. If you already have a stored dictionary, please type it\'s name and click the Restore button.', // MISSING

		aboutTab		: 'Thông tin'
	},

	about :
	{
		title		: 'Thông tin về CKEditor',
		dlgTitle	: 'Thông tin về CKEditor',
		moreInfo	: 'Vui lòng gh&eacute; thăm trang web của ch&uacute;ng tôi để c&oacute; thông tin về giấy ph&eacute;p:',
		copy		: 'Bản quyền &copy; $1. Giữ toàn quyền.'
	},

	maximize : 'Ph&oacute;ng to tối đa',
	minimize : 'Thu nhỏ',

	fakeobjects :
	{
		anchor	: 'Điểm neo',
		flash	: 'Flash',
		div		: 'Ngắt trang',
		unknown	: 'Đối tượng không rõ ràng'
	},

	resize : 'K&eacute;o rê để thay đổi k&iacute;ch cỡ',

	colordialog :
	{
		title		: 'Chọn màu',
		options	:	'Color Options', // MISSING
		highlight	: 'Màu chọn',
		selected	: 'Màu đã chọn',
		clear		: 'X&oacute;a bỏ'
	},

	toolbarCollapse	: 'Thu gọn thanh công cụ',
	toolbarExpand	: 'Mở rộng thnah công cụ'
};
