<?php
/**
 * Motivos de retirada de pauta
 * @author emanuel.sampaio - XTI
 * @since 24/01/2012
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2012 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_MotivoRetirarDePauta
{
    /**
     * Método com os motivos de retirada de pauta
     * @access public
     * @param string $tp
     * @return string
     */
    public function motivoRetirarDePauta($tp)
    {
        $tp = trim($tp);

        if ($tp == '1') {
            $ds = 'Devolver para vinculada';
        } elseif ($tp == '2') {
            $ds = 'Consultoria da CONJUR';
        } elseif ($tp == '3') {
            $ds = 'Mudança de Área/Segmento';
        } else {
            $ds = 'Outras';
        }

        return $ds;
    } // fecha método motivoRetirarDePauta()
} // fecha class
