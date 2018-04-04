<!-- ========== IN�CIO MENU ========== -->
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

    <!-- in�cio: conte�do principal #container -->
    <div id="container">
        <!-- in�cio: navega�?o local #qm0 -->
        <script type="text/javascript">
            function layout_fluido(){
                var janela = $(window).width();
                var fluidNavGlobal = janela - 245;
                var fluidConteudo = janela - 253;
                var fluidTitulo = janela - 252;
                var fluidRodape = janela - 19;

//                $("#navglobal").css("width",fluidNavGlobal);
//                $("#conteudo").css("width",fluidConteudo);
//                $("#titulo").css("width",fluidTitulo);
//                $("#rodapeConteudo").css("width",fluidConteudo);
//                $("#rodape").css("width",fluidRodape);
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
                <a href='<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'dados-bancarios-liberacao'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' class="no_seta" title="Ir para Libera&ccedil;&atilde;o">Libera&ccedil;&atilde;o</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'verprojetos', 'action' => 'dados-bancarios-captacao'), '', true); ?>?idPronac=<?php echo $this->idPronac; ?>' title="Ir para Capta&ccedil;&atilde;o">Capta&ccedil;&atilde;o</a>
            </div>
            <div class="bottom"></div>
            <div id="space_menu"></div>
        </div>
    </div>
</div>
<!-- ========== FIM MENU ========== -->
