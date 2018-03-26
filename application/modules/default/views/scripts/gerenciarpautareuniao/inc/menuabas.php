<?php


$somaProjetos = ($aprovados + $retiradosPauta + $indeferidos + $naoAnalisados);
foreach ($this->consultaReuniao as $value) {
    ?>
<script>
    var verificavotacao = window.setInterval(function(){
        validardados(<?php echo $this->consultaProjetosPautaReuniao[0]->NumeroReuniao; ?>);
    }, 1000);

    
    function validardados(idNrReuniao)
    {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "../gerenciarpautareuniao/atualizacao",
            data:
            {
        	idNrReuniao : idNrReuniao
            },
            success: function(valor)
            {
            	if((valor.dtfechamento != '<?php echo data::tratarDataZend($value->DtFechamento, 'americano'); ?>') || (valor.stEstado != <?php echo$value->stEstado?>))
                {
            		window.location.reload();
            		clearInterval(verificavotacao);
             	}
            }
        });
    }

</script>
<?php
//                    $("#teste").html('idNrReuniao:'+idNrReuniao+'databanco'+valor.dtfechamento+'dataatual:<?php echo data::tratarDataZend($value->DtFechamento,'americano'); ');
    if ($i == 0) {
        $nrreuniao = $value->idNrReuniao;
        echo "<br><br><div style='margin-left:20px'>";
        echo "<div id='teste'></div>";
        echo "<table>";
        echo "<tr>";
        echo "<td>Status da Reunião: </td>";
        if ($value->stEstado == 0 and Data::CompararDatas($value->DtFechamento) >= 0) {
            echo "<td>Reunião em andamento</td>";
        }
        
        if (Data::CompararDatas($value->DtFechamento) <= 0 and $value->stEstado <= 0) {
            echo "<td>Aguardando fechamento da Pauta</td>";
        }
        echo "</tr>";
        echo "<tr >";
        echo "<td>Período para inclusão de projetos:</td>";
        echo "<td>" . $value->DtInicio . " a " . $value->DtFechamento . "</td>";
        echo "</tr>";
        echo "<tr >";
        echo "<td>Total de projetos submetidos a Plenária:</td>";
        if (isset($submetidosPlenaria)) {
            echo "<td>$submetidosPlenaria</td>";
        } else {
            echo "<td>0</td>";
        }
        echo "</tr>";
        echo "<tr>";
        echo "<td>Total de projetos não submetidos a Plenária:</td>";
        if (isset($naoSubmetidosPauta)) {
            echo "<td>$naoSubmetidosPauta</td>";
        } else {
            echo "<td>0</td>";
        }
        echo "</tr>";
        echo "<tr >";
        echo "<td>Total de projetos em pauta</td>";
        if (isset($somaProjetos)) {
            echo "<td>$somaProjetos</td>";
        } else {
            echo "<td>0</td>";
        }
        echo "</tr>";
        echo "<tr>";
        echo "<form action='" . $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao' )) . "' method='post'>";
        if (isset($tipousuario) && $tipousuario == "presidente") {
            if ($value->stEstado == 0 and Data::CompararDatas($value->DtFechamento) >= 0) {
                echo "<input name='idReuniao' type='hidden' value='";
                echo $this->consultaProjetosPautaReuniao[0]->NumeroReuniao;
                echo "'>";
                echo "<input type='hidden' value='encerrar' name='reuniao'>";
                echo "<td></td>";
                echo "<td><input type='submit' size='30' value='Encerrar Reunião'></td>";
            } else {
                echo "<input name='idReuniao' type='hidden' value='";
                echo $this->consultaProjetosPautaReuniao[0]->NumeroReuniao;
                echo "'>";
                echo "<input type='hidden' value='iniciar' name='reuniao'>";
                echo "<td></td>";
                echo "<td><input type='submit' size='30' value='Fechar pauta/iniciar reunião'></td>";
            }
        }
        echo "</form>";
        echo "</tr>";
        echo "</table>";
        echo "</div>";
        $i ++;
    }
}

?>
