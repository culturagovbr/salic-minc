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
    public function disponivelParaAssinatura($siEncaminhamento, $idDocumentoAssinatura, $idPerfil)
    {
        if (!$idDocumentoAssinatura) {
            return;
        }

        $listAvailable = [
            Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO => [
                Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_AO_MINC,
                Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO
            ],
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO => [
                Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL,
                Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_SECRETARIO,
                Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_DE_PARECER_PELO_PRESIDENTE
            ]
        ];

        if (!in_array($idPerfil, array_keys($listAvailable))) {
            return;
        };
        
        return in_array($siEncaminhamento, $listAvailable[$idPerfil]);
    }
}
