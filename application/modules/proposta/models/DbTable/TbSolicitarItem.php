<?php

/**
 * Modelo que representa a tabela SAC.dbo.tbSolicitarItem
 * @version 1.0 - 08/01/2013
 */

class Proposta_Model_DbTable_TbSolicitarItem extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name   = 'tbsolicitaritem';
    protected $_primary = 'idSolicitarItem';


    public function listaSolicitacoesItens($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('s' => $this->_name),
            array('idSolicitarItem', 'Descricao as Justificativa', 'idPlanilhaItens',
                    new Zend_Db_Expr('
                        CASE
                            WHEN  s.IdPlanilhaItens > 0 THEN i.Descricao
                            ELSE s.NomeDoItem
                       END as ItemSolicitado
                    '),
                    new Zend_Db_Expr("
                        CASE
                            WHEN  s.IdPlanilhaItens > 0 THEN 'Associa&ccedil;&atilde;o'
                            ELSE 'Inclus&atilde;o'
                       END as TipoSolicitacao
                    "),
                    new Zend_Db_Expr("
                        CASE s.stEstado
                            WHEN 0 THEN 'Solicitado'
                            WHEN 1 THEN 'Atendido'
                            ELSE 'Negado'
                       END as Estado
                    ")
                )
        );
        $select->joinInner(
            array('p' => 'Produto'),
            "s.idProduto = p.Codigo",
            array('Codigo as idProduto', 'Descricao as Produto'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('e' => 'tbPlanilhaEtapa'),
            "s.idEtapa = e.idPlanilhaEtapa",
            array('idPlanilhaEtapa', 'Descricao as Etapa'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('i' => 'tbPlanilhaItens'),
            "s.idPlanilhaItens = i.idPlanilhaItens",
            array(''),
            'SAC.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($select);
    }

    public function buscarDadosItem($idItem)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name)
        );
        $select->where('idSolicitarItem = ?', $idItem);

        return $this->fetchRow($select);
    }

    public function buscarItens($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('sol' => $this->_name),
            array(
                "prod.Codigo as idProduto",
                "prod.Descricao as Produto",
                "et.idPlanilhaEtapa",
                "et.Descricao as Etapa",
                "sol.idSolicitarItem",
                new Zend_Db_Expr("(CASE WHEN  sol.IdPlanilhaItens > 0 THEN it.Descricao ELSE sol.NomeDoItem END) as ItemSolicitado"),
                "sol.Descricao as Justificativa",
                "sol.stEstado",
                new Zend_Db_Expr("(CASE sol.stEstado WHEN 0 THEN 'Solicitado' WHEN 1 THEN 'Atendido' ELSE 'Negado' END) as Estado"),
                "Resposta"
            ),
            $this->_schema
        );
        $select->joinInner(
            array('prod' => 'Produto'),
            'sol.idProduto = prod.Codigo',
            null,
            $this->_schema
        );

        $select->joinInner(
            array('et' => 'tbPlanilhaEtapa'),
            'sol.idEtapa = et.idPlanilhaEtapa',
            null,
            $this->_schema
        );

        $select->joinLeft(
            array('it' => 'TbPlanilhaItens'),
            'sol.idPlanilhaItens = it.idPlanilhaItens',
            null,
            $this->_schema
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            if (!empty($valor)) {
                $select->where($coluna, $valor);
            }
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        //adicionando linha order ao select
        $select->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($select);
    }

    public function exibirprodutoetapaitem($item=null, $nomeItem=null, $idEtapa=null, $idProduto=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('sol' => $this->_name),
            array(
                "pr.Codigo as idProduto",
                "pr.descricao as Produto",
                "e.idPlanilhaEtapa",
                "i.idPlanilhaItens",
            ),
            $this->_schema
        );


        $select->joinInner(
            array('pr'=>'Produto'),
            'sol.idProduto = pr.Codigo',
            null,
            $this->_schema
        );

        $select->joinInner(
            array('i' => 'TbPlanilhaItens'),
            'sol.idPlanilhaItens = i.idPlanilhaItens',
            null,
            $this->_schema
        );

        $select->joinInner(
            array('e' => 'tbPlanilhaEtapa'),
            'sol.idEtapa = e.idPlanilhaEtapa',
            null,
            $this->_schema
        );


        if (!empty($nomeItem)) {
            $select->where('i.Descricao = ', $nomeItem);
        }
        if (!empty($item)) {
            $select->where('i.idPlanilhaItens = ?', $item);
        }
        if (!empty($idEtapa)) {
            $select->where('e.idPlanilhaEtapa = ', $idEtapa);
        }
        if (!empty($idProduto)) {
            $select->where('pr.Codigo = ', $idProduto);
        }
        $select->order('pr.codigo Asc');

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($select);
    } // fecha metodo buscaprodutoetapaitem()


    public function exibirEtapa($idProduto)
    {
        $sql = "SELECT distinct  e.idPlanilhaEtapa as idEtapa, e.Descricao as Etapa
					FROM SAC.tbItensPlanilhaProduto p
					INNER JOIN SAC.Produto pr on (p.idProduto = pr.Codigo)
					INNER JOIN SAC.TbPlanilhaItens i on (p.idPlanilhaItens = i.idPlanilhaItens)
					INNER JOIN SAC.TbPlanilhaEtapa e on (p.idPlanilhaEtapa = e.idPlanilhaEtapa)
						Where  idProduto = {$idProduto} 
						 ORDER BY  e.Descricao  ASC ";

        //die('<pre>'.$sql);
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public function exibirItem($idProduto, $idEtapa)
    {
        $sql = "SELECT i.idPlanilhaItens as idItem,	i.Descricao as NomeDoItem
				FROM SAC.tbItensPlanilhaProduto p
				INNER JOIN SAC.Produto pr on (p.idProduto = pr.Codigo)
				INNER JOIN SAC.TbPlanilhaItens i on (p.idPlanilhaItens = i.idPlanilhaItens)
				INNER JOIN SAC.TbPlanilhaEtapa e on (p.idPlanilhaEtapa = e.idPlanilhaEtapa)
					Where  p.idProduto = ".$idProduto." and e.idPlanilhaEtapa = ".$idEtapa." 
					ORDER BY  i.Descricao  ASC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    /*********************************************************************************************************/


    public function buscaprodutoetapaitem($item=null, $nomeItem=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->getName('tbItensPlanilhaProduto')),
            array(
                "pr.Codigo as idProduto",
                "p.idPlanilhaItens",
                "e.idPlanilhaEtapa as idEtapa",
                "pr.Descricao as Produto",
                "e.Descricao as Etapa",
                "i.Descricao as NomeDoItem"
            ),
            $this->_schema
        );

        $select->joinInner(
            array('pr' => 'Produto'),
            'pr.Codigo = p.idProduto',
            array(),
            $this->_schema
        );

        $select->joinInner(
            array('e' => 'TbPlanilhaEtapa'),
            'e.idPlanilhaEtapa = p.idPlanilhaEtapa',
            array(),
            $this->_schema
        );

        $select->joinInner(
            array('i' => 'TbPlanilhaItens'),
            'i.idPlanilhaItens = p.idPlanilhaItens',
            array(),
            $this->_schema
        );

        if (!empty($item)) {
            $select->where('i.idPlanilhaItens = ?', $item);
        }
        if (!empty($nomeItem)) {
            $select->where('i.Descricao = ?', $nomeItem);
        }

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//        echo '<pre>';
//        var_dump($select->assemble());
//        exit;
        return $db->fetchRow($select);
    } // fecha metodo buscaprodutoetapaitem()


    public function buscaproduto($where=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('sol' => $this->getName('produto')),
            array(
                "Codigo as codProduto",
                "Descricao as Produto"
            ),
            $this->_schema
    );

        $select->where('stEstado = 0');

        $select->order('produto');

        if (!empty($where)) {
            $select->where('Descricao = ?', $nomeItem);
        }
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($select);
    } // fecha metodo buscaprodutoetapaitem()



    public function buscaetapa()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('sol' => $this->getName('tbplanilhaetapa')),
            array(
                "idplanilhaetapa as codetapa",
                "Descricao as etapa"
            ),
            $this->_schema
        );


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($select);
    } // fecha metodo buscaprodutoetapaitem()




    public function buscaitem()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('sol' => $this->getName('tbplanilhaitens')),
            array(
                "idPlanilhaItens as coditens",
                "Descricao as item",
                "idUsuario"
            ),
            $this->_schema
        );

        $select->order("descricao");

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($select);
    } // fecha metodo buscaprodutoetapaitem()



    public function solicitacoes($idAgente)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('sol' => $this->_name),
            array(
                "prod.Codigo as idProduto",
                "prod.descricao as Produto",
                "et.idPlanilhaEtapa",
                "et.Descricao as Etapa",
                "sol.idSolicitarItem",
                new Zend_Db_Expr("(CASE WHEN sol.IdPlanilhaItens > 0 THEN it.Descricao ELSE sol.NomeDoItem END) as ItemSolicitado"),
                "sol.Descricao as Justificativa",
                new Zend_Db_Expr("(CASE sol.stEstado WHEN 0 THEN 'Solicitado' WHEN 1 THEN 'Atendido' ELSE 'Negado' END) as Estado"),
                "Resposta"
            ),
            $this->_schema
        );

        $select->joinInner(
            array('prod'=>'Produto'),
            'sol.idProduto = prod.Codigo',
            null,
            $this->_schema
        );

        $select->joinInner(
            array('et'=>'tbPlanilhaEtapa'),
            'sol.idEtapa = et.idPlanilhaEtapa',
            null,
            $this->_schema
        );

        $select->joinLeft(
            array('it' => 'TbPlanilhaItens'),
            'sol.idPlanilhaItens = it.idPlanilhaItens',
            null,
            $this->_schema
        );
        $select->where('sol.idAgente = '.$idAgente);
        $select->order('sol.idsolicitaritem');

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($select);
    } // fecha metodo buscaprodutoetapaitem()





    public function cadastraritem($dadosassociar)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);


        //Codigo anterior
        /*$sql = "insert into SAC.tbPlanilhaItens (Descricao, idUsuario)
                            values ('".$Descricao."', ".$idAgente.")";*/

        $cadastrar = $this->insert($dadosassociar);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }

        /* $resultado = $db->query($sql);
         return $resultado;*/
    } // fecha metodo cadastraritem()



    public function buscarItem($idAgente)
    {
        $sql = "SELECT TOP 1 idPlanilhaItens FROM SAC.tbPlanilhaItens where idUsuario = ".$idAgente." order by idPlanilhaItens desc";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_ASSOC);
        return $db->fetchRow($sql);
    } // fecha metodo buscarItem()



    public function associaritem($dadosassociar)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $cadastrar = $db->insert("SAC.tbSolicitarItem", $dadosassociar);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    public function buscarSolicitacoes($where=array(), $nomeItem=null)
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
			 FROM SAC.tbSolicitarItem sol
			      INNER JOIN SAC.Produto prod ON sol.idProduto = prod.Codigo
			      INNER JOIN SAC.tbPlanilhaEtapa et ON sol.idEtapa = et.idPlanilhaEtapa
			      LEFT JOIN SAC.TbPlanilhaItens it ON sol.idPlanilhaItens = it.idPlanilhaItens";

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
}
