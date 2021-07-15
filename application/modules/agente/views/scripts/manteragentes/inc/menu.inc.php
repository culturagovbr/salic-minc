<!-- ========== INICIO MENU ========== -->
<style type="text/css">
		.sanfonaDiv {
			clear: both;
			display: none;
		}
</style>
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

	<!-- inicio: conteudo principal #container -->
	<div id="container">

		<!-- inicio: navegacao local #qm0 -->
		<script type="text/javascript">
		function layout_fluido()
		{
			var janela = $(window).width();

			var fluidNavGlobal = janela - 245;
			var fluidConteudo = janela - 253;
			var fluidTitulo = janela - 252;
			var fluidRodape = janela - 19;

//			$("#navglobal").css("width",fluidNavGlobal);
//			$("#conteudo").css("width",fluidConteudo);
//			$("#titulo").css("width",100%);
//			$("#rodapeConteudo").css("width",fluidConteudo);
//			$("#rodape").css("width",fluidRodape);

			$("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
		}
		</script>

		<div id="menuContexto">
			<div class="top"></div>
			<div id="qm0" class="qmmc">
				<a class ="no_seta" href="<?php echo $this->url(array('controller' => 'manteragentes', 'action' => 'buscaragente')); ?>" title="Ir para Localizar Agente">Pesquisar Agentes</a>
				<a class ="no_seta" href="<?php echo $this->url(array('controller' => 'manteragentes', 'action' => 'agentes')); ?>?acao=cc" title="Ir para Incluir Agente" >Incluir Agente</a>
                                <a class ="no_seta" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'vincularresponsavel', 'action' => 'index')); 	?>" title="Ir para Vincular Respons&aacute;vel">	Vincular Responsável </a>
                                <a class ="no_seta" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'vincularresponsavel', 'action' => 'desvincularresponsavel')); 		?>" title="Ir para Desvincular Respons&aacute;vel" >		Desvincular Responsável</a>
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
		<!-- final: navegacao local #qm0 -->
	</div>
</div>
<!-- ========== FIM MENU ========== -->
