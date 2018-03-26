<script>

$(document).ready(function(){


	$(".abrir").click(function () {

		$(".produtos").slideToggle("slow");

      });
});

</script>

<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <td>
            Parecerista: <?php echo $this->parecerista; ?>
        </td>
    </tr>
</table>

<?php if (isset($this->histFerias) && ($this->histFerias != 1)) {
    ?>
<?php $qtd = 1; ?>
<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <th colspan="3">
            Hist&oacute;rico de F&eacute;rias
        </th>
    </tr>
    <tr>
    	<td width="10px"></td>
        <td>
            Data In&iacute;cio
        </td>
        <td>
            Data Fim
        </td>
    </tr>
    <?php foreach ($this->histFerias as $ferias) :?> 
    <tr>
    	<td><?php echo $qtd; ?></td>
    	<td><?php echo $ferias->dtInicio; ?></td>
    	<td><?php echo $ferias->dtFim; ?></td>
    </tr>
    <?php $qtd++;
    endforeach; ?>
</table>

<?php
} elseif ($this->histFerias != 1) {
        ?>
<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <td>
            Nenhum Hist&oacute;rico de F&eacute;rias
        </td>
    </tr>
</table>
<?php
    }?>

<?php if (!empty($this->feriasAgend) && ($this->feriasAgend != 1)) {
        ?>
<?php $qtd = 1; ?>
<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <th colspan="3">
            F&eacute;rias Agendadas
        </th>
    </tr>
    <tr>
    	<td width="10px"></td>
        <td>
            Data In&iacute;cio:
        </td>
        <td>
            Data Fim:
        </td>
    </tr>
    <?php foreach ($this->feriasAgend as $agendadas) :?>
    <tr>
    	<td><?php echo $qtd; ?></td>
    	<td><?php echo $agendadas->dtInicio; ?></td>
    	<td><?php echo $agendadas->dtFim; ?></td>
    </tr>
    <?php $qtd++;
        endforeach; ?>
</table>
<?php
    } elseif ($this->feriasAgend != 1) {
        ?>
<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <td>
            N&atilde;o h&aacute; F&eacute;rias Agendadas
        </td>
    </tr>
</table>
<?php
    }?>

<?php if (isset($this->atestados) && ($this->atestados != 1)) {
        ?>
<?php $qtd = 1; ?>
<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <th colspan="3">
            Atestados M&eacute;dicos
        </th>
    </tr>
    <tr>
    	<td width="10px"></td>
        <td>
            Data In&iacute;cio:
        </td>
        <td>
            Data Fim:
        </td>
    </tr>
    <?php foreach ($this->atestados as $atestados) :?>
    <tr>
    	<td><?php echo $qtd; ?></td>
    	<td><?php echo $atestados->dtInicio; ?></td>
    	<td><?php echo $atestados->dtFim; ?></td>
    </tr>
    <?php $qtd++;
        endforeach; ?>
</table>
<?php
    } elseif ($this->atestados != 1) {
        ?>
<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <td>
            N&atilde;o h&aacute; Atestados M&eacute;dicos
        </td>
    </tr>
</table>
<?php
    }?>

<?php if ($this->projetos && ($this->projetos != 1)) {
        ?>
<!--<table class="tabela" style="width: 95%">-->
<!--	<tr>-->
<!--		<th colspan="6" align="center">-->
<!--    		Lista de An&aacute;lises Realizadas-->
<!--    	</th>-->
<!--	</tr>-->
<!--</table>-->
<?php foreach ($this->projetos as $projetos):?>
<div class="projeto">
	<table class="tabela">
		<tr>
	 		<td><input type="button" id="" class="btn_adicionar produto abrir" onclick="$('.produtos_<?php echo $projetos['IdPRONAC'] ?>').slideToggle('slow')"/> 
	 		<label id='proanc'>Pronac: </label><a target="_blank" href='<?php echo $this->url(array('controller' => 'consultardadosprojeto', 'action' => 'index'))."?idPronac=$projetos[IdPRONAC]"?>'>
	 		<?php echo $projetos['Pronac']; ?></a>
	 		&nbsp;&nbsp;&nbsp;&nbsp;
	 		<label id="nome projeto">Nome Projeto:</label> 
	 		<a href='<?php echo $this->url(array('controller' => 'consultarpareceristas', 'action' => 'carregarhistorico'))."?Pronac=$projetos[Pronac]&idPronac=$projetos[IdPRONAC]"?>'><?php echo $projetos['NomeProjeto']; ?></a>
	 		</td>
	 	</tr>
	</table>
	<div class="produtos_<?php echo $projetos['IdPRONAC']?>">
		<table class="tabela" style="width: 95%">
		    <tr align="center">
		        <th>
		            PRODUTO
		        </th>
		        <th>
		            DIAS DE AN&Aacute;LISE
		        </th>
		        <th>
		            PRODUTO PRINCIPAL
		        </th>
		        <th>
		            VALOR PAGAMENTO
		        </th>
		        <th>
		        	SITUA&ccedil;&atilde;O DO PAGAMENTO
		        </th>
		        <th>
		            MEMORANDO
		        </th>
		    </tr>
		    <?php foreach ($this->produtos as $produto): ?>
		    	<?php if ($produto->IdPRONAC == $projetos['IdPRONAC']):?>
					<tr style='text-align: center;'>
				        <td><?php echo $produto->Produto; ?></td>
				        <td><?php echo $produto->nrDias; ?></td>
				        <td><?php if ($produto->stPrincipal == 1) {
            echo 'Sim';
        } else {
            echo 'Nâo';
        } ?></td>
				        <td><?php echo $this->formatarReal($produto->vlPagamento); ?></td>
				        <td><?php if ($produto->siPagamento == 4) {
            echo 'Pago';
        } else {
            echo 'N&atilde;o Pago';
        } ?></td>
				        <td><?php echo $produto->memorando; ?></td>
			        </tr>
		        <?php endif; ?>
		    <?php endforeach; ?>
		</table>
	</div>
</div>
<?php endforeach; ?>
<?php
    } elseif ($this->projetos != 1) {
        ?>
<table class="tabela" style="text-align: left;width: 95%">
    <tr>
        <td>
            Nenhum produto para este Parecerista
        </td>
    </tr>
</table>
<?php
    } ?>