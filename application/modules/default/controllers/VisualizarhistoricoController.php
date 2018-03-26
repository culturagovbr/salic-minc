<?php

class VisualizarhistoricoController extends MinC_Controller_Action_Abstract
{
    public function visualizarhistoricoAction()
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
            //$PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            //$PermissoesGrupo[] = 94;  // Parecerista
            $PermissoesGrupo[] = 103; // Coordenador de Análise
            $PermissoesGrupo[] = 118; // Componente da Comissão
            //$PermissoesGrupo[] = 119; // Presidente da Mesa
            //$PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
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
    } // fecha método init()



    public function indexAction()
    {
        $pronac = $this->_request->getParam("idpronac");

        $mens = new VisualizarhistoricoDAO();

        $tbprojeto = $mens->buscaProjeto($pronac);
        $this->view->projeto = $tbprojeto;

        $tbhistorico = $mens->buscaHistorico($pronac);
        $this->view->historico = $tbhistorico;

        $comboComponenteComissao = $mens->buscaConselheiro();
        $this->view->componentecomissao = $comboComponenteComissao;

        $auth     = Zend_Auth::getInstance(); // pega a autenticação
        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];
        //-------------------------------------------------------------------------------------------------------------
        //-------------------------------------------------------------------------------------------------------------
        $ConsultaReuniaoAberta = ReuniaoDAO::buscarReuniaoAberta();
        $this->view->dadosReuniaoPlenariaAtual = $ConsultaReuniaoAberta;
        //---------------------------------------------------------------------------------------------------------------
        $exibirVotantes = AtualizaReuniaoDAO::selecionarvotantes($ConsultaReuniaoAberta['idnrreuniao']);
        if (count($exibirVotantes) > 0) {
            foreach ($exibirVotantes as $votantes) {
                $dadosVotante[] = $votantes->idagente;
            }
            if (count($dadosVotante) > 0) {
                if (in_array($idagente, $dadosVotante)) {
                    $this->view->votante = 'ok';
                } else {
                    $this->view->votante = 'nao';
                }
            }
        }


        if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
            // recebe os dados via post
            $post               = Zend_Registry::get('post');
            $componenteComissao = $post->componenteComissao;
            $mensagem           = $post->descricao;
            $pronac             = $post->pronac;

            try {
                if (empty($mensagem) || $mensagem == 'Digite a Mensagem e depois selecione o Componente da Comissão...') {
                    throw new Exception("Por favor, informe a Mensagem!");
                } elseif (empty($componenteComissao)) {
                    throw new Exception("Por favor, Selecione o Componente da Comissão!");
                } else {
                    // realiza a inserção do histórico
                    $resultado = $mens->inserirMensagem($pronac, $componenteComissao, $mensagem);
                    if ($resultado) {
                        parent::message("Mensagem enviada com sucesso!", "visualizarhistorico/index?pronac=" . $pronac, "CONFIRM");
                    } else {
                        throw new Exception("Erro a enviar Mensagem!");
                    }
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "visualizarhistorico/index?pronac=" . $pronac, "ERROR");
            }
        } // fecha if
    } // fecha método indexAction()
} // fecha class
