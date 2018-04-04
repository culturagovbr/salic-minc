function abrirDinamico(elemento, obj) {
    if($(elemento).css('display') == 'none') {
        $(elemento).fadeIn('fast', function() {
	    $('img', $(obj)).attr('src','/public/img/navigation-baixo.PNG');
        });
    } else {
        $(elemento).fadeOut('fast', function() {
	    $('img', $(obj)).attr('src','/public/img/navigation-right.png');
        });
    }
}

$(document).ready(function () {
    var editorRico = $("#despacho").editorRico({
        altura: 200,
        isLimitarCarateres : true
    });
    
    $("#formEncaminhar").validate({
        rules: {
            despacho : {
                validarDespacho: true
            }
        },
        messages: {
                 despacho: "Dado obrigat&oacute;rio n&atilde;o informado"
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
    
    $.validator.addMethod("validarDespacho", function(value, element) {
        if(editorRico.contarCaracteres() > 0) {
            return true;
        }
    });
});
