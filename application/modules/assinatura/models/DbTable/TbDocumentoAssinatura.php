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
        $idTipoDoAtoAdministrativo = null
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

        $query->where("Projetos.Orgao = ?", $idOrgaoDoAssinante);
        $query->where("tbDocumentoAssinatura.cdSituacao = ?", Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA);
        $query->where("tbDocumentoAssinatura.stEstado = ?", Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO);

        if ($idTipoDoAtoAdministrativo) {
            $query->where("tbDocumentoAssinatura.idTipoDoAtoAdministrativo = ?", $idTipoDoAtoAdministrativo);
        }

        $ordenacao[] = 'possuiAssinatura asc';
        $query->order($ordenacao);
        return $this->_db->fetchAll($query);
    }

    public function isProjetoDisponivelParaAssinatura($idPronac, $idTipoDoAtoAdministrativo)
    {
        $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $where = array(
            'IdPRONAC = ?' => $idPronac,
            'idTipoDoAtoAdministrativo = ?' => $idTipoDoAtoAdministrativo,
            'cdSituacao = ?' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
            'stEstado' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
        );

        return (count($objModelDocumentoAssinatura->findBy($where)) > 1);
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
            [
                'Verificacao.Descricao as dsAtoAdministrativo'
            ],
            $this->_schema
        );

        $objQuery->where('tbDocumentoAssinatura.stEstado = ?', 1);
        $objQuery->where('tbDocumentoAssinatura.cdSituacao = ?', 2);
        $objQuery->where('tbDocumentoAssinatura.IdPRONAC = ?', $idPronac);

        return $this->_db->fetchAll($objQuery);
    }
}
