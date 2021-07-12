
/**
 * Fun��o para escolher Artigo 18 e desabilitar Artigo 26
 */

function APPescolherArt_18()
{
	// recebe os campos do formAnaliseConteudoul�rio
    var Artigo18 = null;
    for (i = 0; i < document.formAnaliseConteudo.Artigo18.length; i++)
    {
        if (document.formAnaliseConteudo.Artigo18[i].checked)
        {
        	Artigo18 = document.formAnaliseConteudo.Artigo18[i];
        }
    }

    var Artigo26 = null;
    for (i = 0; i < document.formAnaliseConteudo.Artigo26.length; i++)
    {
        if (document.formAnaliseConteudo.Artigo26[i].checked)
        {
        	Artigo26 = document.formAnaliseConteudo.Artigo26[i];
        }
    }
    var AlineaArtigo18 = document.formAnaliseConteudo.AlineaArtigo18;
    var AlineaArt18    = document.formAnaliseConteudo.AlineaArt18.value;

    if(Artigo18 != null){
        if (Artigo18.value == '1')
        {
            document.formAnaliseConteudo.Artigo26[0].checked = false;
            document.formAnaliseConteudo.Artigo26[1].checked = true;
            AlineaArtigo18.disabled = false;
            AlineaArtigo18.value = AlineaArt18;
        }
        else
        {
            document.formAnaliseConteudo.Artigo26[0].checked = true;
            document.formAnaliseConteudo.Artigo26[1].checked = false;
            AlineaArtigo18.disabled = true;
            AlineaArtigo18.value = '';
        }
    }
} // fecha fun��o escolherArt_18()



/**
 * Fun��o para escolher Artigo 26 e desabilitar Artigo 18
 */

function APPescolherArt_26()
{
	// recebe os campos do formAnaliseConteudoul�rio
    var Artigo18 = null;
    for (i = 0; i < document.formAnaliseConteudo.Artigo18.length; i++)
    {
        if (document.formAnaliseConteudo.Artigo18[i].checked)
        {
        	Artigo18 = document.formAnaliseConteudo.Artigo18[i];
        }
    }
    var Artigo26 = null;
    for (i = 0; i < document.formAnaliseConteudo.Artigo26.length; i++)
    {
        if (document.formAnaliseConteudo.Artigo26[i].checked)
        {
        	Artigo26 = document.formAnaliseConteudo.Artigo26[i];
        }
    }
    var AlineaArtigo18 	 = document.formAnaliseConteudo.AlineaArtigo18;
    var AlineaArt18      = document.formAnaliseConteudo.AlineaArt18.value;
    if(Artigo26 != null){
        if (Artigo26.value == '1')
        {
            document.formAnaliseConteudo.Artigo18[0].checked = false;
            document.formAnaliseConteudo.Artigo18[1].checked = true;
            AlineaArtigo18.disabled = true;
            AlineaArtigo18.value = '';
        }
        else
        {
            document.formAnaliseConteudo.Artigo18[0].checked = true;
            document.formAnaliseConteudo.Artigo18[1].checked = false;
            AlineaArtigo18.disabled = false;
            AlineaArtigo18.value = AlineaArt18;
        }
    }
} // fecha fun��o escolherArt_26()



/**
 * Fun��o para desabilitar todos os campos caso a op��o selecionada seja Não
 */

function APPescolherLei_8313()
{
    // recebe os campos do formAnaliseConteudoulario
    var Lei8313 = null;
    for (i = 0; i < document.formAnaliseConteudo.Lei8313.length; i++)
    {
        if (document.formAnaliseConteudo.Lei8313[i].checked)
        {
        	Lei8313 = document.formAnaliseConteudo.Lei8313[i].value;
        }
    }
    stArtigo3              = document.formAnaliseConteudo.Artigo3;
    nrIncisoArtigo3        = document.formAnaliseConteudo.IncisoArtigo3;
    dsAlineaArt3           = document.formAnaliseConteudo.AlineaArtigo3;
    stArtigo18             = document.formAnaliseConteudo.Artigo18;
    dsAlineaArtigo18       = document.formAnaliseConteudo.AlineaArtigo18;
    stArtigo26             = document.formAnaliseConteudo.Artigo26;
    stLei5761              = document.formAnaliseConteudo.Lei5761;
    stArtigo27             = document.formAnaliseConteudo.Artigo27;
    stIncisoArtigo27_I     = document.formAnaliseConteudo.IncisoArtigo27_I;
    stIncisoArtigo27_II    = document.formAnaliseConteudo.IncisoArtigo27_II;
    stIncisoArtigo27_III   = document.formAnaliseConteudo.IncisoArtigo27_III;
    stIncisoArtigo27_IV    = document.formAnaliseConteudo.IncisoArtigo27_IV;
    stAvaliacao            = document.formAnaliseConteudo.ParecerFavoravel;
    dsAvaliacao            = document.formAnaliseConteudo.ParecerDeConteudo;

    // desabilita os campos
    if (Lei8313 == '0')
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
    if (Lei8313 == '1')
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

function APPescolherArtigo3()
{
	// recebe os campos do formAnaliseConteudoulario
	var Artigo3 = null;
	for (i = 0; i < document.formAnaliseConteudo.Artigo3.length; i++)
	{
		if (document.formAnaliseConteudo.Artigo3[i].checked)
		{
			Artigo3 = document.formAnaliseConteudo.Artigo3[i].value;
		}
	}

	inciso_artigo3 = document.getElementById('IncisoArtigo3');
	dsAlineaArt3   = document.getElementById('AlineaArtigo3').value;

	// limpa os campos
	if (Artigo3 == '0')
	{
		inciso_artigo3.options[0].selected = true;
		document.getElementById('AlineaArtigo3').value = "";
	}

} // escolherArtigo3()



/**
 * Fun��o para limpar os campos caso a op��o selecionada seja Não
 */

function APPescolherDecreto5761()
{
    // recebe os campos do formAnaliseConteudoulario
    var Lei5761 = "";
    for (i = 0; i < document.formAnaliseConteudo.Lei5761.length; i++)
    {
        if (document.formAnaliseConteudo.Lei5761[i].checked)
        {
        	Lei5761 = document.formAnaliseConteudo.Lei5761[i].value;
        }
    }
    stArtigo27             = document.formAnaliseConteudo.Artigo27;
    stIncisoArtigo27_I     = document.formAnaliseConteudo.IncisoArtigo27_I;
    stIncisoArtigo27_II    = document.formAnaliseConteudo.IncisoArtigo27_II;
    stIncisoArtigo27_III   = document.formAnaliseConteudo.IncisoArtigo27_III;
    stIncisoArtigo27_IV    = document.formAnaliseConteudo.IncisoArtigo27_IV;

    // desabilita os campos
    if (Lei5761 == '0')
    {
        stArtigo27[0].disabled         = true;
        stArtigo27[1].disabled         = true;
        stIncisoArtigo27_I.disabled    = true;
        stIncisoArtigo27_II.disabled   = true;
        stIncisoArtigo27_III.disabled  = true;
        stIncisoArtigo27_IV.disabled   = true;
    }
    // habilita os campos
    if (Lei5761 == '1')
    {
        stArtigo27[0].disabled         = false;
        stArtigo27[1].disabled         = false;
        stIncisoArtigo27_I.disabled    = false;
        stIncisoArtigo27_II.disabled   = false;
        stIncisoArtigo27_III.disabled  = false;
        stIncisoArtigo27_IV.disabled   = false;
    }
} // escolherDecreto5761()
