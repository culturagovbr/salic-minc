<?php
/**
 * Nomes dos tipos de Inconsist�ncias Banc�rias
 * @author Equipe RUP - Politec
 * @since 11/02/2010
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright � 2011 - Minist&eacute;rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_InconsistenciaBancaria
{
    /**
     * M�todo com os tipos de inconsist�ncias banc�rias
     * @access public
     * @param integer $tipo
     * @return string $dsTipo
     */
    public function inconsistenciaBancaria($tipo)
    {
        if ($tipo == 1) {
            $dsTipo = "O Per�odo de Execu��o Não est� vigente.";
        } elseif ($tipo == 2) {
            $dsTipo = "O Per�odo de Capta��o Não est� vigente.";
        } elseif ($tipo == 3) {
            $dsTipo = "Incentivador Não cadastrado.";
        } elseif ($tipo == 4) {
            $dsTipo = "Tipo de Dep�sito Não foi informado.";
        } elseif ($tipo == 5) {
            $dsTipo = "Não foi poss�vel encontrar o E-mail do Proponente.";
        } elseif ($tipo == 6) {
            $dsTipo = "Proponente Não cadastrado.";
        } elseif ($tipo == 7) {
            $dsTipo = "Ag�ncia e Conta Banc�ria Não cadastrada.";
        } elseif ($tipo == 8) {
            $dsTipo = "O Projeto Não possui Enquadramento.";
        } elseif ($tipo == 9) {
            $dsTipo = "Não existe Projeto associado a Conta.";
        } else {
            $dsTipo = " ";
        }

        return $dsTipo;
    } // fecha m�todo inconsistenciaBancaria()
} // fecha class
