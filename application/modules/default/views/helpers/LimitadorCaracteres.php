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

class Zend_View_Helper_LimitadorCaracteres
{
    /**
     * Metodo que limita a quantidade dos carateres.
     * @access public
     * @param Varchar $string
     * @return string
     */
    public function LimitadorCaracteres($string)
    {
        if (strlen($string) > 0) {
            $string = substr($string, 0, 80);
            if (strlen($string) >=  80) {
                $string = $string.' ...';
            }
        } else {
            $string = '';
        }

        return $string;
    } // fecha método corLinha()
} // fecha class
