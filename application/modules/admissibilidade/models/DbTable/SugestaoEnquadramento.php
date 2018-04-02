<?php

class Admissibilidade_Model_DbTable_SugestaoEnquadramento extends MinC_Db_Table_Abstract
{
    const ULTIMA_SUGESTAO_ATIVA = 1;
    const ULTIMA_SUGESTAO_INATIVA = 0;

    protected $_name = "sugestao_enquadramento";
    protected $_schema = "sac";
    protected $_primary = "id_sugestao_enquadramento";
    /**
     * @var Admissibilidade_Model_SugestaoEnquadramento
     */
    public $sugestaoEnquadramento;

    public function __construct(array $config = array())
    {
        $this->sugestaoEnquadramento = new Admissibilidade_Model_SugestaoEnquadramento();
        parent::__construct($config);
    }


    public function obterHistoricoEnquadramento()
    {
        $tableSelect = $this->obterQueryDetalhadaEnquadramentosProposta();
        $resultado = $this->fetchAll($tableSelect);
        if ($resultado) {
            return $resultado->toArray();
        }
    }

    /**
     * @return array
     */
    public function obterUltimaSugestaoEnquadramentoProposta()
    {
        $this->sugestaoEnquadramento->setUltimaSugestao(self::ULTIMA_SUGESTAO_ATIVA);
        $tableSelect = $this->obterQueryDetalhadaEnquadramentosProposta();
        $resultado = $this->fetchRow($tableSelect);
        if ($resultado) {
            return $resultado->toArray();
        }
    }

    public function obterRecursoEnquadramentoProposta()
    {
        $this->sugestaoEnquadramento->setUltimaSugestao(self::ULTIMA_SUGESTAO_ATIVA);
        $tableSelect = $this->obterQueryDetalhadaEnquadramentosProposta();
        $condicaoJoinTbRecursoProposta = 'sugestao_enquadramento.id_preprojeto = tbRecursoProposta.idPreProjeto ';
        $condicaoJoinTbRecursoProposta .= " and tbRecursoProposta.stAtivo = " . Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_ATIVO ;
        $condicaoJoinTbRecursoProposta .= " and tbRecursoProposta.stAtendimento in (";
        $condicaoJoinTbRecursoProposta .= "'" . Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_SEM_AVALIACAO . "'" ;
        $condicaoJoinTbRecursoProposta .= ", '" . Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_INDEFERIDO . "')";
        $tableSelect->joinInner(
            'tbRecursoProposta'
            , $condicaoJoinTbRecursoProposta
            ,[
//                '*',
                'idRecursoProposta',
                'dtRecursoProponente',
                'dsRecursoProponente',
                'idProponente',
                'dtAvaliacaoTecnica',
                'idAvaliadorTecnico',
                'dsAvaliacaoTecnica',
                'tpRecurso',
                'idArquivo',
                'stAtendimento',
                'tpSolicitacao',
                'diasDesdeAberturaRecurso' => new Zend_Db_Expr('DATEDIFF(DAY, dtRecursoProponente, GETDATE())')
            ],
            $this->getSchema('sac')
        );
        $tableSelect->joinLeft(
            ['distribuicao_avaliacao_proposta']
            , "distribuicao_avaliacao_proposta.id_preprojeto = sugestao_enquadramento.id_preprojeto
                    and distribuicao_avaliacao_proposta.id_orgao_superior = sugestao_enquadramento.id_orgao_superior
                    and distribuicao_avaliacao_proposta.id_perfil = " . Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE
                 . " and distribuicao_avaliacao_proposta.avaliacao_atual = " . Admissibilidade_Model_DistribuicaoAvaliacaoProposta::AVALIACAO_ATUAL_ATIVA
            ,
            ['*']
            , $this->getSchema('sac')
        );
//xd((string)$tableSelect);
        $resultado = $this->fetchRow($tableSelect);
        if ($resultado) {
            return $resultado->toArray();
        }
    }

    public function obterQueryDetalhadaEnquadramentosProposta()
    {
        $tableSelect = $this->select();
        $tableSelect->setIntegrityCheck(false);
        $tableSelect->from(
            [$this->_name => $this->_name],
            '*',
            $this->_schema
        );
        $tableSelect->joinInner(
            ['Orgaos' => 'Orgaos'],
            "{$this->_name}.id_orgao = Orgaos.org_codigo",
            [
                'org_sigla'
            ],
            $this->getSchema('tabelas')
        );
        $tableSelect->joinInner(
            ['Grupos' => 'Grupos'],
            "{$this->_name}.id_perfil_usuario = Grupos.gru_codigo",
            [
                'gru_nome'
            ],
            $this->getSchema('tabelas')
        );
        $tableSelect->joinInner(
            ['Usuarios' => 'Usuarios'],
            "{$this->_name}.id_usuario_avaliador = Usuarios.usu_codigo",
            [
                'usu_nome'
            ],
            $this->getSchema('tabelas')
        );
        $tableSelect->joinLeft(
            ['Segmento' => 'Segmento'],
            "{$this->_name}.id_segmento = Segmento.Codigo",
            [
                'segmento' => 'Descricao',
                'enquadramento' => new Zend_Db_Expr(
                    "CASE WHEN Segmento.tp_enquadramento = 1 THEN 'Artigo 26' "
                    . " WHEN Segmento.tp_enquadramento = 2 THEN 'Artigo 18' END"
                ),
                'tp_enquadramento'
            ],
            $this->_schema
        );
        $tableSelect->joinLeft(
            ['Area' => 'Area'],
            "{$this->_name}.id_area = Area.Codigo",
            [
                'area' => 'Descricao'
            ],
            $this->_schema
        );

        if (!$this->sugestaoEnquadramento->getIdPreprojeto()) {
            throw new Exception("Identificador da proposta n&atilde;o informado.");
        }

        $tableSelect->where("{$this->_name}.id_preprojeto = ?", $this->sugestaoEnquadramento->getIdPreprojeto());
        if ($this->sugestaoEnquadramento->getIdPerfilUsuario()) {
            $tableSelect->where('id_perfil_usuario = ?', $this->sugestaoEnquadramento->getIdPerfilUsuario());
        }

        if ($this->sugestaoEnquadramento->getUltimaSugestao() === self::ULTIMA_SUGESTAO_ATIVA
            || $this->sugestaoEnquadramento->getUltimaSugestao() === self::ULTIMA_SUGESTAO_INATIVA) {
            $tableSelect->where('ultima_sugestao = ?', $this->sugestaoEnquadramento->getUltimaSugestao());
        }

        $tableSelect->order('data_avaliacao desc');

        return $tableSelect;
    }

    /**
     * @param Admissibilidade_Model_SugestaoEnquadramento $sugestaoEnquadramento
     * @return bool|null
     */
    public function isPropostaEnquadrada(Admissibilidade_Model_SugestaoEnquadramento $sugestaoEnquadramento)
    {
        $arrayPesquisa = [];
        if ($sugestaoEnquadramento->getIdDistribuicaoAvaliacaoProposta()) {
            $arrayPesquisa['id_distribuicao_avaliacao_proposta'] = $sugestaoEnquadramento->getIdDistribuicaoAvaliacaoProposta();
        }
        if ($sugestaoEnquadramento->getIdPreprojeto()
            && $sugestaoEnquadramento->getIdOrgao()
            && $sugestaoEnquadramento->getIdPerfilUsuario()) {
            $arrayPesquisa['id_preprojeto'] = $sugestaoEnquadramento->getIdPreprojeto();
            $arrayPesquisa['id_orgao'] = $sugestaoEnquadramento->getIdOrgao();
            $arrayPesquisa['id_perfil_usuario'] = $sugestaoEnquadramento->getIdPerfilUsuario();
        }

        if ($sugestaoEnquadramento->getIdOrgaoSuperior()) {
            $arrayPesquisa['id_orgao_superior'] = $sugestaoEnquadramento->getIdOrgaoSuperior();
        }
        if ($sugestaoEnquadramento->getUltimaSugestao()) {
            $arrayPesquisa['ultima_sugestao'] = $sugestaoEnquadramento->getUltimaSugestao();
        }

        if (count($arrayPesquisa) > 0) {
            $resultado = $this->findAll($arrayPesquisa);
            if (count($resultado) > 0) {
                return true;
            }
            return false;
        }
    }


    public function inativarSugestoes($id_preprojeto)
    {
        $this->alterar(
            ['ultima_sugestao' => self::ULTIMA_SUGESTAO_INATIVA],
            ['id_preprojeto = ?' => $id_preprojeto]
        );
    }

    public function obterSugestaoAtiva($id_preprojeto)
    {
        return $this->findBy(
            [
                'id_preprojeto' => $id_preprojeto,
                'ultima_sugestao' => self::ULTIMA_SUGESTAO_ATIVA,
            ]
        );
    }

    public function salvarSugestaoEnquadramento(array $dadosSugestaoEnquadramento)
    {
        $sugestaoEnquadramento = new Admissibilidade_Model_SugestaoEnquadramento([
            'id_perfil_usuario' => $dadosSugestaoEnquadramento['id_perfil']
        ]);

        $descricao_motivacao = trim($dadosSugestaoEnquadramento['descricao_motivacao']);
        if (empty($descricao_motivacao)) {
            throw new Exception("O campo 'Parecer de Enquadramento' é de preenchimento obrigatório.");
        }

        if (!$sugestaoEnquadramento->isPermitidoSugerirEnquadramento()) {
            throw new Exception("Perfil sem permissão para executar a ação");
        }

        $id_area = ($dadosSugestaoEnquadramento['id_area']) ? $dadosSugestaoEnquadramento['id_area'] : null;
        $id_segmento = ($dadosSugestaoEnquadramento['id_segmento']) ? $dadosSugestaoEnquadramento['id_segmento'] : null;
        $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();

        $orgaoDbTable = new Orgaos();
        $resultadoOrgaoSuperior = $orgaoDbTable->codigoOrgaoSuperior($dadosSugestaoEnquadramento['id_orgao']);
        $orgaoSuperior = $resultadoOrgaoSuperior[0]['Superior'];

        $distribuicaoAvaliacaoPropostaDbTable = new Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta();
        $distribuicaoAvaliacaoPropostaDbTable->setDistribuicaoAvaliacaoProposta([
            'id_preprojeto' => $dadosSugestaoEnquadramento['id_preprojeto']
        ]);
        $distribuicaoAvaliacaoProposta = $distribuicaoAvaliacaoPropostaDbTable->findBy([
            'id_preprojeto' => $dadosSugestaoEnquadramento['id_preprojeto'],
            'id_orgao_superior' => $orgaoSuperior,
            'id_perfil' => $dadosSugestaoEnquadramento['id_perfil']
        ]);

        if (!$distribuicaoAvaliacaoProposta &&
            (
                $dadosSugestaoEnquadramento['id_perfil'] != Autenticacao_Model_Grupos::TECNICO_ADMISSIBILIDADE
                && $dadosSugestaoEnquadramento['id_perfil'] != Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE
            )
        ) {
            throw new Exception("Distribui&ccedil;&atilde;o n&atilde;o localizada para o perfil atual.");
        }

        $dadosNovaSugestaoEnquadramento = [
            'id_orgao' => $dadosSugestaoEnquadramento['id_orgao'],
            'id_preprojeto' => $dadosSugestaoEnquadramento['id_preprojeto'],
            'id_orgao_superior' => $orgaoSuperior,
            'id_perfil_usuario' => $dadosSugestaoEnquadramento['id_perfil'],
            'id_usuario_avaliador' => $dadosSugestaoEnquadramento['id_usuario_avaliador'],
            'id_area' => $id_area,
            'id_segmento' => $id_segmento,
            'descricao_motivacao' => $descricao_motivacao,
            'data_avaliacao' => $sugestaoEnquadramentoDbTable->getExpressionDate(),
            'ultima_sugestao' => Admissibilidade_Model_DbTable_SugestaoEnquadramento::ULTIMA_SUGESTAO_ATIVA,
        ];

        $dadosBuscaPorSugestao = $sugestaoEnquadramentoDbTable->findBy(
            [
                'id_orgao' => $dadosSugestaoEnquadramento['id_orgao'],
                'id_preprojeto' => $dadosSugestaoEnquadramento['id_preprojeto'],
                'id_orgao_superior' => $orgaoSuperior,
                'id_perfil_usuario' => $dadosSugestaoEnquadramento['id_perfil'],
                'id_usuario_avaliador' => $dadosSugestaoEnquadramento['id_usuario_avaliador']
            ]
        );

        if ($distribuicaoAvaliacaoProposta && $distribuicaoAvaliacaoProposta['id_distribuicao_avaliacao_prop']) {
            $dadosNovaSugestaoEnquadramento['id_distribuicao_avaliacao_proposta'] = $distribuicaoAvaliacaoProposta['id_distribuicao_avaliacao_prop'];
        }

        if (count($dadosBuscaPorSugestao) < 1) {
            $sugestaoEnquadramentoDbTable->inativarSugestoes($dadosSugestaoEnquadramento['id_preprojeto']);
            $sugestaoEnquadramentoDbTable->inserir($dadosNovaSugestaoEnquadramento);

            $distribuicaoAtiva = $distribuicaoAvaliacaoPropostaDbTable->obterDistribuicaoAtiva();
            if (/*$distribuicaoAtiva['id_perfil'] == Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE
            &&*/ $this->isPermitidoCadastrarRecurso($dadosSugestaoEnquadramento['id_perfil'])) {
                $tbRecursoPropostaDbTable = new Recurso_Model_DbTable_TbRecursoProposta();
                $tbRecursoPropostaDbTable->cadastrarRecurso($dadosSugestaoEnquadramento['id_preprojeto']);

                $this->enviarEmailAberturaDePrazoRecursal($dadosSugestaoEnquadramento['id_preprojeto']);
            }
        } else {
            $dadosBuscaPorSugestao['id_distribuicao_avaliacao_proposta'] = $distribuicaoAvaliacaoProposta['id_distribuicao_avaliacao_prop'];
            $sugestaoEnquadramentoDbTable->update($dadosNovaSugestaoEnquadramento, [
                'id_sugestao_enquadramento = ?' => $dadosBuscaPorSugestao['id_sugestao_enquadramento']
            ]);
        }

    }

    private function isPermitidoCadastrarRecurso($id_perfil) {
        return  ($id_perfil == Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE
        || $id_perfil == Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE);
    }

    private function enviarEmailAberturaDePrazoRecursal($id_preprojeto)
    {
        $distribuicaoAvaliacaoPropostaDbTable = new Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta();
        $distribuicaoAvaliacaoPropostaDbTable->setDistribuicaoAvaliacaoProposta(['id_preprojeto' => $id_preprojeto]);
        $mensagemEmail = <<<MENSAGEM_EMAIL
Foi aberto o prazo para entrada com Recurso ou Desist&ecirc;ncia do Prazo Recursal.
Ao acessar a Proposta {$id_preprojeto} a op&ccedil;&atilde;o "Enquadramento" no menu lateral estar&aacute; dispon&iacute;vel.
MENSAGEM_EMAIL;

        $preprojetoDbTable = new Proposta_Model_DbTable_PreProjeto();
        $preprojetoDbTable->enviarEmailProponente(
            $id_preprojeto,
            'Recurso',
            $mensagemEmail
        );
    }

}