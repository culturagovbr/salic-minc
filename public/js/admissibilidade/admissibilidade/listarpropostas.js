(function ($) {
    $(document).ready(function () {
        $('#tabelaAnaliseFinal').DataTable(
            {
                'language': {
                    'url': 'https://cdn.datatables.net/plug-ins/1.10.12/i18n/Portuguese-Brasil.json'
                }
                , 'order': [0, 1]
                , 'searching': true
                , 'lengthChange': true
                , columnDefs: [
                {
                    targets: [0, 1],
                    //className: 'mdl-data-table__cell--non-numeric'
                }
            ]
                ,
                'aoColumnDefs': [
                    {
                        'bSortable': false,
                        'aTargets': function () {
                            if ($('#perfil_atual').val() != $('#perfil_componente_comissao').val()) {
                                return [4, 6, 7]
                            }
                        }
                    }
                ]
                , 'ajax': {
                url: '/admissibilidade/admissibilidade/listar-propostas-ajax',
                type: 'POST'
            },
                'processing': true,
                'serverSide': true,
                'createdRow': function (row, data, index) {
                    if (data.CodSituacao == $('#PROPOSTA_EM_ANALISE_FINAL').val()) {
                        $(row).addClass('green lighten-5')
                    }
                },
                'columns': obterColunasListagem()
            }
        )

    })
}($.noConflict(true)))

function obterColunasListagem () {
    var colunas = []
    colunas.push({
        data: null,
        name: 'idProjeto',
        render: function (data, type, row) {
            return '<a class="waves-effect waves-dark btn white black-text" href="/admissibilidade/admissibilidade/exibirpropostacultural?idPreProjeto=' + data.idProjeto + '">'
                + data.idProjeto + ' </a>'
        }
    })
    colunas.push({
        'name': 'NomeProposta',
        'data': 'NomeProposta'

    })
    colunas.push({
        'name': 'DtMovimentacao',
        'data': 'DtMovimentacao'
    })
    colunas.push({
        'name': 'diasCorridos',
        'data': 'diasCorridos'
    })
    if ($('#perfil_atual').val() != $('#perfil_componente_comissao').val()) {
        colunas.push({
            data: null,
            render: function (data, type, row) {
                return '<a class="btn waves-effect waves-darrk white black-text" href="' + $('#base_url').val() + '/admissibilidade/admissibilidade/proposta-por-proponente?agente=' + data.idAgente + '">'
                    + '<i class="material-icons"'
                    + 'title="Vizualizar outras Propostas deste Proponente" alt="Vizualizar outras Propostas deste Proponente">search</i> </a>'

            }
        })
        colunas.push({
            'name': 'Tecnico',
            'data': 'Tecnico'
        })
        colunas.push({
            data: null,
            render: function (data, type, row) {
                if (data.CodSituacao == $('#PROPOSTA_EM_ANALISE_FINAL').val()) {
                    return '<i class="material-icons">done</i>' +
                        ''
                }
                return ''
            }
        })
    } else {
        colunas.push({
            data: null,
            render: function (data, type, row) {
                var diasRestantes = 5 - parseInt(data.dias_corridos_distribuicao,10);
                if (diasRestantes <= 0) {
                    return '<i class="material-icons">close</i>' +
                        ''
                }
                return diasRestantes;
            }
        })
    }
    colunas.push({
        data: null,
        render: function (data, type, row) {
            if (data.isEnquadrada == true) {
                return '<i class="material-icons">done</i>' +
                    ''
            }
            return ''
        }
    })
    colunas.push({
        data: null,
        render: function (data, type, row) {
            return '<a class="btn waves-effect waves-darrk white black-text" href="' + $('#base_url').val() + '/admissibilidade/admissibilidade/exibirpropostacultural?idPreProjeto=' + data.idProjeto + '&realizar_analise=sim">'
                + '<i class="material-icons" '
                + 'title="Fazer An&aacute;lise Visual da Proposta" alt="Fazer An&aacute;lise Visual da Proposta">visibility</i></a>'
        }
    })

    if ($('#liberar_encaminhamento').val() == 'sim') {
        colunas.push({
            data: null,
            render: function (data, type, row) {
                if (permitirEncaminhamento(data)) {
                    return '<a class="waves-effect waves-light btn modal-trigger" data-id_preprojeto="' + data.idProjeto + '" href="#dialogEncaminharProposta">'
                        + '<i class="material-icons" '
                        + ' title="Encaminhar An&aacute;lise de Proposta" '
                        + ' alt="Encaminhar An&aacute;lise de Proposta">forward</i></button>'
                        + ' </a>'
                }

                return ''
            }
        })
    }

    return colunas
}

function permitirEncaminhamentoTecnicoAdmissibilidade (data) {
    if (parseInt(data.quantidade_distribuicoes, 10) == 0
        && $('#perfil_atual').val() == $('#perfil_tecnico_admissibilidade').val()) {

        return true
    }
}

function permitirEncaminhamentoCoordenadorAdmissibilidade (data) {
    if ((
            $('#perfil_atual').val() == $('#perfil_coordenador_admissibilidade').val()
            && data.id_area != null
            && data.isEnquadrada == true
        )
        ||
        (
            parseInt(data.quantidade_distribuicoes, 10) > 0
            && parseInt(data.avaliacao_atual, 10) == 1
            && $('#perfil_atual').val() != $('#perfil_coordenador_admissibilidade').val()
            && data.isEnquadrada == true
        )) {
        return true
    }
}

function permitirEncaminhamentoComponenteComissao (data) {
    if (parseInt(data.quantidade_distribuicoes, 10) > 0
        && parseInt(data.avaliacao_atual, 10) == 1
        && $('#perfil_atual').val() == $('#perfil_componente_comissao').val()
        && data.id_area != null
        && data.isEnquadrada == true
    ) {
        return true
    }
}

function permitirEncaminhamento (data) {
    if (data.CodSituacao != $('#PROPOSTA_EM_ANALISE_FINAL').val()) {
        return false
    }
    if (
        !permitirEncaminhamentoTecnicoAdmissibilidade(data)
        && !permitirEncaminhamentoCoordenadorAdmissibilidade(data)
        && !permitirEncaminhamentoComponenteComissao(data)
    ) {

        return false
    }
    return true

}

$3('#imprimir').on('click', function () {
    $3('#conteudoImprimir').val($3('.conteudoImprimivel').html())
    $3('#formGerarPdf').submit()
})

$3(document).ready(function () {

    $3('.modal').modal()
    $3('#id_perfil').material_select()
    $3('#dialogEncaminharProposta').modal(
        {
            dismissible: true,
            opacity: .5,
            inDuration: 300,
            outDuration: 200,
            startingTop: '4%',
            endingTop: '10%',
            ready: function (modal, trigger) {
                $3('#id_preprojeto').val($3(trigger).data('id_preprojeto'))
                $3('#span_id_preprojeto').html($3(trigger).data('id_preprojeto'))
            },
            complete: function () {
            }
        }
    )

    $3('#botaoEnviarAvaliacaoProposta').click(function () {
        var parametros = {}
        $3('#dialogEncaminharProposta form').serializeArray().map(function (x) {
            parametros[x.name] = x.value
        })

        $3('#botaoEnviarAvaliacaoProposta').prop('disabled', true)
        $3.ajax({
            type: 'POST',
            url: $('#dialogEncaminharProposta form').attr('action'),
            data: parametros,
            success: function (data) {
                var callback = function () {}
                if (data.resposta) {
                    callback = function () {
                        window.location.reload()
                    }
                    $3('#dialogEncaminharProposta').modal('close')
                }
                Materialize.toast(data.mensagem, 2000, '', callback)
                $3('#botaoEnviarAvaliacaoProposta').prop('disabled', false)
            }
        })
    })
})

$('#tabelaAnaliseFinal').ready(function () {

    $3('#modalTransformarProposta').modal()
    $3('tbody').on('click', '.transformarPropostaEmProjeto', function () {
        $3('#idPreProjeto').html($3(this).data('id-pre-projeto'))
        $3('#modalTransformarProposta').modal('open')
    })

    $('#botaoTransformarPropostaEmProjeto').click(function () {
        var idPreProjeto = parseInt($('#idPreProjeto').html(), 10)

        Materialize.toast('Gerando PRONAC...', 2000, '', function () {
            $.post(
                $('#base_url').val() +
                '/admissibilidade/admissibilidade/transformar-proposta-em-projeto?idPreProjeto='
                + idPreProjeto,
                {},
                function (data) {
                    Materialize.toast('PRONAC gerado com sucesso.', 2000, '', function () {
                        window.location = ''
                    })
                }
            )
        })
    })
})
