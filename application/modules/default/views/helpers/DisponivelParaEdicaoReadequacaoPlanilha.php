<?php
/**
 * Helper para verificar se projeto está disponível para edição da readequação da planilha
 */

class Zend_View_Helper_DisponivelParaEdicaoReadequacaoPlanilha
{
    /**
     * Método para verificar se o projeto está disponível para edição da readequação da planilha
     * @access public
     * @param integer $idPronac
     * @param integer $idAgente
     * @return string
     */
    public function disponivelParaEdicaoReadequacaoPlanilha($idPronac, $idAgente = null)
    {
        $tbReadequacao = new tbReadequacao();
        
        return $tbReadequacao->disponivelParaEdicaoReadequacaoPlanilha($idPronac, $idAgente);
    }
}
