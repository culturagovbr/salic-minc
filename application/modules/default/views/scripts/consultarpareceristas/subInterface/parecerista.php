
<table class="tabela" style="text-align: left; width: 95%;">
    <tr>
        <td colspan="6">
            Parecerista: <?php echo $this->parecerista; ?>
        </td>
    </tr>
</table>
<?php if ($this->projetos) {
    ?>
<?php $proj_atual = 0;
    $proj_anterior = 0; ?>
<?php foreach ($this->projetos as $projetos):?>
<?php $proj_atual = $projetos['IdPRONAC']; ?>
			<?php $ok = 1; ?>
			<?php if ($proj_anterior) {
        if ($proj_atual == $proj_anterior) {
            $ok = 0;
        }
    } ?>
<?php if ($ok) {
        ?>
<div class="projeto">
	<table class="tabela" style="width: 95%">
	    <tr>
	        <td colspan="6">
	        	<input type="button" id="" class="btn_adicionar projeto" onclick="$('.produtos_<?php echo $projetos['IdPRONAC']?>').toggle()" />
	        	<label id="pronac">Pronac:</label>
	        	<a target="_blank" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index'))."?idPronac=$projetos[IdPRONAC]"?>'><?php echo $projetos['Pronac']; ?></a>
	        	&nbsp;&nbsp;&nbsp;&nbsp;<label id="nome projeto">Nome Projeto:</label> 
	        	<a href='<?php echo $this->url(array('controller' => 'consultarpareceristas', 'action' => 'carregarhistorico'))."?Pronac=$projetos[Pronac]&idPronac=$projetos[IdPRONAC]"?>'><?php echo $projetos['NomeProjeto']; ?></a>
	        </td>
	    </tr>
	</table>
	<div class="produtos_<?php echo $projetos['IdPRONAC']?>">
		<table class="tabela" style="width: 95%">
			<tr>
				<th colspan="6" align="center">
		    		Lista de An&aacute;lises Realizadas
		    	</th>
			</tr>
		    <tr align="center">
		        <th>
		            PRODUTO
		        </th>
		        <th>
		            PRODUTO PRINCIPAL
		        </th>
		        <th>
		            VALOR PAGAMENTO
		        </th>
		        <th>
		            MEMORANDO
		        </th>
		    </tr>
		    <?php foreach ($this->produtos as $produto): ?>
		    	<?php if ($produto->idPronac == $projetos['IdPRONAC']):?>
					<tr style='text-align: center;'>
				        <td><?php echo $produto->Descricao; ?></td>
				        <td><?php if ($produto->stPrincipal == 1) {
            echo 'Sim';
        } else {
            echo 'Nâo';
        } ?></td>
				        <td><?php echo $this->formatarReal($produto->vlPagamento); ?></td>
				        <td><?php echo $produto->memorando; ?></td>
			        </tr>
		        <?php endif; ?>
		    <?php endforeach; ?>
		    <?php $proj_anterior = $projetos['IdPRONAC']; ?>
		</table>
	</div>
</div>
<?php
    } ?>
<?php endforeach; ?>
<?php
} else {
        ?>
<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <td>
            Nenhum produto para este Parecerista
        </td>
    </tr>
</table>
<?php
    }?>