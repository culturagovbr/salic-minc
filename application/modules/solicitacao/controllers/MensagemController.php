<?php

class Solicitacao_MensagemController extends Solicitacao_GenericController
{
    public function init()
    {
        parent::init();

        if (!empty($this->idPreProjeto) || !empty($this->idPronac)) {
            parent::verificarPermissaoAcesso(!empty($this->idPreProjeto), !empty($this->idPronac), false);
        }

        if ($this->idPronac) {
            $this->view->urlMenu = [
                'module' => 'projeto',
                'controller' => 'menu',
                'action' => 'obter-menu-ajax',
                'idPronac' => $this->idPronac
            ];
        }
    }

    public function indexAction()
    {
        $this->view->listarTudo = $this->getRequest()->getParam('listarTudo', null);
        $this->view->existeSolicitacaoEnviadaNaoRespondida = $this->verificarSolicitacaoEnviadaNaoRespondida(
            $this->idPreProjeto,
            $this->idPronac
        );

        $this->view->isArquivado = $this->verificarArquivamento();
    }

    private function verificarArquivamento() {
        if (!empty($this->projeto)) {
            return in_array(
                $this->projeto->Situacao,
                Projeto_Model_Situacao::obterSituacoesProjetoArquivado()
            );
        }

        if (!empty($this->proposta)) {
            return $this->proposta->stEstado == 0;
        }

        return false;
    }

    public function prepareForm($dataForm = [], $arrConfig = [], $strUrlAction = '', $strActionBack = 'index')
    {

        $intId = $this->getRequest()->getParam('id', null);

        if (!empty($dataForm['idDocumento'])) {
            $tbl = new Arquivo_Model_DbTable_TbDocumento();
            $dataForm['arquivo'] = $tbl->buscarDocumento($dataForm['idDocumento'])->toArray();
        }

        if (!empty($dataForm['idDocumentoResposta'])) {
            $tbl = new Arquivo_Model_DbTable_TbDocumento();
            $dataForm['arquivoResposta'] = $tbl->buscarDocumento($dataForm['idDocumentoResposta'])->toArray();
        }

        if ($this->proposta) {
            $dataForm['idProjeto'] = $this->idPreProjeto;
            $dataForm['NomeProjeto'] = isset($this->proposta->NomeProjeto) ? $this->proposta->NomeProjeto : '';
        }

        if ($this->projeto) {
            $dataForm['Pronac'] = $this->projeto->AnoProjeto . $this->projeto->Sequencial;
            $dataForm['NomeProjeto'] = isset($this->projeto->NomeProjeto) ? $this->projeto->NomeProjeto : '';
            $dataForm['idPronac'] = $this->idPronac;
        }

        $this->view->arrPartial = array(
            'dataForm' => $dataForm,
            'arrConfig' => $arrConfig,
            'id' => $intId,
            'urlAction' => $strUrlAction,
            'strActionBack' => $strActionBack,
            'currentUrl' => Zend_Controller_Front::getInstance()->getRequest()->getRequestUri(),
            'isProponente' => $this->isProponente
        );
    }

    public function listarAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->getRequest()->getParam('idPronac', null);
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto', null);
        $listarTudo = $this->getRequest()->getParam('listarTudo', null);
        $this->view->isTecnico = false;

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $where = [];
        if ($idPronac) {
            $where['a.idPronac = ?'] = (int) $idPronac;
        }

        if ($idPreProjeto) {
            $where['a.idProjeto = ?'] = (int) $idPreProjeto;
        }

        # Proponente
        if (empty($where)) {

            if (isset($this->usuario['cpf'])) {

                if (!empty($this->idAgente)) {
                    $whereAgente = "OR a.idAgente = {$this->idAgente}";
                }

                $where["(a.idSolicitante = {$this->idUsuario} {$whereAgente})"] = '';
            }
        }

        # funcionarios do minc
        if (isset($this->usuario['usu_codigo'])) {

            if (empty($listarTudo)) {

                $tecnicos = (new Autenticacao_Model_Grupos)->buscarTecnicosPorOrgao($this->grupoAtivo->codOrgao)->toArray();

                if (in_array($this->grupoAtivo->codGrupo, array_column($tecnicos, 'gru_codigo'))) {
                    $where['a.idTecnico = ?'] = $this->idUsuario;
                }

                $where['a.idOrgao = ?'] = $this->grupoAtivo->codOrgao;
                $where['a.siEncaminhamento = ?'] = Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC;
            }
        }

        $this->view->arrResult = (new Solicitacao_Model_DbTable_TbSolicitacao)->obterSolicitacoes($where);
        $this->view->idPronac = $idPronac;
        $this->view->codOrgaoUsuario = $this->grupoAtivo->codOrgao;

    }

    /**
     * utilizado nas notificacoes
     */
    public function listarAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->getRequest()->getParam('idPronac', null);
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto', null);
        $this->view->isTecnico = false;

        $tbSolicitacoes = new Solicitacao_Model_DbTable_TbSolicitacao();

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $where = [];
        if ($idPronac) {
            $where['a.idPronac = ?'] = (int) $idPronac;
        }

        if ($idPreProjeto) {
            $where['a.idProjeto = ?'] = (int) $idPreProjeto;
        }

        # Proponente
        if (isset($this->usuario['cpf'])) {
            $where["(a.idAgente = {$this->idAgente} OR a.idSolicitante = {$this->idUsuario})"] = '';
            $where['a.siEncaminhamento = ?'] = Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_FINALIZADA_MINC;
            $where['a.stLeitura = ?'] = 0;
        }

        if (isset($this->usuario['usu_codigo'])) {

            $where['a.idTecnico = ?'] = $this->idUsuario;
            $where['a.dsResposta IS NULL'] = '';

            if (isset($this->grupoAtivo->codOrgao)) {
                $where['a.idOrgao = ?'] = $this->grupoAtivo->codOrgao;
            }

            $where['a.siEncaminhamento = ?'] = Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC;
        }


        $solicitacoes = $tbSolicitacoes->obterSolicitacoes($where, ['dtResposta DESC', 'dtSolicitacao DESC']);

        $this->view->arrResult = $solicitacoes;
        $this->view->idPronac = $idPronac;
    }

    public function visualizarAction()
    {
        $urlAction = $this->_urlPadrao . "/solicitacao/mensagem/salvar";

        try {

            $idSolicitacao = $this->getRequest()->getParam('id', null);

            if (empty($idSolicitacao)) {
                throw new Exception("Informe o id da solicita&ccedil;&atilde;o para visualizar!");
            }

            $where['a.idSolicitacao = ?'] = $idSolicitacao;

            $tbSolicitacoes = new Solicitacao_Model_DbTable_TbSolicitacao();
            $dataForm = $tbSolicitacoes->obterSolicitacoes($where)->current()->toArray();

            if (empty($dataForm)) {
                throw new Exception("Nenhuma solicita&ccedil;&atilde;o encontrada!");
            }

            $permissao = parent::verificarPermissaoAcesso($dataForm['idProjeto'], $dataForm['idPronac'], false, true);

            if ($permissao['status'] === false) {
                throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar esta solicita&ccedil;&atilde;o");
            }

            # marcar como mensagem lida pelo proponente
            if ($dataForm['siEncaminhamento'] == Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_FINALIZADA_MINC) {
                if ($dataForm['idAgente'] == $this->idAgente || $dataForm['idSolicitante'] == $this->idUsuario) {

                    $model = new Solicitacao_Model_TbSolicitacao();
                    $model->setIdSolicitacao($dataForm['idSolicitacao']);
                    $model->setStLeitura(1);
                    $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();
                    $mapperSolicitacao->atualizarSolicitacao($model);
                }
            }

            $this->view->usuarioInterno = false;
            if (isset(Zend_Auth::getInstance()->getIdentity()->usu_codigo)) {
                $this->view->usuarioInterno = true;
            }

            $arrConfig['dsResposta']['show'] = true;

            $this->prepareForm($dataForm, $arrConfig, $urlAction);

        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/solicitacao/mensagem", "ALERT");
        }
    }


    public function solicitarAction()
    {
        $urlAction = $this->_urlPadrao . "/solicitacao/mensagem/salvar";
        $urlCallBack = $this->_urlPadrao . "/solicitacao/mensagem/index";
        $this->view->isEditavel = true;
        try {

            if (empty($this->idPronac) && empty($this->idPreProjeto)) {
                throw new Exception("Informe o projeto ou proposta para realizar uma solicita&ccedil;&atilde;o");
            }

            if($this->verificarArquivamento()) {
                throw new Exception("Projeto ou proposta arquivado, voc&ecirc; n&atilde;o pode realizar uma solicita&ccedil;&atilde;o");
            }

            $dataForm = [
                'siEncaminhamento' => Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_CADASTRADA
            ];

            $arrConfig['dsSolicitacao']['disabled'] = false;
            $arrConfig['actions']['show'] = true;

            $whereSolicitacoes = [];
            if ($this->projeto) {
                $urlCallBack .= '/idPronac/' . $this->idPronac;
                $dataForm['idPronac'] = $this->idPronac;
                $whereSolicitacoes['idPronac'] = $this->idPronac;
            } else if ($this->proposta) {
                $urlCallBack .= '/idPreProjeto/' . $this->idPreProjeto;
                $dataForm['idProjeto'] = $this->idPreProjeto;
                $whereSolicitacoes['idProjeto'] = $this->idPreProjeto;
            }

            $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();
            $dataForm = $mapperSolicitacao->obterSolicitacaoAtiva($whereSolicitacoes);

            if ($dataForm['siEncaminhamento'] == Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC) {
                $this->redirect($this->_urlPadrao . '/solicitacao/mensagem/visualizar/id/' . $dataForm['idSolicitacao']);
            }

            $this->prepareForm($dataForm, $arrConfig, $urlAction, $urlCallBack);

        } catch (Exception $objException) {
            parent::message($objException->getMessage(), $urlCallBack, "ALERT");
        }
    }

    public function salvarAction()
    {
        if ($this->getRequest()->isPost()) {

            try {

                if($this->verificarArquivamento()) {
                    throw new Exception("Projeto ou proposta arquivado, voc&ecirc; n&atilde;o pode realizar uma solicita&ccedil;&atilde;o");
                }

                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);

                $arrayForm = $this->getRequest()->getPost();
                $arrayForm['idUsuario'] = $this->idUsuario;

                $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();
                $solicitacao = $mapperSolicitacao->obterSolicitacaoAtiva($arrayForm);

                if (!empty($solicitacao)) {

                    if ($solicitacao['siEncaminhamento'] <> Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_CADASTRADA) {
                        throw new Exception("Voc&ecirc; j&aacute; possui uma solicita&ccedil;&atilde;o aguardando resposta para este projeto!");
                    }

                    $arrayForm['idSolicitacao'] = $solicitacao['idSolicitacao'];
                }

                $idSolicitacao = $mapperSolicitacao->salvar($arrayForm);

                $status = true;
                if (empty($idSolicitacao)) {
                    $status = false;
                }

                $strParams = '';
                $strParams .= ($arrayForm['idPronac']) ? '/idPronac/' . $arrayForm['idPronac'] : '';
                $strParams .= ($arrayForm['idProjeto']) ? '/idPreProjeto/' . $arrayForm['idProjeto'] : '';

                $strUrl = '/solicitacao/mensagem/solicitar' . $strParams;
                if ($arrayForm['siEncaminhamento'] == Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC
                    && $status) {
                    $strUrl = '/solicitacao/mensagem/visualizar/id/' . $idSolicitacao . $strParams;
                }

                $this->_helper->json(array('status' => $status, 'msg' => $mapperSolicitacao->getMessages(), 'redirect' => $strUrl));
            } catch (Exception $objException) {
                $this->_helper->json(array('status' => false, 'msg' => $objException->getMessage()));
            }

        }
    }

    public function deletarArquivoAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->getRequest()->isPost()) {
            try {
                $arrayForm = $this->getRequest()->getPost();

                (new Solicitacao_Model_TbSolicitacaoMapper)->deletarArquivo($arrayForm);
                (new Arquivo_Model_DbTable_TbDocumento)->excluir("idDocumento = {$arrayForm['idDocumento']}");

            } catch (Exception $objException) {
                echo $objException->getMessage();
            }
        }
    }

    public function responderAction()
    {
        $idSolicitacao = $this->getRequest()->getParam('id', null);
        $strActionBack = "solicitacao/mensagem/index";

        try {

            if (empty($idSolicitacao)) {
                throw new Exception("Informe o id da solicita&ccedil;&atilde;o para responder!");
            }

            $where['idSolicitacao = ?'] = $idSolicitacao;

            $tbSolicitacao = new Solicitacao_Model_DbTable_TbSolicitacao();
            $solicitacao = $tbSolicitacao->obterSolicitacoes($where)->current()->toArray();

            if (empty($solicitacao)) {
                throw new Exception("Nenhuma solicita&ccedil;&atilde;o encontrada!");
            }

            if ($solicitacao['idTecnico'] != $this->idUsuario) {
                throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para responder esta solicita&ccedil;&atilde;o!");
            }

            if (!empty($solicitacao['dsResposta'])) {
                $this->redirect("/solicitacao/mensagem/visualizar/id/{$idSolicitacao}");
            }


            if ($this->getRequest()->isPost()) {

                $status = false;

                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                $arrayForm = $this->getRequest()->getPost();

                $strUrl = '/solicitacao/mensagem/index';
                $strUrl .= ($arrayForm['idPronac']) ? '/idPronac/' . $arrayForm['idPronac'] : '';
                $strUrl .= ($arrayForm['idProposta']) ? '/idproposta/' . $arrayForm['idproposta'] : '';
                $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();

                $idSolicitacao = $mapperSolicitacao->responder($this->getRequest()->getPost());

                if ($idSolicitacao) {
                    $strUrl = '/solicitacao/mensagem/visualizar/id/' . $idSolicitacao;
                    $status = true;
                }

                $this->_helper->json(
                    array(
                        'status' => $status,
                        'msg' => $mapperSolicitacao->getMessages(),
                        'redirect' => $strUrl
                    )
                );

            } else {

                $vwGrupos = new vwUsuariosOrgaosGrupos();

                $arrConfig = [
                    'dsSolicitacao' => ['disabled' => true],
                    'dsResposta' => ['show' => true, 'disabled' => false],
                    'actions' => ['show' => true],
                    'actionredistribuirSolicitacao' => $this->_urlPadrao . "/solicitacao/mensagem/redistribuir-solicitacao",
                    'redistribuirTecnicos' => $vwGrupos->carregarTecnicosPorUnidade($solicitacao['idOrgao']),
                ];


                $this->prepareForm($solicitacao, $arrConfig, '', $strActionBack);
            }

            $this->view->arrConfig['dsMensagem'] = ['disabled' => true];

        } catch (Exception $objException) {
            parent::message($objException->getMessage(), $strActionBack, "ALERT");
        }
    }

    public function encaminharAction()
    {
        $idSolicitacao = $this->getRequest()->getParam('id', null);
        $strActionBack = "solicitacao/mensagem/index";

        try {

            if (empty($idSolicitacao)) {
                throw new Exception("Informe o id da solicita&ccedil;&atilde;o para responder!");
            }
            $where = [];
            $where['idSolicitacao = ?'] = $idSolicitacao;

            $tbSolicitacao = new Solicitacao_Model_DbTable_TbSolicitacao();
            $solicitacao = $tbSolicitacao->obterSolicitacoes($where)->current()->toArray();

            if (empty($solicitacao)) {
                throw new Exception("Nenhuma solicita&ccedil;&atilde;o encontrada!");
            }

            if ($this->isProponente || $this->grupoAtivo->codOrgao != $solicitacao['idOrgao']) {
                throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa p&aacute;gina!");
            }

            if (!empty($solicitacao['dsResposta'])) {
                $this->redirect("/solicitacao/mensagem/visualizar/id/{$idSolicitacao}");
            }

            $orgaos = new Orgaos();

            $arrConfig = [
                'dsSolicitacao' => ['disabled' => true],
                'dsResposta' => ['show' => true, 'disabled' => false],
                'actions' => ['show' => true],
                'actionredistribuirSolicitacao' => $this->_urlPadrao . "/solicitacao/mensagem/redistribuir-solicitacao",
                'unidades' => $orgaos->pesquisarUnidades(array('o.Sigla != ?' => '', 'o.idSecretaria IN (?)' => [160, 251])),
                'tecnico' => $solicitacao['idTecnico'],

            ];

            $this->prepareForm($solicitacao, $arrConfig, '', $strActionBack);

            $this->view->arrConfig['dsMensagem'] = ['disabled' => true];

        } catch (Exception $objException) {
            parent::message($objException->getMessage(), $strActionBack, "ALERT");
        }
    }

    public function usuariosAction()
    {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $vw = new vwUsuariosOrgaosGrupos();
            $intId = $this->getRequest()->getParam('intId', null);
            $arrUsuarios = $vw->carregarTecnicosPorUnidade($intId)->toArray();
            $arrUsuarios = TratarArray::utf8EncodeArray($arrUsuarios);
            $this->_helper->json($arrUsuarios);
        } catch (Exception $objException) {
            $this->_helper->json(array('status' => false, 'msg' => $objException->getMessage()));
        }
    }

    public function redistribuirSolicitacaoAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->isPost()) {

            try {

                $arrayForm = $this->getRequest()->getPost();

                if (empty($arrayForm['idTecnico'])) {
                    throw new Exception("T&eacute;cnico &eacute; obrigat&oacute;rio!");
                }

                if (empty($arrayForm['idSolicitacao'])) {
                    throw new Exception("Solicita&ccedil;&atilde;o &eacute; obrigat&oacute;rio!");
                }

                $where = [];
                $where['idSolicitacao = ?'] = $arrayForm['idSolicitacao'];

                $tbSolicitacao = new Solicitacao_Model_DbTable_TbSolicitacao();
                $solicitacao = $tbSolicitacao->obterSolicitacoes($where)->current()->toArray();

                if ($this->isProponente || $this->grupoAtivo->codOrgao != $solicitacao['idOrgao']) {
                    throw new Exception("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa p&aacute;gina!");
                }

                $strUrl = '/solicitacao/mensagem/index';
                $strUrl .= ($arrayForm['idPronac']) ? '/idPronac/' . $arrayForm['idPronac'] : '';
                $strUrl .= ($arrayForm['idProposta']) ? '/idproposta/' . $arrayForm['idproposta'] : '';

                $model = new Solicitacao_Model_TbSolicitacao();
                $model->setIdSolicitacao($arrayForm['idSolicitacao']);
                $model->setIdTecnico($arrayForm['idTecnico']);
                $model->setIdOrgao($arrayForm['idOrgao']);
                $model->setDtEncaminhamento(date('Y-m-d h:i:s'));

                $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();
                $idSolicitacao = $mapperSolicitacao->atualizarSolicitacao($model);

                if ($idSolicitacao) {
                    $this->_helper->json(array('status' => true, 'msg' => 'Encaminhamento realizado com sucesso!', 'redirect' => $strUrl));
                }

            } catch (Exception $objException) {
                $this->_helper->json(array('status' => false, 'msg' => $objException->getMessage(), 'redirect' => $strUrl));
            }
        }
    }

    public function abrirdocumentosolicitacaoAction()
    {
        $idDocumento = $this->getRequest()->getParam('id', null);

        try {
            $tbSolicitacao = new Solicitacao_Model_DbTable_TbSolicitacao();
            $solicitacao = $tbSolicitacao->findBy(
                ['idDocumentoResposta = ? OR idDocumento = ?' => $idDocumento]
            );

            if (empty($solicitacao)) {
                throw new Exception('Documento n&atilde;o encontrado!');
            }

            $idProjeto = $solicitacao['idProjeto'] ? $solicitacao['idProjeto'] : false;
            $idPronac = $solicitacao['idPronac'] ? $solicitacao['idPronac'] : false;

            # verificar se o usuario tem permissao para acessar este documento por meio do id do projeto/proposta
            $permissao = parent::verificarPermissaoAcesso($idProjeto, $idPronac, false, true);

            if ($permissao['status'] === false) {
                throw new Exception('Voc&ecirc; n&atilde;o tem permiss&atilde;o para baixar esse arquivo!');
            }

            parent::abrirDocumento($idDocumento);

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function contarSolicitacoesNaoLidasAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            $tbSolicitacao = new Solicitacao_Model_DbTable_TbSolicitacao();

            if ($this->usuario['usu_codigo']) {
                $resultado = $tbSolicitacao->contarSolicitacoesNaoRespondidasTecnico($this->idUsuario, $this->grupoAtivo->codOrgao);
            } else {
                $resultado = $tbSolicitacao->contarSolicitacoesNaoLidasUsuario($this->idUsuario, $this->idAgente);
            }

            $this->_helper->json(array('status' => true, 'msg' => $resultado));
        } catch (Exception $objException) {
            $this->_helper->json(array('status' => false, 'msg' => $objException->getMessage()));
        }
    }

    private function verificarSolicitacaoEnviadaNaoRespondida($idPreProjeto = null, $idPronac = null)
    {
        $where = [];
        if ($idPreProjeto) {
            $where['idProjeto'] = $idPreProjeto;
        }

        if ($idPronac) {
            $where['idPronac'] = $idPronac;
        }

        $mapperSolicitacao = new Solicitacao_Model_TbSolicitacaoMapper();
        $dataForm = $mapperSolicitacao->obterSolicitacaoAtiva($where);

        if (empty($dataForm)) {
            return false;
        }

        return $dataForm['siEncaminhamento'] != Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_CADASTRADA;
    }
}
