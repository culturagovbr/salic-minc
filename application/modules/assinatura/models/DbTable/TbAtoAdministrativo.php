<?php

/**
 * Class Assinatura_Model_DbTable_TbAtoAdministrativo
 * @var Assinatura_Model_TbAtoAdministrativo $dbTableTbAtoAdministrativo
 */
class Assinatura_Model_DbTable_TbAtoAdministrativo extends MinC_Db_Table_Abstract
{
    public $modelAtoAdministrativo;
    protected $_schema = 'sac';
    protected $_name = 'tbAtoAdministrativo';
    protected $_primary = 'idAtoAdministrativo';

    public function definirModeloAssinatura(array $dados)
    {
        $this->modelAtoAdministrativo = new Assinatura_Model_TbAtoAdministrativo($dados);
        return $this;
    }

    public function obterPerfilAssinante(
        $idOrgaoDoAssinante,
        $idPerfilDoAssinante,
        $idTipoDoAto
    ) {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            array(
                'idAtoAdministrativo',
                'idTipoDoAto',
                'idCargoDoAssinante',
                'idPerfilDoAssinante',
                'idOrgaoDoAssinante',
                'idOrdemDaAssinatura',
            ),
            $this->_schema
        );
        $objQuery->joinInner(
            array('Verificacao' => 'Verificacao'),
            'Verificacao.idVerificacao = tbAtoAdministrativo.idCargoDoAssinante',
            array('dsCargoAssinante' => 'Verificacao.Descricao'),
            $this->getSchema('Agentes')
        );
        $objQuery->joinInner(
            array('grupos' => 'Grupos'),
            'grupos.gru_codigo = tbAtoAdministrativo.idPerfilDoAssinante',
            array('dsPerfil' => 'grupos.gru_nome'),
            $this->getSchema('Tabelas')
        );
        $objQuery->where('idOrgaoDoAssinante = ?', $idOrgaoDoAssinante);
        $objQuery->where('idPerfilDoAssinante = ?', $idPerfilDoAssinante);
        $objQuery->where('idTipoDoAto = ?', $idTipoDoAto);
        $objQuery->where('stEstado = ?', Assinatura_Model_TbAtoAdministrativo::ST_ESTADO_ATIVO);

        $result = $this->fetchRow($objQuery);
        if ($result) {
            return $result->toArray();
        }
    }

    public function obterQuantidadeMinimaAssinaturas(
        $idTipoDoAto,
        $idOrgaoSuperiorDoAssinante,
        $grupo = null
    ) {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            array(
                'quantidade_assinaturas' => new Zend_Db_Expr(
                    "count(*)"
                ),
            ),
            $this->_schema
        );
        $objQuery->where('idTipoDoAto in (?)', $idTipoDoAto);
        $objQuery->where('stEstado = ?', true);

        if ($grupo) {
            $objQuery->where('grupo = ?', $grupo);
        } else {
            $objQuery->where('idOrgaoSuperiorDoAssinante = ?', $idOrgaoSuperiorDoAssinante);
        }
        
        $objResultado = $this->fetchRow($objQuery);
        if ($objResultado) {
            $resultadoArray = $objResultado->toArray();
            return $resultadoArray['quantidade_assinaturas'];
        }
    }

    /**
     * @return string Código do orgao
     */
    public function obterProximoOrgaoDeDestino(
        $idTipoDoAto,
        $idOrdemDaAssinaturaAtual,
        $idOrgaoSuperiorDoAssinante,
        $grupo = null
    ) {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            'idOrgaoDoAssinante',
            $this->_schema
        );
        $objQuery->where('idTipoDoAto = ?', $idTipoDoAto);
        $objQuery->where('idOrdemDaAssinatura > ?', $idOrdemDaAssinaturaAtual);
        $objQuery->where('idOrgaoSuperiorDoAssinante = ?', $idOrgaoSuperiorDoAssinante);
        if($grupo) {
            $objQuery->where('grupo = ?', $grupo);
        }
        $objQuery->order('idOrdemDaAssinatura asc');
        $objQuery->limit(1);

        $objResultado = $this->fetchRow($objQuery);
        if ($objResultado) {
            $arrayResultado = $objResultado->toArray();
            return $arrayResultado['idOrgaoDoAssinante'];
        }
    }

    protected function obterQueryAtoAdministrativo(
        $idTipoDoAto,
        $idPerfilDoAssinante,
        $idOrgaoDoAssinante,
        $idOrgaoSuperiorDoAssinante = null,
        $grupo = null
    ): \MinC_Db_Table_Select{

        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            '*',
            $this->_schema
        );
        $objQuery->where('idTipoDoAto = ?', $idTipoDoAto);
        $objQuery->where('idPerfilDoAssinante = ?', $idPerfilDoAssinante);
        $objQuery->where('idOrgaoDoAssinante = ?', $idOrgaoDoAssinante);
        $objQuery->where('stEstado = ?', 1);

        if (!is_null($idOrgaoSuperiorDoAssinante)) {
            $objQuery->where('idOrgaoSuperiorDoAssinante = ?', $idOrgaoSuperiorDoAssinante);
        }
        if (!is_null($grupo)) {
            $objQuery->where('grupo = ?', $grupo);
        }
        
        return $objQuery;
    }

    public function obterPrimeiroAtoAdministrativo(
        $idTipoDoAto,
        $idOrgaoSuperiorDoAssinante,
        $idPerfilDoAssinante,
        $idOrgaoDoAssinante
    ) {

        $objQuery = $this->obterQueryAtoAdministrativo(
            $idTipoDoAto,
            $idPerfilDoAssinante,
            $idOrgaoDoAssinante
        );

        $objQuery->where("idOrdemDaAssinatura = ?", 1);
        
        return $this->_db->fetchRow($objQuery);
    }

    public function obterGrupoPorIdDocumentoAssinatura($idDocumentoAssinatura)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            ['tbAtoAdministrativo', 'tbAtoAdministrativo'],
            new Zend_Db_Expr('TOP 1 grupo'),
            $this->_schema
        );

        $objQuery->joinInner(
            array('tbAssinatura' => 'tbAssinatura'),
            'tbAssinatura.idAtoAdministrativo = tbAtoAdministrativo.idAtoAdministrativo',
            '',
            $this->_schema
        );

        $objQuery->where('idDocumentoAssinatura = ?', $idDocumentoAssinatura);        

        $grupo = $this->_db->fetchRow($objQuery);
        
        return $grupo['grupo'];
    }
    
    public function obterPrimeiroAtoPorIdDocumento($idDocumentoAssinatura)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            ['tbAssinatura', 'tbAssinatura'],
            new Zend_Db_Expr('TOP 1 *'),
            $this->_schema
        );

        $objQuery->where('idDocumentoAssinatura = ?', $idDocumentoAssinatura);

        return $this->_db->fetchRow($objQuery);        
    }

    public function obterAtoAdministrativoAtual(
        $idTipoDoAto,
        $idPerfilDoAssinante,
        $idOrgaoDoAssinante,
        $grupo = ''
    ) {
        
        $objQuery = $this->obterQueryAtoAdministrativo(
            $idTipoDoAto,
            $idPerfilDoAssinante,
            $idOrgaoDoAssinante,
            null,
            $grupo
        );

        $objResultado = $this->fetchRow($objQuery);
        if ($objResultado) {
            return $objResultado->toArray();
        }
    }

    /**
     * Transposição da view "vwAtoAdministrativo".
     */
    public function obterAtoAdministrativoDetalhado()
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            $this->_name,
            '*',
            $this->_schema
        );

        $objQuery->joinInner(
            ["Verificacao" => "Verificacao"],
            "{$this->_name}.idTipoDoAto = Verificacao.idVerificacao",
            ["dsAtoAdministrativo" => "Descricao"],
            $this->_schema
        );

        $objQuery->joinLeft(
            ["Verificacao_Agentes" => "Verificacao"],
            "{$this->_name}.idCargoDoAssinante = Verificacao_Agentes.idVerificacao",
            ["dsCargoDoAssinante" => "Descricao"],
            "Agentes.dbo"
        );

        $objQuery->joinInner(
            ["Orgaos" => "Orgaos"],
            "{$this->_name}.idOrgaoDoAssinante = Orgaos.Codigo",
            ["dsOrgaoDoAssinante" => "Sigla"],
            $this->_schema
        );

        $objQuery->joinLeft(
            ["OrgaoSuperior" => "Orgaos"],
            "{$this->_name}.idOrgaoSuperiorDoAssinante = OrgaoSuperior.Codigo",
            ["dsOrgaoSuperiorDoAssinante" => "OrgaoSuperior.Sigla"],
            $this->_schema
        );

        $objQuery->joinInner(
            ["Grupos" => "Grupos"],
            "{$this->_name}.idPerfilDoAssinante = Grupos.gru_codigo",
            ["dsPerfil" => "gru_nome"],
            'tabelas'
        );

        $objQuery->joinInner(
            ["tbAtoAdministrativoMetodoEncaminhamento", "tbAtoAdministrativoMetodoEncaminhamento"],
            "{$this->_name}.idTipoDoAto = tbAtoAdministrativoMetodoEncaminhamento.idTipoDoAto",
            ["encaminhaProjeto" => "encaminhaProjeto"],
            $this->_schema
        );
        
        $objQuery->order([
            "Verificacao.Descricao asc",
            "OrgaoSuperior.Sigla asc",
            "TbAtoAdministrativo.idOrdemDaAssinatura asc",
            "Orgaos.Sigla asc",
        ]);
        return $this->fetchAll($objQuery)->toArray();
    }

    public function obterTiposDeAtosAdministrativosAtivos()
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            ['Verificacao' => 'Verificacao'],
            array(
                'codigo' => 'idVerificacao',
                'descricao' => 'Descricao',
            ),
            $this->_schema
        );
        $objQuery->where('stEstado = ?', 1);
        $objQuery->where('idTipo= ?', 24);
        $objQuery->order(2);

        return $this->fetchAll($objQuery)->toArray();
    }

    public function obterCargosDoAssinante()
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            ['Verificacao' => 'Verificacao'],
            array(
                'codigo' => 'idVerificacao',
                'descricao' => 'Descricao',
            ),
            'Agentes'
        );
        $objQuery->where('Sistema = ?', 21);
        $objQuery->where('idTipo = ?', 27);

        return $this->fetchAll($objQuery)->toArray();
    }

    public function obterOrgaosSuperiores()
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            ['Orgaos' => 'Orgaos'],
            array(
                'codigo' => 'Codigo',
                'descricao' => 'Sigla',
            ),
            $this->_schema
        );
        $objQuery->where('Codigo = idSecretaria');
        $objQuery->where('Status = ?', 0);
        $objQuery->order(2);

        return $this->fetchAll($objQuery)->toArray();
    }

    public function obterOrgaos($idOrgaoSuperior)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            ['Orgaos' => 'Orgaos'],
            array(
                'codigo' => 'Codigo',
                'descricao' => 'Sigla',
            ),
            $this->_schema
        );
        $objQuery->where('(idSecretaria = ?)', $idOrgaoSuperior);
        $objQuery->where('Status = ?', 0);
        $objQuery->order(2);

        return $this->fetchAll($objQuery)->toArray();
    }

    public function obterPerfisDoAssinante()
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            ['Grupos' => 'Grupos'],
            array(
                'codigo' => 'gru_codigo',
                'descricao' => 'gru_nome',
            ),
            'tabelas'
        );
        $objQuery->where('gru_sistema = ?', 21);

        return $this->fetchAll($objQuery)->toArray();
    }

    public function obterOrdensAssinaturaDisponiveis(Assinatura_Model_TbAtoAdministrativo $objModelAtoAdministrativo)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            [$this->_name],
            array(
                'codigo' => 'idOrdemDaAssinatura',
                'descricao' => 'idOrdemDaAssinatura',
            ),
            $this->_schema
        );
        $objQuery->where('idTipoDoAto = ?', $objModelAtoAdministrativo->getIdTipoDoAto());
        $objQuery->where('idOrgaoSuperiorDoAssinante = ?', $objModelAtoAdministrativo->getIdOrgaoSuperiorDoAssinante());

        if ($objModelAtoAdministrativo->getIdOrdemDaAssinatura()) {
            $objQuery->where('idOrdemDaAssinatura = ?', $objModelAtoAdministrativo->getIdOrdemDaAssinatura());
        }

        return $this->fetchAll($objQuery)->toArray();
    }

    public function obterProximaOrdemDeAssinatura(Assinatura_Model_TbAtoAdministrativo $objModelAtoAdministrativo)
    {
        $objQuery = $this->select();
        $objQuery->setIntegrityCheck(false);
        $objQuery->from(
            [$this->_name],
            [new Zend_Db_Expr('coalesce(max(idOrdemDaAssinatura), 0) + 1 as idOrdemDaAssinatura')],
            $this->_schema
        );

        $objQuery->where('idTipoDoAto = ?', $objModelAtoAdministrativo->getIdTipoDoAto());
        $objQuery->where('idOrgaoSuperiorDoAssinante = ?', $objModelAtoAdministrativo->getIdOrgaoSuperiorDoAssinante());
        if($objModelAtoAdministrativo->getGrupo()) {
            $objQuery->where('grupo = ?', $objModelAtoAdministrativo->getGrupo());
        }
        $objResultado = $this->fetchRow($objQuery);

        if ($objResultado) {
            $arrayResultado = $objResultado->toArray();
            return $arrayResultado['idOrdemDaAssinatura'];
        }
    }
}
