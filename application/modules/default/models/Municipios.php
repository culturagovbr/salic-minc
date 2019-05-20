<?php

class Municipios extends MinC_Db_Table_Abstract
{
    protected $_name = 'Municipios';
    protected $_schema = 'agentes';
    protected $_primary = 'idMunicipioIBGE';


    /**
     * Busca os munic�pios para as combos
     * @access public
     * @param array $where (filtros)
     * @param array $order (ordena��o)
     * @return object
     */
    public function combo($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            $this->_name,
            array('idMunicipioIBGE AS id'
            , 'Descricao AS descricao'
            )
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        // adicionando linha order ao select
        $select->order($order);
        return $this->fetchAll($select);
    }


    public function buscaCompleta($where = array(), $order = array(), $dbg = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('mu' => $this->_name),
            array('mu.idMunicipioIBGE',
                'mu.IdMeso',
                'mu.idMicro',
                'mu.Descricao as dsMunicipio',
                'mu.Latitude',
                'mu.Longitude')
        );

        $select->joinInner(
            array('uf' => 'UF'),
            'mu.idUFIBGE = uf.idUF',
            array('uf.idUF',
                'uf.Sigla',
                'uf.Descricao as dsUF',
                'uf.Regiao')
        );

        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        $select->order($order);

        if ($dbg) {
            xd($select->assemble());
        }

        return $this->fetchAll($select);
    }

    /**
     * Metodo para buscar as cidades de um determinado estado
     * @access public
     * @param integer $idUF
     * @return object $db->fetchAll($sql)
     */
    public function buscar($idUF)
    {
        try {

            $sql = $this->select()
                ->from($this->_name, [
                    'idMunicipioIBGE AS id',
                    'Descricao AS descricao'
                    ],
                    $this->_schema)
                ->where('idUFIBGE = ?', $idUF)
                ->order('Descricao');

            $this->_db->setFetchMode(Zend_DB::FETCH_OBJ);
            return $this->_db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Cidades: " . $e->getMessage();
        }
    }
}
