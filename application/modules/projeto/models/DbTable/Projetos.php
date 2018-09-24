<?php

/**
 * @todo Mover todas os métodos e alterar todas as referências para da antiga classe para essa.
 */
class Projeto_Model_DbTable_Projetos extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'Projetos';
    protected $_primary = 'IdPRONAC';


    public function buscarProjeto($where = [])
    {
        $query = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                ['p' => $this->_name],
                [
                    'p.IdPRONAC as idPronac',
                    'p.NomeProjeto',
                    'p.CgcCPf',
                    'p.idProjeto as idPreProjeto',
                    'p.Situacao',
                    'p.Mecanismo as idMecanismo',
                    new Zend_Db_Expr('p.AnoProjeto + p.Sequencial as Pronac')
                ],
                $this->_schema
            );

        $query->joinLeft(
            ['a' => 'Agentes'],
            "p.CgcCpf = a.CNPJCPF",
            [
                'a.idAgente',
                new Zend_Db_Expr('sac.dbo.fnNome(a.idAgente) AS NomeProponente')
            ],
            $this->getSchema('agentes')
        );

        foreach ($where as $coluna => $valor) {
            $query->where($coluna, $valor);
        }
        return $this->fetchAll($query);
    }


    public function alterarOrgao($orgao, $idPronac)
    {
        $this->update(
            array(
                'Orgao' => $orgao
            ),
            array('IdPRONAC = ?' => $idPronac)
        );
    }

    public function obterValoresProjeto($idPronac)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            array(
                'projetos' => $this->_name
            ),
            array(
                "ValorProposta" => new Zend_Db_Expr("sac.dbo.fnValorSolicitado(projetos.AnoProjeto,projetos.Sequencial)"),
                "ValorSolicitado" => new Zend_Db_Expr("sac.dbo.fnValorSolicitado(projetos.AnoProjeto,projetos.Sequencial)"),
                "OutrasFontes" => new Zend_Db_Expr("sac.dbo.fnOutrasFontes(projetos.idPronac)"),
                "ValorAprovado" => new Zend_Db_Expr(
                    "case when projetos.Mecanismo ='2' or projetos.Mecanismo ='6'
                        then sac.dbo.fnValorAprovadoConvenio(projetos.AnoProjeto,projetos.Sequencial)
                     else
                        sac.dbo.fnValorAprovado(projetos.AnoProjeto,projetos.Sequencial)
                     end"
                ),
                "ValorProjeto" => new Zend_Db_Expr(
                    "case when projetos.Mecanismo ='2' or projetos.Mecanismo ='6'
                     then sac.dbo.fnValorAprovadoConvenio(projetos.AnoProjeto,projetos.Sequencial)
                     else sac.dbo.fnValorAprovado(projetos.AnoProjeto,projetos.Sequencial) + sac.dbo.fnOutrasFontes(projetos.idPronac)
                      end "
                ),
                "ValorCaptado" => new Zend_Db_Expr("sac.dbo.fnCustoProjeto (projetos.AnoProjeto,projetos.Sequencial)"),
            )
        );
        $objQuery->where('projetos.IdPRONAC = ?', $idPronac);

        return $this->_db->fetchRow($objQuery);
    }

    /**
     * @param $idPronac
     * @return array
     * @deprecated Utilizar a model fnVerificarProjetoAprovadoIN2017, metodo verificar
     */
    public function verificarIN2017($idPronac)
    {
        $retorno = 0;

        $projetoAprovado = $this->select();
        $projetoAprovado->setIntegrityCheck(false);
        $projetoAprovado->from(
            array('a' => $this->_name),
            'idPRONAC',
            $this->_schema
        );

        $projetoAprovado->joinInner(
            array('b' => 'preprojeto'),
            'b.idPreProjeto = a.idProjeto',
            array(),
            $this->_schema
        );

        $projetoAprovado->joinInner(
            array('c' => 'tbDocumentoAssinatura'),
            'a.IdPRONAC = c.IdPRONAC',
            array(),
            $this->_schema
        );

        $projetoAprovado->where("CONVERT(CHAR(10), (a.DtProtocolo),112) >= '20170410'");
        $projetoAprovado->where("CONVERT(CHAR(10), sac.dbo.fnDtEnvioAvaliacao(b.idPreProjeto),112) >= '20170410'");
        $projetoAprovado->where("a.idPronac = ?", $idPronac);

        $resultadoProjetoAprovado = $this->_db->fetchRow($projetoAprovado);

        $projetoTransformado = $this->select();
        $projetoTransformado->setIntegrityCheck(false);
        $projetoTransformado->from(
            array('a' => $this->_name),
            'idPRONAC',
            $this->_schema
        );

        $projetoTransformado->joinInner(
            array('b' => 'preprojeto'),
            'b.idPreProjeto = a.idProjeto',
            array(),
            $this->_schema
        );

        $projetoTransformado->where("CONVERT(CHAR(10), (a.DtProtocolo),112) >= '20170512'");
        $projetoTransformado->where("CONVERT(CHAR(10), sac.dbo.fnDtEnvioAvaliacao(b.idPreProjeto),112) >= '20170512'");
        $projetoTransformado->where("a.idPronac = ?", $idPronac);

        $resultadoProjetoTransformado = $this->_db->fetchRow($projetoTransformado);

        if (!empty($resultadoProjetoAprovado) || !empty($resultadoProjetoTransformado)) {
            $retorno = 1;
        }

        return $retorno;
    }

    /*
    * Verifica se existe uma proposta relacionada ao projeto
    * Projetos mais antigos eram impressos e não possuiam propostas
    *
    * Substitui a fnVerificarExistenciaDaProposta
    */
    public function obterIdPreProjetoDoProjeto($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()
            ->from(array('p' => $this->_name), array(), $this->_schema)
            ->join(array('pre' => 'preprojeto'), 'pre.idPreProjeto = p.idProjeto', array('pre.idPreProjeto'), $this->_schema)
            ->where('p.idPronac = ?', $idPronac);

        return $db->fetchOne($sql);
    }

    public function fnChecarLiberacaoDaAdequacaoDoProjeto($idPronac)
    {
        $exec = new Zend_Db_Expr("SELECT dbo.fnChecarLiberacaoDaAdequacaoDoProjeto({$idPronac})");

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchOne($exec);
    }

    public function spClonarProjeto($idPronac, $usuarioLogado)
    {
        $exec = new Zend_Db_Expr("EXEC SAC.dbo.spClonarProjeto {$idPronac}, {$usuarioLogado}");

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchRow($exec);
    }

    public function atualizarProjetoEnquadrado($projeto, $id_usuario_logado, $situacaoFinalProjeto = 'B02')
    {
        $orgaoDestino = null;
        $providenciaTomada = 'Projeto enquadrado ap&oacute;s avalia&ccedil;&atilde;o t&eacute;cnica.';
        if ($projeto['Situacao'] == 'B03') {
            $situacaoFinalProjeto = Projeto_Model_Situacao::PROJETO_ENQUADRADO_COM_RECURSO;
            $objOrgaos = new Orgaos();
            $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($projeto['Orgao']);
            $orgaoDestino = Orgaos::ORGAO_SAV_DAP;
            if ($dadosOrgaoSuperior['Codigo'] == Orgaos::ORGAO_SUPERIOR_SEFIC) {
                $orgaoDestino = Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
            }
            $providenciaTomada = 'Projeto enquadrado ap&oacute;s avalia&ccedil;&atilde;o t&eacute;cnica do recurso.';
        }

        $objPlanoDistribuicaoProduto = new PlanoDistribuicao();
        $objPlanoDistribuicaoProduto->atualizarAreaESegmento(
            $projeto['Area'],
            $projeto['Segmento'],
            $projeto['idProjeto']
        );

        $objProjeto = new Projetos();
        $arrayDadosProjeto = array(
            'Situacao' => $situacaoFinalProjeto,
            'DtSituacao' => $objProjeto->getExpressionDate(),
            'ProvidenciaTomada' => $providenciaTomada,
            'Area' => $projeto['Area'],
            'Segmento' => $projeto['Segmento'],
            'logon' => $id_usuario_logado
        );

        if ($orgaoDestino) {
            $arrayDadosProjeto['Orgao'] = $orgaoDestino;
        }

        $arrayWhere = array('IdPRONAC  = ?' => $projeto['IdPRONAC']);
        $objProjeto->update($arrayDadosProjeto, $arrayWhere);

        if ($projeto['Situacao'] == 'B03') {
            $tbRecurso = new tbRecurso();
            $tbRecurso->finalizarRecurso($projeto['IdPRONAC']);
        }

        $objVerificacao = new Verificacao();
        $verificacao = $objVerificacao->findBy(array(
            'idVerificacao = ?' => 620
        ));

        $tbTextoEmailDAO = new tbTextoEmail();
        $textoEmail = $tbTextoEmailDAO->findBy(array(
            'idTextoEmail = ?' => 23
        ));

        $objInternet = new Agente_Model_DbTable_Internet();
        $arrayEmails = $objInternet->obterEmailProponentesPorPreProjeto($projeto['idProjeto']);

        foreach ($arrayEmails as $email) {
            EmailDAO::enviarEmail($email->Descricao, $verificacao['Descricao'], $textoEmail['dsTexto']);
        }
    }

    public function obterProjetosPorProponente(
        $idResponsavel,
        $idProponente,
        $mecanismo,
        $where = [],
        $order = [],
        $start = 0,
        $limit = 20,
        $search = null,
        $total = false)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('0 as Ordem'),
                'IdPRONAC',
                'NomeProjeto',
                'CgcCpf',
                'Situacao',
                new Zend_Db_Expr('DtInicioExecucao as DtInicioDeExecucao'),
                new Zend_Db_Expr('DtFimExecucao as DtFinalDeExecucao'),
                'Mecanismo',
                'idProjeto',
                new Zend_Db_Expr('a.AnoProjeto + a.Sequencial as Pronac'),
            )
        );
        $a->joinInner(
            array('b' => 'Agentes'),
            "a.CgcCpf = b.CNPJCPF",
            array('idAgente', new Zend_Db_Expr('sac.dbo.fnNome(b.idAgente) AS NomeProponente')),
            $this->getSchema('agentes')
        );
        $a->joinInner(
            array('c' => 'SGCacesso'),
            "a.CgcCpf = c.Cpf",
            array(),
            'CONTROLEDEACESSO.dbo'
        );
        $a->joinInner(
            array('d' => 'Situacao'),
            "a.Situacao = d.Codigo",
            array('Descricao', new Zend_Db_Expr('0 AS idSolicitante')),
            'SAC.dbo'
        );
        $a->where('c.IdUsuario = ?', $idResponsavel);
        if (!empty($mecanismo)) {
            $a->where('a.Mecanismo = ?', $mecanismo);
        }
        if (!empty($idProponente)) {
            $a->where('b.idAgente = ?', $idProponente);
        }

        $b = $this->select();
        $b->setIntegrityCheck(false);
        $b->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('1 as Ordem'),
                'IdPRONAC',
                'NomeProjeto',
                'CgcCpf',
                'Situacao',
                new Zend_Db_Expr('DtInicioExecucao as DtInicioDeExecucao'),
                new Zend_Db_Expr('DtFimExecucao as DtFinalDeExecucao'),
                'Mecanismo',
                'idProjeto',
                new Zend_Db_Expr('a.AnoProjeto + a.Sequencial as Pronac'),
            )
        );
        $b->joinInner(
            array('b' => 'Agentes'),
            "a.CgcCpf = b.CNPJCPF",
            array('idAgente', new Zend_Db_Expr('sac.dbo.fnNome(b.idAgente) AS NomeProponente')),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('c' => 'tbProcuradorProjeto'),
            "a.IdPRONAC = c.idPronac",
            array(),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('d' => 'tbProcuracao'),
            "c.idProcuracao = d.idProcuracao",
            array(),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('f' => 'Agentes'),
            "d.idAgente = f.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('e' => 'SGCacesso'),
            "f.CNPJCPF = e.Cpf",
            array(),
            'CONTROLEDEACESSO.dbo'
        );
        $b->joinInner(
            array('g' => 'Situacao'),
            "a.Situacao = g.Codigo",
            array('Descricao', new Zend_Db_Expr('d.idSolicitante')),
            'SAC.dbo'
        );
        $b->where('c.siEstado = ?', 2);
        $b->where('e.IdUsuario = ?', $idResponsavel);
        if (!empty($mecanismo)) {
            $b->where('a.Mecanismo = ?', $mecanismo);
        }
        if (!empty($idProponente)) {
            $b->where('b.idAgente = ?', $idProponente);
        }

        $c = $this->select();
        $c->setIntegrityCheck(false);
        $c->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('2 as Ordem'),
                'IdPRONAC',
                'NomeProjeto',
                'CgcCpf',
                'Situacao',
                new Zend_Db_Expr('DtInicioExecucao as DtInicioDeExecucao'),
                new Zend_Db_Expr('DtFimExecucao as DtFinalDeExecucao'),
                'Mecanismo',
                'idProjeto',
                new Zend_Db_Expr('a.AnoProjeto + a.Sequencial as Pronac'),
            )
        );
        $c->joinInner(
            array('b' => 'Agentes'),
            "a.CgcCpf = b.CNPJCPF",
            array('idAgente', new Zend_Db_Expr('sac.dbo.fnNome(b.idAgente) AS NomeProponente')),
            $this->getSchema('agentes')
        );
        $c->joinInner(
            array('c' => 'Vinculacao'),
            "b.idAgente = c.idVinculoPrincipal",
            array(),
            $this->getSchema('agentes')
        );
        $c->joinInner(
            array('d' => 'Agentes'),
            "c.idAgente = d.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $c->joinInner(
            array('e' => 'SGCacesso'),
            "d.CNPJCPF = e.Cpf",
            array(),
            'CONTROLEDEACESSO.dbo'
        );
        $c->joinInner(
            array('f' => 'Situacao'),
            "a.Situacao = f.Codigo",
            array('Descricao', new Zend_Db_Expr('0 AS idSolicitante')),
            'SAC.dbo'
        );
        $c->where('e.IdUsuario = ?', $idResponsavel);
        if (!empty($mecanismo)) {
            $c->where('a.Mecanismo = ?', $mecanismo);
        }
        if (!empty($idProponente)) {
            $c->where('b.idAgente = ?', $idProponente);
        }

//        $slctUnion = $this->select()
//            ->union(array('(' . $a . ')', '(' . $b . ')', '(' . $c . ')'))
//            ->order('Ordem')
//            ->order('CgcCpf')
//            ->order('NomeProjeto')
//            ->order('idProjeto');
//        return $this->fetchAll($slctUnion);
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = $db->select()->union(array($a, $b, $c), Zend_Db_Select::SQL_UNION);

        if ($total) {
            $sqlFinal = $db->select()->from(array("p" => $sql), array('count(distinct IdPRONAC)'));
            return $db->fetchOne($sqlFinal);
        }

        $sqlFinal = $db->select()->from(array("p" => $sql));

        foreach ($where as $coluna => $valor) {
            $sqlFinal->where($coluna, $valor);
        }

        if (!empty($search['value']) && count($search['value']) > 5) {
            $sqlFinal->where('a.AnoProjeto + a.Sequencial like ?', '%' . $search['value']);
        }

        if (count($order) > 0 && !empty(trim($order[0]))) {
            $sqlFinal->order($order);
        }

        if (!is_null($start) && $limit) {
            $start = (int)$start;
            $limit = (int)$limit;
            $sqlFinal->limit($limit, $start);
        }

        return $db->fetchAll($sqlFinal);
    }

    public function obterProjetoIncentivoCompleto($idPronac, $where = [])
    {

        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            ['a' => $this->_name],
            [
                'a.idPronac',
                new Zend_Db_Expr('a.AnoProjeto+a.Sequencial as Pronac'),
                'a.NomeProjeto',
                'a.CgcCpf AS CgcCPf',
                new Zend_Db_Expr('sac.dbo.fnNomeDoProponente(a.idPronac) as Proponente'),
                'a.UfProjeto',
                'a.Mecanismo as idMecanismo',
                'a.DtSituacao',
                new Zend_Db_Expr("sac.dbo.fnFormataProcesso(a.idPronac) as Processo"),
                new Zend_Db_Expr("sac.dbo.fnInicioCaptacao(a.AnoProjeto, a.Sequencial) as DtInicioCaptacao"),
                new Zend_Db_Expr("sac.dbo.fnFimCaptacao(a.AnoProjeto, a.Sequencial) as DtFimCaptacao"),
                new Zend_Db_Expr("a.DtInicioExecucao as DtInicioExecucao"),
                new Zend_Db_Expr("a.DtFimExecucao as DtFimExecucao"),
                new Zend_Db_Expr("sac.dbo.fnTipoAprovacaoVigente(a.idPronac) as TipoPortariaVigente"),
                new Zend_Db_Expr("sac.dbo.fnNrPortariaVigente(a.idPronac) as NrPortariaVigente"),
                new Zend_Db_Expr("CONVERT(varchar(10), sac.dbo.fnDtPublicacaoPortariaVigente(a.idPronac), 103) as DtPublicacaoPortariaVigente"),
                new Zend_Db_Expr("CAST(a.ResumoProjeto AS TEXT) AS ResumoProjeto"),
                new Zend_Db_Expr("CAST(a.ProvidenciaTomada AS TEXT) AS ProvidenciaTomada"),
                new Zend_Db_Expr("tabelas.dbo.fnEstruturaOrgao(a.Orgao, 0) as LocalizacaoAtual"),
                new Zend_Db_Expr("sac.dbo.fnVlSolicitadoPropostaIncentivo(a.idPronac) AS vlSolicitadoOriginal"),
                new Zend_Db_Expr("sac.dbo.fnVlSolicitadoPropostaOutrasFontes(a.idPronac) AS vlOutrasFontesPropostaOriginal"),
                new Zend_Db_Expr("sac.dbo.fnVlSolicitadoProposta(a.idPronac) AS vlTotalPropostaOriginal"),
                new Zend_Db_Expr("sac.dbo.fnVlAutorizadoACaptarIncentivo(a.idPronac) AS vlAutorizado"),
                new Zend_Db_Expr("sac.dbo.fnVlAutorizadoACaptarOutrasFontes(a.idPronac) AS vlAutorizadoOutrasFontes"),
                new Zend_Db_Expr("sac.dbo.fnVlAutorizadoACaptarIncentivo(a.idPronac) - sac.dbo.fnVlAutorizadoACaptarOutrasFontes(a.idPronac) AS vlTotalAutorizado"),
                new Zend_Db_Expr("sac.dbo.fnVlAdequadoIncentivo(a.idPronac) AS vlAdequadoIncentivo"),
                new Zend_Db_Expr("sac.dbo.fnVlAdequadoOutrasFontes(a.idPronac) AS vlAdequadoOutrasFontes"),
                new Zend_Db_Expr("sac.dbo.fnVlTotalAdequado(a.idPronac) AS vlTotalAdequado"),
                new Zend_Db_Expr("CONVERT(DECIMAL(18,2),sac.dbo.fnVlHomologadoIncentivo(a.idPronac)) AS vlHomologadoIncentivo"),
                new Zend_Db_Expr("sac.dbo.fnVlHomologadoOutrasFontes(a.idPronac) AS vlHomologadoOutrasFontes"),
                new Zend_Db_Expr("CONVERT(DECIMAL(18,2),sac.dbo.fnVlTotalHomologado(a.idPronac)) AS vlTotalHomologado"),
                new Zend_Db_Expr("sac.dbo.fnVlReadequadoIncentivo(a.idPronac) AS vlReadequadoIncentivo"),
                new Zend_Db_Expr("sac.dbo.fnVlReadequadoOutrasFontes(a.idPronac) AS vlReadequadoOutrasFontes"),
                new Zend_Db_Expr("sac.dbo.fnVlTotalReadequado(a.idPronac) AS vlTotalReadequado"),
                new Zend_Db_Expr("sac.dbo.fnTotalCaptadoProjeto (a.AnoProjeto,a.Sequencial) as vlCaptado"),
                new Zend_Db_Expr("sac.dbo.fnVlTransferido(a.idPronac) as vlTransferido"),
                new Zend_Db_Expr("sac.dbo.fnVlRecebido(a.idPronac) as vlRecebido"),
                new Zend_Db_Expr("CONVERT(DECIMAL(18,2),
                    (sac.dbo.fnTotalAprovadoProjeto(a.AnoProjeto,a.Sequencial) 
                    - (sac.dbo.fnTotalCaptadoProjeto (a.AnoProjeto,a.Sequencial) 
                    + sac.dbo.fnVlRecebido(a.idPronac) 
                    - sac.dbo.fnVlTransferido(a.idPronac)))) as vlSaldoACaptar"
                ),
                new Zend_Db_Expr("CONVERT(DECIMAL(18,2),
                    ((sac.dbo.fnTotalCaptadoProjeto (a.AnoProjeto,a.Sequencial) 
                    + sac.dbo.fnVlRecebido(a.idPronac) 
                    - sac.dbo.fnVlTransferido(a.idPronac)) 
                    / sac.dbo.fnTotalAprovadoProjeto(a.AnoProjeto,a.Sequencial)) * 100) as PercentualCaptado"
                ),
                new Zend_Db_Expr("sac.dbo.fnVlComprovadoProjeto(a.idPronac) as vlComprovado"),
                new Zend_Db_Expr("CONVERT(DECIMAL(18,2),
                ((sac.dbo.fnTotalCaptadoProjeto (a.AnoProjeto,a.Sequencial) 
                    + sac.dbo.fnVlRecebido(a.idPronac) 
                    - sac.dbo.fnVlComprovadoProjeto(a.idPronac))) ) as vlAComprovar"
                ),
                new Zend_Db_Expr("
                    CASE
	                    WHEN sac.dbo.fnTotalCaptadoProjeto (a.AnoProjeto,a.Sequencial) > 0 
	                        THEN CONVERT(DECIMAL(18,2), (
	                        sac.dbo.fnVlComprovadoProjeto(a.idPronac) 
	                        / (sac.dbo.fnTotalCaptadoProjeto (a.AnoProjeto,a.Sequencial) 
	                        + sac.dbo.fnVlRecebido(a.idPronac) 
	                        - sac.dbo.fnVlTransferido(a.idPronac))) * 100)  
                        ELSE 0 
                    END as PercentualComprovado
                ")
            ], $this->_schema
        );

        $sql->joinLeft(
            array('b' => 'Enquadramento'),
            "a.idPronac = b.idPronac",
            [
                new Zend_Db_Expr(
                    "CASE 
                    WHEN b.Enquadramento = '1' THEN 'Artigo 26' 
                    WHEN b.Enquadramento = '2' THEN 'Artigo 18' 
                    ELSE 'N&atilde;o enquadrado' 
                END as Enquadramento"
                )
            ],
            $this->_schema
        );

        $sql->joinLeft(
            array('c' => 'PreProjeto'),
            "a.idProjeto = c.idPreProjeto",
            [
                'c.idPreProjeto',
                'c.idAgente',
                new Zend_Db_Expr("
                    CASE 
                        WHEN c.stDataFixa = 1 THEN 'Sim' 
                        ELSE 'N&atilde;o' 
                    END AS DataFixa"
                ),
                new Zend_Db_Expr("
                    CASE 
                        WHEN c.tpProrrogacao = 1 THEN 'Sim' 
                        ELSE 'N&atilde;o' 
                    END as ProrrogacaoAutomatica"
                )
            ],
            $this->_schema
        );

        $sql->joinInner(
            array('d' => 'Area'),
            "a.Area = d.Codigo",
            ['d.Descricao as Area'],
            $this->_schema
        );

        $sql->joinInner(
            array('e' => 'Segmento'),
            "a.Segmento = e.Codigo",
            ['e.Descricao AS Segmento'],
            $this->_schema
        );

        $sql->joinInner(
            array('f' => 'Mecanismo'),
            "a.Mecanismo = f.Codigo",
            ['f.Descricao as Mecanismo'],
            $this->_schema
        );

        $sql->joinInner(
            array('g' => 'Situacao'),
            "a.Situacao = g.Codigo",
            [
                new Zend_Db_Expr("a.Situacao + ' - ' + g.Descricao as Situacao")
            ],
            $this->_schema
        );

        $sql->joinLeft(
            array('h' => 'Verificacao'),
            "c.stProposta = h.idVerificacao",
            [
                new Zend_Db_Expr("ISNULL(h.Descricao, 'N&atilde;o Informado') as PlanoExecucaoImediata")
            ],
            $this->_schema
        );

        $sql->joinLeft(
            array('i' => 'ContaBancaria'),
            "a.AnoProjeto = i.AnoProjeto AND a.Sequencial = i.Sequencial",
            [
                'i.Agencia as AgenciaBancaria',
                'i.ContaBloqueada as ContaCaptacao',
                'i.ContaLivre as ContaMovimentacao'
            ],
            $this->_schema
        );

        $sql->joinLeft(
            array('j' => 'Liberacao'),
            "a.AnoProjeto = j.AnoProjeto AND a.Sequencial = j.Sequencial",
            [
                new Zend_Db_Expr("CASE WHEN j.DtLiberacao IS NOT NULL THEN 'Sim' ELSE 'N&atilde;o' END as ContaBancariaLiberada"),
                new Zend_Db_Expr("CONVERT(varchar(10), j.DtLiberacao, 103) as DtLiberacaoDaConta")
            ],
            $this->_schema
        );

        $sql->joinLeft(
            array('k' => 'tbArquivamento'),
            "a.IdPRONAC = k.idPronac AND k.stAcao = 0 AND k.stEstado = 1",
            [
                'k.data as DtArquivamento',
                'k.CaixaInicio',
                'k.CaixaFinal'
            ],
            $this->_schema
        );

        $sql->joinLeft(
            array('l' => 'tbProjetoFase'),
            "a.IdPRONAC = l.idPronac AND l.stEstado = 1",
            [
                'l.dtInicioFase',
                'l.dtFinalFase'
            ],
            $this->_schema
        );

        $sql->joinLeft(
            array('m' => 'tbNormativos'),
            "l.idNormativo = m.idNormativo",
            [
                'm.idNormativo',
                'm.nmNormativo AS Normativo',
                'm.dtPublicacao AS dtPublicacaoNormativo',
                'm.dtRevogacao AS dtRevogacaoNormativo',
            ],
            $this->_schema
        );

        $sql->joinLeft(
            array('n' => 'Verificacao'),
            "l.idFase = n.idVerificacao",
            [
                'l.idFase',
                'n.Descricao AS FaseProjeto'
            ],
            $this->_schema
        );

        $sql->where('a.idPronac = ?', $idPronac);

        return $this->fetchRow($sql);
    }

    public function obterProjetosESituacao($where)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from(
            ['projeto' => $this->_name],
            [
                new Zend_Db_Expr('projeto.AnoProjeto+projeto.Sequencial AS pronac'),
                'IdPRONAC as idPronac',
                'NomeProjeto as nomeProjeto',
                'Cgccpf as cgcCpf',
                'Situacao as situacao',
                'DtSituacao as dtSituacao',
                'diasSituacao' => new Zend_Db_Expr('DATEDIFF(DAY, DtSituacao, GETDATE())')
            ],
            $this->_schema
        );

        $query->joinInner(
            ['situacao' => 'Situacao'],
            'situacao.Codigo = projeto.Situacao',
            ['Descricao as descricaoSituacao'],
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            $query->where($coluna, $valor);
        }

        return $this->fetchAll($query);
    }
}
