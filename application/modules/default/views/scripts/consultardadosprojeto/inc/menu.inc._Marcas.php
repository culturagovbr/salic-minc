<!-- ========== INÍCIO MENU ========== --> 
 
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

    <!-- início: conteúdo principal #container -->
    <div id="container">

        <!-- início: navegaç?o local #qm0 -->
        <script type="text/javascript">
            function layout_fluido()
            {
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
            
            function carregaDados(url,divRetorno)
            {
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
                    url : '<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'form-imprimir-projeto' ));?>?idPronac='+idPronac,
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
                    height:400,
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
                    $('#msgErroImpressao').html("<center><font color='red'>É obrigatório selecionar ao menos uma informação para impressão.</font></center>");
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
                   // x($this->blnProponente);
        if ($this->blnProponente) {
            /* $proj = new Projetos();
              $r = new tbRelatorio();
              $tblLib = new Liberacao();
              $resp = $proj->buscar(array('IdPRONAC = ?' => $id))->current();
              $anoProjeto = $resp->AnoProjeto;
              $sequencial = $resp->Sequencial;

              //$rsLib = $tblLib->buscar(array('AnoProjeto =?'=>'00','Sequencial =?'=>'0044'));
              $rsLib = $tblLib->buscar(array('AnoProjeto =?'=>$anoProjeto,'Sequencial =?'=>$sequencial));
              $liberacao = $rsLib->count();
              $intervalo = round(Data::CompararDatas($resp->DtInicioExecucao,$resp->DtFimExecucao));
              $qtdRelatorioEsperado = round($intervalo/90);
              $countRelTrimestral = count($r->buscar(array('idPronac = ?' => $id, 'tpRelatorio = ?'=> 'T', 'idAgenteAvaliador IS NOT NULL'=>'')));

              $buscarrelatorioTrimestral = $r->buscar(array('idPronac = ?' => $id, 'tpRelatorio = ?'=> 'T', 'idAgenteAvaliador IS NULL'=>''));
              $buscarrelatorioConsolidado = $r->buscar(array('idPronac = ?' => $id, 'tpRelatorio = ?'=> 'C', 'idAgenteAvaliador IS NOT NULL'=>''));

              $totalReg = $r->buscar(array('idPronac = ?' => $id, 'tpRelatorio = ?'=> 'T'));
              $diasExecutados = round(Data::CompararDatas($resp->DtInicioExecucao));
              $qtdHabilitado = ceil($diasExecutados/90); */
//                        x('Dt Inicio = '.$resp->DtInicioExecucao.' e Dt Fim = '.$resp->DtFimExecucao);
//                        x('Qtd de relatórios esperados para o projeto = '.$qtdRelatorioEsperado);
//                        x('Qtd de dias após o início da execuç?o = '.$diasExecutados);
//                        x('Qtd de relatórios habilitados para o cadastro = '.$qtdHabilitado);
//                        x('Qtd de relatórios cadastrados = '.count($totalReg));
        }
        ?>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
            
                <!-- CONSULTAR DADOS DO PROJETO  -->
                <a href="#" title="Abrir menu Consultar" class="ancoraDadosdoprojeto" onclick="return false;">Consultar dados do projeto</a>
                <div style="display: none;">
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-complementares')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Dados complementares do projeto">Dados complementares do projeto</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'local-realizacao-deslocamento')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Local de realiza&ccedil;&atilde;o/ Deslocamento">Local de realiza&ccedil;&atilde;o/ Deslocamento</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'plano-de-divulgacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Plano de divulga&ccedil;&atilde;o">Plano de divulga&ccedil;&atilde;o</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'plano-de-distribuicao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Plano de distribui&ccedil;&atilde;o">Plano de distribui&ccedil;&atilde;o</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'planilha-orcamentaria')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Planilha or&ccedil;ament&aacute;ria">Planilha or&ccedil;ament&aacute;ria</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'documentos-anexados')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Documentos anexados">Documentos anexados</a>
                    <!--<a href='<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaanalista')); ?><?php echo $codPronac;?>' target="_blank" title="Ir para Dilig&ecirc;ncias do projeto">Dilig&ecirc;ncias do projeto</a>-->
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'diligencias')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Dilig&ecirc;ncias do projeto">Dilig&ecirc;ncias do projeto</a>
                    <?php if (in_array($this->intFaseProjeto, array('2','3','4'))):?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'recurso')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Recursos">Recursos</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'aprovacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Aprova&ccedil;&atilde;o">Aprova&ccedil;&atilde;o</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'analise-projeto')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para An&aacute;lise do projeto">An&aacute;lise do projeto</a>
                        <!--<a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'analise-readequacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para An&aacute;lise de readequa&ccedil;&atilde;o">An&aacute;lise de readequa&ccedil;&atilde;o</a>-->
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-bancarios')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Dados banc&aacute;rios">Dados banc&aacute;rios</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorios-trimestrais')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Relat&oacute;rios trimestrais">Relat&oacute;rios trimestrais</a>
                    <?php endif;?>
                    <?php if (in_array($this->intFaseProjeto, array('4'))):?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-final')); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Relat&oacute;rios final">Relat&oacute;rio final</a>
                    <?php endif;?>
                    <?php if (in_array($this->intFaseProjeto, array('2','3','4'))):?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-fiscalizacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Dados da fiscaliza&ccedil;&atilde;o">Dados da fiscaliza&ccedil;&atilde;o</a>
                    <?php endif;?>
                    <?php if (in_array($this->intFaseProjeto, array('4'))):?>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'prestacao-de-contas')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Presta&ccedil;&atilde;o de Contas">Presta&ccedil;&atilde;o de contas</a>
                    <?php endif;?>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'tramitacao')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Tramita&ccedil;&atilde;o">Tramita&ccedil;&atilde;o</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'providencia-tomada')); ?><?php echo $codPronac;?>','conteudo'); return false" title="Ir para Provid&ecirc;ncia tomada">Provid&ecirc;ncia tomada</a>
                </div>
                <!-- FIM - CONSULTAR DADOS DO PROJETO  -->
				
<?php //if(($this->respProponente == 'P') && ($this->inabilitado == 'N') OR (($this->respProponente == 'R') && ($this->procuracaoValida == 'S') && ($this->inabilitado == 'N') && ($this->intFaseProjeto == 2))):?>
<?php if ($this->inabilitado == 'N'): ?>

                <!-- ======================= DILIGENCIA ====================== -->
                <?php if (in_array($this->intFaseProjeto, array('1','2','3'))):?>
                    <?php if (($this->blnProponente) || (in_array($this->intFaseProjeto, array('1')) && $this->respProponente == 'R') || (!in_array($this->intFaseProjeto, array('1')) && $this->respProponente == 'R' && $this->procuracaoValida == 'S')) {
            ?>
                        <a class="no_seta" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaproponente')); ?><?php echo $codPronac; ?>">Dilig&ecirc;ncias</a>
                    <?php
        } else {
            ?>
                        <!--<a class="no_seta" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaanalista')); ?><?php echo $codPronac; ?>">Dilig&ecirc;ncias</a>
                        <div class="sanfonaDiv" style="display: none;"></div>-->
                        <a class="no_seta" target="_blank" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem')); ?><?php echo $auxPronac; ?>">Mensagens</a>
                    <?php
        } ?>
                <?php endif;?>
                <!-- FIM - DILIGENCIA -->



                <!-- ======================= SOLICITAR RECURSO  =======================   -->
                <?php
                    $arrSitIndef = array('A14','A16','A17','A41','D14');
                    $arrSitIndefAprov = array('A14','A16','A17','A41','D14','D09','D11','D25','D36','D38');
                ?>
                <?php if (in_array($this->intFaseProjeto, array('1','2'))):?>
                        <?php if ($this->blnProponente || (in_array($this->intFaseProjeto, array('1')) && $this->respProponente == 'R') || (!in_array($this->intFaseProjeto, array('1')) && $this->respProponente == 'R' && $this->procuracaoValida == 'S')) {
                    ?>
                            <?php if (isset($this->resp->Situacao) && in_array($this->resp->Situacao, $arrSitIndef) && $this->intFaseProjeto == '1') : ?>
                                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'solicitarrecursodecisao', 'action' => 'recurso', 'idpronac' => $this->idPronac), '', true); ?>">Solicitar Recurso</a>
                            <?php endif; ?>
                            <?php if (isset($this->resp->Situacao) && in_array($this->resp->Situacao, $arrSitIndefAprov) && $this->intFaseProjeto == '2') : ?>
                                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'solicitarrecursodecisao', 'action' => 'recurso', 'idpronac' => $this->idPronac), '', true); ?>">Solicitar Recurso</a>
                            <?php endif; ?>
                        <?php
                } ?>
                <?php endif;?>
                <!-- FIM - SOLICITAR RECURSO -->

                <!-- ======================= SOLICITAR READEQUACAO  =======================   -->
                <?php if (in_array($this->intFaseProjeto, array('2'))):?>
                    <?php if ($this->blnProponente) {
                    ?>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'solicitarreadequacaodoprojeto', 'action' => 'index')); ?>?idusuario=<?php echo $this->usuario->IdUsuario; ?>&idpronac=<?php echo $this->idPronac; ?>">Solicitar Readequa&ccedil;&atilde;o</a>
                    <?php
                } ?>
                <?php endif;?>
                <!-- FIM - SOLICITAR READEQUACAO -->

                <!-- ======================= COMPRAVACAO FINANCEIRA  ======================= -->
                <?php
                    $arrSitComprov = array('E12','E13','E15','E50','E59','E61','E62');
                ?>
                <?php if (in_array($this->intFaseProjeto, array('2'))):?>
                    <?php if ($this->blnProponente) {
                    if (isset($this->liberacao) && $this->liberacao > 0) {
                        ?>
                                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'pagamento')); ?>?idusuario=<?php echo $this->usuario->IdUsuario; ?>&idpronac=<?php echo $this->idPronac; ?>" title="Realizar Comprova&ccedil;&atilde;o Financeira">Realizar Comprova&ccedil;&atilde;o Financeira</a>
                            <?php
                    }
                } ?>
                <?php endif; ?>
                <!-- FIM - COMPROVACAO FINANCEIRA -->


                <!-- ======================= COMPROVACAO FISICA  ======================= -->
                <?php if (in_array($this->intFaseProjeto, array('2'))): ?>
                    <?php if ($this->blnProponente) {
                    if (isset($this->liberacao) && $this->liberacao > 0) { //ANTIGO MODO DE VALIDAR A APRESENTACAO DO LINK?>
                                <div class="sanfonaDiv" style="display:none;"></div>
                                <a href="#" title="Realizar Comprova&ccedil;&atilde;o F&iacute;sica" class="ancoraComprovacaoFisica" onclick="return false;">Realizar Comprova&ccedil;&atilde;o F&iacute;sica</a>
                                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                                    <?php if ($this->TrimestraisCadastrados == $this->qtdRelatorioEsperado) {
                        ?>
                                        <a style="margin-left: 10px;" href="<?php echo $this->url(array('module' => 'comprovacao-objeto', 'controller' => 'comprovarexecucaofisica', 'action' => 'relatoriotrimestralfinalizado', 'idpronac' => $this->idPronac), '', true); ?>" >Relat&oacute;rio Trimestral</a>
                                        <a style="margin-left: 10px;" href="<?php echo $this->url(array('module' => 'comprovacao-objeto', 'controller' => 'comprovarexecucaofisica', 'action' => 'relatoriofinal', 'idpronac' => $this->idPronac), '', true); ?>">Comprovar Realiza&ccedil;&atilde;o do Objeto</a>
                                    <?php
                    } else {
                        ?>
                                        <?php if ($this->TrimestraisCadastrados <= $this->qtdHabilitado) {
                            ?>
                                            <a style="margin-left: 10px;" href="<?php echo $this->url(array('module' => 'comprovacao-objeto', 'controller' => 'comprovarexecucaofisica', 'action' => 'relatoriotrimestral', 'idpronac' => $this->idPronac), '', true); ?>">Relat&oacute;rio Trimestral</a>
                                            <a style="margin-left: 10px;" href="#" class="mensagem_alerta_bloqueio_consolidado">Comprovar Realiza&ccedil;&atilde;o do Objeto</a>
                                        <?php
                        } else {
                            ?>
                                            <a style="margin-left: 10px;" href="<?php echo $this->url(array('module' => 'comprovacao-objeto', 'controller' => 'comprovarexecucaofisica', 'action' => 'relatoriotrimestralfinalizado', 'idpronac' => $this->idPronac), '', true); ?>" >Relat&oacute;rio Trimestral</a>
                                            <a style="margin-left: 10px;" href="#" class="mensagem_alerta_trim">Comprovar Realiza&ccedil;&atilde;o do Objeto</a>
                                        <?php
                        } ?>
                                    <?php
                    } ?>
                                </div>
                            <?php
                    } // FECHA O ANTIGO MODO DE VALIDAR A APRESENTACAO DO LINK
                } //bln_proponente?>
                <?php endif; ?>
                <!-- FIM - COMPROVACAO FISICA -->

                

                <!--  ======================= PROCURACAO =======================  -->
                <?php if ($this->blnProponente) {
                    ?>
                    <!-- 
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'procuracao', 'action' => 'index')); ?>?idPreProjeto=<?php echo $this->idprojeto; ?>">Procura&ccedil;&atilde;o</a>
                     -->
                <?php
                } ?>
                <!-- FIM - PROCURACAO -->

<?php endif;?>

                <!--<?php if ($this->blnProponente) {
                    ?><a class="no_seta" href="<?php echo $this->url(array('controller' => 'controlarmovimentacaobancaria', 'action' => 'form', 'idpronac' => $this->idPronac), '', true); ?>">Extrato da Movimenta&ccedil;&atilde;o Banc&aacute;ria</a><?php
                } ?>-->

                <!--  ======================= MARCAS =======================  -->
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'upload', 'action' => 'form-enviar-arquivo-marca')); ?><?php echo $codPronac;?>">Marcas</a>
                <!-- FIM - MARCAS -->

                <?php if ($this->blnProponente) {
                    ?>
                    <!--<a href="#" class="ancoraDadosbancarios" >Dados Banc&aacute;rios</a>
                    <div class="sanfonaDiv">
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'dadobancario', 'action' => 'capitacao')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>">Capta&ccedil;&atilde;o</a>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'dadobancario', 'action' => 'contabancaria')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>">Conta banc&aacute;ria</a>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'dadobancario', 'action' => 'liberacaodeconta')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>">Libera&ccedil;&atilde;o de Conta</a>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'dadobancario', 'action' => 'pedidodeprorrogacao')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>">Pedido de prorroga&ccedil;&atilde;o</a>
                    </div>-->
                    

                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'listarprojetos', 'action' => 'listarprojetos')); ?>">Listar Projetos</a>
                
                <?php
                } ?>
                <!--<a class="no_seta" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'imprimir-projeto')); ?><?php echo $codPronac;?>' target="_blank" title="Ir Imprimir Projeto">Imprimir Projeto</a>-->
                <a class="no_seta" href='#' onclick="imprimirProjeto('<?php echo $this->idPronac;?>'); return false;" title="Ir Imprimir Projeto">Imprimir Projeto</a>
                <?php if (isset($this->menumsg)) {
                    ?>
                    <div class="sanfonaDiv"></div>
                    <!--<a class="no_seta" href="<?php //echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem', 'idpronac' => $this->idPronac), '', true);?>">Mensagens</a>-->
                <?php
                } ?>
            <!-- <a class="no_seta" href="<?php echo $this->url(array('controller' => 'controlarmovimentacaobancaria', 'action' => '')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>">Extrato de Movimentaç?o Bancária</a> -->
            <!-- <a class="no_seta" href="<?php echo $this->url(array('controller' => 'upload', 'action' => 'form-enviar-arquivo-marca')); ?><?php echo $codPronac; ?>">Marcas</a> -->
                    
             <!-- Manter Mensagens -->
            <?php 
                $perfisMensagens  = array(131,92,93,122,123,121,129,94,103,110,118,126,125,124,132,136,134,135,138,139);
                if (in_array($this->grupoAtivo, $perfisMensagens)) {
                    ?>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem', 'idpronac' => $this->idPronac), '', true); ?>">Mensagens</a>
            <?php
                } ?>
             
                    
                    
            </div>
            <div class="bottom"></div>
            <div id="space_menu"></div>
        </div>
        <!-- final: navegaç?o local #qm0 -->
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
