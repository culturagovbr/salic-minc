<?php
/**
 * Helper para verificar a situação do projeto
 * @author Equipe RUP - Politec
 * @since 16/09/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_VerificarSituacaoProjeto
{
    /**
     * Método para verificar a situação do projeto
     * @access public
     * @param integer $idPronac
     * @return string
     */
    public function verificarSituacaoProjeto($idPronac = null)
    {
        if (isset($idPronac) && !empty($idPronac)) :

            $Projeto = new Projetos();

        // busca a situação do projeto
        $buscarSituacao = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        if (count($buscarSituacao) > 0) :
                return $buscarSituacao->Situacao; else :
                return 0;
        endif; else :
            return 0;
        endif;
    } // fecha método verificarSituacaoProjeto()
} // fecha class
