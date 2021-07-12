<script>
//***************** Mascaras ************************
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

function mascaras(form){
    $('[type=text]').addClass('input_simples');
    $('textarea').addClass('textarea_simples');
    $('select[id!=perfilXgrupo]').addClass('select_simples');
    $(form).find('[data=true]').each(function(){
        var este = this;
        $(este).attr('maxlength',10);
        $(este).keyup(function(){
            mascara(this,format_data);
            if($(este).val().length == 10){
                validaDataCorreta(este,$(este).val().substr(6,4),$(este).val().substr(3,2),$(este).val().substr(0,2));
            }
        });
        $(este).click(function(){
            limpar_campo(this, '00/00/0000');
        });
        $(este).blur(function(){
            restaurar_campo(this, '00/00/0000');
            if($(este).val().length == 10){
                validaDataCorreta(este,$(este).val().substr(6,4),$(este).val().substr(3,2),$(este).val().substr(0,2));
            }
        });
        $(este).datepicker($.datepicker.regional['pt-BR']);
    });
    $(form).find('[sonumero=true]').keyup(function(){
        mascara(this,format_num);
    });
    $(form).find('[dinheiro=true]').keyup(function(){
        mascara(this,format_moeda);
    });
    $(form).find('[cpf=true]').each(function(){
        var este = this;
        $(este).attr('maxlength',14);
        $(este).keyup(function(){
            mascaraCNPJCPF(este,'cpf');
        });
    });
    $(form).find('[cnpj=true]').each(function(){
        var este = this;
        $(este).attr('maxlength',18);
        $(este).keyup(function(){
            mascaraCNPJCPF(este,'cnpj');
        });
    });
    $(form).find('[cnpjcpf=true]').each(function(){
        var este = this;
        $(este).attr('maxlength',18);
        $(este).keyup(function(){
            mascaraCNPJCPF(este,'cnpjcpf');
        });
    });
}
function validaDataCorreta(id,ano,mes,dia){
    if(dia <= 31){
        if(mes==4 || mes==6 || mes==9 || mes==11 && dia > 30){
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
function mascaraCNPJCPF(este,tipo){
    switch(tipo){
        case 'cpf':
            mascara(este,format_cpf);
            break;
        case 'cnpj':
            mascara(este,format_cnpj);
            break;
        case 'cnpjcpf':
            if($(este).length>=11 && $(este).length<14){
                mascara(este,format_cpf);
            }
            if($(este).length>14){
                mascara(este,format_cnpj);
            }
            break;
    }
}
//***************** Mascaras ************************
//***************** validacao **********************
function validarFormulario(form,mensagem){
    var validar = false;
    if(mensagem== undefined){
        mensagem = '';
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
        if($(this).val() != undefined && $(this).val() != '' && $(this).val() != '00/00/0000'){
            var estadata =   $(this).val().slice(6,10)+''+$(this).val().slice(3,5)+''+$(this).val().slice(0,2);
            if($(this).attr('menorque')!=undefined){
                var menorid     =   '#'+$(this).attr('menorque');
                if($(menorid).val() != '' && $(menorid).val() != '00/00/0000'){
                    var menordata   =   $(menorid).val().slice(6,10)+''+$(menorid).val().slice(3,5)+''+$(menorid).val().slice(0,2);
                    if(estadata > menordata){
                        validar     =   true;
                        mensagem    +=   '<br />A "'+$(this).attr('title')+'" deve ser menor que a "'+$(menorid).attr('title')+'"';
                    }
                }
                else{
                    if(!$(menorid).hasClass('sumir')){
                        validar     =   true;
                        mensagem    +=   '<br />Preencher o campo "'+$(menorid).attr('title')+'"';
                    }
                }
            }
            if($(this).attr('maiorque')!=undefined){
                var maiorid     =   '#'+$(this).attr('maiorque');
                if($(maiorid).val() != '' && $(maiorid).val() != '00/00/0000'){
                    var maiordata   =   $(maiorid).val().slice(6,10)+''+$(maiorid).val().slice(3,5)+''+$(maiorid).val().slice(0,2);
                    if(estadata < maiordata){
                        validar = true;
                        mensagem    +=   '<br />A "'+$(this).attr('title')+'" deve ser maior que a "'+$(maiorid).attr('title')+'"';
                    }
                }
                else{
                    if(!$(maiorid).hasClass('sumir')){
                        validar     =   true;
                        mensagem    +=   '<br />Preencher o campo "'+$(maiorid).attr('title')+'"';
                    }
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
    });

    if(validar){
        janelaAlerta(mensagem);
        return false;
    }
    else
        return true;
}
//***************** validacao **********************

//***************** dialog/janelas ********************
function janelaAlerta(mensagem,funcaoAdcional){
    if(funcaoAdcional==undefined){
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
//***************** dialog/janelas ********************

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

    if(textoIni == undefined)
        textoIni = 'Todos';

    var selectVazio = '<option value="">Não encontrado!</option>';


    var select      = '<option value="">'+textoIni+'</option>';
    var resposta    = buscarJson(pagina,dados);
	var cont = 0;
    for(var j in resposta)
    {
        select += '<option value="'+resposta[j].id+'">'+resposta[j].nome+'</option>';
        cont++;
    }

    if(cont == 1)
    {
    	$(idSelect).html(selectVazio);
    }
    else
    {
    	$(idSelect).html(select);
    }




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
</script>
