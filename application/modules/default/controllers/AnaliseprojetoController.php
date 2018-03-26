<?php

class AnaliseprojetoController extends Zend_Controller_Action
{

    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura"; // título da página
        $auth = Zend_Auth::getInstance(); // pega a autenticação
        $Usuario = new UsuarioDAO(); // objeto usuário
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuário esteja autenticado
            // verifica as permissões
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 93;
            $PermissoesGrupo[] = 118;
            $PermissoesGrupo[] = 119;
            $PermissoesGrupo[] = 120;
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo está no array de permissões
                parent::message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visão
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuário para a visão
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuário para a visão
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão
        } // fecha if
        else { // caso o usuário não esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    // fecha método init()


    public function analiseprojetoAction()
    {
    }

    public function indexAction()
    {
        $get = Zend_Registry::get('get');
        $pronac = $get->pronac;

        $tbanaliseprojeto = AnaliseprojetoDAO::buscar($pronac);

        $this->view->analiseprojeto = $tbanaliseprojeto;
    }
}
