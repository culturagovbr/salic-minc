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
