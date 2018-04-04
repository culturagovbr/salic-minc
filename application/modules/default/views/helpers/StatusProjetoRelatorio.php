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
 
class Zend_View_Helper_StatusProjetoRelatorio
{
    /**
     * Mщtodo com a descriчуo dos tipos de parecer
     * @access public
     * @param string $parecer
     * @return string $descricao
     */
    public function statusProjetoRelatorio($status)
    {
        if ($status == 1) {
            $valor = 'N&atilde;o Analisado';
        }
        if ($status == 2) {
            $valor = 'Em An&aacute;lise';
        }
        if ($status == 3) {
            $valor = 'Analisado';
        }
        return $valor;
    } // fecha mщtodo tipoParecer()
} // fecha class
