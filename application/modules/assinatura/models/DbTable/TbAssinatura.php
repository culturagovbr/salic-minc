<?php

class Assinatura_Model_DbTable_TbAssinatura extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbAssinatura';
    protected $_primary = 'idAssinatura';

    const TIPO_ATO_ENQUADRAMENTO = 626;
    const TIPO_ATO_ANALISE_INICIAL = 630;
    const TIPO_ATO_ANALISE_CNIC = 631;
    const TIPO_ATO_ADMINISTRATIVO = 643; // Homologacao para execucao do projeto.

    /**
    * Esse metodo deve retornar Objeto
    */
    public function obterAssinaturas(
        $idPronac,
        $idTipoDoAtoAdministrativo,
        $idDocumentoAssinatura = null
    ) {
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

//    public function obterSituacaoAtualAssinaturas($idPronac, $idOrgaoDoAssinante, $idTipoDoAto)
//    {
//        $objQuery = $this->select();
//        $objQuery->setIntegrityCheck(false);
//        $objQuery->from(
//            $this->_name,
//            array(
//                'idAtoAdministrativo',
//                'idTipoDoAto',
//                'idCargoDoAssinante',
//                'idOrdemDaAssinatura'
//            ),
//            $this->_schema
//        );
//        $objQuery->joinInner(
//            array('Verificacao' => 'Verificacao'),
//            'Verificacao.idVerificacao = tbAtoAdministrativo.idCargoDoAssinante',
//            array('dsCargoAssinante' => 'Verificacao.Descricao'),
//            $this->getSchema('Agentes')
//        );
//        $objQuery->where('idOrgaoDoAssinante = ?', $idOrgaoDoAssinante);
    ////        $objQuery->where('idPerfilDoAssinante = ?', $idPerfilDoAssinante);
//        $objQuery->where('idTipoDoAto = ?', $idTipoDoAto);
//        $result = $this->fetchAll($objQuery);
//        if ($result) {
//            return $result->toArray();
//        }
//    }

    public function obterProjetosAssinados(
        $idOrgaoSuperiorDoAssinante,
        $idAssinante = null
    ) {
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

//        $query->where("Projetos.Orgao = ?", $idOrgaoDoAssinante);

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
}
