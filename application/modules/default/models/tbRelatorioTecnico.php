<?php

class tbRelatorioTecnico extends MinC_Db_Table_Abstract
{
    protected $_name   = 'tbRelatorioTecnico';
    protected $_schema = 'sac';
    protected $_primary  = 'idRelatorioTecnico';

    public function insertParecerTecnico($data)
    {
        $insertparecertecnico = $this->insert($data);
        return $insertparecertecnico;
    }

    private function queryConsultaBasica(array $where = []) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            [
                'idRelatorioTecnico',
                'meRelatorio' => new Zend_Db_Expr('CAST(meRelatorio AS TEXT)'),
                'dtRelatorio',
                'IdPRONAC',
                'idAgente',
                'cdGrupo',
                'siManifestacao',
            ],
            $this->getSchema('sac')
        );

        if(count($where) > 0) {
            foreach($where as $campo => $valor) {
                $select->where(
                    "{$campo} = ?",
                    $valor
                );
            }
        }

        return $select;
    }

    public function obterTodos(array $where = [])
    {
        $select = $this->queryConsultaBasica($where);
        return $this->fetchAll($select);
    }

    public function obterUm(array $where = [])
    {
        $select = $this->queryConsultaBasica($where);
        return $this->fetchRow($select);
    }
}
