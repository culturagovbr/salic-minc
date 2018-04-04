<?php
class Contratoxplanilhaaprovacao extends MinC_Db_Table_Abstract
{
    protected $_name    = 'tbContratoxPlanilhaAprovacao';
    protected $_schema  = 'bdcorporativo.scSAC';

    public function inserirContratoxPlanilhaAprovacao($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarContratoxPlanilhaAprovacao($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarContratoxPlanilhaAprovacao($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function itensVinculados($idContrato)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a'=>'scSAC.tbContratoxPlanilhaAprovacao'),
                array('a.idContrato', 'a.idPlanilhaAprovacao')
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
        $select->where("a.idContrato = ?", $idContrato);

        return $this->fetchAll($select);
    }
}
