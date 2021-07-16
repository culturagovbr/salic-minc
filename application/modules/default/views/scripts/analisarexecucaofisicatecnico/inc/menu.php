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
?>

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

    <!-- INÍCIO: CONTEÚDO principal #container -->
    <div id="container">
        <!-- INÍCIO: navega�?o local #qm0 -->
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
                        //alert(data);
                        $("#"+divRetorno).html(data);
                    },
                    type : 'post'

                });
            }

            //OPCOES DE IMPRESSAO DO PROJETO
        </script>
        <style type="text/css">
            .sanfonaDiv {
                clear: both;
                display: none;
            }
        </style>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">


                <!-- ==================== TESTE  =======================   -->

                <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/parecer-tecnico?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Parecer Técnico" class="no_seta">Parecer Técnico</a>
                <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/etapas-de-trabalho?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Etapas de Trabalho" class="no_seta">Etapas de Trabalho</a>
                <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/local-de-realizacao?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Local de Realiza&ccedil;&atilde;o" class="no_seta">Local de Realiza&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/plano-de-divulgacao?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Plano de Divulga&ccedil;&atilde;o" class="no_seta">Plano de Divulga&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/plano-de-distribuicao?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Plano de Distribui&ccedil;&atilde;o" class="no_seta">Plano de Distribui&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/metas-comprovadas?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Metas Comprovadas" class="no_seta">Metas Comprovadas</a>
                <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/itens-comprovados?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Itens Comprovados" class="no_seta">Itens Comprovados</a>
                <a href="<?php echo $this->baseUrl(); ?>/analisarexecucaofisicatecnico/comprovantes-de-execucao?idpronac=<?php echo $this->idPronac; ?>&relatorio=<?php echo $this->idRelatorio; ?>" title="Comprovantes de Execu&ccedil;&atilde;o" class="no_seta">Comprovantes de Execu&ccedil;&atilde;o</a>

                <div class="sanfonaDiv" style="display:none;"></div>
                <a href="#" title="Execu��o" class="ancoraPrestacaoContas" onclick="return false;">Presta&ccedil;&atilde;o de Contas</a>
                <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'execucao-receita-despesa', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Execu&ccedil;&atilde;o da receita e despesa">Execu&ccedil;&atilde;o da receita e despesa</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-relacao-pagamentos', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Rela&ccedil;&atilde;o de Pagamentos">Rela&ccedil;&atilde;o de pagamentos</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-fisico', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Relat&oacute;rio f&iacute;sico">Relat&oacute;rio f&iacute;sico</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-bens-capital', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Rela&ccedil;&atilde;o de bens de capital">Rela&ccedil;&atilde;o de bens de capital</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'pagamentos-por-uf-municipio', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos por UF / Munic&iacute;pio</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'pagamentos-consolidados-por-uf-municipio', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Pagamentos por UF/Munic&iacute;pio">Pagamentos Consolidados</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorios-trimestrais', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Relat&oacute;rios trimestrais">Relat&oacute;rios trimestrais</a>
                    <a href='#' onclick="carregaDados('<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'relatorio-final', 'idPronac'=>$this->idPronac)); ?><?php echo $codPronac;?>','conteudo'); return false"  title="Ir para Relat&oacute;rio de cumprimento do objeto">Relat&oacute;rio de cumprimento do objeto</a>
                </div>
                <div class="sanfonaDiv" style="display:none;"></div>

                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'realizarprestacaodecontas', 'action' => 'planilhaorcamentaria', 'idPronac'=>$this->idPronac)).'?idPronac='.$this->idPronac.'&relatorio='.$this->idRelatorio; ?>" title="Ir para Execu��o Financeira Comprovada">Execu��o Financeira Comprovada</a>



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
            $(this).next().toggle('fast');
            $('.ancoraPrestacaoContas').click(function(){
                $(this).next().toggle('fast');
            });
    });
</script>
<!-- ========== FIM MENU ========== -->



