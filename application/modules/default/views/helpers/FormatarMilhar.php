<?php
/**
 * Classe para formatar Milhar
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_FormatarMilhar
{
    /**
     * Formatar Milhar
     * @access public
     * @param string $valor
     * @return void
     */
    public function formatarMilhar($valor)
    {
        $valor = number_format($valor, 0, '.', '.');
        return $valor;
    } // fecha método formatarMilhar()
} // fecha class
