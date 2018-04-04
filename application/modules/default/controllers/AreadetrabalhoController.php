<?php

class AreadetrabalhoController extends MinC_Controller_Action_Abstract
{
    private $idAgente = 0;

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // titulo da pagina
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $Usuario = new Autenticacao_Model_Usuario(); // objeto usuario
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuario esteja autenticado
            // verifica as permissoes
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 118; // Componente da Comissao
            $PermissoesGrupo[] = 148;
            $PermissoesGrupo[] = 151;
            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo esta no array de permissoes
                parent::message("Voc&ecirc; n&atilde;o tem permiss&atilde;o para acessar essa &aacute;rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, orgaos e grupos do usuario (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

            // manda os dados para a visao
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usuario para a visao
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usuario para a visao
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o orgao ativo do usuario para a visao

            // pega o idAgente
            $this->idAgente = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
            $this->idAgente = ($this->idAgente) ? $this->idAgente["idAgente"] : 0;
        } 
        else { // caso o usuario nao esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init();
    }

    public function indexAction()
    {
        $this->view->title = "Salic - Sistema de Apoio &agrave;s Leis de Incentivo &agrave; Cultura"; // titulo da pagina
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $Usuario = new Autenticacao_Model_Usuario(); // objeto usuario
        $idagente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idagente'];
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
            parent::message("Nao existe CNIC aberta no momento. Favor aguardar!", "principal/index", "ERROR");
        }
        $distribuicao = new DistribuicaoProjetoComissao();
        $diligencia = new Diligencia();
        $pauta = new Pauta();

        $tblDistribuicao = new tbDistribuicaoProjetoComissao();

        $this->view->qtdfinalizados = null;
        $this->view->qtdfinalizadosreadequados = null;

        $tbanalise = $distribuicao->buscarProjetosDistribuidos($idagente, $ConsultaReuniaoAberta['idNrReuniao']);

        $analisados = 0;
        $naoanalisados = 0;
        foreach ($tbanalise as $result) {
            if ($result->idTipoAgente == 6) {
                $analisados++;
            } elseif ($result->idTipoAgente == 1) {
                $naoanalisados++;
            }
        }
        $this->view->analise = $tbanalise;
        $this->view->qtdanalisados = $analisados;
        $this->view->qtdnaoanalisados = $naoanalisados;

        $arrBusca = array();
        $arrBusca['Pr.Situacao = ?'] = "C30";
        $arrBusca['D.DtResposta IS NULL'] = "(?)";
        $arrBusca['D.idTipoDiligencia = ?'] = "126"; //diligencia na cnic
        $arrBusca['DPC.idAgente = ?'] = $idagente;
        $diligenciado = $diligencia->buscarProjetosDiligenciadosCNIC($arrBusca);
        $this->view->diligenciado = $diligenciado;


        $arrBusca = array();
        $arrBusca['Pr.Situacao IN (?)'] = array('C10','D01');
        $arrBusca['D.DtResposta IS NOT NULL'] = "(?)";
        $arrBusca['D.idTipoDiligencia = ?'] = "126"; //diligencia na cnic
        $arrBusca['DPC.idAgente = ?'] = $idagente;
        $diligenciadoresposta = $diligencia->buscarProjetosDiligenciadosCNIC($arrBusca);
        $this->view->diligenciarespondida = $diligenciadoresposta;

        $diligenciados = 0;
        $pronac=0;
        foreach ($diligenciado as $result) {
            if ($pronac != $result->PRONAC) {
                $diligenciados++;
            }
            $pronac = $result->PRONAC;
        }
        $this->view->qtddiligenciados = $diligenciados;

        $respondidos = 0;
        $pronac=0;
        foreach ($diligenciadoresposta as $result) {
            if ($pronac != $result->PRONAC) {
                $respondidos++;
            }
            $pronac = $result->PRONAC;
        }
        $this->view->qtdrespondidos = $respondidos;

        $arrBusca = array();
        $arrBusca['DPC.idAgente = ?'] = $idagente;
        $arrBusca['DPC.stDistribuicao = ?'] = 'A';
        $arrBusca['Pr.Situacao = ?'] = 'C10';
        $arrBusca['Pa.TipoParecer IN (?)'] = array('2','4');
        $arrBusca['Pa.stAtivo = ?'] = '1';
        //PROJETOS DE READEQUACAO
        $tbanalisereadequacao = $distribuicao->buscarProjetosDistribuidosReadequados($arrBusca);
        $this->view->analisereadequacao = $tbanalisereadequacao;

        $analisados = 0;
        $naoanalisados = 0;
        foreach ($tbanalisereadequacao as $result) {
            if ($result->idTipoAgente == 6) {
                $analisados++;
            } elseif ($result->idTipoAgente == 1) {
                $naoanalisados++;
            }
        }
        $this->view->qtdanalisadosreadequados = $analisados;
        $this->view->qtdnaoanalisadosreadequados = $naoanalisados;
    }



    /**
     * Metodo para efetuar a retirada de pauta
     */
    public function retirarDePautaAction()
    {
        // recebe os dados do formulario
        $post = Zend_Registry::get('post');
        $idPronac      = $post->idPronacPauta;
        $motivo        = $post->motivoPauta;
        $justificativa = $post->justificativaPauta;

        try {
            if (!empty($idPronac) && !empty($motivo) && !empty($justificativa)) {
                $tbRetirarDePauta = new tbRetirarDePauta();
                $dados = array(
                        'MotivoRetirada'    => $motivo
                        ,'dsJustificativa'  => $justificativa
                        ,'idPronac'         => $idPronac
                        ,'idAgenteEnvio'    => $this->idAgente
                        ,'dtEnvio'          => new Zend_Db_Expr('GETDATE()')
                        ,'tpAcao'           => 1 // retirado de pauta pelo componente da comissao
                        ,'stAtivo'          => 1);

                if ($tbRetirarDePauta->inserir($dados)) {
                    parent::message("Solicita&ccedil;&atilde;o enviada com sucesso!", "areadetrabalho/index", "CONFIRM");
                } else {
                    throw new Exception("Erro ao efetuar solicita&ccedil;&atilde;o!");
                }
            } else {
                throw new Exception("Todos os campos s&atilde;o de preenchimento obrigat&oacute;rio!");
            }
        } catch (Exception $e) {
            parent::message($e->getMessage(), "areadetrabalho/index", "ERROR");
        }
    } 

    /**
     * Metodo para efetuar o cancelamento da retirada de pauta
     */
    public function cancelarRetirarDePautaAction()
    {
        // recebe os dados do formulario
        $post = Zend_Registry::get('post');
        $idRetirarDePauta = $post->idRetirarDePauta;

        try {
            if (!empty($idRetirarDePauta)) {
                $tbRetirarDePauta = new tbRetirarDePauta();
                $dados = array(
                        'idAgenteEnvio'    => $this->idAgente
                        ,'dtEnvio'         => new Zend_Db_Expr('GETDATE()')
                        ,'tpAcao'          => 2 // cancelamento da retirada de pauta pelo componente da comissao
                        ,'stAtivo'         => 0);
                $where = array('idRetirarDePauta = ?' => $idRetirarDePauta);

                if ($tbRetirarDePauta->alterar($dados, $where)) {
                    parent::message("Solicita&ccedil;&atilde;o enviada com sucesso!", "areadetrabalho/index", "CONFIRM");
                } else {
                    throw new Exception("Erro ao efetuar cancelamento de solicita&ccedil;&atilde;o!");
                }
            } else {
                throw new Exception("Erro ao efetuar cancelamento de solicita&ccedil;&atilde;o!");
            }
        } catch (Exception $e) {
            parent::message($e->getMessage(), "areadetrabalho/index", "ERROR");
        }
    } 
}
