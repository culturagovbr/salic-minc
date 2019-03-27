<?php

class Recurso_RecursoPropostaController extends Proposta_GenericController
{

    private $authIdentity;
    private $grupoAtivo;
    private $auth;

    const VISAO_PROPONENTE = 'proponente';
    const VISAO_AVALIADOR = 'avaliador';

    public function init()
    {
        parent::init();

        $this->auth = Zend_Auth::getInstance();
        $this->authIdentity = array_change_key_case((array)$this->auth->getIdentity());
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        $this->view->id_perfil = $this->grupoAtivo->codGrupo;
    }

    public function indexAction()
    {
        throw new Exception("Implementar");
    }

    public function visaoProponenteAction()
    {
        if ((int)$this->view->id_perfil != (int)Autenticacao_Model_Grupos::PROPONENTE) {
            throw new Exception("Perfil de Usu&aacute;rio sem permiss&atilde;o acessar essa funcionalidade.");
        }

        $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();
        $sugestaoEnquadramentoDbTable->sugestaoEnquadramento->setIdPreprojeto($this->idPreProjeto);
        $this->view->recursoEnquadramento = $sugestaoEnquadramentoDbTable->obterRecursoEnquadramentoProposta();
        if (!empty($this->view->recursoEnquadramento['idArquivo'])
            && !is_null($this->view->recursoEnquadramento['idArquivo'])) {
            $tbArquivoDbTable = new tbArquivo();
            $this->view->arquivoRecursoProponente = $tbArquivoDbTable->findBy([
                'idArquivo' => $this->view->recursoEnquadramento['idArquivo'],
            ]);
        }

        $this->view->isPermitidoEditar = $this->_isPermitidoEditar(
            $this->view->recursoEnquadramento,
            Recurso_RecursoPropostaController::VISAO_PROPONENTE
        );

        $recursoPropostaDbTable = new Recurso_Model_DbTable_TbRecursoProposta();
        $this->view->historicoRecursoProposta = $recursoPropostaDbTable->obterHistoricoRecurso($this->idPreProjeto)->toArray();
    }

    private function _isPermitidoEditar($recursoEnquadramento, $visao = Recurso_RecursoPropostaController::VISAO_PROPONENTE)
    {
        return (
            (
                is_null($recursoEnquadramento['stRascunho'])
                ||
                (
                    !is_null($recursoEnquadramento['stRascunho']) &&
                    (int)$recursoEnquadramento['stRascunho'] != (int)Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_ENVIADO
                )
            ) && !$recursoEnquadramento['dtAvaliacaoTecnica']
        );
    }

    public function visaoAvaliadorAction()
    {
        if ((int)$this->view->id_perfil != (int)Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE
            && (int)$this->view->id_perfil != (int)Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE) {
            throw new Exception("Perfil de Usu&aacute;rio sem permiss&atilde;o acessar essa funcionalidade.");
        }

        $get = $this->getRequest()->getParams();
        $idPreProjeto = trim($get['idPreProjeto']);

        if (empty($idPreProjeto) || is_null($idPreProjeto)) {
            throw new Exception("Identificador da Proposta n&atilde;o foi localizado.");
        }

        $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();
        $sugestaoEnquadramentoDbTable->sugestaoEnquadramento->setIdPreprojeto($idPreProjeto);
        $this->view->recursoEnquadramento = $sugestaoEnquadramentoDbTable->obterRecursoEnquadramentoProposta();

        if (!empty($this->view->recursoEnquadramento['idArquivo'])
            && !is_null($this->view->recursoEnquadramento['idArquivo'])) {
            $tbArquivoDbTable = new tbArquivo();
            $this->view->arquivoRecursoProponente = $tbArquivoDbTable->findBy([
                'idArquivo' => $this->view->recursoEnquadramento['idArquivo'],
            ]);
        }

        $this->view->isPermitidoEditar = (
            is_null($this->view->recursoEnquadramento['stRascunho'])
            || (
                !is_null($this->view->recursoEnquadramento['stRascunho'])
                && (int)$this->view->recursoEnquadramento['stRascunho'] != (int)Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_ENVIADO
            )
            || (
                !is_null($this->view->recursoEnquadramento['stRascunho'])
                && (int)$this->view->recursoEnquadramento['stRascunho'] == (int)Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_ENVIADO
                && !$this->view->recursoEnquadramento['dtAvaliacaoTecnica']
            )
        );

        $planoDistribuicaoProdutoDbTable = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $this->view->enquadramentoInicialProponente = $planoDistribuicaoProdutoDbTable->obterEnquadramentoInicialProponente($idPreProjeto);
    }

    /**
     * @todo Refatorar esse trecho de c&oacute;digo pois as demandas desse m&oacute;dulo foram emergenciais.
     */
    public function visaoProponenteSalvarAction()
    {
        try {
            $post = $this->getRequest()->getPost();
            $id_preprojeto = trim($post['id_preprojeto']);
            if (empty($id_preprojeto) || is_null($id_preprojeto)) {
                throw new Exception("Identificador da Proposta n&atilde;o foi localizado.");
            }

            $tpSolicitacao = trim($post['tpSolicitacao']);
            if (empty($tpSolicitacao) || is_null($tpSolicitacao)) {
                throw new Exception("O campo 'Tipo de Solicita&amp;ccedil;&amp;atilde;o' &eacute; de preenchimento obrigat&oacute;rio.");
            }

            $justificativa = trim($post['dsRecursoProponente']);
            if (empty($justificativa) || is_null($justificativa)) {
                throw new Exception("O campo 'Justificativa' &eacute; de preenchimento obrigat&oacute;rio.");
            }

            $acao_salvar = trim($post['acao_salvar']);
            if (empty($acao_salvar) || is_null($acao_salvar)) {
                throw new Exception("Bot&atilde;o de a&ccedil;&atilde;o n&atilde;o informado.");
            }
            $stRascunho = ($acao_salvar == 'rascunho') ? Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_SALVO : Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_ENVIADO;

            $recursoEnquadramentoDbTable = new Recurso_Model_DbTable_TbRecursoProposta();
            $recursoEnquadramento = $recursoEnquadramentoDbTable->obterRecursoAtualVisaoProponente($id_preprojeto);

            $idArquivo = $this->uploadAnexoProponente($recursoEnquadramento);
            $tbRecursoModel = new Recurso_Model_TbRecursoProposta([
                'idRecursoProposta' => $recursoEnquadramento['idRecursoProposta'],
                'idPreProjeto' => $id_preprojeto,
                'dtRecursoProponente' => $recursoEnquadramentoDbTable->getExpressionDate(),
                'dsRecursoProponente' => $justificativa,
                'tpSolicitacao' => $tpSolicitacao,
                'idArquivo' => $idArquivo,
                'stRascunho' => $stRascunho,
            ]);
            $tbRecursoMapper = new Recurso_Model_TbRecursoPropostaMapper();
            $tbRecursoMapper->save($tbRecursoModel);

            parent::message(
                'Dados armazenados com sucesso.',
                "/recurso/recurso-proposta/visao-proponente/idPreProjeto/{$id_preprojeto}",
                'CONFIRM'
            );
        } catch (Exception $exception) {
            if ($id_preprojeto) {
                parent::message($exception->getMessage(), "/recurso/recurso-proposta/visao-proponente/idPreProjeto/{$id_preprojeto}");
            }
            parent::message($exception->getMessage(), "/proposta/manterpropostaincentivofiscal/listarproposta");
        }
    }

    public function visaoAvaliadorSalvarAction()
    {
        try {
            $post = $this->getRequest()->getPost();
            $id_preprojeto = trim($post['id_preprojeto']);
            if (empty($id_preprojeto) || is_null($id_preprojeto)) {
                throw new Exception("Identificador da Proposta n&atilde;o foi localizado.");
            }

            $stAtendimento = trim($post['stAtendimento']);
            if (empty($stAtendimento) || is_null($stAtendimento)) {
                throw new Exception("O campo 'Tipo de Avalia&ccedil;&atilde;o' &eacute; de preenchimento obrigat&oacute;rio.");
            }

            $dsAvaliacaoTecnica = trim($post['dsAvaliacaoTecnica']);
            if (empty($dsAvaliacaoTecnica) || is_null($dsAvaliacaoTecnica)) {
                throw new Exception("O campo 'Motiva&ccedil;&atilde;o' &eacute; de preenchimento obrigat&oacute;rio.");
            }

            $acao_salvar = trim($post['acao_salvar']);
            if (empty($acao_salvar) || is_null($acao_salvar)) {
                throw new Exception("Bot&atilde;o de a&ccedil;&atilde;o n&atilde;o informado.");
            }
            $stRascunho = (int)Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_ENVIADO;
            if ($acao_salvar == 'rascunho') {
                $stRascunho = (int)Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_SALVO;
            }

            $idAvaliadorTecnico = $this->authIdentity['usu_codigo'];
            $recursoEnquadramentoDbTable = new Recurso_Model_DbTable_TbRecursoProposta();
            $recursoEnquadramento = $recursoEnquadramentoDbTable->obterRecursoAtual($id_preprojeto);

            if (!$recursoEnquadramento['idRecursoProposta']) {
                throw new Exception("Identificador do Recurso da Proposta n&atilde;o localizado.");
            }

            if ($recursoEnquadramento['stRascunho'] == Recurso_Model_TbRecursoProposta::SITUACAO_RASCUNHO_ENVIADO
                && !empty(trim($recursoEnquadramento['dtAvaliacaoTecnica']))) {
                throw new Exception("N&atilde;o &eacute; poss&iacute;vel avaliar novamente um recurso j&aacute; Enviado.");
            }

            $stAtivo = Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_ATIVO;
            if ($recursoEnquadramento['stAtendimento'] == Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_INDEFERIDO
                && $recursoEnquadramento['tpRecurso'] == Recurso_Model_TbRecursoProposta::TIPO_RECURSO_PEDIDO_DE_RECONSIDERACAO) {
                $stAtivo = Recurso_Model_TbRecursoProposta::SITUACAO_RECURSO_INATIVO;
            }

            $tbRecursoModel = new Recurso_Model_TbRecursoProposta([
                'idRecursoProposta' => $recursoEnquadramento['idRecursoProposta'],
                'dtAvaliacaoTecnica' => $recursoEnquadramentoDbTable->getExpressionDate(),
                'dsAvaliacaoTecnica' => $dsAvaliacaoTecnica,
                'stAtendimento' => $stAtendimento,
                'idAvaliadorTecnico' => $idAvaliadorTecnico,
                'stRascunho' => $stRascunho,
                'stAtivo' => $stAtivo,
            ]);
            $tbRecursoMapper = new Recurso_Model_TbRecursoPropostaMapper();
            $tbRecursoMapper->save($tbRecursoModel);

            if ($stAtendimento == (string)Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_INDEFERIDO
                && (int)$recursoEnquadramento['tpRecurso'] == (int)Recurso_Model_TbRecursoProposta::TIPO_RECURSO_PEDIDO_DE_RECONSIDERACAO) {
                $tbRecursoPropostaDbTable = new Recurso_Model_DbTable_TbRecursoProposta();
                $tbRecursoPropostaDbTable->cadastrarRecurso(
                    $id_preprojeto,
                    Recurso_Model_TbRecursoProposta::TIPO_RECURSO_RECURSO
                );
            }

            if ($stAtendimento == (string)Recurso_Model_TbRecursoProposta::SITUACAO_ATENDIMENTO_DEFERIDO) {
                $planoDistribuicaoProdutoDbTable = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
                $enquadramentoInicialProponente = $planoDistribuicaoProdutoDbTable->obterEnquadramentoInicialProponente($this->idPreProjeto);

                $dadosSugestaoEnquadramento = [];
                $dadosSugestaoEnquadramento['id_orgao'] = $this->grupoAtivo->codOrgao;
                $dadosSugestaoEnquadramento['id_perfil'] = $this->grupoAtivo->codGrupo;
                $dadosSugestaoEnquadramento['id_usuario_avaliador'] = $this->auth->getIdentity()->usu_codigo;
                $dadosSugestaoEnquadramento['id_preprojeto'] = $id_preprojeto;
                $dadosSugestaoEnquadramento['descricao_motivacao'] = $dsAvaliacaoTecnica;
                $dadosSugestaoEnquadramento['id_area'] = $enquadramentoInicialProponente['id_area'];
                $dadosSugestaoEnquadramento['id_segmento'] = $enquadramentoInicialProponente['id_segmento'];

                $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();
                $sugestaoEnquadramentoDbTable->salvarSugestaoEnquadramento($dadosSugestaoEnquadramento, false);
            }

            parent::message(
                'Dados armazenados com sucesso.',
                "/admissibilidade/admissibilidade/exibirpropostacultural/idPreProjeto/{$id_preprojeto}?realizar_analise=sim",
                'CONFIRM'
            );
        } catch (Exception $exception) {
            if ($id_preprojeto) {
                parent::message($exception->getMessage(), "/recurso/recurso-proposta/visao-avaliador/idPreProjeto/{$id_preprojeto}");
            }
            parent::message($exception->getMessage(), "/admissibilidade/admissibilidade/listar-propostas");
        }
    }

    /**
     * @return int|null
     */
    private function uploadAnexoProponente(array $recursoProposta)
    {

        $nomeArquivoUpload = 'arquivo';
        $file = new Zend_File_Transfer();
        $tbArquivoDbTable = new tbArquivo();
        if ($file->isUploaded() && !empty($file->getFileInfo()) && $recursoProposta['idArquivo']) {
            $tbArquivoDbTable->removerAnexoDoRecursoDaPropostaVisaoProponente($recursoProposta, $this->authIdentity['idusuario']);
        }

        return $tbArquivoDbTable->uploadAnexoSqlServer($this->authIdentity['idusuario'], $nomeArquivoUpload);
    }

    public function removerAnexoProponenteAction()
    {
        try {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $get = $this->getRequest()->getParams();

            $id_perfil = $this->grupoAtivo->codGrupo;
            if ((int)$id_perfil != (int)Autenticacao_Model_Grupos::PROPONENTE) {
                throw new Exception("Perfil de Usu&aacute;rio sem permiss&atilde;o para realizar essa opera&ccedil;&atilde;o.");
            }

            $id_preprojeto = trim($get['id_preprojeto']);
            if (empty($id_preprojeto) || is_null($id_preprojeto)) {
                throw new Exception("Identificador da Proposta n&atilde;o foi localizado.");
            }

            $idArquivo = trim($get['idArquivo']);
            if (empty($idArquivo) || is_null($idArquivo)) {
                throw new Exception("Identificador do arquivo foi localizado.");
            }

            $recursoPropostaDbTable = new Recurso_Model_DbTable_TbRecursoProposta();
            $recursoProposta = $recursoPropostaDbTable->obterRecursoAtualVisaoProponente($id_preprojeto);

            if (count($recursoProposta) < 1) {
                throw new Exception("Informa&ccedil;&otilde;es do Arquivo e Proposta Cultural n&atilde;o coincidem.");
            }

            $fnVerificarPermissao = new Autenticacao_Model_FnVerificarPermissao();
            $possuiPermissaoDeEdicao = (bool)$fnVerificarPermissao->verificarPermissaoProposta(
                $id_preprojeto,
                $this->authIdentity['idusuario'],
                false
            );
            if (!$possuiPermissaoDeEdicao) {
                throw new Exception("Usu&aacute;rio sem permiss&atilde;o para remo&ccedil;&atilde;o do arquivo.");
            }

            $tbArquivoDbTable = new tbArquivo();
            $isArquivoRemovido = $tbArquivoDbTable->removerAnexoDoRecursoDaPropostaVisaoProponente(
                $recursoProposta,
                $this->authIdentity['idusuario']
            );

            if ($isArquivoRemovido) {
                parent::message(
                    'Anexo removido com sucesso.',
                    "/recurso/recurso-proposta/visao-proponente/idPreProjeto/{$id_preprojeto}",
                    'CONFIRM'
                );
            } else {
                parent::message(
                    'Não foi possível remover o arquivo..',
                    "/recurso/recurso-proposta/visao-proponente/idPreProjeto/{$id_preprojeto}",
                    'ALERT'
                );
            }
        } catch (Exception $objException) {
            throw $objException;
        }
    }

}
