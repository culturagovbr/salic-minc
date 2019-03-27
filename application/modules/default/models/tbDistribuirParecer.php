<?php

class tbDistribuirParecer extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "tbDistribuirParecer";
    protected $_primary = "idDistribuirParecer";

    public function BuscarQtdAreasProjetos($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('t' => $this->_name),
            array(new Zend_Db_Expr('COUNT(distinct r.Area) as QDTArea'))
        );
        $slct->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array(),
            'SAC.dbo'
        );

        $slct->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array(),
            'SAC.dbo'
        );

        $slct->where('p.IdPRONAC = ? ', $idPronac);


        return $this->fetchRow($slct);
    }

    public function BuscarQtdAvaliacoes($idPronac, $idProduto)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('t' => $this->_name),
            array(new Zend_Db_Expr('COUNT(*) as QtdAvaliacoes'))
        );

        $slct->where('t.IdPRONAC = ? ', $idPronac);
        $slct->where('t.idProduto = ? ', $idProduto);
        $slct->where(new Zend_Db_Expr('t.DtDevolucao is not null'), '');


        return $this->fetchRow($slct);
    }

    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()

    public function buscarHistoricoDeAnalise($idPronac, $codOrgao)
    {
        $sql = "SELECT distinct d.idPronac, tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) AS Unidade,
				d.DtEnvio, CONVERT(CHAR(10),DtEnvio,103) AS DtEnvioPT,
				d.Observacao, d.idUsuario,
				SAC.dbo.fnNomeUsuario(d.idUsuario) AS Remetente,
				d.idAgenteParecerista, age.CNPJCPF, nm.Descricao AS Parecerista
				 FROM sac.dbo.tbDistribuirParecer AS d
				 INNER JOIN sac.dbo.Produto AS p ON d.idProduto = p.Codigo
				 INNER JOIN AGENTES.dbo.agentes AS age ON age.idAgente = d.idAgenteParecerista
				 INNER JOIN AGENTES.dbo.Nomes AS nm ON nm.idAgente = d.idAgenteParecerista
				 INNER JOIN TABELAS.dbo.usuarios AS usu ON usu.usu_identificacao = age.CNPJCPF
				 WHERE (idPronac = '$idPronac') and d.idOrgao = $codOrgao and SAC.dbo.fnNomeUsuario(d.idUsuario) is not null";


        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Projetos: " . $e->getMessage();
        }

        return $db->fetchAll($sql);
    }// fecha m�todo buscarHistoricoDeAnalise()

    public function buscarHistorico($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('d' => $this->_name),
            array(
                "d.idPronac",
                "d.idProduto",
                "TipoAnalise" => new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Conteudo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' ELSE 'Custo Administrativo' END"),
                "d.idOrgao",
                new Zend_Db_Expr("tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) as Unidade"),
                "DtEnvio",
                "DtRetorno",
                "stPrincipal",
                "DtDistribuicao",
                new Zend_Db_Expr("CONVERT(CHAR(10),DtDistribuicao,103) AS DtDistribuicaoPT", "CONVERT(CHAR(10),DtEnvio,103) AS DtEnvioPT"),
                new Zend_Db_Expr("cast(d.Observacao as Text) as Observacao"),
                new Zend_Db_Expr("SAC.dbo.fnNomeUsuario(d.idUsuario) as nmUsuario"),
                "d.idAgenteParecerista",
                "d.stDiligenciado",
                "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = d.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = d.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = d.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = d.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
            )
        );

        $select->joinInner(
            array('p' => 'Produto'),
            'd.idProduto = p.Codigo',
            array('Descricao as dsProduto')
        );

        $select->joinInner(array("age" => "agentes"), "age.idAgente = d.idAgenteParecerista", array("age.CNPJCPF"), "AGENTES.dbo");

        $select->joinInner(array("nm" => "Nomes"), "nm.idAgente = d.idAgenteParecerista", array("nm.Descricao as nmParecerista"), "AGENTES.dbo");

        $select->joinInner(
            array("usu" => "usuarios"),
            'usu.usu_identificacao = age.CNPJCPF',
            array('usu.usu_codigo as idUsuario'),
            'TABELAS.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('idDistribuirParecer DESC');
        // 		xd($select->assemble());
        return $this->fetchAll($select);
    }// fecha m�todo buscarHistorico()

    public function buscarHistoricoCoordenador($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('d' => $this->_name),
            array(
                "d.idPronac",
                "d.idProduto",
                "TipoAnalise" => new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Cont�udo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' ELSE 'Custo Administrativo' END"),
                "d.idOrgao",
                new Zend_Db_Expr("tabelas.dbo.fnEstruturaOrgao(d.idOrgao,0) as Unidade"),
                "DtEnvio",
                "DtRetorno",
                "stPrincipal",
                "DtDistribuicao",
                "DtDevolucao",
                new Zend_Db_Expr("CONVERT(CHAR(10),DtEnvio,103) AS DtEnvioPT"),
                new Zend_Db_Expr("CAST(Observacao AS TEXT) AS Observacao"),
                new Zend_Db_Expr("SAC.dbo.fnNomeUsuario(d.idUsuario) as nmUsuario"),
                "d.idAgenteParecerista",
                "d.stDiligenciado",
                "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = d.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = d.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = d.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = d.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
            )
        );

        $select->joinInner(
            array('p' => 'Produto'),
            'd.idProduto = p.Codigo',
            array('Descricao as dsProduto')
        );

        $select->joinInner(array("age" => "agentes"), "age.idAgente = d.idAgenteParecerista", array("age.CNPJCPF"), "AGENTES.dbo");

        $select->joinInner(array("nm" => "Nomes"), "nm.idAgente = d.idAgenteParecerista", array("nm.Descricao as nmParecerista"), "AGENTES.dbo");

        $select->joinInner(
            array("usu" => "usuarios"),
            'usu.usu_identificacao = age.CNPJCPF',
            array('usu.usu_codigo as idUsuario'),
            'TABELAS.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('idDistribuirParecer DESC');

        return $this->fetchAll($select);
    }// fecha m�todo buscarHistorico()


    /**
     * M�todo que lista os projetos na tela inicial do UC 103 ( Gerenciar Parecerer )
     * @param Int
     * @return List
     */
    public function listarProjetos($org_codigo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => $this->_name),
            array(
                new Zend_Db_Expr("DATEDIFF(DAY, t.DtDistribuicao, GETDATE()) AS DIAS"),
                "t.idDistribuirParecer",
                "t.idOrgao",
                "t.idAgenteParecerista",
                "t.DtDistribuicao",
                "t.idProduto",
                "t.DtDevolucao",
                "t.TipoAnalise",
                "t.FecharAnalise",
                "t.DtEnvio",
                "t.stPrincipal",
                "tempoFimParecer" => new Zend_Db_Expr("CASE WHEN t.stPrincipal = 1 THEN 20 ELSE 10 END"),
                new Zend_Db_Expr("CONVERT(CHAR(10),t.DtDistribuicao,103) AS DtDistribuicaoPT"),
                new Zend_Db_Expr("CONVERT(CHAR(10),t.DtEnvio,103) AS DtEnvioPT"),
//                "agentes.dbo.fnNome(t.idAgenteParecerista) AS nomeParecerista",
                "DescricaoAnalise" => new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Cont�udo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' ELSE 'Custo Administrativo' END"),
                new Zend_Db_Expr("SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise) AS Obs")
            )
        );

        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array('p.IdPRONAC', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS NrProjeto'), 'p.NomeProjeto',
                "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = t.idProduto order by dili1.DtSolicitacao desc)'),
                "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = t.idProduto order by dili2.DtSolicitacao desc)'),
                "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = t.idProduto order by dili3.DtSolicitacao desc)'),
                "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = t.idProduto order by dili4.DtSolicitacao desc)")
            )
        );
        $select->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array('r.Descricao AS Produto')
        );

        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area')
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento')
        );

        $dadosWhere = array('t.stEstado = ?' => 0,
            new Zend_Db_Expr('t.FecharAnalise not in(1)') => '?',
            new Zend_Db_Expr('t.tipoanalise IN (3,1)') => '?', //solucao para mostrar projetos novo e projeto do legado
            new Zend_Db_Expr('t.tipoanalise = (SELECT TOP 1 max(tipoanalise) FROM SAC..tbDistribuirParecer WHERE idPRONAC = p.IdPRONAC and stEstado=0 and TipoAnalise in (1,3) )') => '?',  //solucao para mostrar projetos novo e projeto do legado
            "p.Situacao IN ('B11', 'B14')" => '',
            't.idOrgao = ?' => $org_codigo);

        foreach ($dadosWhere as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $select->order(array(new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial)'), 't.stPrincipal desc', 't.DtDevolucao', 't.DtEnvio', 'r.Descricao'));

        return $this->fetchAll($select);
    } // fecha m�todo listarProjetos()


    public function verificarProdutosSecundarios($idPronac, $codOrgao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => $this->_name),
            array("t.idDistribuirParecer")
        );

        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array('p.IdPRONAC', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS NrProjeto'), 'p.NomeProjeto')
        );
        $select->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array('r.Descricao AS Produto')
        );

        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area')
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento')
        );

        $dadosWhere = array('t.stEstado = ?' => 0,
            //'t.FecharAnalise = ?' 				=> 0,
            't.FecharAnalise IN (0,2)' => '',
            't.TipoAnalise = ?' => 3,
            'p.Situacao IN (\'B11\', \'B14\')' => '',
            't.DtDevolucao is null' => '',
            't.idAgenteParecerista is not null' => '',
            't.stPrincipal = ? ' => 0,
            //'t.idOrgao = ?'   				=> $codOrgao,
            'p.IdPRONAC = ? ' => $idPronac
        );


        foreach ($dadosWhere as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $select->order(array(new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial)'), 't.stPrincipal desc', 't.DtDevolucao', 't.DtEnvio', 'r.Descricao'));

        return $this->fetchAll($select);
    } // fecha m�todo listarProjetos()

    public function produtosDistribuidos($org_codigo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => $this->_name),
            array(
                "t.idDistribuirParecer",
                "t.idOrgao",
                "t.idAgenteParecerista",
                "t.idProduto",
                "t.DtDevolucao",
                "t.TipoAnalise",
                "t.DtEnvio",
                "t.stPrincipal",
                new Zend_Db_Expr("CONVERT(CHAR(10),t.DtDistribuicao,103) AS DtDistribuicaoPT"),
                new Zend_Db_Expr("CONVERT(CHAR(10),t.DtEnvio,103) AS DtEnvioPT"),
                "nrDias" => new Zend_Db_Expr("DATEDIFF(day, t.DtEnvio,t.DtDistribuicao)"),
                new Zend_Db_Expr("agentes.dbo.fnNome(t.idAgenteParecerista) AS nomeParecerista"),
                "DescricaoAnalise" => new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Conte&uacute;do' WHEN TipoAnalise = 1 THEN 'Custo do Produto' ELSE 'Custo Administrativo' END"),
                new Zend_Db_Expr("SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise) AS Obs"),

            )
        );

        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array('p.IdPRONAC', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS NrProjeto'), 'p.NomeProjeto')
        );
        $select->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array('r.Descricao AS Produto')
        );

        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area')
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento')
        );
        $select->joinleft(
            array('ac' => 'tbAnaliseDeConteudo'),
            'ac.IdPRONAC = t.IdPRONAC AND ac.idProduto = t.idProduto',
            array('ac.ParecerFavoravel AS Status')
        );


        $dadosWhere = array('t.stEstado = ?' => 0, 't.FecharAnalise IN (0,2)' => '', 't.TipoAnalise = ?' => 3, 'p.Situacao IN (\'B11\',\'B14\')' => '', 't.idOrgao = ?' => $org_codigo);
//        $dadosWhere = array('t.stEstado = ?' => 0, 't.FecharAnalise = ?' => 0, 't.TipoAnalise = ?' => 3,'p.Situacao IN (\'B11\',\'B14\')'=> '', 't.idOrgao = ?'=>$org_codigo);

        foreach ($dadosWhere as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $select->order(array('t.DtEnvio', 'r.Descricao', '(p.AnoProjeto + p.Sequencial)'));

        return $this->fetchAll($select);
    } // fecha m�todo listarProjetos()

    public function pagamentoParecerista($org_codigo, $perfil)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => $this->_name),
            array(
                "t.idDistribuirParecer",
                "t.idOrgao",
                "t.idAgenteParecerista",
                "t.idProduto",
                "t.DtDevolucao",
                "t.TipoAnalise",
                "t.DtEnvio",
                "t.stPrincipal",
                new Zend_Db_Expr("CONVERT(CHAR(10),t.DtDistribuicao,103) AS DtDistribuicaoPT"),
                new Zend_Db_Expr("CONVERT(CHAR(10),t.DtEnvio,103) AS DtEnvioPT"),
                "nrDias" => new Zend_Db_Expr("DATEDIFF(day, t.DtEnvio,t.DtDistribuicao)"),
                new Zend_Db_Expr("agentes.dbo.fnNome(t.idAgenteParecerista) AS nomeParecerista"),
                "DescricaoAnalise" => new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Conte&uacute;do' WHEN TipoAnalise = 1 THEN 'Custo do Produto' ELSE 'Custo Administrativo' END"),
                new Zend_Db_Expr("SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise) AS Obs"),

            )
        );

        $select->joinLeft(
            array('pp' => 'tbPagamentoParecerista'),
            'pp.idProduto = t.idDistribuirParecer',
            array('pp.idPagamentoParecerista', 'idComprovantePagamento'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array('p.IdPRONAC', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS NrProjeto'), 'p.NomeProjeto')
        );

        $select->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array('r.Descricao AS Produto')
        );

        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area')
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento')
        );
        $select->joinleft(
            array('ac' => 'tbAnaliseDeConteudo'),
            'ac.IdPRONAC = t.IdPRONAC AND ac.idProduto = t.idProduto',
            array('ac.ParecerFavoravel AS Status')
        );


        if ($perfil == 93) {
            $dadosWhere = array('t.stEstado = ?' => 0,
                't.FecharAnalise = ?' => 1,
                't.TipoAnalise = ?' => 3,
                'p.Situacao IN (\'B11\',\'B14\')' => '',
                't.idOrgao = ?' => $org_codigo,
                'pp.idProduto is null ' => ''


            );
        } else {
            $dadosWhere = array('t.stEstado = ?' => 0,
                't.FecharAnalise = ?' => 1,
                't.TipoAnalise = ?' => 3,
                'p.Situacao IN (\'B11\',\'B14\')' => '',
                'pp.idProduto is not null ' => '',
                'pp.siPagamento = ? ' => 0


            );
        }


        foreach ($dadosWhere as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        $select->order(array('t.DtEnvio', 'r.Descricao', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial)')));

        return $this->fetchAll($select);
    } // fecha m�todo listarProjetos()


    public function BuscarParaMemorando($dadosWhere)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => $this->_name),
            array(
                "t.idDistribuirParecer",
                "t.idOrgao",
                "t.idAgenteParecerista",
                "t.idProduto",
                "t.DtDevolucao",
                "t.TipoAnalise",
                "t.DtEnvio",
                "t.stPrincipal",
                new Zend_Db_Expr("CONVERT(CHAR(10),t.DtDistribuicao,103) AS DtDistribuicaoPT"),
                new Zend_Db_Expr("CONVERT(CHAR(10),t.DtEnvio,103) AS DtEnvioPT"),
                "nrDias" => new Zend_Db_Expr("DATEDIFF(day, t.DtEnvio,t.DtDistribuicao)"),
                new Zend_Db_Expr("agentes.dbo.fnNome(t.idAgenteParecerista) AS nomeParecerista"),
                "DescricaoAnalise" => new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Conte&uacute;do' WHEN TipoAnalise = 1 THEN 'Custo do Produto' ELSE 'Custo Administrativo' END"),
                new Zend_Db_Expr("SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise) AS Obs"),

            )
        );

        $select->joinLeft(
            array('pp' => 'tbPagamentoParecerista'),
            'pp.idProduto = t.idDistribuirParecer',
            array('pp.idPagamentoParecerista', 'idComprovantePagamento'),
            'AGENTES.dbo'
        );

        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array('p.IdPRONAC', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS NrProjeto'), 'p.NomeProjeto', 'p.Processo')
        );

        $select->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array('r.Descricao AS Produto')
        );

        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area')
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento')
        );
        $select->joinleft(
            array('ac' => 'tbAnaliseDeConteudo'),
            new Zend_Db_Expr('ac.IdPRONAC = t.IdPRONAC AND ac.idProduto = t.idProduto'),
            array('ac.ParecerFavoravel AS Status')
        );


        foreach ($dadosWhere as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order(array('t.DtEnvio', 'r.Descricao', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial)')));

        return $this->fetchAll($select);
    } // fecha m�todo()

    public function dadosParaDistribuir($dadosWhere)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => $this->_name),
            array(
                "t.idProduto",
                "t.idDistribuirParecer",
                "t.idOrgao",
                "t.DtDevolucao",
                "t.DtEnvio",
                "t.idAgenteParecerista",
                "t.stDiligenciado",
                "t.DtDistribuicao",
                "t.TipoAnalise",
                "t.FecharAnalise",
                "t.stPrincipal",
                "nrDias" => new Zend_Db_Expr("DATEDIFF(day, t.DtEnvio,t.DtDistribuicao)"),
                new Zend_Db_Expr("CONVERT(CHAR(10), DtEnvio, 103) AS DtEnvioPT"),
                new Zend_Db_Expr("CONVERT(CHAR(10), t.DtDevolucao, 103) AS DtDevolucaoPT"),
                new Zend_Db_Expr("CONVERT(CHAR(10), t.DtDistribuicao, 103) AS DtDistribuicaoPT"),
                "DescricaoAnalise" => new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Cont�udo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' ELSE 'Custo Administrativo' END"),
                'Obs' => new Zend_Db_Expr("SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise)"),
                'Valor' => new Zend_Db_Expr("(Select SUM(x.Ocorrencia*x.Quantidade*x.ValorUnitario) FROM SAC.dbo.tbPlanilhaProjeto x WHERE p.IdPRONAC = x.idPRONAC and x.FonteRecurso = 109 and x.idProduto = t.idProduto)"),
                new Zend_Db_Expr("(SELECT x1.Segmento FROM sac.dbo.PlanoDistribuicaoProduto x1
                     WHERE x1.idProjeto = p.idProjeto and x1.idProduto = t.idProduto)  AS
                    idSegmento, (SELECT x1.Segmento FROM sac.dbo.PlanoDistribuicaoProduto x1
                         INNER JOIN sac.dbo.Projetos y1 on (x1.idProjeto = y1.idProjeto)
                         WHERE x1.idProjeto = p.idProjeto and x1.idProduto = t.idProduto)  AS idSegmento"),
                new Zend_Db_Expr("(SELECT z1.Descricao FROM sac.dbo.PlanoDistribuicaoProduto x1
                                 INNER JOIN sac.dbo.Segmento z1 on (x1.Segmento  = z1.Codigo)
                                        WHERE x1.idProjeto = p.idProjeto and x1.idProduto = t.idProduto)  AS Segmento")
            )
        );


        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array('p.IdPRONAC', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS NrProjeto'), 'p.NomeProjeto', 'p.Area as idArea')
        );

        $select->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array('r.Descricao AS Produto')
        );

        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area')
        );

        foreach ($dadosWhere as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->distinct('t.idDistribuirParecer');

        $select->order('t.stPrincipal desc');

        return $this->fetchAll($select);
    } // fecha m�todo dadosParaDistribuir()

    public function dadosParaDistribuirSecundarios($dadosWhere)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => $this->_name),
            array(
                "t.idProduto",
                "t.idDistribuirParecer",
                "t.idOrgao",
                "t.DtDevolucao",
                "t.TipoAnalise",
                "t.stPrincipal",
                "nrDias" => new Zend_Db_Expr("DATEDIFF(day, t.DtEnvio,t.DtDistribuicao)"),
                new Zend_Db_Expr("CONVERT(CHAR(10), DtEnvio, 103) AS DtEnvioPT"),
                new Zend_Db_Expr("CONVERT(CHAR(10), t.DtDevolucao, 103) AS DtDevolucaoPT"),
                new Zend_Db_Expr("CONVERT(CHAR(10), t.DtDistribuicao, 103) AS DtDistribuicaoPT"),
                "DescricaoAnalise" => new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Conte&uacute;do' WHEN TipoAnalise = 1 THEN 'Custo do Produto' ELSE 'Custo Administrativo' END"),
                new Zend_Db_Expr("SAC.dbo.fnChecarDistribuicaoProjeto(p.IdPRONAC, t.idProduto, t.TipoAnalise) AS Obs")
            )
        );

        /*$select->joinInner(
        array('ac' => 'tbAnaliseDeConteudo'),'t.idPRONAC = ac.idPRONAC',
        array('ac.ParecerFavoravel', 'ac.ParecerDeConteudo as dsParecer')
        );*/

        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array('p.IdPRONAC', new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS NrProjeto'), 'p.NomeProjeto')
        );

        $select->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array('r.Descricao AS Produto')
        );

        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area')
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento')
        );


        foreach ($dadosWhere as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->distinct('t.idDistribuirParecer');

        $select->order('t.stPrincipal desc');

        return $this->fetchAll($select);
    } // fecha m�todo dadosParaDistribuir()


    public function distribuirParecer($dados)
    {

        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tbDistribuirParecer = new tbDistribuirParecer();

        //BUSCA O REGISTRO QUE VAI SER ALTERADO
        $rstbDistribuirParecer = $tbDistribuirParecer->find($dados['idDistribuirParecer'])->current();


        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS

        $rstbDistribuirParecer->FecharAnalise = 0;
        $rstbDistribuirParecer->Observacao = $dados['Observacao'];
        $rstbDistribuirParecer->idUsuario = $dados['idUsuario'];
        $rstbDistribuirParecer->idAgenteParecerista = $dados['idAgenteParecerista'];


        //SALVANDO O OBJETO
        $id = $rstbDistribuirParecer->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    } // fecha m�todo distribuirParecer()

    public function encaminharParecer($dados)
    {

        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tbDistribuirParecer = new tbDistribuirParecer();

        //BUSCA O REGISTRO QUE VAI SER ALTERADO
        $rstbDistribuirParecer = $tbDistribuirParecer->find($dados['idDistribuirParecer'])->current();


        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS

        $rstbDistribuirParecer->FecharAnalise = 0;
        $rstbDistribuirParecer->Observacao = $dados['Observacao'];
        $rstbDistribuirParecer->idUsuario = $dados['idUsuario'];
        $rstbDistribuirParecer->idOrgao = $dados['idOrgao'];


        //SALVANDO O OBJETO
        $id = $rstbDistribuirParecer->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    } // fecha m�todo encaminharParecer()

    public function concluirParecer($dados)
    {

        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA
        $tbDistribuirParecer = new tbDistribuirParecer();

        //BUSCA O REGISTRO QUE VAI SER ALTERADO
        $rstbDistribuirParecer = $tbDistribuirParecer->find($dados['idDistribuirParecer'])->current();


        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS

        $rstbDistribuirParecer->FecharAnalise = $dados['FecharAnalise'];
        $rstbDistribuirParecer->Observacao = $dados['Observacao'];
        $rstbDistribuirParecer->idUsuario = $dados['idUsuario'];


        //SALVANDO O OBJETO
        $id = $rstbDistribuirParecer->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    } // fecha m�todo concluirParecer()

    public function atualizarParecer($data, $idDistribuirParecer)
    {
        $where = "idDistribuirParecer = " . $idDistribuirParecer;

        try {
            $this->update($data, $where);
            return true;
        } catch (Zend_Db_Exception $e) {
            return false;
        }
    }

    public function excluirDados($where)
    {
        $where = "idDistribuirParecer = " . $where;
        return $this->delete($where);
    } // fecha m�todo excluirDados()

    public function aguardandoparecerresumo($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array("dp" => $this->_name),
            array()
        );

        $select->joinInner(
            array("ag" => "Agentes"),
            "ag.idAgente = dp.idAgenteParecerista",
            array('ag.idAgente'),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = dp.idAgenteParecerista",
            array('nmParecerista' => 'nm.Descricao'),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("usu" => "Usuarios"),
            "ag.CNPJCPF = usu.usu_identificacao",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            "usu.usu_codigo = uog.uog_usuario and uog.uog_status = 1",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = uog.uog_orgao",
            array('idOrgao' => 'org.Codigo', 'nmOrgao' => 'org.Sigla'),
            'SAC.dbo'
        );
        $select->joinInner(
            array("proj" => "Projetos"),
            "proj.IdPRONAC = dp.idPRONAC",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array("prod" => "Produto"),
            "prod.Codigo = dp.idProduto",
            array(),
            'SAC.dbo'
        );
        $select->where('uog.uog_grupo = 94');
        $select->where('DtDevolucao is null');
        $select->group(
            array('org.Sigla',
                'nm.Descricao',
                'proj.AnoProjeto',
                'proj.Sequencial',
                'proj.NomeProjeto',
                'prod.Descricao',
                'dp.idProduto',
                'ag.idAgente',
                'org.Codigo',
                'proj.IdPRONAC')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from(
            array('qpp' => $select),
            array('qpp.idOrgao',
                'qpp.nmOrgao',
                'qpp.idAgente',
                'qpp.nmParecerista',
                'qt' => new Zend_Db_Expr('count(qpp.idOrgao)')
            )
        );
        $selectAux->order(array('qpp.nmOrgao ASC', 'qpp.nmParecerista ASC'));
        $selectAux->group(array(
            'qpp.idOrgao',
            'qpp.nmOrgao',
            'qpp.idAgente',
            'qpp.nmParecerista'
        ));

        return $this->fetchAll($selectAux);
    }

    public function aguardandoparecer($where, $tamanho = -1, $inicio = -1)
    {
        $tmpInicio = null;
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            //$select->limit($tamanho, $tmpInicio);
        }
        $select = $this->select();
        $select->setIntegrityCheck(false);
        if ($inicio < 0) {
            $select->from(
                array("dp" => $this->_name),
                array("dp.idProduto", "dp.DtDistribuicao", "dp.DtEnvio", 'nrDias' => new Zend_Db_Expr('DATEDIFF(day, dp.DtEnvio,dp.DtDistribuicao)'))
            );
        } else {
            $soma = $tamanho + $tmpInicio;
            $select->from(
                array("dp" => $this->_name),
                array(new Zend_Db_Expr("TOP $soma  dp.idProduto"), "dp.DtDistribuicao", "dp.DtEnvio", 'nrDias' => new Zend_Db_Expr('DATEDIFF(day, dp.DtEnvio,dp.DtDistribuicao)'))
            );
        }
        $select->joinInner(
            array("ag" => "Agentes"),
            "ag.idAgente = dp.idAgenteParecerista",
            array('ag.idAgente'),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = dp.idAgenteParecerista",
            array('nmParecerista' => 'nm.Descricao'),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("usu" => "Usuarios"),
            "ag.CNPJCPF = usu.usu_identificacao",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            "usu.usu_codigo = uog.uog_usuario and uog.uog_status = 1",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = uog.uog_orgao",
            array('idOrgao' => 'org.Codigo', 'nmOrgao' => 'org.Sigla'),
            'SAC.dbo'
        );
        $select->joinInner(
            array("proj" => "Projetos"),
            "proj.IdPRONAC = dp.idPRONAC",
            array('proj.IdPRONAC', 'pronac' => new Zend_Db_Expr('proj.AnoProjeto+proj.Sequencial'), 'proj.NomeProjeto'),
            'SAC.dbo'
        );
        $select->joinInner(
            array("prod" => "Produto"),
            "prod.Codigo = dp.idProduto",
            array('nmProduto' => 'prod.Descricao'),
            'SAC.dbo'
        );
        $select->where('uog.uog_grupo = 94');
        $select->where('DtDevolucao is null');
        $select->order(
            array('org.Sigla',
                'nm.Descricao',
                'proj.AnoProjeto',
                'proj.Sequencial',
                'proj.NomeProjeto',
                'prod.Descricao',
                'dp.DtDistribuicao',
                'dp.DtEnvio')
        );
        /*$select->group(array('org.Sigla',
                        'nm.Descricao',
                        'proj.AnoProjeto',
                        'proj.Sequencial',
                        'proj.NomeProjeto',
                        'prod.Descricao',
                        'dp.DtDistribuicao',
                        'dp.DtEnvio',
                        'dp.idProduto',
                        'ag.idAgente',
                        'org.Codigo',
                        'proj.IdPRONAC')
                        );*/

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from(
            $select,
            array(new Zend_Db_Expr("TOP $tamanho  *"))

        );
        $selectAux->order(
            array('nmOrgao desc',
                'nmParecerista desc',
                'pronac desc',
                'NomeProjeto desc',
                'nmProduto desc',
                'DtDistribuicao desc',
                'DtEnvio desc')
        );

        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order(
            array('nmOrgao',
                'nmParecerista',
                'pronac',
                'NomeProjeto',
                'nmProduto',
                'DtDistribuicao',
                'DtEnvio')
        );

        // paginacao
        if ($tmpInicio <= 0 || $tmpInicio == null) {
            return $this->fetchAll($select);
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    public function aguardandoparecerTotal($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("dp" => $this->_name),
            array("total" => new Zend_Db_Expr("count(*)"))

        );
        $select->joinInner(
            array("ag" => "Agentes"),
            "ag.idAgente = dp.idAgenteParecerista",
            array(),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = dp.idAgenteParecerista",
            array(),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("usu" => "Usuarios"),
            "ag.CNPJCPF = usu.usu_identificacao",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            "usu.usu_codigo = uog.uog_usuario",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = uog.uog_orgao",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array("proj" => "Projetos"),
            "proj.IdPRONAC = dp.idPRONAC",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array("prod" => "Produto"),
            "prod.Codigo = dp.idProduto",
            array(),
            'SAC.dbo'
        );
        $select->where('uog.uog_grupo = 94');
        $select->where('DtDevolucao is null');
        /*$select->group(array(
                        'dp.DtDistribuicao',
                        'dp.DtEnvio',
                        'dp.idProduto',
                        'ag.idAgente',
                        'org.Codigo',
                        'proj.IdPRONAC')
                        );*/
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        /*$selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from(
            $select
            ,array("total"=>new Zend_Db_Expr("count(*)"))

        );
                xd($select->assemble());*/
        return $this->fetchAll($select);
    }

    public function pareceremitido($pronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("t" => $this->_name),
            array("t.stPrincipal", "t.idDistribuirParecer", "t.idOrgao", "t.idProduto", "t.DtDevolucao", "t.TipoAnalise", new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Conteudo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' WHEN TipoAnalise = 2 THEN 'Custo Administrativo' END AS DescricaoAnalise"))

        );
        $select->joinInner(
            array("p" => "Projetos"),
            "t.idPRONAC = p.IdPRONAC",
            array(
                'p.IdPRONAC',
                'NrProjeto' => new Zend_Db_Expr('p.AnoProjeto + p.Sequencial'),
                'p.NomeProjeto'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array("r" => "Produto"),
            "t.idProduto = r.Codigo",
            array('Produto' => 'r.Descricao'),
            'SAC.dbo'
        );
        $select->joinInner(
            array("a" => "Area"),
            "p.Area = a.Codigo",
            array('Area' => 'a.Descricao'),
            'SAC.dbo'
        );
        $select->joinInner(
            array("s" => "Segmento"),
            "p.Segmento = s.Codigo",
            array('Segmento' => 's.Descricao'),
            'SAC.dbo'
        );

        $select->joinInner(
            array("o" => "Orgaos"),
            "t.idOrgao = o.Codigo",
            array('o.Sigla'),
            'SAC.dbo'
        );

        $select->where('t.stEstado = 0');
        $select->where('t.FecharAnalise = 1');
        $select->where('t.TipoAnalise <> 2');
        $select->where(new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) = ?'), $pronac);


        return $this->fetchAll($select);
    }

    public function parecerconsolidado($pronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("t" => $this->_name),
            array("t.stPrincipal", "t.idDistribuirParecer", "t.idOrgao", "t.idProduto", "t.DtDevolucao", "t.TipoAnalise", new Zend_Db_Expr("CASE WHEN TipoAnalise = 0 THEN 'Conteudo' WHEN TipoAnalise = 1 THEN 'Custo do Produto' WHEN TipoAnalise = 2 THEN 'Custo Administrativo' END AS DescricaoAnalise"))

        );
        $select->joinInner(
            array("p" => "Projetos"),
            "t.idPRONAC = p.IdPRONAC",
            array(
                'p.IdPRONAC',
                'NrProjeto' => new Zend_Db_Expr('p.AnoProjeto + p.Sequencial'),
                'p.NomeProjeto'
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array("r" => "Produto"),
            "t.idProduto = r.Codigo",
            array('Produto' => 'r.Descricao'),
            'SAC.dbo'
        );
        $select->joinInner(
            array("a" => "Area"),
            "p.Area = a.Codigo",
            array('Area' => 'a.Descricao'),
            'SAC.dbo'
        );
        $select->joinInner(
            array("s" => "Segmento"),
            "p.Segmento = s.Codigo",
            array('Segmento' => 's.Descricao'),
            'SAC.dbo'
        );

        $select->joinInner(
            array("o" => "Orgaos"),
            "t.idOrgao = o.Codigo",
            array('o.Sigla'),
            'SAC.dbo'
        );

        $select->where('t.stEstado = 0');
        $select->where('t.FecharAnalise = 1');
        $select->where('t.TipoAnalise <> 2');
        $select->where(new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) = ?'), $pronac);


        return $this->fetchAll($select);
    }

    public function analisePorParecerista($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("dp" => $this->_name),
            array(
                "dp.idDistribuirParecer",
                "dp.DtDistribuicao",
                "dp.DtEnvio",
                "nrDias" => new Zend_Db_Expr("DATEDIFF(day, dp.DtEnvio,dp.DtDistribuicao)"),
                "tempoFimDiligencia" => new Zend_Db_Expr("(
                                select
                                        top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )"),
                "DtSolicitacao" => new Zend_Db_Expr("(
                                select
                                        top 1 DtSolicitacao
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )"),
                "DtResposta" => new Zend_Db_Expr("(
                                select
                                        top 1 DtResposta
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )")
            )
        );
        $select->joinInner(
            array("ag" => "Agentes"),
            "ag.idAgente = dp.idAgenteParecerista",
            array(
                "ag.idAgente"
            ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = dp.idAgenteParecerista",
            array(
                "nmParecerista" => "nm.Descricao"
            ),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array("cp" => "tbCredenciamentoParecerista"),
            "cp.idAgente = dp.idAgenteParecerista",
            array(
                "nmParecerista" => "nm.Descricao",
                "qtPonto"
            ),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array("ar" => "Area"),
            "ar.Codigo = cp.idCodigoArea",
            array(
                'Area' => 'ar.Descricao'

            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array("seg" => "Segmento"),
            "seg.Codigo = cp.idCodigoSegmento",
            array(
                'Segmento' => 'seg.Descricao'
            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array("au" => "tbAusencia"),
            new Zend_Db_Expr("au.idAgente = dp.idAgenteParecerista and au.idTipoAusencia = 2 and au.dtFimAusencia >= GETDATE()"),
            array(
                'au.dtFimAusencia',
                'au.dtInicioAusencia',
            ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("usu" => "Usuarios"),
            "ag.CNPJCPF = usu.usu_identificacao",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            "usu.usu_codigo = uog.uog_usuario and uog.uog_status = 1",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = uog.uog_orgao",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array("proj" => "Projetos"),
            "proj.IdPRONAC = dp.idPRONAC",
            array(
                "proj.IdPRONAC",
                "pronac" => new Zend_Db_Expr("proj.AnoProjeto+proj.Sequencial"),
                "proj.NomeProjeto"

            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array("prod" => "Produto"),
            "prod.Codigo = dp.idProduto",
            array("nmProduto" => "prod.Descricao", 'idProduto' => 'prod.Codigo',),
            'SAC.dbo'
        );

        $select->where('uog.uog_grupo = 94');
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchAll($select);
    }

    public function buscarPareceristaCoordParecer($idpronac)
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('dp' => $this->_name),
            array()
        );
        $slct->joinInner(
            array('ag' => 'Agentes'),
            'ag.idAgente = dp.idAgenteParecerista',
            array(),
            'AGENTES.dbo'
        );
        $slct->joinInner(
            array('nm' => 'Nomes'),
            'nm.idAgente = dp.idAgenteParecerista',
            array('nm.idAgente', 'Nome' => 'nm.Descricao'),
            'AGENTES.dbo'
        );
        $slct->joinInner(
            array('usu' => 'Usuarios'),
            'ag.CNPJCPF = usu.usu_identificacao',
            array(),
            'TABELAS.dbo'
        );
        $slct->joinInner(
            array('uog' => 'UsuariosXOrgaosXGrupos'),
            'uog.uog_usuario = usu.usu_codigo',
            array(),
            'TABELAS.dbo'
        );
        $slct->joinInner(
            array('gru' => 'Grupos'),
            'gru.gru_codigo = uog.uog_grupo',
            array('cdPerfil' => 'gru.gru_codigo', 'Perfil' => 'gru.gru_nome'),
            'TABELAS.dbo'
        );
        $slct->joinInner(
            array('org' => 'Orgaos'),
            //'org.Codigo = uog.uog_orgao',
            'org.Codigo = uog.uog_orgao and org.Codigo = dp.idOrgao',
            array('Orgao' => 'org.Sigla'),
            'SAC.dbo'
        );
        $slct->joinInner(
            array('usu2' => 'Usuarios'),
            'usu2.usu_codigo = dp.idUsuario',
            array(),
            'TABELAS.dbo'
        );

        $slct->where('dp.idPRONAC = ?', $idpronac);
        $slct->where('gru.gru_codigo = 93 or gru.gru_codigo = 94');

        return $this->fetchAll($slct);
    }

    public function analisePorPareceristaPagamento($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(

            array("dp" => $this->_name),
            array(
                "dp.idDistribuirParecer",
                "dp.DtDistribuicao",
                "dp.DtEnvio",
                "dp.stPrincipal",
                "dp.FecharAnalise",
                "nrDias" => new Zend_Db_Expr("DATEDIFF(day, dp.DtEnvio,dp.DtDistribuicao)"),
                "tempoFimDiligencia" => new Zend_Db_Expr("(
                                select
                                        top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )"),
                "DtSolicitacao" => new Zend_Db_Expr("(
                                select
                                        top 1 DtSolicitacao
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )"),
                "DtResposta" => new Zend_Db_Expr("(
                                select
                                        top 1 DtResposta
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )")
            )
        );
        $select->joinInner(
            array("ag" => "Agentes"),
            "ag.idAgente = dp.idAgenteParecerista",
            array(
                "ag.idAgente"
            ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = dp.idAgenteParecerista",
            array(
                "nmParecerista" => "nm.Descricao"
            ),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array("cp" => "tbCredenciamentoParecerista"),
            "cp.idAgente = dp.idAgenteParecerista",
            array(
                "nmParecerista" => "nm.Descricao",
                "qtPonto"
            ),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array("ar" => "Area"),
            "ar.Codigo = cp.idCodigoArea",
            array(
                'Area' => 'ar.Descricao'

            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array("seg" => "Segmento"),
            "seg.Codigo = cp.idCodigoSegmento",
            array(
                'Segmento' => 'seg.Descricao'
            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array("au" => "tbAusencia"),
            new Zend_Db_Expr("au.idAgente = dp.idAgenteParecerista and au.idTipoAusencia = 2 and au.dtFimAusencia >= GETDATE()"),
            array(
                'au.dtFimAusencia',
                'au.dtInicioAusencia',
            ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("usu" => "Usuarios"),
            "ag.CNPJCPF = usu.usu_identificacao",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            "usu.usu_codigo = uog.uog_usuario and uog.uog_status = 1",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = uog.uog_orgao",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array("proj" => "Projetos"),
            "proj.IdPRONAC = dp.idPRONAC",
            array(
                "proj.IdPRONAC",
                "pronac" => new Zend_Db_Expr("proj.AnoProjeto+proj.Sequencial"),
                "proj.NomeProjeto"

            ),
            'SAC.dbo'
        );

        $select->joinInner(
            array("prod" => "Produto"),
            "prod.Codigo = dp.idProduto",
            array("nmProduto" => "prod.Descricao", 'idProduto' => 'prod.Codigo',),
            'SAC.dbo'

        );
        $select->where('uog.uog_grupo = 94');
        // $select->where('dp.FecharAnalise = 1');

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    public function analiseParecerista($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(

            array("dp" => $this->_name),
            array(
                "dp.idDistribuirParecer",
                "dp.DtDistribuicao",
                "dp.DtEnvio",
                "dp.stPrincipal",
                "dp.FecharAnalise",
                "nrDias" => new Zend_Db_Expr("DATEDIFF(day, dp.DtEnvio,dp.DtDistribuicao)"),
                "tempoFimDiligencia" => new Zend_Db_Expr("(
                                select
                                        top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )"),
                "DtSolicitacao" => new Zend_Db_Expr("(
                                select
                                        top 1 DtSolicitacao
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )"),
                "DtResposta" => new Zend_Db_Expr("(
                                select
                                        top 1 DtResposta
                                from SAC.dbo.tbDiligencia  d
                                where d.idPronac = proj.IdPRONAC and d.idProduto = prod.Codigo
                                order by DtSolicitacao desc
                         )")
            )
        );
        $select->joinInner(
            array("ag" => "Agentes"),
            "ag.idAgente = dp.idAgenteParecerista",
            array(
                "ag.idAgente"
            ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = dp.idAgenteParecerista",
            array(
                "nmParecerista" => "nm.Descricao"
            ),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array("cp" => "tbCredenciamentoParecerista"),
            "cp.idAgente = dp.idAgenteParecerista",
            array(
                "nmParecerista" => "nm.Descricao",
                "qtPonto"
            ),
            'AGENTES.dbo'
        );
        $select->joinLeft(
            array("ar" => "Area"),
            "ar.Codigo = cp.idCodigoArea",
            array(
                'Area' => 'ar.Descricao'

            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array("seg" => "Segmento"),
            "seg.Codigo = cp.idCodigoSegmento",
            array(
                'Segmento' => 'seg.Descricao'
            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array("au" => "tbAusencia"),
            new Zend_Db_Expr("au.idAgente = dp.idAgenteParecerista and au.idTipoAusencia = 2 and au.dtFimAusencia >= GETDATE()"),
            array(
                'au.dtFimAusencia',
                'au.dtInicioAusencia',
            ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array("usu" => "Usuarios"),
            "ag.CNPJCPF = usu.usu_identificacao",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("uog" => "UsuariosXOrgaosXGrupos"),
            "usu.usu_codigo = uog.uog_usuario and uog.uog_status = 1",
            array(),
            'TABELAS.dbo'
        );
        $select->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = uog.uog_orgao",
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array("proj" => "Projetos"),
            "proj.IdPRONAC = dp.idPRONAC",
            array(
                "proj.IdPRONAC",
                "pronac" => new Zend_Db_Expr("proj.AnoProjeto+proj.Sequencial"),
                "proj.NomeProjeto"

            ),
            'SAC.dbo'
        );

        $select->joinInner(
            array("prod" => "Produto"),
            "prod.Codigo = dp.idProduto",
            array("nmProduto" => "prod.Descricao", 'idProduto' => 'prod.Codigo',),
            'SAC.dbo'

        );
        $select->where('uog.uog_grupo = 94');
        // $select->where('dp.FecharAnalise = 1');

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    /*===========================================================================*/
    /*====================== ABAIXO - METODOS DA CNIC ===========================*/
    /*===========================================================================*/

    public function buscarProdutos($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
            array('t' => $this->_name),
            array('idDistribuirParecer',
                'idOrgao',
                'idProduto',
                'stPrincipal',
                'TipoAnalise',
                'Orgao' => new Zend_Db_Expr('TABELAS.dbo.fnEstruturaOrgao(t.idOrgao,0)'),
            )
        );
        $slct->joinInner(
            array('p' => 'Projetos'),
            't.idPRONAC = p.IdPRONAC',
            array("PRONAC" => new Zend_Db_Expr("p.AnoProjeto+ p.Sequencial"),
                "IdPRONAC",
                "NomeProjeto"),
            'SAC.dbo'
        );
        $slct->joinInner(
            array('r' => 'Produto'),
            't.idProduto = r.Codigo',
            array("Produto" => "r.Descricao"),
            'SAC.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        //paginacao
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
     * M�todo que lista os projetos na tela inicial do UC 103 ( Gerenciar Parecerer )
     * @param Int
     * @return List
     */
    public function painelAnaliseTecnica($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false, $tipoFiltro = '')
    {
        $from = "";
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        switch ($tipoFiltro) {
            case 'aguardando_distribuicao':

                $slct->from(
                    array('dbo.vwPainelCoordenadorVinculadasAguardandoAnalise'),
                    array('IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'Produto',
                        'stPrincipal',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'idDistribuirParecer',
                        'idOrgao',
                        'FecharAnalise',
                        'DtEnvioMincVinculada',
                        'qtDiasDistribuir',
                        'Valor')
                );
                $from = ' FROM sac.dbo.vwPainelCoordenadorVinculadasAguardandoAnalise';
                break;

            case 'em_analise':

                $slct->from(
                    array('dbo.vwPainelCoordenadorVinculadasEmAnalise'),
                    array('IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'Produto',
                        'stPrincipal',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'idDistribuirParecer',
                        'Parecerista',
                        'idOrgao',
                        'DtEnvioMincVinculada',
                        'DtDistribuicao',
                        'qtDiasParaDistribuir',
                        'TempoTotalAnalise',
                        'TempoParecerista',
                        'TempoDiligencia',
                        'idAgenteParecerista',
                        'dtEnvioDiligencia',
                        'dtRespostaDiligencia',
                        'qtDiligenciaProduto',
                        'QtdeSecundarios',
                        'Valor',
                        'FecharAnalise')
                );

                $from = ' FROM sac.dbo.vwPainelCoordenadorVinculadasEmAnalise';
                break;
            case 'em_validacao':

                $slct->from(
                    array('dbo.vwPainelCoordenadorVinculadasEmValidacao'),
                    array('IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'Produto',
                        'stPrincipal',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'idDistribuirParecer',
                        'Parecerista',
                        'idOrgao',
                        'FecharAnalise',
                        'DtEnvioMincVinculada',
                        'DtDistribuicao',
                        'DtDevolucao',
                        'TempoTotalAnalise',
                        'TempoParecerista',
                        'TempoDiligencia',
                        'qtDiligenciaProduto',
                        'Valor')
                );

                $from = ' FROM sac.dbo.vwPainelCoordenadorVinculadasEmValidacao';
                break;
            case 'validados':

                $slct->from(
                    array('dbo.vwPainelCoordenadorVinculadasValidados'),
                    array('IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'Produto',
                        'stPrincipal',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'DtEnvioMincVinculada',
                        'idDistribuirParecer',
                        'Parecerista',
                        'idOrgao',
                        'qtDiligenciaProduto',
                        'Valor',
                        'FecharAnalise',
                        'TecnicoValidador',
                        'DtValidacao')
                );

                $from = ' FROM sac.dbo.vwPainelCoordenadorVinculadasValidados';
                break;

            case 'presidente_vinculadas':

                $slct->from(
                    array('dbo.vwPainelPresidenteVinculadas'),
                    array('IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'stPrincipal',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'idDistribuirParecer',
                        'Parecerista',
                        'idOrgao',
                        'Valor',
                        'FecharAnalise',
                        'TecnicoValidador',
                        'dtValidacao')
                );

                $from = ' FROM sac.dbo.vwPainelPresidenteVinculadas';
                break;

            case 'superintendente_vinculadas':

                $slct->from(
                    array('dbo.vwPainelSuperintendenteVinculadas'),
                    array('IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'stPrincipal',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'idDistribuirParecer',
                        'Parecerista',
                        'idOrgao',
                        'Valor',
                        'FecharAnalise',
                        'TecnicoValidador',
                        'dtValidacao')
                );

                $from = ' FROM sac.dbo.vwPainelSuperintendenteVinculadas';
                break;

            case 'analisado_superintendencia':

                $slct->from(
                    array('dbo.vwPainelAnalisadoSuperintendencia'),
                    array('IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'stPrincipal',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'idDistribuirParecer',
                        'Parecerista',
                        'idOrgao',
                        'Valor',
                        'FecharAnalise',
                        'TecnicoValidador',
                        'dtValidacao')
                );

                $from = ' FROM sac.dbo.vwPainelAnalisadoSuperintendencia';
                break;

            case 'devolvida':

                $slct->from(
                    array('dbo.vwPainelCoordenadorVinculadasReanalisar'),
                    array('IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'Produto',
                        'stPrincipal',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'idDistribuirParecer',
                        'idOrgao',
                        'FecharAnalise',
                        'idAgenteParecerista',
                        'Parecerista',
                        'DtEnvioMincVinculada',
                        'qtDiasDistribuir',
                        'CAST (JustComponente AS TEXT) AS JustComponente',
                        'JustDevolucaoPedido',
                        'JustSecretaria',
                        'Valor')
                );
                $from = 'FROM sac.dbo.vwPainelCoordenadorVinculadasReanalisar';
                break;

            case 'impedimento_parecerista':
                $slct->from(
                    array('dbo.vwPainelCoordenadorImpedimentoParecerista'),
                    array(
                        'IdPRONAC',
                        'NrProjeto',
                        'NomeProjeto',
                        'idProduto',
                        'Produto',
                        'idArea',
                        'Area',
                        'idSegmento',
                        'Segmento',
                        'idDistribuirParecer',
                        'idOrgao',
                        'idAgenteParecerista',
                        'Parecerista',
                        'DtEnvioMincVinculada',
                        'DtDistribuicao',
                        'DtDevolucao',
                        'JustParecerista',
                        'Valor',
                        'stPrincipal',
                        'FecharAnalise'
                    )
                );
                $from = 'FROM dbo.vwPainelCoordenadorImpedimentoParecerista';
                break;
        }

        // se for totalizador
        if ($qtdeTotal) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
            $whereSql = '';
            if (!empty($where)) {
                $whereSql = ' WHERE ';
                foreach ($where as $coluna => $valor) {
                    if ($whereSql != ' WHERE ') {
                        $whereSql .= ' AND ';
                    }
                    $whereSql .= str_replace('?', "'$valor'", $coluna);
                }
            }
            $sql = "SELECT COUNT(IdPRONAC) " . $from . $whereSql;
            return $db->fetchOne($sql);
        } else {
            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct->where($coluna, $valor);
            }

            //adicionando linha order ao select
            $slct->order($order);

            //paginacao
            if ($tamanho > -1) {
                $tmpInicio = 0;
                if ($inicio > -1) {
                    $tmpInicio = $inicio;
                }
                $slct->limit($tamanho, $tmpInicio);
            }

            return $this->fetchAll($slct);
        }
    }

    public function buscarHistoricoEncaminhamento($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
            array('a' => $this->_name),
            array(new Zend_Db_Expr('a.idPRONAC,b.Descricao as Produto,c.Sigla as Unidade,a.Observacao,convert(char(10),a.DtEnvio,121) as DtEnvio,convert(char(10),a.DtRetorno,121) as DtRetorno, DATEDIFF(DAY,a.DtEnvio,a.DtRetorno) as qtDias'))
        );
        $slct->joinInner(
            array('b' => 'Produto'),
            'a.idProduto = b.Codigo',
            array(),
            'SAC.dbo'
        );
        $slct->joinInner(
            array('c' => 'Orgaos'),
            'a.idOrgao = c.Codigo',
            array(),
            'SAC.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->group(new Zend_Db_Expr('a.idPRONAC,b.Descricao,c.Sigla,a.Observacao,convert(char(10),a.DtEnvio,121),convert(char(10),a.DtRetorno,121), DATEDIFF(DAY,a.DtEnvio,a.DtRetorno)'));
        $slct->order(array('b.Descricao', 'c.Sigla', new Zend_Db_Expr('convert(char(10),a.DtRetorno,121)')));


        return $this->fetchAll($slct);
    }

    /*
     * Criado por Jefferson
     * Data: 20/10/2014
     * Serve para mostrar quantos produtos possui um determinado projeto, e quantos deles foram validados.
     */
    public function QntdProdutosXValidados($where)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
            array('a' => $this->_name),
            array(new Zend_Db_Expr('a.idProduto'))
        );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        $slct->group('a.idProduto');


        return $this->fetchAll($slct)->count();
    }

    public function checarValidacaoProdutosSecundarios($idPronac)
    {
        $sql = "SELECT SAC.dbo.fnchecarValidacaoProdutoSecundario($idPronac)";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchOne($sql);
    }

    public function inserirDistribuicaoParaParecer($idPreProjeto, $idPronac, $idVinculada)
    {
        $sqlDistribuirParecer = "INSERT INTO SAC.dbo.tbDistribuirParecer (idPronac,idProduto,TipoAnalise,idOrgao,DtEnvio, stPrincipal)
                                         SELECT {$idPronac},idProduto, 3,{$idVinculada},getdate(), stPrincipal FROM SAC.dbo.PlanoDistribuicaoProduto
                                          WHERE idProjeto = {$idPreProjeto}";
        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query($sqlDistribuirParecer);
    }
}
