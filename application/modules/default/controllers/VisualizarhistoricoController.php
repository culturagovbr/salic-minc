<?php

class VisualizarhistoricoController extends MinC_Controller_Action_Abstract
{
    public function visualizarhistoricoAction()
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
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p&aacute;gina
        $auth              = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario           = new UsuarioDAO(); // objeto usu�rio
        $GrupoAtivo        = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usu�rio esteja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();
            //$PermissoesGrupo[] = 93;  // Coordenador de Parecerista
            //$PermissoesGrupo[] = 94;  // Parecerista
            $PermissoesGrupo[] = 103; // Coordenador de An&aacute;lise
            $PermissoesGrupo[] = 118; // Componente da comissão
            //$PermissoesGrupo[] = 119; // Presidente da Mesa
            //$PermissoesGrupo[] = 120; // Coordenador Administrativo CNIC
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
    } // fecha m�todo init()



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

        $auth     = Zend_Auth::getInstance(); // pega a autentica��o
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
                if (empty($mensagem) || $mensagem == 'Digite a Mensagem e depois selecione o Componente da comissão...') {
                    throw new Exception("Por favor, informe a Mensagem!");
                } elseif (empty($componenteComissao)) {
                    throw new Exception("Por favor, Selecione o Componente da comissão!");
                } else {
                    // realiza a inser&aacute;�o do hist�rico
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
    } // fecha m�todo indexAction()
} // fecha class
