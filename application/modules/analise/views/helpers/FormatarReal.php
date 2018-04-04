<?php
/**
 * Classe para formatar em moeda real
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_FormatarReal
{
    /**
     * Formatar moeda em real
     * @access public
     * @param string $moeda
     * @return void
     */
    public function formatarReal($moeda)
    {
        $moeda = number_format($moeda, 2, ',', '.');

        return $moeda;
    } // fecha método formatarReal()
} // fecha class
