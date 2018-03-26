<?php
class EstadoDAO extends MinC_Db_Table_Abstract
{
    protected $_name = 'uf'; // nome da tabela
    protected $_schema = 'agentes'; // nome da tabela

    public function listar($id = null)
    {
        echo '<pre>';
        var_dump('Método transferido para Agente Model Dbtable UF');
        exit;
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from($this->_name, array('iduf AS id', 'sigla AS descricao'), $this->_schema);

        if (!empty($id)) {
            $sql->where('idUF = ?', $id);
        }

        return $db->fetchAll($sql);
    }
}
