<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<!-- ========== IN?CIO MENU ========== -->

<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>

<div id="modalEnviarArquivo" title="ANEXAR" class="sumir">
    <div ID="load"><center><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" style="margin-top:120px"></center></div>
            <form class="form" id="form" name="form" enctype="multipart/form-data" target="enviar_arquivo" method="post" action="<?php echo $this->url(array('controller' => 'Alterarprojeto', 'action' => 'incluirarquivo')); ?>">


   <table>
            <tr class="fundo" style="font-size:8pt;">
                <td class="" colspan="2" align="left">Enviar anexo (tamanho m�ximo de 10 MB)<br>ipos de arquivos permitidos:   <font color="red">PDF</font></td>
            </tr>
            <tr class="fundo">
                <td>Arquivo</td>
                <td><input name="arquivo" id="arquivo" value="Procurar arquivo" class="input_simples" type="file"><span class="error" id="arquivoERRO1"> </span></td>
            </tr>
            <tr class="fundo">
                <td width="100px">
                    Descri&ccedil;&atilde;o
                </td>
                <td>
                    <textarea cols="40" rows="3" name="justificativa"  style="width:300px" class="input_simples"></textarea>
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
                $("#navglobal").css("width",fluidNavGlobal);
                $("#conteudo").css("width",fluidConteudo);
                $("#titulo").css("width",fluidTitulo);
                $("#rodapeConteudo").css("width",fluidConteudo);
                $("#rodape").css("width",fluidRodape);
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


       <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc sanfona">

                <a class="no_seta" href="<?php echo $this->url(array('controller' => 'distribuirprojetos', 'action' => 'distribuir')); ?>" title="Ir para Distribuir Projetos">Distribuir Projetos</a>

                <a class="no_seta last" href="<?php echo $this->url(array('controller' => 'distribuirprojetos', 'action' => 'redistribuir')); ?>" title="Ir para Redistribuir Projetos">Redistribuir Projetos</a>

<?php
} ?>

            </div>

            <div class="bottom">
            </div>
        <!-- final: navegaￜￜo local -->
        </div>
    </div>
</div>
<div id="confirmaExcluir" Title="Confima&ccedil;&atilde;o" style="display: none">Deseja realmente excluir sua proposta?</div>
<!-- ========== FIM MENU ========== -->
