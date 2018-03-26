<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of StatusLocalRealizacaoProjeto
 *
 * @author 01129075125
 */
class Zend_View_Helper_StatusSituacaoProjeto
{
    public function StatusSituacaoProjeto($status)
    {
        switch ($status) {
            case 'E10': $status = "E10 - Aguarda Captaчуo de Recursos";
            // no break
            case 'E11': $status = "E11 - Encerrado Prazo de Captaчуo";
            // no break
            case 'E12': $status = "E12 - Captaчуo Parcial";
            // no break
            case 'E13': $status = "E13 - Encerrado prazo de captaчуo - Projeto em execuчуo";
            // no break
            case 'E15': $status = "E15 - Encerrado prazo de prestaчуo de contas";
            // no break
            case 'E16': $status = "E16 - Encerrado prazo/captaчуo - Proponente inabilitado";
        }
        return  $status;
    }
}
