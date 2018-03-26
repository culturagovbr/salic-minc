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

            $("#navglobal").css("width",fluidNavGlobal);
            $("#conteudo").css("width",fluidConteudo);
            $("#titulo").css("width",fluidTitulo);
            $("#rodapeConteudo").css("width",fluidConteudo);
            $("#rodape").css("width",fluidRodape);

            $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
        }
        </script>

        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'despacharprojetos')); ?>" title="Ir para Cadastrar projetos">Cadastrar</a>
                <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'enviarprojetos')); ?>?projetoEnviado=true" title="Ir para Enviar documentos">Enviar</a>-->
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'receberprojetos')); ?>?projetoRecebido=true" title="Ir para Receber documentos">Receber</a>
                <?php if (in_array($this->grupoAtivo, array(109))) {
    ?>
		<a href="#" title="Abrir menu arquivamento" class="saf">Arquivamento</a>
                <div id="qm0" class="sanfona sanfonaDiv" style="display: none;">
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'arquivar')); ?>" title="Ir para Arquivar">Arquivar</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'desarquivar')); ?>" title="Ir para Desarquivar">Desarquivar</a>
                </div>
                <?php
} ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'consultarprojetosarquivados')); ?>" title="Ir para Consultar projetos arquivados">Consultar Projetos Arquivados</a>
                <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'solicitacoes')); ?>" title="Ir para Solicitações">Solicitações</a>-->
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'guias')); ?>" title="Ir para Guias de tramitação">Guias de Tramitação</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'consultarprojetos'));	?>" title="Ir para Consultar projetos">Consultar Tramitação</a>
                <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'receberprojetos')); ?>" title="Ir para Projetos recebidos">Projetos Recebidos</a>
                <a class="no_seta last" href="<?php echo $this->url(array('controller' => 'tramitarprojetos', 'action' => 'enviarprojetos')); ?>" title="Ir para Projetos enviados">Projetos Enviados</a>-->
            </div>
            <div class="bottom"></div>
            <div id="space_menu"></div>
        </div>

        <div id="alertar"></div>
        <!-- final: navegação local #qm0 -->
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $('.saf').click(function(){
        $('.sanfona .sanfonaDiv').each(function(indice, valor) {
            $(valor).hide('fast');
        });
        $(this).next().toggle('fast');
    });
});
</script>
<!-- ========== FIM MENU ========== -->