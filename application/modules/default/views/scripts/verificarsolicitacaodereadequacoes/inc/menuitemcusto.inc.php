<?php if (isset($_GET['idPronac']) && !empty($_GET['idPronac'])) {
    ?>
<!-- ========== INÍCIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>

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
	$("#rodapeConteudo").css("margin-left", "225px");
	$(".sanfonaDiv").css("clear", "both");
	$(".sanfonaDiv").css("width", "91%");
} // fecha função layout_fluido()

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

<!-- início: navegação local #qm0 -->
<script type="text/javascript">
$(function(){
   carregaPagina();
});

function carregaPagina(){
    $.get(
        '<?php echo $this->url(array('controller' => 'checarregularidade', 'action' => 'index')); ?>',
        {
        	CgcCpf : <?php echo $this->verificarProponenteProjeto($_GET['idPronac']); ?>},
            function(data){
                $('#UC27').html(data)
            }
        );
   }
</script>


<div id="menuContexto" style="margin-bottom: 15px;">
    <div class="top"></div>
    <div id="qm0" class="qmmc">
    	<a class="no_seta last" onclick="redirecionar('<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaanalista')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>&tpDiligencia=179&situacao=<?php echo $this->verificarSituacaoProjeto($_GET['idPronac']); ?>');" href="#" title="Ir para Projetos Diligenciados">Diligenciar</a>
    </div>
    <div class="bottom"></div>
    <div id="alertar"></div>
    <div id="espaco">
		<br /><br />
		<div id="UC27" style="width:180px; height:280px; margin-left: 10px;"></div>
        <!-- <table style="width:16.8em; height:13em; margin: 1.5em 0em 0em 1em; border: 0;">
            <tr>
                <th colspan="2"><span style="font-weight: normal; font-size: 10px">TIPO DE SOLICITAÇÃO</span></th>
            </tr>
            <tr>
                <td width="1%"; style="font-size: 40px; color: blue;">&bull;</td>
                <td width="99%" style="text-align:left;" >Readequação de itens do produto</td>
            </tr>
            <tr>
                <td style="font-size: 40px; color: green;">&bull;</td>
                <td style="text-align:left; padding-left:3%;">Inclusão de novo produto</td>
            </tr>
            <tr>
                <td style="font-size: 40px; color: red;">&bull;</td>
                <td style="text-align:left;">Exclusão de produto</td>
            </tr>
            <tr>
                <td style="font-size: 40px; color: black;">&bull;</td>
                <td style="text-align:left; padding-left:3%;">Nenhuma solicitação de alteração</td>
            </tr>
        </table> -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->
<?php
} ?>