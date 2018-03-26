<?php
$geraranexoHREF =   $this->url(array('controller' => 'gerarrelatorioparecerista', 'action' => 'geraranexo'));
?>
<form id="frGerarAnexo" action="<?php echo $geraranexoHREF;?>" method="post" target="_blank">
    <input type="hidden" name="tipo" id="tipo" value="" />
    <input type="hidden" name="tela"    value="<?php echo $this->tela;?>" />
    <input type="hidden" name="filtro"  value="<?php echo $this->filtro;?>" />
    <?php
    foreach ($this->post as $key=>$info) {
        if (!is_array($info)) {
            ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $info; ?>"/>
            <?php
        } else {
            foreach ($info as $key2=>$info2) {
                ?>
                <input type="hidden" name="cpconsulta_dest[]" value="<?php echo $info2; ?>"/>
                <?php
            }
        }
    }
    ?>
</form>
<script>
    $(function(){
        $('.btn_pdf').click(function(){
            $('#frGerarAnexo #tipo').val('pdf');
            $('#frGerarAnexo').submit();
        });
        $('.btn_xls').click(function(){
            $('#frGerarAnexo #tipo').val('xls');
            $('#frGerarAnexo').submit();
        });
    })
</script>