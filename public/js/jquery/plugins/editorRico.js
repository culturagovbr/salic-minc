jQuery.fn.editorRico = function (options) {

    if (!options) {
        options = {};
    }

    var minchar = (options.minchar) ? options.minchar : -1;
    var maxchar = (options.maxchar) ? options.maxchar : 1000;
    var isLimitarCarateres = (typeof  options.isLimitarCarateres !== 'undefined') ? options.isLimitarCarateres : false;
    var isDesabilitarEdicao = (typeof  options.isDesabilitarEdicao !== 'undefined') ? options.isDesabilitarEdicao : false;
    var idElemento = $(this).attr('id');
    var altura = (options.altura) ? options.altura : 500;

    var metodos = {
        elemento: {},
        contarCaracteres: function () {
            var body = metodos.elemento.getBody();
            var content = tinymce.trim(body.innerText || body.textContent);
            return content.length;
        },
        limitarQuantidadeDeCaracteres: function (idElemento, minchar, maxchar) {
            var countChars = metodos.contarCaracteres(idElemento);
            $("#contadorRico" + idElemento).html("Caracteres: " + countChars + "/" + maxchar);
            $("#contadorRico" + idElemento).css('color', 'black');
            if ((countChars > maxchar) || (countChars <= minchar)) {
                $("#contadorRico" + idElemento).css('color', 'red');
            }
        }
    };

    tinymce.init({
        plugins: "paste,textcolor",
        language: "pt_BR",
        paste_as_text: true,
        selector: '#' + idElemento,
        height: altura,
        toolbar: "bold,italic,underline,color,forecolor backcolor,fontsizeselect, undo, redo, removeformat",
        menubar: "",
        readonly: isDesabilitarEdicao,
        mode: "specific_textareas",
        editor_selector: "mceEditor",
        content_style: ".mce-content-body {font-size:14px;}",
        // entity_encoding : "raw",

        setup: function (ed) {
            if (isLimitarCarateres) {
                ed.on('init', function (e) {
                    metodos.elemento = tinymce.get(idElemento);
                    $("#" + idElemento).after("<div id='contadorRico" + idElemento + "'></div>");
                    metodos.limitarQuantidadeDeCaracteres(idElemento, minchar, maxchar);
                }).on('keyup', function (e) {
                    metodos.limitarQuantidadeDeCaracteres(idElemento, minchar, maxchar);
                });
            }
        }
    });

    return metodos;
};