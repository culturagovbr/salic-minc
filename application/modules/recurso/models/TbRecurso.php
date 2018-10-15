<?php

/**
 * Class Projeto_Model_Situacao
 *
 *
 *
 * select * from sac.dbo.tbRecurso;
 * -- tpRecurso 1 - quando é pedido de reconsideração 2 - quando é recurso
 * -- tpSolicitacao - PI - Projeto Indeferido; DR - Desitência do prazo recursal; OR - Orcamento; - EN - Enquadramento;
 * -- siFaseProjeto = 2 homologação
 * -- siRecurso - 1 - quando envia; 12 - quando cadastra; 9 - foi finalizado 15 - finaliza
 * -- stEstado - 1 inativo e 0 é ativo
 *
 * --finalizado -- stEstado = 1 e siRecurso = 9
 * --arquivado -- stEstado = 1 e siRecurso = 15
 * -- desistencia -- stEstado = 1 e tpSolicitacao = DR e siRecurso = 0;
 *
 * select * from sac.dbo.tbRecurso where IdPRONAC = 209751 and stEstado = 1 and siFaseProjeto = 2 and tpSolicitacao = 'DR' and siRecurso = 0; -- desistiu do prazo recursal
 * select * from sac.dbo.tbRecurso where IdPRONAC = 167719 and stEstado = 1 and siFaseProjeto = 2 and tpSolicitacao = 'DR' and siRecurso = 0; -- desistiu do prazo recursal
 * select * from sac.dbo.tbRecurso; --where IdPRONAC = 209751 and stEstado = 1 and siFaseProjeto = 2 and tpSolicitacao = 'DR' and siRecurso = 0;
 *
 * select * from sac.dbo.tbRecurso where IdPRONAC = 167719 and stEstado = 1 and siFaseProjeto = 2 and tpSolicitacao = 'DR' and siRecurso = 0;
 *
 * select * from sac.dbo.tbRecurso where tpSolicitacao = 'or';
 *
 *
 * --se desistiu não pode mais entrar com recurso --  IdPRONAC = 167719
 * select * from sac.dbo.tbRecurso where stEstado = 1 and siFaseProjeto = 2 and tpSolicitacao = 'DR' AND siRecurso = 0 AND tpRecurso = 1;
 *
 * -- verificar se tem direito a segundo recurso --  IdPRONAC = 167719
 * select * from sac.dbo.tbRecurso where stEstado = 1 and siFaseProjeto = 2 and tpSolicitacao in ('PI', 'OR', 'EN') and siRecurso in (9, 15) AND tpRecurso = 1;
 *
 * -- ja teve os dois recursos
 * select * from sac.dbo.tbRecurso where stEstado = 1 and siFaseProjeto = 2 and tpSolicitacao in ('PI', 'OR', 'EN') and siRecurso in (9, 15) AND tpRecurso = 2;
 */

class Recurso_Model_TbRecurso extends MinC_Db_Model
{
    protected $_idRecurso;
    protected $_IdPRONAC;
    protected $_dtSolicitacaoRecurso;
    protected $_dsSolicitacaoRecurso;
    protected $_idAgenteSolicitante;
    protected $_dtAvaliacao;
    protected $_dsAvaliacao;
    protected $_idAgenteAvaliador;
    protected $_stAnalise;
    protected $_idNrReuniao;
    protected $_stAtendimento;

    /**
     * O proponente pode interpor recurso duas vezes:
     * @var $_tpRecurso
     * 1 => Pedido de reconsideração
     * 2 => Recurso
     */
    protected $_tpRecurso;

    /**
     * @var $_tpSolicitacao
     * DR => Desistência do Prazo Recursal
     * PI => Projet Indeferido
     * EN => Enquadramento
     * OR => Orçamento
     */
    protected $_tpSolicitacao;

    /**
     * @var $_siFaseProjeto
     * 1 => admissibilidade
     * 2 => homologação
     */
    protected $_siFaseProjeto;

    /**
     * @var $_siRecurso
     * 1 => Recurso enviado
     * 12 => Recurso cadastrado
     * 8 => Enviado a plenaria
     * 9 => Finalizado enviado para portaria
     * 15 => Arquivado
     */
    protected $_siRecurso;

    /**
     * @var $_stEstado
     * 0 => ativo
     * 1 => inativo
     */
    protected $_stEstado;

    const PEDIDO_DE_RECONSIDERACAO = 1;
    const RECURSO = 2;

    const TIPO_RECURSO_DESISTENCIA_DO_PRAZO_RECURSAL = 'DR';
    const TIPO_RECURSO_ENQUADRAMENTO = 'EN';
    const TIPO_RECURSO_PROJETO_INDEFERIDO = 'PI';
    const TIPO_RECURSO_ORCAMENTO = 'OR';

    const SITUACAO_RECURSO_ATIVO = 0;
    const SITUACAO_RECURSO_INATIVO = 1;

    const SI_RECURSO_ENVIADO = 1;
    const SI_RECURSO_CADASTRADO = 12;
    const SI_RECURSO_FINALIZADO = 9;
    const SI_RECURSO_ARQUIVADO = 15;

    const FASE_ADMISSIBILIDADE = 1;
    const FASE_HOMOLOGACAO = 2;

    const PRAZO_RECURSAL = 10;

    /**
     * liberar o recurso após alterar a situação
     */
    const SITUACOES_RECURSO_ORCAMENTO = [
        Projeto_Model_Situacao::READEQUACAO_DO_PROJETO_APROVADA_AGUARDANDO_ANALISE_DOCUMENTAL,
        Projeto_Model_Situacao::PROJETO_APROVADO_AGUARDANDO_ANALISE_DOCUMENTAL,
        Projeto_Model_Situacao::PROJETO_HOMOLOGADO
    ];

    /**
     * liberar o recurso após a reunião da cnic - tbReuniao
     */
    const SITUACOES_RECURSO_PROJETO_INDEFERIDO = [
        Projeto_Model_Situacao::INDEFERIDO_NAO_ENQUADRAMENTO_NOS_OBJETIVOS,
        Projeto_Model_Situacao::INDEFERIDO_PROJETO_JA_REALIZADO,
        Projeto_Model_Situacao::INDEFERIDO_NAO_ATENDIMENTO_A_DILIGENCIA,
        Projeto_Model_Situacao::INDEFERIDO_PROJETO_EM_DUPLICIDADE,
        Projeto_Model_Situacao::INDEFERIDO_SOMATORIO_DOS_PROJETOS_EXCEDE_O_LIMITE_PESSOA_FISICA,
        Projeto_Model_Situacao::INDEFERIDO_SOMATORIO_DOS_PROJETOS_EXCEDE_O_LIMITE_PESSOA_JURIDICA,
        Projeto_Model_Situacao::INDEFERIDO_50_PORCENTO_DE_CORTE_VALOR_SOLICITADO,
        Projeto_Model_Situacao::PROJETO_INDEFERIDO
    ];

    public function getIdRecurso()
    {
        return $this->_idRecurso;
    }

    public function setIdRecurso($idRecurso): void
    {
        $this->_idRecurso = $idRecurso;
    }

    public function getIdPRONAC()
    {
        return $this->_IdPRONAC;
    }

    public function setIdPRONAC($IdPRONAC): void
    {
        $this->_IdPRONAC = $IdPRONAC;
    }

    public function getDtSolicitacaoRecurso()
    {
        return $this->_dtSolicitacaoRecurso;
    }

    public function setDtSolicitacaoRecurso($dtSolicitacaoRecurso): void
    {
        $this->_dtSolicitacaoRecurso = $dtSolicitacaoRecurso;
    }

    public function getDsSolicitacaoRecurso()
    {
        return $this->_dsSolicitacaoRecurso;
    }

    public function setDsSolicitacaoRecurso($dsSolicitacaoRecurso): void
    {
        $this->_dsSolicitacaoRecurso = $dsSolicitacaoRecurso;
    }

    public function getIdAgenteSolicitante()
    {
        return $this->_idAgenteSolicitante;
    }

    public function setIdAgenteSolicitante($idAgenteSolicitante): void
    {
        $this->_idAgenteSolicitante = $idAgenteSolicitante;
    }

    public function getDtAvaliacao()
    {
        return $this->_dtAvaliacao;
    }

    public function setDtAvaliacao($dtAvaliacao): void
    {
        $this->_dtAvaliacao = $dtAvaliacao;
    }

    public function getDsAvaliacao()
    {
        return $this->_dsAvaliacao;
    }

    public function setDsAvaliacao($dsAvaliacao): void
    {
        $this->_dsAvaliacao = $dsAvaliacao;
    }

    public function getTpRecurso()
    {
        return $this->_tpRecurso;
    }

    public function setTpRecurso($tpRecurso): void
    {
        $this->_tpRecurso = $tpRecurso;
    }

    public function getTpSolicitacao()
    {
        return $this->_tpSolicitacao;
    }

    public function setTpSolicitacao($tpSolicitacao): void
    {
        $this->_tpSolicitacao = $tpSolicitacao;
    }

    public function getIdAgenteAvaliador()
    {
        return $this->_idAgenteAvaliador;
    }

    public function setIdAgenteAvaliador($idAgenteAvaliador): void
    {
        $this->_idAgenteAvaliador = $idAgenteAvaliador;
    }

    public function getStAtendimento()
    {
        return $this->_stAtendimento;
    }

    public function setStAtendimento($stAtendimento): void
    {
        $this->_stAtendimento = $stAtendimento;
    }

    public function getSiFaseProjeto()
    {
        return $this->_siFaseProjeto;
    }

    public function setSiFaseProjeto($siFaseProjeto): void
    {
        $this->_siFaseProjeto = $siFaseProjeto;
    }

    public function getSiRecurso()
    {
        return $this->_siRecurso;
    }

    public function setSiRecurso($siRecurso): void
    {
        $this->_siRecurso = $siRecurso;
    }

    public function getStAnalise()
    {
        return $this->_stAnalise;
    }

    public function setStAnalise($stAnalise): void
    {
        $this->_stAnalise = $stAnalise;
    }

    public function getIdNrReuniao()
    {
        return $this->_idNrReuniao;
    }

    public function setIdNrReuniao($idNrReuniao): void
    {
        $this->_idNrReuniao = $idNrReuniao;
    }

    public function getStEstado()
    {
        return $this->_stEstado;
    }

    public function setStEstado($stEstado): void
    {
        $this->_stEstado = $stEstado;
    }

    public static function obterSituacoesPassiveisDeRecursoFase2()
    {
        return array_merge(
            self::SITUACOES_RECURSO_PROJETO_INDEFERIDO,
            self::SITUACOES_RECURSO_ORCAMENTO
        );
    }

    public static function obterSituacoesPassiveisDeRecurso()
    {
        return self::obterSituacoesPassiveisDeRecursoFase2();
    }
}
