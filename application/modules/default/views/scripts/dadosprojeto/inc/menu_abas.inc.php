<?php
/**
 * Menu
 * @author Equipe RUP - Politec
 * @since 07/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller.realizaranaliseprojeto.inc
 * @link http://www.cultura.gov.br
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 */
$pronac = $this->idpronac;
?>

<!-- ========== MENU ========== -->
<table class="tabela">
    <tr>
        <th class="<?php if (strstr($this->url(), 'parecerconsolidado') == 'parecerconsolidado') {
    echo "fundo_linha4";
} else {
    echo "fundo_linha2";
} ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'parecerconsolidado')); ?>?idPronac=<?php echo $pronac; ?>">Parecer Técnico Consolidado</a></th>
        <th class="<?php if (strstr($this->url(), 'analisedeconta') == 'analisedeconta') {
    echo "fundo_linha4";
} else {
    echo "fundo_linha2";
} ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'analisedeconta')); ?>?idPronac=<?php echo $pronac; ?>">Análise de Cortes Sugeridos</a></th>
        <th class="<?php if (strstr($this->url(), 'analisedeconteudo') == 'analisedeconteudo') {
    echo "fundo_linha4";
} else {
    echo "fundo_linha2";
} ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'analisedeconteudo')); ?>?idPronac=<?php echo $pronac; ?>">Análise de Conteúdo</a></th>
        <th class="<?php if (strstr($this->url(), 'analisedecustos') == 'analisedecustos') {
    echo "fundo_linha4";
} else {
    echo "fundo_linha2";
} ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'analisedecustos')); ?>?idPronac=<?php echo $pronac; ?>" id="custos">Análise de Custos</a></th>
        <th class="<?php if (strstr($this->url(), 'emitirparecer') == 'emitirparecer') {
    echo "fundo_linha4";
} else {
    echo "fundo_linha2";
} ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'emitirparecer')); ?>?idPronac=<?php echo $pronac; ?>">Emitir parecer</a></th>
    </tr>
</table>
<div id="load" class="carregando sumir" style="width:100%; height:100%;" title="Carregando..."><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" alt="carregando"><br/><br/>Carregando...<br>Por Favor, aguarde!!</div>
<script>
    $('document').ready(function(){
        $("#custos").click(function(){
            $("#load").dialog({
                resizable: false,
                width:300,
                height:160,
                modal: true,
                autoOpen:false
            });
            $("#load").dialog('open');
            $('.ui-dialog-titlebar-close').remove();
        });
    });
</script>