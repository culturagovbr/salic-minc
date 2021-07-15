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
                <a href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-bancarios'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' class="no_seta" title="Ir para Contas Banc&aacute;rias">Contas Banc&aacute;rias</a>
                <a href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-bancarios-liberacao'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' class="no_seta" title="Ir para Libera&ccedil;&atilde;o">Libera&ccedil;&atilde;o</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'dados-bancarios-captacao'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Capta&ccedil;&atilde;o">Capta&ccedil;&atilde;o</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'extratos-bancarios'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Extrato Banc&aacute;rio">Extrato Banc&aacute;rio</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'extrato-conta-movimento-consolidado'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Extrato Banc&aacute;rio Consolidado">Extrato Banc&aacute;rio Consolidado</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'extrato-de-saldo-bancario'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Saldo das Contas">Saldo das Contas</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'conciliacao-bancaria'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Concilia&ccedil;&atilde;o Banc&aacute;ria">Concilia&ccedil;&atilde;o Banc&aacute;ria</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'inconsistencia-bancaria'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Inconsist&ecirc;ncias Banc&aacute;rias">Inconsist&ecirc;ncias Banc&aacute;rias</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'aporte-captacao', 'action' => 'deposito-equivocado', 'idPronac' => $this->idPronac), '', true); ?>' title="Ir para Dep&oacute;sito Equivocado">Dep&oacute;sito Equivocado</a>
                <a class="last no_seta <?php echo $this->itemMenu == 'devolucoes-do-incentivador' ? 'menuAtivo2':'';?>" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'devolucoes-do-incentivador', 'idPronac' => $this->idPronac), '', true); ?>' title="Ir para Devolu��es do Incentivador">Devolu��es do Incentivador</a>
            </div>
            <div class="bottom"></div>
            <div id="space_menu"></div>
        </div>
    </div>
</div>
<!-- ========== FIM MENU ========== -->
