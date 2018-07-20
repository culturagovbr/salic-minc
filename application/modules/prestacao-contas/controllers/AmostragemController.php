<?php
class PrestacaoContas_AmostragemController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $PermissoesGrupo = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS
        ];

        $auth = Zend_Auth::getInstance();

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        isset($auth->getIdentity()->usu_codigo) ? $this->idUsuario = $auth->getIdentity()->usu_codigo : $this->idUsuario = $auth->getIdentity()->IdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->codGrupo = $GrupoAtivo->codGrupo;
            $this->codOrgao = $GrupoAtivo->codOrgao;
            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;
        }

        parent::init();
    }
    
    public function indexAction()
    {
        $idpronac = $this->_request->getParam('idPronac');
        $tipoAvaliacao = $this->_request->getParam('tipoAvaliacao');

        if (!$idPronac) {
            throw new Exception('Não existe idPronac');
        }
        if (!$tipoAvaliacao) {
            throw new Exception('Não existe tipoAvaliacao');
        }

        $this->view->idPronac = $idpronac;
        $this->view->tipoAvaliacao = $tipoAvaliacao;

        $comprovantes = new PrestacaoContas_Model_spComprovantes();
        $comprovantes = $comprovantes->exec($idPronac, $tipoAvaliacao);

    }
}