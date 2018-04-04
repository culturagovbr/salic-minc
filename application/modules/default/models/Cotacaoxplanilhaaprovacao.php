<?php
class Cotacaoxplanilhaaprovacao extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbCotacaoxPlanilhaAprovacao';
    protected $_schema  = 'scSAC';

    public function inserirCotacaoxPlanilhaAprovacao($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarCotacaoxPlanilhaAprovacao($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarCotacaoxPlanilhaAprovacao($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function itensVinculados($idCotacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>'scSAC.tbCotacaoxPlanilhaAprovacao'),
                array('a.idCotacao', 'a.idPlanilhaAprovacao')
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
        $select->joinInner(
                array('f' => 'tbCotacaoxAgentes'),
                'a.idCotacaoxAgentes = f.idCotacaoxAgentes',
                array('f.idAgente'),
                'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
                array('g' => 'Nomes'),
                'f.idAgente = g.idAgente',
                array('g.Descricao as nmAgente'),
                'AGENTES.dbo'
        );
        $select->where("a.idCotacao = ?", $idCotacao);

        return $this->fetchAll($select);
    }
}
