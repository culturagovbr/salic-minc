<?php
/**
 * Helper para verificar o proponente do projeto
 * @author Equipe RUP - Politec
 * @since 23/09/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2011 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_VerificarProponenteProjeto
{
    /**
     * M�todo para verificar o proponente do projeto
     * @access public
     * @param integer $idPronac
     * @return string
     */
    public function verificarProponenteProjeto($idPronac = null)
    {
        if (isset($idPronac) && !empty($idPronac)) :

            $Projeto = new Projetos();

        // busca a situa��o do projeto
        $buscarSituacao = $Projeto->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        if (count($buscarSituacao) > 0) :
                return $buscarSituacao->CgcCpf; else :
                return 0;
        endif; else :
            return 0;
        endif;
    } // fecha m�todo verificarProponenteProjeto()
} // fecha class
