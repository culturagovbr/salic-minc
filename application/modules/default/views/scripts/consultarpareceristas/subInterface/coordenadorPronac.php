<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <td width="80%">
            Parecerista: <?php echo $this->parecerista;?>
        </td>
    </tr>
</table>
<br />
<?php
if ($this->projetosPagos) {
    ?>
<table class="tabela" style="width: 100%">
    <tr>
        <td colspan="7" align="center">
            <strong style="font-size: 18px">
                Produtos Pagos
            </strong>
        </td>
    </tr>
</table>
<?php $proj_atual = 0;
    $proj_anterior = 0; ?>
<?php foreach ($this->projetosPagos as $projetosP):?>
<?php $proj_atual = $projetosP['IdPRONAC']; ?>
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
	        	<input type="button" id="" class="btn_adicionar projeto" onclick="$('.produtos_<?php echo $projetosP['IdPRONAC']?>').toggle()" />
	        	<label id="pronac">Pronac:</label>
	        	<a target="_blank" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index'))."?idPronac=$projetosP[IdPRONAC]"?>'><?php echo $projetosP['Pronac']; ?></a>
	        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="nome projeto">Nome Projeto:</label> 
	        	<a href='<?php echo $this->url(array('controller' => 'consultarpareceristas', 'action' => 'carregarhistorico'))."?Pronac=$projetosP[Pronac]&idPronac=$projetosP[IdPRONAC]"?>'><?php echo $projetosP['NomeProjeto']; ?></a>
	        </td>
	    </tr>
	</table>
	<div class="produtos_<?php echo $projetosP['IdPRONAC']?>">
		<table class="tabela" style="width: 95%">
    <tr>
        <th>
            PRODUTO
        </th>
        <th>
            PRODUTO PRINCIPAL
        </th>
        <th>
            ORDEM DE PAGAMENTO
        </th>
        <th>
            MEMORANDO / STATUS
        </th>
    </tr>
    <?php foreach ($this->produtos as $produto):?>
    	<?php if ($produto->idPronac == $projetosP['IdPRONAC']): $pronac = $projetosP['Pronac'];
        $idPronac = $produto->idPronac; ?>
			<tr style='text-align: center;'>
		        <td><?php echo $produto->Descricao; ?></td>
		        <td><?php if ($produto->stPrincipal == 1) {
            echo 'Sim';
        } else {
            echo 'Nâo';
        } ?></td>
		        <td><?php echo $produto->OrdemPagamento; ?></td>
		        <td><?php echo $produto->memorando.' / ';
        if ($produto->TipoParecer != 4) {
            echo 'Pendente';
        } elseif ($produto->TipoParecer == 4) {
            echo 'Gerado';
        } ?></td>
	        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php $proj_anterior = $projetosP['IdPRONAC']; ?>
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
            Nenhuma Produto Pago para este Parecerista
        </td>
    </tr>
</table>
<?php
    }?>
<br />



<?php
if ($this->projetosLiberados) {
        ?>
<table class="tabela" style="width: 100%">
    <tr>
        <td colspan="7" align="center">
            <strong style="font-size: 18px">
                Produtos Liberados
            </strong>
        </td>
    </tr>
</table>
<?php $proj_atual = 0;
        $proj_anterior = 0; ?>
<?php foreach ($this->projetosLiberados as $projetosL):?>
<?php $proj_atual = $projetosL['IdPRONAC']; ?>
			<?php $ok2 = 1; ?>
			<?php if ($proj_anterior) {
            if ($proj_atual == $proj_anterior) {
                $ok2 = 0;
            }
        } ?>
<?php if ($ok2) {
            ?>
<div class="projeto">
    <table class="tabela" style="width: 95%">
	    <tr>
	        <td colspan="6">
	        	<input type="button" id="" class="btn_adicionar projeto" onclick="$('.produtos_<?php echo $projetosL['IdPRONAC']?>').toggle()" />
	        	<label id="pronac">Pronac:</label>
	        	<a target="_blank" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index'))."?idPronac=$projetosL[IdPRONAC]"?>'><?php echo $projetosL['Pronac']; ?></a>
	        	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label id="nome projeto">Nome Projeto:</label> 
	        	<a href='<?php echo $this->url(array('controller' => 'consultarpareceristas', 'action' => 'carregarhistorico'))."?Pronac=$projetosL[Pronac]&idPronac=$projetosL[IdPRONAC]"?>'><?php echo $projetosL['NomeProjeto']; ?></a>
	        </td>
	    </tr>
	</table>
	<div class="produtos_<?php echo $projetosL['IdPRONAC']?>">
		<table class="tabela" style="width: 95%">
    <tr>
        <th>
            PRODUTO
        </th>
        <th>
            PRODUTO PRINCIPAL
        </th>
        <th>
            ORDEM DE PAGAMENTO
        </th>
        <th>
            MEMORANDO / STATUS
        </th>
    </tr>
    <?php foreach ($this->produtos as $produto): ?>
    	<?php if ($produto->idPronac == $projetosL['IdPRONAC']): $pronac = $projetosL['Pronac'];
            $idPronac = $produto->idPronac; ?>
			<tr style='text-align: center;'>
		        <td><?php echo $produto->Descricao; ?></td>
		        <td><?php if ($produto->stPrincipal == 1) {
                echo 'Sim';
            } else {
                echo 'Nâo';
            } ?></td>
		        <td><?php echo $produto->OrdemPagamento; ?></td>
		        <td><?php echo $produto->memorando.' / ';
            if ($produto->TipoParecer != 4) {
                echo 'Pendente';
            } elseif ($produto->TipoParecer == 4) {
                echo 'Gerado';
            } ?></td>
	        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php $proj_anterior = $projetosL['IdPRONAC']; ?>
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
            Nenhum Produto Liberado para este Parecerista
        </td>
    </tr>
</table>
<?php
    }?>