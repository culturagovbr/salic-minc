<?php
/**
 * Nomes dos status da comprovação física do projeto
 * @author Equipe RUP - Politec
 * @since 14/05/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_StatusCompFisicaProjeto
{
    /**
     * Método com os status dos comprovantes do projeto
     * @access public
     * @param string $status
     * @return string $nomeStatus
     */
    public function statusCompFisicaProjeto($status)
    {
        if ($status == 'AG') {
            $nomeStatus = "Aguardando Avaliação";
        } elseif ($status == 'AV') {
            $nomeStatus = "Em Avaliação";
        } elseif ($status == 'EA') {
            $nomeStatus = "Em Aprovação";
        } elseif ($status == 'AD') {
            $nomeStatus = "Avaliado - Deferido";
        } elseif ($status == 'AI') {
            $nomeStatus = "Avaliado - Indeferido";
        } elseif ($status == 'CS') {
            $nomeStatus = "Comprovante Substituído";
        } else {
            $nomeStatus = "Avaliado";
        }

        return $nomeStatus;
    } // fecha método statusCompFisicaProjeto()
} // fecha class
