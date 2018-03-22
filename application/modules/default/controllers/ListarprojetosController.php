<?php

class ListarprojetosController extends MinC_Controller_Action_Abstract
{
    private $getIdUsuario = 0;
    private $getCNPJCPF = 0;
    private $idResponsavel = 0;
    private $idAgente = 0;
    private $idUsuario = 0;
    private $cpfLogado = 0;

    /*     * *
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */

    public function init()
    {
        ini_set('memory_limit', '128M');
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        // define as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97;  // Gestor Salic
        $PermissoesGrupo[] = 93;  // Acompanhamento
        $PermissoesGrupo[] = 134; // Coordenador de Fiscalizacao
        //SE CAIU A SECAO REDIRECIONA
        if (!$auth->hasIdentity()) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl();
            JS::redirecionarURL($url);
        }

        /*         * ****************************************************************************************************** */
        $cpf = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_identificacao : $auth->getIdentity()->Cpf;

        $this->cpfLogado = $cpf;

        // Busca na SGCAcesso
        $sgcAcesso = new Autenticacao_Model_Sgcacesso();
        $buscaAcesso = $sgcAcesso->buscar(array('Cpf = ?' => $cpf));

        // Busca na Usuarios
        $usuarioDAO = new Autenticacao_Model_Usuario();
        $buscaUsuario = $usuarioDAO->buscar(array('usu_identificacao = ?' => $cpf));

        // Busca na Agentes
        $agentesDAO = new Agente_Model_DbTable_Agentes();
        $buscaAgente = $agentesDAO->BuscaAgente($cpf);


        if (count($buscaAcesso) > 0) {
            $this->idResponsavel = $buscaAcesso[0]->IdUsuario;
        }
        if (count($buscaAgente) > 0) {
            $this->idAgente = $buscaAgente[0]->idAgente;
        }
        if (count($buscaUsuario) > 0) {
            $this->idUsuario = $buscaUsuario[0]->usu_codigo;
        }

        $this->view->idAgenteLogado = $this->idAgente;
        /*         * ****************************************************************************************************** */

        // pega o idAgente do usuario logado
        if (isset($auth->getIdentity()->usu_codigo)) {
            parent::perfil(1, $PermissoesGrupo);

            $this->getCNPJCPF = $auth->getIdentity()->usu_identificacao;

            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            if ($this->getIdUsuario) {
                $this->getIdUsuario = $this->getIdUsuario["idAgente"];
            } else {
                $this->getIdUsuario = 0;
            }
        } else {
            parent::perfil(4, $PermissoesGrupo);
            $this->getCNPJCPF = $auth->getIdentity()->Cpf;
            $this->getIdUsuario = $auth->getIdentity()->IdUsuario;
        }

        parent::init();
    }

    public function indexAction()
    {
        $this->_redirect("Listarprojetos/listarprojetos");
    }

    public function listarprojetosAction()
    {
        /***************************************************************************** */
        $tblVinculo = new Agente_Model_DbTable_TbVinculo();
        $dadosCombo = array();

        $rsVinculo = $tblVinculo->buscarProponenteResponsavel($this->idResponsavel);

        $i = 1;
        foreach ($rsVinculo as $rs) {
            $dadosCombo[$i]['idAgenteProponente'] = $rs->idAgente;
            $dadosCombo[$i]['CPF'] = $rs->CNPJCPF;
            $dadosCombo[$i]['Nome'] = $rs->NomeProponente;
            $i++;
        }

        $this->view->buscaProponente = $dadosCombo;
        $this->view->idResponsavel = $this->idResponsavel;
        $this->view->idUsuario = $this->idUsuario;

        /*****************************************************************************/

        if (!isset($_POST['idProponente']) || empty($_POST['idProponente'])) {
            $this->view->listarprojetos = 0;
        } else {
            try {
                $post = Zend_Registry::get('post');

                $idProponente = !empty($post->idProponente) ? $post->idProponente : ''; // deleta a m�scara
                $mecanismo = $_POST['mecanismo'];
                $idResponsavel = $this->idResponsavel;

                $a = new Projetos();
                $ProjetosVinculados = $a->listarProjetosConsulta($idResponsavel, $idProponente, $mecanismo)->toArray();

                $tbProjetos = new Projeto_Model_DbTable_Projetos();
                $projetos = [];

                if (count($ProjetosVinculados) > 0) {
                    foreach ($ProjetosVinculados as $projeto) {

                        $idPreProjeto = $tbProjetos->obterIdPreProjetoDoProjeto($projeto['IdPRONAC']);
                        $projeto['podeClonarProjeto'] = !empty($idPreProjeto) ? true : false;
                        $projeto['liberarEdicao'] = $tbProjetos->fnChecarLiberacaoDaAdequacaoDoProjeto($projeto['IdPRONAC']);

                        $projetos[] = $projeto;
                    }
                    $this->view->listarprojetos = $projetos;
                    $this->view->mecanismo = $mecanismo;
                    $this->view->agenteId = $idProponente;
                } else {
                    parent::message("Nenhum projeto encontrado!", "listarprojetos/listarprojetos", "ALERT");
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "listarprojetos/listarprojetos", "ERROR");
            }
        }
    }

    public function buscarProponentesComboAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $mecanismo = $_POST['mecanismo'];

        $tblVinculo = new Agente_Model_DbTable_TbVinculo();
        $rsVinculo = $tblVinculo->buscarProponenteResponsavel($this->idResponsavel, $mecanismo);
        $agente = array();

        $i = 1;
        if (count($rsVinculo) > 0) {
            foreach ($rsVinculo as $rs) {
                $dadosCombo[$i]['idAgenteProponente'] = $rs->idAgente;
                if (strlen($rs->CNPJCPF) == 11) {
                    $proponente = '[' . Mascara::addMaskCPF($rs->CNPJCPF) . '] - ' . utf8_encode($rs->NomeProponente);
                } else {
                    $proponente = '[' . Mascara::addMaskCNPJ($rs->CNPJCPF) . '] - ' . utf8_encode($rs->NomeProponente);
                }
                $dadosCombo[$i]['proponente'] = $proponente;
                $i++;
            }
            $jsonEncode = json_encode($dadosCombo);
            $this->_helper->json(array('resposta' => true, 'conteudo' => $dadosCombo));
        } else {
            $this->_helper->json(array('resposta' => false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();

        $cpf = $_GET['cpf'];
        $listar = ListarprojetosDAO::buscarDadosListarProjetos($cpf);
        $this->view->listarprojetos = $listar;
    }

    public function gerarxlsAction()
    {
        $cpf = $_GET['cpf'];
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $listar = ListarprojetosDAO::buscarDadosListarProjetos($cpf);
        $this->view->listarprojetos = $listar;
    }

    public function clonarProjetoAjaxAction()
    {
        $params = $this->getRequest()->getParams();

        $return['msg'] = '';
        $return['status'] = false;

        $idPronac = isset($params['idPronac']) ? $params['idPronac'] : '';

        $permissaoProjeto = $this->verificarPermissaoAcesso(false, $idPronac, false, true);

        if (true !== $permissaoProjeto['status']) {
            $this->_helper->json($permissaoProjeto);
            die;
        }

        if (empty($this->idResponsavel)) {
            $return['msg'] = 'Usu&aacute;rio inv&aacute;lido!';
            $return['status'] = false;

            $this->_helper->json($return);
            die;
        }

        $tbProjetos = new Projeto_Model_DbTable_Projetos();

        $idPreProjeto = $tbProjetos->obterIdPreProjetoDoProjeto($idPronac);

        if (empty($idPreProjeto)) {
            $return['msg'] = 'Erro ao Clonar! Este projeto n&atilde;o possui proposta!';
            $return['status'] = false;

            $this->_helper->json($return);
            die;
        }

        $retorno = (array) $tbProjetos->spClonarProjeto($idPronac, $this->idResponsavel);

        $return['msg'] = $retorno['Mensagem'];
        $return['status'] = false;

        if ($retorno['stEstado'] == 'TRUE') {
            $return['idPreProjeto'] = $retorno['Mensagem'];
            $return['msg'] = 'Sucesso! Voc&ecirc; ser&aacute; redirecionado para a proposta!';
            $return['status'] = true;
        }

        $this->_helper->json($return);
        die;
    }
}
