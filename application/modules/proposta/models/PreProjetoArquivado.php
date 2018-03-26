<?php
class Proposta_Model_PreProjetoArquivado  extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'PreProjetoArquivado';
    protected $_primary = 'idPreProjetoArquivado';

    public function recuperarQtdePropostaTecnicoOrgao($idTecnico, $idOrgao)
    {
        $sql = "
                SELECT count(*) as qtdePropostas
                FROM tbAvaliacaoProposta a
                INNER JOIN tabelas.dbo.vwUsuariosOrgaosGrupos  u ON (a.idTecnico = u.usu_Codigo)
                WHERE uog_orgao={$idOrgao} AND idTecnico={$idTecnico} and sis_codigo=21 and gru_codigo=92 and
                stEstado = 0 and year(DtAvaliacao)=year(Getdate()) and month(DtAvaliacao)=month(Getdate())";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function listar(
        $idAgente,
        $idResponsavel,
        $idAgenteCombo,
        $where = array(),
        $order = array(),
        $start = 0,
        $limit = 20,
        $search = null,
        $stEstado = 1
    )
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $subSql = $db->select()
            ->from(array('pr' => 'projetos'), array('idprojeto'), $this->_schema)
            ->where('a.idpreprojeto = pr.idprojeto')
        ;

        $sql = $db->select()
             ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto', 'a.DtArquivamento'), $this->_schema)
            ->join(array('ppa' => $this->_name), 'ppa.idpreprojeto = a.idpreprojeto', array('ppa.MotivoArquivamento', 'ppa.SolicitacaoDesarquivamento AS SolicitacaoDesarquivamento', 'ppa.Avaliacao', 'ppa.idAvaliador', 'ppa.dtSolicitacaoDesarquivamento', 'ppa.dtAvaliacao', 'ppa.stDecisao'), $this->getSchema($this->_schema))
             ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('a.idagente = ? ', $idAgente)
            ->where('a.stestado = ?', $stEstado)
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
        ;

        $sql2 = $db->select()
            ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto', 'a.DtArquivamento'), $this->_schema)
              ->join(array('ppa' => $this->_name), 'ppa.idpreprojeto = a.idpreprojeto', array('ppa.MotivoArquivamento', 'ppa.SolicitacaoDesarquivamento AS SolicitacaoDesarquivamento', 'ppa.Avaliacao', 'ppa.idAvaliador', 'ppa.dtSolicitacaoDesarquivamento', 'ppa.dtAvaliacao', 'ppa.stDecisao'), $this->getSchema($this->_schema))
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->join(array('c' => 'vinculacao'), 'b.idagente = c.idvinculoprincipal', array(), $this->getSchema('agentes'))
            ->join(array('d' => 'agentes'), 'c.idagente = d.idagente', array(), $this->getSchema('agentes'))
            ->join(array('e' => 'sgcacesso'), 'd.cnpjcpf = e.cpf', array(), $this->getSchema('controledeacesso'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('e.idusuario = ?', $idResponsavel)
            ->where('a.stestado = ?', $stEstado)
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
        ;

        $sql3 = $db->select()
            ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto', 'a.DtArquivamento'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->join(array('ppa' => $this->_name), 'ppa.idpreprojeto = a.idpreprojeto', array('ppa.MotivoArquivamento', 'ppa.SolicitacaoDesarquivamento AS SolicitacaoDesarquivamento', 'ppa.Avaliacao', 'ppa.idAvaliador', 'ppa.dtSolicitacaoDesarquivamento', 'ppa.dtAvaliacao', 'ppa.stDecisao'), $this->getSchema($this->_schema))                                            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('c' => 'nomes'), 'b.idagente = c.idagente', array('c.descricao as nomeproponente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('d' => 'sgcacesso'), 'a.idusuario = d.idusuario', array(), Proposta_Model_DbTable_PreProjeto::getSchema('controledeacesso'))
            ->join(array('e' => 'tbvinculoproposta'), 'a.idpreprojeto = e.idpreprojeto', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('f' => 'tbvinculo'), 'e.idvinculo = f.idvinculo', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->where('a.stestado = ?', $stEstado)
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
            ->where('e.sivinculoproposta = 2')
            ->where('f.idusuarioresponsavel = ?', $idResponsavel)
        ;

        if (!empty($idAgenteCombo)) {
            $sql->where('b.idagente = ?', $idAgenteCombo);
            $sql2->where('b.idagente = ?', $idAgenteCombo);
            $sql3->where('b.idagente = ?', $idAgenteCombo);
        }

        $sql = $db->select()->union(array($sql, $sql2,$sql3), Zend_Db_Select::SQL_UNION);

        $sqlFinal = $db->select()->from(array("p" => $sql));

        foreach ($where as $coluna=>$valor) {
            $sqlFinal->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sqlFinal->where('p.idpreprojeto like ? OR p.nomeprojeto like ? OR  p.nomeproponente like ?', '%'.$search['value'].'%');
        }

        /* $sqlFinal->where('SolicitacaoDesarquivamento IS NULL'); */
        //$sqlFinal->order($order);

        /* echo $sqlFinal;die; */
        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sqlFinal->limitPage($start, $limit);
        }

        return $db->fetchAll($sqlFinal);
    }

    /**
     * propostas
     * @param $idAgente
     * @param $idResponsavel
     * @param $idAgenteCombo
     * @param array $where
     * @param array $order
     * @param int $start
     * @param int $limit
     * @param null $search
     * @return array
     */
    public function propostasTotal($idAgente, $idResponsavel, $idAgenteCombo, $where = array(), $order = array(), $start = 0, $limit = 20, $search = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $subSql = $db->select()
            ->from(array('pr' => 'projetos'), array('idprojeto'), $this->_schema)
            ->where('a.idpreprojeto = pr.idprojeto')
        ;

        $sql = $db->select()
            ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->join(array('ppa' => $this->_name), 'ppa.idpreprojeto = a.idpreprojeto', array('ppa.*'), $this->getSchema($this->_schema))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('a.idagente = ? ', $idAgente)
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
        ;

        $sql2 = $db->select()
            ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->join(array('ppa' => $this->_name), 'ppa.idpreprojeto = a.idpreprojeto', array('ppa.*'), $this->getSchema($this->_schema))
            ->join(array('c' => 'vinculacao'), 'b.idagente = c.idvinculoprincipal', array(), $this->getSchema('agentes'))
            ->join(array('d' => 'agentes'), 'c.idagente = d.idagente', array(), $this->getSchema('agentes'))
            ->join(array('e' => 'sgcacesso'), 'd.cnpjcpf = e.cpf', array(), $this->getSchema('controledeacesso'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('e.idusuario = ?', $idResponsavel)
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
        ;

        $sql3 = $db->select()
            ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('ppa' => $this->_name), 'ppa.idpreprojeto = a.idpreprojeto', array('ppa.*'), $this->getSchema($this->_schema))
            ->join(array('c' => 'nomes'), 'b.idagente = c.idagente', array('c.descricao as nomeproponente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('d' => 'sgcacesso'), 'a.idusuario = d.idusuario', array(), Proposta_Model_DbTable_PreProjeto::getSchema('controledeacesso'))
            ->join(array('e' => 'tbvinculoproposta'), 'a.idpreprojeto = e.idpreprojeto', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('f' => 'tbvinculo'), 'e.idvinculo = f.idvinculo', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
            ->where('e.sivinculoproposta = 2')
            ->where('f.idusuarioresponsavel = ?', $idResponsavel)
        ;

        if (!empty($idAgenteCombo)) {
            $sql->where('b.idagente = ?', $idAgenteCombo);
            $sql2->where('b.idagente = ?', $idAgenteCombo);
            $sql3->where('b.idagente = ?', $idAgenteCombo);
        }

        $sql = $db->select()->union(array($sql, $sql2,$sql3), Zend_Db_Select::SQL_UNION);

        $sqlFinal = $db->select()->from(array("p" => $sql), array('count(distinct p.idpreprojeto)'));

        foreach ($where as $coluna=>$valor) {
            $sqlFinal->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sqlFinal->where('p.idpreprojeto like ? OR p.nomeprojeto like ? OR  p.nomeproponente like ?', '%'.$search['value'].'%');
        }

        $sqlFinal->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sqlFinal->limitPage($start, $limit);
        }
        return $db->fetchOne($sqlFinal);
    }

    public function listarSolicitacoes(
        $where = array(),
        $order = array(),
        $start = 0,
        $limit = 20,
        $search = null,
        $stEstado = 1
    )
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
             ->from(
                 array('a'=>'preprojeto'),
                 array(
                     'a.idpreprojeto',
                     'a.nomeprojeto',
                     'a.DtArquivamento'),
                 $this->_schema
             )
             ->join(
                 array('ppa' => $this->_name),
                 'ppa.idpreprojeto = a.idpreprojeto',
                 array(
                     'ppa.MotivoArquivamento',
                     'ppa.SolicitacaoDesarquivamento AS SolicitacaoDesarquivamento',
                     'ppa.Avaliacao',
                     'ppa.idAvaliador',
                     'ppa.dtSolicitacaoDesarquivamento',
                     'ppa.dtAvaliacao',
                     'ppa.stDecisao',
                     'ppa.stEstado'
                 ),
                 $this->getSchema($this->_schema))
             ->where("a.mecanismo = '1'");

        $sql = $db->select()->from(array("p" => $sql));

        $sql->where('SolicitacaoDesarquivamento IS NOT NULL');

        foreach ($where as $coluna=>$valor) {
            $sql->where($coluna, $valor);
        }

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sql->limitPage($start, $limit);
        }

        return $db->fetchAll($sql);
    }
}
