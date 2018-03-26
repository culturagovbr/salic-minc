<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Licitacaoxplanilhaaprovacao
 *
 * @author 01610881125
 */
class Licitacaoxplanilhaaprovacao extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbLicitacaoxPlanilhaAprovacao';
    protected $_schema  = 'scSAC';

    public function inserirLicitacaoxPlanilhaAprovacao($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarLicitacaoxPlanilhaAprovacao($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarLicitacaoxPlanilhaAprovacao($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function itensVinculados($idLicitacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>'scSAC.tbLicitacaoxPlanilhaAprovacao'),
                array('a.idLicitacao', 'a.idPlanilhaAprovacao')
        );
        $select->joinInner(
                array('b' => 'tbPlanilhaAprovacao'),
                'a.idPlanilhaAprovacao = b.idPlanilhaAprovacao',
                array('b.idProduto','b.idEtapa','b.idPlanilhaItem'),
            'SAC.dbo'
        );
        $select->joinLeft(
                array('c' => 'Produto'),
                'b.idProduto = c.Codigo',
                array('c.Descricao as dsProduto'),
            'SAC.dbo'
        );
        $select->joinInner(
                array('d' => 'tbPlanilhaEtapa'),
                'b.idEtapa = d.idPlanilhaEtapa',
                array('d.Descricao as dsEtapa'),
                'SAC.dbo'
        );
        $select->joinInner(
                array('e' => 'tbPlanilhaItens'),
                'b.idPlanilhaItem = e.idPlanilhaItens',
                array('e.Descricao as dsItem'),
                'SAC.dbo'
        );
        $select->where("a.idLicitacao = ?", $idLicitacao);

        return $this->fetchAll($select);
    }
}
