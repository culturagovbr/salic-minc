<?php

/**
 * Description of Contrato
 *
 * @author 01610881125
 */
class Contrato extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbContrato';
    protected $_schema  = 'bdcorporativo.scSAC';

    public function inserirContrato($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarContrato($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarContrato($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function buscarContrato($idContrato)
    {
        $slct = $this->select();
        $slct->from(
                 array('ct'=>$this->_name),
                 array(
                    'ct.idContrato','ct.nrContratoSequencial','ct.tpAquisicao','ct.nrContratoAno','ct.dtPublicacao','CAST(ct.dsObjetoContrato AS TEXT) AS dsObjetoContrato','ct.vlGlobal','ct.dtInicioVigencia','ct.dtFimVigencia','ct.dtAssinatura'
                 )
        );
        $slct->where('ct.idContrato = ? ', $idContrato);
        return $this->fetchAll($slct);
    }

    public function buscarContratoProjeto($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('con'=>$this->_name),
                        array(
                                'con.idContrato','con.nrContratoSequencial','con.nrContratoAno','con.dtPublicacao'
                              )
                      );

        $select->joinInner(
                            array('cpa'=>'tbContratoxPlanilhaAprovacao'),
                            'con.idContrato = cpa.idContrato',
                            array(),
                            'BDCORPORATIVO.scSAC'
                           );
        $select->joinInner(
                            array('pa'=>'tbPlanilhaAprovacao'),
                            'cpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
                            array('pa.IdPRONAC'),
                            'SAC.dbo'
                           );

        $select->where('pa.IdPRONAC = ?', $idpronac);

        $select->order('con.dtPublicacao');

        $select->group(array(
                            'con.idContrato','con.nrContratoSequencial','con.nrContratoAno','con.dtPublicacao','pa.IdPRONAC'
                          ));


        return $this->fetchAll($select);
    }

    public function buscarContratoItem($idpronac, $idProduto, $idEtapa, $idItem)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('con'=>$this->_name),
                        array(
                                'con.idContrato','con.nrContratoSequencial','con.nrContratoAno'
                              )
                      );

        $select->joinInner(
                            array('cpa'=>'tbContratoxPlanilhaAprovacao'),
                            'con.idContrato = cpa.idContrato',
                            array('cpa.idPlanilhaAprovacao'),
                            'BDCORPORATIVO.scSAC'
                           );
        $select->joinInner(
                            array('pa'=>'tbPlanilhaAprovacao'),
                            'cpa.idPlanilhaAprovacao = pa.idPlanilhaAprovacao',
                            array('pa.IdPRONAC'),
                            'SAC.dbo'
                           );

        $select->where('pa.IdPRONAC = ?', $idpronac);
        $select->where('pa.idProduto = ?', $idProduto);
        $select->where('pa.idEtapa = ?', $idEtapa);
        $select->where('pa.idPlanilhaItem = ?', $idItem);

        $select->order('con.dtPublicacao');

        return $this->fetchRow($select);
    }
}
