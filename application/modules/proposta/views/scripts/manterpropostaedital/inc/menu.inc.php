<!-- ========== IN?CIO MENU ========== -->

<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<script>
// modal
function confirmaExcluir(obj){



    $("#confirmaExcluir").dialog("destroy");
    $("#confirmaExcluir").dialog
    ({
        width:350,
        height:170,
        EscClose:false,
        modal:true
        ,
        buttons:
        {
            'Cancelar':function()
            {
                $(this).dialog('close'); // fecha a modal
            },
            'Confirmar':function()
            {
                location.href=obj;
            }
        }
    });

    $("#confirm").dialog('open');


}


function confirmacao(obj,texto){

    $("#corfirma").html(texto);
    $("#corfirma").dialog("destroy");
    $("#corfirma").dialog
    ({
        width:350,
        height:170,
        EscClose:false,
        modal:true
        ,
        buttons:
        {
            'Cancelar':function()
            {
                $(this).dialog('close'); // fecha a modal
            },
            'Confirmar':function()
            {
                location.href=obj;
            }
        }
    });

    $("#confirm").dialog('open');


}

function trocarproponente()
{

	$("#trocarproponente").dialog("destroy");
    $("#trocarproponente").dialog
    ({
        width:600,
        height:250,
        EscClose:false,
        modal:true,
        title:'Trocar Proponente'
        ,buttons:
        {
            'Cancelar':function()
            {
                $(this).dialog('close'); // fecha a modal
            },
            'Novo Proponente':function()
            {
            	window.location = "<?php echo $this->url(array('controller' => 'vincularresponsavel', 'action' => 'index')); ?>";
            },
            'Trocar Proponente':function()
            {
            	$("#formtrocaproponente").submit();

            }
        }
    });



    return false;
}
</script>



<div id="menu">

    <!-- inￜcio: conteￜdo principal #container -->
    <div id="container">

        <!-- inￜcio: navegaￜￜo local  -->
        <script type="text/javascript">
            function layout_fluido() {
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
        </script>

        <style type="text/css">
            .sanfonaDiv {
                clear: both;
                display: none;
            }
        </style>
        <div id="corfirma" title="Confirma&ccedil;&atilde;o" style='display:none;'></div>
        <div id="ok" title="Confirma&ccedil;&atilde;o" style='display:none;'></div>
        <?php

            $get = Zend_Registry::get("get");
            //define id do PreProjeto que sera passado as outras implementacoes
            $codProjeto = "?idPreProjeto=";
            if (isset($this->idPreProjeto)) {
                $codProjeto .= $this->idPreProjeto;
            } elseif (isset($get->idPreProjeto)) {
                $codProjeto .= $get->idPreProjeto;
            }
        ?>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                    <!--<a class="no_seta" href="<?php echo $this->url(array('controller' => 'manterpropostaedital', 'action' => 'edital')); ?>" title="Ir para Informacoes Complementares">Minhas Propostas</a>-->
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'manterpropostaedital', 'action' => 'dadospropostaedital')); ?><?php echo $codProjeto; ?>" title="localderealizacao">Proposta Atual</a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'localderealizacao',    'action' => 'index')); ?><?php echo $codProjeto; ?>&edital=s">Local de realiza��o <!--/ Deslocamento --></a>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'manterpropostaedital', 'action' => 'responderquestionarioedital')); ?><?php echo $codProjeto; ?> ">Responder Question�rio</a>
                    <?php //if(isset($this->blnJaEnviadaAoMinc) && $this->blnJaEnviadaAoMinc >= 1):?>
                        <!--<a class="no_seta" href="#" onclick="return false;" style="color:#9d9d9d;">Anexar Documentos</a>-->
                    <?php //else:?>
                        <a class="no_seta" href="<?php echo $this->url(array('controller' => 'manterpropostaedital', 'action' => 'enviararquivoedital')); ?><?php echo $codProjeto; ?>&edital=s">Anexar Documentos</a>
                    <?php //endif;?>
                    <?php //if($this->verificarmenu == 1){?>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'manterpropostaedital', 'action' => 'documentospendentesedital')); ?><?php echo $codProjeto; ?>&edital=s">Documentos Pendentes</a>
                    <?php //}?>
                    <!-- <a class="no_seta" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaproponente')); ?><?php echo $codProjeto; ?>&edital=s">Dilig&ecirc;ncias</a> -->
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'Gerarimprimirpdf', 'action' => 'index')); ?><?php echo $codProjeto; ?>">Imprimir/Gerar PDF</a>
                    <?php //if(isset($this->blnPossuiDiligencias) && $this->blnPossuiDiligencias > 0):?>
                    <?php if ($this->enviado != 'false'): ?>
                    <a class="no_seta" href="<?php echo $this->url(array('module' => 'proposta', 'controller' => 'diligenciar', 'action' => 'listardiligenciaproponente')).$codProjeto; ?>&edital=s" title="Visualizar dilig&ecirc;ncias">Dilig&ecirc;ncias</a>
                    <?php endif; ?>
                    <?php if ($this->enviado == 'false') {
            ?>
                    <a class="no_seta" href="#" onclick="confirmaExcluir('<?php echo $this->url(array('controller' => 'manterpropostaedital', 'action' => 'exluirproposta')) ?><?php echo $codProjeto; ?>')">Excluir Proposta</a>
                    <?php
        } ?>
                    <?php if ($this->enviado == 'false') {
            ?>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'manterpropostaedital', 'action' => 'enviar-proposta')); ?><?php echo $codProjeto; ?>&edital=s">Enviar Proposta ao MINC</a>
                    <?php
        }?>

                    <?php if ($this->siVinculoProponente): ?>
                    	<a class="no_seta" href="#" onclick="trocarproponente('<?php echo $codProjeto; ?>');">Trocar Proponente</a>
                	<?php endif; ?>
                    <span class="no_seta last">&nbsp;</span>
            </div>
            <div class="bottom"></div>

        <!-- final: navegaￜￜo local -->
        </div>
    </div>
</div>
<div id="confirmaExcluir" Title="Confima&ccedil;&atilde;o" style="display: none">Deseja realmente excluir sua proposta?</div>
<!-- ========== FIM MENU ========== -->

<div id="trocarproponente" style="display:none">

<form id="formtrocaproponente" action="<?php echo $this->url(array('controller' => 'vincularresponsavel', 'action' => 'trocarproponente')); ?>" method="post">
<input type="hidden" value="<?php echo $this->dadosVinculo[0]->idVinculoProposta; ?>" name="idVinculoProposta" />
<input type="hidden" value="<?php echo $this->idPreProjeto; ?>" name="idPreProjeto" />
<input type="hidden" value="2" name="mecanismo" />
	<table class="tabela">
		<tr>
			<td class="titulo_tabela">Selecione um Proponente ou cadastre um novo.</td>
		</tr>
		<tr>
			<td>
				CPF/CNPJ Proponente:&nbsp;
				<select name="propronente" id="propronente" class="input_simples w240">
				<?php $idAgente = 0; ?>
				<?php foreach ($this->listaProponentes as $lp):?>

					<?php if ($lp->idAgenteProponente != $idAgente):?>
						<option value="<?php echo $lp->idVinculo;?>:<?php echo $lp->idAgenteProponente;?>"><?php echo $lp->NomeProponente;?></option>
					<?php endif;?>

				<?php $idAgente = $lp->idAgenteProponente; ?>
				<?php endforeach;?>
				</select>&nbsp;<span id="msgValidaProponente"></span>&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
	</table>
</form>
</div>