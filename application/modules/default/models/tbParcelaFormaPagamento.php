<?php 


class tbParcelaFormaPagamento extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbParcelaFormaPagamento';
//    protected  $_primary = 'idRegiaoCriterioParticipacao';

    
//    public function buscarRegiaoCriterioParticipacao($idCriterioParticipacao, $dbg = null){
//
//        $select = $this->select();
//
//        $select->setIntegrityCheck(false);
//
//        $select->from(array('eas' => 'tbRegiaoCriterioParticipacao'),
//                        array('idRegiaoCriterioParticipacao',
//                              'idCriterioParticipacao',
//                              'dsRegiao',
//                              'idUF',
//                              'idCidade')
//        );
//
//        $select->where('idCriterioParticipacao = ?', $idCriterioParticipacao);
//
//        if($dbg){

//        }
//
//        return $this->fetchAll($select)->toArray();
//    }
}
