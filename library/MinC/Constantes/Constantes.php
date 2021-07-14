<?php

/**
* Prove constantes que encapsulam recursos utilizados pelo sistema. O
* objetivo desta classe � diminuir o uso de strings literais durante a
* programacao do sistema, facilitando o entendimento do codigo
* especialmente durante manutencoes.
* @author Marcos Rodrigo Ribeiro
* @version  <pre>
*   <b>1.0</b>
*    Data: 22/05/2012
*    Autor: Marcos Rodrigo Ribeiro
*    Descricao: Versao inicial.
*   <hr>
* </pre>
* @name Constantes()
* @access public
*/

class Constantes {

        /* =========================================================================================================== */
	/* ================== CódigoS (ID's) REFERENTE � TABELA TIPO (SAC.dbo.Tipo) ================================== */
        /* =========================================================================================================== */

        /*=== valor do campo ID da tabela SAC.dbo.Tipo que referencia ao registro "Termo de "Decisao" === */
	const cteIdTipoTermoDecisao = 19; //(no Minc (em homologacao) o valor e 19)

        /* =========================================================================================================== */
	/* ================== CódigoS (ID's) REFERENTE � TABELA VERIFICACAO (SAC.dbo.Verificacao) ==================== */
        /* =========================================================================================================== */
	const cteIdVerificacaoTipoTermoAnaliseInicial   = 407; //(no Minc (em homologacao) o valor e 352) /*=== valor do campo ID da tabela SAC.dbo.Verificacao que referencia o registro "Inicial" (Tipo de termo de decisao) === */
	const cteIdVerificacaoTipoTermoReadequacao      = 408; //(no Minc (em homologacao) o valor e 353) /*=== valor do campo ID da tabela SAC.dbo.Verificacao que referencia o registro "Readequa��o" (Tipo de termo de decisao) === */
	const cteIdVerificacaoTipoRecurso               = 409; //(no Minc (em homologacao) o valor e 354) /*=== valor do campo ID da tabela SAC.dbo.Verificacao que referencia o registro "Recurso" (Tipo de termo de decisao) === */

	private function __construct()
	{
		parent::__constructor();
	}
}
