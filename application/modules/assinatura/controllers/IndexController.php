<?php

class Assinatura_IndexController extends Assinatura_GenericController
{
    private $idTipoDoAtoAdministrativo;
    private $grupoAtivo;
    private $cod_usuario;
    public $moduloDeOrigem;

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
        $get = Zend_Registry::get('get');
        $post = (object)$this->getRequest()->getPost();
        $this->view->origin = "{$this->moduleName}/index";
        if (!empty($get->origin) || !empty($post->origin)) {
            $this->view->origin = (!empty($post->origin)) ? $post->origin : $get->origin;
        }
        $this->moduloDeOrigem = $this->view->origin;
    }

    public function indexAction()
    {
        $this->redirect("/{$this->moduleName}/index/gerenciar-assinaturas");
    }

    public function gerenciarAssinaturasAction()
    {
        $this->view->idUsuarioLogado = $this->auth->getIdentity()->usu_codigo;
        $this->view->dados = [];
        $this->view->codGrupo = $this->grupoAtivo->codGrupo;
    }

    public function gerenciarAssinaturaAjaxAction()
    {
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');
//
//        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : ["Pronac desc"];

        $get = Zend_Registry::get('get');
        $idTipoDoAtoAdministrativo = $get->idTipoDoAtoAdministrativo;
        $idTipoDoAtoAdministrativos = [];

        $stringIdTipoDoAtoAdministrativos = $get->idTipoDoAtoAdministrativos;
        if (!is_null($stringIdTipoDoAtoAdministrativos) || !empty($stringIdTipoDoAtoAdministrativos)) {
            array_push($idTipoDoAtoAdministrativos, explode(',', $stringIdTipoDoAtoAdministrativos));
        }

        if (!is_null($idTipoDoAtoAdministrativo) || !empty($idTipoDoAtoAdministrativo)) {
            $idTipoDoAtoAdministrativos[] = $idTipoDoAtoAdministrativo;
        }

        $tbAssinaturaDbTable = new Assinatura_Model_DbTable_TbAssinatura([
            'search' => $search,
            'start' => $start,
            'length' => $length,
            'order' => $order,
            'columns' => $columns
        ]);

        $tbAssinaturaDbTable->preencherModeloAtoAdministrativo([
            'idOrgaoDoAssinante' => $this->grupoAtivo->codOrgao,
            'idPerfilDoAssinante' => $this->grupoAtivo->codGrupo,
            'idOrgaoSuperiorDoAssinante' => $this->auth->getIdentity()->usu_org_max_superior,
            'idTipoDoAto' => $idTipoDoAtoAdministrativos
        ]);

        $projetosDisponiveis = $tbAssinaturaDbTable->obterAssinaturasDisponiveis();
        $recordsFiltered = 0;
        $recordsTotal = 0;
        $projetos = 0;

        if (count($projetosDisponiveis) > 0) {
            $projetos = $projetosDisponiveis;
            array_walk($projetos, function (&$value) {
                $value = array_map('utf8_encode', $value);
            });
            $recordsTotal = count($projetos);
        }

        $this->_helper->json([
            "data" => $projetos,
            'recordsTotal' => $recordsTotal,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered,
        ]);
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

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

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

            $moduleAndControllerArray = explode('/', $this->view->origin);
            $this->view->moduleOrigin = $moduleAndControllerArray[0];
            $this->view->controllerOrigin = $moduleAndControllerArray[1];
            $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $perfilAssinanteAtoAdministrativo = $objTbAtoAdministrativo->obterPerfilAssinante(
                $this->grupoAtivo->codOrgao,
                $this->grupoAtivo->codGrupo,
                $this->view->documentoAssinatura['idTipoDoAtoAdministrativo']
            );

            $this->view->isPermitidoAssinar = false;
            if (count($perfilAssinanteAtoAdministrativo) > 0) {
                $objAssinatura->preencherModeloAssinatura([
                    'idPronac' => $this->view->IdPRONAC,
                    'idAtoAdministrativo' => $perfilAssinanteAtoAdministrativo['idAtoAdministrativo'],
                    'idDocumentoAssinatura' => $idDocumentoAssinatura,
                ]);

                if (!$objAssinatura->isProjetoAssinado()
                    && (int)$this->view->documentoAssinatura['cdSituacao'] == (int)Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO) {
                    $this->view->isPermitidoAssinar = true;
                }
            }

        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/index/visualizar-documento-assinado?idDocumentoAssinatura={$idDocumentoAssinatura}&origin={$this->view->origin}");
        }
    }

    public function visualizarProjetoAction()
    {
        $get = Zend_Registry::get('get');
        if (filter_input(INPUT_GET, 'modal')) {
            $this->_helper->layout->disableLayout();
        }
        $idDocumentoAssinatura = $get->idDocumentoAssinatura;

        $this->obterDocumentoAssinado($idDocumentoAssinatura);
    }

    public function visualizarDocumentoAssinadoAction()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_visualizar'));

        $get = Zend_Registry::get('get');
        $idDocumentoAssinatura = $get->idDocumentoAssinatura;

        $this->obterDocumentoAssinado($idDocumentoAssinatura);
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
                $this->redirect("/{$this->moduleName}/index/assinar-projeto?IdPRONAC={$idPronacUnidos}&idTipoDoAtoAdministrativo={$idTipoDoAtoAdministrativo}&origin={$this->view->origin}");
            }

            $this->view->IdPRONAC = $get->IdPRONAC;
            $arrayIdPronacs = explode(',', $get->IdPRONAC);
            if (count($arrayIdPronacs) < 1) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            $post = $this->getRequest()->getPost();


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

            $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $dadosAtoAdministrativoAtual = $objTbAtoAdministrativo->obterAtoAdministrativoAtual(
                $idTipoDoAtoAdministrativo,
                $this->grupoAtivo->codGrupo,
                $this->grupoAtivo->codOrgao
            );
            
            if ($post) {

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
                        $tblUsuario = new Autenticacao_Model_DbTable_Usuario();
                        $codOrgaoMaxSuperior = $tblUsuario->recuperarOrgaoMaxSuperior($this->grupoAtivo->codOrgao);
                        
                        $servicoAssinatura = new \MinC\Assinatura\Servico\Assinatura(
                            [
                                'idPronac' => $idPronac,
                                'idAtoAdministrativo' => $dadosAtoAdministrativoAtual['idAtoAdministrativo'],
                                'idAssinante' => $this->auth->getIdentity()->usu_codigo,
                                'dsManifestacao' => $post['dsManifestacao'],
                                'idDocumentoAssinatura' => $documentoAssinatura['idDocumentoAssinatura'],
                                'idTipoDoAto' => $idTipoDoAtoAdministrativo,
                                'idOrgaoDoAssinante' => $this->grupoAtivo->codOrgao,
                                'idPerfilDoAssinante' => $this->grupoAtivo->codGrupo,
                                'idOrgaoSuperiorDoAssinante' => $codOrgaoMaxSuperior
                            ]
                        );

                        $servicoAssinatura->viewModelAssinatura->request = $this->getRequest();
                        $servicoAssinatura->viewModelAssinatura->response = $this->getResponse();
                        $servicoAssinatura->assinarProjeto(
                            $post,
                            $this->auth->getIdentity()
                        );
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
                            "/{$this->moduleName}/index/visualizar-projeto?idDocumentoAssinatura={$idDocumentoAssinatura}&origin={$this->view->origin}",
                            'CONFIRM'
                        );
                    }
                    die;
                } catch (Exception $objException) {
                    parent::message(
                        $objException->getMessage(),
                        "/{$this->moduleName}/index/assinar-projeto?IdPRONAC={$idPronac}&idTipoDoAtoAdministrativo={$idTipoDoAtoAdministrativo}&origin={$this->view->origin}",
                        'ERROR'
                    );
                }
            }


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

            $servicoAutenticacao = new \MinC\Assinatura\Servico\Autenticacao(
                $post,
                $this->auth->getIdentity()
            );

            $this->view->templateAutenticacao = $servicoAutenticacao->obterMetodoAutenticacao()->obterTemplateAutenticacao();
            $this->view->idTipoDoAtoAdministrativo = $get->idTipoDoAtoAdministrativo;

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

            $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
            $dadosAtoAdministrativoAtual = $objTbAtoAdministrativo->obterAtoAdministrativoAtual(
                $get->idTipoDoAtoAdministrativo,
                $this->grupoAtivo->codGrupo,
                $this->grupoAtivo->codOrgao
            );

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $dadosDocumentoAssinatura = $objModelDocumentoAssinatura->findBy(
                array(
                    'IdPRONAC' => $get->IdPRONAC,
                    'idTipoDoAtoAdministrativo' => $get->idTipoDoAtoAdministrativo,
                    'cdSituacao' => Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                    'stEstado' => Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
                )
            );

            $servicoAssinatura = new \MinC\Assinatura\Servico\Assinatura(
                [
                    'idAtoAdministrativo' => $dadosAtoAdministrativoAtual['idAtoAdministrativo'],
                    'idTipoDoAtoAdministrativo' => $get->idTipoDoAtoAdministrativo,
                    'idTipoDoAto' => $get->idTipoDoAtoAdministrativo,
                    'idOrdemDaAssinatura' => $dadosAtoAdministrativoAtual['idOrdemDaAssinatura'],
                    'idOrgaoSuperiorDoAssinante' => $this->auth->getIdentity()->usu_org_max_superior,
                    'idPronac' => $get->IdPRONAC,
                    'idAssinante' => $this->auth->getIdentity()->usu_codigo,
                    'idDocumentoAssinatura' => $dadosDocumentoAssinatura['idDocumentoAssinatura'],
                    'idPerfilDoAssinante' => $this->grupoAtivo->codGrupo
                ]
            );

            $servicoAssinatura->encaminhar();

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
}
