<?php
class GerenciarPautaReuniaoController extends MinC_Controller_Action_Abstract
{
    private $bln_readequacao = "false";
    private $intTamPag = 10;

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
        $Usuario = new UsuarioDAO(); // objeto usuario
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo

        if ($auth->hasIdentity()) { // caso o usuario esteja autenticado
            $PermissoesGrupo = array();
            $PermissoesGrupo[] = 133;
            $PermissoesGrupo[] = 118;
            $PermissoesGrupo[] = 119;
            $PermissoesGrupo[] = 120;
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
        } else {// caso o usuario nao esteja autenticado
            return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
        }

        parent::init(); // chama o init() do pai GenericControllerNew
        /**** CODIGO DE READEQUACAO ****/
        $this->view->bln_readequacao = "false";

        $idpronac = null;
        $idpronac = $this->_request->getParam('idpronac');
        //VERIFICA SE O PROJETO ESTA NA FASE DE READEQUACAO
        if (!empty($idpronac)) {
            $tbPedidoAlteracao = new tbPedidoAlteracaoProjeto();
            $arrBusca = array();
            $arrBusca['pa.idPronac = ?']          = $idpronac;
            $arrBusca['pa.stPedidoAlteracao = ?'] = 'I'; //pedido enviado pelo proponente
            $arrBusca['pa.siVerificacao = ?']     = '2'; //pedido ja finalizado pelo componente
            $arrBusca['paxta.tpAlteracaoProjeto = ?']='10'; //tipo Readequacao de Itens de Custo
            $rsPedidoAlteraco = $tbPedidoAlteracao->buscarPedidoAlteracaoPorTipoAlteracao($arrBusca, array('dtSolicitacao DESC'))->current();
            if (!empty($rsPedidoAlteraco)) {
                $this->bln_readequacao = "true";
                $this->view->bln_readequacao = "true";
            }
        }
        /**** FIM - CODIGO DE READEQUACAO ****/
    }


    public function gerenciarpautareuniaoAction()
    {
        $post = Zend_Registry::get('post');

        $pauta = new Pauta();
        $reuniao = new Reuniao();
        $raberta = $reuniao->buscarReuniaoAberta();

        $reuniaoaberta = $raberta['idNrReuniao'];
        $buscarProjetoPauta = $pauta->PautaReuniaoAtual($reuniaoaberta);
        $plenario['plenario'] = array();
        $plenario['naoplenario'] = array();
        $contplenario = 1;
        $contnaoplenario = 1;

        foreach ($buscarProjetoPauta as $buscaplenario) {
            if ($buscaplenario->stEnvioPlenario == 'S') {
                $plenario['plenario'][$contplenario]['numero'] = $contplenario;
                $contplenario++;
            }
            if ($buscaplenario->stEnvioPlenario == 'N') {
                $plenario['naoplenario'][$contnaoplenario]['numero'] = $contnaoplenario;
                $plenario['naoplenario'][$contnaoplenario]['pronac'] = $buscaplenario->pronac;
                $plenario['naoplenario'][$contnaoplenario]['IdPRONAC'] = $buscaplenario->IdPRONAC;
                $plenario['naoplenario'][$contnaoplenario]['nomeprojeto'] = $buscaplenario->NomeProjeto;
                $plenario['naoplenario'][$contnaoplenario]['area'] = $buscaplenario->area;
                $plenario['naoplenario'][$contnaoplenario]['parecerfavoravel'] = $buscaplenario->stAnalise == 'IC' ? 'N&atilde;o' : 'Sim';
                $plenario['naoplenario'][$contnaoplenario]['segmento'] = $buscaplenario->segmento;
                $plenario['naoplenario'][$contnaoplenario]['datarecebimento'] = Data::tratarDataZend($buscaplenario->dtEnvioPauta, 'Brasileiro', true);
                $plenario['naoplenario'][$contnaoplenario]['componente'] = $buscaplenario->nomeComponente;
                $contnaoplenario++;
            }
        }
        $qtdplenario = count($plenario['plenario']);
        $qtdnaoplenario = count($plenario['naoplenario']);

        $totalProjeto = $qtdplenario + $qtdnaoplenario;
        $this->view->qtdenviadoplenaria = $contplenario;
        $this->view->totalprojetos = $totalProjeto;
        $this->view->numerocnic = $raberta['NrReuniao'];
        $this->view->totalprojetoplenaria = $qtdplenario;
        $this->view->totalnaoprojetoplenaria = $qtdnaoplenario;
        $this->view->statusplenaria = $raberta['stPlenaria'] == 'N' ? 'Plen&aacute;ria N&atilde;o Iniciada' : 'Plen&aacute;ria Iniciada';
        $this->view->Plenaria = $raberta;
        $this->view->projetosnaoplenaria = $plenario['naoplenario'];

        //BUSCAR PROJETOS DE READEQUACAO
        $readequacao = $this->_request->getParam('readequacao');

        if (!empty($readequacao) && $readequacao == "true") {
            $this->view->readequacao = "true";
        } else {
            $this->view->readequacao = "false";
        };

        //BUSCAR PROJETOS NAO SUBMETIDOS A PLENARIA
        $plenaria = $this->_request->getParam('plenaria');

        if (empty($plenaria) || $plenaria == "true") {
            $this->view->plenaria = "true";
        } else {
            $this->view->plenaria = "false";
        };
    }

    public function gerenciarpresidenteemreuniaoAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $pauta = new Pauta();
        $reuniao = new Reuniao();
        $votacao = new Votacao();
        if (isset($_POST['idReuniao'])) {
            $recebidoPost = Zend_Registry::get("post");
            $votantes = new Votante();
            $buscarvotantes = $votantes->selecionarvotantes($recebidoPost->idReuniao);

            //VERIFICA SE HA VOTANTES CADASTRADOS
            if ($buscarvotantes->count() > 0) {

                //VERIFICA SE ESTA ENCERRANDO A PELNARIA
                if ($recebidoPost->reuniao == "E") {
                    $reuniaoatual = $reuniao->buscarReuniaoAberta();
                    $nrProximaReuniao = $reuniaoatual->NrReuniao + 1;
                    $dadosproximareuniao = $reuniao->buscar(array('NrReuniao = ?' => $nrProximaReuniao))->current();

                    //VERIFICA SE JA FOI CRIADA A PROXIMA REUNIAO
                    if (!empty($dadosproximareuniao)) {
                        $buscarvotacao = $votacao->buscar(array('idNrReuniao = ?' => $recebidoPost->idReuniao, 'stVoto is null' => ''));

                        //VERIFICA SE AINDA HA VOTOS EM ABERTO - SE FALTOU ALGUM COMPONENTE VOTAR
                        if ($buscarvotacao->count() == 0) {
                            $dados = array(
                                'stPlenaria' => $recebidoPost->reuniao,
                                'stEstado' => $recebidoPost->reuniao == 'E' ? 1 : 0,
                                'DtFinal' => date('Y-m-d H:i:s')
                            );
                            $where = " NrReuniao = " . $reuniaoatual->NrReuniao;
                            $reuniao->alterar($dados, $where);

                            $dados = array(
                                'stPlenaria' => 'N',
                                'stEstado' => '0',
                                'DtFinal' => date('Y-m-d H:i:s')
                            );
                            $where = " NrReuniao = " . $nrProximaReuniao;
                            $reuniao->alterar($dados, $where);

                            $arquivo = getcwd() . "/public/plenaria/verificaplenaria.txt";
                            unlink($arquivo);

                            $buscarpauta = $pauta->PautaProximaReuniao($reuniaoatual->NrReuniao);
                            foreach ($buscarpauta as $pautaproximareuniao) {
                                $dados = array('idNrReuniao' => $dadosproximareuniao->idNrReuniao);
                                $alterarpauta = $pauta->alterar($dados, 'idNrReuniao = ' . $pautaproximareuniao->idNrReuniao . ' and IdPRONAC = ' . $pautaproximareuniao->IdPRONAC);
                            }

                            $tbRecurso = new tbRecurso();
                            $tbRecurso->atualizarRecursosProximaPlenaria($recebidoPost->idReuniao);
                            $tbRecurso->atualizarStatusRecursosNaoSubmetidos($recebidoPost->idReuniao);

                            $tbReadequacoes = new Readequacao_Model_DbTable_TbReadequacao();
                            $tbReadequacoes->atualizarReadequacoesProximaPlenaria($recebidoPost->idReuniao);
                            $tbReadequacoes->atualizarStatusReadequacoesNaoSubmetidos($recebidoPost->idReuniao);

                            //CHAMA SP DE ENCERRAMENTO DA CNIC
                            $this->paEncerrarCnic($_POST['idReuniao']);

                            parent::message("Vota&ccedil;&atilde;o encerrada com o sucesso!", "gerenciarpautareuniao/gerenciarpresidenteemreuniao", "CONFIRM");
                        } else {
                            parent::message("Ainda existe uma vota&ccedil;&atilde;o em aberto, favor esperar finaliza&ccedil;&atilde;o ou cancelar a vota&ccedil;&atilde;o do projeto!", "gerenciarpautareuniao/gerenciarpresidenteemreuniao", "ERROR");
                        }
                    } else {
                        parent::message("A pr&oacute;xima reuni&atilde;o ainda n&atilde;o foi cadastrada. &Eacute; necess&aacute;rio cadastr&aacute;-la para encerrar a Plen&aacute;ria.", "gerenciarpautareuniao/gerenciarpresidenteemreuniao", "ERROR");
                    }

                    //INICIANDO - ABRINDO A PLENARIA
                } else {
                    try {
                        $dados = array(
                            'stPlenaria' => $recebidoPost->reuniao,
                            'stEstado' => $recebidoPost->reuniao == 'E' ? 1 : 0,
                            'DtFinal' => date('Y-m-d H:i:s')
                        );
                        $where = " idNrReuniao = " . $recebidoPost->idReuniao;
                        $reuniao->alterar($dados, $where);
                        $dadosPlenaria = array(
                            'idNrReuniao' => $recebidoPost->idReuniao,
                            'Status' => 'A',
                            'TempoInicio' => date('Y-m-d H:i:s'),
                        );
                        $arquivo = getcwd() . "/public/plenaria/verificaplenaria.txt";
                        if (file_exists($arquivo)) {
                            unlink($arquivo);
                        }
                        // "a" representa que o arquivo e aberto para ser escrito
                        $fp = fopen($arquivo, "a+");
                        $escreve = fwrite($fp, json_encode($dadosPlenaria));
                        fclose($fp);
                        parent::message("Plen&aacute;ria iniciada com sucesso! Aguarde os 10 minutos para o in&iacute;cio da plen&aacute;ria!", "gerenciarpautareuniao/gerenciarpresidenteemreuniao", "CONFIRM");
                    } catch (Exception $e) {
                        parent::message("Erro ao iniciar a Plen&aacute;ria! ".$e->getMessage(), "gerenciarpautareuniao/gerenciarpresidenteemreuniao", "ERROR");
                    }
                }
            } else {
                parent::message("Favor solicitar ao Secret&aacute;rio CNIC que inclua os votantes e possa iniciar a Plen&aacute;ria!", "gerenciarpautareuniao/gerenciarpresidenteemreuniao", "ERROR");
            }
        } else {
            $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
            $raberta = $reuniao->buscarReuniaoAberta();
            $reuniaoaberta = $raberta['idNrReuniao'];
            $pa = new Parecer_Model_DbTable_Parecer();
            $dpc = new DistribuicaoProjetoComissao();
            $buscarProjetoPauta = $pauta->PautaReuniaoAtual($reuniaoaberta);
            $plenario['plenario'] = array();
            $plenario['naoplenario'] = array();
            $contplenario = 1;
            $contnaoplenario = 1;

            foreach ($buscarProjetoPauta as $buscaplenario) {
                if ($buscaplenario->stEnvioPlenario == 'S') {
                    $plenario['plenario'][$contplenario]['numero'] = $contplenario;
                    $plenario['plenario'][$contplenario]['IdPRONAC'] = $buscaplenario->IdPRONAC;
                    $plenario['plenario'][$contplenario]['pronac'] = $buscaplenario->pronac;
                    $plenario['plenario'][$contplenario]['nomeprojeto'] = $buscaplenario->NomeProjeto;
                    $plenario['plenario'][$contplenario]['area'] = $buscaplenario->area;
                    $plenario['plenario'][$contplenario]['parecerfavoravel'] = $buscaplenario->stAnalise == 'IC' ? 'N&atilde;o' : 'Sim';
                    $plenario['plenario'][$contplenario]['segmento'] = $buscaplenario->segmento;
                    $plenario['plenario'][$contplenario]['datarecebimento'] = Data::tratarDataZend($buscaplenario->dtEnvioPauta, 'Brasileiro', true);
                    $plenario['plenario'][$contplenario]['componente'] = $buscaplenario->nomeComponente;
                    $contplenario++;
                }
                if ($buscaplenario->stEnvioPlenario == 'N') {
                    $plenario['naoplenario'][$contnaoplenario]['numero'] = $contnaoplenario;
                    $plenario['naoplenario'][$contnaoplenario]['pronac'] = $buscaplenario->pronac;
                    $plenario['naoplenario'][$contnaoplenario]['IdPRONAC'] = $buscaplenario->IdPRONAC;
                    $plenario['naoplenario'][$contnaoplenario]['nomeprojeto'] = $buscaplenario->NomeProjeto;
                    $plenario['naoplenario'][$contnaoplenario]['area'] = $buscaplenario->area;
                    $plenario['naoplenario'][$contnaoplenario]['parecerfavoravel'] = $buscaplenario->stAnalise == 'IC' ? 'N&atilde;o' : 'Sim';
                    $plenario['naoplenario'][$contnaoplenario]['segmento'] = $buscaplenario->segmento;
                    $plenario['naoplenario'][$contnaoplenario]['datarecebimento'] = Data::tratarDataZend($buscaplenario->dtEnvioPauta, 'Brasileiro', true);
                    $plenario['naoplenario'][$contnaoplenario]['componente'] = $buscaplenario->nomeComponente;
                    $contnaoplenario++;
                }
            }
            $qtdplenario = count($plenario['plenario']);
            $qtdnaoplenario = count($plenario['naoplenario']);

            $totalProjeto = $qtdplenario + $qtdnaoplenario;

            $buscarvotacao = $votacao->buscar(array('idNrReuniao = ?' => $reuniaoaberta, 'dtVoto is null' => ''));
            if ($buscarvotacao->count() > 0) {
                $buscarvotacao = $buscarvotacao->current()->toArray();
                if ($buscarvotacao['tpVotacao'] == 3) { //Se for readequa��o
                    $this->view->pronacvotacaoatual = $buscarvotacao['IdPRONAC'].'_'.$buscarvotacao['tpTipoReadequacao'];
                } else {
                    $this->view->pronacvotacaoatual = $buscarvotacao['IdPRONAC'];
                }
            } else {
                $this->view->pronacvotacaoatual = false;
            }

            //$qtdprojetonaoanalisados = $dpc->projetosNaoAnalisados($raberta['NrReuniao'])->count();
            //$qtdprojetoanalisados = $dpc->projetosAnalisados($raberta['idNrReuniao'])->count();
            $tblDistribuicao = new tbDistribuicaoProjetoComissao();
            //ANALISADOS
            $qtdprojetoanalisados = $tblDistribuicao->buscarProjetoEmPauta(array(), null, null, null, false, null, null, 1)->count();
            //NAO ANALISADOS
            $arrReuniao = array();
            $arrReuniao['idNrReuniao IS NULL ']= "?";
            $qtdprojetonaoanalisados = count($tblDistribuicao->buscarProjetoEmPauta(array(), null, null, null, false, "N&atilde;o analisado", $arrReuniao));

            $this->view->qtdprojetoanalisados = $qtdprojetoanalisados;
            $this->view->qtdenviadoplenaria = $contplenario;
            $this->view->qtdprojetonaoanalisados = $qtdprojetonaoanalisados;
            $this->view->qtdtotalprojetospauta = $qtdprojetoanalisados + $qtdprojetonaoanalisados;
            $this->view->grupoAtivo = $GrupoAtivo->codGrupo;
            $this->view->reuniaoaberta = $raberta;
            $this->view->totalprojetos = $totalProjeto;
            $this->view->numerocnic = $raberta['NrReuniao'];
            $this->view->totalprojetoplenaria = $qtdplenario;
            $this->view->totalnaoprojetoplenaria = $qtdnaoplenario;
            $this->view->statusplenaria = $raberta['stPlenaria'] == 'N' ? 'Plen&aacute;ria N&atilde;o Iniciada' : 'Plen&aacute;ria Iniciada';
            $this->view->codstplenaria = $raberta['stPlenaria'];

            $this->view->Plenaria = $raberta;

            $this->view->projetosplenaria = $plenario['plenario'];
            $this->view->projetosnaoplenaria = $plenario['naoplenario'];
        }

        //BUSCAR PROJETOS DE READEQUACAO
        $readequacao = $this->_request->getParam('readequacao');

        if (!empty($readequacao) && $readequacao == "true") {
            $this->view->readequacao = "true";
        } else {
            $this->view->readequacao = "false";
        };

        //BUSCAR PROJETOS NAO SUBMETIDOS A PLENARIA
        $plenaria = $this->_request->getParam('plenaria');

        if (empty($plenaria) || $plenaria == "true") {
            $this->view->plenaria = "true";
        } else {
            $this->view->plenaria = "false";
        };
    }

    public function projetosSubmetidosAPlenariaAction()
    {
        $pauta = new Pauta();
        $reuniao = new Reuniao();
        $raberta = $reuniao->buscarReuniaoAberta();
        $reuniaoaberta = $raberta['idNrReuniao'];
        $this->view->numerocnic = $raberta['NrReuniao'];
        $this->view->statusplenaria = $raberta['stPlenaria'] == 'N' ? 'Plen&aacute;ria N&atilde;o Iniciada' : 'Plen&aacute;ria Iniciada';
    }

    public function gerenciaradministrativoAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $pauta = new Pauta();
        $pa = new Parecer_Model_DbTable_Parecer();
        $reuniao = new Reuniao();
        $votacao = new Votacao();
        $dpc = new DistribuicaoProjetoComissao();

        $raberta = $reuniao->buscarReuniaoAberta();
        $reuniaoaberta = $raberta['idNrReuniao'];
        $buscarProjetoPauta = $pauta->PautaReuniaoAtual($reuniaoaberta);
        $plenario['plenario'] = array();
        $plenario['naoplenario'] = array();
        $contplenario = 1;
        $contnaoplenario = 1;

        //DEVOLVEDO PROJETO PARA O COMPONENTE
        if (isset($_POST['retorna'])) {
            $pronac = $_POST['pronac'];
            $arrRetorno = array();
            try {
                $sp = new paVoltarProjetoFinalizadoComponente();
                $ret = $sp->execSP($pronac);

                if (!is_object($ret)) {
                    throw new Exception($ret);
                }

                $arrRetorno['error'] = false;
                $arrRetorno['msg']   = 'Projeto devolvido com sucesso!';
                $this->_helper->json($arrRetorno);
                $this->_helper->viewRenderer->setNoRender(true);
            } catch (Exception $e) {
                $arrRetorno['error'] = true;
                $arrRetorno['msg']   = $e->getMessage();
                $this->_helper->json($arrRetorno);
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }
        //DEVOLVEDO PROJETO PARA O COMPONENTE - RECURSO
        if (isset($_POST['retornaRecurso'])) {
            $pronac = $_POST['pronac'];
            $idRecurso = $_POST['recurso'];
            $arrRetorno = array();
            try {
                $tbRecurso = new tbRecurso();
                $r = $tbRecurso->find(array('idRecurso = ?'=>$idRecurso))->current();
                $r->siRecurso = 7;
                $r->save();

                $arrRetorno['error'] = false;
                $arrRetorno['msg']   = 'Projeto devolvido com sucesso!';
                $this->_helper->json($arrRetorno);
                $this->_helper->viewRenderer->setNoRender(true);
            } catch (Exception $e) {
                $arrRetorno['error'] = true;
                $arrRetorno['msg']   = $e->getMessage();
                $this->_helper->json($arrRetorno);
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }
        foreach ($buscarProjetoPauta as $buscaplenario) {
            if ($buscaplenario->stEnvioPlenario == 'S') {
                $plenario['plenario'][$contplenario]['numero'] = $contplenario;
                $plenario['plenario'][$contplenario]['IdPRONAC'] = $buscaplenario->IdPRONAC;
                $plenario['plenario'][$contplenario]['pronac'] = $buscaplenario->pronac;
                $plenario['plenario'][$contplenario]['nomeprojeto'] = $buscaplenario->NomeProjeto;
                $plenario['plenario'][$contplenario]['area'] = $buscaplenario->area;
                $plenario['plenario'][$contplenario]['parecerfavoravel'] = $buscaplenario->stAnalise == 'IC' ? 'N&atilde;o' : 'Sim';
                $plenario['plenario'][$contplenario]['segmento'] = $buscaplenario->segmento;
                $plenario['plenario'][$contplenario]['datarecebimento'] = Data::tratarDataZend($buscaplenario->dtEnvioPauta, 'Brasileiro', true);
                $plenario['plenario'][$contplenario]['componente'] = $buscaplenario->nomeComponente;
                $contplenario++;
            } elseif ($buscaplenario->stEnvioPlenario == 'N') {
                $plenario['naoplenario'][$contnaoplenario]['numero'] = $contnaoplenario;
                $plenario['naoplenario'][$contnaoplenario]['pronac'] = $buscaplenario->pronac;
                $plenario['naoplenario'][$contnaoplenario]['IdPRONAC'] = $buscaplenario->IdPRONAC;
                $plenario['naoplenario'][$contnaoplenario]['nomeprojeto'] = $buscaplenario->NomeProjeto;
                $plenario['naoplenario'][$contnaoplenario]['area'] = $buscaplenario->area;
                $plenario['naoplenario'][$contnaoplenario]['parecerfavoravel'] = $buscaplenario->stAnalise == 'IC' ? 'N&atilde;o' : 'Sim';
                $plenario['naoplenario'][$contnaoplenario]['segmento'] = $buscaplenario->segmento;
                $plenario['naoplenario'][$contnaoplenario]['datarecebimento'] = Data::tratarDataZend($buscaplenario->dtEnvioPauta, 'Brasileiro', true);
                $plenario['naoplenario'][$contnaoplenario]['componente'] = $buscaplenario->nomeComponente;
                $contnaoplenario++;
            }
        }
        //$qtdprojetonaoanalisados = $dpc->projetosNaoAnalisados($raberta['NrReuniao'])->count();
        //$qtdprojetoanalisados = $dpc->projetosAnalisados($raberta['idNrReuniao'])->count();
        $tblDistribuicao = new tbDistribuicaoProjetoComissao();
        //ANALISADOS
        $qtdprojetoanalisados = $tblDistribuicao->buscarProjetoEmPauta(array(), null, null, null, false, null, null, 1)->count();
        //NAO ANALISADOS
        $arrReuniao = array();
        $arrReuniao['idNrReuniao IS NULL ']= "?";

        //$qtdprojetonaoanalisados = $tblDistribuicao->buscarProjetoEmPauta( array(), null, null, null, false, "Não analisado", $arrReuniao)->count();
        $qtdprojetonaoanalisados = $tblDistribuicao->qtdProjetoNaoAnalisados();

        $qtdplenario = count($plenario['plenario']);
        $qtdnaoplenario = count($plenario['naoplenario']);

        $totalProjeto = $qtdplenario + $qtdnaoplenario;
        $this->view->totalprojetos = $totalProjeto;
        $this->view->numerocnic = $raberta['NrReuniao'];
        $this->view->totalprojetoplenaria = $qtdplenario;
        $this->view->totalnaoprojetoplenaria = $qtdnaoplenario;
        $this->view->statusplenaria = $raberta['stPlenaria'] == 'N' ? 'Plen&aacute;ria N&atilde;o Iniciada' : 'Plen&aacute;ria Iniciada';
        $this->view->Plenaria = $raberta;

        $this->view->qtdprojetoanalisados = $qtdprojetoanalisados;
        $this->view->qtdprojetonaoanalisados = $qtdprojetonaoanalisados;
        $this->view->qtdtotalprojetospauta = $qtdprojetoanalisados + $qtdprojetonaoanalisados;
        $this->view->qtdenviadoplenaria = $contplenario;

        $this->view->projetosplenaria = $plenario['plenario'];
        $this->view->projetosnaoplenaria = $plenario['naoplenario'];

        //BUSCAR PROJETOS DE READEQUACAO
        $readequacao = $this->_request->getParam('readequacao');

        if (!empty($readequacao) && $readequacao == "true") {
            $this->view->readequacao = "true";
        } else {
            $this->view->readequacao = "false";
        };

        //BUSCAR PROJETOS NAO SUBMETIDOS A PLENARIA
        $plenaria = $this->_request->getParam('plenaria');

        if (empty($plenaria) || $plenaria == "true") {
            $this->view->plenaria = "true";
        } else {
            $this->view->plenaria = "false";
        };
    }

    public function listarpaineisdareuniaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $idReuniao = $this->_request->getParam("idReuniao");

        //RECUPERA ID DA REUNIAO ATUAL (ABERTA)
        $tblReuniao = new tbreuniao();
        $rsReuniao = $tblReuniao->buscar(array("stEstado=?"=>0))->current();
        $idReuniaoAutal = $rsReuniao->idNrReuniao;

        //RECUPERA AREAS CULTURAIS
        $idsAreas = "";
        $tblArea = new Area();
        $rsArea = $tblArea->buscar(array(), array("Descricao ASC"));
        $this->view->areas = $rsArea;
        $arrAreas = array();
        foreach ($rsArea as $area) {
            $arrAreas[$area->Descricao] = $area->Codigo;
            $idsAreas .= $area->Codigo.",";
        }
        //retira ultima virgula
        $idsAreas = substr($idsAreas, 0, strlen($idsAreas)-1);
        $arrIdAreas = explode(",", $idsAreas);

        $tblDistribuicao = new tbDistribuicaoProjetoComissao();

        //ANALISADOS
        $arrBusca =array();
        $arrBusca['ar.Codigo IN (?)'] = $arrIdAreas;
        if (isset($idReuniao) && $idReuniao != "") {
            $arrBusca['r.idNrReuniao = ?']=$idReuniao;
        }
        $arrReuniao = array();
        if (isset($idReuniao) && $idReuniao != "") {
            $arrReuniao['r.idNrReuniao = ?']=$idReuniao;
        }
        $ordem = array('1','21'); //ORDENACAO: analise , area cultural
        $rsProjAnalisados = $tblDistribuicao->buscarProjetoEmPauta($arrBusca, $ordem, null, null, false, null, $arrReuniao, 1);

        //NAO ANALISADOS
        $arrBusca =array();
        $arrBusca['ar.Codigo IN (?)'] = $arrIdAreas;
        $arrReuniao = array();
        if (isset($idReuniao) && $idReuniao != "" && $idReuniao==$idReuniaoAutal) {
            $arrReuniao['idNrReuniao IS NULL ']= "?";
        } else {
            $arrReuniao['idNrReuniao IS NOT NULL ']= "?";
        }
        $ordem = array('1','21'); //ORDENACAO: analise , area cultural
        $rsProjNaoAnalisados = $tblDistribuicao->buscarProjetoEmPauta($arrBusca, $ordem, null, null, false, "N&atilde;o analisado", $arrReuniao);

        //======== GRID 1 ==========/
        $arrGrid1 = array();
        foreach ($rsProjAnalisados as $projeto) {
            if (key_exists($projeto->DescArea, $arrAreas)) {
                $arrGrid1['analisados'][$projeto->DescArea][] = $projeto->idPronac;
            }
        }
        $projeto = null;
        foreach ($rsProjNaoAnalisados as $projeto) {
            if (key_exists($projeto->DescArea, $arrAreas)) {
                $arrGrid1['nao_analisados'][$projeto->DescArea][] = $projeto->idPronac;
            }
        }

        //======== GRID 2 ==========/
        $arrGrid2 = array();
        $arrAprovados = array('AC','AS', 'AR');
        foreach ($rsProjAnalisados as $projeto) {
            if (key_exists($projeto->DescArea, $arrAreas)) {
                if (in_array($projeto->stAnalise, $arrAprovados)) {
                    $arrGrid2['analisados'][$projeto->DescArea]['aprovado'][] = $projeto->idPronac;
                } else {
                    $arrGrid2['analisados'][$projeto->DescArea]['indeferido'][] = $projeto->idPronac;
                }
            }
        }
        $projeto = null;
        foreach ($rsProjNaoAnalisados as $projeto) {
            if (key_exists($projeto->DescArea, $arrAreas)) {
                $arrGrid2['nao_analisados'][$projeto->DescArea][] = $projeto->idPronac;
            }
        }


        //======== GRID 3 ==========/
        $arrBusca = array("d.stPrincipal = ?"=>1, "d.stEstado = ?"=>0, "z.stDistribuicao = ?"=>"A");
        if (isset($idReuniao) && $idReuniao != "") {
            $arrBusca['r.idNrReuniao = ?']=$idReuniao;
        }
        $ordem = array('13'); //ORDENACAO: area cultural
        $tblPauta = new tbPauta();
        $rsProjAprovados = $tblPauta->buscarProjetosAvaliados($arrBusca, $ordem, null, null, null);

        $arrGrid3 = array();
        $valorAprovado = 0;
        $valorTotalAprovado = 0;
        foreach ($rsProjAprovados as $projeto) {
            if (key_exists($projeto->DescArea, $arrAreas)) {
                if (in_array($projeto->stAnalise, $arrAprovados)) {
                    $arrGrid3['analisados'][$projeto->DescArea]['aprovado'][] = $projeto->IdPronac;
                }
                $arrGrid3[$projeto->DescArea]['vlAprovado'][] = $projeto->VlAprovado;
                $valorAprovado = $projeto->VlAprovado;
                $valorTotalAprovado = $valorTotalAprovado + $valorAprovado;
            }
        }
        $arrGrid3['vlTotalAprovado'] = $valorTotalAprovado;


        //======== GRID 4 ==========/
        $arrBusca = array("d.stPrincipal = ?"=>1, "d.stEstado = ?"=>0, "z.stDistribuicao = ?"=>"A");
        if (isset($idReuniao) && $idReuniao != "") {
            $arrBusca['r.idNrReuniao = ?']=$idReuniao;
        }
        $ordem = array('13','3'); //ORDENACAO: area cultural
        $tblPauta = new tbPauta();
        $rsProjAprovados = $tblPauta->buscarProjetosAvaliados($arrBusca, $ordem, null, null, null);

        $arrGrid4 = array();
        $valorAprovado = 0;
        $valorTotalAprovado = 0;
        foreach ($rsProjAprovados as $projeto) {
            if (key_exists($projeto->DescArea, $arrAreas)) {
                if (in_array($projeto->stAnalise, $arrAprovados)) {
                    $arrGrid4[$projeto->DescArea]['idPronac'][]     = $projeto->IdPronac;
                    $arrGrid4[$projeto->DescArea]['pronac'][]       = $projeto->Pronac;

                    /**** CODIGO DE READEQUACAO ****/
                    $rs = array();
                    $rsReadequacao = array();
                    /***** inicio - verifica se o projeto e de readequacao ***********
                    $tbPedidoAlteracao = new tbPedidoAlteracaoProjeto();
                    $arrBuscaRead = array();
                    $arrBuscaRead['pa.idPronac = ?']          = $projeto->IdPronac;
                    $arrBuscaRead['pa.stPedidoAlteracao = ?'] = 'I'; //pedido enviado pelo proponente
                    $arrBuscaRead['pa.siVerificacao = ?']     = '1';
                    $arrBuscaRead['paxta.tpAlteracaoProjeto = ?']='10'; //tipo Readequacao de Itens de Custo
                    $rsReadequacao = $tbPedidoAlteracao->buscarPedidoAlteracaoPorTipoAlteracao($arrBuscaRead)->current();
                    if(!empty($rsReadequacao)){
                        $arrBuscaProjRead = $arrBusca;
                        $arrBuscaProjRead['p.idPronac=?']=$projeto->IdPronac;
                        $rs = $tblPauta->buscarProjetosAvaliados($arrBuscaProjRead, $ordem, null, null, null, true)->current();
                    }
                    /***** fim - verifica se o projeto e de readequacao **************/

                    if (isset($rs) && !empty($rsReadequacao)) {
                        $arrGrid4[$projeto->DescArea]['vlSolicitado'][] = $rs->VlSolicitado;
                        $arrGrid4[$projeto->DescArea]['vlSugerido'][]   = $rs->VlSugerido;
                        $arrGrid4[$projeto->DescArea]['vlAprovado'][]   = $rs->VlAprovado;
                    } else {
                        $arrGrid4[$projeto->DescArea]['vlSolicitado'][] = $projeto->VlSolicitado;
                        $arrGrid4[$projeto->DescArea]['vlSugerido'][]   = $projeto->VlSugerido;
                        $arrGrid4[$projeto->DescArea]['vlAprovado'][]   = $projeto->VlAprovado;
                    }
                    /**** FIM - CODIGO DE READEQUACAO ****/
                }

                $valorAprovado = $projeto->VlAprovado;
                $valorTotalAprovado = $valorTotalAprovado + $valorAprovado;
            }
        }
        $arrGrid4['vlTotalAprovado'] = $valorTotalAprovado;

        $dados['areas'] = $arrAreas;
        $dados['grid1'] = $arrGrid1;
        $dados['grid2'] = $arrGrid2;
        $dados['grid3'] = $arrGrid3;
        $dados['grid4'] = $arrGrid4;
        $this->montaTela("gerenciarpautareuniao/gridspainelreuniao.phtml", $dados);
        return;
    }

    public function paineisdareuniaoAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo
        $reuniao = new Reuniao();
        $buscarReuniaoAberta = $reuniao->buscarReuniaoAberta();
        $reuniaoaberta = $buscarReuniaoAberta['idNrReuniao'];
        $this->view->Plenaria = $reuniaoaberta;

        if ($GrupoAtivo->codGrupo == 133 or $GrupoAtivo->codGrupo == 118) {
            $url = array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpautareuniao');
        }
        if ($GrupoAtivo->codGrupo == 120 || $GrupoAtivo->codGrupo == 148) { // Coordenador e Diretor da CNIC
            $url = array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciaradministrativo');
        }
        if ($GrupoAtivo->codGrupo == 119) { // presidente
            $url = array('controller' => 'gerenciarpautareuniao', 'action' => 'gerenciarpresidenteemreuniao');
        }
        $this->view->url = $url;

        //RECUPERA NUMERO REUNIOES
        $tblTbReuniao = new tbreuniao();
        $rsTbReuniao = $tblTbReuniao->buscar(array(), array("NrReuniao DESC"));
        $this->view->reunioes = $rsTbReuniao;
        $this->view->idReuniaoAtual = $reuniaoaberta;

        //----------------------------------------------------------------------------------------------------------------------------------------------------------------
        /*if ($_POST and $_POST['verifica']) {
            $this->_helper->layout->disableLayout();
            $idnrreuniao = $_POST['idnrreuniao'];
            $dadosreuniao = ConsultaReuniaoDAO::dadosReuniaoEncerrada($idnrreuniao);
            $i = 0;
            $dadosvalores = array();
            foreach ($dadosreuniao as $dados) {
                $dadosvalores[$i]['NomeProjeto'] = utf8_encode($dados->NomeProjeto);
                $dadosvalores[$i]['pronac'] = $dados->pronac;
                $dadosvalores[$i]['stAnalise'] = $dados->stAnalise;
                $dadosvalores[$i]['area'] = utf8_encode($dados->area);
                $dadosvalores[$i]['AprovadoReal'] = 'R$ ' . number_format($dados->AprovadoReal, 2, ',', '.');
                $dadosvalores[$i]['dsAnalise'] = utf8_encode($dados->dsConsolidacao);
                $i++;
            }
            $jsonenviar = json_encode($dadosvalores);
            echo $jsonenviar;
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }
        $consultaReuniao = ConsultaReuniaoDAO::listaReuniaoEncerrada();
        if (count($consultaReuniao) > 0) {
            $this->view->consultaReuniaoEncerrada = $consultaReuniao;
            $consultaProjetosPautaReuniao = ConsultaProjetosPautaReuniaoDAO::consultaProjetosPautaReuniao('S', true);
            $this->view->consultaProjetosPautaReuniaoEnvioPlenaria = $consultaProjetosPautaReuniao;
        } else {
            if ($GrupoAtivo->codGrupo == 119) {
                $url = "gerenciarpautareuniao/gerenciarpresidenteemreuniao";
            } else if ($GrupoAtivo->codGrupo == 120) {
                $url = "gerenciarpautareuniao/gerenciaradministrativo";
            } else if ($GrupoAtivo->codGrupo == 118 or $GrupoAtivo->codGrupo == 93) {
                $url = "gerenciarpautareuniao/gerenciarpautareuniao";
            }
            parent::message("Pain&eacute;is de Reuni&tilde;o: Nenhuma Vota&ccedil;o Consolidada at&aacute; o momento!", $url, "ALERT");
            $this->_helper->viewRenderer->setNoRender(TRUE);
        }*/
    }

    public function verificarcnicAction()
    {
        if (isset($_POST['verificacnic'])) {
            $this->_helper->layout->disableLayout();
            $caminhoverificar = getcwd() . "/public/plenaria/verificaplenaria.txt";
            if (file_exists($caminhoverificar)) {
                $read = fopen($caminhoverificar, 'r');
                if ($read) {
                    while (($buffer = fgets($read, 4096)) !== false) {
                        $verificareuniao = $buffer;
                    }
                    fclose($read);
                }
                $reuniao = new Reuniao();
                $raberta = $reuniao->buscarReuniaoAberta();
                $reuniaoaberta = $raberta['idNrReuniao'];
                $verificareuniao = json_decode($verificareuniao, true);
                if (isset($verificareuniao->Status) and ($verificareuniao['Status'] != $_POST['stPlenaria'])) {
                    $this->_helper->json(array('error' => false, 'acao' => 'reload'));
                } else {
                    $horaBanco = date('Y-m-d H:i:s', strtotime($verificareuniao['TempoInicio']));
                    $horaadicionada = strtotime($horaBanco . "+10 minutes");
                    $horaAtual = strtotime("NOW");
                    $real = $horaadicionada - $horaAtual;
                    $data = date('i:s', $real);
                    $this->_helper->json(array('error' => false, 'acao' => 'naoreload', 'cronometro' => $data, 'real' => $real, 'status' => $verificareuniao['Status']));
                }
            } else {
                $this->_helper->json(array('error' => true, 'acao' => 'reload', 'status'=>'N'));
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function votacaoAction()
    {
        $this->_helper->layout->disableLayout();

        $votantes = new Votante();
        $votacao = new Votacao();
        $reuniao = new Reuniao();
        $raberta = $reuniao->buscarReuniaoAberta();
        $reuniaoaberta = $raberta['idNrReuniao'];
        $idpronac = $_POST['idpronac'];

        if ($_POST['acao'] == 'iniciar') {
            $buscarvotantes = $votantes->selecionarvotantes($reuniaoaberta);

            try {
                //verifica se ja existe votacao para este projeto nesta reuniao
                $where = array();
                $where['IdPRONAC = ?'] = $idpronac;
                $where['idNrReuniao = ?'] = $reuniaoaberta;
                $rsVotacao = $votacao->buscar($where)->current();

                if (!empty($rsVotacao) && $this->_request->getParam("tipo") != 'readequacao') {
                    $this->_helper->json(array('error' => true, 'descricao' => 'J&aacute; existe uma vota&ccedil;&atildeo em aberto para este Pronac. Favor encerrar a vota&ccedil;&atildeo antes de iniciar uma outra.'));
                } else {
                    $tpVotacao = 1;
                    $idtipo = null;

                    if ($this->_request->getParam("tipo") == 'recurso') {
                        $tpVotacao = 2;
                    } elseif ($this->_request->getParam("tipo") == 'readequacao') {
                        $tpVotacao = 3;
                        $idtipo = $_POST['idtipo'];
                    }

                    //Inserindo registros na tabela tbVotacao para recber o voto de cada participante da plenaria
                    foreach ($buscarvotantes as $adicionarvotacao) {
                        $dadosinserirvotacao = array(
                            'idNrReuniao' => $reuniaoaberta,
                            'idAgente' => $adicionarvotacao->idAgente,
                            'IdPRONAC' => $_POST['idpronac'],
                            'tpVotacao' => $tpVotacao, //1.Inical, 2.Recurso, 3.Readequacao
                            'tpTipoReadequacao' => $idtipo //Se for readequa��o, esse campo retornar� o valor do idTipoReadequacao
                        );
                        $inserirvotacao = $votacao->inserir($dadosinserirvotacao);
                    }

                    $arquivo = getcwd() . "/public/plenaria/votacao.txt";
                    //$arquivo = getcwd() . "/public/plenaria/votacao_".$_POST['idpronac'].".txt";
                    if (file_exists($arquivo)) {
                        unlink($arquivo);
                    }

                    $dadosvotacao = array(
                        'idpronac' => $_POST['idpronac'],
                        'status' => "aberta",
                        'datahora' => date('Y-m-d H:i:s')
                    );
                    if ($tpVotacao == 3) { //Se for readequa��o
                        $dadosvotacao['idtiporeadequacao'] = $idtipo;
                    }
                    $fp = fopen($arquivo, "a+");
                    $escreve = fwrite($fp, json_encode($dadosvotacao));
                    fclose($fp);
                    $this->_helper->json(array('error' => false));
                }
            } catch (Exception $e) {
                $this->_helper->json(array('error' => true, 'descricao' => $e->getMessage()));
            }
        } else {
            try {
                $idpronac = $_POST['idpronac'];
                $arquivo = getcwd() . "/public/plenaria/votacao.txt";
                //$arquivo = getcwd() . "/public/plenaria/votacao_".$_POST['idpronac'].".txt";
                if (file_exists($arquivo)) {
                    unlink($arquivo);
                }
                $where = "idNrReuniao = $reuniaoaberta and IdPRONAC = $idpronac";
                $apagar = $votacao->apagar($where);
                $this->_helper->json(array('error' => false));
            } catch (Exception $e) {
                $this->_helper->json(array('error' => true, 'descricao' => $e->getMessage()));
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function verificaArquivosVotacaoAction()
    {
        $this->_helper->layout->disableLayout();

        //********* VERIFICA QUANTOS ARQUIVOS DE VOTACAO FORAM CRIADOS (votacao_XXXXX.txt) *******/
        $qtdeArquivos = 0;
        $arrPronacs = array();
        $diretorio =  getcwd() . "/public/plenaria/";
        try {
            $handle = opendir($diretorio);
            if ($handle) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $arq = strstr($file, 'votacao_');
                        if (!empty($arq)) { //se existe arquivo com iniciado com este nome
                            $qtdeArquivos += 1; //Caso sim, acrescente 1
                            $arr = explode('_', $arq);
                            $arr = explode('.', $arr[1]);
                            $arrPronacs[] = $arr[0]; //recupera valor do pronac que esta no nome do arquivo
                        }
                    }
                }
                closedir($handle); //Fecha a manipulacao
            }
            $this->_helper->json(array('error' => false, 'qtdeArquivos' => $qtdeArquivos, 'arrPronacs' => $arrPronacs));
        } catch (Exception $e) {
            $this->_helper->json(array('error' => true, 'qtdeArquivos' => 0, 'arrPronacs' => $arrPronacs));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public static function ComponentesDaComissao($idpronac)
    {
        $consultaComponente = ConsultaTitulacaoConselheiroDAO::consultaComponente($idpronac);
        if (count($consultaComponente) > 0) {
            return $consultaComponente[0]->Descricao;
        } else {
            return 'Sem Componente';
        }
    }

    public function exibirvotantesAction()
    {
        $reuniao = new Reuniao();
        $vt = new Votante();
        $area = new Area();
        $tc = new TitulacaoConselheiro();
        $usuariosorgao = new Usuariosorgaosgrupos();
        $usuario = new Autenticacao_Model_DbTable_Usuario();
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $Agente = $usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
        $buscarReuniaoAberta = $reuniao->buscarReuniaoAberta();
        
        //$reuniaoaberta = $buscarReuniaoAberta['idNrReuniao'];
        if (isset($_POST['votantes'])) {
            $votantesSelecionados = array_unique($_POST['votantes']);
            $buscarVotante = $vt->buscar(array('idReuniao = ?' => $buscarReuniaoAberta['idNrReuniao']))->count();
            
            if ($buscarVotante > 1) {
                $where = "idReuniao = " . $buscarReuniaoAberta['idNrReuniao'];
                $vt->apagar($where);
            }
            foreach ($votantesSelecionados as $votantesadicionar) {
                $dados = array(
                    'idReuniao' => $buscarReuniaoAberta['idNrReuniao'],
                    'idAgente' => $votantesadicionar
                );
                $vt->inserir($dados);
            }
            parent::message("Votantes cadastrados com sucesso!", "gerenciarpautareuniao/exibirvotantes", "CONFIRM");
        }
        $buscarVotante = $vt->buscar(array('idReuniao = ?' => $buscarReuniaoAberta['idNrReuniao']));
        
        $votanteCadastrado = array();
        foreach ($buscarVotante as $verificarVotante) {
            $votanteCadastrado[] = $verificarVotante->idAgente;
        }
        $buscarArea = $area->buscar();
        $votantes = array();
        foreach ($buscarArea as $area) {
            $c = 0;
            $buscarTitConselheiro = $tc->buscarTitulacaoConselheiro(array('cdArea' => $area->Codigo, 'TC.stConselheiro'=> 'A'));
            foreach ($buscarTitConselheiro as $conselheiro) {
                $tipo = $conselheiro->stTitular == 1 ? 'Titular' : 'Suplente';
                $votantes[$area->Descricao][$c]['conselheiro'] = $conselheiro->nome . " - " . $tipo;
                $votantes[$area->Descricao][$c]['idagente'] = $conselheiro->idAgente;
                $votantes[$area->Descricao][$c]['selecionado'] = in_array($conselheiro->idAgente, $votanteCadastrado) ? true : false;
                $c++;
            }
        }
        $this->view->votantes = $votantes;
        $this->view->alterarvotante = $buscarVotante->count() > 0 ? true : false;

        $whereView = array('gru_codigo = ?' => 133);
        $buscarMembrosNatos = $usuariosorgao->buscarViewUsuariosOrgaoGrupos($whereView);

        $num = 0;
        $idagenteAtual = '';
        $membrosnatos = array();
        foreach ($buscarMembrosNatos as $membros) {
            $Agente = $usuario->getIdUsuario($membros->usu_codigo);
            
            if ($Agente['idagente']) {
                if ($idagenteAtual == $Agente['idagente']) {
                    $idagenteAtual = $Agente['idagente'];
                } else {
                    $membrosnatos[$num]['idAgente'] = $Agente['idagente'];
                    $membrosnatos[$num]['nome'] = $membros->usu_nome;
                    $membrosnatos[$num]['selecionado'] = in_array($Agente['idagente'], $votanteCadastrado) ? true : false;
                    $idagenteAtual = $Agente['idagente'];
                }
            }
            $num++;
        }
        
        $this->view->Plenaria = $buscarReuniaoAberta;
        $this->view->membrosnatos = $membrosnatos;
    }

    public function menuabasAction()
    {
        $constultaReuniao = ConsultaReuniaoDAO::listaReuniao();
        $this->view->consultaReuniao = $constultaReuniao;
    }

    public function verificarvotacaobancoajaxAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $recebidoGet = Zend_Registry::get('get');

        if ($recebidoGet) {
            $enviar = AtualizaReuniaoDAO::verificaReuniao($recebidoGet->idAgente, $recebidoGet->idNrReuniao, true, '');
            if (count($enviar) > 0) {
                foreach ($enviar as $dadosenviar) {
                    $arrayenviar['idpronac'] = $dadosenviar->idPRONAC;
                    $arrayenviar['pronac'] = $dadosenviar->pronac;
                    $arrayenviar['nomeprojeto'] = utf8_encode($dadosenviar->nomeprojeto);
                    $arrayenviar['status'] = 'OK';
                    $arrayenviar['error'] = false;
                }
                $jsonEnviar = json_encode($arrayenviar);

                echo $jsonEnviar;
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                $this->_helper->json(array('error' => true));
            }
        } else {
            $this->_helper->json(array('error' => true));
        }
    }

    public function verificavotacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];

        $reuniao = new Reuniao();
        $buscarReuniaoaberta = $reuniao->buscarReuniaoAberta();
        $reuniaoaberta = $buscarReuniaoaberta['idNrReuniao'];

        $votacao = new Votacao();
        $consolidacaoVotacao = new Consolidacaovotacao();

        $tbVotantes = new Votante();
        $rsVotante = $tbVotantes->buscar(array('idAgente=?'=>$idagente, 'idReuniao=?'=>$reuniaoaberta))->current();

        if (!empty($rsVotante)) {
            $bln_liberarVoto = true;
        } else {
            $bln_liberarVoto = false;
        }

        $caminhoverificarvotacao = getcwd() . "/public/plenaria/votacao.txt";
        //$caminhoverificarvotacao = getcwd() . "/public/plenaria/votacao_".$_POST['idpronac'].".txt"; //codigo mantido para historico
        if (file_exists($caminhoverificarvotacao)) {
            $read = fopen($caminhoverificarvotacao, 'r');
            if ($read) {
                while (($buffer = fgets($read, 4096)) !== false) {
                    $verificavotacao = $buffer;
                }
                fclose($read);
                $verificavotacao = str_replace("'", "", $verificavotacao);
            }
            $dados = json_decode($verificavotacao, true);
            $dados['bln_liberarvoto'] = $bln_liberarVoto; //adiciona informacao no array

            $arrVotacao = array();
            $arrVotacao = $votacao->buscar(array('idAgente = ?' => $idagente, 'idNrReuniao = ?' => $reuniaoaberta, 'dtVoto is null' => '')); //dados da votacao aberta
            if ($arrVotacao->count() > 0) {
                if (isset($dados['idtiporeadequacao'])) {
                    $dados['idpronac'] = $dados['idpronac'].'_'.$dados['idtiporeadequacao'];
                }
                $verificavotacao = json_encode($dados);
                echo $verificavotacao;
            } else {
                if (isset($dados['idtiporeadequacao'])) {
                    $arrVotoComponenteLogado = $votacao->buscar(array('idAgente = ?' => $idagente, 'idNrReuniao = ?' => $reuniaoaberta, 'idPronac = ?' => $dados['idpronac'], 'tpTipoReadequacao = ?' => $dados['idtiporeadequacao']))->current(); //recupera voto do componente
                } else {
                    $arrVotoComponenteLogado = $votacao->buscar(array('idAgente = ?' => $idagente, 'idNrReuniao = ?' => $reuniaoaberta, 'idPronac = ?' => $dados['idpronac']))->current(); //recupera voto do componente
                }

                $arrVotacaoaberta = $votacao->buscar(array('idNrReuniao = ?' => $reuniaoaberta, 'dtVoto is null' => ''));

                $arrayBuscaConsolidacao = array();
                $arrayBuscaConsolidacao['idNrReuniao = ?'] = $reuniaoaberta;
                $arrayBuscaConsolidacao['IdPRONAC = ?'] = $dados['idpronac'];
                if (isset($dados['idtiporeadequacao'])) {
                    $arrayBuscaConsolidacao['tpTipoReadequacao = ?'] = $dados['idtiporeadequacao'];
                }
                $ConsolidacaoVotacao = $consolidacaoVotacao->buscar($arrayBuscaConsolidacao);

                if ($arrVotacaoaberta->count() > 0) {
                    $arrVotacaoaberta = $arrVotacaoaberta->current()->toArray();
                    if (isset($dados['idtiporeadequacao'])) {
                        $dados['idpronac'] = $dados['idpronac'].'_'.$dados['idtiporeadequacao'];
                    }
                    $this->_helper->json(array('error' => false, 'stvoto' => 'ok', 'status' => 'aberta', 'idpronac' => $dados['idpronac'], 'tpvoto' => $arrVotoComponenteLogado['stVoto'], 'bln_liberarvoto' => $bln_liberarVoto));
                } elseif ($ConsolidacaoVotacao->count() == 0) {
                    if (isset($dados['idtiporeadequacao'])) {
                        $dados['idpronac'] = $dados['idpronac'].'_'.$dados['idtiporeadequacao'];
                    }
                    $this->_helper->json(array('error' => false, 'stvoto' => 'ok', 'status' => 'aberta', 'idpronac' => $dados['idpronac'], 'tpvoto' => $arrVotoComponenteLogado['stVoto'], 'bln_liberarvoto' => $bln_liberarVoto));
                } else {
                    $this->_helper->json(array('error' => false, 'stvoto' => 'ok', 'status' => 'completa', 'idpronac' => $dados['idpronac'], 'tpvoto' => $arrVotoComponenteLogado['stVoto'], 'bln_liberarvoto' => $bln_liberarVoto));
                }
            }
        } else {
            $this->_helper->json(array('error' => true, 'status' => 'naoiniciada'));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function verificacronometroAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        if (isset($_POST['idnrreuniao'])) {
            $verificareuniao = AtualizaReuniaoDAO::analisaReuniao($_POST['idnrreuniao']);
            $horaBanco = date('Y-m-d H:i:s', strtotime($verificareuniao[0]->dtFinal));
            $horaadicionada = strtotime($horaBanco . "+ 15 minutes");
            $horaAtual = strtotime("NOW");
            $real = $horaadicionada - $horaAtual;
            $data = date('i:s', $real);
            $dados = array(
                'real' => $real,
                'dataCron' => $data,
                'stPlenaria' => $verificareuniao[0]->stPlenaria
            );
            $jsonEnviar = json_encode($dados);
            echo $jsonEnviar;
        } else {
            $this->_helper->json(array('error' => true));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function verificavotacaocoordenadorAction()
    {
        $this->_helper->layout->disableLayout();
        $reuniao = new Reuniao();
        $buscarReuniaoaberta = $reuniao->buscarReuniaoAberta();
        $reuniaoaberta = $buscarReuniaoaberta['idNrReuniao'];
        $caminhoverificarvotacao = getcwd() . "/public/plenaria/votacao.txt";
        if (file_exists($caminhoverificarvotacao)) {
            $read = fopen($caminhoverificarvotacao, 'r');
            if ($read) {
                while (($buffer = fgets($read, 4096)) !== false) {
                    $verificavotacao = $buffer;
                }
                fclose($read);
            }
            $verificavotacao = str_replace("'", '', $verificavotacao);
            echo $verificavotacao;
        } else {
            $this->_helper->json(array('error' => 'true'));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function atualizarstatusreuniaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idpronac = $_POST['idpronac'];
        $idnrreuniao = $_POST['idnrreuniao'];
        $votacao = new Votacao();
        try {
            $where = "IdPRONAC = $idpronac and idNrReuniao = $idnrreuniao";
            $votacao->excluirVotacao($where);
            $this->_helper->json(array('error' => false));
        } catch (Zend_Db_Table_Exception $e) {
            $this->_helper->json(array('error' => true));
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function verificarvotacaobancoadministrativoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $recebidoGet = Zend_Registry::get('get');
        if ($recebidoGet->idNrReuniao) {
            $enviar = AtualizaReuniaoDAO::verificaReuniaoAdministrativo($recebidoGet->idNrReuniao);
            if (count($enviar) > 0) {
                $this->_helper->json(array('idPRONAC' => $enviar[0]->idPRONAC));
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                $this->_helper->json(array('idPRONAC' => false));
                $this->_helper->viewRenderer->setNoRender(true);
            }
        } else {
            $this->_helper->json(array('idPRONAC' => false));
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function atualizacaoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idpronac = $_POST['idpronac'];
        $IN2017 = $projeto->VerificarIN2017($idpronac);
        $reuniao = new Reuniao();
        $buscareuniao = $reuniao->buscarReuniaoAberta();
        $reuniaoaberta = $buscareuniao['idNrReuniao'];

        $consolidacao = new Consolidacaovotacao();
        $buscarconsolidacao = $consolidacao->verificarConsolidacaoprojeto($reuniaoaberta, $idpronac);
        $votacao = array();
        if ($buscarconsolidacao->count() > 0) {
            foreach ($buscarconsolidacao as $dadosVotacao) {
                $votacao['pronac'] = $dadosVotacao->pronac;
                $votacao['NomeProjeto'] = utf8_encode($dadosVotacao->NomeProjeto);
                $votacao['stAnalise'] = $dadosVotacao->stAnalise;
                $votacao['dsConsolidacao'] = utf8_encode($dadosVotacao->dsConsolidacao);
            }
            $jsonEncode = json_encode($votacao);
            echo $jsonEncode;
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $this->_helper->json(array('error' => true));
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    /**
     * ========== INICIO MODAL ==========
     */

    /**
     * Metodo com o parecer consolidado
     * @access public
     * @param void
     * @return void
     */
    public function parecerconsolidadoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idpronac = $_POST['idpronac'];
        $projeto = new Projetos();
        $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $planilhaprojeto = new PlanilhaProjeto();
        $planilhaAprovacao = new PlanilhaAprovacao();
        $tblParecer = new Parecer_Model_DbTable_Parecer();
        $pt = new Pauta();
        $analiseaprovacao = new AnaliseAprovacao();
        $buscarPronac = $projeto->buscar(array('IdPRONAC = ?' => $idpronac))->current()->toArray();
        $idprojeto = $buscarPronac['idProjeto'];

        $IN2017 = $projeto->VerificarIN2017($idpronac);
        $this->view->IN2017 = $IN2017;
        
        $parecerAtivo = $tblParecer->buscar(array('idPronac=?'=>$idpronac,'stAtivo=?'=>'1'))->current();
        $analiseparecer = $tblParecer->buscar(array('idTipoAgente in (?)'=>array('1','6'), 'TipoParecer=?'=>$parecerAtivo->TipoParecer, 'idPronac=?'=>$idpronac));

        $dadosparecerconsolidado = array();
        $buscarPauta = $pt->buscar(array('idPronac = ?' => $idpronac), array('dtEnvioPauta DESC'))->current();
        if (count($buscarPauta)>0) {
            $buscarPauta = $buscarPauta->toArray();
        } else {
            $buscarPauta = array();
        }

        $dadosparecerconsolidado['DtParecer'] = isset($analiseparecer[1]->DtParecer) ? $analiseparecer[1]->DtParecer : $analiseparecer[0]->DtParecer;
        $dadosparecerconsolidado['ParecerFavoravel'] = isset($analiseparecer[1]->ParecerFavoravel) ? $analiseparecer[1]->ParecerFavoravel : $analiseparecer[0]->ParecerFavoravel;
        $dadosparecerconsolidado['TipoParecer'] = isset($analiseparecer[1]->TipoParecer) ? $analiseparecer[1]->TipoParecer : $analiseparecer[0]->TipoParecer;

        $dadosparecerconsolidado['ParecerParecerista'] = $analiseparecer[0]->ResumoParecer;
        $dadosparecerconsolidado['ParecerComponente'] = isset($analiseparecer[1]->ResumoParecer) ? $analiseparecer[1]->ResumoParecer : ' ';
        $dadosparecerconsolidado['Envioplenaria'] = trim(isset($buscarPauta['dsAnalise']) && $buscarPauta['dsAnalise']) == '' ? 'N&atilde;o existe justificativa para o envio deste projeto para plen&aacute;ria' : @$buscarPauta['dsAnalise'];

        $produtos = $analiseaprovacao->buscarAnaliseProduto('CO', $idpronac);
        $this->view->idpronac = $idpronac;
        $this->view->projeto = $buscarPronac;
        $this->view->ResultRealizarAnaliseProjeto = $dadosparecerconsolidado;

        /**** CODIGO DE READEQUACAO ****/
        /********** MODO ANTIGO ***************/
        //$fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
        //$outrasfontes = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
        //$valorplanilha = $planilhaAprovacao->somarPlanilhaAprovacao($idpronac, 206, 'CO');
        //$valorparecerista = $planilhaprojeto->somarPlanilhaProjeto($idpronac, false);
        //$this->view->fontesincentivo = $fonteincentivo['soma'];
        //$this->view->outrasfontes = $outrasfontes['soma'];
        //$this->view->valorproposta = $fonteincentivo['soma'] + $outrasfontes['soma'];
        //$this->view->valorcomponente = $valorplanilha['soma'];
        //$this->view->valorparecerista = $valorparecerista['soma'];
        /********** FIM - MODO ANTIGO ***************/

        /********** MODO NOVO ***************/
        //TRATANDO SOMA DE PROJETO QUANDO ESTE FOR DE READEQUACAO
        $arrWhereSomaPlanilha = array();
        $arrWhereSomaPlanilha['idPronac = ?']=$idpronac;
        if ($this->bln_readequacao == "false") {
            $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
            $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
            $valorparecerista = $planilhaprojeto->somarPlanilhaProjeto($idpronac, false);
            //$valorplanilha = $planilhaAprovacao->somarPlanilhaAprovacao($idpronac, 206, 'CO');
        } else {
            $arrWhereFontesIncentivo = $arrWhereSomaPlanilha;
            $arrWhereFontesIncentivo['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
            $arrWhereFontesIncentivo['tpPlanilha = ? ']='SR';
            $arrWhereFontesIncentivo['stAtivo = ? ']='N';
            $arrWhereFontesIncentivo['NrFonteRecurso = ? ']='109';
            $arrWhereFontesIncentivo["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWhereFontesIncentivo["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';
            $fonteincentivo = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereFontesIncentivo);

            $arrWhereOutrasFontes = $arrWhereSomaPlanilha;
            $arrWhereOutrasFontes['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
            $arrWhereOutrasFontes['tpPlanilha = ? ']='SR';
            $arrWhereOutrasFontes['stAtivo = ? ']='N';
            $arrWhereOutrasFontes['NrFonteRecurso <> ? ']='109';
            $arrWhereOutrasFontes["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWhereOutrasFontes["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';
            $outrasfontes = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereOutrasFontes);

            $arrWherePlanilhaPA = $arrWhereSomaPlanilha;
            $arrWherePlanilhaPA['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
            $arrWherePlanilhaPA['tpPlanilha = ? ']='PA';
            $arrWherePlanilhaPA['stAtivo = ? ']='N';
            $arrWherePlanilhaPA['NrFonteRecurso = ? ']='109';
            $arrWherePlanilhaPA["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWherePlanilhaPA["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';
            $valorparecerista = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWherePlanilhaPA);
        }

        $arrWhereSomaPlanilha = array();
        $arrWhereSomaPlanilha['idPronac = ?']=$idpronac;
        $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
        $arrWhereSomaPlanilha['tpPlanilha = ? ']='CO';
        $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
        $arrWhereSomaPlanilha['stAtivo = ? ']='S';
        $valorplanilha = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

        $this->view->fontesincentivo = $fonteincentivo['soma'];
        $this->view->outrasfontes = $outrasfontes['soma'];
        $this->view->valorproposta = $fonteincentivo['soma'] + $outrasfontes['soma'];
        $this->view->valorcomponente = $valorplanilha['soma'];
        $this->view->valorparecerista = $valorparecerista['soma'];
        /***************** FIM  - MODO NOVO ********************/
        /**** FIM - CODIGO DE READEQUACAO ****/

        $this->view->ResultProduto = $produtos;
        
        $verificaEnquadramento = RealizarAnaliseProjetoDAO::verificaEnquadramento($idpronac, 'CO', $IN2017);
        if ($IN2017) {
            $this->view->enquadramento = $verificaEnquadramento[0]->stArtigo;
        } elseif (!$IN2017) {
            if ($verificaEnquadramento[0]->stArtigo18 == true) {
                $this->view->enquadramento = 'Artigo 18';
            } elseif ($verificaEnquadramento[0]->stArtigo26 == true) {
                $this->view->enquadramento = 'Artigo 26';
            } else {
                $this->view->enquadramento = 'NAO ENQUADRADO';
            }
        }
        $tbArea = new Area();
        $rsArea = $tbArea->buscar(array('Codigo=?'=>$buscarPronac['Area']))->current();
        $this->view->area = $rsArea->Descricao;

        $tbSegmento = new Segmento();
        $rsSegmento = $tbSegmento->buscar(array('Codigo=?'=>$buscarPronac['Segmento']))->current();
        $this->view->segmento = $rsSegmento->Descricao;
    }

    /**
     * Metodo com o parecer consolidado - Recursos
     * @access public
     * @param void
     * @return void
     */
    public function parecerconsolidadorecursosAction()
    {
        $mapperArea = new Agente_Model_AreaMapper();

        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idPronac = $_POST['idpronac'];

        $tbRecurso = new tbRecurso();
        $dadosRecurso = $tbRecurso->buscar(array('IdPRONAC=?'=>$idPronac, 'siRecurso in (?)'=>array(8,9), 'stEstado=?'=>0))->current();

        if ($dadosRecurso) {
            $dados = $tbRecurso->buscarDadosRecursos(array('idRecurso = ?'=>$dadosRecurso->idRecurso))->current();
            $this->view->dados = $dados;

            $this->view->nmPagina = '';
            if ($dados->siFaseProjeto == 2) {
                if ($dados->tpSolicitacao == 'PI' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR') {
                    $d = array();
                    $d['situacao'] = 'B11';
                    $d['ProvidenciaTomada'] = 'Recurso enviado para avalia��o t�cnica.';
                    $d['dtSituacao'] = new Zend_Db_Expr('GETDATE()');
                    $where = "IdPRONAC = $dados->IdPRONAC";
                    $Projetos = new Projetos();
                    $Projetos->update($d, $where);

                    //ATUALIZA OS DADOS DA TABELA tbAnaliseAprovacao
                    $e = array();
                    $e['stDistribuicao'] = 'I'; // I=Inativo
                    $w = "idPRONAC = $dados->IdPRONAC";
                    $tbDistribuicaoProjetoComissao = new tbDistribuicaoProjetoComissao();
                    $tbDistribuicaoProjetoComissao->update($e, $w);

                    $PlanoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
                    $dadosProdutos = $PlanoDistribuicaoProduto->buscarProdutosProjeto($dados->IdPRONAC);
                    $this->view->produtos = $dadosProdutos;

                    $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
                    $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($dados->IdPRONAC, 4); // 4=Cortes Or�ament�rios Aprovados
                    $this->view->planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, 4); // 4=Cortes Or�ament�rios Aprovados
                }
            }
            if ($dados->tpSolicitacao == 'EN' || $dados->tpSolicitacao == 'EO' || $dados->tpSolicitacao == 'OR' || $dados->tpSolicitacao == 'PI') {
                $Projetos = new Projetos();
                $this->view->projetosEN = $Projetos->buscaAreaSegmentoProjeto($dados->IdPRONAC);

                $this->view->comboareasculturais = $mapperArea->fetchPairs('codigo', 'descricao');
                $objSegmentocultural = new Segmentocultural();
                $this->view->combosegmentosculturais = $objSegmentocultural->buscarSegmento($this->view->projetosEN->cdArea);

                $parecer = new Parecer_Model_DbTable_Parecer();
                $this->view->Parecer = $parecer->buscar(array('IdPRONAC = ?' => $dados->IdPRONAC, 'TipoParecer in (?)' => array(1,7), 'stAtivo = ?' => 1))->current();
            }

            //DADOS DO PROJETO
            $Projetos = new Projetos();
            $p = $Projetos->buscarProjetoXProponente(array('idPronac = ?' => $dados->IdPRONAC))->current();
            $this->view->projeto = $p;
        } else {
            $this->view->dados = array();
        }
    }

    /*
     * Alterada em 13/03/14
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Fun��o criada buscar os dados consolidados da readequa��o.
    */
    public function parecerconsolidadoreadequacoesAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idPronac = $_POST['idpronac'];
        $idReadequacao = $_POST['idreadequacao'];

        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $dadosReadequacao = $Readequacao_Model_DbTable_TbReadequacao->buscar(array('idPronac=?'=>$idPronac, 'idReadequacao=?'=>$idReadequacao, 'siEncaminhamento in (?)'=>array(8,9), 'stEstado=?'=>0))->current();

        if ($dadosReadequacao) {
            $dados = $Readequacao_Model_DbTable_TbReadequacao->buscarDadosReadequacoes(array('idReadequacao = ?'=>$dadosReadequacao->idReadequacao))->current();
            $this->view->dados = $dados;

            $Readequacao_Model_DbTable_TbReadequacaoXParecer = new Readequacao_Model_DbTable_TbReadequacaoXParecer();
            $pareceres = $tbReadequacaoXParecer->buscarPareceresReadequacao(array('a.idReadequacao =?'=>$dados->idReadequacao), array('1'));
            $this->view->Pareceres = $pareceres;
        } else {
            $this->view->dados = array();
        }
    }

    // fecha metodo parecerconsolidadoAction()

    /**
     * Metodo com a Analise de Cortes Sugeridos
     * @access public
     * @param void
     * @return void
     */
    public function analisedecontaAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $planilhaaprovacao = new PlanilhaAprovacao();
        $pt = new Pauta();
        $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $projeto = new Projetos();
        $idpronac = $this->_request->getParam("idpronac");
        $buscarprojeto = $projeto->buscar(array('IdPRONAC = ?' => $idpronac))->current()->toArray();

        if ($this->bln_readequacao == "false") {
            $buscarAnaliseConta = $planilhaaprovacao->buscarAnaliseConta($idpronac, 'CO', array('pap.stAtivo=?'=>'S'));
            // ===== TOTAL VALOR REDUZIDO E TOTAL DE ITENS =====
            $itemReduzido = false;
            $itemRetirado = false;
            $totalValorReduzido = 0;
            $totalItemReduzido = 0;

            $totalValorRetirado = 0;
            $totalItemRetirado = 0;
            $valores['reduzido'] = array();
            $valores['retirado'] = array();
            foreach ($buscarAnaliseConta as $b) {
                $valorproponente = ($b->qtdSolicitado * $b->ocoSolicitado * $b->vlSolicitado);
                $valorcomponente = ($b->ocorrenciaRelator * $b->vlunitarioRelator * $b->qtdRelator);
                $valorparecerista = ($b->ocoParecer * $b->vlParecer * $b->qtdParecer);
                if ($valorcomponente < $valorproponente and $valorcomponente != 0) {
                    $valores['reduzido'][$totalItemReduzido]['idPlanilhaAprovacao'] = $b->idPlanilhaAprovacao;
                    $valores['reduzido'][$totalItemReduzido]['nrFonteRecurso'] = $b->nrFonteRecurso;
                    $valores['reduzido'][$totalItemReduzido]['idProduto'] = $b->idProduto;
                    $valores['reduzido'][$totalItemReduzido]['item'] = $b->Item;
                    $valores['reduzido'][$totalItemReduzido]['idEtapa'] = $b->idEtapa;
                    $valores['reduzido'][$totalItemReduzido]['Etapa'] = $b->Etapa;
                    $valores['reduzido'][$totalItemReduzido]['Produto'] = $b->produto;
                    $valores['reduzido'][$totalItemReduzido]['vlreduzidoComp'] = $valorproponente - $valorcomponente;
                    $valores['reduzido'][$totalItemReduzido]['VlReduzidoParecerista'] = $valorparecerista - $valorproponente;

                    $valores['reduzido'][$totalItemReduzido]['vltotalsolicitado'] = $valorproponente;
                    $valores['reduzido'][$totalItemReduzido]['UnidadeProposta'] = $b->UnidadeProposta;
                    $valores['reduzido'][$totalItemReduzido]['qtdSolicitado'] = $b->qtdSolicitado;
                    $valores['reduzido'][$totalItemReduzido]['ocoSolicitado'] = $b->ocoSolicitado;
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioSolicitado'] = $b->vlSolicitado;
                    $valores['reduzido'][$totalItemReduzido]['diasSolicitado'] = $b->diasSolicitado;

                    $valores['reduzido'][$totalItemReduzido]['idUnidade'] = $b->idUnidade;
                    $valores['reduzido'][$totalItemReduzido]['Unidade'] = $b->Unidade;
                    $valores['reduzido'][$totalItemReduzido]['diasRelator'] = $b->diasRelator;
                    $valores['reduzido'][$totalItemReduzido]['ocorrenciaRelator'] = $b->ocorrenciaRelator;
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioRelator'] = $b->vlunitarioRelator;
                    $valores['reduzido'][$totalItemReduzido]['diasRelator'] = $b->diasRelator;
                    $valores['reduzido'][$totalItemReduzido]['qtdRelator'] = $b->qtdRelator;
                    $valores['reduzido'][$totalItemReduzido]['vltotalcomponente'] = $valorcomponente;
                    $valores['reduzido'][$totalItemReduzido]['justcomponente'] = $b->JSComponente;

                    $valores['reduzido'][$totalItemReduzido]['UnidadeProjeto'] = $b->UnidadeProposta;
                    $valores['reduzido'][$totalItemReduzido]['qtdParecer'] = $b->qtdParecer;
                    $valores['reduzido'][$totalItemReduzido]['ocoParecer'] = $b->ocoParecer;
                    $valores['reduzido'][$totalItemReduzido]['diasParecerista'] = $b->diasParecerista;
                    $valores['reduzido'][$totalItemReduzido]['vltotalparecerista'] = $valorparecerista;
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioparecerista'] = $b->vlParecer;
                    $valores['reduzido'][$totalItemReduzido]['justparecerista'] = $b->JSParecerista;

                    $itemReduzido = true;
                    $reduzido = $valorproponente - $valorcomponente;
                    $totalValorReduzido += (float) $reduzido;
                    $totalItemReduzido++;
                }
                if ($valorcomponente == 0 and $valorproponente > 0) {
                    $valores['retirado'][$totalItemRetirado]['idPlanilhaAprovacao'] = $b->idPlanilhaAprovacao;
                    $valores['retirado'][$totalItemRetirado]['nrFonteRecurso'] = $b->nrFonteRecurso;
                    $valores['retirado'][$totalItemRetirado]['idProduto'] = $b->idProduto;
                    $valores['retirado'][$totalItemRetirado]['item'] = $b->Item;
                    $valores['retirado'][$totalItemRetirado]['idEtapa'] = $b->idEtapa;
                    $valores['retirado'][$totalItemRetirado]['Etapa'] = $b->Etapa;
                    $valores['retirado'][$totalItemRetirado]['Produto'] = $b->produto;
                    $valores['retirado'][$totalItemRetirado]['vlretiradoComp'] = $valorproponente - $valorcomponente;
                    $valores['retirado'][$totalItemRetirado]['VlretiradoParecerista'] = $valorparecerista - $valorproponente;

                    $valores['retirado'][$totalItemRetirado]['vltotalsolicitado'] = $valorproponente;
                    $valores['retirado'][$totalItemRetirado]['UnidadeProposta'] = $b->UnidadeProposta;
                    $valores['retirado'][$totalItemRetirado]['qtdSolicitado'] = $b->qtdSolicitado;
                    $valores['retirado'][$totalItemRetirado]['ocoSolicitado'] = $b->ocoSolicitado;
                    $valores['retirado'][$totalItemRetirado]['vlunitarioSolicitado'] = $b->vlSolicitado;
                    $valores['retirado'][$totalItemRetirado]['diasSolicitado'] = $b->diasSolicitado;

                    $valores['retirado'][$totalItemRetirado]['idUnidade'] = $b->idUnidade;
                    $valores['retirado'][$totalItemRetirado]['Unidade'] = $b->Unidade;
                    $valores['retirado'][$totalItemRetirado]['diasRelator'] = $b->diasRelator;
                    $valores['retirado'][$totalItemRetirado]['qtdRelator'] = $b->qtdRelator;
                    $valores['retirado'][$totalItemRetirado]['ocorrenciaRelator'] = $b->ocorrenciaRelator;
                    $valores['retirado'][$totalItemRetirado]['vlunitarioRelator'] = $b->vlunitarioRelator;
                    $valores['retirado'][$totalItemRetirado]['diasRelator'] = $b->diasRelator;
                    $valores['retirado'][$totalItemRetirado]['vltotalcomponente'] = $valorcomponente;
                    $valores['retirado'][$totalItemRetirado]['justcomponente'] = $b->JSComponente;

                    $valores['retirado'][$totalItemRetirado]['UnidadeProjeto'] = $b->UnidadeProposta;
                    $valores['retirado'][$totalItemRetirado]['qtdParecer'] = $b->qtdParecer;
                    $valores['retirado'][$totalItemRetirado]['ocoParecer'] = $b->ocoParecer;
                    $valores['retirado'][$totalItemRetirado]['diasParecerista'] = $b->diasParecerista;
                    $valores['retirado'][$totalItemRetirado]['vltotalparecerista'] = $valorparecerista;
                    $valores['retirado'][$totalItemRetirado]['vlunitarioparecerista'] = $b->vlParecer;
                    $valores['retirado'][$totalItemRetirado]['justparecerista'] = $b->JSParecerista;

                    $itemRetirado = true;
                    $retirado = $valorproponente - $valorcomponente;
                    $totalValorRetirado += (float) $retirado;
                    $totalItemRetirado++;
                }
            }
        } else {

            /**** CODIGO DE READEQUACAO ****/
            $buscarplanilhaCO = $planilhaaprovacao->buscarAnaliseContaPlanilhaAprovacao($idpronac, 'CO', array('pap.stAtivo=?'=>'S'));
            $buscarAnaliseConta = array();
            $cont = 0;
            foreach ($buscarplanilhaCO as $resuplanilha) {
                $buscarAnaliseConta[$cont]['qtdRelator'] = $resuplanilha->qtItem;
                $buscarAnaliseConta[$cont]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                $buscarAnaliseConta[$cont]['diasRelator'] = $resuplanilha->qtDias;
                $buscarAnaliseConta[$cont]['ocorrenciaRelator'] = $resuplanilha->nrOcorrencia;
                $buscarAnaliseConta[$cont]['vlunitarioRelator'] = $resuplanilha->vlUnitario;
                $buscarAnaliseConta[$cont]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $buscarAnaliseConta[$cont]['idProduto'] = $resuplanilha->idProduto;
                $buscarAnaliseConta[$cont]['idUnidade'] = $resuplanilha->idUnidade;
                $buscarAnaliseConta[$cont]['idEtapa'] = $resuplanilha->idEtapa;
                $buscarAnaliseConta[$cont]['JSComponente'] = $resuplanilha->dsJustificativa;

                $buscarAnaliseConta[$cont]['Unidade'] = $resuplanilha->Unidade;
                $buscarAnaliseConta[$cont]['Item'] = $resuplanilha->Item;
                $buscarAnaliseConta[$cont]['Etapa'] = $resuplanilha->Etapa;
                $buscarAnaliseConta[$cont]['produto'] = $resuplanilha->produto;
                $cont++;
            }

            /******** Planilha aprovacao SR (Proponente - solicitada) ****************/
            $arrBuscaPlanilha = array();
            $arrBuscaPlanilha["pap.stAtivo = ? "] = 'N';
            $arrBuscaPlanilha["pap.idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')"] = '(?)';

            $resuplanilha = null;
            $cont = 0;
            $buscarplanilhaSR = $planilhaaprovacao->buscarAnaliseContaPlanilhaAprovacao($idpronac, 'SR', $arrBuscaPlanilha);
            foreach ($buscarplanilhaSR as $resuplanilha) {
                $buscarAnaliseConta[$cont]['qtdSolicitado']  = $resuplanilha->qtItem;
                $buscarAnaliseConta[$cont]['ocoSolicitado']  = $resuplanilha->nrOcorrencia;
                $buscarAnaliseConta[$cont]['vlSolicitado']   = $resuplanilha->vlUnitario;
                $buscarAnaliseConta[$cont]['diasSolicitado'] = $resuplanilha->qtDias;
                $buscarAnaliseConta[$cont]['UnidadeProposta'] = $resuplanilha->Unidade;
                $cont++;
            }

            /******** Planilha aprovacao PA (Parecerista) ****************/
            $resuplanilha = null;
            $cont = 0;
            $buscarplanilhaPA = $planilhaaprovacao->buscarAnaliseContaPlanilhaAprovacao($idpronac, 'PA', $arrBuscaPlanilha);
            foreach ($buscarplanilhaPA as $resuplanilha) {
                $buscarAnaliseConta[$cont]['qtdParecer']   = $resuplanilha->qtItem;
                $buscarAnaliseConta[$cont]['ocoParecer']   = $resuplanilha->nrOcorrencia;
                $buscarAnaliseConta[$cont]['vlParecer']    = $resuplanilha->vlUnitario;
                $buscarAnaliseConta[$cont]['JSParecerista']   = $resuplanilha->dsJustificativa;
                $buscarAnaliseConta[$cont]['diasParecerista'] = $resuplanilha->qtDias;
                $buscarAnaliseConta[$cont]['UnidadeProjeto'] = $resuplanilha->Unidade;
                $cont++;
            }

            /********************************************************************************/

            // ===== TOTAL VALOR REDUZIDO E TOTAL DE ITENS =====
            $itemReduzido       = false;
            $itemRetirado       = false;
            $totalValorReduzido = 0;
            $totalItemReduzido  = 0;

            $totalValorRetirado = 0;
            $totalItemRetirado  = 0;
            $valores['reduzido'] = array();
            $valores['retirado'] = array();

            foreach ($buscarAnaliseConta as $b) {
                $valorproponente = ($b['qtdSolicitado'] * $b['ocoSolicitado'] * $b['vlSolicitado']);
                $valorcomponente  = ($b['ocorrenciaRelator'] * $b['vlunitarioRelator'] * $b['qtdRelator']);
                $valorparecerista = ($b['ocoParecer'] * $b['vlParecer'] * $b['qtdParecer']);
                if ($valorcomponente < $valorproponente and $valorcomponente != 0) {
                    $valores['reduzido'][$totalItemReduzido]['idPlanilhaAprovacao']         = $b['idPlanilhaAprovacao'];
                    $valores['reduzido'][$totalItemReduzido]['nrFonteRecurso']              = $b['nrFonteRecurso'];
                    $valores['reduzido'][$totalItemReduzido]['idProduto']                   = $b['idProduto'];
                    $valores['reduzido'][$totalItemReduzido]['item']                        = $b['Item'];
                    $valores['reduzido'][$totalItemReduzido]['idEtapa']                     = $b['idEtapa'];
                    $valores['reduzido'][$totalItemReduzido]['Etapa']                       = $b['Etapa'];
                    $valores['reduzido'][$totalItemReduzido]['Produto']                     = $b['produto'];
                    $valores['reduzido'][$totalItemReduzido]['vlreduzidoComp']              = $valorproponente - $valorcomponente ;
                    $valores['reduzido'][$totalItemReduzido]['VlReduzidoParecerista']       = $valorparecerista - $valorproponente;

                    $valores['reduzido'][$totalItemReduzido]['vltotalsolicitado']           = $valorproponente;
                    $valores['reduzido'][$totalItemReduzido]['UnidadeProposta']             = $b['UnidadeProposta'];
                    $valores['reduzido'][$totalItemReduzido]['qtdSolicitado']               = $b['qtdSolicitado'];
                    $valores['reduzido'][$totalItemReduzido]['ocoSolicitado']               = $b['ocoSolicitado'];
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioSolicitado']        = $b['vlSolicitado'];
                    $valores['reduzido'][$totalItemReduzido]['diasSolicitado']              = $b['diasSolicitado'];

                    $valores['reduzido'][$totalItemReduzido]['idUnidade']                   = $b['idUnidade'];
                    $valores['reduzido'][$totalItemReduzido]['Unidade']                     = $b['Unidade'];
                    $valores['reduzido'][$totalItemReduzido]['diasRelator']                 = $b['diasRelator'];
                    $valores['reduzido'][$totalItemReduzido]['ocorrenciaRelator']           = $b['ocorrenciaRelator'];
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioRelator']           = $b['vlunitarioRelator'];
                    $valores['reduzido'][$totalItemReduzido]['diasRelator']                 = $b['diasRelator'];
                    $valores['reduzido'][$totalItemReduzido]['qtdRelator']                  = $b['qtdRelator'];
                    $valores['reduzido'][$totalItemReduzido]['vltotalcomponente']           = $valorcomponente;
                    $valores['reduzido'][$totalItemReduzido]['justcomponente']              = $b['JSComponente'];

                    $valores['reduzido'][$totalItemReduzido]['UnidadeProjeto']              = $b['UnidadeProjeto'];
                    $valores['reduzido'][$totalItemReduzido]['qtdParecer']                  = $b['qtdParecer'];
                    $valores['reduzido'][$totalItemReduzido]['ocoParecer']                  = $b['ocoParecer'];
                    $valores['reduzido'][$totalItemReduzido]['diasParecerista']             = $b['diasParecerista'];
                    $valores['reduzido'][$totalItemReduzido]['vltotalparecerista']          = $valorparecerista;
                    $valores['reduzido'][$totalItemReduzido]['vlunitarioparecerista']       = $b['vlParecer'];
                    $valores['reduzido'][$totalItemReduzido]['justparecerista']             = $b['JSParecerista'];

                    $itemReduzido = true;
                    $reduzido = $valorproponente - $valorcomponente;
                    $totalValorReduzido += (float) $reduzido;
                    $totalItemReduzido++;
                }
                if ($valorcomponente == 0 and $valorproponente > 0) {
                    $valores['retirado'][$totalItemRetirado]['idPlanilhaAprovacao']         = $b['idPlanilhaAprovacao'];
                    $valores['retirado'][$totalItemRetirado]['nrFonteRecurso']              = $b['nrFonteRecurso'];
                    $valores['retirado'][$totalItemRetirado]['idProduto']                   = $b['idProduto'];
                    $valores['retirado'][$totalItemRetirado]['item']                        = $b['Item'];
                    $valores['retirado'][$totalItemRetirado]['idEtapa']                     = $b['idEtapa'];
                    $valores['retirado'][$totalItemRetirado]['Etapa']                       = $b['Etapa'];
                    $valores['retirado'][$totalItemRetirado]['Produto']                     = $b['produto'];
                    $valores['retirado'][$totalItemRetirado]['vlretiradoComp']              = $valorproponente - $valorcomponente ;
                    $valores['retirado'][$totalItemRetirado]['VlretiradoParecerista']       = $valorparecerista - $valorproponente;

                    $valores['retirado'][$totalItemRetirado]['vltotalsolicitado']           = $valorproponente;
                    $valores['retirado'][$totalItemRetirado]['UnidadeProposta']             = $b['UnidadeProposta'];
                    $valores['retirado'][$totalItemRetirado]['qtdSolicitado']               = $b['qtdSolicitado'];
                    $valores['retirado'][$totalItemRetirado]['ocoSolicitado']               = $b['ocoSolicitado'];
                    $valores['retirado'][$totalItemRetirado]['vlunitarioSolicitado']        = $b['vlSolicitado'];
                    $valores['retirado'][$totalItemRetirado]['diasSolicitado']              = $b['diasSolicitado'];

                    $valores['retirado'][$totalItemRetirado]['idUnidade']                   = $b['idUnidade'];
                    $valores['retirado'][$totalItemRetirado]['Unidade']                     = $b['Unidade'];
                    $valores['retirado'][$totalItemRetirado]['diasRelator']                 = $b['diasRelator'];
                    $valores['retirado'][$totalItemRetirado]['qtdRelator']                 = $b['qtdRelator'];
                    $valores['retirado'][$totalItemRetirado]['ocorrenciaRelator']           = $b['ocorrenciaRelator'];
                    $valores['retirado'][$totalItemRetirado]['vlunitarioRelator']           = $b['vlunitarioRelator'];
                    $valores['retirado'][$totalItemRetirado]['diasRelator']                 = $b['diasRelator'];
                    $valores['retirado'][$totalItemRetirado]['vltotalcomponente']           = $valorcomponente;
                    $valores['retirado'][$totalItemRetirado]['justcomponente']              = $b['JSComponente'];

                    $valores['retirado'][$totalItemRetirado]['UnidadeProjeto']              = $b['UnidadeProjeto'];
                    $valores['retirado'][$totalItemRetirado]['qtdParecer']                  = $b['qtdParecer'];
                    $valores['retirado'][$totalItemRetirado]['ocoParecer']                  = $b['ocoParecer'];
                    $valores['retirado'][$totalItemRetirado]['diasParecerista']             = $b['diasParecerista'];
                    $valores['retirado'][$totalItemRetirado]['vltotalparecerista']          = $valorparecerista;
                    $valores['retirado'][$totalItemRetirado]['vlunitarioparecerista']       = $b['vlParecer'];
                    $valores['retirado'][$totalItemRetirado]['justparecerista']             = $b['JSParecerista'];

                    $itemRetirado = true;
                    $retirado = $valorproponente - $valorcomponente;
                    $totalValorRetirado += (float) $retirado;
                    $totalItemRetirado++;
                }
            }//fecha foreach
        }//fecha if bln_readequacao

        $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();

        //ANTIGO MODELO DE SOMA
        //$buscarsomaaprovacao = $planilhaaprovacao->somarPlanilhaAprovacao($idpronac, 206, 'CO');
        //$buscarsomaproposta = $ppr->somarPlanilhaProposta($buscarprojeto['idProjeto']);

        //NOVO MODELO DE SOMA
        /**********************************/
        $arrWhereSomaPlanilha = array();
        $arrWhereSomaPlanilha['idPronac = ?']=$idpronac;
        $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
        $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';

        if ($this->bln_readequacao == "false") {
            //proponente
            $buscarsomaproposta = $tblPlanilhaProposta->somarPlanilhaProposta($buscarprojeto['idProjeto']);

            //componente
            $arrWhereSomaPlanilha['stAtivo = ? ']='S';
            $arrWhereSomaPlanilha['tpPlanilha = ? ']='CO';
            $buscarsomaaprovacao = $planilhaaprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
        } else {
            //proponente
            $arrWhereSomaSR = $arrWhereSomaPlanilha;
            $arrWhereSomaSR['tpPlanilha = ? ']= 'SR';
            $arrWhereSomaSR['stAtivo = ? ']='N';
            $arrWhereSomaSR["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWhereSomaSR["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';
            $buscarsomaproposta = $planilhaaprovacao->somarItensPlanilhaAprovacao($arrWhereSomaSR);

            //componente
            $arrWhereSomaPlanilha['tpPlanilha = ? ']='CO';
            $arrWhereSomaPlanilha['stAtivo = ? ']='S';
            $buscarsomaaprovacao = $planilhaaprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
        }
        /************************************/
        /**** FIM - CODIGO DE READEQUACAO ****/
        $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
        $this->view->analiseReduzido = $valores['reduzido'];
        $this->view->analiseRetirado = $valores['retirado'];
        $this->view->pronac = $buscarprojeto;
        $this->view->idpronac = $idpronac;
        $this->view->itemReduzido = $itemReduzido;
        $this->view->itemRetirado = $itemRetirado;

        $this->view->totValRed = $totalValorReduzido;
        $this->view->totItemRed = $totalItemReduzido;
        $this->view->totValRet = $totalValorRetirado;
        $this->view->totItemRet = $totalItemRetirado;

        $this->view->totalproponente = $buscarsomaproposta['soma'];
        $this->view->totalcomponente = $buscarsomaaprovacao['soma'];
    }

    // fecha metodo analisedecontaAction()

    /**
     * Metodo para realizar a Analise de Conteudo
     * @access public
     * @param void
     * @return void
     */
    public function analisedeconteudoAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idPronac = $_POST['idpronac'];
        $pt = new Pauta();
        $projeto = new Projetos();

        $buscarprojeto = $projeto->buscar(array('IdPRONAC = ?' => $idPronac))->current()->toArray();
        $analise = new AnaliseAprovacao();

        $buscaReadAprovacadoCnic = $pt->buscar(array('IdPRONAC = ?' => $idPronac, 'stAnalise = ?' => "AS"));
        // busca as informacoes de acordo com o id da planilha
        $buscar = $analise->buscarAnaliseProduto('CO', $idPronac);
        // manda para a visao
        $this->view->dados = $buscar;
        $this->view->dadosprojeto = $buscarprojeto;
        $this->view->qtdItens = count($buscar); // quantidade de itens
    }

    // fecha metodo analisedeconteudoAction()

    /**
     * Metodo com a tabela de analise de custos
     * @access public
     * @param void
     * @return void
     */
    public function analisedecustosAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        // recebe os dados via get
        $idpronac = $this->_request->getParam("idpronac");
        $tblPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $tblPlanilhaProjeto = new PlanilhaProjeto();
        $tblPlanilhaAprovacao = new PlanilhaAprovacao();
        $tblProjetos = new Projetos();
        // busca os produtos do projeto
        /*$pt = new Pauta();
        $buscaReadAprovacadoCnic = $pt->buscar(array('IdPRONAC = ?' => $idpronac, 'stAnalise = ?' => "AS"));
        $tipoplanilha = $buscaReadAprovacadoCnic->count() > 0 ? 'SE' : 'CO';*/

        $rsPlanilhaAtual = $tblPlanilhaAprovacao->buscar(array('IdPRONAC = ?'=>$idpronac), array('dtPlanilha DESC'))->current();
        $tipoplanilha = (!empty($rsPlanilhaAtual) && $rsPlanilhaAtual->tpPlanilha == 'SE') ? 'SE' : 'CO';

        if ($this->bln_readequacao == "false") {
            $buscarplanilha = $tblPlanilhaAprovacao->buscarAnaliseCustos($idpronac, $tipoplanilha, array('PAP.stAtivo=?'=>'S'));

            $planilhaaprovacao = array();
            $count = 0;
            $fonterecurso = null;
            foreach ($buscarplanilha as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->diasprop;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->quantidadeprop;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->ocorrenciaprop;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->valorUnitarioprop;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->UnidadeProposta;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->VlSolicitado;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->justificitivaproponente;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->UnidadeProjeto;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->quantidadeparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->ocorrenciaparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->diasparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->valorUnitarioparc;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->VlSugeridoParecerista;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativaParecerista;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtitemcomp;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrocorrenciacomp;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlunitariocomp;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtdiascomp;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->VlSugeridoConselheiro;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativaConselheiro;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa . ' - ' . $resuplanilha->Etapa][$resuplanilha->UF . ' - ' . $resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                $count++;
            }
            $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?' => $idpronac))->current();
            //$buscarsomaaprovacao = $pa->somarPlanilhaAprovacao($idpronac, 206, $tipoplanilha);
            $buscarsomaproposta = $tblPlanilhaProposta->somarPlanilhaProposta($buscarprojeto->idProjeto);
            $buscarsomaprojeto = $tblPlanilhaProjeto->somarPlanilhaProjeto($idpronac);
        } else {

            /**** CODIGO DE READEQUACAO ****/
            $buscarplanilhaCO = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idpronac, 'CO', array('PAP.stAtivo=?'=>'S'));

            $planilhaaprovacao = array();
            $count = 0;
            $fonterecurso = null;
            foreach ($buscarplanilhaCO as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['idPlanilhaAprovacao'] = $resuplanilha->idPlanilhaAprovacao;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['idUnidade'] = $resuplanilha->idUnidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['nrFonteRecurso'] = $resuplanilha->nrFonteRecurso;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['item'] = $resuplanilha->Item;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['Unidade'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['qtitemcomp'] = $resuplanilha->qtItem;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['nrocorrenciacomp'] = $resuplanilha->nrOcorrencia;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlunitariocomp'] = $resuplanilha->vlUnitario;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['qtdiascomp'] = $resuplanilha->qtDias;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['Unidadecomp'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlconselheiro'] = $resuplanilha->vlTotal;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificativaconselheiro'] = $resuplanilha->dsJustificativa;
                //$planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['reducao'] = $resuplanilha->VlSugeridoConselheiro < $resuplanilha->VlSolicitado ? 1 : 0;
                $count++;
            }

            /******** Planilha aprovacao SR (Proponente - solicitada) ****************/
            $arrBuscaPlanilha = array();
            $arrBuscaPlanilha["pap.stAtivo = ? "] = 'N';
            $arrBuscaPlanilha["pap.idPedidoAlteracao = (SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')"] = '(?)';

            $resuplanilha = null;
            $count = 0;
            $buscarplanilhaSR = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idpronac, 'SR', $arrBuscaPlanilha);
            foreach ($buscarplanilhaSR as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;

                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['diasprop'] = $resuplanilha->qtDias;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['quantidadeprop'] = $resuplanilha->qtItem;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['ocorrenciaprop'] = $resuplanilha->nrOcorrencia;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['valorUnitarioprop'] = $resuplanilha->vlUnitario;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['UnidadeProposta'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlproponente'] = $resuplanilha->vlTotal;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificitivaproponente'] = $resuplanilha->dsJustificativa;

                $valorConselheiro = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlconselheiro'];
                $valorSolicitado  = $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlproponente'];
                $reducao = $valorConselheiro < $valorSolicitado ? 1 : 0;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['reducao'] = $reducao;
                $count++;
            }

            /******** Planilha aprovacao PA (Parecerista) ****************/
            $resuplanilha = null;
            $count = 0;
            $buscarplanilhaPA = $tblPlanilhaAprovacao->buscarAnaliseCustosPlanilhaAprovacao($idpronac, 'PA', $arrBuscaPlanilha);
            foreach ($buscarplanilhaPA as $resuplanilha) {
                $produto = $resuplanilha->Produto == null ? 'Administra&ccedil;&atilde;o do Projeto' : $resuplanilha->Produto;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['UnidadeProjeto'] = $resuplanilha->Unidade;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['quantidadeparc'] = $resuplanilha->qtItem;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['ocorrenciaparc'] = $resuplanilha->nrOcorrencia;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['diasparc'] = $resuplanilha->qtDias;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['valorUnitarioparc'] = $resuplanilha->vlUnitario;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['vlparecerista'] = $resuplanilha->vlTotal;
                $planilhaaprovacao[$resuplanilha->FonteRecurso][$produto][$resuplanilha->idEtapa.' - '.$resuplanilha->Etapa][$resuplanilha->UF.' - '.$resuplanilha->Cidade][$count]['justificativaparecerista'] = $resuplanilha->dsJustificativa;
                $count++;
            }

            $buscarprojeto = $tblProjetos->buscar(array('IdPRONAC = ?'=>$idpronac))->current();

            $arrWhereSomaPlanilha = array();
            $arrWhereSomaPlanilha['idPronac = ?']=$idpronac;
            $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
            $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
            $arrWhereSomaPlanilha['stAtivo = ? ']='N';
            $arrWhereSomaPlanilha["idPedidoAlteracao = (?)"] = new Zend_Db_Expr("(SELECT TOP 1 max(idPedidoAlteracao) from SAC.dbo.tbPlanilhaAprovacao where IdPRONAC = '{$idpronac}')");
            $arrWhereSomaPlanilha["tpAcao <> ('E') OR tpAcao IS NULL "]   = '(?)';

            $arrWhereSomaPlanilha['tpPlanilha = ? ']='SR';
            $buscarsomaproposta = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
            $arrWhereSomaPlanilha['tpPlanilha = ? ']='PA';
            $buscarsomaprojeto = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);
        }//feacha if bln_readequacao
        /**** FIM - CODIGO DE READEQUACAO ****/

        $arrWhereSomaPlanilha = array();
        $arrWhereSomaPlanilha['idPronac = ?']=$idpronac;
        $arrWhereSomaPlanilha['idPlanilhaItem <> ? ']='206'; //elaboracao e agenciamento
        $arrWhereSomaPlanilha['tpPlanilha = ? ']='CO';
        $arrWhereSomaPlanilha['NrFonteRecurso = ? ']='109';
        $arrWhereSomaPlanilha['stAtivo = ? ']='S';
        $buscarsomaaprovacao = $tblPlanilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereSomaPlanilha);

        $buscarPlanilhaUnidade = PlanilhaUnidadeDAO::buscar();
        $this->view->planilhaUnidade = $buscarPlanilhaUnidade;
        $this->view->planilha = $planilhaaprovacao;
        $this->view->projeto = $buscarprojeto;
        $this->view->totalcomponente = $buscarsomaaprovacao['soma'];
        $this->view->totalparecerista = $buscarsomaprojeto['soma'];
        $this->view->totalproponente = $buscarsomaproposta['soma'];
    }

    // fecha metodo analisedecustosAction()

    public function dadosproponenteAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idpronac = $this->_request->getParam("idpronac");

        $geral = new ProponenteDAO();
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
        $this->view->ativos = $tbativos;
        $this->view->idpronac = $idpronac;*/
    }


    public function diligenciasAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idPronac = $this->_request->getParam("idpronac");
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        if (!empty($idPronac)) {
            $tblProjeto        = new Projetos();
            $tblPreProjeto      = new Proposta_Model_DbTable_PreProjeto();
            $projeto = $tblProjeto->buscar(array('IdPRONAC = ?' => $idPronac));

            if (isset($projeto[0]->idProjeto) && !empty($projeto[0]->idProjeto)) {
                $this->view->diligenciasProposta = $tblPreProjeto->listarDiligenciasPreProjeto(array('pre.idPreProjeto = ?' => $projeto[0]->idProjeto,'aval.ConformidadeOK = ? '=>0));
            }
            $this->view->diligencias = $tblProjeto->listarDiligencias(array('pro.IdPRONAC = ?' => $idPronac, 'dil.stEnviado = ?' => 'S'));
        }
        $this->view->idPronac = $idPronac;
    }


    /**
     * Metodo para aprovar parecer
     * @access public
     * @param void
     * @return void
     */
    public function aprovarparecerAction()
    {
        $pauta = new Pauta();
        $reuniao = new Reuniao();
        $reuniaoaberta = $reuniao->buscarReuniaoAberta();
        $idreuniaoaberta = $reuniaoaberta['idNrReuniao'];

        if (isset($_POST['stEnvioPlenaria'])) {
            $dadosalterar = array(
                'stEnvioPlenario' => $_POST['stEnvioPlenaria'],
                'dtEnvioPauta' => date("Y-m-d H:i:s")
            );
            $where = "IdPRONAC = " . $_POST['idPronac'] . " and idNrReuniao = " . $idreuniaoaberta;
            $alterar = $pauta->alterar($dadosalterar, $where);
            if ($_POST['stEnvioPlenaria'] == 'S') {
                parent::message('Projeto submetido &agrave; plen&aacute;ria com sucesso!', "gerenciarpautareuniao/gerenciarpautareuniao", "CONFIRM");
            }
            if ($_POST['stEnvioPlenaria'] == 'N') {
                parent::message('Projeto retirado da plen&aacute;ria com sucesso!', "gerenciarpautareuniao/gerenciaradministrativo", "CONFIRM");
            }
        }
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $idpronac = $_POST['idpronac'];
        $buscapauta = $pauta->buscar(array('IdPRONAC = ?' => $idpronac, 'idNrReuniao = ?'=>$idreuniaoaberta))->current();
        if (count($buscapauta)>0) {
            $buscapauta = $buscapauta->toArray();
        } else {
            $buscapauta = array();
        }
        $tipodeacao = $buscapauta['stEnvioPlenario'] == 'N' ? 'submeter' : 'retirar';
        $this->view->tipoacao = $tipodeacao;
        $tbProjeto = new Projetos();
        $rsProjeto = $tbProjeto->buscar(array('IdPRONAC=?'=>$idpronac))->current();
        $this->view->pauta = $buscapauta;
        $this->view->nrPronac = $rsProjeto->AnoProjeto.$rsProjeto->Sequencial;
    }

    public function ParecerComponente($idpronac)
    {
        $projetoAtual = $projeto->buscar(array('IdPRONAC = ?' => $idpronac))->current()->toArray();
        $idprojeto = $projetoAtual['idProjeto'];
        $buscarPlano = $planoDistribuicao->buscar(array('idProjeto = ?' => $projetoAtual['idProjeto'], 'stPrincipal= ?' => 1))->current()->toArray();
        $buscarAnaliseAp = $analiseaprovacao->buscar(array('IdPRONAC = ?' => $idpronac, 'idProduto = ?' => $buscarPlano['idProduto'], 'tpAnalise = ?' => $tpAnalise));

        //VALOR DA PROPOSTA
        $planilhaproposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $fonteincentivo = $planilhaproposta->somarPlanilhaProposta($idprojeto, 109);
        $outrasfontes   = $planilhaproposta->somarPlanilhaProposta($idprojeto, false, 109);
        $this->view->valorproposta = $fonteincentivo['soma'] + $outrasfontes['soma'];

        //VALOR TOTAL DO PROJETO
        $planilhaAprovacao = new PlanilhaAprovacao();
        $valorProjeto = $planilhaAprovacao->somarPlanilhaAprovacao($idpronac, 206, 'CO');
        $this->view->totalsugerido = $valorProjeto['soma'] ? $valorProjeto['soma'] :0; //valor total do projeto (Planilha Aprovacao)

        if ($buscarAnaliseAp->count() > 0) {
            $buscarAnaliseAp = $buscarAnaliseAp->current()->toArray();
            //$aprovacao['planilhaprovacao'] = 0;
            //$aprovacao['planilhaprojeto'] = 0;
            if ($buscarAnaliseAp['stAvaliacao'] == 1) {
                //CODIGO ANTIGO
                /*$buscaraprovacao  = $planilhaAprovacao->CompararPlanilha($idpronac, $tpPlanilha);
                foreach($buscaraprovacao as $resu){
                    $aprovacao['planilhaprovacao'] += $resu->planilhaaprovacao;
                    $aprovacao['planilhaprojeto'] += $resu->planilhaprojeto;
                }
                $aprovacao['planilhaprovacao'] = $aprovacao['planilhaprovacao'] != 0 ? $aprovacao['planilhaprovacao'] : 1;
                $valoraprovacao = $aprovacao['planilhaprojeto'] * 0.5; */
                $valoraprovacao = $this->view->valorproposta * 0.5;
                if ($valoraprovacao >= $this->view->totalsugerido) {
                    $parecer = 'NAO';
                } else {
                    $parecer = 'SIM';
                }
            } else {
                $parecer = 'NAO';
            }
        } else {
            $parecer = 'NAO';
        }
        return $parecer;
    }

    public function listaprojetoscnicAction()
    {
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $post = Zend_Registry::get('post');
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $stenvioplenaria = $this->_request->getParam('stenvioplenaria');
        $grid = $this->_request->getParam("grid"); // pega o tipo da grid que deve ser carregada
        $ordenacaoVotado = new Zend_Session_Namespace('ordenacaoVotado'); // cria a sessao para manter a ordenacao da grid
        $ordenacaoNaoPauta = new Zend_Session_Namespace('ordenacaoNaoPauta'); // cria a sessao para manter a ordenacao da grid
        $where = array();
        $readequacao = $this->_request->getParam('readequacao');
        $plenaria = $this->_request->getParam('plenaria');
        $rsProjetosNaoAnalisados = array();
        $rsProjetosVotados = array();
        $qntdPlenariaRecursos = array();
        $projetosRecursos = array();
        $qntdPlenariaReadequacoes = array();
        $projetosReadequacoes = array();
        $rsProjetosEmPauta = array();
        $countProjetosEmPauta = 0;

        $idagente = GerenciarPautaReuniaoDAO::consultaAgenteUsuario($auth->getIdentity()->usu_codigo);
        $idagente = $idagente['idAgente'];

        $tbPauta = new tbPauta();
        $reuniao = new Reuniao();
        $raberta = $reuniao->buscarReuniaoAberta();
        $votacao = new Votacao();
        $tbRecurso = new tbRecurso();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $idNrReuniao = $raberta['idNrReuniao'];
        $ordenacao = array(10,4); //ORDENANDO POR NOME DO COMPONENTE E PRONAC

        //GRID - PROJETO SUBMETIDOS A PLENARIA - PLANO ANUAL
        if ($grid == "planoanual") {
            $view = "listar-projetos-plenaria-planoanual.phtml";
            $stPlanoAnual = '1';

            //GRID - PROJETO SUBMETIDOS A PLENARIA - RECURSO
        } elseif ($grid == "recurso") {
            $view = "listar-projetos-plenaria-recurso.phtml";
            $projetosRecursos = $tbRecurso->buscarRecursosEnviadosPlenaria($idNrReuniao);
            $qntdPlenariaRecursos = $projetosRecursos->count();

            //GRID - PROJETO SUBMETIDOS A PLENARIA - READEQUA��O
        } elseif ($grid == "readequacao") {
            $view = "listar-projetos-plenaria-readequacao.phtml";
            $projetosReadequacoes = $Readequacao_Model_DbTable_TbReadequacao->buscarReadequacoesEnviadosPlenaria($idNrReuniao);
            $qntdPlenariaReadequacoes = $projetosReadequacoes->count();

            //GRID - PROJETOS VOTADOS
        } elseif ($grid == "votado") {
            $view = "listar-projetos-plenaria-votado.phtml";
            $stPlanoAnual = '0';

            $arrBuscaVotados = array();
            $arrBuscaVotados['cv.idNrReuniao = ?'] = $idNrReuniao;
            $arrBuscaVotados['tp.idNrReuniao = ?'] = $idNrReuniao;
            if ($GrupoAtivo->codGrupo == '118' || $GrupoAtivo->codGrupo == '133') { //118 = componente da comissao  133 = membros natos
                $arrBuscaVotados['vt.idAgente = ?'] = $idagente;
            } else {
                $arrBuscaVotados['vt.idAgente = (?)'] = new Zend_Db_Expr('(SELECT TOP 1 max(idAgente) from BDCORPORATIVO.scSAC.tbVotacao where IdPRONAC = pr.IdPRONAC)');
            }
            $arrBuscaVotados['tp.idNrReuniao = ?'] = $idNrReuniao;
            $arrBuscaVotados['vt.idNrReuniao = ?'] = $idNrReuniao;
            $arrBuscaVotados['par.stAtivo = ?'] = 1;
            if (!empty($readequacao) &&  $readequacao == 'true') {
                $arrBuscaVotados['par.TipoParecer <> ?'] = 1; /**parecer de readequacao**/
            } else {
                $arrBuscaVotados['par.TipoParecer = ?'] = 1; /**parecer de analise inicial**/
            }
            $rsProjetosVotados = $tbPauta->buscarProjetosVotadosCnic($arrBuscaVotados, $ordenacaoVotado->ordemVotado);

            //GRID - PROJETO SUBMETIDOS A PLENARIA /OU/ NAO SUBMETIDOS
        } else {
            if ($grid == "pautaNaoPlenaria") { //NAO SUBMETIDOS
                $view = "listar-projetos-nao-plenaria.phtml";
            } elseif ($grid == "naoPauta") { //NAO SUBMETIDOS - NAO ANALISADOS
                $view = "listar-projetos-nao-pauta.phtml";
                $tblDistribuicao = new tbDistribuicaoProjetoComissao();
                $arrReuniao['idNrReuniao IS NULL ']= "?";

                $whereNaoAnalisados = array();
                if (!empty($readequacao) &&  $readequacao == 'true') {
                    $whereNaoAnalisados['par.TipoParecer <> ?'] = 1; /**parecer de readequacao**/
                } else {
                    $whereNaoAnalisados['par.TipoParecer = ?'] = 1; /**parecer de analise inicial**/
                }

                $rsProjetosNaoAnalisados = $tblDistribuicao->buscarProjetoEmPauta($whereNaoAnalisados, $ordenacaoNaoPauta->ordemNaoPauta, null, null, false, "N&atilde;o analisado", $arrReuniao);
            } else { //SUBMETIDOS
                $view = "listar-projetos-plenaria.phtml";
                $stPlanoAnual = '0';
            }
        }
        //$buscarProjetoPauta = $pauta->PautaReuniaoAtual($idNrReuniao);

        if ($grid != "recurso" && $grid != "readequacao") {

            //RECUPERA PROJETOS INCLUIDOS NA PAUTA DA REUNIAO ATUAL - PLENARIA
            $where['tp.idNrReuniao = ?'] = $idNrReuniao;
            $where['par.stAtivo = ?'] = 1;
            $where['dpc.stDistribuicao = ?'] = 'A';
            $where["tp.stAnalise not in ('AS', 'IS', 'AR')"] = '?';
            if ($grid != "pautaNaoPlenaria" && $grid != "naoPauta") {
                $where["tp.stPlanoAnual = ?"] = $stPlanoAnual;
            }

            //BUSCAR PROJETOS DE READEQUACAO
            if (!empty($readequacao) &&  $readequacao == 'true') {
                //$arrBusca['par.TipoParecer IN (?)'] = array('2','4');
                $where['par.TipoParecer <> ?'] = 1; //parecer de readequacao
                $readequacao = "true";
            } else {
                $where['par.TipoParecer = ?'] = 1; //parecer de analise inicial
                $readequacao = "false";
            }

            //BUSCAR PROJETOS NAO SUBMETIDOS A PLENARIA
            if (empty($plenaria) || $plenaria == "true") {
                $where['tp.stEnvioPlenario = ?'] = 'S'; //projeto submetido a plenaria
                $plenaria = "true";
            } else {
                $where['tp.stEnvioPlenario <> ?'] = 'S'; //projeto nao submetido a plenaria
                $plenaria = "false";
            };
            //$where["pr.idPronac in (?)"] = $arrPronacs;
            $rsProjetosEmPauta = $tbPauta->buscarProjetosEmPautaReuniaoCnic($where, $ordenacao);
            $countProjetosEmPauta = $rsProjetosEmPauta->count();
        }

        /*
         * CODIGO NOVO PARA VERIFICAR SE UM PROJETO AINDA ESTA EM VOTACAO PARA MOSTRA O Play OU Stop NO PAINEL DO PRESIDENTE CNIC
         */
        $idPronacEmVotacao = null;
        $arquivoProjetoEmVotacao = getcwd() . "/public/plenaria/votacao.txt";
        if (file_exists($arquivoProjetoEmVotacao)) {
            $verificavotacao = null;
            $read = fopen($arquivoProjetoEmVotacao, 'r');
            if ($read) {
                while (($buffer = fgets($read, 4096)) !== false) {
                    $verificavotacao = $buffer;
                }
                fclose($read);
                $verificavotacao = str_replace("'", "", $verificavotacao);
            }
            $dados = json_decode($verificavotacao, true);
            if (count($dados)>0) {
                $idPronacEmVotacao = $dados['idpronac'];
            }
        }

        //BUSCA PROJETO QUE ESTEJA COM DT DE VOTACAO NULA PARA IDENTIFICAR QUE ETE PROJETO AINDA ESTA EM VOTACAO
        $rsProjetosEmVotacao = $votacao->buscar(array('idNrReuniao = ?' => $idNrReuniao, 'dtVoto is null' => ''));
        //$rsProjetosEmVotacao = $votacao->buscar(array('idNrReuniao = ?' => $idNrReuniao));
        $arrPronacs = array();
        if ($rsProjetosEmVotacao->count() > 0) {
            $rsProjetosEmVotacao = $rsProjetosEmVotacao->current()->toArray();
            if ($rsProjetosEmVotacao['tpVotacao'] == 3) { //Se for readequa��o
                $idPronacEmVotacao = $rsProjetosEmVotacao['IdPRONAC'].'_'.$rsProjetosEmVotacao['tpTipoReadequacao'];
            } else {
                $idPronacEmVotacao = $rsProjetosEmVotacao['IdPRONAC'];
            }
        }

        //BUSCAR ULTIMO PROJETO VOTADO
        $order = array('dtVoto DESC');
        $rsUltimoProjetoVotado = $votacao->buscar(array('idNrReuniao = ?' => $idNrReuniao), $order)->current();
        //x($rsUltimoProjetoVotado);
        if (!empty($rsUltimoProjetoVotado)) {
            $tbConsolidacao = new Consolidacaovotacao();
            $arrBuscaConsolidacao = array();
            $arrBuscaConsolidacao['idNrReuniao = ?'] = $idNrReuniao;
            $arrBuscaConsolidacao['IdPRONAC = ?'] = $rsUltimoProjetoVotado->IdPRONAC;
            $rsConsolidacao = $tbConsolidacao->buscar($arrBuscaConsolidacao)->current();

            if (empty($rsConsolidacao)) {
                if ($rsUltimoProjetoVotado['tpVotacao'] == 3) { //Se for readequa��o
                    $idPronacEmVotacao = $rsUltimoProjetoVotado['IdPRONAC'].'_'.$rsUltimoProjetoVotado['tpTipoReadequacao'];
                } else {
                    $idPronacEmVotacao = $rsUltimoProjetoVotado['IdPRONAC'];
                }
            }
        }

        $grupoativo = $GrupoAtivo->codGrupo;
        $this->montaTela(
                'gerenciarpautareuniao/'.$view,
            array(
                //'projetosplenaria' => $plenario['plenario'],
                'projetosplenaria' => $rsProjetosEmPauta,
                'projetosplenariarecurso' => $projetosRecursos,
                'projetosplenariareadequacao' => $projetosReadequacoes,
                'projetosplenarianaoanalisado' => $rsProjetosNaoAnalisados,
                'projetosvotados' => $rsProjetosVotados,
                'grupoativo' => $grupoativo,
                'pronacvotacaoatual' => $idPronacEmVotacao,
                //'arrPronacVotacaoAtual' => $arrPronacs,
                'stPlenaria' => $raberta['stPlenaria'],
                'qtdplenario' => $countProjetosEmPauta,
                'qtdplenariorecurso' => $qntdPlenariaRecursos,
                'qtdplenarioreadequacao' => $qntdPlenariaReadequacoes,
                'qtdNaoAnalisado' => count($rsProjetosNaoAnalisados),
                'qtdvotados' => count($rsProjetosVotados),
                'parametrosBusca' => $_POST,
                'readequacao' => $readequacao,
                'plenaria' => $plenaria
                )
        );
    }

    public function recursosNaoSubmetidosAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
        $where['a.siRecurso = ?'] = 9; // 9=N�o submetidos a plen�ria - Checklist Publica��o

        $tbRecurso = new tbRecurso();
        $recursos = $tbRecurso->recursosNaoSubmetidos($where, array());

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $this->view->conselheiros = $tbTitulacaoConselheiro->buscarConselheirosTitulares();
        $this->view->dados = $recursos;
    }

    /*
     * Alterada em 13/03/14
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     * Fun��o criada acessar as readequa��es que n�o foram submetidas � plen�ria.
    */
    public function readequacoesNaoSubmetidasAction()
    {
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $this->view->grupoAtivo = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuario para a visao

        /* ================== PAGINACAO ======================*/
        $where = array();
        $where['a.stEstado = ?'] = 0; // 0=Atual; 1=Historico
        $where['a.siEncaminhamento = ?'] = 9; // 9=N�o submetidos a plen�ria - Checklist Publica��o

        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $readequacoes = $Readequacao_Model_DbTable_TbReadequacao->readequacoesNaoSubmetidas($where, array());

        //$tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        //$this->view->conselheiros = $tbTitulacaoConselheiro->buscarConselheirosTitulares();
        $this->view->dados = $readequacoes;
    }

    public function projetosvotadosAction()
    {
        $reuniao = new Reuniao();
        $raberta = $reuniao->buscarReuniaoAberta();
        $idNrReuniao = $raberta['idNrReuniao'];

        $tbPauta = new tbPauta();
        $dados = $tbPauta->buscaProjetosAprovados($idNrReuniao);
        $this->view->projetos = $dados;
    }

    public function paEncerrarCnic($idNrReuniao)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('mssql.timeout', 10485760000);

        //passa o trabalho de executar a procedure para um arquivo externo a aplicacao
        //$cmd = "php /var/www/salic/public/plenaria/exec_paEncerrarCNIC.php idReuniao={$idNrReuniao} > /dev/null &"; //linux
        $cmd = "php ".getcwd()."/public/plenaria/exec_paEncerrarCNIC.php idReuniao={$idNrReuniao} > /dev/null &"; //linux
        exec($cmd);

        //$cmd = "c:/xamp/php/php.exe c:/xamp/htdocs/procedure.php"; // windows
        //Proc_close(Proc_open("start /B ". $cmd, "r")); // windows
        // Executar em segundo plano em Windows
        //$shell = new COM('WScript.Shell');
        //$shell->run('php c:/xamp/htdocs/procedure.php', 0, false);

        return true;
    }

    public function paEncerrarCnicAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $this->_helper->viewRenderer->setNoRender(true);
        $idNrReuniao = $this->_request->getParam('idReuniao');
        $sp = new paEncerrarCNIC();
        $sp->execSP($idNrReuniao);
    }

    // fecha metodo aprovarparecerAction()
}
