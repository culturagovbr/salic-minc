<!-- ========== IN�CIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<script type="text/javascript">
    function layout_fluido(){
        var janela = $(window).width();
        var fluidNavGlobal = janela - 245;
        var fluidConteudo = janela - 253;
        var fluidTitulo = janela - 252;
        var fluidRodape = janela - 19;

//        $("#navglobal").css("width",fluidNavGlobal);
//        $("#conteudo").css("width",fluidConteudo);
//        $("#titulo").css("width",fluidTitulo);
//        $("#rodapeConteudo").css("width",fluidConteudo);
//        $("#rodape").css("width",fluidRodape);
        $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
    }

    $(document).ready(function(){
       $('.sanfona').click(function(){
            $('.sanfona .sanfonaDiv').each(function(indice, valor) {
                $(valor).hide('fast');
            });
            $(this).next().toggle('fast');
        });
    });
    
    function JSExcluirProposta(idPreProjeto) {
        
        $("#modalExcluirProposta").html("Deseja realmente excluir sua proposta?");
        $("#modalExcluirProposta").dialog("destroy");
        $("#modalExcluirProposta").dialog({
            width:450,
            height:200,
            EscClose:false,
            modal:true,
            buttons:{
                'Cancelar':function(){
                    $(this).dialog('close'); // fecha a modal
                },
                'OK':function(){
                    window.location = "<?php echo $this->baseUrl(); ?>/manterpropostaedital/exluirproposta"+idPreProjeto;
                    $(this).dialog('close'); // fecha a modal
                }
            }
        });
        return false;
    }
</script>

<div id="menu">
    <!-- in�cio: conte�do principal #container -->
    <div id="container">
        <!-- in�cio: navega��o local #qm0 -->
        <?php
            $get = Zend_Registry::get("get");
            //define id do PreProjeto que sera passado as outras implementacoes
            $codProjeto = "?idPreProjeto=";
            if (isset($this->proposta->idPreProjeto)) {
                $codProjeto .= $this->proposta->idPreProjeto;
                $idPreProjeto = $this->proposta->idPreProjeto;
            } elseif (isset($get->idPreProjeto)) {
                $codProjeto .= $get->idPreProjeto;
                $idPreProjeto = $get->idPreProjeto;
            }
        ?>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                <?php
                if ($this->grupoAtivo == 97 //Gestor SALIC
                    || $this->grupoAtivo == 93 //Coord. de Parecer
                    || $this->grupoAtivo == 103 //Coord. de Analise
                    || $this->grupoAtivo == 118 //Componente da Comissao
                    || $this->grupoAtivo == 119 //Presidente da CNIC
                    || $this->grupoAtivo == 120 //Coordenador CNIC
                    || $this->grupoAtivo == 133 //Membros Natos da CNIC
                    ) :
                ?>
                <a class="sanfona" href="#" title="Extrato">Extrato de Pauta</a>
                <div style="display: none; width: 90%" id="Extrato">
                    <a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'projetos-em-pauta-reuniao-cnic-sem-quebra')); ?>" title="Projetos em pauta">Projetos em pauta</a>
                    <a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'projetos-em-pauta-reuniao-cnic')); ?>" title="Pauta por Componente da Comiss&ailtde;o">Pauta por componente da comiss&atilde;o</a>
                    <a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'projetos-avaliados-cnic')); ?>" title="Resultado da CNIC">Resultado da CNIC</a>
                    <a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'projetos-voto-alterado')); ?>" title="Projetos com voto alterado na plen&aacute;ria">Projetos com voto alterado na plen&aacute;ria</a>
                    <a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'demonstrativo-captacao-recurso')); ?>" title="Demostrativo de capta&ccedil;&atilde;o de recusos">Demostrativo de capta&ccedil;&atilde;o de recusos</a>
                    <!--<a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'extrato-pauta-reuniao-cnic')); ?>" title="Extrato de Pauta de Reuni&atilde;o da CNIC">Extrato de Pauta de Reuni&atilde;o da CNIC</a>-->
                </div>
                <?php endif; ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'diagnostico')); ?>" title="Diagn&oacute;stico">Diagn&oacute;stico</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'editais-minc')); ?>" title="Editais">Editais</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'tramitacao')); ?>" title="Tramita&ccedil;&atilde;o">Tramita&ccedil;&atilde;o</a>
                <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'extrato-pauta-intercambio')); ?>" title="Extrato de Pauta de Interc&ac&acirc;mbio">Extrato de Pauta de Interc&acirc;mbio</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'relatorio', 'action' => 'extrator')); ?>" title="Extrator de Dados">Extrator de Dados</a>-->
                <a class="sanfona" href="#" title="Acompanhamento">Acompanhamento</a>
                <div style="display: none; width: 90%" id="Acompanhamento">
                    <a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'agencia-bancaria')); ?>">Consultar Ag&ecirc;ncia Banc&aacute;ria</a>
                    <a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'pedido-prorrogacao')); ?>">Consultar Pedido de Prorroga&ccedil;&atilde;o</a>
                    <a href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'conta-bancaria')); ?>">Conta Banc&aacute;ria</a>
                </div>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'tabelas')); ?>" title="Tabelas">Tabelas</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'regularidade-proponente')); ?>" title="Regularidade Proponente">Regularidade Proponente</a>
            </div>
            <div class="bottom">
            </div>
            <div id="space_menu">
            </div>
        </div>
        <div id="alertar"></div>
        <!-- final: navega��o local #qm0 -->
    </div>
</div>
<div id="modalExcluirProposta" style="display:none"></div>
<!-- ========== FIM MENU ========== -->