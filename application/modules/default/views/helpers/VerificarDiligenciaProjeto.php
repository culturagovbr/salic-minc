<?php
/**
 * Helper para verificar a diligencia do projeto
 * @author Equipe RUP - Politec
 * @since 16/09/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_VerificarDiligenciaProjeto
{
    /**
     * Método para verificar a diligencia do projeto
     * @access public
     * @param integer $idPronac
     * @return string
     */
    public function verificarDiligenciaProjeto($idPronac = null)
    {
        if (isset($idPronac) && !empty($idPronac)) :

            $Diligencia = new Diligencia();

        // busca a situação do projeto
        $buscarDiligencia = $Diligencia->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        if (count($buscarDiligencia) > 0) :
                return $buscarDiligencia->idTipoDiligencia; else :
                return 0;
        endif; else :
            return 0;
        endif;
    } // fecha método verificarDiligenciaProjeto()
} // fecha class
