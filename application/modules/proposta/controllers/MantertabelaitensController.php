<?php

/**
 * MantertabelaitensController
 * @since 10/12/2010
 * @link http://www.cultura.gov.br
 */
class Proposta_MantertabelaitensController extends Proposta_GenericController
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

        //recupera ID do pre projeto (proposta)
        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto = $this->idPreProjeto;
        }
    }

    /**
     * Redireciona para o fluxo inicial
     * @access public
     * @param void
     * @return void
     */
    public function indexAction()
    {
        $tbproduto = new Produto();
        $this->view->produto = $tbproduto->listarProdutos();

        $tbetapa = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $this->view->etapa = $tbetapa->listarEtapas();

        $tbitem = new Proposta_Model_DbTable_TbPlanilhaItens();
        $this->view->item = $tbitem->listarItens();
    }

    /**
     * produtosetapasitensAction
     *
     * @access public
     * @return void
     */
    public function produtosetapasitensAction()
    {
        $tbproduto = MantertabelaitensDAO::buscaproduto();
        $this->view->produto = $tbproduto;

        $tbetapa = MantertabelaitensDAO::buscaetapa();
        $this->view->etapa = $tbetapa;

        $tbitem = MantertabelaitensDAO::buscaitem();
        $this->view->item = $tbitem;
    }

    /**
     * solicitaritensAction
     *
     * @access public
     * @return void
     * @author <wouerner@gmail.com>
     */
    public function solicitaritensAction()
    {
        $tbproduto = new Produto();
        $this->view->produto = $tbproduto->listarProdutos();

        $tbetapa = new Proposta_Model_DbTable_TbPlanilhaEtapa();
        $this->view->etapa = $tbetapa->listarEtapas();

        $tbitem = new Proposta_Model_DbTable_TbPlanilhaItens();
        $this->view->item = $tbitem->listarItens();
    }

    /**
     * salvarAssociacaoItemAction
     *
     * @access public
     * @return void
     */
    public function salvarsolicitacaoitemAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();

            $justificativa = substr(trim($params['justificativa']), 0, 1000);
            $descricaoItem = trim($params['Descricao']);

            $tipoSolicitacao = $params['solicitacao'];
            $idProduto = $params['produto'];
            $idEtapa = $params['etapa'];
            $hoje = MinC_Db_Expr::date();

            try {
                if (empty($justificativa)) {
                    throw new Exception("Informe a justificativa!");
                }


                if (strlen($justificativa) > 1000) {
                    throw new Exception("A justificativa n&atilde;o pode conter mais de 1000 caracteres!");
                }

                $tbSolicitarItem = new Proposta_Model_DbTable_TbSolicitarItem();

                $dados = array(
                    'idplanilhaitens' => 0,
                    'nomedoitem' => $descricaoItem,
                    'descricao' => $justificativa,
                    'idproduto' => $idProduto,
                    'idetapa' => $idEtapa,
                    'idagente' => $this->idUsuario,
                    'dtsolicitacao' => new Zend_Db_Expr($hoje),
                    'stestado' => '0'
                );

                if ($tipoSolicitacao == "novoitem") {
                    if (empty($params['Descricao'])) {
                        throw new Exception("Descri&ccedil;&atilde;o do item &eacute; obrigat&oacute;ria!");
                    }

                    if (strlen($params['Descricao']) > 100) {
                        throw new Exception("O nome do Item n&atilde;o pode conter mais de 100 caracteres!");
                    }

                    $tbPlanilhaItens = new Proposta_Model_DbTable_TbPlanilhaItens();
                    $descricao = $tbPlanilhaItens->buscarDescricao($descricaoItem);

                    if (!empty($descricao)) {
                        throw new Exception("Este item j&aacute; existe na base de dados, solicite associa&ccedil;&atilde;o!, a&ccedil;&atilde;o cancelada!");
                    }

                    $dados['idplanilhaitens'] = 0;
                    $dados['nomedoitem'] = $descricaoItem;

                    $resultado = $tbSolicitarItem->insert($dados);

                    if ($resultado) {
                        parent::message(
                            "A solicita&ccedil;&atilde;o foi encaminhada ao MinC. Aguarde a resposta!",
                            "/proposta/mantertabelaitens/minhas-solicitacoes/idPreProjeto/" . $this->idPreProjeto . "?tipoFiltro=solicitado",
                            "CONFIRM"
                        );
                    }
                }

                if ($tipoSolicitacao == "associacao") {
                    if (empty($params['idPlanilhaItens'])) {
                        throw new Exception("Item não informado!");
                    }

                    $itemPesquisa = [];
                    $itemPesquisa['idProduto'] = $params['produto'];
                    $itemPesquisa['idPlanilhaEtapa'] = $params['etapa'];
                    $itemPesquisa['idPlanilhaItens'] = $params['idPlanilhaItens'];

                    $tbItensPlanilhaProduto = new tbItensPlanilhaProduto();
                    $itemProduto = $tbItensPlanilhaProduto->findBy($itemPesquisa);

                    if (count($itemProduto) > 0) {
                        throw new Exception("Item j&aacute; associado ao produto e  etapa informados. A&ccedil;&atilde;o cancelada!");
                    }

                    $itemPesquisa['idagente'] = $this->idUsuario;

                    $itemJaSolicitado = $tbSolicitarItem->findBy(
                        [
                            'idplanilhaitens' => $params['idPlanilhaItens'],
                            'idagente' => $this->idUsuario,
                            'idProduto' => $params['produto'],
                            'idPlanilhaItens' => $params['idPlanilhaItens']
                        ]
                    );

                    if (count($itemJaSolicitado) > 0) {
                        throw new Exception("Voc&ecirc; j&aacute; solicitou esta associa&ccedil;&atilde;o. A&ccedil;&atilde;o cancelada!");
                    }


                    $tbPlanilhaItens = new Proposta_Model_DbTable_TbPlanilhaItens();
                    $item = $tbPlanilhaItens->findBy(['idplanilhaitens' => $params['idPlanilhaItens']]);

                    $dados['nomedoitem'] = $item['Descricao'];
                    $dados['idplanilhaitens'] = $item['idPlanilhaItens'];

                    $resultado = $tbSolicitarItem->insert($dados);

                    if ($resultado) {
                        parent::message(
                            "A solicita&ccedil;&atilde;o foi encaminhada ao MinC. Aguarde a resposta!",
                            "/proposta/mantertabelaitens/minhas-solicitacoes/idPreProjeto/" . $this->idPreProjeto . "?tipoFiltro=solicitado",
                            "CONFIRM"
                        );
                    }
                }
            } catch (Exception $e) {
                parent::message(
                    $e->getMessage(),
                    "proposta/mantertabelaitens/solicitaritens/idPreProjeto/" . $this->idPreProjeto,
                    "ERROR"
                );

                return;
            }
        } else {
            parent::message(
                "Nada enviado!",
                "proposta/mantertabelaitens/solicitaritens/idPreProjeto/" . $this->idPreProjeto,
                "ERROR"
            );
        }
    }

    /**
     * minhasSolicitacoesAction
     *
     * @access public
     * @return void
     */
    public function minhasSolicitacoesAction()
    {
        $mantertbitens = new Proposta_Model_DbTable_TbSolicitarItem();
        $this->intTamPag = 10;

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
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        } else {
            $campo = null;
            $order = array(2, 4, 6);
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
//        $this->view->idPreProjeto = $this->idPreProjeto;

        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $where = array();
        $where['sol.idAgente = ?'] = $auth->getIdentity()->IdUsuario;
        $where['sol.stEstado = ?'] = 1; // Atendido

        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                case '':
                    $where['sol.stEstado = ?'] = 1; // Atendido
                    $this->view->nmPagina = 'Atendido';
                    break;
                case 'solicitado':
                    $where['sol.stEstado = ?'] = 0; // Solicitado
                    $this->view->nmPagina = 'Solicitado';
                    break;
                case 'negado':
                    $where['sol.stEstado = ?'] = 2; // Negado
                    $this->view->nmPagina = 'Negado';
                    break;
            }
        } else {
            $where['sol.stEstado = ?'] = 1; // Atendido
            $this->view->nmPagina = 'Atendido';
        }

        $tbSolicitarItem = new Proposta_Model_DbTable_TbSolicitarItem();
        $total = $tbSolicitarItem->buscarItens($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbSolicitarItem->buscarItens($where, $order, $tamanho, $inicio);
        $paginacao = array(
            "pag" => $pag,
            "qtde" => $this->intTamPag,
            "campo" => $campo,
            "ordem" => $ordem,
            "ordenacao" => $ordenacao,
            "novaOrdem" => $novaOrdem,
            "total" => $total,
            "inicio" => ($inicio + 1),
            "fim" => $fim,
            "totalPag" => $totalPag,
            "Itenspag" => $this->intTamPag,
            "tamanho" => $tamanho
        );

        $tbTitulacaoConselheiro = new tbTitulacaoConselheiro();
        $this->view->conselheiros = $tbTitulacaoConselheiro->buscarConselheirosTitulares();

        $this->view->paginacao = $paginacao;
        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->view->intTamPag = $this->intTamPag;

        $tbsolicitacao = $mantertbitens->solicitacoes($this->idUsuario);
        $this->view->solicitacao = $tbsolicitacao;
    }

    /**
     * imprimirMinhasSolicitacoesAction
     *
     * @access public
     * @return void
     */
    public function imprimirMinhasSolicitacoesAction()
    {
        $this->intTamPag = 10;

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
            $order = array($campo . " " . $ordem);
            $ordenacao = "&campo=" . $campo . "&ordem=" . $ordem;
        } else {
            $campo = null;
            $order = array(2, 4, 6);
            $ordenacao = null;
        }

        $pag = 1;
        $get = Zend_Registry::get('get');
//        $this->view->idPreProjeto = $get->idPreProjeto;

        if (isset($get->pag)) {
            $pag = $get->pag;
        }
        $inicio = ($pag > 1) ? ($pag - 1) * $this->intTamPag : 0;

        /* ================== PAGINACAO ======================*/
        $auth = Zend_Auth::getInstance(); // pega a autenticacao
        $where = array();
        $where['sol.idAgente = ?'] = $auth->getIdentity()->IdUsuario;
        $where['sol.stEstado = ?'] = 1; // Atendido

        if (isset($_POST['tipoFiltro']) || isset($_GET['tipoFiltro'])) {
            $filtro = isset($_POST['tipoFiltro']) ? $_POST['tipoFiltro'] : $_GET['tipoFiltro'];
            $this->view->filtro = $filtro;
            switch ($filtro) {
                case '':
                    $where['sol.stEstado = ?'] = 1; // Atendido
                    $this->view->nmPagina = 'Atendido';
                    break;
                case 'solicitado':
                    $where['sol.stEstado = ?'] = 0; // Solicitado
                    $this->view->nmPagina = 'Solicitado';
                    break;
                case 'negado':
                    $where['sol.stEstado = ?'] = 2; // Negado
                    $this->view->nmPagina = 'Negado';
                    break;
            }
        } else {
            $where['sol.stEstado = ?'] = 1; // Atendido
            $this->view->nmPagina = 'Atendido';
        }

        $tbSolicitarItem = new Proposta_Model_DbTable_TbSolicitarItem();
        $total = $tbSolicitarItem->buscarItens($where, $order, null, null, true);
        $fim = $inicio + $this->intTamPag;

        $totalPag = (int)(($total % $this->intTamPag == 0) ? ($total / $this->intTamPag) : (($total / $this->intTamPag) + 1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;

        $busca = $tbSolicitarItem->buscarItens($where, $order, $tamanho, $inicio);

        $this->view->qtdRegistros = $total;
        $this->view->dados = $busca;
        $this->_helper->layout->disableLayout(); // Desabilita o Zend Layout
    }


    /**
     * exibirdadosAction
     *
     * @access public
     * @return void
     */
    public function exibirdadosAction()
    {
        if ($this->getRequest()->isPost()) {

            // recebe os dados via post
            $post = Zend_Registry::get('post');
            $tipoPesquisa = $post->tipoPesquisa;
            $itemBuscado = $post->itemBuscado;

            $this->view->produto = $post->produto;
            $this->view->etapa = $post->etapa;
            $this->view->itemBuscado = $itemBuscado;
            $this->view->tipopesquisa = $post->TipoPesquisa;
            $this->view->item = $post->NomeDoItem;

            try {
                if (strlen($itemBuscado) < 3) {
                    throw new Exception("Informe uma palavra de pelo menos 3 caracteres na pesquisa!");
                }

                $where = null;
                switch ($tipoPesquisa) {
                    case 1:
                        $where["i.descricao LIKE (?)"] = "%" . $itemBuscado . "%";
                        break;
                    case 2:
                        $where["i.descricao LIKE (?)"] = $itemBuscado . "%";
                        break;
                    case 3:
                        $where["i.descricao = ?"] = $itemBuscado;
                        break;
                    case 4:
                        $where["i.descricao <> ?"] = "%" . $itemBuscado;
                        break;
                }

                $tbpretitem = new tbItensPlanilhaProduto();
                $etapasComTermoPesquisado = $tbpretitem->listarProdutoEtapaItem(null, null, $post->etapa, $post->produto, $where);

                $this->view->pretitem = $etapasComTermoPesquisado;


                if ($tbpretitem) {
                } else {
                    throw new Exception("Dados n&atilde;o localizados");
                }
            } catch (Exception $e) {
                parent::message($e->getMessage(), "proposta/mantertabelaitens/index/idPreProjeto/" . $this->idPreProjeto, "ERROR");
            }
        }
    }

    /**
     * imprimirAction
     *
     * @access public
     * @return void
     */
    public function imprimirAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $post = Zend_Registry::get('post');

        if (empty($post->tipoPesquisa) && empty($post->item) && empty($post->etapa) && empty($post->produto)) {
            $this->_redirect("mantertabelaitens/exibirdados/idPreProjeto/" . $this->idPreProjeto);
        }
        $tipoPesquisa = $post->tipoPesquisa;
        $item = $post->item;
        $etapa = $post->etapa;
        $produto = $post->produto;

        $where = null;
        if ($tipoPesquisa == 1) {
            $where["i.descricao LIKE (?)"] = "%" . $item . "%";
        } elseif ($tipoPesquisa == 2) {
            $where["i.descricao LIKE (?)"] = "%" . $item;
        } elseif ($tipoPesquisa == 3) {
            $where["i.descricao = ?"] = $item;
        } elseif ($tipoPesquisa == 4) {
            $where["i.descricao <> ?"] = "%" . $item;
        }

        $tbpretitem = new MantertabelaitensDAO();
        $tbpretitem = $tbpretitem->listarProdutoEtapaItem($item = null, $nomeitem = null, $etapa, $produto, $where);

        $arr = array();
        $arrNomeProduto = array();
        foreach ($tbpretitem as $item) {
            $arr[$item->idProduto][$item->idEtapa][] = $item;
            $arrNomeProduto[$item->idProduto] = $item->Produto;
        }

        $html = '
                <table width="100%" style="font-family:Arial">
                    <tr>
                        <th style="font-size:20px;" colspan="3">
                            Relatório Produto/Etapa/Item
                        </th>
                    </tr>
                ';

        if (!empty($arr)) {
            $ct = 0;
            foreach ($arr as $chaveProduto => $Produto) {
                $html .= '
                            <tr>
                                <td colspan="3" align="left" style="background-color: #cccccc; font-size:14px; font-weight:bold;">
                                    Produto : ' . $arrNomeProduto[$chaveProduto] . '
                                </td>
                            </tr>';
                foreach ($Produto as $chaveEtapa => $Etapa) {
                    $html .= '
                                <tr>
                                    <td width="25px">  </td>
                                    <td colspan="2" align="left" style="background-color: #EFEFEF; font-size:14px; font-weight:bold;">
                                        Etapa: ' . $arr[$chaveProduto][$chaveEtapa][0]->Etapa . '
                                    </td>
                                </tr>';

                    foreach ($Etapa as $Item) {
                        $html .= '
                                        <tr>
                                        <td width="25px">  </td>
                                        <td width="25px">  </td>
                                        <td align="left">
                                                Item: ' . $Item->NomeDoItem . '
                                            </td>
                                        </tr>';
                    }
                }
            }
            $html .= '</table>';
            $ct++;
        }


        try {
            //echo $html; die;
            $pdf = new PDF($html, 'pdf');
            $pdf->gerarRelatorio();
        } catch (Exception $e) {
            xd($e->getMessage());
        }
    }

    /**
     * gerarpdfAction
     *
     * @access public
     * @return void
     * @todo retirar html
     */
    public function gerarpdfAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $output = '
			<style>
				th{
                                background:#ABDA5D;
                                color:#3A7300;
                                text-transform:uppercase;
                                font-size:14px;
        			font-weight: bold;
        			font-family: sans-serif;
        			height: 16px;
        			line-height: 16px;
                                border:1px #ccc solid;
        		}
        		td{
        			color:#000;
        			font-size:14px;
        			font-family: sans-serif;
        			height: 14px;
        			line-height: 14px;
                                border:1px #ccc solid;
        		}
        		.blue{
        			color: blue;
        		}
        		.red{
        			color: red;
        		}
        		.orange{
        			color: orange;
        		}
        		.green{
        			color: green;
        		}

        		.direita{
        			text-align: right;
        		}

        		.centro{
        			text-align: center;
        		}

			</style>';

        $output .= $_POST['html'];

        $patterns = array();
        $patterns[] = '/<table.*?>/is';
        $patterns[] = '/<thead>/is';
        $patterns[] = '/<\/thead>/is';
        $patterns[] = '/<tbody>/is';
        $patterns[] = '/<\/tbody>/is';
        $patterns[] = '/<col.*?>/is';
        $patterns[] = '/<a.*?>/is';

        $replaces = array();
        $replaces[] = '<table cellpadding="0" cellspacing="1" border="1" width="97%" align="center">';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';
        $replaces[] = '';

        $output = preg_replace($patterns, $replaces, $output);

        $pdf = new PDF($output, 'pdf');
        $pdf->gerarRelatorio('h');
    }
}
