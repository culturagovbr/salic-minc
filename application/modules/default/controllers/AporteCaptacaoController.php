<?php

class AporteCaptacaoController extends MinC_Controller_Action_Abstract
{
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        $auth              = Zend_Auth::getInstance(); // pega a autenticacao
        $Usuario           = new UsuarioDAO(); // objeto usuario
        $GrupoAtivo        = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $PermissoesGrupo = array();
        
        //Da permissao de acesso a todos os grupos do usuario logado
        if (isset($auth->getIdentity()->usu_codigo)) {
            //Recupera todos os grupos do Usuario
            $Usuario = new Autenticacao_Model_Usuario(); // objeto usuário
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            foreach ($grupos as $grupo) {
                $PermissoesGrupo[] = $grupo->gru_codigo;
            }
        }
        
        if (isset($auth->getIdentity()->usu_codigo)) {
            parent::perfil(1, $PermissoesGrupo);
        } else {
            parent::perfil(4, $PermissoesGrupo);
        }

        if (isset($auth->getIdentity()->usu_codigo)) {
            // autenticacao novo salic
            $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario['idAgente'] : 0;
        } else {
            // autenticacao espaco proponente
            $this->getIdUsuario = 0;
        }
        /* ========== FIM PERFIL ==========*/

        
        /* ========== INÍCIO ÓRGÃO ========== */
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
        $this->getIdGrupo = $GrupoAtivo->codGrupo; // id do grupo ativo
        $this->getIdOrgao = $GrupoAtivo->codOrgao; // id do órgão ativo
        parent::init();
    }

    public function depositoEquivocadoAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        
        $Projetos = new Projetos();
        $this->view->projeto = $Projetos->buscar(array('IdPRONAC = ?'=>$idPronac))->current();
        $this->view->idPronac = $idPronac;
        # aportes
        $whereData = array('idPronac = ?' => $idPronac, 'nrLote = ?' => -1,);
        if ($this->getRequest()->getParam('dtDevolucaoInicio')) {
            $whereData['dtLote >= ?'] = ConverteData($this->getRequest()->getParam('dtDevolucaoInicio'), 13);
        }
        if ($this->getRequest()->getParam('dtDevolucaoFim')) {
            $whereData['dtLote <= ?'] = ConverteData($this->getRequest()->getParam('dtDevolucaoFim'), 13);
        }
        $aporteModel = new tbAporteCaptacao();
        $this->view->dados = $aporteModel->pesquisarDepositoEquivocado($whereData);
        $this->view->dataDevolucaoInicio = $this->getRequest()->getParam('dtDevolucaoInicio');
        $this->view->dataDevolucaoFim = $this->getRequest()->getParam('dtDevolucaoFim');
    }
}
