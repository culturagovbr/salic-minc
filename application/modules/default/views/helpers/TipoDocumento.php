<?php
/**
 * Nomes dos status da comprovação física do projeto
 * @author Equipe RUP - Politec
 * @since 14/05/2010
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_TipoDocumento
{
    /**
     * Método com os status dos comprovantes de status do projeto
     * @access public
     * @param string $status
     * @return string $nomeStatus
     */
    public function tipoDocumento($status)
    {
        switch ($status) {
                    case 1:
                        $status = 'Boleto Bancário';
                        break;
                    case 2:
                        $status = 'Cupom Fiscal';
                        break;
                    case 3:
                        $status = 'Nota fiscal/Fatura';
                        break;
                    case 4:
                        $status = 'Recibo de Pagamento Autônomo';
                        break;
                    default:
                        $status = '-';
                        break;
                }

        return $status;
    } // fecha método statusComprovacaoFisicaDoProjeto()
} // fecha class
