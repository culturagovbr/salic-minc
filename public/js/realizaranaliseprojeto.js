<!--
/**
 * Fun��es Realizar Análise Projeto
 * @author Equipe RUP - Politec
 * @since 07/06/2010
 * @version 1.0
 * @package public
 * @subpackage public.js
 * @copyright � 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */



/**
 * Busca os valores iniciais (valores predefinidos) dos campos
 */

var qtd_parec_relat        = 0;
var ocorrencia_parec_relat = 0;
var valor_parec_relat      = 0;

function buscar_val_pred_parec_relat(i)
{
	qtd_parec_relat            = document.getElementById('qtd' + i + '').value;
	ocorrencia_parec_relat     = document.getElementById('ocorrencia' + i + '').value;
	valor_parec_relat          = document.getElementById('vlunitario' + i + '').value;
	calcular_total_parec_relat = document.getElementById('calcular_total' + i + '').innerHTML;
} // fecha fun��o buscar_val_pred_parec_relat()



/**
 * Verificar se o valor do parecerista � maior que o valor do relator
 */

function validar_val_parec_relat(i)
{
	// pega o valor total do relator
	calcular_total = $("#calcular_total"+i).html();
	calcular_total = calcular_total.replace('R$ ', '');

	// retira os pontos e as v�rgulas, deixando somente n�meros
	calcular_total = calcular_total.replace(/\D/g, "");
	calcular_total = calcular_total.replace(/(\d{0})(\d)/, "$1$2");

	// adiciona o ponto na casa decimal
	calcular_total = calcular_total.replace(/(\d)(\d{2})$/, "$1.$2");

	// converte para float
	calcular_total = parseFloat(calcular_total);
        totalcalculos  = new Number($("#totalcalculos" + i).val())
        valormaximo  = new Number($("#valorpossivel").val())
        somatotal = new Number(totalcalculos+valormaximo);
//        alert(somatotal+"/"+valormaximo);
	// se o valor do parecerista � maior que o valor do relator
	if (calcular_total > somatotal)
	{
		// reseta os campo (atribui os valores iniciais)
		document.getElementById('qtd' + i + ''). value               = qtd_parec_relat;
		document.getElementById('ocorrencia' + i + ''). value        = ocorrencia_parec_relat;
		document.getElementById('vlunitario' + i + ''). value        = valor_parec_relat;
		document.getElementById('calcular_total' + i + '').innerHTML = calcular_total_parec_relat;

		// abre o modal com a mensagem de erro
		$("#dialog-valor").dialog("destroy");
		$("#dialog-valor").dialog
		({
			width:400,
			height:230,
			resizable:false,
			EscClose:false,
			modal:false,
			buttons:
			{
				'Ok':function()
				{
					$(this).dialog('close'); // fecha a modal
				}
			}
		});
		$('.ui-dialog-titlebar-close').remove();
	}
} // fecha fun��o validar_val_parec_relat()



/**
 * Fun��o para realizar o calculo autom�tico da atualiza��o de planilha do projeto
 */

function calcular_planilha_projeto(cont)
{
	// recebe os valores do formul�rio
	qtd        = document.getElementById('qtd' + cont + '').value;
	ocorrencia = document.getElementById('ocorrencia' + cont + '').value;
	valor      = document.getElementById('vlunitario' + cont + '').value;

	// retira os pontos e as v�rgulas, deixando somente n�meros
	valor = valor.replace(/\D/g, "");
	valor = valor.replace(/(\d{0})(\d)/, "$1$2");

	// adiciona o ponto na casa decimal
	valor = valor.replace(/(\d)(\d{2})$/, "$1.$2");

	// converte para float e adiciona precis�o decimal
	qtd        = parseFloat(qtd).toFixed(2);
	ocorrencia = parseFloat(ocorrencia).toFixed(2);
	valor      = parseFloat(valor).toFixed(2);

	// vari�vel com o resultado
	resultado = parseFloat(qtd * ocorrencia * valor).toFixed(2); // armazena o resultado

	// se Não for n�mero
	if (isNaN(resultado))
	{
		resultado = '';
	}
	// caso seja n�mero
	else
	{
		// formata para real
		resultado = resultado.replace(/\D/g, "");
		resultado = resultado.replace(/(\d)(\d{2})$/, "$1,$2");
		resultado = resultado.replace(/(\d+)(\d{3},\d{2})$/g, "$1.$2");

		var q = (resultado.length - 3) / 3; // quantidade caracteres
		var c = 0; // contador
		while (q > c)
		{
			c++;
			resultado = resultado.replace(/(\d+)(\d{3}.*)/, "$1.$2");
		}
		resultado = resultado.replace(/^(0+)(\d)/g, "$2");
		resultado = 'R$ ' + resultado;
	} // fecha else
	document.getElementById('calcular_total' + cont + '').innerHTML = resultado;
} // fecha fun��o calcular_planilha_projeto()



/**
 * Fun��o para escolher Artigo 18 e desabilitar Artigo 26
 */

function escolherArt_18()
{
	// recebe os campos do formul�rio
    stArtigo18 = "";
    for (i = 0; i < document.form.stArtigo18.length; i++)
    {
        if (document.form.stArtigo18[i].checked)
        {
        	stArtigo18 = document.form.stArtigo18[i];
        }
    }
    stArtigo26 = "";
    for (i = 0; i < document.form.stArtigo26.length; i++)
    {
        if (document.form.stArtigo26[i].checked)
        {
        	stArtigo26 = document.form.stArtigo26[i];
        }
    }
    dsAlineaArtigo18 = document.form.dsAlineaArtigo18;
    AlineaArt18      = document.form.AlineaArt18.value;

    if (stArtigo18.value == 1)
    {
    	document.form.stArtigo26[0].checked = false;
    	document.form.stArtigo26[1].checked = true;
    	dsAlineaArtigo18.disabled = false;
    	dsAlineaArtigo18.value = AlineaArt18;
    }
    else
    {
    	document.form.stArtigo26[0].checked = true;
    	document.form.stArtigo26[1].checked = false;
    	dsAlineaArtigo18.disabled = true;
    	dsAlineaArtigo18.value = '';
    }
} // fecha fun��o escolherArt_18()



/**
 * Fun��o para escolher Artigo 26 e desabilitar Artigo 18
 */

function escolherArt_26()
{
	// recebe os campos do formul�rio
    stArtigo18 = "";
    for (i = 0; i < document.form.stArtigo18.length; i++)
    {
        if (document.form.stArtigo18[i].checked)
        {
        	stArtigo18 = document.form.stArtigo18[i];
        }
    }
    stArtigo26 = "";
    for (i = 0; i < document.form.stArtigo26.length; i++)
    {
        if (document.form.stArtigo26[i].checked)
        {
        	stArtigo26 = document.form.stArtigo26[i];
        }
    }
    dsAlineaArtigo18 = document.form.dsAlineaArtigo18;
    AlineaArt18      = document.form.AlineaArt18.value;

    if (stArtigo26.value == 1)
    {
    	document.form.stArtigo18[0].checked = false;
    	document.form.stArtigo18[1].checked = true;
    	dsAlineaArtigo18.disabled = true;
    	dsAlineaArtigo18.value = '';
    }
    else
    {
    	document.form.stArtigo18[0].checked = true;
    	document.form.stArtigo18[1].checked = false;
    	dsAlineaArtigo18.disabled = false;
    	dsAlineaArtigo18.value = AlineaArt18;
    }
} // fecha fun��o escolherArt_26()



/**
 * Fun��o para desabilitar todos os campos caso a op��o selecionada seja Não
 */

function escolherLei_8313()
{
	// recebe os campos do formulario
	stLei8313 = "";
    for (i = 0; i < document.form.stLei8313.length; i++)
    {
        if (document.form.stLei8313[i].checked)
        {
        	stLei8313 = document.form.stLei8313[i].value;
        }
    }
    stArtigo3              = document.form.stArtigo3;
    nrIncisoArtigo3        = document.form.nrIncisoArtigo3;
    dsAlineaArt3           = document.form.dsAlineaArt3;
    stArtigo18             = document.form.stArtigo18;
    dsAlineaArtigo18       = document.form.dsAlineaArtigo18;
    stArtigo26             = document.form.stArtigo26;
    stLei5761              = document.form.stLei5761;
    stArtigo27             = document.form.stArtigo27;
    stIncisoArtigo27_I     = document.form.stIncisoArtigo27_I;
    stIncisoArtigo27_II    = document.form.stIncisoArtigo27_II;
    stIncisoArtigo27_III   = document.form.stIncisoArtigo27_III;
    stIncisoArtigo27_IV    = document.form.stIncisoArtigo27_IV;
    stAvaliacao            = document.form.stAvaliacao;
    dsAvaliacao            = document.form.dsAvaliacao;

    // desabilita os campos
    if (stLei8313 == '0')
    {
    	stArtigo3[0].disabled          = true;
    	stArtigo3[1].disabled          = true;
        nrIncisoArtigo3.disabled       = true;
        dsAlineaArt3.disabled          = true;
        stArtigo18[0].disabled         = true;
        stArtigo18[1].disabled         = true;
        dsAlineaArtigo18.disabled      = true;
        stArtigo26[0].disabled         = true;
        stArtigo26[1].disabled         = true;
        stLei5761[0].disabled          = true;
        stLei5761[1].disabled          = true;
        stArtigo27[0].disabled         = true;
        stArtigo27[1].disabled         = true;
        stIncisoArtigo27_I.disabled    = true;
        stIncisoArtigo27_II.disabled   = true;
        stIncisoArtigo27_III.disabled  = true;
        stIncisoArtigo27_IV.disabled   = true;
        stAvaliacao[0].disabled        = true;
        stAvaliacao[1].disabled        = true;
        //dsAvaliacao.disabled           = true;
    }
    // habilita os campos
    if (stLei8313 == '1')
    {
    	stArtigo3[0].disabled          = false;
    	stArtigo3[1].disabled          = false;
        nrIncisoArtigo3.disabled       = false;
        dsAlineaArt3.disabled          = false;
        stArtigo18[0].disabled         = false;
        stArtigo18[1].disabled         = false;
        dsAlineaArtigo18.disabled      = false;
        stArtigo26[0].disabled         = false;
        stArtigo26[1].disabled         = false;
        stLei5761[0].disabled          = false;
        stLei5761[1].disabled          = false;
        stArtigo27[0].disabled         = false;
        stArtigo27[1].disabled         = false;
        stIncisoArtigo27_I.disabled    = false;
        stIncisoArtigo27_II.disabled   = false;
        stIncisoArtigo27_III.disabled  = false;
        stIncisoArtigo27_IV.disabled   = false;
        stAvaliacao[0].disabled        = false;
        stAvaliacao[1].disabled        = false;
        dsAvaliacao.disabled           = false;
    }
} // escolherLei_8313()



/**
 * Fun��o para limpar os campos caso a op��o selecionada seja Não
 */

function escolherArtigo3()
{
	// recebe os campos do formulario
	stArtigo3 = "";
	for (i = 0; i < document.form.stArtigo3.length; i++)
	{
		if (document.form.stArtigo3[i].checked)
		{
			stArtigo3 = document.form.stArtigo3[i].value;
		}
	}

	inciso_artigo3 = document.getElementById('inciso_artigo3');
	dsAlineaArt3   = document.getElementById('dsAlineaArt3').value;

	// limpa os campos
	if (stArtigo3 == '0')
	{
		inciso_artigo3.options[0].selected = true;
		document.getElementById('dsAlineaArt3').value = "";
	}

} // escolherArtigo3()



/**
 * Fun��o para limpar os campos caso a op��o selecionada seja Não
 */

function escolherDecreto5761()
{
	// recebe os campos do formulario
	stLei5761 = "";
    for (i = 0; i < document.form.stLei5761.length; i++)
    {
        if (document.form.stLei5761[i].checked)
        {
        	stLei5761 = document.form.stLei5761[i].value;
        }
    }
    stArtigo27             = document.form.stArtigo27;
    stIncisoArtigo27_I     = document.form.stIncisoArtigo27_I;
    stIncisoArtigo27_II    = document.form.stIncisoArtigo27_II;
    stIncisoArtigo27_III   = document.form.stIncisoArtigo27_III;
    stIncisoArtigo27_IV    = document.form.stIncisoArtigo27_IV;

    // desabilita os campos
    if (stLei5761 == '0')
    {
        stArtigo27[0].disabled         = true;
        stArtigo27[1].disabled         = true;
        stIncisoArtigo27_I.disabled    = true;
        stIncisoArtigo27_II.disabled   = true;
        stIncisoArtigo27_III.disabled  = true;
        stIncisoArtigo27_IV.disabled   = true;
    }
    // habilita os campos
    if (stLei5761 == '1')
    {
        stArtigo27[0].disabled         = false;
        stArtigo27[1].disabled         = false;
        stIncisoArtigo27_I.disabled    = false;
        stIncisoArtigo27_II.disabled   = false;
        stIncisoArtigo27_III.disabled  = false;
        stIncisoArtigo27_IV.disabled   = false;
    }
} // escolherDecreto5761()

//-->
