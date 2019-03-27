<!-- ========== INÍCIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">

    <!-- início: conteúdo principal #container -->
    <div id="container">

        <!-- início: navegação local #qm0 -->
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

            $(document).ready(function(){

                $(".Desativado").click(function(){

                    $("#produtoDesativado").dialog({
                        title :'Erro',
                        resizable: false,
                        width:400,
                        height:150,
                        modal: true,
                        autoOpen:false,
                        buttons: {
                            'OK': function() {
                                $(this).dialog('close');
                            }
                        }
                    });
                    $("#produtoDesativado").dialog('open');
                });
            });

        </script>

        <style type="text/css">
            .sanfonaDiv {
                clear: both;
            }
        </style>
        <div style="display:none" id="dialog-alert">Solicitação Enviada com Sucesso</div>
        <div style="display:none" id="produtoDesativado">Não há produtos cadastrados para este projeto!</div>
        <div style="display:none" id="pergunta">Seu projeto sofreu <?php echo $this->verificarReadequacao; ?>. Tem certeza que deseja enviar planilha?</div>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">

                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica" title="Comprovar Execu&ccedil;&atilde;o F&iacute;sica" class="no_seta">Comprovar Execu&ccedil;&atilde;o F&iacute;sica</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/relatoriotrimestral" title="Relat&oacute;rio Trimestral" class="no_seta">Relat&oacute;rio Trimestral</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/relatoriofinal/idpronac/119570" title="Relat&oacute;rio Final" class="no_seta">Relat&oacute;rio Final</a>

            </div>
            <div class="bottom"></div>
        </div>
    </div>
</div>
<!-- ========== FIM MENU ========== -->
