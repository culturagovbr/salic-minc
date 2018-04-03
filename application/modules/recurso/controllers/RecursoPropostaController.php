<?php

class Recurso_RecursoPropostaController extends Proposta_GenericController
{

    private $authIdentity;

    public function init()
    {
        parent::init();

        $auth = Zend_Auth::getInstance();
        $this->authIdentity = array_change_key_case((array)$auth->getIdentity());

    }

    public function indexAction()
    {
        throw new Exception("Implementar");
    }

    public function visaoProponenteAction()
    {
        $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();
        $sugestaoEnquadramentoDbTable->sugestaoEnquadramento->setIdPreprojeto($this->idPreProjeto);
        $this->view->recursoEnquadramento = $sugestaoEnquadramentoDbTable->obterRecursoEnquadramentoProposta();
        if (!empty($this->view->recursoEnquadramento['idArquivo'])
            && !is_null($this->view->recursoEnquadramento['idArquivo'])) {
            $tbArquivo = new tbArquivo();
            $this->view->arquivoRecursoProponente = $tbArquivo->buscarDados($this->view->recursoEnquadramento['idArquivo']);
        }
    }

    /**
     * @todo Refatorar esse trecho de c&oacute;digo pois as demandas desse m&oacute;dulo foram emergenciais.
     */
    public function visaoProponenteSalvarAction()
    {
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
            'idPreProjeto' => $recursoEnquadramento['idPreProjeto'],
            'dtRecursoProponente' => $recursoEnquadramentoDbTable->getExpressionDate(),
            'dsRecursoProponente' => $justificativa,
            'tpRecurso' => Recurso_Model_TbRecursoProposta::TIPO_RECURSO_PEDIDO_DE_RECONSIDERACAO,
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

            $grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
            $id_perfil = $grupoAtivo->codGrupo;
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

    public function downloadAnexoProponente()
    {
//        $idDocumento = $this->getRequest()->getParam('id', null);
//        try {
//            $tbSolicitacao = new Solicitacao_Model_DbTable_TbSolicitacao();
//            $solicitacao = $tbSolicitacao->obterSolicitacoes(
//                ['a.idDocumento = ?' => $idDocumento]
//            )->current();
//            if (empty($solicitacao))
//                throw new Exception('Documento n&atilde;o encontrado!');
//            $idProjeto = $solicitacao['idProjeto'] ? $solicitacao['idProjeto'] : false;
//            $idPronac = $solicitacao['idPronac'] ? $solicitacao['idPronac'] : false;
//            # verificar se o usuario tem permissao para acessar este documento por meio do id do projeto/proposta
//            $permissao = parent::verificarPermissaoAcesso($idProjeto, $idPronac, false, true);
//            if ($permissao['status'] === false)
//                throw new Exception('Voc&ecirc; n&atilde;o tem permiss&atilde;o para baixar esse arquivo!');
//            parent::abrirDocumento($idDocumento);
//        } catch (Exception $e) {
//            throw $e;
//        }


    }

//    public function abrirDocumentosPreProjetoAction()
//    {
//        $get = Zend_Registry::get('get');
//        $id = (int) isset($get->id) ? $get->id : $this->_request->getParam('id');
//
//        # Configuracao o php.ini para 10MB
//        @ini_set("mssql.textsize", 10485760);
//        @ini_set("mssql.textlimit", 10485760);
//        @ini_set("upload_max_filesize", "10M");
//
//        # busca o arquivo
//        $tbl = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
//        $resultado = $tbl->abrir($id)->current();
//
//        # erro ao abrir o arquivo
//        $this->_helper->layout->disableLayout();        # Desabilita o Zend Layout
//        $this->_helper->viewRenderer->setNoRender();    # Desabilita o Zend Render
//        if (!$resultado) {
//            die("N&atilde;o existe o arquivo especificado");
//            $this->view->message = 'N&atilde;o foi poss&iacute;vel abrir o arquivo!';
//            $this->view->message_type = 'ERROR';
//        } else {
//            Zend_Layout::getMvcInstance()->disableLayout(); # Desabilita o Zend MVC
//            $this->_response->clearBody();                  # Limpa o corpo html
//            $this->_response->clearHeaders();               # Limpa os headers do Zend
//            $up = new Upload();
//            $tipoArquivo = method_exists($up, $up->getMimeType($resultado->noarquivo)) ? $up->getMimeType("jpg") : "application/pdf";
//            if ($tbl->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mssql) {
//                $this->getResponse()
//                    ->setHeader('Content-Type', $tipoArquivo)
//                    ->setHeader('Content-Disposition', 'attachment; filename="' . $resultado->noarquivo . '"')
//                    ->setBody($resultado->imdocumento);
//            } else {
//                $this->getResponse()
//                    ->setHeader('Content-Type', $tipoArquivo)
//                    ->setHeader('Content-Disposition', 'attachment; filename="' . $resultado->noarquivo . '"');
//                readfile(APPLICATION_PATH . '/..' . $resultado->imdocumento);
//            }
//        }
//    }
}
