<!--
/**
 * Funções AJAX
 * @author Emanuel Sampaio <contato@emanuelsampaio.com.br>
 * @since 24/03/2010
 * @version 1.0
 * @copyright © 2010 Politec - Todos os direitos reservados.
 * @package js
 * @link http://www.politec.com.br
 */

/**
 * variáveis globais
 */
var linha_cache = null; // guarda o id do registro a ser editado
var fundo_linha = "#ffffcc"; // guarda a cor do registro a ser editado

// preloader ajax
var carregando = "<div class='carregando'><img src='public/img/ajax.gif' alt='Aguarde...' /><br />CARREGANDO</div>";



/**
 * Função para converter as aspas simples dos inputs nos formulários de  alteração
 */
function tratar_input(campo)
{
	return campo.replace(/[']+/g, "&#039;");
}



/**
 * Função para pegar o objeto XMLHTTP para uso com AJAX
 */
function xmlhttp()
{
	// XMLHttpRequest para firefox e outros navegadores
	if (window.XMLHttpRequest)
	{
		return new XMLHttpRequest();
	}

	// ActiveXObject para navegadores microsoft
	var versao = ['Microsoft.XMLHttp', 'Msxml2.XMLHttp', 'Msxml2.XMLHttp.6.0', 
	              'Msxml2.XMLHttp.5.0', 'Msxml2.XMLHttp.4.0', 'Msxml2.XMLHttp.3.0'];
	for (var i = 0; i < versao.length; i++)
	{
		try
		{
			return new ActiveXObject(versao[i]);
		}
		catch(e)
		{
			alertar("Seu navegador não possui recursos para o uso do AJAX!");
		}
	} // fecha for
	return null;
} // fecha função xmlhttp



/**
 * Função para pegar os scripts que ficam dentro das páginas aberta via AJAX
 */
function pegar_script(tag_script)
{
	var js_home = 0; // início do script
	var js_end  = 0; // final do script

	// varre os scripts encontrados
	while (js_home != -1)
	{
		js_home = tag_script.indexOf('<script', js_home); // procura a tag
															// script
		if (js_home >= 0)
		{
			js_home = tag_script.indexOf('>', js_home) + 1; // pega o início
			js_end  = tag_script.indexOf('</script>', js_home); // pega o final
			codigo  = tag_script.substring(js_home, js_end); // pega o código

			var novo  = document.createElement('script');
			novo.type = 'text/javascript';
			novo.text = codigo;
			document.body.appendChild(novo);
		} // fecha if
	} // fecha while
} // fecha função pegar_script()



/**
 * Função AJAX para abrir os links das páginas via GET
 */
function abrir_pag(pag, id, carregando)
{
	id == null ? id = document.getElementById("conteudo") : id = document.getElementById(id);
	ajax = xmlhttp(); // instancia ajax
	ajax.open("GET", pag, true);
	ajax.onreadystatechange = function()
	{
		// enquanto estiver processando emite a msg de carregando
		if (ajax.readyState > 0 && ajax.readyState <= 3)
		{
                        if (carregando == null || carregando == undefined)
                        {
                            id.innerHTML = carregando;
                        }
                        else
                        {
                            id.innerHTML = '';
                        }
		}
		if (ajax.readyState == 4 && ajax.status == 200)
		{
			pegar_script(unescape(ajax.responseText.replace(/\+/g," "))); // conversão de scripts
			id.innerHTML = ajax.responseText;
			linha_cache = null; // limpa o id do registro em edição no cache
		}
	}
	ajax.send(null);
} // fecha função abrir_pag()



/**
 * Função AJAX para enviar dados via POST
 */
function enviar_pag(url, dados, id)
{
	id == null ? id = document.getElementById("conteudo") : id = document.getElementById(id);
	ajax = xmlhttp(); // instancia ajax
	ajax.open("POST", url, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.onreadystatechange = function()
	{
		// enquanto estiver processando emite a msg de carregando
		if (ajax.readyState > 0 && ajax.readyState <= 3)
		{
			id.innerHTML = carregando;
		}
		if (ajax.readyState == 4 && ajax.status == 200)
		{
			id.innerHTML = ajax.responseText;
			linha_cache = null; // limpa o id do registro em edição no cache
		}
	}
	ajax.send(dados);
} // fecha função enviar_pag()


/**
 * Função AJAX para enviar dados via POST
 */
function enviar_pag_nao_assincrono(url, dados, id)
{
	id == null ? id = document.getElementById("conteudo") : id = document.getElementById(id);
	ajax = xmlhttp(); // instancia ajax
	ajax.open("POST", url, false); //false - ajax nao assincrono.
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.onreadystatechange = function()
	{
		// enquanto estiver processando emite a msg de carregando
		if (ajax.readyState > 0 && ajax.readyState <= 3)
		{
			id.innerHTML = carregando;
		}
		if (ajax.readyState == 4 && ajax.status == 200)
		{
			id.innerHTML = ajax.responseText;
			linha_cache = null; // limpa o id do registro em edição no cache
		}
	}
	ajax.send(dados);
}

/**
 * Função para buscar os dados de um combo de acordo com o valor de outro
 */
function carregar_combo(valor, combo, url, txt_combo, campo_selecionado)
{
    console.log(valor, combo, url, txt_combo, campo_selecionado);
	ajax = xmlhttp(); // instancia ajax

	// deixa apenas um elemento no combo, os outros são excluídos
	document.getElementById(combo).options.length = 1;

	// abre a página que possue o XML gerado
	ajax.open("POST", url, true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	ajax.onreadystatechange = function()
	{
		if (ajax.readyState <= 3) // carregando
		{
			txt_combo == null ? document.getElementById(combo).options[0].innerHTML = "..." : document.getElementById(combo).options[0].innerHTML = "carregando...";
		}

		// lê os dados do XML
		if (ajax.readyState == 4)
		{
			if (ajax.responseXML)
			{
				buscar_xml_combo(ajax.responseXML, combo, txt_combo, campo_selecionado);
                $3('#' + combo).material_select();
			}
			else
			{
				txt_combo == null ? document.getElementById(combo).options[0].innerHTML = " -- " : document.getElementById(combo).options[0].innerHTML = txt_combo;
			}
		}
	}
	// passa o código do ítem escolhido
	var params = "id=" + valor;
	ajax.send(params);

}

/**
 * Função para lê o XML de um combo gerado a partir de outro
 */
function buscar_xml_combo(xml, combo, txt_combo, campo_selecionado)
{
	// pega o nó do elemento
	var no = xml.getElementsByTagName("item");

	// total de elementos contidos no nó
	if (no.length > 0)
	{
		// percorre o nó para extrair seus filhos
		for (var i = 0; i < no.length; i++)
		{
			var item = no[i];

			// pega o valor dos filhos do nó
			var id        = item.getElementsByTagName("id")[0].firstChild.nodeValue;
			var descricao = item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

			txt_combo == null ? document.getElementById(combo).options[0].innerHTML = " -- " : document.getElementById(combo).options[0].innerHTML = " - Selecione - ";

			// cria um novo option dinamicamente
			var novo   = document.createElement("option");
			novo.value = id; // atribui um valor ao option
			novo.text  = descricao; // atribui um texto ao option

			// selected
			if (id == campo_selecionado)
			{
				novo.selected = true;
			}
			document.getElementById(combo).options.add(novo); // adiciona o novo elemento
		}
	}
	else
	{
		// se o XML retornar vazio
		txt_combo == null ? document.getElementById(combo).options[0].innerHTML = " -- " : document.getElementById(combo).options[0].innerHTML = txt_combo;
	}
} // fecha função buscar_xml_combo()



/**
 * Função AJAX para abrir duas requisições para os combos de alteração
 */
function abrir_pag_combo(pag, id, pag2, id2)
{
	id  = document.getElementById(id);
	id2 = document.getElementById(id2);
	ajax  = xmlhttp(); // instancia ajax
	ajax2 = xmlhttp(); // instancia ajax
	ajax.open("GET", pag, true);
	ajax2.open("GET", pag2, true);

	ajax.onreadystatechange = function()
	{
		// enquanto estiver processando emite a msg de carregando
		if (ajax.readyState > 0 && ajax.readyState <= 3)
		{
			id.innerHTML = carregando;
		}
		if (ajax.readyState == 4 && ajax.status == 200)
		{
			id.innerHTML = ajax.responseText;
		}
	}
	ajax2.onreadystatechange = function()
	{
		// enquanto estiver processando emite a msg de carregando
		if (ajax2.readyState > 0 && ajax2.readyState <= 3)
		{
			id2.innerHTML = carregando;
		}
		if (ajax2.readyState == 4 && ajax2.status == 200)
		{
			id2.innerHTML = ajax2.responseText;
		}
	}
	ajax.send(null);
	ajax2.send(null);
} // fecha função abrir_pag_combo()



/**
 * Formulário Alterar Senha
 */
function alterar_senha()
{
	senhaAntiga   = document.getElementById("senhaAntiga").value;
	novaSenha     = document.getElementById("novaSenha").value;
	confirmaSenha = document.getElementById("confirmaSenha").value;

	if (senhaAntiga == "")
	{
		alertar("Informe a senha antiga!", "senhaAntiga");
	}
	else if (novaSenha == "")
	{
		alertar("Informe a nova senha!", "novaSenha");
	}
	else if (confirmaSenha == "")
	{
		alertar("Informe a confirmação de senha!", "confirmaSenha");
	}
	else if (novaSenha.length < 6 || novaSenha.length > 20)
	{
		alertar("Por questões de segurança, a nova senha deverá possuir de 6 à 20 caracteres!", "novaSenha");
	}
	else if (novaSenha != confirmaSenha)
	{
		alertar("A confirmação de senha não confere!", "confirmaSenha");
	}
	else
	{
		// campos do formulário
		dados = 'senhaAntiga=' + encodeURIComponent(senhaAntiga);
		dados+= '&novaSenha=' + encodeURIComponent(novaSenha);
		dados+= '&confirmaSenha=' + encodeURIComponent(confirmaSenha);
		enviar_pag("php/alterar_senha.php?acao=AlterarSenha", dados);
	} // fecha else
} // fecha função alterar_senha()





/**
 * ==============================
 *  Início DATA GRID
 * ==============================
 */



/**
 * Funções para cadastro, alteração e exclusão de CONSELHEIRO
 */
function cadastrar_proponente() // cadastro
{
	cpf                     = document.getElementById("cpf").value;
	nome                    = document.getElementById("nome").value;
	cep                     = document.getElementById("cep").value;
	uf                      = document.getElementById("uf").value;
	cidade                  = document.getElementById("cidade").value;
	tipoEndereco            = document.getElementById("tipoEndereco").value;
	tipoLogradouro          = document.getElementById("tipoLogradouro").value;
	logradouro              = document.getElementById("logradouro").value;
	numero                  = document.getElementById("numero").value;
	complemento             = document.getElementById("complemento").value;
	bairro                  = document.getElementById("bairro").value;
	titular                 = document.getElementById("titular").value;
	divulgarEndereco        = document.getElementById("divulgarEndereco").value;
	enderecoCorrespondencia = document.getElementById("enderecoCorrespondencia").value;
	areaCultural            = document.getElementById("areaCultural").value;
	segmentoCultural        = document.getElementById("segmentoCultural").value;

	tipoFone                = document.getElementById("tipoFone").value;
	ufFone                  = document.getElementById("ufFone").value;
	dddFone                 = document.getElementById("dddFone").value;
	fone                    = document.getElementById("fone").value;
	divulgarFone            = document.getElementById("divulgarFone").value;

	tipoEmail               = document.getElementById("tipoEmail").value;
	email                   = document.getElementById("email").value;
	divulgarEmail           = document.getElementById("divulgarEmail").value;
	enviarEmail             = document.getElementById("enviarEmail").value;

	if (cpf == "")                           { alertar("Por favor, informe o cpf!", "cpf");	}
	else if (nome == "")                     { alertar("Por favor, informe o nome!", "nome");	}
	else if (cep == "")                      { alertar("Por favor, informe o cep!", "cep");	}
	else if (uf == "")                       { alertar("Por favor, selecione o estado!", "uf");	}
	else if (cidade == "")                   { alertar("Por favor, selecione a cidade!", "cidade");	}
	else if (tipoEndereco == "")             { alertar("Por favor, selecione o tipo de endereço!", "tipoEndereco");	}
	else if (tipoLogradouro == "")           { alertar("Por favor, selecione o tipo de logradouro!", "tipoLogradouro");	}
	else if (logradouro == "")               { alertar("Por favor, informe o logradouro!", "logradouro");	}
	else if (numero == "")                   { alertar("Por favor, informe o número!", "numero");	}
	else if (complemento == "")              { alertar("Por favor, informe o complemento!", "complemento");	}
	else if (bairro == "")                   { alertar("Por favor, informe o bairro!", "bairro");	}
	//else if (titular == "")                  { alertar("Por favor, informe o nome do Proponente!", "titular");	}
	//else if (divulgarEndereco == "")         { alertar("Por favor, informe o nome do Proponente!", "divulgarEndereco");	}
	//else if (enderecoCorrespondencia == "")  { alertar("Por favor, informe o nome do Proponente!", "enderecoCorrespondencia");	}
	else if (areaCultural == "")             { alertar("Por favor, selecione a área cultural!", "areaCultural");	}
	else if (segmentoCultural == "")         { alertar("Por favor, selecione o segmento cultural!", "segmentoCultural");	}

	//else if (tipoFone == "")                 { alertar("Por favor, informe o nome do Proponente!", "tipoFone");	}
	//else if (ufFone == "")                   { alertar("Por favor, informe o nome do Proponente!", "ufFone");	}
	//else if (dddFone == "")                  { alertar("Por favor, informe o nome do Proponente!", "dddFone");	}
	//else if (fone == "")                     { alertar("Por favor, informe o nome do Proponente!", "fone");	}
	//else if (divulgarFone == "")             { alertar("Por favor, informe o nome do Proponente!", "divulgarFone");	}

	//else if (tipoEmail == "")                { alertar("Por favor, informe o nome do Proponente!", "tipoEmail");	}
	//else if (email == "")                    { alertar("Por favor, informe o nome do Proponente!", "email");	}
	//else if (divulgarEmail == "")            { alertar("Por favor, informe o nome do Proponente!", "divulgarEmail");	}
	//else if (enviarEmail == "")              { alertar("Por favor, informe o nome do Proponente!", "enviarEmail");	}
	else
	{
		// campos do formulário
		dados = 'cpf=' + encodeURIComponent(cpf);
		dados+= '&nome=' + encodeURIComponent(nome);
		dados+= '&cep=' + encodeURIComponent(cep);
		dados+= '&uf=' + encodeURIComponent(uf);
		dados+= '&cidade=' + encodeURIComponent(cidade);
		dados+= '&tipoEndereco=' + encodeURIComponent(tipoEndereco);
		dados+= '&tipoLogradouro=' + encodeURIComponent(tipoLogradouro);
		dados+= '&logradouro=' + encodeURIComponent(logradouro);
		dados+= '&numero=' + encodeURIComponent(numero);
		dados+= '&complemento=' + encodeURIComponent(complemento);
		dados+= '&bairro=' + encodeURIComponent(bairro);
		dados+= '&titular=' + encodeURIComponent(titular);
		dados+= '&divulgarEndereco=' + encodeURIComponent(divulgarEndereco);
		dados+= '&enderecoCorrespondencia=' + encodeURIComponent(enderecoCorrespondencia);
		dados+= '&areaCultural=' + encodeURIComponent(areaCultural);
		dados+= '&segmentoCultural=' + encodeURIComponent(segmentoCultural);
		dados+= '&ufFone=' + encodeURIComponent(ufFone);
		dados+= '&divulgarFone=' + encodeURIComponent(divulgarFone);
		dados+= '&tipoEmail=' + encodeURIComponent(tipoEmail);
		dados+= '&email=' + encodeURIComponent(email);
		dados+= '&divulgarEmail=' + encodeURIComponent(divulgarEmail);
		dados+= '&enviarEmail=' + encodeURIComponent(enviarEmail);
		enviar_pag("conselheiro/confirmarinsert", dados, "meu_frame");
	} // fecha else
}

function alterar_proponente(linha) // cria o formulário de aleração
{
	if (!linha_cache)
	{
		linha_cache = linha; // armazena o id no cache
		dados = document.getElementById(linha); // linha a ser editada
		dados.style.backgroundColor = fundo_linha; // cor da linha
		dados = dados.cells; // celulas

		// salva os dados da linha no cache
		c_nome = dados[0].innerHTML;

		dados[0].innerHTML = "<input type='text' name='nomeProponenteAlterar' id='nomeProponenteAlterar' maxlength='50' value='" + tratar_input(dados[0].innerHTML) + "' />";
		dados[0].innerHTML+= "<p><br />";
		dados[0].innerHTML+= "	<input onclick=\"del_alterar_proponente('" + linha + "', '" + escape(c_nome) + "'); return false;\" type='reset' value=' ' title='Cancelar' class='btn_cancelar' />";
		dados[0].innerHTML+= "	<input onclick=\"add_alterar_proponente('" + linha + "', '" + escape(c_nome) + "'); return false;\" type='submit' value=' ' title='Alterar' class='btn_alterar' />";
		dados[0].innerHTML+= "</p>";
	}
	else
	{
		alertar("Termine de editar o ítem \"" + c_nome + "\"!");
	}
}

function del_alterar_proponente(linha, c1) // apaga o formulário de alteração
{
	linha = document.getElementById(linha); // linha alterada
	linha.style.backgroundColor = ""; // cor da linha
	linha.cells[0].innerHTML = unescape(c1); // variável em cache
	linha_cache = null; // limpa o id do registro em edição no cache
}

function add_alterar_proponente(linha, nomeAntigo) // alteração
{
	prm  = document.getElementById("prm").value;
	nome = document.getElementById("nomeProponenteAlterar").value;

	if (nome == "")
	{
		alertar("Por favor, informe o nome do proponente!", "nomeProponenteAlterar");
	}
	else
	{
		// campos do formulário
		dados = 'prm=' + encodeURIComponent(prm);
		dados+= '&cod=' + encodeURIComponent(linha.substr(5));
		dados+= '&nomeAntigo=' + encodeURIComponent(nomeAntigo);
		dados+= '&nome=' + encodeURIComponent(nome);
		enviar_pag("php/proponentes.php?acao=AlterarProponente", dados);
	} // fecha else
}

function excluir_proponente(linha) // exclusão
{
	if (!linha_cache)
	{
		prm = document.getElementById("prm").value;
		document.getElementById(linha).style.backgroundColor = fundo_linha; // cor
																			// da
																			// linha
		confirma = confirmar("Deseja realmente excluir esse proponente?");
		document.getElementById(linha).style.backgroundColor = ""; // cor da
																	// linha
		if (confirma)
		{
			dados = 'prm=' + encodeURIComponent(prm);
			dados+= '&cod=' + encodeURIComponent(linha.substr(5));
			enviar_pag("php/proponentes.php?acao=ExcluirProponente", dados);
		}
		else
		{
			return false;
		}
	}
	else
	{
		alertar("Para continuar é necessário terminar a edição do ítem ativo!");
	}
}





/**
* Funções para cadastro, alteração e exclusão de FONE
*/
function cadastrar_fone() // cadastro
{
	idagente = document.getElementById('idAgente').value;
	tipo     = document.getElementById('tipoFone').value;
	uf       = document.getElementById('ufFone').value;
	ddd      = document.getElementById('dddFone').value;
	fone     = document.getElementById('fone').value;
	divulgarFone = "";
    for (i = 0; i < document.formAlterar.divulgarFone.length; i++)
    {
        if (document.formAlterar.divulgarFone[i].checked)
        {
            divulgarFone = document.formAlterar.divulgarFone[i].value;
        }
    }
	
	divulgar = divulgarFone;

	if (tipo == "")
	{
		alert("Por favor, informe o tipo de telefone!", "tipoFone");
	}
	else if (uf == "")
	{
		alert("Por favor, selecione o estado do telefone!", "ufFone");
	}
	else if (ddd == "")
	{
		alert("Por favor, informe ddd do telefone!", "dddFone");
	}
	else if (fone == "")
	{
		alert("Por favor, informe o telefone!", "fone");
	}
	else
	{
		// campos do formulário
		dados = 'tipo=' + encodeURIComponent(tipo);
		dados+= '&idAgente=' + encodeURIComponent(idagente);
		dados+= '&uf=' + encodeURIComponent(uf);
		dados+= '&ddd=' + encodeURIComponent(ddd);
		dados+= '&fone=' + encodeURIComponent(fone);
		dados+= '&divulgar=' + encodeURIComponent(divulgar);
		redirecionar("conselheiro/inserirtel", dados);
	} // fecha else
}

function alterar_fone(linha) // cria o formulário de aleração
{
	if (!linha_cache)
	{
		linha_cache = linha; // armazena o id no cache
		dados = document.getElementById(linha); // linha a ser editada
		dados.style.backgroundColor = fundo_linha; // cor da linha
		dados = dados.cells; // celulas

		// salva os dados da linha no cache
		c_tipo = dados[0].innerHTML;
		c_uf   = dados[1].innerHTML;
		c_ddd  = dados[2].innerHTML;
		c_fone = dados[3].innerHTML;
		c_divulgar = dados[4].innerHTML;

		dados[0].innerHTML = "<input type='text' name='tipoFoneAlterar' id='tipoFoneAlterar' maxlength='50' value='" + tratar_input(dados[0].innerHTML) + "' />";
		dados[0].innerHTML+= "<p><br />";
		dados[0].innerHTML+= "	<input onclick=\"del_alterar_fone('" + linha + "', '" + escape(c_tipo) + "', '" + escape(c_uf) + "', '" + escape(c_ddd) + "', '" + escape(c_fone) + "', '" + escape(c_divulgar) + "'); return false;\" type='reset' value=' ' title='Cancelar' class='btn_cancelar' />";
		dados[0].innerHTML+= "	<input onclick=\"add_alterar_fone('" + linha + "', '" + escape(c_tipo) + "', '" + escape(c_uf) + "', '" + escape(c_ddd) + "', '" + escape(c_fone) + "', '" + escape(c_divulgar) + "'); return false;\" type='submit' value=' ' title='Alterar' class='btn_alterar' />";
		dados[0].innerHTML+= "</p>";
		dados[1].innerHTML = "<input type='text' name='ufFoneAlterar' id='ufFoneAlterar' maxlength='2' value='" + tratar_input(dados[1].innerHTML) + "' />";
		dados[2].innerHTML = "<input type='text' name='dddFoneAlterar' id='dddFoneAlterar' maxlength='3' value='" + tratar_input(dados[2].innerHTML) + "' />";
		dados[3].innerHTML = "<input type='text' name='foneAlterar' id='foneAlterar' maxlength='9' value='" + tratar_input(dados[3].innerHTML) + "' />";
		dados[4].innerHTML = "<input type='text' name='divulgarFoneAlterar' id='divulgarFoneAlterar' maxlength='2' value='" + tratar_input(dados[4].innerHTML) + "' />";
	}
	else
	{
		alertar("Termine de editar o ítem \"" + c_tipo + "\"!");
	}
}

function del_alterar_fone(linha, c1, c2, c3, c4, c5) // apaga o formulário de
														// alteração
{
	linha = document.getElementById(linha); // linha alterada
	linha.style.backgroundColor = ""; // cor da linha
	linha.cells[0].innerHTML = unescape(c1); // variável em cache
	linha.cells[1].innerHTML = unescape(c2); // variável em cache
	linha.cells[2].innerHTML = unescape(c3); // variável em cache
	linha.cells[3].innerHTML = unescape(c4); // variável em cache
	linha.cells[4].innerHTML = unescape(c5); // variável em cache
	linha_cache = null; // limpa o id do registro em edição no cache
}

function add_alterar_fone(linha, nomeAntigo) // alteração
{
	prm  = document.getElementById("prm").value;
	nome = document.getElementById("nomeProponenteAlterar").value;

	if (nome == "")
	{
		alertar("Por favor, informe o nome do proponente!", "nomeProponenteAlterar");
	}
	else
	{
		// campos do formulário
		dados = 'prm=' + encodeURIComponent(prm);
		dados+= '&cod=' + encodeURIComponent(linha.substr(5));
		dados+= '&nomeAntigo=' + encodeURIComponent(nomeAntigo);
		dados+= '&nome=' + encodeURIComponent(nome);
		enviar_pag("php/proponentes.php?acao=AlterarProponente", dados);
	} // fecha else
}

function excluir_fone(linha) // exclusão
{
	if (!linha_cache)
	{
		document.getElementById(linha).style.backgroundColor = fundo_linha; // cor da linha
		confirma = confirm("Deseja realmente excluir esse telefone?");
		document.getElementById(linha).style.backgroundColor = ""; // cor da linha
		if (confirma)
		{
			dados = 'cod=' + encodeURIComponent(linha.substr(5));
			enviar_pag("conselheiro/excluirtel", dados);
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert("Para continuar é necessário terminar a edição do ítem ativo!");
	}
}

/**
* Funções para cadastro, alteração e exclusão de EMAIL
*/
function cadastrar_email() // cadastro
{
	idAgente  = document.getElementById("idAgente").value;
	tipo      = document.getElementById("tipoEmail").value;
	descricao = document.getElementById("email").value;
	divulgarEmail = "";
	for (i = 0; i < document.formAlterar.divulgarEmail.length; i++)
	{
		if (document.formAlterar.divulgarEmail[i].checked)
		{
			divulgarEmail = document.formAlterar.divulgarEmail[i].value;
		}
	}
	divulgar = divulgarEmail;
	enviarEmail = "";
	for (i = 0; i < document.formAlterar.enviarEmail.length; i++)
	{
		if (document.formAlterar.enviarEmail[i].checked)
		{
			enviarEmail = document.formAlterar.enviarEmail[i].value;
		}
	}
	envEmail = enviarEmail;
	if (tipo == "")
	{
		alertar("Por favor, selecione o tipo de Email!", "tipoEmail");
	}
	else if (descricao == "")
	{
		alertar("Por favor, informe o e-mail!", "descricao");
	}
	else if (divulgar == "")
	{
		alertar("Por favor, informe se o e-mail poderá ser divulgado!", "divulgar");
	}
	else if (envEmail == "")
	{
		alertar("Por favor, se o E-mail é para correspondência!", "envEmail");
	}
	else
	{
		// campos do formulário
		dados = 'idAgente=' 		+ encodeURIComponent(idAgente);
		dados+= '&tipo=' 			+ encodeURIComponent(tipo);
		dados+= '&descricao=' 		+ encodeURIComponent(descricao);
		dados+= '&divulgarEmail=' 	+ encodeURIComponent(divulgar);
		dados+= '&enviarEmail=' 	+ encodeURIComponent(envEmail);
		enviar_pag("conselheiro/inseriremail", dados);
	} // fecha else
}

function alterar_email(linha) // cria o formulário de aleração
{
	if (!linha_cache)
	{
		linha_cache = linha; // armazena o id no cache
		dados = document.getElementById(linha); // linha a ser editada
		dados.style.backgroundColor = fundo_linha; // cor da linha
		dados = dados.cells; // celulas

		// salva os dados da linha no cache
		c_tipo     = dados[0].innerHTML;
		c_email    = dados[1].innerHTML;
		c_divulgar = dados[2].innerHTML;
		c_enviar   = dados[3].innerHTML;

		dados[0].innerHTML = "<input type='text' name='tipoEmailAlterar' id='tipoEmailAlterar' maxlength='50' value='" + tratar_input(dados[0].innerHTML) + "' />";
		dados[0].innerHTML+= "<p><br />";
		dados[0].innerHTML+= "	<input onclick=\"del_alterar_email('" + linha + "', '" + escape(c_tipo) + "', '" + escape(c_email) + "', '" + escape(c_divulgar) + "', '" + escape(c_enviar) + "'); return false;\" type='reset' value=' ' title='Cancelar' class='btn_cancelar' />";
		dados[0].innerHTML+= "	<input onclick=\"add_alterar_email('" + linha + "', '" + escape(c_tipo) + "', '" + escape(c_email) + "', '" + escape(c_divulgar) + "', '" + escape(c_enviar) + "'); return false;\" type='submit' value=' ' title='Alterar' class='btn_alterar' />";
		dados[0].innerHTML+= "</p>";
		dados[1].innerHTML = "<input type='text' name='emailAlterar' id='emailAlterar' maxlength='50' value='" + tratar_input(dados[1].innerHTML) + "' />";
		dados[2].innerHTML = "<input type='text' name='divulgarEmailAlterar' id='divulgarEmailAlterar' maxlength='3' value='" + tratar_input(dados[2].innerHTML) + "' />";
		dados[3].innerHTML = "<input type='text' name='enviarEmailAlterar' id='enviarEmailAlterar' maxlength='3' value='" + tratar_input(dados[3].innerHTML) + "' />";
	}
	else
	{
		alertar("Termine de editar o ítem \"" + c_email + "\"!");
	}
}

function del_alterar_email(linha, c1, c2, c3, c4) // apaga o formulário de
													// alteração
{
	linha = document.getElementById(linha); // linha alterada
	linha.style.backgroundColor = ""; // cor da linha
	linha.cells[0].innerHTML = unescape(c1); // variável em cache
	linha.cells[1].innerHTML = unescape(c2); // variável em cache
	linha.cells[2].innerHTML = unescape(c3); // variável em cache
	linha.cells[3].innerHTML = unescape(c4); // variável em cache
	linha_cache = null; // limpa o id do registro em edição no cache
}

function add_alterar_email(linha, nomeAntigo) // alteração
{
	prm  = document.getElementById("prm").value;
	nome = document.getElementById("nomeProponenteAlterar").value;

	if (nome == "")
	{
		alert("Por favor, informe o nome do proponente!", "nomeProponenteAlterar");
	}
	else
	{
		// campos do formulário
		dados = 'prm=' + encodeURIComponent(prm);
		dados+= '&cod=' + encodeURIComponent(linha.substr(5));
		dados+= '&nomeAntigo=' + encodeURIComponent(nomeAntigo);
		dados+= '&nome=' + encodeURIComponent(nome);
		enviar_pag("php/proponentes.php?acao=AlterarProponente", dados);
	} // fecha else
}

function excluir_email(linha) // exclusão
{
	if (!linha_cache)
	{	
		//document.getElementById(linha).style.backgroundColor = fundo_linha; // cor da linha
		confirma = confirm("Deseja realmente excluir esse e-mail?");
		//document.getElementById(linha).style.backgroundColor = ""; // cor da linha
		
		if (confirma)
		{
			dados = 'cod=' + encodeURIComponent(linha.substr(5));
			enviar_pag("conselheiro/excluiremail", dados);
		}
		else
		{
			return false;
		}
	}
	else
	{
		alert("Para continuar é necessário terminar a edição do ítem ativo!");
	}
}





/**
 * ==============================
 *  Fim DATA GRID
 * ==============================
 */



/**
 * Funções do botão voltar
 */

// Definição de variáveis
var v_cache = new Array;
var v_cont = 0;
var v_indiceAtual = "";
var v_iframeCarregado = false;
var v_divSaida = "";

// Lê o índice armazenado no IFRAME. Se ele for diferente do índice atual
// da cache, significa que o botão Voltar foi pressionado. Nesse caso,
// obtém o conteúdo da cache e atualiza a página
function v_checaEstado()
{
	if (v_iframeCarregado == false)
		return;
	var doc =  window.frames["frame_voltar"].document;
	var novoIndice = doc.getElementById("divContagem").innerHTML;

	if (novoIndice != v_indiceAtual)
	{
		// Carrega o estado anterior da cache (se houver)
		if (v_cache[novoIndice])
		{
			var conteudo = document.getElementById("meu_frame");
 			conteudo.innerHTML = v_cache[novoIndice];
 		}
 		v_indiceAtual = novoIndice;
 	}
}

// Função chamada após o carregamento do IFRAME
function v_carregado()
{
	v_iframeCarregado = true;
}

// Atualiza o IFRAME invisível
function v_carregaFrame()
{
	var frame_voltar = document.getElementById("frame_voltar");
	v_iframeCarregado = false;
	frame_voltar.src = "index/voltar/?voltar=" + v_cont;
}

// Salva o estado corrente na cache
function v_salvaEstado()
{
	var conteudoCache = document.getElementById(v_divSaida);
	v_cont++;
	v_cache[v_cont] = conteudoCache.innerHTML;
	v_carregaFrame();
	v_indiceAtual = v_cont;
}

// Carrega o IFRAME invisível e inicia o timer
function v_inicia(id)
{
	v_divSaida = id;
	v_carregaFrame();
	window.setInterval('v_checaEstado()', 1000);
	v_salvaEstado();
}










/**
* Combo para buscar as cidades de acordo com um estado escolhido
*/
function carregar_cidades(valor)
{
	ajax = xmlhttp(); // instancia ajax

	// deixa apenas o elemento 1 no option, os outros são excluídos
	document.forms[0].cidade.options.length = 1;

	idOpcao = document.getElementById("opcoesCidade");

	ajax.open("POST", "../cidade/cidade", true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	ajax.onreadystatechange = function()
	{
		if (ajax.readyState == 1)
		{
			idOpcao.innerHTML = "carregando...";
		}

		// lê os dados
		if (ajax.readyState == 4)
		{
			if (ajax.responseXML)
			{
				buscar_xml_cidades(ajax.responseXML);
			}
			else
			{
				// caso não seja um arquivo XML emite a mensagem abaixo
				idOpcao.innerHTML = " - Selecione primeiro o UF - ";
			}
		}
	}
	// passa o código do estado escolhido
	var params = "id=" + valor;
	ajax.send(params);
}

/**
* Lê o XML com as cidades
*/
function buscar_xml_cidades(obj)
{
	// pega a tag cidade
	var dataArray = obj.getElementsByTagName("cidade");

	// total de elementos contidos na tag cidade
	if (dataArray.length > 0)
	{
		// percorre o arquivo XML paara extrair os dados
		for (var i = 0 ; i < dataArray.length ; i++)
		{
			var item = dataArray[i];

			// contéudo dos campos no arquivo XML
			var codigo    = item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
			var descricao = item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

			idOpcao.innerHTML = " - Selecione - ";

			// cria um novo option dinamicamente
			var novo = document.createElement("option");

			novo.setAttribute("id", "opcoesCidade"); // atribui um ID a esse
														// elemento
			novo.value = codigo; // atribui um valor
			novo.text  = descricao; // atribui um texto
			document.forms[0].cidade.options.add(novo); // adiciona o novo
														// elemento
		}
	}
	else
	{
		// se o xml retorna vazio
		idOpcao.innerHTML = " - Selecione primeiro o UF - ";
	}
}



/**
* Combo para buscar os ddd de acordo com um estado escolhido
*/
function carregar_ddd(valor)
{
	ajax = xmlhttp(); // instancia ajax

	// deixa apenas o elemento 1 no option, os outros são excluídos
	document.forms[0].dddFone.options.length = 1;

	idOpcao = document.getElementById("opcoesDDD");

	ajax.open("POST", "../ddd/ddd", true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	ajax.onreadystatechange = function()
	{
		if (ajax.readyState == 1)
		{
			idOpcao.innerHTML = "...";
		}

		// lê os dados
		if (ajax.readyState == 4)
		{
			if (ajax.responseXML)
			{
				buscar_xml_ddd(ajax.responseXML);
			}
			else
			{
				// caso não seja um arquivo XML emite a mensagem abaixo
				idOpcao.innerHTML = " -- ";
			}
		}
	}
	// passa o código do estado escolhido
	var params = "id=" + valor;
	ajax.send(params);
}

/**
* Lê o XML com os ddd
*/
function buscar_xml_ddd(obj)
{
	// pega a tag ddd
	var dataArray = obj.getElementsByTagName("ddd");

	// total de elementos contidos na tag cidade
	if (dataArray.length > 0)
	{
		// percorre o arquivo XML paara extrair os dados
		for (var i = 0 ; i < dataArray.length ; i++)
		{
			var item = dataArray[i];

			// contéudo dos campos no arquivo XML
			var codigo    = item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
			var descricao = item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

			idOpcao.innerHTML = " -- ";

			// cria um novo option dinamicamente
			var novo = document.createElement("option");

			novo.setAttribute("id", "opcoesDDD"); // atribui um ID a esse
													// elemento
			novo.value = codigo; // atribui um valor
			novo.text  = descricao; // atribui um texto
			document.forms[0].dddFone.options.add(novo); // adiciona o novo
															// elemento
		}
	}
	else
	{
		// se o xml retorna vazio
		idOpcao.innerHTML = " -- ";
	}
}



/**
* Combo para buscar os segmentos culturais de acordo com a área cultural escolhida
*/
function carregar_segmentocultural(valor)
{
	ajax = xmlhttp(); // instancia ajax

	// deixa apenas o elemento 1 no option, os outros são excluídos
	document.forms[0].segmentoCultural.options.length = 1;

	idOpcao = document.getElementById("opcoesSegmentocultural");

	ajax.open("POST", "../segmentocultural/segmentocultural", true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	ajax.onreadystatechange = function()
	{
		if (ajax.readyState == 1)
		{
			idOpcao.innerHTML = "carregando...";
		}

		// lê os dados
		if (ajax.readyState == 4)
		{
			if (ajax.responseXML)
			{
				buscar_xml_segmentocultural(ajax.responseXML);
			}
			else
			{
				// caso não seja um arquivo XML emite a mensagem abaixo
				idOpcao.innerHTML = " - Selecione uma área cultural - ";
			}
		}
	}
	// passa o código do estado escolhido
	var params = "id=" + valor;
	ajax.send(params);
}

/**
* Lê o XML com os segmentos culturais
*/
function buscar_xml_segmentocultural(obj)
{
	// pega a tag segmentocultural
	var dataArray = obj.getElementsByTagName("segmentocultural");

	// total de elementos contidos na tag segmentocultural
	if (dataArray.length > 0)
	{
		// percorre o arquivo XML paara extrair os dados
		for (var i = 0 ; i < dataArray.length ; i++)
		{
			var item = dataArray[i];

			// contéudo dos campos no arquivo XML
			var codigo    = item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
			var descricao = item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

			idOpcao.innerHTML = " - Selecione - ";

			// cria um novo option dinamicamente
			var novo = document.createElement("option");

			novo.setAttribute("id", "opcoesSegmentocultural"); // atribui um ID
																// a esse
																// elemento
			novo.value = codigo; // atribui um valor
			novo.text  = descricao; // atribui um texto
			document.forms[0].segmentoCultural.options.add(novo); // adiciona
																	// o novo
																	// elemento
		}
	}
	else
	{
		// se o xml retorna vazio
		idOpcao.innerHTML = " - Selecione uma área cultural - ";
	}
}



/**
 * Funções para mostrar as abas
 */
function mostrar_menu_dados()
{
	$('#menuDados').css({'background-color':'#A8CA65', 'color':'#fff'});
	$('#menuEmail, #menuFone').css({'background-color':'', 'color':''});
	$('#menuDados').css({'background-image':'url(public/img/dolphin_bg.gif)'});
	$('#menuFone, #menuEmail').css({'background-image':''});

	$('#tabFone, #tabBuscarFone').hide();
	$('#tabEmail, #tabBuscarEmail').hide();
	$('#tabDados').show();
}

function mostrar_menu_fone()
{
	$('#menuFone').css({'background-color':'#A8CA65', 'color':'#fff'});
	$('#menuEmail, #menuDados').css({'background-color':'', 'color':''});
	$('#menuFone').css({'background-image':'url(public/img/dolphin_bg.gif)'});
	$('#menuDados, #menuEmail').css({'background-image':''});

	$('#tabDados').hide();
	$('#tabEmail, #tabBuscarEmail').hide();
	$('#tabFone, #tabBuscarFone').show();
}

function mostrar_menu_email()
{
	$('#menuEmail').css({'background-color':'#A8CA65', 'color':'#fff'});
	$('#menuDados, #menuFone').css({'background-color':'', 'color':''});
	$('#menuEmail').css({'background-image':'url(public/img/dolphin_bg.gif)'});
	$('#menuDados, #menuFone').css({'background-image':''});

	$('#tabDados').hide();
	$('#tabFone, #tabBuscarFone').hide();
	$('#tabEmail, #tabBuscarEmail').show();
}


/**
 * validacao de cpf
 */
function validarCPF(cpf)
{
	var cpf = cpf;
	cpf = cpf.toString().replace(/\.|\-/g, "");

	var digitoDigitado = eval(cpf.charAt(9) + cpf.charAt(10));
	var soma1 = 0, soma2 = 0;
	var vlr = 11;

	for(i = 0; i < 9; i++)
	{
		soma1 += eval(cpf.charAt(i)*(vlr-1));
		soma2 += eval(cpf.charAt(i)*vlr);
		vlr--;
	}
	soma1 = (((soma1*10)%11)==10 ? 0:((soma1*10)%11));
	soma2 = (((soma2+(2*soma1))*10)%11);

	var digitoGerado = (soma1 * 10) + soma2;
	if (digitoGerado != digitoDigitado)
	{
		mostrar_menu_dados(); alertar("CPF Inválido!", "cpf");
	}
}



/**
* confirmação de insert
*/

function confirmouinsert()
{
	// DADOS
	
	cpf                = document.getElementById("cpf").value;
	nome               = document.getElementById("nome").value;
	cep                = document.getElementById("cep").value;
	uf                 = document.getElementById("uf").value;
	cidade             = document.getElementById("cidade").value;
	tipoEndereco       = document.getElementById("tipoEndereco").value;
	tipoLogradouro     = document.getElementById("tipoLogradouro").value;
	logradouro         = document.getElementById("logradouro").value;
	numero             = document.getElementById("numero").value;
	complemento        = document.getElementById("complemento").value;
	bairro             = document.getElementById("bairro").value;
	titular             = document.getElementById("titular").value;
	divulgarEndereco             = document.getElementById("divulgarEndereco").value;
	enderecoCorrespondencia             = document.getElementById("enderecoCorrespondencia").value;

	areaCultural     = document.getElementById("areaCultural").value;

	segmentoCultural = document.getElementById("segmentoCultural").value;

	// FONE
	tipoFone     = document.getElementById("tipoFone").value;
	ufFone       = document.getElementById("ufFone").value;
	dddFone       = document.getElementById("dddFone").value;
	fone         = document.getElementById("fone").value;
	divulgarFone         = document.getElementById("divulgarFone").value;

	// E-MAIL
	tipoEmail   = document.getElementById("tipoEmail").value;
	email       = document.getElementById("email").value;
	divulgarEmail       = document.getElementById("divulgarEmail").value;
	enviarEmail       = document.getElementById("enviarEmail").value;

	dados = 'cpf=' + encodeURIComponent(cpf);
	dados+= '&nome=' + encodeURIComponent(nome);
	dados+= '&cep=' + encodeURIComponent(cep);
	dados+= '&uf=' + encodeURIComponent(uf);
	dados+= '&cidade=' + encodeURIComponent(cidade);
	dados+= '&tipoEndereco=' + encodeURIComponent(tipoEndereco);
	dados+= '&tipoLogradouro=' + encodeURIComponent(tipoLogradouro);
	dados+= '&logradouro=' + encodeURIComponent(logradouro);
	dados+= '&numero=' + encodeURIComponent(numero);
	dados+= '&complemento=' + encodeURIComponent(complemento);
	dados+= '&bairro=' + encodeURIComponent(bairro);
	dados+= '&titular=' + encodeURIComponent(titular);
	dados+= '&divulgarEndereco=' + encodeURIComponent(divulgarEndereco);
	dados+= '&enderecoCorrespondencia=' + encodeURIComponent(enderecoCorrespondencia);
	dados+= '&areaCultural=' + encodeURIComponent(areaCultural);
	dados+= '&segmentoCultural=' + encodeURIComponent(segmentoCultural);
	dados+= '&tipoFone=' + encodeURIComponent(tipoFone);
	dados+= '&ufFone=' + encodeURIComponent(ufFone);
	dados+= '&dddFone=' + encodeURIComponent(dddFone);
	dados+= '&fone=' + encodeURIComponent(fone);
	dados+= '&divulgarFone=' + encodeURIComponent(divulgarFone);
	dados+= '&tipoEmail=' + encodeURIComponent(tipoEmail);
	dados+= '&email=' + encodeURIComponent(email);
	dados+= '&divulgarEmail=' + encodeURIComponent(divulgarEmail);
	dados+= '&enviarEmail=' + encodeURIComponent(enviarEmail);

	enviar_pag("conselheiro/confirmouinsert", dados);
}

function confirmarinsert()
{
	// DADOS
	
	cpf                = document.getElementById("cpf").value;
	nome               = document.getElementById("nome").value;
	cep                = document.getElementById("cep").value;
	uf                 = document.getElementById("uf").value;
	ufNome             = document.getElementById("uf");
	ufNome             = ufNome.options[ufNome.selectedIndex].text;
	cidade             = document.getElementById("cidade").value;
	cidadeNome         = document.getElementById("cidade");
	cidadeNome         = cidadeNome.options[cidadeNome.selectedIndex].text;
	tipoEndereco       = document.getElementById("tipoEndereco").value;
	tipoEnderecoNome   = document.getElementById("tipoEndereco");
	tipoEnderecoNome   = tipoEnderecoNome.options[tipoEnderecoNome.selectedIndex].text;
	tipoLogradouro     = document.getElementById("tipoLogradouro").value;
	tipoLogradouroNome = document.getElementById("tipoLogradouro");
	tipoLogradouroNome = tipoLogradouroNome.options[tipoLogradouroNome.selectedIndex].text;
	logradouro         = document.getElementById("logradouro").value;
	numero             = document.getElementById("numero").value;
	complemento        = document.getElementById("complemento").value;
	bairro             = document.getElementById("bairro").value;
	titular = "";
	for (i = 0; i < document.formCadastrar.titular.length; i++)
	{
		if (document.formCadastrar.titular[i].checked)
		{
			titular = document.formCadastrar.titular[i].value;
		}
	}
	divulgarEndereco = "";
	for (i = 0; i < document.formCadastrar.divulgarEndereco.length; i++)
	{
		if (document.formCadastrar.divulgarEndereco[i].checked)
		{
			divulgarEndereco = document.formCadastrar.divulgarEndereco[i].value;
		}
	}
	enderecoCorrespondencia = "";
	for (i = 0; i < document.formCadastrar.enderecoCorrespondencia.length; i++)
	{
		if (document.formCadastrar.enderecoCorrespondencia[i].checked)
		{
			enderecoCorrespondencia = document.formCadastrar.enderecoCorrespondencia[i].value;
		}
	}
	areaCultural     = document.getElementById("areaCultural").value;
	areaCulturalNome           = document.getElementById("areaCultural");
	areaCulturalNome           = areaCulturalNome.options[areaCulturalNome.selectedIndex].text;

	segmentoCultural = document.getElementById("segmentoCultural").value;
	segmentoCulturalNome           = document.getElementById("segmentoCultural");
	segmentoCulturalNome           = segmentoCulturalNome.options[segmentoCulturalNome.selectedIndex].text;

	// FONE
	tipoFone     = document.getElementById("tipoFone").value;
	tipoFoneNome = document.getElementById("tipoFone");
	tipoFoneNome = tipoFoneNome.options[tipoFoneNome.selectedIndex].text;

	ufFone       = document.getElementById("ufFone").value;
	ufFoneNome   = document.getElementById("ufFone");
	ufFoneNome   = ufFoneNome.options[ufFoneNome.selectedIndex].text;

	dddFone       = document.getElementById("dddFone").value;
	dddFoneNome   = document.getElementById("dddFone");
	dddFoneNome   = dddFoneNome.options[dddFoneNome.selectedIndex].text;

	fone         = document.getElementById("fone").value;
	divulgarFone = "";
	for (i = 0; i < document.formCadastrar.divulgarFone.length; i++)
	{
		if (document.formCadastrar.divulgarFone[i].checked)
		{
			divulgarFone = document.formCadastrar.divulgarFone[i].value;
		}
	}

	// E-MAIL
	tipoEmail   = document.getElementById("tipoEmail").value;
	tipoEmailNome   = document.getElementById("tipoEmail");
	tipoEmailNome   = tipoEmailNome.options[tipoEmailNome.selectedIndex].text;

	email       = document.getElementById("email").value;
	divulgarEmail = "";
	for (i = 0; i < document.formCadastrar.divulgarEmail.length; i++)
	{
		if (document.formCadastrar.divulgarEmail[i].checked)
		{
			divulgarEmail = document.formCadastrar.divulgarEmail[i].value;
		}
	}
	enviarEmail = "";
	for (i = 0; i < document.formCadastrar.enviarEmail.length; i++)
	{
		if (document.formCadastrar.enviarEmail[i].checked)
		{
			enviarEmail = document.formCadastrar.enviarEmail[i].value;
		}
	}


	// DADOS
	if (cpf == "")                   { mostrar_menu_dados(); alertar("Por favor, informe o número do CPF!", "cpf"); }
	else if (nome == "")             { mostrar_menu_dados(); alertar("Por favor, informe o nome!", "nome"); }
	else if (nome.length <= 3)       { mostrar_menu_dados(); alertar("Por favor, informe um nome válido!", "nome"); }
	else if (cep == "")              { mostrar_menu_dados(); alertar("Por favor, informe o CEP!", "cep"); }
	else if (cep != "" && cep.length < 10)
	{
		mostrar_menu_dados();
		alertar("CEP inválido!", "cep");
	}
	else if (uf == 0)                { mostrar_menu_dados(); alertar("Por favor, selecione o estado!", "uf"); }
	else if (cidade == 0)            { mostrar_menu_dados(); alertar("Por favor, selecione uma cidade!", "cidade"); }
	else if (tipoEndereco == 0)      { mostrar_menu_dados(); alertar("Por favor, selecione um tipo de endereço!", "tipoEndereco"); }
	else if (tipoLogradouro == 0)    { mostrar_menu_dados(); alertar("Por favor, selecione um tipo de logradouro!", "tipoLogradouro"); }
	else if (logradouro == "")       { mostrar_menu_dados(); alertar("Por favor, informe o logradouro!", "logradouro"); }
	else if (numero == "")           { mostrar_menu_dados(); alertar("Por favor, informe o número!", "numero"); }
	else if (complemento == "")      { mostrar_menu_dados(); alertar("Por favor, informe o complemento!", "complemento"); }
	else if (bairro == "")           { mostrar_menu_dados(); alertar("Por favor, informe o bairro!", "bairro"); }
	else if (areaCultural == 0)      { mostrar_menu_dados(); alertar("Por favor, selecione uma área cultural!", "areaCultural"); }
	else if (segmentoCultural == 0)  { mostrar_menu_dados(); alertar("Por favor, selecione um segmento cultural!", "segmentoCultural"); }

	// FONE
	else if (tipoFone == 0)          { mostrar_menu_fone(); alertar("Por favor, informe o tipo de telefone!", "tipoFone"); }
	else if (ufFone == 0)            { mostrar_menu_fone(); alertar("Por favor, selecione o estado do telefone!", "ufFone"); }
	else if (dddFone == 0)           { mostrar_menu_fone(); alertar("Por favor, informe ddd do telefone!", "dddFone"); }
	else if (fone == "")             { mostrar_menu_fone(); alertar("Por favor, informe o telefone!", "fone"); }
	else if (fone != "" && fone.length < 9)
	{
		mostrar_menu_fone(); alertar("Por favor, informe um telefone válido!", "fone");
	}

	// E-MAIL
	else if (tipoEmail == 0)         { mostrar_menu_email(); alertar("Por favor, informe o tipo de email!", "tipoEmail"); }
	else if (email == "")            { mostrar_menu_email(); alertar("Por favor, informe o e-mail!", "email"); }
	else if ((email.indexOf("@") < 1) || (email.lastIndexOf(".") <= email.indexOf("@")) || (email.indexOf("@") == email.length))
	{
		mostrar_menu_email();
		alertar("Email inválido!", "email");
	}

	else
	{
		
		dados = 'cpf=' + encodeURIComponent(cpf);
		dados+= '&nome=' + encodeURIComponent(nome);
		dados+= '&cep=' + encodeURIComponent(cep);
		dados+= '&uf=' + encodeURIComponent(uf);
		dados+= '&ufNome=' + encodeURIComponent(ufNome);
		dados+= '&cidade=' + encodeURIComponent(cidade);
		dados+= '&cidadeNome=' + encodeURIComponent(cidadeNome);
		dados+= '&tipoEndereco=' + encodeURIComponent(tipoEndereco);
		dados+= '&tipoEnderecoNome=' + encodeURIComponent(tipoEnderecoNome);
		dados+= '&tipoLogradouro=' + encodeURIComponent(tipoLogradouro);
		dados+= '&tipoLogradouroNome=' + encodeURIComponent(tipoLogradouroNome);
		dados+= '&logradouro=' + encodeURIComponent(logradouro);
		dados+= '&numero=' + encodeURIComponent(numero);
		dados+= '&complemento=' + encodeURIComponent(complemento);
		dados+= '&bairro=' + encodeURIComponent(bairro);
		dados+= '&titular=' + encodeURIComponent(titular);
		dados+= '&divulgarEndereco=' + encodeURIComponent(divulgarEndereco);
		dados+= '&enderecoCorrespondencia=' + encodeURIComponent(enderecoCorrespondencia);
		dados+= '&areaCultural=' + encodeURIComponent(areaCultural);
		dados+= '&areaCulturalNome=' + encodeURIComponent(areaCulturalNome);
		dados+= '&segmentoCultural=' + encodeURIComponent(segmentoCultural);
		dados+= '&segmentoCulturalNome=' + encodeURIComponent(segmentoCulturalNome);
		dados+= '&tipoFone=' + encodeURIComponent(tipoFone);
		dados+= '&tipoFoneNome=' + encodeURIComponent(tipoFoneNome);
		dados+= '&ufFone=' + encodeURIComponent(ufFone);
		dados+= '&ufFoneNome=' + encodeURIComponent(ufFoneNome);
		dados+= '&dddFone=' + encodeURIComponent(dddFone);
		dados+= '&dddFoneNome=' + encodeURIComponent(dddFoneNome);
		dados+= '&fone=' + encodeURIComponent(fone);
		dados+= '&divulgarFone=' + encodeURIComponent(divulgarFone);
		dados+= '&tipoEmail=' + encodeURIComponent(tipoEmail);
		dados+= '&tipoEmailNome=' + encodeURIComponent(tipoEmailNome);
		dados+= '&email=' + encodeURIComponent(email);
		dados+= '&divulgarEmail=' + encodeURIComponent(divulgarEmail);
		dados+= '&enviarEmail=' + encodeURIComponent(enviarEmail);

		enviar_pag("conselheiro/confirmarinsert", dados);
	}
}


/**
* cancela confirmação de insert
*/
function cancelar_confirmarinsert()
{
	// DADOS
	cpf                     = document.getElementById("cpf").value;
	nome                    = document.getElementById("nome").value;
	cep                     = document.getElementById("cep").value;
	uf                      = document.getElementById("uf").value;
	ufNome                  = document.getElementById("ufNome").value;
	cidade                  = document.getElementById("cidade").value;
	cidadeNome              = document.getElementById("cidadeNome").value;
	tipoEndereco            = document.getElementById("tipoEndereco").value;
	tipoEnderecoNome        = document.getElementById("tipoEnderecoNome").value;
	tipoLogradouro          = document.getElementById("tipoLogradouro").value;
	tipoLogradouroNome      = document.getElementById("tipoLogradouroNome").value;
	logradouro              = document.getElementById("logradouro").value;
	numero                  = document.getElementById("numero").value;
	complemento             = document.getElementById("complemento").value;
	bairro                  = document.getElementById("bairro").value;
	titular                 = document.getElementById("titular").value;
	divulgarEndereco        = document.getElementById("divulgarEndereco").value;
	enderecoCorrespondencia = document.getElementById("enderecoCorrespondencia").value;
	areaCultural            = document.getElementById("areaCultural").value;
	areaCulturalNome        = document.getElementById("areaCulturalNome").value;
	segmentoCultural        = document.getElementById("segmentoCultural").value;
	segmentoCulturalNome    = document.getElementById("segmentoCulturalNome").value;

	// FONE
	tipoFone     = document.getElementById("tipoFone").value;
	tipoFoneNome = document.getElementById("tipoFoneNome").value;
	ufFone       = document.getElementById("ufFone").value;
	ufFoneNome   = document.getElementById("ufFoneNome").value;
	dddFone       = document.getElementById("dddFone").value;
	dddFoneNome   = document.getElementById("dddFoneNome").value;
	divulgarFone  = document.getElementById("divulgarFone").value;

	// E-MAIL
	tipoEmail     = document.getElementById("tipoEmail").value;
	tipoEmailNome = document.getElementById("tipoEmailNome").value;
	email         = document.getElementById("email").value;
	divulgarEmail = document.getElementById("divulgarEmail").value;
	enviarEmail   = document.getElementById("enviarEmail").value;

	
	dados = 'cpf=' + encodeURIComponent(cpf);
	dados+= '&nome=' + encodeURIComponent(nome);
	dados+= '&cep=' + encodeURIComponent(cep);
	dados+= '&uf=' + encodeURIComponent(uf);
	dados+= '&ufNome=' + encodeURIComponent(ufNome);
	dados+= '&cidade=' + encodeURIComponent(cidade);
	dados+= '&cidadeNome=' + encodeURIComponent(cidadeNome);
	dados+= '&tipoEndereco=' + encodeURIComponent(tipoEndereco);
	dados+= '&tipoEnderecoNome=' + encodeURIComponent(tipoEnderecoNome);
	dados+= '&tipoLogradouro=' + encodeURIComponent(tipoLogradouro);
	dados+= '&tipoLogradouroNome=' + encodeURIComponent(tipoLogradouroNome);
	dados+= '&logradouro=' + encodeURIComponent(logradouro);
	dados+= '&numero=' + encodeURIComponent(numero);
	dados+= '&complemento=' + encodeURIComponent(complemento);
	dados+= '&bairro=' + encodeURIComponent(bairro);
	dados+= '&titular=' + encodeURIComponent(titular);
	dados+= '&divulgarEndereco=' + encodeURIComponent(divulgarEndereco);
	dados+= '&enderecoCorrespondencia=' + encodeURIComponent(enderecoCorrespondencia);
	dados+= '&areaCultural=' + encodeURIComponent(areaCultural);
	dados+= '&areaCulturalNome=' + encodeURIComponent(areaCulturalNome);
	dados+= '&segmentoCultural=' + encodeURIComponent(segmentoCultural);
	dados+= '&segmentoCulturalNome=' + encodeURIComponent(segmentoCulturalNome);
	dados+= '&tipoFone=' + encodeURIComponent(tipoFone);
	dados+= '&tipoFoneNome=' + encodeURIComponent(tipoFoneNome);
	dados+= '&ufFone=' + encodeURIComponent(ufFone);
	dados+= '&ufFoneNome=' + encodeURIComponent(ufFoneNome);
	dados+= '&dddFone=' + encodeURIComponent(dddFone);
	dados+= '&dddFoneNome=' + encodeURIComponent(dddFoneNome);
	dados+= '&fone=' + encodeURIComponent(fone);
	dados+= '&divulgarFone=' + encodeURIComponent(divulgarFone);
	dados+= '&tipoEmail=' + encodeURIComponent(tipoEmail);
	dados+= '&tipoEmailNome=' + encodeURIComponent(tipoEmailNome);
	dados+= '&email=' + encodeURIComponent(email);
	dados+= '&divulgarEmail=' + encodeURIComponent(divulgarEmail);
	dados+= '&enviarEmail=' + encodeURIComponent(enviarEmail);

	enviar_pag("conselheiro/cadastrar", dados, "meu_frame");

}




/**
* confirmação de update
*/

function confirmarupdate() // alteração
{
	idAgente 				= document.getElementById("idAgente").value;
	sigla 					= document.getElementById("uf");
	sigla                   = sigla.options[sigla.selectedIndex].text;
	
	nomeCidade 				= document.getElementById("cidade");
	nomeCidade				= nomeCidade.options[nomeCidade.selectedIndex].text;
	
	nomeTipoLogradouro 		= document.getElementById("tipoLogradouro");
	nomeTipoLogradouro		= nomeTipoLogradouro.options[nomeTipoLogradouro.selectedIndex].text;
	
	nomeTipoEndereco 		= document.getElementById("tipoEndereco");
	nomeTipoEndereco		= nomeTipoEndereco.options[nomeTipoEndereco.selectedIndex].text;
	
	nomeAreaCultural 		= document.getElementById("areaCultural");
	nomeAreaCultural		= nomeAreaCultural.options[nomeAreaCultural.selectedIndex].text;
	
	nomeSegmentoCultural 	= document.getElementById("segmentoCultural");
	nomeSegmentoCultural	= nomeSegmentoCultural.options[nomeSegmentoCultural.selectedIndex].text;
	
	cpf 					= document.getElementById("cpf").value;
	nome 					= document.getElementById("nome").value;
	cep 					= document.getElementById("cep").value;
	uf 						= document.getElementById("uf").value;
	cidade 					= document.getElementById("cidade").value;
	tipoEndereco 			= document.getElementById("tipoEndereco").value;
	tipoLogradouro 			= document.getElementById("tipoLogradouro").value;
	logradouro 				= document.getElementById("logradouro").value;
	numero 					= document.getElementById("numero").value;
	complemento 			= document.getElementById("complemento").value;
	bairro 					= document.getElementById("bairro").value;
	
	titular = "";
    for (i = 0; i < document.formAlterar.titular.length; i++)
    {
        if (document.formAlterar.titular[i].checked)
        {
            titular = document.formAlterar.titular[i].value;
            
        }
    }
	
	
	//divulgarEndereco        = document.getElementById("divulgarEndereco").value;
	
	divulgarEndereco = "";
    for (i = 0; i < document.formAlterar.divulgarEndereco.length; i++)
    {
        if (document.formAlterar.divulgarEndereco[i].checked)
        {
        	divulgarEndereco = document.formAlterar.divulgarEndereco[i].value;
        }
    }
    
	//enderecoCorrespondencia = document.getElementById("enderecoCorrespondencia").value;
    
    enderecoCorrespondencia = "";
    for (i = 0; i < document.formAlterar.enderecoCorrespondencia.length; i++)
    {
        if (document.formAlterar.enderecoCorrespondencia[i].checked)
        {
        	enderecoCorrespondencia = document.formAlterar.enderecoCorrespondencia[i].value;
        }
    }
    
    
    areaCultural            = document.getElementById("areaCultural").value;
	segmentoCultural        = document.getElementById("segmentoCultural").value;
	dsCidade       			= document.getElementById("cidade").value;	
	dsTipoEndereco          = document.getElementById("tipoEndereco").value;
	dsTipoLogradouro        = document.getElementById("tipoLogradouro").value;
	dsArea        			= document.getElementById("areaCultural").value;
	dsSegmento        		= document.getElementById("segmentoCultural").value;

	if (cpf == "")
	{
		alertar("Por favor, informe o cpf!", "cpf");
	}
	else
	{
		// campos do formulário
		dados = 'idAgente='					+ encodeURIComponent(idAgente);
		dados+= '&sigla=' 					+ encodeURIComponent(sigla);
		dados+= '&nomeCidade=' 				+ encodeURIComponent(nomeCidade);
		dados+= '&nomeTipoLogradouro=' 		+ encodeURIComponent(nomeTipoLogradouro);
		dados+= '&nomeTipoEndereco=' 		+ encodeURIComponent(nomeTipoEndereco);
		dados+= '&nomeAreaCultural=' 		+ encodeURIComponent(nomeAreaCultural);
		dados+= '&nomeSegmentoCultural=' 	+ encodeURIComponent(nomeSegmentoCultural);
		dados+= '&cpf=' 					+ encodeURIComponent(cpf);
		dados+= '&nome=' 					+ encodeURIComponent(nome);
		dados+= '&cep=' 					+ encodeURIComponent(cep);
		dados+= '&uf=' 						+ encodeURIComponent(uf);
		dados+= '&cidade=' 					+ encodeURIComponent(cidade);
		dados+= '&tipoEndereco=' 			+ encodeURIComponent(tipoEndereco);
		dados+= '&tipoLogradouro=' 			+ encodeURIComponent(tipoLogradouro);
		dados+= '&logradouro=' 				+ encodeURIComponent(logradouro);
		dados+= '&numero=' 					+ encodeURIComponent(numero);
		dados+= '&complemento=' 			+ encodeURIComponent(complemento);
		dados+= '&bairro=' 					+ encodeURIComponent(bairro);
		dados+= '&titular=' 				+ encodeURIComponent(titular);
		dados+= '&divulgarEndereco=' 		+ encodeURIComponent(divulgarEndereco);
		dados+= '&enderecoCorrespondencia=' + encodeURIComponent(enderecoCorrespondencia);
		dados+= '&areaCultural=' 			+ encodeURIComponent(areaCultural);
		dados+= '&segmentoCultural=' 		+ encodeURIComponent(segmentoCultural);
		dados+= '&dsCidade=' 		        + encodeURIComponent(dsCidade);
		dados+= '&dsTipoEndereco=' 			+ encodeURIComponent(dsTipoEndereco);
		dados+= '&dsTipoLogradouro=' 		+ encodeURIComponent(dsTipoLogradouro);
		dados+= '&dsArea=' 					+ encodeURIComponent(dsArea);
		dados+= '&dsSegmento=' 				+ encodeURIComponent(dsSegmento);
		
		
		enviar_pag("conselheiro/confirmarupdate", dados, "meu_frame");
	}
}




/**
* cancela confirmação de update
*/
function cancelar_confirmarupdate()
{
	// DADOS
	cpf                     = document.getElementById("cpf").value;
	nome                    = document.getElementById("nome").value;
	cep                     = document.getElementById("cep").value;
	uf                      = document.getElementById("uf").value;
	ufNome                  = document.getElementById("ufNome").value;
	cidade                  = document.getElementById("cidade").value;
	cidadeNome              = document.getElementById("cidadeNome").value;
	tipoEndereco            = document.getElementById("tipoEndereco").value;
	tipoEnderecoNome        = document.getElementById("tipoEnderecoNome").value;
	tipoLogradouro          = document.getElementById("tipoLogradouro").value;
	tipoLogradouroNome      = document.getElementById("tipoLogradouroNome").value;
	logradouro              = document.getElementById("logradouro").value;
	numero                  = document.getElementById("numero").value;
	complemento             = document.getElementById("complemento").value;
	bairro                  = document.getElementById("bairro").value;
	titular                 = document.getElementById("titular").value;
	divulgarEndereco        = document.getElementById("divulgarEndereco").value;
	enderecoCorrespondencia = document.getElementById("enderecoCorrespondencia").value;
	areaCultural            = document.getElementById("areaCultural").value;
	areaCulturalNome        = document.getElementById("areaCulturalNome").value;
	segmentoCultural        = document.getElementById("segmentoCultural").value;
	segmentoCulturalNome    = document.getElementById("segmentoCulturalNome").value;

	// FONE
	tipoFone     = document.getElementById("tipoFone").value;
	tipoFoneNome = document.getElementById("tipoFoneNome").value;
	ufFone       = document.getElementById("ufFone").value;
	ufFoneNome   = document.getElementById("ufFoneNome").value;
	dddFone      = document.getElementById("dddFone").value;
	dddFoneNome  = document.getElementById("dddFoneNome").value;
	divulgarFone = document.getElementById("divulgarFone").value;

	// E-MAIL
	tipoEmail     = document.getElementById("tipoEmail").value;
	tipoEmailNome = document.getElementById("tipoEmailNome").value;
	email         = document.getElementById("email").value;
	divulgarEmail = document.getElementById("divulgarEmail").value;
	enviarEmail   = document.getElementById("enviarEmail").value;

	
	dados = 'cpf=' + encodeURIComponent(cpf);
	dados+= '&nome=' + encodeURIComponent(nome);
	dados+= '&cep=' + encodeURIComponent(cep);
	dados+= '&uf=' + encodeURIComponent(uf);
	dados+= '&ufNome=' + encodeURIComponent(ufNome);
	dados+= '&cidade=' + encodeURIComponent(cidade);
	dados+= '&cidadeNome=' + encodeURIComponent(cidadeNome);
	dados+= '&tipoEndereco=' + encodeURIComponent(tipoEndereco);
	dados+= '&tipoEnderecoNome=' + encodeURIComponent(tipoEnderecoNome);
	dados+= '&tipoLogradouro=' + encodeURIComponent(tipoLogradouro);
	dados+= '&tipoLogradouroNome=' + encodeURIComponent(tipoLogradouroNome);
	dados+= '&logradouro=' + encodeURIComponent(logradouro);
	dados+= '&numero=' + encodeURIComponent(numero);
	dados+= '&complemento=' + encodeURIComponent(complemento);
	dados+= '&bairro=' + encodeURIComponent(bairro);
	dados+= '&titular=' + encodeURIComponent(titular);
	dados+= '&divulgarEndereco=' + encodeURIComponent(divulgarEndereco);
	dados+= '&enderecoCorrespondencia=' + encodeURIComponent(enderecoCorrespondencia);
	dados+= '&areaCultural=' + encodeURIComponent(areaCultural);
	dados+= '&areaCulturalNome=' + encodeURIComponent(areaCulturalNome);
	dados+= '&segmentoCultural=' + encodeURIComponent(segmentoCultural);
	dados+= '&segmentoCulturalNome=' + encodeURIComponent(segmentoCulturalNome);
	dados+= '&tipoFone=' + encodeURIComponent(tipoFone);
	dados+= '&tipoFoneNome=' + encodeURIComponent(tipoFoneNome);
	dados+= '&ufFone=' + encodeURIComponent(ufFone);
	dados+= '&ufFoneNome=' + encodeURIComponent(ufFoneNome);
	dados+= '&dddFone=' + encodeURIComponent(dddFone);
	dados+= '&dddFoneNome=' + encodeURIComponent(dddFoneNome);
	dados+= '&fone=' + encodeURIComponent(fone);
	dados+= '&divulgarFone=' + encodeURIComponent(divulgarFone);
	dados+= '&tipoEmail=' + encodeURIComponent(tipoEmail);
	dados+= '&tipoEmailNome=' + encodeURIComponent(tipoEmailNome);
	dados+= '&email=' + encodeURIComponent(email);
	dados+= '&divulgarEmail=' + encodeURIComponent(divulgarEmail);
	dados+= '&enviarEmail=' + encodeURIComponent(enviarEmail);

	enviar_pag("conselheiro/alterar", dados, "meu_frame");
}



function confirmarupdate() // alteração
{
    idAgente                 = document.getElementById("idAgente").value;
    sigla                     = document.getElementById("uf");
    sigla                   = sigla.options[sigla.selectedIndex].text;
    
    nomeCidade                 = document.getElementById("cidade");
    nomeCidade                = nomeCidade.options[nomeCidade.selectedIndex].text;
    
    nomeTipoLogradouro         = document.getElementById("tipoLogradouro");
    nomeTipoLogradouro        = nomeTipoLogradouro.options[nomeTipoLogradouro.selectedIndex].text;
    
    nomeTipoEndereco         = document.getElementById("tipoEndereco");
    nomeTipoEndereco        = nomeTipoEndereco.options[nomeTipoEndereco.selectedIndex].text;
    
    nomeAreaCultural         = document.getElementById("areaCultural");
    nomeAreaCultural        = nomeAreaCultural.options[nomeAreaCultural.selectedIndex].text;
    
    nomeSegmentoCultural     = document.getElementById("segmentoCultural");
    nomeSegmentoCultural    = nomeSegmentoCultural.options[nomeSegmentoCultural.selectedIndex].text;
    
    cpf                     = document.getElementById("cpf").value;
    nome                     = document.getElementById("nome").value;
    cep                     = document.getElementById("cep").value;
    uf                         = document.getElementById("uf").value;
    cidade                     = document.getElementById("cidade").value;
    tipoEndereco             = document.getElementById("tipoEndereco").value;
    tipoLogradouro             = document.getElementById("tipoLogradouro").value;
    logradouro                 = document.getElementById("logradouro").value;
    numero                     = document.getElementById("numero").value;
    complemento             = document.getElementById("complemento").value;
    bairro                     = document.getElementById("bairro").value;
    
    titular = "";
    for (i = 0; i < document.formAlterar.titular.length; i++)
    {
        if (document.formAlterar.titular[i].checked)
        {
            titular = document.formAlterar.titular[i].value;
            
        }
    }
    
    
    //divulgarEndereco        = document.getElementById("divulgarEndereco").value;
    
    divulgarEndereco = "";
    for (i = 0; i < document.formAlterar.divulgarEndereco.length; i++)
    {
        if (document.formAlterar.divulgarEndereco[i].checked)
        {
            divulgarEndereco = document.formAlterar.divulgarEndereco[i].value;
        }
    }
    
    //enderecoCorrespondencia = document.getElementById("enderecoCorrespondencia").value;
    
    enderecoCorrespondencia = "";
    for (i = 0; i < document.formAlterar.enderecoCorrespondencia.length; i++)
    {
        if (document.formAlterar.enderecoCorrespondencia[i].checked)
        {
            enderecoCorrespondencia = document.formAlterar.enderecoCorrespondencia[i].value;
        }
    }
    
    
    areaCultural            = document.getElementById("areaCultural").value;
    segmentoCultural        = document.getElementById("segmentoCultural").value;
    dsCidade                   = document.getElementById("cidade").value;    
    dsTipoEndereco          = document.getElementById("tipoEndereco").value;
    dsTipoLogradouro        = document.getElementById("tipoLogradouro").value;
    dsArea                    = document.getElementById("areaCultural").value;
    dsSegmento                = document.getElementById("segmentoCultural").value;

    if (cpf == "")
    {
        alertar("Por favor, informe o cpf!", "cpf");
    }
    else
    {
        // campos do formulário
        dados = 'idAgente='                    + encodeURIComponent(idAgente);
        dados+= '&sigla='                     + encodeURIComponent(sigla);
        dados+= '&nomeCidade='                 + encodeURIComponent(nomeCidade);
        dados+= '&nomeTipoLogradouro='         + encodeURIComponent(nomeTipoLogradouro);
        dados+= '&nomeTipoEndereco='         + encodeURIComponent(nomeTipoEndereco);
        dados+= '&nomeAreaCultural='         + encodeURIComponent(nomeAreaCultural);
        dados+= '&nomeSegmentoCultural='     + encodeURIComponent(nomeSegmentoCultural);
        dados+= '&cpf='                     + encodeURIComponent(cpf);
        dados+= '&nome='                     + encodeURIComponent(nome);
        dados+= '&cep='                     + encodeURIComponent(cep);
        dados+= '&uf='                         + encodeURIComponent(uf);
        dados+= '&cidade='                     + encodeURIComponent(cidade);
        dados+= '&tipoEndereco='             + encodeURIComponent(tipoEndereco);
        dados+= '&tipoLogradouro='             + encodeURIComponent(tipoLogradouro);
        dados+= '&logradouro='                 + encodeURIComponent(logradouro);
        dados+= '&numero='                     + encodeURIComponent(numero);
        dados+= '&complemento='             + encodeURIComponent(complemento);
        dados+= '&bairro='                     + encodeURIComponent(bairro);
        dados+= '&titular='                 + encodeURIComponent(titular);
        dados+= '&divulgarEndereco='         + encodeURIComponent(divulgarEndereco);
        dados+= '&enderecoCorrespondencia=' + encodeURIComponent(enderecoCorrespondencia);
        dados+= '&areaCultural='             + encodeURIComponent(areaCultural);
        dados+= '&segmentoCultural='         + encodeURIComponent(segmentoCultural);
        dados+= '&dsCidade='                 + encodeURIComponent(dsCidade);
        dados+= '&dsTipoEndereco='             + encodeURIComponent(dsTipoEndereco);
        dados+= '&dsTipoLogradouro='         + encodeURIComponent(dsTipoLogradouro);
        dados+= '&dsArea='                     + encodeURIComponent(dsArea);
        dados+= '&dsSegmento='                 + encodeURIComponent(dsSegmento);
        
        
        enviar_pag("conselheiro/confirmarupdate", dados, "meu_frame");
    }
}



//manda alteração confirmado
function confirmouUpdate() 
{
    idAgente                 = document.getElementById("idAgente").value;
    cpf                     = document.getElementById("cpf").value;
    nome                     = document.getElementById("nome").value;
    cep                     = document.getElementById("cep").value;
    uf                         = document.getElementById("uf").value;
    cidade                     = document.getElementById("cidade").value;
    tipoEndereco             = document.getElementById("tipoEndereco").value;
    tipoLogradouro             = document.getElementById("tipoLogradouro").value;
    logradouro                 = document.getElementById("logradouro").value;
    numero                     = document.getElementById("numero").value;
    complemento             = document.getElementById("complemento").value;
    bairro                     = document.getElementById("bairro").value;
    titular                 = document.getElementById("titular").value;
    divulgarEndereco        = document.getElementById("divulgarEndereco").value;
    enderecoCorrespondencia = document.getElementById("enderecoCorrespondencia").value;
    areaCultural            = document.getElementById("areaCultural").value;
    segmentoCultural        = document.getElementById("segmentoCultural").value;

    
        // campos do formulário
        dados = 'idAgente='                    + encodeURIComponent(idAgente);
        dados+= '&cpf='                     + encodeURIComponent(cpf);
        dados+= '&nome='                     + encodeURIComponent(nome);
        dados+= '&cep='                     + encodeURIComponent(cep);
        dados+= '&uf='                         + encodeURIComponent(uf);
        dados+= '&cidade='                     + encodeURIComponent(cidade);
        dados+= '&tipoEndereco='             + encodeURIComponent(tipoEndereco);
        dados+= '&tipoLogradouro='             + encodeURIComponent(tipoLogradouro);
        dados+= '&logradouro='                 + encodeURIComponent(logradouro);
        dados+= '&numero='                     + encodeURIComponent(numero);
        dados+= '&complemento='             + encodeURIComponent(complemento);
        dados+= '&bairro='                     + encodeURIComponent(bairro);
        dados+= '&titular='                 + encodeURIComponent(titular);
        dados+= '&divulgarEndereco='         + encodeURIComponent(divulgarEndereco);
        dados+= '&enderecoCorrespondencia=' + encodeURIComponent(enderecoCorrespondencia);
        dados+= '&areaCultural='             + encodeURIComponent(areaCultural);
        dados+= '&segmentoCultural='         + encodeURIComponent(segmentoCultural);
        dados+= '&msg='         + encodeURIComponent("Cadastrado com sucesso!");
        
        enviar_pag("conselheiro/alterou", dados, "meu_frame");
    
}




function adicionarFone()
{
    // FONE
    tipoFone     = document.getElementById("tipoFone").value;
    ufFone       = document.getElementById("ufFone").value;
    dddFone      = document.getElementById("dddFone").value;
    fone         = document.getElementById("fone").value;

    divulgarFone = "";
    for (i = 0; i < document.formCadastrar.divulgarFone.length; i++)
    {
        if (document.formCadastrar.divulgarFone[i].checked)
        {
            divulgarFone = document.formCadastrar.divulgarFone[i].value;
        }
    }

    if (tipoFone == 0)
    {
        alertar("Por favor, selecione o tipo de telefone!", "tipoFone");
    }
    else if (ufFone == 0)
    {
        alertar("Por favor, selecione o estado!", "ufFone");
    }
    else if (dddFone == 0)
    {
        alertar("Por favor, selecione o ddd do telefone!", "dddFone");
    }
    else if (fone == "")
    {
        alertar("Por favor, informe o telefone!", "fone");
    }
    else
    {
        var local = document.getElementById('tabBuscarFone');
        var tblBody = local.tBodies[0];
        var newRow = tblBody.insertRow(-1);
        newRow.setAttribute("name", "linhaFone[]");

        var newCell1 = newRow.insertCell(0);
        newCell1.innerHTML = document.getElementById("tipoFone").options[document.getElementById("tipoFone").selectedIndex].text;
        newCell1.innerHTML+= '<input type="hidden" name="tipoFones[]" id="tipoFones[]" value=' + tipoFone + '>';
    
        var newCell2 = newRow.insertCell(1);
        newCell2.innerHTML = document.getElementById("ufFone").options[document.getElementById("ufFone").selectedIndex].text;
        newCell2.innerHTML+= '<input type="hidden" name="ufFones[]" id="ufFones[]" value=' + ufFone + '>';
        newCell2.setAttribute("class", "center");
    
        var newCell3 = newRow.insertCell(2);
        newCell3.innerHTML = document.getElementById("dddFone").options[document.getElementById("dddFone").selectedIndex].text;
        newCell3.innerHTML+= '<input type="hidden" name="dddFones[]" id="dddFones[]" value=' + dddFone + '>';
        newCell3.setAttribute("class", "center");
    
        var newCell4 = newRow.insertCell(3);
        newCell4.innerHTML = fone;
        newCell4.innerHTML+= '<input type="hidden" name="Fones[]" id="Fones[]" value=' + fone + '>';
        newCell4.setAttribute("class", "center");
    
        var newCell5 = newRow.insertCell(4);
        newCell5.innerHTML = (divulgarFone == 1 ? 'Sim' : 'Não');
        newCell5.innerHTML+= '<input type="hidden" name="divulgarFones[]" id="divulgarFones[]" value=' + divulgarFone + '>';
        newCell5.setAttribute("class", "center");

        var newCell6 = newRow.insertCell(5);
        newCell6.setAttribute("class", "center");
        newCell6.setAttribute("name", "enter");
        newCell6.innerHTML = '<input type="button" title="Excluir E-mail" class="btn_excluir" onclick="return linhaFone();" />';
    }
}

function linhaFone()
{
    var local = document.getElementById('tabBuscarFone');
    var tblBody = local.tBodies[0];
    var newRow = tblBody.removeChild(1);
}


function adicionarEmail()
{
    // E-MAIL
    tipoEmail     = document.getElementById("tipoEmail").value;
    email         = document.getElementById("email").value;

    divulgarEmail = "";
    for (i = 0; i < document.formCadastrar.divulgarEmail.length; i++)
    {
        if (document.formCadastrar.divulgarEmail[i].checked)
        {
            divulgarEmail = document.formCadastrar.divulgarEmail[i].value;
        }
    }

    enviarEmail = "";
    for (i = 0; i < document.formCadastrar.enviarEmail.length; i++)
    {
        if (document.formCadastrar.enviarEmail[i].checked)
        {
            enviarEmail = document.formCadastrar.enviarEmail[i].value;
        }
    }

    if (tipoEmail == 0)
    {
        alertar("Por favor, selecione o tipo de e-mail!", "tipoEmail");
    }
    else if (email == "")
    {
        alertar("Por favor, informe o e-mail!", "email");
    }
    else if ((email.indexOf("@") < 1) || (email.lastIndexOf(".") <= email.indexOf("@")) || (email.indexOf("@") == email.length))
    {
        alertar("Email inválido!", "email");
    }
    else
    {
        var local = document.getElementById('tabBuscarEmail');
        var tblBody = local.tBodies[0];
        var newRow = tblBody.insertRow(-1);
        newRow.setAttribute("name", "linhaEmail[]");

        var newCell1 = newRow.insertCell(0);
        newCell1.innerHTML = document.getElementById("tipoEmail").options[document.getElementById("tipoEmail").selectedIndex].text;
        newCell1.innerHTML+= '<input type="hidden" name="tipoEmails[]" id="tipoEmails[]" value="' + tipoEmail + '" />';
    
        var newCell2 = newRow.insertCell(1);
        newCell2.innerHTML = email;
        newCell2.innerHTML+= '<input type="hidden" name="Emails[]" id="Emails[]" value="' + email + '" />';
    
        var newCell3 = newRow.insertCell(2);
        newCell3.setAttribute("class", "center");
        newCell3.innerHTML = (divulgarEmail=='1' ? 'Sim' : 'Não');
        newCell3.innerHTML+= '<input type="hidden" name="divulgarEmails[]" id="divulgarEmails[]" value="' + divulgarEmail + '" />';
    
        var newCell4 = newRow.insertCell(3);
        newCell4.setAttribute("class", "center");
        newCell4.innerHTML = (enviarEmail=='1' ? 'Sim' : 'Não');
        newCell4.innerHTML+= '<input type="hidden" name="enviarEmails[]" id="enviarEmails[]" value="' + enviarEmail + '" />';

        var newCell5 = newRow.insertCell(4);
        newCell5.setAttribute("class", "center");
        newCell5.innerHTML = '<input type="button" title="Excluir E-mail" class="btn_excluir" onclick="" />';
    }
}





function buscaNomeCpf() 
{
    opcao                     = document.getElementById("opcao").value;
    dado                     = document.getElementById("dado").value;
    alert(opcao);
    alert(dado);
    // campos do formulário
    dados = 'opcao='                    + encodeURIComponent(opcao);
    dados+= '&dado='                     + encodeURIComponent(dado);

    enviar_pag("conselheiro/conselheiro", dados);
}
//-->

/*-------
FUNCAO RESPONSAVEL POR PERMITIR A EXECUCAO DE UM SCRIPT NUMA SOLICITACAO VIA AJAX
--------*/
function extraiScript(texto){
   var ini = 0;
   while (ini!=-1){
       ini = texto.indexOf('<script', ini);
       if (ini >=0){
           ini = texto.indexOf('>', ini) + 1;
           var fim = (texto.indexOf('/script>', ini) - 1);
           codigo = texto.substring(ini,fim);
           novo = document.createElement("script")
           novo.text = codigo;
           document.body.appendChild(novo);
       }
   }
}


/*Funcao para acessar um controller via ajax e retornar os dados para o local indicado*/
var jqAjaxNum = 0;
function jqAjaxLink(fUrlDestino, fDados, fLocalExibir) {
    var num_interno = jqAjaxNum;
    jqAjaxNum++;
    $.ajax({
        method: "get",
        url: fUrlDestino,
        data: fDados,
        dataType: "html",
        //timeout: 1000,
        beforeSend: function(){
            $("#"+fLocalExibir).append('<div class="loader centro">Aguarde carregando dados...</div>');
        },
        complete: function(){
            $("#"+fLocalExibir+" div#loading"+num_interno).remove();
        }, //esconde loadig ao terminar
        success: function(html){ //se for bem sucedido exibe html do arquivo
            $("#erro").hide();
            $("#"+fLocalExibir).html(html); //insere html na div corpo
            
        },
        error: function(d,msg) {
                /*if(contRecursao < 3){
                        jqAjaxLink(fUrlDestino, fDados, fLocalExibir, contRecursao+1);
                }*/
            $("#erro").show();
            $("#erro").html("<b>Erro</b><br/><br/>"+msg+"<br/>");
            $("#erro").append("Nao foi possivel carregar arquivo: "+fUrlDestino);
            if (msg=="timeout") {
                $("#erro").append("Internet lenta? Problema no site?");
            }
        }
    }); //fecha $.ajax(
}

function jqAjaxLinkSemLoading(fUrlDestino, fDados, fLocalExibir, callback = '') {
    var callback = callback || null;
    var num_interno = jqAjaxNum;

    jqAjaxNum++;
    $.ajax({
        method: "get",
        url: fUrlDestino,
        data: fDados,
        dataType: "html",
        //timeout: 10000,
        beforeSend: function(){
            //$("#"+fLocalExibir).append('<div class="bg_loading"></div><div id="loading'+num_interno+'" class="loading">Aguarde carregando dados...</div>');
        },
        complete: function(){
            $("#"+fLocalExibir+" div#loading"+num_interno).remove();
        }, //esconde loadig ao terminar
        success: function(html, outsideData){ //se for bem sucedido exibe html do arquivo
            $("#erro").hide();
            $("#"+fLocalExibir).html(html); //insere html na div corpo
            if (typeof callback == 'function') {
                callback(html);
            }
        },
        error: function(d,msg) {
                /*if(contRecursao < 3){
                        jqAjaxLink(fUrlDestino, fDados, fLocalExibir, contRecursao+1);
                }*/
            $("#erro").show();
            $("#erro").html("<b>Erro</b><br/><br/>"+msg+"<br/>");
            $("#erro").append("Nao foi possivel carregar arquivo: "+fUrlDestino);
            if (msg=="timeout") {
                $("#erro").append("Internet lenta? Problema no site?");
            }
        }
    }); //fecha $.ajax(
}

/*Funcao para submeter os dados de um formulario via Ajax*/
function jqAjaxForm(fDados, fLocalExibir) {
    // pega todos os campos do formulario
    var params = $(fDados.elements).serialize();

    var num_interno = jqAjaxNum;
    jqAjaxNum++;
    //var posicaoTop = $("#"+fLocalExibir).offset().top;

    $.ajax({
        type: 'POST',
        data: params,
        url: fDados.action,
        dataType: "html",
        //timeout: 8000,
        timeout: 1800000, //ISSO CORRESPONDE A 30minutos
        beforeSend: function(){
            $("#"+fLocalExibir).append('<div class="loader centro">Aguarde carregando dados...</div>');
        },
        complete: function(){
            $("#"+fLocalExibir+" div#loading"+num_interno).remove();
        }, //esconde loadig ao terminar
        success: function(html){ //se for bem sucedido exibe html do arquivo
            $("#erro").hide();
            $("#"+fLocalExibir).html(html); //insere html na div corpo
            if(fLocalExibir == "div_mensagem" || fLocalExibir == "div_auxiliar" || fLocalExibir == "resultadoConsulta")
            {
	            $("html, body").animate(
	            	{scrollTop: $("#" + fLocalExibir).offset().top},800
	            );
            }
        },
        error: function(d,msg) {
            $("#erro").show();
            $("#erro").html("<b>Erro</b><br/><br/>"+msg+"<br/>");
            $("#erro").append("Falha ao enviar formulario<br/>");
            if (msg=="timeout") {
                $("#erro").append("Internet lenta? Problema no site?");
            }
        }
    })
}

function message(msg, type)
{
	$('#novas_mensagens').html('');
	$('#novas_mensagens').append('<div class="msg'+type+'"><div class="float_right"><input type="button" class="btn_close" title="Fechar mensagem" id="msg'+type+'" onclick=$(".msg'+type+'").hide(); /></div><div>'+msg+'</div></div>');
        $('html, body').animate({
            scrollTop: $('#novas_mensagens').offset().top
        }, 1000);
}

function fecharModal(elemento)
{
	$('#' + elemento).dialog('destroy');
}

function carregandoModal(msg, titulo, largura, altura)
{
	msg     = (msg     == null) ? '<br /><br /><div class="loader centro">Aguarde, carregando dados...</div>' : '<div class="loader centro">' + msg + '</div>';
	titulo  = (titulo  == null) ? 'carregando...' : titulo;
	largura = (largura == null) ? 280 : largura;
	altura  = (altura  == null) ? 160 : altura;

	$('#carregandoModalAjax').remove();
	$('#novas_mensagens').append('<div id="carregandoModalAjax" class="sumir">' + msg + '</div>');

	$('#carregandoModalAjax').dialog("destroy");
	$('#carregandoModalAjax').dialog
	({
		modal: true,
		resizable: false,
		width: largura,
		height: altura,
		title: titulo
	});
	$('.ui-dialog-titlebar-close').remove();
}

function modalValidacaoEnvioArquivos(msg)
{
	$('#modalValidacaoEnvioArquivos').remove();
	$('#novas_mensagens').append('<div id="modalValidacaoEnvioArquivos" class="sumir">' + msg + '</div>');

	alertModal('Alerta!', 'modalValidacaoEnvioArquivos', 420, 170);
}

/**
 * Função para carregar uma página dentro de outra via ajax
 */
function carregarDados(url, divRetorno)
{
	$('#' + divRetorno).html('<br><br><center>Aguarde, carregando dados...<br><img src="<?php echo $this->baseUrl(); ?>/public/img/ajax.gif" /></center><br><br>');
	$.ajax(
	{
		url : url,
		success: function(data)
		{
			$('#' + divRetorno).html(data);
		},
		type : 'post'
	});
} // fecha função carregaDados()
//-->