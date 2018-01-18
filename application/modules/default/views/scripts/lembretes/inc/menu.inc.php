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

		<?php
        // pega a variável com o id do pronac via get
        $getPronac = isset($_GET['idPronac']) ? $_GET['idPronac'] : $_GET['pronac'];
        ?>

		<div id="menuContexto">
			<div class="top"></div>
			<div id="qm0" class="qmmc">
				<a href="<?php echo $this->url(array('controller' => 'proponente', 'action' => 'index')); ?>?pronac=<?php echo $getPronac; ?>" title="Ir para Dados do Proponente">Dados do Proponente</a>
				<a href="<?php echo $this->url(array('controller' => 'anexardocumentos', 'action' => 'index')); ?>?pronac=<?php echo $getPronac; ?>" title="Ir para Documentos Anexados">Documentos Anexados</a>
				<a href="<?php echo $this->url(array('controller' => 'visualizarhistorico', 'action' => 'index')); ?>?pronac=<?php echo $getPronac; ?>" title="Ir para Histórico">Histórico</a>
				<a class="last" href="<?php echo $this->url(array('controller' => 'diligenciarproponente', 'action' => 'index')); ?>?idPronac=<?php echo $getPronac; ?>" title="Ir para Diligenciar Proponente">Diligenciar Proponente</a>
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
		<!-- final: navegação local #qm0 -->
	</div>
</div>
<!-- ========== FIM MENU ========== -->