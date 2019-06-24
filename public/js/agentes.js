function remarcarferias(id, diasmarcados)
{
    $("#dtinicioalteracao").val('');
    $("#dtfimalteracao").val('');

    $("#modal").dialog({
        title : 'Altera&ccedil;&atilde;o / Cancelamento de f&eacute;rias',
        resizable: false,
        width:930,
        draggable:false,
        position: [420,65],
        modal: true,
        autoOpen:false,
        buttons : {
            'Cancelar' : function()
            {
                $(this).dialog('close');
            },
            'Confirmar' : function()
            {
                validarFormR();
            }
        }
    });

    $("#modal").dialog('open');

    CKEDITOR.replace('justificativa');







    $("#idferias").val(id);
    $("#diasmarcados").val(diasmarcados);

    var dias 			= $("#dias").val();
    var diminuidias 	= ((parseInt(dias)) - (parseInt(diasmarcados)));
    var disponiveis 	= (60) - (parseInt(diminuidias));
    $("#diassubtraidos").html('Total de dias dispon&iacute;veis: ' + disponiveis);



}

function novo()
{
    $("#formNovo").show();
    $("#salvos").hide();

}

function salvo()
{
    $("#formNovo").hide();
    $("#salvos").show();

}


function confirmaExclusao(msg, dados)
{
    if(msg == '')
    {
        $("#confirma").html("Confirmar a exclus&atilde;o?");
    }
    else
    {
        $("#confirma").html(msg);
    }

    $("#confirma").dialog({
        title : 'Alerta!',
        resizable: false,
        width:350,
        height:180,
        modal: true,
        autoOpen:false,
        buttons : {
            'N\u00e3o' : function(){
                $(this).dialog('close');
            },
            'Sim' : function(){
                window.location = dados;
            }
        }
    });
    $("#confirma").dialog('open');

}

/**
 * Efetua a valida&ccedil;&atilde;o do formul&aacute;rio de agentes
 */
function validaAgenteNovo()
{
    cpf              = $3('#cpf').val();
    nome             = $3('#nome').val();
    visao            = document.getElementById('visao').options[document.getElementById('visao').selectedIndex].value;
    areaCultural     = document.getElementById('areaCultural').options[document.getElementById('areaCultural').selectedIndex].value;
//    segmentoCultural = document.getElementById('segmentoCultural').options[document.getElementById('segmentoCultural').selectedIndex].value;
    grupologado      = $3("#grupologado").val();

    cep             = $3('#cep').val();
    uf              = $3('#uf').find(':selected').val();
    cidade          = $3('#cidade').find(':selected').val();
    tipoEndereco    = $3('#tipoEndereco').find(':selected').val();
    tipoLogradouro  = $3('#tipoLogradouro').find(':selected').val();
    logradouro      = $3('#logradouro').val();
    numero          = $3('#numero').val();
    complemento     = $3('#complemento').val();
    bairro          = $3('#bairro').val();
    tipoFone = null;

    if ( $3('exibirTelefone').val() ==  's' ) {
        tipoFone 		= $3('#tipoFone').val();
        ufFone   		= $3('#ufFone').val();
        dddFone  		= $3('#dddFone').val();
        fone     		= $3('#fone').val();

    } else {
        tipoFone 	= "";
        ufFone   	= "";
        dddFone  	= "";
        fone     	= "";
    }

    if ($3('exibirEmail').val() == 's' ) {
        tipoEmail     	= $3('#tipoEmail').val();
        email         	= $3('#email').val();
    }else {
        tipoEmail     	= "";
        email         	= "";
    }


    var verifica = false;
    $('[name=titular]').each(function(){
        if(!$(this).attr('disabled'))
            verifica = true;
    });

    if (cpf == '')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o CNPJ/CPF!", "cpf");
        exibirMsgErro('cpf','erroCpf');
    }
    else if (nome == '')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o nome!", "nome");
        exibirMsgErro('nome','erroNome');
    } // validacao da visao
    else if ((visao == '0') && (grupologado != '118'))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe a vis&atilde;o!", "visao");
        exibirMsgErro('visao','erroVisao');
    }
    else if ((visao == '210') && (grupologado != '118') && (verifica == false)) // valida&ccedil;&atilde;o de vis&atilde;o para o Componente da Comiss&atilde;o
    {
        alertar("Selecione outra &aacute;rea Cultural!", "area");
        exibirMsgErro('area','erroTitular');
    }
    else if ((visao == '210') && (grupologado != '118') && (areaCultural == '0')) // valida&ccedil;&atilde;o de vis&atilde;o para o Componente da Comiss&atilde;o
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe a &aacute;rea Cultural!", "area");
        exibirMsgErro('area','erroAreaCultural');
    }
//    else if ((visao == '210') && (grupologado != '118') && (segmentoCultural == '0')) // valida&ccedil;&atilde;o de vis&atilde;o para o Componente da Comiss&atilde;o
//    {
//        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o Segmento Cultural!", "area");
//        exibirMsgErro('area','erroSegmentoCultural');
//    }


    // valida&ccedil;&atilde;o para endere&ccedil;os
    else if ((cep == 0 || cep == null || cep == ' ' || cep == '' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe um CEP!", "cep");
        exibirMsgErro('cep','erroCep');
    }
    else if ((cidade == 0 || cidade == null || cidade == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione uma cidade!", "Cidade");
        exibirMsgErro('cidade','erroCidade');
        return;
    }
    else if ((tipoEndereco == 0 || tipoEndereco == null || tipoEndereco == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o tipo de endere&ccedil;o!", "tipoEndereco");
        exibirMsgErro('tipoEndereco','erroTipoEndereco');
        return;
    }
    else if ((tipoLogradouro == 0 || tipoLogradouro == null || tipoLogradouro == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o tipo de logradouro!", "tipoLogradouro");
        exibirMsgErro('tipoLogradouro','erroTipoLogradouro');
        return;
    }
    else if ((numero == '' || numero == null))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, preencha o campo n&uacute;mero!", "numero");
        exibirMsgErro('numero','erroNumero');
        return;
    }
    else if ((bairro == ' ' || bairro == '' || bairro == null))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, preencha o campo bairro!", "bairro");
        exibirMsgErro('bairro','erroBairro');
        return;
    }

    // valida&ccedil;&atilde;o para telefones
    if ($3('#tipoFone').val() == "" && $3('#exibirTelefone').val() == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o Tipo de Telefone!", "tipoFone");
        exibirMsgErro('tipoFone','erroTipoFone');
        return;
    }

    if ($3('#ufFone') == 0 && $3('#exibirTelefone').val() == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione a UF!", "ufFone");
        exibirMsgErro('ufFone','erroUfFone');
    }
    else if ($3('#dddFone') == "" && $3('#exibirTelefone').val() == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o DDD do telefone!", "dddFone");
        exibirMsgErro('dddFone','erroDddFone');
    }
    else if ($3('#fone') == "" && $3('#exibirTelefone').val() == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o Telefone!", "fone");
        exibirMsgErro('fone','erroFone');
    }

    if (
        $3('#exibirTelefone').val() == 's'
        && (
            $3('#fone').val().length < 9
            || !(/\d{4}\-\d{4}/.test($3('#fone').val()))
            || $3('#fone').val() == "0000-0000"
            || $3('#fone').val() == "1111-1111"
            || $3('#fone').val() == "2222-2222"
            || $3('#fone').val() == "3333-3333"
            || $3('#fone').val() == "4444-4444"
            || $3('#fone').val() == "5555-5555"
            || $3('#fone').val() == "6666-6666"
            || $3('#fone').val() == "7777-7777"
            || $3('#fone').val() == "8888-8888"
            || $3('#fone').val() == "9999-9999"
        )
    ) {
        alertar("O n&uacute;mero do Telefone &eacute; inv&aacute;lido!", "fone");
        exibirMsgErro('fone','erroFone');
        return;
    }

    // valida&ccedil;&atilde;o para emails
    if (
        $3('#tipoEmail').val() == 0
        && $3('#exibirEmail').val() == 's'
    ) {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o Tipo de E-mail!", "tipoEmail");
        exibirMsgErro('tipoEmail','erroTipoEmail');
        return;
    }
    else if ($3('#email').val() == "" && $3('#exibirEmail').val() == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o E-mail!", "email");
        exibirMsgErro('email','erroEmail');
        return;
    }
    if (
        $3('#exibirEmail').val() == 's'
        && (
            ($3('#email').val().indexOf("@") < 1)
            || ($3('#email').val().lastIndexOf(".") <= $3('#email').val().indexOf("@"))
            || ($3('#email').val().indexOf("@") == $3('#email').val().length)
            || !(/^[a-z0-9.]+@[a-z0-9]+\.[a-z]+(\.[a-z]+)?$/i.test($3('#email').val()))
        )
    ) {
        alertar("E-mail inv&aacute;lido!", "email");
        exibirMsgErro('email','erroEmail');
        return;
    }
    else
    {
        $("#nome").removeAttr("disabled");
        $("#logradouro").removeAttr("disabled");
        $("#tipoLogradouro").removeAttr("disabled");
        $("#bairro").removeAttr("disabled");
        $("#cidade").removeAttr("disabled");
        $("#uf").removeAttr("disabled");

        if( $3('#modal').val() == 's' ){
            jqAjaxForm( formCadAgentes, "divDinamicaAgentes");
        }else{
           $("#formCadAgentes").submit();
        }
    }
}

/**
 * Efetua a valida&ccedil;&atilde;o do formul&aacute;rio de agentes
 */
function validaDirigenteNovo()
{
    cpf              = document.getElementById('cpf').value;
    nome             = document.getElementById('nome').value;
    visao            = document.getElementById('visao').options[document.getElementById('visao').selectedIndex].value;
    grupologado      = document.getElementById("grupologado").value;

    cep             = document.getElementById('cep').value;
    uf              = document.getElementById('uf').value;
    cidade          = document.getElementById('cidade').value;
    tipoEndereco    = document.getElementById('tipoEndereco').value;
    tipoLogradouro  = document.getElementById("tipoLogradouro").value;
    logradouro      = document.getElementById('logradouro').value;
    numero          = document.getElementById('numero').value;
    complemento     = document.getElementById('complemento').value;
    bairro          = document.getElementById('bairro').value;

    tipoFone 	= document.getElementById('tipoFone').value;
    ufFone   	= document.getElementById('ufFone').value;
    dddFone  	= document.getElementById('dddFone').value;
    fone     	= document.getElementById('fone').value;
    tipoEmail     	= document.getElementById('tipoEmail').value;
    email         	= document.getElementById('email').value;



    if (cpf == '')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o CNPJ/CPF!", "cpf");
        exibirMsgErro('cpf','erroCpf');
    }
    else if (nome == '')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o nome!", "nome");
        exibirMsgErro('nome','erroNome');
    }

    // valida&ccedil;&atilde;o para endere&ccedil;os

    else if ((cep == 0 || cep == null || cep == ' ' || cep == '' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe um CEP!", "cep");
        exibirMsgErro('cep','erroCep');
    }
    else if ((cidade == 0 || cidade == null || cidade == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione uma cidade!", "Cidade");
        exibirMsgErro('cidade','erroCidade');
    }
    else if ((tipoEndereco == 0 || tipoEndereco == null || tipoEndereco == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o tipo de endere&ccedil;o!", "tipoEndereco");
        exibirMsgErro('tipoEndereco','erroTipoEndereco');
    }
    else if ((tipoLogradouro == 0 || tipoLogradouro == null || tipoLogradouro == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o tipo de logradouro!", "tipoLogradouro");
        exibirMsgErro('tipoLogradouro','erroTipoLogradouro');
    }
    else if ((numero == '' || numero == null))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, preencha o campo n&uacute;mero!", "numero");
        exibirMsgErro('numero','erroNumero');
    }
    else if ((bairro == ' ' || bairro == '' || bairro == null))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, preencha o campo bairro!", "bairro");
        exibirMsgErro('bairro','erroBairro');
    }

    // valida&ccedil;&atilde;o para telefones
    else if (tipoFone == "" )
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o Tipo de Telefone!", "tipoFone");
        exibirMsgErro('tipoFone','erroTipoFone');
    }
    else if (ufFone == 0 && document.getElementById('exibirTelefone').value == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione a UF!", "ufFone");
        exibirMsgErro('ufFone','erroUfFone');
    }
    else if (dddFone == "" && document.getElementById('exibirTelefone').value == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o DDD do telefone!", "dddFone");
        exibirMsgErro('dddFone','erroDddFone');
    }
    else if (fone == "" && document.getElementById('exibirTelefone').value == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o Telefone!", "fone");
        exibirMsgErro('fone','erroFone');
    }
    else if ((fone.length < 9 || !(/\d{4}\-\d{4}/.test(fone)) || fone == "0000-0000" ||
        fone == "1111-1111" || fone == "2222-2222" || fone == "3333-3333" ||
        fone == "4444-4444" || fone == "5555-5555" || fone == "6666-6666" ||
        fone == "7777-7777" || fone == "8888-8888" || fone == "9999-9999") && document.getElementById('exibirTelefone').value == 's')
        {
        alertar("O n&uacute;mero do Telefone &eacute; inv&aacute;lido!", "fone");
        exibirMsgErro('fone','erroFone');
    }

    // valida&ccedil;&atilde;o para emails
    else if (tipoEmail == 0 )
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o Tipo de E-mail!", "tipoEmail");
        exibirMsgErro('tipoEmail','erroTipoEmail');
    }
    else if (email == "" && document.getElementById('exibirEmail').value == 's')
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o E-mail!", "email");
        exibirMsgErro('email','erroEmail');
    }
    else if (((email.indexOf("@") < 1) || (email.lastIndexOf(".") <= email.indexOf("@")) || (email.indexOf("@") == email.length) || !(/^[a-z0-9.]+@[a-z0-9]+\.[a-z]+(\.[a-z]+)?$/i.test(email))) && document.getElementById('exibirTelefone').value == 's')
    {
        alertar("E-mail inv&aacute;lido!", "email");
        exibirMsgErro('email','erroEmail');
    }
    else
    {
        $("#logradouro").removeAttr("disabled");
        $("#tipoLogradouro").removeAttr("disabled");
        $("#bairro").removeAttr("disabled");
        $("#cidade").removeAttr("disabled");
        $("#uf").removeAttr("disabled");

        $("#formCadAgentes").submit();

    }

} // fecha fun&ccedil;&atilde;o validaAgente()

function validaTelefone()
{

    tipoFone = $3("#tipoFone").val();
    ufFone   = $3("#ufFone").val();
    dddFone  = $3("#dddFone").val();
    fone     = $3("#fone").val();

    if (tipoFone == "")
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o Tipo de Telefone!", "tipoFone");
    }
    else if (ufFone == 0)
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione a UF!", "ufFone");
    }
    else if (dddFone == "")
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o DDD do telefone!", "dddFone");
    }
    else if (fone == "")
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o Telefone!", "fone");
    }
    else if ((fone.length < 9 || !(/\d{4}\-\d{4}/.test(fone)) || fone == "0000-0000" ||
        fone == "1111-1111" || fone == "2222-2222" || fone == "3333-3333" ||
        fone == "4444-4444" || fone == "5555-5555" || fone == "6666-6666" ||
        fone == "7777-7777" || fone == "8888-8888" || fone == "9999-9999"))
        {
        alertar("O n&uacute;mero do Telefone &eacute; inv&aacute;lido!", "fone");
    }
    else
    {
        return true;
    }
}


function validaEmail()
{
    tipoEmail     = document.getElementById("tipoEmail").value;
    email         = document.getElementById("email").value;

    if (tipoEmail == 0)
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o Tipo de E-mail!", "tipoEmail");
    }
    else if (email == "")
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe o E-mail!", "email");
    }
    else if (((email.indexOf("@") < 1) || (email.lastIndexOf(".") <= email.indexOf("@")) || (email.indexOf("@") == email.length) || !(/^[a-z0-9.]+@[a-z0-9]+\.[a-z]+(\.[a-z]+)?$/i.test(email))))
    {
        alertar("E-mail inv&aacute;lido!", "email");
    }
    else
    {
        return true;
    }
}

function validaEndereco()
{

    cep             = document.getElementById('cep').value;//
    uf              = document.getElementById('uf').value; //
    cidade          = document.getElementById('cidade').value; //
    tipoEndereco    = document.getElementById('tipoEndereco').value;//
    tipoLogradouro  = document.getElementById("tipoLogradouro").value;
    logradouro      = document.getElementById('logradouro').value;//
    numero          = document.getElementById('numero').value;
    complemento     = document.getElementById('complemento').value;
    bairro          = document.getElementById('bairro').value;


    if ((cep == 0 || cep == null || cep == ' ' || cep == '' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, informe um CEP!", "cep");
    }
    else if ((cidade == 0 || cidade == null || cidade == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione uma cidade!", "Cidade");
    }
    else if ((tipoEndereco == 0 || tipoEndereco == null || tipoEndereco == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o tipo de endere&ccedil;o!", "tipoEndereco");
    }
    else if ((tipoLogradouro == 0 || tipoLogradouro == null || tipoLogradouro == ' ' ))
    {
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, selecione o tipo de logradouro!", "tipoLogradouro");
    }
    else if ((numero == '' || numero == null)){
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, preencha o campo n&uacute;mero!", "numero");
    }
    else if ((bairro == ' ' || bairro == '' || bairro == null)){
        alertar("Dados obrigat&oacute;rios n&atilde;o informados:\nPor favor, preencha o campo bairro!", "bairro");
    }
    else
    {
        return true;
    }
}





