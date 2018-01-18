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

        <!-- in�cio: navega��o local #qm0 -->
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
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'dadosprojeto', 'action' => 'index')); ?>?idPronac=<?php echo $getPronac; ?>" title="Ir para Consultar Projetos">Consultar Projetos </a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'proponente', 'action' => 'index')); ?>?idPronac=<?php echo $getPronac; ?>" title="Ir para Dados do Proponente">Dados do Proponente</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'anexardocumentos', 'action' => 'index')); ?>?pronac=<?php echo $getPronac; ?>" title="Ir para Documentos Anexados">Documentos Anexados</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'visualizarhistorico', 'action' => 'index')); ?>?pronac=<?php echo $getPronac; ?>" title="Ir para Hist�rico">Hist�rico</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'diligenciarproponente', 'action' => 'index')); ?>?idPronac=<?php echo $getPronac; ?>" title="Ir para Diligenciar Proponente">Diligenciar Proponente</a>
            </div>
            <div class="bottom">
            </div>
            <div id="cronometro" style="background: #f8f8f8; display: none; font-size: 1.8em; padding-top: 2em; text-align: center; color: red; font-weight: 800; padding-left: 0.3em;" >
                In�cio da Plen�ria em <br/><br/> <span id="minu"></span>' : <span id="seg" ></span>"
            </div>
            <div id="space_menu">
            </div>
        </div>
        <div id="iniciareuniao" class="sumir">Plen�ria Iniciada. Voc� ser� redirecionado</div>

        <div id="alertar"></div>
        <!-- final: navega��o local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->