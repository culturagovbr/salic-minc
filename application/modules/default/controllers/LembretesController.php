<?php

class LembretesController extends MinC_Controller_Action_Abstract
{
    public function LembretesAction()
    {
    }
    
    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura"; // título da página
        $auth              = Zend_Auth::getInstance(); // pega a autenticação
        $Usuario           = new UsuarioDAO(); // objeto usuário
        $GrupoAtivo        = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuário esteja autenticado
            // verifica as permissões
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 118; // Componente da Comissão
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo está no array de permissões
                parent::message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visão
            $this->view->usuario     = $auth->getIdentity(); // manda os dados do usuário para a visão
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuário para a visão
            $this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
            $this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão
        } // fecha if
        else { // caso o usuário não esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    } 
    
    public function indexAction()
    {
        // caso o formulário seja enviado via post
        if ($this->getRequest()->isPost()) {
            $post = Zend_Registry::get('post');
            $contador  = $post->Contador;
            $lembrete  = $post->descricao;
            $idPronac  = $post->pronac;
            $databusca = $post->databusca;
            
        
            $mens = new LembretesDAO();
            $mens->alterarlembrete($contador, $lembrete);
            parent::message("Alteração efetuada com sucesso!", "lembretes/index?pronac=".$idPronac."&databusca=".$databusca);
        }
        $get = Zend_Registry::get('get');
        $pronac    = $get->pronac;
        $dtlembrete = $get->databusca;
            
        $this->view->lembretesprojeto 	= LembretesDAO::buscaProjeto($pronac);
        //$this->view->lembretes 			= LembretesDAO::buscaLembrete($pronac);
        $this->view->lembretes 			= LembretesDAO::pesquisaLembrete($pronac, $dtlembrete);
    }
        
    public function inserirlembreteAction()
    {
        $get = Zend_Registry::get('get');
        $pronac = $get->pronac;
        

        $tblembretesprojeto = LembretesDAO::buscaProjeto($pronac);
        $this->view->lembretesprojeto = $tblembretesprojeto;

        $tblembretes = LembretesDAO::buscaLembrete($pronac);
        $this->view->lembretes = $tblembretes;

        $this->view->anoprojeto = $tblembretesprojeto[0]->AnoProjeto;
        $this->view->sequencial = $tblembretesprojeto[0]->Sequencial;

        // caso o formulário seja enviado via post
        if ($this->getRequest()->isPost()) {
            $post 		= Zend_Registry::get('post');
            $pronac     = $post->pronac;
            $lembrete   = $post->descricao;
            $anoprojeto = $post->AnoProjeto;
            $sequencial = $post->Sequencial;
            
            try {
                if (empty($lembrete)) {
                    throw new Exception("Por favor, informe o lembrete!");
                } else {
                    $mens = new LembretesDAO();

                    if ($mens->inserirLembrete($anoprojeto, $sequencial, $lembrete)) {
                        parent::message("Cadastro efetuado com sucesso!", "lembretes/index?pronac=".$pronac);
                    } else {
                        throw new Exception("Erro ao efetuar cadastro!");
                    }
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "lembretes/inserirlembrete?pronac=".$pronac, "ERROR");
            }
        }
    }
        


    
    
    public function excluirAction()
    {
        $contador  = $_GET['id'];
        $pronac    = $_GET['pronac'];
        $databusca = $_GET['databusca'];

        $resultado = LembretesDAO::exluirlembrete($contador);
            
        if ($resultado) {
            parent::message("Erro ao excluir lembrete!", "lembretes/index?pronac=".$pronac."&databusca=".$databusca, "CONFIRM");
        } else {
            parent::message("Lembrete excluído com sucesso!", "lembretes/index?pronac=".$pronac."&databusca=".$databusca, "CONFIRM");
        }
    }

    /*

public function alterarlembreteAction()
        {


        $contador  = $_GET['id'];
        $pronac    = $_GET['pronac'];

        $lembrete = $_GET['descricao'];

        $resultado = LembretesDAO::alterarlembrete($contador, $lembrete);

        if ($resultado)
        {
            parent::message("Erro ao alterar lembrete!", "lembretes/index?pronac=".$pronac, "CONFIRM");
        }
        else
        {
            parent::message("Lembrete alterado com sucesso!", "lembretes/index?pronac=".$pronac, "CONFIRM");
        }


    }


*/

    public function buscalembreteAction()
    {
        // caso o formulário seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe o pronac e data do lembrete via post
            $post   = Zend_Registry::get('post');
            $pronac = (int) $post->pronac;
            
            $dtlembrete =  $post->dtlembrete;
            
            try {
                // verifica se a data dolembrete veio vazio
                if (empty($dtlembrete) && !Data::validarData($dtlembrete)) {
                    throw new Exception("A Data é inválida!");
                }
                // busca o pronac no banco
                else {
                    // integração MODELO e VISÃO

                    $resultado = LembretesDAO::pesquisaLembrete($pronac, $dtlembrete);

                    // caso o Lembrete não esteja cadastrado
                    if (!$resultado) {
                        throw new Exception("Registro não encontrado!");
                    }
                    // caso o Lembrete esteja cadastrado,
                    // vai para a página dos Lembretes
                    else {
                        // redireciona a data para o lembrete
                        $this->redirect("lembretes/index?pronac=" . $pronac ."&databusca=".$dtlembrete);
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "lembretes/buscalembrete?pronac=" . $pronac, "ERROR");
            }
        } // fecha if
        $get = Zend_Registry::get('get');
        $pronac = $get->pronac;
        $dtlembrete = $get->dtlembrete;
        
        $mens = new LembretesDAO();
        
        $tblembretesprojeto = $mens->buscaProjeto($pronac);
        $this->view->lembretesprojeto = $tblembretesprojeto;
        
        $tblembretes = $mens->buscaLembrete($pronac);
        $this->view->lembretes = $tblembretes;
    }
}
