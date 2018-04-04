<?php if ((empty($this->qtdDirigentes)) and ($this->qtdDirigentes == 0) and ($this->dados[0]->tipopessoa == 1)):?>
    <center>
        <div class="msgALERT" style="width: 96%;">
            <div style="float: left;">Voc&ecirc; deve cadastrar pelo menos um dirigente!</div>
        </div>
    </center>
<?php endif;?>

<table class="tabela">
    <tr>
        <td width="160" class="centro">
            <?php if (($this->dados[0]->tipopessoa) == 1): ?>
            <b>CNPJ:</b> <?php echo Mascara::addMaskCNPJ($this->dados[0] ->cnpjcpf); ?>
            <?php else:?>
            <b>CPF:</b> <?php echo Mascara::addMaskCPF($this->dados[0] ->cnpjcpf); ?>
            <?php endif;?>
        </td>
        <td width="250"><b>NOME:</b> <?php echo $this->dados[0] ->nome; ?></td>
        <td><b>VIS&Otilde;ES:</b>
            <?php
                $i = 0;
                if ($this->visoes):
                    foreach ($this->visoes as $v) {
                        if ($i == 0):
                                echo $v->Descricao; else:
                                echo " | " .$v->Descricao;
                        endif;
                        $i++;
                    } else:
                    echo 'O Agente n&atilde;o tem nenhuma vis&atilde;o!';
                endif;
            ?>
        </td>
    </tr>
</table>
