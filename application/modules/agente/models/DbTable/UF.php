<?php

class Agente_Model_DbTable_UF extends MinC_Db_Table_Abstract
{
    protected $_banco = 'AGENTES';
    protected $_name = 'uf';
    protected $_schema = 'AGENTES';

    public function buscar($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            $this->_name,
            array('id'=>'iduf',
                'descricao'=> 'sigla'),
            $this->_schema
        );
        $select->order('sigla');
        try {
            return $this->fetchAll($select);
        } catch (Zend_Exception_Db $objException) {
            throw new Exception("Erro ao buscar Estados: " . $objException->getMessage(), 0, $objException);
        }
    }

    public function buscarRegiao($regiao)
    {
        $objEstado = self::obterInstancia();
        $sql = 'SELECT idUF AS id, Descricao AS descricao
			FROM ' . GenericModel::getStaticTableName($objEstado->_schema, $objEstado->_name) . "
			WHERE Regiao = '{$regiao}'
			ORDER BY Sigla";

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);

            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $objException) {
            throw new Exception("Erro ao buscar Estados: " . $objException->getMessage(), 0, $objException);
        }
    }

    public function listar($id = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from($this->_name, array('iduf AS id', 'sigla AS descricao'), $this->_schema);

        if (!empty($id)) {
            $sql->where('iduf = ?', $id);
        }

        return $db->fetchAll($sql);
    }
}
