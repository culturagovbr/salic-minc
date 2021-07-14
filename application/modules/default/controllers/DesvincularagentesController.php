<?php

class DesvincularagentesController extends MinC_Controller_Action_Abstract
{

    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p&aacute;gina
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio esteja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            $PermissoesGrupo[] = 103; // Coordenador de An&aacute;lise
            $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
            $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc� Não tem permiss�o para acessar essa �rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a vis�o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o
        } // fecha if
        else { // caso o usu�rio Não esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init();
    }

    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        // despacha para buscaragentes.phtml
        $this->forward("buscaragentes");
    }

    // fecha m�todo buscaragentesAction()
    public function desvincularagentesAction()
    {
        $filter = new Zend_Filter_StripTags();

        $idagente = $filter->filter($this->_request->getPost('idagente'));
        $dados = DesvincularagentesDAO::desvincularagentes($idagente);

        $post = Zend_Registry::get('get');
        $cnpjcpfsuperior = $post->cnpjcpfsuperior;
        $cnpjcpf = $post->cnpjcpf;
        $proponente = $post->proponente;
        $idagente = $post->idagente;

        $vin = new DesvincularagentesDAO();

        $tbagentedesvinculado = $vin->desvincularagentes($idagente);
        $this->view->agentedesvinculado = $tbagentedesvinculado;

        $tbvinculoagente = $vin->buscaragentes($cnpjcpf);
        $this->view->vinculoagente = $tbvinculoagente;

        $tbentidade = $vin->buscaentidade($cnpjcpf);
        $this->view->entidade = $tbentidade;
    }

    public function excluirAction()
    {
        $idVinculoPrincipal = $_GET['idVinculoPrincipal'];
        $idAgente = $_GET['idAgente'];

        if ($this->getRequest()->isGET()) {
            $get = Zend_Registry::get('get');
            $cnpjcpf = $get->cnpjcpf;
            $nome = $get->nome;


            try {
                $resultado = DesvincularagentesDAO::desvincularagentes($idAgente, $idVinculoPrincipal);

                parent::message("Agente desvinculado com sucesso!", "desvincularagentes/mostraragentes?cnpjcpf=" . $cnpjcpf . "&nome=" . $nome." ", "CONFIRM");
            } catch (Exception $e) {
                parent::message("Erro ao desvincular Agentes!", "desvincularagentes/mostraragentes?cnpjcpf=" . $cnpjcpf . "&nome=" . $nome." ", "ERROR");
            }

            //$this->_redirect("desvincularagentes/mostraragentes?cnpjcpf=" . $cnpjcpf . "&nome=" . $nome);
        }
    }


    public function buscaragentesAction()
    {
    }


    public function buscaragentesvinculadosAction()
    {

        // caso o formul�rio seja enviado via post
        if ($this->getRequest()->isPOST()) {
            // recebe o cpf/cnpj via post
            $post = Zend_Registry::get('post');
            $cnpjcpf = Mascara::delMaskCNPJ($post->cnpjcpf);
            $nome = $post->nome;

            // VALIDA��O
            try {
                if (!$cnpjcpf && !$nome) {
                    throw new Exception("Por favor, informe o CNPJ/CPF ou o Nome!");
                } else {
                    if ($cnpjcpf) {
                        if (strlen($cnpjcpf) == 11 && !Validacao::validarCPF($cnpjcpf)) {
                            throw new Exception("O N� do CPF � inv�lido!");
                        } elseif (strlen($cnpjcpf) > 11 && !Validacao::validarCNPJ($cnpjcpf)) {
                            throw new Exception("O N� do CNPJ � inv�lido!");
                        }

                        if (strlen($cnpjcpf) < 11 || strlen($cnpjcpf) > 14) {
                            throw new Exception("Informe todos os digitos!");
                        }

                        if ($nome) {
                            if (strlen($nome) > 70) {
                                throw new Exception("Nome inv�lido!");
                            }
                        }
                    } else {
                        if (strlen($nome) > 70) {
                            throw new Exception("Nome inv�lido!");
                        }
                    }

                    $this->redirect("desvincularagentes/mostraragentes?cnpjcpf=" . $cnpjcpf . "&nome=" . $nome);
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "desvincularagentes/buscaragentes", "ERROR");
            }
        }
    }

    public function mostraragentesAction()
    {
        $vin = new DesvincularagentesDAO();

        if ($this->getRequest()->isGET()) {
            // recebe o cpf/cnpj via post
            $get = Zend_Registry::get('get');
            $cnpjcpf = $get->cnpjcpf;
            $nome = $get->nome;

            $tbentidade = $vin->buscaentidade($nome, $cnpjcpf);

            try {
                if ($tbentidade) {

                    // ========== IN&iacute;CIO PAGINAÇÂO ==========
                    //criando a pagina�ao
                    Zend_Paginator::setDefaultScrollingStyle('Sliding');
                    Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                    $paginator = Zend_Paginator::factory($tbentidade); // dados a serem paginados

                    // p&aacute;gina atual e quantidade de �tens por p&aacute;gina
                    $currentPage = $this->_getParam('page', 1);
                    $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(1);
                    // ========== FIM PAGINAÇÂO ==========

                    $this->view->entidade = $paginator;
                    $this->view->qtdEntidade    = count($tbentidade); // quantidade
                } else {
                    throw new Exception("Registro Não encontrado!");
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "desvincularagentes/buscaragentes", "ERROR");
            }
        }
    }
}// fecha class
