<?php

/**
 * Description of Uf
 *
 * @author 01610881125
 */
class Uf extends MinC_Db_Table_Abstract
{
    protected $_primary = "idUF";
    protected $_name = 'Uf';
    protected $_schema = 'agentes';

    public function buscarRegiao()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array($this->_name),
            array(
            'Regiao'
                )
        );
        $select->order('Regiao');
        $select->group('Regiao');
        return $this->fetchAll($select);
    }

    public function buscaRegiaoPorPRONAC($PRONAC)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('uf'=>$this->_name),
            array(
            'Regiao'
                ),
            $this->_schema
        );
        $select->joinInner(array('p' => 'Projetos'), 'uf.Sigla = p.UfProjeto', array(), 'SAC.dbo');

        $select->where('(p.AnoProjeto+p.Sequencial) = ?', $PRONAC);

        return $this->fetchAll($select);
    }

    /**
     * Metodo para buscar os estados
     * @access public
     * @param void
     * @return array
     * @author wouerner <wouerner@gmail.com>
     */
    public function buscar()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('uf'=>$this->_name),
            array(
            'idUF',
            'Sigla',
            'Regiao'
        ),
            $this->_schema
        );
        $select->order('Sigla ASC');

        return $this->fetchAll($select);

        /*$sql = 'SELECT idUF AS id, Sigla AS descricao ';
        //$sql .= 'FROM ' . GenericModel::getStaticTableName($objEstado->_schema, $objEstado->_name);
        $sql .= 'FROM ' . $this->getStaticTableName('agentes', 'uf') . ' ';
        $sql .= ' ORDER BY Sigla';

        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $objException) {
            throw new Exception("Erro ao buscar Estados: " . $objException->getMessage(), 0, $objException);
        }*/
    }
}
