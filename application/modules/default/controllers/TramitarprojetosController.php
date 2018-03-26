<?php

/**
 * Description of TramitarprojetosController
 *
 * @author gabriela
 */
class TramitarprojetosController extends MinC_Controller_Action_Abstract
{
    private $getIdUsuario = 0;
    private $getIdGrupo   = 0;
    private $getIdOrgao   = 0;
    private $intTamPag = 10;

    public function init()
    {
        $this->view->title = "Salic - Sistema de Apoio �s Leis de Incentivo � Cultura"; // t�tulo da p�gina
        /*$auth = Zend_Auth::getInstance(); // pega a autentica��o
        $Usuario = new UsuarioDAO(); // objeto usu�rio*/
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo

        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 97; //Gestor Salic
        $PermissoesGrupo[] = 99; //Acompanhamento
        $PermissoesGrupo[] = 103; //Coordenador de An�lise
        $PermissoesGrupo[] = 104; //Protocolo - (Envio / Recebimento)
        $PermissoesGrupo[] = 91; //Protocolo - Recebimento
        $PermissoesGrupo[] = 109; //Arquivo
        $PermissoesGrupo[] = 128; //T�cnico de Portaria
        $PermissoesGrupo[] = 121; //T�cnico de Acompanhamento
        $PermissoesGrupo[] = 105; //FCRB
        $PermissoesGrupo[] = 106; //Coordenador SAV
        $PermissoesGrupo[] = 113; //Coordenador de Arquivo
        $PermissoesGrupo[] = 99; //Acompanhamento
        $PermissoesGrupo[] = 102; //Administrtivo
        $PermissoesGrupo[] = 115; //Atendimento Representa��es
        $PermissoesGrupo[] = 101; //Interc�mbio
        $PermissoesGrupo[] = 114; //Coordenador de Editais
        $PermissoesGrupo[] = 100; //Presta��o de Contas
        $PermissoesGrupo[] = 124; //T�cnico de Presta��o de Contas
        $PermissoesGrupo[] = 122; //Coordenador de Acompanhamento

        parent::perfil(1, $PermissoesGrupo); // perfil novo salic

        // pega o idAgente do usu�rio logado
        $auth = Zend_Auth::getInstance(); // pega a autentica��o

        $this->getIdUsuario = UsuarioDAO::getIdUsuario($auth->getIdentity()->usu_codigo);
        //$this->getIdUsuario = ($this->getIdUsuario) ? $this->getIdUsuario["idAgente"] : 0;
        $this->getIdUsuario = $auth->getIdentity()->usu_codigo;
        $this->getIdGrupo   = $GrupoAtivo->codGrupo;
        $this->getIdOrgao   = $auth->getIdentity()->usu_orgao;
        /* ========== FIM PERFIL ==========*/

        /*if ($auth->hasIdentity()) { // caso o usu�rio estja autenticado
            // verifica as permiss�es
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 97; //Gestor Salic
            $PermissoesGrupo[] = 99; //Acompanhamento
            $PermissoesGrupo[] = 103; //Coordenador de An�lise
            $PermissoesGrupo[] = 104; //Protocolo - (Envio / Recebimento)
            $PermissoesGrupo[] = 91; //Protocolo - Recebimento
            $PermissoesGrupo[] = 109; //Arquivo
            $PermissoesGrupo[] = 128; //T�cnico de Portaria
            $PermissoesGrupo[] = 121; //T�cnico de Acompanhamento
            $PermissoesGrupo[] = 105; //FCRB
            $PermissoesGrupo[] = 106; //Coordenador SAV
            $PermissoesGrupo[] = 113; //Coordenador de Arquivo
            $PermissoesGrupo[] = 99; //Acompanhamento
            $PermissoesGrupo[] = 102; //Administrtivo
            $PermissoesGrupo[] = 115; //Atendimento Representa��es
            $PermissoesGrupo[] = 101; //Interc�mbio
            $PermissoesGrupo[] = 114; //Coordenador de Editais
            $PermissoesGrupo[] = 100; //Presta��o de Contas
            $PermissoesGrupo[] = 124; //T�cnico de Presta��o de Contas
            $PermissoesGrupo[] = 122; //Coordenador de Acompanhamento

           parent::perfil(1, $PermissoesGrupo);

            if (!in_array($GrupoAtivo->codGrupo, $PermissoesGrupo)) { // verifica se o grupo ativo est� no array de permiss�es
                parent::message("Voc� n�o tem permiss�o para acessar essa �rea do sistema!", "principal/index", "ALERT");
            }

            // pega as unidades autorizadas, org�os e grupos do usu�rio (pega todos os grupos)
            $grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
            // manda os dados para a vis�o
            $this->view->usuario = $auth->getIdentity(); // manda os dados do usu�rio para a vis�o
            $this->view->arrayGrupos = $grupos; // manda todos os grupos do usu�rio para a vis�o
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usu�rio para a vis�o
            $this->view->orgaoAtivo = $GrupoAtivo->codOrgao; // manda o �rg�o ativo do usu�rio para a vis�o



        //} // fecha if
        else {
            // caso o usu�rio n�o esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }*/

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;
        $this->_redirect("tramitarprojetos/despacharprojetos");

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->view->codGrupo = $codGrupo;

        /*         * *************************************************************** */
        $orgaos = new Orgaos();
        $historicodocumento = new HistoricoDocumento();

        /*         * *************************************************************** */

        $todosDestinos = $orgaos->pesquisarTodosOrgaos();
        $this->view->TodosDestinos = $todosDestinos;

        $destino = $historicodocumento->pesquisarOrgaosPorAcao(1, 4, $idusuario);
        $this->view->Destino = $destino;
        //
        $despacho = $historicodocumento->projetosDespachados(array(1, 4));
        $this->view->Despacho = $despacho;


        if ($this->_request->getParam("Destino")) {
            $idDestino = $this->_request->getParam("Destino");
            $despacho = $historicodocumento->projetosDespachados(array(1, 4), $idDestino);
            $lote = new Lote();
            $insereLote = $lote->inserirLote(array('dtLote' => date('Y-m-d H:i:s')));
            $idLoteAtual = $insereLote; // Retorno do ultimo Lote Inserido

            $acaoAlterada = 2;
            $recusado = false;
            foreach ($despacho as $despachoResu) {
                $despachos = $despachoResu->despacho;
                $idPronac = $despachoResu->idPronac;
                if ($despachoResu->Acao == 4) {
                    $recusado = true;
                } else {
                    $dados = array('stEstado' => 0);
                    $where = "idPronac =  $idPronac and stEstado = 1";
                    $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                    /*                     * ****************************************************************************************** */
                    $dadosInserir = array(
                        'idPronac' => $idPronac,
                        'idUnidade' => $despachoResu->idDestino,
                        'dtTramitacaoEnvio' => date('Y-m-d H:i:s'),
//                        'idUsuarioEmissor' => $despachoResu->idUsuarioEmissor,
                        'idUsuarioEmissor' => $idusuario,
//                        'idUsuarioReceptor' => $despachoResu->idUsuarioReceptor,
                        'idUsuarioReceptor' => $idusuario,
                        'idLote' => $idLoteAtual,
                        'Acao' => $acaoAlterada,
                        'stEstado' => 1,
                        'meDespacho' => $despachos
                    );

                    $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
                }
            }
            if ($recusado) {
                parent::message("Projetos com a situa&ccedil;&atilde;o RECUSADO n&atilde;o foram tramitados!", "/tramitarprojetos/despacharprojetos", "ALERT");
            } else {
                parent::message("O projeto foi enviado com sucesso!", "/tramitarprojetos/despacharprojetos", "CONFIRM");
            }
        }
        if (isset($_GET['pronac'])) {
            $pronac = $_GET['pronac'];
            $acao = $_GET['acao'];
            $setProjeto = TramitarprojetosDAO::setProjeto($pronac, $acao);
            $this->view->setProjeto = $setProjeto;
        }
    }

    public function buscaprojetoAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        $this->view->idorgao = $idorgao;
        /*         * *************************************************************** */

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $post = Zend_Registry::get('post');
        $pronac = $post->pronac;

        $buscaProjeto = TramitarprojetosDAO::buscaProjeto($pronac);
        $orgao = $buscaProjeto[0]->Orgao;
        $NomeProjeto = $buscaProjeto[0]->NomeProjeto;

        if (isset($_POST['msg']) and $_POST['msg'] == 'ok') {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
//
            if ($orgao == $codOrgao) {
                $dadosProjeto['Orgao'] = utf8_encode($buscaProjeto[0]->Orgao);
                $dadosProjeto['Processo'] = utf8_encode($buscaProjeto[0]->Processo);
                $dadosProjeto['Sigla'] = utf8_encode($buscaProjeto[0]->Sigla);
                $dadosProjeto['NomeProjeto'] = utf8_encode($buscaProjeto[0]->NomeProjeto);

                $jsonEncode = json_encode($dadosProjeto);

                //echo $jsonEncode;
                $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosProjeto));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }

            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function buscaprojetodesarquivadoAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        $this->view->idorgao = $idorgao;
        /*         * *************************************************************** */

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $post = Zend_Registry::get('post');
        $pronac = $post->pronac;

        $buscaProjeto = TramitarprojetosDAO::buscaProjeto($pronac);
        $orgao = $buscaProjeto[0]->Orgao;
        $NomeProjeto = $buscaProjeto[0]->NomeProjeto;

        if (isset($_POST['msg']) and $_POST['msg'] == 'ok') {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
//
            if ($orgao != $codOrgao) {
                $dadosProjeto['Orgao'] = utf8_encode($buscaProjeto[0]->Orgao);
                $dadosProjeto['Processo'] = utf8_encode($buscaProjeto[0]->Processo);
                $dadosProjeto['Sigla'] = utf8_encode($buscaProjeto[0]->Sigla);
                $dadosProjeto['NomeProjeto'] = utf8_encode($buscaProjeto[0]->NomeProjeto);

                $jsonEncode = json_encode($dadosProjeto);

//                               echo $jsonEncode;
                $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosProjeto));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }

            $this->_helper->viewRenderer->setNoRender(true);
        }
    }


    public function buscaprojetodespacharAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        //$codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        $this->view->idorgao = $idorgao;
        /*         * *************************************************************** */

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $post = Zend_Registry::get('post');
        $pronac = $post->pronac;

        $tblProjetos = new Projetos();
        $dadoProjeto = $tblProjetos->buscar(array("AnoProjeto = ?"=>substr($pronac, 0, 2), "Sequencial = ?"=>substr($pronac, 2)))->current();

        //VERIFICANDO SE O PRONAC PESQUISADO JA ESTA CADASTRADO PARA ESTE ORGAO
        $historicodocumento = new HistoricoDocumento();
        $despachos = $historicodocumento->projetosDespachadosListagem(array(1,2), null, null, $dadoProjeto->IdPRONAC);
        $blnJaExisteProjeto = false;
        foreach ($despachos as $despacho) {
            if ($despacho->Pronac == $pronac && $despacho->idOrigem == $codOrgao) {
                $blnJaExisteProjeto = true;
            }
        }
        //VERIFICANDO SE O PRONAC PESQUISADO J� FOI ENVIADO A ALGUM ORGAO
        $despachados = $historicodocumento->projetosDespachados(array(1,2), null, null, $dadoProjeto->IdPRONAC);
        if (count($despachados) > 0) {
            $blnJaExisteProjeto = true;
        }

        $buscaProjeto = TramitarprojetosDAO::buscaProjeto($pronac);
        if (isset($_POST['msg']) and $_POST['msg'] == 'ok') {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            //
            if ($buscaProjeto && !$blnJaExisteProjeto) {
                $orgao = $buscaProjeto[0]->Orgao;
                $NomeProjeto = $buscaProjeto[0]->NomeProjeto;

                $dadosProjeto['Orgao'] = utf8_encode($buscaProjeto[0]->Orgao);
                $dadosProjeto['Processo'] = utf8_encode(FuncoesDoBanco::fnFormataProcesso($buscaProjeto[0]->Processo));
                $dadosProjeto['Sigla'] = utf8_encode($buscaProjeto[0]->Sigla);
                $dadosProjeto['NomeProjeto'] = utf8_encode($buscaProjeto[0]->NomeProjeto);

                $jsonEncode = json_encode($dadosProjeto);

                //echo $jsonEncode;
                $this->_helper->json(array('resposta'=>true,'conteudo'=>$dadosProjeto));
            } else {
                $this->_helper->json(array('resposta'=>false));
            }
        }

        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function consultarprojetosAction()
    {
        $orgaos = new Orgaos();
        $todosDestinos = $orgaos->pesquisarTodosOrgaos();
        $this->view->TodosDestinos = $todosDestinos;
    }

    public function despacharprojetosAction()
    {
        /** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // instancia da autenticacao
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;

        $tramitacoesRepetidas = TramitarprojetosDAO::verificaTramitacoesRepetidas();
        if (count($tramitacoesRepetidas)>0) {
            $mens = "Verificar os projetos abaixo na tabela tbHistoricoDocumento.<br /><br />";
            foreach ($tramitacoesRepetidas as $t) {
                $mens .= "idPronac: ".$t->idPronac.'<br /><br />';
                $mens .= "SELECT idHistorico,idPronac,idDocumento,idOrigem,idUnidade,idLote,Acao,stEstado,dsJustificativa<br />FROM SAC.dbo.tbHistoricoDocumento WHERE idPronac = $t->idPronac AND idDocumento = 0 ORDER BY 1 DESC";
                $mens .= '<br /><br />';
            }
            $email = 'jefferson.silva@cultura.gov.br';
            $assunto = 'Tramita&ccedil;&atilde;o Projetos - Pronac Repetido';

            $perfil = 'PerfilGrupoPRONAC';
            //  EmailDAO::enviarEmail($email, $assunto, $mens, $perfil);
        }

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->view->codorgaoverifica = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        /* **************************************************************** */
        $orgaos = new Orgaos();
        $orgaossigla = $orgaos->buscar(array("Codigo =?" =>$codOrgao));
        $this->view->siglaOrgao = $orgaossigla[0]->Sigla;
        $historicodocumento = new HistoricoDocumento();

        /* **************************************************************** */
        $todosDestinos = $orgaos->pesquisarTodosOrgaos();
        $this->view->TodosDestinos = $todosDestinos;
        $destino = $historicodocumento->pesquisarOrgaosPorAcao(1, 4, $idusuario, $codOrgao);
        $anexo = $historicodocumento->pesquisarOrgaosPorAcao(null, 6, $idusuario, $codOrgao);
        $this->view->Destino = $destino;

        $despacho = $historicodocumento->projetosDespachadosListagem(array(1,4), null, null, null, $idusuario);
        $this->view->Despacho = $despacho;

        $verificaPendencia = 0;
        $verificaEnviado = 0;

        if ($this->_request->getParam("Destino")) {
            $idDestino = $this->_request->getParam("Destino");
            $despachos = $historicodocumento->projetosDespachados(array(1,4), $idDestino, null, null, $codOrgao);

            $lote = new Lote();
            $insereLote = $lote->inserirLote(array('dtLote' => date('Y-m-d H:i:s')));
            $idLoteAtual = $insereLote; // Retorno do ultimo Lote Inserido

            $acaoAlterada = 2;
            $recusado = false;
            $existeDocumento = false;
            foreach ($despachos as $despachoResu) {
                $despachos = $despachoResu->despacho;
                $idPronac = $despachoResu->idPronac;
                if ($despachoResu->Acao == 4) {
                    $recusado = true;
                } else {
                    $cadastrado = true;
                    $dados = array('stEstado' => 0);
                    $where = "idPronac = $idPronac and stEstado = 1 and idDocumento = 0";

                    $verificar = TramitarprojetosDAO::verificaHistoricoDocumento($idPronac, 6);
                    if (count($verificar) > 0) {
                        $acao = $verificar[0]->Acao;
                        if ($acao == 4) {
                            $verificaPendencia = 1;
                        } else {
                            $verificaEnviado = 1;
                            $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                        }
                    } else {
                        $verificaEnviado = 1;
                        $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                    }
                    /* ******************************************************************************************* */
                    $dadosInserir = array(
                        'idPronac' => $idPronac,
                        'idDocumento' => 0,
                        'idUnidade' => $despachoResu->idDestino,
                        'idOrigem' => $codOrgao,
                        'dtTramitacaoEnvio' => date('Y-m-d H:i:s'),
                        'idUsuarioEmissor' => $idusuario,
                        'idUsuarioReceptor' => null,
                        'idLote' => $idLoteAtual,
                        'Acao' => $acaoAlterada,
                        'stEstado' => 1,
                        'meDespacho' => $despachos
                    );
                    $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
                }
            }

            if ($recusado && !$cadastrado) {
                parent::message("Projetos com a situa��o RECUSADO n�o foram tramitados!", "/tramitarprojetos/enviarprojetos", "ALERT");
            } elseif ($recusado && $cadastrado) {
                parent::message("Projetos com a situa��o RECUSADO n�o foram tramitados!", "/tramitarprojetos/imprimirguia?idLote=".$idLoteAtual, "ALERT");
            } elseif ($verificaPendencia == 1 && $verificaEnviado == 0) {
                parent::message("Projeto enviado com sucesso!", "/tramitarprojetos/imprimirguia?idLote=".$idLoteAtual, "CONFIRM");
//                    parent::message($msgEnviado .  $msgPendencia, "/tramitarprojetos/despacharprojetos", "ALERT");
            } elseif ($verificaPendencia == 0 && $verificaEnviado == 1) {
                parent::message("Projeto enviado com sucesso!", "/tramitarprojetos/imprimirguia?idLote=".$idLoteAtual, "CONFIRM");
//                    parent::message($msgEnviado .  $msgPendencia, "/tramitarprojetos/imprimirguia?s=s&idLote=".$idLoteAtual, "ALERT");
            } elseif ($verificaPendencia == 1 && $verificaEnviado == 1) {
                parent::message("Projeto enviado com sucesso!", "/tramitarprojetos/imprimirguia?idLote=".$idLoteAtual, "CONFIRM");
//                    parent::message($msgEnviado . " " . $msgPendencia, "/tramitarprojetos/imprimirguia?s=s&idLote=".$idLoteAtual, "ALERT");
            } else {
                //parent::message("Projeto enviado com Sucesso!", "tramitarprojetos/enviarprojetos", "CONFIRM");
                parent::message("Projeto enviado com sucesso!", "/tramitarprojetos/imprimirguia?idLote=".$idLoteAtual, "CONFIRM");
            }
        }
        if (isset($_GET['pronac'])) {
            $pronac = $_GET['pronac'];
            $acao = $_GET['acao'];
            $setProjeto = TramitarprojetosDAO::setProjeto($pronac, $acao);
            $this->view->setProjeto = $setProjeto;
        }
    }

    public function receberprojetosAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->grupoAtivo = $codGrupo;
        $this->view->idUsuarioLogado = $idusuario;
        $this->view->idorgao = $idorgao;

        $orgaos = new Orgaos();
        $projetos = new Projetos();
        $historicodocumento = new HistoricoDocumento();

        /* ================ PROJETOS ENVIADOS (2) ====================*/
        $destino = $historicodocumento->pesquisarOrgaosPorDestinoRecebimento(2, 2, $idusuario, $codOrgao);
        $this->view->Destino = $destino;

        // ========== IN�CIO PAGINA��O ==========
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($destino); // dados a serem paginados

        // p�gina atual e quantidade de �tens por p�gina
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(5);
        $this->view->Destino = $paginator;
        $this->view->qtdDoc    = count($destino); // quantidade
        // ========== FIM PAGINA��O ==========

        /* =================== FIM PROJETOS RECEBIDOS ====================*/
        if (isset($_POST['idH'])) {
            $idPronac = $_POST['idP'];
            $idHistorico = $_POST['idH'];

            $historicoDocumentos = new HistoricoDocumento();
            $dados = array('stEstado' => 0);
            $where = "idPronac =  $idPronac and stEstado = 1 and Acao = 3";
            $alterar = $historicoDocumentos->alterarHistoricoDocumento($dados, $where);

            $inserir = 0;
            $acao = 4;
            $stEstado = 1;
            if ($alterar) {
                $inserir = TramitarprojetosDAO::recusarProjeto($idPronac, $acao, $codOrgao, null);
            }
            if ($inserir) {
                parent::message("Projeto recusado com sucesso!", "tramitarprojetos/receberprojetos?projetoRecebido=true", "CONFIRM");
            }
        }

        if (isset($_POST['justificativa'])) {
            $idDestino = $_POST['idDes'];
            $idPronac = $_POST['idPro'];
            $justificativa = $_POST['justificativa'];
            $idLote = $_POST['lote'];
            $idOrigem = $_POST['idOrigem'];

            $acaoAlterada = 4;
            $historicoDocumentos = new HistoricoDocumento();
            $historico = $historicoDocumentos->buscar(array('idPronac = ?' => $idPronac, 'stEstado = ?' => 1));

            if (count($historico)>0) {
                if ($historico[0]->Acao == 3) {
                    $dados = array('Orgao' => $idOrigem);
                    $where = "IdPRONAC = $idPronac";
                    $atualizarProjeto = $projetos->alterarProjetos($dados, $where);
                }
            }

            foreach ($historico as $hit) {
                $dtEnvio = $hit->dtTramitacaoEnvio;
                $dtRecebido = $hit->dtTramitacaoRecebida;
                $meDespacho = $hit->meDespacho;
                $idEmissor = $hit->idUsuarioEmissor;
                $idReceptor = $hit->idUsuarioReceptor;
            }

            $dados = array('stEstado' => 0);
            $where = "idPronac = $idPronac and stEstado = 1 and idDocumento = 0";
            $alterar = $historicoDocumentos->alterarHistoricoDocumento($dados, $where);

            $dadosInserir = array(
                    'idPronac' => $idPronac,
                    'idDocumento' => 0,
                    'idUnidade' => $idDestino,
                    'dtTramitacaoEnvio' => $dtEnvio,
                    'dtTramitacaoRecebida' => $dtRecebido,
                    'idUsuarioEmissor' => $idEmissor,
                    'idOrigem' => $idOrigem,
                    'idUsuarioReceptor' => $idReceptor,
                    'idLote' => $idLote,
                    'Acao' => $acaoAlterada,
                    'stEstado' => 1,
                    'meDespacho' => $meDespacho,
                    'dsJustificativa' => $justificativa
            );
            $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
            parent::message("O recebimento foi cancelado!", "tramitarprojetos/receberprojetos?projetoRecebido=true", "CONFIRM");
        }

        /* ================ PARA RECEBER OS PROJETOS ====================*/
        if ($this->_request->getParam("Destino")) {
            $idDestino = $this->_request->getParam("Destino");
            $idLote = $this->_request->getParam("Lote");
            $acaoAlterada = 3;
            $despacho = $historicodocumento->projetosDespachados(array(2, 4), $idDestino, $idLote);

            foreach ($despacho as $despachoResu) {
                $despachos = $despachoResu->despacho;
                $idPronac = $despachoResu->idPronac;
                $dados = array('stEstado' => 0);
                $where = "idPronac =  $idPronac and stEstado = 1";
                $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                /*                 * ****************************************************************************************** */
                $data = data::dataAmericana($despachoResu->dtEnvio);
                $dadosInserir = array(
                        'idPronac' => $idPronac,
                        'idDocumento' => 0,
                        'idUnidade' => $despachoResu->idDestino,
                        'dtTramitacaoEnvio' => $data,
                        'dtTramitacaoRecebida' => date('Y-m-d H:i:s.m'),
                        'idUsuarioEmissor' => $despachoResu->idUsuarioEmissor,
                        'idOrigem' => $despachoResu->idOrigem,
                        'idUsuarioReceptor' => $idusuario,
                        'idLote' => $idLote,
                        'Acao' => $acaoAlterada,
                        'stEstado' => 1,
                        'meDespacho' => $despachos
                );
                $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);

                $dados = array('Orgao' => $idDestino);
                $where = "IdPRONAC = $idPronac";
                $atualizarProjeto = $projetos->alterarProjetos($dados, $where);
            }

            parent::message("Projeto recebido com sucesso!", "tramitarprojetos/receberprojetos?projetoRecebido=true", "CONFIRM");
        }
        /*         * *************************************************************** */

        /* ================ PARA ARQUIVAR OS PROJETOS ====================*/
        if (isset($_POST['inicial'])) {
            $idPronac = $_POST['idPro2'];
            $pronac = $_POST['Pro2'];
            $idDestino = $_POST['idDes2'];
            $idLote = $_POST['lote2'];
            $idOrigem = $_POST['idOrigem2'];

            $cxInicio = $_POST['inicial'];
            $cxFinal = $_POST['final'];

            $busca2 = TramitarprojetosDAO::buscaProjetoUnidade($idPronac); //Verifica se o projeto j� tem registro na tabela tbArquivamento

            if ($busca2) {
                foreach ($busca2 as $b) {
                    $stAcao = $b->stAcao;
                    $idArquivamento = $b->idArquivamento;
                }
                if ($stAcao == 0) {
                    parent::message("O projeto j� se encontra arquivado nesta unidade!", "tramitarprojetos/receberprojetos?projetoRecebido=true", "ALERT");
                } else {
                    $despacho = $historicodocumento->projetosDespachados(array(2), $idDestino);
                    foreach ($despacho as $despachoResu) {
                        $despachos = $despachoResu->despacho;
                        $idPronac = $despachoResu->idPronac;
                        $dados = array('stEstado' => 0);
                        $where = "idPronac =  $idPronac and stEstado = 1";
                        $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                        /*                 * ****************************************************************************************** */
                        $data = data::dataAmericana($despachoResu->dtEnvio);
                        $dadosInserir = array(
                                'idPronac' => $idPronac,
                                'idDocumento' => 0,
                                'idUnidade' => $despachoResu->idDestino,
                                'dtTramitacaoEnvio' => $data,
                                'dtTramitacaoRecebida' => date('Y-m-d H:i:s.m'),
                                'idUsuarioEmissor' => $idusuario,
                                'idOrigem' => $idOrigem,
                                'idUsuarioReceptor' => $idusuario,
                                'idLote' => $idLote,
                                'Acao' => $acaoAlterada,
                                'stEstado' => 1,
                                'meDespacho' => $despachos
                        );
                        $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
                    }
                    TramitarprojetosDAO::alterarStatusArquivamento($idPronac);
                    $stAcao = 0;

                    TramitarprojetosDAO::arquivarProjeto($idPronac, $stAcao, $cxInicio, $cxFinal, $idusuario, $idArquivamento);
                    parent::message("Projeto arquivado com sucesso!", "tramitarprojetos/receberprojetos?projetoRecebido=true", "CONFIRM");
                }
            } else {
                $acaoAlterada = 3;
                $despacho = $historicodocumento->projetosDespachados(array(2), $idDestino, $idLote, $idPronac);

                foreach ($despacho as $despachoResu) {
                    $despachos = $despachoResu->despacho;
                    $idPronac = $despachoResu->idPronac;
                    $dados = array('stEstado' => 0);
                    $where = "idPronac =  $idPronac and stEstado = 1";
                    $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                    /*                 * ****************************************************************************************** */
                    $data = data::dataAmericana($despachoResu->dtEnvio);
                    $dadosInserir = array(
                            'idPronac' => $idPronac,
                            'idDocumento' => 0,
                            'idUnidade' => $despachoResu->idDestino,
                            'dtTramitacaoEnvio' => $data,
                            'dtTramitacaoRecebida' => date('Y-m-d H:i:s.m'),
                            'idUsuarioEmissor' => $idusuario,
                            'idUsuarioReceptor' => $idusuario,
                            'idLote' => $idLote,
                            'Acao' => $acaoAlterada,
                            'stEstado' => 1,
                            'meDespacho' => $despachos
                    );
                    $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
                    $dados = array('Orgao' => $idDestino);
                    $where = "IdPRONAC = $idPronac";
                    $atualizarProjeto = $projetos->alterarProjetos($dados, $where);
                }

                $busca1 = TramitarprojetosDAO::buscaProjetoExistente($idPronac); //Busca o Projeto na Tabela Projetos
                if (($busca1)) {
                    $stAcao = 0;
                    TramitarprojetosDAO::arquivarProjeto($idPronac, $stAcao, $cxInicio, $cxFinal, $idusuario, null, 1);
                    parent::message("Projeto arquivado com sucesso!", "tramitarprojetos/receberprojetos?projetoRecebido=true", "CONFIRM");
                } else {
                    parent::message("O projeto n�o se encontra na DGI/CGRL/COAL/DCA, transa��o cancelada.", "tramitarprojetos/receberprojetos?projetoRecebido=true", "ALERT");
                }
            }//FIM ELSE $busca2
        }

        /*         * *************************************************************** */
        $this->view->projetoRecebido = false;
        if (isset($_REQUEST['projetoRecebido']) && $_REQUEST['projetoRecebido'] == 'true') {
            $this->view->projetoRecebido = true;
        }
    }

    public function despacharprojAction()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $destino = TramitarprojetosDAO::pesquisarDestinos(1);
        $this->view->Destino = $destino;

        $todosDestinos = TramitarprojetosDAO::pesquisarTodosDestinos();
        $this->view->TodosDestinos = $todosDestinos;

        $despacho = TramitarprojetosDAO::projetosDespachados(1, 4);
        $this->view->Despacho = $despacho;

        $db= Zend_Db_Table::getDefaultAdapter();

        if (isset($_POST)) {
            if ($_POST['projeto']) {
                try {
                    //C�digo novo - Jefferson

                    //VERIFICAR A EXISTENCIA DO PROJETO
                    $Projetos = new Projetos();
                    $dadosProjeto = $Projetos->buscarIdPronac($_POST['pronac']);
                    if (empty($dadosProjeto)) {
                        parent::message("Projeto inexistente.", "tramitarprojetos/despacharprojetos", "ALERT");
                    }

                    //CHECAR SE H� DOCUMENTO SEM ANEXACAO PARA O PROJETO
                    $whereHistorio = array();
                    $whereHistorio['idPronac = ?'] = $dadosProjeto->IdPRONAC;
                    $whereHistorio['idDocumento != ?'] = 0;
                    $whereHistorio['Acao < ?'] = 4;
                    $whereHistorio['stEstado = ?'] = 1;
                    $tbHistoricoDocumento = new tbHistoricoDocumento();
                    $dadosTbHistorico = $tbHistoricoDocumento->buscar($whereHistorio);
                    if (count($dadosTbHistorico) > 0) {
                        parent::message("O projeto n�o pode ser despachado, porque existe documento a ser juntado antes da sua tramita��o para outra unidade.", "tramitarprojetos/despacharprojetos", "ALERT");
                    }

                    //CHECAR SE O PROJETO NAO ESTA DESPACHADO PARA OUTRA UNIDADE
                    $whereHistorio = array();
                    $whereHistorio['idPronac = ?'] = $dadosProjeto->IdPRONAC;
                    $whereHistorio['idDocumento = ?'] = 0;
                    $whereHistorio['Acao < ?'] = 3;
                    $whereHistorio['stEstado = ?'] = 1;
                    $dadosTbHistorico = $tbHistoricoDocumento->buscar($whereHistorio);
                    if (count($dadosTbHistorico) > 0) {
                        parent::message("O projeto n�o pode ser despachado novamente, transa��o cancelada.", "tramitarprojetos/despacharprojetos", "ALERT");
                    }

                    //SE EXISTIR ALGUM REGISTRO ATIVO, ELE TRANSFORMA PARA HISTORICO
                    $whereHistorio = array();
                    $whereHistorio['idPronac = ?'] = $dadosProjeto->IdPRONAC;
                    $whereHistorio['idDocumento = ?'] = 0;
                    $whereHistorio['stEstado = ?'] = 1;
                    $atualizaHistorico = $tbHistoricoDocumento->buscar($whereHistorio);
                    foreach ($atualizaHistorico as $a) {
                        $dadosAlteracao = array();
                        $dadosAlteracao['stEstado'] = 0;
                        $whereAlteracao = "idHistorico = $a->idHistorico";
                        $atualizaHistorico = $tbHistoricoDocumento->alterar($dadosAlteracao, $whereAlteracao);
                    }

                    //CADASTRA UM REGISTRO DE TRAMITACAO
                    $dados = array(
                        'idPronac' => $dadosProjeto->IdPRONAC,
                        'idDocumento' => 0,
                        'idOrigem' => $codOrgao,
                        'idUnidade' => $_POST['idunidade'],
                        'dtTramitacaoEnvio' => new Zend_Db_Expr('GETDATE()'),
                        'idUsuarioEmissor' => $idusuario,
                        'meDespacho' => $_POST['despacho'],
                        'Acao' => 1,
                        'stEstado' => 1
                    );
                    if ($codOrgao == $_POST['idunidade']) {
                        parent::message("O projeto n�o pode ser despachado para sua pr�pria unidade.", "tramitarprojetos/despacharprojetos", "ALERT");
                    } else {
                        $tbHistoricoDocumento->inserir($dados);
                    }
                    parent::message("Cadastro realizado com sucesso!", "tramitarprojetos/despacharprojetos", "CONFIRM");
                } // fecha try
                catch (Exception $e) {
                    parent::message("Error: ".$e->getMessage(), "gerenciarpareceres/index", "ERROR");
                    die($e->getMessage());
                }
            }
        }
    }

    public function editarprojetosAction()
    {
        if (isset($_POST)) {
            $pronac = $_POST['pronac'];
            $destino = $_POST['destino'];
            $despacho = $_POST['despacho'];
            $buscaDados = TramitarprojetosDAO::buscarDadosPronac($pronac);

            if (count($buscaDados)>0) {
                $idPronac = $buscaDados[0]->IdPRONAC;
            } else {
                parent::message("Projeto n�o encontrado!", "tramitarprojetos/despacharprojetos", "ALERT");
            }

            $tbHistoricoDocumento = new tbHistoricoDocumento();
            $dados = array(
                'idUnidade' => $destino,
                'meDespacho' => $despacho
            );
            $where = 'idPronac = '.$idPronac;
            $atualizaDados = $tbHistoricoDocumento->update($dados, $where);
            if ($atualizaDados) {
                parent::message("Altera��o realizada com sucesso!", "tramitarprojetos/despacharprojetos", "CONFIRM");
            } else {
                parent::message("N�o foi poss�vel fazer a altera��o!", "tramitarprojetos/despacharprojetos", "ERROR");
            }
        }
        parent::message("N�o foi poss�vel fazer nenhuma a��o!", "tramitarprojetos/despacharprojetos", "ALERT");
    }

    public function enviarprojetosAction()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;


        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        $this->view->idorgao = $idorgao;
        /*         * *************************************************************** */
        $orgaos = new Orgaos();
        $historicodocumento = new HistoricoDocumento();

        /*         * *************************************************************** */
        $todosDestinos = $orgaos->pesquisarTodosOrgaos();
        $this->view->TodosDestinos = $todosDestinos;

        $destino = $historicodocumento->pesquisarOrgaosPorAcao(1, null, $idusuario, $codOrgao);
        $this->view->Destino = $destino;
        //
        $despacho = $historicodocumento->projetosDespachados(array(1));
        $this->view->Despacho = $despacho;

        $x = 0;
        $destinos = $historicodocumento->pesquisarOrgaosPorAcao(2, 2, $idusuario, $codOrgao);
        $this->view->Dest = $destinos;
        foreach ($destinos as $dest) {
            $dp[] = $historicodocumento->projetosDespachados(array(2), $dest->idDestino, $dest->lote);
            if ($dp) {
                $x = 1;
            }
        }
        if ($x) {
            foreach ($dp as $desp) {
                foreach ($desp as $teste) {
                    $novoDesp[] = $teste;
                }
            }
            $this->view->Desp = $novoDesp;
        }


        if (isset($_POST['justificativa'])) {
            $idDestino = $_POST['idDes'];
            $idPronac = $_POST['idPro'];
            $justificativa = $_POST['justificativa'];
            $idLote = $_POST['idLote'];
            $idOrigem= $_POST['idOrigem'];

            $historicodocumento = new HistoricoDocumento();
            $despachos = $historicodocumento->projetosDespachados(array(2), $idDestino, null, $idPronac);

            foreach ($despachos as $desp) {
                $meDespacho = $desp['despacho'];
                $dtEnvio    = !empty($desp['dtEnvio'])    ? trim(Data::dataAmericana($desp['dtEnvio']))    : null;
                $dtRecebido = !empty($desp['dtRecebida']) ? trim(Data::dataAmericana($desp['dtRecebida'])) : null;
                $dtEnvio    = $dtEnvio == '--'    ? null : $dtEnvio;
                $dtRecebido = $dtRecebido == '--' ? null : $dtRecebido;
                $idEmissor  = $desp['idUsuarioEmissor'];
            }

            $acaoAlterada = 4;
            $historicoDocumentos = new HistoricoDocumento();
            $dados = array('stEstado' => 0);
            $where = "idPronac = $idPronac and stEstado = 1 and idDocumento = 0";
            $alterar = $historicoDocumentos->alterarHistoricoDocumento($dados, $where);

            if ($alterar) {
                $dadosInserir = array(
                            'idPronac' => $idPronac,
                            'idUnidade' => $idDestino,
                            'dtTramitacaoEnvio' => $dtEnvio,
                            'dtTramitacaoRecebida' => $dtRecebido,
                            'idUsuarioEmissor' => $idEmissor,
//	                        'idUsuarioEmissor' => $idusuario,
                                'idOrigem' => $idOrigem,
    //                        'idUsuarioReceptor' => $despachoResu->idUsuarioReceptor,
                            'idUsuarioReceptor' => $idusuario,
                            'idLote' => $idLote,
                            'Acao' => $acaoAlterada,
                            'stEstado' => 1,
                            'dsJustificativa' => $justificativa,
                            'meDespacho' => $meDespacho
                        );
                //				xd($dadosInserir);
                $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
            }
            if ($inserir) {
                parent::message("Projeto cancelado com sucesso!", "tramitarprojetos/enviarprojetos", "CONFIRM");
            }
        }


        if ($this->_request->getParam("Destino")) {
            $idDestino = $this->_request->getParam("Destino");
            $despacho = $historicodocumento->projetosDespachados(array(1, 4), $idDestino);
            $lote = new Lote();
            $insereLote = $lote->inserirLote(array('dtLote' => date('Y-m-d H:i:s')));
            $idLoteAtual = $insereLote; // Retorno do ultimo Lote Inserido

            $acaoAlterada = 2;
            $recusado = false;
            foreach ($despacho as $despachoResu) {
                $despachos = $despachoResu->despacho;
                $idPronac = $despachoResu->idPronac;
                if ($despachoResu->Acao == 4) {
                    $recusado = true;
                } else {
                    $dados = array('stEstado' => 0);
                    $where = "idPronac =  $idPronac and stEstado = 1";
                    $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                    /*                     * ****************************************************************************************** */
                    $dadosInserir = array(
                        'idPronac' => $idPronac,
                        'idUnidade' => $despachoResu->idDestino,
                        'dtTramitacaoEnvio' => date('Y-m-d H:i:s'),
//                        'idUsuarioEmissor' => $despachoResu->idUsuarioEmissor,
                        'idUsuarioEmissor' => $idusuario,
//                        'idUsuarioReceptor' => $despachoResu->idUsuarioReceptor,
                        'idUsuarioReceptor' => $idusuario,
                        'idLote' => $idLoteAtual,
                        'Acao' => $acaoAlterada,
                        'stEstado' => 1,
                        'meDespacho' => $despachos
                    );

                    $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
                }
            }
            if ($recusado) {
                parent::message("Projetos com a situa��o RECUSADO n�o foram tramitados!", "/tramitarprojetos/enviarprojetos", "ALERT");
            } else {
                parent::message("Projeto enviado com sucesso!", "/tramitarprojetos/enviarprojetos", "CONFIRM");
            }
        }

        /*         * *************************************************************** */
        $this->view->projetoEnviado = false;
        if (isset($_REQUEST['projetoEnviado']) && $_REQUEST['projetoEnviado'] == 'true') {
            $this->view->projetoEnviado = true;
        }
    }

    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $pdf = new PDF($_POST['html'], 'pdf');
        $pdf->gerarRelatorio();
    }

    public function guiasAction()
    {

        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        /* *************************************************************** */

        /* *************************************************************** */
        $orgaos = new Orgaos();
        $todosDestinos = $orgaos->pesquisarTodosOrgaos();
        $this->view->TodosDestinos = $todosDestinos;

        $where = array();
        $where['h.stEstado = ?'] = 1;
        $where['(h.idDocumento is NULL OR h.idDocumento = 0)'] = '';
        $where['h.Acao = ?'] = 2;
        $where['h.idOrigem = ?'] = $codOrgao;
        $where['h.idUsuarioEmissor = ?'] = $idusuario;
        $order = array(8); //idLote

        $tbHistoricoDocumento = new tbHistoricoDocumento();
        $this->view->registros = $tbHistoricoDocumento->consultarTramitacoes($where, $order);
    }

    public function recusarprojetosAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
        $this->view->idorgao = $idorgao;
        /*         * *************************************************************** */

        if ($this->_request->getParam("Destino")) {
            $idPronac = $this->_request->getParam("idPronac");
            $idDestino = $this->_request->getParam("Destino");
            $historicodocumento = new HistoricoDocumento();
            $despacho = $historicodocumento->projetosDespachados(array(2), $idDestino, $idPronac)->current()->toArray();
            $idLote = $despacho['idLote'];
            $meDespacho = $despacho['despacho'];

            $acaoAlterada = 4;
            $historicoDocumentos = new HistoricoDocumento();
            $dados = array('stEstado' => 0);
            $where = "idPronac = $idPronac and stEstado = 1 and idDocumento = 0";
            $alterar = $historicoDocumentos->alterarHistoricoDocumento($dados, $where);

            $dadosInserir = array(
                        'idPronac' => $idPronac,
                        'idUnidade' => $idDestino,
                        'dtTramitacaoEnvio' => date('Y-m-d H:i:s'),
//                        'idUsuarioEmissor' => $despachoResu->idUsuarioEmissor,
                        'idUsuarioEmissor' => $idusuario,
//                        'idUsuarioReceptor' => $despachoResu->idUsuarioReceptor,
                        'idUsuarioReceptor' => $idusuario,
                        'idLote' => $idLote,
                        'Acao' => $acaoAlterada,
                        'stEstado' => 1,
                        'meDespacho' => $meDespacho
                    );
            $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
            parent::message("Projeto recusado com sucesso!", "tramitarprojetos/receberprojetos", "CONFIRM");
        }
    }

    public function localizarprojetosAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $this->view->orgaoLogado = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->view->usuarioLogado = $this->getIdUsuario;

        //DEFINE PARAMETROS DE ORDENACAO / QTDE. REG POR PAG. / PAGINACAO
        if ($this->_request->getParam("qtde")) {
            $this->intTamPag = $this->_request->getParam("qtde");
        }
        $order = array();

        //==== parametro de ordenacao  ======//
        if ($this->_request->getParam("ordem")) {
            $ordem = $this->_request->getParam("ordem");
            if ($ordem == "ASC") {
                $novaOrdem = "DESC";
            } else {
                $novaOrdem = "ASC";
            }
        } else {
            $ordem = "ASC";
            $novaOrdem = "ASC";
        }

        //==== campo de ordenacao  ======//
        if ($this->_request->getParam("campo")) {
            $campo = $this->_request->getParam("campo");
            $order = array($campo." ".$ordem);
            $ordenacao = "&campo=".$campo."&ordem=".$ordem;
        } else {
            $campo = null;
            $order = array(19); //Pronac
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['h.stEstado = ?'] = 1;
        $where['(h.idDocumento is NULL OR h.idDocumento = 0)'] = '';

        if (isset($get->estado) && !empty($get->estado)) {
            $where['h.Acao = ?'] = $this->view->estado = $get->estado;
        }
        if (isset($get->pronac) && !empty($get->pronac)) {
            $where['p.AnoProjeto+p.Sequencial = ?'] = $this->view->pronac = $get->pronac;
        }
        if (isset($get->origem) && !empty($get->origem)) {
            $where['h.idOrigem = ?'] = $this->view->origem = $get->origem;
            $where['h.idUnidade = ?'] = $codOrgao;
        }
        if (isset($get->destino) && !empty($get->destino)) {
            $where['h.idOrigem = ?'] = $codOrgao;
            $where['h.idUnidade = ?'] = $this->view->destino = $get->destino;
        }
        if (isset($get->dtEnvioI)  && !empty($get->dtEnvioI)) {
            $this->view->tipo_dtEnvio = $get->tipo_dtEnvio;
            $this->view->dtEnvioI = $get->dtEnvioI;
            $this->view->dtEnvioF = $get->dtEnvioF;
            $d1 = Data::dataAmericana($get->dtEnvioI);
            if ($get->tipo_dtEnvio == 1) {
                $where["h.dtTramitacaoEnvio BETWEEN '$d1' AND '$d1 23:59:59.999'"] = '';
            } elseif ($get->tipo_dtEnvio == 2) {
                $d2 = Data::dataAmericana($get->dtEnvioF);
                $where["h.dtTramitacaoEnvio BETWEEN '$d1' AND '$d2'"] = '';
            }
        }
        if (isset($get->dtRecebidoI)  && !empty($get->dtRecebidoI)) {
            $this->view->tipo_dtRecebida = $get->tipo_dtRecebida;
            $this->view->dtRecebidoI = $get->dtRecebidoI;
            $this->view->dtRecebidoF = $get->dtRecebidoF;
            $d1 = Data::dataAmericana($get->dtRecebidoI);
            if ($get->tipo_dtRecebida == 1) {
                $where["h.dtTramitacaoRecebida BETWEEN '$d1' AND '$d1 23:59:59.999'"] = '';
            } elseif ($get->tipo_dtRecebida == 2) {
                $d2 = Data::dataAmericana($get->dtRecebidoF);
                $where["h.dtTramitacaoRecebida BETWEEN '$d1' AND '$d2'"] = '';
            }
        }
        if (isset($get->lote) && !empty($get->lote)) {
            $where['h.idLote = ?'] = $this->view->lote = $get->lote;
        }

        $tbHistoricoDocumento = new tbHistoricoDocumento();
        $total = $tbHistoricoDocumento->consultarTramitacoes($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbHistoricoDocumento->consultarTramitacoes($where, $order, $tamanho, $inicio);
        $paginacao = array(
                "pag"=>$pag,
                "qtde"=>$this->intTamPag,
                "campo"=>$campo,
                "ordem"=>$ordem,
                "ordenacao"=>$ordenacao,
                "novaOrdem"=>$novaOrdem,
                "total"=>$total,
                "inicio"=>($inicio+1),
                "fim"=>$fim,
                "totalPag"=>$totalPag,
                "Itenspag"=>$this->intTamPag,
                "tamanho"=>$tamanho
         );

        $this->view->paginacao     = $paginacao;
        $this->view->qtdRegistros  = $total;
        $this->view->dados         = $busca;
        $this->view->intTamPag     = $this->intTamPag;
    }

    public function solicitarcancelamentoenvioprojetosAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        /*         * *************************************************************** */

        $orgaos = new Orgaos();
        $historicodocumento = new HistoricoDocumento();
        $todosDestinos = $orgaos->pesquisarTodosOrgaos();
        $this->view->TodosDestinos = $todosDestinos;

//        $destino = $historicodocumento->pesquisarOrgaosPorAcao(2, $idusuario);
        $destino = $historicodocumento->pesquisarOrgaosPorAcao(2);

        $this->view->Destino = $destino;
    }

    public function solicitacoesAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->view->codOrgao = $codOrgao;
        $this->view->grupoAtivo = $codGrupo;
        $this->view->idUsuarioLogado = $idusuario;
        $this->view->idorgao = $idorgao;
        /*         * *************************************************************** */

//        $historicodocumento = new HistoricoDocumento();
//        $despachos = $historicodocumento->projetosDespachados(array(0), $idDestino, null, $idPronac)->current()->toArray();

        $cancelOrgaos = $cancelamento = TramitarprojetosDAO::buscarCancelOrgao(null);
        $this->view->cancelOrgao = $cancelOrgaos;

        $cancelamento = TramitarprojetosDAO::buscarCancelamento(null);
        $this->view->cancel = $cancelamento;

        $arquivados = TramitarprojetosDAO::buscarDesarquivar();
        $this->view->Arquivados = $arquivados;


        if (isset($_POST['idHistorico'])) {
            $idPronac = $_POST['idPronac'];
            $solicitacao = $_POST['solicitacao'];

            if ($solicitacao == 1) {
                $historicoDocumentos = new HistoricoDocumento();
                $dados = array('Acao' => 1);
                $where = "idPronac =  $idPronac and stEstado = 1 and Acao = 0";
                $alterar = $historicoDocumentos->alterarHistoricoDocumento($dados, $where);

                parent::message("Envio cancelado com Sucesso!", "tramitarprojetos/solicitacoes", "CONFIRM");
            } else {
                if ($solicitacao == 2) {
                    $historicoDocumentos = new HistoricoDocumento();
                    $dados = array('Acao' => 2);
                    $where = "idPronac =  $idPronac and stEstado = 1 and Acao = 0";
                    $alterar = $historicoDocumentos->alterarHistoricoDocumento($dados, $where);

                    parent::message("Solicita��o Cancelada!", "tramitarprojetos/solicitacoes", "CONFIRM");
                }
            }
        }

        if (isset($_POST['idArq'])) {
            $idArquivamento = $_POST['idArq'];
            $idPronac = $_POST['idPro'];

            TramitarprojetosDAO::alterarStatusArquivamento($idPronac);
            $stAcao = 1;
            TramitarprojetosDAO::arquivarProjeto($idPronac, $stAcao, null, null, $idusuario, $idArquivamento);
            parent::message("Projeto desarquivado com sucesso!", "tramitarprojetos/solicitacoes", "CONFIRM");
        }
    }

    public function arquivarAction()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $orgaos = new Orgaos();
        $orgaossigla = $orgaos->buscar(array("Codigo =?" =>$codOrgao));
        $this->view->siglaOrgao = $orgaossigla[0]->Sigla;
        $this->view->codOrgao = $codOrgao;
        $historicodocumento = new HistoricoDocumento();
        $buscaprojeto = new Projetos();

        $lote = new Lote();
        $insereLote = $lote->inserirLote(array('dtLote' => date('Y-m-d H:i:s')));
        $idLote = $insereLote; // Retorno do ultimo Lote Inserido
        $acaoAlterada = 3;

        if (isset($_POST['pronac'])) {
            $pronac = $_POST['pronac'];
            $cxInicio = $_POST['inicial'];
            $cxFinal = $_POST['final'];
            $idDestino = 290;

            $buscaDados = TramitarprojetosDAO::buscarDadosPronac($pronac);

            foreach ($buscaDados as $dados) {
                $idPronac = $dados->IdPRONAC;
            }

            $busca2 = TramitarprojetosDAO::buscaProjetoUnidade($idPronac); //Verifica se o projeto j� tem registro na tabela tbArquivamento

            if ($busca2) {
                foreach ($busca2 as $b) {
                    $stAcao = $b->stAcao;
                    $idArquivamento = $b->idArquivamento;
                }
                if ($stAcao == 0) {
                    parent::message("O projeto j� se encontra arquivado nesta unidade!", "tramitarprojetos/arquivar", "ALERT");
                } else {
                    $despacho = $historicodocumento->projetosDespachados(array(), $idDestino, null, $idPronac);
                    foreach ($despacho as $despachoResu) {
                        $despachos = $despachoResu->despacho;
                        $idPronac = $despachoResu->idPronac;
                        $dtEnvio = $despachoResu->dtEnvio;
                        $dados = array('stEstado' => 0);
                        $where = "idPronac =  $idPronac and stEstado = 1";
                        $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                        /*                 * ****************************************************************************************** */
                        $data = data::dataAmericana($dtEnvio);
                        $dadosInserir = array(
                                'idPronac' => $idPronac,
                                'idUnidade' => $idDestino,
                                'dtTramitacaoEnvio' => $data,
                                   'dtTramitacaoRecebida' => date('Y-m-d H:i:s'),
        //                        'idUsuarioEmissor' => $despachoResu->idUsuarioEmissor,
                                'idUsuarioEmissor' => $idusuario,
        //                        'idUsuarioReceptor' => $despachoResu->idUsuarioReceptor,
                                'idUsuarioReceptor' => $idusuario,
                                'idLote' => $idLote,
                                'Acao' => $acaoAlterada,
                                'stEstado' => 1,
                                'meDespacho' => $despachos
                            );
                        $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
                    }
                    TramitarprojetosDAO::alterarStatusArquivamento($idPronac);
                    $stAcao = 0;

                    TramitarprojetosDAO::arquivarProjeto($idPronac, $stAcao, $cxInicio, $cxFinal, $idusuario, $idArquivamento);
                    parent::message("Projeto arquivado com sucesso!", "tramitarprojetos/arquivar", "CONFIRM");
                }
            } else {
                $tramitacao = $historicodocumento->projetosDespachados(array(), null, null, $idPronac);
                if (count($tramitacao)) {    //Se tiver Historico de tramita��o
                    foreach ($tramitacao as $despachoResu) {
                        $despachos = $despachoResu->despacho;
                        $idPronac = $despachoResu->idPronac;
                        $dtEnvio = $despachoResu->dtEnvio;
                        $dados = array('stEstado' => 0);
                        $where = "idPronac =  $idPronac and stEstado = 1";
                        $atualizarHistoricoDocumento = $historicodocumento->alterarHistoricoDocumento($dados, $where);
                        /*                 * ****************************************************************************************** */
                        $data = data::dataAmericana($dtEnvio);
                        $dadosInserir = array(
                                'idPronac' => $idPronac,
                                'idUnidade' => $idDestino,
                                'dtTramitacaoEnvio' => $data,
                                   'dtTramitacaoRecebida' => date('Y-m-d H:i:s'),
        //                        'idUsuarioEmissor' => $despachoResu->idUsuarioEmissor,
                                'idUsuarioEmissor' => $idusuario,
        //                        'idUsuarioReceptor' => $despachoResu->idUsuarioReceptor,
                                'idUsuarioReceptor' => $idusuario,
                                'idLote' => $idLote,
                                'Acao' => $acaoAlterada,
                                'stEstado' => 1,
                                'meDespacho' => 'Para arquivamento'
                            );
                        $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
                        $dados = array('Orgao' => $idDestino);
                        $where = "IdPRONAC = $idPronac";
                        $atualizarProjeto = $buscaprojeto->alterarProjetos($dados, $where);
                    }

                    $busca1 = TramitarprojetosDAO::buscaProjetoExistente($idPronac); //Busca o Projeto na Tabela Projetos
                    if (($busca1)) {
                        //				$situacao = 'K00';
                        //				$providenciaTomada = 'Projeto arquivamento';
                        //	            TramitarprojetosDAO::alterarSituacao($situacao, $providenciaTomada, $idPronac);
                        TramitarprojetosDAO::alterarStatusArquivamento($idPronac);
                        $stAcao = 0;

                        TramitarprojetosDAO::arquivarProjeto($idPronac, $stAcao, $cxInicio, $cxFinal, $idusuario, null, 1);
                        parent::message("Projeto arquivado com sucesso!", "tramitarprojetos/arquivar", "CONFIRM");
                    } else {
                        parent::message("O projeto n�o se encontra na DGI/CGRL/COAL/DCA, transa��o cancelada.", "tramitarprojetos/arquivar", "ALERT");
                    }
                }//FIM IF TRAMITA��O
                else {
                    $despacho = $buscaprojeto->buscarTodosDadosProjeto($idPronac);

                    $dadosInserir = array(
                            'idPronac' => $idPronac,
                            'idUnidade' => $idDestino,
                            'dtTramitacaoEnvio' => date('Y-m-d H:i:s'),
                               'dtTramitacaoRecebida' => date('Y-m-d H:i:s'),
    //                        'idUsuarioEmissor' => $despachoResu->idUsuarioEmissor,
                            'idUsuarioEmissor' => $idusuario,
    //                        'idUsuarioReceptor' => $despachoResu->idUsuarioReceptor,
                            'idUsuarioReceptor' => $idusuario,
                            'idLote' => $idLote,
                            'Acao' => $acaoAlterada,
                            'stEstado' => 1,
                            'meDespacho' => 'Para arquivamento'
                        );
                    $inserir = $historicodocumento->inserirHistoricoDocumento($dadosInserir);
                    $dados = array('Orgao' => $idDestino);
                    $where = "IdPRONAC = $idPronac";
                    $atualizarProjeto = $buscaprojeto->alterarProjetos($dados, $where);

                    $busca1 = TramitarprojetosDAO::buscaProjetoExistente($idPronac); //Busca o Projeto na Tabela Projetos
                    if (($busca1)) {
                        //				$situacao = 'K00';
                        //				$providenciaTomada = 'Projeto arquivamento';
                        //	            TramitarprojetosDAO::alterarSituacao($situacao, $providenciaTomada, $idPronac);
                        TramitarprojetosDAO::alterarStatusArquivamento($idPronac);
                        $stAcao = 0;

                        TramitarprojetosDAO::arquivarProjeto($idPronac, $stAcao, $cxInicio, $cxFinal, $idusuario, null, 1);
                        parent::message("Projeto arquivado com sucesso!", "tramitarprojetos/arquivar", "CONFIRM");
                    } else {
                        parent::message("O projeto n�o se encontra na DGI/CGRL/COAL/DCA, transa��o cancelada.", "tramitarprojetos/arquivar", "ALERT");
                    }
                }
            }//FIM ELSE $busca2
        }
    }

    public function desarquivarAction()
    {
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o
        $this->view->codOrgao = $codOrgao;
        $buscaprojeto = new Projetos();

        if (isset($_POST['pronac'])) {
            $pronac = $_POST['pronac'];

            $buscaDados = TramitarprojetosDAO::buscarDadosPronac($pronac);

            foreach ($buscaDados as $dados) {
                $idPronac = $dados->IdPRONAC;
                $OrgaoOrigem = $dados->OrgaoOrigem;
            }
            $busca = TramitarprojetosDAO::buscaProjetoUnidade($idPronac);

            foreach ($busca as $b) {
                $stAcao = $b->stAcao;
                $idArquivamento = $b->idArquivamento;
            }
            if ($stAcao == 1) {
                parent::message("O projeto N�O se encontra Arquivado nesta Unidade.!", "tramitarprojetos/desarquivar", "ALERT");
            } else {
                if (($busca) && ($stAcao == 0)) {
                    TramitarprojetosDAO::alterarStatusArquivamento($idPronac);
                    $stAcaoA = 1;
                    TramitarprojetosDAO::arquivarProjeto($idPronac, $stAcaoA, null, null, $idusuario, $idArquivamento);
                    if ($OrgaoOrigem != 0) {
                        $dados = array('Orgao' => $OrgaoOrigem);
                    } else {
                        $dados = array('Orgao' => $codOrgao);
                    }
                    $where = "IdPRONAC = $idPronac";
                    $atualizarProjeto = $buscaprojeto->alterarProjetos($dados, $where);
                    parent::message("Projeto desarquivado com sucesso!", "tramitarprojetos/desarquivar", "CONFIRM");
                } else {
                    parent::message("O projeto n�o se encontra na DGI/CGRL/COAL/DCA, transa��o cancelada.", "tramitarprojetos/desarquivar", "ALERT");
                }
            }
        }
    }

    public function cancelarrecebimentosAction()
    {
    }

    public function excluirAction()
    {
        $historicoDocumentos = new HistoricoDocumento();
        $idPronac = $_GET['idPronac'];
        $dados = array('stEstado' => 0);
        $where = "idPronac = $idPronac and stEstado = 1";
        $alterar = $historicoDocumentos->alterarHistoricoDocumento($dados, $where);
        if ($alterar) {
            parent::message("Exclus�o realizada com sucesso!", "tramitarprojetos/despacharprojetos", "CONFIRM");
        }
    }

    public function consultarprojetosarquivadosAction()
    {
    }

    public function projetosarquivadosAction()
    {
        /** Usuario Logado *********************************************** */
        $auth = Zend_Auth::getInstance(); // instancia da autentica��o
        $idusuario = $this->getIdUsuario;
        $idorgao = $this->getIdOrgao;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $codGrupo = $GrupoAtivo->codGrupo; //  Grupo ativo na sess�o
        $codOrgao = $GrupoAtivo->codOrgao; //  �rg�o ativo na sess�o

        $this->view->codOrgao = $codOrgao;
        $this->view->grupoAtivo = $codGrupo;
        $this->view->idUsuarioLogado = $idusuario;
        $this->view->idorgao = $idorgao;
        /* *************************************************************** */

        function formatadata($data, $v)
        {
            $dia = substr($data, 0, 2);
            $mes = substr($data, 3, 2);
            $ano = substr($data, 6, 4);
            if ($v == 1) {
                $dataformatada = $ano . "/" . $mes . "/" . $dia . " 00:00:00.000";
            } else {
                $dataformatada = $ano . "/" . $mes . "/" . $dia . " 23:59:59.999";
            }
            return $dataformatada;
        }
        $post = Zend_Registry::get('post');

        $pronac = null;
        if (isset($post->pronac)) {
            if (!empty($_SESSION['pronac'])) {
                if ($post->pronac != $_SESSION['pronac']) {
                    $_SESSION['pronac'] = $post->pronac;
                    $pronac = $post->pronac;
                } else {
                    $pronac = $_SESSION['pronac'];
                }
            } else {
                $pronac = $post->pronac;
                $_SESSION['pronac'] = $pronac;
            }
        } elseif (!empty($_SESSION['pronac'])) {
            $pronac = $_SESSION['pronac'];
        }

        $tipo_nome = null;
        if (isset($post->tipo_nome)) {
            if (!empty($_SESSION['tipo_nome'])) {
                if ($post->tipo_nome != $_SESSION['tipo_nome']) {
                    $_SESSION['tipo_nome'] = $post->tipo_nome;
                    $tipo_nome = $post->tipo_nome;
                } else {
                    $tipo_nome = $_SESSION['tipo_nome'];
                }
            } else {
                $tipo_nome = (int) $post->tipo_nome;
                $_SESSION['tipo_nome'] = $tipo_nome;
            }
        } elseif (!empty($_SESSION['tipo_nome'])) {
            $tipo_nome = $_SESSION['tipo_nome'];
        }

        $nome = null;
        if (isset($post->nome)) {
            if (!empty($_SESSION['nome'])) {
                if ($post->nome != $_SESSION['nome']) {
                    $_SESSION['nome'] = $post->nome;
                    $nome = $post->nome;
                } else {
                    $nome = $_SESSION['nome'];
                }
            } else {
                $nome = $post->nome;
                $_SESSION['nome'] = $nome;
            }
        } elseif (!empty($_SESSION['nome'])) {
            $nome = $_SESSION['nome'];
        }

        $tipo_processo = null;
        if (isset($post->tipo_processo)) {
            if (!empty($_SESSION['tipo_processo'])) {
                if ($post->tipo_processo != $_SESSION['tipo_processo']) {
                    $_SESSION['tipo_processo'] = $post->tipo_processo;
                    $tipo_processo = $post->tipo_processo;
                } else {
                    $tipo_processo = $_SESSION['tipo_processo'];
                }
            } else {
                $tipo_processo = (int) $post->tipo_processo;
                $_SESSION['tipo_processo'] = $tipo_processo;
            }
        } elseif (!empty($_SESSION['tipo_processo'])) {
            $tipo_processo = $_SESSION['tipo_processo'];
        }

        $processo = null;
        if (isset($post->processo)) {
            if (!empty($_SESSION['processo'])) {
                if ($post->processo != $_SESSION['processo']) {
                    $_SESSION['processo'] = $post->processo;
                    $processo = $post->processo;
                } else {
                    $processo = $_SESSION['processo'];
                }
            } else {
                $processo = $post->processo;
                $_SESSION['processo'] = $processo;
            }
        } elseif (!empty($_SESSION['processo'])) {
            $processo = $_SESSION['processo'];
        }

        $tipo_dtArquivo = null;
        if (isset($post->tipo_dtArquivo)) {
            if (!empty($_SESSION['tipo_dtArquivo'])) {
                if ($post->tipo_dtArquivo != $_SESSION['tipo_dtArquivo']) {
                    $_SESSION['tipo_dtArquivo'] = $post->tipo_dtArquivo;
                    $tipo_dtArquivo = $post->tipo_dtArquivo;
                } else {
                    $tipo_dtArquivo = $_SESSION['tipo_dtArquivo'];
                }
            } else {
                $tipo_dtArquivo = (int) $post->tipo_dtArquivo;
                $_SESSION['tipo_dtArquivo'] = $tipo_dtArquivo;
            }
        } elseif (!empty($_SESSION['tipo_dtArquivo'])) {
            $tipo_dtArquivo = $_SESSION['tipo_dtArquivo'];
        }

        $dtArquivI = null;
        $dtArquivInull = null;

        if (isset($post->dtArquivI)) {
            $dtArquivI = formatadata($post->dtArquivI, 1);
            $dtArquivInull = formatadata($post->dtArquivI, 2);
            if (!empty($_SESSION['dtArquivI'])) {
                if ($dtArquivI != $_SESSION['dtArquivI']) {
                    $_SESSION['dtArquivI'] = $dtArquivI;
                    $dtArquivI =  $dtArquivI;
                } else {
                    $dtArquivI =  $_SESSION['dtArquivI'];
                }
            } else {
                $dtArquivI = formatadata($post->dtArquivI, 1);
                $dtArquivInull = formatadata($post->dtArquivI, 2);
                $_SESSION['dtArquivI'] = $dtArquivI;
            }
        } elseif (isset($_SESSION['dtArquivI'])) {
            $dtArquivI = $_SESSION['dtArquivI'];
            $dtArquivInull = substr($dtArquivI, 0, 10)." 23:59:59.999";
        }

        $dtArquivF = null;
        if (isset($post->dtArquivF)) {
            if (!empty($_SESSION['dtArquivF'])) {
                if ($post->dtArquivF != $_SESSION['dtArquivF']) {
                    $_SESSION['dtArquivF'] = formatadata($post->dtArquivF, 2);
                    $dtArquivF = formatadata($post->dtArquivF, 2);
                } else {
                    $dtArquivF = formatadata($_SESSION['dtArquivF'], 2);
                }
            } else {
                $dtArquivF = formatadata($post->dtArquivF, 2);
                $_SESSION['dtArquivF'] = $dtArquivF;
            }
        } elseif (isset($_SESSION['dtArquivF'])) {
            $dtArquivF = $_SESSION['dtArquivF'];
        }

        $tipo_cxInicio = null;
        if (isset($post->tipo_cxInicio)) {
            if (!empty($_SESSION['tipo_cxInicio'])) {
                if ($post->tipo_cxInicio != $_SESSION['tipo_cxInicio']) {
                    $_SESSION['tipo_cxInicio'] = $post->tipo_cxInicio;
                    $tipo_cxInicio = $post->tipo_cxInicio;
                } else {
                    $tipo_cxInicio = $_SESSION['tipo_cxInicio'];
                }
            } else {
                $tipo_cxInicio = (int) $post->tipo_cxInicio;
                $_SESSION['tipo_cxInicio'] = $tipo_cxInicio;
            }
        } elseif (!empty($_SESSION['tipo_cxInicio'])) {
            $tipo_cxInicio = $_SESSION['tipo_cxInicio'];
        }

        $cxInicio = null;
        if (isset($post->cxInicio)) {
            if (!empty($_SESSION['cxInicio'])) {
                if ($post->cxInicio != $_SESSION['cxInicio']) {
                    $_SESSION['cxInicio'] = $post->cxInicio;
                    $cxInicio = $post->cxInicio;
                } else {
                    $cxInicio = $_SESSION['cxInicio'];
                }
            } else {
                $cxInicio = (int) $post->cxInicio;
                $_SESSION['cxInicio'] = $cxInicio;
            }
        } elseif (!empty($_SESSION['cxInicio'])) {
            $cxInicio = $_SESSION['cxInicio'];
        }

        $tipo_cxFinal = null;
        if (isset($post->tipo_cxFinal)) {
            if (!empty($_SESSION['tipo_cxFinal'])) {
                if ($post->tipo_cxFinal != $_SESSION['tipo_cxFinal']) {
                    $_SESSION['tipo_cxFinal'] = $post->tipo_cxFinal;
                    $tipo_cxFinal = $post->tipo_cxFinal;
                } else {
                    $tipo_cxFinal = $_SESSION['tipo_cxFinal'];
                }
            } else {
                $tipo_cxFinal = (int) $post->tipo_cxFinal;
                $_SESSION['tipo_cxFinal'] = $tipo_cxFinal;
            }
        } elseif (!empty($_SESSION['tipo_cxFinal'])) {
            $tipo_cxFinal = $_SESSION['tipo_cxFinal'];
        }

        $cxFinal = null;
        if (isset($post->cxFinal)) {
            if (!empty($_SESSION['cxFinal'])) {
                if ($post->cxFinal != $_SESSION['cxFinal']) {
                    $_SESSION['cxFinal'] = $post->cxFinal;
                    $cxFinal = $post->cxFinal;
                } else {
                    $cxFinal = $_SESSION['cxFinal'];
                }
            } else {
                $cxFinal = (int) $post->cxFinal;
                $_SESSION['cxFinal'] = $cxFinal;
            }
        } elseif (!empty($_SESSION['cxFinal'])) {
            $cxFinal = $_SESSION['cxFinal'];
        }

        $arquivados = TramitarprojetosDAO::projetosArquivados(
            $idusuario,
            $pronac,
            $tipo_nome,
            $nome,
            $tipo_processo,
            $processo,
            $tipo_dtArquivo,
                                $dtArquivI,
            $dtArquivInull,
            $dtArquivF,
            $tipo_cxInicio,
            $cxInicio,
            $tipo_cxFinal,
            $cxFinal
        );
        // ========== IN�CIO PAGINA��O ==========
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginacao/paginacao.phtml');
        $paginator = Zend_Paginator::factory($arquivados); // dados a serem paginados

        // p�gina atual e quantidade de �tens por p�gina
        $currentPage = $this->_getParam('page', 1);
        $paginator->setCurrentPageNumber($currentPage)->setItemCountPerPage(30);
        $this->view->Arquivados = $paginator;
        $this->view->qtdDocs    = count($arquivados); // quantidade
        // ========== FIM PAGINA��O ==========

        if (!$arquivados) {
            parent::message("Nenhum Projeto Encontrado!", "tramitarprojetos/consultarprojetosarquivados", "CONFIRM");
        }

        if (isset($_POST['idArquivamento'])) {
            $idPronac = $_POST['idPro'];
            $justificativa = $_POST['justificativa'];
            $cxInicio = $_POST['cxInicioDesarquivar'];
            $cxFinal = $_POST['cxFinalDesarquivar'];

            $acao = 0;
            $stEstado = 1;
            $inserir = TramitarprojetosDAO::inserirSolicitacaoArquivamento($idPronac, $justificativa, $idusuario, $cxInicio, $cxFinal, $acao, $stEstado);
            $alterar = TramitarprojetosDAO::alterarStatusArquivamento($idPronac);
            parent::message("Solicita��o enviada com sucesso!", "tramitarprojetos/projetosarquivados", "CONFIRM");
        }
    }

    public function imprimirguiaAction()
    {
        //** Usuario Logado ************************************************/
        $auth = Zend_Auth::getInstance(); // pega a autentica��o
        $idusuario = $this->getIdUsuario;

        /* *************************************************************** */
        $this->_helper->layout->disableLayout();
        //$this->_helper->viewRenderer->setNoRender();

        $get = Zend_Registry::get('get');
        $idLote = $get->idLote;

        $docs = TramitarprojetosDAO::projetosImprimirGuia($idusuario, $idLote);
        $this->view->docs = $docs;

        $this->view->Origem = $docs[0]->Origem;
        $this->view->Destino = $docs[0]->Destino;
        $this->view->Emissor = $docs[0]->Emissor;
        $this->view->idLote = $docs[0]->idLote;
    }


    public function cancelarTramitacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idHistorico = $_POST['idHistorico'];

        $HistoricoDocumento = new HistoricoDocumento();
        $rs = $HistoricoDocumento->buscar(array('idHistorico = ?'=>$idHistorico))->current();

        $conclusao = false;
        switch ($rs->Acao) {
            case 1:
                $rs->stEstado = 0;
                $rs->save();
                $conclusao = true;
                break;
            case 2:
                $rs->stEstado = 0;
                $rs->save();
                $conclusao = true;
                break;
            default:
                break;
        }

        if ($conclusao) {
            $this->_helper->json(array('resposta'=>true));
        } else {
            $this->_helper->json(array('resposta'=>false));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
