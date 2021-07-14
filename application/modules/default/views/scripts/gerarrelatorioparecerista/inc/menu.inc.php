<?php
$aguardandoparecerHref          =   $this->url(array('controller' => 'gerarrelatorioparecerista', 'action' => 'aguardandoparecer'));
$pareceremitidoHref             =   $this->url(array('controller' => 'gerarrelatorioparecerista', 'action' => 'pareceremitido'));
$geraldeanaliseHref             =   $this->url(array('controller' => 'gerarrelatorioparecerista', 'action' => 'geraldeanalise'));
$consolidacaopareceristaHref    =   $this->url(array('controller' => 'gerarrelatorioparecerista', 'action' => 'consolidacaoparecerista'));
?>
<!-- ========== INÍCIO MENU ========== -->
        <!-- INÍCIO: navega�?o local #qm0 -->
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

        <a class="no_seta"      href="<?php echo $aguardandoparecerHref;?>"       title="Ir para Projeto aguardando distribui��o">Projeto aguardando distribui��o</a>
        <a class="no_seta"      href="<?php echo $pareceremitidoHref;?>"          title="Ir para Projeto com parecer">Projeto com parecer</a>
        <a class="no_seta"      href="<?php echo $geraldeanaliseHref;?>"         title="Ir para Geral de Análise">Geral de Análise</a>
        <a class="no_seta last" href="<?php echo $consolidacaopareceristaHref;?>" title="Ir para Análises por Pareceristas">Análises por Pareceristas</a>
            <br clear="left" />
    </div>
    <div class="bottom"></div>
</div>

<!-- ========== FIM MENU ========== -->
