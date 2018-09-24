<?php

/**
 * Class Assinatura_Model_DbTable_TbAssinatura
 */
class Assinatura_Model_DbTable_TbAssinatura extends MinC_Db_Table_Abstract
{
    /**
     * @var Assinatura_Model_TbAssinatura $modeloTbAssinatura
     */
    public $modeloTbAssinatura;

    /**
     * @var Assinatura_Model_TbAtoAdministrativo $modeloTbAtoAdministrativo
     */
    public $modeloTbAtoAdministrativo;

    protected $_schema = 'sac';
    protected $_name = 'tbAssinatura';
    protected $_primary = 'idAssinatura';

    const TIPO_ATO_LAUDO_PRESTACAO_CONTAS = 622;
    const TIPO_ATO_ENQUADRAMENTO = 626;
    const TIPO_ATO_ANALISE_INICIAL = 630;
    const TIPO_ATO_ANALISE_CNIC = 631;
    const TIPO_ATO_HOMOLOGAR_PROJETO = 643;
    const TIPO_ATO_PARECER_TECNICO_READEQUACAO_VINCULADAS = 653;
    const TIPO_ATO_PARECER_TECNICO_AJUSTE_DE_PROJETO = 654;
    const TIPO_ATO_PARECER_TECNICO_READEQUACAO_PROJETOS_MINC = 655;

    public function preencherModeloAssinatura(array $dados)
    {
        $this->modeloTbAssinatura = new Assinatura_Model_TbAssinatura($dados);
        return $this;
    }

    public function preencherModeloAtoAdministrativo(array $dados)
    {
        $this->modeloTbAtoAdministrativo = new Assinatura_Model_TbAtoAdministrativo($dados);
        return $this;
    }

    /**
     * Esse metodo deve retornar Objeto
     */
    public function obterAssinaturas(
        $idPronac,
        $idTipoDoAtoAdministrativo,
        $idDocumentoAssinatura = null
    )
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);

        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            array(
                'tbAssinatura' => $this->_name
            ),
            '*',
            $this->_schema
        );

        $objQuery->joinInner(
            'tbAtoAdministrativo',
            'tbAssinatura.idAtoAdministrativo = tbAtoAdministrativo.idAtoAdministrativo',
            'tbAtoAdministrativo.*',
            $this->_schema
        );

        $objQuery->joinInner(
            'Verificacao',
            'Verificacao.idVerificacao = tbAtoAdministrativo.idCargoDoAssinante',
            array('dsCargoAssinante' => 'Verificacao.Descricao'),
            $this->getSchema('Agentes')
        );

        $objQuery->joinInner(
            'usuarios',
            'tbAssinatura.idAssinante = usuarios.usu_codigo',
            array(
                'usuarios.usu_identificacao',
                'usuarios.usu_nome'
            ),
            $this->getSchema('tabelas')
        );
        $objQuery->where("IdPRONAC = ?", $idPronac);
        $objQuery->where("tbAtoAdministrativo.idTipoDoAto = ?", $idTipoDoAtoAdministrativo);
        if (!is_null($idDocumentoAssinatura)) {
            $objQuery->where("tbAssinatura.idDocumentoAssinatura = ?", $idDocumentoAssinatura);
        }
        return $this->_db->fetchAll($objQuery);
    }

    public function obterProjetosAssinados(
        $idOrgaoSuperiorDoAssinante,
        $idAssinante = null
    )
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);

        $objQuery->from(
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
                'tbDocumentoAssinatura.idDocumentoAssinatura',
                'tbDocumentoAssinatura.cdSituacao',
                'tbDocumentoAssinatura.stEstado'
            ),
            $this->_schema
        );

        $objQuery->joinInner(
            array('Area' => 'Area'),
            "Area.Codigo = Projetos.Area",
            "Area.Descricao as area",
            $this->_schema
        );

        $objQuery->joinInner(
            array('Segmento' => 'Segmento'),
            "Segmento.Codigo = Projetos.Segmento",
            array(
                "Segmento.Descricao as segmento",
                "Segmento.tp_enquadramento"
            ),
            $this->_schema
        );

        $objQuery->joinInner(
            array('tbDocumentoAssinatura' => 'tbDocumentoAssinatura'),
            "tbDocumentoAssinatura.IdPRONAC = Projetos.IdPRONAC",
            array(
                "tbDocumentoAssinatura.idTipoDoAtoAdministrativo"
            ),
            $this->_schema
        );

        $objQuery->joinInner(
            array('tbAssinatura' => 'tbAssinatura'),
            "tbAssinatura.idDocumentoAssinatura = tbDocumentoAssinatura.idDocumentoAssinatura",
            "",
            $this->_schema
        );

        $objQuery->joinInner(
            array('tbAtoAdministrativo' => 'tbAtoAdministrativo'),
            "tbAtoAdministrativo.idAtoAdministrativo = tbAssinatura.idAtoAdministrativo",
            "",
            $this->_schema
        );

        $objQuery->joinInner(
            array('Verificacao' => 'Verificacao'),
            "Verificacao.idVerificacao = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            'Verificacao.Descricao as tipoDoAtoAdministrativo',
            $this->_schema
        );

        if ($idAssinante) {
            $objQuery->where(new Zend_Db_Expr(

                'tbDocumentoAssinatura.idDocumentoAssinatura IN (
                SELECT distinct idDocumentoAssinatura from "sac"."dbo"."tbAssinatura"
                 where "sac"."dbo"."tbAssinatura".idAssinante = ' . $idAssinante . '
             )'
            ));
        }
        $objQuery->where("{$this->_schema}.tbAtoAdministrativo.idOrgaoSuperiorDoAssinante = ?", $idOrgaoSuperiorDoAssinante);
        $ordenacao[] = 'tbDocumentoAssinatura.dt_criacao desc';
        $objQuery->order($ordenacao);
        return $this->_db->fetchAll($objQuery);
    }

    /**
     * @return bool
     * @throws Exception
     * @uses Assinatura_Model_TbAssinatura $modelAssinatura
     */
    public function isProjetoAssinado()
    {
        if (!$this->modeloTbAssinatura) {
            throw new Exception("&Eacute; necess&aacute;rio definir uma entidade de Assinatura.");
        }


        if (is_null($this->modeloTbAssinatura->getIdAtoAdministrativo())) {
            throw new Exception("Identificador do Ato Administrativo n&atilde;o informado.");
        }

        if (is_null($this->modeloTbAssinatura->getIdDocumentoAssinatura())) {
            throw new Exception("Identificador do Documento de Assinatura n&atilde;o informado.");
        }

        $assinaturaExistente = $this->buscar(array(
            'idAtoAdministrativo = ?' => $this->modeloTbAssinatura->getIdAtoAdministrativo(),
            'idDocumentoAssinatura = ?' => $this->modeloTbAssinatura->getIdDocumentoAssinatura()
        ));

        if ($assinaturaExistente->current()) {
            return true;
        }
        return false;
    }

    public function obterAssinaturasDisponiveis()
    {
        $query = $this->obterQueryAssinaturasDisponiveis();
        $query = $this->filtrarBuscaDatatable($query);

        return $this->_db->fetchAll($query);
    }

    public function obterQuantidadeAssinaturasRealizadas()
    {
        if (is_null($this->modeloTbAssinatura->getIdDocumentoAssinatura())) {
            throw new Exception("`Identificador do Documento Assinatura n&atilde;o informado.");
        }

        $query = $this->select();
        $query->setIntegrityCheck(false);

        $query->from(
            [$this->_name],
            ['quantidade' => new Zend_Db_Expr('COUNT(*)')],
            $this->_schema
        );

        $query->where("idDocumentoAssinatura = ?", $this->modeloTbAssinatura->getIdDocumentoAssinatura());
        $quantidadeAssinaturas = $this->_db->fetchRow($query);
        
        if ($quantidadeAssinaturas) {
            return $quantidadeAssinaturas['quantidade'];
        }
    }
        
    public function obterQueryAssinaturasDisponiveis(): \MinC_Db_Table_Select
    {
        if (!$this->modeloTbAtoAdministrativo) {
            throw new Exception("&Eacute; necess&aacute;rio definir uma entidade de Assinatura.");
        }

        if (is_null($this->modeloTbAtoAdministrativo->getIdOrgaoDoAssinante())) {
            throw new Exception("`Identificador do Org&atilde;o` n&atilde;o informado.");
        }

        if (is_null($this->modeloTbAtoAdministrativo->getIdPerfilDoAssinante())) {
            throw new Exception("`Identificador do Perfil` n&atilde;o informado.");
        }

        if (is_null($this->modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante())) {
            throw new Exception("`Identificador do Org&atilde;o Superior` n&atilde;o informado.");
        }

        if (is_null($this->modeloTbAtoAdministrativo->getIdTipoDoAto())) {
            throw new Exception("`Identificador do Tipo do Ato Administrativo` n&atilde;o informado.");
        }

        $query = $this->select();
        $query->setIntegrityCheck(false);

        $sqlQuantidadeAssinaturas = "(select count(*) 
                                        from TbAssinatura
                                       where idPronac = Projetos.IdPRONAC
                                         and idDocumentoAssinatura = tbDocumentoAssinatura.idDocumentoAssinatura)";

        $sqlTotalQuantidadeAssinaturas = "(SELECT count(1) 
                                             FROM TbAtoAdministrativo TbAtoAdministrativoInterno 
                                            WHERE TbAtoAdministrativoInterno.idOrgaoSuperiorDoAssinante = {$this->modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante()}
                                              AND TbAtoAdministrativoInterno.idTipoDoAto = {$this->_schema}.tbDocumentoAssinatura.idTipoDoAtoAdministrativo)";

        $query->from(
            array("Projetos" => "Projetos"),
            array(
                'pronac' => new Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
                'Projetos.nomeProjeto',
                'Projetos.IdPRONAC',
                'Projetos.CgcCpf',
                'Projetos.Area as cdarea',
                'Projetos.ResumoProjeto',
                'Projetos.Orgao',
                'dias' => 'DATEDIFF(DAY, projetos.DtSituacao, GETDATE())',
                'tbDocumentoAssinatura.idDocumentoAssinatura',
                'possuiProximaAssinatura' => new Zend_Db_Expr("
                    (
                    
                    select top 1 {$this->_schema}.TbAtoAdministrativo.idOrdemDaAssinatura
                      from {$this->_schema}.TbAtoAdministrativo
                     where {$this->_schema}.TbAtoAdministrativo.idTipoDoAto = {$this->_schema}.tbDocumentoAssinatura.idTipoDoAtoAdministrativo
                       and {$this->_schema}.TbAtoAdministrativo.idOrdemDaAssinatura > (
                       
                         select top 1 {$this->_schema}.TbAtoAdministrativo.idOrdemDaAssinatura 
                           from {$this->_schema}.TbAssinatura
                          inner join {$this->_schema}.TbAtoAdministrativo 
                             ON {$this->_schema}.TbAtoAdministrativo.idAtoAdministrativo = {$this->_schema}.TbAssinatura.idAtoAdministrativo
                            AND {$this->_schema}.TbAtoAdministrativo.idOrgaoDoAssinante = {$this->modeloTbAtoAdministrativo->getIdOrgaoDoAssinante()}
                            AND {$this->_schema}.TbAtoAdministrativo.idPerfilDoAssinante = {$this->modeloTbAtoAdministrativo->getIdPerfilDoAssinante()}
                            AND {$this->_schema}.TbAtoAdministrativo.idOrgaoSuperiorDoAssinante = {$this->modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante()}
                          WHERE {$this->_schema}.TbAssinatura.idDocumentoAssinatura = {$this->_schema}.tbDocumentoAssinatura.idDocumentoAssinatura
                          AND {$this->_schema}.TbAtoAdministrativo.idTipoDoAto = {$this->_schema}.tbDocumentoAssinatura.idTipoDoAtoAdministrativo
                      )
                      order by idOrdemDaAssinatura asc
                    )
                "),
                'quantidadeAssinaturas' => new Zend_Db_Expr($sqlQuantidadeAssinaturas),
                'quantidadeTotalAssinaturas' => new Zend_Db_Expr($sqlTotalQuantidadeAssinaturas),
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
            array("tbDocumentoAssinatura.idTipoDoAtoAdministrativo"),
            $this->_schema
        );

        $query->joinInner(
            array('Verificacao' => 'Verificacao'),
            "Verificacao.idVerificacao = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            'Verificacao.Descricao as tipoDoAtoAdministrativo',
            $this->_schema
        );

        $query->joinInner(
            array('TbAtoAdministrativo' => 'TbAtoAdministrativo'),
            "TbAtoAdministrativo.idOrgaoDoAssinante = {$this->modeloTbAtoAdministrativo->getIdOrgaoDoAssinante()}
             AND TbAtoAdministrativo.idPerfilDoAssinante = {$this->modeloTbAtoAdministrativo->getIdPerfilDoAssinante()}
             AND TbAtoAdministrativo.idOrgaoSuperiorDoAssinante = {$this->modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante()}
             AND TbAtoAdministrativo.idTipoDoAto = tbDocumentoAssinatura.idTipoDoAtoAdministrativo",
            [
                'idOrdemDaAssinatura',
                'idAtoAdministrativo'
            ],
            $this->_schema
        );

        $query->where("TbAtoAdministrativo.idOrgaoDoAssinante = ?", $this->modeloTbAtoAdministrativo->getIdOrgaoDoAssinante());
        $query->where("tbDocumentoAssinatura.cdSituacao = ?", Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA);
        $query->where("tbDocumentoAssinatura.stEstado = ?", Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO);
        $query->where("tbDocumentoAssinatura.idDocumentoAssinatura not in (
            select idDocumentoAssinatura 
              from TbAssinatura
             where idPronac = Projetos.IdPRONAC
               and idAtoAdministrativo = TbAtoAdministrativo.idAtoAdministrativo 
               and idDocumentoAssinatura = tbDocumentoAssinatura.idDocumentoAssinatura
        )");
        $query->where("{$sqlQuantidadeAssinaturas} + 1 = idOrdemDaAssinatura");

        if ($this->modeloTbAtoAdministrativo->getIdTipoDoAto()) {
            $query->where("tbDocumentoAssinatura.idTipoDoAtoAdministrativo in (?)", $this->modeloTbAtoAdministrativo->getIdTipoDoAto());
        }
        $query->where("Orgaos.idSecretaria = ?", $this->modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante());

        return $query;
    }

    public function obterAssinaturasReadequacaoDisponiveis()
    {
        $query = $this->obterQueryAssinaturasDisponiveis();

        $query->joinInner(
            array('tbReadequacaoXParecer' => 'tbReadequacaoXParecer'),
            "tbReadequacaoXParecer.idParecer = tbDocumentoAssinatura.idAtoDeGestao",
            "",
            $this->_schema
        );

        $query->joinInner(
            array('tbReadequacao' => 'tbReadequacao'),
            "tbReadequacao.idReadequacao = tbReadequacaoXParecer.idReadequacao",
            "",
            $this->_schema
        );

        $query->joinInner(
            array('tbTipoReadequacao' => 'tbTipoReadequacao'),
            "tbTipoReadequacao.idTipoReadequacao = tbReadequacao.idTipoReadequacao",
            "tbTipoReadequacao.dsReadequacao",
            $this->_schema
        );

        return $this->_db->fetchAll($query);
    }
}
