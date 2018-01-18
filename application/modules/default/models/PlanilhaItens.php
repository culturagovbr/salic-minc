<?php

/**
 * Description of PlanilhaItens
 *
 * @author 01610881125
 */
class PlanilhaItens extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbPlanilhaItens";

    public function buscarItemContrato($idpronac, $idproduto, $idetapa)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        /*
         * erro 26
         $select->from(
                        array('pItens'=>$this->_name),
                        array('id'=>'pItens.idPlanilhaItens','nome'=>'pItens.Descricao')
                      );
         $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
                            array(),
                            'SAC.dbo'
                           );
         *
         *
         */
        $select->from(
                        array('pItens'=>$this->_name),
                        array('nome'=>'pItens.Descricao')
                      );
        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
                            array('id'=>'pAprovacao.idPlanilhaAprovacao'),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('prod'=>'Produto'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('pEtapa'=>'tbPlanilhaEtapa'),
                            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
                            array(),
                            'SAC.dbo'
                           );

        $select->joinLeft(
                            array('cxpa'=>'tbContratoxPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaAprovacao = cxpa.idPlanilhaAprovacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.idProduto = ?', $idproduto);
        $select->where('pEtapa.idPlanilhaEtapa = ?', $idetapa);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('cxpa.idContrato is null');
//        $select->where('pAprovacao.idPlanilhaAprovacao in (?)', new Zend_Db_Expr('select idPlanilhaAprovacao  from  SAC.dbo.tbDeParaPlanilhaAprovacao'));

        $select->order('pItens.Descricao');

        /*
         * erro 26
         * $select->group('pItens.idPlanilhaItens');
        $select->group('pItens.Descricao');*/

        return $this->fetchAll($select);
    }
    public function buscarItemComprovacao($idpronac, $idproduto, $idetapa, $ckItens)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        /*
         * erro 26
        $select->from(
                        array('pItens'=>$this->_name),
                        array('id'=>'pItens.idPlanilhaItens','nome'=>'pItens.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
                            array(),
                            'SAC.dbo'
                           );

        */
        $select->from(
                        array('pItens'=>$this->_name),
                        array('nome'=>'pItens.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
                            array('id'=>'pAprovacao.idPlanilhaAprovacao'),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('prod'=>'Produto'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('pEtapa'=>'tbPlanilhaEtapa'),
                            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
                            array(),
                            'SAC.dbo'
                           );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.idProduto = ?', $idproduto);
        $select->where('pEtapa.idPlanilhaEtapa = ?', $idetapa);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        //$select->where('pAprovacao.idPlanilhaAprovacao in (?)', new Zend_Db_Expr('select idPlanilhaAprovacao  from  SAC.dbo.tbDeParaPlanilhaAprovacao'));
        /*
        if(is_array($ckItens) and count(is_array($ckItens))>0)
            $select->where('pAprovacao.idPlanilhaItem in ('.implode(',',$ckItens).')');
        */
        if (is_array($ckItens) and count(is_array($ckItens))>0) {
            $select->where('pAprovacao.idPlanilhaAprovacao in ('.implode(',', $ckItens).')');
        }


        $select->order('pItens.Descricao');

        /*erro 26
        $select->group('pItens.idPlanilhaItens');

        $select->group('pItens.Descricao');
        */
        

        return $this->fetchAll($select);
    }
    public function buscarItem($idpronac, $idproduto, $idetapa)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('pItens'=>$this->_name),
                        array('nome'=>'pItens.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
                            array('id'=>'pAprovacao.idPlanilhaAprovacao'),
                            'SAC.dbo'
                           );

        /* erro UC26

        $select->from(
                        array('pItens'=>$this->_name),
                        array('id'=>'pItens.idPlanilhaItens','nome'=>'pItens.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
                            array(),
                            'SAC.dbo'
                           );
         //*/
        $select->joinLeft(
                            array('prod'=>'Produto'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('pEtapa'=>'tbPlanilhaEtapa'),
                            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
                            array(),
                            'SAC.dbo'
                           );

        $select->joinLeft(
                            array('cxpa'=>'tbCotacaoxPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaAprovacao = cxpa.idPlanilhaAprovacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        $select->joinLeft(
                            array('dlxpa'=>'tbDispensaLicitacaoxPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaAprovacao = dlxpa.idPlanilhaAprovacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        $select->joinLeft(
                            array('lxpa'=>'tbLicitacaoxPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaAprovacao = lxpa.idPlanilhaAprovacao',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );


        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.idProduto = ?', $idproduto);
        $select->where('pEtapa.idPlanilhaEtapa = ?', $idetapa);
        $select->where('pAprovacao.stAtivo = ?', 'S');
        //$select->where('cxpa.idCotacao is null');
        //$select->where('dlxpa.idDispensaLicitacao is null');
        //$select->where('lxpa.idLicitacao is null');
        //$select->where('pAprovacao.idPlanilhaAprovacao in (?)', new Zend_Db_Expr('select idPlanilhaAprovacao  from  SAC.dbo.tbDeParaPlanilhaAprovacao'));

        $select->order('pItens.Descricao');

        /* erro UC26

        $select->group('pItens.idPlanilhaItens');
        $select->group('pItens.Descricao');//*/



        return $this->fetchAll($select);
    }

    public function carregarItem($idpronac, $idproduto, $idetapa, $idCotacao, $idDispensaLicitacao, $idLicitacao, $idContrato)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        /* erro 26
        $select->from(
                        array('pItens'=>$this->_name),
                        array('id'=>'pItens.idPlanilhaItens','nome'=>'pItens.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
                            array(),
                            'SAC.dbo'
                           );
         *
         */
        $select->from(
                        array('pItens'=>$this->_name),
                        array('nome'=>'pItens.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idPlanilhaItem = pItens.idPlanilhaItens',
                            array('id'=>'pAprovacao.idPlanilhaAprovacao'),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('prod'=>'Produto'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('pEtapa'=>'tbPlanilhaEtapa'),
                            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
                            array(),
                            'SAC.dbo'
                           );
        if ($idCotacao) {
            $select->joinInner(
                            array('ctxpa'=>'tbCotacaoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = ctxpa.idPlanilhaAprovacao and ctxpa.idCotacao = '$idCotacao' ",
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
            $select->joinInner(
                            array('ctxa'=>'tbCotacaoxAgentes'),
                            "ctxpa.idCotacaoxAgentes = ctxa.idCotacaoxAgentes",
                            array('ctxa.idAgente'),
                            'BDCORPORATIVO.scSAC'
                           );
            $select->joinInner(
                            array('nm'=>'Nomes'),
                            "ctxa.idAgente = nm.idAgente",
                            array('Fornecedor'=>'nm.Descricao'),
                            'AGENTES.dbo'
                           );
        }
        if ($idDispensaLicitacao) {
            $select->joinInner(
                            array('dlxpa'=>'tbDispensaLicitacaoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = dlxpa.idPlanilhaAprovacao and dlxpa.idDispensaLicitacao  = '$idDispensaLicitacao' ",
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        }
        if ($idLicitacao) {
            $select->joinInner(
                            array('lxpa'=>'tbLicitacaoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = lxpa.idPlanilhaAprovacao and lxpa.idLicitacao  = '$idLicitacao' ",
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        }
        if ($idContrato) {
            $select->joinInner(
                            array('cnxpa'=>'tbContratoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = cnxpa.idPlanilhaAprovacao and cnxpa.idContrato = '$idContrato' ",
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        }

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);
        $select->where('pAprovacao.idProduto = ?', $idproduto);
        $select->where('pEtapa.idPlanilhaEtapa = ?', $idetapa);
        $select->where('pAprovacao.idPlanilhaAprovacao in (?)', new Zend_Db_Expr('select idPlanilhaAprovacao  from  SAC.dbo.tbDeParaPlanilhaAprovacao'));

        $select->order('pItens.Descricao');

        /*$select->group('pItens.idPlanilhaItens');
        $select->group('pItens.Descricao');
        if($idCotacao){
            $select->group('ctxa.idAgente');
            $select->group('nm.Descricao');
        }*/

        return $this->fetchAll($select);
    }


    /**
     * Busca os itens de acordo com uma etapa para as combos
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function combo($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('tpi' => $this->_name),
            array('tpi.idPlanilhaItens AS id'
                ,'tpi.Descricao AS descricao'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('tipp' => 'tbItensPlanilhaProduto'),
            'tpi.idPlanilhaItens = tipp.idPlanilhaItens',
            array(),
            'SAC.dbo'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);

                
        return $this->fetchAll($select);
    } // fecha m�todo combo()

    public function buscarItens()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        //$select->assemble();
        return $this->fetchAll($select);
    }
}
