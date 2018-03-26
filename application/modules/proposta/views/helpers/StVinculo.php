<?php
/**
 * Cores alternativas dos registros de uma tabela (cor sim, cor não)
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Proposta_View_Helper_StVinculo
{
    /**
     * Método com as cores alternativas das linhas da tabela
     * @access public
     * @param integer $i
     * @return string
     */
    public function stVinculo($stvinculo)
    {
        if ($stvinculo == 0) {
            $return =  "Aguardando V&iacute;nculo";
        }
        if ($stvinculo == 1) {
            $return =  "Rejeitado";
        }
        if ($stvinculo == 2) {
            $return =  "Vinculado";
        }
        if ($stvinculo == 3) {
            $return =  "Desvinculado";
        }
        return $return;
    }
}
