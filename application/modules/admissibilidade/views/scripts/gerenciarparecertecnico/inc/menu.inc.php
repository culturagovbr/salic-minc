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

//                $("#navglobal").css("width",fluidNavGlobal);
//                $("#conteudo").css("width",fluidConteudo);
//                $("#titulo").css("width",100%);
//                $("#rodapeConteudo").css("width",fluidConteudo);
//                $("#rodape").css("width",fluidRodape);

                $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
            }
        </script>

        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
                <a class="no_seta" href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'gerenciarparecertecnico', 'action' => 'imprimiretiqueta')); ?>'>Imprimir Etiqueta</a>
				<a class="no_seta" href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'gerenciarparecertecnico', 'action' => 'imprimirparecertecnico')); ?>'>Imprimir Parecer T&eacute;cnico</a>
				<a class="no_seta" href='<?php echo $this->url(array('module' => 'admissibilidade', 'controller' => 'gerenciarparecertecnico', 'action' => 'parecertecnico')); ?>'>Parecer Técnico</a>
            </div>
            <div class="bottom">
            </div>
            <div id="space_menu">
            </div>
        </div>

        <div id="alertar"></div>
        <!-- final: navega��o local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->






