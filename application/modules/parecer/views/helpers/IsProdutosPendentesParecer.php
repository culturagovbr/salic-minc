 <?php
/**
 * Helper para verificar se h&aacute; produtos pendentes de parecer
 */

class Zend_View_Helper_IsProdutosPendentesParecer
{
    /**
     * M&eacute;todo para verificar se h&aacute; produtos pendentes de parecer
     * @access public
     * @param integer $idPronac
     * @param integer $idProduto
     * @return string
     */
    public function IsProdutosPendentesParecer($idPronac, $idProduto)
    {
        $tbAnaliseDeConteudoDAO = new Parecer_Model_DbTable_TbAnaliseDeConteudo();
        $where['IdPRONAC = ?'] = $idPronac;
        $where['idProduto = ?'] = $idProduto;
        $where['ParecerDeConteudo = ?'] = '';
        $naoAnalisados = $tbAnaliseDeConteudoDAO->dadosAnaliseconteudo(null, $where);

        if (count($naoAnalisados) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
