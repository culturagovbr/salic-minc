<?php

/**
 * @since 07/06/2010
 * @link http://www.cultura.gov.br
 */
class Proposta_ManterorcamentoController extends Proposta_GenericController
{

    /**
     * Reescreve o metodo init()
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->verificarPermissaoAcesso(true, false, false);
    }

    /**
     * Redireciona para o fluxo inicial do sistema
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        // Usuario Logado
        $auth = Zend_Auth::getInstance(); // instancia da autenticacao
        $idusuario = $auth->getIdentity()->usu_codigo;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessao com o grupo ativo
        $codOrgao = $GrupoAtivo->codOrgao; //  orgao ativo na sessao

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
    }

    /**
     * produtoscadastradosAction
     *
     * @name produtoscadastradosAction
     */
    public function produtoscadastradosAction()
    {
        $this->view->idPreProjeto = $this->idPreProjeto;

        $this->view->charset = Zend_Registry::get('config')->db->params->charset;
    }

    /**
     * Lista os produtos na planilha orcamentaria
     *
     * @access public
     * @return void
     */
    public function listarprodutosAction()
    {
        $this->_helper->layout->disableLayout();

        $tbPreprojeto = new Proposta_Model_DbTable_PreProjeto();
        $this->view->Produtos = $tbPreprojeto->listarProdutos($this->idPreProjeto);

        $manterOrcamento = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $listaEtapa = $manterOrcamento->buscarEtapas('P');
        $this->view->EtapasProduto = $this->reordenaretapas($listaEtapa);

        $this->view->ItensProduto = $tbPreprojeto->listarItensProdutos($this->idPreProjeto, null, Zend_DB::FETCH_ASSOC);

        $arrBusca = array(
            'idprojeto' => $this->idPreProjeto,
            'stabrangencia' => 1
        );

        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $locais = $tblAbrangencia->buscar($arrBusca);

        // Remove outros paises para cadastro na planilha
        $novosLocais = array();
        foreach ($locais as $key => $local) {
            if (isset($local['idPais']) && $local['idPais'] == 31) {
                $novosLocais[] = $local;
            }
        }
        $this->view->localRealizacao = $novosLocais;

        $CustosMapper = new Proposta_Model_TbCustosVinculadosMapper();
        $this->view->custoVinculadoProponente = $CustosMapper->findBy(array('idProjeto' => $this->idPreProjeto));
//
//     $this->view->idPreProjeto = $this->idPreProjeto;
//     $this->view->charset = Zend_Registry::get('config')->db->params->charset;
    }

    /**
     * altera ordem de apresentação das etapas no orcamento
     *
     * @access public
     * @return void
     */
    public function reordenaretapas($etapas)
    {
        if (empty($etapas)) {
            return false;
        }

        $newListaEtapa = $etapas;
        $newListaEtapa[3] = $etapas[2];
        $newListaEtapa[2] = $etapas[3];

        return $newListaEtapa;
    }

    /**
     * planilhaorcamentariaAction
     *
     *
     * @access public
     * @return void
     */
    public function planilhaorcamentariaAction()
    {
        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    /**
     * planilhaorcamentariageralAction
     *
     * @access public
     * @return void
     */
    public function planilhaorcamentariageralAction()
    {
        $this->view->tipoPlanilha = 0; // 0=Planilha Or?ament?ria da Proposta
        $this->view->idPreProjeto = $this->getRequest()->getParam('idPreProjeto');
    }

    /**
     * consultarcomponenteAction
     *
     * @access public
     * @return void
     * @deprecated metodo substituido apos a IN 2017
     */
    public function consultarcomponenteAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o layout


        if (!empty($this->idPreProjeto) || $this->idPreProjeto == '0') {
            $uf = new Agente_Model_DbTable_UF();
            $this->view->Estados = $uf->buscar();

            $buscarProduto = new Proposta_Model_DbTable_PreProjeto();
            $this->view->Produtos = $buscarProduto->buscarProdutos($this->idPreProjeto);

            $buscarEtapa = ManterorcamentoDAO::buscarEtapasProdutos($this->idPreProjeto);
            $this->view->Etapa = $buscarEtapa;

            $buscarItem = ManterorcamentoDAO::buscarItensProdutos($this->idPreProjeto);
            $this->view->Item = $buscarItem;

            $buscarPlanilhaProduto = ManterorcamentoDAO::buscarPlanilhaOrcamentariaP($this->idPreProjeto);
            $this->view->PlanilhaProduto = $buscarPlanilhaProduto;

            $buscarPlanilhaCustos = ManterorcamentoDAO::buscarPlanilhaOrcamentariaC($this->idPreProjeto);
            $this->view->PlanilhaCusto = $buscarPlanilhaCustos;

            $buscarPlanilha = ManterorcamentoDAO::buscarPlanilha($this->idPreProjeto);
            $this->view->Planilha = $buscarPlanilha;

            $buscarPlanilhaEtapa = ManterorcamentoDAO::buscarPlanilhaEtapa($this->idPreProjeto);
            $this->view->PlanilhaEtapa = $buscarPlanilhaEtapa;

            $this->view->idPreProjeto = $this->idPreProjeto;
        } else {
            return false;
        }
    }

    /**
     * cadastrarprodutosAction
     *
     * @access public
     * @return void
     * @deprecated metodo substituido apos a IN 2017
     */
    public function cadastrarprodutosAction()
    {
        $this->_helper->layout->disableLayout();

        $this->view->idPreProjeto = $this->idPreProjeto;

        if (isset($_GET['idPreProjeto']) && isset($_GET['produto'])) {
            $idPreProjeto = $_GET['idPreProjeto'];
            $idProduto = $_GET['produto'];
            $TDP = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
            $this->view->Dados = $TDP->buscarDadosCadastrarProdutos($idPreProjeto, $idProduto);
            $this->view->idProduto = $idProduto;
        }

        if (isset($_POST['iduf'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $iduf = $_POST['iduf'];

            $tbMun = new Agente_Model_DbTable_Municipios();
            $cidade = $tbMun->listar($iduf);

            $a = 0;
            foreach ($cidade as $DadosCidade) {
                $cidadeArray[$a]['idCidade'] = $DadosCidade->id;
                $cidadeArray[$a]['nomeCidade'] = utf8_encode($DadosCidade->Descricao);
                $a++;
            }
            $this->_helper->json($cidadeArray);
            die;
        }

        if (isset($_POST['idetapa'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $idetapa = $_POST['idetapa'];
            $idProduto = $_POST['idProduto'];

            $itensPlanilhaProduto = new tbItensPlanilhaProduto();
            $item = $itensPlanilhaProduto->buscarItens($idetapa, $idProduto);

            if (count($item) <= 0) {
                $item = $itensPlanilhaProduto->buscarItens($idetapa, null);
            }
            $a = 0;
            foreach ($item as $Dadositem) {
                $itemArray[$a]['idItem'] = $Dadositem->idPlanilhaItens;
                $itemArray[$a]['nomeItem'] = utf8_encode($Dadositem->Descricao);
                $a++;
            }
            $this->_helper->json($itemArray);
            die;
        }

        $etapaSelecionada["id"] = $_GET["etapa"];
        $etapaSelecionada["etapaNome"] = $_GET["etapaNome"];
        $this->view->etapaSelecionada = $etapaSelecionada;

        $uf = new Agente_Model_DbTable_UF();
        $buscarEstado = $uf->buscar();
        $this->view->Estados = $buscarEstado;

        $buscarEtapa = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $this->view->Etapa = $buscarEtapa->buscarEtapasCadastrarProdutos();

        $buscarRecurso = new Proposta_Model_DbTable_Verificacao();
        $this->view->Recurso = $buscarRecurso->buscarFonteRecurso();

        $buscarUnidade = new Proposta_Model_DbTable_PlanilhaUnidade();
        $this->view->Unidade = $buscarUnidade->buscarUnidade();

        $buscarItem = new Proposta_Model_DbTable_PreProjeto();
        $this->view->Item = $buscarItem->listarItensProdutos($this->idPreProjeto);

        $buscarProduto = new Proposta_Model_DbTable_PreProjeto();
        $this->view->Produtos = $buscarProduto->buscarProdutos($this->idPreProjeto);
    }

    public function custosvinculadosAction()
    {
        $this->view->acao = $this->_urlPadrao . "/proposta/manterorcamento/salvarpercentuaiscustosvinculados";

        $arrBusca = array(
            'idprojeto' => $this->idPreProjeto,
            'stabrangencia' => 1
        );

        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $this->view->localRealizacao = $tblAbrangencia->buscar($arrBusca);

        $this->view->itensCustosVinculados = $this->gerarArrayCustosVinculados($this->idPreProjeto);
    }

    public function salvarpercentuaiscustosvinculadosAction()
    {
        $params = $this->getRequest()->getParams();

        $custosVinculados = $params['itensCustosVinculados'];
        $arrayCustosVinculados = $this->gerarArrayCustosVinculados($params['idPreProjeto']);

        $mapper = new Proposta_Model_TbCustosVinculadosMapper();

        try {
            foreach ($custosVinculados as $key => $item) {
                if (in_array($key, array_column($arrayCustosVinculados, 'idPlanilhaItens'))) {
                    $dados = array(
                        'idCustosVinculados' => $item['idCustosVinculados'],
                        'idProjeto' => $params['idPreProjeto'],
                        'idPlanilhaItem' => $key,
                        'dtCadastro' => new Zend_Db_Expr('getdate()'),
                        'pcCalculo' => $item['percentual'],
                        'idUsuario' => $params['idagente']
                    );

                    $mapper->save(new Proposta_Model_TbCustosVinculados($dados));
                }
            }

            $this->atualizarcustosvinculadosdaplanilha($this->idPreProjeto);

            parent::message('Cadastro realizado com sucesso!', "/proposta/manterorcamento/custosvinculados?idPreProjeto=" . $this->idPreProjeto, "CONFIRM");
        } catch (Zend_Exception $ex) {
            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!" . $ex->getMessage(), "/proposta/manterorcamento/custosvinculados?idPreProjeto=" . $this->idPreProjeto, "ERROR");
        }
    }

    /**
     * resumoplanilhaAction
     *
     * @name resumoplanilhaAction
     */
    public function resumoplanilhaAction()
    {
        $this->_helper->layout->disableLayout();

        $tbPreprojeto = new Proposta_Model_DbTable_PreProjeto();
        $itens = $tbPreprojeto->listarItensProdutos($this->idPreProjeto, null, Zend_DB::FETCH_ASSOC);

        $manterOrcamento = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $listaEtapa = $manterOrcamento->buscarEtapas('P', Zend_DB::FETCH_ASSOC);

        $this->view->EtapaCusto = $manterOrcamento->buscarEtapas("A");
        $this->view->ItensEtapaCusto = $manterOrcamento->listarItensCustosAdministrativos($this->idPreProjeto, "A");

        $valorEtapa = array();

        if ($itens) {
            foreach ($itens as $item) {
                $valorTotalItem = $item['Quantidade'] * $item['Ocorrencia'] * $item['ValorUnitario'];
                $valorEtapa[$item['idEtapa']] = $valorTotalItem + $valorEtapa[$item['idEtapa']];
            }

            foreach ($listaEtapa as $etapa) {
                if (isset($valorEtapa[$etapa['idEtapa']])) {
                    $etapa['valorTotal'] = $valorEtapa[$etapa['idEtapa']];
                }

                $etapasPlanilha[] = $etapa;
            }

            $this->view->EtapasProduto = $this->reordenaretapas($etapasPlanilha);
        }
    }

    /**
     * formitemAction
     *
     * @access public
     * @return void
     */
    public function formitemAction()
    {
        $this->_helper->layout->disableLayout();

        $this->view->idPreProjeto = $this->idPreProjeto;
        $this->view->locaisRealizacao = array();

        $params = $this->getRequest()->getParams();

        $idPreProjeto = $params['idPreProjeto'];
        $idProduto = $params['produto'];
        $iduf = $params['idUf'];
        $idMunicipio = $params['idMunicipio'];
        $etapa = $params['etapa'];

        if (!empty($params['idPlanilhaProposta'])) { // editar item
            $idProposta = $params['idPreProjeto'];
            $idEtapa = $params['etapa'];
            $idProduto = $params['produto'];
            $idItem = $params['item'];
            $idPlanilhaProposta = $params['idPlanilhaProposta'];
            $tblanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $buscaDados = $tblanilhaProposta->buscarDadosEditarProdutos($idProposta, $idEtapa, $idProduto, $idItem, $idPlanilhaProposta, $iduf, $idMunicipio);

            $this->view->Dados = $buscaDados;
        } else { // novo item
            if (!empty($idPreProjeto) && !empty($idProduto)) {
                $TDP = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
                $this->view->Dados = $TDP->buscarDadosCadastrarProdutos($idPreProjeto, $idProduto);
                $this->view->idProduto = $idProduto;
            }

            $tbLocaisDeRealizacao = new Proposta_Model_DbTable_Abrangencia();
            $this->view->locaisRealizacao = $tbLocaisDeRealizacao->buscar(['idProjeto' => $params['idPreProjeto']]);
        }

        $uf = new Agente_Model_DbTable_UF();
        $estado = $uf->findBy(array("iduf" => $iduf));
        $this->view->Estado = $estado;

        $mun = new Agente_Model_DbTable_Municipios();
        $municipio = $mun->findBy(array("idMunicipioIBGE" => $idMunicipio));
        $this->view->Municipio = $municipio;

        $buscarEtapa = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $this->view->Etapa = $buscarEtapa->findBy(array('idPlanilhaEtapa' => $etapa));

        $buscarRecurso = new Proposta_Model_DbTable_Verificacao();
        $this->view->Recurso = $buscarRecurso->buscarFonteRecurso();

        $buscarUnidade = new Proposta_Model_DbTable_PlanilhaUnidade();
        $this->view->Unidade = $buscarUnidade->buscarUnidade();

        $itensPlanilhaProduto = new tbItensPlanilhaProduto();
        $this->view->Item = $itensPlanilhaProduto->buscarItens($etapa, $idProduto);

        $buscarProduto = new Proposta_Model_DbTable_PreProjeto();
        $this->view->Produtos = $buscarProduto->buscarProdutos($this->idPreProjeto);
    }

    /**
     * salvaritemaction
     *
     * @access public
     * @return void
     */
    public function salvaritemAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->getRequest()->getParams();

        $idPreProjeto = $params['idPreProjeto'];

        $justificativa = utf8_decode(substr(trim(strip_tags($params['justificativa'])), 0, 1000));

        $dados = array(
            'idProjeto' => $idPreProjeto,
            'idProduto' => $params['produto'],
            'idEtapa' => $params['idPlanilhaEtapa'],
            'idPlanilhaItem' => $params['planilhaitem'],
            'Descricao' => '',
            'Unidade' => $params['unidade'],
            'Quantidade' => $params['qtd'],
            'Ocorrencia' => $params['ocorrencia'],
            'ValorUnitario' => str_replace(",", ".", str_replace(".", "", $params['vlunitario'])),
            'QtdeDias' => $params['qtdDias'],
            'TipoDespesa' => 0,
            'TipoPessoa' => 0,
            'Contrapartida' => 0,
            'FonteRecurso' => $params['fonterecurso'],
            'UfDespesa' => $params['uf'],
            'MunicipioDespesa' => $params['municipio'],
            'dsJustificativa' => $justificativa,
            'idUsuario' => $this->idUsuario,
            'stCustoPraticado' => $params['stCustoPraticado']
        );

        $idPlanilhaProposta = isset($params['idPlanilhaProposta']) ? $params['idPlanilhaProposta'] : '';

        $retorno[] = self::salvarItemPlanilha($dados, $idPlanilhaProposta); # salva o item principal

        $outrasLocalidades = isset($params['comboOutrasCidades']) ? $params['comboOutrasCidades'] : '';

        if ($outrasLocalidades && count($outrasLocalidades) > 0) {
            foreach ($outrasLocalidades as $localidade) {
                if ($localidade == 'all') {
                    continue;
                }

                $custoPraticado = isset($params['stCustoPraticado_' . $localidade]) ? $params['stCustoPraticado_' . $localidade] : 0;

                # se o custo praticado for igual a 1, justificativa eh obrigatoria
                if ($custoPraticado == 1) {
                    $justificativa = isset($params['justificativa_' . $localidade]) ? $params['justificativa_' . $localidade] : '';

                    $dados['dsJustificativa'] = utf8_decode(substr(trim(strip_tags($justificativa)), 0, 1000));

                    if (empty($justificativa)) {
                        continue;
                    }
                }

                $dados['stCustoPraticado'] = $custoPraticado;

                $estadoMunicipio = explode(':', $localidade);
                $dados['UfDespesa'] = $estadoMunicipio[0];
                $dados['MunicipioDespesa'] = $estadoMunicipio[1];

                $retorno[] = self::salvarItemPlanilha($dados, null, true);
            }
        }

        $this->atualizarcustosvinculadosdaplanilha($idPreProjeto);

        $this->_helper->json($retorno);
        die;
    }

    private function salvarItemPlanilha($dados, $idPlanilhaProposta = null, $outraLocalidade = false)
    {
        $resultAlterarProjeto = true;
        $idPreProjeto = $dados['idProjeto'];

        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $buscarProdutos = $tbPlanilhaProposta->buscarDadosEditarProdutos(
            $idPreProjeto,
            $dados['idEtapa'],
            $dados['idProduto'],
            $dados['idPlanilhaItem'],
            null,
            $dados['UfDespesa'],
            $dados['MunicipioDespesa'],
            null,
            null,
            null,
            null,
            null,
            $dados['FonteRecurso']
        );

        $buscarProdutos = converterObjetosParaArray($buscarProdutos);

        if ($buscarProdutos && !in_array($idPlanilhaProposta, array_column($buscarProdutos, 'idPlanilhaProposta'))) {
            $retorno['msg'] = "Item duplicado na mesma etapa!";
            $retorno['close'] = false;
            $retorno['status'] = false;
        } else {
            if ($this->isEditarProjeto($idPreProjeto)) {
                $verifica = $this->verificarSeUltrapassaValorOriginal($idPreProjeto, $dados, $idPlanilhaProposta);

                if ($verifica && $dados['FonteRecurso'] == 109) {
                    $retorno['msg'] = "O item cadastrado ultrapassa o valor original do projeto!";
                    $retorno['close'] = false;
                    $retorno['status'] = false;
                    $resultAlterarProjeto = false;
                }
            }

            if ($resultAlterarProjeto == true) {
                if (empty($idPlanilhaProposta)) { #insert

                    if ($buscarProdutos) {
                        $retorno['msg'] = "Item duplicado na mesma etapa!";
                        $retorno['close'] = false;
                        $retorno['status'] = false;
                    } else {
                        $result = $tbPlanilhaProposta->insert($dados);

                        if ($result) {
                            $retorno['idPlanilhaProposta'] = $result;
                            $retorno['msg'] = "Item cadastrado com sucesso.";
                            $retorno['close'] = false;
                            $retorno['status'] = true;
                            $retorno['action'] = 'insert';
                        }
                    }
                } else { #update

                    if (isset($dados['idProduto'])) {
                        $where = "idPlanilhaProposta = " . $idPlanilhaProposta;

                        $result = $tbPlanilhaProposta->update($dados, $where);

                        if ($result) {
                            $retorno['idPlanilhaProposta'] = $idPlanilhaProposta;
                            $retorno['msg'] = "Altera&ccedil;&atilde;o realizada com sucesso!";
                            $retorno['close'] = true;
                            $retorno['status'] = true;
                            $retorno['action'] = 'update';
                        }
                    }
                }
            }

            if (isset($retorno['idPlanilhaProposta']) && !empty($retorno['idPlanilhaProposta'])) {
                $retorno['html'] = self::criarItemHtml($dados, $retorno['idPlanilhaProposta']);
            }
        }

        $dados['dsJustificativa'] = utf8_encode($dados['dsJustificativa']);
        $retorno['dados'] = $dados;

        return $retorno;
    }

    protected function criarItemHtml($dados, $idPlanilhaProposta)
    {
        $tbItens = new tbItensPlanilhaProduto();
        $item = $tbItens->buscarItem(array("idPlanilhaItens = ?" => $dados['idPlanilhaItem']));

        $buscarUnidade = new Proposta_Model_DbTable_TbPlanilhaUnidade();
        $unidade = $buscarUnidade->findBy(array("idUnidade" => $dados['Unidade']));

        $editarProduto = array(
            'module' => 'proposta',
            'controller' => 'manterorcamento',
            'action' => 'formitem',
            'item' => $dados['idPlanilhaItem'],
            'etapa' => $dados['idEtapa'],
            'produto' => $dados['idProduto'],
            'idPlanilhaProposta' => $idPlanilhaProposta,
            'idPreProjeto' => $dados['idProjeto'],
            'idUf' => $dados['UfDespesa'],
            'idMunicipio' => $dados['MunicipioDespesa'],
        );

        $urlEditarProduto = $this->_helper->url->url($editarProduto);

        $html = '<tr id="item-planilha-' . $idPlanilhaProposta . '" class="green lighten-3">'
            . '<td class="left-align">' . utf8_encode($item->Descricao) . '</td>'
            . '<td>' . utf8_encode($unidade['Descricao']) . '</td>'
            . '<td>' . number_format($dados['Quantidade'], 0) . '</td>'
            . '<td>' . number_format($dados['Ocorrencia'], 0) . '</td>'
            . '<td class="right-align">' . number_format($dados['ValorUnitario'], 2, ",", ".") . '</td>'
            . '<td class="right-align">' . number_format(($dados['Quantidade'] * $dados['ValorUnitario']) * $dados['Ocorrencia'], 2, ",", ".") . '</td>'
            . '<td class="action right-align">'
            . '<a data-ajax-modal="' . $urlEditarProduto . '" href="javascript:void(0);" class="btn small waves-effect waves-light tooltipped btn-primary" data-position="top" data-delay="50" data-tooltip="Editar" data-ajax-modal-type="bottom-sheet">'
            . '<i class="material-icons">edit</i>'
            . '</a>'
            . '</td>'
            . '<td class="action left-align">'
            . '<a class="btn small waves-effect waves-light tooltipped btn-danger btn-excluir-item" href="javascript:void(0);" data-tooltip="Excluir" data-ajax="' . $idPlanilhaProposta . '" ><i class="material-icons">delete</i></a>'
            . '</td>'
            . '</tr>';

        return $html;
    }

    /*
    * Quando o sistema abre a opcao de alterar projeto, o proponente
    * nao pode ultrapassar o valor total inicialmente solicitado para incentivo fiscal(Fonte Recurso=109)
    */
    public function verificarSeUltrapassaValorOriginal($idPreProjeto, $dados, $idPlanilhaProposta = null)
    {
        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

        $totalItemSalvo = 0;

        # Busca o valor total solicitado inicialmente
        $tblProjetos = new Projetos();
        $projeto = $tblProjetos->findBy(array('idprojeto = ?' => $idPreProjeto));
        $valorTotalIncentivoOriginal = $projeto['SolicitadoReal'];

        $TPP = new Proposta_Model_DbTable_TbPlanilhaProposta();

        # Faz a soma da planilha da proposta
        $somaPlanilhaPropostaProdutos = $TPP->somarPlanilhaPropostaProdutos($idPreProjeto, 109);
        $somaPlanilhaPropostaProdutos = !empty($somaPlanilhaPropostaProdutos['soma']) ? $somaPlanilhaPropostaProdutos['soma'] : 0;

        # Item atual
        $totalItemAtual = $dados['Quantidade'] * $dados['ValorUnitario'] * $dados['Ocorrencia'];

        # Se tiver editando um item, tem que subtrair o valor anterior
        if (!empty($idPlanilhaProposta)) {
            $item = $tbPlanilhaProposta->findBy(array("idPlanilhaProposta = ?" => $idPlanilhaProposta));

            if ($item) {
                $totalItemSalvo = $item['Quantidade'] * $item['Ocorrencia'] * $item['ValorUnitario'];

                $custosDesteItem = $this->somarTotalCustosVinculados($idPreProjeto, $totalItemSalvo);
                $totalItemSalvo = $totalItemSalvo + $custosDesteItem;
            }
        }

        # eh necessario calcular os custos vinculados com o novo item
        $valorTotaldosProdutosIncentivados = $somaPlanilhaPropostaProdutos + ($totalItemAtual - $totalItemSalvo);
        $custosvinculados = $this->somarTotalCustosVinculados($idPreProjeto, $valorTotaldosProdutosIncentivados);

        $valorTotalProjetoIncentivo = $valorTotaldosProdutosIncentivados + $custosvinculados;

        return ($valorTotalIncentivoOriginal < $valorTotalProjetoIncentivo);
    }

    /**
     * excluiritemAction
     *
     * @access public
     * @return void
     */
    public function excluiritemAction()
    {
        $this->verificarPermissaoAcesso(true, false, false);

        $this->_helper->layout->disableLayout();

        $params = $this->getRequest()->getParams();

        $return['msg'] = "Erro ao excluir o item";
        $return['status'] = false;

        $idPlanilhaProposta = $params['idPlanilhaProposta'];

        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

        $where = 'idPlanilhaProposta = ' . $idPlanilhaProposta;

        $result = $tbPlanilhaProposta->delete($where);

        if ($result) {
            $this->atualizarcustosvinculadosdaplanilha($this->idPreProjeto);

            $return['msg'] = "Exclus&atilde;o realizada com sucesso!";
            $return['status'] = true;
        }

        $this->_helper->json($return);
        die;
    }

    public function restaurarplanilhaAction()
    {
        $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');

        $return['msg'] = "Erro ao restaurar a planilha! ";
        $return['status'] = false;
        $restaurar = false;

        if (!empty($idPreProjeto)) {
            if ($this->isEditarProjeto($idPreProjeto)) {

                # restaura o local de realizacao
                $TA = new Proposta_Model_DbTable_Abrangencia();
                $this->restaurarObjetoSerializadoParaTabela($TA, $idPreProjeto, 'alterarprojeto_abrangencia');

                # restaura o plano de distribuicao e o plano de distribuicao detalhado
                $this->restaurarPlanoDistribuicaoDetalhado($idPreProjeto);

                # restaura o orcamento
                $TPP = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $restaurar = $this->restaurarObjetoSerializadoParaTabela($TPP, $idPreProjeto, 'alterarprojeto_tbplanilhaproposta');
            }

            if ($restaurar) {
                $return['msg'] = "Plano distribui&ccedil;&atilde;o, Local de realiza&ccedil;&atilde;o e Or&ccedil;amento foram restaurados com sucesso!";
                $return['status'] = true;
            }
        }

        $this->_helper->json($return);
        die;
    }

    public function resumorestaurarplanilhaAction()
    {
    }

    /**
     * custosadministrativosAction
     *
     * @access public
     * @return void
     * @deprecated Custos administrativos foi desativado em Proposta em 23/11/2016
     */
    public function custosadministrativosAction()
    {
        $manterOrcamento = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $this->view->Etapas = $manterOrcamento->listarCustosAdministrativos();
        $this->view->EtapaCusto = $manterOrcamento->listarItensCustosAdministrativos($this->idPreProjeto, "A");
        $this->view->dados = $manterOrcamento->listarDadosCadastrarCustos($this->idPreProjeto);
        $buscarEstado = new Agente_Model_DbTable_UF();
        $this->view->Estados = $buscarEstado->listar();
        $this->view->Etapa = $manterOrcamento->listarEtapasCusto();
        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    /**
     * cadastrarcustosAction
     *
     * @access public
     * @return void
     * @deprecated Custos administrativos foi desativado em Proposta em 23/11/2016 [in2017]
     */
    public function cadastrarcustosAction()
    {
        $this->_helper->layout->disableLayout();

        # Forcando o charset conforme o application.ini
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $this->view->charset = $config->resources->db->params->charset;

        if (isset($_GET['idPreProjeto'])) {
            $idPreProjeto = $_GET['idPreProjeto'];

            $manterOrcamento = new Proposta_Model_DbTable_TbPlanilhaProposta();
            $buscaDados = $manterOrcamento->findBy(array('idProjeto' => $idPreProjeto));
//            $buscaDados = $manterOrcamento->buscarDadosCadastrarCustos($idPreProjeto);
            $this->view->dados = $buscaDados;

            $buscaDados = new Proposta_Model_DbTable_TbPlanilhaProposta();

            $this->view->dados = $buscaDados->buscarDadosCadastrarCustos($idPreProjeto);
        }

        if (isset($_GET['cadastro'])) {
            $dados_cadastrados = ManterorcamentoDAO::buscarUltimosDadosCadastrados();
            $this->view->dados_cadastrados = $dados_cadastrados;

            $mun = new Agente_Model_DbTable_Municipios();
            $cidade = $mun->listar($dados_cadastrados[0]['UfDespesa']);
            $this->view->municipios = $cidade;

            $itens = new tbItensPlanilhaProduto();
            $this->view->item = $itens->buscarItens($dados_cadastrados[0]['idEtapa']);
        } else {
            $this->view->dados_cadastrados = array();
        }

        if (isset($_POST['iduf'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $iduf = $_POST['iduf'];

            $tbMun = new Agente_Model_DbTable_Municipios();
            $cidade = $tbMun->listar($iduf);
            $a = 0;
            foreach ($cidade as $DadosCidade) {
                $cidadeArray[$a]['idCidade'] = $DadosCidade->id;
                $cidadeArray[$a]['nomeCidade'] = utf8_encode($DadosCidade->Descricao);
                $a++;
            }
            $this->_helper->json($cidadeArray);
            die;
        }

        if (isset($_POST['idetapa'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $idetapa = $_POST['idetapa'];
            $item = new tbItensPlanilhaProduto();
            $item = $item->buscarItens($idetapa);
            $a = 0;
            foreach ($item as $Dadositem) {
                $itemArray[$a]['idItem'] = $Dadositem->idplanilhaitens;
                $itemArray[$a]['nomeItem'] = utf8_encode($Dadositem->Descricao);
                $a++;
            }
            $this->_helper->json($itemArray);
            die;
        }

        $etapaSelecionada["id"] = $_GET["etapa"];
        $etapaSelecionada["etapaNome"] = $_GET["etapaNome"];
        $this->view->etapaSelecionada = $etapaSelecionada;

        $buscarEstado = new Agente_Model_DbTable_UF();
        $this->view->Estados = $buscarEstado->buscar();

        $buscarEtapa = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $this->view->Etapa = $buscarEtapa->buscarEtapasCusto();

        $buscarRecurso = new Proposta_Model_DbTable_Verificacao();
        $this->view->Recurso = $buscarRecurso->buscarFonteRecurso();

        $buscarUnidade = new Proposta_Model_DbTable_TbPlanilhaUnidade();
        $this->view->Unidade = $buscarUnidade->buscarUnidade();

        $this->view->idPreProjeto = $this->idPreProjeto;
    }


    /**
     * editarprodutosAction
     *
     * @access public
     * @return void
     * @deprecated Este metodo nao eh mais utilizado [in2017]
     */
    public function editarprodutosAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
        $tblanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $mun = new Agente_Model_DbTable_Municipios();

        if (isset($_POST['produto'])) {
            $idProposta = $_POST['proposta'];
            $idProduto = $_POST['produto'];
            $idUf = $_POST['uf'];
            $municipio = $_POST['municipio'];
            $idEtapa = $_POST['etapa'];
            $idItem = $_POST['item'];
            $unidade = $_POST['unidade'];
            $qtd = $_POST['qtd'];
            $ocorrencia = $_POST['ocorrencia'];
            $valor = str_replace(",", ".", str_replace(".", "", $_POST['vlunitario']));
            $qtdDias = $_POST['qtdDias'];
            $fonte = $_POST['fonterecurso'];

            $dados = array(
                'idetapa' => $_POST['etapa'],
                'idplanilhaitem' => $_POST['item'],
                'unidade' => $_POST['unidade'],
                'quantidade' => $_POST['qtd'],
                'ocorrencia' => $_POST['ocorrencia'],
                'valorunitario' => str_replace(",", ".", str_replace(".", "", $_POST['vlunitario'])),
                'qtdedias' => $_POST['qtdDias'],
                'fonterecurso' => $_POST['fonterecurso'],
                'ufdespesa' => $_POST['uf'],
                'municipiodespesa' => $_POST['municipio'],
                'dsjustificativa' => $_POST['justificativa']
            );

            $where = "idPlanilhaProposta = " . $_POST['proposta'];

            $buscarProdutos = $tblanilhaProposta->buscarDadosEditarProdutos(null, $idEtapa, $idProduto, $idItem, null, $idUf, $municipio);


            $tblanilhaProposta->editarPlanilhaProdutos($dados, $where);

            $this->atualizarcustosvinculadosdaplanilha($_POST['idPreProjeto']);

            $this->_helper->layout->disableLayout();
            echo "Altera&ccedil;&atilde;o realizada com sucesso!";
            die;
        }

        if (!empty($_GET)) {
            $idProposta = $_GET['idPreProjeto'];
            $idEtapa = $_GET['etapa'];
            $idProduto = $_GET['produto'];
            $idItem = $_GET['item'];
            $idPlanilhaProposta = $_GET['idPlanilhaProposta'];

            //$buscaDados = ManterorcamentoDAO::buscarDadosEditarProdutos($idProposta, $idEtapa, $idProduto, $idItem, $idPlanilhaProposta);
            $buscaDados = $tblanilhaProposta->buscarDadosEditarProdutos($idProposta, $idEtapa, $idProduto, $idItem, $idPlanilhaProposta);
            $this->view->Dados = $buscaDados;
        }

        if (isset($_POST['iduf'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $iduf = $_POST['iduf'];

            $cidade = $mun->buscar($iduf);

            $a = 0;
            foreach ($cidade as $DadosCidade) {
                $cidadeArray[$a]['idCidade'] = $DadosCidade->id;
                $cidadeArray[$a]['nomeCidade'] = utf8_encode($DadosCidade->descricao);
                $a++;
            }
            $this->_helper->json($cidadeArray);
            die;
        }

        if (isset($_POST['idetapa'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $idetapa = $_POST['idetapa'];

            $tbItensPlanilhaProduto = new tbItensPlanilhaProduto();
            $item = $tbItensPlanilhaProduto->buscarItens($idetapa);

            $a = 0;
            foreach ($item as $Dadositem) {
                $itemArray[$a]['idItem'] = $Dadositem->idplanilhaitens;
                $itemArray[$a]['nomeItem'] = $Dadositem->descricao;
                $a++;
            }
            $this->_helper->json($itemArray);
            die;
        }
        $uf = new Agente_Model_DbTable_UF();
        $buscarEstado = $uf->buscar();
        $this->view->Estados = $buscarEstado;


        $cidade = $mun->listar($buscaDados[0]->IdUf);
        $this->view->Cidades = $cidade;

        $buscarEtapa = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $this->view->itensEtapaCusto = $buscarEtapa->buscarEtapasCusto();

        $tbPlanilhaEtapa = new Proposta_Model_DbTable_TbPlanilhaEtapa();

        $this->view->Etapa = $tbPlanilhaEtapa->buscarEtapasCadastrarProdutos();

        $buscarRecurso = new Proposta_Model_DbTable_Verificacao();
        $this->view->Recurso = $buscarRecurso->buscarFonteRecurso();

        $buscarUnidade = new Proposta_Model_DbTable_PlanilhaUnidade();
        $this->view->Unidade = $buscarUnidade->buscarUnidade();

        $buscarItem = new tbItensPlanilhaProduto();
        $this->view->Item = $buscarItem->buscarItens($idEtapa);

        $buscarProduto = new Proposta_Model_DbTable_PreProjeto();
        $this->view->Produtos = $buscarProduto->buscarProdutos($this->idPreProjeto);

        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    /**
     * editarcustosAction
     *
     * @access public
     * @return void
     * @deprecated Custos administrativos foi desativado na Proposta em 23/11/2016 [in2017]
     */
    public function editarcustosAction()
    {
        $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout

        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

        if (!empty($_GET) && count($_GET) > 0) {
            $buscaDados = $tbPlanilhaProposta->buscarDadosCustos($_GET);
            $this->view->Dados = $buscaDados;
        }
        if (isset($_POST['iduf'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $iduf = $_POST['iduf'];

            $mun = new Agente_Model_DbTable_Municipios();
            $cidade = $mun->listar($iduf);

            $a = 0;
            foreach ($cidade as $DadosCidade) {
                $cidadeArray[$a]['idCidade'] = $DadosCidade->id;
                $cidadeArray[$a]['nomeCidade'] = $DadosCidade->descricao;
                $a++;
            }
            $this->_helper->json($cidadeArray);
            die;
        }

        if (isset($_POST['idetapa'])) {
            $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
            $idetapa = $_POST['idetapa'];

            $itensPlanilhaProduto = new tbItensPlanilhaProduto();
            $item = $itensPlanilhaProduto->buscarItens($idetapa);

            $a = 0;
            foreach ($item as $Dadositem) {
                $itemArray[$a]['idItem'] = $Dadositem->idPlanilhaItens;
                $itemArray[$a]['nomeItem'] = $Dadositem->Descricao;
                $a++;
            }
            $this->_helper->json($itemArray);
            die;
        }
        $uf = new Agente_Model_DbTable_UF();
        $buscarEstado = $uf->buscar();
        $this->view->Estados = $buscarEstado;

        $mun = new Agente_Model_DbTable_Municipios();
        $cidade = $mun->listar($buscaDados[0]->iduf);

        $this->view->Cidades = $cidade;

        $itensEtapaCusto = new Proposta_Model_DbTable_TbPlanilhaEtapa();

        $this->view->itensEtapaCusto = $itensEtapaCusto->buscarEtapasCusto();

        $this->view->Etapa = $itensEtapaCusto->buscarEtapasCadastrarProdutos();

        $buscarRecurso = new Proposta_Model_DbTable_Verificacao();
        $this->view->Recurso = $buscarRecurso->buscarFonteRecurso();

        $buscarUnidade = new Proposta_Model_DbTable_PlanilhaUnidade();
        $this->view->Unidade = $buscarUnidade->buscarUnidade();

        $tbPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $this->view->Item = $tbPreProjeto->listarItensProdutos($this->idPreProjeto);

        $tbItens = new tbItensPlanilhaProduto();
        $this->view->ListaItens = $tbItens->buscarItens($_GET['etapa']);

        $buscaDados = $tbPlanilhaProposta->buscarDadosCadastrarCustos($_GET['idPreProjeto']);
        $this->view->dados = $buscaDados;

        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    /**
     * salvarprodutosAction
     *
     * @access public
     * @return void
     * @deprecated Custos administrativos foi desativado na Proposta em 23/11/2016 [in2017]
     */
    public function salvarprodutosAction()
    {
        if (isset($_POST)) {
            $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

            $idProposta = $_POST['idPreProjeto'];
            $idProduto = $_POST['produto'];
            $idUf = $_POST['uf'];
            $idMunicipio = $_POST['municipio'];
            $idEtapa = $_POST['etapa'];
            $idItem = $_POST['item'];
            $fonte = $_POST['fonterecurso'];
            $unidade = $_POST['unidade'];
            $quantidade = $_POST['quantidade'];
            $ocorrencia = $_POST['ocorrencia'];
            $vlunitario = str_replace(",", ".", str_replace(".", "", $_POST['vlunitario']));
            $qtdDias = $_POST['qtdDias'];
            $justificativa = utf8_decode(substr(trim(strip_tags($_POST['justificativa'])), 0, 500));
            $buscarProdutos = $tbPlanilhaProposta->buscarDadosEditarProdutos($idProposta, $idEtapa, $idProduto, $idItem, null, $idUf, $idMunicipio);

            if ($buscarProdutos) {
                $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
                echo "Cadastro duplicado de Produto na mesma etapa envolvendo o mesmo Item! Deseja cadastrar um novo item?";
                die;
            } else {
                $this->view->SalvarNovo = $tbPlanilhaProposta->salvarNovoProduto(
                    $idProposta,
                    $idProduto,
                    $idEtapa,
                    $idItem,
                    $unidade,
                    $quantidade,
                    $ocorrencia,
                    $vlunitario,
                    $qtdDias,
                    $fonte,
                    $idUf,
                    $idMunicipio,
                    $justificativa,
                    $this->idUsuario
                );

                if ($this->view->SalvarNovo) {
                    $this->atualizarcustosvinculadosdaplanilha($idProposta);
                }

                $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
                echo "Item cadastrado com sucesso. Deseja cadastrar um novo item?";
                die;
            }
        }

        $this->view->idPreProjeto = $this->idPreProjeto;
    }


    /**
     * salvarcustosAction
     *
     * @access public
     * @return void
     * @deprecated Custos administrativos foi desativado na Proposta em 23/11/2016 [in2017]
     */
    public function salvarcustosAction()
    {
        if (isset($_POST)) {
            $idProposta = $_POST['idPreProjeto'];
            $idUf = $_POST['uf'];
            $idMunicipio = $_POST['municipio'];
            $idEtapa = $_POST['etapa'];
            $idItem = $_POST['item'];
            $fonte = $_POST['fonterecurso'];
            $unidade = $_POST['unidade'];
            $quantidade = $_POST['qtd'];
            $ocorrencia = $_POST['ocorrencia'];
            $vlunitario = str_replace(",", ".", str_replace(".", "", $_POST['vlunitario']));
            $qtdDias = $_POST['qtdDias'];
            $dsJustificativa = substr(trim(strip_tags($_POST['justificativa'])), 0, 500);
            $tipoCusto = 'A';

            $db = Zend_Db_Table::getDefaultAdapter();
            $dados = array('idprojeto' => $idProposta,
                'idetapa' => $idEtapa,
                'idplanilhaitem' => $idItem,
                'descricao' => '',
                'unidade' => $unidade,
                'quantidade' => $quantidade,
                'ocorrencia' => $ocorrencia,
                'valorunitario' => $vlunitario,
                'qtdedias' => $qtdDias,
                'tipodespesa' => '0',
                'tipopessoa' => '0',
                'contrapartida' => '0',
                'fonterecurso' => $fonte,
                'ufdespesa' => $idUf,
                'municipiodespesa' => $idMunicipio,
                'idusuario' => 462,
                'dsjustificativa' => $dsJustificativa
            );

            if ($_POST['acao'] == 'alterar') {
                $buscarCustos = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $where = 'idPlanilhaProposta = ' . $_POST['idPlanilhaProposta'];

                $buscarCustos->update($dados, $where);
                $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
                echo "Altera&ccedil;&atilde;o realizada com sucesso!";
                die;
            } else {
                $TPP = new Proposta_Model_DbTable_TbPlanilhaProposta();
                $buscarCustos = $TPP->buscarCustos($idProposta, $tipoCusto, $idEtapa, $idItem, $idUf, $idMunicipio);
                if ($buscarCustos) {
                    $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
                    echo "Cadastro duplicado de Custo na mesma etapa envolvendo o mesmo Item! Deseja cadastrar um novo item?";
                    die;
                } else {
                    $TPP->insert($dados);
                    $this->_helper->layout->disableLayout(); // desabilita o Zend_Layout
                    echo "Item cadastrado com sucesso. Deseja cadastrar um novo item?";
                    die;
                }
            }
        }

        $this->view->idPreProjeto = $this->idPreProjeto;
    }


    /**
     * salvarmesmoprodutoAction
     *
     * @access public
     * @return void
     * @deprecated Este metodo nao eh mais utilizado com a nova IN [in2017]
     */
    public function salvarmesmoprodutoAction()
    {
        if (isset($_POST)) {
            $dados = array(
                'idProjeto' => $_POST['proposta'],
                'idProduto' => $_POST['produto'],
                'idEtapa' => $_POST['etapa'],
                'idPlanilhaItem' => $_POST['etapa'],
                'Unidade' => $_POST['unidade'],
                'Quantidade' => $_POST['quantidade'],
                'Ocorrencia' => $_POST['ocorrencia'],
                'valorUnitario' => $_POST['vlunitario'],
                'QtdeDias' => $_POST['qtdDias'],
                'FonteRecurso' => $_POST['fonterecurso'],
                'ufDespesa' => $_POST['uf'],
                'MunicipioDespesa' => $_POST['municipio'],
                idUsuario => 462
            );

            $where = "(pp.idProjeto = " . $_POST['proposta'] . " and pp.idProduto = " . $_POST['produto'] . " and pp.idUsuario = 462)";
            print_r($dados);
            die;
            $salvarProdutos = ManterorcamentoDAO::updateProdutos($dados, $where);
            $this->view->Salvar = $salvarProdutos;
        }
        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    /**
     * excluiritensprodutosAction
     *
     * @access public
     * @return void,
     * @deprecated Este metodo nao eh mais utilizado com a nova IN [in2017]
     */
    public function excluiritensprodutosAction()
    {
        $idPlanilhaProposta = $_GET['idPlanilhaProposta'];

        $retorno = $_GET['retorno'];

        $tbPlaninhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

        $where = 'idPlanilhaProposta = ' . $idPlanilhaProposta;

        $resposta = $tbPlaninhaProposta->delete($where);

        if ($resposta) {
            $this->atualizarcustosvinculadosdaplanilha($idPlanilhaProposta);
            parent::message("Exclus&atilde;o realizada com sucesso!", "/proposta/manterorcamento/" . $retorno . "?idPreProjeto=" . $this->idPreProjeto, "CONFIRM");
        } else {
            parent::message("Erro ao excluir os dados", "/proposta/manterorcamento/" . $retorno . "?idPreProjeto=" . $this->idPreProjeto, "ERROR");
        }
        $this->view->idPreProjeto = $this->idPreProjeto;
    }

    public function buscarValorMedianaAjaxAction()
    {
        $params = $idPreProjeto = $this->getRequest()->getParams();

        $tbPlaninhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

        $valorMediana = $tbPlaninhaProposta->calcularMedianaItemOrcamento($params['idproduto'], $params['idunidade'], $params['idplanilhaitem'], $params['idufdespesa'], $params['idmunicipiodespesa']);
        $valorMediana = isset($valorMediana['Mediana']) ? $valorMediana['Mediana'] : 0;

        $return['msg'] = '';
        $return['status'] = 1;
        $return['valorMediana'] = $valorMediana;

        $stringLocalizacao = isset($params['stringLocalizacao']) ? utf8_decode($params['stringLocalizacao']) : 'este item';

        $params['vlunitario'] = str_replace(",", ".", str_replace(".", "", $params['vlunitario']));

        if (!empty($valorMediana) && $valorMediana < $params['vlunitario']) {
            $return['msg'] = utf8_encode('O valor unit&aacute;rio para ' . $stringLocalizacao . ', ultrapassa o valor(R$ ' . number_format($valorMediana, 2, ",", ".") . ') aprovado pelo MinC. Justifique o motivo!');
            $return['status'] = 0;
        }

        $this->_helper->json($return);
        die;
    }
}
