<?php
class Contratoxagentes extends MinC_Db_Table_Abstract
{
    protected $_name    = 'tbContratoxAgentes';
    protected $_schema  = 'bdcorporativo.scSAC';

    public function inserirContratoxAgentes($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }

    public function alterarContratoxAgentes($data, $where)
    {
        $update = $this->update($data, $where);
        return $update;
    }

    public function deletarContratoxAgentes($where)
    {
        $delete = $this->delete($where);
        return $delete;
    }

    public function buscarAgentes($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('cxa'=>$this->_name),
                        array('cxa.idAgente')
                      );

        $select->joinInner(
                            array('ag'=>'Agentes'),
                            'cxa.idAgente = ag.idAgente',
                            array('ag.CNPJCPF','ag.TipoPessoa'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('nm'=>'Nomes'),
                            'nm.idAgente = ag.idAgente',
                            array('nm.Descricao'),
                            'AGENTES.dbo'
                           );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }
}
