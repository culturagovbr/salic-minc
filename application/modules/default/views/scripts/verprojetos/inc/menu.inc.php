<!-- ========== INï¿½CIO MENU ========== -->
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

    <!-- inï¿½cio: conteï¿½do principal #container -->
    <div id="container">
        <!-- inï¿½cio: navegaï¿½?o local #qm0 -->
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
                    /*data :
                    {
                        idPronac : 'teste'
                    },*/
                    success: function(data){
                        //alert(data);
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
                    url : '<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'form-imprimir-projeto' ));?>?idPronac='+idPronac,
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
                    $('#msgErroImpressao').html("<center><font color='red'>ï¿½ obrigatï¿½rio selecionar ao menos uma informaï¿½ï¿½o para impressï¿½o.</font></center>");
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

                <a href='<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'index')); ?>?idPronac=<?php echo Seguranca::encrypt($id); ?>' class="no_seta" title="Ir para Projeto Atual">Projeto Atual</a>


                <!-- ======================= Outras Informações  =======================   -->
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Outras Informações" class="ancoraOutrasInformacoes" onclick="return false;">Outras Informações</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'certidoes-negativas')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Certid&otilde;es Negativas">Certid&otilde;es Negativas</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'dados-complementares')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Dados complementares do projeto">Dados complementares do projeto</a>
  <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'documentos-anexados')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Documentos anexados">Documentos anexados</a>                    
<a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'local-realizacao-deslocamento')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Local de realiza&ccedil;&atilde;o/ Deslocamento">Local de realiza&ccedil;&atilde;o/ Deslocamento</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'plano-de-distribuicao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Plano de distribui&ccedil;&atilde;o">Plano de distribui&ccedil;&atilde;o</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'plano-de-divulgacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Plano de divulga&ccedil;&atilde;o">Plano de divulga&ccedil;&atilde;o</a>

                    <?php if ($this->usuarioInterno) {
            ?>
                    <a href="<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'historico-encaminhamento')); ?><?php echo $codPronac; ?>" title="Ir para Hist&oacute;rico encaminhamento">Hist&oacute;rico encaminhamento</a>
                    <?php
        } ?>
                </div>
                <!-- ==================== FIM - Outras Informações  =======================   -->


                <?php if (($this->fnLiberarLinks['Analise'] && in_array($this->fnLiberarLinks['FaseDoProjeto'], array('2','3','4'))) || $this->usuarioInterno) {
            ?>
                <!-- ======================= Análise e Aprovação  =======================   -->
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Análise e Aprovação" class="ancoraAnaliseAprovacao" onclick="return false;">Análise e Aprovação</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'analise-projeto')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para An&aacute;lise do projeto">An&aacute;lise do projeto</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'aprovacao')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Aprova&ccedil;&atilde;o">Aprova&ccedil;&atilde;o</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'recurso')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Recursos">Recursos</a>
                </div>
                <!-- ==================== FIM - Análise e Aprovação  =======================   -->
                <?php
        } ?>



                <!-- ======================= Execução  =======================   -->
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Execução" class="ancoraExecucao" onclick="return false;">Execução</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <?php if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('2','3','4','5')) || $this->usuarioInterno) {
            ?>
                        <a href="<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'dados-bancarios')); ?><?php echo $codPronac; ?>" title="Ir para Dados banc&aacute;rios">Dados banc&aacute;rios</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'dados-fiscalizacao')); ?><?php echo $codPronac; ?>','conteudo'); return false" title="Ir para Dados da fiscaliza&ccedil;&atilde;o">Dados da fiscaliza&ccedil;&atilde;o</a>
                    <?php
        } ?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'readequacoes')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Readequa&ccedil;&otilde;es">Dados das readequa&ccedil;&otilde;es</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'marcas-anexadas')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Marcas Anexadas">Marcas Anexadas</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'pedido-prorrogacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Pedido de Prorroga&ccedil;&atilde;o">Pedido de Prorroga&ccedil;&atilde;o</a>
                </div>

                <!-- ==================== FIM - Execução  =======================   -->



                <!-- ======================= Prestação de Contas  =======================   -->
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Prestação de Contas" class="ancoraExecucao" onclick="return false;">Presta&ccedil;&atilde;o de Contas</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <?php /*if(in_array($this->fnLiberarLinks['FaseDoProjeto'],array('4','5'))  || $this->usuarioInterno){ ?>
                        <a href='#' class="no_seta" onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'prestacao-de-contas')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Presta&ccedil;&atilde;o de Contas">Presta&ccedil;&atilde;o de contas</a>
                    <?php }*/ ?>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'execucao-receita-despesa')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Execu&ccedil;&atilde;o da receita e despesa">Execu&ccedil;&atilde;o da receita e despesa</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'dados-relacao-pagamentos')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Rela&ccedil;&atilde;o de Pagamentos">Rela&ccedil;&atilde;o de pagamentos</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'relatorio-fisico')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Relat&oacute;rio f&iacute;sico">Relat&oacute;rio f&iacute;sico</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'relatorio-bens-capital')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Rela&ccedil;&atilde;o de bens de capital">Rela&ccedil;&atilde;o de bens de capital</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'pagamentos-por-uf-municipio')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos por UF / Munic&iacute;pio</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'pagamentos-consolidados-por-uf-municipio')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos Consolidados</a>
                    <?php if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('2','3','4','5')) || $this->usuarioInterno) {
            ?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'relatorios-trimestrais')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Relat&oacute;rios trimestrais">Relat&oacute;rios trimestrais</a>
                    <?php
        } ?>
                    <?php if (in_array($this->fnLiberarLinks['FaseDoProjeto'], array('4','5'))  || $this->usuarioInterno) {
            ?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'relatorio-final')); ?><?php echo $codPronac; ?>','conteudo'); return false"  title="Ir para Relat&oacute;rio de cumprimento do objeto">Relat&oacute;rio de cumprimento do objeto</a>
                    <?php
        } ?>
                </div>
                <!-- ==================== FIM - Prestação de Contas  =======================   -->




                <!-- ======================= Readequação  =======================   -->
                <?php if ($this->blnProponente && ($this->fnLiberarLinks['Readequacao'] || $this->fnLiberarLinks['Readequacao_50'])) {
            ?>
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Execução" class="ancoraExecucao" onclick="return false;">Readequa&ccedil;&atilde;o</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <?php if ($this->fnLiberarLinks['Readequacao_50']) {
                ?>
                    <a href="<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'remanejamento-menor'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Ir para Remanejamento &le; 20%">Remanejamento &le; 20%</a>
                    <?php
            } ?>

                    <?php if ($this->fnLiberarLinks['Readequacao']) {
                ?>
                    <a href="<?php echo $this->url(array('module' => 'readequacao', 'controller' => 'readequacoes', 'action' => 'index'), '', true); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Ir para Solicita&ccedil;&otilde;es Gerais">Solicita&ccedil;&otilde;es Gerais</a>
                    <?php
            } ?>
                </div>
                <?php
        } ?>
                <!-- ==================== FIM - Readequação  =======================   -->




                <!-- ======================= SOLICITAR PRAZO CAPTAÇÃO  =======================   -->
                <?php if ($this->blnProponente) {
            ?>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'solicitarprorrogacao', 'action' => 'index', 'idpronac' => Seguranca::encrypt($this->idPronac))); ?>">Solicitar Prorroga&ccedil;&atilde;o</a>
                <?php
        } ?>
                <!-- ==================== FIM - SOLICITAR PRAZO CAPTAÇÃO  =======================   -->



                <!-- ======================= DILIGENCIA ====================== -->
                <?php if ($this->fnLiberarLinks['Diligencia']) {
            ?>
                    <?php if ($this->respProponente == 'R') {
                ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaproponente')); ?><?php echo $codPronac; ?>">Responder Dilig&ecirc;ncia</a>
                    <?php
            } else {
                ?>
                        <a class="no_seta" target="_blank" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem')); ?><?php echo $auxPronac; ?>">Mensagens</a>
                    <?php
            } ?>
                <?php
        } ?>
                <!-- FIM - DILIGENCIA -->


                <!-- ======================= SOLICITAR RECURSO  =======================   -->
                <?php if ($this->fnLiberarLinks['Recursos']) {
            ?>
                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Recurso" class="ancoraRecurso" onclick="return false;">Recurso</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'solicitarrecursodecisao', 'action' => 'recurso')); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>">Solicitar Recurso</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'solicitarrecursodecisao', 'action' => 'recurso-desistir')); ?>?idPronac=<?php echo Seguranca::encrypt($this->idPronac); ?>">Desistir do Recurso</a>
                </div>
                <?php
        } ?>
                <!-- FIM - SOLICITAR RECURSO -->




                <!-- ======================= COMPRAVACAO FINANCEIRA  ======================= -->
                <?php if ($this->fnLiberarLinks['ComprovacaoFinanceira']) {
            ?>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'pagamento')); ?>?idusuario=<?php echo $this->usuario->IdUsuario; ?>&idpronac=<?php echo $this->idPronac; ?>" title="Realizar Comprova&ccedil;&atilde;o Financeira">Realizar Comprova&ccedil;&atilde;o Financeira</a>
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
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'form-enviar-arquivo-marca')); ?><?php echo $codPronac; ?>">Marcas</a>
                <?php
        } ?>
                <!--  ==================== FIM - MARCAS ====================  -->




                <!--  ======================= LISTAR PROJETOS =======================  -->
                <?php if ($this->blnProponente) {
            ?>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'listarprojetos', 'action' => 'listarprojetos')); ?>">Listar Projetos</a>
                <?php
        } ?>
                <!--  ==================== FIM - LISTAR PROJETOS ======================  -->




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

    });
</script>
<!-- ========== FIM MENU ========== -->
