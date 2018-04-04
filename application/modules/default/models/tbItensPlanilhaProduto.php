<?php
class tbItensPlanilhaProduto extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbitensplanilhaproduto';
    protected $_primary = 'idItensPlanilhaProduto';

    /**
     * Metodo para consultar o Valor Real por ano
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function buscaItemProduto($where = array())
    {
        try {
            $this->_db->beginTransaction();

            $select = $this->select();
            $select->setIntegrityCheck(false);

            $select->from(
                array('p' => $this->_name),
                array('p.idPlanilhaItens')
            );

            $select->joinInner(
                array('pr' => 'Produto'),
                'p.idProduto = pr.Codigo',
                array('pr.Descricao as Produto')
            );

            $select->joinInner(
                array('e' => 'TbPlanilhaEtapa'),
                'p.idPlanilhaEtapa = e.idPlanilhaEtapa',
                array('e.Descricao as Etapa')
            );

            $select->joinInner(
                array('i' => 'TbPlanilhaItens'),
                'p.idPlanilhaItens = i.idPlanilhaItens',
                array('i.Descricao as Item')
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $select->where($coluna, $valor);
            }

            $select->order('e.Descricao');
            $select->order('i.Descricao');
            $this->_db->commit();
            return $this->fetchAll($select);
        } catch (Exception $e) {
            $this->_db->rollBack();
            return false;
        }
    }

    public function totalBuscaPaginacao($where = array())
    {
        try {
            $this->_db->beginTransaction();

            $select = $this->select();
            $select->setIntegrityCheck(false);

            $select->from(
                array('p' => $this->_name),
                array('total' => 'count(*)')
            );

            $select->joinInner(
                array('pr' => 'Produto'),
                'p.idProduto = pr.Codigo',
                array('pr.Descricao as Produto')
            );

            $select->joinInner(
                array('e' => 'TbPlanilhaEtapa'),
                'p.idPlanilhaEtapa = e.idPlanilhaEtapa',
                array('e.Descricao as Etapa')
            );

            $select->joinInner(
                array('i' => 'TbPlanilhaItens'),
                'p.idPlanilhaItens = i.idPlanilhaItens',
                array('i.Descricao as Item')
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $select->where($coluna, $valor);
            }
            $this->_db->commit();
            return $this->fetchAll($select);
        } catch (Exception $e) {
            $this->_db->rollBack();
            return false;
        }
    }

    public function buscarEtapasDoItem($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('p' => $this->_name),
            array('p.idPlanilhaItens',
                'p.idPlanilhaEtapa')
        );

        $select->joinInner(
            array('e' => 'tbPlanilhaEtapa'),
            'p.idPlanilhaEtapa = e.idPlanilhaEtapa',
            array('e.Descricao as Etapa')
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order($order);

        return $this->fetchAll($select);
    }

    public function itensPorItemEEtapaReadequacao($idEtapa, $idProduto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('idPlanilhaItens')
        );

        $select->joinInner(
            array('b' => 'tbPlanilhaItens'),
            'a.idPlanilhaItens = b.idPlanilhaItens',
            array('Descricao as Item')
        );

        $select->where('a.idPlanilhaEtapa = ?', $idEtapa);
        $select->where('a.idProduto = ?', $idProduto);

        $select->order('2'); // Descricao


        return $this->fetchAll($select);
    }

    public function buscarItens($where = array(), $idEtapa = null, $idproduto = null, $fetchMode = Zend_DB::FETCH_OBJ)
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                'a.idPlanilhaItens',
                'a.idPlanilhaEtapa'
            ),
            $this->_schema
        );
        $select->joinInner(
            array('b' => 'tbPlanilhaItens'),
            'a.idPlanilhaItens = b.idPlanilhaItens',
            array('b.Descricao'),
            $this->_schema
        );

        if (!empty($idEtapa)) {
            $select->where('a.idPlanilhaEtapa = ?', $idEtapa);
        }

        if (!empty($idproduto)) {
            $select->where('idProduto = ?', $idproduto);
        }
        $select->order('b.Descricao');

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode($fetchMode);

        return $db->fetchAll($select);
    }

    public function buscarItem($where = array(), $order = array())
    {
        $select = $this->select()->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('i' => 'tbPlanilhaItens'),
            array(
                'idPlanilhaItens',
                'Descricao'
            ),
            $this->_schema
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order($order);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchRow($select);
    }

    public function listarProdutoEtapaItem($idplanilhaitens=null, $nomeItem=null, $idEtapa=null, $idProduto=null, $where=array())
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->distinct()
            ->from(array('p' => 'tbitensplanilhaproduto'), null, $this->_schema)
            ->join(
                array('pr' => 'produto'),
                '(p.idproduto = pr.codigo)',
                array(
                    'pr.codigo as idProduto',
                    'pr.descricao as Produto'
                ),
                $this->_schema
            )
            ->join(array('e' => 'tbplanilhaetapa'), '(p.idplanilhaetapa = e.idplanilhaetapa)', array("e.descricao AS Etapa"), $this->_schema)
            ->join(array('i' => 'tbplanilhaitens'), '(p.idplanilhaitens = i.idplanilhaitens)', array("i.descricao AS Item"), $this->_schema)
        ;

        if (!empty($nomeItem)) {
            $sql->where('i.descricao = ?', $nomeItem);
        }
        if (!empty($item)) {
            $sql->where('i.idplanilhaitens = ?', $idplanilhaitens);
        }
        if (!empty($idEtapa)) {
            $sql->where('e.idplanilhaetapa = ?', $idEtapa);
        }
        if (!empty($idProduto)) {
            $sql->where('pr.codigo = ?', $idProduto);
        }

        if (!empty($where)) {
            foreach ($where as $coluna => $valor) {
                $sql->where($coluna, $valor);
            }
        }

        $sql->order('pr.codigo ASC');

        return $db->fetchAll($sql);
    }

    /**
     * Método que retorna uma lista de itens filtrados pela etapa, produto, município e projeto
     *
     * @param integer $idEtapa
     * @param integer $idProduto
     * @param integer $idMunicipio
     * @param integer $idPronac
     * @return array
     */
    public function itensPorProdutoItemEtapaMunicipioReadequacao($idEtapa, $idProduto, $idMunicipio, $idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('idPlanilhaItens')
        );
        
        $select->joinInner(
            array('b' => 'tbPlanilhaItens'),
            'a.idPlanilhaItens = b.idPlanilhaItens',
            array('Descricao as Item')
        );

        $select->joinInner(
            array('tpa' => 'tbPlanilhaAprovacao'),
            'tpa.idPlanilhaItem = a.idPlanilhaItens',
            array('a.idPlanilhaItens AS idItem')
        );

        $select->where('a.idPlanilhaEtapa = ?', $idEtapa);
        $select->where('a.idProduto = ?', $idProduto);
        $select->where('tpa.idEtapa = ?', $idEtapa);
        $select->where('tpa.idMunicipioDespesa = ?', $idMunicipio);
        $select->where('tpa.tpPlanilha = ?', 'SR');
        $select->where('tpa.idPronac = ?', $idPronac);
        
        $select->order('2');
        
        return $this->fetchAll($select);
    }    
}
