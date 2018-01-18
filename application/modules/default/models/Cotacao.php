<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cotacao
 *
 * @author augusto
 */
class Cotacao extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'bdcorporativo';
    protected $_name    = 'tbCotacao';
    protected $_schema  = 'bdcorporativo.scSAC';



    public function inserirCotacao($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarCotacao($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarCotacao($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function buscarCotacaoProjeto($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('cot'=>$this->_name),
                        array(
                                'cot.idCotacao','cot.nrCotacao','CAST(cot.dsCotacao AS TEXT) AS dsCotacao','cot.dtCotacao'
                              )
                      );

        $select->joinInner(
                            array('cpa'=>'tbCotacaoxPlanilhaAprovacao'),
                            'cot.idCotacao = cpa.idCotacao',
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
        $select->order('cot.dtCotacao');
        $select->group(array(
                            'cot.idCotacao','cot.nrCotacao','cot.dsCotacao','cot.dtCotacao','pa.IdPRONAC'
                          ));


        return $this->fetchAll($select);
    }


    public function buscarCotacao($cotacao)
    {
        $slct = $this->select();
        $slct->from(
                 array('cot'=>$this->_name),
                 array(
                    'cot.idCotacao','cot.nrCotacao','CAST(cot.dsCotacao AS TEXT) AS dsCotacao','cot.dtCotacao'
                 )
        );
        $slct->where(' idCotacao = ? ', $cotacao);
        return $this->fetchAll($slct);
    }
}
