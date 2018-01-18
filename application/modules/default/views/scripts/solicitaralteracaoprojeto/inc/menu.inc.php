<!-- ========== INÍCIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">

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

                <a href="#" title="Proponente">Proponente</a>
                <div class="sanfonaDiv">
                </div>
                <a id="abrir_projetos" href="#" title="Projetos">Projetos</a>
                <div class="sanfonaDiv">
                </div>
                <?php if (count($this->buscaPlanilhaCusto) > 0) {
    ?>
                <a href="#" title="Proponente">Custo</a>
                <?php
} ?>
                <div class="sanfonaDiv">
                </div>



            </div>
            <div class="bottom"></div>
        </div>

        <!-- final: navegação local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->
<script>

                $(document).ready(function(){
                $('.sanfona > a').click(function(){
                    $('.sanfona .sanfonaDiv').each(function(indice, valor) {
                        $(valor).hide('slow');
                    });
                    $(this).next().toggle('slow');
                });
            });


</script>