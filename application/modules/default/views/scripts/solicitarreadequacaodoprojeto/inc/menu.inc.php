<!-- ========== INÍCIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">
    <div style="display:none" id="pergunta"><?php if ($this->existirPlanilhaCusto == 'ok') {
    ?>Seu projeto sofreu <?php echo $this->verificarReadequacao; ?>.<br /><br /><?php
} ?>Tem certeza que deseja Enviar e Finalizar?</div>
    <div style="display:none" id="validarPlan">Antes de enviar a solicitação é necessário cadastrar os Itens de Custos para os Produtos sem planilha orçamentária!</div>
    <div style="display:none" id="dialog-alerta">Solicitação realizada com sucesso!</div>
    <div style="display:none" id="dialog-em-analise">Há pedido de readequação em análise. Favor aguardar.</div>

    <!-- início: conteúdo principal #container -->
    <div id="container">

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

            $(document).ready(function()
            {
                $('a.sanfona').click(function()
                {
                    $(this).next().toggle('fast');
                });
            });
        </script>

        <?php
        $resultado = $this->buscaprojeto;
        $idPronac = $this->escape($resultado[0]->IdPRONAC);

        $menu = SolicitarreadequacaodoprojetoController::Menu($idPronac);

        //Zend_Debug::dump($menu);die;

        if ($menu == "Sem Menu") {
            ?>
            <script type="text/javascript">
                <!--
                $(document).ready(function(){
                    alertModal('Alerta!', 'dialog-em-analise', '320', '200', null, '<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')); ?>?idPronac=<?php echo $_GET['idpronac']; ?>', null);
                    return false;
                });
                //-->
            </script>
            <div id="menuContexto">
                <div class="top"></div>
                <div id="qm0" class="qmmc sanfona">
                    <a href="#" class="no_seta abrir_fechar4" title="Proponente">Proponente</a>
                    <div class="sanfonaDiv" style="display:none; width: 91%;"></div>
                    <a href="#" title="Projetos" class="no_seta abrir_fechar4">Projetos</a>
                    <div class="sanfonaDiv" style="display:none; width: 91%;"></div>
                    <a href='#' title="Custo" class="no_seta abrir_fechar4">Custo</a>
                    <div class="sanfonaDiv" style="display:none; width: 91%;"></div>
                </div>
                <div class="sanfonaDiv"></div>
                <div class="bottom"></div>


                <div style="padding-top:50px; text-align:center; background:#f8f8f8;">
                    <p>
    <!--					<a href="<?php //echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')) . '?idPronac=' . $_GET['idpronac']?>" title="Abrir menu principal">Voltar para o menu principal</a>-->
                    </p>
                </div>
            </div>
        <?php
        } ?>

        <?php if ($menu == "Com Menu" || $menu == "Botão") {
            ?>
            <div id="menuContexto">
                <div class="top"></div>
                <div id="qm0" class="qmmc">
                    <a href="<?php echo $this->url(array('controller' => 'solicitaralteracao', 'action' => 'acaoprojeto')); ?>?idpronac=<?php echo $idPronac; ?>" class="no_seta abrir_fechar4" title="Proponente">Proponente</a>
                    <div class="sanfonaDiv" style="display:none; width: 91%;"></div>
                    <a href="#" id="botaoProjetosConfigura" title="Projetos" class="abrir_fechar4">Projetos</a>
                    <div class="sanfonaDiv" style="width: 90%;">
                        <a href='#' id='abrir_fechar'>Produtos</a>
                        <a href='#' class='abrir_fechar5'>Ficha Técnica</a>
                        <a href='#' class='abrir_fechar6'>Local de Realização</a>
                        <a href='#' class='abrir_fechar7'>Nome do Projeto</a>
                        <a href='#' class='abrir_fechar8'>Prorrogar Prazos de Execução</a>
                        <a href='#' class='abrir_fechar9'>Prorrogar Prazos de Captação</a>
                        <a href='#' id='abrir_fechar2'>Proposta Pedagógica</a>
                    </div>
                    <?php if (count($this->buscaPlanilhaCusto) > 0) {
                ?>
                        <a href='<?php echo $this->url(array('controller' => 'solicitarreadequacaocusto', 'action' => 'index')); ?>?idpronac=<?php echo $idPronac; ?>' title="Custo">Custo</a>
                    <?php
            } ?>
                </div>
                <div class="sanfonaDiv"></div>
                <div class="bottom"></div>

                <?php if ($menu == "Botão") {
                ?>
                    <div style='background:#f8f8f8; padding-top: 3.5em;'>
                        <ul id='menuGerenciar' style="border:0; width:auto;">
                            <li style="text-align: center;">
                                <form name="Produto" action="<?php echo $this->url(array('controller' => 'solicitarreadequacaodoprojeto', 'action' => 'incluirproduto')); ?>" method="post" style="text-align:center; margin:auto; width:auto; padding-left:3em;">
                                    <!--<input type="button" id="menuFinal" value="Enviar Solicitação" class="btn" />-->
                                    <input type="button" id="menuFinal" class="btn_enviar_solicitacao" />
                                    <input type="hidden" name="idpronac " value="<?php echo $idPronac; ?>" />
                                </form>
                            </li>
                        </ul>
                    </div>
                <?php
            } ?>

                <div style="padding-top:50px; text-align:center; background:#f8f8f8;">
                    <p>
    <!--					<a href="<?php //echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')) . '?idPronac=' . $_GET['idpronac']?>" title="Abrir menu principal">Voltar para o menu principal</a>-->
                    </p>
                </div>
            </div>
        <?php
        } ?>


        <?php /* if($menu=="Botão"){ ?>
          <div id="menuContexto">
          <div class="top"></div>
          <div id="qm0" class="qmmc sanfona">
          <a href="#" class="no_seta abrir_fechar4" title="Proponente<">Proponente</a>
          <div class="sanfonaDiv" style="display:none; width: 91%;"></div>
          <a href="#" title="Projetos" class="no_seta abrir_fechar4">Projetos</a>
          <div class="sanfonaDiv" style="display:none; width: 91%;"></div>
          <a href='#' title="Custo" class="no_seta abrir_fechar4">Custo</a>
          <div class="sanfonaDiv" style="display:none; width: 91%;"></div>
          </div>
          <div class="sanfonaDiv"></div>
          <div class="bottom"></div>
          <div style='background:#f8f8f8; padding-top: 3.5em;'>
          <ul id='menuGerenciar' style="border:0; width:auto;">
          <li style="text-align: center;">
          <form name="Produto" action="<?php echo $this->url(array('controller' => 'solicitarreadequacaodoprojeto','action' => 'incluirproduto')); ?>" method="post" style="text-align:center; margin:auto; width:auto; padding-left:3em;">
          <input type="button" id="menuFinal" value="Enviar Solicitação" class="btn" />
          <input type="hidden" name="idpronac " value="<?php echo $idPronac; ?>" />
          </form>
          </li>
          </ul>
          </div>


          <div style="padding-top:50px; text-align:center; background:#f8f8f8;">
          <p>
          <!--					<a href="<?php //echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index')) . '?idPronac=' . $_GET['idpronac'] ?>" title="Abrir menu principal">Voltar para o menu principal</a>-->
          </p>
          </div>
          </div>
          <?php } */ ?>

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
<script>
    var existirPlanilhaProduto = '<?php echo $this->existirPlanilhaProduto; ?>';
    $(document).ready(function(){


        $("#menuFinal").click(function(){
            var idpronac = <?php echo $_GET['idpronac']; ?>;   
            var idPedidoAlteracao = <?php echo $this->buscastatus['idPedidoAlteracao']; ?>;
                                            
            var caminho = "<?php echo $this->url(array('controller' => 'solicitaralteracao', 'action' => 'validar-percentual')); ?>";
                                            
            $.ajax({ //funcao jquery para enviar os formularios via ajax
                type: "POST",
                url: caminho,
                dataType : 'json',
                data: {
                    idpronac: idpronac,
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
                                        var idpronac = <?php echo $idPronac; ?>;


                                        var caminho = "<?php echo $this->url(array('controller' => 'solicitarreadequacaodoprojeto', 'action' => 'incluirproduto')); ?>";
                                        $.ajax({ //funcao jquery para enviar os formularios via ajax
                                            type: "POST",
                                            url: caminho,
                                            //dataType : 'json',
                                            data: {
                                                idpronac: idpronac,
                                                acao: "T"

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
                        } else {

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
                                        var idpronac = <?php echo $idPronac; ?>;


                                        var caminho = "<?php echo $this->url(array('controller' => 'solicitarreadequacaodoprojeto', 'action' => 'incluirproduto')); ?>";
                                        $.ajax({ //funcao jquery para enviar os formularios via ajax
                                            type: "POST",
                                            url: caminho,
                                            //dataType : 'json',
                                            data: {
                                                idpronac: idpronac,
                                                acao: "T"

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
                                        var idpronac = <?php echo $idPronac; ?>;


                                        var caminho = "<?php echo $this->url(array('controller' => 'solicitarreadequacaodoprojeto', 'action' => 'incluirproduto')); ?>";
                                        $.ajax({ //funcao jquery para enviar os formularios via ajax
                                            type: "POST",
                                            url: caminho,
                                            //dataType : 'json',
                                            data: {
                                                idpronac: idpronac,
                                                acao: "I"
                                            },
                                            success: function(data)
                                            {
                                
                                                $("#dialog-alerta").dialog({
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
                           
                                        });

                                    }
                                }
                            });
                            $('.ui-dialog-titlebar-close').remove();

                        }
                    
                    }
                }
            });
            
        });



    });

</script>

<!-- final: navegação local #qm0 -->
<!-- ========== FIM MENU ========== -->