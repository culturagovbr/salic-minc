<?php


class Proposta_ManterorcamentoController extends Proposta_GenericController
{

    public function init()
    {
        parent::init();

        $this->verificarPermissaoAcesso(true, false, false);
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $idusuario = $auth->getIdentity()->usu_codigo;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $codOrgao = $GrupoAtivo->codOrgao;

        $this->view->codOrgao = $codOrgao;
        $this->view->idUsuarioLogado = $idusuario;
    }

    public function produtoscadastradosAction()
    {
        $this->view->idPreProjeto = $this->idPreProjeto;

        $this->view->charset = Zend_Registry::get('config')->db->params->charset;
    }

    public function listarprodutosAction()
    {
        $this->_helper->layout->disableLayout();

        $tbPreprojeto = new Proposta_Model_DbTable_PreProjeto();
        $this->view->Produtos = $tbPreprojeto->listarProdutos($this->idPreProjeto);

        $manterOrcamento = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $listaEtapa = $manterOrcamento->buscarEtapas('P');
        $this->view->EtapasProduto = $listaEtapa;

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

    }

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

    public function planilhaorcamentariageralAction()
    {
        $this->view->tipoPlanilha = 0; // 0=Planilha Or?ament?ria da Proposta
        $this->view->idPreProjeto = $this->getRequest()->getParam('idPreProjeto');
    }

    public function custosvinculadosAction()
    {
        $this->view->acao = $this->_urlPadrao . "/proposta/manterorcamento/salvarpercentuaiscustosvinculados";

        $arrBusca = array(
            'idProjeto' => $this->idPreProjeto,
            'stAbrangencia' => 1
        );

        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $this->view->localRealizacao = $tblAbrangencia->buscar($arrBusca);

        $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
        $this->view->itensCustosVinculados = $tbCustosVinculadosMapper->obterCustosVinculados($this->idPreProjeto);
    }

    public function salvarpercentuaiscustosvinculadosAction()
    {

        $params = $this->getRequest()->getParams();

        $custosVinculados = $params['itensCustosVinculados'];

        $mapper = new Proposta_Model_TbCustosVinculadosMapper();

        try {

            if(!$this->isEditavel($this->idPreProjeto)) {
                throw new Exception("Permissão negada!");
            }

            foreach ($custosVinculados as $key => $item) {
                $dados = array(
                    'idCustosVinculados' => $item['idCustosVinculados'],
                    'idProjeto' => $params['idPreProjeto'],
                    'idPlanilhaItem' => $key,
                    'dtCadastro' => new Zend_Db_Expr('getdate()'),
                    'pcCalculo' => $item['percentual'],
                    'idUsuario' => $this->idUsuario
                );

                $mapper->save(new Proposta_Model_TbCustosVinculados($dados));
            }

            $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
            $tbCustosVinculadosMapper->salvarCustosVinculadosDaTbPlanilhaProposta($this->idPreProjeto);

            parent::message('Cadastro realizado com sucesso!', "/proposta/manterorcamento/custosvinculados?idPreProjeto=" . $this->idPreProjeto, "CONFIRM");
        } catch (Zend_Exception $ex) {
            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!" . $ex->getMessage(), "/proposta/manterorcamento/custosvinculados?idPreProjeto=" . $this->idPreProjeto, "ERROR");
        }
    }

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

            $this->view->EtapasProduto = $etapasPlanilha;
        }
    }

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
        $this->view->Item = $itensPlanilhaProduto->buscarItens(array(), $etapa, $idProduto);

        $buscarProduto = new Proposta_Model_DbTable_PreProjeto();
        $this->view->Produtos = $buscarProduto->buscarProdutos($this->idPreProjeto);
    }

    public function salvaritemAction()
    {
        $this->_helper->layout->disableLayout();
        $params = $this->getRequest()->getParams();
        $dados = [];

        try {

            $justificativa = utf8_decode(substr(trim(strip_tags($params['justificativa'])), 0, 1000));

            $dados = array(
                'idProjeto' => $params['idPreProjeto'],
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

            if(!$this->isEditavel($this->idPreProjeto)) {
                throw new Exception("Permiss&atilde;o negada!");
            }

            $idPlanilhaProposta = $this->getRequest()->getParam('idPlanilhaProposta', null);

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

            $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
            $tbCustosVinculadosMapper->salvarCustosVinculadosDaTbPlanilhaProposta($params['idPreProjeto']);

            $this->_helper->json($retorno);

        } catch(Exception $e) {

             $this->_helper->json(
                 [[
                     'status' => false,
                     'msg' => $e->getMessage(),
                     'dados' => $dados
                 ]]
             );
        }
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

    public function excluirItemAction()
    {
        try {

            $this->verificarPermissaoAcesso(true, false, false);

            if(!$this->isEditavel($this->idPreProjeto)) {
                throw new Exception("Permissão negada!");
            }

            $this->_helper->layout->disableLayout();

            $params = $this->getRequest()->getParams();

            $dados['msg'] = "Erro ao excluir o item";
            $dados['status'] = false;

            $idPlanilhaProposta = $params['idPlanilhaProposta'];

            $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

            $where = 'idPlanilhaProposta = ' . $idPlanilhaProposta;

            $result = $tbPlanilhaProposta->delete($where);

            if ($result) {
                $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
                $tbCustosVinculadosMapper->salvarCustosVinculadosDaTbPlanilhaProposta($this->idPreProjeto);

                $dados['msg'] = "Exclus&atilde;o realizada com sucesso!";
                $dados['status'] = true;
            }

            $this->_helper->json($dados);
        } catch (Exception $e) {
            $dados['msg'] = $e->getMessage();
            $dados['status'] = false;
            $this->_helper->json($dados);
        }

    }

    public function restaurarplanilhaAction()
    {

        try {

            if(!$this->isEditavel($this->idPreProjeto)) {
                throw new Exception("Permiss&atilde;o negada!");
            }
            $idPreProjeto = $this->getRequest()->getParam('idPreProjeto');

            $return['msg'] = "Erro ao restaurar a planilha! ";
            $return['status'] = false;
            $restaurar = false;

            if (!empty($idPreProjeto)) {
                if ($this->isEditarProjeto($idPreProjeto)) {

                    # restaura o local de realizacao
                    $tbPreProjetoMeta = new Proposta_Model_TbPreProjetoMetaMapper();
                    $TA = new Proposta_Model_DbTable_Abrangencia();
                    $tbPreProjetoMeta->restaurarObjetoSerializadoParaTabela($TA, $idPreProjeto, 'alterarprojeto_abrangencia');

                    # restaura o plano de distribuicao e o plano de distribuicao detalhado
                    $tbPreProjetoMeta->restaurarPlanoDistribuicaoDetalhado($idPreProjeto);

                    # restaura o orcamento
                    $TPP = new Proposta_Model_DbTable_TbPlanilhaProposta();
                    $restaurar = $tbPreProjetoMeta->restaurarObjetoSerializadoParaTabela($TPP, $idPreProjeto, 'alterarprojeto_tbplanilhaproposta');
                }

                if ($restaurar) {
                    $return['msg'] = "Plano distribui&ccedil;&atilde;o, Local de realiza&ccedil;&atilde;o e Or&ccedil;amento foram restaurados com sucesso!";
                    $return['status'] = true;
                }
            }

            $this->_helper->json($return);
        } catch (Exception $e) {
            $this->_helper->json([
                'status' => false,
                'msg' => $e->getMessage()
            ]);
        }


    }

    public function resumorestaurarplanilhaAction()
    {
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
