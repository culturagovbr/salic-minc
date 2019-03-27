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
        
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/etapas-de-trabalho-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Etapas de Trabalho" class="no_seta">Etapas de Trabalho</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/local-de-realizacao-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Local de Realiza&ccedil;&atilde;o" class="no_seta">Local de Realiza&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/plano-de-divulgacao-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Plano de Divulga&ccedil;&atilde;o" class="no_seta">Plano de Divulga&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/plano-de-distribuicao-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Plano de Distribui&ccedil;&atilde;o" class="no_seta">Plano de Distribui&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/metas-comprovadas-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Metas Comprovadas" class="no_seta">Metas Comprovadas</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/itens-comprovados-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Itens Comprovados" class="no_seta">Itens Comprovados</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/comprovantes-de-execucao-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Comprovantes de Execu&ccedil;&atilde;o" class="no_seta">Comprovantes de Execu&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/aceite-de-obra-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Aceite de Obra" class="no_seta">Aceite de Obra</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/bens-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Bens Doados" class="no_seta">Bens Doados</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/comprovarexecucaofisica/enviar-relatorio-final?idpronac=<?php echo Seguranca::encrypt($this->idPronac); ?>" title="Enviar Relat&oacute;rio" class="no_seta">Enviar Comprova&ccedil;&atilde;o Financeira e do Objeto</a>
            </div>
            <div class="bottom"></div>
        </div>
    </div>
</div>
<!-- ========== FIM MENU ========== -->
