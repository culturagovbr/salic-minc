<?php if ($this->dados) {
    ?>
<!-- ========== INÍCIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>

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

<!-- início: navegação local #qm0 -->
<script type="text/javascript">
    $(function(){
        carregaPagina();
    });

    function carregaPagina(){
        $.get(
            '<?php echo $this->url(array('controller' => 'checarregularidade', 'action' => 'index')); ?>',
            { CgcCpf : '<?php echo $this->dadosTopo['CNPJCPF']; ?>'},
            function(data){
                $('#UC27').html(data)
            }
        );
    }
</script>


<div id="menuContexto" style="margin-bottom: 15px;">
    <div id="alertar"></div>
    <div id="espaco">
        <br /><br />
        <div id="UC27" style="width:180px; height:280px; margin-left: 10px;"></div>
    </div>
</div>
<!-- ========== FIM MENU ========== -->
<?php
} ?>