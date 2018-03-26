<?php
class PlanilhaEtapa extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbPlanilhaEtapa";

    public function buscarEtapaContrato($idpronac, $idproduto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('pEtapa'=>$this->_name),
                        array('id'=>'pEtapa.idPlanilhaEtapa','nome'=>'pEtapa.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('prod'=>'Produto'),
                            'pAprovacao.idProduto = prod.Codigo',
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

        if ($idproduto == '0') {
            $select->where('prod.Codigo is null');
        } elseif (!empty($idproduto)) {
            $select->where('prod.Codigo = ?', $idproduto);
        }

        //$select->where('pEtapa.tpCusto = ?','P');

        $select->where('pAprovacao.stAtivo = ?', 'S');
        $select->where('cxpa.idContrato is null');

        $select->order('pEtapa.Descricao');

        $select->group('pEtapa.idPlanilhaEtapa');
        $select->group('pEtapa.Descricao');

        return $this->fetchAll($select);
    }
    public function buscarEtapaComprovacao($idpronac, $idproduto, $ckItens)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('pEtapa'=>$this->_name),
                        array(
                            'id'=>'pEtapa.idPlanilhaEtapa'
                            ,'nome'=>'pEtapa.Descricao'
                            ,'codigoProduto'=> new Zend_Db_Expr('CASE WHEN prod.Codigo IS NULL THEN \'0\' ELSE prod.Codigo END')
                            )
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('prod'=>'Produto'),
                            'pAprovacao.idProduto = prod.Codigo',
                            array(),
                            'SAC.dbo'
                           );

        $select->where('pAprovacao.IdPRONAC = ?', $idpronac);

        if ($idproduto > 0) {
            $select->where('prod.Codigo = ?', $idproduto);
        } else {
            $select->where('prod.Codigo is null');
        }
        $select->where('pAprovacao.stAtivo = ?', 'S');
        if (is_array($ckItens) and count(is_array($ckItens))>0) {
            $select->where('pAprovacao.idPlanilhaAprovacao in ('.implode(',', $ckItens).')');
        }

        $select->order('pEtapa.Descricao');

        $select->group('pEtapa.idPlanilhaEtapa');
        $select->group('prod.Codigo');
        $select->group('pEtapa.Descricao');

        return $this->fetchAll($select);
    }
    public function buscarEtapa($idpronac, $idproduto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('pEtapa'=>$this->_name),
                        array('id'=>'pEtapa.idPlanilhaEtapa','nome'=>'pEtapa.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('prod'=>'Produto'),
                            'pAprovacao.idProduto = prod.Codigo',
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

        if ($idproduto == '0') {
            $select->where('prod.Codigo is null');
        } elseif (!empty($idproduto)) {
            $select->where('prod.Codigo = ?', $idproduto);
        }

        $select->where('pAprovacao.stAtivo = ?', 'S');
        //$select->where('cxpa.idCotacao is null');
        //$select->where('dlxpa.idDispensaLicitacao is null');
        //$select->where('lxpa.idLicitacao is null');

        $select->order('pEtapa.Descricao');

        $select->group('pEtapa.idPlanilhaEtapa');
        $select->group('pEtapa.Descricao');



        return $this->fetchAll($select);
    }

    public function carregarEtapa($idpronac, $idproduto, $idCotacao, $idDispensaLicitacao, $idLicitacao, $idContrato)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('pEtapa'=>$this->_name),
                        array('id'=>'pEtapa.idPlanilhaEtapa','nome'=>'pEtapa.Descricao')
                      );

        $select->joinInner(
                            array('pAprovacao'=>'tbPlanilhaAprovacao'),
                            'pAprovacao.idEtapa = pEtapa.idPlanilhaEtapa',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinLeft(
                            array('prod'=>'Produto'),
                            'pAprovacao.idProduto = prod.Codigo',
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
        }
        if ($idDispensaLicitacao) {
            $select->joinInner(
                            array('dlxpa'=>'tbDispensaLicitacaoxPlanilhaAprovacao'),
                            "pAprovacao.idPlanilhaAprovacao = dlxpa.idPlanilhaAprovacao and dlxpa.idDispensaLicitacao  = '$idDispensaLicitacao'",
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

        if ($idproduto == '0') {
            $select->where('prod.Codigo is null');
        } elseif (!empty($idproduto)) {
            $select->where('prod.Codigo = ?', $idproduto);
        }

        $select->order('pEtapa.Descricao');

        $select->group('pEtapa.idPlanilhaEtapa');
        $select->group('pEtapa.Descricao');

        return $this->fetchAll($select);
    }

    public function buscarEtapas()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->where("tpCusto = 'P'");
        //$select->assemble();
        return $this->fetchAll($select);
    }
}
