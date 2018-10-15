<?php
/**
 * Helper para verificar se projeto está disponível para assinatura
 */

class Zend_View_Helper_DisponivelParaAssinatura
{
    /**
     * Método para verificar se o projeto está disponível para assinatura
     * @access public
     * @param integer $siEncaminhamento
     * @param integer $idDocumentoAssinatura
     * @return string
     */
    public function disponivelParaAssinatura($siEncaminhamento, $idDocumentoAssinatura)
    {
        if (!$idDocumentoAssinatura) {
            return;
        }
        
        $listAvailable = [
            Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_AO_MINC,
            Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO
        ];
        
        return in_array($siEncaminhamento, $listAvailable);
    }
}
