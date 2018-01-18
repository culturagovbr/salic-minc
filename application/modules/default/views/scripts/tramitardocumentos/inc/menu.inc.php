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
            <?php if (in_array($this->grupoAtivo, array(90,109,115))) {
    ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitardocumentos', 'action' => 'despachar')); ?>" title="Ir para Cadastrar documentos">Cadastrar</a>
                <!--<a class="no_seta" href="<?php //echo $this->url(array('controller' => 'tramitardocumentos', 'action' => 'enviar'));?>" title="Ir para Enviar documentos">Enviar</a>-->
            <?php
}?>

            <?php if (in_array($this->grupoAtivo, array(91,97,104,109,115))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitardocumentos', 'action' => 'receber')); ?>" title="Ir para Receber documentos">Receber</a>
            <?php
    }?>

            <?php if (in_array($this->grupoAtivo, array(91,97,104,109,115))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitardocumentos', 'action' => 'anexar')); ?>" title="Ir para Anexar documento">Anexar</a>
            <?php
    }?>

            <?php /*if(in_array($this->grupoAtivo, array(90,91,97,104,115))){ ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitardocumentos', 'action' => 'desanexar')); ?>" title="Ir para Desanexar documento">Desanexar</a>
            <?php }*/?>

            <?php /*if(in_array($this->grupoAtivo, array(90,91,97,104,115))){ ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitardocumentos', 'action' => 'solicitacoes')); ?>" title="Ir para Solicita&ccedil;&otilde;es">Solicita&ccedil;&otilde;es</a>
            <?php }*/ ?>

            <?php if (in_array($this->grupoAtivo, array(90,91,97,104,109,115))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitardocumentos', 'action' => 'guias')); ?>" title="Ir para Guias de Tramita&ccedil;&atilde;o">Guias de Tramita&ccedil;&atilde;o</a>
            <?php
    }?>

            <?php if (in_array($this->grupoAtivo, array(90,91,97,104,109,115))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'tramitardocumentos', 'action' => 'consultar')); ?>" title="Ir para Consultar Tramita&ccedil;&atilde;o">Consultar Tramita&ccedil;&atilde;o</a>
            <?php
    }?>

            </div>
            <div class="bottom"></div>
        </div>

        <!-- final: navegação local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->