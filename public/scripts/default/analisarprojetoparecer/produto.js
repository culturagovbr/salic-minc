somenteLeitura = parseInt(jQuery('#somenteLeitura').val());
stPrincipal = parseInt(jQuery('#stPrincipal').val());
IN2017 = parseInt(jQuery('#IN2017').val());
produtosSecundariosEmAnalise = parseInt(jQuery('#produtosSecundariosEmAnalise').val());
valorpossivelApresentacao = jQuery('#valorpossivelApresentacao').val();
IdPRONAC = jQuery('#IdPRONAC').val();
baseUrl = jQuery('#baseUrl').val();
vlSolicitado = jQuery('#vlSolicitado').val();
idProduto = jQuery('#idProduto').val();

function carregarSegmento() {
    $('#segmentoCultural').html('<option value=""> - Carregando - </option>');
    $.ajax({
        type: 'POST',
        url: '/segmento/combo',
        data: {
            id: $('#areaCultural').val()
        },
        success: function(dados) {
            $('#segmentoCultural').find('option').remove();
            $('#segmentoCultural').append(dados);
        }
    });
}


function carregarTextAreaCKEditor() {
    if (jQuery('#somenteLeitura').val() == 0) {
        // CKEDITOR.replace( 'ParecerDeConteudo', { toolbar: [] } );
        console.log('teste1', jQuery('#ParecerDeConteudo').val());
    } else {
        var parecer = $('#ParecerDeConteudo').val();
        $('#ParecerDeConteudo').before(parecer).remove();
        $('#ParecerFavoravel').attr('disabled', 1);
        $('#ParecerDesfavoravel').attr('disabled', 1);
        $('#btSalvarAnaliseDeConteudo').remove();
        if (jQuery('#IN2017').val() && jQuery('#stPrincipal').val() && jQuery('#produtosSecundariosEmAnalise').val() == 0) {
            var dsAcaoAlcanceProduto = $('#dsAcaoAlcanceProduto').val();
            $('#dsAcaoAlcanceProduto').before(dsAcaoAlcanceProduto).remove();
        }
    }

    if (jQuery('#stPrincipal').val() == 1 && jQuery('#produtosSecundariosEmAnalise').val() == 0) {
        if (jQuery('#IN2017').val() == 1 && jQuery('#stPrincipal').val() == 1) {
            CKEDITOR.replace( 'dsAcaoAlcanceProduto', { toolbar: [] } );
        }
        CKEDITOR.replace( 'dsConsolidacao', { toolbar: [] } );
    }
}

    $(document).ready(function(){
        // var parecer = null;
        carregarAnaliseDeConteudo();

        // var editorRico = $("#ParecerDeConteudo").editorRico({
        //     altura: 200,
        //     // isLimitarCarateres: true,
        //     // maxchar: limiteMaximo
        // });

        // salvar analise de conteudo
        $('#btSalvarAnaliseDeConteudo').click(function()
        {
            if (!somenteLeitura) {
               parecer = CKEDITOR.instances['ParecerDeConteudo'].getData().toString().replace(/(<.*?>)|(&nbsp;)|(\s+)/g,'');
                console.log(parecer);
            }

            erros = 0;

            if (IN2017 && stPrincipal && (produtosSecundariosEmAnalise == 0)){
                var acoesRelevantes = CKEDITOR.instances['dsAcaoAlcanceProduto'].getData().toString().replace(/(<.*?>)|(&nbsp;)|(\s+)/g,'');
                if( acoesRelevantes.length  < 11){
                    alertar('As a\xE7\xF5es relevantes devem ter no m\xEDnimo 11 caracteres!');
                    erros ++;
                }
            }

            if( parecer.length  < 11){
                alertar('O parecer t&eacute;cnico deve ter no m\xEDnimo 11 caracteres!');
                erros ++;
            }

            if (!erros) {
                $('#formAnaliseConteudo').submit();
            }
            return false;
        });

        window.setTimeout(carregarTextAreaCKEditor(), 1000);

        $('#areaCultural').change(function() {
            carregarSegmento();
        });

        $('#segmentoCultural').change(function() {
            carregarEnquadramento();
        });

        $('#btSalvarConsolidacao').click(function() {

            if (IN2017 && stPrincipal && (produtosSecundariosEmAnalise == 0)){
                $('#consolidacaoDsAcaoAlcanceProduto').val($('#dsAcaoAlcanceProduto').val());
            }

            var i = 0;
            $('.obrigatorio').each(function(){
                if($.trim($(this).val()) == ''){
                    i++;
                }
            });

            var boolValid = false;
            $('.obrigatorioRadio').each(function() {
                if ($(this).is(':checked') ) {
                    boolValid = true;
                }
            });

            var texto = CKEDITOR.instances['dsConsolidacao'].getData().toString().replace(/(<.*?>)|(&nbsp;)|(\s+)/g,'');
            if (texto == '' || texto == 'Digiteseutextoaqui...'){
                i++;
            }

            if(i > 0 || !boolValid) {
                $("#camposObrigatorios").dialog("destroy");
                $("#camposObrigatorios").html("Favor preencher os dados obrig&aacute;torios!");
                $("#camposObrigatorios").dialog({
                    resizable: false,
                    title: 'Alerta!',
                    width:320,
                    height:160,
                    modal: true,
                    buttons : {
                        'OK' : function(){
                            $(this).dialog('close');
                        }
                    }
                });
                $('.ui-dialog-titlebar-close').remove();

            } else {
                $('#formConsolidacao').submit();
            }
        });

        $("#valorpossivelApresentacao").html(valorpossivelApresentacao);
        var grupoAtivo = $("#grupoAtivo").val();
        if(grupoAtivo == "93"){
            desabilitaFormConteudo(true);
            $("#divAnaliseCusto a").click(function() { return false; });
        }

        var cont = 0;
        $('.class_relatorio').each(function(){
            var contAux = cont;
            $('#abrir_fechar'+contAux).click(function(){
                $('#div_relatorio'+contAux).toggle('slow');
            });
            cont++;
        });

        $('.linkrelatorio').click(function(){
            var este = this;
            var cont = $(este).attr('cont');
            $('#div_relatorio'+cont).html('<table class="tabelaRelatorio" cellspacing="1" style="margin: 0;margin-left: 52px; width: 90%;  padding: 0px;"><tr><td align="center"><img src="../public/img/ajax.gif"/>&nbsp;Carregando...</td><tr></table>');
            $('#div_relatorio'+cont).css('display','');
            $.post($(este).attr('link'),{nrRelatorio: cont, idPRONAC:IdPRONAC},function(data){
                $('#div_relatorio'+$(este).attr('cont')).html(data);
            });
        });
        $(".icn_menos").click(function(){
            var tipo = $(this).attr('tipo');
            var aberto = $(this).attr('aberto');
            if(aberto == 'true'){
                adisplay = 'none';
                $(this).attr('aberto','false');
                $(this).removeClass('icn_menos').addClass('icn_mais');
            }
            else{
                adisplay = '';
                $(this).attr('aberto','true');
                $(this).removeClass('icn_mais').addClass('icn_menos');
            }
            if(tipo == 'fonte'){
                fonte = $(this).attr('fonte');
                $(".master[fonte='"+fonte+"']").css('display', adisplay);
                $(".clickproduto").removeClass('icn_mais').addClass('icn_menos');
            }
            if(tipo == 'produto'){
                fonte = $(this).attr('fonte');
                produto = $(this).attr('produto');
                $(".produto[produto='"+produto+"'][fonte='"+fonte+"']").css('display', adisplay);
            }
            if(tipo == 'etapa'){
                fonte = $(this).attr('fonte');
                produto = $(this).attr('produto');
                etapa = $(this).attr('etapa');
                $(".etapa[produto='"+produto+"'][etapa='"+etapa+"'][fonte='"+fonte+"']").css('display', adisplay);
            }
            if(tipo == 'cidade'){
                fonte = $(this).attr('fonte');
                produto = $(this).attr('produto');
                etapa = $(this).attr('etapa');
                cidade = $(this).attr('cidade');
                $(".cidade[produto='"+produto+"'][etapa='"+etapa+"'][cidade='"+cidade+"'][fonte='"+fonte+"']").css('display', adisplay);
            }
        });
        $(".item").mouseover(function(){
            $(this).addClass('fundo_linha3');
        });
        $(".item").mouseout(function(){
            $(this).removeClass('fundo_linha3');
        });
        $(".item").click(function(){
            if($(this).hasClass('fundo_linha4')){
                $(this).removeClass('fundo_linha4');
            }
            else{
                $(this).addClass('fundo_linha4');
            }
        });

        APPescolherLei_8313();

        carregarEnquadramento();
    }); //fecha document.ready

    function AlterarItem(idPlanilhaProjeto,idPronac,idProduto,stPrincipal)
    {
        var este = $('#alteraritemsolicitado');
        este.html('<br><br><br><br><br><br><br><center>Aguarde, carregando dados...<br><img src="/public/img/ajax.gif" /></center><br>');

        este.dialog(
            {
                title:"Alterar Item",
                resizable: false,
                width:800,
                height:600,
                modal: false,
                autoOpen:true,
                closeOnEscape:true
            }
        );

        $.post(
            este.attr('link'),
            {idPlanilhaProjeto:idPlanilhaProjeto,idPronac:idPronac,idProduto:idProduto,stPrincipal:stPrincipal},
            function(data)
                {
                    este.html(data);
                    este.dialog(
                    {
                        title:"Alterar Item",
                        resizable: false,
                        width:800,
                        height:600,
                        modal: false,
                        autoOpen:true,
                        closeOnEscape:true,
                        buttons:
                            {
                            'Cancelar': function() {
                                    $(this).dialog('close');
                            },
                            'Salvar': function() {
                                var texto = $('#justificativa'+$('#idPlanilhaProjeto').val()).val();
                                texto = texto.replace(/^\s+|\s+$/g,""); //retirando espa¿os
                                if (texto.length <= 0 || texto=="")
                                {
                                    alertar('Dados obrigat\xF3rios n\xE3o informados.');
                                    return false;
                                }
                                else
                                {
                                    var vlSugerido = $('#calcular_total'+$('#idPlanilhaProjeto').val()).html().replace(/\R\$\s+/g, '');
                                    $('#totalcalculos'+$('#idPlanilhaProjeto').val()).val(vlSugerido);

                                    var dados = $('#formSalvarItem').serialize();
                                    $(this).append('<br><center>Aguarde, salvando informa\xE7\xF5es...<br><img src="'+baseUrl+'"/public/img/ajax.gif" /></center><br>');
                                    $.post($('#formSalvarItem').attr('action'),dados
                                    ,function(data)
                                    {
                                        var resposta = data;
                                        $('#item'+$('#idPlanilhaProjeto').val()+' .JustParecerista').html($("#justificativa"+$('#idPlanilhaProjeto').val()).val());
                                        $('#item'+$('#idPlanilhaProjeto').val()+' .ValorSugerido').html($("#calcular_total"+$('#idPlanilhaProjeto').val()).html());
                                        somarValores();

                                        //atualiza novo valor possivel de remanejamento
                                        /* var vlSolicitado = vlSolicitado; */
                                        var vlTotalSugerido = recuperaTotalSugerido();
                                        var valorpossivel = vlSolicitado - vlTotalSugerido;

                                        $("#valorpossivel").val(valorpossivel);

                                        //atualiza campo de apresentacao do novo valor possivel de remanejamento
                                        var valorpossivelApresentacao = formatarParaReal(valorpossivel);
                                        $("#valorpossivelApresentacao").html(valorpossivelApresentacao);

                                        este.dialog('close');
                                        janelaAlerta('Informa&ccedil;&otilde;es salvas com sucesso');
                                    });
                                }
                            }
                        }
                    });
                });
    }

function carregarAnaliseDeConteudo()
{
    obj = {
            stAcao: 1,
            idPRONAC: IdPRONAC,
            idProduto: idProduto,
            stPrincipal: stPrincipal
    };

    recuperaFormulario($('#formAnaliseConteudo').prop('action'), obj);
    return true;
}

function carregarEnquadramento(){
    var listaCodSegmento = [ '11','12','13','14','15','17','23','26', '28', '2A','2B','2E','2F','2G','2H','2I','2J','2K','2L','32','33','36','4B','4D','5A','5D','5E','5F','5G','5H','5I','5J','5K','5L','5M','5N','5O','5P','62','65','68','6C','6D','6E','6G','6H' ];
    if(in_array($('#segmentoCultural').val(), listaCodSegmento)){
        $('#enquadramentoProjeto').val('2');
        $('#enquadramentoText').html('Artigo 18');
    } else {
        $('#enquadramentoProjeto').val('1');
        $('#enquadramentoText').html('Artigo 26');
    }
}

function abrirfechar(elemento){
    $('#'+elemento).toggle();
}

function desabilitaFormConteudo(habilitar){
    $('#formAnaliseConteudo').find('input, select, textarea').each(function(key, object){
        if(habilitar){
            $(object).attr('disabled','true');
        }else{
            $(object).removeAttr('disabled');
        }
    });
}

function janelaAnaliseConteudoConfirm(stPrincipal,mensagem1,mensagem2){
    var nomeJanelaAlerta =   janelaObj({
        parametros : {
            width:      400,
            autoOpen:   false,
            resizable:  false,
            modal:      true,
            buttons: {
                'N\u00e3o': function() {
                    $(this).dialog('close');
                },
                Sim: function() {
                    $('#formAnaliseConteudo').submit();
                    $(this).dialog('close');
                }
            }
        },
        removerBtFechar:true,
        title : 'Alerta'
    });
    if(stPrincipal == 1){
        nomeJanelaAlerta.divConteudo.html(mensagem1);
    }else{
        nomeJanelaAlerta.divConteudo.html(mensagem2);
    }
    nomeJanelaAlerta.abrirJanela();

    return nomeJanelaAlerta;
}

function recuperaTotalSugerido()
{
    // pega o valor total do relator
    var valor = $('.valorTotalSugerido').html();
    valor = valor.replace('R$ ', '');

    // retira os pontos e as virgulas, deixando somente numeros
    valor = valor.replace(/\D/g, "");
    valor = valor.replace(/(\d{0})(\d)/, "$1$2");

    // adiciona o ponto na casa decimal
    valor = valor.replace(/(\d)(\d{2})$/, "$1.$2");

    // converte para float
    valor = parseFloat(valor);

    return valor;
}

// formata para real
function formatarParaReal(valor)
{
    valor = (parseFloat(valor)).toFixed(2);
    valor = valor.replace(/\D/g, "");
    valor = valor.replace(/(\d)(\d{2})$/, "$1,$2");
    valor = valor.replace(/(\d+)(\d{3},\d{2})$/g, "$1.$2");

    var q = (valor.length - 3) / 3; // quantidade caracteres
    var c = 0; // contador
    while (q > c)
    {
        c++;
        valor = valor.replace(/(\d+)(\d{3}.*)/, "$1.$2");
    }
    valor = valor.replace(/^(0+)(\d)/g, "$2");
    valor = 'R$ ' + valor;

    return valor;
}

function areadetrabalho()
{
    $('#abrir_fechar4').click(function()
    {
        $('#enquadramento').toggle('slow');
        $('#parecer').toggle('slow');
        $('#divAnaliseConteudo').hide('slow');
        $('#div_teste2').hide('slow');
    });

    $('#abaAnaliseConteudo').click(function()
    {
        $('#divAnaliseConteudo').toggle('slow');
    });

    $('#abaAnaliseCusto').click(function()
    {
        $('#divAnaliseCusto').toggle('slow');
    });
}

window.onload = function()
{
    areadetrabalho();
};

function AbrirFecharPlanilha(elemento)
{
    $('.' + elemento).toggle();
    if ($('#' + elemento).hasClass('icn_mais'))
    {
        $('#' + elemento).addClass('icn_menos');
        $('#' + elemento).removeClass('icn_mais');
    }
    else
    {
        $('#' + elemento).addClass('icn_mais');
        $('#' + elemento).removeClass('icn_menos');
    }
}


function recuperaFormulario(url,params)
{
    if(params.length < 1) {
        params = {};
    }
    var btSalvar = false;
    $.post(url,params,function(data) {
        // console.log(data);
        if(data){
            $('#formAnaliseConteudo').find('input, select, textarea').not('input[type="hidden"]').each(function(key, object){
                // console.log(key, object);
                if($(object).attr('type') == 'radio'){
                    if($(object).val() == data[$(object).prop('name')]){
                        btSalvar = true;
                        $(object).prop('checked','true');
                    }
                }else{
                    if($(object).attr('type') == 'checkbox'){
                        if(data[$(object).attr('name')] == 1){
                            btSalvar = true;
                            $(object).prop('checked','true');
                        }
                    }else{
                        btSalvar = true;
                        $(object).val(data[$(object).prop('name')]);
                    }
                }
            });
            $('#stAcao').val(2);

            var editorRico = $("#ParecerDeConteudo").editorRico({
                altura: 200,
                // isLimitarCarateres: true,
                // maxchar: limiteMaximo
            });
        }else{
            $('#stAcao').val(3);
        }
    },'json');
}

function AbrirFecharPlanilha(elemento){
    $('.' + elemento).toggle();

    if ($('#' + elemento).hasClass('icn_mais'))
    {
        $('#' + elemento).addClass('icn_menos');
        $('#' + elemento).removeClass('icn_mais');
    }
    else
    {
        $('#' + elemento).addClass('icn_mais');
        $('#' + elemento).removeClass('icn_menos');
    }
    return false;
}

function janelaAlerta(mensagem,funcaoAdcional)
{
    if(funcaoAdcional==undefined)
    {
        funcaoAdcional = function(){}
    }
    var nomeJanelaAlerta =   janelaObj({
        parametros : {
            width:      400,
            autoOpen:   false,
            resizable:  false,
            modal:      true,
            buttons: {
                OK: function() {
                    funcaoAdcional();
                    $(this).dialog('close');
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

function janelaObj(dados){
    var divConteudo = $('<div></div>')
    .attr('title',dados.title)
    .appendTo('body');
    var novaJanela =
        {
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
