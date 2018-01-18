<?php 
/**
 * Situação do Proponente
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright 2010 - Ministï¿½rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_ProponenteInabilitado
{
    /**
     * Informa a situação do Proponente
     * @access public
     * @param integer $cpf
     * @return boolean
     */
    public function proponenteInabilitado($cpf)
    {
        $inabilitadoDAO = new Inabilitado();
        
        $where['CgcCpf 		= ?'] = $cpf;
        $where['Habilitado 	= ?'] = 'N';
        $busca = $inabilitadoDAO->Localizar($where);
        
        if (count($busca) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
