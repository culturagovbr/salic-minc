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

        <!-- início: navegação local #qm0 -->
        <script type="text/javascript">
            function layout_fluido()
            {
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

        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'procuracao', 'action' => 'index'), '', true); ?>'>Procurações cadastradas</a>
                <a class="no_seta" href='<?php echo $this->url(array('controller' => 'procuracao', 'action' => 'cadastramento'), '', true); ?>'>Cadastramento</a>
            </div>
            <div class="bottom"></div>
        </div>


        <div id="alertar"></div>
        <!-- final: navegação local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->






