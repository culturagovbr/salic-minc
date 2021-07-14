<!--
/**
 * Vari�veis que diz se pelo menos um item foi cadastrado
 */
var fones      = false;
var emails     = false;
var enderecos  = false;
var dirigentes = false;



/**
 * Fun��o para marcar os campos obrigat�rios
 */
function exibirMsgErro(campo, msg)
{
    campo = document.getElementById(campo);

    if (campo.value == '' || campo.value == ' ')
    {
        //alert(campo+", "+msg);
        document.getElementById(msg).innerHTML = '*';
        campo.style.borderColor = '#e42424';
        return true;
    }
    else
    {
        document.getElementById(msg).innerHTML = '';
        campo.style.borderColor = '';
        return false;
    }
} // fecha fun��o exibirMsgErro()



/**
 * Efetua a valida��o do formul�rio de agentes
 */
function validaAgente()
{
    cpf              = document.getElementById('cpf').value;
    uf               = document.getElementById("uf")              .options[document.getElementById("uf").selectedIndex].value;
    cidade           = document.getElementById("cidade")          .options[document.getElementById("cidade").selectedIndex].value;
    tipoEndereco     = document.getElementById("tipoEndereco")    .options[document.getElementById("tipoEndereco").selectedIndex].value;
    tipoLogradouro   = document.getElementById("tipoLogradouro")  .options[document.getElementById("tipoLogradouro").selectedIndex].value;
    visao            = document.getElementById("visao")           .options[document.getElementById("visao").selectedIndex].value;
    areaCultural     = document.getElementById("areaCultural")    .options[document.getElementById("areaCultural").selectedIndex].value;

    grupologado     = document.getElementById("grupologado").value;



    flag = false;

    if (exibirMsgErro('cpf',         'erroCpf'))         {
        flag = true;
        mostrar_menu_dados_agente();
    }
    if (exibirMsgErro('nome',        'erroNome'))        {
        flag = true;
        mostrar_menu_dados_agente();
    }
    //  if (exibirMsgErro('cep',         'erroCep'))         { flag = true; mostrar_menu_dados_agente(); }
    //	if (exibirMsgErro('logradouro',  'erroLogradouro'))  { flag = true; mostrar_menu_dados_agente(); }
    //	if (exibirMsgErro('numero',      'erroNumero'))      { flag = true; mostrar_menu_dados_agente(); }
    //	if (exibirMsgErro('complemento', 'erroComplemento')) { flag = true; mostrar_menu_dados_agente(); }
    //	if (exibirMsgErro('bairro',      'erroBairro'))      { flag = true; mostrar_menu_dados_agente(); }

    /*if (dirigentes == false && cpf.length > 14)
	{
		flag = true;
		mostrar_menu_dirigente_agente();
	}*/

    // valida��o para emails
    if (emails == false)
    {
        flag = true;
        mostrar_menu_email_agente();
    }

    // valida��o para telefones
    if (fones == false)
    {
        flag = true;
        mostrar_menu_fone_agente();
    }

    // valida��o para enderecos
    if (enderecos == false)
    {
        flag = true;
        mostrar_menu_endereco_agente();
    }

    // valida��o do estado
    if (uf == '0')
    {
    	flag = true;
          document.getElementById('erroUf').innerHTML = '*';
    	mostrar_menu_dados_agente();
    }
    else
    {
    	document.getElementById('erroUf').innerHTML = '';
    }

    // valida��o para cidade
    if (cidade == 0)
    {
    	flag = true;
    	document.getElementById('erroCidade').innerHTML = '*';
    	mostrar_menu_endereco_agente();
    }
    else
    {
    	document.getElementById('erroCidade').innerHTML = '';
    }

    // valida��o para tipo de endere�o
    if (tipoEndereco == 0)
    {
    	flag = true;
    	document.getElementById('erroTipoEndereco').innerHTML = '*';
    	mostrar_menu_endereco_agente();
    }
    else
    {
    	document.getElementById('erroTipoEndereco').innerHTML = '';
    }

    // valida��o para tipo de logradouro
    if (tipoLogradouro == 0)
    {
    	flag = true;
    	document.getElementById('erroTipoLogradouro').innerHTML = '*';
    	mostrar_menu_endereco_agente();
    }
    else
    {
    	document.getElementById('erroTipoLogradouro').innerHTML = '';
    }

    // valida��o da vis�o
    if ((visao == '0') && (grupologado != '118'))
    {
        flag = true;
        document.getElementById('erroVisao').innerHTML = '*';
        mostrar_menu_dados_agente();
    }
    else if ((visao == '210') && (grupologado != '118')) // valida��o de vis�o para o Componente da comissão
    {
        // valida  a �rea cultural
        if (areaCultural == '0')
        {
            flag = true;
            document.getElementById('erroAreaCultural').innerHTML = '*';
            mostrar_menu_dados_agente();
        }
        else
        {
            document.getElementById('erroAreaCultural').innerHTML = '';
        }
    }
    else
    {
        document.getElementById('erroVisao').innerHTML = '';
    }

    // caso Não tenha erros
    if (flag == false)
    {
        return true;
    }
} // fecha fun��o validaAgente()



/**
 * Efetua a valida��o do formul�rio de dirigentes
 */
function validaDirigente()
{
    uf               = document.getElementById("uf")              .options[document.getElementById("uf").selectedIndex].value;
    cidade           = document.getElementById("cidade")          .options[document.getElementById("cidade").selectedIndex].value;
    tipoEndereco     = document.getElementById("tipoEndereco")    .options[document.getElementById("tipoEndereco").selectedIndex].value;
    tipoLogradouro   = document.getElementById("tipoLogradouro")  .options[document.getElementById("tipoLogradouro").selectedIndex].value;

    flag = false;

    if (exibirMsgErro('cpf',         'erroCpf'))         {
        flag = true;
        mostrar_menu_dados_agente();
    }
    if (exibirMsgErro('nome',        'erroNome'))        {
        flag = true;
        mostrar_menu_dados_agente();
    }
    if (exibirMsgErro('cep',         'erroCep'))         {
        flag = true;
        mostrar_menu_dados_agente();
    }
    if (exibirMsgErro('logradouro',  'erroLogradouro'))  {
        flag = true;
        mostrar_menu_dados_agente();
    }
    if (exibirMsgErro('numero',      'erroNumero'))      {
        flag = true;
        mostrar_menu_dados_agente();
    }
    if (exibirMsgErro('complemento', 'erroComplemento')) {
        flag = true;
        mostrar_menu_dados_agente();
    }
    if (exibirMsgErro('bairro',      'erroBairro'))      {
        flag = true;
        mostrar_menu_dados_agente();
    }

    // valida��o para emails

    var tbemails = document.getElementById('tabBuscarEmail').rows.length;

    if(tbemails <=1)
    {
    	emails = false;
    }

    if (emails == false)
    {
        flag = true;
        mostrar_menu_email_agente();
    }

    var tbtels = document.getElementById('tabBuscarFone').rows.length;

    if(tbtels <=1)
    {
    	fones = false;
    }
    // valida��o para telefones
    if (fones == false)
    {
        flag = true;
        mostrar_menu_fone_agente();
    }

    // valida��o do estado
    if (uf == '0')
    {
        flag = true;
        document.getElementById('erroUf').innerHTML = '*';
        mostrar_menu_endereco_agente();
    }
    else
    {
        document.getElementById('erroUf').innerHTML = '';
    }

    // valida��o para cidade
    if (cidade == 0)
    {
        flag = true;
        document.getElementById('erroCidade').innerHTML = '*';
        mostrar_menu_endereco_agente();
    }
    else
    {
        document.getElementById('erroCidade').innerHTML = '';
    }

    // valida��o para tipo de endere�o
    if (tipoEndereco == 0)
    {
        flag = true;
        document.getElementById('erroTipoEndereco').innerHTML = '*';
        mostrar_menu_endereco_agente();
    }
    else
    {
        document.getElementById('erroTipoEndereco').innerHTML = '';
    }

    // valida��o para tipo de logradouro
    if (tipoLogradouro == 0)
    {
        flag = true;
        document.getElementById('erroTipoLogradouro').innerHTML = '*';
        mostrar_menu_endereco_agente();
    }
    else
    {
        document.getElementById('erroTipoLogradouro').innerHTML = '';
    }

    // caso Não tenha erros
    if (flag == false)
    {
        return true;
    }
} // fecha fun��o validaDirigente()



/**
 * Submete o formul�rio de agentes
 */
function verificaValidacaoAgente()
{
    if (validaAgente())
    {
        $("#cpf").attr('disabled','');
        $("#nome").attr('disabled','');
        $("#cep").attr('disabled','');
        $("#numero").attr('disabled','');
        $("#complemento").attr('disabled','');
        $("#tipoEndereco").attr('disabled','');
        $("#tipoLogradouro").attr('disabled','');
        $("#logradouro").attr('disabled','');
        $("#numero").attr('disabled','');
        $("#complemento").attr('disabled','');
        $("#bairro").attr('disabled','');
        $("#uf").attr('disabled','');
        $("#cidade").attr('disabled','');

        $('#erroGeral').hide();
        $('span[class=spanError]').html('');
        $('#formCadAgentes').submit();
    }
    else
    {
        $('#erroGeral').show();
    }
} // fecha fun��o verificaValidacaoAgente()



/**
 * Submete o formul�rio de dirigentes
 */
function verificaValidacaoDirigente()
{
    if (validaDirigente())
    {
        dirigentes = true;

        $("#cpf").attr('disabled','');
        $("#nome").attr('disabled','');
        $("#cep").attr('disabled','');
        $("#numero").attr('disabled','');
        $("#complemento").attr('disabled','');
        $("#tipoEndereco").attr('disabled','');
        $("#tipoLogradouro").attr('disabled','');
        $("#logradouro").attr('disabled','');
        $("#numero").attr('disabled','');
        $("#complemento").attr('disabled','');
        $("#bairro").attr('disabled','');
        $("#uf").attr('disabled','');
        $("#cidade").attr('disabled','');

        $('#erroGeral').hide();
        $('span[class=spanError]').html('');
        $('#formCadAgentes').submit();
    }
    else
    {
        $('#erroGeral').show();
    }
} // fecha fun��o verificaValidacaoDirigente()




function verificarVisao(valor, usu_codigo)
{

    if ((valor == 210) && (usu_codigo != 118))
    {
        $('#visaocomponente').show();
        $('#tbvisaocomponente').show();
    }
    else
    {
        $('#visaocomponente').hide();
        $('#tbvisaocomponente').hide();
    }
}



function salvaragente(cpfId){

    cpf_ou_cnpj = document.getElementById("cpf_ou_cnpj").options[document.getElementById("cpf_ou_cnpj").selectedIndex].text;

    if(cpf_ou_cnpj == 'CPF')
    {
        cpf = document.getElementById(cpfId).value;
        window.location = "./agentes?acao=pr&cpf_ou_cnpj=CPF&cpf="+cpf;
    }
    else
    {
        cnpj = document.getElementById(cpfId).value;
        window.location = "./agentes?acao=pr&cpf_ou_cnpj=CNPJ&cpf="+cnpj;
    }

}


function salvarComponente()
{
    cpf = document.getElementById("cpf").value;
    window.location = "./agentes?acao=buscacpf&cpf="+cpf;
}





function validar(id)
{
    $("#form"+id).validate(
    {
        // Define as regras
        rules:{
            justificativa:{
                // campoNome ser&aacute; obrigatorio (required) e ter� tamanho minimo (minLength)
                required: true,
                minlength: 15
            }
        },
        // Define as mensagens de erro para cada regra
        messages:{
            justificativa:{
                required: "<br />Dados obrigat�rios Não informados.",
                minlength: "<br />A justificativa deve conter, no m�nimo, 15 caracteres"
            }
        }

    });
}


function modal(){

    $('#modalAgentes').modal(
    {
        appendTo:'body',
        focus:true,
        overlayId:'modal-overlay',
        zIndex:1000,
        closeClass:'Close',
        escClose:true
    }
    );

    $('#modalAgentes').draggable({
        handle: '#titleAgentes'
    });


}



/**
 * Fun��es para mostrar as abas
 */
function mostrar_menu_dados_agente(grupoativo)
{
    $('#menuDados').css({
        'background-color':'#36963f',
        'color':'#fff'
    });
    $('#menuDados').css({
        'background-image':'url(../public/img/bg-celulas4.gif)'
    });
    $('#menuEndereco, #menuFone, #menuEmail, #menuDirigente').css({
        'background-color':'',
        'color':'',
        'background-image':''
    });

    $('#tabFone, #tabBuscarFone').hide();
    $('#tabEmail, #tabBuscarEmail').hide();
    $('#tabDirigente, #tabBuscarDirigente').hide();
    $('#tabEndereco, #tabBuscarEndereco').hide();
    $('#tabDados').show();


    if (grupoativo != "118")
    {
        $('#visaocomponente').show();
    }
    else
    {
        $('#visaocomponente').hide();
    }
}

function mostrar_menu_endereco_agente()
{
    $('#menuEndereco').css({
        'background-color':'#36963f',
        'color':'#fff'
    });
    $('#menuEndereco').css({
        'background-image':'url(../public/img/bg-celulas4.gif)'
    });
    $('#menuDados, #menuEmail, #menuFone, #menuDirigente').css({
        'background-color':'',
        'color':''
    });
    $('#menuDados, #menuFone, #menuEmail, #menuDirigente').css({
        'background-image':''
    });


    $('#tabFone, #tabBuscarFone').hide();
    $('#tabEmail, #tabBuscarEmail').hide();
    $('#tabDados').hide();
    $('#tabDirigente, #tabBuscarDirigente').hide();
    $('#tabEndereco, #tabBuscarEndereco').show();
    $('#visaocomponente').hide();

}


function mostrar_menu_fone_agente()
{
    $('#menuFone').css({
        'background-color':'#36963f',
        'color':'#fff'
    });
    $('#menuFone').css({
        'background-image':'url(../public/img/bg-celulas4.gif)'
    });
    $('#menuDados, #menuEndereco, #menuDirigente, #menuEmail').css({
        'background-color':'',
        'color':'',
        'background-image':''
    });

    $('#tabDados').hide();
    $('#tabEmail, #tabBuscarEmail').hide();
    $('#tabDirigente, #tabBuscarDirigente').hide();
    $('#tabFone, #tabBuscarFone').show();
    $('#tabEndereco, #tabBuscarEndereco').hide();

    $('#visaocomponente').hide();
}

function mostrar_menu_email_agente()
{
    $('#menuEmail').css({
        'background-color':'#36963f',
        'color':'#fff'
    });
    $('#menuEmail').css({
        'background-image':'url(../public/img/bg-celulas4.gif)'
    });
    $('#menuDados, #menuEndereco, #menuFone, #menuDirigente').css({
        'background-color':'',
        'color':'',
        'background-image':''
    });

    $('#tabDados').hide();
    $('#tabFone, #tabBuscarFone').hide();
    $('#tabDirigente, #tabBuscarDirigente').hide();
    $('#tabEmail, #tabBuscarEmail').show();
    $('#tabEndereco, #tabBuscarEndereco').hide();

    $('#visaocomponente').hide();
}

function mostrar_menu_dirigente_agente()
{
    $('#menuDirigente').css({
        'background-color':'#36963f',
        'color':'#fff'
    });
    $('#menuDirigente').css({
        'background-image':'url(../public/img/bg-celulas4.gif)'
    });
    $('#menuDados, #menuEndereco, #menuFone, #menuEmail').css({
        'background-color':'',
        'color':'',
        'background-image':''
    });

    $('#tabDados').hide();
    $('#tabEmail, #tabBuscarEmail').hide();
    $('#tabFone, #tabBuscarFone').hide();
    $('#tabDirigente, #tabBuscarDirigente').show();
    $('#tabEndereco, #tabBuscarEndereco').hide();

    $('#visaocomponente').hide();

    idCNPJDirigente = document.getElementById("idAgente").value;
    document.getElementById('iframeDirigente').src = '../manteragentes/dirigentes?acao=cc&idAgenteGeral='+idCNPJDirigente;

    buscarDirigentes();
}

function mudarOpcao()
{
    $('#cpfPro').toggle();
    $('#cnpj').toggle();
}





/**
 * Fun��o para adicionar fones no formul�rio de cadastro
 */
var contFone = 0; // linha a ser exclu�da
function adicionar_fone_agente(validacao)
{
    // FONE
    tipoFone = document.getElementById("tipoFone").value;
    ufFone   = document.getElementById("ufFone").value;
    dddFone  = document.getElementById("dddFone").value;
    fone     = document.getElementById("fone").value;

    divulgarFone = "";
    for (i = 0; i < document.formCadAgentes.divulgarFone.length; i++)
    {
        if (document.formCadAgentes.divulgarFone[i].checked)
        {
            divulgarFone = document.formCadAgentes.divulgarFone[i].value;
        }
    }
    if (validacao && tipoFone == "")
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, selecione o Tipo de Telefone!", "tipoFone");
    }
    else if (validacao && ufFone == 0)
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, selecione a UF!", "ufFone");
    }
    else if (validacao && dddFone == "")
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, selecione o DDD do telefone!", "dddFone");
    }
    else if (validacao && fone == "")
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, informe o Telefone!", "fone");
    }
    else if (validacao && (fone.length != 9 || !(/\d{4}\-\d{4}/.test(fone)) || fone == "0000-0000" ||
        fone == "1111-1111" || fone == "2222-2222" || fone == "3333-3333" ||
        fone == "4444-4444" || fone == "5555-5555" || fone == "6666-6666" ||
        fone == "7777-7777" || fone == "8888-8888" || fone == "9999-9999"))
        {
        alertar("O n�mero do Telefone � inv�lido!", "fone");
    }
    else
    {
        cont = contFone++; // linha a ser exclu�da

        var local = document.getElementById('tabBuscarFone');
        var tblBody = local.tBodies[0];
        var newRow = tblBody.insertRow(-1);
        newRow.setAttribute("id", "fone" + cont);
        newRow.setAttribute("class", "linha");

        var newCell1 = newRow.insertCell(0);
        newCell1.innerHTML = document.getElementById("tipoFone").options[document.getElementById("tipoFone").selectedIndex].text;
        newCell1.innerHTML+= '<input type="hidden" name="tipoFones[]" id="tipoFones[]" value=' + tipoFone + '>';

        var newCell2 = newRow.insertCell(1);
        newCell2.innerHTML = document.getElementById("ufFone").options[document.getElementById("ufFone").selectedIndex].text;
        newCell2.innerHTML+= '<input type="hidden" name="ufFones[]" id="ufFones[]" value=' + ufFone + '>';
        newCell2.setAttribute("class", "centro");

        var newCell3 = newRow.insertCell(2);
        newCell3.innerHTML = document.getElementById("dddFone").options[document.getElementById("dddFone").selectedIndex].text;
        newCell3.innerHTML+= '<input type="hidden" name="dddFones[]" id="dddFones[]" value=' + dddFone + '>';
        newCell3.setAttribute("class", "centro");

        var newCell4 = newRow.insertCell(3);
        newCell4.innerHTML = fone;
        newCell4.innerHTML+= '<input type="hidden" name="Fones[]" id="Fones[]" value=' + fone + '>';
        newCell4.setAttribute("class", "centro");

        var newCell5 = newRow.insertCell(4);
        newCell5.innerHTML = (divulgarFone == 1 ? 'Sim' : 'Não');
        newCell5.innerHTML+= '<input type="hidden" name="divulgarFones[]" id="divulgarFones[]" value=' + divulgarFone + '>';
        newCell5.setAttribute("class", "centro");

        var newCell6 = newRow.insertCell(5);
        newCell6.setAttribute("class", "centro");
        newCell6.setAttribute("name", "enter");
        newCell6.innerHTML = '<div class="botao_icone"><a class="cancelar_ico" href="#" title=" Excluir Fone " onclick=excluirFoneEmail("#fone' + cont + '"); /></div>';

        // limpa o formul�rio
        document.getElementById("tipoFone").options[0].selected = true;
        document.getElementById("ufFone").options[0].selected = true;
        carregar_combo('','dddFone','../ddd/combo','--','');
        document.getElementById("dddFone").options[0].selected = true;
        document.getElementById('fone').value = '';

        fones = true;
        if (validacao)
        {
            msgCadatro();
        }
    }
} // fecha fun��o adicionar_fone_agente()



/**
 * Fun��o para adicionar e-mails no formul�rio de cadastro
 */
var contEndereco = 0; // linha a ser exclu�da
function adicionar_endereco_agente(validacao)
{
    // ENDERECO
    cep             = document.getElementById('cep').value;//
    uf              = document.getElementById('uf').value; //
    cidade          = document.getElementById('cidade').value; //
    tipoEndereco    = document.getElementById('tipoEndereco').value;//
    tipoLogradouro  = document.getElementById("tipoLogradouro").value;
    logradouro      = document.getElementById('logradouro').value;//
    numero          = document.getElementById('numero').value;
    complemento     = document.getElementById('complemento').value;
    bairro          = document.getElementById('bairro').value;

    if (document.getElementById('divulgarEnderecoS').checked){
        divulgarEndereco = 1;
    }else{
        divulgarEndereco = 0;
    }

    if (validacao && (cep == 0 || cep == null || cep == ' ' || cep == '' ))
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, informe um CEP!", "cep");
        exibirMsgErro('cep','erroCep');
    }

    else if (validacao && (cidade == 0 || cidade == null || cidade == ' ' ))
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, selecione uma cidade!", "Cidade");
        exibirMsgErro('cidade','erroCidade');
    }

    else if (validacao && (tipoEndereco == 0 || tipoEndereco == null || tipoEndereco == ' ' ))
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, selecione o tipo de endere�o!", "tipoEndereco");
        exibirMsgErro('tipoEndereco','erroTipoEndereco');
    }

    else if (validacao && (tipoLogradouro == 0 || tipoLogradouro == null || tipoLogradouro == ' ' ))
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, selecione o tipo de logradouro!", "tipoLogradouro");
        exibirMsgErro('tipoLogradouro','erroTipoLogradouro');
    }

    else if (validacao && (numero == '' || numero == null)){
        alertar("Dados obrigat�rios Não informados:\nPor favor, preencha o campo n�mero!", "numero");
        exibirMsgErro('numero','erroNumero');
    }
    else if (validacao && (bairro == ' ' || bairro == '' || bairro == null)){
        alertar("Dados obrigat�rios Não informados:\nPor favor, preencha o campo bairro!", "bairro");
        exibirMsgErro('bairro','erroBairro');
    }
    else if(validacao)
    {
        cont = contEndereco++; // linha a ser exclu�da

        var local = document.getElementById('tabBuscarEndereco');
        var tblBody = local.tBodies[0];
        var newRow = tblBody.insertRow(-1);
        newRow.setAttribute("id", "endereco" + cont);
        newRow.setAttribute("class", "linha");


        var newCell0 = newRow.insertCell(0);
        newCell0.innerHTML = cep;
        newCell0.innerHTML+= '<input type="hidden" name="ceps[]" id="ceps[]" value="' + cep + '" />';

        var newCell1 = newRow.insertCell(1);
        newCell1.innerHTML = document.getElementById("tipoEndereco").options[document.getElementById("tipoEndereco").selectedIndex].text;
        newCell1.innerHTML+= '<input type="hidden" name="tipoEnderecos[]" id="tipoEnderecos[]" value="' + tipoEndereco + '" />';

        var newCell2 = newRow.insertCell(2);
        newCell2.innerHTML = document.getElementById("uf").options[document.getElementById("uf").selectedIndex].text;
        newCell2.innerHTML+= '<input type="hidden" name="ufs[]" id="ufs[]" value="' + uf + '" />';

        var newCell3 = newRow.insertCell(3);
        newCell3.setAttribute("class", "centro");
        newCell3.innerHTML = document.getElementById("cidade").options[document.getElementById("cidade").selectedIndex].text;
        newCell3.innerHTML+= '<input type="hidden" name="cidades[]" id="cidades[]" value="' + cidade + '" />';

        var newCell4 = newRow.insertCell(4);
        newCell4.setAttribute("class", "centro");
        newCell4.innerHTML = logradouro;
        newCell4.innerHTML+= '<input type="hidden" name="logradouros[]" id="logradouros[]" value="' + logradouro + '" />';
        newCell4.innerHTML+= '<input type="hidden" name="numeros[]" id="numeros[]" value="' + numero + '" />';
        newCell4.innerHTML+= '<input type="hidden" name="complementos[]" id="complementos[]" value="' + complemento + '" />';
        newCell4.innerHTML+= '<input type="hidden" name="bairros[]" id="bairros[]" value="' + bairro + '" />';
        newCell4.innerHTML+= '<input type="hidden" name="tipoLogradouros[]" id="tipoLogradouros[]" value="' + tipoLogradouro + '" />';
        newCell4.innerHTML+= '<input type="hidden" name="divulgarEnderecos[]" id="divulgarEnderecos[]" value="' + divulgarEndereco + '" />';


        var newCell5 = newRow.insertCell(5);
        newCell5.innerHTML = ' Correspond�ncias <input type="radio" style="margin:5px" name="correspondenciaEnderecos" id="correspondenciaEnderecos" value="end' + cont +'/>';



        var newCell6 = newRow.insertCell(6);
        newCell6.setAttribute("class", "centro");
        newCell6.setAttribute("name", "enter");
        newCell6.innerHTML = '<div class="botao_icone"><a class="cancelar_ico" href="#" title=" Excluir Endere�o " onclick=excluirFoneEmail("#endereco' + cont + '"); /></div>';

        //		 limpa o formul�rio
        document.getElementById('cep').value = '';
        document.getElementById("uf").options[0].selected = true;
        document.getElementById("cidade").options[0].selected = true;
        document.getElementById("tipoEndereco").options[0].selected = true;
        document.getElementById("tipoLogradouro").options[0].selected = true;
        document.getElementById('logradouro').value = '';
        document.getElementById('numero').value = '';
        document.getElementById('complemento').value = '';
        document.getElementById('bairro').value = '';

        if (validacao)
        {
            msgCadatro();
        }
    }
    enderecos = true;
} // fecha fun��o adicionar_email_agente()













var contEmail = 0; // linha a ser exclu�da
function adicionar_email_agente(validacao)
{
    // E-MAIL
    tipoEmail     = document.getElementById("tipoEmail").value;
    email         = document.getElementById("email").value;

    divulgarEmail = "";
    for (i = 0; i < document.formCadAgentes.divulgarEmail.length; i++)
    {
        if (document.formCadAgentes.divulgarEmail[i].checked)
        {
            divulgarEmail = document.formCadAgentes.divulgarEmail[i].value;
        }
    }

    enviarEmail = "";
    for (i = 0; i < document.formCadAgentes.enviarEmail.length; i++)
    {
        if (document.formCadAgentes.enviarEmail[i].checked)
        {
            enviarEmail = document.formCadAgentes.enviarEmail[i].value;
        }
    }

    if (validacao && tipoEmail == 0)
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, selecione o Tipo de E-mail!", "tipoEmail");
    }
    else if (validacao && email == "")
    {
        alertar("Dados obrigat�rios Não informados:\nPor favor, informe o E-mail!", "email");
    }
    else if (validacao && ((email.indexOf("@") < 1) || (email.lastIndexOf(".") <= email.indexOf("@")) || (email.indexOf("@") == email.length) || !(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email))))
    {
        alertar("E-mail inv�lido!", "email");
    }
    else
    {
        cont = contEmail++; // linha a ser exclu�da

        var local = document.getElementById('tabBuscarEmail');
        var tblBody = local.tBodies[0];
        var newRow = tblBody.insertRow(-1);
        newRow.setAttribute("id", "email" + cont);
        newRow.setAttribute("class", "linha");

        var newCell1 = newRow.insertCell(0);
        newCell1.innerHTML = document.getElementById("tipoEmail").options[document.getElementById("tipoEmail").selectedIndex].text;
        newCell1.innerHTML+= '<input type="hidden" name="tipoEmails[]" id="tipoEmails[]" value="' + tipoEmail + '" />';

        var newCell2 = newRow.insertCell(1);
        newCell2.innerHTML = email;
        newCell2.innerHTML+= '<input type="hidden" name="Emails[]" id="Emails[]" value="' + email + '" />';

        var newCell3 = newRow.insertCell(2);
        newCell3.setAttribute("class", "centro");
        newCell3.innerHTML = (divulgarEmail=='1' ? 'Sim' : 'Não');
        newCell3.innerHTML+= '<input type="hidden" name="divulgarEmails[]" id="divulgarEmails[]" value="' + divulgarEmail + '" />';

        var newCell4 = newRow.insertCell(3);
        newCell4.setAttribute("class", "centro");
        newCell4.innerHTML = (enviarEmail=='1' ? 'Sim' : 'Não');
        newCell4.innerHTML+= '<input type="hidden" name="enviarEmails[]" id="enviarEmails[]" value="' + enviarEmail + '" />';

        var newCell5 = newRow.insertCell(4);
        newCell5.setAttribute("class", "centro");
        newCell5.innerHTML = '<div class="botao_icone"><a class="cancelar_ico" href="#" title=" Excluir E-mail " onclick=excluirFoneEmail("#email' + cont + '"); /></div>';

        // limpa o formul�rio
        document.getElementById("tipoEmail").options[0].selected = true;
        document.getElementById('email').value = '';

        emails = true;
        if (validacao)
        {
            msgCadatro();
        }
    }
} // fecha fun��o adicionar_email_agente()

/**
 * Mensagem de cadastro de e-mail e telefone
 */
function msgCadatro()
{
    $("#confirma").html("Cadastro realizado com sucesso");
    $("#confirma").dialog({
        title : 'Confirma',
        resizable: false,
        width:350,
        height:180,
        modal: true,
        autoOpen:false,
        buttons : {
            'OK' : function(){
                $(this).dialog('close')
                }
        }
    });
    $("#confirma").dialog('open');

}

/**
 * Mensagem de exclus�o de e-mail e telefone
 */
function msgExclusao()
{
    $("#confirma").html("Exlus�o realizada com sucesso");
    $("#confirma").dialog({
        title : 'Confirma',
        resizable: false,
        width:350,
        height:180,
        modal: true,
        autoOpen:false,
        buttons : {
            'OK' : function(){
                $(this).dialog('close')
                }
        }
    });
    $("#confirma").dialog('open');

}

/**
 * Fun��o para exclus�o de telefone e email
 */
function excluirFoneEmail(idLinha)
{

    confirmar = msgExclusao("Deseja realmente excluir dados?");

    $("#confirma").html("Deseja realmente excluir dados?");
    $("#confirma").dialog({
        title : 'Confirma',
        resizable: false,
        width:350,
        height:180,
        modal: true,
        autoOpen:false,
        buttons : {

            'Cancelar' : function(){
                $(this).dialog('close')
                },
            'Confirmar' : function(){
                $(idLinha).remove();
                msgExclusao();
                return true;
                }

        }
    });
    $("#confirma").dialog('open');


} // fecha fun��o excluirFoneEmail()



/**
 * Fun��o para adicionar dirigentes no formul�rio de cadastro
 */
function adicionar_dirigente_agente(validacao)
{
    if (document.getElementById('cpf').length != 18 && $('#erroCpf').html() != "")
    {
        alertar("Para cadastrar Dirigentes � necess�rio que o Agente tenha um CNPJ v�lido!");
        mostrar_menu_dados_agente();
    }
    else
    {
        // modal com os dados do dirigente
        $("#modalDirigente").dialog("destroy");
        $("#modalDirigente").dialog
        ({
            width:920,
            height:620,
            EscClose:false,
            modal:true
            ,
            buttons:
            {
                'Fechar':function()
                {
                    $(this).dialog('close'); // fecha a modal
                    buscarDirigentes();
                    dirigentes = true;
                }
            }
        });
        $('.ui-dialog-titlebar-close').remove();
    } // fecha else
} // fecha fun��o adicionar_dirigente_agente()



/**
 * Fun��o para alterar dirigentes
 */
function alterar_dirigente(cpf)
{
    idCNPJDirigente = document.getElementById("idAgente").value;
    document.getElementById('iframeAlterarDirigente').src = '../manteragentes/dirigentes?acao=cc&cpf='+cpf+'&idAgenteGeral='+idCNPJDirigente;

    // modal com os dados do dirigente
    $("#modalAlterarDirigente").dialog("destroy");
    $("#modalAlterarDirigente").dialog
    ({
        width:920,
        height:620,
        EscClose:false,
        modal:true
        ,
        buttons:
        {
            'Fechar':function()
            {
                $(this).dialog('close'); // fecha a modal
                buscarDirigentes();
            }
        }
    });
    $('.ui-dialog-titlebar-close').remove();
} // fecha fun��o alterar_dirigente()



function existeDirigente()
{
    dirigentes = true;
}
//-->
