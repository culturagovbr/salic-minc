<form action="<?php echo $this->url(array('module'=> 'default','controller' => 'Analisarprojetoparecer', 'action'=>'projetosprodutos')); ?>" method="post" name="frmPaginacao" id="frmPaginacao<?php echo $this->nrRelatorio;?>">
    <?php foreach ($this->parametrosBusca as $name=>$value):?>
        <?php if (!is_array($value)): ?>
    <input type="hidden" name="<?php echo $name?>" id="<?php echo $name?>" value="<?php echo $value?>"/>
        <?php else: ?>
            <?php foreach ($value as $valor):?>
    <input type="hidden" name="<?php echo $name?>[]" id="<?php echo $name?>" value="<?php echo $valor?>"/>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if (empty($this->parametrosBusca["pag"])):?>
    <input type="hidden" name="pag" id="pag" value="<?php echo $this->pag; ?>"/>
    <?php endif; ?>
</form>

<form method="POST" id="formGerarXls" name="formGerarXls" action="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'gerar-xls')); ?>" style="display:none">
    <textarea name="html" id="conteudoXLS"></textarea>
</form>

<form method="POST" id="formGerarPdf" name="formGerarPdf" action="<?php echo $this->url(array('controller' => 'operacional', 'action' => 'gerar-pdf')); ?>" style="display:none">
    <textarea name="html" id="conteudoImprimir"></textarea>
</form>

<table class="tabela" style='width: 100%;'>
    <tr>
        <td align="center">
            <input type="button" style="width: 88px" class="btn_inicio" id="btn_inicio" onclick="paginar('1')"/>
            <input type="button" style="width: 88px" class="btn_p_anterior" id="btn_p_anterior" onclick="paginar('<?php echo ($this->pag > 1)?$this->pag-1:1; ?>')"/>
            <select name="pagina" id="pagina" class="input_simples" onchange="paginar(this.value)">
                <?php for ($i=1; $i<$this->totalPag+1; $i++): ?>
                <option value="<?php echo $i; ?>" <?php if ($i == $this->pag) {
    echo " selected='selected' ";
} ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
            </select>
            <input type="button" style="width: 88px" class="btn_p_proximo" id="btn_p_proximo" onclick="paginar('<?php echo ($this->pag < $this->totalPag)?$this->pag+1:$this->totalPag; ?>')"/>
            <input type="button" style="width: 88px" class="btn_ultimo" id="btn_ultimo" onclick="paginar('<?php echo $this->totalPag; ?>')"/>
            <!--<input type="button" style="width: 44px" class="btn_xls" id="btn_xls" onclick='$("#conteudoXLS").val($(".conteudoImprimivel").html()); $("#formGerarXls").submit();'/>
            <input type="button" style="width: 88px" class="btn_imprimir" id="btn_imprimir" onclick='$("#conteudoImprimir").val($(".conteudoImprimivel").html()); $("#formGerarPdf").submit();'/>-->
            Exibindo de <b><?php echo ($this->fim==0)? 0:$this->inicio; ?></b> a <b><?php echo $this->fim; ?></b> de um total de <b><?php echo $this->total; ?></b>
        </td>
    </tr>
</table>
<script type="text/javascript">
    function paginar(pag){
        var form = '#frmPaginacao'+$('#nrRelatorio').val();
        var nrRelatorio = $('#nrRelatorio').val();
        $("#pag").val(pag);
        var array = $(form).serialize();
        $('#div_relatorio'+nrRelatorio).html('<table class="tabelaRelatorio" cellspacing="1" style="margin: 0;margin-left: 52px; width: 90%;  padding: 0px;"><tr><th>Carregando...</th><tr></table>');
        $.post("<?php echo $this->baseUrl(); ?>/Analisarprojetoparecer/projetosprodutos",array,function(data){
            $('#div_relatorio'+nrRelatorio).html(data);
        });
    }
</script>
