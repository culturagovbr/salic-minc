<?php

/**
 * @name Admissibilidade_MensagemController
 * @package Modules/admissibilidade
 * @subpackage Controller
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 07/12/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Admissibilidade_MensagemController extends MinC_Controller_Action_Abstract
{
    private $idPreProjeto = null;
    protected $idUsuario = null;
    private $intTamPag = 50;
    private $codOrgaoSuperior = null;
    private $codGrupo = null;
    private $codOrgao = null;
    private $COD_CLASSIFICACAO_DOCUMENTO = 23;
    private $arrProjeto = array();
    private $arrBreadCrumb = array();

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autenticacao

        // verifica as permissoes
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 90;  // Protocolo - Documento
        $PermissoesGrupo[] = 91;  // Protocolo - Recebimento
        $PermissoesGrupo[] = 92;  // Tecnico de Admissibilidade
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 95;  // Consulta
        $PermissoesGrupo[] = 96;  // Consulta Gerencial
        $PermissoesGrupo[] = 97;  // Gestor do SALIC
        $PermissoesGrupo[] = 99;  // Acompanhamento
        $PermissoesGrupo[] = 100; // Prestacao de Contas
        $PermissoesGrupo[] = 103; // Coordenador de Analise
        $PermissoesGrupo[] = 104; // Protocolo - Envio / Recebimento
        $PermissoesGrupo[] = 110; // Tecnico de Analise
        $PermissoesGrupo[] = 113; // Coordenador de Arquivo
        $PermissoesGrupo[] = 114; // Coordenador de Editais
        $PermissoesGrupo[] = 115; // Atendimento Representacoes
        $PermissoesGrupo[] = 119; // Presidente da CNIC
        $PermissoesGrupo[] = 120; // Coordenador CNIC
        $PermissoesGrupo[] = 121; // Tecnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento
        $PermissoesGrupo[] = 124; // Tecnico de Prestacao de Contas
        $PermissoesGrupo[] = 125; // Coordenador de Prestacao de Contas
        $PermissoesGrupo[] = 127; // Coordenador de Atendimento
        $PermissoesGrupo[] = 128; // Tecnico de Portaria
        $PermissoesGrupo[] = 131; // Coordenador de Admissibilidade
        $PermissoesGrupo[] = 133; // Membros Natos da CNIC
        $PermissoesGrupo[] = 134; // Coordenador de Fiscalizacao
        $PermissoesGrupo[] = 135; // Tecnico de Fiscalizacao
        $PermissoesGrupo[] = 136; // Coordenador de Entidade Vinculada
        $PermissoesGrupo[] = 138; // Coordenador de Avaliacao
        $PermissoesGrupo[] = 139; // Tecnico de Avaliacao
        $PermissoesGrupo[] = 140; // Tecnico de Admissibilidade Edital
        //parent::perfil(1, $PermissoesGrupo);
        //parent::init();
        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
        parent::init();

        //recupera ID do pre projeto (proposta)
        if (!empty($_REQUEST['idPreProjeto'])) {
            $this->idPreProjeto = $_REQUEST['idPreProjeto'];
        }

        isset($auth->getIdentity()->usu_codigo) ? $this->idUsuario = $auth->getIdentity()->usu_codigo : $this->idUsuario = $auth->getIdentity()->IdUsuario;
        //$this->idUsuario = $auth->getIdentity()->usu_codigo;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->codGrupo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->codOrgao = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o

            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : $auth->getIdentity()->usu_orgao;
        }

        $intIdPronac = $this->getRequest()->getParam('idPronac');
        if ($intIdPronac) {
            $tbProjeto = new Projetos();
            $this->arrProjeto = $tbProjeto->findBy($intIdPronac);
        }


        $this->arrBreadCrumb[] = array('url' => '/principal', 'title' => 'In&iacute;cio', 'description' => 'Ir para in&iacute;cio');
    }

    /**
     * @name indexAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  05/12/2016
     */
    public function indexAction()
    {
        $arrBreadCrumb = array();
        $arrBreadCrumb[] = array('url' => '/principal', 'title' => 'In&iacute;cio', 'description' => 'Ir para in&iacute;cio');
        if ($this->arrProjeto) {
            $arrBreadCrumb[] = array('url' => '/admissibilidade/enquadramento/gerenciar-enquadramento', 'title' => 'Enquadramentos', 'description' => 'Ir para a tela de enquadramentos');
            $arrBreadCrumb[] = array('url' => '', 'title' => "Perguntas: {$this->arrProjeto['AnoProjeto']}{$this->arrProjeto['Sequencial']} - {$this->arrProjeto['NomeProjeto']}", 'description' => 'Tela atual: N&uacute;mero do Pronac - Nome do projeto');
        } else {
            $arrBreadCrumb[] = array('url' => '', 'title' => 'Perguntas', 'description' => 'Tela atual');
        }
        $this->view->idPronac = $this->getRequest()->getParam('idPronac', null);
        $this->view->arrBreadCrumb = $arrBreadCrumb;
    }

    /**
     * @name indexAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  05/12/2016
     */
    public function perguntasUsuarioAction()
    {
        $arrBreadCrumb = array();
        $arrBreadCrumb[] = array('url' => '/principal', 'title' => 'In&iacute;cio', 'description' => 'Ir para in&iacute;cio');
        if ($this->arrProjeto) {
            $arrBreadCrumb[] = array('url' => '/admissibilidade/enquadramento/gerenciar-enquadramento', 'title' => 'Enquadramentos', 'description' => 'Ir para a tela de enquadramentos');
            $arrBreadCrumb[] = array('url' => '', 'title' => "Perguntas: {$this->arrProjeto['AnoProjeto']}{$this->arrProjeto['Sequencial']} - {$this->arrProjeto['NomeProjeto']}", 'description' => 'Tela atual: N&uacute;mero do Pronac - Nome do projeto');
        } else {
            $arrBreadCrumb[] = array('url' => '', 'title' => 'Dirimir d&uacute;vidas', 'description' => 'Tela atual');
        }
        $this->view->idPronac = $this->getRequest()->getParam('idPronac', null);
        $this->view->arrBreadCrumb = $arrBreadCrumb;
    }

    public function listarAction()
    {
        $this->_helper->layout->disableLayout();
        $intIdPronac = $this->getRequest()->getParam('idPronac', null);
        $auth = Zend_Auth::getInstance(); // pega a autenticacao

        $arrAuth = array_change_key_case((array)$auth->getIdentity());
        $intUsuCodigo = $arrAuth['usu_codigo'];
        $grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        $intUsuOrgao = $grupoAtivo->codGrupo;
        //var_dump($intUsuOrgao, $grupoAtivo->codOrgao);die;
        $dbTable = new Admissibilidade_Model_DbTable_TbMensagemProjeto();
        if (empty($intIdPronac)) {
            $arrWhere = array('tbMensagemProjeto.idMensagemOrigem IS NULL AND tbMensagemProjeto.idDestinatario = ?' => $intUsuCodigo);
            $arrOrWhere = array("tbMensagemProjeto.idMensagemOrigem IS NULL AND tbMensagemProjeto.idDestinatario = {$intUsuCodigo} AND tbMensagemProjeto.idDestinatarioUnidade = ?" => $intUsuOrgao);
            $this->view->arrResult = $dbTable->getAllBy($arrWhere, $arrOrWhere);
        } else {
            $arrWhere = array('tbMensagemProjeto.idMensagemOrigem IS NULL' => '');
            $arrWhere['tbMensagemProjeto.IdPRONAC'] = $intIdPronac;
            $this->view->arrResult = $dbTable->getAllBy($arrWhere);
        }
        $this->view->usuCodigo = $intUsuCodigo;
        $this->view->usuOrgao = $intUsuOrgao;
        $this->view->idPronac = $intIdPronac;
    }

    public function listarPerguntasUsuarioAction()
    {
        $this->_helper->layout->disableLayout();
        $intIdPronac = $this->getRequest()->getParam('idPronac', null);
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $arrAuth = array_change_key_case((array)$auth->getIdentity());
        $intUsuCodigo = $arrAuth['usu_codigo'];
        $grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $intUsuOrgao = $grupoAtivo->codOrgao;
        //$intUsuOrgao = $grupoAtivo->codGrupo;
        //var_dump($intUsuOrgao, $grupoAtivo->codOrgao);die;
        $dbTable = new Admissibilidade_Model_DbTable_VwPainelDeMensagens();
        $this->view->arrResult = $dbTable->carregarPerguntasSemResposta($intUsuCodigo, $intUsuOrgao);
        $this->view->usuCodigo = $intUsuCodigo;
        $this->view->usuOrgao = $intUsuOrgao;
        $this->view->idPronac = $intIdPronac;
    }

    /**
     * Acao responsavel por inserir ou editar uma mensagem.
     *
     * @name salvarAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  06/12/2016
     */
    public function salvarAction()
    {
        $intIdPronac = $this->getRequest()->getParam('idPronac');
        $this->view->projeto = $this->arrProjeto;
        if ($this->getRequest()->isPost()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $mapper = new Admissibilidade_Model_TbMensagemProjetoMapper();
            $strUrl = '/admissibilidade/mensagem/index';
            $strUrl .= ($this->arrProjeto) ? '?idPronac=' . $this->arrProjeto['IdPRONAC'] : '';
            $this->_helper->json(array('status' => $mapper->salvar($this->getRequest()->getPost()), 'msg' => $mapper->getMessages(), 'redirect' => $strUrl));
        } else {
            $this->prepareForm(array('dsResposta' => array('show' => false)));
            $this->view->action = 'salvar';
            if ($this->arrProjeto) {
                $this->arrBreadCrumb[] = array('url' => '/admissibilidade/enquadramento/listar', 'title' => 'Enquadramentos', 'description' => 'Ir para a tela de enquadramentos');
                $this->arrBreadCrumb[] = array('url' => "/admissibilidade/mensagem/index?idPronac={$this->arrProjeto['IdPRONAC']}", 'title' => "Perguntas: {$this->arrProjeto['AnoProjeto']}{$this->arrProjeto['Sequencial']} - {$this->arrProjeto['NomeProjeto']}", 'description' => 'Ir para a tela de perguntas');
            } else {
                $this->arrBreadCrumb[] = array('url' => '/admissibilidade/mensagem/index', 'title' => 'Perguntas', 'description' => 'Ir para a tela de perguntas');
            }
            $this->arrBreadCrumb[] = array('url' => '', 'title' => 'Enviar pergunta', 'description' => 'Tela atual');
            $this->view->arrBreadCrumb = $this->arrBreadCrumb;
        }
    }

    /**
     * Acao responsavel por inserir ou editar uma mensagem.
     *
     * @name salvarAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  06/12/2016
     */
    public function visualizarAction()
    {
        $this->_helper->layout->disableLayout();
        $this->prepareForm(array(
            'idDestinatario' => array('disabled' => true),
            'idDestinatarioUnidade' => array('disabled' => true),
            'dsMensagem' => array('disabled' => true),
            'dsResposta' => array('disabled' => true),
            'actions' => array('show' => false)
        ));
    }

    /**
     * Acao responsavel por encaminhar uma mensagem para outro agente.
     *
     * @name responderAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  12/12/2016
     */
    public function encaminharAction()
    {
        $this->_helper->layout->disableLayout();
        if ($this->getRequest()->isPost()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $mapper = new Admissibilidade_Model_TbMensagemProjetoMapper();
            $this->_helper->json(array('status' => $mapper->encaminhar($this->getRequest()->getPost()), 'msg' => $mapper->getMessages()));
        } else {
            $strUrlAction = '/admissibilidade/mensagem/encaminhar';
            $strUrlAction .= ($this->arrProjeto) ? '?idPronac=' . $this->arrProjeto['IdPRONAC'] : '';
            $this->view->title = 'Encaminhar pergunta';
            $this->view->action = 'encaminhar';
            $this->prepareForm(array(
                'dsMensagem' => array('show' => false),
                'dsResposta' => array('show' => false),
                'actions' => array('show' => false)
            ), $strUrlAction);
        }
    }

    /**
     * Acao responsavel por responder uma mensagem, no caso cadastrar uma mensagem referenciando outra.
     *
     * @name responderAction
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  12/12/2016
     */
    public function responderAction()
    {
        $strActionBack = $this->getRequest()->getParam('actionBack');
        $strActionBack = ($strActionBack) ? $strActionBack : 'index';

        if ($this->getRequest()->isPost()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $mapper = new Admissibilidade_Model_TbMensagemProjetoMapper();
            $strUrl = '/admissibilidade/mensagem/' . $strActionBack;
            $strUrl .= ($this->arrProjeto) ? '?idPronac=' . $this->arrProjeto['IdPRONAC'] : '';
            $this->_helper->json(array('status' => $mapper->responder($this->getRequest()->getPost()), 'msg' => $mapper->getMessages(), 'redirect' => $strUrl));
        } else {
            $this->prepareForm(array(
                'idDestinatario' => array('disabled' => true),
                'idDestinatarioUnidade' => array('disabled' => true),
                'dsMensagem' => array('disabled' => true),
            ), '', $strActionBack);
            if ($this->arrProjeto) {
                $this->arrBreadCrumb[] = array('url' => '/admissibilidade/enquadramento/listar', 'title' => 'Enquadramentos', 'description' => 'Ir para a tela de enquadramentos');
                $arrBreadCrumb[] = array('url' => '', 'title' => "Perguntas: {$this->arrProjeto['AnoProjeto']}{$this->arrProjeto['Sequencial']} - {$this->arrProjeto['NomeProjeto']}", 'description' => 'Ir para a tela de perguntas');
            } else {
                $this->arrBreadCrumb[] = array('url' => '/admissibilidade/mensagem/index', 'title' => 'Perguntas', 'description' => 'Ir para a tela de perguntas');
            }
            $this->arrBreadCrumb[] = array('url' => '', 'title' => 'Responder pergunta', 'description' => 'Tela atual');
            $this->view->arrBreadCrumb = $this->arrBreadCrumb;
        }
    }

    /**
     * Metodo responsavel por preparar o formulario conforme cada acao.
     *
     * @name prepareForm
     * @param array $arrConfig
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  12/12/2016
     */
    public function prepareForm($arrConfig = array(), $strUrlAction = '', $strActionBack = 'index')
    {
        if ($this->arrProjeto) {
//            $this->view->title = "Perguntas: {$this->arrProjeto['NomeProjeto']} ({$this->arrProjeto['IdPRONAC']})";
            $arrConfig['idDestinatario'] = array('show' => true);
//            if (in_array($this->arrProjeto['Situacao'], array('B02', 'B03'))) {
                $arrConfig['idDestinatario'] = array('show' => false);
//            }
        }

        $dbTable = new Admissibilidade_Model_DbTable_TbMensagemProjeto();
        $vw = new vwUsuariosOrgaosGrupos();
        $intId = $this->getRequest()->getParam('id', null);
        $idPronac = $this->getRequest()->getParam('idPronac');
        $dataForm = array();
        if ($intId) {
            $dataForm = $dbTable->findBy($intId);
            $dataForm['dsResposta'] = ($arrMensagemResposta = $dbTable->findBy(array('idMensagemOrigem' => $intId))) ? $arrMensagemResposta['dsMensagem'] : '';
        }

        $this->view->arrPartial = array(
            'arrUnidades' => $vw->carregarUnidade(),
            'arrConfig' => $arrConfig,
            'dataForm' => $dataForm,
            'id' => $intId,
            'idPronac' => $idPronac,
            'urlAction' => $strUrlAction,
            'strActionBack' => $strActionBack,
        );
    }

    public function usuariosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $vw = new vwUsuariosOrgaosGrupos();
        $intId = $this->getRequest()->getParam('id', null);
        $arrUsuarios = $vw->carregarUsuariosPorUnidade($intId);
        $this->_helper->json($arrUsuarios);
    }
}
