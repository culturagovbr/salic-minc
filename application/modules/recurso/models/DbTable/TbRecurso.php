<?php

class Recurso_Model_DbTable_TbRecurso extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbRecurso';
    protected $_primary = 'idRecurso';

    /**
     * @var Recurso_Model_TbRecurso
     */
    public $tbRecurso;

    public function __construct(array $config = array())
    {
        $this->tbRecurso = new Recurso_Model_TbRecurso();
        parent::__construct($config);
    }

    public function finalizarRecursos($idPronac, $fase = 1)
    {
        if (!empty($idPronac)) {

            $dados = array(
                'siRecurso' => Recurso_Model_TbRecurso::SI_RECURSO_FINALIZADO,
                'stEstado' => Recurso_Model_TbRecurso::SITUACAO_RECURSO_INATIVO
            );

            $where = array(
                'IdPRONAC = ?' => $idPronac,
                'stEstado = ?' => Recurso_Model_TbRecurso::SITUACAO_RECURSO_ATIVO,
                'siFaseProjeto = ?' => Recurso_Model_TbRecurso::SITUACAO_RECURSO_ATIVO
            );

            $this->alterar($dados, $where);
        }
    }

    /**
     * Retorna o projetos com desistencia recursal apenas fase 1
     *
     * @param mixed $idPronac
     * @access public
     * @return void
     */
    public function desistenciaRecursal($area, $faseProjeto = 1)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('r' => $this->_name), '*', $this->_schema)
            ->join(array('p' => 'projetos'), 'r.IdPRONAC = p.IdPRONAC', '*', $this->_schema)
            ->where('stEstado = 0')
            ->where("tpSolicitacao = 'DR'")
            ->where('siFaseProjeto = ?', $faseProjeto)
        ;

        if ($area == '2') {
            $select->where("area = 2");
        } else {
            $select->where("area <> 2");
        }

        $this->_db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $this->_db->fetchAll($select);
    }

    public function verificarSeTemDireitoPrimeiroRecurso($idPronac, $fase = null)
    {
        $query = $this->select()
            ->from(
                array('a' => 'tbRecurso'),
                array(new Zend_Db_Expr('TOP 1 a.idRecurso')),
                $this->_schema
            )
            ->where('a.stEstado = ?', 1)
            ->where('a.siRecurso = ?', 0)
            ->where('a.idPronac = ?', $idPronac);

        if ($fase) {
            $query->where('siFaseProjeto = ?', $fase);
        }

        return $this->fetchRow($query);
    }

    public function verificarSeTemDireitoASegundoRecurso($idPronac, $fase)
    {
        $query = $this->select()
            ->from(
                array('a' => 'tbRecurso'),
                array(new Zend_Db_Expr('TOP 1 idRecurso')),
                $this->_schema
            )
            ->where('a.stEstado = ?', 0)
            ->where('a.siRecurso <> ?', 0)
            ->where('a.idPronac = ?', $idPronac)
        ;

        if ($fase) {
            $query->where('siFaseProjeto = ?', $fase);
        }

        return $this->fetchRow($query);
    }

    public function verificarSeTemDireitoATerceiroRecurso($idPronac, $fase)
    {
        $query = $this->select()
            ->from(
                array('a' => 'tbRecurso'),
                array('a.idRecurso'),
                $this->_schema
            )
            ->joinInner(
                array('b' => 'tbReuniao'),
                'a.idNrReuniao = b.idNrReuniao',
                array('DtFinal'),
                $this->_schema
            )
            ->where('a.tpRecurso = ?', 1)
            ->where('a.siRecurso <> ?', 0)
            ->where('a.stEstado = ?', 1)
            ->where('a.idPronac = ?', $idPronac)
        ;

        if ($fase) {
            $query->where('a.siFaseProjeto = ?', $fase);
        }

        return $this->fetchRow($query);
    }

    public function verificarSeTemDireitoAQuartoRecurso($idPronac, $fase)
    {
        $query = $this->select()
            ->from(
                array('a' => 'tbRecurso'),
                [],
                $this->_schema
            )
            ->joinInner(
                array('b' => 'tbReuniao'),
                'a.idNrReuniao = b.idNrReuniao',
                array(
                    new Zend_Db_Expr("(DATEDIFF(DAY,(dtFinal ),GETDATE()) ) AS dado"),
                ),
                $this->_schema
            )
            ->where('a.idPronac = ?', $idPronac)
        ;

        if ($fase) {
            $query->where('a.siFaseProjeto = ?', $fase);
        }

        return $this->fetchRow($query);
    }

    public function verificarRecursoAdmissibilidade($idPronac)
    {
        $query = $this->select()
            ->from(
                array('a' => 'tbRecurso'),
                ['idRecurso'],
                $this->_schema
            );

        $query->where('a.idPronac = ?', $idPronac);
        $query->where('a.siFaseProjeto = ?', 1);

        return $this->fetchRow($query);
    }


    public function verificarRecursoIndeferido($idPronac, $fase = 1)
    {
        $query = $this->select()
            ->from(
                array('a' => 'tbRecurso'),
                ['idRecurso'],
                $this->_schema
            )->where('siFaseProjeto = ?', $fase)
            ->where('tpRecurso IN (?)', array(2))
            ->where('idPronac = ?', $idPronac);

        return $this->fetchRow($query);
    }

    public function verificarRecursoFinalizado($idPronac)
    {
        $query = $this->select()
            ->from(
                array('a' => 'tbRecurso'),
                ['idRecurso'],
                $this->_schema
            )->where('siFaseProjeto = ?', 1)
            ->where('siRecurso = ?', 15)
            ->where('stEstado = ?', 1)
            ->where('idPronac = ?', $idPronac)->limit(1);
        return $this->fetchRow($query);

    }
}
