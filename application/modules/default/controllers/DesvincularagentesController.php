<?php

class DesvincularagentesController extends MinC_Controller_Action_Abstract
{

    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio às Leis de Incentivo à Cultura"; // título da página
        $auth = Zend_Auth::getInstance(); // pega a autenticação
        $Usuario = new UsuarioDAO(); // objeto usuário
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuário esteja autenticado
            // verifica as permissões
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            $PermissoesGrupo[] = 103; // Coordenador de Análise
            $PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
            $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
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
        $this->_forward("buscaragentes");
    }

    // fecha método buscaragentesAction()
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
        
        // caso o formulário seja enviado via post
        if ($this->getRequest()->isPOST()) {
            // recebe o cpf/cnpj via post
            $post = Zend_Registry::get('post');
            $cnpjcpf = Mascara::delMaskCNPJ($post->cnpjcpf);
            $nome = $post->nome;
         
            // VALIDAÇÃO
            try {
                if (!$cnpjcpf && !$nome) {
                    throw new Exception("Por favor, informe o CNPJ/CPF ou o Nome!");
                } else {
                    if ($cnpjcpf) {
                        if (strlen($cnpjcpf) == 11 && !Validacao::validarCPF($cnpjcpf)) {
                            throw new Exception("O Nº do CPF é inválido!");
                        } elseif (strlen($cnpjcpf) > 11 && !Validacao::validarCNPJ($cnpjcpf)) {
                            throw new Exception("O Nº do CNPJ é inválido!");
                        }
                        
                        if (strlen($cnpjcpf) < 11 || strlen($cnpjcpf) > 14) {
                            throw new Exception("Informe todos os digitos!");
                        }
                        
                        if ($nome) {
                            if (strlen($nome) > 70) {
                                throw new Exception("Nome inválido!");
                            }
                        }
                    } else {
                        if (strlen($nome) > 70) {
                            throw new Exception("Nome inválido!");
                        }
                    }
                    
                    $this->_redirect("desvincularagentes/mostraragentes?cnpjcpf=" . $cnpjcpf . "&nome=" . $nome);
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
        
                    // ========== INÍCIO PAGINAÇÃO ==========
                    //criando a paginaçao
                    Zend_Paginator::setDefaultScrollingStyle('Sliding');
                    Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
                    $paginator = Zend_Paginator::factory($tbentidade); // dados a serem paginados

                    // página atual e quantidade de ítens por página
                    $currentPage = $this->_getParam('page', 1);
                    $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(1);
                    // ========== FIM PAGINAÇÃO ==========

                    $this->view->entidade = $paginator;
                    $this->view->qtdEntidade    = count($tbentidade); // quantidade
                } else {
                    throw new Exception("Registro não encontrado!");
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "desvincularagentes/buscaragentes", "ERROR");
            }
        }
    }
}// fecha class
