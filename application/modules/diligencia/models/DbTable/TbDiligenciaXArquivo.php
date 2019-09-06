<?php

class Diligencia_Model_DbTable_TbDiligenciaXArquivo extends MinC_Db_Table_Abstract
{
    public $modeloTbDiligencia;

    protected $_schema = 'sac';
    protected $_name = 'tbDiligenciaXArquivo';
    protected $_primary = 'idDiligencia';

    public function obterAnexosDiligencia($where = [])
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            ['a' => $this->_name],
            [
                'idDiligencia'
            ],
            $this->_schema
        );

        $select->joinInner(
            array('b' => 'tbArquivo'),
            'a.idArquivo = b.idArquivo',
            array(
                'idArquivo',
                'nmArquivo',
                'dtEnvio'
            ),
            'BDCORPORATIVO.scCorp'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }
}
