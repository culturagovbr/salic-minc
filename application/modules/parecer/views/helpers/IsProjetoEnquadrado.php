<?php
/**
 * Helper para verificar se o projeto j&aacute; foi enquadrado
 */

class Zend_View_Helper_IsProjetoEnquadrado
{
    /**
     * M&eacute;todo para verificar se o projeto j&aacute; foi enquadrado
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
