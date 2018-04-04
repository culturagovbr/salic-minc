<?php

class DiligenciarProponenteController extends MinC_Controller_Action_Abstract
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
            // $PermissoesGrupo[] = 93;
            $PermissoesGrupo[] = 118;
            // $PermissoesGrupo[] = 119;
           // $PermissoesGrupo[] = 120;
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
    public function indexAction()
    {
        $auth              = Zend_Auth::getInstance(); // pega a autenticação
        $Usuario = new Autenticacao_Model_Usuario(); // objeto usuário
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];

        // caso o formulário seja enviado via post
        // atualiza a planilha
        if ($this->getRequest()->isPost()) {
            // recebe os dados via post
            $post = Zend_Registry::get('post');
            $idPronac      = $post->idPronac;
            $justificativa = $post->justificativa;
            $TipoAprovacao = $post->aprovacao;

            try {
                // valida os campos
                if (empty($justificativa) || $justificativa == "Digite a justificativa...") {
                    throw new Exception("Por favor, informe a justificativa!");
                } elseif (strlen($justificativa) < 20) {
                    throw new Exception("A justificativa deve conter no mï¿½nimo 20 caracteres!");
                } else {
                    // verifica se já está na pauta
                    $projetos = new Projetos();
                    $reuniao = new Reuniao();
                    $diligencia = new Diligencia();
                    $idReuniao = $reuniao->buscarReuniaoAberta();
                    $idReuniao = $idReuniao['idNrReuniao'];
                
                    $dadosDiligencia = array(
                                                                'idPronac' =>$idPronac,
                                                                'idTipoDiligencia' => 126,
                                                                'DtSolicitacao'=> date('Y-m-d H:i:s'),
                                                                'Solicitacao' => TratarString::escapeString($justificativa) ,
                                                                'idSolicitante'=> $idagente,
                                                                );
                    $gravarDiligiencia = $diligencia->inserirDiligencia($dadosDiligencia);
                    $dadosAltProjetos = array('Situacao'=>'C30');
                    $whereAltProjetos = "IdPRONAC = $idPronac";

                    $alterarSituacao = $projetos->alterar($dadosAltProjetos, $whereAltProjetos);
                    $this->_redirect('areadetrabalho/index');
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "diligenciarproponente/index", "ERROR");
            }
        } // fecha if
        else {
            // recebe os dados via get
            $idpronac   = $this->_request->getParam("idpronac");

            // dados via get

            try {
                // busca o pronac
                $pronac = ProjetoDAO::buscarPronac($idpronac);

                $buscarPronac = ProjetoDAO::buscar($pronac['pronac']);

                // validaï¿½ï¿½o
                if (empty($pronac)) {
                    throw new Exception("Por favor, clique no Pronac Aguardando Anï¿½lise!");
                } else {
                    $diligencia = new Diligencia();

                    $respostaDiligencia = $diligencia->buscar(array('idPronac = ?'=>$idpronac));

                    // manda os dados para a visão
                    //$this->view->buscar          = $buscar;

                    $this->view->pronac          = $buscarPronac;
                    $this->view->idpronac        = $idpronac;
                                        
                    $this->view->Respostas       = ($respostaDiligencia->count() > 0) ? $respostaDiligencia : false;
                    //-------------------------------------------------------------------------------------------------------------
                    $reuniao = new Reuniao();
                    $ConsultaReuniaoAberta = $reuniao->buscar(array("stEstado = ?" => 0));
                    if ($ConsultaReuniaoAberta->count() > 0) {
                        $ConsultaReuniaoAberta = $ConsultaReuniaoAberta->current()->toArray();
                        $this->view->dadosReuniaoPlenariaAtual = $ConsultaReuniaoAberta;
                        //---------------------------------------------------------------------------------------------------------------
                        $votantes = new Votante();
                        $exibirVotantes = $votantes->selecionarvotantes($ConsultaReuniaoAberta['idNrReuniao']);
                        if (count($exibirVotantes) > 0) {
                            foreach ($exibirVotantes as $votantes) {
                                $dadosVotante[] = $votantes->idAgente;
                            }
                            if (count($dadosVotante) > 0) {
                                if (in_array($idagente, $dadosVotante)) {
                                    $this->view->votante = true;
                                } else {
                                    $this->view->votante = false;
                                }
                            }
                        }
                    } else {
                        parent::message("Não existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "diligenciarproponente/index", "ERROR");
            }
        } // fecha else
    } // fecha método indexAction()
} // fecha class
