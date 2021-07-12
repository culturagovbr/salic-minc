<?php
/**
 * Verifica o status da Análise na Readequa��o
 * @author emanuel.sampaio - Politec
 * @since 23/09/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_VerificarStatusAnalise
{
    /**
     * M�todo com os status da Análise
     * @access public
     * @param string $dtEnvio
     * @return string
     */
    public function verificarStatusAnalise($dtEnvio)
    {
        $qtdDias = round(Data::CompararDatas($dtEnvio, date("Y-m-d")))+1;
        $cor     = "";
        $alt     = "";

        if ($qtdDias < 10) {
            $cor = 'verde';
            $alt = 'Menos de 10 dias de atraso no recebimento da Solicitação (data inicial)';
        } elseif ($qtdDias >= 10 && $qtdDias < 20) {
            $cor = 'amarelo';
            $alt = 'Entre 10 e 19 dias de atraso no recebimento da Solicitação (data inicial)';
        } elseif ($qtdDias >= 20) {
            $cor = 'vermelho';
            $alt = '20 ou mais dias de atraso no recebimento da Solicitação (data inicial)';
        }

        if (!empty($cor) && !empty($alt)) {
            $img = '<img src="' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/public/img/bola_' . $cor . '.gif" alt="' . $alt . '" title="' . $alt . '" />';
            return $img;
        } else {
            return '&nbsp;';
        }
    } // fecha m�todo verificarStatusAnalise()
} // fecha class
