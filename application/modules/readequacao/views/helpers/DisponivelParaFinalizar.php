<?php
/**
 * Helper para verificar se projeto est&aacute; dispon&iacute;vel para finalização
 */

class Zend_View_Helper_DisponivelParaFinalizar
{
    /**
     * M&eacute;todo para verificar se o projeto est&aacute; dispon&iacute;vel para finalizar
     * @access public
     * @param integer $siEncaminhamento
     * @return string
     */
    public function disponivelParaFinalizar($siEncaminhamento)
    {
        return ($siEncaminhamento == Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_FINAL);
    }
}
