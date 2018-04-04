<?php

class VerificarSolicitacaoDeReadequacoesController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        // verifica as permiss�es
        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 94;  // Parecerista
        $PermissoesGrupo[] = 93;  // Coordenador de Parecerista
        $PermissoesGrupo[] = 121; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 129; // T�cnico de Acompanhamento
        $PermissoesGrupo[] = 122; // Coordenador de Acompanhamento
        $PermissoesGrupo[] = 123; // Coordenador Geral de Acompanhamento

        parent::perfil(1, $PermissoesGrupo);

        parent::init();
        // chama o init() do pai GenericControllerNew
    }

    public function indexAction()
    {
        $idPronac = $_GET['idPronac'];
        
        $auth = Zend_Auth::getInstance();
        //$idSolicitante = $auth->getIdentity()->usu_codigo;
        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;


        $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO;
        $resultadoEtapa = $buscaInformacoes->buscarEtapa();
        $this->view->buscaetapa = $resultadoEtapa;


        foreach ($resultadoEtapa as $idEtapa) {
            $resultadoProdutosItens = $buscaInformacoes->buscarProdutosItens($idPronac, $idEtapa->idPlanilhaEtapa, null, "N");
            $valorProduto[$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItens;
        }
        $this->view->buscaprodutositens = $valorProduto;
    }

    public function planilhaaprovadaAction()
    {
        $idPronac = $_GET['idPronac'];
        $auth = Zend_Auth::getInstance();
        //$idSolicitante = $auth->getIdentity()->usu_codigo;
        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;


        /*        $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO;
                $SolicitarReadequacaoCustoDAO = new SolicitarReadequacaoCustoDAO();
                $resultadoEtapa = $buscaInformacoes->buscarEtapa();
                $this->view->buscaetapa = $resultadoEtapa;


                $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutos($idPronac)->toArray();

                if ( empty ( $resultadoProduto ) )
                {
                    $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutosAprovados($idPronac);
                }
                else
                {
                    $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutos($idPronac);
                }

                $this->view->buscaproduto = $resultadoProduto;

                //var_dump($resultadoProduto);die;

                foreach ($resultadoProduto as $idProduto) {
                    foreach ($resultadoEtapa as $idEtapa) {
                        $resultadoProdutosItens = $buscaInformacoes->buscarProdutosItens($idPronac, $idEtapa->idPlanilhaEtapa, NULL, "S", $idProduto->idProduto);
                        $valorProduto[$idProduto->idProduto][$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItens;
                        $resultadoProdutosItensAdm = $buscaInformacoes->buscarProdutosItensSemProduto($idPronac, $idEtapa->idPlanilhaEtapa, NULL, "S");
                        $valorProdutoAdm[$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItensAdm;
                    }
                }
                $this->view->buscaprodutositens = $valorProduto;
                $this->view->buscaprodutositensadm = $valorProdutoAdm;*/

        $orderPlanilha       = array('PAP.NrFonteRecurso ASC', 'PAP.idProduto ASC', 'PAP.idEtapa ASC', 'FED.Sigla ASC', 'CID.Descricao ASC', 'I.Descricao ASC');
        $whereAP             = array('PAP.tpPlanilha = ?' => 'CO', 'PAP.stAtivo = ?' => 'S', 'PAP.IdPRONAC = ?' => $idPronac);
        $tbPlanilhaAprovacao = new PlanilhaAprovacao();
        $buscarAP            = $tbPlanilhaAprovacao->buscarCustosReadequacao($whereAP, $orderPlanilha);

        // monta a planilha aprovada
        $planAP = array();
        $cont = 0;
        foreach ($buscarAP as $r) :
                $produto = empty($r->Produto) ? 'Administra&ccedil;&atilde;o do Projeto' : $r->Produto;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaAprovacao'] = $r->idPlanilhaAprovacao;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrFonteRecurso']      = $r->nrFonteRecurso;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['FonteRecurso']        = $r->FonteRecurso;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idProduto']           = $r->idProduto;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Produto']             = $r->Produto;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idEtapa']             = $r->idEtapa;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Etapa']               = $r->Etapa;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['UF']                  = $r->UF;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Cidade']              = $r->Cidade;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idPlanilhaItem']      = $r->idPlanilhaItem;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Item']                = $r->Item;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['idUnidade']           = $r->idUnidade;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['Unidade']             = $r->Unidade;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtItem']              = (int) $r->qtItem;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['nrOcorrencia']        = (int) $r->nrOcorrencia;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlUnitario']          = $r->vlUnitario;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['vlTotal']             = $r->vlTotal;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['qtDias']              = $r->qtDias;
        $planAP[$r->FonteRecurso][$produto][$r->idEtapa . ' - ' . $r->Etapa][$r->UF . ' - ' . $r->Cidade][$cont]['dsJustificativa']     = $r->dsJustificativa;
        $cont++;
        endforeach;

        // manda as informa��es para a vis�o
        $this->view->planAP = $planAP;
    }

    public function planilhasolicitadaAction()
    {
        $idPronac = isset($_GET['idPronac']) ? $_GET['idPronac'] : '';
        $auth = Zend_Auth::getInstance();

        $verificaIdPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::verificaPedidoAlteracao($idPronac);
        $idpedidoalteracao = $verificaIdPedidoAlteracao[0]->idPedidoAlteracao;

        if (empty($_POST)) {
            $resultadoItem = VerificarSolicitacaodeReadequacoesDAO::verificaPlanilhaAprovacao($idPronac);
            if (empty($resultadoItem)) {
                $inserirCopiaPlanilha = VerificarSolicitacaodeReadequacoesDAO::inserirCopiaPlanilha($idPronac, $idpedidoalteracao);
            }
        }

        $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO;
        $resultadoOrcamento = $buscaInformacoes->verificaMudancaOrcamentaria($idPronac);
        $this->view->buscaorcamento = $resultadoOrcamento;

        //$idSolicitante = $auth->getIdentity()->usu_codigo;
        $buscaprojeto = new ReadequacaoProjetos();
        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;

        // ========== IN�CIO MENSAGEM DE REDU��O, COMPLEMENTO OU REMANEJAMENTO ==========
        $buscaProjetoProduto = new SolicitarReadequacaoCustoDAO();
        $verificarReadequacao = $buscaProjetoProduto->verificarreadequacao($idPronac);

        $totalPlanilhaAprovada   = !empty($verificarReadequacao[0]['totalAprovado']) ? $verificarReadequacao[0]['totalAprovado'] : 0;
        $totalPlanilhaSolicitada = !empty($verificarReadequacao[0]['totalSolicitado']) ? $verificarReadequacao[0]['totalSolicitado'] : 0;
        $totalPlanilhaSolicitada = !empty($totalPlanilhaAprovada) ? number_format(($totalPlanilhaAprovada + $totalPlanilhaSolicitada) - $verificarReadequacao[0]['totalSolicitadoExcluido'], 2, '.', '') : $totalPlanilhaSolicitada;

        if ($totalPlanilhaAprovada > $totalPlanilhaSolicitada) :
            $this->view->tipoReadeq = 'Solicita��o de Redu��o'; elseif ($totalPlanilhaAprovada < $totalPlanilhaSolicitada) :
            $this->view->tipoReadeq = 'Solicita��o de Complementa��o'; else :
            $this->view->tipoReadeq = 'Solicita��o de Remanejamento';
        endif;
        // ========== FIM MENSAGEM DE REDU��O, COMPLEMENTO OU REMANEJAMENTO ==========

        $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO();
        $SolicitarReadequacaoCustoDAO = new SolicitarReadequacaoCustoDAO();
        $resultadoEtapa = $buscaInformacoes->buscarEtapa();
        $this->view->buscaetapa = $resultadoEtapa;

        $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutos($idPronac)->toArray();
        if (empty($resultadoProduto)) {
            $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutosAprovados($idPronac);
        } else {
            $resultadoProduto = $SolicitarReadequacaoCustoDAO->buscarProdutos($idPronac);
        }
        $this->view->buscaproduto = $resultadoProduto;

        foreach ($resultadoProduto as $idProduto) {
            foreach ($resultadoEtapa as $idEtapa) {
                $resultadoProdutosItens = $buscaInformacoes->buscarProdutosItensParecerista($idPronac, $idEtapa->idPlanilhaEtapa, null, "N", $idProduto->idProduto);
                $valorProduto[$idProduto->idProduto][$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItens;
                $resultadoProdutosItensAdm = $buscaInformacoes->buscarProdutosItensSemProduto($idPronac, $idEtapa->idPlanilhaEtapa, null, "N");
                $valorProdutoAdm[$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItensAdm;
            }
        }
        $this->view->buscaprodutositens = $valorProduto;
        $this->view->buscaprodutositensadm = $valorProdutoAdm;

        $verificaStatus = VerificarSolicitacaodeReadequacoesDAO::verificaStatusItemDeCusto($idpedidoalteracao, 10);
        if (count($verificaStatus) <= 0) {
            parent::message("Planilha or&ccedil;ament&atilde;ria n&atilde;o encontrada!", "/verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$idPronac", "ALERT");
        }
        $stAvaliacaoItemPedidoAlteracao = $verificaStatus->stAvaliacaoItemPedidoAlteracao;
        $this->view->status = $stAvaliacaoItemPedidoAlteracao;

        if ($stAvaliacaoItemPedidoAlteracao == "AG") {
            $this->view->statusAnalise = "Aguardando An�lise";
        } elseif ($stAvaliacaoItemPedidoAlteracao == "EA") {
            $this->view->statusAnalise = "Em An�lise";
        } elseif ($stAvaliacaoItemPedidoAlteracao == "AP") {
            $this->view->statusAnalise = "Aprovado";
        } elseif ($stAvaliacaoItemPedidoAlteracao == "IN") {
            $this->view->statusAnalise = "Indeferido";
        }

        $resultadoAvaliacaoAnalise = $buscaInformacoes->verificaAvaliacaoAnalise();
        $this->view->AvaliacaoAnalise = $resultadoAvaliacaoAnalise;
    }

    public function alterarStatusItensAction()
    {
        $idPronac = $_GET['idpronac'];
        $tbPedidoAlteracao = new tbPedidoAlteracaoProjeto();
        $resultado = $tbPedidoAlteracao->buscar(array('IdPRONAC = ?' => $idPronac))->current();

        $tbAvaliacaoItemPedidoAlteracao = new tbAvaliacaoItemPedidoAlteracao();
        $resultado2 = $tbAvaliacaoItemPedidoAlteracao->buscar(array('idPedidoAlteracao = ?' => $resultado->idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 10, 'stAvaliacaoItemPedidoAlteracao = ?' => 'AG'))->current();

        $resultado2['stAvaliacaoItemPedidoAlteracao'] = 'EA';
        $dados = $resultado2->toArray();
        unset($dados['idAvaliacaoItemPedidoAlteracao']);
        $where = array("idAvaliacaoItemPedidoAlteracao = ?" => $resultado2->idAvaliacaoItemPedidoAlteracao);

        $alterarStatus = $tbAvaliacaoItemPedidoAlteracao->alterar($dados, $where);
        parent::message("Situa��o alterada com sucesso!", "verificarsolicitacaodereadequacoes/planilhasolicitada?idPronac=$idPronac", "CONFIRM");
    }

    public function finalizarAvaliacaoItensAction()
    {
        $idPronac = $_POST['idPronacProjeto'];
        $dsObservacao = $_POST['obervacaoDaAvaliacao'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $auth = Zend_Auth::getInstance();

        //retorna o id do agente logado
        $agente = GerenciarPautaReuniaoDAO::consultaIdAgenteUsuario($auth->getIdentity()->usu_codigo);
        $agente = $db->fetchRow($agente);
        $idAgenteRemetente = $agente->idAgente;
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess�o com o grupo ativo
        $idPerfilRemetente = $GrupoAtivo->codGrupo;

        try {
//            $db->beginTransaction();

            $tbPedidoAlteracao = new tbPedidoAlteracaoProjeto();
            $resultado = $tbPedidoAlteracao->buscar(array('IdPRONAC = ?' => $idPronac))->current();

            $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO;
            $dadosTipo = array('stVerificacao' => 2);
            $atualizapedidotipo = $buscaInformacoes->atualizarTipoAlteracao($dadosTipo, array('idPedidoAlteracao = ?' => $resultado->idPedidoAlteracao));

            $tbAvaliacaoItemPedidoAlteracao = new tbAvaliacaoItemPedidoAlteracao();
            $resultado2 = $tbAvaliacaoItemPedidoAlteracao->buscar(array('idPedidoAlteracao = ?' => $resultado->idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 10))->current();
            $idAvaliacaoItemPedidoAlteracao = $resultado2->idAvaliacaoItemPedidoAlteracao;

            $resultado3 = $tbAvaliacaoItemPedidoAlteracao->buscar(array('idPedidoAlteracao = ?' => $resultado->idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 7))->current();
            $idAvaliacaoItemPedidoAlteracaoProduto = $resultado3->idAvaliacaoItemPedidoAlteracao;

            $dadosAvaliacao = array('stAvaliacaoItemPedidoAlteracao' => 'AP', 'dtFimAvaliacao' => new Zend_Db_Expr('GETDATE()'));
            $buscaInformacoes->atualizarAvaliacaopedido($dadosAvaliacao, array('idPedidoAlteracao = ?' => $resultado->idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 10));

            $where = " idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao";
            $dadosAcao = array('stAtivo' => '1', 'dsObservacao' => $dsObservacao);
            $buscaInformacoes->atualizarAvaliacaoAcao($dadosAcao, $where);

            $where2 = " idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracaoProduto";
            $dadosAcao2 = array('stAtivo' => '1');
            $buscaInformacoes->atualizarAvaliacaoAcao($dadosAcao2, $where2);

            $verificaridorgao = $buscaInformacoes->buscarOrgaoSemDB($idAvaliacaoItemPedidoAlteracao);
            $verificaridorgao = $db->fetchRow($verificaridorgao);
            $orgao = $verificaridorgao->idorgao;

            // pega o coordenador de parecer que fez o envio
            $idAgenteAcionado = $buscaInformacoes->buscarUltimoRemetenteCoordPareceristaSemBD($idAvaliacaoItemPedidoAlteracao);
            $dadosAgente = $db->fetchRow($idAgenteAcionado);
            $idAgenteAcionado = $dadosAgente->idAgenteRemetente;

            $dadosinserir = array(
                'idAvaliacaoItemPedidoAlteracao' => $idAvaliacaoItemPedidoAlteracaoProduto,
                'idAgenteAcionado' => $idAgenteAcionado,
                'dsObservacao' => $dsObservacao,
                'idTipoAgente' => 2,
                'idOrgao' => $orgao,
                'stAtivo' => 0,
                'stVerificacao' => 2,
                'dtEncaminhamento' => new Zend_Db_Expr('GETDATE()'),
                'idAgenteRemetente' => $idAgenteRemetente,
                'idPerfilRemetente' => $idPerfilRemetente,
            );
            $tbAcaoAvaliacaoItemPedidoAlteracao = new tbAcaoAvaliacaoItemPedidoAlteracao();
            $inserir = $tbAcaoAvaliacaoItemPedidoAlteracao->inserir($dadosinserir);

//            $where = " and  stAvaliacaoSubItemPedidoAlteracao  = 'AP'";
//            $condicao = VerificarSolicitacaodeReadequacoesDAO::verificaSubItem($idAvaliacaoItemPedidoAlteracao, $where);
//            if (count($condicao) > 0) {
//                $dados = array('stAvaliacaoItemPedidoAlteracao' => 'AP');
//                $alterarStatus = $buscaInformacoes->atualizarStatus($dados, array('idPedidoAlteracao = ?' => $idpedidoalteracao, 'tpAlteracaoProjeto = ?' => 10));
//            } else {
//                $dados = array('stAvaliacaoItemPedidoAlteracao' => 'IN');
//                $alterarStatus = $buscaInformacoes->atualizarStatus($dados, array('idPedidoAlteracao = ?' => $idpedidoalteracao, 'tpAlteracaoProjeto = ?' => 10));
//            }


//            $db->commit();
            parent::message("Solicita&ccedil;&atilde;o enviada com sucesso!", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetoparecerista", "CONFIRM");
        } catch (Zend_Exception $e) {

//            $db->rollBack();
            parent::message("Erro na finaliza&ccedil;&atilde;o da solicita&ccedil;&atilde;o", "verificarreadequacaodeprojeto/verificarreadequacaodeprojetoparecerista", "ERROR");
        }
    }

    public function formularioAction()
    {
        if ($_POST) {
            $idPronac = $_POST['idPronac'];
            $idProduto = $_POST['idProduto'];
            $idPlanilhaAprovacao = $_POST['planilhaAprovacao'];
            $idEtapa = $_POST['idEtapa'];
            $idItem = $_POST['idItem'];
            $tpAcaoVerifica = $_POST['tpAcao'];
            $idPedidoAlteracao = $_POST['idPedidoAlteracao'];
            $idAgente = $_POST['idAgente'];
            $dsJustificativaAvaliador = strip_tags($_POST['dsjustificativaAvaliador']);

            if (empty($_POST['tipoaprovacao'][0]) || empty($_POST['dsjustificativaAvaliador'])) {
                parent::message("Preencha todos os dados!", "/verificarsolicitacaodereadequacoes/formulario?idPronac=$idPronac&idAprovacao=$idPlanilhaAprovacao&idItem=$idItem&tpAcao=$tpAcaoVerifica", "ALERT");
            }

            if (!empty($_POST['tipoaprovacao'])) {
                if ($_POST['tipoaprovacao'][0] == "AP") {
                    $stDeferimento = "D";
                } else {
                    $stDeferimento = "I";
                }
            }

            $resultadoItem = VerificarSolicitacaodeReadequacoesDAO::verificaPlanilhaAprovacao($idPronac);
            foreach ($resultadoItem as $aprovacao) {
                if ($stDeferimento == "D" || $stDeferimento == "I") {
                    $tbAvaliacaoItemPedidoAlteracao = new tbAvaliacaoItemPedidoAlteracao();
                    $resultado = $tbAvaliacaoItemPedidoAlteracao->buscar(array('idPedidoAlteracao = ?' => $idPedidoAlteracao, 'tpAlteracaoProjeto = ?' => 7, 'stAvaliacaoItemPedidoAlteracao = ?' => 'EA'))->current();
                    $idItemAvaliacaoItemPedidoAlteracao = $resultado->idAvaliacaoItemPedidoAlteracao;

                    $inserirAvaliacaoSubItemPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::inserirAvaliacaoSubItemPedidoAlteracao($dsJustificativaAvaliador, $stDeferimento, $idPedidoAlteracao, $idItemAvaliacaoItemPedidoAlteracao);

                    // altera a justificativa da planilha
//                    $tbPlanilhaAprovacao = new PlanilhaAprovacao();
//                    $tbPlanilhaAprovacao->alterar(array('dsJustificativa' => $dsJustificativaAvaliador), array('idPlanilhaAprovacao = ?' => $idPlanilhaAprovacao));

                    $buscaIdAvaliacaoSubItemPedidoAlteracao = VerificarSolicitacaodeReadequacoesDAO::buscaIdAvaliacaoSubItemPedidoAlteracao($idItemAvaliacaoItemPedidoAlteracao);
                    foreach ($buscaIdAvaliacaoSubItemPedidoAlteracao as $itemAvaliacaoSubItemPedido) {
                        $idAvaliacaoSubItemPedidoAlteracao = $itemAvaliacaoSubItemPedido->idAvaliacaoSubItemPedidoAltera;
                    }

                    $inserirAvaliacaoSubItemCusto = VerificarSolicitacaodeReadequacoesDAO::inserirAvaliacaoSubItemCusto($idItemAvaliacaoItemPedidoAlteracao, $idAvaliacaoSubItemPedidoAlteracao, $idPlanilhaAprovacao);
                    $atualizaPlanilhaAprovacao = VerificarSolicitacaodeReadequacoesDAO::atualizaPlanilhaAprovacao($idPlanilhaAprovacao, "N");

                    parent::message("Dados analisados e atualizados com sucesso!", "verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$idPronac", "CONFIRM");
                }
               
                if ($_POST['tpAcao'] == "N" || empty($_POST['tpAcao'])) {
                    parent::message("N&atilde;o h� solicita�&atilde;o de readequa�&atilde;o para este item.", "/verificarreadequacaodeprojeto/readequacaoitensdecustoeditar?id=$idPronac", "ALERT");
                }
                $this->_helper->viewRenderer->setNoRender(true);
            }
        } else {
            $idPronac = $_GET['idPronac'];
            $idPlanilhaAprovacao = $_GET['idAprovacao'];
            $idPlanilhaItem = $_GET['idItem'];

            $auth = Zend_Auth::getInstance();
            //$idSolicitante = $auth->getIdentity()->usu_codigo;
            $buscaprojeto = new ReadequacaoProjetos();
            $resultado = $buscaprojeto->buscarProjetos($idPronac);
            $this->view->buscaprojeto = $resultado;

            $buscaInformacoes = new VerificarSolicitacaodeReadequacoesDAO;
            $resultadoItem = $buscaInformacoes->buscaItem($idPronac, $idPlanilhaAprovacao, $idPlanilhaItem);
            $this->view->buscaitem = $resultadoItem;

            $tbAvaliacaoItemPedidoAlteracao = new tbAvaliacaoItemPedidoAlteracao();
            $dados = $tbAvaliacaoItemPedidoAlteracao->buscar(array('idPedidoAlteracao = ?'=>$resultadoItem[0]->idPedidoAlteracao, 'stAvaliacaoItemPedidoAlteracao = ?'=>'EA', 'tpAlteracaoProjeto = ?'=>7))->current();

            $resultados = $buscaInformacoes->buscaAvaliacoesSubItemPedidoAlteracao($resultadoItem[0]->idPedidoAlteracao, $idPlanilhaAprovacao, $dados->idAvaliacaoItemPedidoAlteracao);
            if ($resultados) {
                $this->view->itemAvaliado = $resultados;
            }
        }
    }
}
