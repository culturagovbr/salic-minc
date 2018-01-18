<?php
/**
 * DAO MantertabelaitensDAO
 * @author Equipe RUP - Politec
 * @since 13/12/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright  2010 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 * @deprecated utilizar a Proposta_Model_DbTable_TbSolicitarItem
 * @todo verificar os metodos e mover para Proposta_Model_DbTable_TbSolicitarItem
 */

class MantertabelaitensDAO extends MinC_Db_Table_Abstract
{
    protected $_name='tbsolicitaritem';
    protected $_schema = 'sac';
    protected $_primary = 'idSolicitarItem';
    /**
     * exibirprodutoetapaitem
     *
     * @param bool $item
     * @param bool $nomeItem
     * @param bool $idEtapa
     * @param bool $idProduto
     * @static
     * @access public
     * @return void
     * @todo mudar todas as chamadas para $this->listarProdutoEtapaItem
     */
    public static function exibirprodutoetapaitem($item=null, $nomeItem=null, $idEtapa=null, $idProduto=null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = "SELECT distinct pr.Codigo as idProduto, pr.Descricao as Produto
 				FROM SAC.dbo.tbItensPlanilhaProduto p
				INNER JOIN SAC.dbo.Produto pr on (p.idProduto = pr.Codigo)
				INNER JOIN SAC.dbo.TbPlanilhaItens i on (p.idPlanilhaItens = i.idPlanilhaItens)
				INNER JOIN SAC.dbo.TbPlanilhaEtapa e on (p.idPlanilhaEtapa = e.idPlanilhaEtapa)	";


        if (!empty($nomeItem)) {
            $sql .=" AND i.Descricao ".$nomeItem;
        }
        if (!empty($item)) {
            $sql .=" WHERE i.idPlanilhaItens = ".$item;
        }
        if (!empty($idEtapa)) {
            $sql .=" AND e.idPlanilhaEtapa = ".$idEtapa;
        }
        if (!empty($idProduto)) {
            $sql .=" AND pr.Codigo = ".$idProduto;
        }
        $sql .=" ORDER BY pr.Codigo ASC";


        return $db->fetchAll($sql);
    }

    /**
     * listarProdutoEtapaItem
     *
     * @param bool $item
     * @param bool $nomeItem
     * @param bool $idEtapa
     * @param bool $idProduto
     * @static
     * @access public
     * @return void
     * @deprecated este metodo foi movido para tbItensPlanilhaProduto
     */
    public function listarProdutoEtapaItem($item=null, $nomeItem=null, $idEtapa=null, $idProduto=null, $where=array())
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->distinct()
            ->from(array('p' => 'tbitensplanilhaproduto'), null, $this->_schema)
            ->join(
                array('pr' => 'produto'),
                '(p.idproduto = pr.codigo)',
                array(
                    'pr.codigo as idProduto'
                    ,'pr.descricao as Produto'
                    //,'i.descricao as NomeDoItem'
                ),
                $this->_schema
            )
            ->join(array('i' => 'tbplanilhaitens'), '(p.idplanilhaitens = i.idplanilhaitens)', null, $this->_schema)
            ->join(array('e' => 'tbplanilhaetapa'), '(p.idplanilhaetapa = e.idplanilhaetapa)', null, $this->_schema)
            ;

        if (!empty($nomeItem)) {
            $sql->where('i.descricao = ?', $nomeItem);
        }
        if (!empty($item)) {
            $sql->where('i.idplanilhaitens = ?', $item);
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


    public function exibirEtapa($idProduto)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->distinct();
        $sql->from(
            array('p' => 'tbitensplanilhaproduto'),
            null,
            $this->_schema
        );
        $sql->joinInner(array('pr'=>'produto'), 'p.idproduto = pr.codigo', null, $this->_schema);

        $sql->joinInner(array('i'=>'tbplanilhaitens'), 'p.idplanilhaitens = i.idplanilhaitens', null, $this->_schema);

        $sql->joinInner(array('e'=>'tbplanilhaetapa'), 'p.idplanilhaetapa = e.idplanilhaetapa', array('e.idplanilhaetapa as idEtapa', 'e.descricao as Etapa'), $this->_schema);

        $sql->where('p.idproduto = ?', $idProduto);
        $sql->order('Etapa ASC');

        return $db->fetchAll($sql);
    }

    public function exibirItem($idProduto, $idEtapa)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select();
        $sql->from(
            array('p' => 'tbitensplanilhaproduto'),
            null,
            $this->_schema
        );
        $sql->joinInner(array('pr'=>'produto'), 'p.idproduto = pr.codigo', null, $this->_schema);

        $sql->joinInner(
            array('i'=>'tbplanilhaitens'),
            'p.idplanilhaitens = i.idplanilhaitens',
                        array('idItem' => 'i.idplanilhaitens',
                             'NomeDoItem' => 'i.descricao'),
            $this->_schema
                        );
        $sql->joinInner(array('e'=>'tbplanilhaetapa'), 'p.idplanilhaetapa = e.idplanilhaetapa', null, $this->_schema);

        $sql->where('p.idproduto = ? ', $idProduto);
        $sql->where('e.idplanilhaetapa = ?', $idEtapa);

        $sql->order('NomeDoItem  ASC');

        return $db->fetchAll($sql);
    }

    public static function buscaprodutoetapaitem($item=null, $nomeItem=null)
    {
        $sql = "SELECT pr.Codigo as idProduto,
                       p.idPlanilhaItens,
                       e.idPlanilhaEtapa as idEtapa,
                       pr.Descricao as Produto,
                       e.Descricao as Etapa,
                       i.Descricao as NomeDoItem
                FROM SAC.dbo.tbItensPlanilhaProduto p
		INNER JOIN SAC.dbo.Produto pr on (p.idProduto = pr.Codigo)
		INNER JOIN SAC.dbo.TbPlanilhaItens i on (p.idPlanilhaItens = i.idPlanilhaItens)
		INNER JOIN SAC.dbo.TbPlanilhaEtapa e on (p.idPlanilhaEtapa = e.idPlanilhaEtapa)";
        if (!empty($item)) {
            $sql .=" WHERE i.idPlanilhaItens = ".$item;
        }
        if (!empty($nomeItem)) {
            $sql .=" AND i.Descricao ".$nomeItem;
        }
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchRow($sql);
    }

    /**
     * @param null $item
     * @param null $nomeItem
     * @return array
     * @deprecated ja existe o mesmo metodo em tbItensPlanilhaProduto chamado buscaItemProduto
     */
    public function produtoEtapaItem($item=null, $nomeItem=null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('p' => 'tbitensplanilhaproduto'), 'p.idplanilhaitens', $this->_schema)
            ->join(array('pr' => 'produto'), '(p.idproduto = pr.codigo)', array('pr.codigo as idProduto','pr.descricao as Produto'), $this->_schema)
            ->join(array('i' => 'tbplanilhaitens'), '(p.idplanilhaitens = i.idplanilhaitens)', array('i.descricao as NomeDoItem'), $this->_schema)
            ->join(array('e' => 'tbplanilhaetapa'), '(p.idplanilhaetapa = e.idplanilhaetapa)', array('e.idplanilhaetapa as idEtapa', 'e.descricao as Etapa'), $this->_schema)
            ;

        //$sql = "SELECT
        //'pr.Codigo as idProduto','pr.Descricao as Produto',
        //p.idPlanilhaItens,
        //'e.idPlanilhaEtapa as idEtapa', 'e.Descricao as Etapa',
        //i.Descricao as NomeDoItem
        //FROM SAC.dbo.tbItensPlanilhaProduto p
        //INNER JOIN SAC.dbo.Produto pr on (p.idProduto = pr.Codigo)
        //INNER JOIN SAC.dbo.TbPlanilhaItens i on (p.idPlanilhaItens = i.idPlanilhaItens)
        //INNER JOIN SAC.dbo.TbPlanilhaEtapa e on (p.idPlanilhaEtapa = e.idPlanilhaEtapa)";

        if (!empty($item)) {
            //$sql .=" WHERE i.idPlanilhaItens = ".$item;
            $sql->where("i.idplanilhaitens = ? ", $item);
        }
        if (!empty($nomeItem)) {
            //$sql .=" AND i.Descricao ".$nomeItem;
            $sql->where("i.descricao = ? ", $nomeItem);
        }

        return $db->fetchRow($sql);
    }

    public static function buscaproduto($where=null)
    {
        $sql = "SELECT Codigo as codproduto, Descricao as Produto
				FROM SAC.dbo.Produto WHERE stEstado = 0 ORDER BY Produto "; //WHERE stEstado = 0
        if (!empty($where)) {
            $sql .=" AND i.Descricao ".$where;
        }
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo buscaprodutoetapaitem()

    /**
     * buscaProduto
     *
     * @param bool $where
     * @access public
     * @return void
     * @deprecated movido para produto.php
     */
    public function listarProduto($where=null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('produto'), array('codigo as codproduto', 'descricao as Produto'), $this->_schema)
            ->where('stestado = 0')
            ->order('produto')
            ;

        //$sql = "SELECT Codigo as codproduto, Descricao as Produto
        //FROM SAC.dbo.Produto WHERE stEstado = 0 ORDER BY Produto "; //WHERE stEstado = 0

        return $db->fetchAll($sql);
    }

    public static function buscaetapa()
    {
        $sql = "SELECT idPlanilhaEtapa as codetapa, Descricao as Etapa
				FROM SAC.dbo.TbPlanilhaEtapa";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo buscaprodutoetapaitem()

    /**
     * listarEtapa
     *
     * @access public
     * @return void
     * @deprecated movido para tbPlanilhaEtapa.php
     */
    public function listarEtapa()
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('tbplanilhaetapa'), array('idplanilhaetapa as codetapa', 'descricao as Etapa'), $this->_schema)
            ;

        return $db->fetchAll($sql);
    }

    public static function buscaitem()
    {
        $sql = "Select idPlanilhaItens as coditens, Descricao as Item, idUsuario from SAC.dbo.tbPlanilhaItens
		 order by Descricao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo buscaprodutoetapaitem()

    /**
     * listarItem
     *
     * @access public
     * @return void
     * @deprecated movido para TbPlanilhaItens.php metodo listarItens()
     */
    public function listarItem()
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from('tbplanilhaitens', array('idplanilhaitens as coditens', 'descricao as Item', 'idusuario'), $this->_schema)
            ->order('descricao');

        return $db->fetchAll($sql);
    }

    /**
     * solicitacoes
     *
     * @param mixed $idAgente
     * @static
     * @access public
     * @return void
     * @todo migrar metodo para $this->solicitacao
     */
    public function solicitacoes($idagente)
    {
        $select = $this->select();
        $select->setintegritycheck(false);
        $select->from(
            array('sol' => $this->_name),
            array(
                "prod.codigo as idproduto",
                "prod.descricao as produto",
                "et.idplanilhaetapa",
                "et.descricao as etapa",
                "sol.idsolicitaritem",
                new zend_db_expr("(case when sol.idplanilhaitens > 0 then it.descricao else sol.nomedoitem end) as itemsolicitado"),
                "sol.descricao as justificativa",
                new zend_db_expr("(case sol.stestado when 0 then 'solicitado' when 1 then 'atendido' else 'negado' end) as estado"),
                "resposta"
            ),
            $this->_schema
        );

        $select->joininner(
            array('prod'=>'produto'),
            'sol.idproduto = prod.codigo',
            null,
            $this->_schema
        );

        $select->joininner(
            array('et'=>'tbplanilhaetapa'),
            'sol.idetapa = et.idplanilhaetapa',
            null,
            $this->_schema
        );

        $select->joinleft(
            array('it' => 'tbplanilhaitens'),
            'sol.idplanilhaitens = it.idplanilhaitens',
            null,
            $this->_schema
        );
        $select->where('sol.idagente = '.$idagente);
        $select->order('sol.idsolicitaritem');

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($select);
    }

    /**
     * solicitacao
     *
     * @param mixed $idAgente
     * @access public
     * @return void
     * @deprecated movido para tbSolicitarItem.php, metodo solicitacoes
     */
    public function solicitacao($idAgente)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $col = array(
            'prod.codigo as idproduto',
            'prod.descricao as produto',
            'et.idplanilhaetapa',
            'et.descricao as etapa',
            'sol.idsolicitaritem',
            new Zend_Db_Expr("CASE
                WHEN  sol.idplanilhaitens > 0 THEN it.descricao
                ELSE sol.nomedoitem
            END as itemsolicitado"),
            'sol.descricao as justificativa',
            new Zend_Db_Expr("CASE sol.stEstado
                WHEN 0 THEN 'Solicitado'
                WHEN 1 THEN 'Atendido'
                ELSE 'Negado'
            END as estado"),
            new Zend_Db_Expr('resposta')
        );

        $sql = $db->select()
            ->from(array('sol' => 'tbsolicitaritem'), $col, $this->_schema)
            ->join(array('prod' => 'produto'), 'sol.idproduto = prod.codigo', null, $this->_schema)
            ->join(array('et' => 'tbplanilhaetapa'), 'sol.idetapa = et.idplanilhaetapa', null, $this->_schema)
            ->joinLeft(array('it' => 'tbplanilhaitens'), 'sol.idplanilhaitens = it.idplanilhaitens', null, $this->_schema)
            ->where('sol.idagente = ?', $idAgente)
            ->order('sol.idsolicitaritem')
            ;

        return $db->fetchAll($sql);
    }

    public function cadastraritem($dadosassociar)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);


        $cadastrar = $this->insert($dadosassociar);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }

        /* $resultado = $db->query($sql);
         return $resultado;*/
    } // fecha m�todo cadastraritem()

    /**
     * cadastrarItemObj
     *
     * @param mixed $dadosassociar
     * @access public
     * @return void
     */
    public function cadastrarItemObj($dadosassociar)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $cadastrar = $this->insert($dadosassociar);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    public static function buscarItem($idAgente)
    {
        $sql = "SELECT TOP 1 idPlanilhaItens FROM SAC.dbo.tbPlanilhaItens where idUsuario = ".$idAgente." order by idPlanilhaItens desc";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    } // fecha m�todo buscarItem()

    public static function associaritem($dadosassociar)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $cadastrar = $db->insert("tbsolicitaritem", $dadosassociar);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    public function associarItemObj($dadosassociar)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $cadastrar = $db->insert($this->_schema.".tbsolicitaritem", $dadosassociar);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    public static function buscarSolicitacoes($where=array(), $nomeItem=null)
    {
        $sql = "SELECT prod.Codigo as idProduto,
                        prod.Descricao as Produto,
			et.idPlanilhaEtapa,
                        et.Descricao as Etapa,
			sol.idSolicitarItem,
			       CASE
			            WHEN  sol.IdPlanilhaItens > 0 THEN it.Descricao
			            ELSE sol.NomeDoItem
			       END as ItemSolicitado,
			       sol.Descricao as Justificativa,
			       CASE sol.stEstado
			            WHEN 0 THEN 'Solicitado'
			            WHEN 1 THEN 'Atendido'
			            ELSE 'Negado'
			       END as Estado,Resposta
			 FROM SAC.dbo.tbSolicitarItem sol
			      INNER JOIN SAC.dbo.Produto prod ON sol.idProduto = prod.Codigo
			      INNER JOIN SAC.dbo.tbPlanilhaEtapa et ON sol.idEtapa = et.idPlanilhaEtapa
			      LEFT JOIN SAC.dbo.TbPlanilhaItens it ON sol.idPlanilhaItens = it.idPlanilhaItens";

        $ct=1;
        foreach ($where as $coluna=>$valor) {
            if ($ct==1) {
                $sql .= " WHERE ".$coluna." = '".$valor."'";
            } else {
                $sql .= " AND ".$coluna." = '".$valor."'";
            }
            $ct++;
        }
        if (!empty($nomeItem)) {
            $sql .="AND (sol.NomeDoItem = '{$nomeItem}' OR it.Descricao = '{$nomeItem}')";
        }
        $sql .= " ORDER BY sol.idSolicitarItem";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function listarSolicitacoes($where=array(), $nomeItem=null)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cols = array(
            'prod.codigo as idProduto',
            'prod.descricao as Produto',
            'et.idplanilhaetapa',
            'et.descricao as Etapa',
            'sol.idsolicitaritem',
            new Zend_Db_Expr("CASE
                WHEN  sol.idplanilhaitens > 0 THEN it.descricao
                ELSE sol.nomedoitem
            END as ItemSolicitado"),
            'sol.descricao as Justificativa',
            new Zend_Db_Expr("CASE sol.stestado
                WHEN 0 THEN 'Solicitado'
                WHEN 1 THEN 'Atendido'
                ELSE 'Negado'
            END as Estado"),
            new Zend_Db_Expr('resposta')
        );

        $sql = $db->select()
            ->from(array('sol' => 'tbsolicitaritem'), $cols, $this->_schema)
            ->join(array('prod' => 'produto'), 'sol.idproduto = prod.codigo', null, $this->_schema)
            ->join(array('et' => 'tbplanilhaetapa'), 'sol.idetapa = et.idplanilhaetapa', null, $this->_schema)
            ->joinLeft(array('it' => 'tbplanilhaitens'), 'sol.idplanilhaitens = it.idplanilhaitens', null, $this->_schema)
            ;

        foreach ($where as $coluna=>$valor) {
            $sql->where($coluna .' =?', $valor);
        }

        if (!empty($nomeItem)) {
            $sql->where('sol.NomeDoItem = ?', $nomeItem);
            $sql->orWhere('it.descricao = ?', $nomeItem);
        }
        $sql->order('sol.idsolicitaritem');
        return $db->fetchAll($sql);
    }
}
