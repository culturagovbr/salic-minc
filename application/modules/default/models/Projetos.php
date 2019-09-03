<?php

class Projetos extends MinC_Db_Table_Abstract
{
    protected $_name = 'projetos';
    protected $_schema = 'sac';
    protected $_primary = 'IdPRONAC';

    public $_total = 0;
    public $_totalRegistros;
    private $codOrgao = null;

    public function montarFiltrosListaProjetosDeUsuario($consulta, stdClass $objParam)
    {
        # Filtros
        if ($objParam->idUsuario) {
            $consulta->where('p.IdUsuario = ?', $objParam->idUsuario);
        }
        if ($objParam->idUsuario) {
            $consulta->where('p.IdUsuario = ?', $objParam->idUsuario);
        }

        if ($objParam->idProponente) {
            $consulta->where('p.idAgente = ?', (int)$objParam->idProponente);
        }
        if ($objParam->pronac) {
            $consulta->where('p.Pronac = ?', $objParam->pronac);
        }
        if ($objParam->cgcCpf) {
            $consulta->where('p.CgcCpf = ?', $objParam->cgcCpf);
        }
        if ($objParam->nomeProponente) {
            $consulta->where("p.NomeProponente LIKE '%$objParam->nomeProponente%'");
        }

        return $consulta;
    }

    public function listarProjetosDeUsuario(stdClass $objParam)
    {
        $consulta = $this->select();
        $consulta->setIntegrityCheck(false);
        $consulta
            ->from(array('p' => 'vwAgentesSeusProjetos'), array(
                'IdPRONAC',
                'Pronac',
                'NomeProjeto'), 'SAC.dbo')
            ->group(array(
                'IdPRONAC',
                'Pronac',
                'NomeProjeto'))
            ->order(array(
                'Pronac',
                'NomeProjeto'));
        # Filtros
        $this->montarFiltrosListaProjetosDeUsuario($consulta, $objParam);

        # Paginacao
        if ($objParam->next) {
            $consulta->limit($objParam->next, (int)$objParam->offset);
        }

        return $this->fetchAll($consulta);
    }

    public function buscarTotalListarProjetosDeUsuario(stdClass $objParam)
    {
        $total = 0;
        $consulta = $this->select();
        $consulta->setIntegrityCheck(false);
        $consulta
            ->from(array('p' => 'vwAgentesSeusProjetos'), array(
                'total' => new Zend_Db_Expr('COUNT(DISTINCT IdPRONAC)')
            ), 'SAC.dbo');
        # Filtros
        $this->montarFiltrosListaProjetosDeUsuario($consulta, $objParam);

        $rs = $this->fetchRow($consulta);
        if ($rs) {
            $total = (int)$rs->total;
        }

        return $total;
    }

    public function buscarAnoExtratoDeProjeto($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('l' => 'vwExtratoDaMovimentacaoBancaria'), array(
            'ano' => new Zend_Db_Expr('CONVERT(CHAR(4), l.dtLancamento, 120)')
        ), 'dbo')
            ->where('l.idPronac = ?', (int)$idPronac)
            ->group(array(new Zend_Db_Expr('CONVERT(CHAR(4), l.dtLancamento, 120)')))
            ->order(array('ano ASC'));

        return $this->fetchAll($select);
    }

    public function buscarMesExtratoDeProjeto($idPronac, $ano)
    {
        $this->getAdapter()->query('SET Language Brazilian');
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('l' => 'vwExtratoDaMovimentacaoBancaria'), array(
            'numero' => new Zend_Db_Expr('CONVERT(CHAR(2), l.dtLancamento, 101)'),
            'descricao' => new Zend_Db_Expr('DATENAME(MONTH,l.dtLancamento)')
        ), 'dbo')
            ->where('l.idPronac = ?', (int)$idPronac)
            ->where('CONVERT(CHAR(4), l.dtLancamento, 120) = ?', (int)$ano)
            ->group(array(
                new Zend_Db_Expr('CONVERT(CHAR(2), l.dtLancamento, 101)'),
                new Zend_Db_Expr('DATENAME(MONTH,l.dtLancamento)')
            ))
            ->order(array('numero ASC'));

        return $this->fetchAll($select);
    }

    public function buscarTotalExtrato(stdClass $objParam)
    {
        $total = 0;
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('l' => 'vwExtratoDaMovimentacaoBancaria'), array('total' => new Zend_Db_Expr('COUNT(l.idPronac)')), 'dbo')
            ->where('l.idPronac = ?', (int)$objParam->idPronac);

        # Filtros
        # Filtros
        $select = $this->montarFiltrosExtrato($select, $objParam);

        # Busca o total de registros do banco.
        $rs = $this->fetchRow($select);
        if ($rs) {
            $total = (int)$rs->total;
        }

        return $total;
    }

    public function montarFiltrosExtrato($select, stdClass $objParam)
    {
        # Filtros
        if ($objParam->ano) {
            $select->where('CONVERT(CHAR(4), l.dtLancamento, 120) = ?', $objParam->ano);
        }
        if ($objParam->mes) {
            $select->where("CONVERT(CHAR(2), l.dtLancamento, 101) = ?", $objParam->mes);
        }

        return $select;
    }

    public function buscarExtrato(stdClass $objParam)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('l' => 'vwExtratoDaMovimentacaoBancaria'), array(
            'dtLancamento' => new Zend_Db_Expr('CONVERT(CHAR(10),l.dtLancamento,103)'),
            'Lancamento',
            'nrLancamento',
            'vlLancamento',
            'stLancamento'
        ), 'dbo')
            ->where('l.idPronac = ?', (int)$objParam->idPronac)
            ->order(array('dtLancamento ASC', 'nrLancamento ASC'));

        # Filtros
        $this->montarFiltrosExtrato($select, $objParam);

        # Paginacao
        if ($objParam->next) {
            $select->limit($objParam->next, (int)$objParam->offset);
        }

        return $this->fetchAll($select);
    }

    public function buscarPorPronac($pronac)
    {
        $consulta = $this->select();
        $consulta->setIntegrityCheck(false);
        $consulta->from(
            array('a' => 'vwConsultaProjetoSimplificada'),
            array(
                'IdPRONAC',
                'Pronac',
                'NomeProjeto',
                'CNPJCPF',
                'Proponente',
                'UFProjeto',
                'Area',
                'Segmento',
                'ResumoProjeto',
                'CodigoSituacao',
                'Situacao',
                'Enquadramento',
                'stConta',
                'dtFimCaptacao',
                'DtFimExecucao',
                'Agencia',
                'Conta',
                'ValorAprovado',
                'ValorProjeto',
                'ValorCaptado',
                'VlComprovado',
                'PercCaptado'
            ),
            'SAC.dbo'
        );

        if (!empty($pronac)) {
            $consulta->where('a.IdPRONAC = ?', $pronac);
        }

        return $this->fetchRow($consulta);
    }

    /**
     * Metodo para buscar os dados b?sicos de projetos e proponentes com projetos
     * @access public
     * @param array $where
     * @return object
     */
    public function buscarProjetoXProponente($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array('pronac' => new Zend_Db_Expr('p.AnoProjeto + p.Sequencial')
            , 'p.IdPRONAC'
            , 'p.NomeProjeto'
            , 'p.idProjeto'
            )
        );

        $select->joinInner(
            array('a' => 'Agentes'),
            'a.CNPJCPF = p.CgcCpf',
            array('a.idAgente'
            , 'a.CNPJCPF'
            ),
            $this->getSchema('agentes')
        );

        $select->joinLeft(
            array('n' => 'Nomes'),
            'n.idAgente = a.idAgente',
            array('n.Descricao AS NomeProponente'),
            $this->getSchema('agentes')
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->order('p.NomeProjeto ASC');

        return $this->fetchAll($select);
    }

    // fecha metodo buscarProjetoXProponente()

    public function BuscarAreaSegmentoProjetos($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('Pr' => $this->_name),
            array()
        );
        $slct->joinInner(
            array('ar' => 'Area'),
            'ar.Codigo = pr.Area',
            array('ar.Codigo as area')
        );
        $slct->joinLeft(
            array('sg' => 'Segmento'),
            'sg.Codigo = pr.Segmento',
            array('sg.Codigo as segmento')
        );
        $slct->where('Pr.idPRONAC = ? ', $idPronac);

        return $this->fetchRow($slct);
    }

    public function buscaAreaSegmentoProjeto($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('Pr' => $this->_name),
            array()
        );
        $slct->joinInner(
            array('ar' => 'Area'),
            'ar.Codigo = pr.Area',
            array('ar.Codigo as cdArea', 'ar.Descricao as area')
        );
        $slct->joinLeft(
            array('sg' => 'Segmento'),
            'sg.Codigo = pr.Segmento',
            array('sg.Codigo as cdSegmento', 'sg.Descricao as segmento')
        );
        $slct->where('Pr.idPRONAC = ? ', $idPronac);

        return $this->fetchRow($slct);
    }

    public function buscarPedidosReadProjetosAprovados($idpronac = null, $situacao = null, $situacaoSalic = null, $tipoaprovacao = null, $orgao = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("pr" => $this->_name),
            array('pronac' => new Zend_Db_Expr('pr.AnoProjeto + pr.Sequencial'),
                'pr.NomeProjeto',
                'pr.IdPRONAC',
                'pr.CgcCpf',
                'pr.idpronac',
                'pr.Area as cdarea',
                'pr.ResumoProjeto',
                'pr.UfProjeto',
                'pr.DtInicioExecucao',
                'pr.DtFimExecucao',
                'DtInicioCaptacao' => new Zend_Db_Expr("dateadd(day,1,getdate())"),
                'DtFimCaptacao' => new Zend_Db_Expr("case when CONVERT(char(10),pr.DtFimExecucao,111) <= CONVERT(char(4),year(getdate())) + '/12/31' then pr.DtFimExecucao else CONVERT(char(4),year(getdate())) + '/12/31' end"),
                'DtSolicitacao' => new Zend_Db_Expr('(select top 1 DtSolicitacao from SAC.dbo.tbDiligencia dili1 where dili1.idPronac = pr.idPronac order by dili1.DtSolicitacao desc)'),
                'DtResposta' => new Zend_Db_Expr('(select top 1 DtResposta from SAC.dbo.tbDiligencia dili2 where dili2.idPronac = pr.idPronac order by dili2.DtSolicitacao desc)'),
                'stEnviado' => new Zend_Db_Expr('(select top 1 stEnviado from SAC.dbo.tbDiligencia dili3 where dili3.idPronac = pr.idPronac order by dili3.DtSolicitacao desc)')
            ),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ap" => "Aprovacao"),
            "ap.idPronac = pr.idPronac",
            array(
                'ap.DtInicioCaptacao',
                'ap.DtFimCaptacao',
                'ap.DtPublicacaoAprovacao',
                'ap.PortariaAprovacao',
                'ap.AprovadoReal'),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ar" => "Area"),
            "ar.Codigo = pr.Area",
            array("ar.Descricao as area"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("seg" => "Segmento"),
            "seg.Codigo = pr.Segmento",
            array('seg.Descricao as segmento'),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("en" => "Enquadramento"),
            "en.IdPRONAC = pr.IdPRONAC",
            array('en.Enquadramento as nrenq',
                'en.Observacao',
                'enquadramento' => new Zend_Db_Expr("case when en.Enquadramento = 1 then '26'
                when en.Enquadramento = 2 then '18' end ")),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("tp" => "tbPauta"),
            "tp.IdPRONAC = pr.IdPRONAC",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $slct->joinInner(
            array("tr" => "tbReuniao"),
            "tr.idNrReuniao = tp.idNrReuniao",
            array('tr.NrReuniao'),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("ag" => "Agentes"),
            "ag.CNPJCPF = pr.CgcCpf",
            array(),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = ag.idAgente",
            array("nm.Descricao as nome"),
            "AGENTES.dbo"
        );
        /* $slct->joinInner(
          array("tvp"=>"tbVerificaProjeto"),
          "tvp.IdPRONAC = pr.IdPRONAC",
          array(),
          "SAC.dbo"
          ); */

        if (!empty($situacao)) {
            $slct->where('pr.Situacao = ?', $situacao);
        }
        if (!empty($situacaoSalic)) {
            $slct->where('ap.TipoAprovacao = ?', 1);
            $slct->where('ap.DtPublicacaoAprovacao IS NULL');
            $slct->where('ap.PortariaAprovacao IS NULL');
        }
        return $this->fetchAll($slct);
    }

    public function buscarPedidosReadProjetos($idpronac = null, $situacao = null, $situacaoSalic = null, $tipoaprovacao = null, $orgao = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("pr" => $this->_name),
            array('pronac' => new Zend_Db_Expr('pr.AnoProjeto + pr.Sequencial'),
                'pr.NomeProjeto',
                'pr.IdPRONAC',
                'pr.CgcCpf',
                'pr.idpronac',
                'pr.Area as cdarea',
                'pr.ResumoProjeto',
                'pr.UfProjeto',
                'pr.DtInicioExecucao',
                'pr.DtFimExecucao',
                'DtInicioCaptacao' => new Zend_Db_Expr("dateadd(day,1,getdate())"),
                'DtFimCaptacao' => new Zend_Db_Expr("case when CONVERT(char(10),pr.DtFimExecucao,111) <= CONVERT(char(4),year(getdate())) + '/12/31' then pr.DtFimExecucao else CONVERT(char(4),year(getdate())) + '/12/31' end"),
                'DtSolicitacao' => new Zend_Db_Expr('(select top 1 DtSolicitacao from SAC.dbo.tbDiligencia dili1 where dili1.idPronac = pr.idPronac order by dili1.DtSolicitacao desc)'),
                'DtResposta' => new Zend_Db_Expr('(select top 1 DtResposta from SAC.dbo.tbDiligencia dili2 where dili2.idPronac = pr.idPronac order by dili2.DtSolicitacao desc)'),
                'stEnviado' => new Zend_Db_Expr('(select top 1 stEnviado from SAC.dbo.tbDiligencia dili3 where dili3.idPronac = pr.idPronac order by dili3.DtSolicitacao desc)')
            ),
            "SAC.dbo"
        );

        /* dateadd(day,1,getdate()) as DtInicioCaptacao,

          case when CONVERT(char(10),"pr"."DtFimExecucao",111) <= CONVERT(char(4),year(getdate())) + '/12/31' then pr.DtFimExecucao else CONVERT(char(4),year(getdate())) + '/12/31' end as "DtFimCaptacao", */

        $slct->joinInner(
            array("ap" => "Aprovacao"),
            "ap.idPronac = pr.idPronac",
            array(
                'ap.DtPublicacaoAprovacao',
                'ap.PortariaAprovacao',
                'ap.AprovadoReal'),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ar" => "Area"),
            "ar.Codigo = pr.Area",
            array("ar.Descricao as area"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("seg" => "Segmento"),
            "seg.Codigo = pr.Segmento",
            array('seg.Descricao as segmento'),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("en" => "Enquadramento"),
            "en.IdPRONAC = pr.IdPRONAC",
            array('en.Enquadramento as nrenq',
                'en.Observacao',
                'enquadramento' => new Zend_Db_Expr("case when en.Enquadramento = 1 then '26'
                when en.Enquadramento = 2 then '18' end ")),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("tp" => "tbPauta"),
            "tp.IdPRONAC = pr.IdPRONAC",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $slct->joinInner(
            array("tr" => "tbReuniao"),
            "tr.idNrReuniao = tp.idNrReuniao",
            array('tr.NrReuniao'),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("ag" => "Agentes"),
            "ag.CNPJCPF = pr.CgcCpf",
            array(),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = ag.idAgente",
            array("nm.Descricao as nome"),
            "AGENTES.dbo"
        );
        $slct->joinLeft(
            array("tvp" => "tbVerificaProjeto"),
            "tvp.IdPRONAC = ag.idAgente",
            array(),
            "SAC.dbo"
        );

        if (!empty($situacao)) {
            $slct->where('pr.Situacao = ?', $situacao);
        }
        if (!empty($situacaoSalic)) {
            $slct->where('ap.TipoAprovacao = ?', 1);
            $slct->where('ap.DtPublicacaoAprovacao IS NULL');
            $slct->where('ap.PortariaAprovacao IS NULL');
        }

        return $this->fetchAll($slct);
    }

    public function ProjetosCheckList($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("pr" => $this->_name),
            array('pronac' => new Zend_Db_Expr('pr.AnoProjeto + pr.Sequencial'),
                'pr.NomeProjeto',
                'pr.IdPRONAC',
                'pr.CgcCpf',
                'pr.idpronac',
                'pr.Area as cdarea',
                'pr.Segmento as cdsegmento',
                'pr.ResumoProjeto',
                'pr.UfProjeto',
                'pr.DtInicioExecucao',
                'pr.DtFimExecucao',
                'DtInicioCaptacao' => new Zend_Db_Expr("CASE WHEN DtInicioCaptacao IS NOT NULL
                                                                            THEN ap.DtInicioCaptacao
                                                                            ELSE dateadd(day,1,getdate())
                                                                       END"),
                'DtFimCaptacao' => new Zend_Db_Expr("CASE WHEN DtFimCaptacao IS NOT NULL THEN ap.DtFimCaptacao
                                                                           WHEN CONVERT(char(10),pr.DtFimExecucao,111) <= CONVERT(char(4),year(getdate())) + '/12/31'
                                                                                THEN pr.DtFimExecucao
                                                                                ELSE CONVERT(char(4),year(getdate())) + '/12/31'
                                                                       END"),
            ),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ap" => "Aprovacao"),
            "ap.idPronac = pr.idPronac",
            array(
                'ap.idAprovacao',
                'ap.DtPublicacaoAprovacao',
                'ap.PortariaAprovacao',
                'ap.DtInicioCaptacao as DtInicioCaptacaoGravada',
                'ap.DtFimCaptacao as DtFimCaptacaoGravada',
                'AprovadoReal' => new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(pr.AnoProjeto,pr.Sequencial)")),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ar" => "Area"),
            "ar.Codigo = pr.Area",
            array("ar.Descricao as area"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("seg" => "Segmento"),
            "seg.Codigo = pr.Segmento",
            array('seg.Descricao as segmento'),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("en" => "Enquadramento"),
            "en.IdPRONAC = pr.IdPRONAC",
            array('en.Enquadramento as nrenq',
                'en.Observacao',
                'enquadramento' => new Zend_Db_Expr("CASE WHEN en.Enquadramento = 1
                                                                            THEN '26'
                                                                       WHEN en.Enquadramento = 2
                                                                            THEN '18'
                                                                  END")),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("tp" => "tbPauta"),
            "tp.IdPRONAC = pr.IdPRONAC AND tp.dtEnvioPauta IN (SELECT TOP 1 Max(dtEnvioPauta) FROM BDCORPORATIVO.scSAC.tbPauta WHERE  IdPRONAC = pr.IdPRONAC)",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $slct->joinLeft(
            array("tr" => "tbReuniao"),
            "tr.idNrReuniao = tp.idNrReuniao",
            array('tr.NrReuniao'),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("ag" => "Agentes"),
            "ag.CNPJCPF = pr.CgcCpf",
            array(),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = ag.idAgente",
            array("nm.Descricao as nome"),
            "AGENTES.dbo"
        );
        $slct->joinLeft(
            array("vp" => "tbVerificaProjeto"),
            "vp.IdPRONAC = pr.IdPRONAC",
            array('vp.idUsuario',
                'NomeTecnico' => new Zend_Db_Expr('(SELECT top 1 usu_nome FROM TABELAS.dbo.Usuarios tecnico WHERE tecnico.usu_codigo = vp.idUsuario)'),
                'vp.stAnaliseProjeto',
                'status' => new Zend_Db_Expr("CASE WHEN vp.stAnaliseProjeto IS NULL
                                                                        THEN 'Aguardando An&aacute;lise'
                                                                   WHEN vp.stAnaliseProjeto = '1'
                                                                        THEN 'Aguardando An&aacute;lise'
                                                                   WHEN vp.stAnaliseProjeto = '2'
                                                                        THEN 'Em An&aacute;lise'
                                                                   WHEN vp.stAnaliseProjeto = '3'
                                                                        THEN 'An&aacute;lise Finalizada'
                                                                   WHEN vp.stAnaliseProjeto = '4'
                                                                        THEN 'Encaminhado para portaria'
                                                              END "),
                'tempoAnalise' => new Zend_Db_Expr("DATEDIFF(day, vp.DtRecebido, GETDATE())"),
                "vp.dtRecebido"
            ),
            "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order('pr.DtInicioExecucao ASC');

        return $this->fetchAll($slct);
    }

    public function buscarProjetosCheckList($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("pr" => $this->_name),
            array('pronac' => new Zend_Db_Expr('pr.AnoProjeto + pr.Sequencial'),
                'pr.NomeProjeto',
                'pr.IdPRONAC',
                'pr.CgcCpf',
                'pr.idpronac',
                'pr.Area as cdarea',
                'pr.Segmento as cdsegmento',
                'pr.ResumoProjeto',
                'pr.UfProjeto',
                'pr.DtInicioExecucao',
                'pr.DtFimExecucao',
                //'DtInicioCaptacao' => New Zend_Db_Expr("dateadd(day,1,getdate())"),
                'DtInicioCaptacao' => new Zend_Db_Expr("CASE WHEN DtInicioCaptacao IS NOT NULL
                                                                          THEN ap.DtInicioCaptacao
                                                                          ELSE dateadd(day,1,getdate()) END"),
                'DtFimCaptacao' => new Zend_Db_Expr("CASE WHEN DtFimCaptacao IS NOT NULL THEN ap.DtFimCaptacao
                                                                          WHEN CONVERT(char(10),pr.DtFimExecucao,111) <= CONVERT(char(4),year(getdate())) + '/12/31' THEN pr.DtFimExecucao
                                                                          ELSE CONVERT(char(4),year(getdate())) + '/12/31' END"),
                /* 'DtSolicitacao' => new Zend_Db_Expr('(select top 1 DtSolicitacao from SAC.dbo.tbDiligencia dili1 where dili1.idPronac = pr.idPronac and idTipoDiligencia=181 order by dili1.DtSolicitacao desc)'),
                  'DtResposta' => new Zend_Db_Expr('(select top 1 DtResposta from SAC.dbo.tbDiligencia dili2 where dili2.idPronac = pr.idPronac and idTipoDiligencia=181 order by dili2.DtSolicitacao desc)'),
                  'stEnviado' => new Zend_Db_Expr('(select top 1 stEnviado from SAC.dbo.tbDiligencia dili3 where dili3.idPronac = pr.idPronac and idTipoDiligencia=181 order by dili3.DtSolicitacao desc)') */
            ),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ap" => "Aprovacao"),
            "ap.idPronac = pr.idPronac AND ap.DtAprovacao in (select TOP 1 max(DtAprovacao) from SAC..Aprovacao where IdPRONAC = pr.IdPRONAC)",
            array(
                'ap.idAprovacao',
                'ap.DtPublicacaoAprovacao',
                'ap.PortariaAprovacao',
                'ap.DtInicioCaptacao as DtInicioCaptacaoGravada',
                'ap.DtFimCaptacao as DtFimCaptacaoGravada',
                //'ap.AprovadoReal',
                'AprovadoReal' => new Zend_Db_Expr("SAC.dbo.fnTotalAprovadoProjeto(pr.AnoProjeto,pr.Sequencial)")),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ar" => "Area"),
            "ar.Codigo = pr.Area",
            array("ar.Descricao as area"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("seg" => "Segmento"),
            "seg.Codigo = pr.Segmento",
            array('seg.Descricao as segmento'),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("en" => "Enquadramento"),
            "en.IdPRONAC = pr.IdPRONAC",
            array('en.Enquadramento as nrenq',
                'en.Observacao',
                'enquadramento' => new Zend_Db_Expr("case when en.Enquadramento = 1 then '26' when en.Enquadramento = 2 then '18' end ")),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("tp" => "tbPauta"),
            "tp.IdPRONAC = pr.IdPRONAC AND tp.dtEnvioPauta IN (SELECT TOP 1 Max(dtEnvioPauta) FROM BDCORPORATIVO.scSAC.tbPauta WHERE  IdPRONAC = pr.IdPRONAC)",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $slct->joinLeft(
            array("tr" => "tbReuniao"),
            "tr.idNrReuniao = tp.idNrReuniao",
            array('tr.NrReuniao'),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("ag" => "Agentes"),
            "ag.CNPJCPF = pr.CgcCpf",
            array(),
            "AGENTES.dbo"
        );
        $slct->joinInner(
            array("nm" => "Nomes"),
            "nm.idAgente = ag.idAgente",
            array("nm.Descricao as nome"),
            "AGENTES.dbo"
        );
        $slct->joinLeft(
            array("vp" => "tbVerificaProjeto"),
            "vp.IdPRONAC = pr.IdPRONAC",
            array('vp.idUsuario',
                'NomeTecnico' => new Zend_Db_Expr('(SELECT top 1 usu_nome FROM TABELAS.dbo.Usuarios tecnico WHERE tecnico.usu_codigo = vp.idUsuario)'),
                'vp.stAnaliseProjeto',
                'status' => new Zend_Db_Expr("CASE WHEN vp.stAnaliseProjeto IS NULL THEN 'Aguardando An&aacute;lise'
                                                                 WHEN vp.stAnaliseProjeto = '1' THEN 'Aguardando An&aacute;lise'
                                                                 WHEN vp.stAnaliseProjeto = '2' THEN 'Em An&aacute;lise'
                                                                 WHEN vp.stAnaliseProjeto = '3' THEN 'An&aacute;lise Finalizada'
                                                                 WHEN vp.stAnaliseProjeto = '4' THEN 'Encaminhado para portaria'
                                                                 END "),
                new Zend_Db_Expr("DATEDIFF(day, vp.DtRecebido, GETDATE()) AS tempoAnalise"),
                "vp.dtRecebido"
            ),
            "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(
                array("pr" => $this->_name),
                array("total" => "count(*)"),
                "SAC.dbo"
            );
            $slctContador->joinInner(
                array("ap" => "Aprovacao"),
                "ap.idPronac = pr.idPronac AND ap.DtAprovacao in (select TOP 1 max(DtAprovacao) from SAC..Aprovacao where IdPRONAC = pr.IdPRONAC)",
                array(),
                "SAC.dbo"
            );

            $slctContador->joinInner(
                array("ar" => "Area"),
                "ar.Codigo = pr.Area",
                array(),
                "SAC.dbo"
            );
            $slctContador->joinInner(
                array("seg" => "Segmento"),
                "seg.Codigo = pr.Segmento",
                array(),
                "SAC.dbo"
            );
            $slctContador->joinInner(
                array("en" => "Enquadramento"),
                "en.IdPRONAC = pr.IdPRONAC",
                array(),
                "SAC.dbo"
            );
            $slctContador->joinInner(
                array("tp" => "tbPauta"),
                "tp.IdPRONAC = pr.IdPRONAC AND tp.dtEnvioPauta IN (SELECT TOP 1 Max(dtEnvioPauta) FROM BDCORPORATIVO.scSAC.tbPauta WHERE  IdPRONAC = pr.IdPRONAC)",
                array(),
                "BDCORPORATIVO.scSAC"
            );
            $slctContador->joinInner(
                array("tr" => "tbReuniao"),
                "tr.idNrReuniao = tp.idNrReuniao",
                array(),
                "SAC.dbo"
            );
            $slctContador->joinInner(
                array("ag" => "Agentes"),
                "ag.CNPJCPF = pr.CgcCpf",
                array(),
                "AGENTES.dbo"
            );
            $slctContador->joinInner(
                array("nm" => "Nomes"),
                "nm.idAgente = ag.idAgente",
                array(),
                "AGENTES.dbo"
            );
            $slctContador->joinLeft(
                array("vp" => "tbVerificaProjeto"),
                "vp.IdPRONAC = pr.IdPRONAC",
                array(),
                "SAC.dbo"
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctContador)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
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

    public function buscarParecer($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("pr" => $this->_name),
            array('pronac' => new Zend_Db_Expr('pr.AnoProjeto + pr.Sequencial'),
                'pr.NomeProjeto',
                'pr.IdPRONAC',
                'pr.CgcCpf',
                'pr.idpronac',
                'pr.Area as cdarea',
                'pr.ResumoProjeto',
                'pr.UfProjeto',
                'pr.DtInicioExecucao',
                'pr.Processo',
                'pr.Mecanismo',
                'pr.DtFimExecucao',
                'DtInicioCaptacao' => new Zend_Db_Expr("dateadd(day,1,getdate())"),
                'DtFimCaptacao' => new Zend_Db_Expr("case when CONVERT(char(10),pr.DtFimExecucao,111) <= CONVERT(char(4),year(getdate())) + '/12/31' then pr.DtFimExecucao else CONVERT(char(4),year(getdate())) + '/12/31' end")
            ),
            "SAC.dbo"
        );

        $slct->joinInner(
            array("ar" => "Area"),
            "ar.Codigo = pr.Area",
            array("ar.Descricao as area"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array("seg" => "Segmento"),
            "seg.Codigo = pr.Segmento",
            array('seg.Descricao as segmento'),
            "SAC.dbo"
        );

        $slct->where('pr.IdPRONAC = ?', $idPronac);


        return $this->fetchAll($slct);
    }

    public function BuscarPrestacaoContas($Situacao, $tamanho = -1, $inicio = -1, $arrSituacao = null, $arrNotSituacao = null)
    {
        $select = $this->select();

        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->_name),
            array(
                'p.AnoProjeto', 'p.Sequencial', 'p.UfProjeto', 'p.NomeProjeto', 'p.IdPRONAC', 'p.Situacao', 'p.Mecanismo', 'p.NomeProjeto', 'p.idProjeto'
            )
        );

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array('i.CgcCPf'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao as Area'),
            'SAC.dbo'
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao as Segmento'),
            'SAC.dbo'
        );

        $select->joinLeft(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac',
            array('e.idEncPrestContas'),
            'BDCORPORATIVO.scSAC'
        );

        $select->where('p.Situacao = ?', $Situacao);

        if (!empty($arrSituacao)) {
            $select->where('idEncPrestContas in (?)', $arrSituacao);
        }

        if (!empty($arrNotSituacao)) {
            $select->where('idEncPrestContas not in (?)', $arrNotSituacao);
        }

        $this->_total = $this->fetchAll($select)->count();

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }
        xd($select->assemble());

        return $this->fetchAll($select);
    }

    public function buscarProjetosPrestacaoContas_old($where, $tamanho = -1, $inicio = -1, $order = array())
    {
        $select = $this->select();


        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->_name),
            array(
                '(p.AnoProjeto+p.Sequencial) as pronac', 'p.AnoProjeto', 'p.Sequencial', 'p.UfProjeto', 'p.NomeProjeto', 'p.IdPRONAC', 'p.DtSituacao', 'p.OrgaoOrigem'
            )
        );

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array('i.CgcCPf'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao as Area'),
            'SAC.dbo'
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao as Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao as Mecanismo'),
            'SAC.dbo'
        );

        $select->joinLeft(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac',
            array('e.idEncPrestContas',
                'e.dtInicioEncaminhamento',
                'e.dtFimEncaminhamento'),
            'BDCORPORATIVO.scSAC'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $this->_total = $this->fetchAll($select)->count();

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        //adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    }

    //metodo descontinuado (mantido para posteriores confirmacoes ate que o modulo seja homologado)
    public function buscaProjetoDiligenciadosPrestacaoContas_old($arrWhere)
    {
        $select = $this->select();


        $select->setIntegrityCheck(false);


        $select->from(
            array('p' => $this->_name),
            array(
                'p.AnoProjeto', 'p.Sequencial', 'p.UfProjeto', 'p.NomeProjeto', 'p.IdPRONAC', 'd.idSolicitante', 'p.Mecanismo',
                'p.OrgaoOrigem', 'd.DtSolicitacao' => new Zend_Db_Expr('max(d.DtSolicitacao)'), 'p.DtSituacao' => new Zend_Db_Expr('max(p.DtSituacao)')
            )
        );
        $select->group('p.AnoProjeto');
        $select->group('p.Sequencial');
        $select->group('p.UfProjeto');
        $select->group('p.NomeProjeto');
        $select->group('p.IdPRONAC');
        $select->group('d.idSolicitante');
        $select->group('p.OrgaoOrigem');
        $select->group('p.CgcCPf');
        $select->group('i.CgcCPf');
        $select->group('a.Codigo');
        $select->group('p.Segmento');
        $select->group('p.Mecanismo');
        $select->group('m.Descricao');
        $select->group('a.Descricao');
        $select->group('s.Descricao');
        $select->group('d.DtSolicitacao');
        $select->group('e.idEncPrestContas');
        $select->group('e.dtInicioEncaminhamento');
        $select->group('e.dtFimEncaminhamento');

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array('i.CgcCPf'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao as Area'),
            'SAC.dbo'
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao as Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao as Mecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('d' => 'tbDiligencia'),
            'p.IdPRONAC = d.IdPRONAC',
            array('d.DtSolicitacao as DtSolicitacao', 'd.idSolicitante'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac',
            array('e.idEncPrestContas', 'e.dtInicioEncaminhamento', 'e.dtFimEncaminhamento'),
            'BDCORPORATIVO.scSAC'
        );
        //adiciona quantos filtros foram enviados
        foreach ($arrWhere as $coluna => $valor) {
            $select->where($coluna, $valor);
        }


        return $this->fetchAll($select);
    }

    public function buscarTodosDadosProjeto($idpronac = null, $CpfCnpj = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pr' => $this->_name),
            array(
                '*',
                '(pr.AnoProjeto+pr.Sequencial) as pronac',
                'pr.IdPRONAC',
                'pr.CgcCpf',
                'pr.idProjeto',
                'pr.NomeProjeto',
                'pr.ResumoProjeto',
                'pr.UfProjeto',
                'pr.Processo',
                new Zend_Db_Expr('CAST(pr.ProvidenciaTomada AS TEXT) AS ProvidenciaTomada'),
                'pr.Localizacao',
                'pr.SolicitadoReal',
                'pr.dtSituacao'
            )
        );
        $select->joinInner(
            array('mc' => 'Mecanismo'),
            "mc.Codigo = pr.Mecanismo",
            array('mc.Descricao as dsMecanismo')
        );
        $select->joinInner(
            array('ar' => 'Area'),
            "ar.Codigo = pr.Area",
            array('ar.descricao as dsArea')
        );
        $select->joinInner(
            array('sg' => 'Segmento'),
            "sg.Codigo = pr.Segmento",
            array('sg.descricao as dsSegmento')
        );
        $select->joinLeft(
            array('enq' => 'Enquadramento'),
            "enq.IdPRONAC = pr.IdPRONAC",
            array('enq.Enquadramento', 'enq.dtEnquadramento', 'IdEnquadramento')
        );
        $select->joinInner(
            array("st" => 'Situacao'),
            "st.Codigo = pr.Situacao",
            array(
                "st.Descricao as dsSituacao",
            )
        );
        $select->joinLeft(
            array("hb" => 'Inabilitado'),
            "hb.CgcCpf = pr.CgcCpf and
                          hb.AnoProjeto = pr.AnoProjeto and
                          hb.Sequencial = pr.Sequencial",
            array(
                "hb.Habilitado", "hb.idTipoInabilitado"
            )
        );
        $select->joinLeft(
            array('tbRel' => 'tbRelatorio'),
            'tbRel.idPRONAC = pr.IdPRONAC',
            array('tbRel.idRelatorio'),
            'SAC.dbo'
        );

        if ($CpfCnpj != null) {
            $select->where("pr.CgcCpf = ?", $CpfCnpj);
        } else {
            $select->where("pr.IdPRONAC = ?", $idpronac);
        }
        $select->order("tbRel.idRelatorio desc");

        return $this->fetchAll($select);
    }

    public function VerificaPronac($where)
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(array('tbr' => $this->_name));


        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }


        return $this->fetchAll($slct);
    }

    public function buscarTodosDadosProjeto2($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pr' => $this->_name),
            array(
                'SAC.dbo.fnchecarDiligencia(pr.IdPRONAC) AS Diligencia',
                'pr.AnoProjeto',
                'pr.Sequencial',
                '(pr.AnoProjeto+pr.Sequencial) as pronac',
                'pr.IdPRONAC',
                'pr.CgcCpf',
                'pr.idProjeto',
                'pr.NomeProjeto',
                'pr.ResumoProjeto',
                'pr.UfProjeto',
                'pr.Processo',
                'pr.ProvidenciaTomada',
                'pr.Localizacao',
                'pr.SolicitadoReal',
                'pr.dtSituacao',
                'pr.Situacao',
                'pr.DtInicioExecucao',
                'pr.DtFimExecucao',
                new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia as dil where dil.idPronac = pr.idPronac) as stEnviado')
            )
        );
        $select->joinInner(
            array('mc' => 'Mecanismo'),
            "mc.Codigo = pr.Mecanismo",
            array('mc.Descricao as dsMecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('ar' => 'Area'),
            "ar.Codigo = pr.Area",
            array('ar.descricao as dsArea'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sg' => 'Segmento'),
            "sg.Codigo = pr.Segmento",
            array('sg.descricao as dsSegmento'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('enq' => 'Enquadramento'),
            "enq.IdPRONAC = pr.IdPRONAC",
            array('enq.Enquadramento', 'enq.dtEnquadramento', 'IdEnquadramento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array("st" => 'Situacao'),
            "st.Codigo = pr.Situacao",
            array(
                "st.Descricao as dsSituacao",
            ),
            'SAC.dbo'
        );
        $select->joinLeft(
            array("hb" => 'Inabilitado'),
            "hb.CgcCpf = pr.CgcCpf and
                          hb.AnoProjeto = pr.AnoProjeto and
                          hb.Sequencial = pr.Sequencial",
            array(
                "hb.Habilitado"
            ),
            'SAC.dbo'
        );

        $select->joininner(
            array("rel" => 'tbRelatorio'),
            "rel.idPronac = pr.idPronac",
            array(),
            'SAC.dbo'
        );
        $select->joinLeft(
            array("relc" => 'tbRelatorioConsolidado'),
            "relc.idRelatorio = rel.idRelatorio",
            array(
                "relc.dtCadastro"
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array("org" => 'Orgaos'),
            "org.org_codigo = pr.Orgao",
            array('org.org_superior'),
            'TABELAS.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
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

    public function pegaTotalBuscarTodosDadosProjeto2($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pr' => $this->_name),
            array("count(*) as total")
        );
        $select->joinInner(
            array('mc' => 'Mecanismo'),
            "mc.Codigo = pr.Mecanismo",
            array("")
        );
        $select->joinInner(
            array('ar' => 'Area'),
            "ar.Codigo = pr.Area",
            array("")
        );
        $select->joinInner(
            array('sg' => 'Segmento'),
            "sg.Codigo = pr.Segmento",
            array("")
        );
        $select->joinLeft(
            array('enq' => 'Enquadramento'),
            "enq.IdPRONAC = pr.IdPRONAC",
            array("")
        );
        $select->joinInner(
            array("st" => 'Situacao'),
            "st.Codigo = pr.Situacao",
            array("")
        );
        $select->joinLeft(
            array("hb" => 'Inabilitado'),
            "hb.CgcCpf = pr.CgcCpf and
                          hb.AnoProjeto = pr.AnoProjeto and
                          hb.Sequencial = pr.Sequencial",
            array("")
        );
        $select->joinLeft(
            array("dil" => 'tbDiligencia'),
            "dil.idPronac = pr.idPronac",
            array("")
        );
        $select->joinLeft(
            array("rel" => 'tbRelatorio'),
            "rel.idPronac = pr.idPronac",
            array("")
        );
        $select->joinInner(
            array("org" => 'Orgaos'),
            "org.org_codigo = pr.Orgao",
            array(),
            'TABELAS.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }


        return $this->fetchAll($select)->current()->total;
    }

    public function salvar($dados)
    {
        //INSTANCIANDO UM OBJETO DE ACESSO AOS DADOS DA TABELA

        $tmpTbl = new Projetos();

        $tmpTbl = $tmpTbl->find($dados['idPRONAC'])->current();

        //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS

        if (isset($dados['Area'])) {
            $tmpTbl->Area = $dados['Area'];
        }
        if (isset($dados['Segmento'])) {
            $tmpTbl->Segmento = $dados['Segmento'];
        }
        if (isset($dados['NomeProjeto'])) {
            $tmpTbl->NomeProjeto = $dados['NomeProjeto'];
        }
        if (isset($dados['ResumoProjeto'])) {
            $tmpTbl->ResumoProjeto = $dados['ResumoProjeto'];
        }
        if (isset($dados['Situacao'])) {
            $tmpTbl->Situacao = $dados['Situacao'];
        }
        if (isset($dados['Orgao'])) {
            $tmpTbl->Orgao = $dados['Orgao'];
        }
        if (isset($dados['DtInicioExecucao'])) {
            $tmpTbl->DtInicioExecucao = $dados['DtInicioExecucao'];
        }
        if (isset($dados['DtFimExecucao'])) {
            $tmpTbl->DtFimExecucao = $dados['DtFimExecucao'];
        }
        if (isset($dados['idEnquadramento'])) {
            $tmpTbl->idEnquadramento = $dados['idEnquadramento'];
        }
        if (isset($dados['ProvidenciaTomada'])) {
            $tmpTbl->ProvidenciaTomada = $dados['ProvidenciaTomada'];
        }
        if (isset($dados['CgcCpf'])) {
            $tmpTbl->CgcCpf = $dados['CgcCpf'];
        }
        $id = $tmpTbl->save();

        if ($id) {
            return $id;
        } else {
            return false;
        }
    }


    public function buscarDadosParaImpressao($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(
            array('pr' => $this->_name),
            array('*',
                'dbo.fnFormataProcesso(pr.idPronac) as Processo')
        );

        $slct->joinInner(
            array('ar' => 'Area'),
            "ar.Codigo = pr.Area",
            array('ar.descricao as dsArea')
        );
        $slct->joinInner(
            array('sg' => 'Segmento'),
            "sg.Codigo = pr.Segmento",
            array('sg.descricao as dsSegmento')
        );
        $slct->joinInner(
            array('mc' => 'Mecanismo'),
            "mc.Codigo = pr.Mecanismo",
            array('mc.Descricao as dsMecanismo')
        );
        $slct->joinInner(
            array('i' => 'Interessado'),
            "pr.CgcCPf = i.CgcCPf"
        );

        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    public function buscarPareceresProjetoParaImpressao($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(
            array('p' => $this->_name),
            array('p.IdPRONAC',
                '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                'p.NomeProjeto',
                'dbo.fnFormataProcesso(p.idPronac) as Processo')
        );

        $slct->joinInner(
            array('i' => 'Interessado'),
            "p.CgcCPf = i.CgcCPf",
            array('i.Nome AS Proponente')
        );

        $slct->joinInner(
            array('a' => 'tbAnaliseDeConteudo'),
            "p.IdPRONAC = a.idPronac",
            array("a.idProduto",
                "Lei8313" => new Zend_Db_Expr("CASE WHEN Lei8313 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "Artigo3" => new Zend_Db_Expr("CASE WHEN Artigo3 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo3" => new Zend_Db_Expr("CASE WHEN IncisoArtigo3 = 1 THEN 'I' WHEN IncisoArtigo3 = 2 THEN 'II' WHEN IncisoArtigo3 = 3 THEN 'III' WHEN IncisoArtigo3 = 4 THEN 'IV' WHEN IncisoArtigo3 = 5 THEN 'V' END"),
                "a.AlineaArtigo3",
                "Artigo18" => new Zend_Db_Expr("CASE WHEN Artigo18 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "a.AlineaArtigo18",
                "Artigo26" => new Zend_Db_Expr("CASE WHEN Artigo26 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "Lei5761" => new Zend_Db_Expr("CASE WHEN Lei5761 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "Artigo27" => new Zend_Db_Expr("CASE WHEN Artigo27 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo27_I" => new Zend_Db_Expr("CASE WHEN IncisoArtigo27_I = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo27_II" => new Zend_Db_Expr("CASE WHEN IncisoArtigo27_II = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo27_III" => new Zend_Db_Expr("CASE WHEN IncisoArtigo27_III = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo27_IV" => new Zend_Db_Expr("CASE WHEN IncisoArtigo27_IV = 1 THEN 'Sim' ELSE 'N�o' END"),
                "TipoParecer" => new Zend_Db_Expr("CASE WHEN TipoParecer = 1 THEN 'Aprova��o' WHEN TipoParecer = 2 THEN 'Complementa��o' WHEN TipoParecer = 4 THEN 'Redu��o' END"),
                "ParecerFavoravel" => new Zend_Db_Expr("CASE WHEN ParecerFavoravel = 1 THEN 'Sim' ELSE 'N�o' END"),
                "a.ParecerDeConteudo",
                "sac.dbo.fnNomeParecerista(a.idUsuario) AS Parecerista",
            )
        );


        $slct->joinInner(
            array('pr' => 'Produto'),
            "a.idProduto = pr.Codigo",
            array('pr.Descricao AS Produto')
        );

        $slct->joinInner(
            array('dp' => 'tbDistribuirParecer'),
            "dp.idPronac = a.idPronac AND dp.idProduto = a.idProduto",
            array(new Zend_Db_Expr('CONVERT(CHAR(23), dp.DtDevolucao, 120) AS DtDevolucao'),
                new Zend_Db_Expr('CONVERT(CHAR(23), dp.DtRetorno, 120) AS DtRetorno', 'dp.idOrgao'))
        );

        $slct->joinLeft(
            array('u' => 'Usuarios'),
            "dp.idusuario = u.usu_codigo",
            array('u.usu_nome AS Coordenador',
                'u.usu_identificacao  AS cpfCoordenador'),
            'TABELAS.dbo'
        );

        $slct->joinInner(
            array('b' => 'Nomes'),
            "dp.idAgenteParecerista = b.idAgente",
            array(),
            $this->getSchema('agentes')
        );

        $slct->joinInner(
            array('h' => 'Agentes'),
            "h.idAgente = b.idAgente",
            array('h.CNPJCPF as cpfParecerista'),
            $this->getSchema('agentes')
        );

        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    public function vwAnaliseConteudo($where = array())
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(
            array('p' => $this->_name),
            array('p.IdPRONAC',
                '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                'p.NomeProjeto',
                'dbo.fnFormataProcesso(p.idPronac) as Processo')
        );

        $slct->joinInner(
            array('i' => 'Interessado'),
            "p.CgcCpf = i.CgcCpf",
            array('i.Nome AS Proponente')
        );

        $slct->joinInner(
            array('a' => 'tbAnaliseDeConteudo'),
            "p.IdPRONAC = a.idPronac",
            array("a.idProduto",
                "Lei8313" => new Zend_Db_Expr("CASE WHEN Lei8313 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "Artigo3" => new Zend_Db_Expr("CASE WHEN Artigo3 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo3" => new Zend_Db_Expr("CASE WHEN IncisoArtigo3 = 1 THEN 'I' WHEN IncisoArtigo3 = 2 THEN 'II' WHEN IncisoArtigo3 = 3 THEN 'III' WHEN IncisoArtigo3 = 4 THEN 'IV' WHEN IncisoArtigo3 = 5 THEN 'V' END"),
                "a.AlineaArtigo3",
                "Artigo18" => new Zend_Db_Expr("CASE WHEN Artigo18 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "a.AlineaArtigo18",
                "Artigo26" => new Zend_Db_Expr("CASE WHEN Artigo26 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "Lei5761" => new Zend_Db_Expr("CASE WHEN Lei5761 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "Artigo27" => new Zend_Db_Expr("CASE WHEN Artigo27 = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo27_I" => new Zend_Db_Expr("CASE WHEN IncisoArtigo27_I = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo27_II" => new Zend_Db_Expr("CASE WHEN IncisoArtigo27_II = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo27_III" => new Zend_Db_Expr("CASE WHEN IncisoArtigo27_III = 1 THEN 'Sim' ELSE 'N�o' END"),
                "IncisoArtigo27_IV" => new Zend_Db_Expr("CASE WHEN IncisoArtigo27_IV = 1 THEN 'Sim' ELSE 'N�o' END"),
                "TipoParecer" => new Zend_Db_Expr("CASE WHEN TipoParecer = 1 THEN 'Aprova��o' WHEN TipoParecer = 2 THEN 'Complementa��o' WHEN TipoParecer = 4 THEN 'Redu��o' END"),
                "ParecerFavoravel" => new Zend_Db_Expr("CASE WHEN ParecerFavoravel = 1 THEN 'Sim' ELSE 'N�o' END"),
                "a.ParecerDeConteudo",
                new Zend_Db_Expr("sac.dbo.fnNomeParecerista(a.idUsuario) AS Parecerista"),
            )
        );


        $slct->joinInner(
            array('pr' => 'Produto'),
            "a.idProduto = pr.Codigo",
            array('pr.Descricao AS Produto')
        );


        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }


        return $this->fetchAll($slct);
    }

    /**
     * Metodo para buscar o per?odo de execu??o de um determinado projeto
     * @access public
     * @param integer $idPronac
     * @param string $pronac
     * @param string $data
     * @return array
     */
    public function buscarPeriodoExecucao($idPronac = null, $pronac = null, $data = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            $this->_name,
            array(new Zend_Db_Expr("DATEDIFF(DAY, DtInicioExecucao, DtFimExecucao) AS qtdDias")
            , new Zend_Db_Expr("CONVERT(CHAR(10), DtInicioExecucao, 103) AS DtInicioExecucao")
            , new Zend_Db_Expr("CONVERT(CHAR(10), DtFimExecucao, 103) AS DtFimExecucao"))
        );

        // busca pelo $idPronac
        if (!empty($idPronac)) {
            $select->where("IdPRONAC = ?", $idPronac);
        }

        // busca pelo pronac
        if (!empty($pronac)) {
            $select->where("(AnoProjeto+Sequencial) = ?", $pronac);
        }

        // busca pela data (verifica se a data est? entre o per?odo de execu??o)
        if (!empty($data)) {
            $select->where("? >= DtInicioExecucao", $data);
            $select->where("? <= DtFimExecucao", $data);
        }

        $select->order("IdPRONAC DESC");

        return $this->fetchRow($select);
    }

    // fecha metodo buscarPeriodoExecucao()

    /**
     * Método que retorna se o período de um projeto está vigente ou não
     * @param integer $idPronac
     * @return bool
     */
    public function verificarPeriodoExecucaoVigente($idPronac)
    {
        $periodoExecucao = $this->buscarPeriodoExecucao($idPronac);
        $dataCorrente = strtotime(date('d-m-Y'));

        $periodoExecucaoVigente = (
            strtotime(
                str_replace(
                    '/',
                    '-',
                    $periodoExecucao->DtInicioExecucao
                )
            ) <= $dataCorrente
            &&
            strtotime(
                str_replace(
                    '/',
                    '-',
                    $periodoExecucao->DtFimExecucao
                )
            ) >= $dataCorrente
        ) ? true : false;

        return $periodoExecucaoVigente;
    }

    /**
     * Metodo para buscar o per?odo de capta??o de um determinado projeto
     * @access public
     * @param integer $idPronac
     * @param string $pronac
     * @param string $data
     * @param boolean $buscarTodos (informa se busca todos ou somente um)
     * @return array
     */
    public function buscarPeriodoCaptacao($idPronac = null, $pronac = null, $data = null, $buscarTodos = true, $where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            "Aprovacao",
            array(new Zend_Db_Expr("DATEDIFF(DAY, DtInicioCaptacao, DtFimCaptacao) AS qtdDias")
            , new Zend_Db_Expr("CONVERT(CHAR(10), DtInicioCaptacao, 103) AS DtInicioCaptacao")
            , new Zend_Db_Expr("CONVERT(CHAR(10), DtFimCaptacao, 103) AS DtFimCaptacao"))
        );

        // busca pelo $idPronac
        if (!empty($idPronac)) {
            $select->where("IdPRONAC = ?", $idPronac);
        }

        // busca pelo pronac
        if (!empty($pronac)) {
            $select->where("(AnoProjeto+Sequencial) = ?", $pronac);
        }

        // busca pela data (verifica se a data est? entre o per?odo de execu??o)
        if (!empty($data)) {
            $select->where("? >= DtInicioCaptacao", $data);
            $select->where("? <= DtFimCaptacao", $data);
        }

        foreach ($where as $key => $val) {
            $select->where($key, $val);
        }

        $select->order("idAprovacao DESC");

        return $buscarTodos ? $this->fetchAll($select) : $this->fetchRow($select);
    }

    // fecha metodo buscarPeriodoCaptacao()

    public function buscaProjetosProdutosAnaliseInicial($where, $qtdTotal = false, $tamanho = -1, $inicio = -1)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
        } else {
            $tmpInicio = null;
        }
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $selectQtdTotal = $this->select();
        $selectQtdTotal->setIntegrityCheck(false);
        if ($qtdTotal) {
            $select->from(
                array('p' => $this->_name),
                array('p.IdPRONAC', 'p.situacao', 'p.Orgao as idOrgao', 'd.idProduto', 'd.stPrincipal'
                )
            );
            $select->joinInner(array('d' => 'tbDistribuirParecer'), 'p.idPronac = d.idPronac', array());
            $select->joinLeft(array('pr' => 'Produto'), 'd.idProduto = pr.Codigo', array());
        } else {
            if ($inicio < 0) {
                $select->from(
                    array('p' => $this->_name),
                    array(
                        'p.IdPRONAC',
                        'p.AnoProjeto',
                        'p.Sequencial',
                        'p.Area',
                        'p.Segmento',
                        '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                        'p.NomeProjeto as NomeProjeto',
                        new Zend_Db_Expr('CONVERT(CHAR(10), p.DtAnalise, 103)  AS DtAnalise'),
                        'p.situacao as situacao',
                        'p.Orgao as idOrgao',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                        "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
                    )
                );
            } else {
                $soma = $tamanho + $tmpInicio;
                $select->from(
                    array('p' => $this->_name),
                    array(new Zend_Db_Expr("TOP $soma  p.IdPRONAC"),
                        '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                        'p.NomeProjeto as NomeProjeto',
                        'p.Area',
                        'p.Segmento',
                        new Zend_Db_Expr('CONVERT(CHAR(10), p.DtAnalise, 103)  AS DtAnalise'),
                        'p.situacao as situacao',
                        'p.Orgao as idOrgao',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                        "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
                    )
                );
            }

            $select->joinInner(array('d' => 'tbDistribuirParecer'), 'p.idPronac = d.idPronac', array(
                'd.idProduto',
                'd.stPrincipal',
                'd.idDistribuirParecer',
                'd.TipoAnalise',
                "d.DtDistribuicao",
                "d.stDiligenciado",
                "d.DtDevolucao",
                new Zend_Db_Expr("CONVERT(CHAR(10), d.DtEnvio, 103) as DtEnvio"),
                "d.idAgenteParecerista",
                'tempoFimParecer' => new Zend_Db_Expr('CASE WHEN d.stPrincipal = 1 THEN 20 ELSE 10 END'),
            ));
//            $select->joinLeft(array('ac'=>'tbAnaliseDeConteudo'), 'ac.idPRONAC = p.IdPRONAC  and ac.idProduto = d.idProduto',array('ac.ParecerFavoravel'),'SAC.dbo');

            $select->joinLeft(array('pr' => 'Produto'), 'd.idProduto = pr.Codigo', array('pr.Descricao as dsProduto'));
            $select->joinInner(array('ar' => 'Area'), 'p.Area = ar.Codigo', array('ar.Descricao as dsArea'));
            $select->joinInner(array('sg' => 'Segmento'), 'p.Segmento = sg.Codigo', array('sg.Descricao as dsSegmento'));

            $select->order('p.IdPRONAC');
            $select->order('d.idProduto');
        }


        $select->where('Situacao in (?)', array('B11', 'B14'));
        $select->where('d.stEstado = ?', 0);
        foreach ($where as $key => $val) {
            $select->where($key, $val);
        }

        /* Consultas auxiliares 1 */
        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from($select, array(new Zend_Db_Expr("TOP $tamanho  *")));
        $selectAux->order('IdPRONAC DESC');
        $selectAux->order('idProduto DESC');

        /* Consultas auxiliares 2 */
        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order('IdPRONAC');
        $selectAux2->order('idProduto');
        if ($qtdTotal) {
            $selectQtdTotal->from(array('t2' => $select), array("total" => new Zend_Db_Expr("count(*)")));
        }

        /* Resultado */
        if ($qtdTotal || $tmpInicio <= 0 && $qtdTotal || $tamanho == -1) {
            if ($qtdTotal) {
                return $this->fetchAll($selectQtdTotal);
            } else {
                return $this->fetchAll($select);
            }
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    public function buscaProjetosProdutosReadequacao($where, $qtdTotal = false, $tamanho = -1, $inicio = -1)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
        } else {
            $tmpInicio = null;
        }
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $selectQtdTotal = $this->select();
        $selectQtdTotal->setIntegrityCheck(false);
        if ($qtdTotal) {
            $select->from(
                array('p' => $this->_name),
                array('p.IdPRONAC', 'p.situacao', 'p.Orgao as idOrgao', 'd.idProduto', 'd.stPrincipal'
                )
            );
            $select->joinInner(array('d' => 'tbDistribuirParecer'), 'p.idPronac = d.idPronac', array());
            $select->joinLeft(array('pr' => 'Produto'), 'd.idProduto = pr.Codigo', array());
        } else {
            if ($inicio < 0) {
                $select->from(
                    array('p' => $this->_name),
                    array(
                        'p.IdPRONAC',
                        '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                        'p.NomeProjeto as NomeProjeto',
                        new Zend_Db_Expr('CONVERT(CHAR(10), p.DtAnalise, 103)  AS DtAnalise'),
                        'p.situacao as situacao',
                        'p.Orgao as idOrgao',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                        "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
                    )
                );
            } else {
                $soma = $tamanho + $tmpInicio;
                $select->from(
                    array('p' => $this->_name),
                    array(new Zend_Db_Expr("TOP $soma  p.IdPRONAC"),
                        '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                        'p.NomeProjeto as NomeProjeto',
                        new Zend_Db_Expr('CONVERT(CHAR(10), p.DtAnalise, 103)  AS DtAnalise'),
                        'p.situacao as situacao',
                        'p.Orgao as idOrgao',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                        "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
                    )
                );
            }

            $select->joinInner(array('d' => 'tbDistribuirParecer'), 'p.idPronac = d.idPronac', array(
                'd.idProduto',
                'd.stPrincipal',
                "d.DtDistribuicao",
                "d.stDiligenciado",
                "d.DtDevolucao",
                new Zend_Db_Expr("CONVERT(CHAR(10), d.DtEnvio, 103) as )DtEnvio"),
                "d.idAgenteParecerista",
                'tempoFimParecer' => new Zend_Db_Expr('CASE WHEN d.stPrincipal = 1 THEN 20 ELSE 10 END'),
            ));
//            $select->joinLeft(array('ac'=>'tbAnaliseDeConteudo'), 'ac.idPRONAC = p.IdPRONAC  and ac.idProduto = d.idProduto',array('ac.ParecerFavoravel'),'SAC.dbo');

            $select->joinLeft(array('pr' => 'Produto'), 'd.idProduto = pr.Codigo', array('pr.Descricao as dsProduto'));

            $select->order('p.IdPRONAC');
            $select->order('d.idProduto');
        }

        $select->where('d.DtDistribuicao is not null');
        $select->where('d.DtDevolucao is NULL');
        $select->where('Situacao in (?)', array('E10', 'E11', 'E12', 'E13', 'E15', 'E16'));
        $select->where('d.stEstado = ?', 0);
        foreach ($where as $key => $val) {
            $select->where($key, $val);
        }

        /* Consultas auxiliares 1 */
        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from($select, array(new Zend_Db_Expr("TOP $tamanho  *")));
        $selectAux->order('IdPRONAC DESC');
        $selectAux->order('idProduto DESC');

        /* Consultas auxiliares 2 */
        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order('IdPRONAC');
        $selectAux2->order('idProduto');
        if ($qtdTotal) {
            $selectQtdTotal->from(array('t2' => $select), array("total" => new Zend_Db_Expr("count(*)")));
        }

        /* Resultado */
        if ($qtdTotal || $tmpInicio <= 0 && $qtdTotal || $tamanho == -1) {
            if ($qtdTotal) {
                return $this->fetchAll($selectQtdTotal);
            } else {
                return $this->fetchAll($select);
            }
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    public function buscaProjetosProdutosExecucaoObjeto($where, $qtdTotal = false, $tamanho = -1, $inicio = -1)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
        } else {
            $tmpInicio = null;
        }
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $selectQtdTotal = $this->select();
        $selectQtdTotal->setIntegrityCheck(false);
        if ($qtdTotal) {
            $select->from(
                array('p' => $this->_name),
                array('p.IdPRONAC', 'p.situacao', 'p.Orgao as idOrgao', 'd.idProduto', 'd.stPrincipal'
                )
            );
            $select->joinInner(array('d' => 'tbDistribuirParecer'), 'p.idPronac = d.idPronac', array());
            $select->joinLeft(array('pr' => 'Produto'), 'd.idProduto = pr.Codigo', array());
        } else {
            if ($inicio < 0) {
                $select->from(
                    array('p' => $this->_name),
                    array(
                        'p.IdPRONAC',
                        '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                        'p.NomeProjeto as NomeProjeto',
                        new Zend_Db_Expr('CONVERT(CHAR(10), p.DtAnalise, 103)  AS DtAnalise'),
                        'p.situacao as situacao',
                        'p.Orgao as idOrgao',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                        "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
                    )
                );
            } else {
                $soma = $tamanho + $tmpInicio;
                $select->from(
                    array('p' => $this->_name),
                    array(new Zend_Db_Expr("TOP $soma  p.IdPRONAC"),
                        '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                        'p.NomeProjeto as NomeProjeto',
                        new Zend_Db_Expr('CONVERT(CHAR(10), p.DtAnalise, 103)  AS DtAnalise'),
                        'p.situacao as situacao',
                        'p.Orgao as idOrgao',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                        "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
                    )
                );
            }

            $select->joinInner(array('d' => 'tbDistribuirParecer'), 'p.idPronac = d.idPronac', array(
                'd.idProduto',
                'd.stPrincipal',
                "d.DtDistribuicao",
                "d.stDiligenciado",
                "d.DtDevolucao",
                new Zend_Db_Expr("CONVERT(CHAR(10), d.DtEnvio, 103) as )DtEnvio"),
                "d.idAgenteParecerista",
                'tempoFimParecer' => new Zend_Db_Expr('CASE WHEN d.stPrincipal = 1 THEN 20 ELSE 10 END'),
            ));
//            $select->joinLeft(array('ac'=>'tbAnaliseDeConteudo'), 'ac.idPRONAC = p.IdPRONAC  and ac.idProduto = d.idProduto',array('ac.ParecerFavoravel'),'SAC.dbo');

            $select->joinLeft(array('pr' => 'Produto'), 'd.idProduto = pr.Codigo', array('pr.Descricao as dsProduto'));

            $select->order('p.IdPRONAC');
            $select->order('d.idProduto');
        }

        $select->where('d.DtDistribuicao is not null');
        $select->where('d.DtDevolucao is NULL');
        $select->where('Situacao in (?)', array('E12', 'E13', 'E15', 'E23', 'E60', 'E61', 'E62', 'E66', 'E67', 'E68'));
        $select->where('d.stEstado = ?', 0);
        foreach ($where as $key => $val) {
            $select->where($key, $val);
        }

        /* Consultas auxiliares 1 */
        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from($select, array(new Zend_Db_Expr("TOP $tamanho  *")));
        $selectAux->order('IdPRONAC DESC');
        $selectAux->order('idProduto DESC');

        /* Consultas auxiliares 2 */
        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order('IdPRONAC');
        $selectAux2->order('idProduto');
        if ($qtdTotal) {
            $selectQtdTotal->from(array('t2' => $select), array("total" => new Zend_Db_Expr("count(*)")));
        }


        /* Resultado */
        if ($qtdTotal || $tmpInicio <= 0 && $qtdTotal || $tamanho == -1) {
            if ($qtdTotal) {
                return $this->fetchAll($selectQtdTotal);
            } else {
                return $this->fetchAll($select);
            }
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    public function buscaProjetosProdutosPrestacaoContas($where, $qtdTotal = false, $tamanho = -1, $inicio = -1)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
        } else {
            $tmpInicio = null;
        }
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $selectQtdTotal = $this->select();
        $selectQtdTotal->setIntegrityCheck(false);
        if ($qtdTotal) {
            $select->from(
                array('p' => $this->_name),
                array('p.IdPRONAC', 'p.situacao', 'p.Orgao as idOrgao', 'd.idProduto', 'd.stPrincipal'
                )
            );
            $select->joinInner(array('d' => 'tbDistribuirParecer'), 'p.idPronac = d.idPronac', array());
            $select->joinLeft(array('pr' => 'Produto'), 'd.idProduto = pr.Codigo', array());
        } else {
            if ($inicio < 0) {
                $select->from(
                    array('p' => $this->_name),
                    array(
                        'p.IdPRONAC',
                        '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                        'p.NomeProjeto as NomeProjeto',
                        new Zend_Db_Expr('CONVERT(CHAR(10), p.DtAnalise, 103)  AS DtAnalise'),
                        'p.situacao as situacao',
                        'p.Orgao as idOrgao',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                        "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
                    )
                );
            } else {
                $soma = $tamanho + $tmpInicio;
                $select->from(
                    array('p' => $this->_name),
                    array(new Zend_Db_Expr("TOP $soma  p.IdPRONAC"),
                        '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                        'p.NomeProjeto as NomeProjeto',
                        new Zend_Db_Expr('CONVERT(CHAR(10), p.DtAnalise, 103)  AS DtAnalise'),
                        'p.situacao as situacao',
                        'p.Orgao as idOrgao',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac and dili1.idProduto = d.idProduto order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac and dili2.idProduto = d.idProduto order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac and dili3.idProduto = d.idProduto order by dili3.DtSolicitacao desc)'),
                        "tempoFimDiligencia" => new Zend_Db_Expr("(select top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia from tbDiligencia dili4 where dili4.idPronac = p.idPronac and dili4.idProduto = d.idProduto order by dili4.DtSolicitacao desc)")
                    )
                );
            }

            $select->joinInner(array('d' => 'tbDistribuirParecer'), 'p.idPronac = d.idPronac', array(
                'd.idProduto',
                'd.stPrincipal',
                "d.DtDistribuicao",
                "d.stDiligenciado",
                "d.DtDevolucao",
                new Zend_Db_Expr("CONVERT(CHAR(10), d.DtEnvio, 103) as )DtEnvio"),
                "d.idAgenteParecerista",
                'tempoFimParecer' => new Zend_Db_Expr('CASE WHEN d.stPrincipal = 1 THEN 20 ELSE 10 END'),
            ));
//            $select->joinLeft(array('ac'=>'tbAnaliseDeConteudo'), 'ac.idPRONAC = p.IdPRONAC  and ac.idProduto = d.idProduto',array('ac.ParecerFavoravel'),'SAC.dbo');

            $select->joinLeft(array('pr' => 'Produto'), 'd.idProduto = pr.Codigo', array('pr.Descricao as dsProduto'));

            $select->order('p.IdPRONAC');
            $select->order('d.idProduto');
        }

        $select->where('d.DtDistribuicao is not null');
        $select->where('d.DtDevolucao is NULL');
        $select->where('Situacao in (?)', array('E17', 'E18', 'E19', 'E22', 'E23', 'E24', 'E27', 'E30', 'E46',
            'G20', 'G21', 'G22', 'G24', 'G43', 'G47', 'L07', 'G19', 'G25',
            'L02'/* L02 ou L03 Falta defini??o do Gestor */
//          'L03'/* L02 ou L03 Falta defini??o do Gestor */
        ));
        $select->where('d.stEstado = ?', 0);
        foreach ($where as $key => $val) {
            $select->where($key, $val);
        }

        /* Consultas auxiliares 1 */
        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from($select, array(new Zend_Db_Expr("TOP $tamanho  *")));
        $selectAux->order('IdPRONAC DESC');
        $selectAux->order('idProduto DESC');

        /* Consultas auxiliares 2 */
        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order('IdPRONAC');
        $selectAux2->order('idProduto');
        if ($qtdTotal) {
            $selectQtdTotal->from(array('t2' => $select), array("total" => new Zend_Db_Expr("count(*)")));
        }

        /* Resultado */
        if ($qtdTotal || $tmpInicio <= 0 && $qtdTotal || $tamanho == -1) {
            if ($qtdTotal) {
                return $this->fetchAll($selectQtdTotal);
            } else {
                return $this->fetchAll($select);
            }
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    public function dadosProjetoProduto($idpronac, $idproduto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array('SAC.dbo.fnchecarDiligencia(p.IdPRONAC) AS Diligencia',
                'p.IdPRONAC',
                '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                'p.NomeProjeto')
        );

        $select->joinInner(
            array('d' => 'tbDistribuirParecer'),
            'p.IdPRONAC = d.idPRONAC',
            array('d.idDistribuirParecer',
                'd.idProduto',
                'd.idAgenteParecerista',
                'd.idOrgao',
                new Zend_Db_Expr('CONVERT(CHAR(10),d.DtDistribuicao,103) AS DtDistribuicao2'),
                new Zend_Db_Expr('DATEDIFF(day, d.DtDistribuicao, GETDATE()) AS NrDias'),
                'd.Observacao',
                'DescricaoAnalise' => new Zend_Db_Expr('CASE WHEN TipoAnalise = 0 THEN \'Cont?udo\' WHEN TipoAnalise = 1 THEN \'Custo do Produto\' ELSE \'Custo Administrativo\' END'),
                'd.TipoAnalise',
                'AGENTES.dbo.fnNome(d.idAgenteParecerista) AS Parecerista')
        );


        $select->joinInner(
            array('a' => 'Agentes'),
            'd.idAgenteParecerista = a.idAgente',
            array(),
            $this->getSchema('agentes')
        );

        $select->joinInner(
            array('u' => 'Usuarios'),
            'a.CNPJCPF = u.usu_identificacao',
            array('u.usu_codigo'),
            'TABELAS.dbo'
        );

        /* LEFT OUTER JOIN */
        $select->joinLeft(
            array('pr' => 'Produto'),
            'd.idProduto = pr.Codigo',
            array('pr.Descricao AS Produto')
        );


        $dadosWhere = array('d.stEstado = 0',
            'd.DtDistribuicao IS NOT NULL',
            'd.DtDevolucao IS NULL',
            '(p.Situacao = \'B11\') OR (p.Situacao = \'B14\')',
            'p.idpronac = ' . $idpronac,
            'd.idproduto = ' . $idproduto);


        foreach ($dadosWhere as $coluna) {
            $select->where($coluna);
        }

        $select->order('d.DtDistribuicao');


        return $this->fetchAll($select);
    }

    public function enquadramentoProjeto($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array("p" => $this->_name),
            array(
                'p.ResumoProjeto',
                'p.SolicitadoReal AS VlSolicitado',
                new Zend_Db_Expr('sac.dbo.fnOutrasFontes(p.IdPRONAC) AS VlOutrasFontes'),
                new Zend_Db_Expr('sac.dbo.fnValorDaProposta(p.idProjeto) AS VlProjeto'),
                new Zend_Db_Expr('p.SolicitadoReal AS CustoProjeto'),
                new Zend_Db_Expr('dbo.fnOutrasFontes(p.IdPRONAC) AS VlOutrasFontesAprovado'),
                new Zend_Db_Expr('dbo.fnValorDaProposta(p.idProjeto) AS CustoTotal')
            )
        );

        $select->joinLeft(
            array('e' => 'Enquadramento'),
            new Zend_Db_Expr('(p.AnoProjeto = e.AnoProjeto AND p.Sequencial = e.Sequencial)'),
            array(
                new Zend_Db_Expr("
                CASE 
                    WHEN e.Enquadramento = '1' THEN 'Artigo 26' 
                    WHEN e.Enquadramento = '2' THEN 'Artigo 18' 
                    ELSE 'Não enquadrado' END AS Enquadramento"
                )
            )
        );

        $select->where("p.idPronac = ?", $idpronac);

        return $this->fetchAll($select);
    }

    public function assinarParecerTecnico($idpronac)
    {
        $returnData = array();

        $returnData['enquadramento'] = $this->enquadramentoProjeto($idpronac);

        $select3 = $this->select();
        $select3->setIntegrityCheck(false);
        $select3->from(
            array('p' => 'tbAcaoAlcanceProjeto'),
            array(
                'p.dsAcaoAlcanceProduto'
            )
        );
        $select3->where("p.tpAnalise = ?", 1);
        $select3->where("p.idPronac = ?", $idpronac);
        $returnData['alcance'] = $this->fetchAll($select3);

        $select4 = $this->select();
        $select4->setIntegrityCheck(false);
        $select4->from(
            array('p' => 'Parecer'),
            array(
                'p.tpResultado' => new Zend_Db_Expr("
                CASE 
                WHEN ParecerFavoravel = 1 THEN 'Desfavor&aacute;vel' 
                WHEN ParecerFavoravel = 2 THEN 'Favor&aacute;vel' 
                END"),
                'p.ResumoParecer',
                'p.SugeridoReal'
            )
        );
        $select4->where("p.stAtivo = ?", 1);
        $select4->where("p.TipoParecer = ?", 1);
        $select4->where("p.idTipoagente = ?", 1);
        $select4->where("p.idPronac = ?", $idpronac);
        $returnData['parecer'] = $this->fetchAll($select4);

        return $returnData;
    }


    public function assinarApreciacaoCnic($idpronac)
    {
        $returnData = array();

        $returnData['enquadramento'] = $this->enquadramentoProjeto($idpronac);

        $select2 = $this->select();
        $select2->setIntegrityCheck(false);
        $select2->from(
            array('p' => 'vwPlanoDeDistribuicaoProduto'),
            array(
                'p.idProjeto',
                'p.IdPRONAC',
                'p.idProduto',
                'p.Produto',
                'p.tpProduto',
                'p.QtdeProduzida',
                'p.QtdeDistribuicaoGratuita',
                'p.QtdeVendaPopular',
                'p.QtdeVendaProponente',
                'p.PrecoMedio',
                'p.ReceitaTotal',
                'p.vlProduto'
            )
        );
        $select2->where("p.idPronac = ?", $idpronac);
        $returnData['produtos'] = $this->fetchAll($select2);

        $select3 = $this->select();
        $select3->setIntegrityCheck(false);
        $select3->from(
            array('p' => 'Parecer'),
            array(
                'p.tpResultado' => new Zend_Db_Expr("CASE WHEN ParecerFavoravel = 1 THEN 'Desfavor&aacute;vel' WHEN ParecerFavoravel = 2 THEN 'Favor&aacute;vel' END"),
                'p.ResumoParecer'
            )
        );
        $select3->where("p.stAtivo = ?", 1);
        $select3->where("p.TipoParecer = ?", 1);
        $select3->where("p.idTipoagente = ?", 6);
        $select3->where("p.idPronac = ?", $idpronac);
        $returnData['parecer'] = $this->fetchAll($select3);

        return $returnData;
    }

    public function dadosFechar($idAgente, $idpronac, $idDistribuirParecer)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(new Zend_Db_Expr('SAC.dbo.fnchecarDiligencia(p.IdPRONAC) AS Diligencia'),
                'p.IdPRONAC',
                new Zend_Db_Expr('(p.AnoProjeto + p.Sequencial) AS PRONAC'),
                'p.NomeProjeto')
        );

        $select->joinInner(
            array('d' => 'tbDistribuirParecer'),
            'p.IdPRONAC = d.idPRONAC',
            array('d.idDistribuirParecer',
                'd.idProduto',
                'd.idAgenteParecerista',
                'd.idOrgao',
                new Zend_Db_Expr('CONVERT(CHAR(10),d.DtDistribuicao,103) AS DtDistribuicao2'),
                new Zend_Db_Expr('DATEDIFF(day, d.DtDistribuicao, GETDATE()) AS NrDias'),
                'd.Observacao',
                'DescricaoAnalise' => new Zend_Db_Expr('CASE WHEN TipoAnalise = 0 THEN \'Cont?udo\' WHEN TipoAnalise = 1 THEN \'Custo do Produto\' ELSE \'Custo Administrativo\' END'),
                'd.TipoAnalise',
                new Zend_Db_Expr('AGENTES.dbo.fnNome(d.idAgenteParecerista) AS Parecerista'))
        );


        $select->joinInner(
            array('a' => 'Agentes'),
            'd.idAgenteParecerista = a.idAgente',
            array(),
            $this->getSchema('agentes')
        );

        $select->joinInner(
            array('u' => 'Usuarios'),
            'a.CNPJCPF = u.usu_identificacao',
            array('u.usu_codigo'),
            'TABELAS.dbo'
        );


        $select->joinLeft(
            array('pr' => 'Produto'),
            'd.idProduto = pr.Codigo',
            array('pr.Descricao AS Produto'),
            $this->_schema
        );


        $dadosWhere = array(
            'd.stEstado = 0',
            'd.DtDistribuicao IS NOT NULL',
            'd.DtDevolucao IS NULL',
            '(p.Situacao = \'B11\') OR (p.Situacao = \'B14\')',
            'a.idAgente = ' . $idAgente,
//            'u.usu_codigo = ' . $usu_Codigo,
            'p.IdPRONAC = ' . $idpronac,
            'd.idDistribuirParecer = ' . $idDistribuirParecer
        );


        foreach ($dadosWhere as $coluna) {
            $select->where($coluna);
        }

        $select->order('d.DtDistribuicao');

        return $this->fetchAll($select);
    }

    /**
     * @param $idpronac
     * @param null $idItem
     * @return Zend_Db_Table_Rowset_Abstract
     * @deprecated metodo nao utilizado
     */
    public function analiseDeCustos($idpronac, $idItem = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array('p.IdPRONAC',
                '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                'p.NomeProjeto')
        );

        $select->joinInner(
            array('b' => 'tbPlanilhaProjeto'),
            'p.IdPRONAC = b.idPRONAC',
            array('b.Quantidade',
                'b.Ocorrencia',
                'b.ValorUnitario',
                'b.QtdeDias',
                'b.TipoDespesa',
                'b.TipoPessoa',
                'b.Contrapartida',
                'b.idProduto',
                'b.idPlanilhaProjeto',
                'b.idUsuario',
                new Zend_Db_Expr('CAST(b.Justificativa AS TEXT) AS Justificativa'),
                new Zend_Db_Expr('ROUND(b.Quantidade * b.Ocorrencia * b.ValorUnitario, 2) AS Sugerido'),
                'Produto' => new Zend_Db_Expr('CASE WHEN b.idProduto = 0 THEN \'Administra??o do Projeto\' ELSE c.Descricao END'),
                'b.FonteRecurso AS idFonte')
        );


        $select->joinInner(
            array('z' => 'tbPlanilhaProposta'),
            'b.idPlanilhaProposta = z.idPlanilhaProposta',
            array('(z.Quantidade * z.Ocorrencia * z.ValorUnitario) AS VlSolicitado')
        );

        $select->joinLeft(
            array('PAP' => 'tbPlanilhaAprovacao'),
            'PAP.idPlanilhaProposta = z.idPlanilhaProposta',
            array(
                new Zend_Db_Expr('CAST(PAP.dsJustificativa AS TEXT) as dsJustificativaConselheiro'),
                '(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro')
        );

        $select->joinLeft(
            array('c' => 'Produto'),
            'b.idProduto = c.Codigo',
            array(
                new Zend_Db_Expr('CAST(PAP.dsJustificativa AS TEXT) as dsJustificativaConselheiro'),
                '(PAP.qtItem * PAP.nrOcorrencia * PAP.vlUnitario) AS VlSugeridoConselheiro')
        );

        $select->joinInner(
            array('d' => 'tbPlanilhaEtapa'),
            'b.idEtapa = d.idPlanilhaEtapa',
            array(new Zend_Db_Expr('CONVERT(varchar(8), d.idPlanilhaEtapa) + \' - \' + d.Descricao AS Etapa'),
                'd.idPlanilhaEtapa')
        );

        $select->joinInner(
            array('e' => 'tbPlanilhaUnidade'),
            'b.idUnidade = e.idUnidade',
            array('e.Descricao AS Unidade',
                'e.idUnidade')
        );

        $select->joinInner(
            array('i' => 'tbPlanilhaItens'),
            'b.idPlanilhaItem = i.idPlanilhaItens',
            array('i.Descricao AS Item',
                'i.idPlanilhaItens As idItem')
        );

        $select->joinInner(
            array('x' => 'Verificacao'),
            'b.FonteRecurso = x.idVerificacao',
            array('x.Descricao AS FonteRecurso')
        );

        $select->joinInner(
            array('f' => 'vUFMunicipio'),
            'b.UfDespesa = f.idUF AND b.MunicipioDespesa = f.idMunicipio',
            array('f.UF', 'f.idUF', 'f.Municipio', 'f.idMunicipio'),
            $this->getSchema('agentes')
        );


        $select->where('p.idpronac = ' . $idpronac);

        if ($idItem != null) {
            $select->where('i.idPlanilhaItens = ' . $idItem);
        }


        $select->order('x.Descricao');
        $select->order('Produto');
        $select->order('d.Descricao');
        $select->order('f.UF');
        $select->order('i.Descricao');


        return $this->fetchAll($select);
    }

    /**
     * Metodo para buscar a situa??o atual do projeto
     * @access public
     * @param integer $idPronac
     * @param string $pronac
     * @return array
     */
    public function buscarSituacaoAtual($idPronac = null, $pronac = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            $this->_name,
            array(
                "AnoProjeto"
            , "Sequencial"
            , "DtSituacao"
            , "Situacao"
            , "ProvidenciaTomada"
            , "Logon")
        );

        // busca pelo $idPronac
        if (!empty($idPronac)) {
            $select->where("IdPRONAC = ?", $idPronac);
        }

        // busca pelo pronac
        if (!empty($pronac)) {
            $select->where("(AnoProjeto+Sequencial) = ?", $pronac);
        }

        return $this->fetchRow($select);
    }

    /**
     * Metodo para alterar a situa??o do projeto
     * @access public
     * @param integer $idPronac
     * @param string $pronac
     * @param string $situacao
     * @param string $ProvidenciaTomada
     * @return integer (quantidade de registros alterados)
     */
    public function alterarSituacao($idPronac = null, $pronac = null, $situacao, $ProvidenciaTomada = null, $idUsuario = null)
    {
        // pega logon para gravar alteracao da situacao
        $auth = Zend_Auth::getInstance();
        $Logon = empty($idUsuario) ? $auth->getIdentity()->usu_codigo : $idUsuario;

        // grava no hist?rico a situacao atual do projeto caso a trigger HISTORICO_INSERT esteja desabilitada
        $HistoricoInsert = new HistoricoInsert();

        if ($HistoricoInsert->statusHISTORICO_INSERT() == 1) { // desabilitada

            // busca a situacao atual do projeto
            $p = $this->buscarSituacaoAtual($idPronac, $pronac);

            // grava o hist?rico da situa??o
            if ($situacao != $p['Situacao']) :
                $dadosHistorico = array(
                    'AnoProjeto' => $p['AnoProjeto'],
                    'Sequencial' => $p['Sequencial'],
                    'DtSituacao' => $p['DtSituacao'],
                    'Situacao' => $p['Situacao'],
                    'ProvidenciaTomada' => $p['ProvidenciaTomada'],
                    'Logon' => $p['Logon']);
                $HistoricoSituacao = new HistoricoSituacao();
                $cadastrarHistorico = $HistoricoSituacao->cadastrarDados($dadosHistorico);
            endif;
        } // fecha if

        $dados = array(
            'Situacao' => $situacao
        , 'DtSituacao' => new Zend_Db_Expr('GETDATE()')
        , 'ProvidenciaTomada' => $ProvidenciaTomada
        , 'Logon' => $Logon);

        $where = '';
        // alterar pelo idPronac
        if (!empty($idPronac)) {
            $where = "IdPRONAC = " . $idPronac;
        }

        // alterar pelo pronac
        if (!empty($pronac)) {
            $where = "(AnoProjeto+Sequencial) = '" . $pronac . "'";
        }

        if (!empty($where)) {
            return $this->update($dados, $where);
        } else {
            return new Exception("Erro ao alterar situa&ccedil;&atilde;o do Projeto.");
        }
    }

    // fecha metodo alterarSituacao()

    /**
     * Metodo para alterar o Orgao do Projeto
     * @access public
     * @param integer $dados ($idDestino)
     * @param string $where ($idPronac)
     */
    public function alterarProjetos($dados, $where)
    {
        try {
            $update = $this->update($dados, $where);
            return $update;
        } catch (Zend_Db_Table_Exception $e) {
            return 'Projetos -> alterarProjetos. Erro:' . $e->getMessage();
        }
    }

    /**
     * Metodo para buscar o pronac de acordo com um idPronac
     * @access public
     * @param integer $idPronac
     * @return array
     */
    public function buscarPronac($idPronac = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name, "(AnoProjeto+Sequencial) AS pronac");

        if (!empty($idPronac)) {
            $select->where("IdPRONAC = ?", $idPronac);
        }

        return $this->fetchRow($select);
    }

    public function buscarAnoProjetoSequencial($idPronac = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name, array("AnoProjeto", "Sequencial"));

        if (!empty($idPronac)) {
            $select->where("IdPRONAC = ?", $idPronac);
        }

        return $this->fetchRow($select);
    }


    /**
     * Metodo para buscar o idPronac de acordo com um pronac
     * @access public
     * @param string $pronac
     * @return array
     */
    public function buscarIdPronac($pronac = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name, "IdPRONAC");

        if (!empty($pronac)) {
            $select->where("(anoprojeto " . parent::getConcatExpression() . " sequencial) = ?", $pronac);
        }

        return $this->fetchRow($select);
    }

    public function buscarNomeProjeto($pronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name, "NomeProjeto");
        $select->where("(AnoProjeto+Sequencial) = ?", $pronac);
        return $this->fetchAll($select);
    }

    /**
     * Metodo para buscar os projetos para solicita??o de recurso (UC33)
     * @access public
     * @param integer $idPronac
     * @param string $cpf_cnpj
     * @return object
     */
    public function buscarProjetosSolicitacaoRecurso($idPronac = null, $cpf_cnpj = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("Pr" => $this->_name),
            array("(Pr.AnoProjeto+Pr.Sequencial) AS pronac"
            , "Pr.IdPRONAC"
            , "Pr.NomeProjeto"
            , "A.idAgente"
            , "A.CNPJCPF"
            , "St.Codigo AS CodSituacao"
            , "St.descricao AS Situacao"
            , "NomeProponente" => new Zend_Db_Expr("
                    CASE
                        WHEN N.Descricao IS NULL
                            THEN I.Nome
                        ELSE N.Descricao
                    END")
            , "StatusProjeto" => new Zend_Db_Expr("
                    CASE
                        WHEN (St.Codigo = 'D09'
                            OR St.Codigo = 'D11'
                            OR St.Codigo = 'D25'
                            OR St.Codigo = 'D36'
                            OR St.Codigo = 'D38'
                            OR St.Codigo = 'E10')
                            THEN 'Projeto Aprovado'
                        WHEN (St.Codigo = 'A14'
                            OR St.Codigo = 'A16'
                            OR St.Codigo = 'A17'
                            OR St.Codigo = 'A41'
                            OR St.Codigo = 'D14')
                            THEN 'Projeto Indeferido'
                        WHEN (St.Codigo = 'E12'
                            OR St.Codigo = 'E13'
                            OR St.Codigo = 'E15'
                            OR St.Codigo = 'E50'
                            OR St.Codigo = 'E59'
                            OR St.Codigo = 'E60'
                            OR St.Codigo = 'E61'
                            OR St.Codigo = 'E62')
                            THEN 'Projeto em Fase de Execu??o'
                        WHEN (St.Codigo = 'E17'
                            OR St.Codigo = 'E18'
                            OR St.Codigo = 'E19'
                            OR St.Codigo = 'E22'
                            OR St.Codigo = 'E23'
                            OR St.Codigo = 'E24'
                            OR St.Codigo = 'E27'
                            OR St.Codigo = 'E30'
                            OR St.Codigo = 'E46')
                            THEN 'Projeto em Fase de Presta??o de Contas'
                        ELSE
                            'Projeto com Solicita??o de Recurso em An?lise'
                    END"))
        );
        $select->joinInner(
            array("PP" => "PreProjeto"),
            "Pr.idProjeto = PP.idPreProjeto",
            array()
        );
        $select->joinInner(
            array("I" => "Interessado"),
            "Pr.CgcCpf = I.CgcCpf",
            array()
        );
        $select->joinInner(
            array("St" => "Situacao"),
            "Pr.Situacao = St.Codigo",
            array()
        );
        $select->joinLeft(
            array("A" => "Agentes"),
            "A.CNPJCPF = Pr.CgcCpf",
            array(),
            "AGENTES.dbo"
        );
        $select->joinLeft(
            array("Vi" => "Visao"),
            "Vi.idAgente = A.idAgente",
            array(),
            "AGENTES.dbo"
        );
        $select->joinLeft(
            array("N" => "Nomes"),
            "N.idAgente = A.idAgente",
            array(),
            "AGENTES.dbo"
        );
        $select->joinLeft(
            array("VER" => "Verificacao"),
            "VER.idVerificacao = '144'",
            array(),
            "AGENTES.dbo"
        );

        // filta pelas situa??es
        $select->where("
            St.Codigo    = 'D09'
            OR St.Codigo = 'D11'
            OR St.Codigo = 'D25'
            OR St.Codigo = 'D36'
            OR St.Codigo = 'D38'
            OR St.Codigo = 'E10'
            OR St.Codigo = 'A14'
            OR St.Codigo = 'A16'
            OR St.Codigo = 'A17'
            OR St.Codigo = 'A41'
            OR St.Codigo = 'D14'
            OR St.Codigo = 'E12'
            OR St.Codigo = 'E13'
            OR St.Codigo = 'E15'
            OR St.Codigo = 'E50'
            OR St.Codigo = 'E59'
            OR St.Codigo = 'E60'
            OR St.Codigo = 'E61'
            OR St.Codigo = 'E62'
            OR St.Codigo = 'E17'
            OR St.Codigo = 'E18'
            OR St.Codigo = 'E19'
            OR St.Codigo = 'E22'
            OR St.Codigo = 'E23'
            OR St.Codigo = 'E24'
            OR St.Codigo = 'E27'
            OR St.Codigo = 'E30'
            OR St.Codigo = 'E46'
            OR St.Codigo = 'D20'
        ");

        // busca pelo idPronac
        if (!empty($idPronac)) {
            $select->where("Pr.IdPRONAC = ?", $idPronac);
        }

        // busca pelo cnpj ou cpf
        if (!empty($cpf_cnpj)) {
            $select->where("Pr.CgcCpf = ?", $cpf_cnpj);
        }

        return $this->fetchAll($select);
    }

    public function buscarComboOrgaos($idOrgaoDestino, $idGrupo)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(array("a" => "vwUsuariosOrgaosGrupos"), array("usu_codigo", "usu_nome", "org_superior"), "TABELAS.dbo");
        $slct->where("gru_codigo = ? ", $idGrupo);
        $slct->where("uog_orgao = ? ", $idOrgaoDestino);
        $slct->where("uog_status = ? ", 1);
        $slct->order("2 ASC");

        return $this->fetchAll($slct);
    }

    public function buscarProjetosCaptacao($cpf)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('proj' => $this->_name)
        );
        $select->where("CgcCpf = '" . $cpf . "'");
        $select->where("situacao in ('E12', 'E13', 'E15') ");
        $select->order('proj.NomeProjeto ASC');

        return $this->fetchAll($select);
    }

    /**
     * @deprecated: método migrado para modulo correto. Utilizar model Diligencia_Model_DbTable_TbDiligencia
     **/
    public function listarDiligencias($consulta = array(), $retornaSelect = false)
    {
        $tbDiligenciaDbTable = new Diligencia_Model_DbTable_TbDiligencia();
        return $tbDiligenciaDbTable->listarDiligencias($consulta, $retornaSelect);
    }

    public function dadosProjeto($consulta = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pro' => $this->_name),
            array(
                'nomeProjeto' => 'pro.NomeProjeto',
                'situacao' => 'pro.Situacao',
                'pronac' => new Zend_Db_Expr('pro.AnoProjeto+pro.Sequencial')
            )
        );

        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    public function buscarEditalProjeto($consulta = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pro' => $this->_name),
            array()
        );
        $select->joinInner(
            array('pre' => 'PreProjeto'),
            'pro.idProjeto = pre.idPreProjeto',
            array(
                'pre.idPreProjeto', 'pre.idEdital'
            )
        );

        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchRow($select);
    }

    public function buscarAgenteProjeto($consulta = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pro' => $this->_name),
            array()
        );
        $select->joinInner(
            array('ag' => 'Agentes'),
            'pro.CgcCpf = ag.CNPJCPF',
            array(
                'ag.idAgente'
            ),
            $this->getSchema('agentes')
        );
        foreach ($consulta as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    public function buscarDadosProjetoProdutos($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('a.IdPRONAC', 'a.idProjeto', '(a.AnoProjeto + a.Sequencial) AS Pronac', 'a.CgcCpf', 'a.NomeProjeto', 'a.DtInicioExecucao', 'a.DtFimExecucao')
        );
        $select->joinInner(
            array('b' => 'PlanoDistribuicaoProduto'),
            'b.idProjeto = a.idProjeto ',
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('c' => 'Produto'),
            'b.idProduto = c.Codigo ',
            array('c.Codigo', 'c.Descricao'),
            'SAC.dbo'
        );
        $select->where("a.IdPRONAC = '" . $idpronac . "'");
        $select->where("b.stPlanoDistribuicaoProduto = 1");


        return $this->fetchAll($select);
    }

    public function buscarTodosDadosProjetoProdutos($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('*')
        );
        $select->joinInner(
            array('b' => 'PlanoDistribuicaoProduto'),
            'b.idProjeto = a.idProjeto ',
            array('*'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('c' => 'Produto'),
            'b.idProduto = c.Codigo ',
            array('*'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('d' => 'Verificacao'),
            'd.idVerificacao = b.idPosicaoDaLogo',
            array('Descricao AS PosicaoLogo'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('e' => 'tbDistribuicaoProduto'),
            'e.idPlanoDistribuicao= b.idPlanoDistribuicao',
            array('qtDistribuicao', 'stFinsLucrativos'),
            'SAC.dbo'
        );
        $select->where("a.IdPRONAC = '" . $idpronac . "'");
        $select->where("b.stPlanoDistribuicaoProduto = 1");
        $select->order("a.IdPRONAC DESC");

        return $this->fetchAll($select);
    }

    public function buscarProjetosConsolidados($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->_name),
            array(
                'p.IdPRONAC',
                '(p.AnoProjeto + p.Sequencial) AS PRONAC',
                'p.NomeProjeto', 'Situacao'
            )
        );
        $select->joinInner(
            array('pa' => 'Parecer'),
            "p.IdPRONAC = pa.idPRONAC AND pa.TipoParecer = '1' AND pa.stAtivo = '1'",
            array('DtConsolidacao' => new Zend_Db_Expr('CONVERT(CHAR(11),pa.DtParecer,103) + CONVERT(CHAR(20),pa.DtParecer,108)'))
        );
        $select->joinInner(
            array('o' => 'Orgaos'),
            "p.Orgao = o.Codigo"
        );
        $select->joinInner(
            array('a' => 'Agentes'),
            "a.CNPJCPF = p.CgcCpf",
            array(new Zend_Db_Expr('SAC.dbo.fnNome(a.idAgente) AS NomeProponente')),
            "AGENTES.dbo"
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

    public function buscarProjetosPautaReuniao($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("p" => $this->_name),
            array("AnoProjeto", "Sequencial", "NomeProjeto", "Processo", "ResumoProjeto", "Solicitado" => "SolicitadoReal")
        );
        $slct->joinInner(
            array("pa" => "parecer"),
            "p.AnoProjeto = pa.AnoProjeto AND p.Sequencial = pa.Sequencial",
            array("idParecerVinculadas" => "idParecer", "TipoParecer", "NrReuniao" => "NumeroReuniao", "ParecerTecnico" => "ResumoParecer", "Sugerido" => "SugeridoReal")
        );
        $slct->joinInner(
            array("i" => "Interessado"),
            "p.CgcCpf = i.CgcCpf",
            array("Proponente" => "Nome", "Cidade", "Uf")
        );
        $slct->joinInner(
            array("a" => "Area"),
            "p.Area = a.Codigo",
            array("Area" => "Descricao")
        );
        $slct->joinInner(
            array("o" => "Orgaos"),
            "p.Orgao = o.Codigo",
            array("Orgao" => "Sigla")
        );
        $slct->joinInner(
            array("s" => "Situacao"),
            "p.Situacao = s.Codigo",
            array("Situacao" => "Descricao")
        );
        $slct->joinLeft(
            array("e" => "Enquadramento"),
            "p.AnoProjeto = e.AnoProjeto AND p.Sequencial = e.Sequencial",
            array("Enquadramento")
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

    public function pegaTotalProjetosPautaReuniao($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array("p" => $this->_name),
            array("total" => "count(*)")
        );
        $slct->joinInner(
            array("pa" => "parecer"),
            "p.AnoProjeto = pa.AnoProjeto AND p.Sequencial = pa.Sequencial",
            array()
        );
        $slct->joinInner(
            array("i" => "Interessado"),
            "p.CgcCpf = i.CgcCpf",
            array()
        );
        $slct->joinInner(
            array("a" => "Area"),
            "p.Area = a.Codigo",
            array()
        );
        $slct->joinInner(
            array("o" => "Orgaos"),
            "p.Orgao = o.Codigo",
            array()
        );
        $slct->joinInner(
            array("s" => "Situacao"),
            "p.Situacao = s.Codigo",
            array()
        );
        $slct->joinLeft(
            array("e" => "Enquadramento"),
            "p.AnoProjeto = e.AnoProjeto AND p.Sequencial = e.Sequencial",
            array()
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }


        return $this->fetchAll($slct)->current();
    }

    public function listaEditais($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pro' => $this->_name),
            array()
        );
        $slct->joinInner(
            array('pp' => 'PreProjeto'),
            'pp.idPreProjeto = pro.idProjeto',
            array('')
        );
        $slct->joinInner(
            array('edi' => 'Edital'),
            'edi.idEdital = pp.idEdital',
            array(new Zend_Db_Expr('distinct(edi.idEdital)'), 'edi.qtAvaliador', 'edi.NrEdital')
        );
        $slct->joinInner(
            array('fod' => 'tbFormDocumento'),
            'fod.idEdital = edi.idEdital and fod.idClassificaDocumento not in (23,24,25)',
            array('fod.nmFormDocumento'),
            'BDCORPORATIVO.scQuiz'
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    public function listaProjetosDistribuidos($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pro' => $this->_name),
            array('pro.idPronac', 'pro.AnoProjeto', 'pro.Sequencial', 'pro.NomeProjeto', 'pro.UfProjeto')
        );
        $slct->joinInner(
            array('pp' => 'PreProjeto'),
            'pp.idPreProjeto = pro.idProjeto',
            array('pp.idPreProjeto')
        );
        $slct->joinInner(
            array('edi' => 'Edital'),
            'edi.idEdital = pp.idEdital',
            array('edi.NrEdital', 'edi.qtAvaliador')
        );
        $slct->joinInner(
            array('fod' => 'tbFormDocumento'),
            'fod.idEdital = edi.idEdital and fod.idClassificaDocumento not in (23,24,25)',
            array('fod.nmFormDocumento'),
            'BDCORPORATIVO.scQuiz'
        );
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }


        return $this->fetchAll($slct);
    }

    public function listaProjetosPainelAvaliador($where = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->distinct();
        $slct->from(
            array('pro' => $this->_name),
            array('pro.idPronac', 'pro.AnoProjeto', 'pro.Sequencial', 'pro.NomeProjeto', 'pro.UfProjeto')
        );
        $slct->joinInner(
            array('pp' => 'PreProjeto'),
            'pp.idPreProjeto = pro.idProjeto',
            array('pp.idPreProjeto')
        );
        $slct->joinInner(
            array('edi' => 'Edital'),
            'edi.idEdital = pp.idEdital',
            array('edi.NrEdital', 'edi.qtAvaliador')
        );
        $slct->joinInner(
            array('fod' => 'tbFormDocumento'),
            'fod.idEdital = edi.idEdital and fod.idClassificaDocumento not in (23,24,25)',
            array('fod.nmFormDocumento', 'fod.nrFormDocumento', 'fod.nrVersaoDocumento'),
            'BDCORPORATIVO.scQuiz'
        );
        $slct->joinInner(
            array('dis' => 'tbDistribuicao'),
            'dis.idItemDistribuicao = pp.idPreProjeto',
            array('dis.dtEnvio'),
            'BDCORPORATIVO.scSAC'
        );
        $slct->joinleft(
            array('app' => 'tbAvaliacaoPreProjeto'),
            'app.idPreProjeto = pp.idPreProjeto and app.idAvaliador = dis.idDestinatario',
            array('app.stAvaliacao', 'app.nrNotaFinal'),
            'BDCORPORATIVO.scSAC'
        );
        $slct->joinInner(
            array('age' => 'Agentes'),
            'age.idAgente = dis.idDestinatario',
            array('age.idAgente'),
            'Agentes.dbo'
        );
        $slct->joinInner(
            array('vis' => 'Visao'),
            'vis.idAgente = age.idAgente',
            array(),
            'Agentes.dbo'
        );

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        $slct->order('app.nrNotaFinal desc');


        return $this->fetchAll($slct);
    }

    public function buscaProjetosComissao($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pro' => $this->_name),
            array('pro.AnoProjeto',
                'pro.Sequencial',
                'pro.IdPRONAC',
                'pronac' => new Zend_Db_Expr('pro.AnoProjeto+pro.Sequencial'),
                'pro.NomeProjeto',
                'pro.UfProjeto')
        );
        $slct->joinInner(
            array('pp' => 'PreProjeto'),
            'pp.idPreProjeto = pro.idProjeto',
            array('pp.idPreProjeto')
        );
        $slct->joinInner(
            array('edi' => 'Edital'),
            'edi.idEdital = pp.idEdital',
            array('edi.NrEdital',
                'edi.qtAvaliador',
                'edi.idEdital')
        );
        $slct->joinInner(
            array('fod' => 'tbFormDocumento'),
            'fod.idEdital = edi.idEdital and fod.idClassificaDocumento not in (23,24,25)',
            array('fod.nmFormDocumento as NomeEdital'),
            'BDCORPORATIVO.scQuiz'
        );
        $slct->joinInner(
            array('app' => 'tbAvaliacaoPreProjeto'),
            'app.idPreProjeto = pp.idPreProjeto',
            array('notaTotal' => new Zend_Db_Expr('cast(sum(app.nrNotaFinal)/COUNT(app.nrNotaFinal) as float)')),
            'BDCORPORATIVO.scSAC'
        );
        $slct->joinInner(
            array('i' => 'Interessado'),
            'i.CgcCpf = pro.CgcCpf',
            array('i.Nome as Proponente')
        );
        $slct->group(array('edi.idEdital', 'pro.UfProjeto', 'pro.AnoProjeto', 'pro.Sequencial',
            'pro.NomeProjeto', 'edi.qtAvaliador', 'pp.idPreProjeto', 'edi.NrEdital',
            'fod.nmFormDocumento', 'pro.IdPRONAC', 'i.Nome'));


        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($slct)->count();
        }

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        //adicionando linha order ao select
        $slct->order($order);

        return $this->fetchAll($slct);
    }

    public function listaPareceremitido($tamanho = -1, $inicio = -1)
    {
        $tmpInicio = -1;
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            //$select->limit($tamanho, $tmpInicio);
        }
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        if ($inicio < 0) {
            $slct->from(
                array('pro' => $this->_name),
                array(
                    'pronac' => new Zend_Db_Expr('pro.AnoProjeto+pro.Sequencial'),
                    'NomeProjeto',
                    'OrgaoOrigem' => new Zend_Db_Expr('tabelas.dbo.fnEstruturaOrgao(OrgaoOrigem,0)'),
                    'DtEnvio' => new Zend_Db_Expr('dbo.fnDtEnvioVinculada(pro.idPronac)'),
                    'DtRetorno' => new Zend_Db_Expr('dbo.fnDtRetornoMinC(pro.idPronac)'),
                    'QtdeConsolidar' => new Zend_Db_Expr('datediff(day,DtProtocolo,dbo.fnDtConsolidacaoParecer(pro.idPronac))'),
                    'DtConsolidacaoParecer' => new Zend_Db_Expr('dbo.fnDtConsolidacaoParecer(pro.idPronac)'),
                    'Dias' => new Zend_Db_Expr('datediff(day,dbo.fnDtEnvioVinculada(pro.idPronac),dbo.fnDtRetornoMinC(pro.idPronac))'),
                    'pro.IdPRONAC'
                )
            );
        } else {
            $soma = $tamanho + $tmpInicio;
            $slct->from(
                array('pro' => $this->_name),
                array(
                    'IdPRONAC' => new Zend_Db_Expr(" TOP $soma pro.IdPRONAC"),
                    'pronac' => new Zend_Db_Expr("(pro.AnoProjeto+pro.Sequencial)"),
                    'NomeProjeto',
                    'OrgaoOrigem' => new Zend_Db_Expr('tabelas.dbo.fnEstruturaOrgao(OrgaoOrigem,0)'),
                    'DtEnvio' => new Zend_Db_Expr('dbo.fnDtEnvioVinculada(pro.idPronac)'),
                    'DtRetorno' => new Zend_Db_Expr('dbo.fnDtRetornoMinC(pro.idPronac)'),
                    'QtdeConsolidar' => new Zend_Db_Expr('datediff(day,DtProtocolo,dbo.fnDtConsolidacaoParecer(pro.idPronac))'),
                    'DtConsolidacaoParecer' => new Zend_Db_Expr('dbo.fnDtConsolidacaoParecer(pro.idPronac)'),
                    'Dias' => new Zend_Db_Expr('datediff(day,dbo.fnDtEnvioVinculada(pro.idPronac),dbo.fnDtRetornoMinC(pro.idPronac))')
                )
            );
        }
        $slct->where('dbo.fnDtEnvioVinculada(pro.idPronac) is not null');
        $slct->where('idProjeto is not null');
        $slct->where('dbo.fnchecarConclusao(pro.idPronac) = 1');
        $slct->where('not exists (Select * from Parecer pr where pro.idPronac=pr.idPronac)');
        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from(
            $slct,
            array(new Zend_Db_Expr("TOP $tamanho  *"))
        );
        $selectAux->order(
            array('pronac desc',
                'NomeProjeto desc',
                'OrgaoOrigem desc',
                'DtEnvio desc',
                'DtRetorno desc',
                'Dias desc',
                'QtdeConsolidar desc',
                'DtConsolidacaoParecer desc',
                'IdPRONAC desc')
        );

        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order(
            array('pronac',
                'NomeProjeto',
                'OrgaoOrigem',
                'DtEnvio',
                'DtRetorno',
                'Dias',
                'QtdeConsolidar desc',
                'DtConsolidacaoParecer desc',
                'IdPRONAC')
        );
        // paginacao
        if ($tmpInicio <= 0) {
            return $this->fetchAll($slct);
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    public function listaPareceremitidoTotal()
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pro' => $this->_name),
            array('total' => new Zend_Db_Expr('count(*)'))
        );

        $slct->where('dbo.fnDtEnvioVinculada(pro.idPronac) is not null');
        $slct->where('idProjeto is not null');
        $slct->where('dbo.fnchecarConclusao(pro.idPronac) = 1');
        $slct->where('not exists (Select * from Parecer pr where pro.idPronac=pr.idPronac)');


        return $this->fetchAll($slct);
    }

    public function listaParecerconsolidado($tamanho = -1, $inicio = -1)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            //$select->limit($tamanho, $tmpInicio);
        }
        $slct = $this->select();
        $slct->setIntegrityCheck(false);


        if ($inicio < 0) {
            $slct->from(
                array('pro' => $this->_name),
                array(
                    'pronac' => new Zend_Db_Expr('pro. AnoProjeto+pro.Sequencial'),
                    'NomeProjeto',
                    'OrgaoOrigem' => new Zend_Db_Expr('tabelas.dbo.fnEstruturaOrgao(OrgaoOrigem,0)'),
                    'DtEnvio' => new Zend_Db_Expr('dbo.fnDtEnvioVinculada(pro.idPronac)'),
                    'DtRetorno' => new Zend_Db_Expr('dbo.fnDtRetornoMinC(pro.idPronac)'),
                    'DtConsolidacaoParecer' => new Zend_Db_Expr('dbo.fnDtConsolidacaoParecer(pro.idPronac)'),
                    'Dias' => new Zend_Db_Expr('datediff(day,dbo.fnDtEnvioVinculada(pro.idPronac),dbo.fnDtRetornoMinC(pro.idPronac))'),
                    'QtdeConsolidar' => new Zend_Db_Expr('datediff(day,DtProtocolo,dbo.fnDtConsolidacaoParecer(pro.idPronac))'),
                    'pro.IdPRONAC'
                )
            );
        } else {
            $soma = $tamanho + $tmpInicio;

            $slct->from(
                array('pro' => $this->_name),
                array(
                    'pronac' => new Zend_Db_Expr("TOP $soma pro. AnoProjeto+pro.Sequencial"),
                    'NomeProjeto',
                    'OrgaoOrigem' => new Zend_Db_Expr('tabelas.dbo.fnEstruturaOrgao(OrgaoOrigem,0)'),
                    'DtEnvio' => new Zend_Db_Expr('dbo.fnDtEnvioVinculada(pro.idPronac)'),
                    'DtRetorno' => new Zend_Db_Expr('dbo.fnDtRetornoMinC(pro.idPronac)'),
                    'DtConsolidacaoParecer' => new Zend_Db_Expr('dbo.fnDtConsolidacaoParecer(pro.idPronac)'),
                    'Dias' => new Zend_Db_Expr('datediff(day,dbo.fnDtEnvioVinculada(pro.idPronac),dbo.fnDtRetornoMinC(pro.idPronac))'),
                    'QtdeConsolidar' => new Zend_Db_Expr('datediff(day,DtProtocolo,dbo.fnDtConsolidacaoParecer(pro.idPronac))'),
                    'pro.IdPRONAC'
                )
            );
        }

        $slct->where('dbo.fnDtEnvioVinculada(pro.idPronac) is not null');
        $slct->where('idProjeto is not null');
        $slct->where('exists(Select * from Parecer pr where pro.idPronac=pr.idPronac)');


        //return $this->fetchAll($slct);


        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from(
            $slct,
            array(new Zend_Db_Expr("TOP $tamanho  *"))
        );
        $selectAux->order(
            array('pronac desc',
                'NomeProjeto desc',
                'OrgaoOrigem desc',
                'DtEnvio desc',
                'DtRetorno desc',
                'DtConsolidacaoParecer desc',
                'Dias desc',
                'QtdeConsolidar desc',
                'IdPRONAC desc')
        );

        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order(
            array('pronac',
                'NomeProjeto',
                'OrgaoOrigem',
                'DtEnvio',
                'DtRetorno',
                'DtConsolidacaoParecer',
                'Dias',
                'QtdeConsolidar',
                'IdPRONAC')
        );

        // paginacao
        if ($inicio <= 0) {
            return $this->fetchAll($slct);
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    public function listaParecerconsolidadoTotal()
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pro' => $this->_name),
            array('total' => new Zend_Db_Expr('count(*)'))
        );

        $slct->where('dbo.fnDtEnvioVinculada(pro.idPronac) is not null');
        $slct->where('idProjeto is not null');
        $slct->where('exists(Select * from Parecer pr where pro.idPronac=pr.idPronac)');


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
    public function relatorioProjeto($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pr' => $this->_name),
            array("pr.IdPRONAC as idPronac",
                new Zend_Db_Expr("pr.AnoProjeto+pr.Sequencial AS Pronac"),
                "pr.NomeProjeto", "nm.Descricao AS NomeAgente", "ar.Descricao AS Area", "sg.Descricao AS Segmento", "pr.UfProjeto",
                new Zend_Db_Expr("
                        sac.dbo.fnValorSolicitado(pr.AnoProjeto,pr.Sequencial) as ValorSolicitado,
                        CASE
                            WHEN p.Mecanismo ='2' or p.Mecanismo ='6'
                            THEN sac.dbo.fnValorAprovadoConvenio(pr.AnoProjeto,pr.Sequencial)
                            ELSE sac.dbo.fnValorAprovado(pr.AnoProjeto,pr.Sequencial)
                        END AS ValorAprovado,
                        sac.dbo.fnCustoProjeto (pr.AnoProjeto,pr.Sequencial) as ValorCaptado,
                        CASE
                            WHEN inab.Habilitado = 'S' THEN 'Sim'
                            WHEN inab.Habilitado = 'N' THEN 'N&atilde;o'
                            WHEN inab.Habilitado = '' THEN 'Sim'
                            WHEN inab.Habilitado is null THEN 'Sim'
                        END AS Habilitado, pr.Situacao, si.Descricao AS dsSituacao
                    "),
            )
        );
        $slct->joinInner(
            array('ar' => 'Area'),
            'ar.Codigo = pr.Area',
            array()
        );
        $slct->joinInner(
            array('sg' => 'Segmento'),
            'sg.Codigo = pr.Segmento',
            array()
        );
        $slct->joinInner(
            array('si' => 'Situacao'),
            'pr.Situacao = si.Codigo',
            array(),
            'SAC.dbo'
        );
        $slct->joinInner(
            array('uf' => 'UF'),
            'uf.Uf = pr.UfProjeto',
            array(),
            'SAC.dbo'
        );
        $slct->joinLeft(
            array('ag' => 'Agentes'),
            'ag.CNPJCPF = pr.CgcCpf',
            array(),
            "AGENTES.dbo"
        );
        $slct->joinLeft(
            array('nm' => 'Nomes'),
            'ag.idAgente = nm.idAgente',
            array(),
            "AGENTES.dbo"
        );
        $slct->joinLeft(
            array('inab' => 'Inabilitado'),
            'inab.CgcCpf = pr.CgcCpf and pr.AnoProjeto = inab.AnoProjeto and pr.Sequencial = inab.Sequencial',
            array(),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array('p' => 'PreProjeto'),
            'pr.idProjeto = p.idPreProjeto',
            array(),
            'SAC.dbo'
        );
        $slct->joinLeft(
            array("ap" => "Aprovacao"),
            "ap.Anoprojeto = pr.AnoProjeto and ap.Sequencial = pr.Sequencial",
            array(),
            "Sac.dbo"
        );
        $slct->joinLeft(
            array("e" => "EnderecoNacional"),
            "ag.idAgente = e.idAgente and e.Status = 1",
            array(),
            "AGENTES.dbo"
        );
        $slct->joinLeft(
            array("u" => "vUFMunicipio"),
            "e.UF = u.idUF and e.Cidade = u.idMunicipio",
            array('u.Municipio'),
            "AGENTES.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
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
    public function extratorProjeto($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pr' => $this->_name),
            array(
                "pr.IdPRONAC", "pr.AnoProjeto", "pr.Sequencial",
                "pr.NomeProjeto", "pr.Area", "pr.Segmento",
                "pr.Mecanismo", "pr.Processo", "pr.UFProjeto",
                "DtProtocolo" => new Zend_Db_Expr("CONVERT(CHAR(11),pr.DtProtocolo,120)"), "pr.Orgao", "pr.OrgaoOrigem",
                "pr.Situacao", "DtSituacao" => new Zend_Db_Expr("CONVERT(CHAR(11),pr.DtSituacao,120)"), "pr.ResumoProjeto",
                "pr.ProvidenciaTomada", "pr.CgcCpf",
                "NrPortaria" => new Zend_Db_Expr("SAC.dbo.fnNrPortariaAprovacao(AnoProjeto,Sequencial)"),
                "DtPortaria" => new Zend_Db_Expr("CONVERT(CHAR(11),SAC.dbo.fnDtPortariaAprovacao(AnoProjeto,Sequencial),120)"),
                "DtPublicacao" => new Zend_Db_Expr("CONVERT(CHAR(11),SAC.dbo.fnDtPortariaPublicacao(AnoProjeto,Sequencial),120)"),
                "DtInicioCaptacao" => new Zend_Db_Expr("CONVERT(CHAR(11),SAC.dbo.fnInicioCaptacao(AnoProjeto,Sequencial),120)"),
                "DtFinalCaptacao" => new Zend_Db_Expr("CONVERT(CHAR(11),SAC.dbo.fnFimCaptacao(AnoProjeto,Sequencial),120)"),
                "DtPrimeiraCaptacao" => new Zend_Db_Expr("CONVERT(CHAR(11),SAC.dbo.fnDtPrimeiraCaptacao(AnoProjeto,Sequencial),120)"),
                "DtUltimaCaptacao" => new Zend_Db_Expr("CONVERT(CHAR(11),SAC.dbo.fnDtUltimaCaptacao(AnoProjeto,Sequencial),120)"),
                "DtLiberacao" => new Zend_Db_Expr("CONVERT(CHAR(11),SAC.dbo.fnDtLiberacaoConta(AnoProjeto,Sequencial),120)"),
                "Valor" => new Zend_Db_Expr("SAC.dbo.fnValorSolicitado(AnoProjeto,Sequencial)"),
                "VlAprovado" => new Zend_Db_Expr("SAC.dbo.fnAprovadoProjeto(AnoProjeto,Sequencial)"),
                "Captado" => new Zend_Db_Expr("SAC.dbo.fnTotalCaptadoProjeto(AnoProjeto,Sequencial)")
            )
        );
        $slct->joinInner(
            array('ar' => 'Area'),
            'ar.Codigo = pr.Area',
            array("AreaNome" => "Descricao"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array('seg' => 'Segmento'),
            'seg.Codigo = pr.Segmento',
            array("SegmentoNome" => "Descricao"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array('i' => 'Interessado'),
            'pr.CgcCpf = i.CgcCpf',
            array("Nome"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array('mec' => 'Mecanismo'),
            'pr.Mecanismo = mec.Codigo',
            array("MecanismoNome" => "Descricao"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array('o' => 'Orgaos'),
            'pr.Orgao = o.Codigo',
            array("OrgaoNome" => "Sigla"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array('oo' => 'Orgaos'),
            'pr.OrgaoOrigem = oo.Codigo',
            array("OrgaoOrigemNome" => "Sigla"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array('sit' => 'Situacao'),
            'pr.Situacao = sit.Codigo',
            array("SituacaoNome" => "Descricao"),
            "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                array('pr' => $this->_name),
                array("total" => "count(*)")
            );
            $slct2->joinInner(
                array('ar' => 'Area'),
                'ar.Codigo = pr.Area',
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array('seg' => 'Segmento'),
                'seg.Codigo = pr.Segmento',
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array('i' => 'Interessado'),
                'pr.CgcCpf = i.CgcCpf',
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array('mec' => 'Mecanismo'),
                'pr.Mecanismo = mec.Codigo',
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array('o' => 'Orgaos'),
                'pr.Orgao = o.Codigo',
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array('oo' => 'Orgaos'),
                'pr.OrgaoOrigem = oo.Codigo',
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array('sit' => 'Situacao'),
                'pr.Situacao = sit.Codigo',
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

    public function diagnostico($where = array(), $order = array(), $tamanho = -1, $inicio = -1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pr' => $this->_name),
            array(
                //"Orgao",
                "Situacao",
                //"Secretaria" => new Zend_Db_Expr("Tabelas.dbo.fnEstruturaOrgao(idSecretaria,0)"),
                "Qtde" => new Zend_Db_Expr("count(*)")
            )
        );
        $slct->joinInner(
            array('o' => 'Orgaos'),
            'pr.Orgao = o.Codigo',
            //array("idSecretaria", "Sigla"),
            array(),
            "SAC.dbo"
        );
        $slct->joinInner(
            array('s' => 'Situacao'),
            'pr.Situacao = s.Codigo',
            array("SituacaoNome" => "Descricao"),
            "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        $slct->group(array("pr.Situacao", "Situacao", "s.Descricao"));

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

    public function pontoCulturaRegiaoUfCidade($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pr' => $this->_name),
            array(
                "Custo" => new Zend_Db_Expr("sum(SAC.dbo.fnCustoProjeto(pr.AnoProjeto,pr.Sequencial))"),
                "Qtde" => new Zend_Db_Expr("count(*)")
            )
        );
        $slct->joinInner(
            array('i' => 'Interessado'),
            'pr.CgcCpf = i.CgcCpf',
            array("Cidade"),
            "SAC.dbo"
        );
        $slct->joinInner(
            array('u' => 'UF'),
            'pr.UfProjeto = u.UF',
            array("UF" => "Descricao", "Regiao"),
            "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                array('pr' => $this->_name),
                array("total" => "count(*)")
            );
            $slct2->joinInner(
                array('i' => 'Interessado'),
                'pr.CgcCpf = i.CgcCpf',
                array(),
                "SAC.dbo"
            );
            $slct2->joinInner(
                array('u' => 'UF'),
                'pr.UfProjeto = u.UF',
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

        //adicionando linha order ao select
        $slct->order($order);

        $slct->group(array("Regiao", "u.Descricao", "i.Cidade"));

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        //dx($slct->__toString());
        return $this->fetchAll($slct);
    }

    public function extratoPautaItercambio($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('pr' => $this->_name),
            array('idPronac', 'AnoProjeto', 'Sequencial', 'NomeProjeto', 'ResumoProjeto')
        );
        $slct->joinInner(
            array('pa' => 'Passagem'),
            'pr.AnoProjeto = pa.AnoProjeto AND pr.Sequencial = pa.Sequencial',
            array('Evento', 'EntidadePromotora', 'CidadeEvento' => 'Cidade', 'Pais', 'ParecerTecnico' => 'ResumoParecer', 'VlNormal' => 'ValorNormalR', 'VlPromocional' => 'ValorPromocionalR', 'VlNormalTotal' => 'QtePassagem', 'VlPromocionalTotal' => new Zend_Db_Expr('ValorPromocionalR * QtePassagem'))
        );
        $slct->joinInner(
            array('i' => 'Interessado'),
            'pr.CgcCpf = i.CgcCpf',
            array('Proponente' => 'Nome', 'Uf', 'Cidade'),
            'SAC.dbo'
        );
        $slct->joinInner(
            array('ar' => 'Area'),
            'pr.Area = ar.Codigo',
            array('Area' => 'Descricao'),
            'SAC.dbo'
        );
        $slct->joinInner(
            array('uf' => 'Uf'),
            'pr.UfProjeto = uf.Uf',
            array(),
            'SAC.dbo'
        );
        $slct->joinLeft(
            array('p' => 'PreProjeto'),
            'pr.idProjeto = p.idPreProjeto',
            array(),
            'SAC.dbo'
        );
        $slct->joinLeft(
            array("m" => "tbMovimentacao"),
            "p.idPreProjeto = m.idProjeto AND m.stEstado = 0",
            array(),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("x" => "tbAvaliacaoProposta"),
            "p.idPreProjeto = x.idProjeto AND x.stEstado = 0",
            array(),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("x1" => "tbAvaliacaoProposta"),
            "p.idPreProjeto = x1.idProjeto",
            array(),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("mv" => "tbMovimentacao"),
            "p.idPreProjeto = mv.idProjeto and mv.stEstado = 0",
            array(),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("vr" => "Verificacao"),
            "mv.movimentacao = vr.idVerificacao and vr.idTipo = 4",
            array(),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("e" => "Edital"),
            "e.idEdital = p.idEdital",
            array(),
            "SAC.dbo"
        );
        $slct->joinLeft(
            array("fd" => "tbFormDocumento"),
            "fd.idEdital = p.idEdital AND idClassificaDocumento NOT IN (23,24,25)",
            array(),
            "BDCORPORATIVO.scQuiz"
        );
        $slct->joinLeft(
            array("cl" => "tbClassificaDocumento"),
            "cl.idClassificaDocumento = fd.idClassificaDocumento",
            array(),
            "BDCORPORATIVO.scSAC"
        );
        $slct->joinLeft(
            array("vr2" => "Verificacao"),
            "e.cdTipoFundo = vr2.idVerificacao and vr2.idTipo = 15",
            array(),
            "SAC.dbo"
        );

        if ($count) {
            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                array('pr' => $this->_name),
                array("total" => "count(*)")
            );
            $slct2->joinInner(
                array('pa' => 'Passagem'),
                'pr.AnoProjeto = pa.AnoProjeto AND pr.Sequencial = pa.Sequencial',
                array()
            );
            $slct2->joinInner(
                array('i' => 'Interessado'),
                'pr.CgcCpf = i.CgcCpf',
                array(),
                'SAC.dbo'
            );
            $slct2->joinInner(
                array('ar' => 'Area'),
                'pr.Area = ar.Codigo',
                array(),
                'SAC.dbo'
            );
            $slct2->joinInner(
                array('uf' => 'Uf'),
                'pr.UfProjeto = uf.Uf',
                array(),
                'SAC.dbo'
            );
            $slct2->joinLeft(
                array('p' => 'PreProjeto'),
                'pr.idProjeto = p.idPreProjeto',
                array(),
                'SAC.dbo'
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
            $slct2->joinLeft(
                array("e" => "Edital"),
                "e.idEdital = p.idEdital",
                array(),
                "SAC.dbo"
            );
            $slct2->joinLeft(
                array("fd" => "tbFormDocumento"),
                "fd.idEdital = p.idEdital AND idClassificaDocumento NOT IN (23,24,25)",
                array(),
                "BDCORPORATIVO.scQuiz"
            );
            $slct2->joinLeft(
                array("cl" => "tbClassificaDocumento"),
                "cl.idClassificaDocumento = fd.idClassificaDocumento",
                array(),
                "BDCORPORATIVO.scSAC"
            );
            $slct2->joinLeft(
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

//    public function parecerProjetos(){
//        $slct = $this->select();
//        $slct->setIntegrityCheck(false);
//        $slct->from(
//                array('pr' => $this->_name),
//                array(
//                    "Diligencia"=>new Zend_Db_Expr("SAC.dbo.fnchecarDiligencia(pr.IdPRONAC)"),
//                    "IdPRONAC"
//                    )
//        );
//        $slct->joinInner(
//                array('dp' => 'tbDistribuirParecer'),
//                'pr.IdPRONAC = dp.idPRONAC',
//                array(),
//                "SAC.dbo"
//        );
//        $slct->joinInner(
//                array('ag' => 'Agentes'),
//                'dp.idAgenteParecerista = ag.idAgente',
//                array(),
//                "AGENTES.dbo"
//        );
//        $slct->joinInner(
//                array('us' => 'Usuarios'),
//                'ag.CNPJCPF = us.usu_identificacao',
//                array(),
//                "TABELAS.dbo"
//        );
//
//        //adiciona quantos filtros foram enviados
//        foreach ($where as $coluna => $valor) {
//            $slct->where($coluna, $valor);
//        }
//
//        if($count){
//            $slct2 = $this->select();
//            $slct2->setIntegrityCheck(false);
//            $slct2->from(
//                    array('pr' => $this->_name),
//                    array("total"=>"count(*)")
//            );
//            $slct2->joinInner(
//                    array('dp' => 'tbDistribuirParecer'),
//                    'pr.IdPRONAC = dp.idPRONAC',
//                    array(),
//                    "SAC.dbo"
//            );
//            $slct2->joinInner(
//                    array('ag' => 'Agentes'),
//                    'd.idAgenteParecerista = ag.idAgente',
//                    array(),
//                    "AGENTES.dbo"
//            );
//            $slct2->joinInner(
//                    array('us' => 'Usuarios'),
//                    'ag.CNPJCPF = us.usu_identificacao',
//                    array(),
//                    "TABELAS.dbo"
//            );
//
//            //adiciona quantos filtros foram enviados
//            foreach ($where as $coluna => $valor) {
//                $slct2->where($coluna, $valor);
//            }
//
//            $rs = $this->fetchAll($slct2)->current();
//            if($rs){ return $rs->total; }else{ return 0; }
//        }
//
//        //adicionando linha order ao select
//        $slct->order($order);
//
//        // paginacao
//        if ($tamanho > -1) {
//            $tmpInicio = 0;
//            if ($inicio > -1) {
//                $tmpInicio = $inicio;
//            }
//            $slct->limit($tamanho, $tmpInicio);
//        }
//
//        return $this->fetchAll($slct);
//    }

    public function pedidoProrrogacaoPorProjeto($idPronac, $tamanho = -1, $inicio = -1)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            //$select->limit($tamanho, $tmpInicio);
        }
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        if ($inicio < 0) {
            $slct->from(
                array('p' => $this->_name),
                array(
                    'NrProjeto' => new Zend_Db_Expr('p.AnoProjeto+p.Sequencial'),
                    'p.NomeProjeto'
                )
            );
        } else {
            $soma = $tamanho + $tmpInicio;
            $slct->from(
                array('p' => $this->_name),
                array(
                    'NrProjeto' => new Zend_Db_Expr("TOP $soma p.AnoProjeto+p.Sequencial"),
                    'p.NomeProjeto'
                )
            );
        }
        $slct->joinInner(
            array("pr" => "Prorrogacao"),
            "p.AnoProjeto=pr.AnoProjeto and p.Sequencial=pr.Sequencial",
            array(
                'pr.DtPedido',
                'pr.DtInicio',
                'pr.DtFinal',
                'pr.Diligenciado',
                'pr.DtVencDiligencia',
                'pr.Atendimento'
            )
        );
        $slct->joinInner(
            array("u" => "Usuarios"),
            "pr.Logon = u.usu_Codigo",
            array(
                'u.usu_Nome'
            ),
            'tabelas.dbo'
        );

        $slct->where('p.IdPRONAC = ?', $idPronac);
        $slct->order(
            array('pr.DtPedido',
                'pr.DtInicio',
                'pr.DtFinal')
        );


        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from(
            $slct,
            array(new Zend_Db_Expr("TOP $tamanho  *"))
        );
        $selectAux->order(
            array('DtPedido desc',
                'DtInicio desc',
                'DtFinal desc')
        );

        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order(
            array('pr.DtPedido',
                'DtInicio',
                'DtFinal')
        );


        // paginacao
        if ($inicio <= 0) {
            return $this->fetchAll($slct);
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    public function pedidoProrrogacaoPorProjetoTotal($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('p' => $this->_name),
            array('total' => new Zend_Db_Expr('count(*)'))
        );

        $slct->joinInner(
            array("pr" => "Prorrogacao"),
            "p.AnoProjeto=pr.AnoProjeto and p.Sequencial=pr.Sequencial",
            array()
        );
        $slct->joinInner(
            array("u" => "Usuarios"),
            "pr.Logon = u.usu_Codigo",
            array(),
            'tabelas.dbo'
        );

        $slct->where('p.IdPRONAC = ?', $idPronac);


        return $this->fetchAll($slct);
    }

    public function geraldeanalise($where, $tamanho = -1, $inicio = -1)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            //$select->limit($tamanho, $tmpInicio);
        }
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        if ($inicio < 0) {
            $slct->from(
                array('p' => $this->_name),
                array(
                    "tempoFimDiligencia" => new Zend_Db_Expr("(
                        select
                        top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia
                        from SAC.dbo.tbDiligencia  d
                        where d.idPronac = p.IdPRONAC and d.idProduto = pr.Codigo
                        order by DtSolicitacao desc
                 )"),
                    "DtSolicitacao" => new Zend_Db_Expr("(
                        select
                                top 1 DtSolicitacao
                        from SAC.dbo.tbDiligencia  d
                        where d.idPronac = p.IdPRONAC and d.idProduto = pr.Codigo
                        order by DtSolicitacao desc
                 )"),
                    "DtResposta" => new Zend_Db_Expr("(
                        select
                                top 1 DtResposta
                        from SAC.dbo.tbDiligencia  d
                        where d.idPronac = p.IdPRONAC and d.idProduto = pr.Codigo
                        order by DtSolicitacao desc
                 )"),
                    'p.DtFimExecucao',
                    'QtdeDiasVencido' => new Zend_Db_Expr('DATEDIFF(day,GETDATE(), p.DtFimExecucao)'),
                    'PeriodoExecucao' => new Zend_Db_Expr("convert(char(10),p.DtInicioExecucao,103) + ' a ' + convert(char(10),p.DtFimExecucao,103)"),
                    'Parecerista' => new Zend_Db_Expr("ISNULL(n.Descricao,'')"),
                    'd.TipoAnalise',
                    'DescricaoAnalise' => new Zend_Db_Expr("CASE
                                                         WHEN TipoAnalise = 0
                                                            THEN 'Cont?udo'
                                                         WHEN TipoAnalise = 1
                                                            THEN 'Custo do Produto'
                                                         WHEN TipoAnalise = 2
                                                            THEN 'Custo Administrativo'
                                                         END "),
                    'QtdeTotalDiasAnalisar' => new Zend_Db_Expr("DATEDIFF(day, p.DtProtocolo, isnull(d.DtDevolucao,GETDATE()))"),
                    'QtdeDiasDevolvidosParaCoordenador' => new Zend_Db_Expr("CASE
                                                                                         WHEN d.DtDevolucao is not null and d.DtRetorno is null and d.FecharAnalise=0
                                                                                            THEN DATEDIFF(day, d.DtDevolucao,GETDATE())
                                                                                         END "),
                    'QtdeDiasParaPareceristaAnalisar' => new Zend_Db_Expr("CASE
                                                                                     WHEN d.DtDevolucao is not null
                                                                                        THEN DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao)
                                                                                     END"),
                    'QtdeDiasComParecerista' => new Zend_Db_Expr("CASE
                                                                             WHEN d.DtDevolucao is null
                                                                                THEN DATEDIFF(day, d.DtDistribuicao, GETDATE())
                                                                             WHEN d.DtDevolucao is not  null
                                                                                THEN DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao)
                                                                             WHEN Situacao = 'B14'
                                                                                THEN DATEDIFF(day, d.DtDistribuicao, GETDATE()) - DATEDIFF(day, p.dtSituacao, GETDATE())
                                                                             END"),
                    'QtdeDiasParaDistribuir' => new Zend_Db_Expr("DATEDIFF(day, d.DtEnvio, isnull(d.DtDistribuicao,GETDATE()))"),
                    'PRONAC' => new Zend_Db_Expr("p.AnoProjeto+p.Sequencial"),
                    'Diligencia' => new Zend_Db_Expr("dbo.fnchecarDiligencia(p.idPronac)"),
                    'd.idDistribuirParecer',
                    'p.idPronac',
                    'p.NomeProjeto',
                    'd.idProduto',
                    'pr.Descricao as Produto',
                    'd.idAgenteParecerista',
                    'd.idOrgao',
                    'org.Sigla as nmOrgao',
                    new Zend_Db_Expr('CONVERT(CHAR(10), p.DtProtocolo,103) as DtPrimeiroEnvio'),
                    new Zend_Db_Expr('CONVERT(CHAR(10), d.DtEnvio,103) as DtUltimoEnvio'),
                    new Zend_Db_Expr('CONVERT(CHAR(10), d.DtDistribuicao,103) as DtDistribuicao'),
                    new Zend_Db_Expr('CONVERT(CHAR(10), d.dtDevolucao,103) as dtDevolucao)')
                )
            );
        } else {
            $soma = $tamanho + $tmpInicio;
            $slct->from(
                array('p' => $this->_name),
                array(
                    'DtFimExecucao' => new Zend_Db_Expr("Top $soma p.DtFimExecucao"),
                    "tempoFimDiligencia" => new Zend_Db_Expr("(
                        select
                                top 1 CASE WHEN stProrrogacao = 'N' THEN 20 ELSE 40 END AS tempoFimDiligencia
                        from SAC.dbo.tbDiligencia  d
                        where d.idPronac = p.IdPRONAC and d.idProduto = pr.Codigo
                        order by DtSolicitacao desc
                 )"),
                    "DtSolicitacao" => new Zend_Db_Expr("(
                        select
                                top 1 DtSolicitacao
                        from SAC.dbo.tbDiligencia  d
                        where d.idPronac = p.IdPRONAC and d.idProduto = pr.Codigo
                        order by DtSolicitacao desc
                 )"),
                    "DtResposta" => new Zend_Db_Expr("(
                        select
                                top 1 DtResposta
                        from SAC.dbo.tbDiligencia  d
                        where d.idPronac = p.IdPRONAC and d.idProduto = pr.Codigo
                        order by DtSolicitacao desc
                 )"),
                    'p.IdPRONAC',
                    'QtdeDiasVencido' => new Zend_Db_Expr('DATEDIFF(day,GETDATE(), p.DtFimExecucao)'),
                    'PeriodoExecucao' => new Zend_Db_Expr("convert(char(10),p.DtInicioExecucao,103) + ' a ' + convert(char(10),p.DtFimExecucao,103)"),
                    'Parecerista' => new Zend_Db_Expr("ISNULL(n.Descricao,'')"),
                    'd.TipoAnalise',
                    'DescricaoAnalise' => new Zend_Db_Expr("CASE
                                                         WHEN TipoAnalise = 0
                                                            THEN 'Cont?udo'
                                                         WHEN TipoAnalise = 1
                                                            THEN 'Custo do Produto'
                                                         WHEN TipoAnalise = 2
                                                            THEN 'Custo Administrativo'
                                                         END "),
                    'QtdeTotalDiasAnalisar' => new Zend_Db_Expr("DATEDIFF(day, p.DtProtocolo, isnull(d.DtDevolucao,GETDATE()))"),
                    'QtdeDiasDevolvidosCoordenador' => new Zend_Db_Expr("CASE
                                                                                         WHEN d.DtDevolucao is not null and d.DtRetorno is null and d.FecharAnalise=0
                                                                                            THEN DATEDIFF(day, d.DtDevolucao,GETDATE())
                                                                                         END "),
                    'QtdeDiasPareceristaAnalisar' => new Zend_Db_Expr("CASE
                                                                                     WHEN d.DtDevolucao is not null
                                                                                        THEN DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao)
                                                                                     END"),
                    'QtdeDiasComParecerista' => new Zend_Db_Expr("CASE
                                                                             WHEN d.DtDevolucao is null
                                                                                THEN DATEDIFF(day, d.DtDistribuicao, GETDATE())
                                                                             WHEN d.DtDevolucao is not  null
                                                                                THEN DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao)
                                                                             WHEN Situacao = 'B14'
                                                                                THEN DATEDIFF(day, d.DtDistribuicao, GETDATE()) - DATEDIFF(day, p.dtSituacao, GETDATE())
                                                                             END"),
                    'QtdeDiasParaDistribuir' => new Zend_Db_Expr("DATEDIFF(day, d.DtEnvio, isnull(d.DtDistribuicao,GETDATE()))"),
                    'PRONAC' => new Zend_Db_Expr("p.AnoProjeto+p.Sequencial"),
                    'Diligencia' => new Zend_Db_Expr("dbo.fnchecarDiligencia(p.idPronac)"),
                    'd.idDistribuirParecer',
                    'p.NomeProjeto',
                    'd.idProduto',
                    'pr.Descricao as Produto',
                    'd.idAgenteParecerista',
                    'd.idOrgao',
                    'org.Sigla as nmOrgao',
                    new Zend_Db_Expr('CONVERT(CHAR(10), p.DtProtocolo,103) as DtPrimeiroEnvio'),
                    new Zend_Db_Expr('CONVERT(CHAR(10), d.DtEnvio,103) as DtUltimoEnvio'),
                    new Zend_Db_Expr('CONVERT(CHAR(10), d.DtDistribuicao,103) as DtDistribuicao'),
                    new Zend_Db_Expr('CONVERT(CHAR(10), d.dtDevolucao,103) as dtDevolucao)')
                )
            );
        }
        $slct->joinInner(
            array("d" => "tbDistribuirParecer"),
            "p.idPronac = d.idPronac",
            array()
        );
        $slct->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = d.idOrgao",
            array()
        );
        $slct->joinInner(
            array("n" => "Nomes"),
            "d.idAgenteParecerista = n.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array("pr" => "Produto"),
            "d.idProduto = pr.Codigo",
            array()
        );
        $slct->where('d.stEstado = 0');
        $slct->where("Situacao in('B11','B14')");
        $slct->where('d.FecharAnalise = 0');
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        $slct->order(array('p.AnoProjeto', 'p.Sequencial', 'd.idProduto', 'd.TipoAnalise'));


        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from(
            $slct,
            array(new Zend_Db_Expr("TOP $tamanho  *"))
        );
        $selectAux->order(
            array(
                'PRONAC desc',
                'idProduto desc',
                'TipoAnalise desc')
        );

        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order(
            array('PRONAC',
                'idProduto',
                'TipoAnalise')
        );
        // paginacao
        if ($tmpInicio <= 0) {
            return $this->fetchAll($slct);
        } else {
            return $this->fetchAll($selectAux2);
        }


        return $this->fetchAll($slct);
    }

    public function geraldeanaliseTotal($where)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('p' => $this->_name),
            array('total' => new Zend_Db_Expr('count(*)'))
        );
        $slct->joinInner(
            array("d" => "tbDistribuirParecer"),
            "p.idPronac = d.idPronac",
            array()
        );
        $slct->joinInner(
            array("org" => "Orgaos"),
            "org.Codigo = d.idOrgao",
            array()
        );
        $slct->joinInner(
            array("n" => "Nomes"),
            "d.idAgenteParecerista = n.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $slct->joinInner(
            array("pr" => "Produto"),
            "d.idProduto = pr.Codigo",
            array()
        );
        $slct->where('d.stEstado = 0');
        $slct->where("Situacao in('B11','B14')");
        $slct->where('d.FecharAnalise = 0');
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    public function dadosProjetoDiligencia($idPronac)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
            array('p' => $this->_name),
            array(
                'pronac' => new Zend_Db_Expr('p.AnoProjeto+p.Sequencial'),
                'p.NomeProjeto'
            )
        );
        $slct->joinInner(
            array("a" => "Agentes"),
            "a.CNPJCPF = p.CGCCPF",
            array(),
            'agentes.dbo'
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

        $slct->where('p.idPronac = ?', $idPronac);

        return $this->fetchAll($slct);
    }

    public function buscarPlanilhaSugerida($where = array())
    {
        $select = $this->select();
        $select->distinct();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => $this->_name), array());
        $select->joinLeft(array('d' => 'tbDistribuirParecer'), 'd.idPronac = p.IdPRONAC', array());
        $select->joinRight(array('ps' => 'vwPlanilhaSugerida'), 'ps.idPronac = p.IdPRONAC and ps.idProduto = d.idProduto');
        foreach ($where as $key => $value) {
            $select->where($key, $value);
        }
        return $this->fetchAll($select);
    }

    /**
     * @deprecated migrado para a Parecer_Model_DbTable_TbDistribuirParecer
     */
    public function buscaProjetosProdutosParaAnalise($where)
    {
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        return $tbDistribuirParecer->buscaProjetosProdutosParaAnalise($where);
    }

    public function buscarProjetosAprovados($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        //$select->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pr' => 'Projetos'),
            array('(pr.AnoProjeto+pr.Sequencial) AS pronac',
                'pr.NomeProjeto',
                'pr.CgcCpf',
                'pr.IdPRONAC',
                new Zend_db_Expr('TABELAS.dbo.fnEstruturaOrgao(pr.Orgao,0) AS LocalizacaoProjeto')
            )
        );
        $select->joinInner(
            array('a' => 'Area'),
            'a.Codigo = pr.Area',
            array('a.Descricao AS area')
        );
        $select->joinInner(
            array('sg' => 'Segmento'),
            'sg.Codigo = pr.Segmento',
            array('sg.Descricao AS segmento')
        );
        $select->joinInner(
            array('ap' => 'Aprovacao'),
            'ap.AnoProjeto = pr.AnoProjeto AND ap.Sequencial = pr.Sequencial',
            array(
                'ap.AprovadoReal',
                'ap.idAprovacao',
                'ap.PortariaAprovacao',
                'ap.DtPublicacaoAprovacao',
                'ap.dtPortariaAprovacao'
            )
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(
                array('pr' => 'Projetos'),
                array("total" => "count(*)")
            );
            $slctContador->joinInner(
                array('a' => 'Area'),
                'a.Codigo = pr.Area',
                array()
            );
            $slctContador->joinInner(
                array('sg' => 'Segmento'),
                'sg.Codigo = pr.Segmento',
                array()
            );
            $slctContador->joinInner(
                array('ap' => 'Aprovacao'),
                'ap.AnoProjeto = pr.AnoProjeto AND ap.Sequencial = pr.Sequencial',
                array()
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }
            $rs = $this->fetchAll($slctContador)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
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

    public function buscarProjetosReadequacoes($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        //$select->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('r' => 'tbReadequacao'),
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('pr' => 'Projetos'),
            'r.idPronac = pr.IdPRONAC',
            array(
                'pr.IdPRONAC', '(pr.AnoProjeto+pr.Sequencial) AS pronac', 'pr.NomeProjeto', 'pr.CgcCpf', new Zend_Db_Expr('TABELAS.dbo.fnEstruturaOrgao(pr.Orgao,0) AS LocalizacaoProjeto')
            ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('ap' => 'Aprovacao'),
            'ap.IdPRONAC = r.idPronac AND ap.idReadequacao = r.idReadequacao',
            array('ap.AprovadoReal', 'ap.idAprovacao', 'ap.PortariaAprovacao', 'ap.DtPublicacaoAprovacao', 'ap.dtPortariaAprovacao'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('ar' => 'Area'),
            'pr.Area = ar.Codigo',
            array('ar.Descricao AS area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sg' => 'Segmento'),
            'pr.Segmento = sg.Codigo',
            array('sg.Descricao AS segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('tpr' => 'tbTipoReadequacao'),
            'tpr.idTipoReadequacao = r.idTipoReadequacao',
            array('tpr.idTipoReadequacao', 'tpr.dsReadequacao'),
            'SAC.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(
                array('r' => 'tbReadequacao'),
                array("total" => "count(*)"),
                'SAC.dbo'
            );
            $slctContador->joinInner(
                array('pr' => 'Projetos'),
                'r.idPronac = pr.IdPRONAC',
                array(),
                'SAC.dbo'
            );
            $slctContador->joinInner(
                array('ap' => 'Aprovacao'),
                'ap.IdPRONAC = r.idPronac AND ap.idReadequacao = r.idReadequacao',
                array(),
                'SAC.dbo'
            );
            $slctContador->joinInner(
                array('ar' => 'Area'),
                'pr.Area = ar.Codigo',
                array(),
                'SAC.dbo'
            );
            $slctContador->joinInner(
                array('sg' => 'Segmento'),
                'pr.Segmento = sg.Codigo',
                array(),
                'SAC.dbo'
            );
            $slctContador->joinInner(
                array('tpr' => 'tbTipoReadequacao'),
                'tpr.idTipoReadequacao = r.idTipoReadequacao',
                array(),
                'SAC.dbo'
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctContador)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
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

    public function buscarProjetosProponente($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false, $fetchMode = Zend_DB::FETCH_OBJ)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(
                'idPronac' => 'p.IdPRONAC',
                'NomeProjeto' => 'p.NomeProjeto',
                'CgcCpf' => 'p.CgcCpf',
                'NrProjeto' => new Zend_Db_Expr('p.AnoProjeto+p.Sequencial'),
                'Solicitado' => new Zend_Db_Expr('SAC.dbo.fnValorSolicitado(p.AnoProjeto,p.Sequencial)'),
                'Aprovado' => new Zend_Db_Expr('SAC.dbo.fnAprovadoProjeto(p.AnoProjeto,p.Sequencial)'),
                'Captado' => new Zend_Db_Expr('SAC.dbo.fnCustoProjeto(p.AnoProjeto,p.Sequencial)'),
                'stEstado' => new Zend_Db_Expr('ISNULL((SELECT x.stAcao from tbArquivamento x where x.stAcao = 0 and stEstado = 1 and x.IdPRONAC = p.IdPRONAC),1)'),
                'Situacao' => new Zend_Db_Expr("p.Situacao + ' - ' + si.Descricao")
            )
        );
        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCpf = i.CgcCpf',
            array('i.Nome')
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('Segmento' => 's.Descricao')
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('Area' => 'a.Descricao')
        );
        $select->joinInner(
            array('si' => 'Situacao'),
            'p.Situacao = si.Codigo',
            array()
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($count) {
            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(
                array('p' => $this->_name),
                array('p.CgcCpf')
            );
            $slctContador->joinInner(
                array('i' => 'Interessado'),
                'p.CgcCpf = i.CgcCpf',
                array()
            );
            $slctContador->joinInner(
                array('s' => 'Segmento'),
                'p.Segmento = s.Codigo',
                array()
            );
            $slctContador->joinInner(
                array('a' => 'Area'),
                'p.Area = a.Codigo',
                array()
            );
            $slctContador->joinInner(
                array('si' => 'Situacao'),
                'p.Situacao = si.Codigo',
                array()
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }

            $slctContadorMaster = new Zend_Db_Expr('SELECT
                            count(*) AS "total"
                        FROM
                            (' . $slctContador . ') as master');

            $rs = $this->_db->fetchAll($slctContadorMaster);

            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
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

        $this->_db->setFetchMode($fetchMode);

        return $this->_db->fetchAll($select);
    }

    public function exibirResultadoProjetoSituacao($where, $QntdPorPagina, $PaginaAtual)
    {
        $filtroOrgao = '';
        if (isset($where['orgao']) && !empty($where['orgao'])) {
            $filtroOrgao = "AND Orgao = '" . $where['orgao'] . "'";
        }
        $TotalReg = $PaginaAtual * $QntdPorPagina;
        $select = new Zend_Db_Expr("
            SELECT * FROM (
                SELECT TOP " . $QntdPorPagina . " * FROM (
                    SELECT TOP " . $TotalReg . " * FROM SAC.dbo.Projetos
                    WHERE Situacao = '" . $where['situacao'] . "' $filtroOrgao ORDER BY IdPRONAC
                ) AS tabela ORDER BY IdPRONAC desc
            ) AS tabela ORDER BY IdPRONAC");
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }

        return $db->fetchAll($select);
    }

    public function imprimirResultadoProjetoSituacao($where)
    {
        $filtroOrgao = '';
        if (isset($where['orgao']) && !empty($where['orgao'])) {
            $filtroOrgao = "AND Orgao = '" . $where['orgao'] . "'";
        }

        $select = new Zend_Db_Expr("
            SELECT * FROM SAC.dbo.Projetos
            WHERE Situacao = '" . $where['situacao'] . "' $filtroOrgao ORDER BY IdPRONAC");
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }

        return $db->fetchAll($select);
    }

    /* Buscar Pela Situa??o da UC45 */

    public function BuscarPrestacaoContasSituacao($Situacao)
    {
        $select = $this->select();

        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->_name),
            array(
                'p.AnoProjeto', 'p.Sequencial', 'p.UfProjeto', 'p.NomeProjeto', 'p.IdPRONAC', 'p.Situacao', 'p.Mecanismo', 'p.NomeProjeto', 'p.idProjeto'
            )
        );

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array('i.CgcCPf'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao as Area'),
            'SAC.dbo'
        );

        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao as Segmento'),
            'SAC.dbo'
        );

        $select->where('p.Situacao = ?', $Situacao);
    }

    /**
     * PRESTACAO DE CONTAS
     */
    public function buscarProjetosPrestacaoContas($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false, $joinEncaminhamento = true, $dadosDiligencia = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)->distinct();
        if ($dadosDiligencia) {
            $select->from(
                array('p' => $this->_name),
                array('(p.AnoProjeto+p.Sequencial) as pronac',
                    'p.AnoProjeto',
                    'p.Sequencial',
                    'p.UfProjeto',
                    'p.NomeProjeto',
                    'p.IdPRONAC',
                    'p.DtSituacao',
                    'p.OrgaoOrigem',
                    'DtSolicitacao' => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac order by dili1.DtSolicitacao desc)'),
                    'DtResposta' => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac order by dili2.DtSolicitacao desc)'),
                    'stEnviado' => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac order by dili3.DtSolicitacao desc)'),
                )
            );
        } else {
            $select->from(
                array('p' => $this->_name),
                array('(p.AnoProjeto+p.Sequencial) as pronac',
                    'p.AnoProjeto',
                    'p.Sequencial',
                    'p.UfProjeto',
                    'p.NomeProjeto',
                    'p.IdPRONAC',
                    'p.DtSituacao',
                    'p.OrgaoOrigem',
                    'p.AnoProjeto as DtSolicitacao',
                    'p.AnoProjeto as DtResposta',
                    'p.AnoProjeto as stEnviado',
                )
            );
        }
        $select->joinInner(array('i' => 'Interessado'), 'p.CgcCPf = i.CgcCPf', array('i.CgcCPf'), 'SAC.dbo');
        $select->joinInner(array('a' => 'Area'), 'p.Area = a.Codigo', array('a.Descricao as Area'), 'SAC.dbo');
        $select->joinInner(array('s' => 'Segmento'), 'p.Segmento = s.Codigo', array('s.Descricao as Segmento'), 'SAC.dbo');
        $select->joinInner(array('m' => 'Mecanismo'), 'p.Mecanismo = m.Codigo', array('m.Descricao as Mecanismo'), 'SAC.dbo');
        $select->joinInner(array('sit' => 'Situacao'), 'sit.Codigo = p.Situacao', array('sit.Codigo'), 'SAC.dbo');

        if ($joinEncaminhamento) {
            $select->joinLeft(
                array('e' => 'tbEncaminhamentoPrestacaoContas'),
                'p.IdPRONAC = e.idPronac',
                array('e.idEncPrestContas', 'e.dtInicioEncaminhamento', 'e.dtFimEncaminhamento'),
                'BDCORPORATIVO.scSAC'
            );
        }

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        //Select COUNT
        if ($count) {
            $rs = $this->fetchAll(
                $this->select()->setIntegrityCheck(false)->from(
                    array('t2' => $select),
                    array("total" => new Zend_Db_Expr("count(*)"))
                )
            )->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
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

    //DILIGENCIADOS PRESTACAO CONTAS
    public function buscaProjetoDiligenciadosPrestacaoContas($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdTotal = false)
    {
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
        } else {
            $tmpInicio = null;
        }
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $selectQtdTotal = $this->select();
        $selectQtdTotal->setIntegrityCheck(false);
        if ($qtdTotal) {
            $select->from(array('p' => $this->_name), array('IdPronac'));
            $select->joinInner(array('i' => 'Interessado'), 'p.CgcCPf = i.CgcCPf', array(), 'SAC.dbo');
            $select->joinInner(array('a' => 'Area'), 'p.Area = a.Codigo', array(), 'SAC.dbo');
            $select->joinInner(array('s' => 'Segmento'), 'p.Segmento = s.Codigo', array(), 'SAC.dbo');
            $select->joinInner(array('m' => 'Mecanismo'), 'p.Mecanismo = m.Codigo', array(), 'SAC.dbo');
            $select->joinLeft(array('e' => 'tbEncaminhamentoPrestacaoContas'), 'p.IdPRONAC = e.idPronac', array(), 'BDCORPORATIVO.scSAC');
            $select->joinInner(
                array('d' => 'tbDiligencia'),
                'p.IdPRONAC = d.IdPRONAC and d.DtSolicitacao = (
                    SELECT top 1 d2.DtSolicitacao FROM SAC..tbDiligencia d2 WHERE d2.idPronac = d.idPronac ORDER BY d2.DtSolicitacao DESC
                )',
                array(),
                'SAC.dbo'
            );
        } else {
            if ($inicio < 0) {
                $select->from(
                    array('p' => $this->_name),
                    array(
                        '(p.AnoProjeto+p.Sequencial) as pronac',
                        'p.AnoProjeto',
                        'p.Sequencial',
                        'p.UfProjeto',
                        'p.NomeProjeto',
                        'p.IdPRONAC',
                        'p.DtSituacao',
                        'p.OrgaoOrigem',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac order by dili3.DtSolicitacao desc)'),
                    )
                );
            } else {
                $soma = $tamanho + $tmpInicio;
                $select->from(
                    array('p' => $this->_name),
                    array(
                        new Zend_Db_Expr("DISTINCT TOP $soma (p.AnoProjeto+p.Sequencial) as pronac"),
                        'p.AnoProjeto',
                        'p.Sequencial',
                        'p.UfProjeto',
                        'p.NomeProjeto',
                        'p.IdPRONAC',
                        'p.DtSituacao',
                        'p.OrgaoOrigem',
                        "DtSolicitacao" => new Zend_Db_Expr('(select top 1 DtSolicitacao from tbDiligencia dili1 where dili1.idPronac = p.idPronac order by dili1.DtSolicitacao desc)'),
                        "DtResposta" => new Zend_Db_Expr('(select top 1 DtResposta from tbDiligencia dili2 where dili2.idPronac = p.idPronac order by dili2.DtSolicitacao desc)'),
                        "stEnviado" => new Zend_Db_Expr('(select top 1 stEnviado from tbDiligencia dili3 where dili3.idPronac = p.idPronac order by dili3.DtSolicitacao desc)'),
                    )
                );
            }

            $select->joinInner(array('i' => 'Interessado'), 'p.CgcCPf = i.CgcCPf', array('i.CgcCPf'), 'SAC.dbo');
            $select->joinInner(array('a' => 'Area'), 'p.Area = a.Codigo', array('a.Descricao as Area'), 'SAC.dbo');
            $select->joinInner(array('s' => 'Segmento'), 'p.Segmento = s.Codigo', array('s.Descricao as Segmento'), 'SAC.dbo');
            $select->joinInner(array('m' => 'Mecanismo'), 'p.Mecanismo = m.Codigo', array('m.Descricao as Mecanismo'), 'SAC.dbo');
            $select->joinLeft(
                array('e' => 'tbEncaminhamentoPrestacaoContas'),
                'p.IdPRONAC = e.idPronac',
                array('e.idEncPrestContas', 'e.dtInicioEncaminhamento', 'e.dtFimEncaminhamento'),
                'BDCORPORATIVO.scSAC'
            );
            $select->joinInner(array('d' => 'tbDiligencia'), 'p.IdPRONAC = d.IdPRONAC', array(), 'SAC.dbo');
            $select->order($order);
        }

        foreach ($where as $key => $val) {
            $select->where($key, $val);
        }

        /* Consultas auxiliares 1 */
        $selectAux = $this->select();
        $selectAux->setIntegrityCheck(false);
        $selectAux->from($select, array(new Zend_Db_Expr("TOP $tamanho * ")));

        //troca a ordem do segundo select para a paginacao funcionar corretamente
        $neworder = $order;
        $tmpOrder = implode(',', $order);
        if (count($order) > 0) {
            //if(in_array('1 ASC',$tmpOrder)){
            if (!is_array($tmpOrder) && strpos($tmpOrder, 'ASC') > 0) {
                //$tmpOrder = implode(',', $order);
                $tmpOrder = explode(' ASC', $tmpOrder);
                $neworder = array($tmpOrder[0] . ' DESC');
            }
            if (!is_array($tmpOrder) && strpos($tmpOrder, 'DESC') > 0) {
                $tmpOrder = explode(' DESC', $tmpOrder);
                $neworder = array($tmpOrder[0] . ' ASC');
            }
        }
        $selectAux->order($neworder);

        /* Consultas auxiliares 2 */
        $selectAux2 = $this->select();
        $selectAux2->setIntegrityCheck(false);
        $selectAux2->from($selectAux);
        $selectAux2->order($order);

        if ($qtdTotal) {
            $selectQtdTotal->from(array('t2' => $select->distinct()), array("total" => new Zend_Db_Expr("count(*)")));
        }

        /* Resultado */
        if ($qtdTotal || $tmpInicio <= 0 && $qtdTotal || $tamanho == -1) {
            if ($qtdTotal) {
                $rs = $this->fetchAll($selectQtdTotal)->current();
                if ($rs) {
                    return $rs->total;
                } else {
                    return 0;
                }
            } else {
                return $this->fetchAll($select);
            }
        } else {
            return $this->fetchAll($selectAux2);
        }
    }

    //metodo descontinuado (mantido ate que o modulo seja homologado)
    public function buscaProjetoDiligenciadosPrestacaoContas_old2($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false)
    {
        $select = $this->select();
        $select->distinct();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(
                '(p.AnoProjeto+p.Sequencial) as pronac',
                'p.AnoProjeto',
                'p.Sequencial',
                'p.UfProjeto',
                'p.NomeProjeto',
                'p.IdPRONAC',
                'p.DtSituacao',
                'p.OrgaoOrigem'
            )
        );
        $select->joinInner(array('i' => 'Interessado'), 'p.CgcCPf = i.CgcCPf', array('i.CgcCPf'), 'SAC.dbo');
        $select->joinInner(array('a' => 'Area'), 'p.Area = a.Codigo', array('a.Descricao as Area'), 'SAC.dbo');
        $select->joinInner(array('s' => 'Segmento'), 'p.Segmento = s.Codigo', array('s.Descricao as Segmento'), 'SAC.dbo');
        $select->joinInner(array('m' => 'Mecanismo'), 'p.Mecanismo = m.Codigo', array('m.Descricao as Mecanismo'), 'SAC.dbo');
        $select->joinLeft(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac',
            array('e.idEncPrestContas', 'e.dtInicioEncaminhamento', 'e.dtFimEncaminhamento'),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('d' => 'tbDiligencia'),
            'p.IdPRONAC = d.IdPRONAC',
            array(
                'd.idSolicitante',
                'DtSolicitacao' => new Zend_Db_Expr("(select top 1 d2.DtSolicitacao from SAC.dbo.tbDiligencia  d2 where d2.idPronac = p.IdPRONAC order by d2.DtSolicitacao desc)"),
                'd.DtResposta as DtResposta',
                'd.stEnviado as stEnviado'
            ),
            'SAC.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        /*******************************/
        //Select COUNT
        if ($count) {
            $selectCount = $this->select();
            $selectCount->distinct();
            $selectCount->setIntegrityCheck(false);
            $selectCount->from(array('p' => $this->_name), array('total' => "count(*)"));
            $selectCount->joinInner(array('i' => 'Interessado'), 'p.CgcCPf = i.CgcCPf', array(), 'SAC.dbo');
            $selectCount->joinInner(array('a' => 'Area'), 'p.Area = a.Codigo', array(), 'SAC.dbo');
            $selectCount->joinInner(array('s' => 'Segmento'), 'p.Segmento = s.Codigo', array(), 'SAC.dbo');
            $selectCount->joinInner(array('m' => 'Mecanismo'), 'p.Mecanismo = m.Codigo', array(), 'SAC.dbo');
            $selectCount->joinLeft(
                array('e' => 'tbEncaminhamentoPrestacaoContas'),
                'p.IdPRONAC = e.idPronac',
                array(),
                'BDCORPORATIVO.scSAC'
            );
            $selectCount->joinInner(
                array('d' => 'tbDiligencia'),
                'p.IdPRONAC = d.IdPRONAC and d.DtSolicitacao = (
                    SELECT top 1 d2.DtSolicitacao FROM SAC..tbDiligencia d2 WHERE d2.idPronac = d.idPronac ORDER BY d2.DtSolicitacao DESC
                )',
                array(),
                'SAC.dbo'
            );
            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $selectCount->where($coluna, $valor);
            }

            $rs = $this->fetchAll($selectCount)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
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

    public function buscarProjProcuracao($where)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(
                'Pronac' => new Zend_Db_Expr('p.AnoProjeto + p.Sequencial')
            , 'p.CgcCpf'
            , 'p.NomeProjeto'
            , 'p.IdPRONAC'
            ),
            $this->_schema
        );

        $select->joinInner(
            array('a' => 'Agentes'),
            'p.CgcCpf = a.CNPJCPF',
            array('idAgente'),
            $this->getSchema('agentes')
        );

        $select->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array('Descricao as nmAgente'),
            $this->getSchema('agentes')
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) :
            $select->where($coluna, $valor);
        endforeach;

        $select->where('NOT EXISTS(SELECT * FROM Agentes.dbo.tbProcuradorProjeto pp WHERE pp.idPronac = p.IdPRONAC and pp.siEstado not in (1,3))');
        $select->order('p.NomeProjeto ASC');
        return $this->fetchAll($select);
    }

    public function listarProjetosProcuracoes($idResponsavel)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->distinct();
        $a->from(
            array('a' => $this->_name),
            array()
        );
        $a->joinInner(
            array('b' => 'tbProcuradorProjeto'),
            "a.IdPRONAC = b.idPronac",
            array(),
            $this->getSchema('agentes')
        );
        $a->joinInner(
            array('c' => 'tbProcuracao'),
            "b.idProcuracao = c.idProcuracao",
            array('idProcuracao', 'siProcuracao'),
            $this->getSchema('agentes')
        );
        $a->joinInner(
            array('e' => 'SGCacesso'),
            "a.CgcCpf = e.Cpf",
            array(),
            'CONTROLEDEACESSO.dbo'
        );
        $a->joinInner(
            array('f' => 'Agentes'),
            "f.idAgente = c.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $a->joinInner(
            array('g' => 'Nomes'),
            "g.idAgente = f.idAgente",
            array('Descricao AS Procurador'),
            $this->getSchema('agentes')
        );
        $a->joinInner(
            array('h' => 'tbDocumento'),
            "h.idDocumento = c.idDocumento",
            array('idDocumento'),
            'BDCORPORATIVO.scCorp'
        );
        $a->joinInner(
            array('i' => 'tbArquivo'),
            "i.idArquivo = h.idArquivo",
            array('dtEnvio', 'idArquivo', 'nmArquivo'),
            'BDCORPORATIVO.scCorp'
        );
        $a->joinInner(
            array('j' => 'Agentes'),
            "a.CgcCpf = j.CNPJCPF",
            array(),
            $this->getSchema('agentes')
        );
        $a->joinInner(
            array('l' => 'Nomes'),
            "j.idAgente = l.idAgente",
            array('Descricao AS Proponente'),
            $this->getSchema('agentes')
        );
//        $a->where('c.siProcuracao = ?', 1);
//        $a->where('b.siEstado = ?', 2);
        $a->where('e.IdUsuario = ?', $idResponsavel);


        $b = $this->select();
        $b->setIntegrityCheck(false);
        $b->distinct();
        $b->from(
            array('a' => $this->_name),
            array()
        );
        $b->joinInner(
            array('b' => 'tbProcuradorProjeto'),
            "a.IdPRONAC = b.idPronac",
            array(),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('c' => 'tbProcuracao'),
            "b.idProcuracao = c.idProcuracao",
            array('idProcuracao', 'siProcuracao'),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('f' => 'Agentes'),
            "f.idAgente = c.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('g' => 'Nomes'),
            "g.idAgente = f.idAgente",
            array('Descricao AS Procurador'),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('e' => 'SGCacesso'),
            "f.CNPJCPF = e.Cpf",
            array(),
            'CONTROLEDEACESSO.dbo'
        );
        $b->joinInner(
            array('h' => 'tbDocumento'),
            "h.idDocumento = c.idDocumento",
            array('idDocumento'),
            'BDCORPORATIVO.scCorp'
        );
        $b->joinInner(
            array('i' => 'tbArquivo'),
            "i.idArquivo = h.idArquivo",
            array('dtEnvio', 'idArquivo', 'nmArquivo'),
            'BDCORPORATIVO.scCorp'
        );
        $b->joinInner(
            array('j' => 'Agentes'),
            "a.CgcCpf = j.CNPJCPF",
            array(),
            $this->getSchema('agentes')
        );
        $b->joinInner(
            array('l' => 'Nomes'),
            "j.idAgente = l.idAgente",
            array('Descricao AS Proponente'),
            $this->getSchema('agentes')
        );

        $b->where('e.IdUsuario = ?', $idResponsavel);

        $slctUnion = $this->select()
            ->union(array('(' . $a . ')', '(' . $b . ')'))
            ->order('3', '8');


        return $this->fetchAll($slctUnion);
    }

    public function visualizarProcuracoes($idDocumento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('Pronac' => new Zend_Db_Expr('a.AnoProjeto + a.Sequencial'), 'a.NomeProjeto', 'a.IdPRONAC',
                new Zend_Db_Expr("CASE
                          WHEN b.siEstado = 0
                               THEN 'Aguardando v&iacute;nculo'
                          WHEN  b.siEstado = 1
                               THEN 'V&iacute;nculo rejeitado'
                          WHEN  b.siEstado = 2
                               THEN 'Vinculado'
                          WHEN  b.siEstado = 3
                               THEN 'Desvinculado'
                        END as stEstado")
            )
        );
        $select->joinInner(
            array('b' => 'tbProcuradorProjeto'),
            "a.IdPRONAC = b.idPronac",
            array('siEstado as status', 'idProcuradorProjeto'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('c' => 'tbProcuracao'),
            "b.idProcuracao = c.idProcuracao",
            array('idDocumento', 'siProcuracao', 'idProcuracao', 'dsObservacao'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('d' => 'Agentes'),
            "d.idAgente = c.idAgente",
            array(),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('e' => 'Nomes'),
            "e.idAgente = d.idAgente",
            array('Descricao as Procurador'),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('f' => 'Agentes'),
            "a.CgcCpf = f.CNPJCPF",
            array(),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('g' => 'Nomes'),
            "f.idAgente = g.idAgente",
            array('Descricao as Proponente'),
            $this->getSchema('agentes')
        );

        $select->where('c.idDocumento = ?', $idDocumento);

        return $this->fetchAll($select);
    }

    public function detalharProjetosProcuracao($idProcuracao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('Pronac' => new Zend_Db_Expr('a.AnoProjeto + a.Sequencial'), 'a.NomeProjeto')
        );
        $select->joinInner(
            array('b' => 'tbProcuradorProjeto'),
            "a.idPronac = b.IdPRONAC",
            array(),
            $this->getSchema('agentes')
        );
        $select->where('b.siEstado = ?', 0);
        $select->where('b.idProcuracao = ?', $idProcuracao);
        $select->order('a.NomeProjeto');

        return $this->fetchAll($select);
    }

    /**
     * @todo remover apos utilizar nova tela de listar projetos
     */
    public function listarProjetosConsulta($idResponsavel, $idProponente, $mecanismo)
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
                'DtInicioExecucao as DtInicioDeExecucao',
                'DtFimExecucao as DtFinalDeExecucao',
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
                'DtInicioExecucao as DtInicioDeExecucao',
                'DtFimExecucao as DtFinalDeExecucao',
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
                'DtInicioExecucao as DtInicioDeExecucao',
                'DtFimExecucao as DtFinalDeExecucao',
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

        $slctUnion = $this->select()
            ->union(array('(' . $a . ')', '(' . $b . ')', '(' . $c . ')'))
            ->order('Ordem')
            ->order('CgcCpf')
            ->order('NomeProjeto')
            ->order('idProjeto');
        return $this->fetchAll($slctUnion);
    }

    public function buscarProjetoEmails($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('p' => $this->_name),
            array()
        );
        $a->joinInner(
            array('pr' => 'PreProjeto'),
            "p.idProjeto = pr.idPreProjeto",
            array(),
            'SAC.dbo'
        );
        $a->joinInner(
            array('i' => 'Internet'),
            "i.idAgente = pr.idAgente",
            array('Descricao as Email'),
            $this->getSchema('agentes')
        );
        $a->where('p.IdPRONAC = ?', $idPronac);
        return $this->fetchAll($a);
    }

    public function buscarProponenteProjeto($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('p' => $this->_name),
            array('p.CgcCpf')
        );
        $a->where('p.IdPRONAC = ?', $idPronac);
        return $this->fetchRow($a);
    }

    public function buscarMetasComprovadas($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('vw' => 'vwMetasComprovadas'),
            array(new Zend_Db_Expr("Etapa,qtFisicaAprovada,qtFisicaExecutada,PerFisica,vlAprovado,vlExecutado,PercFinanceiro,SaldoAExecutar"))
        );
        $a->where('vw.idpronac = ?', $idPronac);
        $a->order('Etapa');
        return $this->fetchAll($a);
    }

    public function buscarItensComprovados($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('vw' => 'vwItensOrcamentariosComprovados'),
            array('*')
        );
        $a->where('vw.idpronac = ?', $idPronac);
        $a->order('Item');
        return $this->fetchAll($a);
    }

    public function buscarLocaisDeRealizacao($idPronac, $idLocal = null)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('a' => $this->_name),
            array('')
        );
        $a->joinInner(
            array('b' => 'Abrangencia'),
            "a.idProjeto = b.idProjeto",
            array('idAbrangencia', 'idProjeto', 'dtInicioRealizacao', 'dtFimRealizacao', 'idPais', 'idUF', 'idMunicipioIBGE', 'siAbrangencia', 'CAST(dsJustificativa AS TEXT) AS dsJustificativa'),
            'SAC.dbo'
        );
        $a->joinInner(
            array('c' => 'Pais'),
            "b.idPais = c.idPais",
            array('Descricao as Pais'),
            $this->getSchema('agentes')
        );
        $a->joinLeft(
            array('d' => 'UF'),
            "b.idUF = d.idUF",
            array('Descricao as UF'),
            $this->getSchema('agentes')
        );
        $a->joinLeft(
            array('e' => 'Municipios'),
            "b.idUF = e.idUFIBGE and b.idMunicipioIBGE = e.idMunicipioIBGE",
            array('Descricao as Municipio'),
            $this->getSchema('agentes')
        );
        $a->where('a.IdPRONAC = ?', $idPronac);
        $a->where('b.stAbrangencia = ?', 1);
        $a->order('b.idAbrangencia');

        if ($idLocal) {
            $a->where('b.idAbrangencia = ?', $idLocal);
        }

        return $this->fetchAll($a);
    }

    public function buscarPlanoDeDivulgacao($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('a' => $this->_name),
            array('')
        );
        $a->joinInner(
            array('b' => 'PlanoDeDivulgacao'),
            "a.idProjeto = b.idProjeto",
            array('idPlanoDivulgacao', 'idPeca', 'idVeiculo', 'siPlanoDeDivulgacao', 'idDocumento', 'Usuario'),
            'SAC.dbo'
        );
        $a->joinInner(
            array('c' => 'Verificacao'),
            "b.idPeca = c.idVerificacao",
            array('Descricao as Peca'),
            'SAC.dbo'
        );
        $a->joinInner(
            array('d' => 'Verificacao'),
            "b.idVeiculo = d.idVerificacao",
            array('Descricao as Veiculo'),
            'SAC.dbo'
        );
        $a->joinLeft(
            array('e' => 'tbDocumento'),
            "b.idDocumento = e.idDocumento",
            array(''),
            'BDCORPORATIVO.scCorp'
        );
        $a->joinLeft(
            array('f' => 'tbArquivo'),
            "e.idArquivo = f.idArquivo",
            array('idArquivo', 'nmArquivo', 'sgExtensao', 'dtEnvio'),
            'BDCORPORATIVO.scCorp'
        );
        $a->where('a.IdPRONAC = ?', $idPronac);
        return $this->fetchAll($a);
    }

    public function pedidosDeProrrogacao($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array('IdPRONAC', 'NomeProjeto', 'ResumoProjeto', 'DtInicioExecucao', 'DtFimExecucao', 'Situacao',
                new Zend_Db_Expr('p.AnoProjeto+p.Sequencial as PRONAC'),
                new Zend_Db_Expr('dbo.fnQtdeDeMesesDaUltimaCaptacao(p.AnoProjeto,p.Sequencial,getdate()) AS Meses'),
                new Zend_Db_Expr('dbo.fnPercentualCaptado(p.AnoProjeto,p.Sequencial) as Percentual')
            )
        );
        $select->joinInner(
            array('pr' => 'Prorrogacao'),
            "(p.AnoProjeto = pr.AnoProjeto and p.Sequencial = pr.Sequencial)",
            array('idProrrogacao', 'DtPedido', 'DtInicio', 'DtFinal', 'idDocumento', 'Observacao', 'Atendimento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('o' => 'Orgaos'),
            "p.Orgao = o.Codigo",
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('d' => 'tbDocumento'),
            "d.idDocumento = pr.idDocumento",
            array('idArquivo'),
            'BDCORPORATIVO.scCorp'
        );
        $select->joinLeft(
            array('a' => 'tbArquivo'),
            "d.idArquivo = a.idArquivo",
            array('nmArquivo'),
            'BDCORPORATIVO.scCorp'
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

    public function buscarDatasPrazos($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
            array('p' => $this->_name),
            array('idPronac', 'NomeProjeto', 'DtInicioExecucao', 'DtFimExecucao',
                new Zend_Db_Expr("tabelas.dbo.fnFormataDataHora('D/M/Y',dbo.fnInicioCaptacao(AnoProjeto,Sequencial)) as DtInicioCaptacao"),
                new Zend_Db_Expr("tabelas.dbo.fnFormataDataHora('D/M/Y',dbo.fnFimCaptacao(AnoProjeto,Sequencial)) as DtFimCaptacao")
            )
        );
        $a->where('p.IdPRONAC = ?', $idPronac);
        return $this->fetchAll($a);
    }

    public function painelAguardandoAnaliseDocumental($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pr' => $this->_name),
            array('NomeProjeto', 'IdPRONAC', 'cgccpf', 'DtInicioExecucao', 'DtFimExecucao',
                new Zend_Db_Expr("pr.anoprojeto + pr.sequencial AS pronac"),
                new Zend_Db_Expr("CASE
                                        WHEN en.enquadramento = 1 THEN '26'
                                        WHEN en.enquadramento = 2 THEN '18'
                                    END AS enquadramento"),
                new Zend_Db_Expr("CASE
                                         WHEN vp.stanaliseprojeto IS NULL THEN 'Aguardando An?lise'
                                         WHEN vp.stanaliseprojeto = '1' THEN 'Aguardando An?lise'
                                         WHEN vp.stanaliseprojeto = '2' THEN 'Em An?lise'
                                         WHEN vp.stanaliseprojeto = '3' THEN 'An?lise Finalizada'
                                         WHEN vp.stanaliseprojeto = '4' THEN 'Encaminhado para portaria'
                                       END AS Estado"),
                new Zend_Db_Expr("CASE
                                        WHEN pr.Mecanismo ='2' or pr.Mecanismo ='6'
                                            THEN sac.dbo.fnValorAprovadoConvenio(pr.AnoProjeto,pr.Sequencial)
                                            ELSE sac.dbo.fnValorAprovado(pr.AnoProjeto,pr.Sequencial)
                                        END AS ValorAprovado"),
                new Zend_Db_Expr("Datediff(day, vp.dtrecebido, GETDATE()) AS tempoAnalise")
            )
        );

        $select->joinInner(
            array('en' => 'Enquadramento'),
            'en.idpronac = pr.IdPRONAC',
            array(''),
            'SAC.dbo'
        );

        $select->joinLeft(
            array('tp' => 'tbpauta'),
            'tp.idpronac = pr.idpronac AND tp.dtenviopauta IN (SELECT TOP 1 Max(dtenviopauta) FROM bdcorporativo.scsac.tbpauta WHERE  idpronac = pr.idpronac)',
            array(''),
            'BDCORPORATIVO.scSAC'
        );

        $select->joinLeft(
            array('tr' => 'tbreuniao'),
            'tr.idnrreuniao = tp.idnrreuniao',
            array('nrreuniao'),
            'SAC.dbo'
        );

        $select->joinInner(
            array('vp' => 'tbverificaprojeto'),
            'vp.idpronac = pr.idpronac',
            array('dtrecebido', 'stAnaliseProjeto'),
            'SAC.dbo'
        );

        $select->joinInner(
            array('u' => 'Usuarios'),
            'vp.idUsuario = u.usu_codigo',
            array('usu_nome AS Tecnico'),
            'TABELAS.dbo'
        );

        $select->joinInner(
            array('ar' => 'Area'),
            'ar.Codigo = pr.Area',
            array('Descricao as dsArea'),
            'SAC.dbo'
        );

        $select->joinInner(
            array('sg' => 'Segmento'),
            'sg.Codigo = pr.Segmento',
            array('Descricao as dsSegmento'),
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

    public function buscarDadosUC75($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(new Zend_Db_Expr("p.IdPRONAC, p.idProjeto, p.AnoProjeto+p.Sequencial as NrProjeto,p.NomeProjeto,UfProjeto,a.Descricao as Area,
                            s.Descricao as Segmento, p.Mecanismo as idMecanismo, m.Descricao as Mecanismo,p.Situacao + ' - ' + si.Descricao as Situacao,
                            convert(varchar(10),DtSituacao,103) as DtSituacao,
                            CAST(p.ProvidenciaTomada AS TEXT) AS ProvidenciaTomada,
                            isnull(sac.dbo.fnValorDaProposta(idProjeto),
                            sac.dbo.fnValorSolicitado(p.AnoProjeto,p.Sequencial)) as ValorProposta,
                            sac.dbo.fnValorSolicitado(p.AnoProjeto,p.Sequencial) as ValorSolicitado,
                            sac.dbo.fnOutrasFontes(p.idPronac) as OutrasFontes,
                            case
                            when p.Mecanismo ='2' or p.Mecanismo ='6'
                            then sac.dbo.fnValorAprovadoConvenio(p.AnoProjeto,p.Sequencial)
                            else sac.dbo.fnValorAprovado(p.AnoProjeto,p.Sequencial)
                            end as ValorAprovado,
                            case
                            when p.Mecanismo ='2' or p.Mecanismo ='6'
                            then sac.dbo.fnValorAprovadoConvenio(p.AnoProjeto,p.Sequencial)
                            else sac.dbo.fnValorAprovado(p.AnoProjeto,p.Sequencial) + sac.dbo.fnOutrasFontes(p.idPronac) end as ValorProjeto,
                            sac.dbo.fnCustoProjeto (p.AnoProjeto,p.Sequencial) as ValorCaptado,
                            p.CgcCPf,n.Descricao as Proponente,sac.dbo.fnFormataProcesso(p.idPronac) as Processo,
                            tabelas.dbo.fnEstruturaOrgao(p.Orgao,0) as Origem,
                            h.Destino,h.DtTramitacaoEnvio,h.dtTramitacaoRecebida,h.Situacao as Estado,
                            sac.dbo.fnNomeUsuario(idUsuarioEmissor)  as Emissor,
                            sac.dbo.fnNomeUsuario(idUsuarioReceptor) as Receptor,
                            h.meDespacho,
                            CAST(p.ResumoProjeto AS TEXT) AS ResumoProjeto,case
                            when Enquadramento = '1'
                            then 'Artigo 26'
                            when Enquadramento = '2'
                            then 'Artigo 18'
                            else 'N&atilde;o enquadrado'
                            end as Enquadramento, p.Situacao as codSituacao,
                            sac.dbo.fnVlComprovadoProjeto($idPronac)AS vlComprovado,
                                CONVERT(varchar(10),sac.dbo.fnInicioCaptacao(p.AnoProjeto,p.Sequencial),103) as DtInicioCaptacao,
                                CONVERT(varchar(10),sac.dbo.fnFimCaptacao(p.AnoProjeto,p.Sequencial),103) as DtFimCaptacao
                            ")
            )
        );

        $select->joinLeft(
            array('e' => 'Enquadramento'),
            'p.idPronac = e.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('ag' => 'Agentes'),
            'p.CgcCPf = ag.CNPJCPF',
            array(''),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('n' => 'Nomes'),
            'ag.idAgente = n.idAgente',
            array(''),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('si' => 'Situacao'),
            'p.Situacao = si.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('h' => 'vwTramitarProjeto'),
            'p.idPronac = h.idPronac',
            array(''),
            'SAC.dbo'
        );

        $select->where('p.IdPRONAC = ?', $idPronac);


        return $this->fetchAll($select);
    }

    public function dadosImpressaoLaudo($Pronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("p.IdPRONAC, p.AnoProjeto+p.Sequencial AS pronac, p.NomeProjeto AS nomeProjeto,
                        p.CgcCPf, n.Descricao AS nomeProponente, SAC.dbo.fnFormataProcesso(p.idPronac) AS processo")
            )
        );

        $select->joinLeft(
            array('ag' => 'Agentes'),
            'p.CgcCPf = ag.CNPJCPF',
            array(''),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('n' => 'Nomes'),
            'ag.idAgente = n.idAgente',
            array(''),
            $this->getSchema('agentes')
        );
        $select->where('p.AnoProjeto+p.Sequencial = ?', $Pronac);


        return $this->fetchAll($select);
    }


    public function projetosCnicOpinioes($where = array(), $order = array())
    {
        $select1 = $this->select();
        $select1->setIntegrityCheck(false);
        $select1->from(
            array('t' => 'tbDistribuicaoProjetoComissao'),
            array(
                new Zend_Db_Expr("
                        p.IdPRONAC as idPronac,
                        p.AnoProjeto + p.Sequencial AS Pronac,
                        p.NomeProjeto,
                        z.Descricao as Proponente,
                        p.UfProjeto AS UF,
                        (SELECT TOP 1 m1.Descricao FROM Agentes.dbo.EnderecoNacional x1
                            INNER JOIN Agentes.dbo.UF u1 ON (x1.UF = u1.idUF)
                            INNER JOIN Agentes.dbo.Municipios m1 ON (x1.UF = m1.idUFIBGE AND x1.Cidade = m1.idMunicipioIBGE)
                            WHERE x.idAgente = x1.idAgente) AS Cidade,
                        CASE
                            WHEN e.Enquadramento = 1
                                THEN 'Artigo 26'
                            WHEN e.Enquadramento = 2
                                THEN 'Artigo 18'
                        END AS descEnquadramento,
                        a.Descricao AS dsArea,
                        se.Descricao AS dsSegmento,
                        CASE
                            WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                            WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                        END AS descAvaliacao,
                        p.SolicitadoReal as vlSolicitado,
                        (SELECT SUM(qtItem*nrOcorrencia*vlUnitario) FROM SAC.dbo.tbPlanilhaAprovacao pa WHERE pa.IdPRONAC = p.IdPRONAC AND stAtivo = 'S' and pa.nrFonteRecurso=109) AS vlSugerido,
                        p.ResumoProjeto
                    ")
            ),
            'BDCORPORATIVO.scSAC'
        );

        $select1->joinInner(
            array('p' => 'Projetos'),
            't.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select1->joinInner(
            array('pr' => 'Parecer'),
            'pr.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select1->joinLeft(
            array('e' => 'Enquadramento'),
            'e.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select1->joinInner(
            array('s' => 'Situacao'),
            'p.Situacao = s.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select1->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select1->joinInner(
            array('se' => 'Segmento'),
            'p.Segmento = se.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select1->joinInner(
            array('n' => 'Nomes'),
            't.idAgente = n.idAgente',
            array(''),
            $this->getSchema('agentes')
        );
        $select1->joinInner(
            array('x' => 'Agentes'),
            'p.CgcCpf = x.CNPJCPF',
            array(''),
            $this->getSchema('agentes')
        );
        $select1->joinInner(
            array('z' => 'Nomes'),
            'x.idAgente = z.idAgente',
            array(''),
            $this->getSchema('agentes')
        );
        $select1->where('t.stDistribuicao = ?', 'A');
        $select1->where('pr.stAtivo = ?', 1);
        $select1->where('z.Status = ?', 0);
        $select1->where('p.Situacao in (?)', array('C10', 'C30'));
        $select1->where('NOT EXISTS(SELECT TOP 1 * FROM BDCORPORATIVO.scSAC.tbPauta  o  WHERE o.IdPRONAC = p.IdPronac)', '');


        $select2 = $this->select();
        $select2->setIntegrityCheck(false);
        $select2->from(
            array('t' => 'tbPauta'),
            array(
                new Zend_Db_Expr("
                        p.IdPRONAC as idPronac,
                        p.AnoProjeto + p.Sequencial AS Pronac,
                        p.NomeProjeto,
                        y.Descricao as Proponente,
                        p.UfProjeto AS UF,
                        (SELECT TOP 1 m1.Descricao FROM Agentes.dbo.EnderecoNacional x1
                            INNER JOIN Agentes.dbo.UF u1 ON (x1.UF = u1.idUF)
                            INNER JOIN Agentes.dbo.Municipios m1 ON (x1.UF = m1.idUFIBGE AND x1.Cidade = m1.idMunicipioIBGE)
                            WHERE x.idAgente = x1.idAgente) AS Cidade,
                        CASE
                            WHEN e.Enquadramento = 1
                                THEN 'Artigo 26'
                            WHEN e.Enquadramento = 2
                                THEN 'Artigo 18'
                        END AS descEnquadramento,
                        a.Descricao AS dsArea,
                        se.Descricao AS dsSegmento,
                        CASE
                            WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                            WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                        END AS descAvaliacao,
                        p.SolicitadoReal as vlSolicitado,
                        (SELECT SUM(qtItem*nrOcorrencia*vlUnitario) FROM SAC.dbo.tbPlanilhaAprovacao pa WHERE pa.IdPRONAC = p.IdPRONAC AND stAtivo = 'S' and pa.nrFonteRecurso=109) AS vlSugerido,
                        p.ResumoProjeto
                    ")
            ),
            'BDCORPORATIVO.scSAC'
        );

        $select2->joinInner(
            array('z' => 'tbDistribuicaoProjetoComissao'),
            't.IdPRONAC = z.idPRONAC',
            array(''),
            'BDCORPORATIVO.scSAC'
        );
        $select2->joinInner(
            array('p' => 'Projetos'),
            't.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select2->joinInner(
            array('pr' => 'Parecer'),
            'pr.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select2->joinLeft(
            array('e' => 'Enquadramento'),
            'e.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select2->joinInner(
            array('s' => 'Situacao'),
            'p.Situacao = s.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select2->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select2->joinInner(
            array('se' => 'Segmento'),
            'p.Segmento = se.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select2->joinInner(
            array('n' => 'Nomes'),
            'z.idAgente = n.idAgente',
            array(''),
            $this->getSchema('agentes')
        );
        $select2->joinInner(
            array('x' => 'Agentes'),
            'p.CgcCpf = x.CNPJCPF',
            array(''),
            $this->getSchema('agentes')
        );
        $select2->joinInner(
            array('y' => 'Nomes'),
            'x.idAgente = y.idAgente',
            array(''),
            $this->getSchema('agentes')
        );
        $select2->joinInner(
            array('r' => 'tbReuniao'),
            't.idNrReuniao = r.idNrReuniao',
            array(''),
            'SAC.dbo'
        );
        $select2->where('z.stDistribuicao = ?', 'A');
        $select2->where('pr.idTipoAgente = ?', 6);
        $select2->where('pr.stAtivo = ?', 1);
        $select2->where('r.stEstado = ?', 0);
        $select2->where('y.Status = ?', 0);

        $slctUnion = $this->select()->union((array('(' . $select1 . ')', '(' . $select2 . ')')), 'UNION ALL');

        //adicionando linha order ao select
        $slctUnion->order($order);


        return $this->fetchAll($slctUnion);
    }

    /**
     * Retorna Os Projetos da CNIC por IdNrReuniao
     * @param $idNrReuniao
     * @param array $where
     * @param array $order
     * @return Zend_Db_Table_Rowset_Abstract
     * @throws Zend_Db_Select_Exception
     */
    public function projetosCnicOpinioesPorIdReuniao($idNrReuniao = null, $where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => 'tbPauta'),
            array(
                new Zend_Db_Expr("
                        p.IdPRONAC as idPronac,
                        p.AnoProjeto + p.Sequencial AS Pronac,
                        p.NomeProjeto,
                        y.Descricao as Proponente,
                        p.UfProjeto AS UF,
                        (SELECT TOP 1 m1.Descricao FROM Agentes.dbo.EnderecoNacional x1
                            INNER JOIN Agentes.dbo.UF u1 ON (x1.UF = u1.idUF)
                            INNER JOIN Agentes.dbo.Municipios m1 ON (x1.UF = m1.idUFIBGE AND x1.Cidade = m1.idMunicipioIBGE)
                            WHERE x.idAgente = x1.idAgente) AS Cidade,
                        CASE
                            WHEN e.Enquadramento = 1
                                THEN 'Artigo 26'
                            WHEN e.Enquadramento = 2
                                THEN 'Artigo 18'
                        END AS descEnquadramento,
                        a.Descricao AS dsArea,
                        se.Descricao AS dsSegmento,
                        CASE
                            WHEN pr.ParecerFavoravel = '1' THEN 'Desfavor&aacute;vel'
                            WHEN pr.ParecerFavoravel = '2' THEN 'Favor&aacute;vel'
                        END AS descAvaliacao,
                        SolicitadoReal AS vlSolicitado,
                        CASE
                            WHEN p.Mecanismo ='2' OR p.Mecanismo ='6'
                                THEN sac.dbo.fnValorAprovadoConvenio(p.AnoProjeto,p.Sequencial)
                            ELSE sac.dbo.fnTotalAprovadoProjeto(p.AnoProjeto,p.Sequencial)
                        END AS vlAprovado,
                        sac.dbo.fnCustoProjeto (p.AnoProjeto,p.Sequencial) AS vlCaptado,

                        ResumoProjeto,
			DtInicioExecucao,
			DtFimExecucao

                    ")
            ),
            'BDCORPORATIVO.scSAC'
        );

        $select->joinInner(
            array('z' => 'tbDistribuicaoProjetoComissao'),
            't.IdPRONAC = z.idPRONAC',
            array(''),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('pr' => 'Parecer'),
            'pr.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('e' => 'Enquadramento'),
            'e.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Situacao'),
            'p.Situacao = s.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('se' => 'Segmento'),
            'p.Segmento = se.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('x' => 'Agentes'),
            'p.CgcCpf = x.CNPJCPF',
            array(''),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('y' => 'Nomes'),
            'x.idAgente = y.idAgente',
            array(''),
            $this->getSchema('agentes')
        );

        if (!is_null($idNrReuniao)) {
            $select->joinInner(
                array('r' => 'tbReuniao'),
                't.idNrReuniao = r.idNrReuniao',
                array(''),
                'SAC.dbo'
            );
            $select->where('r.idNrReuniao = ?', $idNrReuniao);
        }

        $select->where('z.stDistribuicao = ?', 'A');
        $select->where('pr.idTipoAgente = ?', 6);
        $select->where('pr.stAtivo = ?', 1);
        #$select->where('r.stEstado = ?', 0);
        $select->where('y.Status = ?', 0);

        // adiciona quantos filtros foram enviados
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

    public function countProjetosCnicOpinioesPorIdReuniao($idNrReuniao = null, $where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('t' => 'tbPauta'),
            array(
                new Zend_Db_Expr("
                      count ( p.IdPRONAC) as total
                    ")
            ),
            'BDCORPORATIVO.scSAC'
        );

        $select->joinInner(
            array('z' => 'tbDistribuicaoProjetoComissao'),
            't.IdPRONAC = z.idPRONAC',
            array(''),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinInner(
            array('p' => 'Projetos'),
            't.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('pr' => 'Parecer'),
            'pr.idPronac = p.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('x' => 'Agentes'),
            'p.CgcCpf = x.CNPJCPF',
            array(''),
            $this->getSchema('agentes')
        );
        $select->joinInner(
            array('y' => 'Nomes'),
            'x.idAgente = y.idAgente',
            array(''),
            $this->getSchema('agentes')
        );

        if (!is_null($idNrReuniao)) {
            $select->joinInner(
                array('r' => 'tbReuniao'),
                't.idNrReuniao = r.idNrReuniao',
                array(''),
                'SAC.dbo'
            );
            $select->where('r.idNrReuniao = ?', $idNrReuniao);
        }

        $select->where('z.stDistribuicao = ?', 'A');
        $select->where('pr.idTipoAgente = ?', 6);
        $select->where('pr.stAtivo = ?', 1);
        #$select->where('r.stEstado = ?', 0);
        $select->where('y.Status = ?', 0);

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }
        return $this->fetchRow($select);
    }

    public function cidadaoDadosProjeto($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.IdPRONAC, p.AnoProjeto+p.Sequencial AS PRONAC,
                    p.NomeProjeto,
                    UfProjeto,
                    a.Descricao AS Area,
                    s.Descricao AS Segmento,
                    m.Descricao AS Mecanismo,
                    isnull(sac.dbo.fnValorDaProposta(idProjeto),sac.dbo.fnValorSolicitado(p.AnoProjeto,p.Sequencial)) AS ValorProposta,
                    sac.dbo.fnValorSolicitado(p.AnoProjeto,p.Sequencial) AS ValorSolicitado,
                    sac.dbo.fnOutrasFontes(p.idPronac) AS OutrasFontes,
                    CASE
                        WHEN p.Mecanismo ='2' or p.Mecanismo ='6'
                        THEN SAC.dbo.fnValorAprovadoConvenio(p.AnoProjeto,p.Sequencial)
                        ELSE SAC.dbo.fnValorAprovado(p.AnoProjeto,p.Sequencial)
                    END AS ValorAprovado,
                    CASE
                        WHEN p.Mecanismo ='2' or p.Mecanismo ='6'
                        THEN SAC.dbo.fnValorAprovadoConvenio(p.AnoProjeto,p.Sequencial)
                        ELSE SAC.dbo.fnValorAprovado(p.AnoProjeto,p.Sequencial) + sac.dbo.fnOutrasFontes(p.idPronac) end as ValorProjeto,
                    SAC.dbo.fnCustoProjeto (p.AnoProjeto,p.Sequencial) AS ValorCaptado,
                    Nome AS Proponente,
                    CAST(p.ResumoProjeto AS TEXT) AS ResumoProjeto,
                    CASE
                        WHEN Enquadramento = '1'
                        THEN 'Artigo 26'
                        WHEN Enquadramento = '2'
                        THEN 'Artigo 18'
                        ELSE 'N&atilde;o enquadrado'
                    END AS Enquadramento,
                    pr.Objetivos,
                    pr.Justificativa,
                    pr.Acessibilidade,
                    pr.DemocratizacaoDeAcesso,
                    pr.EtapaDeTrabalho,
                    pr.FichaTecnica
                ")
            )
        );
        $select->joinInner(
            array('pr' => 'PreProjeto'),
            'p.idProjeto = pr.idPreProjeto',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('e' => 'Enquadramento'),
            'p.idPronac = e.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array(''),
            'SAC.dbo'
        );

        // adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }


        return $this->fetchAll($select);
    }

    // fecha metodo buscarProjetoXProponente()

    /**
     *
     * @param type $idPronac
     * @param type $situacaoDestino
     * @param type $situacaoOrigem
     * @throws BadFunctionCallException
     */
    public function mudarSituacao($idPronac, $situacaoDestino, $situacaoOrigem = null)
    {
        $this->getAdapter()->getProfiler()->setEnabled(true);

        $projetoRow = $this->fetchRow($this->select()->where('IdPRONAC = ?', $idPronac));
        if ($situacaoOrigem && $situacaoOrigem != $projetoRow->Situacao) {
            throw new BadFunctionCallException(
                "A situa&ccedil;&atilde;o do projeto ({$projetoRow->Situacao}) n&atilde;o &eacute; a mesma "
                . "da situa&ccedil;&atilde;o de origem ({$situacaoOrigem})"
            );
        }
        $projetoRow->Situacao = $situacaoDestino;
        $projetoRow->save();
    }

    /*
     * Alterada em 24/04/2014
     * @author: Jefferson Alessandro
     * Essa consulta retorna os dados do painel de relatorios geral de analise - Perfil: Coordenador de Pronac
     */
    public function painelRelatoriosGeralAnalise($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.idPronac,
                    p.AnoProjeto+p.Sequencial AS Pronac,
                    p.NomeProjeto,
                    pr.Descricao AS Produto,
                    p.DtProtocolo As DtPrimeiroEnvio,
                    d.DtEnvio AS DtUltimoEnvio,
                    d.DtDistribuicao,
                    d.dtDevolucao,
                    DATEDIFF(day, d.DtEnvio, isnull(d.DtDistribuicao,GETDATE())) AS QtdeDiasParaDistribuir,
                    CASE
                        WHEN d.DtDevolucao is null
                            THEN DATEDIFF(day, d.DtDistribuicao, GETDATE())
                        WHEN d.DtDevolucao is not null
                            THEN DATEDIFF(day, d.DtDistribuicao, d.DtDevolucao)
                        WHEN Situacao = 'B14'
                            THEN DATEDIFF(day, d.DtDistribuicao, GETDATE()) - DATEDIFF(day, p.dtSituacao, GETDATE())
                    END as QtdeDiasParaParecAnalisar,
                    CASE
                        WHEN d.DtDevolucao is not null and d.DtRetorno is null AND d.FecharAnalise=0
                            THEN DATEDIFF(day, d.DtDevolucao,GETDATE())
                    END as QtdeDiasDevolvidosParaCoord,
                    ISNULL(n.Descricao,'') AS Parecerista,
                    p.DtInicioExecucao,
                    p.DtFimExecucao,
                    DATEDIFF(day,GETDATE(),
                    p.DtInicioExecucao) AS QtdeDiasVencido,
                    CASE
                        WHEN dbo.fnchecarDiligencia(p.idPronac) = 0
                        THEN 'Sem Dilig�ncia'
                        WHEN dbo.fnchecarDiligencia(p.idPronac) = 1
                        THEN 'Diligenciado'
                        WHEN dbo.fnchecarDiligencia(p.idPronac) = 2
                        THEN 'Dilig�ncia Respondida'
                    END AS Diligencia,
                    o.Sigla as Vinculada
                ")
            )
        );
        $select->joinInner(
            array('d' => 'tbDistribuirParecer'),
            'p.idPronac = d.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('o' => 'Orgaos'),
            'd.idOrgao = o.codigo',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('n' => 'Nomes'),
            'd.idAgenteParecerista = n.idAgente',
            array(''),
            $this->getSchema('agentes')
        );
        $select->joinLeft(
            array('pr' => 'Produto'),
            'd.idProduto = pr.Codigo',
            array(''),
            'SAC.dbo'
        );
        $select->where('d.TipoAnalise in (?)', array(1, 3));
        $select->where('d.stEstado = ?', 0);
        $select->where('p.Situacao in (?)', array('B11', 'B14'));
        $select->where('d.FecharAnalise = ?', 0);

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

    /*
     * Alterada em 28/01/2015
     * @author: Jefferson Alessandro
     * Essa consulta retorna os dados do painel de presta��o de contas - Perfil: Coordenador de Presta��o de Contas
     */
    public function buscarPainelPrestacaoDeContas($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false, $filtro = '')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        if ($filtro == 'emanalise') {
            $select->from(
                array('p' => $this->_name),
                array(
                    new Zend_Db_Expr("
                        p.IdPRONAC AS idPronac,
                        (p.AnoProjeto+p.Sequencial) AS Pronac,
                        p.NomeProjeto,
                        p.Situacao,
                        e.dtInicioEncaminhamento,
                        DATEDIFF(day, e.dtInicioEncaminhamento, GETDATE()) AS qtDiasAnalise
                    ")
                )
            );
        } else {
            $select->from(
                array('p' => $this->_name),
                array(
                    new Zend_Db_Expr("
                        p.IdPRONAC AS idPronac,
                        (p.AnoProjeto+p.Sequencial) AS Pronac,
                        p.NomeProjeto,
                        p.Situacao
                    ")
                )
            );
        }

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao AS Mecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sit' => 'Situacao'),
            'sit.Codigo = p.Situacao',
            array(''),
            'SAC.dbo'
        );

        if ($filtro == 'devolvidos' || $filtro == 'tce' || $filtro == 'diligenciados' || $filtro == 'emanalise') {
            $select->joinLeft(
                array('e' => 'tbEncaminhamentoPrestacaoContas'),
                'p.IdPRONAC = e.idPronac',
                array('e.dtInicioEncaminhamento', 'e.dtFimEncaminhamento', 'e.idEncPrestContas'),
                'BDCORPORATIVO.scSAC'
            );
        }

        if ($filtro == 'diligenciados') {
            $select->joinInner(
                array('d' => 'tbDiligencia'),
                'p.IdPRONAC = d.IdPRONAC',
                array(''),
                'SAC.dbo'
            );
        }

        if ($filtro == 'emanalise') {
            $select->joinInner(
                array('u' => 'Usuarios'),
                'e.idAgenteDestino = u.usu_codigo',
                array('usu_nome'),
                'TABELAS.dbo'
            );
        }
        if ($filtro == 'analisados') {
            $select->joinInner(
                array('e' => 'tbEncaminhamentoPrestacaoContas'),
                'p.IdPRONAC = e.idPronac AND e.stAtivo = 1',
                array('e.idSituacaoEncPrestContas'),
                'BDCORPORATIVO.scSAC'
            );
        }
        if ($filtro == 'tce') {
            $select->joinInner(
                array('d' => 'tbDiligencia'),
                'p.IdPRONAC = d.IdPRONAC',
                array(''),
                'SAC.dbo'
            );
        }

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

    /*
     * Criada em 12/02/2015
     * @author: Jefferson Alessandro
     * Essa consulta retorna os dados do painel de presta��o de contas - Perfil: T�cnico de Presta��o de Contas
     */
    public function buscarPainelTecPrestacaoDeContas($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false, $filtro = '')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.IdPRONAC AS idPronac,
                    (p.AnoProjeto+p.Sequencial) AS Pronac,
                    p.NomeProjeto,
                    p.UfProjeto,
                    p.DtSituacao,
                    p.Situacao,
                    CASE
                        WHEN r.idRelatorioTecnico is null
                        THEN 'False'
                        ELSE 'True'
                    END AS 'RelatorioTecnico',
                    ISNULL(usu_Nome, ' ') as Tecnico
                ")
            )
        );
        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao AS Mecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sit' => 'Situacao'),
            'sit.Codigo = p.Situacao',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac AND e.stAtivo = 1',
            array(''),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('u' => 'Usuarios'),
            'e.idAgenteDestino = u.usu_codigo',
            array(''),
            'TABELAS.DBO'
        );
        $select->joinLeft(
            array('r' => 'tbRelatorioTecnico'),
            'r.idPRONAC = p.IdPRONAC AND cdGrupo = 124',
            array(''),
            'SAC.DBO'
        );

        if ($filtro == 'diligenciados') {
            $select->joinInner(
                array('d' => 'tbDiligencia'),
                'p.IdPRONAC = d.IdPRONAC and d.DtSolicitacao = (
                    SELECT top 1 d2.DtSolicitacao FROM SAC..tbDiligencia d2 WHERE d2.idPronac = d.idPronac ORDER BY d2.DtSolicitacao DESC
                )',
                array(),
                'SAC.dbo'
            );
        }

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

    /*
     * Criada em 07/04/2016
     * @author: Fern�o Lopes
     * Essa consulta retorna os dados do painel de presta��o de contas - Perfil: Chefe de divis�o
     */
    public function buscarPainelChefeDivisaoPrestacaoDeContas($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false, $filtro = '')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        // em aguardando an�lise, nao puxa t�cnico nem relat�rio
        if ($filtro == '') {
            $select->from(
                array('p' => $this->_name),
                array(
                    new Zend_Db_Expr("
                        p.IdPRONAC AS idPronac,
                        (p.AnoProjeto+p.Sequencial) AS Pronac,
                        p.NomeProjeto,
                        p.Situacao
                    ")
                )
            );
        } else {
            $select->from(
                array('p' => $this->_name),
                array(
                    new Zend_Db_Expr("
                        p.IdPRONAC AS idPronac,
                        (p.AnoProjeto+p.Sequencial) AS Pronac,
                        p.NomeProjeto,
                        p.Situacao,
                        ISNULL(usu_Nome, ' ') as Tecnico
                    ")
                )
            );
        }

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao AS Mecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sit' => 'Situacao'),
            'sit.Codigo = p.Situacao',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac AND e.stAtivo = 1',
            array(''),
            'BDCORPORATIVO.scSAC'
        );

        // se n�o for 'aguardando an�lise'
        if ($filtro != '') {
            $select->joinLeft(
                array('u' => 'Usuarios'),
                'e.idAgenteDestino = u.usu_codigo',
                array(''),
                'TABELAS.DBO'
            );
        }

        if ($filtro == 'diligenciados') {
            $select->joinInner(
                array('d' => 'tbDiligencia'),
                'p.IdPRONAC = d.IdPRONAC and d.DtSolicitacao = (
                    SELECT top 1 d2.DtSolicitacao FROM SAC..tbDiligencia d2 WHERE d2.idPronac = d.idPronac ORDER BY d2.DtSolicitacao DESC
                )',
                array(),
                'SAC.dbo'
            );
        }

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


    /*
     * Criada em 26/02/2015
     * @author: Jefferson Alessandro
     * Essa consulta retorna os dados do painel de presta��o de contas - Perfil: Coord. Geral de Presta��o de Contas
     */
    public function buscarPainelCoordGeralPrestDeContas($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.IdPRONAC AS idPronac,
                    (p.AnoProjeto+p.Sequencial) AS Pronac,
                    p.NomeProjeto,
                    p.UfProjeto,
                    p.DtSituacao,
                    p.Situacao
                ")
            )
        );
        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao AS Mecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sit' => 'Situacao'),
            'sit.Codigo = p.Situacao',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac',
            array(''),
            'BDCORPORATIVO.scSAC'
        );
        $select->joinLeft(
            array('l' => 'tbLaudoFinal'),
            'p.IdPRONAC = l.idPronac',
            array(''),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('rt' => 'tbRelatorioTecnico'),
            'p.IdPRONAC = rt.idPronac',
            array('rt.siManifestacao'),
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

    public function painelDadosBancariosExtrato($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('vwExtratoDaMovimentacaoBancaria');

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

    public function painelDadosConciliacaoBancaria($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('vwConciliacaoBancaria');

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

    public function inconsistenciasComprovacao($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('vwInconsistenciaNaComprovacao');

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

    public function extratoDeSaldoBancario($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('vwExtratoDeSaldoDasContasBancarias');

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

    public function extratoContaMovimentoConsolidado($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $qtdeTotal = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from('vwExtratoDaContaMovimentoConsolidado');

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

    public function obterInteressadoProjeto($idPronac)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            array('Projetos' => 'Projetos'),
            array('NomeProjeto'),
            $this->_schema
        );
        $objQuery->joinInner(
            array('Interessado' => 'Interessado'),
            'Interessado.CgcCpf = Projetos.CgcCpf',
            'Nome',
            $this->_schema
        );
        $objQuery->where('Projetos.IdPRONAC = ?', $idPronac);

        return $this->_db->fetchRow($objQuery);
    }

    public function verificarLiberacaoParaAdequacao($idPronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchOne('select sac.dbo.fnChecarLiberacaoDaAdequacaoDoProjeto (?)', $idPronac);
    }

    public function verificarIN2017($idPronac)
    {
        $fnVerificarProjetoAprovadoIN2017 = new fnVerificarProjetoAprovadoIN2017();

        return $fnVerificarProjetoAprovadoIN2017->verificar($idPronac);
    }

    public function painelPrestacaoDeContasAguardandoAnalise($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.IdPRONAC AS idPronac,
                    (p.AnoProjeto+p.Sequencial) AS Pronac,
                    p.NomeProjeto,
                    p.Situacao
                ")
            )
        );

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao AS Mecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sit' => 'Situacao'),
            'sit.Codigo = p.Situacao',
            array(''),
            'SAC.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }

    public function painelPrestacaoDeContasEmAnalise($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.IdPRONAC AS idPronac,
                    (p.AnoProjeto+p.Sequencial) AS Pronac,
                    p.NomeProjeto,
                    p.Situacao,
                    e.dtInicioEncaminhamento,
                    DATEDIFF(day, e.dtInicioEncaminhamento, GETDATE()) AS qtDiasAnalise
                ")
            )
        );

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao AS Mecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sit' => 'Situacao'),
            'sit.Codigo = p.Situacao',
            array(''),
            'SAC.dbo'
        );

        $select->joinLeft(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac',
            array('e.dtInicioEncaminhamento', 'e.dtFimEncaminhamento', 'e.idEncPrestContas'),
            'BDCORPORATIVO.scSAC'
        );

        $select->joinInner(
            array('u' => 'Usuarios'),
            'e.idAgenteDestino = u.usu_codigo',
            array('usu_nome'),
            'TABELAS.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    }

    public function painelPrestacaoDeContasAnalisados($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.IdPRONAC AS idPronac,
                    (p.AnoProjeto+p.Sequencial) AS Pronac,
                    p.NomeProjeto,
                    p.Situacao
                ")
            )
        );

        $select->joinInner(
            array('i' => 'Interessado'),
            'p.CgcCPf = i.CgcCPf',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Area'),
            'p.Area = a.Codigo',
            array('a.Descricao AS Area'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('s' => 'Segmento'),
            'p.Segmento = s.Codigo',
            array('s.Descricao AS Segmento'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('m' => 'Mecanismo'),
            'p.Mecanismo = m.Codigo',
            array('m.Descricao AS Mecanismo'),
            'SAC.dbo'
        );
        $select->joinInner(
            array('sit' => 'Situacao'),
            'sit.Codigo = p.Situacao',
            array(''),
            'SAC.dbo'
        );

        $select->joinInner(
            array('e' => 'tbEncaminhamentoPrestacaoContas'),
            'p.IdPRONAC = e.idPronac AND e.stAtivo = 1',
            array('e.idSituacaoEncPrestContas'),
            'BDCORPORATIVO.scSAC'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    }

    public function buscarProjetoTransferidor($idPronac)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
            array('p' => $this->_name),
            array(
                new Zend_Db_Expr("
                    p.IdPRONAC AS idPronac,
                    (p.AnoProjeto+p.Sequencial) AS pronac,
                    p.nomeProjeto,
                    p.CgcCpf,
                    (SELECT sac.dbo.fnVlAComprovarProjeto(idPronac)) AS valorAComprovar
                ")
            )
        );

        $select->joinInner(
            ['a' => 'Area'],
            'a.Codigo = p.Area',
            [
                'a.Descricao as area',
                'a.Codigo as codArea'
            ]
        );

        $select->where('idPronac = ?', $idPronac);

        $projeto = $this->fetchAll($select);
        if (count($projeto) > 0) {
            return $projeto->current();
        } else {
            return false;
        }
    }

    public function buscarProjetosRecebedoresDisponiveis($idPronac)
    {
        $projetos = [];
        try {
            $select = $this->select();
            $select->where('idPronac = ?', $idPronac);
            $projetoAtual = $this->fetchAll($select)->current();

        } catch (Exception $objException) {
            $this->view->message = $objException->getMessage();
            return [];
        }

        $areasProjetosOutrosProponentes = [
            Area::AREA_PATRIMONIO_CULTURAL,
            Area::AREA_MUSEUS_MEMORIA
        ];

        $select = $this->select();
        $select->where('idPronac != ?', $projetoAtual->IdPRONAC);

        $select->where(new Zend_Db_Expr('DtInicioExecucao > GETDATE() AND DtFimExecucao < GETDATE()'));
        $select->where(new Zend_Db_Expr('(SELECT SAC.DBO.fnNrPortariaAprovacao(AnoProjeto,Sequencial)) IS NOT NULL'));

        if (!in_array($projetoAtual->Area, $areasProjetosOutrosProponentes)) {
            $select->where('CgcCpf = ?', $projetoAtual->CgcCpf);
        }

        $projetos = $this->fetchAll($select);

        return $projetos;
    }

    public function verificarPronacDisponivelReceber($idPronac, $pronacRecebedor, $idReadequacao)
    {
        try {

            $TbSolicitacaoTransferenciaRecursos = new Readequacao_Model_DbTable_TbSolicitacaoTransferenciaRecursos();
            $projetosRecebedores = $TbSolicitacaoTransferenciaRecursos->obterProjetosRecebedores($idReadequacao);

            $listaProjetosRecebedores = [];

            foreach ($projetosRecebedores as $projeto) {
                $listaProjetosRecebedores[] = $projeto->idPronacRecebedor;
            }

            $select = $this->select();

            if (!empty($listaProjetosRecebedores)) {
                $select->where('IdPRONAC NOT IN (?)', $listaProjetosRecebedores);
            }
            $select->where('IdPRONAC != ?', $idPronac);
            $select->where(new Zend_Db_Expr('AnoProjeto + Sequencial = ?'), $pronacRecebedor);

            $projeto = $this->fetchAll($select);
            $saida = [];

            if (count($projeto) > 0) {
                $saida['disponivel'] = true;
                $saida['nomeProjeto'] = $projeto->current()['NomeProjeto'];
                $saida['idPronac'] = $projeto->current()['IdPRONAC'];
            } else {
                $saida['disponivel'] = false;
                $saida['nomeProjeto'] = '';
            }

            return $saida;

        } catch (Exception $objException) {
            throw $objException;
        }
    }

    public function alterarProvidenciaTomada(
        $idPronac,
        $providenciaTomada
    )
    {
        $projeto = $this->buscarSituacaoAtual($idPronac);
        $dadosHistorico = [
            'AnoProjeto' => $projeto['AnoProjeto'],
            'Sequencial' => $projeto['Sequencial'],
            'DtSituacao' => $projeto['DtSituacao'],
            'Situacao' => $projeto['Situacao'],
            'ProvidenciaTomada' => $projeto['ProvidenciaTomada'],
            'Logon' => $projeto['Logon']
        ];
        $HistoricoSituacao = new HistoricoSituacao();
        $HistoricoSituacao->insert($dadosHistorico);

        $auth = Zend_Auth::getInstance();
        $dados = [
            'DtSituacao' => new Zend_Db_Expr('GETDATE()'),
            'ProvidenciaTomada' => $providenciaTomada,
            'Logon' => $auth->getIdentity()->usu_codigo
        ];

        return $this->update($dados, [
            "IdPRONAC = ?" => $idPronac
        ]);
    }

    public function buscarDadosCompletos($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name, "*");
        $select->where("idPronac = ?", $idPronac);
        return $this->fetchAll($select);
    }
}
