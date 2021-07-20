<?php
/**
 * Tipos de status da Análise para Verificar a Readequa&ccedil;&atilde;o
 * @author emanuel.sampaio - Politec
 * @since 30/08/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2011 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_StatusAnalise
{
    /**
     * M�todo com os status da Análise
     * @access public
     * @param string $cor
     * @return string
     */
    public function statusAnalise($cor)
    {
        $cor = trim($cor);

        if ($cor == 'vermelho') {
            $ds = '20 dias de atraso no recebimento da Solicitação (data inicial)';
        } elseif ($cor == 'amarelo') {
            $ds = '>= 10 e < 20 dias de atraso no recebimento da Solicitação (data inicial)';
        } elseif ($cor == 'verde') {
            $ds = '< 10 dias de atraso no recebimento da Solicitação (data inicial)';
        } else {
            $ds = ' ';
        }

        return $ds;
    } // fecha m�todo statusAnalise()
} // fecha class
