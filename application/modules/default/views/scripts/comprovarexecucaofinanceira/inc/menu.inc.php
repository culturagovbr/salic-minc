<?php
$urlParams = array('controller' => 'comprovarexecucaofinanceira', 'idusuario' => $this->idusuario, 'idpronac' => $this->idpronac,);
$licitacaoHref = $this->url(array_merge($urlParams, array('action' => 'licitacao',)));
$cotacaoHref = $this->url(array_merge($urlParams, array('action' => 'cotacao',)));
$dispensaHref = $this->url(array_merge($urlParams, array('action' => 'dispensa',)));
$contratoHref = $this->url(array_merge($urlParams, array('action' => 'contrato',)));
$finalizarHref = $this->url(array_merge($urlParams, array('action' => 'finalizar',)));
$pagamentoHref = $this->url(array_merge($urlParams, array('action' => 'pagamento',)), null, true);
$licitacaoAnteriorHref = $this->url(array_merge($urlParams, array('action' => 'licitacaoanterior',)));
$vincularCEFHref = $this->url(array_merge($urlParams, array('action' => 'vincularcomprovacao',)));

$menuHref                   =   $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'menu')).'?idusuario='.$this->idusuario;
$contratoItemHref           =   $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'carregacontratoajax')).'?idusuario='.$this->idusuario;
$ExcluirItensCustoHref      =   $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'excluiritenscusto')).'?idusuario='.$this->idusuario;
$carregarSelectHref         =   $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'carregaselectajax')).'?idusuario='.$this->idusuario;
$vincularitemcustoHref      =   $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'vincularitemcusto')).'?idusuario='.$this->idusuario;
$buscarFornecedorHref       =   $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'buscarfornecedor')).'?idusuario='.$this->idusuario;

$finalizadoHref              =   $this->url(array('controller' => 'comprovarexecucaofinanceira', 'action' => 'finalizado')).'?cadastro=1'.'&idusuario='.$this->idusuario;
?>
<style type="text/css">
    .sanfonaDiv {
        clear: both;
        display: none;
    }
    .displayNone{
        display: none;
    }
</style>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl(); ?>/public/scripts/quickmenu.js"></script>
<div id="menu">
    <div id="container">
        <script type="text/javascript">
            function layout_fluido()
            {
                var janela = $(window).width();

                var fluidNavGlobal = janela - 245;
                var fluidConteudo = janela - 253;
                var fluidTitulo = janela - 252;
                var fluidRodape = janela - 19;

                $("#navglobal").css("width",fluidNavGlobal);
                $("#conteudo").css("width",fluidConteudo);
                $("#titulo").css("width",fluidTitulo);
                $("#rodapeConteudo").css("width",fluidConteudo);
                $("#rodape").css("width",fluidRodape);

                $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");

            }
        </script>
        <div id="menuContexto">
            <div class="top"></div>
            <div id="qm0" class="qmmc">
                <a href="<?php echo $pagamentoHref; ?>" title="Ir para Pagamento" class="no_seta">Pagamento</a>
                <a href="<?php echo $this->url(array_merge($urlParams, array('action' => 'licitacao',)), null, true); ?>"
                    title="Ir para Licita&ccedil;&atilde;o" class="no_seta">Licita&ccedil;&atilde;o
                </a>
                <a href="<?php echo $cotacaoHref; ?>" title="Ir para Cota&ccedil;&atilde;o" class="no_seta">Cota&ccedil;&atilde;o</a>
                <a href="<?php echo $dispensaHref; ?>" title="Ir para Dispensa" class="no_seta">Dispensa</a>
                <a href="<?php echo $contratoHref; ?>" title="Ir para Contrato" class="no_seta">Contrato</a>
                <span class="no_seta last">&nbsp;</span>
            </div>
            <div class="bottom"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
var nomeJanelaAux = undefined;
function salvar(info){
    var form = '#'+info.corpo+' #'+info.idformulario;
    if(validarFormulario(form,ag1)){
        var pagina = requisicaoAjaxObj();
        pagina.executar({
            pagina: $(info.este).attr('href'),
            parametros:$(form).serializeArray(),
            resposta: undefined,
            funcaoRetorno:function(resp){
                if(resp.result){
                    $('#'+info.corpo+' .displayNone').removeClass('displayNone');
                    if(info.idNome!=undefined)
                        $('#'+info.corpo+' #'+info.idformulario+' #'+info.idNome).val(resp[info.idNome]);
                }
                janelaAlerta(resp.mensagem, null, resp.fechar);
            }
            ,dataType:'json'
        });
    }
}
function acoesLink(info){
    if($(info.este).attr('padrao')!='false'){
        if($(info.este).attr('excluir')=='true'){
            var este = info.este;
            var nomeJanelaAlerta =   janelaObj({
                parametros : {
                    width:      400,
                    autoOpen:   false,
                    resizable:  false,
                    modal:      true,
                    buttons: {
                        'N\u00e3o':function(){
                            $(this).dialog('close');
                        },
                        Sim: function() {
                            var carregarCont    =   requisicaoAjaxObj();
                            carregarCont.executar({
                                pagina      :   $(este).attr('href'),
                                parametros  :   {id:$(este).attr('id')},
                                resposta    :   '',
                                async       :   false,
                                funcaoRetorno : function(resposta){
                                    if(resposta.resp)
                                        $('#'+info.corpo+' #'+$(este).attr('complemento')+$(este).attr('id')).remove();
                                    if(resposta.mensagem != ''){
                                        janelaAlerta(resposta.mensagem);
                                    }
                                }
                                ,dataType : 'json'
                            });

                            $(this).dialog('close');
                        }
                    }
                },
                removerBtFechar:true,
                title : 'Alerta'
            });
            if($(info.este).attr('excluir')=='true')
                nomeJanelaAlerta.divConteudo.html(ag4);
            nomeJanelaAlerta.abrirJanela();
        }
        else{
            if($(info.este).attr('modal')=='true'){
                var parametro = {};
                if($(info.este).attr('somentevisualizar')=='true')
                    parametro = {
                        width:800,
                        autoOpen: false,
                        modal:true,
                        resizable: false,
                        buttons: {
                            Sair:function(){
                                $(this).dialog('close');
                                nomeJanelaAux.divConteudo.html('');
                            }
                        }
                    };
                else
                    parametro = {
                        width:800,
                        autoOpen: false,
                        modal:true,
                        resizable: false,
                        buttons: {
                            Cancelar:function(){
                                $(this).dialog('close');
                                nomeJanelaAux.divConteudo.html('');
                            },
                            Salvar: function() {
                                $('#'+info.formularioModal).submit();
                                if(info.naoFechar == undefined){
                                    $(this).dialog('close');
                                    nomeJanelaAux.divConteudo.html('');
                                }
                            }
                        }
                    };
                nomeJanelaAux      =   janelaObj({
                    parametros : parametro,
                    title : $(info.este).attr('title')
                });
                var carregarCont    =   requisicaoAjaxObj();
                info.parametros.id = $(info.este).attr('id');
                carregarCont.executar({
                    pagina      :   $(info.este).attr('href'),
                    parametros  :   info.parametros,
                    resposta    :   nomeJanelaAux.divConteudo
                });
                nomeJanelaAux.abrirJanela();
            }
            else{
                var pagina = requisicaoAjaxObj();
                pagina.executar({
                    pagina      :   $(info.este).attr('href'),
                    parametros  :   info.parametros
                });
            }
        }
    }
}
function linhaPEI(info){

    var vinculado = true;
    if(info.cadastro == undefined)
        info.cadastro = false;
    if(info.cadastro){
        var select = requisicaoAjaxObj();
        select.executar({
            pagina: '<?php echo $vincularitemcustoHref;?>',
            parametros:info,
            async:false,
            resposta: undefined,
            funcaoRetorno:function(dados){
                vinculado = dados.vinculado;
                if(dados.mensagem != undefined && dados.mensagem != ''){
                    janelaAlerta(dados.mensagem);
                }
            }
            ,dataType:'json'
        });
    } else{
        vinculado = false;
    }


    if(!info.cadastro || vinculado){

        var tr = $('<tr></tr>').appendTo(info.tabela);
        montarColuna(tr,info.produto,'produto[]',info.idProduto);
        montarColuna(tr,info.etapa,'etapa[]',info.idEtapa);
        montarColuna(tr,info.itens,'item[]',info.idItem,'item');

        if(info.cotacao){
            montarColuna(tr,info.fornecedor,'fornecedor[]',info.idFornecedor);
        }
        if(info.comprovar){
            montarColuna(tr,info.valor,'vlComprovado[]',info.valor.replace(/\.|,/g,''),'valores');
        }
        if(info.desvincular == undefined){
            var td = montarColuna(tr,'');
            $('<a></a>')
                .attr('href','<?php echo $ExcluirItensCustoHref;?>')
                .attr('idProduto'   ,   info.idProduto)
                .attr('idEtapa'     ,   info.idEtapa)
                .attr('idItem'      ,   info.idItem)
                .attr('idpronac'    ,   info.idpronac)
                .html('<center><img border="0" title="Excluir" src="<?php echo $this->baseUrl(); ?>/public/img/buttons/excluir.gif" /></center>')
                .click(function(){
                    var este = this;

                    var contador = 0;
                    $(info.tabela+' tr ').each(function(){
                        contador++;
                    });
                        var nomeJanelaAlerta =   janelaObj({
                            parametros : {
                                width:      400,
                                autoOpen:   false,
                                resizable:  false,
                                modal:      true,
                                buttons: {
                                    'N\u00e3o':function(){
                                        $(this).dialog('close');
                                    },
                                    Sim: function() {
                                        var idcotacao = '';
                                        if($('#idcotacao').val() != undefined)
                                            idcotacao = $('#idcotacao').val();
                                        var iddispensa = '';
                                        if($('#iddispensa').val() != undefined)
                                            iddispensa = $('#iddispensa').val();
                                        var idlicitacao = '';
                                        if($('#idlicitacao').val() != undefined)
                                            idlicitacao = $('#idlicitacao').val();
                                        var idcontrato = '';
                                        if($('#idcontrato').val() != undefined)
                                            idcontrato = $('#idcontrato').val();
                                        if(idcotacao!='' || iddispensa!='' || idlicitacao!='' || idcontrato!='' ){
                                            var dados = {
                                                idcotacao       :   idcotacao,
                                                iddispensa      :   iddispensa,
                                                idlicitacao     :   idlicitacao,
                                                idcontrato      :   idcontrato,
                                                idProduto       :   $(este).attr('idProduto'),
                                                idEtapa         :   $(este).attr('idEtapa'),
                                                idItem          :   $(este).attr('idItem'),
                                                idpronac        :   $(este).attr('idpronac')
                                            };
                                            var resp = buscarJson($(este).attr('href'),dados);
                                            if(resp.resp)
                                                $(tr).remove();
                                            janelaAlerta(resp.mensagem);
                                        }
                                        else{
                                            $(tr).remove();
                                            janelaAlerta('Excluído com sucesso!');
                                        }

                                        $(this).dialog('close');
                                    }
                                }
                            },
                            removerBtFechar:true,
                            title : 'Alerta'
                        });
                        nomeJanelaAlerta.divConteudo.html(ag4);
                        nomeJanelaAlerta.abrirJanela();
                    return false;
                })
                .appendTo(td);
        }
    }
}

function verificandoExistenciaAdd(info){
    var verificandoExistencia = false;
    $(info.corpo+' .item').each(function(){
        if($(this).val() == info.idItem)
            verificandoExistencia = true;
    });

    return verificandoExistencia;
}
function carregarPEI(info){
    info.pagina =   '<?php echo $contratoItemHref;?>';
    if(info.idcotacao == undefined)
        info.idcotacao = '';
    if(info.iddispensa == undefined)
        info.iddispensa = '';
    if(info.idlicitacao == undefined)
        info.idlicitacao = '';
    if(info.idcontrato == undefined)
        info.idcontrato = '';
    var respostaProduto = buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'produtoCarga',idpronac:'<?php echo $this->idpronac?>',idCotacao:info.idcotacao,idDispensaLicitacao:info.iddispensa,idLicitacao:info.idlicitacao,idContrato:info.idcontrato});
    for(var k in respostaProduto){
        info.idProduto = respostaProduto[k].id;
        info.produto   = respostaProduto[k].nome;
        if(info.idProduto == null){
            info.idProduto = 0;
            info.produto   = 'Administra&ccedil;&atilde;o do Projeto';
        }
        var respostaEtapa    = buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'etapaCarga',idpronac:'<?php echo $this->idpronac?>',idProduto:info.idProduto,idCotacao:info.idcotacao,idDispensaLicitacao:info.iddispensa,idLicitacao:info.idlicitacao,idContrato:info.idcontrato});
        for(var j in respostaEtapa){
            info.idEtapa = respostaEtapa[j].id;
            info.etapa   = respostaEtapa[j].nome;
            var respostaItem    =   buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'itensCarga',idpronac:'<?php echo $this->idpronac?>',idProduto:info.idProduto,idEtapa:info.idEtapa,idCotacao:info.idcotacao,idDispensaLicitacao:info.iddispensa,idLicitacao:info.idlicitacao,idContrato:info.idcontrato});
            for(var i in respostaItem){
                info.idItem = respostaItem[i].id;
                info.itens   = respostaItem[i].nome;

                if(info.cotacao){
                    info.fornecedor              =   respostaItem[i].Fornecedor;
                    info.idFornecedor            =   respostaItem[i].idAgente;
                    info.classFornecedor         =   'fornecedor'+buscarNumeroSelect(info.corpo+' #fornecedor',info.idFornecedor);
                }

                linhaPEI(info);
            }
        }
    }
}
function listaPEI(info){
    info.pagina =   '<?php echo $contratoItemHref;?>';
    if(info.comprovar == undefined)
        info.comprovar = false;
    if(info.cotacao == undefined)
        info.cotacao = false;
    if(info.idFornecedor == undefined)
        info.idFornecedor = false;
    if(info.contrato == undefined)
        info.contrato = '';
    // Valida cotacao / fornecedor
    if (info.cotacao && !info.idFornecedor) {
        janelaAlerta('Selecione um Fornecedor!');
        return;
    }

    //
        if(!info.comprovar || (info.valor != undefined && info.valor != '')){
            var nomeJanelaAguarde =   janelaObj({
                parametros : {
                    width:      400,
                    autoOpen:   false,
                    resizable:  false,
                    modal:      true
                },
                removerBtFechar:true,
                title : 'Alerta'
            });
            nomeJanelaAguarde.divConteudo.html('Aguarde!');
            nomeJanelaAguarde.abrirJanela();
            if(info.idProduto == '' && info.idEtapa == '' && info.idItem == ''){
                var respostaProduto = buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'produto',idpronac:'<?php echo $this->idpronac;?>',contrato:info.contrato,ckItens:info.ckItens});
                for(var k in respostaProduto){
                    info.idProduto = respostaProduto[k].id;
                    info.produto   = respostaProduto[k].nome;
                    var respostaEtapa    = buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'etapa',idpronac:'<?php echo $this->idpronac;?>',idProduto:info.idProduto,contrato:info.contrato,ckItens:info.ckItens});
                    for(var j in respostaEtapa){
                        info.idEtapa = respostaEtapa[j].id;
                        info.etapa   = respostaEtapa[j].nome;
                        var respostaItem    =   buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'itens',idpronac:'<?php echo $this->idpronac;?>',idProduto:info.idProduto,idEtapa:info.idEtapa,contrato:info.contrato,ckItens:info.ckItens});
                        for(var i in respostaItem){
                            info.idItem = respostaItem[i].id;
                            info.itens   = respostaItem[i].nome;
                            if(!verificandoExistenciaAdd(info))
                                linhaPEI(info);
                        }
                    }
                }

            } else {
                if(info.idEtapa == '' && info.idItem == ''){
                    var respostaEtapa    = buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'etapa',idpronac:'<?php echo $this->idpronac;?>',idProduto:info.idProduto,contrato:info.contrato,ckItens:info.ckItens});
                    for(var j in respostaEtapa){
                        info.idEtapa = respostaEtapa[j].id;
                        info.etapa   = respostaEtapa[j].nome;
                        var respostaItem    =   buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'itens',idpronac:'<?php echo $this->idpronac;?>',idProduto:info.idProduto,idEtapa:info.idEtapa,contrato:info.contrato,ckItens:info.ckItens});
                        for(var i in respostaItem){
                            info.idItem = respostaItem[i].id;
                            info.itens   = respostaItem[i].nome;
                            if(!verificandoExistenciaAdd(info))
                                linhaPEI(info);
                        }
                    }
                } else {
                    if(info.idItem == ''){
                        var respostaItem = buscarJson('<?php echo $carregarSelectHref;?>',{tpSelect:'itens',idpronac:'<?php echo $this->idpronac?>',idProduto:info.idProduto,idEtapa:info.idEtapa,contrato:info.contrato,ckItens:info.ckItens});
                        for(var i in respostaItem){
                            info.idItem = respostaItem[i].id;
                            info.itens   = respostaItem[i].nome;
                            if(!verificandoExistenciaAdd(info))
                                linhaPEI(info);
                        }
                    } else {
                        if(!verificandoExistenciaAdd(info))
                            linhaPEI(info);
                        else{
                            janelaAlerta('Este item j\u00e1 foi adicionado!');
                        }
                    }
                }
            }
            nomeJanelaAguarde.fecharJanela();
        }
        else{
            janelaAlerta('Digite o valor do item!');
        }

//    carregarPgHtml('<?php //echo $carregarSelectHref;?>','#produto',{tpSelect:'produto',idpronac:'<?php //echo $this->idpronac?>',contrato:info.contrato,ckItens:info.ckItens});
//    carregarPgHtml('<?php //echo $carregarSelectHref;?>','#etapa',{tpSelect:'etapa',idpronac:'<?php //echo $this->idpronac?>',idProduto:$('#produto').val(),contrato:info.contrato,ckItens:info.ckItens});
//    carregarPgHtml('<?php //echo $carregarSelectHref;?>','#itens',{tpSelect:'itens',idpronac:'<?php //echo $this->idpronac?>',idProduto:$('#produto').val(),idEtapa:$('#etapa').val(),contrato:info.contrato,ckItens:info.ckItens});
    if(info.cotacao)
        $('#vlComprovado').val('');

}
function janelaAlerta(mensagem,funcaoAdcional, fechar){
    if(funcaoAdcional==undefined){
        funcaoAdcional = function(){}
    }
    var nomeJanelaAlerta =   janelaObj({
            parametros : {
                width:      450,
                autoOpen:   false,
                resizable:  false,
                modal:      true,
                buttons: {
                    OK: function() {
                        funcaoAdcional();
                        $(this).dialog('close');
                        if (fechar == 'ok')
                        {
                        	window.location.reload();
                        }
                    }
                }
            },
            removerBtFechar:true,
            title : 'Alerta'
        });
        nomeJanelaAlerta.divConteudo.html(mensagem);
        nomeJanelaAlerta.abrirJanela();

        return nomeJanelaAlerta;
}
function buscarHtmlSelect(id){
    var valorRetorno = '';
    $(id+' option ').each(function(){
        if($(this).val()==$(id).val())
            valorRetorno = $(this).html();
    });
    return valorRetorno;
}
function buscarNumeroSelect(id,valor){
    var valorRetorno = 0;
    var contador     = 0;
    $(id+' option ').each(function(){

        if(valor != undefined){
            if($(this).val() == valor)
                valorRetorno = contador;
        }
        else{
            if($(this).val() == $(id).val())
                valorRetorno = contador;
        }
        contador++;
    });
    return valorRetorno;
}
function montarColuna(linha, vwItem, nmCampo, vlCampo, classe) {
    var coluna = $('<td></td>')
            .html(vwItem);
    coluna.addClass('td_'+classe)
            .appendTo(linha);
    if(nmCampo != undefined && vlCampo != undefined ){
        $('<input />')
            .attr('type','hidden')
            .attr('name',nmCampo)
            .val(vlCampo)
            .appendTo(linha)
            .addClass(classe);
    }
    return coluna;
}
function buscarJsonAux(pagina,dados){
    var retorno = '';
    var select = requisicaoAjaxObj();
    select.executar({
        pagina          :   pagina,
        parametros      :   dados,
        resposta        :   undefined,
        async           :   false,
        funcaoRetorno   :   function (resposta){
            alert(resposta);
            retorno = resposta;
        }
        //,dataType        :   'json'
    });
    return retorno;
}
function carregarPgHtmlAux(pagina,idSelect,dados){
    var resposta    = buscarJsonAux(pagina,dados);
}
function buscarJson(pagina,dados){
    var retorno = '';
    var select = requisicaoAjaxObj();
    select.executar({
        pagina          :   pagina,
        parametros      :   dados,
        resposta        :   undefined,
        async           :   false,
        funcaoRetorno   :   function (resposta){
            retorno = resposta;
        }
        ,dataType        :   'json'
    });
    return retorno;
}
function carregarPgHtml(pagina,idSelect,dados,textoIni){
    var idRetorno = undefined;
    if(textoIni == undefined)
        textoIni = 'Todos';

    var resposta    = buscarJson(pagina,dados);
    var cont = 0;
    for(var j in resposta){
        if (resposta[j].nome.length > 40) {
            var stringValue = resposta[j].nome.substr(0, 40) + ' ...';
        } else {
            var stringValue = resposta[j].nome;
        }
        select += '<option value="'+resposta[j].id+'">'+stringValue+'</option>';
        cont++;
        if(cont>1)
            idRetorno = undefined;
        else
            idRetorno = resposta[j].id;
    }
    if(cont>1 || cont==0)
        var select = select;
    $(idSelect).html(select);
    return idRetorno;
}
function layout_fluido()
{
    var janela = $(window).width();
    var fluidNavGlobal = janela - 245;
    var fluidConteudo = janela - 253;
    var fluidTitulo = janela - 252;
    var fluidRodape = janela - 19;
    $("#navglobal").css("width",fluidNavGlobal);
    $("#conteudo").css("width",fluidConteudo);
    $("#titulo").css("width",fluidTitulo);
    $("#rodapeConteudo").css("width",fluidConteudo);
    $("#imagemRodape").css("width",fluidConteudo);
    $("#rodape").css("width",fluidRodape);
    $("div#rodapeConteudo").attr("id", "rodapeConteudo_com_menu");
}

function validaDataCorreta(id,ano,mes,dia){
    if(mes>12){
        janelaAlerta("Data incorreta!");
        $(id).val('');
        return false;
    }
    if(ano<2005){
        janelaAlerta("Favor utilizar uma data posterior ao ano de 2005.");
        $(id).val('');
        return false;
    }
    if(dia <= 31){
        if( (mes==4 || mes==6 || mes==9 || mes==11) && dia > 30){
            janelaAlerta("Dia incorreto ! O m&ecirc;s especificado cont&eacute;m no m&aacute;ximo 30 dias.");
            $(id).val('');
        } else{
            if(ano%4!=0 && mes==2 && dia>28){
                janelaAlerta("Data incorreta! O m&ecirc;s especificado cont&eacute;m no m&aacute;ximo 28 dias.");
                $(id).val('');
            } else{
                if(ano%4==0 && mes==2 && dia>29){
                    janelaAlerta("Data incorreta! O m&ecirc;s especificado cont&eacute;m no m&aacute;ximo 29 dias.");
                    $(id).val('');
                }
            }
        }
    }
    else{
        janelaAlerta('Digite uma data v&aacute;lida!');
        $(id).val('');
    }
}
function mascaras(form){
    $('input[type!=button][type!=submit]').addClass('input_simples');
    $('textarea').addClass('textarea_simples');
    $('select[id!=perfilXgrupo]').addClass('select_simples');
    $(form).find('[data=true]').each(function(){
        $(this).keyup(function(){
            mascara(this,format_data);
            var este = this;
            setTimeout(function(){
                $(este).val($(este).val().substr(0,10));
            },2);
            if($(este).val().length == 10){
                validaDataCorreta(este,$(este).val().substr(6,4),$(este).val().substr(3,2),$(este).val().substr(0,2));
            }
        });
        $(this).datepicker({
                showOn: 'button',
                buttonImage: '<?php echo $this->baseUrl(); ?>/public/img/ico/calendar.gif',
                buttonImageOnly: true,
                dateFormat:'dd/mm/yy'
        });
    });
    $(form).find('[sonumero=true]').keyup(function(){
        mascara(this,format_num);
    });
    $(form).find('[dinheiro=true]').keyup(function(){
        mascara(this,format_moeda);
    });

    $(form).find('[cnpjcpf=true]').each(function(){
        var este = this;
        $('.'+$(este).attr('classaux')).click(function(){
            mascaraCNPJCPF(este);
            buscarFornecedor(este);
        });
        $('#'+$(este).attr('idDescricao')).attr('readonly',true);
    });
    $(form).find('[cnpjcpf=true]').keyup(function(){
        mascaraCNPJCPF(this);
        buscarFornecedor(this);
    });
}
function buscarRadioMarcado(este){
    var marcado = '';
    $('.'+$(este).attr('classaux')).each(function(){
        if($(this).attr('checked')){
            marcado = $(this).val();
        }
    });
    return marcado;
}
function mascaraCNPJCPF(este){
    $('#cpf').val('cpf');
    $('#cnpj').val('cnpj');
    var marcado = buscarRadioMarcado(este);
    switch(marcado){
        case 'cpf':
            $(este).val($(este).val().slice(0,14));
            $(este).attr('maxlength',14);
            mascara(este,format_cpf);
            break;
        case 'cnpj':
            $(este).attr('maxlength',18);
            mascara(este,format_cnpj);
            break;
        default:
            janelaAlerta('Selecione o Tipo do Fornecedor');
            $(este).val('');
    }
}
function buscarFornecedor(este){
    if($(este).attr('idAgente') != undefined && $(este).attr('idAgente') && $(este).attr('idDescricao') != undefined && $(este).attr('idDescricao')){
        var marcado = buscarRadioMarcado(este);
        if((marcado == 'cpf' && $(este).val().length == 14) || (marcado == 'cnpj' && $(este).val().length == 18)){
            var fornecedor = buscarJson('<?php echo $buscarFornecedorHref;?>',{cnpjcpf:$(este).val()});
            if(fornecedor.retorno){
               $('#'+$(este).attr('idAgente')).val(fornecedor.idAgente);
               $('#'+$(este).attr('idDescricao')).val(fornecedor.descricao).attr('readonly',true);

            } else {
                $('html').css('overflow', 'hidden');
                $("body").append("<div id='divDinamicaAgentes'></div>");
                $("#divDinamicaAgentes").html("");
                $('#divDinamicaAgentes').html("<br><br><center>Carregando dados...</center>");
                $.ajax({
                    url : '<?php echo $this->url(array('module' => 'agente', 'controller' => 'agentes', 'action' => 'incluirfornecedor')); ?>',
                    data : {
                        cpf : fornecedor.CNPJCPF,
                        caminho: "",
                        modal : "s"
                    },
                    success: function(data){
                        if (data.error) {
                            $('#divDinamicaAgentes').html(data.msg);
                        } else {
                            $('#divDinamicaAgentes').html(data);
                        }
                    },
                    complete: function(){
                        $("#resultadoFinalizar").html("");
                    },
                    type : 'post'

                });

                $("#divDinamicaAgentes").dialog({
                    resizable: true,
                    width:$(window).width() - 100,
                    height:$(window).height() - 100,
                    modal: true,
                    autoOpen:true,
                    draggable:false,
                    title: 'Cadastrar Fornecedor',
                    buttons: {
                        'Fechar': function() {
                            $("#divDinamicaAgentes").remove();
                            $('#'+$(este).attr('idAgente')).val('');
                            $('#'+$(este).attr('idDescricao')).val('');
                            $(this).dialog('close');
                            $('html').css('overflow', 'auto');
                            outroFornecedor(este);
                        }
                    }
                });
                $('.ui-dialog-titlebar-close').remove();
           }
        }
        else{
           $('#'+$(este).attr('idAgente')).val('');
           $('#'+$(este).attr('idDescricao')).val('');
       }
    }
    fornecedores();
}

function outroFornecedor(este){
    $("#divPerguntaFornecedor").dialog('close');
    $("#divPerguntaFornecedor").html('Deseja utilizar este fornecedor na comprovação do pagamento?');
    $("#divPerguntaFornecedor").dialog('open');
    $("#divPerguntaFornecedor").dialog({
        resizable: false,
        width: 320,
        height: 180,
        modal: true,
        draggable:false,
        title: 'Alerta!',
        buttons: {
            'Não': function(){
                $('#'+$(este).attr('idAgente')).val('');
                $('#'+$(este).attr('idDescricao')).val('');
                $('#CNPJCPF').val('');
                $("#divPerguntaFornecedor").dialog('close');
            },
            'Sim': function() {
                $(this).dialog('close');
                buscarFornecedor(este);
            }
        }
    });
    $('.ui-dialog-titlebar-close').remove();
}


function validarFormulario(form,mensagem){
    var validar = false;

    var contador = 0;
    $('.linhaMaior1 tr').each(function(){
        contador++;
    })
    if(mensagem== undefined){
        mensagem = '';
    }
    if(contador==1){
        validar = true;
        mensagem += '<br />Vincule o(s) Item(ns) de Custo! ';
    }

    $(form).find('[cnpjcpf=true]').each(function(){
        var este = this;
        var marcado = buscarRadioMarcado(este);
        if(marcado == 'cpf' && $(este).val().length < 14){
            validar = true;
            mensagem += '<br />Preencha o CPF.';
        }
        if(marcado == 'cnpj' && $(este).val().length < 18){
            validar = true;
            mensagem += '<br />Preencha o CNPJ.';
        }
    });

    $(form).find('[data=true]').each(function(){
        var estadata =   $(this).val().slice(6,10)+''+$(this).val().slice(3,5)+''+$(this).val().slice(0,2);

        if($(this).attr('menorque')!=undefined){
            var menorid     =   '#'+$(this).attr('menorque');
            if($(menorid).val() != ''){
                var menordata   =   $(menorid).val().slice(6,10)+''+$(menorid).val().slice(3,5)+''+$(menorid).val().slice(0,2);
                if(estadata > menordata){
                    validar     =   true;
                    mensagem    +=   '<br />A "'+$(this).attr('title')+'" deve ser menor que a "'+$(menorid).attr('title')+'"';
                }
            }
        }
        if($(this).attr('maiorque')!=undefined){
            var maiorid     =   '#'+$(this).attr('maiorque');
            if($(maiorid).val() != ''){
                var maiordata   =   $(maiorid).val().slice(6,10)+''+$(maiorid).val().slice(3,5)+''+$(maiorid).val().slice(0,2);
                if(estadata < maiordata){
                    validar = true;
                    mensagem    +=   '<br />A "'+$(this).attr('title')+'" deve ser maior que a "'+$(maiorid).attr('title')+'"';
                }
            }
        }
    });

    $(form).find('[null=false]').each(function(){
        if($(this).attr('type')=='radio'){
            var validaRadio = true;
            $(form+' [name='+$(this).attr('name')+']').each(function(){
                if($(this).attr('checked'))
                    validaRadio = false;
            });
            if(validaRadio)
                validar = true;
        }else{
            if($.trim($(this).val()).length == 0)
                validar = true;
        }
//        if(validar)
//            alert($(this).attr('name'));
    });

    if(validar){
        janelaAlerta(mensagem);
        return false;
    }
    else
        return true;
}

function requisicaoAjaxObj(){
    var ajaxObj={
        pagina          :   '',
        parametros      :   {},
        type            :   'post',
        dataType        :   '',
        resposta        :   '#conteudo',
        async           :   true,
        funcaoRetorno   :   function (resposta){
            $(this.resposta).html(resposta);
        },
        executar        :   function(dados){
            this.refineParametrosObj(dados);
            var esteObj = this;
            if(this.resposta != undefined && this.resposta != '')
                $(this.resposta).html('<img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" alt="carregando"><br/><br/>Carregando...<br>Por Favor, aguarde!!');
            $.ajax({
                type      : esteObj.type,
                url       : esteObj.pagina,
                data      : esteObj.parametros,
                async     : esteObj.async,
                success   : function(resp){
                    esteObj.funcaoRetorno(resp);
                }
                ,dataType  : esteObj.dataType
            });
        },
        refineParametrosObj : function(data){
            if(data!= undefined)
                for(var j in data){

                    this[j]=data[j];
                }
        }
    }
    return ajaxObj;
}
function janelaObj(dados){
    var divConteudo = $('<div></div>')
                        .attr('title',dados.title)
                        .appendTo('body');
    var novaJanela = {
        divConteudo : divConteudo,
        removerBtFechar: true,
        parametros : {autoOpen: false},
        iniciarJanela : function(dados){
            this.refineParametrosObj(dados);

            this.divConteudo.dialog(this.parametros);
        },
        abrirJanela:function(){
            this.divConteudo.dialog('open');
            if(this.removerBtFechar)
            $('.ui-dialog-titlebar-close').remove();
        },
        fecharJanela:function(){
            this.divConteudo.dialog('close');
            this.divConteudo.remove();
        },
        refineParametrosObj : function(data){
            if(data!= undefined)
                for(var j in data){
                    this[j]=data[j];
                }
        }
    }
    novaJanela.iniciarJanela(dados);
    return novaJanela;
}
function montagem(nr){
    var conteudo = '';
    if($('#Descricao'+nr).val()!=undefined){
        if($('#Descricao'+nr).val().replace(/\s+/g, '')){
            var idAgente = '';
            if($('#idAgente'+nr).val() == '')
                idAgente = '-'+nr;
            else
                idAgente = $('#idAgente'+nr).val();
            conteudo    +=  '<option value="'+idAgente+'">'+$('#Descricao'+nr).val()+'</option>';
            $('.fornecedor'+nr).each(function(){
                $(this).val(idAgente);
            });
            $('.td_fornecedor'+nr).each(function(){
                $(this).html($('#Descricao'+nr).val());
            });
        }
    }
    return conteudo;
}
function fornecedores(){
    var conteudo    =  '<option value="">Selecione</option>';
    conteudo += montagem(1);
    conteudo += montagem(2);
    conteudo += montagem(3);
    $('#fornecedor').html(conteudo);
}
</script>
<script type="text/javascript">
    var me71 = 'Esse projeto não possui itens de custo para comprovação financeira. Favor incluir todos os itens de custo para inclusão de comprovação financeira.';
    var me72 = 'Deseja realmente finalizar esse processo? Com essa confirmação você não poderá mais alterar os dados desse processo.';
    var ag1  = 'Dados obrigatórios não informados.';
    var ag4  = 'Deseja realmente excluir dados?';
    var sg1  = '';
    var sg2  = '';
    var eg1  = '';
    var eg2  = '';
    var sg14 = '';
    var me88 = '';
    var me85 = '';
    var me89 = '';
    var me90 = '';
    var me72 = '';
    var me99 = '';

    $(document).ready(function() {
        $.datepicker.regional['pt-BR'] = {
            closeText: 'Fechar',
            prevText: '&#x3c;Anterior',
            nextText: 'Pr&oacute;ximo&#x3e;',
            currentText: 'Hoje',
            monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
            dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 0,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['pt-BR']);

        // cadastrar agente callback event heandler
        $(document).bind('agenteCadastrar_POST', function(event){
            var agente = event.detail;
            $("#idAgente").val(agente.id);
            $("#Descricao").val(agente.nome).attr('disabled', 'true');
            $("#divDinamicaAgentes").dialog('destroy').remove();
            $('html').css('overflow', 'auto');
        });
    });

    function executarPagina(info){
        var pagina = requisicaoAjaxObj();
        pagina.executar({
            pagina: info.pagina,
            parametros:info.parametros
        });
    }
</script>
