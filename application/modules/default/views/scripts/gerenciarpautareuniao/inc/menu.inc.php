<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">
    <!-- inicio: conteudo principal #container -->
    <div id="container">
        <!-- inicio: navegacao local #qm0 -->
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
                $("#menuDados").click(function(){
                    $("#corfirma").dialog({
                        resizable: false,
                        width:400,
                        height:200,
                        modal: true,
                        autoOpen:false,
                        buttons: {
                            'Não': function() {
                                $(this).dialog('close');
                            },
                            'Sim': function() {
                                if($("input[name='reuniao']").val()== 'A'){
                                    clearInterval(votacao);
                                    $(this).dialog('close');
                                    $("#ok").dialog({
                                        resizable: true,
                                        width:450,
                                        height:150,
                                        modal: true,
                                        autoOpen:false
                                    });
                                    $("#ok").html('A plen&aacute;ria ser&aacute; iniciada em 10 minutos. Favor aguardar o encerramento das atividades!');
                                    $("#ok").dialog('open');
                                    $("form[name='form']").submit();
                                }
                                else{
                                    clearInterval(telaacompanhamentopresidente);
                                    $("#corfirma").html('<br><br><center>Encerrando votação...<br><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" /></center>');
                                    var idNrReuniao = $("input[name='idReuniao']").val();
                                    //jqAjaxLinkSemLoading('<?php //echo $this->Url(array('controller' => 'gerenciarpautareuniao', 'action' => 'pa-encerrar-cnic'))?>?idReuniao=' + idNrReuniao, '', 'corfirma');
                                    $("form[name='form']").submit();
                                }

                            }
                        }
                    });
                    $("#corfirma").html($("#msg").val());
                    $("#corfirma").dialog('open');
                });

                $('.ancoraTeste').click(function(){
                    $(this).next().toggle('fast');
                });

            });
        </script>

        <style type="text/css">
            .sanfonaDiv {
                clear: both;
                display: none;
            }
        </style>
        <div id="corfirma" title="Confirma&ccedil;&atilde;o" style='display:none;'></div>
        <div id="ok" title="Confirma&ccedil;&atilde;o" style='display:none;'></div>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                <?php
                if ($this->grupoAtivo == 120 || $this->grupoAtivo == 148) { //coordenador CNIC
                ?>
                    <!--<a class="no_seta last" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciaradministrativo')); ?>" title="Ir para Projetos em Pauta">Projetos em Pauta</a>-->
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'exibirvotantes')); ?>" title="Ir para Votantes">Votantes</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciaradministrativo', 'readequacao' => 'false', 'plenaria' => 'true')); ?>" title="Ir para Plenária -  submetidos à plenária">Plenária - submetidos à plenária</a>
                    <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciaradministrativo', 'readequacao' => 'true', 'plenaria' => 'true')); ?>" title="Ir para Plenária -  readequação">Plenária - readequação</a>-->
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciaradministrativo', 'readequacao' => 'false', 'plenaria' => 'false')); ?>" title="Ir para Não submetidos -  análise inicial">Não submetidos - análise inicial</a>
                    <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciaradministrativo', 'readequacao' => 'true', 'plenaria' => 'false')); ?>" title="Ir para Não submetidos -  readequação">Não submetidos - readequação</a>-->
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'recursos-nao-submetidos')); ?>" title="Ir para Não submetidos - recursos">Não submetidos - recursos</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'readequacoes-nao-submetidas')); ?>" title="Ir para Não submetidos - readequa&ccedil;&otilde;es">Não submetidos - readequa&ccedil;&otilde;es</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'paineisdareuniao')); ?>" title="Ir para Painel de Reuni&otilde;es">Painel de Reuni&otilde;es</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'projetosvotados')); ?>" title="Ir para Painel de Projetos Votados">Projetos Votados</a>
<?php
                } ?>
                <?php
                if ($this->grupoAtivo == 118 or $this->grupoAtivo == 133) { //118 = componente da comissao  133 = membros natos
                ?>
                    <!--<a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao')); ?>" title="Ir para Projetos em Pauta">Projetos em Pauta</a>-->
                    <a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao', 'readequacao' => 'false', 'plenaria' => 'true')); ?>" title="Ir para Plenária - análise inicial">Plenária - análise inicial</a>
                    <!--<a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao' , 'readequacao' => 'true', 'plenaria' => 'true')); ?>" title="Ir para Plenária - readequação">Plenária - readequação</a>-->
                    <a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao', 'readequacao' => 'false', 'plenaria' => 'false')); ?>" title="Ir para Não submetidos - análise inicial">Não submetidos - análise inicial</a>
                    <!--<a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao', 'readequacao' => 'true', 'plenaria' => 'false')); ?>" title="Ir para Não submetidos - readequação">Não submetidos - readequação</a>-->
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'recursos-nao-submetidos')); ?>" title="Ir para Não submetidos - recursos">Não submetidos - recursos</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'readequacoes-nao-submetidas')); ?>" title="Ir para Não submetidos - readequa&ccedil;&otilde;es">Não submetidos - readequa&ccedil;&otilde;es</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'paineisdareuniao')); ?>" title="Ir para Painel de Reuni&otilde;es">Painel de Reuni&otilde;es</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'projetosvotados')); ?>" title="Ir para Painel de Projetos Votados">Projetos Votados</a>
                    <span class="no_seta last">&nbsp;</span>
<?php
                } ?>
<?php
                if ($this->grupoAtivo == 119) { //presidente CNIC
?>
                    <!--<a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao')); ?>" title="Ir para Projetos em Pauta">Projetos em Pauta</a>-->
                    <a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao', 'readequacao' => 'false', 'plenaria' => 'true')); ?>" title="Ir para Plenária - análise inicial">Plenária - análise inicial</a>
                    <!--<a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao', 'readequacao' => 'true', 'plenaria' => 'true')); ?>" title="Ir para Plenária - readequação">Plenária - readequação</a>-->
                    <a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao', 'readequacao' => 'false', 'plenaria' => 'false')); ?>" title="Ir para Não submetidos - análise inicial">Não submetidos - análise inicial</a>
                    <!--<a class="no_seta"  href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao', 'readequacao' => 'true', 'plenaria' => 'false')); ?>" title="Ir para Não submetidos - readequação">Não submetidos - readequação</a>-->
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'recursos-nao-submetidos')); ?>" title="Ir para Não submetidos - recursos">Não submetidos - recursos</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'readequacoes-nao-submetidas')); ?>" title="Ir para Não submetidos - readequa&ccedil;&otilde;es">Não submetidos - readequa&ccedil;&otilde;es</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'paineisdareuniao')); ?>" title="Ir para Painel de Reuni&otilde;es">Painel de Reuni&otilde;es</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'projetosvotados')); ?>" title="Ir para Painel de Projetos Votados">Projetos Votados</a>
                    <div class="sanfonaDiv" style="display:none;"></div>
                    <?php /* ?>
                    <a href="#" title="Teste" class="ancoraTeste" onclick="return false;">Teste</a>
                    <div class="sanfonaDiv" style="width: 90%; margin-left: 20px;">
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'projetosvotados')); ?>" title="Ir para Painel de Projetos Votados">por UF do Projeto</a>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'projetosvotados')); ?>" title="Ir para Painel de Projetos Votados">por Local de Realização</a>
                    </div>
                    <?php */ ?>
                    <span class="no_seta last">&nbsp;</span>
<?php
                } ?>
            </div>
            <div class="bottom"></div>
                <?php
                if ($this->grupoAtivo == 119) {
                    echo "<form name='form' action='" . $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao')) . "' method='post'>";
                    if ($this->reuniaoaberta['stPlenaria'] == 'N') {
                        echo "<div style='background:#f8f8f8;'>";
                        echo "<br><br><br>";
                        echo " <ul id='menuGerenciar'>";
                        echo "<li>";
                        echo "<input type='hidden' id='msg' value='Deseja realmente fechar pauta da reuni&atilde;o para inclus&atilde;o de novos projetos e iniciar a sess&atilde;o Plen&aacute;ria?'/>";
                        echo "<a style='cursor:pointer;' id='menuDados' title='Fechar Pauta /Iniciar Plen&aacute;ria'>INICIAR PLEN&Aacute;RIA</a>";
                        echo "</li>";
                        echo "</ul>";
                        echo "<input type='hidden' value='A' name='reuniao'>";
                        echo "<input name='idReuniao' type='hidden' value='";
                        echo $this->reuniaoaberta['idNrReuniao'];
                        echo "'>";
                        echo "</div>";
                    } elseif ($this->reuniaoaberta['stPlenaria'] == 'A') {
                        echo "<div style='background:#f8f8f8; padding-bottom:10em;'>";
                        echo "<br><br><br>";
                        echo " <ul id='menuGerenciar' class='sumir'>";
                        echo "<li>";
                        echo "<input type='hidden' id='msg' value='Deseja realmente encerrar esta sess&atilde;o Plen&aacute;ria?'/>";
                        echo "<a style='cursor:pointer;' id='menuDados' title='Encerrar Plen&aacute;ria'>ENCERRAR</a>";
                        echo "</li>";
                        echo "</ul>";
                        echo "<input name='idReuniao' type='hidden' value='";
                        echo $this->reuniaoaberta['idNrReuniao'];
                        echo "'>";
                        echo "<input type='hidden' value='E' name='reuniao'>";
                        echo "</div>";
                    }
                }
                echo "</form>";
                ?>
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
        <!-- final: navegacao local #qm0 -->
    </div>
</div>
<!-- ========== FIM MENU ========== -->
