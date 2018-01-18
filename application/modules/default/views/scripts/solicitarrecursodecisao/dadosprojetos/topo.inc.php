<?php
/**
 * Topo com dados do Pronac e do Proponente
 * @author Equipe RUP - Politec
 * @since 20/07/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.scripts.recurso.inc
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
?>
	<table class="tabela">
  <tr>
    <td colspan="4" class="titulo_tabela">Dados do Recurso</td>
  </tr>
  <tr>
    <td class="fundo_linha2 bold">Proponente</td>
    <td class="fundo_linha2 bold">Pronac</td>
    <td class="fundo_linha2 bold">Nome do Projeto</td>
    <td class="fundo_linha2 bold">Situação</td>
  </tr>
 
 
	  <?php foreach ($this->proponente as $tbproponente): ?>
<tr>
<td class="fundo_linha2 center blue"><?php echo $this->escape($tbproponente->nmproponente);?></td>
<td class="fundo_linha2 center blue"><?php echo $this->escape($tbproponente->pronac);?></td>
<td class="fundo_linha2 center blue"><?php echo $this->escape($tbproponente->nmprojeto);?></td>
<td class="fundo_linha2 center blue"><?php echo $this->escape($tbproponente->situacao);?></td>
</tr>
<?php endforeach;?>	
</table>