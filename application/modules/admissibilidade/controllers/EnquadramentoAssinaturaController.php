<?php

class Admissibilidade_EnquadramentoAssinaturaController extends Assinatura_GenericController
{
    private $idTipoDoAtoAdministrativo;
    private $grupoAtivo;

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
        $documentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();

        $this->view->dados = $documentoAssinatura->obterProjetosComAssinaturasAbertas(
            $this->grupoAtivo->codOrgao,
            $this->grupoAtivo->codGrupo,
            $this->auth->getIdentity()->usu_org_max_superior,
            $this->idTipoDoAtoAdministrativo
        );

        $this->view->codGrupo = $this->grupoAtivo->codGrupo;

        $objTbAtoAdministrativo = new Assinatura_Model_DbTable_TbAtoAdministrativo();
        $this->view->quantidade_minima_assinaturas = $objTbAtoAdministrativo->obterQuantidadeMinimaAssinaturas(
            $this->idTipoDoAtoAdministrativo,
            $this->auth->getIdentity()->usu_org_max_superior
        );
        $this->view->idTipoDoAtoAdministrativo = $this->idTipoDoAtoAdministrativo;
    }

    public function devolverProjetoAction()
    {
        $this->validarPerfis();
        $get = Zend_Registry::get('get');
        try {
            if (!filter_input(INPUT_GET, 'IdPRONAC')) {
                throw new Exception("Identificador do projeto é necessário para acessar essa funcionalidade.");
            }

            $objTbProjetos = new Projeto_Model_DbTable_Projetos();

            $this->view->projeto = $objTbProjetos->findBy(array(
                'IdPRONAC' => $get->IdPRONAC
            ));

            $post = $this->getRequest()->getPost();
            if ($post) {
                if (!$post['motivoDevolucao']) {
                    throw new Exception("Campo 'Motivação da Devolução para nova avaliação' não informado.");
                }

                $assinaturaService = new \MinC\Assinatura\Servico\Assinatura(
                    [
                        'Despacho' => $post['motivoDevolucao'],
                        'idTipoDoAto' => $this->idTipoDoAtoAdministrativo,
                        'idPronac' => $get->IdPRONAC,
                        'idPerfilDoAssinante' => $this->grupoAtivo->codGrupo
                    ]
                );
                $assinaturaService->devolver();

                parent::message('Projeto devolvido com sucesso.', "/{$this->moduleName}/enquadramento-assinatura/gerenciar-assinaturas", 'CONFIRM');
            }

            $objModelDocumentoAssinatura = new Assinatura_Model_DbTable_TbDocumentoAssinatura();
            $this->view->abertoParaDevolucao = $objModelDocumentoAssinatura->isProjetoDisponivelParaAssinatura(
                $get->IdPRONAC,
                $this->idTipoDoAtoAdministrativo
            );

            $this->view->IdPRONAC = $get->IdPRONAC;

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
            parent::message($objException->getMessage(), "/{$this->moduleName}/enquadramento-assinatura/devolver-projeto?IdPRONAC={$get->IdPRONAC}");
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
