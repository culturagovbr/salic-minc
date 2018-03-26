<!-- ========== INÍCIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">

    <!-- início: conteúdo principal #container -->
    <div id="container">

        <!-- início: navegação local #qm0 -->
        <script type="text/javascript">
            function layout_fluido()
            {
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

                $('a.sanfona').click(function()
                {
                    $(this).next().toggle('fast');
                });
            });

        </script>

        <style type="text/css">
            .sanfonaDiv {
                clear: both;
            }
        </style>
        <div style="display:none" id="dialog-alert">Solicitação realizada com sucesso!</div>
        <div style="display:none" id="produtoDesativado">Não há produtos cadastrados para este projeto!</div>
        <div style="display:none" id="pergunta"><?php if ($this->existirPlanilhaCusto == 'ok') {
    ?>Seu projeto sofreu <?php echo $this->verificarReadequacao; ?>.<br /><br /><?php
} ?>Tem certeza que deseja Enviar e Finalizar?</div>
        <div style="display:none" id="validarPlan">Antes de enviar a solicitação é necessário cadastrar os Itens de Custos para os Produtos sem planilha orçamentária!</div>
        <div style="display:none" id="validar15pct"></div>
        <div style="display:none" id="dialog-em-analise">Há pedido de readequação em análise. Favor aguardar.</div>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">

                <?php
                foreach ($this->buscaPlanilhaCusto as $dados) {
                    $idpronac = $dados->IdPRONAC;
                    $tipoPessoa = $dados->TipoPessoa;
                    $idProduto = $dados->idProduto;
                    $idAgente = $dados->idAgente;
                }
                if (empty($this->buscastatus)) {
                    ?>

                    <a href="<?php echo $this->url(array('controller' => 'solicitaralteracao', 'action' => 'acaoprojeto')); ?>?idpronac=<?php echo $_GET['idpronac']; ?>" class="no_seta abrir_fechar4" title="Proponente">Proponente</a>
                    <a id="abrir_projetos" href="<?php echo $this->url(array('controller' => 'solicitarreadequacaodoprojeto', 'action' => 'index')); ?>?idpronac=<?php echo $_GET['idpronac']; ?>" title="Projetos">Projetos</a>

                    <?php
                    if (!empty($this->buscaPlanilhaCusto)) {
                        echo '<a href="#" title="Proponente">Custo</a>';
                        echo '<div class="sanfonaDiv" style="width: 90%;">'; ?>
                        <?php if (sizeof($this->buscaPlanilhaCusto) > 0) {
                            ?>
                            <!-- <a href="#" id="abrir_produto" class="no_seta abrir_fechar4">Custo por Produtos</a> -->
                            <a id="abrir_adm" href="<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'incluirproduto')); ?>?idpronac=<?php echo $idpronac; ?>&idAgente=<?php echo $idAgente; ?>&idPessoa=<?php echo $tipoPessoa; ?>&menu=produtos" title="Custo por Produtos">Custo por Produtos</a>
                            <a id="abrir_adm" href="<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'incluirproduto')); ?>?idpronac=<?php echo $idpronac; ?>&idAgente=<?php echo $idAgente; ?>&idPessoa=<?php echo $tipoPessoa; ?>" title="Custo Administrativo">Custo Administrativo</a>
                        <?php
                        } else {
                            ?>

                            <a href="#" class="Desativado">Custo por Produtos</a>
                            <a href="#" class="Desativado" title="Custo Administrativo">Custo Administrativo</a>

                        <?php
                        } ?>

                        <?php
                    }
                    echo '</div></div>';
                } else {
                    if ($this->buscastatus['stPedidoAlteracao'] == "I") {
                        ?>
                        <a href="#" class="no_seta abrir_fechar4" title="Proponente">Proponente</a>
                        <a id="abrir_projetos" class="no_seta abrir_fechar4" href="#" title="Projetos">Projetos</a>
                        <script type="text/javascript">
                            <!--
                            $(document).ready(function(){
                                alertModal('Alerta!', 'dialog-em-analise', '320', '200', null, '<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')); ?>?idPronac=<?php echo $_GET['idpronac']; ?>', null);
                                return false;
                            });
                            //-->
                        </script>
                        <?php
                        if (!empty($this->buscaPlanilhaCusto)) {
                            echo '<a href="#" title="Proponente" class="no_seta abrir_fechar4">Custo</a>';
                        }
                        echo '</div>';
                    } else {
                        ?>

                        <a href="<?php echo $this->url(array('controller' => 'solicitaralteracao', 'action' => 'acaoprojeto')); ?>?idpronac=<?php echo $_GET['idpronac']; ?>" class="no_seta abrir_fechar4" title="Proponente">Proponente</a>
                        <a id="abrir_projetos" href="<?php echo $this->url(array('controller' => 'solicitarreadequacaodoprojeto', 'action' => 'index')); ?>?idpronac=<?php echo $_GET['idpronac']; ?>&idAgente=<?php echo $idAgente; ?>&idPessoa=<?php echo $tipoPessoa; ?>" title="Projetos">Projetos</a>

                        <?php
                        if (!empty($this->buscaPlanilhaCusto)) {
                            echo '<a href="#" title="Proponente">Custo</a>';
                            echo '<div class="sanfonaDiv" style="width: 90%;">'; ?>
                            <?php if (sizeof($this->buscaPlanilhaCusto) > 0) {
                                ?>
                                <!-- <a href="#" id="abrir_produto" class="no_seta abrir_fechar4">Custo por Produtos</a> -->
                                <a id="abrir_adm" href="<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'incluirproduto')); ?>?idpronac=<?php echo $idpronac; ?>&idAgente=<?php echo $idAgente; ?>&idPessoa=<?php echo $tipoPessoa; ?>&menu=produtos" title="Custo por Produtos">Custo por Produtos</a>
                                <a id="abrir_adm" href="<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'incluirproduto')); ?>?idpronac=<?php echo $idpronac; ?>&idAgente=<?php echo $idAgente; ?>&idPessoa=<?php echo $tipoPessoa; ?>" title="Custo Administrativo">Custo Administrativo</a>
                            <?php
                            } else {
                                ?>

                                <a href="#" class="Desativado">Custo por Produtos</a>
                                <a href="#" class="Desativado" title="Custo Administrativo">Custo Administrativo</a>

                            <?php
                            } ?>

                            <?php
                        }
                        echo '</div></div>';
                    }
                }
                ?>

                <div class="sanfonaDiv"></div>
                <div class="bottom"></div>


                <?php if ($this->buscastatus['stPedidoAlteracao'] == "A") {
                    ?>
                    <div style='background:#f8f8f8;text-align: center'><br /><br /><br />
                        <!--<input id="menunovo"  class="btn" value="Enviar Solicitação" style="text-align: center">-->
                        <input type="button" id="menunovo" class="btn_enviar_solicitacao" />
                    </div>

                <?php
                } ?>


                <div style="padding-top:50px; text-align:center; background:#f8f8f8;">
                    <p>
<!--						<a href="<?php //echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index'));?>?idPronac=<?php //echo $_GET['idpronac'];?>" title="Abrir menu principal">Voltar para o menu principal</a>-->
                    </p>
                </div>


            </div>
            <!-- final: navegação local #qm0 -->
        </div>
    </div>
</div>
<!-- ========== FIM MENU ========== -->

<?php if (isset($_GET['idAgente'])) {
                    ?>

    <script>
        var existirPlanilhaProduto = '<?php echo $this->existirPlanilhaProduto; ?>';
        $(document).ready(function(){
            $('#menunovo').click(function(){
                var idpronac = <?php echo $_GET['idpronac']; ?>;
                var idProduto = '<?php echo isset($_GET['idProduto']) ? $_GET['idProduto'] : 0; ?>';
                var idAgente = '<?php echo $_GET['idAgente']; ?>';
                var idTipoPessoa = '<?php echo $_GET['idPessoa']; ?>';
                var idPedidoAlteracao = <?php echo $this->buscastatus['idPedidoAlteracao']; ?>;
                                            
                var caminho = "<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'validar-percentual')); ?>";
                                            
                $.ajax({ //funcao jquery para enviar os formularios via ajax
                    type: "POST",
                    url: caminho,
                    dataType : 'json',
                    data: {
                        idpronac: idpronac,
                        idProduto: idProduto,
                        idAgente: idAgente,
                        idTipoPessoa: idTipoPessoa,
                        atualizar: "atualiza",
                        acao: "T",                        
                        idPedidoAlteracao : idPedidoAlteracao
                    },
                    success: function(data)
                    {
                        if (data.error) {
                            $('#novas_mensagens').append('<div id="validar-pct">'+data.descricao+'<div>');                               
                            $('#validar-pct').dialog("destroy");
                            $('#validar-pct').dialog
                            ({
                                modal: true,
                                resizable: false,
                                width: 360,
                                height: 180,
                                title: "ALERT!",
                                buttons:
                                    {
                                    "Ok": function()
                                    {
                                    window.location.reload();
                                    }
                                }
                            });
                            return false;
                        } else {

                            if (existirPlanilhaProduto != 'ok') {
                                $("#validarPlan").dialog
                                ({
                                    title : 'Alerta!',
                                    height: 200,
                                    modal: true,
                                    draggable: false,
                                    resizable: false,
                                    closeOnEscape: true,
                                    autoOpen:true,
                                    buttons: {
                                        'Ok': function()
                                        {
                                            $(this).dialog('close');
                                            var idpronac = <?php echo $_GET['idpronac']; ?>;
                                            var idProduto = '<?php echo isset($_GET['idProduto']) ? $_GET['idProduto'] : 0; ?>';
                                            var idAgente = '<?php echo $_GET['idAgente']; ?>';
                                            var idTipoPessoa = '<?php echo $_GET['idPessoa']; ?>';
                                            var idPedidoAlteracao = <?php echo $this->buscastatus['idPedidoAlteracao']; ?>

                                            var caminho = "<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'index')); ?>";
                                            $.ajax({ //funcao jquery para enviar os formularios via ajax
                                                type: "POST",
                                                url: caminho,
                                                dataType : 'json',
                                                data: {
                                                    idpronac: idpronac,
                                                    idProduto: idProduto,
                                                    idAgente: idAgente,
                                                    idTipoPessoa: idTipoPessoa,
                                                    atualizar: "atualiza",
                                                    acao: "T",
                                                    idPedidoAlteracao : idPedidoAlteracao
                                                },
                                                success: function(data)
                                                {
                                                    if(!data.error){
                                                        window.location.reload();
                                                    }
                                                }
                                            });
                                        }}
                                });
                                $('.ui-dialog-titlebar-close').remove();
                            }
                            else {
                                
                                    $("#pergunta").dialog
                                    ({
                                        title : 'Alerta!',
                                        height: 200,
                                        modal: true,
                                        draggable: false,
                                        resizable: false,
                                        closeOnEscape: true,
                                        autoOpen:true,
                                        buttons: {
                                            'Não': function()
                                            {
                                                $(this).dialog('close');
                                                var idpronac = <?php echo $_GET['idpronac']; ?>;
                                                var idProduto = '<?php echo isset($_GET['idProduto']) ? $_GET['idProduto'] : 0; ?>';
                                                var idAgente = '<?php echo $_GET['idAgente']; ?>';
                                                var idTipoPessoa = '<?php echo $_GET['idPessoa']; ?>';
                                                var idPedidoAlteracao = <?php echo $this->buscastatus['idPedidoAlteracao']; ?>

                                                var caminho = "<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'index')); ?>";
                                                $.ajax({ //funcao jquery para enviar os formularios via ajax
                                                    type: "POST",
                                                    url: caminho,
                                                    dataType : 'json',
                                                    data: {
                                                        idpronac: idpronac,
                                                        idProduto: idProduto,
                                                        idAgente: idAgente,
                                                        idTipoPessoa: idTipoPessoa,
                                                        atualizar: "atualiza",
                                                        acao: "T",
                                                        idPedidoAlteracao : idPedidoAlteracao
                                                    },
                                                    success: function(data)
                                                    {
                                                        if(!data.error){
                                                            window.location.reload();
                                                        }
                                                    }
                                                });
                                        
                                            },
                                            'Sim': function()
                                            {
                                                $(this).dialog('close');
                                                var idpronac = <?php echo $_GET['idpronac']; ?>;
                                                var idProduto = '<?php echo isset($_GET['idProduto']) ? $_GET['idProduto'] : 0; ?>';
                                                var idAgente = '<?php echo $_GET['idAgente']; ?>';
                                                var idTipoPessoa = '<?php echo $_GET['idPessoa']; ?>';
                                                var idPedidoAlteracao = <?php echo $this->buscastatus['idPedidoAlteracao']; ?>

                                                var caminho = "<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'index')); ?>";
                                                $.ajax({ //funcao jquery para enviar os formularios via ajax
                                                    type: "POST",
                                                    url: caminho,
                                                    dataType : 'json',
                                                    data: {
                                                        idpronac: idpronac,
                                                        idProduto: idProduto,
                                                        idAgente: idAgente,
                                                        idTipoPessoa: idTipoPessoa,
                                                        atualizar: "atualiza",
                                                        acao: "I",
                                                        idPedidoAlteracao : idPedidoAlteracao
                                                    },
                                                    success: function(data)
                                                    {
                                                        if(!data.error){
                                                            $("#dialog-alert").dialog({
                                                                title:'Alerta',
                                                                resizable: false,
                                                                width:300,
                                                                height:150,
                                                                modal: true,
                                                                autoOpen:true,
                                                                buttons:{'OK':function(){
                                                                        $(this).dialog('close');
                                                                        window.location.reload();
                                                                    }}
                                                            });
                                                            $('.ui-dialog-titlebar-close').remove();
                                                        }
                                                    }
                                                });

                                            }
                                        }
                                    });
                                    $('.ui-dialog-titlebar-close').remove();
                               
                            } // fecha else

                        }
                    }
                                
                }                
            );


            });


                          
        });

    </script>

<?php
                } ?>