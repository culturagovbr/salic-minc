<?php
/**
 * Menu
 * @author Equipe RUP - Politec
 * @since 07/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller.realizaranaliseprojeto.inc
 * @link http://www.cultura.gov.br
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 */

$pronac = $this->idpronac;
?>
<!-- ========== MENU ========== -->
<table class="tabela">
	<tr>
            <th class="<?php if (strstr($this->url(), 'parecerconsolidado')== 'parecerconsolidado') {
    echo "fundo_linha4";
} else {
    echo "fundo_linha2";
} ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'parecerconsolidado'));?>">Parecer T&eacute;cnico Consolidado</a></th>

<?php if (!$this->VerificarIN2017($this->idpronac)) : ?>
            <?php if ($this->bln_readequacao == "false") {
    ?>
                <th class="<?php if (strstr($this->url(), 'analisedeconta')    == 'analisedeconta') {
        echo "fundo_linha4";
    } else {
        echo "fundo_linha2";
    } ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'analisedeconta')); ?>">An&aacute;lise de Cortes Sugeridos</a></th>
            <?php
} else {
        ?>
                <th class="<?php if (strstr($this->url(), 'analisedeconta')    == 'analisedeconta') {
            echo "fundo_linha4";
        } else {
            echo "fundo_linha2";
        } ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'analisedecontareadequacao')); ?>">An&aacute;lise de Cortes Sugeridos</a></th>
            <?php
    } ?>
            
                <th class="<?php if (strstr($this->url(), 'analisedeconteudo') == 'analisedeconteudo') {
        echo "fundo_linha4";
    } else {
        echo "fundo_linha2";
    } ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'analisedeconteudo')); ?>">An&aacute;lise de Conte&uacute;do</a></th>
<?php endif; ?>            
            <?php if ($this->bln_readequacao == "false") {
        ?>
                <th class="<?php if (strstr($this->url(), 'analisedecustos')   == 'analisedecustos') {
            echo "fundo_linha4";
        } else {
            echo "fundo_linha2";
        } ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'analisedecustos')); ?>" id="custos">An&aacute;lise de Custos</a></th>
            <?php
    } else {
        ?>
                <th class="<?php if (strstr($this->url(), 'analisedecustos')   == 'analisedecustos') {
            echo "fundo_linha4";
        } else {
            echo "fundo_linha2";
        } ?>"><a href="<?php echo $this->url(array('controller' => 'realizaranaliseprojeto', 'action' => 'analisedecustosreadequacao')); ?>" id="custos">An&aacute;lise de Custos</a></th>
            <?php
    } ?>
            
                <th class="<?php if (strstr($this->url(), 'emitirparecer')     == 'emitirparecer') {
        echo "fundo_linha4";
    } else {
        echo "fundo_linha2";
    } ?>"><a href="<?php echo $this->url(array('module' => 'parecer', 'controller' => 'analise-cnic', 'action' => 'emitirparecer'));     ?>">Emitir parecer</a></th>
	</tr>
</table>
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