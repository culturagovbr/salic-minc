<?php

/**
 * Description of SolicitarReadequacaoCustoController
 *
 * @author 01373930160
 */

class SolicitarReadequacaoCustoController extends MinC_Controller_Action_Abstract
{

    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::perfil(4);
        //		parent::perfil(3);

        parent::init(); // chama o init() do pai GenericControllerNew
    }

    // fecha m�todo init()

    public function indexAction()
    {
        if (isset($_POST['atualizar'])) {
            try {
                $dados = array('stPedidoAlteracao' => $_POST['acao']);
                $atualizaPedido = SolicitarReadequacaoCustoDAO::atualizaPedidoAlteracao($dados, $_POST['idPedidoAlteracao']);
                $this->_helper->json(array('error' => false));
                $this->_helper->viewRenderer->setNoRender(true);
            } catch (Zend_Exception $e) {
                die('Erro:' . $e->getMessage());
                $this->_helper->json(array('error' => true));
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }
        $idPronac = $_GET['idpronac'];
        $buscaProjetoProduto = new SolicitarReadequacaoCustoDAO();


        // ========== IN�CIO MENSAGEM DE REDU��O, COMPLEMENTO OU REMANEJAMENTO ==========
        $verificarReadequacao = $buscaProjetoProduto->verificarreadequacao($idPronac);

        $totalPlanilhaAprovada = !empty($verificarReadequacao[0]['totalAprovado']) ? $verificarReadequacao[0]['totalAprovado'] : 0;
        $totalPlanilhaSolicitada = !empty($verificarReadequacao[0]['totalSolicitado']) ? $verificarReadequacao[0]['totalSolicitado'] : 0;
        $totalPlanilhaSolicitada = !empty($totalPlanilhaAprovada) ? number_format(($totalPlanilhaAprovada + $totalPlanilhaSolicitada) - $verificarReadequacao[0]['totalSolicitadoExcluido'], 2, '.', '') : $totalPlanilhaSolicitada;

        if (!empty($verificarReadequacao[0]['totalSolicitado']) || !empty($verificarReadequacao[0]['totalSolicitadoExcluido'])) :
            $this->view->existirPlanilhaCusto = 'ok'; else :
            $this->view->existirPlanilhaCusto = '';
        endif;

        if ($totalPlanilhaAprovada > $totalPlanilhaSolicitada) :
            $tipoReadeq = 'redu��o'; elseif ($totalPlanilhaAprovada < $totalPlanilhaSolicitada) :
            $tipoReadeq = 'complementa��o'; else :
            $tipoReadeq = 'remanejamento';
        endif;
        // ========== FIM MENSAGEM DE REDU��O, COMPLEMENTO OU REMANEJAMENTO ==========

        $this->view->verificarReadequacao = $tipoReadeq;

        $verificaPlanilhaCustoVerifica = $buscaProjetoProduto->buscarProdutoAprovacao($idPronac)->toArray();

        if (empty($verificaPlanilhaCustoVerifica)) {
            $verificaPlanilhaCusto = $buscaProjetoProduto->buscarProdutoAprovacaoSemProposta($idPronac);
            $this->view->buscaPlanilhaCusto = $verificaPlanilhaCusto;
        } else {
            $verificaPlanilhaCusto = $buscaProjetoProduto->buscarProdutoAprovacao($idPronac);
            $this->view->buscaPlanilhaCusto = $verificaPlanilhaCusto;
        }

        

        $resultadoAcao = $buscaProjetoProduto->verificaTipoAcao($idPronac);
        $this->view->buscaacao = $resultadoAcao;

        $resultado = $buscaProjetoProduto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado['0'];

        $resultadoStatus = $buscaProjetoProduto->verificaPedidoAlteracao($idPronac);
        $this->view->buscastatus = $resultadoStatus;

        $resultadoproduto = $buscaProjetoProduto->buscarProdutos($idPronac);

        $resultadoItensCadastrados = $buscaProjetoProduto->buscarItensCadastrados($idPronac);

        $this->view->existirPlanilhaProduto = 'ok';
        $_idPedidoAlteracao = isset($resultadoStatus['idPedidoAlteracao']) && !empty($resultadoStatus['idPedidoAlteracao']) ? $resultadoStatus['idPedidoAlteracao'] : 0;
        $_idPronac = isset($idPronac) && !empty($idPronac) ? $idPronac : 0;

        if (!$this->existirPlanilhaProduto($_idPronac, $_idPedidoAlteracao)) {
            $this->view->existirPlanilhaProduto = 'n';
        }

        $i = 0;
        if (empty($resultadoproduto[0]->produto)) {
            $verificaPlanilhaCustoVerificacao = $buscaProjetoProduto->buscarProdutoAprovacao($idPronac);
            foreach ($verificaPlanilhaCustoVerificacao as $produto) {
                $produtosxitens[$produto->idProduto]["produto"] = $produto;
                foreach ($resultadoItensCadastrados as $item) {
                    if ($item->idProduto == $produto->idProduto) {
                        $produtosxitens[$produto->idProduto]["itens"][] = $item;
                    }
                }
            }
            $this->view->produtosxitens = @$produtosxitens;
            $this->view->buscaproduto = $verificaPlanilhaCusto;
        } else {
            $resultadoprodutoVerificacao = $buscaProjetoProduto->buscarProdutos($idPronac);
            foreach ($resultadoprodutoVerificacao as $produto) {
                $produtosxitens[$produto->idProduto]["produto"] = $produto;
                foreach ($resultadoItensCadastrados as $item) {
                    if ($item->idProduto == $produto->idProduto) {
                        $produtosxitens[$produto->idProduto]["itens"][] = $item;
                    }
                }
            }
            $this->view->produtosxitens = $produtosxitens;
            $this->view->buscaproduto = $resultadoproduto;
        }
    }

    public function incluirprodutoAction()
    {
        $buscaPedido = new SolicitarReadequacaoCustoDAO;

        if (isset($_POST['verifica']) && $_POST['verifica']) {
            if ($_POST['verifica'] == "SI") {
                $status = $_POST['status'];
                $idPronac = $_POST['idpronac'];
                $atualizaPedido = SolicitarReadequacaoCustoDAO::controlaStatus($status, $idPronac);
                $this->_helper->viewRenderer->setNoRender(true);
            } else {
                $status = $_POST['status'];
                $idPronac = $_POST['idpronac'];
                $atualizaPedido = SolicitarReadequacaoCustoDAO::controlaStatus($status, $idPronac);
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }

        if (isset($_GET['idpronac'])) {
            $idPronac = $_GET['idpronac'];

            $prods = $buscaPedido->buscarProdutos($idPronac);
            $this->view->prods = $prods;
        }

        if (isset($_POST['ufAjax'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $iduf = $_POST['ufAjax'];
            if ($iduf != 0) {
                $resultadoMunicipio = SolicitarReadequacaoCustoDAO::buscarMunicipio($iduf);
                $i = 0;
                $municipio['error'] = false;
                foreach ($resultadoMunicipio as $valor) {
                    $municipio[$i]['idMunicipioIBGE'] = $valor->idMunicipioIBGE;
                    $municipio[$i]['nomeMunicipio'] = utf8_encode($valor->Descricao);
                    $i++;
                }
            } else {
                $municipio['error'] = true;
            }
            $this->_helper->json($municipio);
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['idEtapa'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            if (isset($_POST['idEtapa'])) {
                $resultadoItem = SolicitarReadequacaoCustoDAO::buscarItens($_POST['idEtapa']);
                $itemEtapa['error'] = false;
                $i = 0;
                foreach ($resultadoItem as $result) {
                    $itemEtapa[$i]['IdItem'] = $result->idPlanilhaItens;
                    $itemEtapa[$i]['NomeItem'] = utf8_encode($result->Descricao);
                    $i++;
                }
            } else {
                $itemEtapa['error'] = true;
            }

            $this->_helper->json($itemEtapa);
            $this->_helper->viewRenderer->setNoRender(true);
        }
        //se o produto estiver setado
        if (isset($_POST['acao'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $verificapedido = $buscaPedido->verificaPedidoAlteracao($_POST['idpronac']);

            if ($verificapedido == null) {
                $dados = array(
                    'IdPRONAC' => $_POST['idpronac'],
                    'idSolicitante' => $_POST['idAgente'],
                    'dtSolicitacao' => date('Y-m-d H:i:s'),
                    'stPedidoAlteracao' => 'T',
                    'siVerificacao' => 0
                );
                $idPedidoAlteracao = $buscaPedido->inserirPedido($dados);
            } else {
                $resultadoStatus = $buscaPedido->verificaPedidoAlteracao($_POST['idpronac']);
                $idPedidoAlteracao = $resultadoStatus['idPedidoAlteracao'];
            }

            $justificativa = Seguranca::tratarVarAjaxUFT8($_POST['justificativa']);
            $dados = array(
                'idPedidoAlteracao' => $idPedidoAlteracao,
                'tpAlteracaoProjeto' => 10,
                'stVerificacao' => 0,
                'dsJustificativa' => $justificativa
            );
            $insereTipo = SolicitarReadequacaoCustoDAO::inserirPedidoTipo($dados);

            try {
                //$verificarplanilha = $buscaPedido->verificarPlanilhaCriada($_POST['idpronac'], 'SR', $_POST['idProduto'], $_POST['etapa'], $_POST['item'], $_POST['uf'], $_POST['municipio']);
                $verificarplanilha = $buscaPedido->verificarPlanilhaCriada($_POST['idpronac'], 'SR');
                if (count($verificarplanilha) == 0) {
                    $insereCopiaPlanilha = SolicitarReadequacaoCustoDAO::inserirCopiaPlanilha($_POST['idpronac'], $idPedidoAlteracao);
                }
//                $valorFinal =  explode(".", $_POST['vlUnitario']);
//                $valorFinal = $valorFinal[0] . $valorFinal[1] . "." . $valorFinal[2];
                // diz se a solicita��o de readequa��o ser� inclu�da ou alterada
                $PA = new PlanilhaAprovacao();
                $buscarPlanilhaAprovacaoPai = $PA->buscar(array('idPlanilhaAprovacaoPai = ?' => $_POST['idPlanilhaAP'], 'tpPlanilha = ?' => 'SR'));
                if (count($buscarPlanilhaAprovacaoPai) > 0) :
                    $acaoSR = 'A';
                $colunaSR = 'idPlanilhaAprovacaoPai';
                $idPlanilhaAprovacaoPai = !empty($_POST['idPlanilhaAP']) ? $_POST['idPlanilhaAP'] : null; else :
                    $buscarPlanilhaAprovacaoSR = $PA->buscar(array('idPlanilhaAprovacao = ?' => $_POST['idPlanilhaAP'], 'tpPlanilha = ?' => 'SR'));
                if (count($buscarPlanilhaAprovacaoSR) > 0) :
                        $acaoSR = 'A';
                $colunaSR = 'idPlanilhaAprovacao';
                $idPlanilhaAprovacaoPai = $buscarPlanilhaAprovacaoSR[0]->idPlanilhaAprovacaoPai; else :
                        $acaoSR = 'I';
                $colunaSR = 'idPlanilhaAprovacaoPai';
                $idPlanilhaAprovacaoPai = !empty($_POST['idPlanilhaAP']) ? $_POST['idPlanilhaAP'] : null;
                endif;
                endif;

                $dados = array(
                    'idPlanilhaAprovacaoPai' => $idPlanilhaAprovacaoPai,
                    'dtPlanilha' => date('Y-m-d H:i:s'),
                    'IdPRONAC' => $_POST['idpronac'],
                    'idProduto' => $_POST['idProduto'],
                    'idEtapa' => $_POST['etapa'],
                    'idPlanilhaItem' => $_POST['item'],
                    'dsItem' => 'Produto',
                    'idUnidade' => $_POST['unidade'],
                    'qtItem' => $_POST['qtd'],
                    'nrOcorrencia' => $_POST['ocorrencia'],
                    'vlUnitario' => $_POST['vlUnitario'],
                    'qtDias' => $_POST['dias'],
                    'tpDespesa' => 0,
                    'tpPessoa' => $_POST['idTipoPessoa'],
                    'nrContraPartida' => 0,
                    'nrFonteRecurso' => $_POST['fonte'],
                    'idUFDespesa' => $_POST['uf'],
                    'idMunicipioDespesa' => $_POST['municipio'],
                    'dsJustificativa' => Seguranca::tratarVarAjaxUFT8($_POST['justificativa']),
                    'idAgente' => $_POST['idAgente'],
                    'idPedidoAlteracao' => $idPedidoAlteracao,
                    'tpAcao' => $_POST['acao'],
                    'tpPlanilha' => 'SR',
                    'stAtivo' => 'N'
                );

                if ($_POST['acao'] == 'E' or $_POST['acao'] == 'A') {

                    // inclus�o da solicita��o de readequa��o
                    if ($acaoSR == 'I') {
                        $insertItem = SolicitarReadequacaoCustoDAO::inserirNovoProduto($dados);
                    }
                    // altera��o da solicita��o de readequa��o
                    else {
                        $where = "tpPlanilha = 'SR' AND stAtivo = 'N' AND $colunaSR = " . $_POST['idPlanilhaAP'];
                        $updateItem = SolicitarReadequacaoCustoDAO::atualizaItem($dados, $where);
                    }


                    /* $where = " (idEtapa = " . $_POST['etapa'] . ") AND (idProduto = " . $_POST['idProduto'] . ") AND (idPlanilhaItem = " . $_POST['item'] . ") AND tpPlanilha = 'SR' AND stAtivo = 'N' AND idUFDespesa = ".$_POST['uf']." AND idMunicipioDespesa = " . $_POST['municipio'];
                      $resultPlanilhaAprovada = SolicitarReadequacaoCustoDAO::buscaUltimaPlanilhaAprovada($where);

                      $idPlanilhaAprovacao = $resultPlanilhaAprovada[0]->idPlanilhaAprovacao;
                      $wherePlanilhaAprovada = " (idPlanilhaAprovacao = " . $idPlanilhaAprovacao . " AND idEtapa =  " . $_POST['etapa'] . ") AND (idProduto = " . $_POST['idProduto'] . ") AND (idPlanilhaItem = " . $_POST['item'] . ") AND tpPlanilha = 'SR' AND stAtivo = 'N'";
                      $atulizaItem = SolicitarReadequacaoCustoDAO::atualizaItem($dados, $wherePlanilhaAprovada);

                      $dados2 = array(
                      'tpAcao' => $_POST['acao']
                      );
                      $where2 = " ( idPlanilhaAprovacao = ". $_POST['idaprovacaoA'] ." ) ";
                      $alterarItem = SolicitarReadequacaoCustoDAO::alterarItem($dados2, $where2); */
                } else {
                    $insertItem = SolicitarReadequacaoCustoDAO::inserirNovoProduto($dados);
                }
                $this->_helper->json(array('error' => false));
                $this->_helper->viewRenderer->setNoRender(true);
            } catch (Zend_Exception $e) {
                $this->_helper->json(array('error' => true, 'descricao:' => $e->getMessage()));
                $this->_helper->viewRenderer->setNoRender(true);
            }
        }

        if (isset($_POST['verifica']) and $_POST['verifica'] == "N") {
            $dados = array(
                'stPedidoAlteracao' => 'A',
                'dtSolicitacao' => date('Y-m-d H:i:s'),
                'siVerificacao' => 0
            );

            $alterarpedido = SolicitarReadequacaoCustoDAO::alterarPedidoAlterado($dados);
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_POST['verificaPlanilha']) and $_POST['verificaPlanilha'] == "S") {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $idProduto = $_POST['idProduto'];

            if ($idProduto == 0) {
                $resultadoProdutosItens = $buscaPedido->buscarProdutosItens(false, false, false, $_POST['idPlanilhaAprovacao']);
            } else {
                $resultadoProdutosItens = $buscaPedido->buscarProdutosItens(false, false, $idProduto, $_POST['idPlanilhaAprovacao']);
            }

            foreach ($resultadoProdutosItens as $resultadoarray) {
                $valorjson['idPlanilhaAprovacao'] = $resultadoarray->idPlanilhaAprovacao;
                $valorjson['idProduto'] = $resultadoarray->idProduto;
                $valorjson['qtItem'] = $resultadoarray->qtItem;
                $valorjson['nrOcorrencia'] = $resultadoarray->nrOcorrencia;
                $valorjson['vlUnitario'] = $resultadoarray->vlUnitario;
                $valorjson['qtDias'] = $resultadoarray->qtDias;
                $valorjson['Total'] = $resultadoarray->Total;
                $valorjson['nrFonteRecurso'] = $resultadoarray->nrFonteRecurso;
                $valorjson['idPlanilhaItem'] = $resultadoarray->idPlanilhaItem;
                $valorjson['idUnidade'] = $resultadoarray->idUnidade;
                $valorjson['Etapa'] = $resultadoarray->Etapa;
                $valorjson['iduf'] = $resultadoarray->iduf;
                $valorjson['idmun'] = $resultadoarray->idmun;
                $valorjson['idPlanilhaEtapa'] = $resultadoarray->idEtapa;
                $valorjson['dsjustificativa'] = !is_null(utf8_encode($resultadoarray->dsJustificativa)) ? utf8_encode($resultadoarray->dsJustificativa) : '';
                $valorjson['acao'] = "A";
            }
            $json = json_encode($valorjson);
            echo $json;
            $this->_helper->viewRenderer->setNoRender(true);
        }

        if (isset($_GET['idpronac'])) {
            $idPronac = $_GET['idpronac'];
            $idProduto = isset($_GET['idProduto']) ? $_GET['idProduto'] : 0;
            $tipoproduto = isset($_GET['idProduto']) ? 'P' : 'A';

            $buscaInformacoes = new SolicitarReadequacaoCustoDAO;

            $verificaPlanilhaCustoVerifica = $buscaInformacoes->buscarProdutoAprovacao($idPronac)->toArray();


            if (empty($verificaPlanilhaCustoVerifica)) {
                $verificaPlanilhaCusto = $buscaInformacoes->buscarProdutoAprovacaoSemProposta($idPronac);
                $this->view->buscaPlanilhaCusto = $verificaPlanilhaCusto;
            } else {
                $verificaPlanilhaCusto = $buscaInformacoes->buscarProdutoAprovacao($idPronac);
                $this->view->buscaPlanilhaCusto = $verificaPlanilhaCusto;
            }

            $resultado = $buscaInformacoes->buscarProjetos($idPronac);
            $this->view->buscaprojeto = $resultado['0'];

            $resultadoProduto = $buscaInformacoes->buscarProdutos($idPronac);

//            Zend_Debug::dump($resultadoProduto); $this->_helper->viewRenderer->setNoRender(TRUE);
//            $this->view->buscaproduto = $resultadoProduto;
            $resultadoItensCadastrados = $buscaInformacoes->buscarItensCadastrados($idPronac);

            $i = 0;

            if (empty($resultadoProduto[0]->produto)) {
                $verificaPlanilhaCustoVerificacao = $buscaInformacoes->buscarProdutoAprovacao($idPronac);
                foreach ($verificaPlanilhaCustoVerificacao as $produto) {
                    $produtosxitens[$produto->idProduto]["produto"] = $produto;

                    $buscaInformacoes = new SolicitarReadequacaoCustoDAO;
                    $buscaInformacoesEtapa = $buscaInformacoes->buscarProdutosItensInseridos($idPronac, null, $produto->idProduto);
                    //if (count($buscaInformacoesEtapa) > 0) :
                    foreach ($resultadoItensCadastrados as $item) {
                        if ($item->idProduto == $produto->idProduto) {
                            $produtosxitens[$produto->idProduto]["itens"][] = $item;
                        }
                    }
                    //endif;
                }
                $this->view->produtosxitens = @$produtosxitens;
                $this->view->buscaproduto = $verificaPlanilhaCusto;
            } else {
                $resultadoprodutoVerificacao = $buscaInformacoes->buscarProdutos($idPronac);
                foreach ($resultadoprodutoVerificacao as $produto) {
                    $produtosxitens[$produto->idProduto]["produto"] = $produto;

                    $buscaInformacoes = new SolicitarReadequacaoCustoDAO;
                    $buscaInformacoesEtapa = $buscaInformacoes->buscarProdutosItensInseridos($idPronac, null, $produto->idProduto);
                    //if (count($buscaInformacoesEtapa) > 0) :
                    foreach ($resultadoItensCadastrados as $item) {
                        if ($item->idProduto == $produto->idProduto) {
                            $produtosxitens[$produto->idProduto]["itens"][] = $item;
                        }
                    }
                    //endif;
                }
                $this->view->produtosxitens = $produtosxitens;
                $this->view->buscaproduto = $resultadoProduto;
            }

            $resultadoEtapa = $buscaInformacoes->buscarEtapa($tipoproduto);
//            Zend_Debug::dump($resultadoEtapa);$this->_helper->viewRenderer->setNoRender(TRUE);
            $this->view->buscaetapa = $resultadoEtapa;

            $resultadoFonteRecurso = $buscaInformacoes->buscarFonteRecurso();
            $this->view->buscafonterecurso = $resultadoFonteRecurso;

            $resultadoUnidade = $buscaInformacoes->buscarUnidade();
            $this->view->buscaunidade = $resultadoUnidade;

            $resultadoUF = $buscaInformacoes->buscarUF();
            $this->view->buscauf = $resultadoUF;

            $resultadoAcao = $buscaInformacoes->verificaTipoAcao($idPronac);
            $this->view->buscaacao = $resultadoAcao;

            $resultadoStatus = $buscaInformacoes->verificaPedidoAlteracao($idPronac);
            $this->view->buscastatus = $resultadoStatus;
            foreach ($resultadoEtapa as $idEtapa) {
                $resultadoProdutosItens = $buscaInformacoes->buscarProdutosItens($idPronac, $idEtapa->idPlanilhaEtapa, $idProduto);
                $valorProduto[$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItens;
            }
            $this->view->buscaprodutositens = $valorProduto;

            $valorProduto = '';
            foreach ($resultadoEtapa as $idEtapa) {
                $resultadoProdutosItens = $buscaInformacoes->buscarProdutosItensInseridos($idPronac, $idEtapa->idPlanilhaEtapa, $idProduto);
                if ($resultadoProdutosItens->count() > 0) {
                    $valorProduto[$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItens;
                }
            }
            $this->view->buscaprodutositensinseridos = $valorProduto;
        }

        $this->view->existirPlanilhaProduto = 'ok';
        $_idPedidoAlteracao = isset($resultadoStatus['idPedidoAlteracao']) && !empty($resultadoStatus['idPedidoAlteracao']) ? $resultadoStatus['idPedidoAlteracao'] : 0;
        $_idPronac = isset($idPronac) && !empty($idPronac) ? $idPronac : 0;

        if (!$this->existirPlanilhaProduto($_idPronac, $_idPedidoAlteracao)) {
            $this->view->existirPlanilhaProduto = 'n';
        }
    }

    public function planilhareadequadaAction()
    {
        $buscaPedido = new SolicitarReadequacaoCustoDAO;

        /* if (isset($_POST['idEtapa'])) {
          $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
          if (isset($_POST['idEtapa'])) {
          $resultadoItem = SolicitarReadequacaoCustoDAO::buscarItens($_POST['idEtapa']);
          $itemEtapa['error'] = false;
          $i = 0;
          foreach ($resultadoItem as $result) {
          $itemEtapa[$i]['IdItem'] = $result->idPlanilhaItens;
          $itemEtapa[$i]['NomeItem'] = utf8_encode($result->Descricao);
          $i++;
          }
          } else {
          $itemEtapa['error'] = true;
          }

          $this->_helper->json($itemEtapa);
          $this->_helper->viewRenderer->setNoRender(TRUE);
          } */

        if (isset($_POST['verificaPlanilha']) and $_POST['verificaPlanilha'] == "S") {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $idProduto = $_POST['idProduto'];
            $idPronac = $_POST['idPronac'];
            $idEtapa = $_POST['idEtapa'];
            $idPlanilhaAprovacao = isset($_POST['idPlanilhaAprovacao']) && !empty($_POST['idPlanilhaAprovacao']) ? $_POST['idPlanilhaAprovacao'] : 0;
            $buscaInformacoes = new SolicitarReadequacaoCustoDAO;
            $resultadoProdutosItens = $buscaInformacoes->buscarProdutosItensInseridos($idPronac, $idEtapa, $idProduto, $idPlanilhaAprovacao);

            foreach ($resultadoProdutosItens as $resultadoarray) {
                $valorjson['idPlanilhaAprovacao'] = $resultadoarray->idPlanilhaAprovacao;
                $valorjson['idProduto'] = $resultadoarray->idProduto;
                $valorjson['qtItem'] = $resultadoarray->qtItem;
                $valorjson['nrOcorrencia'] = $resultadoarray->nrOcorrencia;
                $valorjson['vlUnitario'] = $resultadoarray->vlUnitario;
                $valorjson['qtDias'] = $resultadoarray->qtDias;
                $valorjson['Total'] = $resultadoarray->Total;
                $valorjson['nrFonteRecurso'] = $resultadoarray->nrFonteRecurso;
                $valorjson['idPlanilhaItem'] = $resultadoarray->idPlanilhaItem;
                $valorjson['idUnidade'] = $resultadoarray->idUnidade;
                $valorjson['Etapa'] = $resultadoarray->Etapa;
                $valorjson['iduf'] = $resultadoarray->iduf;
                $valorjson['idmun'] = $resultadoarray->idmun;
                $valorjson['idPlanilhaEtapa'] = $resultadoarray->idEtapa;
                $valorjson['dsjustificativa'] = !is_null(utf8_encode($resultadoarray->dsJustificativa)) ? utf8_encode($resultadoarray->dsJustificativa) : '';
                $valorjson['acao'] = "A";
            }
            $json = json_encode($valorjson);
            echo $json;
            $this->_helper->viewRenderer->setNoRender(true);
        }
    }

    public function existirPlanilhaProduto($idPronac = 0, $idPedidoAlteracao = 0)
    {
        $buscaProjetoProduto = new SolicitarReadequacaoCustoDAO();
        $buscaReadequacaoProduto = new ReadequacaoProjetos();
        $resultadoItensCadastrados = $buscaProjetoProduto->buscarItensCadastrados($idPronac);
        $verificaPlanilhaCustoVerificacao = $buscaReadequacaoProduto->buscarprodutoSolicitado($idPedidoAlteracao);

        $resultadoEtapa = $buscaProjetoProduto->buscarEtapa('P');
        foreach ($resultadoEtapa as $idEtapa) {
            $resultadoProdutosItens = $buscaProjetoProduto->buscarProdutosItens($idPronac, $idEtapa->idPlanilhaEtapa);
            $valorProduto[$idEtapa->idPlanilhaEtapa] = $resultadoProdutosItens;
        }
        $qtdPlanilhaAprovada = count($valorProduto);

        $verificaPlanilhaCustoVerifica = $buscaProjetoProduto->buscarProdutoAprovacao($idPronac);
        $qtProdutos = 0;
        $qtItens = 0;
        $p1 = array();
        $p2 = array();
        if (empty($verificaPlanilhaCustoVerifica)) {
            $verificaPlanilhaCusto = $buscaProjetoProduto->buscarProdutoAprovacaoSemProposta($idPronac);
        } else {
            $verificaPlanilhaCusto = $buscaProjetoProduto->buscarProdutoAprovacao($idPronac);
        }

        foreach ($verificaPlanilhaCustoVerifica as $v1) {
            if (!in_array($v1->idProduto, $p1)) {
                foreach ($verificaPlanilhaCusto as $v2) {
                    if ($v1->idProduto == $v2->idProduto && !in_array($v2->idProduto, $p2)) {
                        $b = $buscaProjetoProduto->buscarProdutosItensInseridos($idPronac, null, $v2->idProduto)->current();
                        if (count($b) > 0) {
                            if ($b->idProduto == $v2->idProduto) { //  && $b->idEtapa == $v2->idEtapa
                                $qtItens += 1;
                                $p2[] = $v2->idProduto;
                            }
                        }
                    }
                }
                $qtProdutos += 1;
                $p1[] = $v1->idProduto;
            }
        }

        $produtosxitens = array();
        $itensxprodutos = array();
        foreach ($verificaPlanilhaCustoVerificacao as $produto) {
            if (!in_array($produto->idProduto, $itensxprodutos)) {
                $itensxprodutos[] = $produto->idProduto;
            }
            foreach ($resultadoItensCadastrados as $item) {
                if ($item->idProduto == $produto->idProduto && !in_array($item->idProduto, $produtosxitens)) {
                    $produtosxitens[] = $produto->idProduto;
                }
            }
        }
        $this->view->Xitens = $p2;
        //x($qtdPlanilhaAprovada);
        //x(count($produtosxitens) .'-'. count($itensxprodutos));
        //x($qtProdutos .'-'. $qtItens);
        if (count($produtosxitens) < count($itensxprodutos) || $qtdPlanilhaAprovada <= 0) {
            return false;
        } elseif (($qtProdutos > $qtItens && count($itensxprodutos) <= 0) && $qtdPlanilhaAprovada <= 0) {
            return false;
        } elseif ($qtProdutos > $qtItens && count($itensxprodutos) <= 0 && count($produtosxitens) <= 0) {
            return false;
        } else {
            return true;
        }
    }

    // fecha m�todo existirPlanilhaProduto()

    public function validarPercentualAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout
        $this->_helper->viewRenderer->setNoRender(true);
        $buscaPedido = new SolicitarReadequacaoCustoDAO;
        $verificapedido = $buscaPedido->verificaPedidoAlteracao($_POST['idpronac']);

        //REGRA DOS 15% ***********************************************
        //soma valor total do projeto
        $planilhaAprovacao = new PlanilhaAprovacao();
        $ProjetoAprovado = new Projetos();
        $AprovadoReal = $ProjetoAprovado->buscarProjetosAprovados(array('pr.IdPRONAC = ?' => $_POST['idpronac'], 'ap.TipoAprovacao = ?' => 1))->current();
        $AprovadoReal = $AprovadoReal['AprovadoReal'];

        //somar valor dos custos administrativo
        $arrWhereCustoAdm = array();
        $arrWhereCustoAdm['idPronac = ?'] = $_POST['idpronac'];
        $arrWhereCustoAdm['idProduto = ?'] = 0; //custos administrativos
        $arrWhereCustoAdm['tpPlanilha = ? '] = 'SR'; //
        $valoracustosadministrativos = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereCustoAdm);

        $valoracustosadministrativos = $valoracustosadministrativos['soma'];
        //$valoracustosadministrativos += (float) $_POST['qtd'] * $_POST['ocorrencia'] * $_POST['vlUnitario']; //
        $valorquinzeporceto = ($AprovadoReal * 0.15); //pegando o valor de 15% do projeto para incluir na msg abaixo
        
        //***REGRA 20% DIVULGA��O/COMERCIALIZA��O  *****************************************************************************/
        //soma valor dos custos DIVULGA��O / COMERCIALIZA��O
        $arrWhereCustoDC = array();
        $arrWhereCustoDC['idPronac = ?'] = $_POST['idpronac'];
        $arrWhereCustoDC['idEtapa = ?'] = 3; //custos DIVULGA��O E COMERCIALIZA��O
        $arrWhereCustoDC['tpPlanilha = ? '] = 'SR';
        $valoracustosdivulgacaocomercializacao = $planilhaAprovacao->somarItensPlanilhaAprovacao($arrWhereCustoDC);
        $valoracustosdivulgacaocomercializacao = (!empty($valoracustosdivulgacaocomercializacao['soma'])) ? $valoracustosdivulgacaocomercializacao['soma'] : 0;
//        $valoracustosdivulgacaocomercializacao += (float) $_POST['qtd'] * $_POST['ocorrencia'] * $_POST['vlUnitario'];
        $valorvinteporcento = ($AprovadoReal * 0.20);
        
        
        $novos_valores = array();
        $dados = array('stPedidoAlteracao' => $_POST['acao']);
        
        if ($valoracustosdivulgacaocomercializacao > $valorvinteporcento) {
            $atualizaPedido = SolicitarReadequacaoCustoDAO::atualizaPedidoAlteracao($dados, $_POST['idPedidoAlteracao']);
            $msg = 'Favor ajustar os custos de Divulga��o / Comercializa��o que excedem <b>'. number_format($valorvinteporcento, '2', ',', '.') .'</b>, valor para que possa enviar sua solicita��o de readequa��o.';
//            $msg = 'Na readequa&ccedil;�o de planilha or&ccedil;ament�ria, o sistema deve bloquear envio planilha com custos administrativos superior a 15% do valor total do projeto.';
            $novos_valores['error'] = true;
            $novos_valores['descricao'] = utf8_encode($msg);
            $this->_helper->json($novos_valores);
            $this->_helper->viewRenderer->setNoRender(true);
        } elseif ($valoracustosadministrativos > $valorquinzeporceto) {
            $atualizaPedido = SolicitarReadequacaoCustoDAO::atualizaPedidoAlteracao($dados, $_POST['idPedidoAlteracao']);

            $msg = 'Favor ajustar os Custos Administrativos que excedem <b>'. number_format($valorquinzeporceto, '2', ',', '.') .'</b>, valor para que possa enviar sua solicita��o de readequa��o.';
            $novos_valores['error'] = true;
            $novos_valores['descricao'] = utf8_encode($msg);
            $this->_helper->json($novos_valores);
            $this->_helper->viewRenderer->setNoRender(true);
        } else {
            $novos_valores['error'] = false;
            $this->_helper->json($novos_valores);
            $this->_helper->viewRenderer->setNoRender(true);
        }
        //***FINAL REGRA 15% CUSTOS ADMINISRATIVOS *****************************************************************************/
        //***FINAL REGRA 20% DIVULGA��O/COMERCIALIZA��O*****************************************************************************/
    }
}
