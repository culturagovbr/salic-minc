<?php

abstract class Solicitacao_GenericController extends MinC_Controller_Action_Abstract
{
    protected $proposta = null;

    protected $projeto = null;

    protected $idPreProjeto = null;

    protected $idPronac = null;

    protected $idUsuario = null;

    protected $idAgente = null;

    protected $usuario = null;

    protected $grupoAtivo = null;

    protected $cpfLogado = null;

    protected $isProponente = false;

    public function init()
    {
        parent::init();

        $auth = Zend_Auth::getInstance();
        $PermissoesGrupo = array();

        //Da permissao de acesso a todos os grupos do usuario logado afim de atender o UC75
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_Usuario();
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }

            if (empty($this->grupoAtivo)) {
                $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
            }
        }

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        $arrAuth = array_change_key_case((array)$auth->getIdentity());

        $this->idPreProjeto = $this->getRequest()->getParam('idPreProjeto');
        $this->view->idPreProjeto = $this->idPreProjeto;

        $this->idPronac = $this->getRequest()->getParam('idPronac');
        if (!empty($this->idPronac)) {
            $tbProjetos = new Projeto_Model_DbTable_Projetos();
            $this->projeto = $tbProjetos->buscar(array('IdPRONAC = ?' => $this->idPronac))->current();
            $this->view->projeto = $this->projeto;
            $this->view->idPronac = $this->idPronac;
        }

        if (!empty($this->idPreProjeto)) {
            $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
            $this->proposta = $tblPreProjeto->buscar(array('idPreProjeto = ?' => $this->idPreProjeto))->current();
            $this->view->proposta = $this->proposta;
        }

        $this->idUsuario = !empty($arrAuth['usu_codigo']) ? $arrAuth['usu_codigo'] : $arrAuth['idusuario'];
        $this->cpfLogado = isset($arrAuth['usu_identificacao']) ? $arrAuth['usu_identificacao'] : $arrAuth['cpf'];

        $this->view->arrayMenu =  self::gerarArrayMenu();
        if($this->idPreProjeto) {
            $this->view->arrayMenu = self::gerarArrayMenuProposta($this->idPreProjeto);
        }

        if ($arrAuth['cpf']) {
            /**
             * Agentes sao proponentes da proposta ou do projeto
             */

            $tblAgentes = new Agente_Model_DbTable_Agentes();
            $agente = $tblAgentes->findBy(array('cnpjcpf' => $arrAuth['cpf']));

            if ($agente) {
                $this->idAgente = $agente['idAgente'];
            }

            $this->isProponente = true;
        }

        $this->usuario = $arrAuth;
        $this->view->usuario = $auth->getIdentity(); //@todo padronizar o usuario no header do layout
        $this->view->isProponente = $this->isProponente;
        $this->view->idUsuario = $this->idUsuario;
    }

//    private function gerarArrayMenuProjeto($idPronac)
//    {
//        $arrMenu = self::gerarArrayMenu();
//
//        $arrMenu['exibirprojeto'] = [
//            'label' => 'Exibir projeto',
//            'title' => '',
//            'link' => [
//                'module' => 'default',
//                'controller' => 'consultardadosprojeto',
//                'action' => 'index',
//                'idPronac' => Seguranca::encrypt($idPronac)
//            ],
//            'menu' => [],
//            'grupo' => []
//        ];
//
//        $arrMenu['solicitacoes']['menu'][] = [
//            'label' => 'Deste projeto',
//            'title' => '',
//            'link' => [
//                'module' => 'solicitacao',
//                'controller' => 'mensagem',
//                'action' => 'index',
//                'idPronac' => $idPronac
//            ],
//            'grupo' => []
//        ];
//
//        return $arrMenu;
//    }


    private function gerarArrayMenuProposta($idPreProjeto)
    {
        $arrMenu = self::gerarArrayMenu();

        $arrMenu['exibirproposta'] = [
            'label' => 'Exibir proposta',
            'title' => '',
            'link' => [
                'module' => 'admissibilidade',
                'controller' => 'admissibilidade',
                'action' => 'exibirpropostacultural',
                'idPreProjeto' => $idPreProjeto
            ],
            'menu' => [],
            'grupo' => []
        ];

//        $arrMenu['solicitacoes']['menu'][] = [
//            'label' => 'Desta proposta',
//            'title' => '',
//            'link' => [
//                'module' => 'solicitacao',
//                'controller' => 'mensagem',
//                'action' => 'index',
//                'idPreProjeto' => $idPreProjeto
//            ],
//            'grupo' => []
//        ];

        return $arrMenu;
    }

    private function gerarArrayMenu()
    {

        $arrMenu = [];
        $arrMenu['solicitacoes'] = [
            'id' => 'solicitacoes',
            'label' => 'Solicita&ccedil;&otilde;es',
            'title' => '',
            'link' => '',
            'menu' => [],
            'grupo' => []
        ];

        $arrMenu['solicitacoes']['menu'][] = [
            'label' => 'Todas',
            'title' => '',
            'link' => [
                'module' => 'solicitacao',
                'controller' => 'mensagem',
                'action' => 'index'
            ],
            'grupo' => []
        ];

        return $arrMenu;
    }
}

