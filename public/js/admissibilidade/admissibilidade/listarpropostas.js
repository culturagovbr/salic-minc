(function ($) {
    $(document).ready(function () {
        var objetoDataTable = $('#tabelaAnaliseFinal').DataTable(
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
                type: 'POST',
                data: {
                    filtro: function () {
                        return $3("#filtro").val()
                    }
                }
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
        );

        $3(".filtro-avaliacao").click(function() {
            $3("#filtro").val('');
            // if(typeof $(this).data('filtro') != 'undefined') {
                $3("#filtro").val($(this).data('filtro'));

                $3("#coluna_encaminhar").hide('fast');
                if($(this).data('filtro') == 'avaliada') {
                    $3("#coluna_encaminhar").show('fast');
                }

                objetoDataTable.ajax.reload();
            // }
        });
    });

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
            return (data.descricao_area != null && data.descricao_area != '') ? data.descricao_area : ' - ';
        }
    })
    colunas.push({
        data: null,
        render: function (data, type, row) {
            return (data.descricao_segmento != null && data.descricao_segmento != '') ? data.descricao_segmento : ' - ';
        }
    })
    colunas.push({
        data: null,
        render: function (data, type, row) {
            return (data.enquadramento != null && data.enquadramento != '') ? data.enquadramento : ' - ';
        }
    })
    colunas.push({
        data: null,
        render: function (data, type, row) {
            return '<a class="btn waves-effect waves-darrk white black-text" href="'
                + $('#base_url').val()
                + '/admissibilidade/admissibilidade/exibirpropostacultural?idPreProjeto='
                + data.idProjeto
                + '&realizar_analise=sim">'
                + '<i class="material-icons" '
                + 'title="Fazer An&aacute;lise Visual da Proposta" alt="Fazer An&aacute;lise Visual da Proposta">visibility</i></a>'
        }
    })

    return colunas
}

$3('#imprimir').on('click', function () {
    $3('#conteudoImprimir').val($3('.conteudoImprimivel').html())
    $3('#formGerarPdf').submit()
})

$3(document).ready(function () {

    $3('.modal').modal()
    $3('#id_perfil').material_select()

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
