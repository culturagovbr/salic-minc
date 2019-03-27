<?php

class DiligenciarProponenteController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o mï¿½todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio ï¿½s Leis de Incentivo ï¿½ Cultura"; // tï¿½tulo da pï¿½gina
        $auth = Zend_Auth::getInstance(); // pega a autenticaï¿½ï¿½o
        $Usuario = new UsuarioDAO(); // objeto usuï¿½rio
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessï¿½o com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuï¿½rio esteja autenticado
            // verifica as permissï¿½es
            $PermissoesGrupo = array();
            // $PermissoesGrupo[] = 93;
            $PermissoesGrupo[] = 118;
            // $PermissoesGrupo[] = 119;
           // $PermissoesGrupo[] = 120;
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo estï¿½ no array de permissï¿½es
                parent::message("Vocï¿½ nï¿½o tem permissï¿½o para acessar essa ï¿½rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgï¿½os e grupos do usuï¿½rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visï¿½o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuï¿½rio para a visï¿½o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuï¿½rio para a visï¿½o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuï¿½rio para a visï¿½o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o ï¿½rgï¿½o ativo do usuï¿½rio para a visï¿½o
        } // fecha if
        else { // caso o usuï¿½rio nï¿½o esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }
    public function indexAction()
    {
        $auth              = Zend_Auth::getInstance(); // pega a autenticação
        $Usuario = new Autenticacao_Model_DbTable_Usuario(); // objeto usuário
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];

        // caso o formulï¿½rio seja enviado via post
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
                    // verifica se jï¿½ estï¿½ na pauta
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
                    $this->redirect('areadetrabalho/index');
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

                    // manda os dados para a visï¿½o
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
                        parent::message("Nï¿½o existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
                    }
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "diligenciarproponente/index", "ERROR");
            }
        } // fecha else
    } // fecha mï¿½todo indexAction()
} // fecha class
