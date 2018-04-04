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
    <div id="container">
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
            	<?php if ($this->perfilAtual == 'CoordenadorPRONAC') {
    ?>
					<a class="no_seta" href="<?php echo $this->url(array('controller' => 'consultarpareceristas', 'action' => 'relatoriomensaldepagamento')); ?>" title="Ir para Relat&oacute;rio Mensal de Pagamento">Relat&oacute;rio Mensal de Pagamento</a>
				<?php
}?>
            	<?php if ($this->perfilAtual == 'Parecerista') {
        ?>
                	<a class="no_seta" href="<?php echo $this->url(array('controller' => 'consultarpareceristas', 'action' => 'consultarprodutospareceristas')); ?>" title="Ir para Consultar Produtos do Parecerista">Consultar Produtos do Parecerista</a>
                <?php
    }?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'consultarpareceristas', 'action' => 'consultardadospareceristas')); ?>" title="Ir para Consultar Pareceristas">Consultar dados do Parecerista</a>
                <?php
                if ($this->perfilAtual == 'CoordenadorPRONAC') {
                    ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'consultarpareceristas', 'action' => 'consultarpagamentospareceristas')); ?>" title="Ir para Consultar Pagamento">Consultar Pagamento/Pareceristas</a>
                <?php
                }
                ?>
                
            </div>
            <div class="bottom">
            </div>
            <div id="space_menu">
            </div>
        </div>
        <div id="alertar"></div>
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