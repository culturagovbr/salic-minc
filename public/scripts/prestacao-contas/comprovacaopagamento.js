// tratamento comum
var form = '#incluircomprovacaoAjax';

$(form).find('[data=true]').each(function () {
    $(this).keyup(function () {
        mascara(this, format_data);
        var este = this;
        setTimeout(function () {
            $(este).val($(este).val().substr(0, 10));
        }, 2);
        if ($(este).val().length == 10) {
            validaDataCorreta(este, $(este).val().substr(6, 4), $(este).val().substr(3, 2), $(este).val().substr(0, 2));
        }
    });
});

$(form).find('[sonumero=true]').keyup(function () {
    mascara(this, format_num);
});
$(form).find('[dinheiro=true]').keyup(function () {
    mascara(this, format_moeda);
});

$(form).find('[cnpjcpf=true]').each(function () {
    var este = this;
    $('.' + $(este).prop('classaux')).click(function () {
        mascaraCNPJCPF(este);
        buscarFornecedorComprovacao(este);
    });
    $('#' + $(este).prop('idDescricao')).prop('readonly', true);
});

$(form).find('[cnpjcpf=true]').keyup(function () {
    mascaraCNPJCPF(this);
    buscarFornecedorComprovacao(this);
});

$(form).find('.tpFornecedor').on("click", function () {
    mascaraCNPJCPF(document.getElementById('CNPJCPF'));
    buscarFornecedorComprovacao(document.getElementById('CNPJCPF'));
});

function mascaraCNPJCPF(este) {
    var marcado = buscarRadioMarcado(este);
    switch (marcado) {
        case 'cpf':
            $(este).val($(este).val().slice(0, 14));
            $(este).attr('maxlength', 14);
            mascara(este, format_cpf);
            break;
        case 'cnpj':
            $(este).attr('maxlength', 18);
            mascara(este, format_cnpj);
            break;
        default:
            janelaAlerta('Selecione o Tipo do Fornecedor');
            $(este).val('');
    }
}

function buscarRadioMarcado(este) {
    var marcado = '';
    $('.' + $(este).attr('classaux')).each(function () {
        if ($(this).prop('checked')) {
            marcado = $(this).val();
        }
    });
    return marcado;
}

function buscarFornecedorComprovacao(elmCNPJCPF) {
    if ($(elmCNPJCPF).attr('idAgente') != undefined && $(elmCNPJCPF).attr('idAgente') && $(elmCNPJCPF).attr('idDescricao') != undefined && $(elmCNPJCPF).attr('idDescricao')) {
        var marcado = buscarRadioMarcado(elmCNPJCPF);
        if ((marcado == 'cpf' && $(elmCNPJCPF).val().length == 14) || (marcado == 'cnpj' && $(elmCNPJCPF).val().length == 18)) {

            var fornecedor = buscarJson($3('#buscarFornecedorComprovacaoHref').val(), {cnpjcpf: $(elmCNPJCPF).val()});
            if (fornecedor.retorno) {
                $('#' + $(elmCNPJCPF).attr('idAgente')).val(fornecedor.idAgente);
                $('#' + $(elmCNPJCPF).attr('idDescricao')).val(fornecedor.descricao).attr('readonly', true);
                $('#' + $(elmCNPJCPF).attr('idDescricao')).next().addClass('active');

            } else {
                $('html').css('overflow', 'hidden');
                $("body").append("<div id='divDinamicaAgentes'></div>");
                $("#divDinamicaAgentes").html("");
                $('#divDinamicaAgentes').html("<br><br><center>Carregando dados...</center>");
                $.ajax({
                    url: '/agente/agentes/incluirfornecedor',
                    data: {
                        cpf: fornecedor.CNPJCPF,
                        caminho: "",
                        modal: "s"
                    },
                    success: function (data) {
                        if (data.error) {
                            $('#divDinamicaAgentes').html(data.msg);
                        } else {
                            $('#divDinamicaAgentes').html(data);
                        }
                    },
                    complete: function () {
                        $("#resultadoFinalizar").html("");
                    },
                    type: 'post'

                });

                $("#divDinamicaAgentes").dialog({
                    resizable: true,
                    width: $(window).width() - 100,
                    height: $(window).height() - 100,
                    modal: true,
                    autoOpen: true,
                    draggable: false,
                    title: 'Cadastrar Fornecedor',
                    buttons: {
                        'Fechar': function () {
                            $("#divDinamicaAgentes").remove();
                            $('#' + $(elmCNPJCPF).attr('idAgente')).val('');
                            $('#' + $(elmCNPJCPF).attr('idDescricao')).val('');
                            $(this).dialog('close');
                            $('html').css('overflow', 'auto');
                            outroFornecedorComprovacao(elmCNPJCPF);
                        }
                    }
                });
                $('.ui-dialog-titlebar-close').remove();
            }
        }
        else {
            $('#' + $(elmCNPJCPF).attr('idAgente')).val('');
            $('#' + $(elmCNPJCPF).attr('idDescricao')).val('');
        }
    }
    fornecedores();
}

function fornecedores() {
    var conteudo = '<option value="">Selecione</option>';
    conteudo += montagem(1);
    conteudo += montagem(2);
    conteudo += montagem(3);
    $('#fornecedor').html(conteudo);
}

function montagem(nr) {
    var conteudo = '';
    if ($('#Descricao' + nr).val() != undefined) {
        if ($('#Descricao' + nr).val().replace(/\s+/g, '')) {
            var idAgente = '';
            if ($('#idAgente' + nr).val() == '')
                idAgente = '-' + nr;
            else
                idAgente = $('#idAgente' + nr).val();
            conteudo += '<option value="' + idAgente + '">' + $('#Descricao' + nr).val() + '</option>';
            $('.fornecedor' + nr).each(function () {
                $(this).val(idAgente);
            });
            $('.td_fornecedor' + nr).each(function () {
                $(this).html($('#Descricao' + nr).val());
            });
        }
    }
    return conteudo;
}

function buscarJson(pagina, dados) {
    var retorno = '';
    var select = requisicaoAjaxObj();
    select.executar({
        pagina: pagina,
        parametros: dados,
        resposta: undefined,
        async: false,
        funcaoRetorno: function (resposta) {
            retorno = resposta;
        }
        , dataType: 'json'
    });
    return retorno;
}

function requisicaoAjaxObj() {
    var ajaxObj = {
        pagina: '',
        parametros: {},
        type: 'post',
        dataType: '',
        resposta: '#conteudo',
        async: true,
        funcaoRetorno: function (resposta) {
            $(this.resposta).html(resposta);
        },
        executar: function (dados) {
            this.refineParametrosObj(dados);
            var esteObj = this;
            if (this.resposta != undefined && this.resposta != '')
                $(this.resposta).html('<img src="/public/img/ajax.gif" alt="carregando"><br/><br/>Carregando...<br>Por Favor, aguarde!!');
            $.ajax({
                type: esteObj.type,
                url: esteObj.pagina,
                data: esteObj.parametros,
                async: esteObj.async,
                success: function (resp) {
                    esteObj.funcaoRetorno(resp);
                }
                , dataType: esteObj.dataType
            });
        },
        refineParametrosObj: function (data) {
            if (data != undefined)
                for (var j in data) {

                    this[j] = data[j];
                }
        }
    };
    return ajaxObj;
}

// quando selecionar cheque, limita n&uacute;mero de caracteres do nrDocumentoDePagamento
$('#tpFormaDePagamento').change(function () {
    if ($(this).val() == 1) {
        $('#nrDocumentoDePagamento').attr('maxlength', 6);
    } else {
        $('#nrDocumentoDePagamento').attr('maxlength', 10)
    }
});

$('#itens').change(function () {
    // Recupera o fornecedor e preenche os dados do mesmo.
    $.ajax({
        url: '/fornecedor/pesquisar-fornecedor-item/format/json',
        type: 'get',
        data: {'item': $(this).val()},
        dataType: 'json',
        success: function (result) {
            if (undefined != result.fornecedor) {
                if (11 == result.fornecedor.CNPJCPF.length) {
                    var mask = '999.999.999-99';
                } else {
                    var mask = '99.999.999/9999-99';
                }
                $("#tabelaComprovante tbody:nth-child(1) tr:nth-child(2)").attr('style', 'display:none');
                $("#idAgente").val(result.fornecedor.idAgente);
                $("#CNPJCPF").val(result.fornecedor.CNPJCPF).mask(mask).attr('disabled', 'true');
                $("#Descricao").val(result.fornecedor.Fornecedor).attr('disabled', 'true');
            }
        }
    });
});

$3("#frComprovarPagamentoSubmit").on('click', function (e) {
    e.preventDefault();
    var msg = '';
    if (isInvoice()) {
        msg = validarParametrosDeComprovacaoDePagamentoInvoice();
    } else {
        msg = validarParametrosDeComprovacaoDePagamento();
    }

    if (isInvoice()) {
        valorComprovado = $('#vlComprovadoInternacional').val();
    } else {
        valorComprovado = $('#vlComprovado').val();
    }

    var valorItem = parseFloat(valorComprovado.replace(".", "").replace(".", "").replace(",", "."));
    var valorAprovado = parseFloat($("#valorAprovado").val());
    var valorComprovado = parseFloat($("#totalComprovado").val());
    var valorComprovadoAntigo = parseFloat($('#vlComprovadoAntigo').val()) ? parseFloat($('#vlComprovadoAntigo').val()) : 0 ;
    var valorAtualComprovado = (valorItem + valorComprovado) - (valorComprovadoAntigo);
    valorAtualComprovado = parseFloat((valorAtualComprovado).toFixed(2)) ;

    if (valorAtualComprovado > valorAprovado) {
        msg += 'O valor comprovado total com esse item ('+ valorAtualComprovado.toFixed(2) +') maior que o valor aprovado('+valorAprovado.toFixed(2)+') passando em: ' + (valorAtualComprovado-valorAprovado).toFixed(2) + '<br />';
    } else if (valorItem > valorAprovado) {
        msg += 'Valor comprovado maior que o valor aprovado.<br />';
    }

    if ('' != msg) {
        $("#msgAlerta").dialog("destroy");
        $("#msgAlerta").html('<center><p>' + msg + '</p></center>');
        $("#msgAlerta").dialog({
            resizable: false,
            title: 'Alerta!',
            width: 500,
            height: 300,
            modal: true,
            buttons: {
                'OK': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('.ui-dialog-titlebar-close').remove();
        return false;
    }

    if ('' == msg) {
        $3('#frComprovarPagamento').submit();
    }
});

function validarParametrosDeComprovacaoDePagamento() {
    var msg = '';
    if (!$("#vlComprovado").val()) {
        msg += 'Informe o valor do item.<br />';
    }
    if (parseFloat($("#vlComprovado").val()) === 0) {
        msg += 'O valor do item n&atilde;o pode ser igual a zero ou menor que R$ 1,00 .<br />';
    }
    if (!$("#idAgente").val()) {
        msg += 'Informe um fornecedor.<br />';
    }
    if (!$("#tpDocumento").val() || 0 == $("#tpDocumento").val()) {
        msg += 'Selecione o tipo de comprovante.<br />';
    }
    if (!$("#nrComprovante").val()) {
        msg += 'Informe o n&uacute;mero do comprovante.<br />';
    }
    if (!$("#dtEmissao").val()) {
        msg += 'Informe a data de emiss&atilde;o do comprovante de despesa.<br />';
    }
    if (!$("#tpFormaDePagamento").val() || 0 == $("#tpFormaDePagamento").val()) {
        msg += 'Selecione a forma de pagamento.<br />';
    }
    if (!$("#nrDocumentoDePagamento").val()) {
        msg += 'Informe o n&uacute;mero do Documento de pagamento.<br />';
    }
    if (!$("#dtPagamento").val()) {
        msg += 'Informe o n&uacute;mero do Data de pagamento.<br />';
    }

    if ($("#idComprovantePagamento").val()) {
        if (!$("#arquivo_edit").val()) {
            msg += 'Selecione um arquivo para editar comprovante para envio .<br />';
        }
    } else {
        if (!$("#arquivo").val()) {
            msg += 'Selecione um arquivo comprovante para envio.<br />';
        }
    }
    return msg;
}

function validarParametrosDeComprovacaoDePagamentoInvoice() {
    var msg = '';
    if (!$("#vlComprovadoInternacional").val()) {
        msg += 'Informe o valor do item.<br />';
    }
    if (!$("#nomeRazaoSocialInternacional").val()) {
        msg += 'Informe o Nome/Raz&atilde;o Social.<br />';
    }
    if (!$("#nif").val()) {
        msg += 'Informe o NIF.<br />';
    }
    if (!$("#dtEmissaoInternacional").val()) {
        msg += 'Informe a data de emiss&atilde;o do comprovante.<br />';
    }
    if ($("#arquivoInternacional").val() == '') {
        if (!$("#arquivo_edit").val()) {
            msg += 'Selecione um arquivo comprovante para envio.<br />';
        }
    } else {
        if (!$("#arquivoInternacional").val()) {
            msg += 'Selecione um arquivo comprovante para envio.<br />';
        }
    }
    return msg;
}

$('#tabelaComprovantePagamento tbody tr td a.exclusao').closest('a').each(function () {
    $(this).unbind('click').click(function (event) {
        event.preventDefault();
        $("#alerta").dialog("destroy");
        $("#alerta").html('Deseja realmente excluir esse comprovante?');
        $("#alerta").dialog({
            resizable: false,
            title: 'Alerta!',
            width: 340,
            modal: true,
            buttons: {
                'OK': function () {
                    deletarItem($(event.target).parent());
                    $(this).dialog('close');
                    location.reload();
                    sleep(2000);
                },
                'Cancelar': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('.ui-dialog-titlebar-close').remove();
    });
});

deletarItem = function (elementLink) {
    $.ajax({
        url: elementLink.attr('href') + '/format/json',
        dataType: 'json',
        success: function (result) {
            // remove a linha
            elementLink.closest('tr').remove();
            // atualiza as informacoes do item
            /* $("campoValorAprovado").html('000').toNumber().formatCurrency({region: 'pt-BR'}); */
            /* $("#campoValorComprovado").html('000').toNumber().formatCurrency({region: 'pt-BR'}); */
            // Recupera o item e preenche os valores aprovado e comprovado.
            $.ajax({
                url: '/planilha-item/pesquisar/format/json',
                type: 'get',
                data: {'item': planilhaItem},
                dataType: 'json',
                success: function (result) {
                    if (result.item) {
                        planilhaItem = result.item;
                        $("campoValorAprovado").html(result.item.valorAprovado).toNumber().formatCurrency({region: 'pt-BR'});
                        $("campoValorComprovado").html(result.item.valorComprovado).toNumber().formatCurrency({region: 'pt-BR'});
                    }
                    message('Exclus&atilde;o realizada com sucesso!', 'CONFIRM');
                    location.reload();
                }
            });
        }
    });
};

function toggleTabelaComprovacaoPagamentoInvoice() {
    if (isInvoice()) {
        $('#bodyTabelaComprovante').hide();
        $('#fornecedor_brasil').hide();
        $('#bodyTabelaComprovanteInternacional').show();
    } else {
        $('#bodyTabelaComprovante').show();
        $('#fornecedor_brasil').show();
        $('#bodyTabelaComprovanteInternacional').hide();
    }
}

function isInvoice() {
    return 'Brasil' !== $('#pais').val();
}

toggleTabelaComprovacaoPagamentoInvoice();
if (!isInvoice()) {
    var cnpjcpf = document.getElementById('CNPJCPF');
    mascaraCNPJCPF(cnpjcpf);
}

//Mensagem para Utilizar este fornecedor para Comprova¿¿o Financeira, sim ou n¿o.
function outroFornecedorComprovacao(este) {
    $("#divPerguntaFornecedor").dialog('close');
    $("#divPerguntaFornecedor").html('Deseja utilizar este fornecedor na comprova&ccedil;&atilde;o do pagamento?');
    $("#divPerguntaFornecedor").dialog('open');
    $("#divPerguntaFornecedor").dialog({
        resizable: false,
        width: 320,
        height: 180,
        modal: true,
        draggable: false,
        title: 'Alerta!',
        buttons: {
            'N\xE2o': function () {
                $('#' + $(este).attr('idAgente')).val('');
                $('#' + $(este).attr('idDescricao')).val('');
                $('#CNPJCPF').val('');
                $("#divPerguntaFornecedor").dialog('close');
            },
            'Sim': function () {
                $(this).dialog('close');
                buscarFornecedorComprovacao(este);
            }
        }
    });
    $('.ui-dialog-titlebar-close').remove();
}

function mostrarFormulario() {

    var elmForm = $('#formularioContainer');
    var elmBotao = $("#botaoMostrarFormulario");

    elmForm.slideToggle('fast');

    if(elmForm.is(":visible")) {
        elmBotao.hide();
        $3("html, body").animate({
            scrollTop: $3(elmForm).offset().top
        }, 600);
    }
}

$(document).ready(function () {
    console.log($3('#tpFornecedorCpf :checked').val());

    // cadastrar agente callback event heandler
    $(document).bind('agenteJaCadastrado', function (response) {
        let agente = response.detail;
        let elementCNPJCPF = $("#frComprovarPagamento #CNPJCPF");
        elementCNPJCPF.val(agente.cnpjcpf);
        $(elementCNPJCPF.attr('idDescricao')).next().addClass('active');
        mascaraCNPJCPF(document.getElementById('CNPJCPF'));
    });
});
