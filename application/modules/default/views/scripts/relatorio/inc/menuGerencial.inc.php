<!-- ========== IN�CIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<script type="text/javascript">
    function layout_fluido()
    {
    	var janela         = $(window).width();
    	var fluidNavGlobal = janela - 245;
    	var fluidConteudo  = janela - 253;
    	var fluidTitulo    = janela - 252;
    	var fluidRodape    = janela - 19;
//    	$("#navglobal").css("width", fluidNavGlobal);
//    	$("#conteudo").css("width", fluidConteudo);
//    	$("#titulo").css("width", fluidTitulo);
//    	$("#rodapeConteudo").css("width", fluidConteudo);
//    	$("#imagemRodape").css("width", fluidConteudo);
//    	$("#rodape").css("width", fluidRodape);
//    	$("#conteudo").css("min-height", $('#menuContexto').height()); // altura minima do conteudo
//    	$("#rodapeConteudo").css("margin-left", "225px");
//    	$(".sanfonaDiv").css("clear", "both");
//    	$(".sanfonaDiv").css("width", "91%");
    } // fecha fun��o layout_fluido()

    $(document).ready(function()
    {
    	$('a.sanfona').click(function()
    	{
    		$(this).next().toggle('fast');
    	});
    });
    
    function JSExcluirProposta(idPreProjeto) {
        
        $("#modalExcluirProposta").html("Deseja realmente excluir sua proposta?");
        $("#modalExcluirProposta").dialog("destroy");
        $("#modalExcluirProposta").dialog
        ({
            width:450,
            height:200,
            EscClose:false,
            modal:true
            ,buttons:
            {
                'Cancelar':function()
                {
                    $(this).dialog('close'); // fecha a modal
                },
                'OK':function()
                {
                    window.location = "<?php echo $this->baseUrl(); ?>/manterpropostaedital/exluirproposta"+idPreProjeto;
                    $(this).dialog('close'); // fecha a modal
                }
            }
        });
        return false;
    }
</script>

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
        <div style="min-height: 80px; float: left;">
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                <a class="no_seta last" href="<?php echo $this->url(array('controller' => 'relatorio', 'action' => 'gerencial')); ?>" title="An&aacute;lise visual por t&eacute;cnico">An&aacute;lise visual por t&eacute;cnico</a>
                <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'relatorio', 'action' => 'parecer-projetos')); ?>" title="Parecer">Parecer</a>-->
            </div>
			<div class="bottom"></div>
			<div id="espaco"></div>
		</div>
        <div id="alertar"></div>
        <!-- final: navega��o local #qm0 -->
   
<div id="modalExcluirProposta" style="display:none"></div>
        </div>
<!-- ========== FIM MENU ========== -->