<?php
/**
 * Menu Lateral
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 30/03/2012
 * @version 1.0
 * @package application
 * @subpackage application.views.scripts.readequacao.inc
 * @copyright � 2012 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */

// define os labels dos menus
$menu[1][0] = 'Proponente';
$menu[2][0] = 'Projetos';
$menu[3][0] = 'Custo';

// define os labels dos sub-menus do menu Projetos
$menu[2][1] = 'Produtos';
$menu[2][2] = 'Ficha T&eacute;cnica';
$menu[2][3] = 'Local de Realiza&ccedil;&atilde;o';
$menu[2][4] = 'Nome do Projeto';
$menu[2][5] = 'Prorrogar Prazos de Execu&ccedil;&atilde;o';
$menu[2][6] = 'Prorrogar Prazos de Capta&ccedil;&atilde;o';
$menu[2][7] = 'Proposta Pedag&oacute;gica';

// define os labels dos sub-menus do menu Custo
$menu[3][1] = 'Custo por Produtos';
$menu[3][2] = 'Custo Administrativo';


// define o m�dulo e todas as actions utilizadas no menu
$controle = 'readequacao';
$acao[0]  = 'proponente';
$acao[1]  = 'produtos';
$acao[2]  = 'ficha-tecnica';
$acao[3]  = 'local-realizacao';
$acao[4]  = 'nome-projeto';
$acao[5]  = 'prazo-execucao';
$acao[6]  = 'prazo-captacao';
$acao[7]  = 'proposta-pedagogica';
$acao[8]  = 'custo-produtos';
$acao[9]  = 'custo';


// marca o menu como ativo
for ($i = 0; $i <= 9; $i++) :
    $mAtivo[$i]  = 'no_seta'; // define se o menu estar� ativo ou n�o
    $onClick[$i] = 'onclick="carregandoModal();"'; // simulador do loader
    
//    if ($this->pagina == $acao[$i]) : // evita carregar o menu ativo novamente
//        $mAtivo[$i] .= '_hover';
//        $onClick[$i] = 'onclick="return false;"';
//    endif;
endfor;


// controla a exibi��o dos sub-menus de Projetos
if ($this->pagina == $acao[1] || $this->pagina == $acao[2] || $this->pagina == $acao[3] || $this->pagina == $acao[4] || $this->pagina == $acao[5] || $this->pagina == $acao[6] || $this->pagina == $acao[7]) :
    $sumirProjetos = '';
else :
    $sumirProjetos = ' sumir';
endif;


// controla a exibi��o dos sub-menus de Custo
if ($this->pagina == $acao[8] || $this->pagina == $acao[9]) :
    $sumirCusto = '';
else :
    $sumirCusto = ' sumir';
endif;
?>



<script type="text/javascript">
/**
 * Fun��o que ajusta o layout para acoplar o menu lateral
 */
    function layout_fluido() {
	var janela         = $(window).width();
	var fluidNavGlobal = janela - 245;
	var fluidConteudo  = janela - 253;
	var fluidTitulo    = janela - 252;
	var fluidRodape    = janela - 19;
//	$("#navglobal").css("width", fluidNavGlobal);
//	$("#conteudo").css("width", fluidConteudo);
//	$("#titulo").css("width", fluidTitulo);
//	$("#rodapeConteudo").css("width", fluidConteudo);
//	$("#imagemRodape").css("width", fluidConteudo);
//	$("#rodape").css("width", fluidRodape);
//	$("#conteudo").css("min-height", $('#menuContexto').height()); // altura minima do conteudo
//	$("#rodapeConteudo").css("margin-left", "225px");
//	$(".sanfonaDiv").css("clear", "both");
//	$(".sanfonaDiv").css("width", "91%");
    } // fecha fun��o layout_fluido()


    /**
     * Fun��o para excluir os arquivos das solicita��es de readequa��o
     */
    function excluir_arq_readeq(idPedidoAlteracao, idArquivo, nmArquivo) {
	$('#modal-excluir-arquivo').dialog('destroy');
	$('#modal-excluir-arquivo').dialog({
            modal: true,
            resizable: false,
            width: 360,
            height: 180,
            title: 'Confirma��o!',
            buttons: {
                N�o: function() {
                    fecharModal('modal-excluir-arquivo');
                },
                'Sim': function() {
                    dados = 'idPedidoAlteracao=' + encodeURIComponent(idPedidoAlteracao);
                    dados+= '&idArquivo=' + encodeURIComponent(idArquivo);
                    dados+= '&nmArquivo=' + encodeURIComponent(nmArquivo);

                        $.ajax({
                            type: 'POST',
                            data: dados,
                            url: '<?php echo $this->url(array('controller' => 'readequacao', 'action' => 'excluir-arquivo')); ?>',
                            dataType: 'html',
                            beforeSend: function() {
                                fecharModal('modal-excluir-arquivo');
                                carregandoModal();
                            },
                            complete: function() {
                                fecharModal('carregandoModalAjax');
                                $('#excluir_arq'+idPedidoAlteracao+''+idArquivo).remove();
                            },
                            success: function(html){
                                $('body').append(html);
                                $('#modal-arquivo-excluido').dialog({
                                    modal: true,
                                    resizable: false,
                                    width: 420,
                                    height: 170,
                                    title: 'Alerta!'
                                });
                                $('.ui-dialog-titlebar-close').remove();
                                var fecharModalArquivo = window.setInterval(
                                    function(){
                                        fecharModal('modal-arquivo-excluido');
                                        clearInterval(fecharModalArquivo);
                                        $('#modal-arquivo-excluido').remove();
                                    }, 3000
                                );
                            }
                        });
                    }
		}
	});
    } // fecha fun��o excluir_arq_readeq()


    /**
     * Fun��o respons�vel por finalizar o envio da solicita��o de readequa��o
     */
    function enviar_solicitacao(stPedidoAlteracao) {
	$('#finalizarPedido').attr('value', stPedidoAlteracao);
	fecharModal('msgConfirm1');
	carregandoModal();
	$('#enviar_solicitacao').submit();
    } // fecha fun��o enviar_solicitacao()


    /**
     * A��es que ser�o executadas ap�s o carregamento da p�gina
     */
    $(document).ready(function() {
	// ajustes do menu
	$('a.sanfona').click(function() {
            $(this).next().toggle('fast');
	});

	// finaliza o envio da solicita��o de readequa��o
	$('#menu_enviar_readequacao').click(function() {
            $(this).dialog('close');
            $('#msgConfirm1').dialog ({
                modal: true,
                resizable: false,
                draggable: false,
                closeOnEscape: false,
                title: 'Alerta!',
                width: 320,
                buttons: {
                    N�o: function() {
                        enviar_solicitacao('T');
                    },
                    'Sim': function() {
                        enviar_solicitacao('I');
                    }
                }
            });
            return false;
	});

	<?php if ($this->stPedidoAlteracao === 'A' || $this->stPedidoAlteracao === 'T') : ?>
	$('#enviar_solicitacao').show(); // exibe o formul�rio de envio
	<?php endif; ?>

	<?php if ($this->stPedidoAlteracao === 'A') : ?>
	// desabilita todos os formul�rios e bot�es de cadastro
	$('.btnsModReadequacao').hide(); // esconde os bot�es
	$('#formReadequacao input, #formReadequacao textarea, #formReadequacao select').attr('disabled', true); // desabilita os campos do formul�rio
	<?php endif; ?>

	<?php if ($this->stPedidoAlteracao === 'I') : ?>
	/*$('#projeto-em-analise').dialog
	({
		modal: true,
		resizable: false,
		draggable: false,
		closeOnEscape: false,
		title: 'Alerta!',
		width: 320,
		height: 200,
		buttons:
		{
			'OK': function()
			{
				fecharModal('projeto-em-analise');
				carregandoModal();
				redirecionar('<?php //echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index'));?>?idPronac=<?php //echo $this->idPronac;?>');
			}
		}
	});
	$('.ui-dialog-titlebar-close').remove();*/
	messageTopo('H&aacute; pedido de readequa&ccedil;&atilde;o em an&aacute;lise. Favor aguardar.', 'ALERT');
	$('#enviar_solicitacao, .btnsModReadequacao').hide(); // esconde os formul�rios e bot�es do sistema
	$('#formReadequacao input, #formReadequacao textarea, #formReadequacao select').attr('disabled', true); // desabilita os campos do formul�rio
	$('.linksModReadequacao').click( function() { return false; }); // bloqueia os links
	$('.linksModReadequacao').css('text-decoration', 'none'); // oculta os links
	$('.linksModReadequacao').css('cursor', 'auto'); // oculta o cursor de link
	$('.linksModReadequacao').attr('title', 'Produto dispon�vel apenas para consulta');
	<?php endif; ?>

	// efetua o envio dos formul�rios
	$('#btn_salvar').click(function() {

            var preenchimento = false;
            $('.preenchimentoObg').each(function(){
                if($.trim($(this).val()) == '')
                    preenchimento = true;
            });

            if(preenchimento) {
                $("#msgAlerta").dialog("destroy");
                $("#msgAlerta").html("Favor preecher os dados obrigat&oacute;rios!");
                $("#msgAlerta").dialog({
                    resizable: false,
                    title: 'Alerta!',
                    width:300,
                    height:160,
                    modal: true,
                    buttons : {
                        'OK' : function(){
                            $(this).dialog('close');
                        }
                    }
                });
                $('.ui-dialog-titlebar-close').remove();
                return false;
            }

            validacao = validarFormularios();
            if (!validacao) {
                alertModal('Alerta!', 'msgAlert3');
                //message('Dados obrigat&oacute;rios n�o informados!', 'ERROR');
                //$('html, body').animate({scrollTop: $("#msgERROR").offset().top}, 2000);
            } else if(validacao == 'cancelarSegundaModal'){
                return false;
            } else {
                $(this).dialog('close');
                $('#msgConfirm0').dialog ({
                    modal: true,
                    resizable: false,
                    draggable: false,
                    closeOnEscape: false,
                    title: 'Enviar Solicita��o',
                    width: 300,
                    height: 200,
                    buttons: {
                        N�o: function() {
                        fecharModal('msgConfirm0');
                        carregandoModal();
                            $('#stPedidoAlteracao').attr('value', 'A');
                            $('#formReadequacao').submit();
                        },
                        'Sim': function() {
                            fecharModal('msgConfirm0');
                            carregandoModal();
                            $('#stPedidoAlteracao').attr('value', 'T');
                            $('#formReadequacao').submit();
                        }
                    }
                });
            }
            return false;
	});
    });

    function messageTopo(msg, type){
        $('#novas_mensagens').html('');
        $('#novas_mensagens').append('<div class="msg'+type+'"><div class="float_right"><input type="button" class="btn_close" title="Fechar mensagem" id="msg'+type+'" onclick=$(".msg'+type+'").hide(); /></div><div>'+msg+'</div></div>');
    }


    /**
     * Fun��o para carregar os dados do hist�rico
     */
    function carregaDados(url, divRetorno) {
	$("#historico").html('<br><br><center>Aguarde, carregando dados...<br><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" /></center><br><br>');
	$.ajax({
            url : url,
            success: function(data) {
                $('#' + divRetorno).html(data);
            },
            type : 'post'
	});
    } // fecha fun��o carregaDados()


    /**
     * Fun��o para validar os formul�rios
     */
    function validarFormularios() {
	validacao = true;
	t = $('#tpAlteracaoProjeto').val();

	// valida��o proponente
	if (t == 1 || t == 2) {
            if ($('#cpfcnpj').val() == '' || $('#nome').val() == '' || $('#justificativa').val() == ''){
                validacao = false;
            }
	}

	// valida��o produtos
	if (t == 7) {
            /*if ($('#').val() == '' || $('#').val() == '' || $('#').val() == '')
            {
                    validacao = false;
            }*/
	}

	// valida��o ficha t�cnica
	if (t == 3) {
            if ($('#fichaSolicitada').val() == '' || $('#justificativa').val() == '') {
                validacao = false;
            }
	}

	// valida��o local de realiza��o
	if (t == 4) {
            if ($('#pais').val() == '31' && ($('#uf').val() == '' || $('#cidade').val() == '') || $('#justificativa').val() == '') {
                validacao = false;
            }
	}

	// valida��o nome do projeto
	if (t == 5) {
            if ($('#nome').val() == '' || $('#justificativa').val() == ''){
                validacao = false;
            }
        }

	// valida��o prazo execu��o
	if (t == 9) {
            if ($('#dtInicioExecucaoSR').val() == '' || $('#dtFimExecucaoSR').val() == '' || $('#justificativa').val() == '') {
                validacao = false;
            } else if (!validarData($('#dtInicioExecucaoSR').val())) {
                var div = $("<div id='modal-validar-dataE-validacao1'></div>").html('Data de In�cio Inv�lida!').appendTo('body');
                alertModal("Alerta!", "modal-validar-dataE-validacao1");
                return 'cancelarSegundaModal';
            } else if (!validarData($('#dtFimExecucaoSR').val())) {
                var div = $("<div id='modal-validar-dataE-validacao2'></div>").html('Data de Fim Inv�lida!').appendTo('body');
                alertModal("Alerta!", "modal-validar-dataE-validacao2");
                return 'cancelarSegundaModal';
            } else if (compararDataInicialDataFinal($('#dtInicioExecucaoSR').val(), $('#dtFimExecucaoSR').val()) == 1){
                var div = $("<div id='modal-validar-dataE-inicio'></div>").html('Data In�cio n�o poder� ser superior a Data Fim!').appendTo('body');
                alertModal("Alerta!", "modal-validar-dataE-inicio");
                return 'cancelarSegundaModal';
            } else if (compararDataInicialDataFinal($('#dtFimExecucao').val(), $('#dtInicioExecucaoSR').val()) != 0 || diasDecorridosEntreDuasDatas($('#dtFimExecucao').val(), $('#dtInicioExecucaoSR').val()) != 1){
                var div = $("<div id='modal-validar-dataE-inicial'></div>").html('A nova Data de In�cio deve ser um dia posterior a Data Final atual!').appendTo('body');
                alertModal("Alerta!", "modal-validar-dataE-inicial");
                return 'cancelarSegundaModal';
            } else if ($('#dtInicioExecucaoSR').val().substr(6,4) != $('#dtFimExecucaoSR').val().substr(6,4)) {
                var div = $("<div id='modal-validar-dataE-ano'></div>").html('As Datas dever�o ser na ocorr�ncia do mesmo ano de exerc�cio fiscal!').appendTo('body');
                alertModal("Alerta!", "modal-validar-dataE-ano");
                return 'cancelarSegundaModal';
            }
	}

	// valida��o prazo capta��o
	if (t == 8) { }

	// valida��o proposta pedag�gica
	if (t == 6) {
            if ($('#especificacaoSolicitada').val() == '' || $('#informacoesSolicitada').val() == '')   {
                validacao = false;
            }
	}

	// valida��o custos
	if (t == 10) {
		/*if ($('#').val() == '' || $('#').val() == '' || $('#').val() == '')
		{
			validacao = false;
		}*/
	}

	return validacao;
    } // fecha fun��o validarFormularios()
</script>

<script src="<?php echo $this->baseUrl(); ?>/public/js/jquery.MultiFile.js" type="text/javascript"></script>

<!-- ========== IN�CIO MENU ========== -->
<div id="menuContexto">
    <div class="top"></div>
    <div id="qm0" class="qmmc">
        <a class="<?php echo $mAtivo[0]; ?>" href="<?php echo $this->url(array('controller' => $controle, 'action' => $acao[0], 'idpronac' => Seguranca::encrypt($this->idPronac)), '', true); ?>" <?php echo $onClick[0]; ?> title="Ir para <?php echo $menu[1][0]; ?>"><?php echo $menu[1][0]; ?></a>
        <a class="sanfona" href="#" title="Abrir menu <?php echo $menu[2][0]; ?>"><?php echo $menu[2][0]; ?></a>
        <div class="sanfonaDiv<?php echo $sumirProjetos; ?>">
            <?php for ($i = 1; $i <= 7; $i++) : ?>
            <a class="<?php echo $mAtivo[$i]; ?>" href="<?php echo $this->url(array('controller' => $controle, 'action' => $acao[$i], 'idpronac' => Seguranca::encrypt($this->idPronac)), '', true); ?>" <?php echo $onClick[$i]; ?> title="Ir para <?php echo $menu[2][$i]; ?>"><?php echo $menu[2][$i]; ?></a>
            <?php endfor; ?>
        </div>
        <a class="sanfona" href="#" title="Abrir menu <?php echo $menu[3][0]; ?>"><?php echo $menu[3][0]; ?></a>
        <div class="sanfonaDiv<?php echo $sumirCusto; ?>">
            <?php $i--; // $i = 7?>
            <?php for ($j = 1; $j <= 2; $j++) : ?>
            <a class="<?php echo $mAtivo[$i+$j]; ?>" href="<?php echo $this->url(array('controller' => $controle, 'action' => $acao[$i+$j], 'idpronac' => Seguranca::encrypt($this->idPronac)), '', true); ?>" <?php echo $onClick[$i+$j]; ?> title="Ir para <?php echo $menu[3][$j]; ?>"><?php echo $menu[3][$j]; ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <br clear="left" class="br" />
    <div class="bottom"></div>
    <div id="espaco">
        <br />
        <br />
        <form class="sumir" name="enviar_solicitacao" id="enviar_solicitacao" action="<?php echo $this->url(array('controller' => 'readequacao', 'action' => 'enviar-solicitacao')); ?>" method="post">
            <input type="hidden" name="finalizarPedido" id="finalizarPedido" value="T" />
            <p align="center"><input type="submit" id="menu_enviar_readequacao" class="btn_enviar_solicitacao" style="width: 120px;" value=" " title="Enviar solicita&ccedil;&atilde;o de readequa&ccedil;&atilde;o" /></p>
        </form>
    </div>
</div>
<!-- ========== FIM MENU ========== -->

<?php
/**
 * Defini��o de todas as mensagens de valida��o do m�dulo readequa��o
 */

// mensagens de alerta
$msgAlert[0]   = 'Solicita&ccedil;&atilde;o realizada com sucesso!';
$msgAlert[1]   = 'Antes de enviar a solicita&ccedil;&atilde;o &eacute; necess&aacute;rio cadastrar os Itens de Custos para os Produtos sem Planilha Or&ccedil;ament&aacute;ria!';
$msgAlert[2]   = 'H&aacute; pedido de readequa&ccedil;&atilde;o em an&aacute;lise. Favor aguardar.';
$msgAlert[3]   = 'Dados obrigat&oacute;rios n&atilde;o informados!';
$msgAlert[4]   = '';

// mensagens de confirma��o
$msgConfirm[0] = 'Solicita&ccedil;&atilde;o de readequa&ccedil;&atilde;o realizada com sucesso! Deseja fazer nova readequa&ccedil;&atilde;o?';
$msgConfirm[1] = $this->tipoReadequacao . 'Tem certeza que deseja Enviar e Finalizar?';
$msgConfirm[2] = '';
?>

<!-- ========== TEXTO MODAIS ========== -->
<?php for ($i = 0; $i < count($msgAlert); $i++) : ?>
<div id="msgAlert<?php echo $i; ?>" class="sumir"><?php echo $msgAlert[$i]; ?></div>
<?php endfor; ?>

<?php for ($i = 0; $i < count($msgConfirm); $i++) : ?>
<div id="msgConfirm<?php echo $i; ?>" class="sumir"><?php echo $msgConfirm[$i]; ?></div>
<?php endfor; ?>

<div id="modal-excluir-arquivo" class="sumir">Deseja realmente excluir o arquivo anexado?</div>
<div id="projeto-em-analise" class="sumir">H&aacute; pedido de readequa&ccedil;&atilde;o em an&aacute;lise. Favor aguardar.</div>