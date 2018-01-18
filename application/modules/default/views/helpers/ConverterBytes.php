<?php
/**
 * Conversão de Bytes
 * @author Equipe RUP - Politec
 * @since 19/05/2010
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_ConverterBytes
{
    /**
     * Método para converter os bytes
     * @access public
     * @param string $bytes
     * @param string $casas (casas decimais)
     * @return string
     */
    public function converterBytes($bytes, $casas = 2)
    {
        $unidades = array('', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $unidade  = 0;

        do {
            $bytes /= 1024;
            $unidade++;
        } while ($bytes > 1024);

        return sprintf("%1.{$casas}f%s", $bytes, $unidades[$unidade]);
    } // fecha método converterBytes()
} // fecha class
