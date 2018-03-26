<?php 


$formapagamentoHref         =   $this->url(array('controller' => 'cadastraredital', 'action' => 'formapagamento'))."?nrFormDocumento=".$_GET['nrFormDocumento']."&nrVersaoDocumento=".$_GET['nrVersaoDocumento']."&idEdital=".$_GET['idEdital']."&idUsuario=".$_GET['idUsuario'];
//http://localhost/NovoSalic/cadastraredital/formapagamento?nrFormDocumento=765&nrVersaoDocumento=1&idUsuario=236

$criteriosavaliacaoHref     =   $this->url(array('controller' => 'cadastraredital', 'action' => 'criteriosavaliacao'))."?nrFormDocumento=".$_GET['nrFormDocumento']."&nrVersaoDocumento=".$_GET['nrVersaoDocumento']."&idEdital=".$_GET['idEdital']."&idUsuario=".$_GET['idUsuario'];
//http://localhost/NovoSalic/cadastraredital/criteriosavaliacao?nrFormDocumento=765&nrVersaoDocumento=1&idUsuario=236&idEdital=771

$propostacustomizavelHref   =   $this->url(array('controller' => 'cadastraredital', 'action' => 'propostacustomizavel'))."?nrFormDocumento=".$_GET['nrFormDocumento']."&nrVersaoDocumento=".$_GET['nrVersaoDocumento']."&idEdital=".$_GET['idEdital']."&idUsuario=".$_GET['idUsuario'];
//http://localhost/NovoSalic/cadastraredital/propostacustomizavel?nrFormDocumento=765&idEdital=771&nrVersaoDocumento=1

$dadosgeraisHref            =   $this->url(array('controller' => 'cadastraredital', 'action' => 'dadosgerais'))."?nrFormDocumento=".$_GET['nrFormDocumento']."&nrVersaoDocumento=".$_GET['nrVersaoDocumento']."&idEdital=".$_GET['idEdital']."&idUsuario=".$_GET['idUsuario'];
//http://localhost/NovoSalic/cadastraredital/dadosgerais

$vinculareditaisHref            =   $this->url(array('controller' => 'cadastraredital', 'action' => 'acessaravaliador'))."?nrFormDocumento=".$_GET['nrFormDocumento']."&nrVersaoDocumento=".$_GET['nrVersaoDocumento']."&idEdital=".$_GET['idEdital']."&idUsuario=".$_GET['idUsuario'];


?>
<!-- ========== IN�CIO MENU ========== -->

<!-- ========== IN�CIO AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->
<script type="text/javascript">
<!--
function layout_fluido()
{
	var janela         = $(window).width();
	var fluidNavGlobal = janela - 245;
	var fluidConteudo  = janela - 253;
	var fluidTitulo    = janela - 252;
	var fluidRodape    = janela - 19;
//	$("#navglobal").css("width", fluidNavGlobal);
//	$("#conteudo").css("width", fluidConteudo);
//	$("#titulo").css("width", fluidTitulo);
//	$("#rodapeConteudo").css("width", fluidConteudo);
//	$("#imagemRodape").css("width", fluidConteudo);
//	$("#rodape").css("width", fluidRodape);
//	$("#conteudo").css("min-height", $('#menuContexto').height()); // altura minima do conteudo
//	$("#rodapeConteudo").css("margin-left", "225px");
//	$(".sanfonaDiv").css("clear", "both");
//	$(".sanfonaDiv").css("width", "91%");
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
<div id="menuContexto">
    <div class="top"></div>
    <div id="qm0" class="qmmc">
        <a class="no_seta"      href="<?php echo $dadosgeraisHref;?>"       title="Ir para Dados do Edital">Dados do Edital</a>
        <a class="no_seta"      href="<?php echo $criteriosavaliacaoHref;?>"       title="Ir para Formulario de Elabora�&atilde;o de Crit�rios de Avalia�&atilde;o">Crit�rios de Avalia�&atilde;o</a>
        <a class="no_seta"      href="<?php echo $formapagamentoHref;?>"          title="Ir para Formulario de Elabora�&atilde;o de Forma de Pagamento">Forma de Pagamento</a>
        <a class="no_seta"      href="<?php echo $propostacustomizavelHref?>"         title="Ir para Formulario de Elabora�&atilde;o de Proposta Customiz�vel">Proposta Customiz�vel</a>
        <a class="no_seta last"      href="<?php echo $vinculareditaisHref?>"         title="Ir para Vincular Avaliador">Vincular Avaliador</a>
            <br clear="left" />
    </div>
    <div class="bottom"></div>
</div>
         
<!-- ========== FIM MENU ========== -->