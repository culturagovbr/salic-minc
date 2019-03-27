<div id="menu">

    <div id="container">


        <script type="text/javascript">

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
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/parecer-tecnico?idPronac=<?php echo $this->idPronac; ?>" title="Parecer T&eacute;cnico" class="no_seta">Parecer T&eacute;cnico</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/etapas-de-trabalho-final?idPronac=<?php echo $this->idPronac; ?>" title="Etapas de Trabalho" class="no_seta">Etapas de Trabalho</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/local-de-realizacao-final?idPronac=<?php echo $this->idPronac; ?>" title="Local de Realiza&ccedil;&atilde;o" class="no_seta">Local de Realiza&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/plano-de-divulgacao-final?idPronac=<?php echo $this->idPronac; ?>" title="Plano de Divulga&ccedil;&atilde;o" class="no_seta">Plano de Divulga&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/plano-de-distribuicao-final?idPronac=<?php echo $this->idPronac; ?>" title="Plano de Distribui&ccedil;&atilde;o" class="no_seta">Plano de Distribui&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/metas-comprovadas-final?idPronac=<?php echo $this->idPronac; ?>" title="Metas Comprovadas" class="no_seta">Metas Comprovadas</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/itens-comprovados-final?idPronac=<?php echo $this->idPronac; ?>" title="Itens Comprovados" class="no_seta">Itens Comprovados</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/comprovantes-de-execucao-final?idPronac=<?php echo $this->idPronac; ?>" title="Comprovantes de Execu&ccedil;&atilde;o" class="no_seta">Comprovantes de Execu&ccedil;&atilde;o</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/aceite-de-obra-final?idPronac=<?php echo $this->idPronac; ?>" title="Aceite de Obra" class="no_seta">Aceite de Obra</a>
                <a href="<?php echo $this->baseUrl(); ?>/comprovacao-objeto/avaliaracompanhamentoprojeto/bens-final?idPronac=<?php echo $this->idPronac; ?>" title="Bens Doados" class="no_seta">Bens Doados</a>
                <a href="
                    <?php
                    echo $this->url(
                            array(
                                'module' => 'comprovacao-objeto',
                                'controller' => 'avaliaracompanhamentoprojeto',
                                'action' => 'recursos-por-fonte',
                                'idPronac' => $this->idPronac,
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
