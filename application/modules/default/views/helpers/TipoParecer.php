<?php
/**
 * Descriчуo dos tipos de parecer da anсlise do projeto
 * @author Equipe RUP - Politec
 * @since 14/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright Љ 2010 - Ministщrio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_TipoParecer
{
    /**
     * Mщtodo com a descriчуo dos tipos de parecer
     * @access public
     * @param string $parecer
     * @return string $descricao
     */
    public function tipoParecer($parecer)
    {
        if ($parecer == 1) {
            $descricao = "Aprovaчуo Inicial";
        } elseif ($parecer == 2) {
            $descricao = "Complementaчуo";
        } elseif ($parecer == 3) {
            $descricao = "Prorrogaчуo";
        } elseif ($parecer == 4) {
            $descricao = "Reduчуo";
        } else {
            $descricao = "";
        }

        return $descricao;
    } // fecha mщtodo tipoParecer()
} // fecha class
