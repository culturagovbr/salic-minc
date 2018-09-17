<?php

class Assinatura_Model_DbTable_TbDocumentoAssinatura extends MinC_Db_Table_Abstract
{
    protected $_schema    = 'sac';
    protected $_name      = 'tbDocumentoAssinatura';
    protected $_primary   = 'idDocumentoAssinatura';

    public function obterDocumentoAssinatura($idPronac, $idTipoDoAtoAdministrativo)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            '*',
            $this->_schema
        );
        $objQuery->where('IdPRONAC = ?', $idPronac);
        $objQuery->where('idTipoDoAtoAdministrativo = ?', $idTipoDoAtoAdministrativo);

        $result = $this->fetchRow($objQuery);
        if ($result) {
            return $result->toArray();
        }
    }

    public function obterProjetosEncaminhadosParaAssinatura($codOrgao = null, $ordenacao = array())
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);

        $queryPlanilhaOrcamentaria = $this->select();
        $queryPlanilhaOrcamentaria->setIntegrityCheck(false);
        $queryPlanilhaOrcamentaria->from(
            'tbPlanilhaAprovacao',
            array(
                "vlAprovado" => new Zend_Db_Expr(
                    "tbPlanilhaAprovacao.vlUnitario * tbPlanilhaAprovacao.qtItem * tbPlanilhaAprovacao.nrOcorrencia"
                )
            ),
            $this->_schema
        );
        $queryPlanilhaOrcamentaria->where("tbPlanilhaAprovacao.IdPRONAC = projetos.IdPRONAC");

        $query->from(
            array("Projetos" => "Projetos"),
            array(
                'pronac' => new Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
                'Projetos.nomeProjeto',
                'Projetos.IdPRONAC',
                'Projetos.CgcCpf',
                'Projetos.Area as cdarea',
                'Projetos.ResumoProjeto',
                'Projetos.UfProjeto',
                'Projetos.DtInicioExecucao',
                'Projetos.DtFimExecucao',
                'Projetos.Situacao',
                'Projetos.DtSituacao',
                'Projetos.Orgao',
                'tbDocumentoAssinatura.cdSituacao',
                'tbDocumentoAssinatura.stEstado',
                'dias' => 'DATEDIFF(DAY, projetos.DtSituacao, GETDATE())',
                '(' . $queryPlanilhaOrcamentaria->assemble() . ') as vlAprovado'
            ),
            $this->_schema
        );

        $query->joinInner(
            array('Area' => 'Area'),
            "Area.Codigo = Projetos.Area",
            "Area.Descricao as area",
            $this->_schema
        );

        $query->joinInner(
            array('Segmento' => 'Segmento'),
            "Segmento.Codigo = Projetos.Segmento",
            array(
                "Segmento.Descricao as segmento",
                "Segmento.tp_enquadramento"
            ),
            $this->_schema
        );

        $query->joinInner(
            array('tbDocumentoAssinatura' => 'tbDocumentoAssinatura'),
            "tbDocumentoAssinatura.IdPRONAC = Projetos.IdPRONAC",
            array(
                "tbDocumentoAssinatura.idTipoDoAtoAdministrativo"
            ),
            $this->_schema
        );

        $query->joinLeft(
            array('Enquadramento' => 'Enquadramento'),
            "Enquadramento.IdPRONAC = Projetos.IdPRONAC
             AND Enquadramento.AnoProjeto = Projetos.AnoProjeto
             AND Enquadramento.Sequencial = Projetos.Sequencial",
            array("Enquadramento.IdEnquadramento"),
            $this->_schema
        );

        if ($codOrgao) {
            $query->where("Projetos.Orgao = ?", $codOrgao);
        }

        $query->where("tbDocumentoAssinatura.cdSituacao = ?", Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA);
        $query->where("tbDocumentoAssinatura.stEstado = ?", Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO);
        $query->where("Projetos.Situacao in (?)", array('B04'));
        $query->order($ordenacao);

        return $this->_db->fetchAll($query);
    }

    public function obterProjetosComAssinaturasAbertas(
        $idOrgaoDoAssinante,
        $idPerfilDoAssinante,
        $idOrgaoSuperiorDoAssinante,
        $idTipoDoAtoAdministrativos = []
    ) {

        $query = $this->select();
        $query->setIntegrityCheck(false);

        $query->from(
            array("Projetos" => "Projetos"),
            array(
                'pronac' => new Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
                'Projetos.nomeProjeto',
                'Projetos.IdPRONAC',
                'Projetos.CgcCpf',
                'Projetos.Area as cdarea',
                'Projetos.ResumoProjeto',
                'Projetos.UfProjeto',
                'Projetos.DtInicioExecucao',
                'Projetos.DtFimExecucao',
                'Projetos.Situacao',
                'Projetos.DtSituacao',
                'Projetos.Orgao',
                'tbDocumentoAssinatura.cdSituacao',
                'tbDocumentoAssinatura.stEstado',
                'tbDocumentoAssinatura.idDocumentoAssinatura',
                'possuiAssinatura'=> new Zend_Db_Expr(" 
                    (select top 1 {$this->_schema}.TbAssinatura.idAssinatura 
                       from {$this->_schema}.TbAssinatura
                      inner join {$this->_schema}.TbAtoAdministrativo 
                         ON {$this->_schema}.TbAtoAdministrativo.idAtoAdministrativo = {$this->_schema}.TbAssinatura.idAtoAdministrativo
                        AND {$this->_schema}.TbAtoAdministrativo.idOrgaoDoAssinante = {$idOrgaoDoAssinante}
                        AND {$this->_schema}.TbAtoAdministrativo.idPerfilDoAssinante = {$idPerfilDoAssinante}
                        AND {$this->_schema}.TbAtoAdministrativo.idOrgaoSuperiorDoAssinante = {$idOrgaoSuperiorDoAssinante}
                      WHERE {$this->_schema}.TbAssinatura.idDocumentoAssinatura = {$this->_schema}.tbDocumentoAssinatura.idDocumentoAssinatura
                      AND {$this->_schema}.TbAtoAdministrativo.idTipoDoAto = {$this->_schema}.tbDocumentoAssinatura.idTipoDoAtoAdministrativo)
                "),
                'possuiProximaAssinatura'=> new Zend_Db_Expr("
                    (
                    
                    select top 1 {$this->_schema}.TbAtoAdministrativo.idOrdemDaAssinatura
                      from {$this->_schema}.TbAtoAdministrativo
                     where {$this->_schema}.TbAtoAdministrativo.idTipoDoAto = {$this->_schema}.tbDocumentoAssinatura.idTipoDoAtoAdministrativo
                       and {$this->_schema}.TbAtoAdministrativo.idOrdemDaAssinatura > (
                       
                         select top 1 {$this->_schema}.TbAtoAdministrativo.idOrdemDaAssinatura 
                           from {$this->_schema}.TbAssinatura
                          inner join {$this->_schema}.TbAtoAdministrativo 
                             ON {$this->_schema}.TbAtoAdministrativo.idAtoAdministrativo = {$this->_schema}.TbAssinatura.idAtoAdministrativo
                            AND {$this->_schema}.TbAtoAdministrativo.idOrgaoDoAssinante = {$idOrgaoDoAssinante}
                            AND {$this->_schema}.TbAtoAdministrativo.idPerfilDoAssinante = {$idPerfilDoAssinante}
                            AND {$this->_schema}.TbAtoAdministrativo.idOrgaoSuperiorDoAssinante = {$idOrgaoSuperiorDoAssinante}
                          WHERE {$this->_schema}.TbAssinatura.idDocumentoAssinatura = {$this->_schema}.tbDocumentoAssinatura.idDocumentoAssinatura
                          AND {$this->_schema}.TbAtoAdministrativo.idTipoDoAto = {$this->_schema}.tbDocumentoAssinatura.idTipoDoAtoAdministrativo
                      )
                      order by idOrdemDaAssinatura asc
                    )
                ")
            ),
            $this->_schema
        );

        $query->joinInner(
            array('Area' => 'Area'),
            "Area.Codigo = Projetos.Area",
            "Area.Descricao as area",
            $this->_schema
        );

        $query->joinInner(
            array('Segmento' => 'Segmento'),
            "Segmento.Codigo = Projetos.Segmento",
            array(
                "Segmento.Descricao as segmento",
                "Segmento.tp_enquadramento"
            ),
            $this->_schema
        );

        $query->joinInner(
            array('Orgaos'),
            "Orgaos.Codigo = Projetos.Orgao",
            [],
            $this->_schema
        );

        $query->joinInner(
            array('tbDocumentoAssinatura' => 'tbDocumentoAssinatura'),
            "tbDocumentoAssinatura.IdPRONAC = Projetos.IdPRONAC",
            array(
                "tbDocumentoAssinatura.idTipoDoAtoAdministrativo"
            ),
            $this->_schema
        );

        $query->joinInner(
            array('Verificacao' => 'Verificacao'),
            "Verificacao.idVerificacao = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            'Verificacao.Descricao as tipoDoAtoAdministrativo',
            $this->_schema
        );

        $query->joinLeft(
            array('Enquadramento' => 'Enquadramento'),
            "Enquadramento.IdPRONAC = Projetos.IdPRONAC
             AND Enquadramento.AnoProjeto = Projetos.AnoProjeto
             AND Enquadramento.Sequencial = Projetos.Sequencial",
            array("Enquadramento.IdEnquadramento"),
            $this->_schema
        );

        $query->joinInner(
            array('TbAtoAdministrativo' => 'TbAtoAdministrativo'),
            "TbAtoAdministrativo.idOrgaoDoAssinante = {$idOrgaoDoAssinante}
             AND TbAtoAdministrativo.idPerfilDoAssinante = {$idPerfilDoAssinante}
             AND TbAtoAdministrativo.idOrgaoSuperiorDoAssinante = {$idOrgaoSuperiorDoAssinante}
             AND TbAtoAdministrativo.idTipoDoAto = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            array(),
            $this->_schema
        );

        $query->where("TbAtoAdministrativo.idOrgaoDoAssinante = ?", $idOrgaoDoAssinante);
        $query->where("tbDocumentoAssinatura.cdSituacao = ?", Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA);
        $query->where("tbDocumentoAssinatura.stEstado = ?", Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO);

        if ($idTipoDoAtoAdministrativos) {
            $query->where("tbDocumentoAssinatura.idTipoDoAtoAdministrativo in (?)", $idTipoDoAtoAdministrativos);
        }
        $query->where("Orgaos.idSecretaria = ?", $idOrgaoSuperiorDoAssinante);

        $ordenacao[] = 'possuiAssinatura asc';
        $query->order($ordenacao);

        return $this->_db->fetchAll($query);
    }

    public function isProjetoDisponivelParaAssinatura(
        $idPronac,
        $idTipoDoAtoAdministrativo
    )
    {
        $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $where = array(
            'IdPRONAC = ?' => $idPronac,
            'idTipoDoAtoAdministrativo in (?)' => $idTipoDoAtoAdministrativo,
            'cdSituacao = ?' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
            'stEstado' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
        );

        return (count($objModelDocumentoAssinatura->findBy($where)) > 1);
    }

    public function obterProjetoDisponivelParaAssinatura(
        $idPronac,
        $idTipoDoAtoAdministrativo
    )
    {
        $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $where = array(
            'IdPRONAC = ?' => $idPronac,
            'idTipoDoAtoAdministrativo in (?)' => $idTipoDoAtoAdministrativo,
            'cdSituacao = ?' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
            'stEstado' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
        );

        return $objModelDocumentoAssinatura->findBy($where);
    }

    public function obterDocumentosAssinadosPorProjeto($idPronac)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            ["tbDocumentoAssinatura" => $this->_name],
            [
                'tbDocumentoAssinatura.idTipoDoAtoAdministrativo',
                "tbDocumentoAssinatura.idDocumentoAssinatura",
                "tbDocumentoAssinatura.dt_criacao"
            ],
            $this->_schema
        );
        
        $objQuery->joinInner(
            ["Projetos" => "Projetos"],
            "tbDocumentoAssinatura.IdPRONAC = Projetos.IdPRONAC",
            [
                'pronac' => new Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
                'Projetos.nomeProjeto',
                'Projetos.IdPRONAC',
            ],
            $this->_schema
        );

        $objQuery->joinInner(
            ["Verificacao" => "Verificacao"],
            "(tbDocumentoAssinatura.idTipoDoAtoAdministrativo = Verificacao.idVerificacao)",
            [],
            $this->_schema
        );

        $objQuery->joinLeft(
            ["tbReadequacaoXParecer" => "tbReadequacaoXParecer"],
            "(tbReadequacaoXParecer.idParecer = tbDocumentoAssinatura.idAtoDeGestao)",
            [],
            $this->_schema
        );

        $objQuery->joinLeft(
            ["tbReadequacao" => "tbReadequacao"],
            "(tbReadequacao.idReadequacao = tbReadequacaoXParecer.idReadequacao)",
            [],
            $this->_schema
        );
        
        $objQuery->joinLeft(
            ["tbTipoReadequacao" => "tbTipoReadequacao"],
            "(tbTipoReadequacao.idTipoReadequacao = tbReadequacao.idTipoReadequacao)",
            [
                "dsAtoAdministrativo" => new Zend_Db_Expr("CASE WHEN tbDocumentoAssinatura.idTipoDoAtoAdministrativo IN(" .
                                                         Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_TECNICO_READEQUACAO_VINCULADAS . "," .
                                                         Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_TECNICO_AJUSTE_DE_PROJETO . "," .
                                                         Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_TECNICO_READEQUACAO_PROJETOS_MINC . ")
                                                               THEN Verificacao.Descricao + ' - ' + tbTipoReadequacao.dsReadequacao 
                                                               ELSE Verificacao.Descricao END")
            ],
            $this->_schema
        );        
        
        $objQuery->where('tbDocumentoAssinatura.stEstado = ?', 1);
        $objQuery->where('tbDocumentoAssinatura.cdSituacao = ?', 2);
        $objQuery->where('tbDocumentoAssinatura.IdPRONAC = ?', $idPronac);
        $objQuery->order('tbDocumentoAssinatura.dt_criacao ASC');
        
        return $this->_db->fetchAll($objQuery);
    }

    public function getIdDocumentoAssinatura($idPronac, $idTipoDoAtoAdministrativo)
    {
        $objQuery = $this->select(); 
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            '*',
            $this->_schema
        );
        
        $objQuery->where('IdPRONAC = ?', $idPronac);
        $objQuery->where('idTipoDoAtoAdministrativo = ?', $idTipoDoAtoAdministrativo);
        $objQuery->where('stEstado = ?', 1);
        
        $result = $this->fetchRow($objQuery);
        if ($result) {
            $resultadoArray = $result->toArray();
            return $resultadoArray['idDocumentoAssinatura'];
        }
    }    
}
