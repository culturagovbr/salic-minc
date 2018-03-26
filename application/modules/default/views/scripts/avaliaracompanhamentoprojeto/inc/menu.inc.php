<!-- ========== IN�CIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">

    <!-- in�cio: conte�do principal #container -->
    <div id="container">

        <!-- in�cio: navega��o local #qm0 -->
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
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/parecer-tecnico?idpronac=<?php echo $this->idPronac; ?>" title="Parecer T&eacute;cnico" class="no_seta">Parecer T&eacute;cnico</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/etapas-de-trabalho-final?idpronac=<?php echo $this->idPronac; ?>" title="Etapas de Trabalho" class="no_seta">Etapas de Trabalho</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/local-de-realizacao-final?idpronac=<?php echo $this->idPronac; ?>" title="Local de Realiza&ccedil;&atilde;o" class="no_seta">Local de Realiza&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/plano-de-divulgacao-final?idpronac=<?php echo $this->idPronac; ?>" title="Plano de Divulga&ccedil;&atilde;o" class="no_seta">Plano de Divulga&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/plano-de-distribuicao-final?idpronac=<?php echo $this->idPronac; ?>" title="Plano de Distribui&ccedil;&atilde;o" class="no_seta">Plano de Distribui&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/metas-comprovadas-final?idpronac=<?php echo $this->idPronac; ?>" title="Metas Comprovadas" class="no_seta">Metas Comprovadas</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/itens-comprovados-final?idpronac=<?php echo $this->idPronac; ?>" title="Itens Comprovados" class="no_seta">Itens Comprovados</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/comprovantes-de-execucao-final?idpronac=<?php echo $this->idPronac; ?>" title="Comprovantes de Execu&ccedil;&atilde;o" class="no_seta">Comprovantes de Execu&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/aceite-de-obra-final?idpronac=<?php echo $this->idPronac; ?>" title="Aceite de Obra" class="no_seta">Aceite de Obra</a>
                <a href="<?php echo $this->baseUrl(); ?>/avaliaracompanhamentoprojeto/bens-final?idpronac=<?php echo $this->idPronac; ?>" title="Bens Doados" class="no_seta">Bens Doados</a>
                <a href="
                    <?php
                    echo $this->url(
                            array(
                                'controller' => 'avaliaracompanhamentoprojeto',
                                'action' => 'recursos-por-fonte',
                                'idpronac' => $this->idPronac,
                            ),
                            null,
                            true
                            );
                    ?>
                   "
                   title="Recusos por Fonte"
                   class="no_seta">
                    Recursos por fonte
                </a>
            </div>
            <div class="bottom"></div>
        </div>
    </div>
</div>
<!-- ========== FIM MENU ========== -->
