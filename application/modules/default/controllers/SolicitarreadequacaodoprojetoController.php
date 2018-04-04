<?php //MinC
/**
 * SolicitarReadequacaoDoProjetoController
 */

class SolicitarReadequacaoDoProjetoController extends MinC_Controller_Action_Abstract
{
    /**
     * Reescreve o m�todo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {

        // define perfil para o scriptcase
        parent::perfil(2);

        parent::init(); // chama o init() do pai GenericControllerNew
    } // fecha m�todo init()



    /**
     * M�todo index
     */
    public function indexAction()
    {
        $mapperArea = new Agente_Model_AreaMapper();
        
        // combo com as �reas culturais
        $this->view->comboareasculturais = $mapperArea->fetchPairs('codigo', 'descricao');

        // busca os pa�ses
        $pais = new PaisDao();
        $r_pais = $pais->buscarPais();
        $this->view->buscapais = $r_pais;

        // busca os estados
        $estado = new EstadoDAO();
        $r_estado = $estado->buscar();
        $this->view->buscaestado = $r_estado;

        // cria o objeto de readequa��o de projetos
        $buscaprojeto = new ReadequacaoProjetos();

        // recebe o id do pronac via get
        $idPronac = $_GET["idpronac"];

        // pega o id do usu�rio logado
        $auth = Zend_Auth::getInstance();
        $idSolicitante = $auth->getIdentity()->IdUsuario;

        $resultado = $buscaprojeto->buscarProjetos($idPronac);
        $this->view->buscaprojeto = $resultado;
        $resultadoid = $buscaprojeto->buscarID($idPronac);
        $idProjeto = $resultadoid[0]->idProjeto;

        $buscaProjetoProduto = new SolicitarReadequacaoCustoDAO();
        $verificaPlanilhaCusto = $buscaProjetoProduto->buscarProdutoAprovacao($idPronac);
        $this->view->buscaPlanilhaCusto = $verificaPlanilhaCusto;

        $resultadodescricao = $buscaprojeto->buscarDescricao();
        $this->view->buscadescricao = $resultadodescricao;

        $resultadoposicao = $buscaprojeto->buscarPosicao();
        $this->view->buscaposicao = $resultadoposicao;
        $valores = $buscaprojeto->buscarSolicitacao($idPronac);
        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
        $Projetos = new SolicitarAlteracaoDAO();


        $prazoProjetos = $buscaprojeto->BuscarPrazoProjetos($idPronac);
        $data1 = $prazoProjetos[0]->DtInicioExecucao;
        $data2 =  $prazoProjetos[0]->DtFimExecucao;
        $this->view->data1 = $data1;
        $this->view->data2 = $data2;

        $prazoCaptacao = $buscaprojeto->BuscarPrazoProjetosCaptacao($idPronac);
        $data3 = isset($prazoCaptacao[0]->DtInicioCaptacao) ? $prazoCaptacao[0]->DtInicioCaptacao : '00/00/0000';
        $data4 = isset($prazoCaptacao[0]->DtFimCaptacao)    ? $prazoCaptacao[0]->DtFimCaptacao    : '00/00/0000';
        $this->view->data3 = $data3;
        $this->view->data4 = $data4;

        // se houver pedido de altera��o
        if (!empty($idPedidoAlteracao)) {
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $enviar = $buscaprojeto->verificarBotao($idPedidoAlteracao);
            $dados = $buscaprojeto->buscarprodutoSolicitado($idPedidoAlteracao);
            $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, 5);
            $resultadoPedidoAlteracao2 = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, 3);
            $resultadoPedidoAlteracao3 = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, 9);
            $resultadoPedidoAlteracao4 = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, 8);
            $resultadoPedidoAlteracao5 = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, 4);
            $resultadoPedidoAlteracao7 = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, 6);
            $resultadoPedidoAlteracao8 = $Projetos->buscarProposta2($idPedidoAlteracao);
            $buscatbProposta = $Projetos->buscarNomeProposta($idPedidoAlteracao);
            $buscatbProposta2 = $Projetos->buscatbProposta($idPedidoAlteracao);
            $prazo = $buscaprojeto->BuscarPrazo($idPedidoAlteracao, 'E');

            $prazoProjetos = $buscaprojeto->BuscarPrazoProjetos($idPronac);

            $prazo2 = $buscaprojeto->BuscarPrazo($idPedidoAlteracao, 'C');

            // busca os locais de acordo com o pedido de altera��o
            $locais = $buscaprojeto->buscarLocais2($idPedidoAlteracao);
            $this->view->locais = $locais;

            if (!empty($resultadoPedidoAlteracao8)) {
                $justicativa8 = $resultadoPedidoAlteracao8[0]->dsEspecificacaoTecnica;
                $this->view->justificativa8 = $justicativa8;
            }
            if (!empty($resultadoPedidoAlteracao5)) {
                $justicativa5 = $resultadoPedidoAlteracao5[0]->dsJustificativa;
                $this->view->justificativa5 = $justicativa5;
            }
            if (!empty($resultadoPedidoAlteracao7)) {
                $justicativa7 = $resultadoPedidoAlteracao7[0]->dsJustificativa;
                $this->view->justificativa7 = $justicativa7;
            }

            // caso n�o tenha locais por pedido de altera��o, busca por projeto
            if (empty($locais)) {
                $locais = $buscaprojeto->buscarLocais($idProjeto);
                $this->view->somenteabrangencia = "ok";
                $this->view->locais = $locais;
            }
            /*else
            {
                $locaisexterior = $buscaprojeto->buscarLocaisExterior($idPedidoAlteracao);
                $this->view->locaisexterior = $locaisexterior;
                $this->view->locais = $locais;
            }*/

            if (!empty($resultadoPedidoAlteracao4)) {
                $justicativa4 = $resultadoPedidoAlteracao4[0]->dsJustificativa;
                $this->view->justificativa4 = $justicativa4;
            }
            if (!empty($resultadoPedidoAlteracao)) {
                $justicativa = $resultadoPedidoAlteracao[0]->dsJustificativa;
                $this->view->justificativa = $justicativa;
            }
            if (!empty($resultadoPedidoAlteracao3)) {
                $justicativa3 = $resultadoPedidoAlteracao3[0]->dsJustificativa;
                $this->view->justificativa3 = $justicativa3;
            }

            if (!empty($prazo)) {
                $data1 = $prazo[0]->dtInicioNovoPrazo;
                $data2 =  $prazo[0]->dtFimNovoPrazo;
                //$this->view->data1 = $data1;
               //$this->view->data2 = $data2;
            }
            if (!empty($prazo2)) {
                $data3 = $prazo2[0]->dtInicioNovoPrazo;
                $data4 = $prazo2[0]->dtFimNovoPrazo;
                $this->view->data3 = $data3;
                $this->view->data4 = $data4;
            }

            if (!empty($prazoProjetos)) {
                $dataProjetosInicio = $prazoProjetos[0]->DtInicioExecucao;
                $dataProjetosFinal = $prazoProjetos[0]->DtFimExecucao;
                $this->view->dataProjetosInicio = $dataProjetosInicio;
                $this->view->dataProjetosFinal = $dataProjetosFinal;
            }

            if (!empty($buscatbProposta)) {
                $nomedoProjeto = $buscatbProposta[0]->nmProjeto;
                $this->view->buscanome = $nomedoProjeto;
            }
            if (!empty($resultadoPedidoAlteracao2)) {
                $justicativa2 = $resultadoPedidoAlteracao2[0]->dsJustificativa;
                $this->view->justificativa2 = $justicativa2;
            }

            if (!empty($buscatbProposta2)) {
                $nomedoProjeto2 = $buscatbProposta2[0]->dsFichaTecnica;
                $this->view->buscanome2 = $nomedoProjeto2;
            }

            if (!empty($dados)) {
                $this->view->buscarprodutoSolicitado = $dados;
                $this->view->botao = $enviar;
            } else {
                $dados = $buscaprojeto->buscarProdutosAtual($idProjeto);
                $this->view->buscarprodutoSolicitado = $dados;
            }
        } else {
            $dados = $buscaprojeto->buscarProdutosAtual($idProjeto);
            $locais = $buscaprojeto->buscarLocais($idProjeto); // busca os locais por projeto
            $this->view->somenteabrangencia = "ok";
            $this->view->locais = $locais;
            $this->view->buscarprodutoSolicitado = $dados;
        }
        //Zend_Debug::dump($locais);
    } // fecha m�todo indexAction()



    public function excluirAction()
    {
        $stPedido = 'T';
        if (!empty($_POST)) {
            $status = 7;
            $idSolicitante = 1;
            $idPronac = $_POST['idPronac'];
            $idProdutoExcluir = $_POST['idProduto'];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            if (!empty($idPedidoAlteracao)) {
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
                if (empty($status1)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                }
                $dados = $buscaSoliciatacao->buscarprodutoSolicitado($idPedidoAlteracao);
                if (!empty($dados)) {
                    $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                    $idProjeto = $buscaidProjeto[0]->idProjeto;
                    $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                    foreach ($p as $result) {
                        $idProduto = $result->idProduto;
                        $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                        if (empty($dados)) {
                            $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                        }
                    }
                    $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoExcluir);
                    if (empty($busca)) {
                        $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    } else {
                        $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    }
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
                } else {
                    $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                    $idProjeto = $buscaidProjeto[0]->idProjeto;
                    $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                    foreach ($p as $result) {
                        $idProduto = $result->idProduto;
                        $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                        if (empty($dados)) {
                            $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                        }
                    }
                    $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoExcluir);
                    if (empty($busca)) {
                        $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    } else {
                        $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                        parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
                    }
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
                }
            } else {
                $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                $idProjeto = $buscaidProjeto[0]->idProjeto;
                $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
                if (empty($status1)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                }
                foreach ($p as $result) {
                    $idProduto = $result->idProduto;
                    $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                    if (empty($dados)) {
                        $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                    }
                }
                $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoExcluir);
                if (empty($busca)) {
                    $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                } else {
                    $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
                }
                parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
            }
        } else {
            parent::message("error!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "ERROR");
        }
    } // fecha m�todo excluirAction()



    public function excluir2Action()
    {
        $stPedido = 'A';
        if (!empty($_POST)) {
            $status = 7;
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $idPronac = $_POST['idPronac'];
            $idProdutoExcluir = $_POST['idProduto'];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);

            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            if (!empty($idPedidoAlteracao)) {
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
                if (empty($status1)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                }
                $dados = $buscaSoliciatacao->buscarprodutoSolicitado($idPedidoAlteracao);
                if (!empty($dados)) {
                    $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                    $idProjeto = $buscaidProjeto[0]->idProjeto;
                    $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                    foreach ($p as $result) {
                        $idProduto = $result->idProduto;
                        $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                        if (empty($dados)) {
                            $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                        }
                    }
                    $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoExcluir);
                    if (empty($busca)) {
                        $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    } else {
                        $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    }
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                    $idProjeto = $buscaidProjeto[0]->idProjeto;
                    $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                    foreach ($p as $result) {
                        $idProduto = $result->idProduto;
                        $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                        if (empty($dados)) {
                            $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                        }
                    }
                    $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoExcluir);
                    if (empty($busca)) {
                        $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    } else {
                        $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                        $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                        parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                    }
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                $idProjeto = $buscaidProjeto[0]->idProjeto;
                $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
                if (empty($status1)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                }
                foreach ($p as $result) {
                    $idProduto = $result->idProduto;
                    $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                    if (empty($dados)) {
                        $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                    }
                }
                $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoExcluir);
                if (empty($busca)) {
                    $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                } else {
                    $excluir = $buscaSoliciatacao->excluirProduto($idPedidoAlteracao, $idProdutoExcluir);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
                $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
            }
        } else {
            parent::message("error!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    } // fecha m�todo excluir2Action()



    public function alterarAction()
    {
        // quando o formul�rio � submetido
        if (!empty($_POST)) {
            $status           = 7;
            $stPedido         = 'T';
            $auth             = Zend_Auth::getInstance();
            $idSolicitante    = $auth->getIdentity()->IdUsuario;
            $idPronac         = $_POST["idPronac"];
            $idProdutoNovo    = $_POST["idProduto"];
            //			$idPosicaoLogo    = $_POST["idPosicaoLogo"];
            $areaCultural     = $_POST["areaCultural"];
            $segmentoCultural = $_POST["segmentoCultural"];

            if (empty($idPosicaoLogo)) {
                $idPosicaoLogo = $_POST["Posicao"];
            }
            //die($idPosicaoLogo);

            $qtPatrocinador        = $_POST["Patrocinador"];
            $qtOutros              = $_POST["Divulgacao"];
            $qtProduzida           = $_POST["Beneficiario"];
            $qtVendaNormal         = str_replace(".", "", $_POST["Normal"]);
            $qtVendaNormal         = str_replace(",", "", $qtVendaNormal);
            $qtVendaPromocional    = str_replace(".", "", $_POST["Promocional"]);
            $qtVendaPromocional    = str_replace(",", "", $qtVendaPromocional);
            $vlUnitarioNormal      = Mascara::delMaskMoeda($_POST["Normal_Uni"]);
            $vlUnitarioPromocional = Mascara::delMaskMoeda($_POST["Proporcional"]);
            $idSolicitante         = 1;

            $buscaSoliciatacao = new ReadequacaoProjetos();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $stPedido = 'T';
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;

            if (!empty($idPedidoAlteracao)) {
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
                if (empty($status1)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                }
                $dados = $buscaSoliciatacao->buscarprodutoSolicitado($idPedidoAlteracao);
                if (!empty($dados)) {
                    $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                    $idProjeto = $buscaidProjeto[0]->idProjeto;
                    $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);

                    foreach ($p as $result) {
                        $idProduto = $result->idProduto;
                        $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                        if (empty($dados)) {
                            $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                        }
                    } // fecha foreach
                    $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);

                    if (empty($busca)) {
                        $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                    } else {
                        $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                    }
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
                } else {
                    $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                    $idProjeto = $buscaidProjeto[0]->idProjeto;
                    $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);

                    foreach ($p as $result) {
                        $idProduto = $result->idProduto;
                        $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                        if (empty($dados)) {
                            $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                        }
                    }
                    $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
                    if (empty($busca)) {
                        $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                    } else {
                        $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                        parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
                    }
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
                }
            } // fecha if
            else {
                $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                $idProjeto = $buscaidProjeto[0]->idProjeto;
                $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
                if (empty($status1)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                }
                foreach ($p as $result) {
                    $idProduto = $result->idProduto;
                    $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                    if (empty($dados)) {
                        $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                    }
                }
                $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
                if (empty($busca)) {
                    $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                } else {
                    $updateproduto =$buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
                }
                parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
            }
        } else {
            parent::message("error!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "ERROR");
        }
    } // fecha m�todo alterarAction()



    public function alterar2Action()
    {
        if (!empty($_POST)) {
            $status = 7;
            $stPedido = 'A';
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $idPronac = $_POST["idPronac"];
            $idProdutoNovo = $_POST["idProduto"];
            //$idPosicaoLogo = $_POST["idPedidoAlteracao"];
            $areaCultural = $_POST["areaCultural"];
            $segmentoCultural = $_POST["segmentoCultural"];
            if (empty($idPosicaoLogo)) {
                $idPosicaoLogo = $_POST["Posicao"];
            }
            $qtPatrocinador = $_POST["Patrocinador"];
            $qtOutros = $_POST["Divulgacao"];
            $qtProduzida= $_POST["Beneficiario"];
            $qtVendaNormal = str_replace(".", "", $_POST["Normal"]);
            $qtVendaNormal = str_replace(",", "", $qtVendaNormal);
            $qtVendaPromocional = str_replace(".", "", $_POST["Promocional"]);
            $qtVendaPromocional = str_replace(",", "", $qtVendaPromocional);
//            $vlUnitarioNormal = str_replace(".", "", $_POST["Normal_Uni"]);
//            $vlUnitarioNormal = str_replace(",", "", $vlUnitarioNormal);
//            $vlUnitarioPromocional = str_replace(".", "", $_POST["Proporcional"]);
//            $vlUnitarioPromocional = str_replace(",", "", $vlUnitarioPromocional);
            $vlUnitarioNormal      = Mascara::delMaskMoeda($_POST["Normal_Uni"]);
            $vlUnitarioPromocional = Mascara::delMaskMoeda($_POST["Proporcional"]);

            $idSolicitante = 1;
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $stPedido = 'A';

            $buscaSoliciatacao = new ReadequacaoProjetos();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            if (!empty($idPedidoAlteracao)) {
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
                if (empty($status1)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                }
                $dados = $buscaSoliciatacao->buscarprodutoSolicitado($idPedidoAlteracao);
                if (!empty($dados)) {
                    $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                    $idProjeto = $buscaidProjeto[0]->idProjeto;
                    $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                    foreach ($p as $result) {
                        $idProduto = $result->idProduto;
                        $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                        if (empty($dados)) {
                            $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                        }
                    }
                    $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
                    if (empty($busca)) {
                        $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                        $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    } else {
                        $updateproduto =$buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                        $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    }
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                    $idProjeto = $buscaidProjeto[0]->idProjeto;
                    $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                    foreach ($p as $result) {
                        $idProduto = $result->idProduto;
                        $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                        if (empty($dados)) {
                            $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                        }
                    }
                    $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
                    if (empty($busca)) {
                        $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                        $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    } else {
                        $updateproduto =$buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                        $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                        parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                    }
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                $idProjeto = $buscaidProjeto[0]->idProjeto;
                $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
                if (empty($status1)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                }
                foreach ($p as $result) {
                    $idProduto = $result->idProduto;
                    $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                    if (empty($dados)) {
                        $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                    }
                }
                $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
                if (empty($busca)) {
                    $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                } else {
                    $updateproduto =$buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
                $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
            }
        }
    } // fecha m�todo alterar2Action()



    public function incluirprodutoAction()
    {
        $idPronac = $_POST["idpronac_"];
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
        if (!empty($idPedidoAlteracao)) {
            $valor = $buscaSoliciatacao->alterarPedido($idPedidoAlteracao);
            parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
        } else {
            parent::message("Solicita��o ja foi Enviada!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    } // fecha m�todo incluirprodutoAction()



    public function novoprodutoAction()
    {
        $status = 7;
        $stPedido = 'T';
        $areaCultural = $_POST["areaCultural"];
        $idPronac = $_POST["idPronac"];
        $idProdutoNovo = $_POST["produto"];
        $idPosicaoLogo = $_POST["posicao"];
        $qtPatrocinador = $_POST["Patrocinador"];
        $qtOutros = $_POST["Divulgacao"];
        $qtProduzida= $_POST["Beneficiario"];
        // no banco est� como inteiro
        $qtVendaNormal = str_replace(".", "", $_POST["Normal"]);
        $qtVendaNormal = str_replace(",", "", $qtVendaNormal);
        $qtVendaPromocional = str_replace(".", "", $_POST["Promocional"]);
        $qtVendaPromocional = str_replace(",", "", $qtVendaPromocional);
        $vlUnitarioNormal = Mascara::delMaskMoeda($_POST["Normal_Uni"]);
        $vlUnitarioPromocional = Mascara::delMaskMoeda($_POST["Proporcional"]);
        $auth = Zend_Auth::getInstance();
        $idSolicitante = $auth->getIdentity()->IdUsuario;
        $areaCultural = $_POST["areaCultural"];
        $segmentoCultural = $_POST["segmentoCultural"];
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
        $dsJustificativa = " ";

        if (!empty($idPedidoAlteracao)) {
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
            if (empty($status1)) {
                $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
            }
            $dados = $buscaSoliciatacao->buscarprodutoSolicitado($idPedidoAlteracao);
            if (!empty($dados)) {
                $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                $idProjeto = $buscaidProjeto[0]->idProjeto;
                $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                foreach ($p as $result) {
                    $idProduto = $result->idProduto;
                    $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                    if (empty($dados)) {
                        $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                    }
                }
            
                $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
            
             
                if (empty($busca)) {
                    $inserirproduto =$buscaSoliciatacao->inserirProduto($idPedidoAlteracao, $idProdutoNovo, $areaCultural, $segmentoCultural, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                } else {
                    $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                }
                parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
            } else {
                $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
           
                $idProjeto = $buscaidProjeto[0]->idProjeto;
                $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                foreach ($p as $result) {
                    $idProduto = $result->idProduto;
                    $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                    if (empty($dados)) {
                        $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                    }
                }
           
                $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
                if (empty($busca)) {
                    $inserirproduto =$buscaSoliciatacao->inserirProduto($idPedidoAlteracao, $idProdutoNovo, $areaCultural, $segmentoCultural, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                } else {
                    $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                }
                parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
            }
        } else {
            $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
            $idProjeto = $buscaidProjeto[0]->idProjeto;
            $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
            $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
            
            if (empty($status1)) {
                $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
            }
            foreach ($p as $result) {
                $idProduto = $result->idProduto;
                $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                if (empty($dados)) {
                    $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                }
            }
            $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
            
            if (empty($busca)) {
                $inserirproduto =$buscaSoliciatacao->inserirProduto($idPedidoAlteracao, $idProdutoNovo, $areaCultural, $segmentoCultural, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
            } else {
                $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
            }
            parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac&ativar_menu_produto=ok", "CONFIRM");
        }
    } // fecha m�todo novoprodutoAction()



    public function novoproduto2Action()
    {
        $status = 7;
        $stPedido = 'A';
        $areaCultural = $_POST["areaCultural"];
        $segmentoCultural = $_POST["segmentoCultural"];
        $idPronac = $_POST["idPronac"];
        $idProdutoNovo = $_POST["produto"];
        $idPosicaoLogo = $_POST["posicao"];
        $qtPatrocinador = $_POST["Patrocinador"];
        $qtOutros = $_POST["Divulgacao"];
        $qtProduzida= $_POST["Beneficiario"];
        $qtVendaNormal = str_replace(".", "", $_POST["Normal"]);
        $qtVendaNormal = str_replace(",", "", $qtVendaNormal);
        $qtVendaPromocional = str_replace(".", "", $_POST["Promocional"]);
        $qtVendaPromocional = str_replace(",", "", $qtVendaPromocional);
        //$vlUnitarioNormal = str_replace(".", "", $_POST["Normal_Uni"]);
        //$vlUnitarioNormal = str_replace(",", "", $vlUnitarioNormal);
        //$vlUnitarioPromocional = str_replace(".", "", $_POST["Proporcional"]);
        //$vlUnitarioPromocional = str_replace(",", "", $vlUnitarioPromocional);


        $vlUnitarioNormal      = Mascara::delMaskMoeda($_POST["Normal_Uni"]);
        $vlUnitarioPromocional = Mascara::delMaskMoeda($_POST["Proporcional"]);
        $idSolicitante         = 1;

        $auth = Zend_Auth::getInstance();
        $idSolicitante = $auth->getIdentity()->IdUsuario;
        $areaCultural = $_POST["areaCultural"];
        $segmentoCultural = $_POST["segmentoCultural"];
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
        if (!empty($idPedidoAlteracao)) {
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
            if (empty($status1)) {
                $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
            }
            $dados = $buscaSoliciatacao->buscarprodutoSolicitado($idPedidoAlteracao);
            if (!empty($dados)) {
                $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                $idProjeto = $buscaidProjeto[0]->idProjeto;
                $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                foreach ($p as $result) {
                    $idProduto = $result->idProduto;
                    $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);

                    if (empty($dados)) {
                        $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                    }
                }
                $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
                if (empty($busca)) {
                    $inserirproduto =$buscaSoliciatacao->inserirProduto($idPedidoAlteracao, $idProdutoNovo, $areaCultural, $segmentoCultural, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                } else {
                    $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);

                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                }
                $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
            } else {
                $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
                $idProjeto = $buscaidProjeto[0]->idProjeto;
                $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
                foreach ($p as $result) {
                    $idProduto = $result->idProduto;
                    $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                    if (empty($dados)) {
                        $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                    }
                }
                $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
                if (empty($busca)) {
                    $inserirproduto =$buscaSoliciatacao->inserirProduto($idPedidoAlteracao, $idProdutoNovo, $areaCultural, $segmentoCultural, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                } else {
                    $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
                $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
            }
        } else {
            $buscaidProjeto = $buscaSoliciatacao->buscarID($idPronac);
            $idProjeto = $buscaidProjeto[0]->idProjeto;
            $p = $buscaSoliciatacao->buscarProdutostabelaAtiva($idProjeto);
            $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $status1 = $buscaSoliciatacao->buscaridPedidoAlteracao($idPedidoAlteracao);
            if (empty($status1)) {
                $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
            }
            foreach ($p as $result) {
                $idProduto = $result->idProduto;
                $d = $buscaSoliciatacao->compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto);
                if (empty($dados)) {
                    $inserirprodutotabela = $buscaSoliciatacao->inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto);
                }
            }
            $busca = $buscaSoliciatacao->buscarProdutobd($idPedidoAlteracao, $idProdutoNovo);
            if (empty($busca)) {
                $inserirproduto =$buscaSoliciatacao->inserirProduto($idPedidoAlteracao, $idProdutoNovo, $areaCultural, $segmentoCultural, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
            } else {
                $updateproduto = $buscaSoliciatacao->alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural);
            }
            parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
        }
    }




    public function acaoAction()
    {
        $mapperArea = new Agente_Model_AreaMapper();

        $this->view->comboareasculturais = $mapperArea->fetchPairs('codigo', 'descricao');
        $stPedido = 'T';

        if (!empty($_POST)) {
            $idPronac  = $_POST['idPronac'];
            $idProduto = $_POST['idProduto'];
            $buscaprojeto = new ReadequacaoProjetos();
            $valores = $buscaprojeto->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $resultado = $buscaprojeto->buscarProjetos($idPronac);
            $this->view->buscaprojeto = $resultado;
            $resultadoproduto = $buscaprojeto->buscarProdutos($idPronac);
            
            $this->view->buscaproduto = $resultadoproduto;
            $resultadodescricao = $buscaprojeto->buscarDescricao();
            $this->view->buscadescricao = $resultadodescricao;
            $resultadoposicao = $buscaprojeto->buscarPosicao();
            $this->view->buscaposicao = $resultadoposicao;

            if (!empty($idPedidoAlteracao)) {
                $enviar = $buscaprojeto->verificarBotao($idPedidoAlteracao);
                $resultadoprodutoacao = $buscaprojeto->buscarProdutobd($idPedidoAlteracao, $idProduto);
                              
                if (!empty($resultadoprodutoacao)) {
                    $this->view->buscarprodutoAcao = $resultadoprodutoacao;
                    $this->view->botao = $enviar;
                    $this->view->Tela = "Tela1";
                } else {
                    $resultadoid = $buscaprojeto->buscarID($idPronac);
                    $idProjeto = $resultadoid[0]->idProjeto;
                    $enviar = $buscaprojeto->verificarBotao($idPedidoAlteracao);
                    $resultadoprodutoacao = $buscaprojeto->buscarprodutoPlano($idProjeto, $idProduto);
                    $this->view->buscarprodutoAcao = $resultadoprodutoacao;
                    $this->view->botao = $enviar;
                }
            } else {
                $resultadoid = $buscaprojeto->buscarID($idPronac);
                $idProjeto = $resultadoid[0]->idProjeto;
                $resultadoprodutoacao = $buscaprojeto->buscarProdutosOpcao($idProjeto, $idProduto);
                                
                $this->view->buscarprodutoAcao = $resultadoprodutoacao;
            }

            $buscaProjetoProduto = new SolicitarReadequacaoCustoDAO();
            $verificaPlanilhaCusto = $buscaProjetoProduto->buscarProdutoAprovacao($idPronac);

            $this->view->buscaPlanilhaCusto = $verificaPlanilhaCusto;
        } // fecha if
        else {
            parent::message("error!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }



    public static function opcaoEnviar()
    {
        $idPronac = $_POST['idpronac'];
        $buscaprojeto = new ReadequacaoProjetos();
        $valores = $buscaprojeto->buscarSolicitacao($idPronac);
        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
        //$value = $buscaprojeto->verificaSolicitacao($idPedidoAlteracao);


        if (!empty($value)) {
            return ('Solicitar Altera��o');
        } else {
            return ;
        }
    }



    public static function proposta($idPronac)
    {
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $mostrar = $buscaSoliciatacao->verificarMenu($idPronac);
                                       
        if (empty($mostrar)) {
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            if (!empty($idPedidoAlteracao)) {
                $proposta = $buscaSoliciatacao->verificarProposta($idPedidoAlteracao);
                if (empty($proposta)) {
                    return "<a href='#' id='abrir_fechar2'>Proposta Pedagogica</a>";
                } else {
                }
            } else {
                return "<a href='#' id='abrir_fechar2'>Proposta Pedagogica</a>";
            }
        } else {
        }
    }
    public static function botao($idPronac)
    {
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
        $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;

        if (!empty($idPedidoAlteracao)) {
            $enviar = $buscaSoliciatacao->verificarBotao($idPedidoAlteracao);
            if (empty($enviar)) {
            } else {
                return "Enviar Solicita��o";
            }
        }
    }
    public static function Menu($idPronac)
    {
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $mostrar = $buscaSoliciatacao->verificarMenu($idPronac);
      
        if (!empty($mostrar)) {
            $menu = $mostrar[0]->stPedidoAlteracao;
       
            switch ($menu) {
    case 'I':
        return "Sem Menu";
        break;
    case 'T':
       return "Com Menu";
        break;
    case 'A':
        return "Bot�o";
        break;
    case '':
        return "Com Menu";
        break;
       }
        } else {
            return "Com Menu";
        }
    }

    
    public static function Teste($idPronac)
    {
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $mostrar = $buscaSoliciatacao->verificarMenu($idPronac);

        if (empty($mostrar)) {
            return 'aaaa';
        } else {
        }
    }


    public function enviarsolicitacaopropostaAction()
    {
        if (!empty($_POST)) {
            $status = 6;
            $nomedoprojeto = $_POST["recurso1"];
            $idPronac = $_POST["idPronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso2"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $coluna = 'dsEspecificacaoTecnica';
            $stPedido = 'T';

            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarNomeProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                } else {
                    $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitaralteracao/acaoprojeto", "ERROR");
        }
    }

    public function naoenviarsolicitacaopropostaAction()
    {
        if (!empty($_POST)) {
            $status = 6;
            $nomedoprojeto = $_POST["recurso1"];
            $idPronac = $_POST["idPronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso2"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $coluna = 'dsEspecificacaoTecnica';
            $stPedido = 'A';

            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarNomeProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                } else {
                    $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        }
    }

    public function nomeproponenteAction()
    {
        if (!empty($_POST)) {
            $stPedido = 'T';
            $nomedoprojeto = $_POST["nomeprojeto"];
            $CNPJCPF = $_POST["CgcCpf"];
            $status = 5;
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso7"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $coluna = 'nmProjeto';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarNomeProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                        //$inserirtbProposta = $Projetos->insertNomeProponente($idPedidoAlteracao, $CNPJCPF, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                        //$updatetbProposta = $Projetos->updateNomeProponente($idPedidoAlteracao, $CNPJCPF, $nomedoprojeto);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                        //$inserirtbProposta = $Projetos->insertNomeProponente($idPedidoAlteracao, $CNPJCPF, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                        //$updatetbProposta = $Projetos->updateNomeProponente($idPedidoAlteracao, $CNPJCPF, $nomedoprojeto);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    //$inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                } else {
                    $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    //$updatetbProposta = $Projetos->updateNomeProponente($idPedidoAlteracao, $CNPJCPF, $nomedoprojeto);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }
    public function nomeproponente2Action()
    {
        if (!empty($_POST)) {
            $stPedido = 'A';
            $nomedoprojeto = $_POST["nomeprojeto"];
            $status = 5;
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso7"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $coluna = 'nmProjeto';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarNomeProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                } else {
                    $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }
    public function termoAction()
    {
        if (!empty($_POST)) {
            $stPedido = 'T';
            $nomedoprojeto = $_POST["nomeprojeto"];
            $status = 3;
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso5"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $coluna = 'dsFichaTecnica';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarNomeProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                } else {
                    $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message("Error!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }


    public function termo2Action()
    {
        if (!empty($_POST)) {
            $stPedido = 'A';
            $nomedoprojeto = $_POST["nomeprojeto"];
            $status = 3;
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso5"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $coluna = 'dsFichaTecnica';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $buscatbProposta = $Projetos->buscarNomeProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    if (empty($buscatbProposta)) {
                        $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    } else {
                        $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                if (empty($buscatbProposta)) {
                    $inserirtbProposta = $Projetos->inserttbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                } else {
                    $updatetbProposta = $Projetos->updatetbProposta($idPedidoAlteracao, $coluna, $nomedoprojeto);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message(" Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }


    public function localAction()
    {
        if (!empty($_POST)) {
            $stPedido = 'T';
            $idPais= $_POST["pais"];
            $idUF= $_POST["estados"];
            if (empty($idUF)) {
                $idUF=0;
            }
            $idMunicipioIBGE = $_POST["cidade"];
            if (empty($idMunicipioIBGE)) {
                $idMunicipioIBGE=0;
            }
            $status = 4;
            $tpAcao = 'I';
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso6"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $resultadoid = $buscaSoliciatacao->buscarID($idPronac);
            $idProjeto = $resultadoid[0]->idProjeto;

            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    } else {
                        $idAbrangencia = $prazo[0]->idAbrangencia;
                        $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                    }


                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    $compararInserirAbrangencia = $Projetos->compararInserirAbrangencia($idProjeto, $idPedidoAlteracao);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    } else {
                        $idAbrangencia = $prazo[0]->idAbrangencia;
                        $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                    }


                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $compararInserirAbrangencia = $Projetos->compararInserirAbrangencia($idProjeto, $idPedidoAlteracao);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                if (empty($prazo)) {
                    $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                } else {
                    $idAbrangencia = $prazo[0]->idAbrangencia;
                    $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                }

                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    $compararInserirAbrangencia = $Projetos->compararInserirAbrangencia($idProjeto, $idPedidoAlteracao);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    $compararInserirAbrangencia = $Projetos->compararInserirAbrangencia($idProjeto, $idPedidoAlteracao);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message("Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }
    public function local2Action()
    {
        if (!empty($_POST)) {
            $stPedido = 'A';
            $idPais= $_POST["pais"];
            $idUF= empty($_POST["estados"]) ? 0 : $_POST["estados"];
            $idMunicipioIBGE = empty($_POST["cidade"]) ? 0 : $_POST["cidade"];
            $status = 4;
            $tpAcao = 'I';
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso6"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;

            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    } else {
                        $idAbrangencia = $prazo[0]->idAbrangencia;
                        $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                    }


                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    } else {
                        $idAbrangencia = $prazo[0]->idAbrangencia;
                        $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                    }


                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                if (empty($prazo)) {
                    $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                } else {
                    $idAbrangencia = $prazo[0]->idAbrangencia;
                    $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                }

                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message("Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }

    public function prazoAction()
    {
        if (!empty($_POST)) {
            $stPedido = 'T';
            $dtInicioNovoPrazo  = $_POST["data1"];
            $dtInicioNovoPrazo = data::dataAmericana($dtInicioNovoPrazo);
            $dtFimNovoPrazo = $_POST["data2"];
            $dtFimNovoPrazo = data::dataAmericana($dtFimNovoPrazo);
            $status = 9;
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso8"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $tpProrrogacao = 'E';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    } else {
                        $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    } else {
                        $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                if (empty($prazo)) {
                    $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                } else {
                    $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message("Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }
    public function prazo2Action()
    {
        if (!empty($_POST)) {
            $stPedido = 'A';
            $dtInicioNovoPrazo  = $_POST["data1"];
            $dtInicioNovoPrazo = data::dataAmericana($dtInicioNovoPrazo);
            $dtFimNovoPrazo = $_POST["data2"];
            $dtFimNovoPrazo = data::dataAmericana($dtFimNovoPrazo);
            $status = 9;
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso8"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $tpProrrogacao = 'E';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    } else {
                        $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    } else {
                        $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                if (empty($prazo)) {
                    $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                } else {
                    $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message("Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }
    public function captacaoAction()
    {
        if (!empty($_POST)) {
            $stPedido = 'T';
            $dtInicioNovoPrazo  = $_POST["data1"];
            $dtInicioNovoPrazo = data::dataAmericana($dtInicioNovoPrazo);
            $dtFimNovoPrazo = $_POST["data2"];
            $dtFimNovoPrazo = data::dataAmericana($dtFimNovoPrazo);
            $status = 8;
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso9"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $tpProrrogacao = 'C';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    } else {
                        $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    } else {
                        $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                if (empty($prazo)) {
                    $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                } else {
                    $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message("Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }
    public function captacao2Action()
    {
        if (!empty($_POST)) {
            $stPedido = 'A';
            $dtInicioNovoPrazo  = $_POST["data1"];
            $dtInicioNovoPrazo = data::dataAmericana($dtInicioNovoPrazo);
            $dtFimNovoPrazo = $_POST["data2"];
            $dtFimNovoPrazo = data::dataAmericana($dtFimNovoPrazo);
            $status = 8;
            $idPronac = $_POST["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = $_POST["recurso9"];
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $tpProrrogacao = 'C';
            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante, $stPedido);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    } else {
                        $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    }

                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                    $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    } else {
                        $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            } else {
                $buscatbProposta = $Projetos->buscatbProposta($idPedidoAlteracao);
                $prazo = $buscaSoliciatacao->BuscarPrazo($idPedidoAlteracao, $tpProrrogacao);
                if (empty($prazo)) {
                    $insertPrazo = $buscaSoliciatacao->insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                } else {
                    $updatePrazo = $buscaSoliciatacao->updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao);
                }
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $enviarsolicitacao = $buscaSoliciatacao->alterarSolicitacao($idPedidoAlteracao, $stPedido);
                    SolicitaralteracaoController::cadastrarArquivosMult($_FILES, $idPedidoAlteracao, $status);
                    parent::message("Solicita��o enviada com sucesso!", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "CONFIRM");
                }
            }
        } else {
            parent::message("Dados Obrigat�rios N�o Informados", "solicitarreadequacaodoprojeto/index?idpronac=$idPronac", "ERROR");
        }
    }

    public function buscarcidadeAction()
    {
        $this->_helper->layout->disableLayout();
        $idUF = $_GET['idUF'];
        if (!empty($idUF)) {
            $cidade = new Cidade();
            $r_cidade = $cidade->buscar($idUF);
            $this->view->buscacidade = $r_cidade;
        }
    }



    public function excluirlocaisAction()
    {
        $buscaSoliciatacao = new ReadequacaoProjetos();
        $idAbrangencia = $_GET['idAbrangencia'];
        $dsJustificativaExclusao = $_GET["dsJustificativaExclusao"];

        if (!empty($idAbrangencia)) {
            $excluir = $buscaSoliciatacao->excluirLocais($idAbrangencia, $dsJustificativaExclusao);
            echo true;
            exit;
        }
    }


    public function verificarexclusaolocalAction()
    {
        if (!empty($_GET)) {
            $stPedido = 'A';
            $idPais= $_GET["pais"];
            $idUF= $_GET["estados"];
            
            if (empty($idUF)) {
                $idUF=0;
            }
            $idMunicipioIBGE = $_GET["cidade"];
            if (empty($idMunicipioIBGE)) {
                $idMunicipioIBGE=0;
            }
            $status = 4;
            $tpAcao = 'E';
            $idPronac = $_GET["idpronac"];
            $auth = Zend_Auth::getInstance();
            $idSolicitante = $auth->getIdentity()->IdUsuario;
            $dsJustificativa = 'inser��o';
            $buscaSoliciatacao = new ReadequacaoProjetos();
            $Projetos = new SolicitarAlteracaoDAO();
            $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
            $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
            $resultadoid = $buscaSoliciatacao->buscarID($idPronac);
            $idProjeto = $resultadoid[0]->idProjeto;

            if (empty($idPedidoAlteracao)) {
                $inserirSolitacao = $buscaSoliciatacao->inserirSolicitacao($idPronac, $idSolicitante);
                $valores = $buscaSoliciatacao->buscarSolicitacao($idPronac);
                $idPedidoAlteracao = $valores[0]->idPedidoAlteracao;
                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao, $stPedido);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao, $tpAcao);
                    } else {
                        $idAbrangencia = $prazo[0]->idAbrangencia;
                        $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                    }


                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $compararInserirAbrangencia = $Projetos->compararInserirAbrangencia($idProjeto, $idPedidoAlteracao);
                } else {
                    $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                    if (empty($prazo)) {
                        $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao, $tpAcao);
                    } else {
                        $idAbrangencia = $prazo[0]->idAbrangencia;
                        $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                    }
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $compararInserirAbrangencia = $Projetos->compararInserirAbrangencia($idProjeto, $idPedidoAlteracao);
                }
            } else {
                $prazo = $buscaSoliciatacao->buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao);
                if (empty($prazo)) {
                    $insertPrazo = $buscaSoliciatacao->insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao, $tpAcao);
                } else {
                    $idAbrangencia = $prazo[0]->idAbrangencia;
                    $updatePrazo = $buscaSoliciatacao->updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia);
                }

                $resultadoPedidoAlteracao = $Projetos->buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status);
                if (empty($resultadoPedidoAlteracao)) {
                    $justificativa =  $buscaSoliciatacao->inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status);
                    $compararInserirAbrangencia = $Projetos->compararInserirAbrangencia($idProjeto, $idPedidoAlteracao);
                } else {
                    $justificativa =  $Projetos->updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status);
                    $compararInserirAbrangencia = $Projetos->compararInserirAbrangencia($idProjeto, $idPedidoAlteracao);
                }
            }
            // excluir o registro inserido
            $excluirLocal = SolicitarAlteracaoDAO::excluirArquivoDuplicado($idPedidoAlteracao, $idPais, $idUF, $idMunicipioIBGE);

            // inclui a justificativa do item exclu�do
            $alterarJustificativa = SolicitarAlteracaoDAO::alterarJustificativaPrimeiroArquivo($idPedidoAlteracao, $idPais, $idUF, $idMunicipioIBGE, $_GET["dsJustificativaExclusao"]);
        }
    }
}
