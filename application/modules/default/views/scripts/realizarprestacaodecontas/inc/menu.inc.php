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

    <!-- inicio: conteudo principal #container -->
    <div id="container">
        <!-- inicio: navegação local #qm0 -->
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
                //alert( url );
                //$("#titulo").html('');
                $("#conteudo").html('<br><br><center>Aguarde, carregando dados...<br><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" /></center><br><br>');
                $.ajax({
                    url : url,
                    success: function(data){
                        $("#"+divRetorno).html(data);
                    },
                    type : 'post'
                });
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
            $IdPronac = $this->idPronac;

            $resp = array();
       
            # urls
            $diligenciaUrl = $this->url(
                array(
                    'module' => 'proposta',
                    'controller' => 'diligenciar',
                    'action' => 'listardiligenciaanalista',
                    'idPronac' => $this->idPronac,
                    'situacao' => 'E17',
                    'tpDiligencia' => '174',
                ),
                null,
                true
            );
        ?>

    <!-- ========== FIM AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== --> 
    <div id="menuContexto">
        <div class="top"></div>
        <div id="qm0" class="qmmc sanfona">
            
            <div id="corfirma" title="Confirmação" style='display:none;'></div>
            <div id="ok" title="Confirmação" style='display:none;'></div>

            <?php
            /*Alysson - Menu do Corrdenador Geral presção de Contas*/
            if ($this->grupoAtivo == 121) { ?>
            <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/parecer-tecnico?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Parecer T&eacute;cnico" class="no_seta">Parecer T&eacute;cnico</a>
            <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/etapas-de-trabalho?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Etapas de Trabalho" class="no_seta">Etapas de Trabalho</a>
            <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/local-de-realizacao?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Local de Realiza&ccedil;&atilde;o" class="no_seta">Local de Realiza&ccedil;&atilde;o</a>
            <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/plano-de-divulgacao?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Plano de Divulga&ccedil;&atilde;o" class="no_seta">Plano de Divulga&ccedil;&atilde;o</a>
            <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/plano-de-distribuicao?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Plano de Distribui&ccedil;&atilde;o" class="no_seta">Plano de Distribui&ccedil;&atilde;o</a>
            <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/metas-comprovadas?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Metas Comprovadas" class="no_seta">Metas Comprovadas</a>
            <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/itens-comprovados?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Itens Comprovados" class="no_seta">Itens Comprovados</a>
            <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/comprovantes-de-execucao?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Comprovantes de Execu&ccedil;&atilde;o" class="no_seta">Comprovantes de Execu&ccedil;&atilde;o</a>

            <div class="sanfonaDiv" style="display:none;"></div>
            <a href="#" title="Execução" class="ancoraPrestacaoContas" onclick="return false;">Presta&ccedil;&atilde;o de Contas</a>
            <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'execucao-receita-despesa', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Execu&ccedil;&atilde;o da receita e despesa">Execu&ccedil;&atilde;o da receita e despesa</a>
                <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-relacao-pagamentos', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Rela&ccedil;&atilde;o de Pagamentos">Rela&ccedil;&atilde;o de pagamentos</a>
                <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-fisico', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rio f&iacute;sico">Relat&oacute;rio f&iacute;sico</a>
                <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-bens-capital', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Rela&ccedil;&atilde;o de bens de capital">Rela&ccedil;&atilde;o de bens de capital</a>
                <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'pagamentos-por-uf-municipio', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos por UF / Munic&iacute;pio</a>
                <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'pagamentos-consolidados-por-uf-municipio', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos Consolidados</a>
                <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorios-trimestrais', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rios trimestrais">Relat&oacute;rios trimestrais</a>
                <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-final', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rio de cumprimento do objeto">Relat&oacute;rio de cumprimento do objeto</a>
            </div>
            <div class="sanfonaDiv" style="display:none;"></div>

            <a class="no_seta" href="<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'planilhaorcamentaria', 'idPronac'=>$this->idPronac)).'?idPronac='.$this->idPronac.'&idRelatorio='.$this->idRelatorio; ?>" title="Ir para Execução Financeira Comprovada">Execução Financeira Comprovada</a>
            <?php
            } ?>

                <?php
                /*Menu do Corrdenador Geral presção de Contas*/
                if ($this->grupoAtivo == 126 || $this->grupoAtivo == 125) { ?>
                    <div class="sanfonaDiv" style="display:none;"></div>
                    <a href="#" title="Execução" class="ancoraPrestacaoContas" onclick="return false;">Presta&ccedil;&atilde;o de Contas</a>
                    <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'execucao-receita-despesa')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Execu&ccedil;&atilde;o da receita e despesa">Execu&ccedil;&atilde;o da receita e despesa</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-relacao-pagamentos')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Rela&ccedil;&atilde;o de Pagamentos">Rela&ccedil;&atilde;o de pagamentos</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-fisico')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rio f&iacute;sico">Relat&oacute;rio f&iacute;sico</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-bens-capital')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Rela&ccedil;&atilde;o de bens de capital">Rela&ccedil;&atilde;o de bens de capital</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'pagamentos-por-uf-municipio')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos por UF / Munic&iacute;pio</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'pagamentos-consolidados-por-uf-municipio')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos Consolidados</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorios-trimestrais')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rios trimestrais">Relat&oacute;rios trimestrais</a>
                        <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-final')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rio de cumprimento do objeto">Relat&oacute;rio de cumprimento do objeto</a>
                    </div>
                    <div class="sanfonaDiv" style="display:none;"></div>
                    
                    <!--<a class="no_seta" href="<?php echo $this->url(array('module' => 'comprovacao-objeto', 'controller' => 'avaliaracompanhamentoprojeto', 'action' => 'relatoriofinal')).'?idPronac='.$id; ?>" title="Ir para Relatorio de Execução do Objeto">Relatório de Comprovação do Objeto</a>-->
                    <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'relatorio-final')).'?idPronac='.$id; ?>" title="Ir para Relatorio de Execução do Objeto">Relatório de Comprovação do Objeto</a>-->
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'planilhaorcamentaria')).'?idPronac='.$id; ?>" title="Ir para Relatorio de Execução do Objeto">Execução Financeira Comprovada</a>
                    <!-- verifica se o projeto esta com encaminhamento atual para o coordenador -->
                    <?php if (isset($this->cdGruposDestinoAtual) && $this->cdGruposDestinoAtual == "125") {
                        ?>
                        <a class="no_seta" target="_blank" href="<?php echo $diligenciaUrl; ?>" title="Ir para Dilegenciar Proponente">Diligenciar Proponente</a>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'laudofinal', 'idPronac' => $this->idPronac), null, true); ?>" title="Ir para Laudo Final">Laudo Final</a>
                    <?php
                    } ?>
                    <!--<a class="no_seta" target="_blank" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem')); ?>/idpronac/<?php echo $_GET['idPronac']; ?>">Mensagens</a>-->
                    <span class="no_seta last">&nbsp;</span>
                <?php } ?>
                <?php
                    /*Menu do Tecnico de prestacao de contas e Chefe de divisao*/
                    if ($this->grupoAtivo == '124' || $this->grupoAtivo == '132') : ?>
                        <!-- div class="sanfonaDiv" style="display:none;"></div>
                        <a href="#" title="Execução" class="ancoraPrestacaoContas" onclick="return false;">Presta&ccedil;&atilde;o de Contas</a>
                        <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                            <!-- a href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'extratos-bancarios'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' target="_blank" class="no_seta" title="Ir para Dados Banc&aacute;rios">Dados Banc&aacute;rios</a>
                            <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'execucao-receita-despesa')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Execu&ccedil;&atilde;o da receita e despesa">Execu&ccedil;&atilde;o da receita e despesa</a>
                            <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-relacao-pagamentos')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Rela&ccedil;&atilde;o de Pagamentos">Rela&ccedil;&atilde;o de pagamentos</a>
                            <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-fisico')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rio f&iacute;sico">Relat&oacute;rio f&iacute;sico</a>
                            <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-bens-capital')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Rela&ccedil;&atilde;o de bens de capital">Rela&ccedil;&atilde;o de bens de capital</a>
                            <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'pagamentos-por-uf-municipio')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos por UF / Munic&iacute;pio</a>
                            <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'pagamentos-consolidados-por-uf-municipio')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos Consolidados</a>
                            <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorios-trimestrais')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rios trimestrais">Relat&oacute;rios trimestrais</a>
                            <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-final')); ?><?php echo $codPronac; ?>','pagina'); return false"  title="Ir para Relat&oacute;rio de cumprimento do objeto">Relat&oacute;rio de cumprimento do objeto</a-->
                        </div-->
                        <div class="sanfonaDiv" style="display:none;"></div>
                        <!--<a class="no_seta" href="<?php echo $this->url(array('module' => 'comprovacao-objeto', 'controller' => 'avaliaracompanhamentoprojeto', 'action' => 'relatoriofinal')).'?idPronac='.$id.'&status=1'; ?>" title="Ir para Relatorio de Execução do Objeto">Relatório de Comprovação do Objeto</a>-->
                        <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'relatorio-final')).'?idPronac='.$id; ?>" title="Ir para Relatorio de Execução do Objeto">Relatório de Comprovação do Objeto</a>-->
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'planilhaorcamentaria')).'?idPronac='.$id; ?>" title="Ir para Execução Financeira Comprovada">Execução Financeira Comprovada</a>
                        <!-- a class="no_seta" href="<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'emitirparecertecnico')).'?idPronac=' . $id; ?>" title="Ir para Emitir Parecer">Emitir Parecer</a-->
                        <!-- a class="no_seta" target="_blank" href="<?php echo $diligenciaUrl; ?>" title="Ir para Diligenciar Proponente">Diligenciar Proponente</a-->
                        <!--<a class="no_seta" target="_blank" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem', 'idpronac' => $this->idPronac), null, true); ?>">Mensagens</a>-->
                        <!-- a class="no_seta" target="_blank" href="#" id="lnkDespacho">Finalizar Análise</a-->
                        <span class="no_seta last">&nbsp;</span>
               <?php endif ?>

               <?php
               /*Menu da CONJUR AECI*/
               if ($this->grupoAtivo == 'CONJUR' || $this->grupoAtivo == 'AECI' || $this->grupoAtivo == '82' || $this->grupoAtivo == '94') {
                   ?>
                    <a class="no_seta"  href="<?php //echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao'));?>" title="Ir para Procurar">Procurar</a>
                    <span class="no_seta last">&nbsp;</span>
                <?php
               } ?>

            </div>
            <br clear="left" class="br" /> 
            <div class="bottom"></div>
            <div id="space_menu"></div>
        </div>
    </div>
    </div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.ancoraPrestacaoContas').click(function(){
            $(this).next().toggle('fast');
        });
        
        $('#lnkDespacho').click(function(event){
            event.preventDefault();
            <?php if ($this->codGrupo != 132) { //SE chefe de divisao?>
                $("#analisar-confirm").html('Tem certeza que deseja finalizar a an&aacute;lise e enviar para o Chefe de Divis&atilde;o ?');
            <?php
               } else {
                   ?>
                $("#analisar-confirm").html('Tem certeza que deseja finalizar a an&aacute;lise e enviar ao Coordenador de Prestação de Contas ?');
            <?php
               }?>
            $("#analisar-confirm").dialog({
                title : 'Confirma&ccedil;&atilde;o',
                resizable: false,
                width: 350,
                height: 210,
                modal: true,
                autoOpen: true,
                buttons : {
                    'N\u00e3o' : function(){
                        $(this).dialog('close');
                    },
                    'Sim' : function(){
                        <?php
                            if ($this->codGrupo == 132) : //SE chefe de divisao
                                $url = $this->url(
                                    array(
                                        'controller' => 'realizarprestacaodecontas',
                                        'action' => 'enviarcoordenador',
                                        'idPronac' => $this->idPronac,
                                        'situacao' => 'E27',
                                    )
                                );
                            else :
                                $url = $this->url(
                                    array(
                                        'controller' => 'realizarprestacaodecontas',
                                        'action' => 'enviarchefedivisao',
                                        'idPronac' => $this->idPronac,
                                        'situacao' => 'E27',
                                    )
                                );
                            endif;
                            echo "window.location='{$url}';";
                        ?>
                        $(this).dialog('close');
                    }
                }
            });
        });
    });
</script>
