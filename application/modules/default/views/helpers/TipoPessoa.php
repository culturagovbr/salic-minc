<?php
/**
 * Cores alternativas dos registros de uma tabela (cor sim, cor não)
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_TipoPessoa
{
    /**
     * Método com as cores alternativas das linhas da tabela
     * @access public
     * @param integer $i
     * @return string
     */
    public function tipopessoa($tipopessoa)
    {
        if ($tipopessoa == 1) {
            $bg = "Pessoa Física";
        } else {
            $bg = "Pessoa Júridica";
        }

        return $bg;
    } // fecha método corLinha()
} // fecha class
