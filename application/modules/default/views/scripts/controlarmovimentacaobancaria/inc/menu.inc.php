<!-- ========== IN�CIO MENU ========== --> 
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>


<div id="menu">
    <!-- in�cio: conte�do principal #container -->
    <div id="container">

        <!-- in�cio: navega��o local #qm0 -->
        <script type="text/javascript">
            function layout_fluido(){
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
                
                $('.relatorio').click(function(){
                    $(this).next().toggle('fast');
                });
            });

        </script>

        <style type="text/css">
            .sanfonaDiv {
                clear: both;
            }
        </style>
        <div style="display:none" id="dialog-alert">Solicita&ccedil;&atilde;o Enviada com Sucesso</div>
        <div style="display:none" id="produtoDesativado">N&atilde;o h&aacute; produtos cadastrados para este projeto!</div>
        <div style="display:none" id="pergunta">Seu projeto sofreu <?php echo $this->verificarReadequacao; ?>. Tem certeza que deseja enviar planilha?</div>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
                <a href="<?php echo $this->url(array('controller' => 'movimentacaodeconta', 'action' => 'resultado-extrato-de-conta-captacao'), '', true); ?>" title="Ir para Relat&oacute;rio de extrato de conta capta&ccedil;&atilde;o" class="no_seta">Transfer&ecirc;ncia de Recurso</a>
                <a href="<?php echo $this->url(array('controller' => 'liberarcontabancaria', 'action' => 'index'), '', true); ?>" title="Ir para Liberar Conta Bancaria" class="no_seta">Liberar conta banc&aacute;ria</a>
                <a href="<?php echo $this->url(array('controller' => 'mantercontabancaria', 'action' => 'consultar'), '', true); ?>" title="Ir para Manter Conta Banc&aacute;ria" class="no_seta">Manter conta banc&aacute;ria</a>
                <a href="<?php echo $this->url(array('controller' => 'captacao', 'action' => 'index'), '', true); ?>" title="Ir para Registrar Capta&ccedil;&atilde;o" class="no_seta">Registrar Capta&ccedil;&atilde;o</a>
                <a href="<?php echo $this->url(array('controller' => 'controlarmovimentacaobancaria', 'action' => 'upload'), '', true); ?>" title="Ir para Arquivo da Conta Movimenta&ccedil;&atilde;o" class="no_seta">Atualizar arquivo de conta movimento</a>
                <a href="<?php echo $this->url(array('controller' => 'movimentacaodeconta', 'action' => 'upload'), '', true); ?>" title="Ir para Arquivo da Conta Capta&ccedil;&atilde;o" class="no_seta">Atualizar arquivo de conta capta&ccedil;&atilde;o</a>
                
                <?php
                    #removendo UCS nao concluidos do menu
                    /** /
                    <a href="<?php echo $this->url(array('controller' => 'manterbloqueioconta', 'action' => 'form-pesquisar-conta'), '', true); ?>" title="Ir para Bloquear contas" class="no_seta">Bloquear contas</a>
                    <a href="<?php echo $this->url(array('controller' => 'manterbloqueioconta', 'action' => 'listar-contas-desbloqueio'), '', true); ?>" title="Ir para Desbloquear" class="no_seta">Desbloquear contas</a>
                    /**/
                ?>

                <a href="#" title="Relat&oacute;rios" class="relatorio">Relat&oacute;rios</a>
                <div id="qm0" class="sanfona sanfonaDiv" style="display: none;">
                    <a href="<?php echo $this->url(array('controller' => 'movimentacaodeconta', 'action' => 'listar-inconsistencias'), '', true); ?>" title="Ir para Relat&oacute;rio de inconsist&ecirc;ncias de conta capta&ccedil;&atilde;o" class="no_seta">Inconsist&ecirc;ncias de conta capta&ccedil;&atilde;o</a>
                    <a href="<?php echo $this->url(array('controller' => 'controlarmovimentacaobancaria', 'action' => ''), '', true); ?>" title="Ir para Relat&oacute;rio de extrato banc&aacute;rio" class="no_seta">Extrato banc&aacute;rio</a>
                    <a href="<?php echo $this->url(array('controller' => 'movimentacaodeconta', 'action' => 'form-relatorio-recibo-captacao'), '', true); ?>" title="Ir para Relat&oacute;rio de recibo de capta&ccedil;&atilde;o" class="no_seta">Recibo de capta&ccedil;&atilde;o</a>
                    <!--<a href="<?php echo $this->url(array('controller' => 'manterbloqueioconta', 'action' => 'listar-contas-bloqueadas'), '', true); ?>" title="Ir para Relat&oacute;rio de contas bloqueadas" class="no_seta">Contas bloqueadas</a>-->
                    <a href="<?php echo $this->url(array('controller' => 'liberarcontabancaria', 'action' => 'contas-liberadas'), '', true); ?>" title="Ir para Relat&oacute;rio de contas liberadas" class="no_seta">Contas liberadas</a>
                </div>
                <a href="<?php echo $this->url(array('controller' => 'controlarmovimentacaobancaria', 'action' => ''), '', true); ?>" title="Ir para Relat&oacute;rio da Conta Movimenta&ccedil;&atilde;o" class="no_seta">Relat&oacute;rio da Conta Movimenta&ccedil;&atilde;o</a>

		<?php if ($this->grupoAtivo == 122 || $this->grupoAtivo == 123) : // s� Coordenador de Acompanhamento e Coord. Geral de Acompanhamento que pode acessar?>
                <a href="<?php echo $this->url(array('controller' => 'dbf', 'action' => ''), '', true); ?>" title="Ir para Gerar DBF" class="no_seta">Gerar DBF</a>
                <?php endif; ?>

                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'mantercontabancaria', 'action' => 'regularidade-proponente')); ?>" title="Regularidade Proponente">Regularidade Proponente</a>

            </div>
            <div class="bottom"></div>
        </div>
    </div>
</div>