<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<script type="text/javascript">
    function layout_fluido()
    {
        var janela = $(window).width();

        var fluidNavGlobal = janela - 245;
        var fluidConteudo = janela - 253;
        var fluidTitulo = janela - 252;
        var fluidRodape = janela - 19;

//        $("#navglobal").css("width",fluidNavGlobal);
//        $("#conteudo").css("width",fluidConteudo);
//        $("#titulo").css("width",fluidTitulo);
//        $("#rodapeConteudo").css("width",fluidConteudo);
//        $("#rodape").css("width",fluidRodape);

        $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
    }
    $(function(){
        $('.menuHorizontal').each(function(){
            var menu = this;
            $(menu).menu({
                content: $(menu).next().html(),
                flyOut: true
            });
        });
        $('.sanfona > a').click(function(){
            $('.sanfona .sanfonaDiv').each(function(indice, valor) {
                $(valor).hide('fast');
            });
            $(this).next().toggle('fast');
        });
        $("#voltar").click(function(){
            history.go('-1');
        });
    });
    
    $(document).ready(function(){
        $(".nomsg").click(function(){
                alertModalPt(null, 'nomsg', null, 150);
        });
    });
</script>
<!-- ========== IN�CIO MENU ========== -->
<div id='nomsg' style="display:none">Este usu�rio n�o participou da an�lise deste projeto!</div>
<div id="menu">
    <!-- in�cio: conte�do principal #container -->
    <div id="container">
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem')); ?>" title="Ir para Consultar Mensagem">Consultar Mensagem</a>
                <?php //if ( !empty ( $this->BuscarSelect ) ){?>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'incluirmensagem')); ?>" title="Ir para Incluir Mensagem" class="last">Incluir Mensagem</a>
                <?php //} else {?>
                    <!-- <a class="no_seta nomsg" title="Ir para Incluir Mensagem" class="last">Incluir Mensagem</a> -->
                <?php //}?>
            </div>
            <div class="bottom"></div>
            <div id="space_menu"></div>
        </div>

        <!-- final: navega��o local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->