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

//			$("#navglobal").css("width",fluidNavGlobal);
//			$("#conteudo").css("width",fluidConteudo);
//			$("#titulo").css("width",100$);
//			$("#rodapeConteudo").css("width",fluidConteudo);
//			$("#rodape").css("width",fluidRodape);

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
			<div id="qm0" class="qmmc sanfona">
				<a href="#">Menu 1</a>
				<div class="sanfonaDiv">
					<a href="#">Sub-menu 1.1</a>
					<a href="#">Sub-menu 1.2</a>
					<a href="#">Sub-menu 1.3</a>
					<a href="#">Sub-menu 1.4</a>
					<a href="#">Sub-menu 1.5</a>
					<a href="#">Sub-menu 1.6</a>
					<a href="#">Sub-menu 1.7</a>
				</div>
				<a href="#">Menu 2</a>
				<div class="sanfonaDiv">
					<a href="#">Sub-menu 2.1</a>
					<a href="#">Sub-menu 2.2</a>
					<a href="#">Sub-menu 2.3</a>
					<a href="#">Sub-menu 2.4</a>
					<a href="#">Sub-menu 2.5</a>
					<a href="#">Sub-menu 2.6</a>
				</div>
				<a href="#">Menu 3</a>
				<div class="sanfonaDiv">
					<a href="#">Sub-menu 3.1</a>
					<a href="#">Sub-menu 3.2</a>
					<a href="#">Sub-menu 3.3</a>
					<a href="#">Sub-menu 3.4</a>
				</div>
				<a href="#" class="last">Menu 4</a>
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
		<!-- final: navega��o local #qm0 -->
	</div>
</div>
<!-- ========== FIM MENU ========== -->