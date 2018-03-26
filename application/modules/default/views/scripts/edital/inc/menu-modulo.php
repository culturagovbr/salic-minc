<!-- ========== INICIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>

<div id="menu">
    <!-- inicio: conteudo principal #container -->
    <div id="container">
        <div id="menuContexto" style="margin-bottom: 50px;">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'modulosedital', 'idEdital' => $this->idEdital), null, true); ?>" title="Módulos Edital">Dados do edital</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'novo-modulo', 'idEdital' => $this->idEdital), null, true); ?>" title="Adicionar módulo">Adicionar módulo</a>
            </div>
            <div class="bottom"></div>

            

            <?php foreach ($this->modulos as $mo): ?>
                <div class="top"></div>
                <div id="qm0" class="qmmc sanfona">
                    <div class="sanfonaDiv" style="display: none;"></div>
                    <a href="#" title="Módulo do edital" class="ancoraModulo <?php echo $this->idModulo == $mo['idModulo'] ? 'menuAtivo':''; ?>" onclick="return false;"><?php echo $mo['dsModulo']; ?></a>
                    <div class="sanfonaDiv" style="width: 90%; margin-left: 20px; <?php echo $this->idModulo == $mo['idModulo'] ? 'display: block':'display: none'; ?>">
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'informacoes-modulo', 'idEdital' => $this->idEdital, 'idModulo' => $mo['idModulo']), null, true); ?>" title="Ir para An&aacute;lise do projeto">Dados do módulo</a>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'nova-categoria', 'idEdital' => $this->idEdital, 'idModulo' => $mo['idModulo']), null, true); ?>"  title="Ir para Adicionar categoria">Adicionar categoria</a>
                        <?php foreach ($mo['catModulo'] as $catMod): ?>
                        <div class="sanfonaDiv2">
                            <a class="<?php echo $this->idCategoria == $catMod['idCategoria'] ? 'menuAtivo2':''; ?>" href="#"><?php echo $catMod['nmCategoria']; ?></a>
                            <div class="sanfonaDiv2 sumir" style="padding-left: 20px; display: block;">
                                <a class="no_seta <?php echo $this->submenuativo == 'informacaogeral' ? 'menu':''; ?><?php echo $this->idCategoria == $catMod['idCategoria'] ? 'Ativo':''; ?>" href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'informacoes-categoria', 'idEdital' => $this->idEdital, 'idModulo' => $mo['idModulo'], 'idCategoria' => $catMod['idCategoria']), null, true); ?>" >Informa&ccedil;&otilde;es do gerais</a>
                                <a class="no_seta <?php echo $this->idCategoria == $catMod['idCategoria'] ? 'menu':''; ?><?php echo $this->submenuativo == 'criterioparticipacao' ? 'Ativo':''; ?>" href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'criterios-participacao', 'idEdital' => $this->idEdital, 'idModulo' => $mo['idModulo'], 'idCategoria' => $catMod['idCategoria']), null, true); ?>">Crit&eacute;rios de participa&ccedil;&atilde;o</a>
                                <?php if ($this->mostrarSubMenuPag == 1):?>
                                <a class="no_seta <?php echo $this->submenuativo == 'formapagamento' ? 'menu':''; ?><?php echo $this->idCategoria == $catMod['idCategoria'] ? 'Ativo':''; ?>" href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'forma-pagamento', 'idEdital' => $this->idEdital, 'idModulo' => $mo['idModulo'], 'idCategoria' => $catMod['idCategoria']), null, true); ?>">Formas de pagamento</a>
                                <?php endif; ?>
                                <a class="no_seta <?php echo $this->submenuativo == 'questionario' ? 'menu':''; ?><?php echo $this->idCategoria == $catMod['idCategoria'] ? 'Ativo':''; ?>" href="<?php echo $this->url(array('controller' => 'edital', 'action' => 'questionario', 'idEdital' => $this->idEdital, 'idModulo' => $mo['idModulo'], 'categoria' => $catMod['idCategoria']), null, true); ?>">Question&aacute;rio</a>
                            </div> 
                        </div>
                        <?php endforeach; ?>
                    </div>

                    
                </div>
                <div class="bottom"></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<style type="text/css">
    .sanfonaDiv {
        clear: both;
        display: none;
    }
    
</style>

<script type="text/javascript">
    
    function layout_fluido() {
        var janela = $(window).width();
        var fluidNavGlobal = janela - 245;
        var fluidConteudo = janela - 253;
        var fluidTitulo = janela - 252;
        var fluidRodape = janela - 19;
        $("#navglobal").css("width", fluidNavGlobal);
        $("#conteudo").css("width", fluidConteudo);
        $("#titulo").css("width", fluidTitulo);
        $("#rodapeConteudo").css("width", fluidConteudo);
        $("#rodape").css("width", fluidRodape);
        $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
    }
    
    /* =========================== MENU ABAS ================================ */
    function JSMostaConteudo(idConteudoAba, idModulo, idCategoria) {

        $("#ctModulo").html(idModulo);
        $("#ctCategoria").html(idCategoria);

        $("._conteudoAba").hide();
        $("#" + idConteudoAba).show();
    }

    function mostrarBoxInformacoesGeraisCategoria(idModulo, idCategoria){

        var conteudo = document.getElementById('formularioInformacaoGeralCategoria');

        var hiddenIdModulo = document.createElement('input');
            hiddenIdModulo.setAttribute('type', 'hidden');
            hiddenIdModulo.setAttribute('name', 'idModulo');
            hiddenIdModulo.setAttribute('value', idModulo);

        var hiddenIdCategoria = document.createElement('input');
            hiddenIdCategoria.setAttribute('type', 'hidden');
            hiddenIdCategoria.setAttribute('name', 'idCategoria');
            hiddenIdCategoria.setAttribute('value', idCategoria);

        conteudo.appendChild(hiddenIdModulo);
        conteudo.appendChild(hiddenIdCategoria);

         $('#boxInformacoesGeraisModulo').hide();
         $('#boxInformacoesGeraisCategoria').show();
         $('#boxCriteriosParticipacao').hide();
         $('#boxFormaPagamento').hide();
         $('#boxQuestionario').hide();
         $('#boxPlanilhaOrcamentaria').hide();
    }

    function mostrarBoxFormaPagamento(idModulo, idCategoria){

        var conteudo = document.getElementById('formularioFormaPagamento');

        var hiddenIdModulo = document.createElement('input');
            hiddenIdModulo.setAttribute('type', 'hidden');
            hiddenIdModulo.setAttribute('name', 'idModulo');
            hiddenIdModulo.setAttribute('value', idModulo);

        var hiddenIdCategoria = document.createElement('input');
            hiddenIdCategoria.setAttribute('type', 'hidden');
            hiddenIdCategoria.setAttribute('name', 'idCategoria');
            hiddenIdCategoria.setAttribute('value', idCategoria);

        conteudo.appendChild(hiddenIdModulo);
        conteudo.appendChild(hiddenIdCategoria);

         $('#boxInformacoesGeraisModulo').hide();
         $('#boxInformacoesGeraisCategoria').hide();
         $('#boxCriteriosParticipacao').hide();
         $('#boxFormaPagamento').show();
         $('#boxQuestionario').hide();
         $('#boxPlanilhaOrcamentaria').hide();
    }

    function mostrarBoxQuestionario(idModulo, idCategoria){

        var conteudo = document.getElementById('formularioGuia');
        var hiddenIdModulo = document.createElement('input');
            hiddenIdModulo.setAttribute('type', 'hidden');
            hiddenIdModulo.setAttribute('name', 'idModulo');
            hiddenIdModulo.setAttribute('value', idModulo);
        var hiddenIdCategoria = document.createElement('input');
            hiddenIdCategoria.setAttribute('type', 'hidden');
            hiddenIdCategoria.setAttribute('name', 'idCategoria');
            hiddenIdCategoria.setAttribute('value', idCategoria);
        conteudo.appendChild(hiddenIdModulo);
        conteudo.appendChild(hiddenIdCategoria);

        $('#boxInformacoesGeraisModulo').hide();
         $('#boxInformacoesGeraisCategoria').hide();
         $('#boxCriteriosParticipacao').hide();
         $('#boxFormaPagamento').hide();
         $('#boxPlanilhaOrcamentaria').hide();
         $('#boxQuestionario').show();
         $('#boxQuestionario').find('#categoria').val(idCategoria);
    }

    function mostrarBoxPlanilhaOrcamentaria(idModulo, idCategoria){

        var conteudo = document.getElementById('formularioPlanilhaOrcamentaria');

        var hiddenIdModulo = document.createElement('input');
            hiddenIdModulo.setAttribute('type', 'hidden');
            hiddenIdModulo.setAttribute('name', 'idModulo');
            hiddenIdModulo.setAttribute('value', idModulo);

        var hiddenIdCategoria = document.createElement('input');
            hiddenIdCategoria.setAttribute('type', 'hidden');
            hiddenIdCategoria.setAttribute('name', 'idCategoria');
            hiddenIdCategoria.setAttribute('value', idCategoria);

        conteudo.appendChild(hiddenIdModulo);
        conteudo.appendChild(hiddenIdCategoria);

         $('#boxInformacoesGeraisModulo').hide();
         $('#boxInformacoesGeraisCategoria').hide();
         $('#boxCriteriosParticipacao').hide();
         $('#boxFormaPagamento').hide();
         $('#boxQuestionario').hide();
         $('#boxPlanilhaOrcamentaria').show();
    }
    
    $("#salvarInformacaoGeralCategoria").click(function(){
        $("#formularioInformacaoGeralCategoria").submit(); 
    });

     $("#salvarInformacaoGeralModulo").click(function(){
       $("#formularioInformacaoGeralModulo").submit(); 
    });

    $("#salvarCriteriosParticipacao").click(function(){
       $("#formularioCriteriosParticipacao").submit(); 
    });

    $("#salvarFormaPagamento").click(function(){
       $("#formularioFormaPagamento").submit(); 
    });

    $("#salvarGuia").click(function(){
       $("#formularioGuia").submit(); 
    });
    
    $('.ancoraModulo').click(function(){
        $(this).next().toggle('fast');
    });
    
</script>
