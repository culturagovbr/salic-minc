<!--
/**
 * M�scaras, efeitos din�micos e etc
 * @author Emanuel Sampaio <contato@emanuelsampaio.com.br>
 * @since 24/03/2010
 * @version 1.0
 * @copyright � 2010 Politec - Todos os direitos reservados.
 * @package js
 * @link http://www.politec.com.br
 */

/**
 * M�scaras para formatar DATA, VALOR E ETC
 */

function selecionartodos(field)
{
    for (i = 0; i < field.length; i++)
        field[i].checked = true;
}

function deselecionartodos(field)
{
    for (i = 0; i < field.length; i++)
        field[i].checked = false;
}

function mascaraCnpjCpf(objeto)
{
    obj = objeto;
    
    if(obj.value.length <= 14){
        fun = format_cpf;
    }else{
        fun = format_cnpj;
    }
        
    setTimeout("exec_mascara()", 1);
}

function mascaraCnpjCpfCaptacao(objeto)
{
    obj = objeto;
    
    if(obj.value.length < 14){
        fun = format_cpf;
    }else{
        fun = format_cnpj;
    }
        
    setTimeout("exec_mascara()", 1);
}

function mascaraProcesso(valor){
    valor = valor.replace( /[^0-9]/g, '' );
    return  valor.substring(0, 5)+'.'+valor.substring(5, 11)+'/'+valor.substring(11, 15)+'-'+valor.substring(15, 17);
}

function mascara(objeto, funcao)
{
    obj = objeto;
    fun = funcao;
    setTimeout("exec_mascara()", 1);
}

function exec_mascara()
{
    obj.value = fun(obj.value);
}

function format_num(v) // formato: somente n�meros inteiros
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{0})(\d)/, "$1$2");
    return v;
}
function format_num_pontos(v) // formato: somente n�meros inteiros
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d+)(\d{3})$/g, "$1.$2");
    var qtd  = (v.length - 3) / 3;
    var cont = 0;
    while (qtd > cont)
    {
        cont++;
        v = v.replace(/(\d+)(\d{3}.*)/, "$1.$2");
    }
    v = v.replace(/^(0+)(\d)/g, "$2");
    return v;
}

function format_num_float(v) // formato: 2.5 (aceita . e numero)
{
    v = v.replace(',', '.');
    v = v.replace(/[^0-9\.]/g, '');
    return v;
}

function format_hora(v) // formato: 00:00:00
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{2})(\d)/, "$1:$2");
    v = v.replace(/(\d{2})(\d)/, "$1:$2");
    // verifica_hora(tipo);
    return v;
}

function format_data(v) // formato: DD/MM/AAAA
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{2})(\d)/, "$1/$2");
    v = v.replace(/(\d{2})(\d)/, "$1/$2");
    return v;
}

function format_cep(v) // formato: 99.999-999
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{2})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d)/, "$1-$2");
    return v;
}

function format_fone(v) // formato: (99) 9999-9999
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{0})(\d)/, "$1($2");
    v = v.replace(/(\d{2})(\d)/, "$1) $2");
    v = v.replace(/(\d{4})(\d)/, "$1-$2");
    return v;
}

function format_tel(v) // formato: 9999-9999
{
    if(v.length == 10){
        v = v.replace(/\D/g, "");
        v = v.replace(/(\d{5})(\d)/, "$1-$2");
    }else{
        v = v.replace(/\D/g, "");
        v = v.replace(/(\d{4})(\d)/, "$1-$2");
    }

    return v;
}

function format_cpf(v) // formato: 999.999.999-99
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{3})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d)/, "$1-$2");
    return v;
}

function format_cnpj(v) // formato: 99.999.999/9999-99
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d{2})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d)/, "$1.$2");
    v = v.replace(/(\d{3})(\d)/, "$1/$2");
    v = v.replace(/(\d{4})(\d)/, "$1-$2");
    return v;
}

function format_float(v) // formato: 9999999.99
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d)(\d{2})$/, "$1.$2");
    return v;
}

function format_moeda(v) // formato: 9.999.999,99
{
    v = v.replace(/\D/g, "");
    v = v.replace(/(\d)(\d{2})$/, "$1,$2");
    v = v.replace(/(\d+)(\d{3},\d{2})$/g, "$1.$2");
    var qtd  = (v.length - 3) / 3;
    var cont = 0;
    while (qtd > cont)
    {
        cont++;
        v = v.replace(/(\d+)(\d{3}.*)/, "$1.$2");
    }
    v = v.replace(/^(0+)(\d)/g, "$2");
    return v;
}

function format_processo(v){
    alert(1)
}

function validar_email(email) // valida��o de e-mail
{
    if (email == "" || email == "undefined")
    {
        alertar("Por favor, informe seu e-mail!", "email");
    }
    else if ((email.indexOf("@") < 1) || (email.lastIndexOf(".") <= email.indexOf("@")) || (email.indexOf("@") == email.length))
    {
        alertar("Email inv�lido!", "email");
    }
}



/**
 * Mensagens de alerta
 */
function alertar(msg, id)
{
    $("#confirma").html(msg);
    $("#confirma").dialog({
        title : 'Alerta',
        resizable: false,
        width:350,
        height:180,
        modal: true,
        autoOpen:false,
        buttons : {
            'OK' : function(){
                $(this).dialog('close')
                if (id != null) // ativa o campo do formul�rio vazio
                {
                    $("#"+id).focus();
                }
            }
        }
    });
    $("#confirma").dialog('open');
}



/**
 * Mensagens de confirma��o
 */
function confirmar(msg)
{
    if (confirm(msg))
    {
        return true;
    }
    else
    {
        return false;
    }
}



/**
 * Redirecionamento de p�gina
 */
function redirecionar(url)
{
    location.href = url;
}



/**
 * Voltar p�gina anterior
 */
function voltar()
{
    history.go(-1);
}



/**
 * Imprimir p�gina
 */
function imprimir()
{
    window.print();
}



/**
 * Quantidade de caracteres em campos textarea
 */
function caracteresTextarea(campo, contador, limite)
{
    if (campo.value.length > limite)
    {
        campo.value = campo.value.substring(0, limite);
    }
    else
    {
        contador.value = limite - campo.value.length;
    }
}



/**
 * Fun��es para senha segura
 */
function caracteres_aceitos(valor)
{
    var especiais = /[@!#$%&*+=?|-]/;
    var maiuscula = /[A-Z]/;
    var minuscula = /[a-z]/;
    var numeros   = /[0-9]/;
    var cont      = 0;
    if (especiais.test(valor)) cont++;
    if (maiuscula.test(valor)) cont++;
    if (minuscula.test(valor)) cont++;
    if (numeros.test(valor))   cont++;
    return cont;
}

function testa_senha(valor)
{
    var id = document.getElementById("seguranca");
    var c = caracteres_aceitos(valor); // tipos de caracteres
    var q = valor.length; // quantidade de caracteres
    if (q <= 0)
    {
        id.innerHTML = "&nbsp;";
        id.style.backgroundColor = "white";
    }
    else if (q >= 8 && c >= 3)
    {
        id.innerHTML = "<strong>A L T A</strong>";
        id.style.backgroundColor = "green";
        id.style.color = "white";
    }
    else if (q >= 8 && c >= 2 || q >= 5 && c >= 3)
    {
        id.innerHTML = "<strong>M � D I A</strong>";
        id.style.backgroundColor = "orange";
        id.style.color = "white";
    }
    else
    {
        id.innerHTML = "<strong>B A I X A</strong>";
        id.style.backgroundColor = "red";
        id.style.color = "white";
    }
} // fecha fun��o testa_senha()



/**
 * Fun��es para mostra e ocultar sub-�tens: [+] e [-]
 */

var id_cache = ""; // vari�vel para guardar o id ativo na mem�ria

// fun��o para mostrar um elemento escondido
function aparecer(id, exibir, ocultar)
{
    if (id_cache != "")
    {
        esconder(id_cache, 'exibir'+id_cache, 'ocultar'+id_cache);
    }
    id_cache = id;
    $("#" + id).show();
    $("#" + ocultar).show();
    $("#" + exibir).hide();

    $("." + id).show();
    $("." + ocultar).show();
    $("." + exibir).hide();
}

//fun��o para esconder um elemento vis�vel
function esconder(id, exibir, ocultar)
{
    $("#" + id).hide();
    $("#" + ocultar).hide();
    $("#" + exibir).show();

    $("." + id).hide();
    $("." + ocultar).hide();
    $("." + exibir).show();
}



/**
 * Fun��o para abrir e fechar a grid (modelo 1)
 */
function grid_01(elemento)
{
	if ( $('#' + elemento).attr('class') == 'btn_adicionar' )
	{
		$('#' + elemento).removeClass('btn_adicionar');
		$('#' + elemento).addClass('btn_remover');
		$('#' + elemento).attr({title:'Recolher'});
	}
	else
	{
		$('#' + elemento).removeClass('btn_remover');
		$('#' + elemento).addClass('btn_adicionar');
		$('#' + elemento).attr({title:'Expandir'});
	}
	$('#div_' + elemento).toggle('slow');
} // fecha fun��o grid_01()



/**
 * Fun��o para abrir e fechar a grid (modelo 3)
 * Foi retirada a fun��o toggle(), pois, a mesma n�o funcionou com tbody no IE 7
 */
function grid_03(elemento)
{
	if ($('#' + elemento).find('div').attr('class') == 'icn_mais')
	{
		$('#' + elemento).find('div').removeClass('icn_mais');
		$('#' + elemento).find('div').addClass('icn_menos');
		$('#' + elemento).find('a').attr({title:'Recolher'});
		$('#tbody_' + elemento).removeClass('sumir');
	}
	else
	{
		$('#' + elemento).find('div').removeClass('icn_menos');
		$('#' + elemento).find('div').addClass('icn_mais');
		$('#' + elemento).find('a').attr({title:'Expandir'});
		$('#tbody_' + elemento).addClass('sumir');
	}
} // fecha fun��o grid_03()



/**
 * Fun��es para destacar uma linha, quando o usu�rio clica em cima da mesma ou passa o mouse
 */

var obj_tr = "";

function over_tr(obj, cor) // onmouseover
{
    if (obj_tr == obj)
    {
        return;
    }
    if (cor == undefined)
    {
        $(obj).addClass("fundo_linha3");
    }
    else
    {
        $(obj).addClass(cor);
    }
}

function out_tr(obj, cor) // onmouseout
{
    if (obj_tr == obj)
    {
        return;
    }
    if (cor == undefined)
    {
        $(obj).removeClass("fundo_linha3");
    }
    else
    {
        $(obj).addClass(cor);
    }
}

function click_tr(obj, cor) // onclick
{
    if (obj_tr != "")
    {
        $(obj_tr).removeClass("fundo_linha3");
        $(obj_tr).removeClass("fundo_linha4");
    }
    if (obj_tr == obj)
    {
        obj_tr = '';
        return;
    }
    obj_tr = obj;

    if (cor == undefined)
    {
        $(obj).addClass("fundo_linha4");
    }
    else
    {
        $(obj).addClass(cor);
    }
}



/**
 * Fun��o para limpar campos de formul�rio
 */

function limpar_campo(campo, texto)
{
    if (campo.value == texto)
    {
        campo.value = '';
    }
}



/**
 * Fun��o para retornar o campo default do formul�rio preenchido
 */

function restaurar_campo(campo, texto)
{
    if (campo.value == texto || campo.value == "")
    {
        campo.value = texto;
    }
}



//fun��o para validar CPF


function valida_cnpj(cnpj)
{
    cnpj = cnpj.replace(".","");
    cnpj = cnpj.replace(".","");
    cnpj = cnpj.replace("/","");
    cnpj = cnpj.replace("-","");

    var numeros, digitos, soma, i, resultado, pos, tamanho, digitos_iguais;
    digitos_iguais = 1;
    if (cnpj.length < 14 && cnpj.length < 15)
        return false;
    for (i = 0; i < cnpj.length - 1; i++)
        if (cnpj.charAt(i) != cnpj.charAt(i + 1))
              {
              digitos_iguais = 0;
              break;
              }
    if (!digitos_iguais)
    {
        tamanho = cnpj.length - 2
        numeros = cnpj.substring(0,tamanho);
        digitos = cnpj.substring(tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--)
              {
              soma += numeros.charAt(tamanho - i) * pos--;
              if (pos < 2)
                    pos = 9;
              }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0))
              return false;
        tamanho = tamanho + 1;
        numeros = cnpj.substring(0,tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--)
              {
              soma += numeros.charAt(tamanho - i) * pos--;
              if (pos < 2)
                    pos = 9;
              }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1))
              return false;
        return true;
    }
else
    return false;
}


function validaCPF(value)
{
    value = value.replace(".","");
    value = value.replace(".","");
    cpf = value.replace("-","");
    while(cpf.length < 11) cpf = "0"+ cpf;
    var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
    var a = [];
    var b = new Number;
    var c = 11;
    for (i=0; i<11; i++){
        a[i] = cpf.charAt(i);
        if (i < 9) b += (a[i] * --c);
    }
    if ((x = b % 11) < 2) {
        a[9] = 0
        } else {
        a[9] = 11-x
        }
    b = 0;
    c = 11;
    for (y=0; y<10; y++) b += (a[y] * c--);
    if ((x = b % 11) < 2) {
        a[10] = 0;
    } else {
        a[10] = 11-x;
    }
    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg)) return false;
    return true;
}



function validaData(campo,nomeFormulario){
    var date = campo.value;
    var array_data = new Array;
    var ExpReg = new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
    array_data = date.split("/");
    dia = parseInt(array_data[0],10);
    mes = parseInt(array_data[1],10);
    anoValida = parseInt(array_data[2],10);
    erro = false;
    ano = parseInt(array_data[2],10);
    if(date.length==0){
        //campo.className = 'campoTexto';
        return true;
    }
    if((ano<1900)||(ano>2099))
        erro = true;
    if ( date.search(ExpReg) == -1 )
        erro = true;
    //Valido os meses que nao tem 31 dias com execao de fevereiro
    else
    //Valido os meses com 30 dias.
    if ( ( ( array_data[1] == 4 ) || ( array_data[1] == 6 ) ||
        ( array_data[1] == 9 ) || ( array_data[1] == 11 ) ) && ( array_data[0] > 30 ) )
        erro = true;
    //Valido o mes de fevereiro
    else
    if ( array_data[1] == 2 ) {
        //Valido ano que nao e bissexto
        if ( ( array_data[0] > 28 ) && ( ( array_data[2] % 4 ) != 0 ) )
            erro = true;
        //Valido ano bissexto
        if ( ( array_data[0] > 29 ) && ( ( array_data[2] % 4 ) == 0 ) )
            erro = true;
    }
    if($(campo).attr('anomenor')!= undefined){
        dia = array_data[0];
        mes = array_data[1];
        ano = array_data[2];
        if($(campo).attr('anomenor') < ano+'-'+mes+'-'+dia)
            erro = true;
    }
    if(erro){
        //campo.className = 'campoTextoErro';
        $("#dialog-alert2").dialog({
            resizable: false,
            width:300,
            height:160,
            modal: true,
            autoOpen:false,
            buttons: {
                'OK': function() {
                    $(this).dialog('close');
                }
            }
        });

        $("#dialog-alert2").dialog('open');
        $("#dialog-alert2").html("Data Inv\xE1lida");
        campo.value = "";
        if ( nomeFormulario != "" ){
            $('#'+nomeFormulario).focus();
        }else{
            $('#formulario').focus();
        }
        return false;
    }
    else{
        //campo.className = 'campoTexto';
        return true;
    }
}

function validaDigito(e)
{
    var controle = false;

    // Pega o valor ASCII da tecla que o usu�rio pressionou
    if(window.event){ //Internet Explorer
        tecla   = e.keyCode;
    }
    else{ //Demais browsers
        tecla = e.which;
    }

    // Permite a digita��o das seguintes teclas: Backspace, Insert, Del, Page UP, Page Down, Home, End, setas de movimenta??o e Shift.
    // Acrescentada a tecla Tab em 13/03/2007
    if (tecla == 8 || tecla == 37 || tecla == 38 || tecla == 39 ||
        tecla == 40 || tecla == 46 || tecla == 36 || tecla == 35 ||
        tecla == 33 || tecla == 34  || tecla == 45 || tecla == 16 || tecla == 9 || tecla==13)
        {
        return;
    }

    // Verifica se a tecla � um d�gito, sendo que o shift n�o pode estar sendo pressionado
    if ((tecla >= 48 && tecla <= 57) && !e.shiftKey){
        return;
    }

    //  Verifica se a tecla � um d�gito do teclado n?m?rico
    if (tecla >= 96 && tecla <= 105){
        return;
    }

    // Permite a digita��o da tecla alt
    if (e.altKey){
        return;
    }

    // Verifica se foi entrada a sequ�ncia Ctrl+c ou Ctrl+v
    if((e.ctrlKey && tecla == 67) || (e.ctrlKey && tecla == 86)){
        controle = true;
        if(browser.isIE){
            setTimeout("validaCampo(\'"+e.srcElement.id+"\')",100);
        }
        else{
            setTimeout("validaCampo(\'"+e.target.id+"\')",100);
        }
    }
    var caracter = String.fromCharCode(tecla);

    //Verifica se o caractere n�o est� entre 0-9, se foi utilizada a sequ�ncia Ctrl+c ou Ctrl+v e se a tecla Shift estava pressionada
    //Se verdadeiro, n�o permite a digita��o do caractere.
    if(((("0123456789".indexOf(caracter) == -1) && !controle)) || e.shiftKey)
    {
        if (window.event){ //IE
            window.event.returnValue = null;
        }
        else{ //Firefox
            e.preventDefault();
            return false;
        }
    }
    else
        return;
}



/**
 * Fun��o com o alert em forma de modal
 */
function alertModal(titulo, mensagem, largura, altura, campo, url, id_formulario)
{
	titulo  = (titulo  == null) ? "ALERT" : titulo;
	largura = (largura == null) ? 400     : largura;
	altura  = (altura  == null) ? 220     : altura;

	$('#' + mensagem).dialog("destroy");

	$('#' + mensagem).dialog
	({
		modal: true,
		resizable: false,
		width: largura,
		height: altura,
		title: titulo,
		buttons:
		{
			"Ok": function()
			{
				$(this).dialog("close");
				if (campo != null) // ativa o campo do formul�rio vazio
				{
					document.getElementById(campo).focus();
				}
				if (url != null) // faz o redirecionamento para uma p�gina
				{
					redirecionar(url);
				}
				if (id_formulario != null) // envia o formul�rio
				{
					$('#' + id_formulario).submit();
				}
			}
		}
	});
} // fecha fun��o alertModal()

function alertModalPt(titulo, mensagem, largura, altura, campo, url, id_formulario)
{
	titulo  = (titulo  == null) ? "ALERTA" : titulo;
	largura = (largura == null) ? 400     : largura;
	altura  = (altura  == null) ? 220     : altura;

	$('#' + mensagem).dialog("destroy");

	$('#' + mensagem).dialog
	({
		modal: true,
		resizable: false,
		width: largura,
		height: altura,
		title: titulo,
		buttons:
		{
			"Ok": function()
			{
				$(this).dialog("close");
				if (campo != null) // ativa o campo do formul�rio vazio
				{
					document.getElementById(campo).focus();
				}
				if (url != null) // faz o redirecionamento para uma p�gina
				{
					redirecionar(url);
				}
				if (id_formulario != null) // envia o formul�rio
				{
					$('#' + id_formulario).submit();
				}
			}
		}
	});
} // fecha fun��o alertModal()

/**
 * Funcao com o confirm em forma de modal
 */
function confirmModal(titulo, mensagem, largura, altura, campo_01, url_01, id_formulario_01, campo_02, url_02, id_formulario_02)
{
	titulo  = (titulo  == null) ? "CONFIRM" : titulo;
	largura = (largura == null) ? 400       : largura;
	altura  = (altura  == null) ? 220       : altura;

	$('#' + mensagem).dialog("destroy");

	$('#' + mensagem).dialog
	({
		modal: true,
		resizable: false,
		width: largura,
		height: altura,
		title: titulo,
		buttons:
		{
            "N\u00e3o": function()
			{
				$(this).dialog("close");
				if (campo_02 != null) // ativa o campo do formulario vazio
				{
					document.getElementById(campo_02).focus();
				}
				if (url_02 != null) // faz o redirecionamento para uma pagina
				{
					redirecionar(url_02);
				}
				if (id_formulario_02 != null) // envia o formulario
				{
					$('#' + id_formulario_02).submit();
				}
			},
			"Sim": function()
			{
				$(this).dialog("close");
				if (campo_01 != null) // ativa o campo do formulario vazio
				{
					document.getElementById(campo_01).focus();
				}
				if (url_01 != null) // faz o redirecionamento para uma pagina
				{
					redirecionar(url_01);
				}
				if (id_formulario_01 != null) // envia o formulario
				{
					$('#' + id_formulario_01).submit();
				}
			}

		}
	});
}



/**
 * Funcao para pegar os dias decorridos entre duas datas no formato (DD/MM/AAAA)
 */
function diasDecorridosEntreDuasDatas(dataInicial, dataFinal)
{
	// ajusta as datas
	var dia1 = new Number(dataInicial.substr(0, 2));
	var mes1 = new Number(dataInicial.substr(3, 2) - 1);
	var ano1 = new Number(dataInicial.substr(6, 4));
	var dia2 = new Number(dataFinal.substr(0, 2));
	var mes2 = new Number(dataFinal.substr(3, 2) - 1 );
	var ano2 = new Number(dataFinal.substr(6, 4));

	dataInicial = new Date(ano1, mes1, dia1);
	dataFinal   = new Date(ano2, mes2, dia2);

	// vari�veis auxiliares
	var minuto       = 60000;
	var dia          = minuto * 60 * 24;
	var horarioVerao = 0;

	// ajusta o horario de cada objeto Date
	dataInicial.setHours(0);
	dataInicial.setMinutes(0);
	dataInicial.setSeconds(0);
	dataFinal.setHours(0);
	dataFinal.setMinutes(0);
	dataFinal.setSeconds(0);

	// determina o fuso hor�rio de cada objeto Date
	var fh1 = dataInicial.getTimezoneOffset();
	var fh2 = dataFinal.getTimezoneOffset();

	// retira a diferen�a do hor�rio de ver�o
	if (dataFinal > dataInicial)
	{
		horarioVerao = (fh2 - fh1) * minuto;
	}
	else
	{
		horarioVerao = (fh1 - fh2) * minuto;
	}

	var dif = Math.abs(dataFinal.getTime() - dataInicial.getTime()) - horarioVerao;
	return Math.ceil(dif / dia);
} // fecha funcao diasDecorridosEntreDuasDatas()



/**
 * Funcao para validar uma data inicial e uma final no formato (DD/MM/AAAA)
 * Se a primeira for menor retorna 0
 * Se a seguda for menor retorna 1
 * Se forem iguais retorna 2
 */
function compararDataInicialDataFinal(dataInicial, dataFinal)
{
	// ajusta as datas
	var dia1 = new Number(dataInicial.substr(0, 2));
	var mes1 = new Number(dataInicial.substr(3, 2) - 1);
	var ano1 = new Number(dataInicial.substr(6, 4));
	var dia2 = new Number(dataFinal.substr(0, 2));
	var mes2 = new Number(dataFinal.substr(3, 2) - 1);
	var ano2 = new Number(dataFinal.substr(6, 4));
	dataInicial = new Date(ano1, mes1, dia1);
	dataFinal   = new Date(ano2, mes2, dia2);

	// ajusta o horario de cada objeto Date
	dataInicial.setHours(0);
	dataInicial.setMinutes(0);
	dataInicial.setSeconds(0);
	dataFinal.setHours(0);
	dataFinal.setMinutes(0);
	dataFinal.setSeconds(0);

	if (dataInicial < dataFinal)
	{
		return 0;
	}
	else
	{
		if (dataInicial > dataFinal)
		{
			return 1;
		}
		else
		{
			return 2;
		}
	}
} // fecha funcao compararDataInicialDataFinal()



/**
 * Funcao para validar uma data no formato (DD/MM/AAAA)
 */
function validarData(campo)
{
	var expReg = /^(([0-2]\d|[3][0-1])\/([0]\d|[1][0-2])\/[1-2][0-9]\d{2})$/;

	if ((campo.match(expReg)) && (campo != ''))
	{
		var dia = new Number(campo.substr(0, 2));
		var mes = new Number(campo.substr(3, 2));
		var ano = new Number(campo.substr(6, 4));

		// dia incorreto, o m�s especificado cont�m no m�ximo 30 dias
		if ((mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia > 30)
		{
			return false;
		}
		else
		{
			// data incorreta, o m�s especificado cont�m no m�ximo 28 dias
			if ((ano % 4 != 0 && mes == 2) && dia > 28)
			{
				return false;
			}
			else
			{
				// data incorreta, o m�s especificado cont�m no m�ximo 29 dias
				if ((ano % 4 == 0 && mes == 2) && dia > 29)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}
	}
	else
	{
		return false;
	}
} // fecha funcao validarData()



/**
 * Funcao para pegar a data atual no formato (DD/MM/AAAA)
 */
function pegarDataAtual()
{
	var data = new Date();
	var dia  = (data.getDate() < 10) ? '0' + data.getDate() : data.getDate();
	var mes  = ((data.getMonth() + 1) < 10) ? '0' + (data.getMonth() + 1) : (data.getMonth() + 1);
	var ano  = data.getFullYear();

	return dia + "/" + mes + "/" + ano;
} // fecha funcao pegarDataAtual()



/**
 * Fun��o para somar dias a uma data
 * @param date (data no padr�o brasileiro)
 * @param integer (quantidade de dias a serem adicionados/retirados na data)
 * @param char (indica se dever� ser adicionado ou retirado dias. Valores: [+] ou [-])
 * @return date
 */
function somarData(dataAtual, qtdDias, tipoOperacao)
{
	qtdDias = parseInt(qtdDias);

	if (qtdDias > 0)
	{
		var dataAtual = dataAtual.split("/");
		var dia = dataAtual[0];
		var mes = dataAtual[1];
		var ano = dataAtual[2];
		dia = dia + '';
		mes = mes + '';
		ano = ano + '';

		r = new Date(ano, (mes - 1), dia);

		if (tipoOperacao == '-') // subtrai os dias na data
		{
			r.setDate(r.getDate() - qtdDias);
		}
		else // soma os dias na data
		{
			r.setDate(r.getDate() + qtdDias);
		}

		dia = (r.getDate() < 10) ? '0' + r.getDate() : r.getDate();
		mes = ((r.getMonth() + 1) < 10) ? '0' + (r.getMonth() + 1) : (r.getMonth() + 1);
		ano = r.getFullYear();

		return (dia + "/" + mes + "/" + ano);
	}
	else
	{
		return dataAtual;
	}
} // fecha fun��o somarData()



/**
 * Funcao responsavel travar o salvamento, alteracao e exclusao de dados do formulario.
 * 
 */
function JSBloquearAlteracaoFormulario()
{
    /*------------- Salvar / Cadastrar / Incluir / Enviar /Novo -------------*/
    $('body').find('.btn_salvar').each(function(){
            $(this).replaceWith("<input type='button' class='btn_salvar-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });
   $('body').find('.btn_cadastrar').each(function(){
        $(this).replaceWith("<input type='button' class='btn_cadastrar-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });
   $('body').find('.btn_enviar').each(function(){
        $(this).replaceWith("<input type='button' class='btn_enviar-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });
   $('body').find('.btn_incluir').each(function(){
        $(this).replaceWith("<input type='button' class='btn_incluir-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });
   $('body').find('.btn_novo').each(function(){
       $(this).replaceWith("<input type='button' class='btn_novo-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });

   /* ==== Alterar / Editar ==== */
   $('body').find('.btn_editar').each(function(){
        $(this).replaceWith("<input type='button' class='btn_editar-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });
   $('body').find('.btn_alterar').each(function(){
        $(this).replaceWith("<input type='button' class='btn_alterar-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });

   /* ==== Excluir ==== */
   $('body').find('.btn_exclusao').each(function(){
        $(this).replaceWith("<input type='button' class='btn_exclusao-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });
   $('body').find('.btn_excluir').each(function(){
        $(this).replaceWith("<input type='button' class='btn_exclusao-off' name='btn_desabilitado' id='btn_desabilitado' disabled>");
   });
   /* ==== Confirmar / Aprovar ==== */
   $('body').find('.btn_confirmar').each(function(){
        $(this).replaceWith("<input type='button' class='btn_confirmar-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });
   $('body').find('.btn_aprovar').each(function(){
        $(this).replaceWith("<input type='button' class='btn_aprovar-off' name='btn_desabilitado' id='btn_desabilitado'>");
   });

    $('form').find('input, textarea, button, select, .btn').attr('disabled','disabled');

    $('.coll-produto').find('.btn').attr('disabled','disabled');

}



// ========== AP�S O CARREGAMENTO DA P�GINA ==========
$(document).ready(function()
{
	$('tr.registros, .registros tr').mouseover(function() // mouse em cima da linha
	{
		over_tr(this);
	});
	$('tr.registros, .registros tr').focus(function() // mouse em cima da linha
	{
		over_tr(this);
	});
	$('tr.registros, .registros tr').mouseout(function() // retirar mouse de cima da linha
	{
		out_tr(this);
	});
	$('tr.registros, .registros tr').blur(function() // retirar mouse de cima da linha
	{
		out_tr(this);
	});
	$('tr.registros, .registros tr').click(function() // clicar em cima da linha
	{
		click_tr(this);
	});
	$('tr.registros, .registros tr').keypress(function() // clicar em cima da linha
	{
		click_tr(this);
	});

	$('input[name=grid1]').click(function() // grid modelo 1
	{
		grid_01(this.id);return false;
	});
	$('div[name=grid3]').click(function() // grid modelo 3
	{
		grid_03(this.id);return false;
	});

	/* planilha de custos */
	$('.planilha_incentivo').click(function() // mecanismo
	{
		planilha_custos(this, 'MECANISMO');return false;
	});
	$('.planilha_produto').click(function() // produto
	{
		planilha_custos(this, 'PRODUTO');return false;
	});
	$('.planilha_etapa').click(function() // etapa
	{
		planilha_custos(this, 'ETAPA');return false;
	});
	$('.planilha_uf').click(function() // uf
	{
		planilha_custos(this, 'UF');return false;
	});
});

/**
 * Funcao responsavel por retornar um valor boleano TRUE ou FALSE caso o valor passado esteja dentro do array informado
 * Esta funcao e semelhante a funcao in_array() do PHP
 * 
 */
function in_array(valor, vetor) {
    var length = vetor.length;
    for(var i = 0; i < length; i++) {
        if(vetor[i] == valor) return true;
    }
    return false;
}
