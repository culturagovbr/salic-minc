<?php
/**
 * Exemplo Menu Lateral
 * @author emanuel.sampaio - Politec
 * @since 27/05/2011
 * @version 1.0
 * @package application
 * @subpackage application.views.scripts.manual-legado.inc
 * @link http://salic.cultura.gov.br
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 */
?>

<!-- ========== INÍCIO AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->
<script type="text/javascript">
    function layout_fluido(){
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

    $(document).ready(function(){
        $('a.sanfona').click(function(){
            $(this).next().toggle('fast');
        });
    });
</script>
<!-- ========== FIM AJUSTE DO LAYOUT PARA ACOPLAR MENU LATERAL ========== -->


<!-- ========== INÍCIO MENU ========== -->
<div id="menuContexto">
    <div class="top"></div>
    <div id="qm0" class="qmmc">
        <a class="no_seta" TARGET="_blank" href="<?php echo $this->url(array('controller' => 'mantermensagens', 'action' => 'consultarmensagem'), '', true); ?>?idpronac=<?php echo $this->projeto->IdPRONAC; ?>" title="Ir para Consulta">Consultar Mensagem</a>
        <br clear="all" />
        <?php if (($this->pscount == 0) && ($this->stPrincipal == 1) && ($this->countAnalizado == 0) && ($this->countEnquadramentoP != 0) && ($this->countParecerP != 0) && ($this->dilig == 0)):?>
        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'Analisarprojetoparecer', 'action' => 'fecharparecer', 'idPronac'=> $this->idPronac, 'idD'=> $this->idD, 'idP' => $this->idProduto), '', true); ?>" title="Ir para Consulta">Concluir análise</a>
        <?php elseif (($this->stPrincipal == 0) && ($this->countAnalizado == 0) && ($this->dilig == 0)):?>
        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'Analisarprojetoparecer', 'action' => 'fecharparecer', 'idPronac'=> $this->idPronac, 'idD'=> $this->idD, 'idP' => $this->idProduto), '', true); ?>" title="Ir para Consulta">Concluir análise</a>
        <?php endif; ?>
        <br clear="all" />
    </div>

    <br clear="left" class="br" />
    <div class="bottom"></div>
    <div id="espaco"></div>
</div>
<!-- ========== FIM MENU ========== -->