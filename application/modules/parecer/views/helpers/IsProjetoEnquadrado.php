<?php
/**
 * Helper para verificar se o projeto já foi enquadrado
 */

class Zend_View_Helper_IsProjetoEnquadrado
{
    /**
     * Método para verificar se o projeto já foi enquadrado
     * @access public
     * @param integer $idPronac
     * @return string
     */
    public function IsProjetoEnquadrado($idPronac)
    {
        $enquadramentoDAO 		= new Admissibilidade_Model_Enquadramento();
        $buscaEnquadramento 	= $enquadramentoDAO->buscarDados($idPronac, null, false);
        $countEnquadramento 	= count($buscaEnquadramento);
        
        if (count($countEnquadramento) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
