<?php
class Cidade extends MinC_Db_Table_Abstract
{
    protected $_name = 'municipios';
    protected $_schema = 'agentes';

    /**
     * Metodo para buscar as cidades de um determinado estado
     * @access public
     * @param integer $idUF
     * @return object $db->fetchAll($sql)
     */
    public function buscar($idUF)
    {
        $sql = " SELECT idMunicipioIBGE AS id, Descricao AS descricao ";
        $sql.= " FROM " . $this->_schema . '.' . $this->_name;
        $sql.= " WHERE idUFIBGE = " . $idUF . " ";
        $sql.= " ORDER BY Descricao;";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Cidades: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }
}
