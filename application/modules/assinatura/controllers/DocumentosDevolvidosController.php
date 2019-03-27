<?php

class Assinatura_DocumentosDevolvidosController extends Assinatura_GenericController
{
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


    public function listarAction()
    {
        $this->view->idUsuarioLogado = $this->auth->getIdentity()->usu_codigo;
        $this->view->dados = [];
        $this->view->codGrupo = $this->grupoAtivo->codGrupo;
    }

    public function listarAjaxAction()
    {
        $start = $this->getRequest()->getParam('start', -1);
        $length = $this->getRequest()->getParam('length', 100);
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search', '');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');

        $order = (!empty($order[0]['dir'])) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : ["idDocumentoAssinatura desc"];

        $where = [];
        $where["TbAtoAdministrativo.idOrgaoDoAssinante = ?"] = $this->grupoAtivo->codOrgao;
        $where["TbAtoAdministrativo.idPerfilDoAssinante = ?"] = $this->grupoAtivo->codGrupo;
        $where["TbAtoAdministrativo.idOrgaoSuperiorDoAssinante = ?"] = $this->auth->getIdentity()->usu_org_max_superior;

        if (!empty($search['value'])) {
            $search['value'] = utf8_decode($search['value']);
            $search['value'] = str_replace('\\', '', $search['value']);
        }

        $tbMotivoDevolucao = new Assinatura_Model_DbTable_TbMotivoDevolucao();
        $projetosDisponiveis = $tbMotivoDevolucao->obterDocumentosDevolvidos($where, $order, $start, $length, $search['value'])->toArray();

        $recordsTotal = $tbMotivoDevolucao->obterTotalDocumentosDevolvidos($where);

        $recordsFiltered = $recordsTotal;
        if (!empty($search['value'])) {
            $recordsFiltered = count($projetosDisponiveis);
        }

        $projetos = [];
        if (count($projetosDisponiveis) > 0) {
            $projetos = $projetosDisponiveis;
            array_walk($projetos, function (&$value) {
                $value = array_map('utf8_encode', $value);
            });
        }

        $this->_helper->json([
            "data" => $projetos,
            'recordsTotal' => $recordsTotal,
            'draw' => $draw,
            'recordsFiltered' => $recordsFiltered,
        ]);
    }

    public function visualizarAjaxAction()
    {
        $this->_helper->layout->disableLayout();
        $idPronac = $this->_request->getParam('idPronac');
        $idDocumentoAssinatura = $this->_request->getParam('idDocumentoAssinatura');

        $projetos = new Projetos();
        $projeto = $projetos->buscarProjetoXProponente(array('idPronac = ?' => $idPronac))->current();
        $this->view->projeto = $projeto;

        $where = [];
        $where["TbAtoAdministrativo.idOrgaoDoAssinante = ?"] = $this->grupoAtivo->codOrgao;
        $where["TbAtoAdministrativo.idPerfilDoAssinante = ?"] = $this->grupoAtivo->codGrupo;
        $where["TbAtoAdministrativo.idOrgaoSuperiorDoAssinante = ?"] = $this->auth->getIdentity()->usu_org_max_superior;
        $where["TbDocumentoAssinatura.idPronac = ?"] = $idPronac;

        if (!empty($idDocumentoAssinatura)) {
            $where["TbDocumentoAssinatura.idDocumentoAssinatura = ?"] = $idDocumentoAssinatura;
        }

        $tbMotivoDevolucao = new Assinatura_Model_DbTable_TbMotivoDevolucao();
        $this->view->documentos = $tbMotivoDevolucao->obterDocumentosDevolvidos($where, ['TbDocumentoAssinatura.idDocumentoAssinatura DESC'])->toArray();
    }
}
