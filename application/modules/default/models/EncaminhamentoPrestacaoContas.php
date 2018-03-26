<?php
class EncaminhamentoPrestacaoContas extends MinC_Db_Table_Abstract
{
    protected $_name   = 'tbEncaminhamentoPrestacaoContas';
    protected $_schema = 'BDCORPORATIVO.scSAC';
    protected $_banco  = 'BDCORPORATIVO';

    public function tbEncaminhamentoPrestacaoContas($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('tbepc'=>$this->_name),
                        array(
                                'tbepc.idAgenteDestino','tbepc.idAgenteOrigem',
                                'tbepc.dtInicioEncaminhamento','p.NomeProjeto',
                                'tbepc.idOrgao','u.usu_nome','uu.usu_nome','o.org_sigla'
                              )
                      );

        $select->where('tbepc.idPronac = ?', '093855');

        return $this->fetchAll($select);
    }
}
