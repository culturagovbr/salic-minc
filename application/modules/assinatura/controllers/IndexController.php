<?php

class Assinatura_IndexController extends Assinatura_GenericController
{
    private $idTipoDoAtoAdministrativo;

    private $grupoAtivo;

    private $cod_usuario;

    public function init()
    {
        parent::init();

        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        $this->cod_usuario = $this->auth->getIdentity()->usu_codigo;

        isset($this->auth->getIdentity()->usu_codigo) ? parent::perfil() : parent::perfil(4);

        $this->definirModuloDeOrigem();
    }

    private function definirModuloDeOrigem()
    {
//        $this->view->module = $this->moduleName;
        $get = Zend_Registry::get('get');
        $post = (object)$this->getRequest()->getPost();
        $this->view->origin = "{$this->moduleName}/index";
        if (!empty($get->origin) || !empty($post->origin)) {
            $this->view->origin = (!empty($post->origin)) ? $post->origin : $get->origin;
        }
    }

    public function indexAction()
    {
        $this->redirect("/{$this->moduleName}/index/gerenciar-assinaturas");
    }

    public function gerenciarAssinaturasAction()
    {
        $this->view->idUsuarioLogado = $this->auth->getIdentity()->usu_codigo;
        $documentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $this->view->dados = $documentoAssinatura->obterProjetosComAssinaturasAbertas(
            $this->grupoAtivo->codOrgao,
            $this->grupoAtivo->codGrupo,
            $this->auth->getIdentity()->usu_org_max_superior
        );

        $this->view->codGrupo = $this->grupoAtivo->codGrupo;
    }

    public function visualizarAssinaturasAction()
    {
        $this->view->idUsuarioLogado = $this->cod_usuario;
        $documentoAssinatura = new Assinatura_Model_DbTable_TbAssinatura();
        $this->view->dados = $documentoAssinatura->obterProjetosAssinados(
            $this->auth->getIdentity()->usu_org_max_superior,
            $this->cod_usuario
        );

        $this->view->codGrupo = $this->grupoAtivo->codGrupo;
    }

    public function visualizarDocumentosAssinaturaAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam("idPronac");
        $idPronac = isset($idPronac) ? $idPronac : null;

        $this->view->idUsuarioLogado = $this->cod_usuario;

        $documentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $this->view->dados = $documentoAssinatura->obterDocumentosAssinadosPorProjeto(
            $idPronac
        );

        $this->view->codGrupo = $this->grupoAtivo->codGrupo;
    }

    protected function obterDocumentoAssinado($idDocumentoAssinatura)
    {
        try {
            try {
                if (!filter_input(INPUT_GET, 'idDocumentoAssinatura')) {
                    throw new Exception("Identificador do Documento &eacute; necess&aacute;rio para acessar essa funcionalidade.");
                }
            } catch (Exception $objException) {
                parent::message($objException->getMessage(), "/{$this->view->origin}/gerenciar-assinaturas");
            }

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $this->view->documentoAssinatura = $objModelDocumentoAssinatura->findBy(
                array('idDocumentoAssinatura' => $idDocumentoAssinatura)
            );


            $this->view->idDocumentoAssinatura = $idDocumentoAssinatura;
            $this->view->IdPRONAC = $this->view->documentoAssinatura['IdPRONAC'];
            $this->view->idTipoDoAtoAdministrativo = $this->view->documentoAssinatura['idTipoDoAtoAdministrativo'];

            $objTbProjetos = new Projeto_Model_DbTable_Projetos();
            $this->view->projeto = $objTbProjetos->findBy(array(
                'IdPRONAC' => $this->view->IdPRONAC
            ));

            $this->view->valoresProjeto = $objTbProjetos->obterValoresProjeto($this->view->IdPRONAC);
            $objAgentes = new Agente_Model_DbTable_Agentes();
            $dadosAgente = $objAgentes->buscarFornecedor(array(
                'a.CNPJCPF = ?' => $this->view->projeto['CgcCpf']
            ));

            $arrayDadosAgente = $dadosAgente->current();
            $this->view->nomeAgente = $arrayDadosAgente['nome'];

            $mapperArea = new Agente_Model_AreaMapper();
            $this->view->areaCultural = $mapperArea->findBy(array(
                'Codigo' => $this->view->projeto['Area']
            ));

            $objSegmentocultural = new Segmentocultural();
            $this->view->segmentoCultural = $objSegmentocultural->findBy(array(
                'Codigo' => $this->view->projeto['Segmento']
            ));

            $objPlanoDistribuicaoProduto = new Projeto_Model_vwPlanoDeDistribuicaoProduto();
            $this->view->dadosProducaoProjeto = $objPlanoDistribuicaoProduto->obterProducaoProjeto(array(
                'IdPronac = ?' => $this->view->IdPRONAC
            ));

            $objAssinatura = new Assinatura_Model_DbTable_TbAssinatura();
            $this->view->assinaturas = $objAssinatura->obterAssinaturas(
                $this->view->IdPRONAC,
                $this->view->idTipoDoAtoAdministrativo,
                $idDocumentoAssinatura
            );

//            $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
//            $this->view->quantidade_minima_assinaturas = $objTbAtoAdministrativo->obterQuantidadeMinimaAssinaturas(
//                $this->view->idTipoDoAtoAdministrativo,
//                $this->auth->getIdentity()->usu_org_max_superior
//            );

            $moduleAndControllerArray = explode('/', $this->view->origin);
            $this->view->moduleOrigin = $moduleAndControllerArray[0];
            $this->view->controllerOrigin = $moduleAndControllerArray[1];
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/index/visualizar-documento-assinado?idDocumentoAssinatura={$idDocumentoAssinatura}&origin={$this->view->origin}");
        }
    }

    public function visualizarProjetoAction()
    {
        $get = Zend_Registry::get('get');
        $idDocumentoAssinatura = $get->idDocumentoAssinatura;

        self::obterDocumentoAssinado($idDocumentoAssinatura);
    }

    public function visualizarDocumentoAssinadoAction()
    {
//        $this->_helper->layout->disableLayout();
//        $this->_helper->viewRenderer->setNoRender();

//        $layout = Zend_Layout::getMvcInstance();
//        $layout->disableLayout();
//        $layout->setLayout('layout_visualizar');

//        $config = Zend_Registry::get('config');
//        $config = new Zend_Config_Ini('/path/to/layout.ini', 'layout');
//        $layout = Zend_Layout::startMvc($config);

        Zend_Layout::startMvc(array('layout' => 'layout_visualizar'));
//        Zend_Layout::startMvc(array('layout'     => 'layout_visualizar'));
        # paginacao
//        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacaoMinc.phtml');

//        // Initialize view
//        $view         = new Zend_View();
//        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
//            'ViewRenderer'
//        );
//        $viewRenderer->setView($view);
//        $view->addHelperPath(
//            APPLICATION_PATH . '/../library/MinC/View/Helper/',
//            'MinC_View_Helper_'
//        );

        $get = Zend_Registry::get('get');
        $idDocumentoAssinatura = $get->idDocumentoAssinatura;

        self::obterDocumentoAssinado($idDocumentoAssinatura);
    }

    public function assinarProjetoAction()
    {
        $get = Zend_Registry::get('get');
        $idPronac = $get->IdPRONAC;
        $idTipoDoAtoAdministrativo = $get->idTipoDoAtoAdministrativo;

        try {
            if (!filter_input(INPUT_GET, 'IdPRONAC') && (is_array($get->IdPRONAC) && count($get->IdPRONAC) < 1)) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            if (!filter_input(INPUT_GET, 'idTipoDoAtoAdministrativo')) {
                throw new Exception("Identificador do tipo do ato administrativo &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->view->origin}/gerenciar-assinaturas");
        }


        try {
            $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();

            $this->view->perfilAssinante = $objTbAtoAdministrativo->obterPerfilAssinante(
                $this->grupoAtivo->codOrgao,
                $this->grupoAtivo->codGrupo,
                $idTipoDoAtoAdministrativo
            );

            if (!$this->view->perfilAssinante) {
                throw new Exception("Usu&aacute;rio sem autoriza&ccedil;&atilde;o para assinar o documento.");
            }

            if (is_array($get->IdPRONAC)) {
                $idPronacUnidos = implode(',', $get->IdPRONAC);
                $this->redirect("/{$this->moduleName}/index/assinar-projeto?IdPRONAC={$idPronacUnidos}&idTipoDoAtoAdministrativo={$idTipoDoAtoAdministrativo}&isMovimentarAssinatura={$get->isMovimentarAssinatura}&origin={$this->view->origin}");
            }

            $this->view->IdPRONAC = $get->IdPRONAC;
            $arrayIdPronacs = explode(',', $get->IdPRONAC);
            if (count($arrayIdPronacs) < 1) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            $post = $this->getRequest()->getPost();
            $objAssinatura = new MinC_Assinatura_Servico_Assinatura($post, $this->auth->getIdentity());
            $objAssinatura->isMovimentarProjetoPorOrdemAssinatura = false;

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $this->view->documentoAssinatura = $objModelDocumentoAssinatura->findBy(
                array(
                    'IdPRONAC' => $idPronac,
                    'idTipoDoAtoAdministrativo' => $idTipoDoAtoAdministrativo,
                    'cdSituacao' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                    'stEstado' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
                )
            );

            $idDocumentoAssinatura = $this->view->documentoAssinatura['idDocumentoAssinatura'];

            if ($post) {
                if ($get->isMovimentarAssinatura == 'true') {
                    $objAssinatura->isMovimentarProjetoPorOrdemAssinatura = true;
                }

                try {
                    $this->view->dsManifestacao = $post['dsManifestacao'];
                    foreach ($arrayIdPronacs as $idPronac) {
                        $documentoAssinatura = $objModelDocumentoAssinatura->findBy(
                            array(
                                'IdPRONAC' => $idPronac,
                                'idTipoDoAtoAdministrativo' => $idTipoDoAtoAdministrativo,
                                'cdSituacao' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                                'stEstado' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
                            )
                        );

                        $modelAssinatura = new MinC_Assinatura_Model_Assinatura();
                        $modelAssinatura->setCodGrupo($this->grupoAtivo->codGrupo)
                            ->setCodOrgao($this->grupoAtivo->codOrgao)
                            ->setIdPronac($idPronac)
                            ->setIdTipoDoAtoAdministrativo($idTipoDoAtoAdministrativo)
                            ->setIdDocumentoAssinatura($documentoAssinatura['idDocumentoAssinatura'])
                            ->setDsManifestacao($post['dsManifestacao'])
                            ->setIdOrgaoSuperiorDoAssinante($this->auth->getIdentity()->usu_org_max_superior);
                        $objAssinatura->assinarProjeto($modelAssinatura);
                    }

                    if (count($arrayIdPronacs) > 1) {
                        parent::message(
                            "Projetos assinados com sucesso!",
                            "/{$this->view->origin}/gerenciar-assinaturas",
                            'CONFIRM'
                        );
                    } else {
                        parent::message(
                            "Projeto assinado com sucesso!",
                            "/{$this->moduleName}/index/visualizar-projeto?idDocumentoAssinatura={$idDocumentoAssinatura}&isMovimentarAssinatura={$get->isMovimentarAssinatura}&origin={$this->view->origin}",
                            'CONFIRM'
                        );
                        die;
                    }
                } catch (Exception $objException) {
                    parent::message(
                        $objException->getMessage(),
                        "/{$this->moduleName}/index/assinar-projeto?IdPRONAC={$idPronac}&idTipoDoAtoAdministrativo={$idTipoDoAtoAdministrativo}&isMovimentarAssinatura={$get->isMovimentarAssinatura}&origin={$this->view->origin}",
                        'ERROR'
                    );
                }
            }



            $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $dadosAtoAdministrativoAtual = $objTbAtoAdministrativo->obterAtoAdministrativoAtual(
                $idTipoDoAtoAdministrativo,
                $this->grupoAtivo->codGrupo,
                $this->grupoAtivo->codOrgao
            );

            $objTbAssinatura = new Assinatura_Model_DbTable_TbAssinatura();
            $assinaturaExistente = $objTbAssinatura->buscar(array(
                'idPronac = ?' => $idPronac,
                'idAtoAdministrativo = ?' => $dadosAtoAdministrativoAtual['idAtoAdministrativo'],
                'idAssinante = ?' => $this->auth->getIdentity()->usu_codigo,
                'idDocumentoAssinatura = ?' => $idDocumentoAssinatura
            ));

            if ($assinaturaExistente->current()) {
                throw new Exception("O documento j&aacute; foi assinado pelo usu&aacute;rio logado nesta fase atual.");
            }

            $objProjeto = new Projeto_Model_DbTable_Projetos();
            $this->view->projeto = array();
            foreach ($arrayIdPronacs as $idPronac) {
                $this->view->projeto[] = $objProjeto->findBy(array(
                    'IdPRONAC' => $idPronac
                ));
            }

            $objVerificacao = new Verificacao();
            $this->view->tipoDocumento = $objVerificacao->findBy(array(
                'idVerificacao = ?' => $idTipoDoAtoAdministrativo
            ));

            $this->view->templateAutenticacao = $objAssinatura->obterServicoAutenticacao()->obterMetodoAutenticacao()->obterTemplateAutenticacao();
            $this->view->idTipoDoAtoAdministrativo = $get->idTipoDoAtoAdministrativo;
            $this->view->isMovimentarAssinatura = false;

            if ($get->isMovimentarAssinatura == 'true') {
                $this->view->isMovimentarAssinatura = 'true';
            }

            $moduleAndControllerArray = explode('/', $this->view->origin);
            $this->view->moduleOrigin = $moduleAndControllerArray[0];
            $this->view->controllerOrigin = $moduleAndControllerArray[1];
        } catch (Exception $objException) {
            parent::message(
                $objException->getMessage(),
                "/{$this->view->origin}/gerenciar-assinaturas"
            );
        }
    }

    public function movimentarProjetoAction()
    {
        try {
            $get = Zend_Registry::get('get');

            if (!filter_input(INPUT_GET, 'IdPRONAC')) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            if (!filter_input(INPUT_GET, 'idTipoDoAtoAdministrativo')) {
                throw new Exception("Identificador do tipo do ato administrativo &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }
            $modelAssinatura = new MinC_Assinatura_Model_Assinatura();
            $modelAssinatura->setIdPronac($get->IdPRONAC);
            $modelAssinatura->setIdTipoDoAtoAdministrativo($get->idTipoDoAtoAdministrativo);

            $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $dadosAtoAdministrativoAtual = $objTbAtoAdministrativo->obterAtoAdministrativoAtual(
                $modelAssinatura->getIdTipoDoAtoAdministrativo(),
                $this->grupoAtivo->codGrupo,
                $this->grupoAtivo->codOrgao
            );

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $dadosDocumentoAssinatura = $objModelDocumentoAssinatura->findBy(
                array(
                    'IdPRONAC' => $modelAssinatura->getIdPronac(),
                    'idTipoDoAtoAdministrativo' => $modelAssinatura->getIdTipoDoAtoAdministrativo(),
                    'cdSituacao' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                    'stEstado' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
                )
            );

            $servicoAssinatura = new MinC_Assinatura_Servico_Assinatura();

            $modelAssinatura->setIdOrdemDaAssinatura($dadosAtoAdministrativoAtual['idOrdemDaAssinatura']);
            $modelAssinatura->setIdAtoAdministrativo($dadosAtoAdministrativoAtual['idAtoAdministrativo']);
            $modelAssinatura->setIdAssinante($this->auth->getIdentity()->usu_codigo);
            $modelAssinatura->setIdDocumentoAssinatura($dadosDocumentoAssinatura['idDocumentoAssinatura']);
            $modelAssinatura->setIdOrgaoSuperiorDoAssinante($this->auth->getIdentity()->usu_org_max_superior);
            $servicoAssinatura->movimentarProjetoAssinadoPorOrdemDeAssinatura($modelAssinatura);

            parent::message(
                'Projeto Movimentado com sucesso!',
                "/{$this->view->origin}/gerenciar-assinaturas",
                'CONFIRM'
            );
        } catch (Exception $objException) {
            parent::message(
                $objException->getMessage(),
                "/{$this->view->origin}/gerenciar-assinaturas"
            );
        }
    }

    public function gerarPdfAction()
    {
        ini_set("memory_limit", "5000M");
        set_time_limit(30);

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $cssContents = file_get_contents(APPLICATION_PATH . '/../public/library/materialize/css/materialize.css');
        $cssContents .= file_get_contents(APPLICATION_PATH . '/../public/library/materialize/css/materialize-custom.css');
        $html = $_POST['html'];

        $pdf = new mPDF('pt', 'A4', 12, '', 8, 8, 5, 14, 9, 9, 'P');
        $pdf->allow_charset_conversion = true;
        $pdf->WriteHTML($cssContents, 1);
        $pdf->charset_in = 'ISO-8859-1';

        if (!mb_check_encoding($html, 'ISO-8859-1')) {
            $pdf->charset_in = 'UTF-8';
        }

        $pdf->WriteHTML($html, 2);
        $pdf->Output();
        die;
    }
}
