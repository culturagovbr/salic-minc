<?php

class LembretesController extends MinC_Controller_Action_Abstract
{
    public function LembretesAction()
    {
    }

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da página
        $auth              = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario           = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo        = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio esteja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 118; // Componente da comissão
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc� Não tem permiss�o para acessar essa �rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a vis�o
            $this->view->usuario     = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o
        } // fecha if
        else { // caso o usu�rio Não esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
    {
        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            $post = Zend_Registry::get('post');
            $contador  = $post->Contador;
            $lembrete  = $post->descricao;
            $idPronac  = $post->pronac;
            $databusca = $post->databusca;


            $mens = new LembretesDAO();
            $mens->alterarlembrete($contador, $lembrete);
            parent::message("Altera��o efetuada com sucesso!", "lembretes/index?pronac=".$idPronac."&databusca=".$databusca);
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

        // caso o formul�rio seja enviado via post
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
            parent::message("Lembrete exclu�do com sucesso!", "lembretes/index?pronac=".$pronac."&databusca=".$databusca, "CONFIRM");
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
        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPost()) {
            // recebe o pronac e data do lembrete via post
            $post   = Zend_Registry::get('post');
            $pronac = (int) $post->pronac;

            $dtlembrete =  $post->dtlembrete;

            try {
                // verifica se a data dolembrete veio vazio
                if (empty($dtlembrete) && !Data::validarData($dtlembrete)) {
                    throw new Exception("A Data � inv�lida!");
                }
                // busca o pronac no banco
                else {
                    // integra��o MODELO e VIS�O

                    $resultado = LembretesDAO::pesquisaLembrete($pronac, $dtlembrete);

                    // caso o Lembrete Não esteja cadastrado
                    if (!$resultado) {
                        throw new Exception("Registro Não encontrado!");
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
