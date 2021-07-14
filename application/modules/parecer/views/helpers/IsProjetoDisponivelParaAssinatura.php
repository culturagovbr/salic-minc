<?php
/**
 * Helper para verificar se projeto est&aacute; dispon&iacute;vel para assinatura
 */

class Zend_View_Helper_IsProjetoDisponivelParaAssinatura
{
    /**
     * M&eacute;todo para verificar se o projeto est&aacute; dispon&iacute;vel para assinatura
     * @access public
     * @param integer $idPronac
     * @param integer $idTipoDoAtoAdministrativo
     * @return string
     */
    public function IsProjetoDisponivelParaAssinatura($idPronac, $idTipoDoAtoAdministrativo)
    {
        $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $isProjetoDisponivelParaAssinatura = $objModelDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
            $idPronac,
            $idTipoDoAtoAdministrativo
        );

        return $isProjetoDisponivelParaAssinatura;
    }
}
