<?php
/**
 * Helper para verificar se projeto est&aacute; dispon&iacute;vel para devolução
 */

class Zend_View_Helper_DisponivelParaDevolucao
{
    /**
     * M&eacute;todo para verificar se o projeto est&aacute; dispon&iacute;vel para devolução
     * @access public
     * @param integer $siEncaminhamento
     * @return string
     */
    public function disponivelParaDevolucao($siEncaminhamento)
    {

        $listAvailable = [
            Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_AO_MINC,
            Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO,
            Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL,
            Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_COORDENADOR_GERAL,
            Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_DIRETOR,
            Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_SECRETARIO
        ];

        return in_array($siEncaminhamento, $listAvailable);
    }
}
