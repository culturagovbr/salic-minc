<?php

/**
* Classe do componente de Pessoa Fisica que gerencia a
* comunicacao via webservice entre o NovoSalic e o sistema da receita federal
*
* @copyright Ministério da Cultura
* @author Politec/Minc - Everton Guilherme
* @since 21/06/2010
* @version 1.0
*/
	class Utils_Wspf {

		# Constante usada na classe para conexao com o WS
		const CAMINHO_WSDL_PF 		= "http://localhost/PessoaFisica/wsdl - exemplo - esperando a definicao da receita federal";

		# Atributos da classe
		private static $objSoapCliente;

		/**
		* Funcao que verifica que possui uma conexao com o WS
		*
		* @param INTEGER $objSoapCliente
		* @return MIX
		*/
		private function getSoapClient( )
		{
			if ( is_null( self::$objSoapCliente ) )
			{
				try
				{
					# Instanciando a classe que conecta ao WebService
					$objSoapCliente = new SoapClient( self::CAMINHO_WSDL_PF );
				}
				catch ( Exception $objException )
				{
					# Retorna mensagem de erro
					return ( $objException->getMessage() );
				}
				self::$objSoapCliente = $objSoapCliente;
			}
			return ( self::$objSoapCliente );
		}

		/**
		 * Carrega o dados resumidos da pessoa fisica pelo CPF
		 *
		 * @param INTEGER $intNuCpf
		 * @return MIX
		 */
		public function solicitarDadosResumidoPessoaFisicaPorCpf( $intNuCpf = NULL )
		{
			# Verificando se possui 11 digitos
			$intNuCpf = str_pad( $intNuCpf , 11 , "0" , STR_PAD_LEFT );
			if ( ( is_null( $intNuCpf ) ) || ( empty( $intNuCpf ) ) || ( $intNuCpf == " " ) || ( $intNuCpf == "" ) ) $intNuCpf = 0;
			$objSoapCliente = self::getSoapClient();
			$mixResult = ( is_null( $intNuCpf ) ) ? NULL : $objSoapCliente->solicitarDadosResumidoPessoaFisicaPorCpf( $intNuCpf );
			if(is_null($mixResult)) return $mixResult;
	        else return ( self::convertXmlToArray( "/*" , $mixResult ) );
		}

		/**
		 * Carrega o dados da pessoa fisica pelo CPF
		 *
		 * @param STRING $strSgUf
		 * @return MIX
		 */
		public function solicitarDadosPessoaFisicaPorCpf( $intNuCpf = NULL )
		{
			# Verificando se possui 11 digitos
			$intNuCpf = str_pad( $intNuCpf , 11 , "0" , STR_PAD_LEFT );
			if ( ( is_null( $intNuCpf ) ) || ( empty( $intNuCpf ) ) || ( $intNuCpf == " " ) || ( $intNuCpf == "" ) ) $intNuCpf = 0;
			$objSoapCliente = self::getSoapClient();
			$mixResult = ( is_null( $intNuCpf ) ) ? NULL : $objSoapCliente->solicitarDadosPessoaFisicaPorCpf( $intNuCpf );
			if(is_null($mixResult)) return $mixResult;
	           else return ( self::convertXmlToArray( "/*" , $mixResult ) );
		}

		/**
		 * Carrega o dados de endereco da pessoa fisica pelo CPF
		 *
		 * @param STRING $strSgUf
		 * @return MIX
		 */
		public function solicitarDadosEnderecoPessoaFisicaPorCpf( $intNuCpf = NULL )
		{
			# Verificando se possui 11 digitos
			$intNuCpf = str_pad( $intNuCpf , 11 , "0" , STR_PAD_LEFT );
			if ( ( is_null( $intNuCpf ) ) || ( empty( $intNuCpf ) ) || ( $intNuCpf == " " ) || ( $intNuCpf == "" ) ) $intNuCpf = 0;
			$objSoapCliente = self::getSoapClient();
			$mixResult = ( is_null( $intNuCpf ) ) ? NULL : $objSoapCliente->solicitarDadosEnderecoPessoaFisicaPorCpf( $intNuCpf );
			if(is_null($mixResult)) return $mixResult;
	        else return ( self::convertXmlToArray( "/*" , $mixResult ) );
		}

		/**
		 * Carrega o dados de contatos da pessoa fisica pelo CPF
		 *
		 * @param STRING $strSgUf
		 * @return MIX
		 */
		public function solicitarDadosContatoPessoaFisicaPorCpf( $intNuCpf = NULL )
		{
			# Verificando se possui 11 digitos
			$intNuCpf = str_pad( $intNuCpf , 11 , "0" , STR_PAD_LEFT );
			if ( ( is_null( $intNuCpf ) ) || ( empty( $intNuCpf ) ) || ( $intNuCpf == " " ) || ( $intNuCpf == "" ) ) $intNuCpf = 0;
			$objSoapCliente = self::getSoapClient();
			$mixResult = ( is_null( $intNuCpf ) ) ? NULL : $objSoapCliente->solicitarDadosContatoPessoaFisicaPorCpf( $intNuCpf );
			if(is_null($mixResult)) return $mixResult;
	       else return ( self::convertXmlToArray( "/*" , $mixResult ) );
		}

		/**
		* Converte a string equivalente a um XML para array, ignorando os atributos dos nodos caso existam
	 	* @param STRING $strPathXml
		* @param STRING $strXml
		* @param DOMNode $objDOMNode
		* @return ARRAY
		*/
		private function convertXmlToArray( $strPathXml = "/*" , $strXml = "" , $objDOMNode = NULL )
		{
			# Variaveis estaticas devido a recursividade
			static $objDOMDocument;
			static $objDOMXPath;

			# Aplicando regex para somente trabalhar a partir de determinado caminho ou pelo root (/*)
			if ( ! ereg( "/\*$" , $strPathXml ) ) $strPathXml = ereg_replace( "/*$" , "" , $strPathXml ) . "/*";

			# Array de retorno
			$arrReturn = array();

		# Caso seja a primeira chamada da recursividade, instancia os objetos
		if ( $strXml )
		{
			$objDOMDocument = new DOMDocument;
			$objDOMDocument->loadXML( $strXml );
			$objDOMXPath = new DOMXPath( $objDOMDocument );
		}

		# Capturando um array com os devidos nodos do caminho atual
		$arrNode = ( $objDOMNode ) ? $objDOMXPath->query( $strPathXml , $objDOMNode ) : $objDOMXPath->query( $strPathXml );

		# Construindo array que armazenara o numero de ocorrencias de cada nome de nodo
		$arrOcorrencia = array();

			# Varrendo array de nodos
			foreach ( $arrNode AS $objNode )
			{
				$arrOcorrencia[ $objNode->nodeName ] = ( isset( $arrOcorrencia[ $objNode->nodeName ] ) ) ? ( $arrOcorrencia[ $objNode->nodeName ] + 1 ) : 0;
				if ( $objDOMXPath->evaluate( "count(./*)" , $objNode ) > 0 )
				{
					if ( $objDOMXPath->evaluate( "count(" . $objNode->nodeName . ")" , $objNode->parentNode ) > 1 )
					{
						$arrReturn[ utf8_decode( $objNode->nodeName ) ][ $arrOcorrencia[ $objNode->nodeName ] ] = self::convertXmlToArray( $objNode->nodeName . "[" . ( $arrOcorrencia[ $objNode->nodeName ] + 1 ) . "]" , "" , $objNode->parentNode );
					}
					else
					{
						$arrReturn[ utf8_decode( $objNode->nodeName ) ] = self::convertXmlToArray( $objNode->nodeName , "" , $objNode->parentNode );
					}
				}
				else
				{
					if ( $objDOMXPath->evaluate( "count(" . $objNode->nodeName . ")" , $objNode->parentNode ) > 1 )
					{
						$arrReturn[ utf8_decode( $objNode->nodeName ) ][ $arrOcorrencia[ $objNode->nodeName ] ] = utf8_decode( $objNode->nodeValue );
					}
					else
					{
						$arrReturn[ utf8_decode( $objNode->nodeName ) ] = utf8_decode( $objNode->nodeValue );
					}
				}
			}

			# Retorno
			return ( $arrReturn );
		}

		/**
		* Metodo chamado quando o objeto da classe e instanciado
		*
		* @return VOID
		*/
		public function __construct()
		{
			return;
		}

		/**
		* Metodo chamado quando o objeto da classe e serializado
		*
		* @return VOID
		*/
		public function __sleep()
		{
			return;
		}

		/**
		* Metodo chamado quando o objeto da classe e unserializado
		*
		* @return VOID
		*/
		public function __wakeup()
		{
			return;
		}

		/**
		* Caso o metodo nao seja encontrado
		*
		* @param STRING $strMethod
		* @param ARRAY $arrParameters
		* @return VOID
		*/
		public function __call( $strMethod , $arrParameters )
		{
			debug( "O metodo " . $strMethod . " nao foi encontrado na classe " . get_class( $this ) . ".<br />" . __FILE__ . "(linha " . __LINE__ . ")" , 1 );
		}

	} // end Utils_Wsdne
