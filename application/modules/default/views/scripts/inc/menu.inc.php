<!-- ========== INCIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">

    <!-- incio: contedo principal #container -->
    <div id="container">

        <!-- incio: navegao local #qm0 -->
        <script type="text/javascript">
            function layout_fluido()
            {
                var janela = $(window).width();

                var fluidNavGlobal = janela - 245;
                var fluidConteudo = janela - 253;
                var fluidTitulo = janela - 252;
                var fluidRodape = janela - 19;

//                $("#navglobal").css("width",fluidNavGlobal);
//                $("#conteudo").css("width",fluidConteudo);
//                $("#titulo").css("width",100%);
//                $("#rodapeConteudo").css("width",fluidConteudo);
//                $("#rodape").css("width",fluidRodape);

                $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
            }
        </script>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
                <a href="#" title="Orcamento">Listar Propostas</a>
                <a href="#" title="Orcamento">Or&ccedil;amento</a>
                <div style="display: none;">
                    <a href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'Manterorcamento', 'action' => 'produtoscadastrados')); ?>" title="Listar Produtos Cadastrados">Listar Produtos Cadastrados</a>
                    <a href='<?php echo $this->url(array('module' => 'proposta', 'controller' => 'Manterorcamento', 'action' => 'custosadministrativos')); ?>'>Custos Administrativos</a>
                    <a href='<?php echo $this->url(array('module' => 'proposta', 'controller' => 'Manterorcamento', 'action' => 'planilhaorcamentariageral')); ?>'>Planilha Or&ccedil;ament&aacute;ria Geral</a>
                </div>    
                <a href="#" title="Orcamento">Enviar Proposta ao Minc</a>  
            </div>
            <div class="bottom"></div>
        </div>
    </div>
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
<!-- final: navegao local #qm0 -->

<!-- ========== FIM MENU ========== -->