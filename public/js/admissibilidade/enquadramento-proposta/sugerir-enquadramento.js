function carregarSegmento () {
    $3('#id_segmento').html('<option value=""> - Carregando - </option>')
    $3.ajax({
        type: 'POST',
        url: $('#action_segmento').val(),
        data: {
            id: $3('#id_area').val()
        },
        success: function (dados) {
            $3('#id_segmento').find('option').remove()
            $3('#id_segmento').append(dados)
        }
    })
}

function carregarEnquadramento (object) {
    $3('#bloco-Artigo').show()
    $3('#enquadramentoText').html('Artigo 26')
    var enquadramentoProjeto = $3(object).children('option:selected').data('tp_enquadramento')

    if (enquadramentoProjeto == '2') {
        $3('#enquadramentoText').html('Artigo 18')
    }
    $3('#enquadramento_preprojeto').val(enquadramentoProjeto)
}

jQuery(function ($) {

    var limiteMaximo = 8000

    var editorRico = $('#descricao_motivacao').editorRico({
        altura: 200,
        isLimitarCarateres: true,
        maxchar: limiteMaximo
    })

    // $('#botaoSugestoesEnquadramento').click(function () {
    //     $("#dialog-sugestoes-enquadramento").dialog({
    //         title :'Hist&oacute;rico de Sugest&otilde;es de Enquadramento.',
    //         resizable: false,
    //         width:'95%',
    //         height: 450,
    //         modal: true,
    //         autoOpen:false
    //     });
    //     $("#dialog-sugestoes-enquadramento").dialog('open');
    // });
    $3('.modal').modal({
        dismissible: true,
        opacity: .5,
        inDuration: 300,
        outDuration: 200,
        startingTop: '4%',
        endingTop: '10%',
        width: '100%'
    })

    $3('#id_area').change(function () {
        $3('#bloco-Artigo').hide()
        carregarSegmento()
    })

    $3('#id_segmento').change(function () {
        carregarEnquadramento(this)
    })

    $('#formEnquadramentoProjeto').validate({
        rules: {
            descricao_motivacao: {
                validarPreenchimento: true,
                validarPreenchimentoMaximo: true
            }
        },
        messages: {
            descricao_motivacao: {
                validarPreenchimento: 'Dado obrigat&oacute;rio n&atilde;o informado',
                validarPreenchimentoMaximo: 'limite excedido'
            }
        },

        submitHandler: function (form) {
            $('#container-progress').show()
            form.submit()
        },
        invalidHandler: function (event, validator) {
            Materialize.toast(validator.submitted.descricao_motivacao, 4000)
        }
    })

    $.validator.addMethod('validarPreenchimento', function (value, element) {
        if (editorRico.contarCaracteres() > 0
            && $3('#id_area').val() != ""
            && $3('#id_segmento').val()) {
            return true
        }
    })
    $.validator.addMethod('validarPreenchimentoMaximo', function (value, element) {
        if (editorRico.contarCaracteres() <= limiteMaximo) {
            return true
        }
    })
})