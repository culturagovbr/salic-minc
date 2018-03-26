<?php
/**
 * Tipos de solicitaчѕes dos recursos
 * @author emanuel.sampaio - Politec
 * @since 12/03/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright Љ 2011 - Ministщrio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_TipoSolicitacaoRecurso
{
    /**
     * Mщtodo com os tipos de solicitaчѕes dos recursos
     * @access public
     * @param string $tp
     * @return string
     */
    public function tipoSolicitacaoRecurso($tp)
    {
        $tp = trim($tp);

        if ($tp == 'PI') {
            $ds = "Projeto Indeferido";
        } elseif ($tp == 'EN') {
            $ds = "Projeto Aprovado - Enquadramento";
        } elseif ($tp == 'OR') {
            $ds = "Projeto Aprovado - Orчamento";
        } elseif ($tp == 'PP') {
            $ds = "Prorrogaчуo de Prazo de Captaчуo";
        } elseif ($tp == 'PE') {
            $ds = "Prorrogaчуo de Prazo de Execuчуo";
        } elseif ($tp == 'PC') {
            $ds = "Prestaчуo de Contas";
        }

        return $ds;
    } // fecha mщtodo tipoSolicitacaoRecurso()
} // fecha class
