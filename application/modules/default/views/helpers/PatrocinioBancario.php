<?php
/**
 * Nomes dos tipos de Patrocinio Bancario
 * @author Equipe RUP - Politec
 * @since 19/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright © 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_PatrocinioBancario
{
    /**
     * Método com os tipos de Patrocinio Bancario
     * @access public
     * @param integer $tipo
     * @return string $dsTipo
     */
    public function patrocinioBancario($tipo)
    {
        if ($tipo == '2') {
            $dsTipo = "Doação";
        } else {
            $dsTipo = "Patrocínio";
        }

        return $dsTipo;
    } // fecha método patrocinioBancario()
} // fecha class
