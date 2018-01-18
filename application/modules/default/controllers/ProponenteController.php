<?php

class ProponenteController extends MinC_Controller_Action_Abstract
{
    private $intTamPag = 300;

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // titulo da pagina
        $auth = Zend_Auth::getInstance(); // pega a autenticao
        $Usuario = new UsuarioDAO(); // objeto usuario
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuario esteja autenticado
            // verifica as permissoes
            $PermissoesGrupo = array();
            // $PermissoesGrupo[] = 93;
            $PermissoesGrupo[] = 118;
            // $PermissoesGrupo[] = 119;
            // $PermissoesGrupo[] = 120;
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgaos e grupos do usuario (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visao
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuario para a visao
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuario para a visao
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o orgao ativo do usuario para a visao
        } // fecha if
        else { // caso o usuario nao esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    // fecha matodo init()
    public function indexAction()
    {
        $idpronac = $this->_request->getParam("idpronac");

        $geral = new ProponenteDAO();
        $tblProjetos = new Projetos();

        $tbdados = $geral->buscarDadosProponente($idpronac);
        $this->view->dados = $tbdados;

        $tbemail = $geral->buscarEmail($idpronac);
        $this->view->email = $tbemail;

        $tbtelefone = $geral->buscarTelefone($idpronac);
        $this->view->telefone = $tbtelefone;

        $tbDirigentes = $geral->buscarDirigentes($idpronac);
        $this->view->dirigentes = $tbDirigentes;

        $this->view->CgcCpf = $tbdados[0]->CgcCpf;

        /*$tbarquivados = $geral->buscarArquivados($idpronac);
        $this->view->arquivados = $tbarquivados;

        $tbinativos = $geral->buscarInativos($tbdados[0]->CgcCpf);
        $this->view->inativos = $tbinativos;

        $tbativos = $geral->buscarAtivos($tbdados[0]->CgcCpf);
        $this->view->ativos = $tbativos;*/

        $auth = Zend_Auth::getInstance(); // pega a autenticao
        $Usuario = new Autenticacao_Model_Usuario(); // objeto usuario
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];
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
            parent::message("N&atilde;o existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
        }
    }

    public function cadastrarpropostaAction()
    {
    }

    public function listarProjetosProponenteAction($fetchMode = Zend_DB::FETCH_OBJ)
    {
        $this->_helper->layout->disableLayout();
        $post = Zend_Registry::get('post');
        $this->intTamPag = 30;
        $cpfCnpj = $this->_request->getParam("CgcCpf");

        $pag = 1;
        if (isset($post->pag)) {
            $pag = $post->pag;
        }
        if (isset($post->tamPag)) {
            $this->intTamPag = $post->tamPag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $arrBusca = array();
        $arrBusca['p.CgcCpf = ?'] = $cpfCnpj;

        $tblProjetos = new Projetos();
        $total = $tblProjetos->buscarProjetosProponente($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) {
            $fim = $total;
        }

        $ordem = array("7",//stEstado (Arquivado / Ativo)
                       "8",//p.Situacao (Situacao Projeto)
                       //"11",//a.Descricao (Area)
                       //"10",//s.Descricao (Segmento)
                       //"1" //NomeProjeto
                    );
        if (!empty($post->ordenacao)) {
            $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}";
        }

        $rsProjetos = $tblProjetos->buscarProjetosProponente($arrBusca, $ordem, $tamanho, $inicio);

        $arrProjetos = array();
        $arrValores = array();
        foreach ($rsProjetos as $projeto) {
            $arrProjetos[$projeto->stEstado][$projeto->Situacao][]=$projeto;
            $arrValores[$projeto->stEstado]['vlSolicitado'][] = $projeto->Solicitado;
            $arrValores[$projeto->stEstado]['vlAprovado'][] = $projeto->Aprovado;
            $arrValores[$projeto->stEstado]['vlCaptado'][] = $projeto->Captado;
        }
        $this->view->CgcCpf = $cpfCnpj;
        $this->view->registros = $arrProjetos;
        $this->view->valores = $arrValores;
        $this->view->pag = $pag;
        $this->view->total = $total;
        $this->view->inicio = ($inicio+1);
        $this->view->fim = $fim;
        $this->view->totalPag = $totalPag;
        $this->view->parametrosBusca = $_POST;
    }
}
