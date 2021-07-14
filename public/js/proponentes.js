<!--
/**
 * Fun��es Proponentes
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package public
 * @subpackage public.js
 * @copyright c 2010 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

/**
 * Funcao AJAX para buscar informacoes do endereco de acordo com o CEP informado
 */
function buscar_cep(cep)
{
    // pega os dados a serem populados
    logradouro     = document.getElementById("logradouro");
    tipoLogradouro = document.getElementById("tipoLogradouro");
    bairro         = document.getElementById("bairro");
    cidade         = document.getElementById("cidade");
    uf             = document.getElementById("uf");

    ajax = xmlhttp(); // instancia ajax
    ajax.open("GET", "/cep/cep?cep=" + cep, true);
    ajax.onreadystatechange = function()
    {
        if (cep.length != 10)
        {
            $('#erroCep').html("O CEP informado &eacute; inv&aacute;lido!");
        }
        else
        {
            $('#erroCep').html("");
        }

        // enquanto estiver processando bloqueia os campos
        if (ajax.readyState > 0 && ajax.readyState <= 3 && cep.length == 10)
        {
            logradouro.disabled     = true;
            tipoLogradouro.disabled = true;
            bairro.disabled         = true;
            cidade.disabled         = true;
            uf.disabled             = true;
            logradouro.value               = "carregando...";
            tipoLogradouro.options[0].text = "carregando...";
            bairro.value                   = "carregando...";
            cidade.options[0].text         = "carregando...";
            uf.options[0].text             = "...";
            $('#erroCep').html("<img src='../public/img/ajax.gif' alt='' /> Aguarde...");
        }
        if (ajax.readyState == 4 && ajax.status == 200 && cep.length == 10)
        {
            // divide a string para colocar cada uma em seu campo
            var s = ajax.responseText;

            if (s == "") // caso os dados retornem vazios ou o cep Não exista
            {
                logradouro.value               = " ";
                tipoLogradouro.options[0].text = " - Selecione - ";
                bairro.value                   = " ";
                cidade.options[0].text         = " - Selecione - ";
                uf.options[0].text             = " -- ";
                $('#erroCep').html("O CEP informado � inv�lido!");
            }
            else // caso o cep exista
            {
                $('#erroCep').html("");
                txtLogradouro = s.substring(0, (i = s.indexOf(':')));
                s = s.substring(++i);
                txtTipoLogradouro = s.substring(0, (i = s.indexOf(':')));
                s = s.substring(++i);
                txtBairro = s.substring(0, (i = s.indexOf(':')));
                s = s.substring(++i);
                txtCodCidade = s.substring(0, (i = s.indexOf(':')));
                s = s.substring(++i);
                txtCidade = s.substring(0, (i = s.indexOf(':')));
                s = s.substring(++i);
                txtUF = s.substring(0, (i = s.indexOf(';')));

                // volta os textos dos combos
                tipoLogradouro.options[0].text = " - Selecione - ";
                cidade.options[0].text         = " - Selecione - ";
                uf.options[0].text             = " -- ";
                if (txtUF == "" && txtCidade == "")
                {
                    cidade.options[0].text = " - Selecione primeiro o UF - ";
                }


                // logradouro
                logradouro.value = txtLogradouro;
                if (txtLogradouro == "")
                {
                    logradouro.disabled = false; // habilita para preenchimento
                }


                // tipoLogradouro
                // seleciona o combo de acordo com a lista existente no formul�rio
                for (i = 0; i < tipoLogradouro.options.length; i++)
                {
                    if (txtTipoLogradouro == tipoLogradouro.options[i].text)
                    {
                        // seleciona o �tem igual
                        tipoLogradouro.options[i].selected = true;
                    }
                }
                if (tipoLogradouro.options[tipoLogradouro.selectedIndex].value == "" ||
                    tipoLogradouro.options[tipoLogradouro.selectedIndex].text != txtTipoLogradouro)
                    {
                    tipoLogradouro.options[0].selected = true; // seleciona o primeiro option
                    tipoLogradouro.disabled = false; // habilita para preenchimento
                }


                // bairro
                bairro.value = txtBairro;

                if (txtBairro == "")
                {
                    bairro.disabled = false; // habilita para preenchimento
                }


                // uf
                // seleciona o combo de acordo com a lista existente no formul�rio
                for (i = 0; i < uf.options.length; i++)
                {
                    if (txtUF == uf.options[i].text)
                    {
                        // seleciona o �tem igual
                        uf.options[i].selected = true;
                    }
                }
                if (uf.options[uf.selectedIndex].value == "" ||
                    uf.options[uf.selectedIndex].text != txtUF)
                    {
                    uf.options[0].selected = true; // seleciona o primeiro option
                    uf.disabled = false; // habilita para preenchimento
                }


                // cidade
                // verifica se a cidade veio vazia
                if (txtCidade == "")
                {
                    cidade.disabled = false; // habilita para preenchimento
                }
                // preenche o combo com a cidade
                else
                {
                    // deixa apenas um elemento no combo, os outros s�o exclu�dos
                    document.getElementById("cidade").options.length = 0;

                    // cria um novo option
                    var novo   = document.createElement("option");
                    novo.value = txtCodCidade; // atribui um valor ao option
                    novo.text  = txtCidade; // atribui um texto ao option
                    document.getElementById("cidade").options.add(novo);
                }
            } // fecha if
        } // fecha else
    } // fecha onreadystatechange

    ajax.send(null);
} // fecha fun��o buscar_cep()



/**
 * Funcao para habilitar os campos quando o formulario for submetido
 */
function habilitar_campos()
{
    // pega os campos a serem habilidados
    document.getElementById("cpf").disabled            = false;
    document.getElementById("logradouro").disabled     = false;
    document.getElementById("tipoLogradouro").disabled = false;
    document.getElementById("bairro").disabled         = false;
    document.getElementById("cidade").disabled         = false;
    document.getElementById("uf").disabled             = false;

    return true;
} // fecha fun��o habilitar_campos()



/**
 * Funcao para buscar os dados do usuario quando o cpf/cnpj e informado
 */
function buscardados(valor)
{
    $("#nome").val('');

    //	$("#idAgente").val('');
    //	$("#cep").val('');
    //	$("#uf").val('');
    //	$("#tipoEndereco").val(0);
    //	$("#tipoLogradouro").val(0);
    //	$("#logradouro").val('');
    //	$("#numero").val('');
    //	$("#complemento").val('');
    //	$("#bairro").val('');
    //	$("#cidade").val(0);
    //	$("#cidade").val(0);
    //	$("#cidade").val(0);

    Tipo = "";
    for (i = 0; i < document.formCadAgentes.Tipo.length; i++)
    {
        if (document.formCadAgentes.Tipo[i].checked)
        {
            Tipo = document.formCadAgentes.Tipo[i].value;
        }
    }

    value = $("#cpf").val();

    if (value == '')
    {
        $('#erroCpf').html('Informe o CPF/CNPJ!');
    }
    else if (Tipo == 0 && value.length != 14)
    {
        $('#erroCpf').html('CPF Incompleto!');
    }
    else if (Tipo == 1 && value.length != 18)
    {
        $('#erroCpf').html('CNPJ Incompleto!');
    }
    else if (Tipo == 0 && value.length != 14)
    {
        $('#erroCpf').html('CPF inv&aacute;lido!');
    }
    else if (Tipo == 1 && value.length != 18)
    {
        $('#erroCpf').html('CNPJ inv&aacute;lido!');
    }
    else
    {
        $('#erroCpf').html('');

        // retira as mascaras do cpf/cnpj
        value = value.replace(".","");
        value = value.replace(".","");
        value = value.replace("/","");
        cpf = value.replace("-","");

        // faz a verificacao do agente via post
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                cpf: cpf
            },
            url: '/agente/manteragentes/salvaragente',
            success: function (data)
            {
                if (data[0].msgCPF == 'ok')
                {
                    // percorre os dados
                    for (i in data)
                    {
                        $("#nome").val(data[i].Nome);
                        if (data[i].Nome == "" || data[i].Nome == " ")
                        {
                            $("#nome").attr('readonly', '');
                        }
                        else
                        {
                            $("#nome").attr('readonly','readonly');
                        }
                        $(".tipos").attr('disabled','disabled');
                        $("#cpf").attr('disabled','disabled');
                        $("#idAgente").val(data[i].idAgente);

                        buscarEnderecos();
                        buscarTelefones(); // busca os telefones do agente
                        buscarEmails(); // busca os e-mails do agente
                        buscarVisao(); // busca as vis�es do agente
                        buscarDirigentes(); // busca os dirigentes do agente
                    } // fecha for
                } // fecha if
                else if (data[0].msgCPF == 'not') // cpf/cnpj inv�lido
                {
                    $('#erroCpf').html('CPF/CNPJ inv�lido');
                }
                else // novo cpf/cnpj
                {
                    $("#nome").attr('readonly','');
                    $("#cpf").attr('disabled','disabled');
                    $("#nome").attr('readonly','');
                    $("#cep").attr('readonly','');
                    $("#cep").attr('readonly','');
                    //$("#uf").val(data[i].UF);
                } // fecha else
            },
            error: function (data)
            {
                alert("Falha na recuperacao dos dados.\nNao foi possivel carregar agente!");
            }
        }); // fecha $.ajax

    } // fecha else
} // fecha fun��o buscardados()



/**
 * funcao para buscar os dados do dirigente quando o cpf e informado
 */
function buscardadosdirigente()
{
    $("#nome").val('');
    $("#idAgente").val('');
    $("#cep").val('');
    $("#uf").val('');
    $("#tipoEndereco").val(0);
    $("#tipoLogradouro").val(0);
    $("#logradouro").val('');
    $("#numero").val('');
    $("#complemento").val('');
    $("#bairro").val('');
    $("#cidade").val(0);
    $("#cidade").val(0);
    $("#cidade").val(0);

    value = $("#cpf").val();

    if (value == '')
    {
        $('#erroCpf').html('Informe o CPF!');
    }
    else if (value.length != 14)
    {
        $('#erroCpf').html('CPF Incompleto!');
    }
    else
    {
        $('#erroCpf').html('');

        // retira as m�scaras do cpf/cnpj
        value = value.replace(".","");
        value = value.replace(".","");
        value = value.replace("/","");
        cpf = value.replace("-","");

        // pega o C&oacute;digo do agente
        idAgenteGeral = document.getElementById('idAgenteGeral').value;

        // faz a verifica��o do dirigente via post
        $.ajax({
            type: "POST",
            dataType: 'json',
            data: {
                cpf: cpf,
                idAgenteGeral: idAgenteGeral
            },
            url: '../manteragentes/salvardirigente',
            success: function (data)
            {
                if (data[0].msgCPF == 'ok')
                {
                    // percorre os dados
                    for (i in data)
                    {
                        $("#nome").val(data[i].Nome);
                        if (data[i].Nome == "" || data[i].Nome == " ")
                        {
                            $("#nome").attr('readonly', '');
                        }
                        else
                        {
                            $("#nome").attr('readonly','readonly');
                        }
                        $(".tipos").attr('disabled','disabled');
                        $("#cpf").attr('disabled','disabled');
                        $("#idAgente").val(data[i].idAgente);

                        $("#cep").val(data[i].CEP);
                        $("#cep").attr('readonly','');
                        $("#numero").attr('readonly','');
                        $("#complemento").attr('readonly','');
                        $("#uf").val(data[i].UF);

                        if (data[i].UF == "" || data[i].UF == " " || data[i].UF == 0)
                        {
                            carregar_combo(data[i].UF, 'cidade', '../cidade/combo', ' - Selecione - ', data[i].Cidade);
                        }
                        else
                        {
                            document.getElementById('cidade').options.length = 0;
                            var novoOption = document.createElement("option");
                            novoOption.value = data[i].Cidade; // atribui um valor ao option
                            novoOption.text  = data[i].dsCidade; // atribui um texto ao option
                            document.getElementById('cidade').options.add(novoOption); // adiciona o novo elemento
                        }

                        $("#tipoEndereco")		.val(data[i].TipoEndereco);
                        $("#tipoLogradouro")	.val(data[i].TipoLogradouro);
                        $("#logradouro")		.val(data[i].Logradouro);
                        $("#numero")			.val(data[i].Numero);
                        $("#complemento")		.val(data[i].Complemento);
                        $("#bairro")			.val(data[i].Bairro);

                        if (data[i].DivulgarEndereco == 1)
                        {
                            $("input[name='divulgarEndereco'][value='1']").attr('checked','checked');
                        }
                        else
                        {
                            $("input[name='divulgarEndereco'][value='0']").attr('checked','checked');
                        }

                        if (data[i].EnderecoCorrespondencia == 1)
                        {
                            $("input[name='enderecoCorrespondencia'][value='1']").attr('checked','checked');
                        }
                        else
                        {
                            $("input[name='enderecoCorrespondencia'][value='0']").attr('checked','checked');
                        }

                        buscarEnderecos();// busca os endere�os
                        buscarTelefones(); // busca os telefones do dirigente
                        buscarEmails(); // busca os e-mails do dirigente
                    } // fecha for
                } // fecha if
                else if (data[0].msgCPF == 'not') // cpf/cnpj inv�lido
                {
                    $('#erroCpf').html('CPF inv�lido');
                }
                else // novo cpf/cnpj
                {
                    $("#nome")	.attr('readonly','');
                    $("#cpf")	.attr('disabled','disabled');
                    $("#nome")	.attr('readonly','');
                    $("#cep")	.attr('readonly','');
                    $("#cep")	.attr('readonly','');
                    $("#uf")	.val(data[i].UF);
                } // fecha else
            },
            error: function (data)
            {
                alert("Falha na recupera��o dos dados.\nNão foi poss�vel carregar dirigente!");
            }
        }); // fecha $.ajax

    } // fecha else
} // fecha fun��o buscardadosdirigente()



/**
 * Fun��o para buscar todos os e-mails do agente/dirigente
 */
function buscarEnderecos()
{
    $.ajax({
        type: "POST",
        dataType: 'json',
        data:
        {
            idAgente: $("#idAgente").val()
        },
        url: '../manteragentes/buscarenderecos',
        success: function(data)
        {
            // percorre todos os dados
            for (e in data)
            {
                var tabela = '<tr id="tabela'+e+'">';
                tabela +='<td>';
                tabela += data[e].Cep;
                tabela += '<input type="hidden" name="ceps[]" id="ceps[]" value="' + data[e].Cep + '" />';
                tabela +='</td>';
                tabela +='<td>';
                tabela += data[e].TipoEndereco
                tabela +='<input type="hidden" name="tipoEnderecos[]" id="tipoEnderecos[]" value="' + data[e].CodTipoEndereco + '" />';
                tabela +='</td>';
                tabela +='<td>';
                tabela += data[e].UF;
                tabela += '<input type="hidden" name="ufs[]" id="ufs[]" value="' + data[e].CodUF + '" />';
                tabela +='</td>';
                tabela +='<td>';
                tabela += data[e].Municipio
                tabela += '<input type="hidden" name="cidades[]" id="cidades[]" value="' +  data[e].CodMun  + '" />';
                tabela +='</td>';
                tabela +='<td>';
                tabela += data[e].Logradouro
                tabela += '<input type="hidden" name="logradouros[]" id="logradouros[]" value="' + data[e].Logradouro + '" />';
                tabela +='</td>';
                tabela +='<td>';
                tabela+= 'Correspond�ncias <input type="radio" style="margin:5px" name="correspondenciaEnderecos" id="correspondenciaEnderecos" value="end'+e+'"/>';
                tabela +='</td>';
                tabela +='<td>';
                tabela += '<div class="botao_icone"><a class="cancelar_ico" href="#" title=" Excluir endere�o " onclick=excluirFoneEmail("#tabela'+e+'"); /></div>';
                tabela += '<input type="hidden" name="numeros[]" id="numeros[]" value="' + data[e].Numero + '" />';
                tabela += '<input type="hidden" name="complementos[]" id="complementos[]" value="' + data[e].Complemento + '" />';
                tabela += '<input type="hidden" name="bairros[]" id="bairros[]" value="' + data[e].Bairro + '" />';
                tabela += '<input type="hidden" name="tipoLogradouros[]" id="tipoLogradouros[]" value="' + data[e].TipoLogradouro + '" />';
                tabela += '<input type="hidden" name="divulgarEnderecos[]" id="divulgarEnderecos[]" value="' + data[e].Divulgar + '" />';
                tabela +='</td>';
                tabela +='</tr>';
                $("#tabBuscarEndereco").append(tabela);
                adicionar_endereco_agente(false);
            } // fecha for
        },
        error: function(data)
        {
            if (data[0].msgCPF == 'ok'){
                alert('Falha na recupera��o dos dados.\nNão foi poss�vel carregar os endere�os!');
            }
        }
    });
}

function buscarEmails()
{
    $.ajax({
        type: "POST",
        dataType: 'json',
        data:
        {
            idAgente: $("#idAgente").val()
        },
        url: '../manteragentes/buscaremails',
        success: function(data)
        {
            // percorre todos os dados
            for (e in data)
            {
                document.getElementById("tipoEmail").options[document.getElementById("tipoEmail").selectedIndex].value = data[e].TipoInternet;
                document.getElementById("tipoEmail").options[document.getElementById("tipoEmail").selectedIndex].text = data[e].tipo;

                $("#email").val(data[e].Descricao);
                if (data[e].Divulgar == 1)
                {
                    $("input[name='divulgarEmail'][value='1']").attr('checked','checked');
                }
                else
                {
                    $("input[name='divulgarEmail'][value='0']").attr('checked','checked');
                }

                if (data[e].Status == 1)
                {
                    $("input[name='enviarEmail'][value='1']").attr('checked','checked');
                }
                else
                {
                    $("input[name='enviarEmail'][value='0']").attr('checked','checked');
                }

                adicionar_email_agente(false);

                // limpa os campos
                document.getElementById("tipoEmail").options[document.getElementById("tipoEmail").selectedIndex].value = 0;
                document.getElementById("tipoEmail").options[document.getElementById("tipoEmail").selectedIndex].text = '- Selecione -';
                $("#email").val('');
                $("input[name='divulgarEmail'][value='0']").attr('checked','checked');
                $("input[name='enviarEmail'][value='1']").attr('checked','checked');
            } // fecha for
        },
        error: function(data)
        {
            alert('Falha na recupera��o dos dados.\nNão foi poss�vel carregar os e-mails!');
        }
    });
} // fecha fun��o buscarEmails()



/**
 * Fun��o para buscar todos os telefones do agente
 */
function buscarTelefones()
{
    $.ajax({
        type: "POST",
        dataType: 'json',
        data:
        {
            idAgente: $("#idAgente").val()
        },
        url: '../manteragentes/buscarfones',
        success: function(data)
        {
            // percorre todos os dados
            for (f in data)
            {
                document.getElementById("tipoFone").options[document.getElementById("tipoFone").selectedIndex].value = data[f].TipoTelefone;
                document.getElementById("tipoFone").options[document.getElementById("tipoFone").selectedIndex].text = data[f].dsTelefone;

                document.getElementById("ufFone").options[document.getElementById("ufFone").selectedIndex].value = data[f].UF;
                document.getElementById("ufFone").options[document.getElementById("ufFone").selectedIndex].text = data[f].ufSigla;

                carregar_combo(data[f].UF,'dddFone','../ddd/combo','--',data[f].DDD);

                document.getElementById("dddFone").options[document.getElementById("dddFone").selectedIndex].value = data[f].DDD;
                document.getElementById("dddFone").options[document.getElementById("dddFone").selectedIndex].text = data[f].Codigo;

                document.getElementById('fone').value = data[f].Numero;

                if (data[f].Divulgar == 1)
                {
                    $("input[name='divulgarFone'][value='1']").attr('checked','checked');
                }
                else
                {
                    $("input[name='divulgarFone'][value='0']").attr('checked','checked');
                }

                adicionar_fone_agente(false); // adiciona o telefone

                // limpa os campos
                document.getElementById("tipoFone").options[document.getElementById("tipoFone").selectedIndex].value = '';
                document.getElementById("tipoFone").options[document.getElementById("tipoFone").selectedIndex].text = '- Selecione -';
                document.getElementById("ufFone").options[document.getElementById("ufFone").selectedIndex].value = 0;
                document.getElementById("ufFone").options[document.getElementById("ufFone").selectedIndex].text = '--';
                document.getElementById("dddFone").options[document.getElementById("dddFone").selectedIndex].value = '';
                document.getElementById("dddFone").options[document.getElementById("dddFone").selectedIndex].text = '--';
                $("#fone").val('');
                $("input[name='divulgarFone'][value='0']").attr('checked','checked');
            } // fecha for
        },
        error: function(data)
        {
            alert('Falha na recupera��o dos dados.\nNão foi poss�vel carregar os telefones!');
        }
    });
} // fecha fun��o buscarTelefones()



/**
 * Fun��o para buscar todos os dirigentes do agente
 */
function buscarDirigentes()
{
    // cnpj do agente
    dados = 'cnpj_cpf=' + encodeURIComponent(document.getElementById('cpf').value);
    dados+= '&idAgente=' + encodeURIComponent(document.getElementById('idAgente').value);

    // busca a tabela com os dados do dirigente
    enviar_pag('../manteragentes/buscardirigentes', dados, 'tabBuscarDirigente');
} // fecha fun��o buscarDirigentes()



/**
 * Fun��o para buscar todas as �reas do agente
 */
function buscarareasegmento()
{
    $("#Q_suplentes").html('');
    $("#Q_titulares").html('');
    $("#msgAS").html('');
    $("#TitularSuplente").html('');
    var TitularSuplente = '<p><strong>COMPONENTE(S) DA comissão CADASTRADO(S):</strong>';

    $.ajax({
        type: "POST",
        dataType: 'json',
        data:
        {
            area: 		$("#areaCultural").val(),
            segmento: 	$("#segmentoCultural").val()
        },
        url: '../manteragentes/buscaareasegmento',
        success: function(data)
        {
            // percorre todos os dados
            for (i in data)
            {
                //$("#areaCultural").attr('disabled','disabled');
                //$("#segmentoCultural").attr('disabled','disabled');

                $("#Q_titulares").html('<strong>' + data[0].Q_titulares + '</strong> Titular e ');
                $("#Q_suplentes").html('<strong>' + data[0].Q_suplentes + '</strong> Suplente(s) cadastrado(s)!');
                $("#msgAS").html('<p>' + data[0].msgAS + '</p>');

                TitularSuplente += '<br />  &raquo; ' + data[i].Nome + ' ' + data[i].Titular;

                if (data[0].Q_titulares >= 1 && data[0].Q_suplentes >= 2) // desabilita os radios de titular e suplente
                {
                    $("input[name='titular'][value='0']").attr('disabled','disabled');
                    $("input[name='titular'][value='1']").attr('disabled','disabled');
                    $("input[name='titular'][value='0']").attr('checked','');
                    $("input[name='titular'][value='1']").attr('checked','');
                }
                else
                {
                    if (data[0].Q_titulares >= 1)
                    {
                        $("input[name='titular'][value='1']").attr('disabled','disabled');
                        $("input[name='titular'][value='1']").attr('checked','');
                    }
                    else
                    {
                        $("input[name='titular'][value='1']").attr('disabled','');
                        $("input[name='titular'][value='1']").attr('checked','checked');
                        $("input[name='titular'][value='0']").attr('checked','');
                    }

                    if (data[0].Q_suplentes >= 2)
                    {
                        $("input[name='titular'][value='0']").attr('disabled','disabled');
                        $("input[name='titular'][value='0']").attr('checked','');
                    }
                    else
                    {
                        $("input[name='titular'][value='0']").attr('disabled','');
                        $("input[name='titular'][value='0']").attr('checked','checked');
                        $("input[name='titular'][value='1']").attr('checked','');
                    }
                } // fecha else
            } // fecha for

            $("#TitularSuplente").html(TitularSuplente + '</p>');

            // limpa os textos
            if ($("#areaCultural").val() == 0 || $("#areaCultural").val() == "")
            {
                $("#Q_suplentes").html('');
                $("#Q_titulares").html('');
                $("#msgAS").html('');
                $("#TitularSuplente").html('');
            }
        },
        error: function(data)
        {
            alert('Falha na recupera��o dos dados.\nNão foi poss�vel carregar �reas e seguimentos culturais!');
        }
    });
} // fecha fun��o buscarareasegmento()



/**
 * Fun��o para buscar todas as vis�es do agente
 */
function buscarVisao()
{
    $("#erroVisao").html('');
    $("#spanVisao").html('');

    $.ajax({
        type: "POST",
        dataType: 'json',
        data:
        {
            idAgente: $("#idAgente").val(),
            visao:    $("#visao").val()
        },
        url: '../manteragentes/buscarvisao',
        success: function(data)
        {
            // percorre todos os dados
            if (data.length > 0)
            {
                // mostra as vis�es do agente e deixa uma vis�o selecionada
                visoesDoAgente = '<strong>VIS&Otilde;ES DO AGENTE:</strong> ';
                for (i in data)
                {
                    if(data[i].area && data[i].verificacao == 210){
                        visoesDoAgente+= data[i].Descricao+'(&Aacuterea: '+data[i].area+")";
                    }
                    else{
                        visoesDoAgente+= data[i].Descricao
                    }
                    if (i < (data.length - 1))
                    {
                        visoesDoAgente+= ' / ';
                    }

                    // percorre o select com as vis�es
                    for (j = 0; j < document.getElementById("visao").options.length; j++)
                    {
                        // seleciona a op��o caso o usu�rio tenha a respectiva vis�o
                        if (document.getElementById("visao").options[j].value == data[i].Visao)
                        {
                            document.getElementById("visao").options[j].selected = true // seleciona o option
                        } // fecha if
                    } // fecha for do select

                } // fecha for data

                $("#spanVisao").html(visoesDoAgente);
            } // fecha if
            else
            {
                $("#erroVisao").html('');
                $("#spanVisao").html('');
            }
        }
    });
} // fecha fun��o buscarVisao()
//-->
