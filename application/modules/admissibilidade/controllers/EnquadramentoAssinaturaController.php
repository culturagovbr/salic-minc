<?php

class Admissibilidade_EnquadramentoAssinaturaController extends Assinatura_GenericController
{
    private $idTipoDoAtoAdministrativo;
    private $grupoAtivo;
    private $codOrgaoSuperior;

    private function validarPerfis()
    {
        $auth = Zend_Auth::getInstance();

        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 147;
        $PermissoesGrupo[] = 148;
        $PermissoesGrupo[] = 149;
        $PermissoesGrupo[] = 150;
        $PermissoesGrupo[] = 151;
        $PermissoesGrupo[] = 152;

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }

    public function init()
    {
        parent::init();

        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->idTipoDoAtoAdministrativo = Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_ENQUADRAMENTO;

        $orgao = new Orgaos();
        $dadosOrgao = $orgao->obterOrgaoSuperior($this->grupoAtivo->codOrgao);
        $this->codOrgaoSuperior = $dadosOrgao['Codigo'];
    }

    public function indexAction()
    {
        $this->validarPerfis();
        $this->redirect("/{$this->moduleName}/enquadramento-assinatura/gerenciar-assinaturas");
    }

    public function gerenciarAssinaturasAction()
    {
        $this->validarPerfis();
        $this->view->idUsuarioLogado = $this->auth->getIdentity()->usu_codigo;


//        $start = $this->getRequest()->getParam('start');
//        $length = $this->getRequest()->getParam('length');
//        $draw = (int)$this->getRequest()->getParam('draw');
//        $search = $this->getRequest()->getParam('search');
//        $order = $this->getRequest()->getParam('order');
//        $columns = $this->getRequest()->getParam('columns');
//
//        $get = Zend_Registry::get('get');

        $tbAssinaturaDbTable = new Assinatura_Model_DbTable_TbAssinatura(
//            [
//            'search' => $search,
//            'start' => $start,
//            'length' => $length,
//            'order' => $order,
//            'columns' => $columns
//            ]
        );

        $tbAssinaturaDbTable->preencherModeloAtoAdministrativo([
            'idOrgaoDoAssinante' => $this->grupoAtivo->codOrgao,
            'idPerfilDoAssinante' => $this->grupoAtivo->codGrupo,
            'idOrgaoSuperiorDoAssinante' => $this->auth->getIdentity()->usu_org_max_superior,
            'idTipoDoAto' => $this->idTipoDoAtoAdministrativo
        ]);

        $this->view->dados = $tbAssinaturaDbTable->obterAssinaturasDisponiveis();

//        $documentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();

//        $this->view->dados = $documentoAssinatura->obterProjetosComAssinaturasAbertas(
//            $this->grupoAtivo->codOrgao,
//            $this->grupoAtivo->codGrupo,
//            $this->codOrgaoSuperior,
//            $this->idTipoDoAtoAdministrativo
//        );

        $this->view->codGrupo = $this->grupoAtivo->codGrupo;

        $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $this->view->quantidade_minima_assinaturas = $objTbAtoAdministrativo->obterQuantidadeMinimaAssinaturas(
            $this->idTipoDoAtoAdministrativo,
            $this->codOrgaoSuperior
        );
        $this->view->idTipoDoAtoAdministrativo = $this->idTipoDoAtoAdministrativo;
    }

    public function devolverProjetoAction()
    {
        $this->validarPerfis();
        $get = $this->_request->getParams();
        try {
            if (empty($get['IdPRONAC'])) {
                throw new Exception("Identificador do projeto &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            if (empty($get['idDocumentoAssinatura'])) {
                throw new Exception("Identificador do documento &eacute; necess&aacute;rio para acessar essa funcionalidade.");
            }

            $objTbProjetos = new Projeto_Model_DbTable_Projetos();

            $this->view->projeto = $objTbProjetos->findBy(array(
                'IdPRONAC' => $get['IdPRONAC']
            ));

            $post = $this->getRequest()->getPost();
            if ($post) {
                if (!$post['motivoDevolucao']) {
                    throw new Exception("Campo 'Motiva&ccedil;&atilde;o da Devolu&ccedil;&atilde;o para nova avalia&ccedil;&atilde;o' n&atilde;o informado.");
                }

                $assinaturaService = new \MinC\Assinatura\Servico\Assinatura(
                    [
                        'dsMotivoDevolucao' => $post['motivoDevolucao'],
                        'idDocumentoAssinatura' => $post['idDocumentoAssinatura'],
                        'idTipoDoAto' => $this->idTipoDoAtoAdministrativo,
                        'idPronac' => $get['IdPRONAC'],
                        'idPerfilDoAssinante' => $this->grupoAtivo->codGrupo
                    ]
                );
                $assinaturaService->devolver();

                parent::message('Projeto devolvido com sucesso.', "/{$this->moduleName}/enquadramento-assinatura/gerenciar-assinaturas", 'CONFIRM');
            }

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $this->view->abertoParaDevolucao = $objModelDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
                $get['IdPRONAC'],
                $this->idTipoDoAtoAdministrativo
            );

            $this->view->IdPRONAC = $get['IdPRONAC'];
            $this->view->idDocumentoAssinatura = $get['idDocumentoAssinatura'];

            $mapperArea = new Agente_Model_AreaMapper();
            $this->view->areaCultural = $mapperArea->findBy(array(
                'Codigo' => $this->view->projeto['Area']
            ));

            $objSegmentocultural = new Segmentocultural();
            $this->view->segmentoCultural = $objSegmentocultural->findBy(array(
                'Codigo' => $this->view->projeto['Segmento']
            ));

            $objEnquadramento = new Admissibilidade_Model_Enquadramento();
            $arrayPesquisa = array(
                'AnoProjeto' => $this->view->projeto['AnoProjeto'],
                'Sequencial' => $this->view->projeto['Sequencial'],
                'IdPRONAC' => $this->view->projeto['IdPRONAC']
            );
            $this->view->dadosEnquadramento = $objEnquadramento->findBy($arrayPesquisa);

            $this->view->titulo = "Devolver";
        } catch (Exception $objException) {
            parent::message(
                $objException->getMessage(),
                "/{$this->moduleName}/enquadramento-assinatura/devolver-projeto/IdPRONAC/{$get['IdPRONAC']}/idDocumentoAssinatura/{$get['idDocumentoAssinatura']}"
            );
        }
    }

    public function finalizarAssinaturaAction()
    {
        $this->validarPerfis();
        $get = Zend_Registry::get('get');
        try {
            if (!filter_input(INPUT_GET, 'IdPRONAC')) {
                throw new Exception("Identificador do projeto é necessário para acessar essa funcionalidade.");
            }

            $assinaturaService = new \MinC\Assinatura\Servico\Assinatura(
                [
                    'idTipoDoAto' => $this->idTipoDoAtoAdministrativo,
                    'idPronac' => $get->IdPRONAC,
                    'idPerfilDoAssinante' => $this->grupoAtivo->codGrupo
                ]
            );
            $assinaturaService->finalizar();

            parent::message('Projeto finalizado com sucesso!', "/{$this->moduleName}/enquadramento-assinatura/gerenciar-assinaturas", 'CONFIRM');
        } catch (Exception $objException) {
            parent::message($objException->getMessage(), "/{$this->moduleName}/enquadramento-assinatura/finalizar-assinatura?IdPRONAC={$get->IdPRONAC}");
        }
    }
}
