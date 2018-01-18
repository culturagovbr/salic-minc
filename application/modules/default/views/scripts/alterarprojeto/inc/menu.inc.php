<style type="text/css">
#lista {
    display: none;
}
.error{
    color: red;
}
#load{
    width:100%;
    height: 298px;
    margin-bottom: -298px;
    z-index: 1;
    position: relative;
    background-color: #d7ecc1;
    opacity:0.85;
    -moz-opacity: 0.85;
    filter: alpha(opacity=85);
    display: none;
}
</style>
<?php

//CRIPTOGRAFA ID DO PRONAC
$idPronac = $this->pronac;

if (in_array($this->grupoAtivo, array(92,93,104,113,114,115,124,125,126,128,131,132,134,135,136,137,138,139,140,143))) {
    if ($this->pagina == "alterarprojeto") {
        header("Location: situacao?pronac=$idPronac");
    }
}
?>

<!-- ========== INICIO MENU ========== -->
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<script type="text/javascript">
  function removeanexo(obj){
     $('#'+obj).remove();
     document.getElementById("excuir").value += obj+",";
  }

  function inserir(id,data,arquivo,idarquivo){
      document.getElementById("arquivo").value = null;

       $('#classificacao').find('.cls').each(function(){
            if($('#classificacao').val() == $(this).val()){
            classificacao = $(this).html();
            }
            });

      linha = "<tr id='"+id+"'>\n\
                 <td><input type='hidden' name='arquivoid[]' value='"+ idarquivo +"'><input type='hidden' name='documentoid[]' value='"+ id +"'>"+$('#tipodocumento').val()+"</td>\n\
                 <td>"+data+"</td>\n\
                 <td>"+classificacao+"</td>\n\
                 <td>"+arquivo+"</td>\n\
                 <td class='excluir'><input type='button' class='btn_exclusao' title='Excluir' onclick=removeanexo('"+ id +"'); /></td>\n\
              </tr>";
       //document.getElementById("tabelaArquivos").innerHTML += linha;
       $("#tabelaArquivos").append(linha);
       
       $('#load').hide();
       $("#modalEnviarArquivo").dialog('close');
       document.getElementById("form").reset();
       $('#conteudo').find('#formularioAtualizaAnexo').each(function(){
            document.formularioAtualizaAnexo.submit();
       });

  }

// modal
    function alerta(texto){
        $("#alerta").dialog("destroy");
        $("#alerta").html(texto);
        $("#alerta").dialog({
            width:350,
            height:170,
            EscClose:false,
            modal:true,
            buttons: {
                'Ok':function(){
                    $(this).dialog('close'); // fecha a modal
                }
            }
        });
        $("#alerta").dialog('open');
    }

    function confirmaExcluir(obj){
        $("#confirmaExcluir").dialog("destroy");
        $("#confirmaExcluir").dialog({
            width:350,
            height:170,
            EscClose:false,
            modal:true,
            buttons:{
                'Cancelar':function(){
                    $(this).dialog('close'); // fecha a modal
                },
                'Confirmar':function(){
                    location.href=obj;
                }
            }
        });
        $("#confirm").dialog('open');
    }

    function EnviarArquivo(){
        // modal com os dados do dirigente
        $("#modalEnviarArquivo").dialog("destroy");
        $("#modalEnviarArquivo").dialog({
            width:510,
            height:420,
            EscClose:false,
            modal:true,
            buttons:{
                'Cancelar':function(){
                    $(this).dialog('close'); // fecha a modal
                    $('#load').hide();
                    buscarDirigentes();
                },
                'Enviar Arquivo':function(){
                    arquivo = document.getElementById("arquivo").value;
                    arquivo = arquivo.substr((arquivo.length-3), 3)
                    if (arquivo == "PDF" || arquivo == "pdf"){
                        if($('#tipodocumento').val() == "selecione" ){
                            $('#arquivoERRO1').html("");
                            $('#arquivoERRO2').html("<Br>*Informe o tipo de documento");
                        } else if($('#classificacao').val() == "selecione" ){
                            $('#arquivoERRO1').html("");
                            $('#arquivoERRO2').html("");
                            $('#arquivoERRO3').html("<Br>*Informe a classifica&ccedil;&atilde;o do documento");
                        } else {
                            document.form.submit();
                            $('#load').show();
                        }
                    } else {
                        $('#arquivoERRO1').html("<Br>*Apenas arquivos PDF");
                    }
                }
            }
        });
    } // fecha funcao anexar arquivo()

    $("textarea[maxlength]").keypress(function(event){
        var key = event.which;
        //todas as teclas incluindo enter
        if(key >= 33 || key == 13) {
            var maxLength = $(this).attr("maxlength");
            var length = this.value.length;
            if(length >= maxLength) {
                event.preventDefault();
            }
        }
    });
</script>

<div id="modalEnviarArquivo" title="Anexar Arquivos" class="sumir">
    <div id="load"><center><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif"></center></div>

        <form class="form" id="form" name="form" enctype="multipart/form-data" target="enviar_arquivo" method="post" action="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'incluirarquivo')); ?>">
        <input type="hidden" value="<?php echo $this->parecer->IdPRONAC; ?>" name="idpronac" />
        <input type="hidden" value="<?php echo $this->parecer->Logon; ?>" name="idagente" />

        <table class="tabela">
            <tr class="fundo">
                <td class="" colspan="2" align="left">Enviar anexo (tamanho máximo de 10 MB)<br>Tipos de arquivos permitidos: <font color="red">PDF</font></td>
            </tr>
            <tr class="fundo">
                <td class="destacar bold" align="right" width="1">Arquivo</td>
                <td><input name="arquivo" id="arquivo" value="Procurar arquivo" class="input_simples" type="file" /><span class="error" id="arquivoERRO1"> </span></td>
            </tr>
            <tr class="fundo">
                <td class="destacar bold" align="right">Tipo do documento</td>
                <td>
                    <select name="tipodocumento" id="tipodocumento" class="select_simples w300">
                        <option value="selecione">Selecione</option>
                        <option value="Projeto">Projeto</option>
                        <option value="Proponente">Proponente</option>
                    </select>
                    <span class="error" id="arquivoERRO2"> </span>
                </td>
            </tr>
            <tr class="fundo">
                <td class="destacar bold" align="right">Classifica&ccedil;&atilde;o</td>
                <td>
                    <select name="classificacao" id="classificacao" class="select_simples w300">
                        <option value="selecione">Selecione</option>
                        <?php foreach ($this->tiposDocumento as $tipo) {
    echo '<option class="cls" value="'.$tipo['idTipoDocumento'].'">'.$tipo['dsTipoDocumento'].'</option>';
} ?>
                    </select>
                    <span class="error" id="arquivoERRO3"> </span>
                </td>
            </tr>
            <tr class="fundo">
                <td class="destacar bold" align="right">Descri&ccedil;&atilde;o</td>
                <td>
                    <textarea cols="40" rows="2" maxlength="400" name="justificativa"  style="width:98%" class="textarea_simples"></textarea>
                </td>
            </tr>
        </table>

    </form>
</div>

<?php
$menu = !empty($_GET["menu"]) ? $_GET["menu"] : 1;
if ($menu != 0) {
    ?>

<div id="menu">

    <!-- inicio: conteudo principal #container -->
    <div id="container">

        <!-- inicio: navegacao local  -->
        <script type="text/javascript">
            function layout_fluido() {
                var janela = $(window).width();
                var fluidNavGlobal = janela - 245;
                var fluidConteudo = janela - 253;
                var fluidTitulo = janela - 252;
                var fluidRodape = janela - 19;
//                $("#navglobal").css("width",fluidNavGlobal);
//                $("#conteudo").css("width",fluidConteudo);
//                $("#titulo").css("width",fluidTitulo);
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
        <div id="corfirma" title="Confirmacao" style='display:none;'></div>
        <div id="ok" title="Confirmacao" style='display:none;'></div>

       <div id="menuContexto" style="margin-bottom: 50px;">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">

                <?php if (in_array($this->grupoAtivo, array(97,103,110,121,122,123,127))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'alterarprojeto'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para alterar nome do projeto">Nome do Projeto</a>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'proponente'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para alterar proponente">Proponente</a>
                <?php
    } ?>
                
                <?php if (in_array($this->grupoAtivo, array(92,93,97,103,104,110,113,114,115,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'situacao'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para alterar situa&ccedil;&atilde;o">Situa&ccedil;&atilde;o</a>
                <?php
    } ?>

                <?php if (in_array($this->grupoAtivo, array(97,103,110,121,122,123,127))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'areasegmento'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para alterar &aacute;rea/segmento">&Aacute;rea / Segmento</a>
                <?php
    } ?>

                <?php if (in_array($this->grupoAtivo, array(97,103,110,121,122,123,127))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'planodistribuicao'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para plano de distribui&ccedil;&atilde;o">Plano de distribui&ccedil;&atilde;o</a>
                <?php
    } ?>
		
                <?php if (in_array($this->grupoAtivo, array(97))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'orgao'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para alterar &oacute;rg&atilde;o">&Oacute;rg&atilde;o</a>
                <?php
    } ?>

                <?php if (in_array($this->grupoAtivo, array(103))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'sintese', 'pronac' => $this->pronac)); ?>" title="Ir para síntese do projeto">Síntese do Projeto</a>
                <?php
    } ?>

                <?php if (in_array($this->grupoAtivo, array(97,103,122,123,125,126,127,134,138))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'habilitarprojeto'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para habilitar projeto">Habilitar Projeto</a>
                <?php
    } ?>

                <?php if (in_array($this->grupoAtivo, array(97,103,110,121,122,123,127)) && $this->parecer->Mecanismo == 1) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'enquadramento'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para alterar enquadramento">Enquadramento</a>
                <?php
    } ?>

                <?php if (in_array($this->grupoAtivo, array(97,103,110,121,122,123,127))) {
        ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'periododeexecucao'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para per&iacute;odo de execu&ccedil;&atilde;o">Período de Execu&ccedil;&atilde;o</a>
                <?php
    } ?>

                <?php /*if(in_array('8', $menuAtor)){ ?>
                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'anexos')); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para documentos anexados">Documentos Anexados</a>
                <?php }*/ ?>

                <?php if (in_array($this->grupoAtivo, array(92,97,103,110,114,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143)) && $this->pj == "true") {
        ?>
                <a class="last no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'dirigentes', 'pronac' => $this->pronac)); ?>" title="Ir para incluir dirigente">Dirigentes</a>
                <?php
    } ?>
                <?php if (in_array($this->grupoAtivo, array(92,93,97,103,104,110,113,114,115,121,122,123,124,125,126,127,128,131,132,134,135,136,137,138,139,140,143))) {
        ?>
                    <a class="no_seta" href="<?php echo $this->url(array('controller' => 'alterarprojeto', 'action' => 'execucao-imediata'), '', true); ?>?pronac=<?php echo $this->pronac ?>" title="Ir para alterar plano de execu&ccedil;&atilde;o imediata">Execu&ccedil;&atilde;o imediata</a>
                <?php
    } ?>

            </div>

            <div class="bottom"></div>
            
        <!-- final: navegacao local -->
        </div>
    </div>
</div>

<?php
} ?>

<div id="confirmaExcluir" Title="Confima&ccedil;&atilde;o" style="display: none">Deseja realmente excluir sua proposta?</div>
<div id="alerta" Title="Aten&ccedil;&atilde;o" style="display: none"></div>
<!-- ========== FIM MENU ========== -->
