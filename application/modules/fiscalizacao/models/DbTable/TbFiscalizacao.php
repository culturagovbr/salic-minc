<?php

class Fiscalizacao_Model_DbTable_TbFiscalizacao extends MinC_Db_Table_Abstract
{
    protected $_name = 'tbFiscalizacao';
    protected $_schema = 'SAC';
    protected $_primary = 'idFiscalizacao';

    public function buscaFiscalizacao($idFiscalizacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('Fisc' => $this->_name),
            array('Fisc.idFiscalizacao'
            , 'Fisc.IdPRONAC'
            , 'Fisc.dtInicioFiscalizacaoProjeto'
            , 'Fisc.dtFimFiscalizacaoProjeto'
            , 'Fisc.dtRespostaSolicitada'
            , new Zend_Db_Expr('CAST(Fisc.dsFiscalizacaoProjeto AS TEXT) as dsFiscalizacaoProjeto')
            , 'Fisc.tpDemandante'
            , 'Fisc.stFiscalizacaoProjeto'
            , 'Fisc.idAgente'
            , 'Fisc.idSolicitante')
        );
        $select->joinInner(
            array('ar' => 'Area'),
            'ar.Codigo = Fisc.Area',
            array('ar.Codigo as area')
        );
        $select->joinLeft(
            array('sg' => 'Segmento'),
            'sg.Codigo = Fisc.Segmento',
            array('sg.Codigo as segmento')
        );
        $select->where('Fisc.idFiscalizacao = ? ', $idFiscalizacao);

        return $this->fetchRow($select);
    }

    public function alteraSituacaoProjeto($situacao, $idFiscalizacao)
    {
        try {
            $dados = array('stFiscalizacaoProjeto' => $situacao);
            $where = array('idFiscalizacao = ?' => $idFiscalizacao);

            return $this->update($dados, $where);
        } catch (Zend_Db_Table_Exception $e) {
            return 'RelatorioFiscalizacao -> alteraRelatorio. Erro:' . $e->getMessage();
        }
    }

    public function filtroFiscalizacao($retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array('IdPRONAC')
        );
        $select->Where("tbFiscalizacao.stFiscalizacaoProjeto = '0'");
        $select->orWhere("tbFiscalizacao.stFiscalizacaoProjeto = '1'");

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

    public function buscarAtoresFiscalizacao($idPronac, $idusuario = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('fisc' => $this->_name),
            array()
        );

        $select->joinInner(
            array('nm' => 'Nomes'),
            'nm.idAgente = fisc.idAgente',
            array('Nome' => 'nm.Descricao'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('ag' => 'Agentes'),
            'ag.idAgente = fisc.idAgente',
            array('ag.idAgente'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('usu' => 'Usuarios'),
            'ag.CNPJCPF = usu.usu_identificacao',
            array(),
            $this->getSchema('tabelas')
        );
        $select->joinInner(
            array('uog' => 'UsuariosXOrgaosXGrupos'),
            'usu.usu_codigo = uog.uog_usuario and uog.uog_status = 1',
            array(),
            $this->getSchema('tabelas')
        );
        $select->joinInner(
            array('org' => 'Orgaos'),
            'org.Codigo = uog.uog_orgao',
            array('Orgao' => 'org.Sigla'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('gru' => 'Grupos'),
            'gru.gru_codigo = uog.uog_grupo',
            array('Perfil' => 'gru.gru_nome', 'cdPerfil' => 'gru.gru_codigo'),
            $this->getSchema('tabelas')
        );
        $select->where('gru.gru_codigo = 135 or gru.gru_codigo = 134');
        //$select->where('usu.usu_codigo <> ?', $idusuario);
        $select->where('fisc.IdPRONAC = ?', $idPronac);

        return $this->fetchAll($select);
    }

    public function gridFiscalizacaoProjetoFiltro($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
//        $queryFiscalizacao = $this->select();
//        $queryFiscalizacao->setIntegrityCheck(false);
//        $queryFiscalizacao->from(array("tbFiscalizacao" => $this->_name), array('tbFiscalizacao.IdPRONAC, tbFiscalizacao.stFiscalizacaoProjeto'), $this->_schema);
//        $queryFiscalizacao->where('stFiscalizacaoProjeto = ?', '0');
//        $queryFiscalizacao->orWhere('stFiscalizacaoProjeto = ?', '1');

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("p" => 'projetos'),
            array(
                new Zend_Db_Expr('p.IdPRONAC'),
                'p.AnoProjeto',
                'p.Sequencial',
                'p.Area',
                'p.Situacao',
                'p.Segmento',
                'p.Mecanismo',
                'p.idProjeto',
                'p.NomeProjeto',
                'p.CgcCpf',
                new Zend_Db_Expr('sac.dbo.fnTotalCaptadoProjeto(p.AnoProjeto, p.Sequencial) as Total'),
                new Zend_Db_Expr('sac.dbo.fnTotalAprovadoProjeto(p.AnoProjeto, p.Sequencial) as  somatorio')
            )
        );
        $select->joinLeft(
            array('pr' => 'PreProjeto'),
            'p.idProjeto = pr.idPreProjeto',
            array('stPlanoAnual'),
            $this->_schema
        );
        $select->joinLeft(
            array('nom' => 'Nomes'),
            "nom.idAgente = pr.idAgente",
            array('nom.Descricao AS nmAgente'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('Descricao AS dsSegmento'),
            $this->_schema
        );
        $select->joinInner(
            array('si' => 'Situacao'),
            'p.Situacao = si.Codigo',
            array('Descricao AS dsSituacao'),
            $this->_schema
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('Descricao AS dsArea'),
            $this->_schema
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('Descricao AS dsMecanismo'),
            $this->_schema
        );
        $select->joinLeft(
            array('e' => 'EnderecoNacional'),
            'pr.idAgente = e.idAgente',
            array(),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('u' => 'UF'),
            'u.idUF = e.UF',
            array('Regiao', 'Sigla as uf'),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('mu' => 'Municipios'),
            'mu.idUFIBGE = e.UF and mu.idMunicipioIBGE = e.Cidade',
            array('Descricao AS cidade'),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('tf' => 'tbFiscalizacao'),
            'tf.IdPRONAC = p.IdPRONAC',
            array('idFiscalizacao',
                'dtInicioFiscalizacaoProjeto',
                'dtFimFiscalizacaoProjeto',
                'stFiscalizacaoProjeto',
                'dsFiscalizacaoProjeto',
                'dtRespostaSolicitada',
                'idUsuarioInterno as idTecnico',
                "statusFiscalizacao" => new Zend_Db_Expr(
                    "CASE tf.stFiscalizacaoProjeto 
                    WHEN 0 THEN 'Cadastrada' 
                    WHEN 1 THEN 'Em andamento' 
                    WHEN 2 THEN 'Em an&aacute;lise pelo Coordenador' 
                    WHEN 3 THEN 'Finalizada' 
                    ELSE 'N&atilde;o realizada' 
                    END ")
            ),
            $this->_schema
        );
        $select->joinLeft(
            array('tbNm' => 'Nomes'),
            "tf.idAgente = tbNm.idAgente",
            array('nmTecnico' => 'tbNm.Descricao'),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('trf' => 'tbRelatorioFiscalizacao'),
            'tf.idFiscalizacao = trf.idFiscalizacao',
            array('stAvaliacao'),
            $this->_schema
        );
//        $select->joinLeft(
//            array('AUXF' => $queryFiscalizacao),
//            'AUXF.IdPRONAC = tf.IdPRONAC'
//        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }

        $select->order($order);

        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($select);
    }

    public function projetosFiscalizacao($selectAb, $selectAp, $where = array(), $filtroOrgao = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('Projetos'),
            array(
                'Projetos.IdPRONAC',
                'Projetos.AnoProjeto',
                'Projetos.Sequencial',
                'Projetos.Area',
                'Projetos.Situacao',
                'Projetos.Segmento',
                'Projetos.Mecanismo',
                'Projetos.idProjeto',
                'Projetos.NomeProjeto',
                'Projetos.idProjeto'
            )
        );
        $select->joinInner(
            array('PreProjeto'),
            "Projetos.idProjeto = PreProjeto.idPreProjeto",
            array('PreProjeto.stPlanoAnual')
        );
        $select->joinInner(
            array('Segmento'),
            "Projetos.Segmento = Segmento.Codigo AND Projetos.Segmento = Segmento.Codigo",
            array('Segmento.Descricao AS dsSegmento')
        );
        $select->joinInner(
            array('Situacao'),
            "Projetos.Situacao = Situacao.Codigo",
            array('Situacao.Descricao AS dsSituacao')
        );
        $select->joinInner(
            array('Area'),
            "Projetos.Area = Area.Codigo",
            array('Area.Descricao AS dsArea')
        );
        $select->joinInner(
            array('Mecanismo'),
            "Projetos.Mecanismo = Mecanismo.Codigo",
            array('Mecanismo.Descricao AS dsMecanismo')
        );

        $select->joinInner(
            array('abrAux' => $selectAb),
            "abrAux.CNPJCPF = Projetos.CgcCpf",
            array('Mecanismo.Descricao AS dsMecanismo')
        );
        $select->joinInner(
            array('mun' => 'Municipios'),
            "mun.idUFIBGE = abrAux.idUF AND mun.idMunicipioIBGE = abrAux.idMunicipioIBGE",
            array('mun.Descricao as cidade'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('uf' => 'UF'),
            "uf.idUF = mun.idUFIBGE",
            array('uf.Descricao as uf', 'uf.Regiao'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('apr' => $selectAp),
            "apr.Anoprojeto = Projetos.AnoProjeto and apr.Sequencial = Projetos.Sequencial",
            array('apr.somatorio')
        );
        $select->joinInner(
            array('tbFiscalizacao'),
            "tbFiscalizacao.IdPRONAC = Projetos.IdPRONAC and tbFiscalizacao.stFiscalizacaoProjeto != 'S'",
            array('tbFiscalizacao.idFiscalizacao', 'tbFiscalizacao.dtInicioFiscalizacaoProjeto', 'tbFiscalizacao.dtFimFiscalizacaoProjeto', 'tbFiscalizacao.stFiscalizacaoProjeto', 'tbFiscalizacao.dtRespostaSolicitada')
        );
        $select->joinLeft(
            array('trf' => 'tbRelatorioFiscalizacao'),
            "tbFiscalizacao.idFiscalizacao = trf.idFiscalizacao",
            array('trf.stAvaliacao')
        );
        if ($filtroOrgao) {
            $select->joinInner(
                array('ofisc' => 'tbOrgaoFiscalizador'),
                "tbFiscalizacao.idFiscalizacao = ofisc.idFiscalizacao",
                array('ofisc.idOrgao')
            );
        }
        $select->order('Projetos.idProjeto');

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    public function projetosFiscalizacaoConsultar($where, $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('Projetos'),
            array(
                'Projetos.IdPRONAC',
                'Projetos.AnoProjeto',
                'Projetos.Sequencial',
                'Projetos.Area',
                'Projetos.Situacao',
                'Projetos.Segmento',
                'Projetos.Mecanismo',
                'Projetos.idProjeto',
                'Projetos.NomeProjeto',
                'Projetos.idProjeto',
                'Projetos.CgcCpf'
            )
        );

        $select->joinLeft(
            array('i' => 'Interessado'),
            'Projetos.CgcCpf = i.CgcCpf',
            array('Nome as Proponente'),
            "SAC.dbo"
        );
        $select->joinLeft(
            array('PreProjeto'),
            "Projetos.idProjeto = PreProjeto.idPreProjeto",
            array('PreProjeto.stPlanoAnual')
        );
        $select->joinLeft(
            array('nom' => 'Nomes'),
            "nom.idAgente = PreProjeto.idAgente",
            array('nom.Descricao AS nmAgente'),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('Segmento'),
            "Projetos.Segmento = Segmento.Codigo AND Projetos.Segmento = Segmento.Codigo",
            array('Segmento.Descricao AS dsSegmento')
        );
        $select->joinInner(
            array('Situacao'),
            "Projetos.Situacao = Situacao.Codigo",
            array('Situacao.Descricao AS dsSituacao')
        );
        $select->joinInner(
            array('Area'),
            "Projetos.Area = Area.Codigo",
            array('Area.Descricao AS dsArea')
        );
        $select->joinInner(
            array('Mecanismo'),
            "Projetos.Mecanismo = Mecanismo.Codigo",
            array('Mecanismo.Descricao AS dsMecanismo')
        );
        $select->joinLeft(
            array('abr' => 'Abrangencia'),
            "abr.idProjeto = Projetos.idProjeto AND abr.stAbrangencia = 1",
            array('abr.idAbrangencia')
        );
        $select->joinLeft(
            array('mun' => 'Municipios'),
            "mun.idUFIBGE = abr.idUF and mun.idMunicipioIBGE = abr.idMunicipioIBGE",
            array('mun.Descricao as cidade'),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('uf' => 'UF'),
            "uf.idUF = abr.idUF",
            array('uf.Descricao as uf', 'uf.Regiao'),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('tbFiscalizacao'),
            "tbFiscalizacao.IdPRONAC = Projetos.IdPRONAC",
            array('idTecnico' => 'tbFiscalizacao.idAgente', 'tbFiscalizacao.tpDemandante',
                'CAST(tbFiscalizacao.dsFiscalizacaoProjeto AS TEXT) AS dsFiscalizacaoProjeto', 'tbFiscalizacao.idFiscalizacao', 'tbFiscalizacao.dtInicioFiscalizacaoProjeto', 'tbFiscalizacao.dtFimFiscalizacaoProjeto', 'tbFiscalizacao.stFiscalizacaoProjeto', 'tbFiscalizacao.dtRespostaSolicitada')
        );
        $select->joinLeft(
            array('usu' => 'Usuarios'),
            "tbFiscalizacao.idUsuarioInterno = usu.usu_codigo",
            array('cpfTecnico' => 'usu_identificacao', 'nmTecnico' => 'usu_nome'),
            'TABELAS.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order($order);

        return $this->fetchAll($select);
    }

    public function consultarFiscalizacao($where, $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('Projetos'),
            array(
                'Projetos.IdPRONAC',
                'Projetos.AnoProjeto',
                'Projetos.Sequencial',
                'Projetos.Area',
                'Projetos.Situacao',
                'Projetos.Segmento',
                'Projetos.Mecanismo',
                'Projetos.idProjeto',
                'Projetos.NomeProjeto',
                'Projetos.idProjeto',
                'Projetos.CgcCpf'
            )
        );

        $select->joinLeft(
            array('i' => 'Interessado'),
            'Projetos.CgcCpf = i.CgcCpf',
            array('Nome as Proponente'),
            "SAC.dbo"
        );
        $select->joinLeft(
            array('PreProjeto'),
            "Projetos.idProjeto = PreProjeto.idPreProjeto",
            array('PreProjeto.stPlanoAnual')
        );
        $select->joinLeft(
            array('nom' => 'Nomes'),
            "nom.idAgente = PreProjeto.idAgente",
            array('nom.Descricao AS nmAgente'),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('Segmento'),
            "Projetos.Segmento = Segmento.Codigo AND Projetos.Segmento = Segmento.Codigo",
            array('Segmento.Descricao AS dsSegmento')
        );
        $select->joinInner(
            array('Situacao'),
            "Projetos.Situacao = Situacao.Codigo",
            array('Situacao.Descricao AS dsSituacao')
        );
        $select->joinInner(
            array('Area'),
            "Projetos.Area = Area.Codigo",
            array('Area.Descricao AS dsArea')
        );
        $select->joinInner(
            array('Mecanismo'),
            "Projetos.Mecanismo = Mecanismo.Codigo",
            array('Mecanismo.Descricao AS dsMecanismo')
        );
        $select->joinLeft(
            array('tbFiscalizacao'),
            "tbFiscalizacao.IdPRONAC = Projetos.IdPRONAC",
            array('idTecnico' => 'tbFiscalizacao.idAgente', 'tbFiscalizacao.tpDemandante',
                'CAST(tbFiscalizacao.dsFiscalizacaoProjeto AS TEXT) AS dsFiscalizacaoProjeto', 'tbFiscalizacao.idFiscalizacao', 'tbFiscalizacao.dtInicioFiscalizacaoProjeto', 'tbFiscalizacao.dtFimFiscalizacaoProjeto', 'tbFiscalizacao.stFiscalizacaoProjeto', 'tbFiscalizacao.dtRespostaSolicitada')
        );
        $select->joinLeft(
            array('usu' => 'Usuarios'),
            "tbFiscalizacao.idUsuarioInterno = usu.usu_codigo",
            array('cpfTecnico' => 'usu_identificacao', 'nmTecnico' => 'usu_nome'),
            'TABELAS.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order($order);

        return $this->fetchAll($select);
    }

    public function projetosFiscalizacaoEntidade($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('Projetos'),
            array(
                'Projetos.IdPRONAC',
                'Projetos.AnoProjeto',
                'Projetos.Sequencial',
                'Projetos.Area',
                'Projetos.Situacao',
                'Projetos.Segmento',
                'Projetos.Mecanismo',
                'Projetos.idProjeto',
                'Projetos.NomeProjeto',
                'Projetos.idProjeto',
                'Projetos.CgcCpf'
            )
        );
        $select->joinInner(
            array('PreProjeto'),
            "Projetos.idProjeto = PreProjeto.idPreProjeto",
            array('PreProjeto.stPlanoAnual')
        );

        $select->joinInner(
            array('nom' => 'Nomes'),
            "nom.idAgente = PreProjeto.idAgente",
            array('nom.Descricao AS nmAgente'),
            'Agentes.dbo'
        );

        $select->joinInner(
            array('Segmento'),
            "Projetos.Segmento = Segmento.Codigo AND Projetos.Segmento = Segmento.Codigo",
            array('Segmento.Descricao AS dsSegmento')
        );
        $select->joinInner(
            array('Situacao'),
            "Projetos.Situacao = Situacao.Codigo",
            array('Situacao.Descricao AS dsSituacao')
        );
        $select->joinInner(
            array('Area'),
            "Projetos.Area = Area.Codigo",
            array('Area.Descricao AS dsArea')
        );
        $select->joinInner(
            array('Mecanismo'),
            "Projetos.Mecanismo = Mecanismo.Codigo",
            array('Mecanismo.Descricao AS dsMecanismo')
        );
        $select->joinInner(
            array('abr' => 'Abrangencia'),
            "abr.idProjeto = Projetos.idProjeto AND abr.stAbrangencia = 1",
            array('abr.idAbrangencia')
        );
        $select->joinInner(
            array('mun' => 'Municipios'),
            "mun.idUFIBGE = abr.idUF and mun.idMunicipioIBGE = abr.idMunicipioIBGE",
            array('mun.Descricao as cidade'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('uf' => 'UF'),
            "uf.idUF = abr.idUF",
            array('uf.Descricao as uf', 'uf.Regiao'),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('tbFiscalizacao'),
            "tbFiscalizacao.IdPRONAC = Projetos.IdPRONAC and tbFiscalizacao.stFiscalizacaoProjeto in ('0','1')",
            array('idTecnico' => 'tbFiscalizacao.idAgente', 'tbFiscalizacao.tpDemandante', 'CAST(tbFiscalizacao.dsFiscalizacaoProjeto AS TEXT) AS dsFiscalizacaoProjeto', 'tbFiscalizacao.idFiscalizacao', 'tbFiscalizacao.dtInicioFiscalizacaoProjeto', 'tbFiscalizacao.dtFimFiscalizacaoProjeto', 'tbFiscalizacao.stFiscalizacaoProjeto', 'tbFiscalizacao.dtRespostaSolicitada')
        );
        $select->joinLeft(
            array('tbAg' => 'Agentes'),
            "tbFiscalizacao.idAgente = tbAg.idAgente",
            array('cpfTecnico' => 'tbAg.CNPJCPF'),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('tbNm' => 'Nomes'),
            "tbFiscalizacao.idAgente = tbNm.idAgente",
            array('nmTecnico' => 'tbNm.Descricao'),
            $this->getSchema('agentes')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchAll($select);
    }

    public function projetosFiscalizacaoPesquisar($where)
    {
        //Query utilizada quando o Coordenador de Fiscaliza??o acionar o bot?o Fiscalizar
        $tbFiscalizacao = $this->select();
        $tbFiscalizacao->setIntegrityCheck(false);
        $tbFiscalizacao->from(array("tbFiscalizacao" => 'tbFiscalizacao'), array('*'));
        $tbFiscalizacao->where('stFiscalizacaoProjeto = ?', '0');
        $tbFiscalizacao->orWhere('stFiscalizacaoProjeto = ?', '1');

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("p" => 'Projetos'),
            array(
                'p.IdPRONAC',
                'p.AnoProjeto',
                'p.Sequencial',
                'p.idProjeto',
                'p.NomeProjeto',
                'p.CgcCpf'
            )
        );
        $select->joinLeft(
            array('i' => 'Interessado'),
            'p.CgcCpf = i.CgcCpf',
            array('Nome as Proponente'),
            "SAC.dbo"
        );
        $select->joinLeft(
            array('tf' => 'tbFiscalizacao'),
            'tf.IdPRONAC = p.IdPRONAC AND stFiscalizacaoProjeto <> 3',
            array('idFiscalizacao',
                'dtInicioFiscalizacaoProjeto',
                'dtFimFiscalizacaoProjeto',
                'stFiscalizacaoProjeto',
                'dsFiscalizacaoProjeto',
                'dtRespostaSolicitada',
                'idUsuarioInterno AS idTecnico'
            ),
            "SAC.dbo"
        );
        $select->joinLeft(
            array('tbNm' => 'Nomes'),
            "tf.idAgente = tbNm.idAgente",
            array('Descricao AS nmTecnico'),
            'Agentes.dbo'
        );
        $select->joinLeft(
            array('trf' => 'tbRelatorioFiscalizacao'),
            'tf.idFiscalizacao = trf.idFiscalizacao',
            array('stAvaliacao'),
            "SAC.dbo"
        );
        $select->joinLeft(
            array('AUXF' => $tbFiscalizacao),
            'AUXF.IdPRONAC = tf.IdPRONAC',
            array()
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('p.AnoProjeto');

        return $this->fetchAll($select);
    }

    public function buscaProjetosFiscalizacao($selectAb = null, $selectAp = null, $selectCa = null, $selectDOU = null, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('Projetos'),
            array(
                'Projetos.IdPRONAC',
                'Projetos.AnoProjeto',
                'Projetos.Sequencial',
                'Projetos.Area',
                'Projetos.Situacao',
                'Projetos.Segmento',
                'Projetos.Mecanismo',
                'Projetos.idProjeto',
                'Projetos.NomeProjeto',
                'Projetos.idProjeto',
                'Projetos.CgcCpf',
                'Projetos.Processo',
                'Projetos.Localizacao',
                'Projetos.UfProjeto as uf',
                'Projetos.DtInicioExecucao',
                'Projetos.DtFimExecucao'
            )
        );
        $select->joinInner(
            array('PreProjeto'),
            "Projetos.idProjeto = PreProjeto.idPreProjeto",
            array('PreProjeto.stPlanoAnual', 'CAST(PreProjeto.ResumoDoProjeto AS TEXT) AS ResumoDoProjeto')
        );
        $select->joinInner(
            array('nom' => 'Nomes'),
            "nom.idAgente = PreProjeto.idAgente",
            array('nom.Descricao AS nmAgente'),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('Segmento'),
            "Projetos.Segmento = Segmento.Codigo AND Projetos.Segmento = Segmento.Codigo",
            array('Segmento.Descricao AS dsSegmento')
        );
        $select->joinInner(
            array('Situacao'),
            "Projetos.Situacao = Situacao.Codigo",
            array('Situacao.Descricao AS dsSituacao')
        );
        $select->joinInner(
            array('Area'),
            "Projetos.Area = Area.Codigo",
            array('Area.Descricao AS dsArea')
        );
        $select->joinInner(
            array('Mecanismo'),
            "Projetos.Mecanismo = Mecanismo.Codigo",
            array('Mecanismo.Descricao AS dsMecanismo')
        );
        $select->joinLeft(
            array('Convenio'),
            "Projetos.AnoProjeto = Convenio.AnoProjeto and Projetos.Sequencial = Convenio.Sequencial",
            array('Convenio.NumeroConvenio as nrConvenio', 'DtInicioExecucao as DtInicioConvenio', 'DtFinalExecucao as DtFimConvenio')
        );

        $select->joinLeft(
            array('tbFiscalizacao'),
            "tbFiscalizacao.IdPRONAC = Projetos.IdPRONAC",
            array(
                'tbFiscalizacao.idFiscalizacao',
                'dtInicioFiscalizacaoProjeto',
                'dtFimFiscalizacaoProjeto',
                'dtRespostaSolicitada',
                'CAST(tbFiscalizacao.dsFiscalizacaoProjeto AS TEXT) as dsFiscalizacaoProjeto',
                'tbFiscalizacao.stFiscalizacaoProjeto',
                'tbFiscalizacao.idAgente',
                'tbFiscalizacao.idSolicitante'
            )
        );
        $select->joinInner(
            array('nmsol' => 'Nomes'),
            "nmsol.idAgente = tbFiscalizacao.idSolicitante",
            array('nmsol.Descricao AS nmSolicitante'),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('abrAux' => $selectAb),
            "Projetos.idProjeto = abrAux.idProjeto",
            array('Mecanismo.Descricao AS dsMecanismo')
        );
        $select->joinInner(
            array('abr' => 'Abrangencia'),
            "abr.idAbrangencia = abrAux.idAbrangencia AND abr.stAbrangencia = 1",
            array('abr.idAbrangencia')
        //array('abr' => 'Abrangencia'), "abr.idAbrangencia = abrAux.idAbrangencia", array('abr.idAbrangencia')
        );
        $select->joinInner(
            array('mun' => 'Municipios'),
            "mun.idUFIBGE = abr.idUF and mun.idMunicipioIBGE = abr.idMunicipioIBGE",
            array('mun.Descricao as cidade'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('uf' => 'UF'),
            "uf.idUF = abr.idUF",
            array('uf.Descricao as uf', 'uf.Regiao', 'uf.sigla as ufSigla'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('apr' => $selectAp),
            "apr.Anoprojeto = Projetos.AnoProjeto and apr.Sequencial = Projetos.Sequencial",
            array('isnull(apr.somatorio,0) as TotalAprovado')
        );

        if ($selectCa) {
            $select->joinLeft(
                array('ca' => $selectCa),
                "ca.Anoprojeto = Projetos.AnoProjeto and ca.Sequencial = Projetos.Sequencial",
                array('isnull(ca.Total,0) as TotalCaptado')
            );
        }
        if ($selectDOU) {
            $select->joinLeft(
                array('dou' => $selectDOU),
                "dou.Anoprojeto = Projetos.AnoProjeto and dou.Sequencial = Projetos.Sequencial",
                array('dou.DtPublicacaoAprovacao')
            );
        }
        $select->joinLeft(
            array('trf' => 'tbRelatorioFiscalizacao'),
            "tbFiscalizacao.idFiscalizacao = trf.idFiscalizacao",
            array('trf.stAvaliacao')
        );
        $select->joinInner(
            array('i' => 'Interessado'),
            "Projetos.CgcCPf = i.CgcCPf",
            array('i.Nome AS Proponente')
        );
        $select->joinLeft(
            array('tbOrgaoFiscalizador'),
            "tbOrgaoFiscalizador.idFiscalizacao = tbFiscalizacao.idFiscalizacao ",
            array(
                'tbOrgaoFiscalizador.idOrgaoFiscalizador',
                'tbOrgaoFiscalizador.idOrgao',
                'CAST(tbOrgaoFiscalizador.dsObservacao as TEXT) as dsObservacao',
                'tbOrgaoFiscalizador.idParecerista'
            )
        );
        $select->joinLeft(
            array('nmpa' => 'Nomes'),
            "nmpa.idAgente = tbOrgaoFiscalizador.idParecerista",
            array('nmpa.Descricao AS nmParecerista'),
            'Agentes.dbo'
        );
        $select->joinLeft(
            array('agepa' => 'Agentes'),
            "agepa.idAgente = tbOrgaoFiscalizador.idParecerista",
            array('agepa.CNPJCPF AS CNPJCPFParecerista'),
            'Agentes.dbo'
        );
        $select->joinLeft(
            array('orgaoPar' => 'Orgaos'),
            "orgaoPar.Codigo = tbOrgaoFiscalizador.idOrgao",
            array('orgaoPar.Sigla AS orgaoParecerista')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('Projetos.idProjeto');
        $select->order('tbFiscalizacao.idFiscalizacao');


        return $this->fetchAll($select);
    }

    public function buscarProjetosFiscalizacao($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => 'Projetos'),
            array(
                'a.IdPRONAC',
                'a.AnoProjeto',
                'a.Sequencial',
                'a.Area',
                'a.Situacao',
                'a.Segmento',
                'a.Mecanismo',
                'a.idProjeto',
                'a.NomeProjeto',
                'a.CgcCpf',
                'a.Processo',
                'a.UfProjeto as uf',
                'a.DtInicioExecucao',
                'a.DtFimExecucao',
                new Zend_Db_Expr('CAST(a.ResumoProjeto as TEXT) as ResumoDoProjeto')
            )
        );
        $select->joinInner(
            array('b' => 'Agentes'),
            "a.CgcCpf = b.CNPJCPF",
            array(''),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('c' => 'Nomes'),
            "b.idAgente = c.idAgente",
            array('Descricao as nmAgente', 'Descricao as Proponente'),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('d' => 'Segmento'),
            "a.Segmento = d.Codigo",
            array('Descricao as dsSegmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('e' => 'Area'),
            "a.Area = e.Codigo",
            array('Descricao as dsArea'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('f' => 'Mecanismo'),
            "a.Mecanismo = f.Codigo",
            array('Descricao as dsMecanismo'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('g' => 'tbFiscalizacao'),
            "a.IdPRONAC = g.IdPRONAC",
            array(
                'idFiscalizacao',
                'dtInicioFiscalizacaoProjeto',
                'dtFimFiscalizacaoProjeto',
                'dtRespostaSolicitada',
                'idUsuarioInterno',
                new Zend_Db_Expr('CAST(g.dsFiscalizacaoProjeto as TEXT) as dsFiscalizacaoProjeto'),
                'stFiscalizacaoProjeto'
            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('h' => 'tbRelatorioFiscalizacao'),
            "g.idFiscalizacao = h.idFiscalizacao",
            array('stAvaliacao'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('i' => 'tbOrgaoFiscalizador'),
            "g.idFiscalizacao = i.idFiscalizacao",
            array('idOrgaoFiscalizador', 'idOrgao', 'idParecerista', new Zend_Db_Expr('CAST(i.dsObservacao as TEXT) as dsObservacao')),
            'SAC.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('a.NomeProjeto');
        $select->order('g.idFiscalizacao');


        return $this->fetchAll($select);
    }

    public function painelFiscalizacaoProjetos($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('b' => 'tbFiscalizacao'),
            array('dtInicioFiscalizacaoProjeto', 'dtFimFiscalizacaoProjeto', 'idFiscalizacao', 'stFiscalizacaoProjeto'),
            $this->_schema
        );

        $select->joinInner(
            array('a' => 'Projetos'),
            'b.IdPRONAC = a.IdPRONAC',
            array('IdPRONAC', 'idProjeto', 'NomeProjeto',
                new Zend_Db_Expr("a.AnoProjeto + a.Sequencial AS Pronac")
            ),
            $this->_schema
        );

        $select->joinLeft(
            array('c' => 'tbRelatorioFiscalizacao'),
            'b.idFiscalizacao = c.idFiscalizacao',
            array(''),
            $this->_schema
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

    /** @todo não utilizado */
    public function enviarEmailFiscalizacao($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array($this->_name),
            array()
        );
        $select->joinInner(
            array('PreProjeto'),
            "Projetos.idProjeto = PreProjeto.idPreProjeto",
            array()
        );


        $select->joinInner(
            array('nom' => 'Nomes'),
            "nom.idAgente = PreProjeto.idAgente",
            array(),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('int' => 'Internet'),
            "int.idAgente = PreProjeto.idAgente and int.Status = 1",
            array('int.Descricao AS emailAgente'),
            'Agentes.dbo'
        );
        $select->joinInner(
            array('Segmento'),
            "Projetos.Segmento = Segmento.Codigo AND Projetos.Segmento = Segmento.Codigo",
            array()
        );
        $select->joinInner(
            array('Situacao'),
            "Projetos.Situacao = Situacao.Codigo",
            array()
        );
        $select->joinInner(
            array('Area'),
            "c = Area.Codigo",
            array()
        );
        $select->joinInner(
            array('Mecanismo'),
            "Projetos.Mecanismo = Mecanismo.Codigo",
            array()
        );
        $select->joinInner(
            array('abr' => 'Abrangencia'),
            "abr.idProjeto = Projetos.idProjeto AND abr.stAbrangencia = 1",
            array()
        );
        $select->joinInner(
            array('mun' => 'Municipios'),
            "mun.idUFIBGE = abr.idUF and mun.idMunicipioIBGE = abr.idMunicipioIBGE",
            array(),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('uf' => 'UF'),
            "uf.idUF = abr.idUF",
            array(),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('tbFiscalizacao'),
            "tbFiscalizacao.IdPRONAC = Projetos.IdPRONAC",
            array()
        );
        $select->joinLeft(
            array('tbAg' => 'Agentes'),
            "tbFiscalizacao.idAgente = tbAg.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('tbNm' => 'Nomes'),
            "tbFiscalizacao.idAgente = tbNm.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('tbint' => 'Internet'),
            "tbint.idAgente = tbFiscalizacao.idAgente and tbint.Status = 1",
            array('tbint.Descricao AS emailtecnico'),
            'Agentes.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }
        return $this->fetchAll($select);
    }
}
