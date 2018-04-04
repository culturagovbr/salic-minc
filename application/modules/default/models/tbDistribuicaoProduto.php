<?php
/**
 * DAO tbDistribuicaoProduto
 * @since 16/03/2011
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbDistribuicaoProduto extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbDistribuicaoProduto";

    public function buscarDistribuicaoProduto($idDistribuicaoProduto)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('dp'=>$this->_name),
                        array(
                            'dp.qtDistribuicao',
                            'dp.idDocumento',
                            'dp.dsTamanhoDuracao',
                            'dp.idDistribuicaoProduto',
                            'dp.stFinsLucrativos',
                            'dp.dsDestinacaoProduto',
                            'dp.dsReceptorProduto',
                            '*'
                            )
                        );
        $slct->joinInner(
                            array('pdp'=>'PlanoDistribuicaoProduto'),
                            'dp.idPlanoDistribuicao = pdp.idPlanoDistribuicao AND pdp.stPlanoDistribuicaoProduto = 1',
                            array(
                                    'pdp.QtdeProduzida',
                                    new Zend_Db_Expr('(pdp.QtdePatrocinador+pdp.QtdeProponente+pdp.QtdeProponente) as DistribuicaoGratuita')
                                 )
                            );
        $slct->joinInner(
                             array('pd'=>'Produto'),
                             "pd.Codigo = pdp.idProduto",
                             array('pd.Descricao')
                            );
        $slct->where('dp.idDistribuicaoProduto = ?', $idDistribuicaoProduto);

        return $this->fetchAll($slct);
    }


    public function buscarDistribuicaoProduto2($idPlanoDistribuicao)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                        array('dp'=>$this->_name),
                        array(
                            'dp.qtDistribuicao',
                            'dp.idDocumento',
                            'dp.dsTamanhoDuracao',
                            'dp.idDistribuicaoProduto',
                            'dp.stFinsLucrativos',
                            'dp.dsDestinacaoProduto',
                            'dp.dsReceptorProduto',
                            '*'
                            )
                        );
        $slct->joinInner(
                            array('pdp'=>'PlanoDistribuicaoProduto'),
            new Zend_Db_Expr('dp.idPlanoDistribuicao = pdp.idPlanoDistribuicao AND pdp.stPlanoDistribuicaoProduto = 1'),
                            array(
                                    'pdp.QtdeProduzida',
                                new Zend_Db_Expr('(pdp.QtdePatrocinador+pdp.QtdeProponente+pdp.QtdeProponente) as DistribuicaoGratuita')
                                 )
                            );
        $slct->joinInner(
                             array('pd'=>'Produto'),
                             "pd.Codigo = pdp.idProduto",
                             array('pd.Descricao')
                            );
        $slct->where('dp.idPlanoDistribuicao = ?', $idPlanoDistribuicao);

        return $this->fetchAll($slct);
    }
}
