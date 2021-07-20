<?php
/**
 * Helper para verificar se projeto est&aacute; disponível para edi&ccedil;ão da readequa&ccedil;ão da planilha
 */

class Zend_View_Helper_DisponivelParaEdicaoReadequacaoPlanilha
{
    /**
     * Método para verificar se o projeto est&aacute; disponível para edi&ccedil;ão da readequa&ccedil;ão da planilha
     * @access public
     * @param integer $idPronac
     * @param integer $idAgente
     * @return string
     */
    public function disponivelParaEdicaoReadequacaoPlanilha($idPronac, $idAgente = null)
    {
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();

        return $Readequacao_Model_DbTable_TbReadequacao->disponivelParaEdicaoReadequacaoPlanilha($idPronac, $idAgente);
    }
}
