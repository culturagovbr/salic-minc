<?php

class Admissibilidade_Model_Enquadramento extends MinC_Db_Table_Abstract
{
    protected $_name = "Enquadramento";
    protected $_schema = "sac";
    protected $_primary = "IdEnquadramento";

    public function alterarEnquadramento($dados)
    {
        $id = null;
        $tmpTbl = new Admissibilidade_Model_Enquadramento();
        $tmpTbl = $tmpTbl->find($dados['IdEnquadramento'])->current();

        if ($tmpTbl) {
            //ATRIBUINDO VALORES AOS CAMPOS QUE FORAM PASSADOS
            $tmpTbl->Enquadramento = $dados['Enquadramento'];
            $tmpTbl->DtEnquadramento = $dados['DtEnquadramento'];
            $tmpTbl->Logon = $dados['Logon'];
            $id = $tmpTbl->save();
        }
        if ($id) {
            return $id;
        }
    }

    public function buscarDados(
        $idPronac = null,
        $pronac = null,
        $buscarTodos = true
    ) {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this->_name);

        // busca pelo idPronac
        if (!empty($idPronac)) {
            $select->where("IdPRONAC = ?", $idPronac);
        }

        // busca pelo pronac
        if (!empty($pronac)) {
            $select->where("AnoProjeto+Sequencial = ?", $pronac);
        }

        return $buscarTodos ? $this->fetchAll($select) : $this->fetchRow($select);
    }


    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    public function alterarDados($dados, $idEnquadramento = null, $idPronac = null, $pronac = null)
    {
        // altera pelo id do enquadramento
        if (!empty($idEnquadramento)) {
            $where = "IdEnquadramento = " . $idEnquadramento;
        } // altera pelo id do pronac
        elseif (!empty($idPronac)) {
            $where = "IdPRONAC = " . $idPronac;
        } // altera pelo pronac
        elseif (!empty($pronac)) {
            $where = "AnoProjeto+Sequencial = " . $pronac;
        }

        return $this->update($dados, $where);
    }

    public function excluirDados($idEnquadramento = null, $idPronac = null, $pronac = null)
    {
        if (!empty($idEnquadramento)) {
            $where = "IdEnquadramento = " . $idEnquadramento;
        } elseif (!empty($idPronac)) {
            $where = "IdPRONAC = " . $idPronac;
        } elseif (!empty($pronac)) {
            $where = "AnoProjeto+Sequencial = " . $pronac;
        }

        return $this->delete($where);
    }

    public function obterProjetosParaEnquadramento($codOrgao, $order = null)
    {
        $select = $this->select();
        $this->_db->setFetchMode(Zend_DB::FETCH_OBJ);

        $queryMensagensNaoRespondidas = $this->select();
        $queryMensagensNaoRespondidas->setIntegrityCheck(false);
        $queryMensagensNaoRespondidas->from(array('tbMensagemProjeto' => 'tbMensagemProjeto'), new Zend_Db_Expr('count(*) as quantidade'), $this->getSchema("BDCORPORATIVO.scsac"));
        $queryMensagensNaoRespondidas->where("Projetos.IdPRONAC = tbMensagemProjeto.IdPRONAC");
        $queryMensagensNaoRespondidas->where("tbMensagemProjeto.idMensagemOrigem IS NULL");

        $queryMensagensRespondidas = $this->select();
        $queryMensagensRespondidas->setIntegrityCheck(false);
        $queryMensagensRespondidas->from(array('tbMensagemProjeto'), 'count(*) as quantidade', $this->getSchema("BDCORPORATIVO.scsac"));
        $queryMensagensRespondidas->where("Projetos.IdPRONAC = tbMensagemProjeto.IdPRONAC");
        $queryMensagensRespondidas->where("tbMensagemProjeto.idMensagemOrigem IS NOT NULL");

        $select->setIntegrityCheck(false);
        $select->from(
            array("Projetos"),
            array('pronac' => new Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
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
                '(' . $queryMensagensNaoRespondidas->assemble() . ') as mensagens_nao_respondidas',
                '(' . $queryMensagensRespondidas->assemble() . ') as mensagens_respondidas'
            ),
            $this->_schema
        );

        $select->joinLeft(array('tbAvaliacaoProposta' => 'tbAvaliacaoProposta'), 'tbAvaliacaoProposta.idProjeto = Projetos.idProjeto and tbAvaliacaoProposta.stEstado = 0', array(), $this->_schema);
        $select->joinLeft(array('Usuarios' => 'Usuarios'), 'tbAvaliacaoProposta.idTecnico = Usuarios.usu_codigo', array('Usuarios.usu_nome'), $this->getSchema('Tabelas'));
//        $select->joinInner(array('PreProjeto' => 'PreProjeto'), 'PreProjeto.idPreProjeto = Projetos.idProjeto', array(), $this->_schema);
        $select->joinInner(array('Area' => 'Area'), 'Area.Codigo = Projetos.Area', array('Area.Descricao AS area'), $this->_schema);
        $select->joinLeft(array('Segmento' => 'Segmento'), 'Segmento.Codigo = Projetos.Segmento', array('Segmento.Descricao AS segmento'), $this->_schema);
        $select->where(
            "Projetos.situacao in ( ? )",
            array(
                'B01',
                'B03',
                Projeto_Model_Situacao::PROJETO_DEVOLVIDO_PARA_ENQUADRAMENTO
            )
        );
        $select->where("Projetos.Orgao in ( ? )", $codOrgao);
//        $select->where("( PreProjeto.AreaAbrangencia = 0 AND 251 = {$codOrgaoSuperior} OR PreProjeto.AreaAbrangencia = 1 AND 160 = {$codOrgaoSuperior} )");


        !empty($order) ? $select->order($order) : null;
        !empty($limit) ? $select->limit($limit) : null;

        return $this->_db->fetchAll($select);
    }

    public function obterProjetosEnquadradosParaAssinatura($codOrgao, $order = null, $limit = null)
    {
        $select = $this->select();
        $this->_db->setFetchMode(Zend_DB::FETCH_OBJ);

        $select->setIntegrityCheck(false);
        $select->from(
            array("projetos"),
            array(
                'pronac' => new Zend_Db_Expr('projetos.AnoProjeto + projetos.Sequencial'),
                'projetos.nomeProjeto',
                'projetos.IdPRONAC',
                'projetos.CgcCpf',
                'projetos.idpronac',
                'projetos.Area',
                'projetos.ResumoProjeto',
                'projetos.UfProjeto',
                'projetos.DtInicioExecucao',
                'projetos.DtFimExecucao',
                'projetos.Situacao',
                'projetos.DtSituacao',
                'dias' => 'DATEDIFF(DAY, projetos.DtSituacao, GETDATE())'
            ),
            $this->_schema
        );

        $select->joinLeft(array('tbAvaliacaoProposta' => 'tbAvaliacaoProposta'), 'tbAvaliacaoProposta.idProjeto = projetos.idProjeto and tbAvaliacaoProposta.stEstado = 0', array(), $this->_schema);
        $select->joinLeft(array('Usuarios' => 'Usuarios'), 'tbAvaliacaoProposta.idTecnico = Usuarios.usu_codigo', array('Usuarios.usu_nome'), $this->getSchema('Tabelas'));
        $select->joinInner(array('Area' => 'Area'), 'Area.Codigo = projetos.Area', array('Area.Descricao AS area'));
        $select->joinLeft(array('Segmento' => 'Segmento'), 'Segmento.Codigo = projetos.Segmento', array('Segmento.Descricao AS segmento', 'Segmento.tp_enquadramento'));
        $select->where("projetos.situacao in ( ? )", array('B02', 'B03'));
        $select->where("projetos.Orgao = ?", $codOrgao);

        !empty($order) ? $select->order($order) : null;
        !empty($limit) ? $select->limit($limit) : null;
        return $this->_db->fetchAll($select);
    }

    public function obterProjetosParaEnquadramentoVinculados($id_usuario, $codOrgao, $order = null)
    {
        $select = $this->select();
        $this->_db->setFetchMode(Zend_DB::FETCH_OBJ);

        $queryMensagensNaoRespondidas = $this->select();
        $queryMensagensNaoRespondidas->setIntegrityCheck(false);
        $queryMensagensNaoRespondidas->from(array('tbMensagemProjeto' => 'tbMensagemProjeto'), 'count(*) as quantidade', $this->getSchema("BDCORPORATIVO.scsac"));
        $queryMensagensNaoRespondidas->where("Projetos.IdPRONAC = tbMensagemProjeto.IdPRONAC");
        $queryMensagensNaoRespondidas->where("tbMensagemProjeto.idMensagemOrigem IS NULL");

        $queryMensagensRespondidas = $this->select();
        $queryMensagensRespondidas->setIntegrityCheck(false);
        $queryMensagensRespondidas->from(array('tbMensagemProjeto'), 'count(*) as quantidade', $this->getSchema("BDCORPORATIVO.scsac"));
        $queryMensagensRespondidas->where("Projetos.IdPRONAC = tbMensagemProjeto.IdPRONAC");
        $queryMensagensRespondidas->where("tbMensagemProjeto.idMensagemOrigem IS NOT NULL");

        $select->setIntegrityCheck(false);
        $select->from(
            array("Projetos"),
            array('pronac' => new Zend_Db_Expr('Projetos.AnoProjeto + Projetos.Sequencial'),
                'Projetos.nomeProjeto',
                'Projetos.IdPRONAC',
                'Projetos.CgcCpf',
                'Projetos.idpronac',
                'Projetos.Area as cdarea',
                'Projetos.ResumoProjeto',
                'Projetos.UfProjeto',
                'Projetos.DtInicioExecucao',
                'Projetos.DtFimExecucao',
                'Projetos.DtSituacao',
                'Projetos.Situacao',
                '(' . $queryMensagensNaoRespondidas->assemble() . ') as mensagens_nao_respondidas',
                '(' . $queryMensagensRespondidas->assemble() . ') as mensagens_respondidas',
            ),
            $this->_schema
        );

        $select->joinInner(array('tbAvaliacaoProposta' => 'tbAvaliacaoProposta'), 'tbAvaliacaoProposta.idProjeto = Projetos.idProjeto', array(), $this->_schema);
        $select->joinInner(array('Area' => 'Area'), 'Area.Codigo = Projetos.Area', array('Area.Descricao AS area'), $this->_schema);
        $select->joinLeft(array('Segmento' => 'Segmento'), 'Segmento.Codigo = Projetos.Segmento', array('Segmento.Descricao AS segmento'), $this->_schema);

        $select->where(
            "Projetos.situacao in ( ? )",
            array(
                'B01',
                'B03',
                Projeto_Model_Situacao::PROJETO_DEVOLVIDO_PARA_ENQUADRAMENTO
            )
        );
        $select->where("Projetos.Orgao = ?", $codOrgao);
        $select->where("tbAvaliacaoProposta.stEstado = ?", array('0'));
        $select->where("tbAvaliacaoProposta.idTecnico = ?", array($id_usuario));

        !empty($order) ? $select->order($order) : null;

        return $this->_db->fetchAll($select);
    }

    public function verificarDesistenciaRecursal($idPronac)
    {
        $select = $this->select();
        $this->_db->setFetchMode(Zend_DB::FETCH_OBJ);

        $queryDesistenciaRecursal = $this->select();
        $queryDesistenciaRecursal->setIntegrityCheck(false);
        $queryDesistenciaRecursal->from(array('tbRecurso' => 'tbRecurso'), array('idRecurso'), $this->getSchema("SAC.dbo"));
        $queryDesistenciaRecursal->where("tpSolicitacao = ?", "DR");
        $queryDesistenciaRecursal->where("siRecurso = ?", TbTipoEncaminhamento::DESISTENCIA_DO_PRAZO_RECURSAL);
        $queryDesistenciaRecursal->where("stEstado = ?", 1);
        $queryDesistenciaRecursal->where("siFaseProjeto = ?", 1);
        $queryDesistenciaRecursal->where("IdPRONAC = ?", $idPronac);
        
        return ($this->_db->fetchOne($queryDesistenciaRecursal)) ? $this->_db->fetchOne($queryDesistenciaRecursal) : false;
    }

    public function obterEnquadramentoPorProjeto($idPronac, $anoProjeto, $sequencial)
    {
        $arrayPesquisa = array(
            'AnoProjeto' => $anoProjeto,
            'Sequencial' => $sequencial,
            'IdPRONAC' => $idPronac
        );

        return $this->findBy($arrayPesquisa);
    }

    public function findBy($where)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(PDO::FETCH_ASSOC);

        $select = $db->select()->from($this->_name, new Zend_Db_Expr("*, cast( Observacao as TEXT) as Observacao"), $this->_schema);
        self::setWhere($select, $where);

        $result = $db->fetchRow($select);
        return $result;
    }
}
