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

        <!-- INÍCIO: navega��o local #qm0 -->
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
        </script>

        <style type="text/css">
            .sanfonaDiv {
                clear: both;
                display: none;
            }
        </style>

        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                <a href="#" title="Ir para Proponente">Proponente</a>
                    <div class="sanfonaDiv">
                        <a href="#">Network Anchors</a>
                        <a href="#">Conventions</a>
                        <a href="#">Printable Materials</a>

                        <a href="#">December Programs</a>
                        <a href="#">Promotional</a>
                        <a href="#">Hollywood Support</a>
                    </div>

                <a href="#" title="Ir para Projetos" >Projetos</a>
                <a href="#" title="Ir para Custos" class="last">Custos</a>
            </div>
            <div class="bottom"></div>
        </div>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.sanfona > a').click(function(){
                    $('.sanfona .sanfonaDiv').each(function(indice, valor) {
                        $(valor).hide('fast');
                    });
                    $(this).next().toggle('fast');
                });
            });
        </script>
        <!-- final: navega��o local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->
