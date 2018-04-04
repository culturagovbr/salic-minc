<?php
/**
 * Helper para verificar se o projeto é da IN2017
 * @since 11/07/2017
 * @version 1.0
 * @package default
 * @subpackage default.view.helpers
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_VerificarIN2017
{
    public function VerificarIN2017($idPronac)
    {
        $fnVerificarProjetoAprovadoIN2017 = new fnVerificarProjetoAprovadoIN2017();
        return $fnVerificarProjetoAprovadoIN2017->verificar($idPronac);
    }
}
