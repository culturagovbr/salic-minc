<?php
/**
 * Descrição dos tipos de parecer da An&aacute;lise do projeto
 * @author Equipe RUP - Politec
 * @since 14/06/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2010 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_TipoAprovacao
{
    /**
     * M�todo com a Descrição dos tipos de parecer
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
    } // fecha m�todo tipoParecer()
} // fecha class
