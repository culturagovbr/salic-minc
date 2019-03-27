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
            <div id="qm0" class="qmmc">
                <!--<a class ="no_seta" href="<?php// echo $this->url(array('controller' => 'fiscalizarprojetocultural', 'action' => 'diligenciarproponente')); ?>" title="Diligenciar">Diligenciar</a>-->
                <a class ="no_seta" href="<?php echo $this->url(array('module'=>'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaanalista')); ?>?idPronac=<?php echo $this->projeto[0]->IdPRONAC;?>&situacao=E60&tpDiligencia=172" target="_blank" title="Diligenciar">Diligenciar Proponente</a>
                <!--<a class="no_seta" target='_blank' href='<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem'), '', true); ?>/idpronac/<?php echo preg_replace('([^0-9])', '', $this->projeto[0]->IdPRONAC); ?>'>Mensagem</a>-->
            </div>
            <div class="bottom"></div>
            <div id="space_menu"></div>
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
        <!-- final: navegação local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->
