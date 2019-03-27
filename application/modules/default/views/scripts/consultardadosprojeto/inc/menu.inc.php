<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">
    <script type="text/javascript">
        $(function(){
            $('.menuHorizontal').each(function(){
                var menu = this;
                $(menu).menu({
                    content: $(menu).next().html(),
                    flyOut: true
                });
            });
        });
    </script>

    <div id="container">
        <script type="text/javascript">
            function layout_fluido(){
                var janela = $(window).width();
                var fluidNavGlobal = janela - 245;
                var fluidConteudo = janela - 253;
                var fluidTitulo = janela - 252;
                var fluidRodape = janela - 19;

                $("#navglobal").css("width",fluidNavGlobal);
                $("#conteudo").css("width",fluidConteudo);
                $("#titulo").css("width",fluidTitulo);
                $("#rodapeConteudo").css("width",fluidConteudo);
                $("#rodape").css("width",fluidRodape);
                $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
            }

            function carregaDados(url,divRetorno){
                //$("#titulo").html('');
                $("#conteudo").html('<br><br><center>Aguarde, carregando dados...<br><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" /></center><br><br>');
                $.ajax({
                    url : url,
                    success: function(data){

                        var data_title = '<div id="titulo"></div>';
                        var data_breadcrumb =
                            '<div id="breadcrumb">' +
                            '<ul>' +
                            '<li class="first">' +
                            '<a href="<?php echo $this->url(array("module" => "default", "controller" => "principal", "action" => "")); ?>" title="Ir para In&iacute;cio">' +
                            'In&iacute;cio</a></li>' +
                            '<li class="second"><a href="<?php echo $this->url(array("module" => "default", "controller" => "consultardadosprojeto", "action" => "")); ?>?idPronac=<?php echo $this->idPronac;?>" title="Ir para In&iacute;cio">Consultar dados do Projeto</a></li>' +
                            '<li class="last" id="caminhoLocalAtual">Consultar dados do Projeto</li>' +
                            '</ul></div>';

                        var breadcrumb_title = data_breadcrumb+data_title;

                        if($("#titulo").length < 1){
                            $("#"+divRetorno).before(breadcrumb_title);
                        }
                        $("#"+divRetorno).html(data);
                    },
                    type : 'post'

                });
            }

            //OPCOES DE IMPRESSAO DO PROJETO
            function  imprimirProjeto(idPronac)
            {
                $('#boxImprimirProjeto').html("<br><br><center>Carregando dados...</center>");

                $.ajax({
                    url : '<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'form-imprimir-projeto' ));?>?idPronac='+idPronac,
                    data :
                        {
                        idPronac : idPronac
                    },
                    success: function(data){
                        $('#boxImprimirProjeto').html(data);
                    },
                    type : 'post'

                });

                $("#boxImprimirProjeto").dialog({
                    title : 'Imprimir Projeto',
                    resizable: true,
                    width:750,
                    height:460,
                    modal: true,
                    autoOpen:false,
                    buttons: {
                        'Fechar': function() {
                            $(this).dialog('close');
                        },
                        'OK': function() {
                            submeteForm();
                            //$('#frmOpcoesImpressao').submit();
                        }
                    }
                });
                $("#boxImprimirProjeto").dialog('open');
            }
            function submeteForm()
            {
                var n = $("input:checked").length;
                if(n > 0){
                    $('#msgErroImpressao').html("");
                    $('#frmOpcoesImpressao').submit();
                }else{
                    $('#msgErroImpressao').html("<center><font color='red'>&eacute; obrigat&oacute;rio selecionar ao menos uma informacao para impress&atilde;o.</font></center>");
                }
            }

        </script>
        <style type="text/css">
            .sanfonaDiv {
                clear: both;
                display: none;
            }
        </style>
        <?php
            $get = Zend_Registry::get("get");
            //define id do PreProjeto que sera passado as outras implementacoes
            $codPronac = "?idPronac=";
            $auxPronac = "/idpronac/";
            if (isset($this->idPronac)) {
                $codPronac .= $this->idPronac;
                $auxPronac .= $this->idPronac;
                $id = $this->idPronac;
            } elseif (isset($get->idPronac)) {
                $codPronac .= $get->idPronac;
                $id = $get->idPronac;
                $auxPronac .= $get->idPronac;
            }
            $resp = array();
        ?>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
                <a
                    href='<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'index')); ?>?idPronac=<?php echo Seguranca::encrypt($id); ?>'
                    class="no_seta"
                    title="Ir para Projeto Atual"
                >
                    Projeto Atual: <span id="pronacProjeto" data-pronac="<?php echo $this->idPronac ?>"><?php echo $this->pronac ?></span>
                </a>
                <a href='#' class="no_seta" onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'dados-proponente')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Dados do Proponente">Proponente</a>
                <!-- ======================= Outras Informacoes  =======================   -->
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Outras Informa&ccedil;&otilde;es" class="ancoraOutrasInformacoes" onclick="return false;">Outras Informa&ccedil;&otilde;es</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'certidoes-negativas')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Certid&otilde;es Negativas">Certid&otilde;es Negativas</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'dados-complementares')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Dados complementares do projeto">Dados complementares do projeto</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'documentos-anexados')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Documentos anexados">Documentos anexados</a>
                    <a href='#' class="no_seta" onclick="carregaDados('<?php echo $this->url(array('module' => 'assinatura', 'controller' => 'index', 'action' => 'visualizar-documentos-assinatura-ajax', 'idPronac' => $this->idPronac)); ?>','conteudo'); return false" title="Ir para Documentos assinados">Documentos assinados</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'diligencias'), null, true); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Dilig&ecirc;ncias do projeto">Dilig&ecirc;ncias do projeto</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'local-realizacao-deslocamento')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Local de realiza&ccedil;&atilde;o/ Deslocamento">Local de realiza&ccedil;&atilde;o/ Deslocamento</a>
                    <a id="planoDistribuicaoId" href='#' title="Ir para Plano de distribui&ccedil;&atilde;o">Plano de distribui&ccedil;&atilde;o</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'plano-de-divulgacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Plano de divulga&ccedil;&atilde;o">Plano de divulga&ccedil;&atilde;o</a>
                    <a href="<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'providencia-tomada')); ?><?php echo $codPronac;?>" title="Ir para Provid&ecirc;ncia tomada">Provid&ecirc;ncia tomada</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'tramitacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Tramita&ccedil;&atilde;o">Tramita&ccedil;&atilde;o</a>

                    <?php if ($this->usuarioInterno) {
            ?>
                        <a href="<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'historico-encaminhamento')); ?><?php echo $codPronac; ?>" title="Ir para Hist&oacute;rico encaminhamento">Hist&oacute;rico encaminhamento</a>
                    <?php
        } ?>
                </div>
                <!-- ==================== FIM - Outras Informacoes  =======================   -->

                <?php if ($this->fnLiberarLinks['Analise'] || $this->usuarioInterno) {
            ?>
                <!-- ======================= Analise e Aprovacao  =======================   -->
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="An&aacute;lise e Aprova&ccedil;&atilde;o" class="ancoraAnaliseAprovacao" onclick="return false;">An&aacute;lise e Aprova&ccedil;&atilde;o</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'analise-projeto')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para An&aacute;lise do projeto">An&aacute;lise do projeto</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'aprovacao')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Aprova&ccedil;&atilde;o">Aprova&ccedil;&atilde;o</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'recurso')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Recursos">Recursos</a>
                </div>
                <!-- ==================== FIM - Analise e Aprova&ccedil;&atilde;o  =======================   -->
                <?php
        } ?>

                <?php if ($this->fnLiberarLinks['Execucao'] || $this->usuarioInterno) {
            ?>
                <!-- ======================= Execucao  =======================   -->
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Execu&ccedil;&atilde;o" class="ancoraExecucao" onclick="return false;">Execu&ccedil;&atilde;o</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <?php if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('2','3','4','5')) || $this->usuarioInterno) {
                ?>
                        <a href="<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'dados-bancarios')); ?><?php echo $codPronac; ?>" title="Ir para Dados banc&aacute;rios">Dados banc&aacute;rios</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'dados-fiscalizacao')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Dados da fiscaliza&ccedil;&atilde;o">Dados da fiscaliza&ccedil;&atilde;o</a>
                    <?php
            } ?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'readequacoes')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Readequa&ccedil;&otilde;es">Dados das readequa&ccedil;&otilde;es</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'marcas-anexadas')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Marcas Anexadas">Marcas Anexadas</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'pedido-prorrogacao')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Pedido de Prorroga&ccedil;&atilde;o">Pedido de Prorroga&ccedil;&atilde;o</a>
                </div>
                <?php
        } ?>
                <!-- ==================== FIM - Execucao  =======================   -->

                <?php if ($this->fnLiberarLinks['PrestacaoContas'] || $this->usuarioInterno) {
            ?>
                <!-- ======================= Prestacao de Contas  =======================   -->
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Presta&ccedil;&atilde;o de Contas" class="ancoraExecucao" onclick="return false;">Presta&ccedil;&atilde;o de Contas</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <?php /*if(in_array($this->fnLiberarLinks['FaseDoProjeto'],array('4','5'))  || $this->usuarioInterno){ ?>
                        <a href='#' class="no_seta" onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'prestacao-de-contas')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Presta&ccedil;&atilde;o de Contas">Presta&ccedil;&atilde;o de contas</a>
                    <?php }*/ ?>

                    <a class="no_seta" href='<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'extratos-bancarios'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Capta&ccedil;&atilde;o">Extrato Banc&aacute;rio</a>
                    <a class="no_seta" href='<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'conciliacao-bancaria'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Concilia&ccedil;&atilde;o Banc&aacute;ria">Concilia&ccedil;&atilde;o Banc&aacute;ria</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'execucao-receita-despesa')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Execu&ccedil;&atilde;o da receita e despesa">Execu&ccedil;&atilde;o da receita e despesa</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'dados-relacao-pagamentos')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Rela&ccedil;&atilde;o de Pagamentos">Rela&ccedil;&atilde;o de pagamentos</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'relatorio-fisico')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Relat&oacute;rio f&iacute;sico">Relat&oacute;rio f&iacute;sico</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'relatorio-bens-capital')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Rela&ccedil;&atilde;o de bens de capital">Rela&ccedil;&atilde;o de bens de capital</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'pagamentos-por-uf-municipio')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos por UF / Munic&iacute;pio</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'pagamentos-consolidados-por-uf-municipio')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos Consolidados</a>
                    <?php if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('2','3','4','5')) || $this->usuarioInterno) {
                ?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'relatorios-trimestrais')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Relat&oacute;rios trimestrais">Relat&oacute;rios trimestrais</a>
                    <?php
            } ?>
                    <?php if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('4','5'))  || $this->usuarioInterno) {
                ?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('module' => 'default', 'controller' => 'consultardadosprojeto', 'action' => 'relatorio-final')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Relat&oacute;rio de cumprimento do objeto">Relat&oacute;rio de cumprimento do objeto</a>
                    <?php
            } ?>
                </div>
                <!-- ==================== FIM - Prestacao de Contas  =======================   -->
                <?php
        } ?>

                <!-- ======================= Readequacao  =======================   -->
                <?php if ($this->blnProponente && ($this->fnLiberarLinks['Readequacao'] || $this->fnLiberarLinks['Readequacao_50'])) {
                    ?>
                    <div class="sanfonaDiv" style="display:none;"></div>
                    <a href="#" title="Execu��o" class="ancoraExecucao" onclick="return false;">Readequa&ccedil;&atilde;o</a>
                    <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                        <a href="<?php echo $this->url(array('module' => 'readequacao', 'controller' => 'local-realizacao', 'action' => 'index'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>"
                           title="Readequar Local de realizaca&ccedil;&atilde;o"
                        >Local de realiza&ccedil;&atilde;o</a>
                        <?php if ($this->fnLiberarLinks['ReadequacaoPlanilha']) {
                            ?>
                            <a href="<?php echo $this->url(array('module' => 'readequacao', 'controller' => 'readequacoes', 'action' => 'planilha-orcamentaria'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>"
                               title="Ir para Solicita&ccedil;&otilde;es Gerais">Planilha or�ament&aacute;ria</a>
                            <?php
                        } ?>
                        <a href="<?php echo $this->url(array('module' => 'readequacao', 'controller' => 'plano-distribuicao', 'action' => 'index'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>"
                           title="Readequar Plano de Distribui&ccedil;&atilde;o"
                        >Plano de Distribui&ccedil;&atilde;o</a>

                        <?php if ($this->fnLiberarLinks['Readequacao_50']) {
                            ?>
                            <a href="<?php echo $this->url(array('module' => 'readequacao', 'controller' => 'remanejamento-menor', 'action' => 'index'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>"
                               title="Ir para Remanejamento &le; 50%">Remanejamento &le; 50%</a>
                            <?php
                        } ?>

                        <?php if ($this->fnLiberarLinks['ReadequacaoTransferenciaRecursos']) : ?>
                            <a href="<?php echo $this->url(array('module' => 'readequacao', 'controller' => 'transferencia-recursos', 'action' => 'index'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>"
                               title="Transfer&ecirc;ncia de recursos">Transfer&ecirc;ncia de recursos</a>
                        <?php endif; ?>
                    <?php if ($this->fnLiberarLinks['ReadequacaoSaldoAplicacao']) {
                    ?>
			<a href="<?php echo $this->url(array('module' => 'readequacao', 'controller' => 'saldo-aplicacao', 'action' => 'index'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Ir para Saldo de aplica&ccedil;&atilde;o">Saldo de aplica&ccedil;&atilde;o</a>
		    <?php
		    } ?>
		    <a href="<?php echo $this->url(array('module' => 'readequacao', 'controller' => 'readequacoes', 'action' => 'index'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>"
                           title="Ir para Solicita&ccedil;&otilde;es Gerais"
                        >Diversas</a>
                    </div>
                    <?php
                } ?>
                <!-- ==================== FIM - Readequacao  =======================   -->

                <!-- ======================= SOLICITAR PRAZO CAPTACAO  =======================   -->
                <?php if ($this->blnProponente) {
            ?>
		<?php if ($this->fnLiberarLinks['SolicitarProrrogacao']): ?>
                <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'solicitarprorrogacao', 'action' => 'index', 'idpronac' => Seguranca::encrypt($this->idPronac))); ?>">Solicitar Prorroga&ccedil;&atilde;o</a>
		<?php endif; ?>

        <?php if ($this->isAdequarARealidade) : ?>
            <a class="no_seta tooltipped"
               data-tooltip="Adequar &agrave; realidade ou Encaminhar projeto adequado para o MinC" target="_blank"
               href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'identificacaodaproposta', 'idPreProjeto' => $this->resp->idProjeto)); ?>">
                Adequar &agrave; realidade
            </a>
        <?php endif; ?>
                <?php
        } ?>
                <!-- ==================== FIM - SOLICITAR PRAZO CAPTACAO  =======================   -->

                <!-- ======================= DILIGENCIA ====================== -->
                <?php if ($this->fnLiberarLinks['Diligencia']) {
            ?>
                    <?php if (true) {
                ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaproponente')); ?><?php echo $codPronac; ?>">Responder Dilig&ecirc;ncia</a>
                    <?php
            } else {
                ?>
                        <a class="no_seta" target="_blank" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'mantermensagens', 'action' => 'consultarmensagem')); ?><?php echo $auxPronac; ?>">Mensagens</a>
                    <?php
            } ?>
                <?php
        } ?>
                <!-- FIM - DILIGENCIA -->

                <!-- ======================= SOLICITAR RECURSO  =======================   -->
                <?php if ($this->fnLiberarLinks['Recursos']): ?>
                    <?php if ($this->codSituacao == 'B02'):
                        /**
                         * @dreprecated: a rotina de recurso do enquadramento foi substituida,
                         * atualmente eh feita na proposta, remover controllers e scripts utilizados.
                         */
                        ?>
                        <div class="sanfonaDiv" style="display:none;"></div>
                        <a href="#" title="Recurso" class="ancoraRecurso" onclick="return false;">Recurso</a>
                        <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                            <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'solicitarrecursodecisao', 'action' => 'recurso-enquadramento')); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>">Solicitar Recurso</a>
                            <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'solicitarrecursodecisao', 'action' => 'recurso-desistir-enquadramento')); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>">Desistir do Recurso</a>
                        </div>
                    <?php else: ?>
                        <div class="sanfonaDiv" style="display:none;"></div>
                        <a href="#" title="Recurso" class="ancoraRecurso" onclick="return false;">Recurso</a>
                        <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                            <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'solicitarrecursodecisao', 'action' => 'recurso')); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>">Solicitar Recurso</a>
                            <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'solicitarrecursodecisao', 'action' => 'recurso-desistir')); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>">Desistir do Recurso</a>
                        </div>
                    <?php endif ?>
                <?php endif ?>
                <!-- FIM - SOLICITAR RECURSO -->

                <!-- ======================= COMPRAVACAO FINANCEIRA  ======================= -->
                <?php if ($this->fnLiberarLinks['ComprovacaoFinanceira']) {
            ?>
                    <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'comprovarexecucaofinanceira', 'action' => 'pagamento')); ?>?idusuario=<?php echo $this->usuario->IdUsuario; ?>&idpronac=<?php echo $this->idPronac; ?>" title="Realizar Comprova&ccedil;&atilde;o Financeira">Realizar Comprova&ccedil;&atilde;o Financeira</a>
                <?php
        } ?>
                <!-- FIM - COMPROVACAO FINANCEIRA -->

                <!-- ======================= COMPROVACAO FISICA  ======================= -->
                <?php if ($this->situacaoProjeto != 'E24') {
            ?>
                    <?php if ($this->fnLiberarLinks['RelatorioTrimestral'] || $this->fnLiberarLinks['RelatorioFinal']) {
                ?>
                        <div class="sanfonaDiv" style="display:none;"></div>
                        <a href="#" title="Realizar Comprova&ccedil;&atilde;o F&iacute;sica" class="ancoraComprovacaoFisica" onclick="return false;">Realizar Comprova&ccedil;&atilde;o F&iacute;sica</a>
                        <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                            <?php if ($this->fnLiberarLinks['RelatorioTrimestral']) {
                    ?>
                            <a style="margin-left: 10px;" href="<?php echo $this->url(array('module' => 'comprovacao-objeto', 'controller' => 'comprovarexecucaofisica', 'action' => 'relatoriotrimestral', 'idpronac' => Seguranca::encrypt($this->idPronac)), '', true); ?>">Relat&oacute;rio Trimestral</a>
                            <?php
                } ?>

                            <?php if ($this->fnLiberarLinks['RelatorioFinal']) {
                    ?>
                            <a style="margin-left: 10px;" href="<?php echo $this->url(array('module' => 'comprovacao-objeto', 'controller' => 'comprovarexecucaofisica', 'action' => 'etapas-de-trabalho-final', 'idpronac' => Seguranca::encrypt($this->idPronac)), '', true); ?>">Comprovar Realiza&ccedil;&atilde;o do Objeto</a>
                            <?php
                } ?>
                        </div>
                    <?php
            } ?>
                <?php
        } ?>
                <!-- FIM - COMPROVACAO FISICA -->

                <!--  ======================= MARCAS =======================  -->
                <?php if ($this->blnProponente) {
            ?>
	        <?php if ($this->fnLiberarLinks['Marcas']): ?>
                <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'upload', 'action' => 'form-enviar-arquivo-marca')); ?><?php echo $codPronac; ?>">Marcas</a>
            <?php endif; ?>
                <?php
        } ?>
                <!--  ==================== FIM - MARCAS ====================  -->

                <!--  ======================= LISTAR PROJETOS =======================  -->
                <?php if ($this->blnProponente) {
            ?>
                    <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'listarprojetos', 'action' => 'listarprojetos')); ?>">Listar Projetos</a>
                <?php
        } ?>
                <!--  ==================== FIM - LISTAR PROJETOS ======================  -->

                <!--  ======================= IMPRIMIR PROJETOS =======================  -->
                <a class="no_seta" href='#' onclick="imprimirProjeto('<?php echo $this->idPronac;?>'); return false;" title="Ir Imprimir Projeto">Imprimir Projeto</a>
                <?php if (isset($this->menumsg)) {
            ?>
                    <div class="sanfonaDiv"></div>
                <?php
        } ?>
                <!--  ==================== FIM - IMPRIMIR PROJETOS ======================  -->

                <!--  ======================= MANTER MENSAGENS =======================  -->
                <?php $perfisMensagens = array(131,92,93,122,123,121,129,94,103,110,118,126,125,124,132,136,134,135,138,139);
                    if (in_array($this->grupoAtivo, $perfisMensagens)) {
                        ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module' => 'default', 'controller' => 'mantermensagens', 'action' => 'consultarmensagem', 'idpronac' => $this->idPronac), '', true); ?>">Mensagens</a>
                <?php
                    } ?>
                <!--  ==================== FIM - MANTER MENSAGENS =======================  -->


                <a class="no_seta" href="<?= $this->url(array('module'=>'solicitacao', 'controller' => 'mensagem', 'action' => 'index', 'idPronac' => $this->idPronac, 'listarTudo' => 'true')); ?>"><?= $this->blnProponente ? "Minhas solicita&ccedil;&otilde;es" : "Solicita&ccedil;&otilde;es"; ?></a>

            </div>
            <div class="bottom"></div>
            <div id="space_menu"></div>
        </div>
    </div>
</div>
<div id="menu_comp_exec" style="display: none;"></div>
<div id="boxImprimirProjeto" style="display: none;"></div>
<script type="text/javascript">
    $(document).ready(function(){
        /*$('.sanfona > a').click(function(){
            var este = $(this).next();
            $('.sanfona .sanfonaDiv').each(function(indice, valor) {
                if(este.html() != $(this).html())
                    $(valor).hide('fast');
            });
            $(this).next().toggle('fast');
        });*/
        $('.ancoraDadosdoprojeto').click(function(){
            $(this).next().toggle('fast');
        });
        $('.ancoraComprovacaoFisica').click(function(){
            $(this).next().toggle('fast');
        });
        $('.ancoraDadosbancarios').click(function(){
            $(this).next().toggle('fast');
        });
        $('.ancoraOutrasInformacoes').click(function(){
            $(this).next().toggle('fast');
        });
        $('.ancoraAnaliseAprovacao').click(function(){
            $(this).next().toggle('fast');
        });
        $('.ancoraExecucao').click(function(){
            $(this).next().toggle('fast');
        });
        $('.ancoraRecurso').click(function(){
            $(this).next().toggle('fast');
        });

        $('.mensagem_alerta_trim').click(function(){
            $("#menu_comp_exec").dialog("destroy");
            $('#menu_comp_exec').html('Aguarde o per&iacute;odo correto para o cadastro do Relat&oacute;rio Trimestral.');
            $("#menu_comp_exec").dialog
            ({
                height: 180,
                title: 'Alerta!',
                modal: true,
                draggable: false,
                resizable: false,
                closeOnEscape: false,
                buttons: {
                    'Ok': function()
                    {
                        $(this).dialog('close');
                    }
                }
            });
            $('.ui-dialog-titlebar-close').remove();
        });


        $('.mensagem_alerta_consolidado').click(function(){
            $("#menu_comp_exec").dialog("destroy");
            $('#menu_comp_exec').html('Relat&oacute;rio Final referente &agrave; Comprova&ccedil;&atilde;o do Objeto j&aacute; foi enviado!');
            $("#menu_comp_exec").dialog
            ({
                height: 180,
                width: 320,
                title: 'Alerta!',
                modal: true,
                draggable: false,
                resizable: false,
                closeOnEscape: false,
                buttons: {
                    'Ok': function()
                    {
                        $(this).dialog('close');
                    }
                }
            });
            $('.ui-dialog-titlebar-close').remove();
        });


        $('.mensagem_alerta_bloqueio_consolidado').click(function(){
            $("#menu_comp_exec").dialog("destroy");
            $('#menu_comp_exec').html('&Eacute; necess&aacute;rio cadastrar todos os Relat&oacute;rios Trimestrais antes de enviar o Relat&oacute;rio Final referente &agrave; Comprova&ccedil;&atilde;o do Objeto!');
            $("#menu_comp_exec").dialog
            ({
                height: 200,
                width: 350,
                title: 'Alerta!',
                modal: true,
                draggable: false,
                resizable: false,
                closeOnEscape: false,
                buttons: {
                    'Ok': function()
                    {
                        $(this).dialog('close');
                    }
                }
            });
            $('.ui-dialog-titlebar-close').remove();
        });


        $('.mensagem_alerta_bloqueio_trimestral').click(function(){
            $("#menu_comp_exec").dialog("destroy");
            $('#menu_comp_exec').html('Todos os Relat&oacute;rios Trimestrais j&aacute; foram enviados.<br />Favor cadastrar o Relat&oacute;rio Final referente &agrave; Comprova&ccedil;&atilde;o do Objeto!');
            $("#menu_comp_exec").dialog
            ({
                height: 190,
                width: 380,
                title: 'Alerta!',
                modal: true,
                draggable: false,
                resizable: false,
                closeOnEscape: false,
                buttons: {
                    'Ok': function()
                    {
                        $(this).dialog('close');
                    }
                }
            });
            $('.ui-dialog-titlebar-close').remove();
        });

        $.get(
            '/projeto/projeto/verificar-in2017/idPronac/' + $('#pronacProjeto').attr("data-pronac"),
            function(data) {
                if(data.IN2017 == false) {
                    $('#pronacProjeto').append(' [IN2013]');
                    $('#planoDistribuicaoId').click( function() {
                        carregaDados('<?php echo $this->url(['module' => 'default','controller' => 'consultardadosprojeto', 'action' => 'plano-de-distribuicao']); ?><?php echo $codPronac;?>','conteudo');
                    });
                } else {
                    $('#pronacProjeto').append(' [IN2017]');
                    $('#planoDistribuicaoId').click( function() {
                        carregaDados('<?php echo $this->url(['module' => 'proposta', 'controller' => 'visualizar-plano-distribuicao', 'action' => 'visualizar', 'idPreProjeto' => $this->idprojeto]); ?>','conteudo');
                    });
                }
            }
        );
    });
</script>
