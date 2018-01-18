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

class Zend_View_Helper_TipoAprovacao
{
    /**
     * Mщtodo com a descriчуo dos tipos de parecer
     * @access public
     * @param string $parecer
     * @return string $descricao
     */
    public function tipoAprovacao($parecer)
    {
        if ($parecer == 'AC') {
            $descricao = "Aprovado pelo Componente";
        } elseif ($parecer == 'IC') {
            $descricao = "Indeferido pelo Componente";
        } elseif ($parecer == 'AS') {
            $descricao = "Aprovado pela CNIC";
        } elseif ($parecer == 'IS') {
            $descricao = "Indeferido pela CNIC";
        } elseif ($parecer == 'AR') {
            $descricao = "Aprovado por AD-REFERENDUM";
        }
        return $descricao;
    } // fecha mщtodo tipoParecer()
} // fecha class
