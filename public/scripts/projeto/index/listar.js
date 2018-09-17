$(document).ready(function () {

    $3('.tooltipped').tooltip({delay: 50});

    function clonar_projeto(idPronac) {

        if (!idPronac) {
            return false;
        }

        $('#container-loading').fadeIn();
        $3('#loading-message').html('Clonando projeto...');

        $3.ajax({
            type: 'POST',
            url: "/projeto/index/clonar-projeto-ajax",
            data: {
                idPronac: idPronac
            },
            success: function (data) {

                $('#container-loading').fadeOut();
                $3('#loading-message').html('carregando...');
                if (data.status == true) {
                    var url = "/proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/" + data.idPreProjeto;

                    Materialize.toast(data.msg, 4000, 'green darken-1 white-text',
                        function () {
                            window.location = url
                        })
                    ;
                } else {
                    Materialize.toast(data.msg, 8000, 'red darken-1 white-text');
                }

            },
            dataType: 'json'
        });
    }

    $("#lista-de-projetos tbody").on('click', '.clonar-projeto', function () {

        var idPronac = $(this).attr('id-pronac');
        var pronac = $(this).attr('pronac');
        var titulo_projeto = $(this).attr('titulo');
        var el = '#mensagem-modal';

        titulo = 'Clonar Projeto';
        largura = 400;
        altura = 220;

        $(el).html('Tem certeza que deseja clonar o projeto <br>(' + pronac + ') ' + titulo_projeto + '?');

        $(el).dialog("destroy");

        $(el).dialog
        ({
            modal: true,
            resizable: false,
            width: largura,
            height: altura,
            title: titulo,
            buttons: {
                "N\u00e3o": function () {
                    $(this).dialog("close");
                    return false;
                },
                "Sim": function () {
                    $(this).dialog("close");
                    clonar_projeto(idPronac);
                }
            }
        });
    });
});

(function ($) {

    $(document).ready(function () {

        $(document).ajaxStart(function () {
            $('#container-loading').fadeIn();
        });

        $(document).ajaxComplete(function () {
            $('#container-loading').fadeOut();
        });

        var table = $('#lista-de-projetos').DataTable(
            {
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.12/i18n/Portuguese-Brasil.json"
                },
                "lengthChange": true,
                "ajax": {
                    url: "/projeto/index/listar-projetos-ajax",
                    data: {
                        "id": function (d) {
                            return $('#idProponente').val();
                        },
                        "mecanismo": function (d) {
                            return $('#mecanismo').val();
                        }
                    },
                    type: "POST"
                },
                "processing": true,
                "serverSide": true,
                "bFilter": false,
                "aoColumnDefs": [
                    {'bSortable': false, 'aTargets': [3, 4, 5]}
                ],
                "order": [[0, 'desc'], [1, 'asc']],
                "columns": [
                    {
                        data: null,
                        "name": "pronac",
                        render: function (data, type, row) {
                            let url = '/projeto/#/' + data.idPronacHash;

                            return '<a target="_blanck" class="btn waves-effect waves-darrk white black-text small" href="' + url + '">'
                                + data.pronac + '</a>'
                        }
                    },
                    {
                        "name": "nomeprojeto",
                        "data": "nomeprojeto"
                    },
                    {
                        data: null,
                        "name": "situacao",
                        render: function (data, type, row) {
                                if(data.emAnaliseCnic === true) {
                                    return '<div style="background-color: #EF5350" class="darken-2 padding10 white-text">\n' +
                                        ' Projeto em an&aacute;lise pela Comiss&atilde;o Nacional\n' +
                                        'de Incentivo &agrave; Cultura-CNIC. Aguardar resultado da avalia&ccedil;&atilde;o.\n' +
                                        '</div>';
                                }

                            return data.situacao;
                        }
                    },
                    {
                        "name": "periodo",
                        "data": "periodo"

                    },
                    {
                        "name": "mecanismo",
                        "data": "mecanismo"

                    },
                    {
                        data: null,
                        "name": "actions",
                        render: function (data, type, row) {
                            let botaoClonar = '', botaoAdequar = '';

                            if (data.podeClonarProjeto && data.idMecanismo == 1) {
                                botaoClonar = '<li>'
                                    + '<a class="clonar-projeto btn btn-floating btn-primary" '
                                    + 'title="Clonar Projeto" '
                                    + 'href="javascript:void(0)" '
                                    + 'id-pronac="' + data.idPronac + '"'
                                    + 'pronac="' + data.pronac + '"'
                                    + 'titulo="' + data.nomeprojeto + '">'
                                    + '<i class="material-icons">content_copy</i>'
                                    + '</a>'
                                    + '</li>';
                            }

                            if (data.podeAdequarProjeto && data.idMecanismo == 1) {
                                botaoAdequar = '<li>'
                                    + '<a class="btn btn-floating waves-effect waves-light tooltipped btn-default" '
                                    + 'title="Ir para solicita&ccedil;&otilde;es" '
                                    + 'href="/proposta/manterpropostaincentivofiscal/identificacaodaproposta/idPreProjeto/' + data.idProjeto + '">'
                                    + '<i class="material-icons">edit</i>'
                                    + '</a>'
                                    + '</li>';
                            }

                            return '<div style="position: relative; height: 60px;">'
                                + '<div class="fixed-action-btn horizontal" style="position: absolute; top: 0px; right: 0; z-index: 996; padding: 15px 0 0 15px;">'
                                + '<a class="btn btn-primary small">'
                                + '<i class="material-icons">more_vert</i></a>'
                                + '<ul>'
                                + '<li>'
                                + '<a class="btn-floating red" title="Ir para solicita&ccedil;&otilde;es" href="/solicitacao/mensagem/index/idPronac/' + data.idPronac + '">'
                                + '<i class="material-icons">message</i>'
                                + '</a>'
                                + '</li>'
                                + botaoClonar
                                + botaoAdequar
                                + '</ul>'
                                + '</div></div>'
                        }
                    }
                ]
            }
        );

        $("#idProponente, #mecanismo").change(function () {
            let idMecanismo = parseInt($("#mecanismo").val());

            var column = table.column(3);
            column.visible(true);
            if (idMecanismo != 1) {
                column.visible(false);
            }

            table.ajax.reload();
        });

    });
}($.noConflict(true)));
