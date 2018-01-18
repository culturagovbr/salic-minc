<?php
class Diligencia extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbDiligencia';
    protected $_schema = "SAC";


    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscar($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $idProduto = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("d"=>$this->_name),
                array(
                    "DtSolicitacao"=> new Zend_Db_Expr("CONVERT(CHAR(10),d.DtSolicitacao,121)"),
                    "idSolicitante",
                    "idProponente",
                    "idDiligencia",
                    "idTipoDiligencia"
                ),
                "SAC.dbo"
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($idProduto) {
            $slct->where('idProduto = ?', $idProduto);
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($slct);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarUltDiligencia($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("d"=>$this->_name),
                array("*"),
                "SAC.dbo"
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($slct);
    }

    /**
     * Retorna registros do banco de dados
     * @param int $idagente - id do agente
     * @param int $idpronac - id do pronac
     * @param boolean $resposta - TRUE ou FALSE
     * @param string ou array $situacao - situacao, ou um array de situacoes
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarDiligencia($idagente, $idpronac=null, $resposta=null, $situacao=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('D' => $this->_name),
                array(
                     new Zend_Db_Expr('CONVERT(CHAR(10),D.DtResposta,103) AS DtResposta'),
                     new Zend_Db_Expr('CONVERT(CHAR(10),D.dtSolicitacao,103) AS dtSolicitacao'),
                    'D.Resposta'
                    ),
                "SAC.dbo"
        );
        $select->joinInner(
                array('Pr' => 'Projetos'),
                'Pr.IdPRONAC = D.idPronac',
                array(
                    'Pr.idPRONAC',
                    '(Pr.AnoProjeto + Pr.Sequencial) AS PRONAC',
                    'Pr.NomeProjeto'
                ),
                'SAC.dbo'
        );
        $select->joinInner(
                array('Pa' => 'Parecer'),
                'Pa.IdPRONAC = D.idPronac',
                array("CASE WHEN Pa.ParecerFavoravel in ('2','3')
				  THEN 'Sim'
				  ELSE 'N�o'
				  End AS ParecerFavoravel"
                ),
                'SAC.dbo'
        );
        $select->where('Pa.TipoParecer = ?', 1);
        $select->where('Pa.stAtivo = ?', 1);
        $select->where('D.idSolicitante = ?', $idagente);
        if (!empty($situacao) && !is_array($situacao)) {
            $select->where('Pr.Situacao = ?', $situacao);
        } elseif (!empty($situacao) && is_array($situacao)) {
            $select->where('Pr.Situacao in (?)', $situacao);
        }
        if ($idpronac) {
            $select->where('Pr.IdPRONAC = ?', $idpronac);
        }

        if ($resposta) {
            $select->where('D.DtResposta is not null');
        } else {
            $select->where('D.DtResposta is null');
        }
        $select->where(new Zend_Db_Expr('D.DtSolicitacao = (SELECT TOP 1 max(DtSolicitacao) from SAC..tbDiligencia WHERE idPronac = Pr.IdPRONAC)'));

        return $this->fetchAll($select);
    }

    public function inserirDiligencia($dados)
    {
        $insert = $this->insert($dados);
    }

    public function diligenciasNaoRespondidas($retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array($this->_name),
                array(
                    'idPronac'
                    ),
            "SAC.dbo"
        );

        $select->where('((((DATEDIFF(day, DtSolicitacao, GETDATE()) > 20');
        $select->where("stProrrogacao =?)", 'N');
        $select->orWhere('(DATEDIFF(day, DtSolicitacao, GETDATE()) > 40');
        $select->where("stProrrogacao =?))", 'S');
        $select->where("idTipoDiligencia =?)", 124);
        $select->orWhere('(DATEDIFF(day, DtSolicitacao, GETDATE()) > 30');
        $select->where("idTipoDiligencia != ?))", 124);
        $select->where('DtResposta is null');

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    public function buscarProjetosDiligenciadosCNIC($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('D' => $this->_name),
                array(
                    new Zend_Db_Expr('CONVERT(CHAR(10),D.DtResposta,103) AS DtResposta'),
                    new Zend_Db_Expr('CONVERT(CHAR(10),D.dtSolicitacao,103) AS dtSolicitacao'),
                    'D.Resposta',
                    new Zend_Db_Expr('ISNULL((SELECT tpAcao FROM BDCORPORATIVO.scSAC.tbRetirarDePauta x WHERE stAtivo = 1 and  x.idPronac = pr.idPronac),0) as Acao'),
                    new Zend_Db_Expr('ISNULL((SELECT idRetirardepauta FROM BDCORPORATIVO.scSAC.tbRetirarDePauta x WHERE stAtivo = 1 and  x.idPronac = pr.idPronac),0) as idRetiradaPauta')
                    ),
                "SAC.dbo"
        );
        $select->joinInner(
                array('Pr' => 'Projetos'),
                'Pr.IdPRONAC = D.idPronac',
                array(
                    'Pr.idPRONAC',
                    '(Pr.AnoProjeto + Pr.Sequencial) AS PRONAC',
                    'Pr.NomeProjeto'
                ),
                'SAC.dbo'
        );
        $select->joinInner(
                array('Pa' => 'Parecer'),
                'Pa.IdPRONAC = D.idPronac  AND Pa.DtParecer = (SELECT TOP 1 max(DtParecer) from SAC..Parecer where IdPRONAC = Pr.IdPRONAC)',
                array("CASE WHEN Pa.ParecerFavoravel in ('2','3')
				  THEN 'Sim'
				  ELSE 'N�o'
				  End AS ParecerFavoravel"
                ),
                'SAC.dbo'
        );
        $select->joinInner(
                array('DPC' => 'tbDistribuicaoProjetoComissao'),
                'Pa.IdPRONAC = DPC.idPronac',
                array(),
                'BDCORPORATIVO.scSAC'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $select->where(new Zend_Db_Expr('D.DtSolicitacao = (SELECT TOP 1 max(DtSolicitacao) from SAC..tbDiligencia WHERE idPronac = Pr.IdPRONAC)'));
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
}
