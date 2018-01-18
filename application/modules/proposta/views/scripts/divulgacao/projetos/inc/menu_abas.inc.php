<?php $pronac = (isset($_GET['idPronac']) ? $_GET['idPronac'] : ''); ?>
<!-- ========== MENU ========== -->
<table id="tabelaLink" class="tabela">
    <tr>
        <th <?php if (strstr($this->url(), 'parecerconsolidadomodal') == 'parecerconsolidadomodal'
    ) {
    echo "class=\"bg_white\"";
} ?>><a href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'parecerconsolidadomodal')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>&idReuniao=<?php echo $_GET['idReuniao']; ?>&tipousuario=<?php echo $_GET['tipousuario']; ?>&tipoprojeto=<?php echo $_GET['tipoprojeto']; ?>">Analisar Parecer Consolidado</a></th>
        <th <?php if (strstr($this->url(), 'analisedeconta') == 'analisedeconta') {
    echo "class=\"bg_white\"";
} ?>><a href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'analisedeconta')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>&idReuniao=<?php echo $_GET['idReuniao']; ?>&tipousuario=<?php echo $_GET['tipousuario']; ?>&tipoprojeto=<?php echo $_GET['tipoprojeto']; ?>">Análise de Cortes Sugeridos</a></th>
        <th <?php if (strstr($this->url(), 'analisedeconteudo') == 'analisedeconteudo') {
    echo "class=\"bg_white\" ";
} ?>><a href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'analisedeconteudo')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>&idReuniao=<?php echo $_GET['idReuniao']; ?>&tipousuario=<?php echo $_GET['tipousuario']; ?>&tipoprojeto=<?php echo $_GET['tipoprojeto']; ?>">Análise de Conteúdo</a></th>
        <th <?php if (strstr($this->url(), 'analisedecustos') == 'analisedecustos') {
    echo "class=\"bg_white\"";
} ?>><a href="<?php echo $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'analisedecustos')); ?>?idPronac=<?php echo $_GET['idPronac']; ?>&idReuniao=<?php echo $_GET['idReuniao']; ?>&tipousuario=<?php echo $_GET['tipousuario']; ?>&tipoprojeto=<?php echo $_GET['tipoprojeto']; ?>" id="custos">Análise de Custos</a></th>
        <?php
        if ($_GET['tipousuario'] == 'a' and $_GET['tipoprojeto'] == 'S') {
            echo "<th ";
            if (strstr($this->url(), 'aprovarparecer') == 'aprovarparecer') {
                echo "class=\"bg_white\"";
            }
            echo"> <a href=\"" . $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'aprovarparecer')) . "?idPronac=" . $_GET['idPronac'] . "&idReuniao=" . $_GET['idReuniao'] . "&tipoprojeto=" . $_GET['tipoprojeto'] . "&tipousuario=" . $_GET['tipousuario'] . "\">Retirar da Plenária</a></th>";
        } elseif ($_GET['tipoprojeto'] == 'N') {
            echo "<th ";
            if (strstr($this->url(), 'aprovarparecer') == 'aprovarparecer') {
                echo "class=\"bg_white\"";
            }
            echo"> <a href=\"" . $this->url(array('controller' => 'gerenciarpautareuniao', 'action' => 'aprovarparecer')) . "?idPronac=" . $_GET['idPronac'] . "&idReuniao=" . $_GET['idReuniao'] . "&tipoprojeto=" . $_GET['tipoprojeto'] . "&tipousuario=" . $_GET['tipousuario'] . "\">Submeter Plenária</a></th>";
        }
        ?>
    </tr>
</table>

<div id="load" class="carregando sumir" style="width:100%; height:100%;" title="Carregando..."><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" alt="carregando"><br/><br/>Carregando...<br>Por Favor, aguarde!!</div>

<script>
    $('document').ready(function(){
        $('#tabelaLink').find('a').each(function(){
            $(this).click(function()
            {
                $.get($(this).attr('href'),{idPronac:<?php echo $pronac; ?>},function(data)
                {
                    $("#tela").html(data);                    
                });
                return false;
            });
        });
    });
</script>