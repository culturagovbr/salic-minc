<?php

class Proposta_Model_DbTable_PreProjeto extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "preprojeto";
    protected $_primary = "idPreProjeto";

    public $_totalRegistros = null;

    /**
     * __construct
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * retirarProjetos
     *
     * @param mixed $idUsuario
     * @param mixed $idUsuarioR
     * @param mixed $idAgente
     *
     * @access public
     * @return void
     */
    public function retirarProjetos($idUsuario, $idUsuarioR, $idAgente)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $where['idUsuario = ?'] = $idUsuarioR;
        $where['idAgente = ?'] = $idAgente;

        return $db->update('SAC.dbo.PreProjeto', array('idUsuario' => $idUsuario), $where);
    }

    /**
     * retirarProjetosVinculos
     *
     * @param mixed $siVinculoProposta
     * @param mixed $idVinculo
     *
     * @access public
     * @return void
     * @todo mover para o lugar correto
     */
    public function retirarProjetosVinculos($siVinculoProposta, $idVinculo)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $where['idVinculo = ? '] = $idVinculo;

        return $db->update('Agentes.dbo.tbVinculoProposta', array('siVinculoProposta' => $siVinculoProposta), $where);
    }

    /**
     * listaProjetos
     *
     * @param mixed $idUsuario
     *
     * @access public
     * @return void
     */
    public function listaProjetos($idUsuario)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $subSql = $db->select()
            ->from(array('pr' => 'projetos'), array('*'), $this->_schema)
            ->where('pr.idProjeto = p.idPreProjeto');

        $p = array(
            'p.idPreProjeto',
            'idagente',
            'NomeProjeto',
            'Mecanismo',
            'stTipoDemanda'
        );

        $sql = $db->select()
            ->from(array('p' => 'PreProjeto'), $p, $this->_schema)
            ->where('stEstado = 1')
            ->where("stTipoDemanda like 'NA'")
            ->where('idUsuario = ?', $idUsuario)
            ->where(new Zend_Db_Expr("not exists ($subSql)"));

        return $db->fetchAll($sql);
    }

    /**
     * Retorna registros do banco de dados referente a Agentes(Proponente)
     * @param aray $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscar($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
            $this,
            array(
                "*",
                "dtiniciodeexecucaoform" => $this->getExpressionToChar("dtiniciodeexecucao"),
                "dtfinaldeexecucaoform" => $this->getExpressionToChar("dtfinaldeexecucao"),
                "dtatotombamentoform" => $this->getExpressionToChar("dtatotombamento"),
                "dtaceiteform" => $this->getExpressionToChar("dtaceite"),
                "dtarquivamentoform" => $this->getExpressionToChar("dtarquivamento"),
            )
        );

        $slct->joinLeft(
            ['vTipicidade' => 'Verificacao'],
            'preprojeto.tpTipicidade = vTipicidade.idVerificacao',
            ["vTipicidade.Descricao as DescricaoTipicidade"],
            $this->_schema
        );

        $slct->joinLeft(
            ['vTipologia' => 'Verificacao'],
            'preprojeto.tpTipologia = vTipologia.idVerificacao',
            ["vTipologia.Descricao as DescricaoTipologia"],
            $this->_schema
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
     * Retorna registros do banco de dados referente a Agentes(Proponente)
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscaCompleta($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('a' => $this->_name),
            array("a.*",
                "a.ResumoDoProjeto" => "CAST(a.ResumoDoProjeto AS TEXT) as ResumoDoProjeto",
                "a.Objetivos" => "CAST(a.Objetivos AS TEXT) as Objetivos",
                "a.Justificativa" => "CAST(a.Justificativa AS TEXT) as Justificativa",
                "a.Acessibilidade" => "CAST(a.Acessibilidade AS TEXT) as Acessibilidade",
                "a.DemocratizacaoDeAcesso" => "CAST(a.DemocratizacaoDeAcesso AS TEXT) as DemocratizacaoDeAcesso",
                "a.EtapaDeTrabalho" => "CAST(a.EtapaDeTrabalho AS TEXT) as EtapaDeTrabalho",
                "a.FichaTecnica" => "CAST(a.FichaTecnica AS TEXT) as FichaTecnica",
                "a.Sinopse" => "CAST(a.Sinopse AS TEXT) as Sinopse",
                "a.ImpactoAmbiental" => "CAST(a.ImpactoAmbiental AS TEXT) as ImpactoAmbiental",
                "a.EspecificacaoTecnica" => "CAST(a.EspecificacaoTecnica AS TEXT) as EspecificacaoTecnica",
                "a.EstrategiadeExecucao" => "CAST(a.EstrategiadeExecucao AS TEXT) as EstrategiadeExecucao",
                "a.DtInicioDeExecucaoForm" => $this->getExpressionToChar(DtInicioDeExecucao),
            ),
            $this->_schema
        );

        $slct->joinLeft(
            array('ag' => 'Agentes'),
            'a.idAgente = ag.idAgente',
            array("ag.CNPJCPF as CNPJCPF"),
            $this->getSchema('agentes')
        );

        $slct->joinLeft(
            array('m' => 'Nomes'),
            'a.idAgente = m.idAgente',
            array("m.Descricao as NomeAgente"),
            $this->getSchema('agentes')
        );

        $slct->joinLeft(
            array('i' => 'Internet'),
            'a.idAgente = i.idAgente and i.Status = 1',
            array("i.Descricao as EmailAgente"),
            $this->getSchema('agentes')
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $queryPropostaComProjeto = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                ['pr' => 'projetos'],
                ['idPronac'],
                $this->_schema)
            ->where('a.idPreProjeto = pr.idProjeto', '')
            ->where('pr.Situacao != ?', Projeto_Model_Situacao::PROJETO_LIBERADO_PARA_AJUSTES);

        $slct->where(new Zend_Db_Expr("NOT EXISTS({$queryPropostaComProjeto})"));

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

    public function salvarMovimentacao($arrData)
    {
        $date = new DateTime();
        if (!$this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
            $mprMovimentacao = new Proposta_Model_TbMovimentacaoMapper();
            $arrMovimentacaoDb = $mprMovimentacao->findBy(array('idprojeto' => $arrData['idpreprojeto']));
            if (empty($arrMovimentacaoDb)) {
                $arrMovimentacao = array();
                $arrMovimentacao['idprojeto'] = $arrData['idpreprojeto'];
                $arrMovimentacao['movimentacao'] = 95;
                $arrMovimentacao['dtmovimentacao'] = $date->format('Y-m-d H:i:sP');
                $arrMovimentacao['stestado'] = 0;
                $arrMovimentacao['usuario'] = $arrData['idusuario'];
                $mprMovimentacao->save(new Proposta_Model_TbMovimentacao($arrMovimentacao));
            }
        }
    }

    /**
     * Grava registro. Se seja passado um ID ele altera um registro existente
     * @param array $dados - array com dados referentes as colunas da tabela no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @return ID do registro inserido/alterado ou FALSE em caso de erro
     */
    public function salvar($dados)
    {
        //DECIDINDO SE INCLUI OU ALTERA UM REGISTRO
        if (!empty($dados['idpreprojeto'])) {
            //UPDATE
            $rsPreProjeto = $this->find($dados['idpreprojeto'])->current();
        } else {
            //INSERT
            unset($dados['idpreprojeto']);
            $id = $this->insert(array_filter($dados));
            $dados['idpreprojeto'] = $id;
            $this->salvarMovimentacao($dados);
            return $id;
        }

        # ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
        $rsPreProjeto->idAgente = $dados["idagente"];
        $rsPreProjeto->NomeProjeto = $dados["nomeprojeto"];
        $rsPreProjeto->Mecanismo = $dados["mecanismo"];
        $rsPreProjeto->AgenciaBancaria = $dados["agenciabancaria"];
        $rsPreProjeto->AreaAbrangencia = $dados["areaabrangencia"];
        $rsPreProjeto->DtInicioDeExecucao = $dados["dtiniciodeexecucao"];
        $rsPreProjeto->DtFinalDeExecucao = $dados["dtfinaldeexecucao"];
        $rsPreProjeto->NrAtoTombamento = $dados["nratotombamento"];
        $rsPreProjeto->DtAtoTombamento = (isset($dados["dtatotombamento"])) ? $dados["dtatotombamento"] : null;
        $rsPreProjeto->EsferaTombamento = $dados["esferatombamento"];
        $rsPreProjeto->DescricaoAtividade = $dados["descricaoatividade"];
        $rsPreProjeto->ResumoDoProjeto = $dados["resumodoprojeto"];
        $rsPreProjeto->Objetivos = $dados["objetivos"];
        $rsPreProjeto->Justificativa = $dados["justificativa"];
        $rsPreProjeto->Acessibilidade = $dados["acessibilidade"];
        $rsPreProjeto->DemocratizacaoDeAcesso = $dados["democratizacaodeacesso"];
        $rsPreProjeto->EtapaDeTrabalho = $dados["etapadetrabalho"];
        $rsPreProjeto->FichaTecnica = $dados["fichatecnica"];
        $rsPreProjeto->Sinopse = $dados["sinopse"];
        $rsPreProjeto->ImpactoAmbiental = $dados["impactoambiental"];
        $rsPreProjeto->EspecificacaoTecnica = $dados["especificacaotecnica"];
        $rsPreProjeto->EstrategiadeExecucao = $dados["estrategiadeexecucao"];
        $rsPreProjeto->dtAceite = $dados["dtaceite"];
        $rsPreProjeto->DtArquivamento = (isset($dados["dtarquivamento"])) ? $dados["dtarquivamento"] : null;
        $rsPreProjeto->stEstado = $dados["stestado"];
        $rsPreProjeto->stDataFixa = $dados["stdatafixa"];
        $rsPreProjeto->stProposta = $dados["stproposta"];
        $rsPreProjeto->idUsuario = $dados["idusuario"];
        $rsPreProjeto->stTipoDemanda = $dados["sttipodemanda"];
        $rsPreProjeto->idEdital = (isset($dados["idedital"])) ? $dados["idedital"] : null;
        $rsPreProjeto->tpProrrogacao = $dados["tpprorrogacao"];
        $rsPreProjeto->tpTipicidade = $dados["tptipicidade"];
        $rsPreProjeto->tpTipologia = $dados["tptipologia"];

        //SALVANDO O OBJETO
        $id = $rsPreProjeto->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * consultaTodosProjetos
     *
     * @param mixed $idAgente
     * @param mixed $idResponsavel
     * @param mixed $arrBusca
     *
     * @access public
     * @return void
     */
    public function consultaTodosProjetos($idAgente, $idResponsavel, $arrBusca)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $a = array(
            new Zend_Db_Expr('0 as Ordem'),
            'a.idPreProjeto',
            'a.NomeProjeto',
            'a.idUsuario',
            'a.idAgente'
        );

        $subSql = $db->select()
            ->from(array('pr' => 'projetos'), new Zend_Db_Expr('1'), $this->_schema)
            ->where('a.idpreprojeto = pr.idprojeto');

        $sql = $db->select()
            ->from(array('a' => 'preprojeto'), $a, $this->_schema)
            ->join(array('ag' => 'agentes'), 'a.idagente = ag.idagente', 'ag.cnpjcpf AS CNPJCPF', $this->getSchema('agentes'))
            ->join(array('m' => 'nomes'), 'a.idagente = m.idagente', 'm.descricao AS NomeAgente', $this->getSchema('agentes'))
            ->where('a.idAgente = ?', $idAgente)
            ->where(new Zend_Db_Expr("NOT EXISTS($subSql)"));

        foreach ($arrBusca as $value) {
            $sql->where($value);
        }

        $aSql = array(
            new Zend_Db_Expr('1 as Ordem'),
            'a.idPreProjeto',
            'a.NomeProjeto',
            'a.idUsuario',
            'a.idAgente'
        );

        $sql2 = $db->select()
            ->from(array('a' => 'preprojeto'), $aSql, $this->_schema)
            ->join(array('ag' => 'agentes'), '(a.idagente = ag.idagente)', 'ag.cnpjcpf AS CNPJCPF', $this->getSchema('agentes'))
            ->join(array('m' => 'nomes'), '(a.idagente = m.idagente)', 'm.descricao AS NomeAgente', $this->getSchema('agentes'))
            ->join(array('s' => 'SGCacesso'), 'a.idUsuario = s.IdUsuario', null, $this->getSchema('controledeacesso'))
            ->where('a.idusuario = ?', $idResponsavel)
            ->where('ag.CNPJCPF <> s.Cpf')
            ->where(new Zend_Db_Expr('NOT EXISTS(SELECT 1 FROM sac.dbo.projetos pr WHERE  a.idpreprojeto = pr.idprojeto)'));

        foreach ($arrBusca as $value) {
            $sql2->where($value);
        }

        $sql = $db->select()->union(array($sql, $sql2), Zend_Db_Select::SQL_UNION_ALL)
            ->order(new Zend_Db_Expr('1'))
            ->order('m.Descricao ASC');

        return $db->fetchAll($sql);
    }

    /**
     * consultaprojetos
     *
     * @param mixed $idagente
     *
     * @access public
     * @return void
     */
    public function consultaprojetos($idagente)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->from('PreProjeto', array('idPreProjeto', 'idagente', 'NomeProjeto', 'Mecanismo'), $this->_schema)
            ->where('idagente = ?', $idagente)
            ->order('nomeprojeto');

        return $db->fetchAll($sql);
    }

    /**
     * inserirProposta
     *
     * @param mixed $dados
     *
     * @access public
     * @return void
     */
    public function inserirProposta($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $cadastrar = $db->insert("SAC.dbo.PreProjeto", $dados);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * alterarDados
     *
     * @param mixed $dados
     * @param mixed $where
     *
     * @access public
     * @return void
     */
    public function alterarDados($dados, $where)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $cadastrar = $db->update("SAC.dbo.PreProjeto", $dados, $where);

        if ($cadastrar) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * listaUF
     *
     *
     * @access public
     * @return void
     * @todo mover para o lugar correto
     */
    public function listaUF()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->from('UF', array('*'), $this->getSchema('agentes'))->order('Sigla');

        return $db->fetchAll($sql);
    }

    /**
     * buscaIdAgente
     *
     * @param mixed $CNPJCPF
     *
     * @access public
     * @return void
     * @todo mover para o lugar correto
     */
    public function buscaIdAgente($CNPJCPF)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->from('Agentes', array('*'), $this->getSchema('agentes'))->where('CNPJCPF = ?', $CNPJCPF);

        return $db->fetchAll($sql);
    }

    /**
     * inserirAgentes
     *
     * @param mixed $dadosAgentes
     *
     * @access public
     * @return void
     * @todo mover para o lugar correto
     */
    public function inserirAgentes($dadosAgentes)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $Agentes = $db->insert("Agentes.dbo.Agentes", $dadosAgentes);
    }

    /**
     * inserirNomes
     *
     * @param mixed $dadosNomes
     *
     * @access public
     * @return void
     * @todo mover para o lugar correto
     */
    public function inserirNomes($dadosNomes)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $Nomes = $db->insert("Agentes.dbo.Nomes", $dadosNomes);
    }

    /**
     * inserirEnderecoNacional
     *
     * @param mixed $dadosEnderecoNacional
     *
     * @access public
     * @return void
     * @todo mover para o lugar correto
     */
    public function inserirEnderecoNacional($dadosEnderecoNacional)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $Nomes = $db->insert("Agentes.dbo.EnderecoNacional", $dadosEnderecoNacional);
    }

    /**
     * inserirVisao
     *
     * @param mixed $dadosVisao
     *
     * @access public
     * @return void
     * @todo mover para o lugar correto
     */
    public function inserirVisao($dadosVisao)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $Nomes = $db->insert("Agentes.dbo.Visao", $dadosVisao);
    }

    /**
     * editarproposta
     *
     * @param mixed $idPreProjeto
     *
     * @access public
     * @return void
     */
    public function editarproposta($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->from('PreProjeto', array('*'), $this->_schema)->where('idPreProjeto = ?', $idPreProjeto);

        return $db->fetchAll($sql);
    }

    /**
     * recuperarTecnicosOrgao
     *
     * @param mixed $idOrgaoSuperior
     * @access public
     * @return void
     * @todo mover para o lugar correto
     */
    public function recuperarTecnicosOrgao($idOrgaoSuperior)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->from('vwUsuariosOrgaosGrupos', array('usu_codigo', 'uog_orgao'), 'tabelas.dbo')
            ->where('sis_codigo=21')
            ->where('gru_codigo=92')
            ->where('uog_status = 1')
            ->where('uog_orgao = ?', $idOrgaoSuperior);

        return $db->fetchAll($sql);
    }

    /**
     * listarDiligenciasPreProjeto
     *
     * @param bool $consulta
     * @param bool $retornaSelect
     * @access public
     * @return void
     */
    public function listarDiligenciasPreProjeto($consulta = array(), $retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pre' => $this->_name),
            array('nomeProjeto' => 'pre.nomeprojeto', 'pronac' => 'pre.idpreprojeto'),
            $this->_schema
        );

        $select->joinInner(
            array('aval' => 'tbAvaliacaoProposta'),
            'aval.idProjeto = pre.idPreProjeto',
            array(
                'aval.stProrrogacao',
                'idDiligencia' => 'aval.idAvaliacaoProposta',
                'idAvaliacaoProposta' => 'aval.idAvaliacaoProposta',
                'dataSolicitacao' => new Zend_Db_Expr('CONVERT(VARCHAR,aval.DtAvaliacao,120)'),
                'dataResposta' => new Zend_Db_Expr('CONVERT(VARCHAR,aval.dtResposta,120)'),
                'Solicitacao' => 'aval.Avaliacao',
                'Resposta' => 'aval.dsResposta',
                'aval.idCodigoDocumentosExigidos',
                'aval.stEnviado'
            ),
            $this->_schema
        );

        $select->joinLeft(
            array('arq' => 'tbArquivo'),
            'arq.idArquivo = aval.idArquivo',
            array(
                'arq.nmArquivo',
                'arq.idArquivo'
            ),
            $this->getSchema('bdcorporativo', true, 'sccorp')
        );

        $select->joinLeft(
            array('a' => 'AGENTES'),
            'pre.idAgente = a.idAgente',
            array(
                'a.idAgente'
            ),
            $this->getSchema('agentes')
        );

        $select->joinLeft(
            array('n' => 'NOMES'),
            'a.idAgente = n.idAgente',
            array(
                'n.Descricao'
            ),
            $this->getSchema('agentes')
        );

        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }


        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    /**
     * dadosPreProjeto
     *
     * @param bool $consulta
     * @access public
     * @return void
     */
    public function dadosPreProjeto($consulta = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pre' => $this->_name),
            array(
                'nomeProjeto' => 'pre.NomeProjeto',
                'pronac' => 'pre.idPreProjeto'
            )
        );

        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    /**
     * buscarAgentePreProjeto
     *
     * @param bool $consulta
     * @access public
     *
     * @todo deletar depois, pois e so usar o findAll da abscract
     */
    public function buscarAgentePreProjeto($consulta = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pre' => $this->_name),
            array(
                'pre.idAgente'
            ),
            $this->_schema
        );

        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    /**
     * listaAvaliadores
     *
     * @param bool $where
     * @access public
     * @return void
     */
    public function listaAvaliadores($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pp' => $this->_name),
            array('pp.idPreProjeto')
        );
        $slct->joinInner(
            array('ave' => 'tbAvaliadorEdital'),
            'ave.idEdital = pp.idEdital',
            array('ave.idAvaliador'),
            'BDCORPORATIVO.scSAC'
        );
        $slct->joinInner(
            array('nom' => 'Nomes'),
            'nom.idAgente = ave.idAvaliador',
            array('nom.Descricao'),
            $this->getSchema('agentes')
        );
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        return $this->fetchAll($slct);
    }

    /**
     * listaApenasAvaliadores
     *
     * @param bool $where
     * @access public
     * @return void
     */
    public function listaApenasAvaliadores($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pp' => $this->_name),
            array('')
        );
        $slct->joinInner(
            array('ave' => 'tbAvaliadorEdital'),
            'ave.idEdital = pp.idEdital',
            array(new Zend_Db_Expr('distinct(ave.idAvaliador)')),
            'BDCORPORATIVO.scSAC'
        );
        $slct->joinInner(
            array('nom' => 'Nomes'),
            'nom.idAgente = ave.idAvaliador',
            array('nom.Descricao'),
            $this->getSchema('agentes')
        );
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        return $this->fetchAll($slct);
    }

    /**
     * buscarPropostaEditalCompleto
     *
     * @param bool $where
     * @access public
     * @return void
     */
    public function buscarPropostaEditalCompleto($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('p' => $this->_name),
            array('p.idPreProjeto,
                       p.idAgente,
                       p.NomeProjeto,
                       p.Mecanismo,
                       p.AgenciaBancaria,
                       p.DtInicioDeExecucao,
                       p.DtFinalDeExecucao,
                       p.stTipoDemanda,
                       p.idEdital,
                       CAST(p.ResumoDoProjeto as TEXT) as ResumoDoProjeto')

        );
        $slct->joinLeft(
            array('fd' => 'tbFormDocumento'),
            'fd.idEdital = p.idEdital OR p.idEdital IS NOT NULL',
            array('fd.nrFormDocumento', 'fd.nrVersaoDocumento'),
            'BDCORPORATIVO.scQuiz'
        );

        $slct->joinInner(
            array('nm' => 'Nomes'),
            'nm.idAgente = p.idAgente',
            array('nm.Descricao as nomeAgente'),
            $this->getSchema('agentes')
        );
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    /**
     * dadosProjetoDiligencia
     *
     * @param mixed $idProjeto
     * @access public
     * @return void
     */
    public function dadosProjetoDiligencia($idProjeto)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('p' => $this->_name),
            array(
                'idProjeto' => 'p.idPreProjeto',
                'p.NomeProjeto'
            )
        );
        $slct->joinInner(
            array("a" => "Agentes"),
            "a.idAgente = p.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array("n" => "Nomes"),
            "a.idAgente = n.idAgente",
            array('Destinatario' => 'Descricao'),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array("int" => "Internet"),
            "a.idAgente = int.idAgente",
            array('Email' => 'Descricao'),
            $this->getSchema('agentes')
        );

        $slct->where('p.idPreProjeto = ?', $idProjeto);
        $slct->where('a.Status = 0'); // Status do registro da pessoa, 0 - para ativo e 1 - para inativo

        return $this->fetchAll($slct);
    }

    /**
     * analiseDeCustos
     *
     * @param mixed $idPreProjeto
     *
     * @access public
     * @return void
     */
    public function analiseDeCustos($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $a = array(
            'a.idPreProjeto',
            'a.NomeProjeto',
        );

        $d = array(
            new Zend_Db_Expr("CONVERT(varchar(8),d.idPlanilhaEtapa) + ' - ' + d.Descricao AS Etapa"),
            'd.idPlanilhaEtapa'
        );

        $z = array(
            'z.idProduto',
            new Zend_Db_Expr("CASE WHEN z.idProduto = 0 THEN 'Administração do Projeto' ELSE c.Descricao END AS Produto"),
            new Zend_Db_Expr('z.Quantidade * z.Ocorrencia * z.ValorUnitario AS VlSolicitado'),
            'z.Quantidade',
            'z.Ocorrencia',
            'z.ValorUnitario',
            'z.QtdeDias',
            'z.TipoDespesa',
            'z.TipoPessoa',
            'z.Contrapartida',
            'z.dsJustificativa as JustificativaProponente',
            'z.FonteRecurso AS idFonte',
            new Zend_Db_Expr('ROUND(z.Quantidade * z.Ocorrencia * z.ValorUnitario, 2) AS Sugerido'),
            'z.idUsuario'
        );

        $f = array(
            'f.UF',
            'f.idUF',
            'f.Municipio',
            'f.idMunicipio',
        );

        $schema = parent::getStaticTableName('sac');
        $sql = $db->select()->from(array('a' => 'PreProjeto'), $a, $schema)
            ->join(array('z' => 'tbPlanilhaProposta'), 'z.idProjeto = a.idPreProjeto', $z, $schema)
            ->joinLeft(array('c' => 'Produto'), 'c.Codigo = z.idProduto', array(), $schema)
            ->join(array('d' => 'tbPlanilhaEtapa'), 'd.idPlanilhaEtapa = z.idEtapa', $d, $schema)
            ->join(array('e' => 'tbPlanilhaUnidade'), 'e.idUnidade = z.Unidade', array('e.Descricao AS Unidade'), $schema)
            ->join(array('i' => 'tbPlanilhaItens'), 'i.idPlanilhaItens = z.idPlanilhaItem', array('i.Descricao AS Item'), $schema)
            ->join(array('x' => 'Verificacao'), 'x.idVerificacao = z.FonteRecurso', array('x.Descricao AS FonteRecurso'), $schema)
            ->join(array('f' => 'vUFMunicipio'), 'f.idUF = z.UfDespesa AND f.idMunicipio = z.MunicipioDespesa', $f, parent::getStaticTableName('agentes'))
            ->where('a.idPreProjeto = ?', $idPreProjeto)
            ->order(array('x.Descricao', 'Produto', 'Etapa', 'UF', 'Item'));

        return $db->fetchAll($sql);
    }

    /**
     * tecnicoTemProposta
     *
     * @param mixed $idTecnico
     * @access public
     * @return void
     */
    public function tecnicoTemProposta($idTecnico)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("ap" => "tbAvaliacaoProposta"),
            array("*"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("m" => "tbMovimentacao"),
            "ap.idProjeto = m.idProjeto",
            array(),
            "SAC.dbo"
        );

        $condicao = new Zend_Db_Expr("select top 1 * from SAC..Projetos p where p.idProjeto = ap.idProjeto");
        $slct->where("ap.idTecnico = ?", $idTecnico);
        $slct->where("m.stEstado = 0");
        $slct->where("m.Movimentacao IN (96,97,128)");
        $slct->where("NOT EXISTS({$condicao})");

        $rs = $this->fetchAll($slct)->toArray();
        if (count($rs) > 0) {
            return true;
        }

        return false;
    }

    /**
     * alteraproponente
     *
     * @param mixed $idPreProjeto
     * @param mixed $idAgente
     *
     * @access public
     * @return void
     */
    public function alteraproponente($idPreProjeto, $idAgente)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $where['idPreProjeto = ?'] = $idPreProjeto;

        return $db->update('SAC.dbo.PreProjeto', array('idAgente' => $idAgente), $where);
    }

    /**
     * alteraresponsavel
     *
     * @param mixed $idPreProjeto
     * @param mixed $idResponsavel
     *
     * @access public
     */
    public function alteraresponsavel($idPreProjeto, $idResponsavel)
    {
        $where['idpreprojeto = ?'] = $idPreProjeto;

        return $this->alterar(array('idusuario' => $idResponsavel), $where);
    }

    /**
     * BuscarPropostaProjetos Busca as propostas/projetos vinculados ao proponente
     *
     * @param bool $where
     * @access public
     * @return void
     */
    public function buscarPropostaProjetos($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pp' => $this->_name),
            array('pp.idPreProjeto',
                'pp.idAgente',
                'pp.NomeProjeto',
                'pp.Mecanismo',
                'pp.AgenciaBancaria',
                'pp.DtInicioDeExecucao',
                'pp.DtFinalDeExecucao',
                'pp.stTipoDemanda',
                'pp.idUsuario',
                'pp.idEdital',
                new Zend_Db_Expr('CAST(pp.ResumoDoProjeto as TEXT) as ResumoDoProjeto')),
            $this->_schema
        );

        $slct->joinLeft(
            array('resp' => 'SGCacesso'),
            'resp.IdUsuario = pp.idUsuario',
            array('resp.Nome', 'resp.Cpf'),
            $this->getSchema('controledeacesso')
        );

        $slct->joinLeft(
            array('pr' => 'Projetos'),
            'pp.idPreProjeto = pr.idProjeto',
            array('pr.idProjeto',
                'PRONAC' => new Zend_Db_Expr('pr.AnoProjeto' + 'pr.Sequencial')
            ),
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order('pp.idpreprojeto');
        $slct->order('pp.nomeprojeto');

        return $this->fetchAll($slct);
    }

    /**
     * buscarPropProjVinculados
     *
     * @param  mixed $idAgenteProponente
     * @access public
     * @return void
     */
    public function buscarPropProjVinculados($idAgenteProponente)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//        $slct->where('NOT EXISTS(SELECT * FROM Projetos p WHERE p.idProjeto = pp.idPreProjeto)', '');
        $subSql = $db->select()
            ->from(array('p' => 'projetos'), array('*'), $this->getSchema('sac'))
            ->where('p.idprojeto = pp.idpreprojeto');

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pp' => $this->_name),
            array('pp.idPreProjeto',
                'pp.idAgente',
                'pp.NomeProjeto',
                'pp.Mecanismo',
                'pp.AgenciaBancaria',
                'pp.DtInicioDeExecucao',
                'pp.DtFinalDeExecucao',
                'pp.stTipoDemanda',
                'pp.idUsuario',
                'pp.idEdital')
        );

        $slct->joinLeft(
            array('resp' => 'SGCacesso'),
            'resp.IdUsuario = pp.idUsuario',
            array('resp.Nome', 'resp.Cpf'),
            $this->getSchema('controledeacesso')
        );

        $slct->where('idAgente = ?', $idAgenteProponente);
        $slct->where('stEstado = ?', 1);
        $slct->where(new Zend_Db_Expr("NOT EXISTS ($subSql)"));
        $slct->order('pp.idpreprojeto');
        $slct->order('pp.nomeprojeto');

        return $this->fetchAll($slct);
    }

    /**
     * buscarVinculadosProponenteDirigentes
     *
     * @param mixed $arrayIdAgentes
     * @access public
     * @return void
     */
    public function buscarVinculadosProponenteDirigentes($arrayIdAgentes)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $subSql = $db->select()
            ->from(array('p' => 'projetos'), array('*'), $this->getSchema('sac'))
            ->where('p.idprojeto = pp.idpreprojeto');

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pp' => $this->_name),
            array('pp.idPreProjeto',
                'pp.idAgente',
                'pp.NomeProjeto',
                'pp.Mecanismo',
                'pp.AgenciaBancaria',
                'pp.DtInicioDeExecucao',
                'pp.DtFinalDeExecucao',
                'pp.stTipoDemanda',
                'pp.idUsuario',
                'pp.idEdital'),
            $this->_schema
        );

        $slct->joinLeft(
            array('resp' => 'SGCacesso'),
            'resp.IdUsuario = pp.idUsuario',
            array('resp.Nome', 'resp.Cpf'),
            $this->getSchema('controledeacesso')
        );

        $slct->where('pp.idAgente in (?)', $arrayIdAgentes);
        $slct->where('stEstado = ?', 1);
        $slct->where(new Zend_Db_Expr("NOT EXISTS ($subSql)"));
        $slct->order('pp.idpreprojeto');
        $slct->order('pp.nomeprojeto');

        return $this->fetchAll($slct);
    }

    /**
     * gerenciarResponsaveisPendentes
     *
     * @param mixed $siVinculo
     * @param bool $idAgente
     *
     * @access public
     * @return void
     */
    public function gerenciarResponsaveisPendentes($siVinculo, $idAgente = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $k = array(
            'k.Cpf',
            'k.IdUsuario as idResponsavel',
            'k.Nome AS NomeResponsavel',
            'k.IdUsuario',
        );

        $v = array(
            'v.idVinculo',
            'v.siVinculo',
            'v.idUsuarioResponsavel',
        );

        $sql = $db->select()->distinct()
            ->from(array('a' => 'Agentes'), array(), $this->getSchema('agentes'))
            ->joinLeft(array('v' => 'tbVinculo'), 'a.idAgente = v.idAgenteProponente', $v, $this->getSchema('agentes'))
            ->join(array('k' => 'SGCacesso'), 'k.IdUsuario = v.idUsuarioResponsavel', $k, $this->getSchema('controledeacesso'))
            ->where('a.idAgente= ?', $idAgente)
            ->where('v.siVinculo = ?', $siVinculo)
            ->where('a.CNPJCPF <> k.Cpf')
            ->order('k.Nome  ASC');

        return $db->fetchAll($sql);
    }

    /**
     * GerenciarResponsaveisVinculados
     *
     * @param mixed $siVinculo
     * @param bool $idAgente
     *
     * @access public
     * @return void
     */
    public function gerenciarResponsaveisVinculados($siVinculo, $idAgente = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->distinct()
            ->from(array('j' => 'PreProjeto'), array(), $this->_schema)
            ->join(array('a' => 'Agentes'), 'j.idAgente = a.idAgente', array(), $this->getSchema('agentes'))
            ->join(array('v' => 'tbVinculoProposta'), 'j.idPreProjeto = v.idPreProjeto', array(), $this->getSchema('agentes'))
            ->join(array('y' => 'tbVinculo'), 'v.idVinculo = y.idVinculo', array('y.idVinculo', 'y.siVinculo', 'y.idUsuarioResponsavel'), $this->getSchema('agentes'))
            ->join(array('k' => 'SGCacesso'), 'k.IdUsuario = y.idUsuarioResponsavel', array('k.Cpf', 'k.IdUsuario as idResponsavel', 'k.Nome AS NomeResponsavel'), $this->getSchema('controledeacesso'))
            ->join(array('r' => 'SGCacesso'), 'r.Cpf = a.CNPJCPF', array('r.IdUsuario'), $this->getSchema('controledeacesso'))
            ->where('j.idAgente= ?', $idAgente)
            ->where('y.siVinculo = ?', $siVinculo)
            ->where('a.CNPJCPF <> k.Cpf')
            ->order(array('k.Nome  ASC'));

        return $db->fetchAll($sql);
    }

    /**
     * listarPropostasResultado
     *
     * @param mixed $idAgente
     * @param mixed $idResponsavel
     * @param mixed $idAgenteCombo
     *
     * @access public
     * @return void
     */
    public function listarPropostasResultado($idAgente, $idResponsavel, $idAgenteCombo)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $subSql = $db->select()
            ->from(array('pr' => 'projetos'), array('idprojeto'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->where('a.idpreprojeto = pr.idprojeto');

        $sql = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->where('a.idagente = ? ', $idAgente)
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'");

        $subSql = $db->select()
            ->from(array('f' => 'projetos'), array('idprojeto'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->where('a.idpreprojeto = f.idprojeto');

        $sql2 = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('c' => 'vinculacao'), 'b.idagente = c.idvinculoprincipal', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('d' => 'agentes'), 'c.idagente = d.idagente', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('e' => 'sgcacesso'), 'd.cnpjcpf = e.cpf', array(), Proposta_Model_DbTable_PreProjeto::getSchema('controledeacesso'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->where('e.idusuario = ?', $idResponsavel)
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'");

        $subSql = $db->select()
            ->from(array('z' => 'projetos'), array('idprojeto'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->where('a.idpreprojeto = z.idprojeto');

        $sql3 = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('c' => 'nomes'), 'b.idagente = c.idagente', array('c.descricao as nomeproponente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('d' => 'sgcacesso'), 'a.idusuario = d.idusuario', array(), Proposta_Model_DbTable_PreProjeto::getSchema('controledeacesso'))
            ->join(array('e' => 'tbvinculoproposta'), 'a.idpreprojeto = e.idpreprojeto', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('f' => 'tbvinculo'), 'e.idvinculo = f.idvinculo', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
            ->where('e.sivinculoproposta = 2')
            ->where('f.idusuarioresponsavel = ?', $idResponsavel);

        if (!empty($idAgenteCombo)) {
            $sql->where('b.idagente = ?', $idAgenteCombo);
            $sql2->where('b.idagente = ?', $idAgenteCombo);
            $sql3->where('b.idagente = ?', $idAgenteCombo);
        }

        $sql = $db->select()->union(array($sql, $sql2, $sql3), Zend_Db_Select::SQL_UNION_ALL);

        return $db->fetchAll($sql);
    }

    /**
     * Metodo para buscar os Proponentes - Combo Listar Propostas
     * @access public
     * @param integer $idResponsavel
     * @return object
     */
    public function listarPropostasCombo($idResponsavel)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('a' => 'preprojeto'), null, $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->joinLeft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->join(array('c' => 'sgcacesso'), 'b.cnpjcpf = c.cpf', null, $this->getSchema('controledeacesso'))
            ->where('c.idusuario = ?', $idResponsavel);

        $sql2 = $db->select()
            ->from(array('a' => 'preprojeto'), null, $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->join(array('c' => 'tbvinculoproposta'), 'a.idpreprojeto = c.idpreprojeto', null, $this->getSchema('agentes'))
            ->join(array('d' => 'tbvinculo'), 'c.idvinculo = d.idvinculo', null, $this->getSchema('agentes'))
            ->join(array('f' => 'agentes'), 'd.idagenteproponente = f.idagente', null, $this->getSchema('agentes'))
            ->join(array('e' => 'sgcacesso'), 'f.cnpjcpf = e.cpf', null, $this->getSchema('controledeacesso'))
            ->where('e.idusuario = ?', $idResponsavel)
            ->where('c.sivinculoproposta = 2');

        $sql3 = $db->select()
            ->from(array('a' => 'agentes'), array('a.cnpjcpf', 'a.idagente'), $this->getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = a.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->join(array('b' => 'vinculacao'), 'a.idagente = b.idvinculoprincipal', null, $this->getSchema('agentes'))
            ->join(array('c' => 'agentes'), 'b.idagente = c.idagente', null, $this->getSchema('agentes'))
            ->join(array('d' => 'sgcacesso'), 'c.cnpjcpf = d.cpf', null, $this->getSchema('controledeacesso'))
            ->where('d.idusuario = ?', $idResponsavel);

        $sql4 = $db->select()
            ->from(array('a' => 'agentes'), array('a.cnpjcpf', 'a.idagente'), $this->getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = a.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->join(array('b' => 'tbvinculo'), 'a.idagente = b.idagenteproponente', null, $this->getSchema('agentes'))
            ->join(array('c' => 'sgcacesso'), 'b.idusuarioresponsavel = c.idusuario', null, $this->getSchema('controledeacesso'))
            ->where('b.sivinculo = 2')
            ->where('c.idusuario = ?', $idResponsavel);

        $sql5 = $db->select()
            ->from(array('a' => 'agentes'), array('a.cnpjcpf', 'a.idagente'), $this->getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = a.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->join(array('b' => 'sgcacesso'), 'a.cnpjcpf = b.cpf', null, $this->getSchema('controledeacesso'))
            ->where('b.idusuario = ?', $idResponsavel);

        $sql = $db->select()->union(array($sql, $sql2, $sql3, $sql4, $sql5))
            ->group(array('a.cnpjcpf', 'a.idagente', 'n.descricao'))
            ->order(array('3 asc'));

//        echo '<pre>';
//        print_r(str_replace('"', '', $sql->assemble()));
//        exit;

        return $db->fetchAll($sql);
    }

    /**
     * relatorioPropostas2
     *
     * @param bool $where
     * @param bool $having
     * @param bool $order
     * @param mixed $tamanho
     * @param mixed $inicio
     * @param bool $count
     * @param bool $dados
     * @access public
     * @return void
     */
    public function relatorioPropostas2($where = array(), $having = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false, $dados = null)
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("p" => $this->_name),
            array("idProjeto" => "idPreProjeto", "NomeProposta" => "NomeProjeto", "idAgente"),
            "SAC.dbo"
        );

        if (!($dados->proposta)) {
            $slct->joinInner(
                array("m" => "tbMovimentacao"),
                "p.idPreProjeto = m.idProjeto AND m.stEstado = 0",
                array(),
                "SAC.dbo"
            );
            $slct->joinInner(
                array("vr" => "Verificacao"),
                "m.movimentacao = vr.idVerificacao and vr.idTipo = 4",
                array(),
                "SAC.dbo"
            );
            $slct->joinInner(
                array("x" => "tbAvaliacaoProposta"),
                "p.idPreProjeto = x.idProjeto AND x.stEstado = 0",
                array(),
                "SAC.dbo"
            );
        }
        if (($dados->uf) || ($dados->municipio)) {
            $slct->joinInner(
                array("ab" => "Abrangencia"),
                "p.idPreProjeto = ab.idProjeto AND ab.stAbrangencia = 1",
                array(),
                "SAC.dbo"
            );
        }

        if ($dados->uf) {
            $slct->joinInner(
                array("uf" => "UF"),
                "uf.idUF = ab.idUF",
                array(),
                "AGENTES.dbo"
            );
        }

        if (($dados->uf) || ($dados->municipio)) {
            $slct->joinInner(
                array("mu" => "Municipios"),
                "mu.idMunicipioIBGE = ab.idMunicipioIBGE",
                array(),
                "AGENTES.dbo"
            );
        }

        if (($dados->area) || ($dados->segmento)) {
            $slct->joinInner(
                array("pdp" => "PlanoDistribuicaoProduto"),
                "pdp.idProjeto = p.idPreProjeto AND pdp.stPlanoDistribuicaoProduto = 1",
                array(),
                "SAC.dbo"
            );
        }

        $slct->joinLeft(
            array("pp" => "tbPlanilhaProposta"),
            "pp.idProjeto = p.idPreProjeto",
            array("valor" => new Zend_Db_Expr("sum(Quantidade*Ocorrencia*ValorUnitario)")),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("ag" => "agentes"),
            "ag.idAgente = p.idAgente",
            array("ag.CNPJCPF"),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("nm" => "nomes"),
            "nm.idAgente = p.idAgente",
            array("nm.Descricao as Proponente"),
            "AGENTES.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->group(array("p.idPreProjeto", "p.NomeProjeto", "p.idAgente", "ag.CNPJCPF", "nm.Descricao"));

        //adiciona quantos filtros foram enviados
        foreach ($having as $coluna => $valor) {
            $slct->having($coluna, $valor);
        }

        if ($count) {
            $this->_totalRegistros = $this->fetchAll($slct)->count();
            return $this->_totalRegistros;
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

    public function relatorioPropostas($where = array(), $having = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("p" => $this->_name),
            array("idProjeto" => "idPreProjeto", "NomeProposta" => "NomeProjeto", "idAgente", "p.stEstado", "p.DtArquivamento"),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("m" => "tbMovimentacao"),
            "p.idPreProjeto = m.idProjeto AND m.stEstado = 0",
            array('m.Movimentacao', 'm.stEstado AS estadoMovimentacao'),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("vr" => "Verificacao"),
            "m.movimentacao = vr.idVerificacao and vr.idTipo = 4",
            array(),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("x" => "tbAvaliacaoProposta"),
            "p.idPreProjeto = x.idProjeto AND x.stEstado = 0",
            array('x.ConformidadeOK', 'x.stEstado AS estadoAvaliacao'),
            "SAC.dbo"
        );

        if (isset($where['ab.idUF = ?']) || isset($where['ab.idMunicipioIBGE = ?'])) {
            $slct->joinInner(
                array("ab" => "Abrangencia"),
                "p.idPreProjeto = ab.idProjeto AND ab.stAbrangencia = 1",
                array(),
                "SAC.dbo"
            );
        }

        if (isset($where['ab.idUF = ?'])) {
            $slct->joinInner(
                array("uf" => "UF"),
                "uf.idUF = ab.idUF",
                array(),
                "AGENTES.dbo"
            );
        }

        if (isset($where['ab.idUF = ?']) || isset($where['ab.idMunicipioIBGE = ?'])) {
            $slct->joinInner(
                array("mu" => "Municipios"),
                "mu.idMunicipioIBGE = ab.idMunicipioIBGE",
                array(),
                "AGENTES.dbo"
            );
        }

        if (isset($where['pdp.Area = ?']) || isset($where['pdp.Segmento = ?'])) {
            $slct->joinInner(
                array("pdp" => "PlanoDistribuicaoProduto"),
                "pdp.idProjeto = p.idPreProjeto AND pdp.stPlanoDistribuicaoProduto = 1",
                array(),
                "SAC.dbo"
            );
        }

        $slct->joinLeft(
            array("pp" => "tbPlanilhaProposta"),
            "pp.idProjeto = p.idPreProjeto",
            array("valor" => new Zend_Db_Expr("sum(Quantidade*Ocorrencia*ValorUnitario)")),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ag" => "agentes"),
            "ag.idAgente = p.idAgente",
            array("ag.CNPJCPF"),
            "AGENTES.dbo"
        );

        $slct->joinInner(
            array("nm" => "nomes"),
            "nm.idAgente = p.idAgente",
            array(
                "nm.Descricao as Proponente"
            ),
            "AGENTES.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->where('NOT EXISTS (SELECT * FROM Projetos z WHERE z.idProjeto = p.idPreProjeto)');
        $slct->group(array("p.idPreProjeto", "p.NomeProjeto", "p.idAgente", "p.stEstado", "p.DtArquivamento", "m.Movimentacao", "m.stEstado", "x.ConformidadeOK", "x.stEstado", "ag.CNPJCPF", "nm.Descricao"));

        //adiciona quantos filtros foram enviados
        foreach ($having as $coluna => $valor) {
            $slct->having($coluna, $valor);
        }

        if ($count) {
            return $this->fetchAll($slct)->count();
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
    public function buscarPropostaAdmissibilidade($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $p = array(
            'p.idPreProjeto AS idProjeto',
            'p.NomeProjeto AS NomeProposta',
            'p.idAgente'
        );

        $m = array(
            new Zend_Db_Expr('CONVERT(CHAR(20),m.DtMovimentacao, 120) AS DtMovimentacao'),
            new Zend_Db_Expr('DATEDIFF(d, m.DtMovimentacao, GETDATE()) AS diasDesdeMovimentacao'),
            'm.idMovimentacao',
            'm.Movimentacao AS CodSituacao',
        );

        // Replace da funcao: SAC.dbo.fnIdOrgaoSuperiorAnalista(a.idTecnico)
        $orgao = $db->select()
            ->from(array('vwUsuariosOrgaosGrupos'), 'org_superior', 'tabelas.dbo')
            ->where('usu_codigo = x.idTecnico')
            ->where('sis_codigo = 21')
            ->where('gru_codigo = 92')
            ->group('org_superior');

        //replace funcao: SAC.dbo.fnNomeTecnicoMinc(a.idTecnico)
        $tecnico = $db->select()
            ->from(array('Usuarios'), 'usu_nome', 'tabelas.dbo')
            ->where('usu_codigo = x.idTecnico');

        $x = array(
            new Zend_Db_Expr('CONVERT(CHAR(20),x.DtAvaliacao, 120) AS DtAdmissibilidade'),
            new Zend_Db_Expr('DATEDIFF(d, x.DtAvaliacao, GETDATE()) AS diasCorridos'),
            'x.idTecnico AS idUsuario',
            'x.DtAvaliacao',
            'x.idAvaliacaoProposta',
            "($tecnico) AS Tecnico",
            "($orgao) AS idSecretaria",
        );

        $sql = $db->select()
            ->from(array("p" => $this->_name), $p, "SAC.dbo")
            ->join(array("m" => "tbMovimentacao"), 'p.idPreProjeto = m.idProjeto AND m.stEstado = 0', $m, "SAC.dbo")
            ->joinInner(array("x" => "tbAvaliacaoProposta"), "p.idPreProjeto = x.idProjeto AND x.stEstado = 0", $x, "SAC.dbo")
            ->joinInner(array("a" => "Agentes"), 'p.idAgente = a.idAgente', array('a.CNPJCPF'), $this->getSchema('agentes'))
            ->joinInner(array("y" => "Verificacao"), 'm.Movimentacao = y.idVerificacao', null, $this->_schema)
            ->joinLeft(array("ap1" => 'tbAvaliacaoProposta'), "p.idPreProjeto = ap1.idProjeto AND ap1.stEnviado = 'S'", array(new Zend_Db_Expr('DATEDIFF(d, ap1.DtEnvio, GETDATE()) AS diasDiligencia')), $this->_schema)
            ->joinLeft(array("ap2" => 'tbAvaliacaoProposta'), "p.idPreProjeto = ap2.idProjeto AND ap2.stEnviado = 'S'", array(new Zend_Db_Expr('DATEDIFF(d, ap2.dtResposta, GETDATE()) AS diasRespostaDiligencia')), $this->_schema)
            ->where(
                new Zend_Db_Expr(
                    'NOT EXISTS
                    (
                    SELECT TOP (1) IdPRONAC, AnoProjeto, Sequencial, UfProjeto, Area, Segmento, Mecanismo, NomeProjeto, Processo, CgcCpf, Situacao, DtProtocolo, DtAnalise, Modalidade, Orgao, OrgaoOrigem, DtSaida, DtRetorno, UnidadeAnalise, Analista, DtSituacao, ResumoProjeto, ProvidenciaTomada, Localizacao, DtInicioExecucao, DtFimExecucao, SolicitadoUfir, SolicitadoReal, SolicitadoCusteioUfir, SolicitadoCusteioReal, SolicitadoCapitalUfir, SolicitadoCapitalReal, Logon, idProjeto
                    FROM SAC.dbo.Projetos AS u
                    WHERE (p.idPreProjeto = idProjeto)
                    )'
                )
            );

        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $sql->where($coluna . '?', $valor);
        }

        // adicionando clausulas order
        $sql->order($order);

        // retornando os registros conforme objeto select
        return $db->fetchAll($sql);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarPropostaAdmissibilidadeZend($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("p" => $this->_name),
            array("idProjeto" => "idPreProjeto", "NomeProposta" => "NomeProjeto", "idAgente")
        );
        $slct->joinInner(
            array("m" => "tbMovimentacao"),
            "p.idPreProjeto = m.idProjeto AND m.stEstado = 0",
            array(
                "idMovimentacao", "CodSituacao" => "m.Movimentacao",
                "DtMovimentacao" => new Zend_Db_Expr("CONVERT(CHAR(20),m.DtMovimentacao, 120)"),
                "diasDesdeMovimentacao" => new Zend_Db_Expr("DATEDIFF(d, m.DtMovimentacao, GETDATE())")
            ),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("x" => "tbAvaliacaoProposta"),
            "p.idPreProjeto = x.idProjeto AND x.stEstado = 0",
            array(
                "idAvaliacaoProposta",
                "DtAdmissibilidade" => new Zend_Db_Expr("CONVERT(CHAR(20),x.DtAvaliacao, 120)"),
                "diasCorridos" => new Zend_Db_Expr("DATEDIFF(d, x.DtAvaliacao, GETDATE())")
            ),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("a" => "Agentes"),
            "p.idAgente = a.idAgente",
            array("CNPJCPF"),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("y" => "Verificacao"),
            "m.Movimentacao = y.idVerificacao",
            array("Situacao" => "Descricao"),
            "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        $this->_totalRegistros = $this->fetchAll($slct)->count();
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
     * buscarTecnicosHistoricoAnaliseVisual
     *
     * @param mixed $idOrgao
     * @access public
     * @return void
     */
    public function buscarTecnicosHistoricoAnaliseVisual($idOrgao)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        // Replace da funcao: SAC.dbo.fnIdOrgaoSuperiorAnalista(a.idTecnico)
        $subSql = $db->select()
            ->from(array('vwUsuariosOrgaosGrupos'), 'org_superior', 'tabelas.dbo')
            ->where('usu_codigo = a.idTecnico')
            ->where('sis_codigo = 21')
            ->where('gru_codigo = 92')
            ->group('org_superior');

        $sql = $db->select()
            ->distinct()
            ->from(array('a' => 'tbAvaliacaoProposta'), array('a.idTecnico'), "SAC.dbo")
            ->join(array('p' => 'PreProjeto'), 'p.idPreProjeto = a.idProjeto', null, "SAC.dbo")
            ->join(array('u' => 'Usuarios'), 'u.usu_codigo = a.idTecnico', 'u.usu_nome as Tecnico', 'TABELAS.dbo')
            ->where('ConformidadeOK<>1')
            ->where('p.stEstado = 1')
            ->where("($subSql) = ?", $idOrgao)
            ->order('u.usu_nome ASC');

        // retornando os registros conforme objeto select
        return $db->fetchAll($sql);
    }

    /**
     * buscarHistoricoAnaliseVisual
     *
     * @param mixed $idOrgao
     * @param bool $idTecnico
     * @param bool $situacao
     * @param bool $dtInicio
     * @param bool $dtFim
     * @access public
     * @return void
     */
    public function buscarHistoricoAnaliseVisual($idOrgao, $idTecnico = null, $situacao = null, $dtInicio = null, $dtFim = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $p = array(
            'p.idPreProjeto',
            'p.NomeProjeto',
        );

        //replace funcao: SAC.dbo.fnNomeTecnicoMinc(a.idTecnico)
        $tecnico = $db->select()
            ->from(array('Usuarios'), 'usu_nome', 'tabelas.dbo')
            ->where('usu_codigo = a.idTecnico');

        // Replace da funcao: SAC.dbo.fnIdOrgaoSuperiorAnalista(a.idTecnico)
        $subSql = $db->select()
            ->from(array('vwUsuariosOrgaosGrupos'), 'org_superior', 'tabelas.dbo')
            ->where('usu_codigo = a.idTecnico')
            ->where('sis_codigo = 21')
            ->where('gru_codigo = 92')
            ->group('org_superior');

        $a = array(
            'a.idTecnico',
            "($tecnico) as Tecnico",
            'a.DtEnvio',
            new Zend_Db_Expr('CONVERT(CHAR(20),a.DtAvaliacao, 120) AS DtAvaliacao'),
            'a.idAvaliacaoProposta',
            'a.ConformidadeOK',
            'a.stEstado',
            "($subSql) as idOrgao"
        );


        $sql = $db->select()
            ->from(array('a' => 'tbAvaliacaoProposta'), $a, $this->_schema)
            ->join(array('p' => 'PreProjeto'), 'p.idPreProjeto = a.idProjeto', $p, $this->_schema)
            ->where('ConformidadeOK<>1')
            ->where('p.stEstado = 1')
            ->where("($subSql) = ?", $idOrgao)
            ->order('p.idPreProjeto DESC')
            ->order('DtAvaliacao ASC')
            ->limit(20);

        if ($idTecnico) {
            $sql->where('a.idTecnico = ?', $idTecnico);
        }
        if ($situacao) {
            $sql->where('a.ConformidadeOK = ?', $situacao);
        }

        if ($dtInicio) {
            if ($dtFim) {
                $sql->where('a.DtAvaliacao > ? 00:00:00', $dtInicio);
                $sql->where('a.DtAvaliacao < ? 23:59:59', $dtFim);
            } else {
                $sql->where('a.DtAvaliacao > ? 00:00:00', $dtInicio);
                $sql->where('a.DtAvaliacao < ? 23:59:59', $dtInicio);
            }
        }

        // retornando os registros conforme objeto select
        return $db->fetchAll($sql);
    }

    /**
     * buscarAvaliacaoHistoricoAnaliseVisual
     *
     * @param mixed $idAvaliacao
     * @access public
     * @return void
     * @deprecated
     */
    public function buscarAvaliacaoHistoricoAnaliseVisual($idAvaliacao)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('a' => 'tbAvaliacaoProposta'), array('a.Avaliacao'), $this->_schema)
            ->where('a.idAvaliacaoProposta = ?', $idAvaliacao);

        return $db->fetchAll($sql);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     *
     */
    public function buscarPropostaAnaliseVisualTecnico($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $db = $this->getAdapter();
        $vw = array(
            'idProjeto',
            'NomeProjeto',
            'Tecnico',
            'idOrgao',
            new Zend_Db_Expr('CONVERT(CHAR(20),vw.DtEnvio, 120) AS DtEnvio'),
            'ConformidadeOK',
            new Zend_Db_Expr('CONVERT(CHAR(20),vw.DtMovimentacao, 120) AS DtMovimentacao'),
            'QtdeDias'
        );

        $sql = $db->select()
            ->from(array('vw' => 'vwAnaliseVisualPorTecnico'), $vw, $this->_schema);

        ($order) ? $sql->order($order) : null;

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna . ' ?', $valor);
        }

        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarPropostaAnaliseDocumentalTecnico($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        //replace funcao: SAC.dbo.fnNomeTecnicoMinc(a.idTecnico)
        $tecnico = $db->select()
            ->from(array('Usuarios'), 'usu_nome', 'tabelas.dbo')
            ->where('usu_codigo = a.idTecnico');

        // Replace da funcao: SAC.dbo.fnIdOrgaoSuperiorAnalista(a.idTecnico)
        $orgao = $db->select()
            ->from(array('vwUsuariosOrgaosGrupos'), 'org_superior', 'tabelas.dbo')
            ->where('usu_codigo = a.idTecnico')
            ->where('sis_codigo = 21')
            ->where('gru_codigo = 92')
            ->group('org_superior');

        //Replace da funcao: sac.dbo.fnDtUltimaDiligenciaDocumental(a.idProjeto)
        $diligencia = $db->select()->from(array('tbMovimentacao'), "max(DtMovimentacao)", $this->_schema)->where('Movimentacao = 97')
            ->where('idProjeto = a.idProjeto');

        $a = array(
            'a.idProjeto',
            "($tecnico) AS Tecnico",
            "($orgao) as idOrgao",
            new Zend_Db_Expr("CONVERT(CHAR(20), ({$diligencia}), 120) AS DtUltima")
        );

        $sql = $db->select()
            ->from(array('a' => 'tbAvaliacaoProposta'), $a, $this->_schema)
            ->join(array('p' => 'PreProjeto'), 'a.idProjeto=p.idPreProjeto', array('p.NomeProjeto'), $this->_schema)
            ->join(array('d' => 'vwDocumentosPendentes'), 'a.idProjeto = d.idProjeto', array('CodigoDocumento'), $this->_schema)
            ->join(array('de' => 'DocumentosExigidos'), 'd.CodigoDocumento = de.Codigo', array('Descricao as Documento'), $this->_schema);

        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $sql->where($coluna . '?', $valor);
        }

        $sql->order($order);

        // retornando os registros conforme objeto select
        return $db->fetchAll($sql);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     * @deprecated
     */
    public function buscarPropostaAnaliseFinal($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $db = $this->getAdapter();
        $vw = array(
            'idPreProjeto',
            'NomeProjeto',
            'Tecnico',
            'DtEnvio',
            new Zend_Db_Expr('CONVERT(CHAR(20),DtMovimentacao, 120) AS DtMovimentacao'),
            'DtAvaliacao',
            'Dias',
            'idOrgao',
            'ConformidadeOK',
            'QtdeDiasAguardandoEnvio'
        );

        $sql = $db->select()
            ->from(array('vw' => 'vwPropostaProjetoSecretaria'), $vw, $this->_schema);

        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $sql->where($coluna . ' = ?', $valor);
        }

        // adicionando clausulas order
        ($order) ? $sql->order($order) : null;

        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        // retornando os registros conforme objeto select
        return $db->fetchAll($sql);
    }

    /**
     * buscarConformidadeVisualTecnico
     *
     * @param mixed $idPreProjeto
     * @access public
     * @return void
     */
    public function buscarConformidadeVisualTecnico($idPreProjeto)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('vw' => 'vwConformidadeVisualTecnico'), 'tecnico', $this->_schema)
            ->where('idprojeto = ?', $idPreProjeto)
            ->query();

        // retornando os registros conforme objeto select
        return $sql->fetchAll();
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarVisual($idUsuario = null, $order = array(), $tamanho = -1, $inicio = -1)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $p = array(
            'p.stTipoDemanda AS TipoDemanda',
            'p.idPreProjeto AS idProjeto',
            'p.NomeProjeto AS NomeProposta',
            'p.idAgente',
        );

        //replace funcao: SAC.dbo.fnNomeTecnicoMinc(a.idTecnico)
        $tecnico = $db->select()
            ->from(array('Usuarios'), 'usu_nome', 'tabelas.dbo')
            ->where('usu_codigo = x.idTecnico');

        // Replace da funcao: SAC.dbo.fnIdOrgaoSuperiorAnalista(a.idTecnico)
        $orgao = $db->select()
            ->from(array('vwUsuariosOrgaosGrupos'), 'org_superior', 'tabelas.dbo')
            ->where('usu_codigo = x.idTecnico')
            ->where('sis_codigo = 21')
            ->where('gru_codigo = 92')
            ->group('org_superior');

        $x = array(
            'x.idTecnico AS idUsuario',
            "($tecnico) AS Tecnico",
            "($orgao) AS idSecretaria",
            new Zend_Db_Expr('CONVERT(CHAR(20),x.DtAvaliacao, 120) AS DtAdmissibilidade'),
            new Zend_Db_Expr('DATEDIFF(d, x.DtAvaliacao, GETDATE()) AS diasCorridos'),
            'x.idAvaliacaoProposta',
        );

        $m = array(
            'm.idMovimentacao',
            'm.Movimentacao AS CodSituacao',
        );

        $subSql = $db->select()->from('vwRedistribuirAnaliseVisual', array('idProjeto'), $this->_schema);

        if ($idUsuario !== null) {
            $subSql->where("($orgao) = ?", $idUsuario);
        }

        $subSql2 = $db->select()
            ->from(
                array('u' => 'Projetos'),
                array('IdPRONAC', 'AnoProjeto', 'Sequencial', 'UfProjeto', 'Area', 'Segmento', 'Mecanismo', 'NomeProjeto', 'Processo',
                    'CgcCpf', 'Situacao', 'DtProtocolo', 'DtAnalise', 'Modalidade', 'Orgao', 'OrgaoOrigem', 'DtSaida', 'DtRetorno', 'UnidadeAnalise',
                    'Analista', 'DtSituacao', 'ResumoProjeto', 'ProvidenciaTomada', 'Localizacao', 'DtInicioExecucao', 'DtFimExecucao', 'SolicitadoUfir',
                    'SolicitadoReal', 'SolicitadoCusteioUfir', 'SolicitadoCusteioReal', 'SolicitadoCapitalUfir', 'SolicitadoCapitalReal', 'Logon', 'idProjeto'),
                $this->_schema
            )
            ->where('p.idPreProjeto = idProjeto')
            ->limit(1);

        $sql = $db->select()
            ->from(array('p' => 'PreProjeto'), null, $this->_schema)
            ->join(array('m' => 'tbMovimentacao'), 'p.idPreProjeto = m.idProjeto AND m.stEstado = 0', $m, $this->_schema)
            ->join(array('x' => 'tbAvaliacaoProposta'), 'p.idPreProjeto = x.idProjeto AND x.stEstado = 0', $x, $this->_schema)
            ->join(array('a' => 'Agentes'), 'p.idAgente = a.idAgente', array('a.CNPJCPF'), $this->getSchema('agentes'))
            ->join(array('y' => 'Verificacao'), 'm.Movimentacao = y.idVerificacao', array('y.Descricao AS Situacao'), $this->_schema)
            ->where('p.stEstado = 1')
            ->where('m.Movimentacao NOT IN(96,128)')
            ->where(new Zend_Db_Expr("p.idPreProjeto IN( $subSql )"))
            ->where(new Zend_Db_Expr("NOT EXISTS ( $subSql2)"))
            ->order($order);

        return $db->fetchAll($sql);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarDocumental($idUsuario = null, $order = array(), $tamanho = -1, $inicio = -1)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $p = array(
            'p.idPreProjeto AS idProjeto',
            'p.NomeProjeto AS NomeProposta',
            'p.idAgente',
            'p.stTipoDemanda AS TipoDemanda'
        );

        //replace funcao: SAC.dbo.fnNomeTecnicoMinc(a.idTecnico)
        $tecnico = $db->select()
            ->from(array('Usuarios'), 'usu_nome', 'tabelas.dbo')
            ->where('usu_codigo = x.idTecnico');

        // Replace da funcao: SAC.dbo.fnIdOrgaoSuperiorAnalista(a.idTecnico)
        $orgao = $db->select()
            ->from(array('vwUsuariosOrgaosGrupos'), 'org_superior', 'tabelas.dbo')
            ->where('usu_codigo = x.idTecnico')
            ->where('sis_codigo = 21')
            ->where('gru_codigo = 92')
            ->group('org_superior');

        // Replace da funcao: SAC.dbo.fnIdOrgaoSuperiorAnalista(a.idTecnico)
        $orgaoSub = $db->select()
            ->from(array('vwUsuariosOrgaosGrupos'), 'org_superior', 'tabelas.dbo')
            ->where('usu_codigo = idTecnico')
            ->where('sis_codigo = 21')
            ->where('gru_codigo = 92')
            ->group('org_superior');

        $x = array(
            'x.idTecnico AS idUsuario',
            "($tecnico) AS Tecnico",
            "($orgao) AS idSecretaria",
            new Zend_Db_Expr('CONVERT(CHAR(20),x.DtAvaliacao, 120) AS DtAdmissibilidade'),
            new Zend_Db_Expr('DATEDIFF(d, x.DtAvaliacao, GETDATE()) AS diasCorridos'),
            'x.idAvaliacaoProposta',
        );

        $m = array(
            'm.idMovimentacao',
            'm.Movimentacao AS CodSituacao',
        );

        $subSql = $db->select()
            ->from(array('u' => 'Projetos'), array(
                'IdPRONAC', 'AnoProjeto', 'Sequencial', 'UfProjeto', 'Area', 'Segmento', 'Mecanismo', 'NomeProjeto', 'Processo', 'CgcCpf', 'Situacao',
                'DtProtocolo', 'DtAnalise', 'Modalidade', 'Orgao', 'OrgaoOrigem', 'DtSaida', 'DtRetorno', 'UnidadeAnalise', 'Analista', 'DtSituacao', 'ResumoProjeto',
                'ProvidenciaTomada', 'Localizacao', 'DtInicioExecucao', 'DtFimExecucao', 'SolicitadoUfir', 'SolicitadoReal', 'SolicitadoCusteioUfir', 'SolicitadoCusteioReal',
                'SolicitadoCapitalUfir', 'SolicitadoCapitalReal', 'Logon', 'idProjeto'
            ), $this->_schema)
            ->where('p.idPreProjeto = idProjeto')
            ->limit(1);

        $subSql2 = $db->select()
            ->from(array('vwConformidadeDocumentalTecnico'), array('idProjeto'), $this->_schema);

        if ($idUsuario !== null) {
            $subSql2->where(new Zend_Db_Expr("($orgaoSub) = ?"), $idUsuario);
        }

        $sql = $db->select()
            ->from(array('p' => 'PreProjeto'), $p, $this->_schema)
            ->join(array('m' => 'tbMovimentacao'), 'p.idPreProjeto = m.idProjeto AND m.stEstado = 0', $m, $this->_schema)
            ->join(array('x' => 'tbAvaliacaoProposta'), 'p.idPreProjeto = x.idProjeto AND x.stEstado = 0', $x, $this->_schema)
            ->join(array('a' => 'Agentes'), 'p.idPreProjeto = x.idProjeto AND x.stEstado = 0', 'a.CNPJCPF', $this->getSchema('agentes'))
            ->join(array('y' => 'Verificacao'), 'm.Movimentacao = y.idVerificacao', array('y.Descricao AS Situacao'), $this->_schema)
            ->where('p.stEstado = 1')
            ->where('m.Movimentacao NOT IN(96,128)')
            ->where(new Zend_Db_Expr("NOT EXISTS ($subSql)"))
            ->where(new Zend_Db_Expr("p.idPreProjeto IN($subSql2)"))
            ->order($order);

        return $db->fetchAll($sql);
    }

    /**
     * transformarPropostaEmProjeto
     *
     * @param mixed $idPreProjeto
     * @param mixed $cnpjcpf
     * @param mixed $idOrgao
     * @param mixed $idUsuario
     * @param mixed $nrProcesso
     * @access public
     * @return void
     * @todo padrao ORM
     */
    public function transformarPropostaEmProjeto($idPreProjeto, $cnpjcpf, $idOrgao, $idUsuario, $nrProcesso)
    {
        $sql = "EXEC SAC.dbo.paPropostaParaProjeto {$idPreProjeto}, '{$cnpjcpf}', {$idOrgao}, {$idUsuario}, {$nrProcesso}";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    /**
     * buscaragencia
     *
     * @param mixed $codigo
     * @access public
     * @return void
     */
    public function buscaragencia($codigo)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $sql = $db->select()
            ->from(array('b' => 'bancoagencia'), 'agencia', $this->_schema)
            ->where('b.agencia = ?', $codigo)
            ->query();

        return $sql->fetchAll();
    }

    /**
     * unidadeAnaliseProposta
     *
     * @param mixed $idPreProjeto
     * @access public
     * @return void
     */
    public function unidadeAnaliseProposta($idPreProjeto)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("p" => $this->_name), array("*"));
        $slct->joinInner(array("ap" => "tbAvaliacaoProposta"), "p.idPreProjeto = ap.idProjeto", array("*"), "SAC.dbo");

        $slct->where("ap.stEstado = ?", 0);
        $slct->where("p.idPreProjeto = ?", $idPreProjeto);

        return $this->fetchAll($slct);
    }

    /**
     * orgaoSecretaria
     *
     * @param mixed $idTecnico
     * @access public
     * @return void
     */
    public function orgaoSecretaria($idTecnico)
    {
        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('vwUsuariosOrgaosGrupos'), array('org_superior AS idOrgao'), 'tabelas.dbo')
            ->where('usu_codigo = ?', 4676)
            ->where('sis_codigo=21')
            ->where('gru_codigo = 92')
            ->order('org_superior');

        $orgao = $db->fetchAll($sql);

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('Orgaos'), null, 'tabelas.dbo')
            ->join(array('Pessoa_Identificacoes'), 'pid_pessoa = org_pessoa', array('pid_identificacao'), 'Tabelas.dbo')
            ->where('pid_meta_dado = 1')
            ->where('pid_sequencia = 1')
            ->where('org_codigo = ?', 160);

        $identificacao = $db->fetchAll($sql);

        $orgao[0]->secretaria = $identificacao[0]->pid_identificacao;

        return $orgao;
    }

    /**
     * propostastransformadas
     *
     * @param mixed $idAgente
     * @access public
     * @return void
     */
    public function propostastransformadas($idAgente)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("pj" => $this->_name), array("*"));
        $slct->joinInner(array("p" => "Projetos"), "pj.idPreProjeto = p.idProjeto", array("*"), "SAC.dbo");

        $slct->where("pj.idAgente = ?", $idAgente);
        return $this->fetchAll($slct);
    }

    /**
     * propostasPorEdital
     *
     * @param bool $where
     * @param bool $order
     * @param mixed $tamanho
     * @param mixed $inicio
     * @param bool $count
     * @access public
     * @return void
     */
    public function propostasPorEdital($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("p" => $this->_name),
            array(
                "idProjeto" => "idPreProjeto",
                "NomeProposta" => "NomeProjeto",
                "idAgente",
                "DtCadastro" => new Zend_Db_Expr("CONVERT(CHAR(20),p.dtAceite, 120)")
            ),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("m" => "tbMovimentacao"),
            "p.idPreProjeto = m.idProjeto AND m.stEstado = 0",
            array("idMovimentacao", "CodSituacao" => "m.Movimentacao", "DtMovimentacao" => new Zend_Db_Expr("CONVERT(CHAR(20),m.DtMovimentacao, 120)")),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("x" => "tbAvaliacaoProposta"),
            "p.idPreProjeto = x.idProjeto AND x.stEstado = 0",
            array("ConformidadeOK"),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("x1" => "tbAvaliacaoProposta"),
            "p.idPreProjeto = x1.idProjeto",
            array("DtEnvioMinC" => new Zend_Db_Expr("CONVERT(CHAR(20),x1.DtEnvio , 120)")),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("mv" => "tbMovimentacao"),
            "p.idPreProjeto = mv.idProjeto and mv.stEstado = 0",
            array("stMovimentacao" => "movimentacao"),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("vr" => "Verificacao"),
            "mv.movimentacao = vr.idVerificacao and vr.idTipo = 4",
            array("Movimentacao" => "Descricao"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("a" => "Agentes"),
            "p.idAgente = a.idAgente",
            array("CNPJCPF"),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("n" => "Nomes"),
            "p.idAgente = n.idAgente",
            array("NomeAgente" => "Descricao"),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("e" => "Edital"),
            "e.idEdital = p.idEdital",
            array("idOrgao"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("fd" => "tbFormDocumento"),
            "fd.idEdital = p.idEdital AND idClassificaDocumento NOT IN (23,24,25)",
            array("Edital" => "nmFormDocumento", "idEdital"),
            "BDCORPORATIVO.scQuiz"
        );
        $slct->joinInner(
            array("cl" => "tbClassificaDocumento"),
            "cl.idClassificaDocumento = fd.idClassificaDocumento",
            array("idClassificaDocumento", "dsClassificaDocumento"),
            "BDCORPORATIVO.scSAC"
        );
        $slct->joinInner(
            array("o" => "Orgaos"),
            "o.Codigo = e.idEdital",
            array("SiglaOrgao" => "Sigla"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("ab" => "Abrangencia"),
            "p.idPreProjeto = ab.idProjeto AND ab.stAbrangencia = 1",
            array(),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("uf" => "UF"),
            "uf.idUF = ab.idUF",
            array("idUF", "SiglaUF" => "Sigla", "NomeUF" => "Descricao", "Regiao"),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("mu" => "Municipios"),
            "mu.idMunicipioIBGE = ab.idMunicipioIBGE",
            array("NomeMunicipio" => "Descricao"),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("vr2" => "Verificacao"),
            "e.cdTipoFundo = vr2.idVerificacao and vr2.idTipo = 15",
            array("FundoNome" => "Descricao", "idFundo" => "idVerificacao"),
            "SAC.dbo"
        );

        if ($count) {
            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                array('p' => $this->_name),
                array("total" => "count(*)")
            );
            $slct2->joinLeft(
                array("m" => "tbMovimentacao"),
                "p.idPreProjeto = m.idProjeto AND m.stEstado = 0",
                array(),
                "SAC.dbo"
            );
            $slct2->joinLeft(
                array("x" => "tbAvaliacaoProposta"),
                "p.idPreProjeto = x.idProjeto AND x.stEstado = 0",
                array(),
                "SAC.dbo"
            );
            $slct2->joinLeft(
                array("x1" => "tbAvaliacaoProposta"),
                "p.idPreProjeto = x1.idProjeto",
                array(),
                "SAC.dbo"
            );
            $slct2->joinLeft(
                array("mv" => "tbMovimentacao"),
                "p.idPreProjeto = mv.idProjeto and mv.stEstado = 0",
                array(),
                "SAC.dbo"
            );
            $slct2->joinLeft(
                array("vr" => "Verificacao"),
                "mv.movimentacao = vr.idVerificacao and vr.idTipo = 4",
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array("a" => "Agentes"),
                "p.idAgente = a.idAgente",
                array(),
                "AGENTES.dbo"
            );
            $slct2->joinInner(
                array("n" => "Nomes"),
                "p.idAgente = n.idAgente",
                array(),
                "AGENTES.dbo"
            );
            $slct2->joinInner(
                array("e" => "Edital"),
                "e.idEdital = p.idEdital",
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array("fd" => "tbFormDocumento"),
                "fd.idEdital = p.idEdital AND idClassificaDocumento NOT IN (23,24,25)",
                array(),
                "BDCORPORATIVO.scQuiz"
            );
            $slct2->joinInner(
                array("cl" => "tbClassificaDocumento"),
                "cl.idClassificaDocumento = fd.idClassificaDocumento",
                array(),
                "BDCORPORATIVO.scSAC"
            );
            $slct2->joinInner(
                array("o" => "Orgaos"),
                "o.Codigo = e.idEdital",
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array("ab" => "Abrangencia"),
                "p.idPreProjeto = ab.idProjeto AND ab.stAbrangencia = 1",
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array("uf" => "UF"),
                "uf.idUF = ab.idUF",
                array(),
                "AGENTES.dbo"
            );
            $slct2->joinInner(
                array("mu" => "Municipios"),
                "mu.idMunicipioIBGE = ab.idMunicipioIBGE",
                array(),
                "AGENTES.dbo"
            );
            $slct2->joinInner(
                array("vr2" => "Verificacao"),
                "e.cdTipoFundo = vr2.idVerificacao and vr2.idTipo = 15",
                array(),
                "SAC.dbo"
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct2->where($coluna, $valor);
            }
            $rs = $this->fetchAll($slct2)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
        }

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
     * checklistEnvioProposta - Checklist para enviar proposta para Minc
     *
     * @param mixed $idPreProjeto
     * @access public
     * @return void
     * @author wouerner <wouerner@gmail.com>
     */
    public function checklistEnvioPropostaSemSp($idPreProjeto, $alterarprojeto = false)
    {
        $validacao = new stdClass();
        $listaValidacao = array();

        $db = $this->getAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);


        #verificar se a proposta está com o proponente
        $whereMovimentacao = array(
            'idProjeto = ?' => $idPreProjeto,
            'Movimentacao <> ?' => 95,
            'stEstado = ?' => 0
        );

        $tbMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
        $movimentacao = $tbMovimentacao->buscar($whereMovimentacao, array(), 1)->current();

        if (!empty($movimentacao) && !$alterarprojeto) {
            $validacao->dsInconsistencia = 'A proposta cultural encontra-se no minist&eacute;rio da Cidadania';
            $validacao->Observacao = '';
            $validacao->Url = '';
            $listaValidacao[] = clone($validacao);
        } else {
            $sql = $db->select()
                ->from($this->_name, $this->_getCols(), $this->_schema)
                ->where('idPreProjeto = ?', $idPreProjeto);
            $idAgente = $db->fetchRow($sql)->idAgente;

            $sql = $db->select()
                ->from(array('tbavaliacaoproposta'), '*', $this->_schema)
                ->where('idProjeto = ?', $idPreProjeto);

            $avaliacaoProposta = $db->fetchAll($sql);
            //if(( date('m') == 13 || date('m') == 1 ) && empty($avaliacaoProposta)) { @todo voltar esta linha, apenas para teste
            if (getenv('APPLICATION_ENV') == 'production' && empty($avaliacaoProposta)) {
                $validacao->dsInconsistencia = 'Conforme Art 9&#176; da Instru&ccedil;&atilde;o Normativa n&#176; 1, de 24 de junho de 2013, nenhuma proposta poder&aacute; ser enviada ao MinC nos meses de DEZEMBRO e JANEIRO!';
                $validacao->Observacao = 'PENDENTE';
                $validacao->Url = '';
                $listaValidacao[] = clone($validacao);
            } else {
                $sql = $db->select()
                    ->from(array('v' => 'vcadastrarproponente'), 'v.*', $this->_schema)
                    ->join(array('p' => 'preprojeto'), 'v.idAgente = p.idAgente', null, $this->_schema)
                    ->where('idpreprojeto = ?', $idPreProjeto)
                    ->where('Correspondencia = 1')
                    ->limit(1);
                $vCadastrarProponente = $db->fetchAll($sql);

                //VERIFICAR AS INFORMACOES DO PROPONENTE
                if (empty($vCadastrarProponente)) {
                    $validacao->dsInconsistencia = 'Dados cadastrais do proponente inexistente ou não h&aacute; endereço para correspondência selecionado.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'agentes', 'id' => $idAgente);
                    $listaValidacao[] = clone($validacao);
                }

                //VERIFICAR A REGULARIDADE DO PROPONENTE
                $sql = $db->select()
                    ->from(array('v' => 'vcadastrarproponente'), 'v.*', $this->_schema)
                    ->join(array('p' => 'preprojeto'), 'v.idAgente = p.idAgente', null, $this->_schema)
                    ->join(array('i' => 'inabilitado'), 'v.CnpjCpf=i.CgcCpf', null, $this->_schema)
                    ->where('idpreprojeto = ?', $idPreProjeto)
                    ->where('v.CnpjCpf=i.CgcCpf')
                    ->where("Habilitado='N'")
                    ->limit(1);

                $regularidadeProponente = $db->fetchAll($sql);
                if (!empty($regularidadeProponente)) {
                    $validacao->dsInconsistencia = 'Proponente em situa&ccedil;&atilde;o IRREGULAR no Minist&eacute;rio da Cidadania.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = '';
                    $listaValidacao[] = clone($validacao);
                }

                //-- VERIFICAR SE HA OS EMAILS DO PROPONENTE CADASTRADOS
                $sql = $db->select()
                    ->from(array('v' => 'internet'), 'v.*', $this->getSchema('agentes'))
                    ->join(array('p' => 'preprojeto'), 'v.idAgente=p.idAgente', null, $this->_schema)
                    ->where('idpreprojeto= ?', $idPreProjeto)
                    ->where('Status=1')
                    ->limit(1);
                $verificarEmail = $db->fetchAll($sql);
                if (empty($verificarEmail)) {
                    $validacao->dsInconsistencia = 'E-mail do proponente inexistente';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'agentes', 'id' => $idAgente);
                    $listaValidacao[] = clone($validacao);
                }

                //-- NO CASO DE PESSOA FISICA, VERIFICAR O LANCAMENTO DA DATA DE NASCIMENTO
                $sql = $db->select()
                    ->from(array('v' => 'agentes'), 'tipopessoa', $this->getSchema('agentes'))
                    ->where('idAgente = ?', $idAgente);
                $resultPessoa = $db->fetchAll($sql);
                if (count($resultPessoa) > 0) {
                    $tipoPessoa = $resultPessoa[0]->TipoPessoa;
                }
                if ($tipoPessoa == 0) {
                    $sql = $db->select()
                        ->from(array('tbagentefisico'), 'dtnascimento', $this->getSchema('agentes'))
                        ->where('idagente = ?', $idAgente);

                    $dataNasc = $db->fetchAll($sql);

                    if (empty($dataNasc)) {
                        $validacao->dsInconsistencia = 'Data de nascimento inexistente.';
                        $validacao->Observacao = 'PENDENTE';
                        $validacao->Url = array('module' => 'agente', 'controller' => 'agentes', 'action' => 'info-adicionais', 'id' => $idAgente);
                        $listaValidacao[] = clone($validacao);
                    }
                }

                //-- NO CASO DE PESSOA JURIDICA, VERIFICAR O LANCAMENTO DA NATUREZA DO PROPONENTE
                if ($tipoPessoa == 1) {
                    $sql = $db->select()
                        ->from(array('n' => 'natureza'), '*', $this->getSchema('agentes'))
                        ->join(array('p' => 'preprojeto'), 'n.idAgente=p.idAgente', '*', $this->_schema)
                        ->where('idpreprojeto = ?', $idPreProjeto)
                        ->limit(1);

                    $natureza = $db->fetchAll($sql);
                    if (empty($natureza)) {
                        $validacao->dsInconsistencia = 'Natureza do proponente.';
                        $validacao->Observacao = 'PENDENTE';
                        $validacao->Url = '';
                        $listaValidacao[] = clone($validacao);
                    }

                    //-- VERIFICAR SE HA DIRIGENTE CADASTRADO
                    $sql = $db->select()
                        ->from(array('v' => 'vcadastrardirigente'), '*', $this->_schema)
                        ->join(array('p' => 'preprojeto'), 'v.idVinculoPrincipal=p.idAgente', '*', $this->_schema)
                        ->where('idPreProjeto= ?', $idPreProjeto);

                    $dirigenteCadastrado = $db->fetchAll($sql);
                    if (empty($dirigenteCadastrado)) {
                        $validacao->dsInconsistencia = 'Cadastro de Dirigente.';
                        $validacao->Observacao = 'PENDENTE';
                        $validacao->Url = '';
                        $listaValidacao[] = clone($validacao);
                    }
                }

                // Verifica se o proponente Proposta aprovado em editais (618) ou Proposta  com contratos de patrocínios (619)
                $sql = $db->select()
                    ->from($this->_name, $this->_getCols(), $this->_schema)
                    ->where('idPreProjeto = ?', $idPreProjeto);
                $stProposta = $db->fetchRow($sql)->stProposta;

                $idDocumento = '';
                if ($stProposta == '618') {
                    $msg = 'No caso de proposta aprovada em editais &eacute; obrigat&oacute;rio anexar o documento Resultado da Sele&ccedil;&atilde;o p&uacute;blica';
                    $idDocumento = 248;
                } elseif ($stProposta == '619') {
                    $msg = 'No caso de proposta com contratos de patroc&iacute;nios &eacute; obrigat&oacute;rio anexar o Contrato firmado com o Incentivador';
                    $idDocumento = 162;
                }

                if (!empty($idDocumento)) {
                    $sql = $db->select()
                        ->from(array('tbDocumentosPreProjeto'), '*', $this->_schema)
                        ->where('idProjeto = ?', $idPreProjeto)
                        ->where('CodigoDocumento = ?', $idDocumento)
                        ->limit(1);
                    $documento = $db->fetchRow($sql);

                    if (empty($documento)) {
                        $validacao->dsInconsistencia = $msg;
                        $validacao->Observacao = 'PENDENTE';
                        $validacao->Url = array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'identificacaodaproposta', 'idPreProjeto' => $idPreProjeto);
                        $listaValidacao[] = clone($validacao);
                    }
                }

                //-- VERIFICAR SE O LOCAL DE REALIZACAO ESTA CADASTRADO
                //IF NOT EXISTS(SELECT TOP 1 * FROM Abrangencia WHERE idProjeto = @idProjeto)

                $sql = $db->select()
                    ->from(array('abrangencia'), '*', $this->_schema)
                    ->where('idProjeto = ?', $idPreProjeto)
                    ->limit(1);

                $local = $db->fetchAll($sql);

                if (empty($local)) {
                    $validacao->dsInconsistencia = 'O Local de realiza&ccedil;&atilde;o da proposta n&atilde;o foi preenchido.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'proposta', 'controller' => 'localderealizacao', 'idPreProjeto' => $idPreProjeto);
                    $listaValidacao[] = clone($validacao);
                }

                //-- VERIFICAR SE EXISTE NO MINIMO 90 DIAS ENTRE A DATA DE ENVIO E O INICIO DO PERIODO DE EXECUCAO DO PROJETO
                $sql = $db->select()
                    ->from($this->_name, array('*'), $this->_schema)
                    ->where('idPreProjeto = ?', $idPreProjeto)
                    ->limit(1);

                if ($this->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
                    $sql->where('DATEDIFF(DAY,GETDATE(),DtInicioDeExecucao) < 90');
                } else {
                    $sql->where("DATE_PART('day', dtiniciodeexecucao - now()) < 90");
                }
                $minimo90 = $db->fetchAll($sql);


                if (!empty($minimo90)) {
                    $validacao->dsInconsistencia = 'A diferen&ccedil;a em dias entre a data de envio do projeto ao MinC e a data de in&iacute;cio de execu&ccedil;&atilde;o do projeto est&aacute; menor do que 90 dias.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'proposta', 'controller' => 'manterpropostaincentivofiscal', 'action' => 'identificacaodaproposta', 'idPreProjeto' => $idPreProjeto);
                    $listaValidacao[] = clone($validacao);
                }

                //-- VERIFICAR SE O PLANO DE DISTRIBUICAO DO PRODUTO ESTA PREENCHIDO
                $sql = $db->select()
                    ->from(array('planodistribuicaoproduto'), '*', $this->_schema)
                    ->where('idProjeto =  ?', $idPreProjeto)
                    ->limit(1);

                $planoDistribuicao = $db->fetchAll($sql);
                if (empty($planoDistribuicao)) {
                    $validacao->dsInconsistencia = 'O Plano Distribui&ccedil;&atilde;o de Produto n&atilde;o foi preenchido.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'proposta', 'controller' => 'plano-distribuicao', 'action' => 'index', 'idPreProjeto' => $idPreProjeto);
                    $listaValidacao[] = clone($validacao);
                }

                //--Verificar a existencia do produto principal
                //SELECT @QtdeOutros=stPrincipal FROM PlanoDistribuicaoProduto  WHERE idProjeto = @idProjeto and stPrincipal = 1
                $sql = $db->select()
                    ->from(array('planodistribuicaoproduto'), 'stprincipal', $this->_schema)
                    ->where('idProjeto =  ?', $idPreProjeto)
                    ->where('stprincipal = 1');

                $quantidade = count($db->fetchAll($sql));

                if ($quantidade == 0) {
                    $validacao->dsInconsistencia = 'N&atilde;o h&aacute; produto principal selecionado na proposta.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'proposta', 'controller' => 'plano-distribuicao', 'action' => 'index', 'idPreProjeto' => $idPreProjeto);
                    $listaValidacao[] = clone($validacao);
                } elseif ($quantidade > 1) {
                    $validacao->dsInconsistencia = 'Só poder&aacute; haver um produto principal em cada proposta, a sua est&aacute; com mais de um produto.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'proposta', 'controller' => 'plano-distribuicao', 'action' => 'index', 'idPreProjeto' => $idPreProjeto);
                    $listaValidacao[] = clone($validacao);
                }

                //-- VERIFICAR SE EXISTE NA PLANILHA ORCAMENTARIA ITENS DA FONTE INCENTIVO FISCAL FEDERAL.
                $sql = $db->select()
                    ->from(array('tbplanilhaproposta'), '*', $this->_schema)
                    ->where('idProjeto =  ?', $idPreProjeto)
                    ->where('FonteRecurso = 109')
                    ->limit(1);

                $planilhaOrcamentaria = $db->fetchAll($sql);

                if (empty($planilhaOrcamentaria)) {
                    $validacao->dsInconsistencia = 'N&atilde;o existe item or&ccedil;ament&aacute;rio referente a fonte de recurso - Incentivo Fiscal Federal.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'proposta', 'controller' => 'manterorcamento', 'action' => 'produtoscadastrados', 'idPreProjeto' => $idPreProjeto);
                    $listaValidacao[] = clone($validacao);
                }

                //-- VERIFICAR SE EXISTE NA PLANILHA ORCAMENTARIA PARA CADA PRODUTO DESCRITO NO PLANO DE DISTRIBUICAO DO PRODUTO
                //IF EXISTS(SELECT * FROM PlanoDistribuicaoProduto pp WHERE idProjeto = @idProjeto and
                //NOT EXISTS(SELECT * FROM tbPlanilhaProposta pl WHERE idProjeto = @idProjeto and pp.idProduto=pl.idProduto and idProduto <> 0))
                $subSql = $db->select()
                    ->from(array('pl' => 'tbplanilhaproposta'), '*', $this->_schema)
                    ->where('idProjeto =  ?', $idPreProjeto)
                    ->where('pp.idProduto=pl.idProduto')
                    ->where('idProduto <> 0');

                $sql = $db->select()
                    ->from(array('pp' => 'planodistribuicaoproduto'), '*', $this->_schema)
                    ->where('idProjeto =  ?', $idPreProjeto)
                    ->where(new Zend_Db_Expr("NOT EXISTS($subSql)"));

                $planilhaProduto = $db->fetchAll($sql);

                if (!empty($planilhaProduto)) {
                    $validacao->dsInconsistencia = 'Existe produto cadastrado sem a respectiva planilha orcament&aacute;ria cadastrada.';
                    $validacao->Observacao = 'PENDENTE';
                    $validacao->Url = array('module' => 'proposta', 'controller' => 'manterorcamento', 'action' => 'produtoscadastrados', 'idPreProjeto' => $idPreProjeto);
                    $listaValidacao[] = clone($validacao);
                }

                //-- VERIFICAR O PERCENTUAL DA REMUNERACAO PARA CAPTACAO DE RECURSOS
                $sql = $db->select()
                    ->from(array('tbplanilhaproposta'), 'SUM(Quantidade * Ocorrencia * ValorUnitario) as total', $this->_schema)
                    ->where('idProjeto =  ?', $idPreProjeto)
                    ->where('FonteRecurso = 109')
                    ->where('idPlanilhaItem <> 5249');

                $total = $db->fetchAll($sql);
                $total = empty($total[0]->total) ? 0 : $total[0]->total;

                //--pega o valor de remuneracao para captacao
                $sql = $db->select()
                    ->from(array('tbplanilhaproposta'), 'SUM(Quantidade * Ocorrencia * ValorUnitario) as total', $this->_schema)
                    ->where('idProjeto =  ?', $idPreProjeto)
                    ->where('FonteRecurso = 109')
                    ->where('idPlanilhaItem = 5249');

                $custoAdm = $db->fetchAll($sql);
                $custoAdm = empty($custoAdm[0]->total) ? 0 : $custoAdm[0]->total;
            }
        }

        $validado = true;
        foreach ($listaValidacao as $valido) {
            if ($valido->Observacao == 'PENDENTE') {
                $validado = false;
                break;
            }
        }

        if ($validado) {
            $validacao->dsInconsistencia = 'A proposta cultural n&atilde;o possui pend&ecirc;ncias';
            $validacao->Observacao = true;
            $validacao->Url = '';
            return $validacao;
        } else {
            $validacao->dsInconsistencia = '<font color=red><b> A PROPOSTA CULTURAL N&Atilde;O FOI ENVIADA AO MINIST&Eacute;RIO DA CIDADANIA DEVIDO &Agrave;S PEND&Ecirc;NCIAS ASSINALADAS ACIMA.</b></font>';
            $validacao->Observacao = '';
            $validacao->Url = '';
            $listaValidacao[] = clone($validacao);
        }

        return $listaValidacao;
    }

    public function listarProdutos($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('pre' => 'preprojeto'), array('pre.idpreprojeto as PreProjeto', 'pre.idpreprojeto as idProposta'), $this->getSchema('sac'))
            ->join(array('pd' => 'planodistribuicaoproduto'), '(pre.idpreprojeto = pd.idProjeto AND pd.stplanodistribuicaoproduto = 1)', array('pd.stPrincipal as ProdutoPrincipal'), $this->getSchema('sac'))
            ->join(array('p' => 'produto'), '(pd.idproduto = p.codigo)', array('p.codigo as CodigoProduto', 'p.descricao as DescricaoProduto'), $this->getSchema('sac'))
            ->where('idpreprojeto = ?', $idPreProjeto)
            ->order('pd.stPrincipal DESC')
            ->group(array('p.codigo', 'p.descricao', 'idpreprojeto', 'pd.stPrincipal'));

        return $db->fetchAll($sql);
    }

    public function listarEtapasProdutos($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        //$sql = $db->select()
        //->from(['pre' => 'preprojeto'], 'pre.idpreprojeto as idPreProjeto', $this->getSchema('sac'))
        //->join(['pp' => 'tbplanilhaproposta'], '(pre.idpreprojeto = pp.idProjeto)', ['pp.idproduto as idProduto', 'pp.idetapa as idEtapa'], $this->getSchema('sac'))
        //->join(['p' => 'produto'], '(pp.idproduto = p.codigo)', 'p.codigo as CodigoProduto', $this->getSchema('sac'))
        //->join(['te' => 'tbplanilhaetapa'], 'te.idplanilhaetapa = pp.idetapa', 'te.descricao as DescricaoEtapa', $this->getSchema('sac'))
        //->where('idpreprojeto = ?', $idPreProjeto)
        //->order('te.DescricaoEtapa')
        //;

        //$sql = "SELECT
        //distinct
        //p.Codigo as CodigoProduto,
        //pp.idProduto as idProduto,
        //pp.idEtapa as idEtapa,
        //te.Descricao as DescricaoEtapa,
        //pre.idPreProjeto as idPreProjeto
        //FROM SAC.dbo.PreProjeto pre
        //INNER JOIN SAC.dbo.tbPlanilhaProposta pp ON (pre.idPreProjeto = pp.idProjeto)
        //INNER JOIN SAC.dbo.Produto p ON (pp.idProduto = p.Codigo)
        //INNER JOIN SAC..tbPlanilhaEtapa te on te.idPlanilhaEtapa = pp.idEtapa
        //WHERE idPreProjeto = {$idPreProjeto}";

        //$sql.= " ORDER BY te.DescricaoEtapa ";

        $sql = $db->select()
            ->from(array('tbplanilhaetapa'), array('idplanilhaetapa as idEtapa', 'descricao as DescricaoEtapa'), $this->getSchema('sac'))
            ->where("tpCusto = 'P'");

        //$sql = " SELECT idPlanilhaEtapa as idEtapa, Descricao as DescricaoEtapa FROM SAC.dbo.tbPlanilhaEtapa WHERE tpCusto = 'P' ";

        throw new Exception('Método transferido para Proposta_Model_DbTable_TbPlanilhaEtapa');

        return $db->fetchAll($sql);
    }

    //@todo lugar certo é tbPlanilhaProposta, remover do ManterOrcamentoDAO tbm
    public function listarItensProdutos($idPreProjeto, $idItem = null, $fetchMode = Zend_DB::FETCH_OBJ)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode($fetchMode);

        $pp = array(
            'pp.idetapa as idEtapa',
            'pp.idplanilhaitem as idItem',
            'pp.ufdespesa as IdUf',
            'pp.Quantidade',
            'pp.Ocorrencia',
            'pp.ValorUnitario',
            'pp.qtdeDias',
            'pp.idPlanilhaProposta',
            'Verificador' => new Zend_Db_Expr("CONCAT(pp.idProduto, pp.idetapa, pp.municipiodespesa)"),
        );

        $sql = $db->select()->distinct()
            ->from(array('pre' => 'preprojeto'), null, $this->getSchema('sac'))
            ->join(array('pp' => 'tbplanilhaproposta'), '(pre.idPreProjeto = pp.idProjeto)', $pp, $this->getSchema('sac'))
            ->join(array('p' => 'produto'), '(pp.idProduto = p.codigo)', array('p.codigo as CodigoProduto'), $this->getSchema('sac'))
            ->join(array('ti' => 'tbplanilhaitens'), 'ti.idplanilhaitens = pp.idplanilhaitem', array('ti.descricao as DescricaoItem'), $this->getSchema('sac'))
            ->join(array('uf' => 'uf'), 'uf.codufibge = pp.ufdespesa', array('uf.descricao as DescricaoUf', 'uf.uf as SiglaUF'), $this->getSchema('sac'))
            ->join(array('municipio' => 'municipios'), 'municipio.idmunicipioibge = pp.municipiodespesa', array('municipio.descricao as Municipio'), $this->getSchema('agentes'))
            ->join(array('mec' => 'mecanismo'), 'mec.codigo = pre.mecanismo', array('mec.descricao as DescricaoMecanismo'), $this->getSchema('sac'))
            ->join(array('un' => 'tbplanilhaunidade'), 'un.idunidade = pp.unidade', 'un.descricao as Unidade', $this->getSchema('sac'))
            ->join(array('veri' => 'verificacao'), 'veri.idverificacao = pp.fonterecurso', array('veri.idverificacao as idFonteRecurso', 'veri.descricao as DescricaoFonteRecurso'), $this->getSchema('sac'))
            ->where('idpreprojeto = ?', $idPreProjeto)
            ->order('ti.descricao');

        if ($idItem) {
            $sql->where("pp.idPlanilhaItem = ?", $idItem);
        }

        return $db->fetchAll($sql);
    }

    public function buscarProdutos($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pre' => $this->_name),
            array(
                'p.codigo as codigoproduto',
                'p.descricao as descricaoproduto',
                'pre.idpreprojeto as preprojeto',
                'pre.idpreprojeto as idproposta'
            ),
            $this->_schema
        );

        $select->joinInner(
            array('pd' => 'PlanoDistribuicaoProduto'),
            'pre.idPreProjeto = pd.idProjeto AND pd.stPlanoDistribuicaoProduto = 1',
            null,
            $this->_schema
        );

        $select->joinInner(
            array('p' => 'Produto'),
            'pd.idproduto = p.codigo',
            null,
            $this->_schema
        );

        $select->where('idpreprojeto = ?', $idPreProjeto);

        $select->group(array('p.codigo', 'p.descricao', 'idpreprojeto'));

        return $db->fetchAll($select);
    }

    public function buscarEtapasProdutos($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = "SELECT
                    distinct
                    p.Codigo as CodigoProduto,
                    pp.idProduto as idProduto,
                    pp.idEtapa as idEtapa,
                    te.Descricao as DescricaoEtapa,
                    pre.idPreProjeto as idPreProjeto
                    FROM SAC.dbo.PreProjeto pre
                    INNER JOIN SAC.dbo.tbPlanilhaProposta pp ON (pre.idPreProjeto = pp.idProjeto)
                    INNER JOIN SAC.dbo.Produto p ON (pp.idProduto = p.Codigo)
                    INNER JOIN SAC..tbPlanilhaEtapa te on te.idPlanilhaEtapa = pp.idEtapa
                    WHERE idPreProjeto = {$idPreProjeto}";

        $sql .= " ORDER BY te.DescricaoEtapa ";

        $sql = " SELECT idPlanilhaEtapa as idEtapa, Descricao as DescricaoEtapa FROM SAC.dbo.tbPlanilhaEtapa
            WHERE tpCusto = 'P' ";

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('pre' => 'PreProjeto'),
            array(
                'p.codigo as codigoproduto',
                'pp.idproduto as idproduto',
                'pp.idetapa as idetapa',
                'te.descricao as descricaoetapa',
                'pre.idpreprojeto as idpreprojeto'
            ),
            $this->_schema
        );

        $select->joinInner(
            array('pp' => 'tbPlanilhaProposta'),
            'pre.idpreprojeto = pp.idprojeto',
            null,
            $this->_schema
        );

        $select->joinInner(
            array('p' => 'Produto'),
            'pp.idproduto = p.codigo',
            null,
            $this->_schema
        );

        $select->joinInner(
            array('p' => 'Produto'),
            'pd.idproduto = p.codigo',
            null,
            $this->_schema
        );

        try {
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Etapas: " . $e->getMessage();
        }
        return $db->fetchAll($sql);
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
    public function propostas($idResponsavel, $idAgenteCombo, $where = array(), $order = [], $start = 0, $limit = 20, $search = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $subSql = $db->select()
            ->from(array('pr' => 'projetos'), array('idprojeto'), $this->_schema)
            ->where('a.idpreprojeto = pr.idprojeto');

        $sql = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('a.idagente = ? ', $idAgenteCombo)
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'");

        $sql2 = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->join(array('c' => 'vinculacao'), 'b.idagente = c.idvinculoprincipal', array(), $this->getSchema('agentes'))
            ->join(array('d' => 'agentes'), 'c.idagente = d.idagente', array(), $this->getSchema('agentes'))
            ->join(array('e' => 'sgcacesso'), 'd.cnpjcpf = e.cpf', array(), $this->getSchema('controledeacesso'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('e.idusuario = ?', $idResponsavel)
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'");

        $sql3 = $db->select()
            ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->join(array('c' => 'nomes'), 'b.idagente = c.idagente', array('c.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->join(array('d' => 'sgcacesso'), 'a.idusuario = d.idusuario', array(), $this->getSchema('controledeacesso'))
            ->join(array('e' => 'tbvinculoproposta'), 'a.idpreprojeto = e.idpreprojeto', array(), $this->getSchema('agentes'))
            ->join(array('f' => 'tbvinculo'), 'e.idvinculo = f.idvinculo', array(), $this->getSchema('agentes'))
            ->where('a.stestado = 1')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
            ->where('e.sivinculoproposta = 2')
            ->where('f.idusuarioresponsavel = ?', $idResponsavel);

        $sql2->where('a.idagente = ?', $idAgenteCombo);
        $sql3->where('a.idagente = ?', $idAgenteCombo);

        $sql = $db->select()->union(array($sql, $sql2, $sql3), Zend_Db_Select::SQL_UNION);

        $sqlFinal = $db->select()->from(array("p" => $sql));

        foreach ($where as $coluna => $valor) {
            $sqlFinal->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sqlFinal->where('p.idpreprojeto like ?', '%'.$search['value']);
        }

        if(count($order) > 0 && !empty(trim($order[0]))) {
            $sqlFinal->order($order);
        }

        if (!is_null($start) && $limit) {
            $start = (int) $start;
            $limit = (int) $limit;
            $sqlFinal->limit($limit, $start);
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
    public function propostasTotal($idResponsavel, $idAgenteCombo, $where = array(), $order = array(), $start = 0, $limit = 20, $search = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $subSql = $db->select()
            ->from(array('pr' => 'projetos'), array('idprojeto'), $this->_schema)
            ->where('a.idpreprojeto = pr.idprojeto');

        $sql = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('a.idagente = ? ', $idAgenteCombo)
            ->where('(a.stestado = 0 AND a.dtArquivamento IS NOT NULL) OR (a.stestado = 1)', '')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'");

        $sql2 = $db->select()
            ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->join(array('c' => 'vinculacao'), 'b.idagente = c.idvinculoprincipal', array(), $this->getSchema('agentes'))
            ->join(array('d' => 'agentes'), 'c.idagente = d.idagente', array(), $this->getSchema('agentes'))
            ->join(array('e' => 'sgcacesso'), 'd.cnpjcpf = e.cpf', array(), $this->getSchema('controledeacesso'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('e.idusuario = ?',$idResponsavel)
            ->where('(a.stestado = 0 AND a.dtArquivamento IS NOT NULL) OR (a.stestado = 1)', '')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'");

        $sql3 = $db->select()
            ->from(array('a'=>'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->join(array('c' => 'nomes'), 'b.idagente = c.idagente', array('c.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->join(array('d' => 'sgcacesso'), 'a.idusuario = d.idusuario', array(), $this->getSchema('controledeacesso'))
            ->join(array('e' => 'tbvinculoproposta'), 'a.idpreprojeto = e.idpreprojeto', array(), $this->getSchema('agentes'))
            ->join(array('f' => 'tbvinculo'), 'e.idvinculo = f.idvinculo', array(), $this->getSchema('agentes'))
            ->where('(a.stestado = 0 AND a.dtArquivamento IS NOT NULL) OR (a.stestado = 1)', '')
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
            ->where('e.sivinculoproposta = 2')
            ->where('f.idusuarioresponsavel = ?', $idResponsavel);

        $sql2->where('a.idagente = ?', $idAgenteCombo);
        $sql3->where('a.idagente = ?', $idAgenteCombo);

        $sql = $db->select()->union(array($sql, $sql2, $sql3), Zend_Db_Select::SQL_UNION);

        $sqlFinal = $db->select()->from(array("p" => $sql), array('count(distinct p.idpreprojeto)'));

        foreach ($where as $coluna => $valor) {
            $sqlFinal->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sqlFinal->where('p.idpreprojeto = ?', $search['value']);
        }

        $sqlFinal->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sqlFinal->limitPage($start, $limit);
        }

        return $db->fetchOne($sqlFinal);
    }

    public function valorTotalSolicitadoNaProposta($idPreProjeto)
    {
        $select = new Zend_Db_Expr("SELECT sac.dbo.fnSolicitadoNaProposta({$idPreProjeto}) as valorTotalSolicitado");

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchOne($select);
    }


    public function spChecklistParaApresentacaoDeProposta($idPreProjeto)
    {
        $select = new Zend_Db_Expr("EXEC sac.dbo.spChecklistParaApresentacaoDeProposta {$idPreProjeto}");

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $result = $db->fetchAll($select);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $result;
    }

    public function buscarProponenteProposta($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('pre' => 'preprojeto'), array(), $this->_schema)
            ->join(array('a' => 'agentes'), 'pre.idagente = a.idagente', array('*'), $this->getSchema('agentes'))
            ->where("pre.idpreprojeto = ?", $idPreProjeto);;
        return $db->fetchRow($sql);
    }


    public function verificarCNAEProponenteComProdutoPrincipal($idPreProjeto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(['proposta' => $this->_name], '', $this->_schema);

        $select->joinInner(['agentes' => 'agentes'], 'proposta.idAgente = agentes.idAgente', '', $this->getSchema('agentes'));

        $select->joinInner(['vw' => 'vwPessoaJuridica_CNAE'], 'agentes.CNPJCPF = vw.NR_CNPJ', '*', $this->_schema);

        $select->joinInner(
            ['produtoprincipal' => 'vwPlanoDeDistribuicaoProduto'],
            'proposta.idPreProjeto = produtoprincipal.idProjeto',
            '',
            $this->_schema
        );

        $select->joinInner(['cnae' => 'tbCnaeCultural'], 'produtoprincipal.Area = cnae.cdArea AND produtoprincipal.Segmento = cnae.cdSegmento AND vw.CD_CNAE  = cnae.cdCnae', '', $this->_schema);

        $select->where('agentes.TipoPessoa = ?', 1);
        $select->where('produtoprincipal.stPrincipal = ?', 1);
        $select->where('proposta.idPreProjeto  = ?', $idPreProjeto);
        $select->limit(1);

        return $this->fetchRow($select);
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
            ->where('a.idpreprojeto = pr.idprojeto');

        $sql = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto', 'a.DtArquivamento'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('a.idagente = ? ', $idAgente)
            ->where('a.stestado = ?', $stEstado)
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'");

        $sql2 = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto', 'a.DtArquivamento'), $this->_schema)
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), $this->getSchema('agentes'))
            ->join(array('c' => 'vinculacao'), 'b.idagente = c.idvinculoprincipal', array(), $this->getSchema('agentes'))
            ->join(array('d' => 'agentes'), 'c.idagente = d.idagente', array(), $this->getSchema('agentes'))
            ->join(array('e' => 'sgcacesso'), 'd.cnpjcpf = e.cpf', array(), $this->getSchema('controledeacesso'))
            ->joinleft(array('n' => 'nomes'), 'n.idagente = b.idagente', array('n.descricao as nomeproponente'), $this->getSchema('agentes'))
            ->where('e.idusuario = ?', $idResponsavel)
            ->where('a.stestado = ?', $stEstado)
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'");

        $sql3 = $db->select()
            ->from(array('a' => 'preprojeto'), array('a.idpreprojeto', 'a.nomeprojeto', 'a.DtArquivamento'), Proposta_Model_DbTable_PreProjeto::getSchema('sac'))
            ->join(array('b' => 'agentes'), 'a.idagente = b.idagente', array('b.cnpjcpf', 'b.idagente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('c' => 'nomes'), 'b.idagente = c.idagente', array('c.descricao as nomeproponente'), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('d' => 'sgcacesso'), 'a.idusuario = d.idusuario', array(), Proposta_Model_DbTable_PreProjeto::getSchema('controledeacesso'))
            ->join(array('e' => 'tbvinculoproposta'), 'a.idpreprojeto = e.idpreprojeto', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->join(array('f' => 'tbvinculo'), 'e.idvinculo = f.idvinculo', array(), Proposta_Model_DbTable_PreProjeto::getSchema('agentes'))
            ->where('a.stestado = ?', $stEstado)
            ->where("NOT EXISTS($subSql)")
            ->where("a.mecanismo = '1'")
            ->where('e.sivinculoproposta = 2')
            ->where('f.idusuarioresponsavel = ?', $idResponsavel);

        if (!empty($idAgenteCombo)) {
            $sql->where('b.idagente = ?', $idAgenteCombo);
            $sql2->where('b.idagente = ?', $idAgenteCombo);
            $sql3->where('b.idagente = ?', $idAgenteCombo);
        }

        $sql = $db->select()->union(array($sql, $sql2, $sql3), Zend_Db_Select::SQL_UNION);

        $sqlFinal = $db->select()->from(array("p" => $sql));

        foreach ($where as $coluna => $valor) {
            $sqlFinal->where($coluna, $valor);
        }

        if (!empty($search['value'])) {
            $sqlFinal->where('p.idpreprojeto like ? OR p.nomeprojeto like ? OR  p.nomeproponente like ?', '%' . $search['value'] . '%');
        }

        $sqlFinal->order($order);

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sqlFinal->limitPage($start, $limit);
        }

        return $db->fetchAll($sqlFinal);
    }

    public function buscarIdentificacaoProposta($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pp' => $this->_name),
            array('*',
                new Zend_Db_Expr('CAST(pp.ResumoDoProjeto as TEXT) as ResumoDoProjeto'),
                new Zend_Db_Expr('CAST(pp.Objetivos as TEXT) as Objetivos'),
                new Zend_Db_Expr('CAST(pp.Justificativa as TEXT) as Justificativa'),
                new Zend_Db_Expr('CAST(pp.Acessibilidade as TEXT) as Acessibilidade'),
                new Zend_Db_Expr('CAST(pp.DemocratizacaoDeAcesso as TEXT) as DemocratizacaoDeAcesso'),
                new Zend_Db_Expr('CAST(pp.EtapaDeTrabalho as TEXT) as EtapaDeTrabalho'),
                new Zend_Db_Expr('CAST(pp.FichaTecnica as TEXT) as FichaTecnica'),
                new Zend_Db_Expr('CAST(pp.Sinopse as TEXT) as Sinopse'),
                new Zend_Db_Expr('CAST(pp.ImpactoAmbiental as TEXT) as ImpactoAmbiental'),
                new Zend_Db_Expr('CAST(pp.EspecificacaoTecnica as TEXT) as EspecificacaoTecnica'),
                new Zend_Db_Expr('CAST(pp.EstrategiadeExecucao as TEXT) as EstrategiadeExecucao')
            ),
            $this->_schema
        );

        $slct->joinInner(
            array('ag' => 'agentes'), 'ag.idAgente= pp.idAgente',
            array('ag.CNPJCPF'),
            $this->getSchema('agentes')
        );

        $slct->joinInner(
            array('n' => 'nomes'), 'n.idAgente= pp.idAgente',
            array('n.Descricao as NomeAgente'),
            $this->getSchema('agentes')
        );

        $slct->joinLeft(
            array('ver' => 'verificacao'), 'ver.idVerificacao = pp.stProposta',
            array('ver.Descricao as TipoExecucao'),
            $this->_schema
        );

        $slct->joinLeft(
            ['vTipicidade' => 'Verificacao'],
            'pp.tpTipicidade = vTipicidade.idVerificacao',
            ["vTipicidade.Descricao as DescricaoTipicidade"],
            $this->_schema
        );

        $slct->joinLeft(
            ['vTipologia' => 'Verificacao'],
            'pp.tpTipologia = vTipologia.idVerificacao',
            ["vTipologia.Descricao as DescricaoTipologia"],
            $this->_schema
        );

        $slct->joinLeft(
            array('pr' => 'Projetos'), 'pp.idPreProjeto = pr.idProjeto',
            array(
                '(pr.AnoProjeto + pr.Sequencial) as PRONAC',
                'pr.idPronac'
            ),
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order('pp.idPreProjeto');
        $slct->order('pp.NomeProjeto');

        return $this->fetchAll($slct);
    }

    public function enviarEmailProponente($idPreProjeto, $titulo, $mensagem)
    {
        $internetDbTable = new Agente_Model_DbTable_Internet();
        $objetoEmailsProponente = $internetDbTable->obterEmailProponentesPorPreProjeto($idPreProjeto);
        $tbHistoricoEmailDAO = new tbHistoricoEmail();
        if (count($objetoEmailsProponente) > 0) {
            $preprojeto = $this->findBy(['idPreProjeto' => $idPreProjeto]);
            $auth = Zend_Auth::getInstance();
            foreach ($objetoEmailsProponente as $emailProponente) {
                $email = trim(strtolower($emailProponente->Descricao));
                $mensagem = "<b>Proposta {$preprojeto['idPreProjeto']} - {$preprojeto['NomeProjeto']} :</b> <br /> {$mensagem}";
                $assunto = utf8_decode("Proposta {$preprojeto['idPreProjeto']} - {$titulo}");

                EmailDAO::enviarEmail($email, $assunto, $mensagem);

                $dados = array(
                    'idProjeto' => $idPreProjeto,
                    'idTextoemail' => new Zend_Db_Expr('NULL'),
                    'idAvaliacaoProposta' => new Zend_Db_Expr('NULL'),
                    'DtEmail' => new Zend_Db_Expr('getdate()'),
                    'stEstado' => $tbHistoricoEmailDAO::SITUACAO_ESTADO_ENVIADO,
                    'idUsuario' => $auth->getIdentity()->usu_codigo,
                );

                $tbHistoricoEmailDAO->inserir($dados);
            }
        }
    }
}
