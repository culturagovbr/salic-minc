<script type="text/javascript">
    
    function layout_fluido(){
            var janela         = $(window).width();
            var fluidNavGlobal = janela - 245;
            var fluidConteudo  = janela - 253;
            var fluidTitulo    = janela - 252;
            var fluidRodape    = janela - 19;
//            $("#navglobal").css("width", fluidNavGlobal);
//            $("#conteudo").css("width", fluidConteudo);
//            $("#titulo").css("width", fluidTitulo);
//            $("#rodapeConteudo").css("width", fluidConteudo);
//            $("#imagemRodape").css("width", fluidConteudo);
//            $("#rodape").css("width", fluidRodape);
//            $("#conteudo").css("min-height", $('#menuContexto').height()); // altura minima do conteudo
//            $("#rodapeConteudo").css("margin-left", "225px");
//            $(".sanfonaDiv").css("clear", "both");
//            $(".sanfonaDiv").css("width", "91%");
    } // fecha função layout_fluido()

    $(document).ready(function(){
        $('a.sanfona').click(function(){
            $(this).next().toggle('fast');
        });
    });

</script>
<!-- ========== FIM AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->
<div id="menuContexto">
    <div class="top"></div>
    <div id="qm0" class="qmmc">
        <a href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'montagem-edital')); ?>" class="no_seta" >Montagem do Edital</a>
        <a href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'adicionar-texto')); ?>" class="no_seta" >Adicionar Texto</a>
        <!--a href="<?php // echo $this->url(array('controller' => 'edital', 'action' => 'adicionar-referencia'));?>" class="no_seta" >Adicionar Referência</a-->
        <a href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'visualizar-edital')); ?>" class="no_seta" >Visualizar/Finalizar Edital</a>
        <br clear="left" />
    </div>
    <div class="bottom"></div>
</div>
<!-- ========== FIM MENU ========== -->