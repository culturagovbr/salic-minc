<?php
$IdPronac = (!empty($_GET['idPronac'])) ? $_GET['idPronac'] : null;

$pronac = (!empty($this->projeto->NrProjeto)) ? $this->projeto->NrProjeto : null;
?>

<!-- ========== INÍCIO AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->
<script type="text/javascript">
<!--
function layout_fluido()
{
	var janela         = $(window).width();
	var fluidNavGlobal = janela - 245;
	var fluidConteudo  = janela - 253;
	var fluidTitulo    = janela - 252;
	var fluidRodape    = janela - 19;
	$("#navglobal").css("width", fluidNavGlobal);
	$("#conteudo").css("width", fluidConteudo);
	$("#titulo").css("width", fluidTitulo);
	$("#rodapeConteudo").css("width", fluidConteudo);
	$("#imagemRodape").css("width", fluidConteudo);
	$("#rodape").css("width", fluidRodape);
	$("#conteudo").css("min-height", $('#menuContexto').height()); // altura minima do conteudo
	$('#menuContexto').css("background", "none"); // retira o fundo do menu
	$("#rodapeConteudo").css("margin-left", "225px");
	$(".sanfonaDiv").css("clear", "both");
	$(".sanfonaDiv").css("width", "91%");
} // fecha fun��o layout_fluido()

$(document).ready(function()
{
	$('a.sanfona').click(function()
	{
		$(this).next().toggle('fast');
	});
});
//-->
</script>
<!-- ========== FIM AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->

<!-- ========== INÍCIO MENU ========== -->
<div id="menuContexto">

    <table class="tabela">
        <tr>
            <th colspan="2" class="centro">Legenda</th>
        </tr>
        <tr>
            <td style="font-size: 40px; color: blue;">&bull;</td>
            <td class="no_seta esquerda">< 10 dias para t�rmino do prazo da Análise do projeto</td>
        </tr>
        <tr>
            <td style="font-size: 40px; color: green;">&bull;</td>
            <td class="no_seta esquerda">< 10 dias de atraso no recebimento da Solicitação (data inicial).</td>
        </tr>
        <tr>
            <td style="font-size: 40px; color: red;">&bull;</td>
           <td class="no_seta esquerda">>= 20 dias de atraso no recebimento da Solicitação (data inicial)</td>
        </tr>
        <tr>
            <td style="font-size: 40px; color: #FF6600;">&bull;</td>
            <td class="no_seta esquerda">>= 10 e < 20 dias de atraso no recebimento da Solicitação (data inicial)</td>
        </tr>
    </table>

</div>
<!-- ========== FIM MENU ========== -->
