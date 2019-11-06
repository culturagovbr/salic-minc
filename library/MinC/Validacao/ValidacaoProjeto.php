<?php
/**
 * Classe para realizar validações de campos especiais
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package library
 * @subpackage library.MinC.Validacao
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class ValidacaoProjeto
{
	/**
	 * Método para buscar o nível do Projeto
	 *
	 * @access public
	 * @static
	 * @param int $idPronac
	 * @return array
	 */
	public static function nivelProjeto($idPronac)
	{
		
		$dados = array('NvNumero' 				=> 1,
					   'NvRomano' 				=> 'I',
					   'VrPagamentoParecerista' => 122.00
		);
		
		
		$projetosDAO 	 = new Parecer_Model_DbTable_TbDistribuirParecer();
    	$PlanilhaDAO = new PlanilhaProjeto();
    	
    	$total = $PlanilhaDAO->valorTotalDoProjeto($idPronac);
		$areas = $projetosDAO->BuscarQtdAreasProjetos($idPronac);
		
		
		$totalAreas = $areas->QDTArea;
		$valorTotal = str_replace(",", ".", $total->valorTotal);  
		
		if(($valorTotal <= 100000.00) AND ($totalAreas <= 2)) 
		{				   
			$dados = array( 'NvNumero' 				 => 1,
					   		'NvRomano' 				 => 'I',
					   		'VrPagamentoParecerista' => 122.00
			);
		}
		else if(($valorTotal >= 100000.00) AND ($valorTotal <= 300000.00) AND ($totalAreas > 2)) 
		{
			$dados = array( 'NvNumero' 				 => 2,
					   		'NvRomano' 				 => 'II',
					   		'VrPagamentoParecerista' => 370.00
			);
		}
		else if(($valorTotal >= 300000.00) AND ($valorTotal <= 10000000.00)) 
		{
			$dados = array( 'NvNumero' 				 => 3,
					   		'NvRomano' 				 => 'III',
					   		'VrPagamentoParecerista' => 661.00
			);
		}
		else if($valorTotal > 10000000.00) 
		{
			$dados = array( 'NvNumero' 				 => 4,
					   		'NvRomano' 				 => 'IV',
					   		'VrPagamentoParecerista' => 1183.00
			);
		}
		else if($valorTotal > 50000000.00) 
		{
			$dados = array( 'NvNumero' 				 => 5,
					   		'NvRomano' 				 => 'V',
					   		'VrPagamentoParecerista' => 1649.00
			);
		}
		
		return $dados;
		
	} // fecha método nivelProjeto()




} // fecha class
