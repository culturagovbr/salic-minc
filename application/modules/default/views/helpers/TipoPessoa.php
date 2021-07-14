<?php
/**
 * Cores alternativas dos registros de uma tabela (cor sim, cor Não)
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2010 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_TipoPessoa
{
    /**
     * M�todo com as cores alternativas das linhas da tabela
     * @access public
     * @param integer $i
     * @return string
     */
    public function tipopessoa($tipopessoa)
    {
        if ($tipopessoa == 1) {
            $bg = "Pessoa F�sica";
        } else {
            $bg = "Pessoa J�ridica";
        }

        return $bg;
    } // fecha m�todo corLinha()
} // fecha class
