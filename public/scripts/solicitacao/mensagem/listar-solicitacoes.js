(function ($) {
    $(document).ready(function () {
        $('#listar_solicitacoes').DataTable(
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
                    url: "/solicitacao/mensagem/listar-solicitacoes-ajax",
                    data: {
                        "idPronac": function (d) {
                            return $('input#idPronac').val();
                        },
                        "idPreProjeto": function (d) {
                            return $('input#idPreProjeto').val();
                        },
                        "listarTudo": function (d) {
                            return $('input#listarTudo').val();
                        }
                    },
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
}($.noConflict(true)));

function obterColunasListagem() {
    var colunas = [];

    var urlVisualizarSolicitacao = '/solicitacao/mensagem/visualizar/';

    colunas.push({
        data: null,
        name: 'idProjeto',
        render: function (data, type, row) {

            let href="", id = data.idProjeto, projeto = data.idProjeto;

            if(data.idPronac) {
                href = '/default/consultardadosprojeto/index/idPronac/' + data.idPronacHash;
                id = data.idPronac;
                projeto = data.Pronac;
            }

            return '<a class="waves-effect waves-dark btn white black-text" ' +
                'href="' + href + '"' +
                'idPreProjeto=' + id + '">' + projeto + '</a>'
        }
    });

    colunas.push({
        'name': 'NomeProjeto',
        'data': 'NomeProjeto'

    });

    colunas.push({
        'name': 'dsSolicitacao',
        'data': 'dsSolicitacao'
    });

    colunas.push({
        'name': 'dsEncaminhamento',
        'data': 'dsEncaminhamento'
    });

    colunas.push({
        'name': 'dtSolicitacao',
        'data': 'dtSolicitacao'
    });

    colunas.push({
        data: null,
        name: 'idProjeto',
        render: function (data, type, row) {

            let params = "/idPreProjeto/" + data.idProjeto;
            let action = 'visualizar';
            let label = "Visualizar solicita&ccedil;&atilde;o";
            let url = '/solicitacao/mensagem/' + action + '/id/' + data.idSolicitacao + params;

            if(data.idPronac) {
                params = "/idPronac/" + data.idPronac;
                id = data.idPronac;
                projeto = data.Pronac;
            }

            if(data.siEncaminhamento == 12) {
                action = 'solicitar'
            }

            return '<a ' +
                'class="btn blue small white-text tooltipped" ' +
                'data-tooltip="'+ label + '"' +
                'href="/solicitacao/mensagem/' + action + '/id/' + data.idSolicitacao + params +'">' +
                '<i class="material-icons">visibility</i>' +
                '</a>'
        }
    });
    return colunas
}

