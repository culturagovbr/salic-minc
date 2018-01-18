<!-- ========== IN?CIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">

    <!-- in�cio: conte�do principal #container -->
    <div id="container">

        <!-- in�cio: navega��o local  -->
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

        <style type="text/css">
            .sanfonaDiv {
                clear: both;
                display: none;
            }
        </style>
        <div id="corfirma" title="Confirmacao" style='display:none;'></div>
        <div id="ok" title="Confirmacao" style='display:none;'></div>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
            		<a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'listarprojetos')); ?>" title="Ir para Listar Projetos">Listar Projetos</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'infocomplementares')); ?>" title="Ir para Informações Complementares">Informa&ccedil;&otilde;es Complementares</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'localderealizacao')); ?>" title="localderealizacao">Local e Per&iacute;odo de Realiza&ccedil;&atilde;o</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'divulgacao')); ?>" title="contabancaria">Divulga&ccedil;&atilde;o</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'orcamentoprojeto')); ?>" title="contabancaria">Or&ccedil;amento do Projeto</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'detalhesdoprojeto')); ?>" title="contabancaria">Detalhes do Projeto Consolidado</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'tabelaitens')); ?>" title="contabancaria">Consultar Tabela de Itens</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'analiseconteudo')); ?>" title="contabancaria">An&aacute;lise de Conte&uacute;do</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'analisecusto')); ?>" title="contabancaria">An&aacute;lise de Custo</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'providenciatomada')); ?>" title="Ir Ppara providencia tomada">Provid&ecirc;ncia Tomada</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'docanexados')); ?>" title="contabancaria">Documentos Anexados</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'recursoorcamento')); ?>" title="contabancaria">Recurso</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'imprimir')); ?>" title="contabancaria">Imprimir</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'captacao')); ?>" title="contabancaria">Capta&ccedil;&atilde;o</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'contabancaria')); ?>" title="contabancaria">Conta Banc&aacute;ria</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'liberacaodeconta')); ?>" title="contabancaria">Libera&ccedil;&atilde;o de Conta</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'pedidodeprorrogacao')); ?>" title="contabancaria">Pedido de Prorroga&ccedil;&atilde;o</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'documentosrecebidos')); ?>" title="contabancaria">Documentos Recebidos</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'tramitacaoprojetos')); ?>" title="contabancaria">Tramita&ccedil;&atilde;o de Projetos</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'prototipos', 'action' => 'visdiligencias')); ?>" title="contabancaria">Visualizar Dilig&ecirc;ncia</a>

                   <span class="no_seta last">&nbsp;</span>
            </div>
            <div class="bottom"></div>

        <!-- final: navega��o local -->
        </div>
    </div>
</div>
<!-- ========== FIM MENU ========== -->