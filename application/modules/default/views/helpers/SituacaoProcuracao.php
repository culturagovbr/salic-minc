<?php
/**
 * Situa��o da procura��o
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_SituacaoProcuracao
{
    /**
     * Informa a situa��o da procura��o
     * @access public
     * @param integer $stvinculo
     * @return string
     */
    public function situacaoProcuracao($stvinculo)
    {
        if ($stvinculo == 0) {
            $return =  "Aguardando Análise";
        }
        if ($stvinculo == 1) {
            $return =  "Aprovado";
        }
        if ($stvinculo == 2) {
            $return =  "Rejeitado";
        }
        return $return;
    }
}
