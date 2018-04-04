<?php
/**
 * Controller Solicitar Recurso de Decisão
 * @author Equipe RUP - Politec
 * @since 21/07/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @link http://www.cultura.gov.br
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 */

class SolicitarRecursoDecisaoController extends MinC_Controller_Action_Abstract
{
    /**
     * Variável com o id do usuário logado
     */
    private $getIdUsuario = 0;

    /**
     * Reescreve o método init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {

        // verifica as permissoes
        $PermissoesGrupo = array();
        $auth = Zend_Auth::getInstance(); // instancia da autenticação
        $GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo');

        $idPronac = $this->_request->getParam("idPronac"); // pega o id do pronac via get
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if (isset($auth->getIdentity()->usu_codigo)) {
            parent::perfil(1, $PermissoesGrupo);
        } else {
            parent::perfil(4, $PermissoesGrupo);
            $this->getIdUsuario = (isset($_GET["idusuario"])) ? $_GET["idusuario"] : 0;

            /* =============================================================================== */
            /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
            /* =============================================================================== */
            if (!isset($idPronac) || empty($idPronac)) {
                parent::message('É necessário o número do PRONAC para acessar essa página!', "principalproponente", "ERROR");
            }
//            $this->verificarPermissaoAcesso(false, true, false);
        }

        //SE CAIU A SECAO REDIRECIONA
        if (!$auth->hasIdentity()) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl();
            JS::redirecionarURL($url);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha método init()



    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        // despacha para recurso.phtml
        $this->_forward("recurso");
    }



    /**
     * Método com os recursos (Projetos Aprovados e Não Aprovados)
     * @param void
     * @return void
     */
    public function recursoAction()
    {
        // caso o formulário seja enviado via post
        if ($this->getRequest()->isPost()) {
            $post          	= Zend_Registry::get('post');
            $idPronac      	= $post->idPronac;
            $tpSolicitacao 	= $post->tpSolicitacao;
            $StatusProjeto	= $post->StatusProjeto;
            $auth           = Zend_Auth::getInstance();

            try {
                if (isset($_POST['checkEnquadramento']) && !empty($_POST['checkEnquadramento']) && isset($_POST['checkOrcamento']) && !empty($_POST['checkOrcamento'])) {
                    $tpSolicitacao = 'EO';
                } elseif (isset($_POST['checkEnquadramento']) && !empty($_POST['checkEnquadramento']) && !isset($_POST['checkOrcamento'])) {
                    $tpSolicitacao = 'EN';
                } elseif (isset($_POST['checkOrcamento']) && !empty($_POST['checkOrcamento']) && !isset($_POST['checkEnquadramento'])) {
                    $tpSolicitacao = 'OR';
                } else {
                    $tpSolicitacao = 'PI';
                }

                $dados = array(
                    'IdPRONAC'              => $_POST['idPronac'],
                    'dtSolicitacaoRecurso'  => new Zend_Db_Expr('GETDATE()'),
                    'dsSolicitacaoRecurso'  => $_POST['dsRecurso'],
                    'idAgenteSolicitante'   => $auth->getIdentity()->IdUsuario,
                    'stAtendimento'         => 'N',
                    'tpSolicitacao'         => $tpSolicitacao
                );

                $tbRecurso = new tbRecurso();
                $resultadoPesquisa = $tbRecurso->buscar(array('IdPRONAC = ?'=>$_POST['idPronac']));

                $dados['tpRecurso'] = 1;
                if (count($resultadoPesquisa)>0) {
                    $dados['tpRecurso'] = 2;
                }

                // tenta cadastrar o recurso
//                $cadastrar = RecursoDAO::cadastrar($dados);
                $cadastrar = $tbRecurso->inserir($dados);

                if ($cadastrar) {
                    // altera a situação do projeto
                    $alterarSituacao = ProjetoDAO::alterarSituacao($idPronac, 'D20');
                    parent::message('Solicitação enviada com sucesso!', "consultardadosprojeto/index?idPronac=".Seguranca::encrypt($idPronac), "CONFIRM");
                } // fecha if
                else {
                    throw new Exception("Erro ao cadastrar recurso!");
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "solicitarrecursodecisao/recurso?idPronac=".$idPronac, "ERROR");
            }
        } else {
            $idPronac = $this->_request->getParam("idPronac"); // pega o id do pronac via get
            if (strlen($idPronac) > 7) {
                $idPronac = Seguranca::dencrypt($idPronac);
            }
            $this->view->idPronac = $idPronac;

            // recebe os dados via get
            $cpf_cnpj = isset($_GET['cpf_cnpj']) ? $_GET['cpf_cnpj'] : '';

            if (!isset($idPronac) || empty($idPronac)) {
                parent::message('É necessário o número do PRONAC para acessar essa página!', "consultardadosprojeto?idPronac=".$idPronac, "ERROR");
            } else {
                // busca os projetos
                $buscarProjetos = SolicitarRecursoDecisaoDAO::buscarProjetos($idPronac, $cpf_cnpj);
                $this->view->projetos = $buscarProjetos;
            }
        }
    }

    /**
     * Método para chamar a tela de descrição do termo de deisitência do recurso
     * @author Jefferson Alessandro <jefferson.silva@cultura.gov.br>
     * @since 21/10/2013
     */
    public function recursoDesistirAction()
    {
        $idPronac = $this->_request->getParam("idPronac"); // pega o id do pronac via get
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $this->view->projetos = $dadosProj;
    }

    public function recursoDesistirEnquadramentoAction()
    {
        $idPronac = $this->_request->getParam("idPronac");

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        $Projetos = new Projetos();
        $dadosProj = $Projetos->buscar(array('IdPRONAC = ?' => $idPronac))->current();
        $this->view->projetos = $dadosProj;
    }


    /**
     * Método para aplicar no banco de dados a desistência do recurso
     * @author Jefferson Alessandro <jefferson.silva@cultura.gov.br>
     * @since 24/10/2013
     */
    public function recursoDesistenciaAction()
    {
        $post = Zend_Registry::get('post');
        $idPronac = $this->_request->getParam("idPronac"); // pega o id do pronac via get
        $auth = Zend_Auth::getInstance();

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if ($post->deacordo) {
            $dados = array(
                'IdPRONAC'              => $post->idPronac,
                'dtSolicitacaoRecurso'  => new Zend_Db_Expr('GETDATE()'),
                'dsSolicitacaoRecurso'  => 'Desistência do prazo recursal',
                'idAgenteSolicitante'   => $auth->getIdentity()->IdUsuario,
                'stAtendimento'         => 'N',
                'siFaseProjeto'         => 2,
                'siRecurso'             => 0,
                'tpSolicitacao'         => 'DR',
                'tpRecurso'             => 1,
                'stAnalise'             => null,
                'stEstado'              => 1
            );

            $tbRecurso = new tbRecurso();
            $resultadoPesquisa = $tbRecurso->buscar(array('IdPRONAC = ?'=>$_POST['idPronac']));

            if (count($resultadoPesquisa)>0) {
                $dados['tpRecurso'] = 2;
            }

            RecursoDAO::cadastrar($dados);
            parent::message('A desistência do prazo recursal foi cadastrada com sucesso!', "consultardadosprojeto?idPronac=". Seguranca::encrypt($idPronac), "CONFIRM");
        } else {
            parent::message('É necessário estar de acordo com os termos para registrar a sua desistência do prazo recursal!', "solicitarrecursodecisao/recurso-desistir?idPronac=". Seguranca::encrypt($idPronac), "ERROR");
        }
    }

    public function recursoDesistenciaEnquadramentoAction()
    {
        $post = Zend_Registry::get('post');
        $idPronac = $this->_request->getParam("idPronac"); // pega o id do pronac via get
        $auth = Zend_Auth::getInstance();

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if ($post->deacordo) {
            $dados = array(
                'IdPRONAC'              => $post->idPronac,
                'dtSolicitacaoRecurso'  => new Zend_Db_Expr('GETDATE()'),
                'dsSolicitacaoRecurso'  => 'Desistência do prazo recursal',
                'idAgenteSolicitante'   => $auth->getIdentity()->IdUsuario,
                'stAtendimento'         => 'N',
                'siFaseProjeto'         => 2,
                'siRecurso'             => 0,
                'tpSolicitacao'         => 'DR',
                'tpRecurso'             => 1,
                'stAnalise'             => null,
                'stEstado'              => 1
            );

            $tbRecurso = new tbRecurso();
            $resultadoPesquisa = $tbRecurso->buscar(array('IdPRONAC = ?'=>$_POST['idPronac']));

            if (count($resultadoPesquisa)>0) {
                $dados['tpRecurso'] = 2;
            }

            RecursoDAO::cadastrar($dados);
            parent::message('A desistência do prazo recursal foi cadastrada com sucesso!', "consultardadosprojeto?idPronac=". Seguranca::encrypt($idPronac), "CONFIRM");
        } else {
            parent::message('É necessário estar de acordo com os termos para registrar a sua desistência do prazo recursal!', "solicitarrecursodecisao/recurso-desistir-enquadramento?idPronac=". Seguranca::encrypt($idPronac), "ERROR");
        }
    }

    /**
     * Método para buscar os projetos aprovados e não aprovados
     * @access public
     * @param void
     * @return void
     */
    public function proponenteprojetoAction()
    {
        // recebe os dados do formulário via get
        $get      = Zend_Registry::get('get');
        $idpronac = $get->idpronac;
        $cpf      = $get->cpf;

        // aprovados
        $buscaprojetoaprovado = SolicitarRecursoDecisaoDAO::buscaprojetosaprovados($idpronac, $cpf);
        $this->view->projetoaprovado = $buscaprojetoaprovado;

        // não aprovados
        $buscaprojetonaoaprovado = SolicitarRecursoDecisaoDAO::buscaprojetosnaoaprovados($idpronac, $cpf);
        $this->view->projetonaoaprovado = $buscaprojetonaoaprovado;
    } // fecha método proponenteprojetoAction()

    public function recursoEnquadramentoAction()
    {
        $recurso = new tbRecurso();
        $idPronac = $this->_request->getParam("idPronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->view->idPronac = $idPronac;

        // recebe os dados via get
        $cpf_cnpj = isset($_GET['cpf_cnpj']) ? $_GET['cpf_cnpj'] : '';

        if (!isset($idPronac) || empty($idPronac)) {
            parent::message('É necessário o número do PRONAC para acessar essa página!', "consultardadosprojeto?idPronac=".$idPronac, "ERROR");
        }
        // busca os projetos
        $buscarProjetos = SolicitarRecursoDecisaoDAO::buscarProjetos($idPronac, $cpf_cnpj);
        $this->view->projeto = $buscarProjetos[0];
        $this->view->recurso = $recurso->existeRecursoIndeferido($idPronac);
    }

    /**
     * recursoEnquadramentoSalvarAction
     *
     * @access public
     * @return void
     * @todo metodo em construção
     */
    public function recursoEnquadramentoSalvarAction()
    {
        if ($this->getRequest()->isPost()) {
            $post = Zend_Registry::get('post');
            $idPronac = $post->idPronac;
            $tpSolicitacao = $post->tpSolicitacao;
            $StatusProjeto = $post->StatusProjeto;
            $auth = Zend_Auth::getInstance();

            try {

                # busca o usuario que fez o enquadramento para encaminhar o recurso para o mesmo
                $ModelEnquadramento = new Admissibilidade_Model_Enquadramento();
                $dadosEnquadramento = $ModelEnquadramento->buscarDados($idPronac, null, false);

                $dados = array(
                    'IdPRONAC'              => $_POST['idPronac'],
                    'dtSolicitacaoRecurso'  => new Zend_Db_Expr('GETDATE()'),
                    'dsSolicitacaoRecurso'  => $_POST['dsRecurso'],
                    'idAgenteSolicitante'   => $auth->getIdentity()->IdUsuario,
                    'stAtendimento'         => 'N',
                    'tpSolicitacao'         => 'EN',
                    'siFaseProjeto'         =>  1,
                    'siRecurso'             =>  1,
                    'stEstado'              =>  0,
                    'idAgenteAvaliador' => isset($dadosEnquadramento->Logon) ? $dadosEnquadramento->Logon : '',
                );

                $tbRecurso = new tbRecurso();
                $resultadoPesquisa = $tbRecurso->existeRecursoIndeferido($_POST['idPronac']);

                $dados['tpRecurso'] = 1;
                if (count($resultadoPesquisa)>0) {
                    $dados['tpRecurso'] = 2;
                }

                $cadastrar = $tbRecurso->insert($dados);

                if ($cadastrar) {
                    // altera a situação do projeto
                    parent::message('Solicitação enviada com sucesso!', "consultardadosprojeto/index?idPronac=".Seguranca::encrypt($idPronac), "CONFIRM");
                } else {
                    throw new Exception("Erro ao cadastrar recurso!");
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "solicitarrecursodecisao/recurso?idPronac=".$idPronac, "ERROR");
            }
        }
    }

    public function recursoDesistenciaEnquadramentoSalvarAction()
    {
        $post = Zend_Registry::get('post');
        $idPronac = $this->_request->getParam("idPronac");
        $auth = Zend_Auth::getInstance();

        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }

        if ($idPronac) {
            $dados = array(
                'IdPRONAC'              => $idPronac,
                'dtSolicitacaoRecurso'  => new Zend_Db_Expr('GETDATE()'),
                'dsSolicitacaoRecurso'  => 'Desistência do prazo recursal',
                'idAgenteSolicitante'   => $auth->getIdentity()->IdUsuario,
                'stAtendimento'         => 'N',
                'siFaseProjeto'         => 1,
                'siRecurso'             => TbTipoEncaminhamento::DESISTENCIA_DO_PRAZO_RECURSAL,
                'tpSolicitacao'         => 'DR',
                'tpRecurso'             => 1,
                'stAnalise'             => null,
                'stEstado'              => 1
            );

            $tbRecurso = new tbRecurso();
            $resultadoPesquisa = $tbRecurso->buscar(array('IdPRONAC = ?'=> $idPronac));

            RecursoDAO::cadastrar($dados);
            parent::message('A desistência do prazo recursal foi cadastrada com sucesso!', "consultardadosprojeto?idPronac=". Seguranca::encrypt($idPronac), "CONFIRM");
        } else {
            parent::message('Não foi possível cadastrar a desistência do prazo recursal!', "consultardadosprojeto?idPronac=". Seguranca::encrypt($idPronac), "ERROR");
        }
    }

    public function concordarDesistenciaRecursalModalAction()
    {
        $this->_helper->layout->disableLayout();
        $this->view->idPronac = $this->_request->getParam("idPronac");
    }
}
