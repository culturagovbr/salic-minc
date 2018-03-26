<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrgaoFiscalizador
 *
 * @author 01610881125
 */
class OrgaoFiscalizador extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name  = 'tbOrgaoFiscalizador';

    public function buscarOrgao($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('orgf'=>$this->_name),
                        array('orgf.idOrgaoFiscalizador')
                     );
        $select->joinInner(
                            array('org'=>'Orgaos'),
                            "org.Codigo = orgf.idOrgao",
                            array('org.Codigo','org.Sigla')
                          );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchAll($select);
    }

    public function dadosOrgaos($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('tbOF'=>$this->_name),
                        array(new Zend_Db_Expr('CAST(tbOF.dsObservacao as TEXT) as dsObservacao'))
                     );
        $select->joinLeft(
                            array('org'=>'Orgaos'),
                            "org.Codigo = tbOF.idOrgao",
                            array('org.Sigla')
                          );
        $select->joinLeft(
                            array('tbAgOF'=>'Agentes'),
                            "tbOF.idParecerista = tbAgOF.idAgente",
                            array('tbAgOF.CNPJCPF'),
                            'AGENTES.dbo'
                          );
        $select->joinLeft(
                            array('tbNmOF'=>'Nomes'),
                            "tbOF.idParecerista = tbNmOF.idAgente",
                            array(new Zend_Db_Expr('CAST(tbNmOF.Descricao AS TEXT) as Descricao')),
                            'AGENTES.dbo'
                          );
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchAll($select);
    }
}
