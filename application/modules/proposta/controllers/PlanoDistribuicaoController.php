<?php

class Proposta_PlanoDistribuicaoController extends Proposta_GenericController
{
    private $intTamPag = 10;

    public function init()
    {
        parent::init();

        if (!empty($this->idPreProjeto)) {
            $this->view->idPreProjeto =  $this->idPreProjeto;
        } else {
            if ($this->idPreProjeto != '0') {
                parent::message("Necess&aacute;rio informar o n&uacute;mero da proposta.", "/proposta/manterpropostaincentivofiscal/index", "ERROR");
            }
        }

        /* ==== VERIFICA PERMISSAO DE ACESSO DO PROPONENTE A PROPOSTA OU AO PROJETO ====== */
        $this->verificarPermissaoAcesso(true, false, false);
        $this->validarEdicaoProposta();
    }

    public function indexAction()
    {
        $this->view->localRealizacao = true;

        $arrBusca = array();
        $arrBusca['idprojeto'] = $this->idPreProjeto;
        $arrBusca['stabrangencia'] = 1;
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);

        if (empty($rsAbrangencia)) {
            $this->view->localRealizacao = false;
        }

        $tblPlanoDistribuicao = new PlanoDistribuicao();

        $rsPlanoDistribuicao = $tblPlanoDistribuicao->buscar(
            array("a.idprojeto = ?" => $this->idPreProjeto, "a.stplanodistribuicaoproduto = ?" => 1),
            array("idplanodistribuicao DESC")
        );

        $this->view->planosDistribuicao = ($rsPlanoDistribuicao);
        $this->view->formulario = $this->_urlPadrao."/proposta/plano-distribuicao/frm-plano-distribuicao/idPreProjeto/".$this->idPreProjeto;
        $this->view->urlApagar = $this->_urlPadrao."/proposta/plano-distribuicao/apagar/idPreProjeto/".$this->idPreProjeto;
        $this->view->isEditavel = $this->isEditavel($this->idPreProjeto);

    }

    public function frmPlanoDistribuicaoAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $bln_exitePP = "false"; //Nao existe Produto Principal cadastrado

        $params = $this->getRequest()->getParams();

        if (!empty($params['idPlanoDistribuicao'])) {
            $tblPlanoDistribuicao = new PlanoDistribuicao();
            $rsPlanoDistribuicao = $tblPlanoDistribuicao->buscarPlanoDistribuicao(array('idPlanoDistribuicao = ?' => $params['idPlanoDistribuicao']));
            $arrDados["planoDistribuicao"] = $rsPlanoDistribuicao;
        }

        $tblProduto = new Produto();
        $rsProdutos = $tblProduto->buscar(array("stestado = ?" => 0), array("descricao ASC"));

        //BUSCA POR PRODUTO PRINCIPAL CADASTRADO
        $tblPlanoDistribuicao = new PlanoDistribuicao();
        $arrPlanoDistribuicao = $tblPlanoDistribuicao->buscar(array(
            "a.idprojeto = ?" => $this->idPreProjeto,
            "a.stprincipal = ?" => 1,
            "a.stplanodistribuicaoproduto = ?" => 1), array("idplanodistribuicao DESC"))
            ->toArray();

        if (!empty($arrPlanoDistribuicao)) {
            $bln_exitePP = "true"; //Existe Produto Principal Cadastrado

            $tblSegmento = new Segmento();
            $arrDados["segmento"] = $tblSegmento->buscar(array("codigo=?" =>$arrPlanoDistribuicao[0]['Segmento']));
        }

        $arrDados["comboprodutos"] = $rsProdutos;
        $manterAgentes = new ManterAgentes();
        $arrDados["comboareasculturais"] = $manterAgentes->listarAreasCulturais();

        $arrDados["acaoSalvar"] = $this->_urlPadrao."/proposta/plano-distribuicao/salvar/idPreProjeto/".$this->idPreProjeto;
        $arrDados["urlApagar"] = $this->_urlPadrao."/proposta/plano-distribuicao/apagar/idPreProjeto/".$this->idPreProjeto;
        $arrDados["acaoCancelar"] = $this->_urlPadrao."/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto;
        $arrDados["bln_exitePP"] = $bln_exitePP;
        $this->montaTela("plano-distribuicao/formplanodistribuicao.phtml", $arrDados);
    }

    public function salvarAction()
    {
        $post = (object)($this->getRequest()->getPost());

        if (($this->isEditarProjeto($this->idPreProjeto) && $post->prodprincipal == 1)) {
            parent::message("Em alterar projeto, n&atilde;o pode alterar o produto principal cadastrado. A opera&ccedil;&atilde;o foi cancelada.", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
        }

        $precopromocional = str_replace(",", ".", str_replace(".", "", $post->precopromocional));
        $preconormal = str_replace(",", ".", str_replace(".", "", $post->preconormal));
        $QtdeProduzida = $post->qtdenormal+$post->qtdepromocional+$post->patrocinador+$post->beneficiarios+$post->divulgacao;
        $dados = array(
                 "Area"=>$post->areaCultural,
                 "idProjeto"=>$this->idPreProjeto,
                 "idProduto"=>$post->produto,
                 "Segmento"=>$post->segmentoCultural,
                 "QtdeProduzida"=>$QtdeProduzida,
                 "QtdeVendaNormal"=>$post->qtdenormal,
                 "QtdeVendaPromocional"=>$post->qtdepromocional,
                 "dsJustificativaPosicaoLogo"=>$post->dsJustificativaPosicaoLogo,
                 "PrecoUnitarioNormal"=>$preconormal,
                 "PrecoUnitarioPromocional"=>$precopromocional,
                 "stPrincipal"=>$post->prodprincipal,
                 "canalAberto"=>$post->canalAberto,
                 );
        if (isset($post->idPlanoDistribuicao)) {
            $dados["idPlanoDistribuicao"] = $post->idPlanoDistribuicao;
        }
        if (isset($post->patrocinador)) {
            $dados["QtdePatrocinador"] = $post->patrocinador;
        }
        if (isset($post->divulgacao)) {
            $dados["QtdeProponente"] = $post->divulgacao;
        }
        if (isset($post->beneficiarios)) {
            $dados["QtdeOutros"] = $post->beneficiarios;
        }
        $dados["stPlanoDistribuicaoProduto"] = 1;

        $tblPlanoDistribuicao = new PlanoDistribuicao();

        //VERIFICA SE JA EXISTE PRODUTO PRINCIPAL JA CADASTRADO
        $arrBusca = array();
        $arrBusca['a.idProjeto = ?'] = $this->idPreProjeto;
        $arrBusca['a.stPrincipal = ?'] = 1;
        !empty($post->idPlanoDistribuicao) ? $arrBusca['idPlanoDistribuicao <> ?'] = $post->idPlanoDistribuicao : '' ;
        $arrBusca['stPlanoDistribuicaoProduto = ?'] = 1;
        $arrPlanoDistribuicao = $tblPlanoDistribuicao->buscar($arrBusca, array("idPlanoDistribuicao DESC"))->toArray();

        if ($post->patrocinador!=0 || $post->divulgacao!=0 || $post->beneficiarios!=0 || $post->qtdenormal!=0 || $post->qtdepromocional!=0) {
            if (!empty($arrPlanoDistribuicao) && $post->prodprincipal == "1") {
                parent::message("J&aacute; existe um Produto Principal cadastrado. A opera&ccedil;&atilde;o foi cancelada.", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
            }
            if ($post->patrocinador > ($QtdeProduzida/10)) {
                parent::message("A quantidade destinada ao patrocinador n&atilde;o pode ser maior do que 10% do n&uacute;mero Exemplares/Ingressos.", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
            }
            if ($post->divulgacao > ($QtdeProduzida/10)) {
                parent::message("A quantidade destinada &agrave; divulga&ccedil;&atilde;o n&atilde;o pode ser maior do que 10% do n&uacute;mero Exemplares/Ingressos.", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
            }
            if ($post->beneficiarios < ($QtdeProduzida/10)) {
                parent::message("A quantidade destinada &agrave; popula&ccedil;&atilde;o de baixa renda n&atilde;o pode ser menor do que 10% do n&uacute;mero Exemplares/Ingressos.", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
            }
            if ((int)str_replace(".", "", $precopromocional) > (int)str_replace(".", "", $preconormal)) {
                parent::message("O valor normal n&atilde;o pode ser menor ou igual ao valor promocional!", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
            }
            if ($post->qtdenormal == null) {
                parent::message("Favor preencher o campo Normal(Qntd).", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
            }
            if ($post->qtdepromocional == null) {
                parent::message("Favor preencher o campo Promocional(Qntd).", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
            }
        }

        //VERIFICA SE PRODUTO JA ESTA CADASTRADO - NAO PODE GRAVAR O MESMO PRODUTO MAIS DE UMA VEZ.
        if (isset($post->produto)) {
            $arrBuscaProduto['a.idProjeto = ?'] = $this->idPreProjeto;
            $arrBuscaProduto['a.idProduto = ?'] = $post->produto;
            $objProduto = $tblPlanoDistribuicao->buscar($arrBuscaProduto);

            if (!empty($objProduto->toArray())) {
                parent::message("Produto j&aacute; cadastrado no plano de distribui&ccedil;&atilde;o desta proposta!", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
            }
        }

        $retorno = $tblPlanoDistribuicao->salvar($dados);

        if ($retorno > 0) {
            parent::message("Opera&ccedil;&atilde;o realizada com sucesso!", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "CONFIRM");
        } else {
            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
        }
    }

    public function apagarAction()
    {
        if (empty($this->idPreProjeto)) {
            parent::message("Informe o numero da proposta", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
        }

        $params = $this->getRequest()->getParams();

        if(empty($params['idPlanoDistribuicao'])) {
            throw new Exception("ID &eacute; obrigat&oacute;rio");
        }

        $tblPlanoDistribuicao = new PlanoDistribuicao();
        $rsPlanoDistribuicao = $tblPlanoDistribuicao->findBy(array("idplanodistribuicao = ?" => $params['idPlanoDistribuicao']));

        if (($this->isEditarProjeto($this->idPreProjeto) && $rsPlanoDistribuicao['stPrincipal'] == 1)) {
            parent::message("Em alterar projeto, n&atilde;o pode excluir o produto principal cadastrado. A opera&ccedil;&atilde;o foi cancelada.", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
        }

        $retorno = $tblPlanoDistribuicao->apagar($params['idPlanoDistribuicao']);

        if ($retorno > 0) {
            parent::message("Opera&ccedil;&atilde;o realizada com sucesso!", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "CONFIRM");
        } else {
            parent::message("N&atilde;o foi poss&iacute;vel realizar a opera&ccedil;&atilde;o!!", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
        }
    }

    public function detalharPlanoDistribuicaoAction()
    {
        $this->carregarScriptsVue();

        $params = $this->getRequest()->getParams();

        if (empty($params['idPlanoDistribuicao'])) {
            parent::message("&Eacute; necess&aacute;rio informar o produto do plano de distribui&ccedil;&atilde;o", "/proposta/plano-distribuicao/index/idPreProjeto/".$this->idPreProjeto, "ERROR");
        }

        $planoDistribuicaoProduto = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $rsPlanoDistribuicao = $planoDistribuicaoProduto->buscar(
            array("a.idplanodistribuicao = ?" => $params['idPlanoDistribuicao'], "a.idprojeto = ?" => $this->idPreProjeto, "a.stplanodistribuicaoproduto = ?" => 1),
            array("idplanodistribuicao DESC")
        );

        $arrBusca['idprojeto'] = $this->idPreProjeto;
        $arrBusca['stabrangencia'] = 1;
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);

        $this->view->abrangencias = $rsAbrangencia;
        $this->view->planosDistribuicao=($rsPlanoDistribuicao);
        $this->view->isEditavel = $this->isEditavel($this->idPreProjeto);

        $objTbProjetoFase = new Projeto_Model_DbTable_TbProjetoFase();
        $normativo = $objTbProjetoFase->obterNormativoProjeto(['a.idProjeto = ?' => $this->idPreProjeto])->current();

        $this->view->idNormativo = $normativo ? $normativo->idNormativo : '';
    }

    public function detalharSalvarAction()
    {
        $dados = $this->getRequest()->getPost();

        try {

            if (empty($this->idPreProjeto)) {
                throw new Exception("Proposta &eacute; obrigat&oacute;ria");
            }

            if (empty($dados['idPlanoDistribuicao'])) {
                throw new Exception("Produto &eacute; obrigat&oacute;rio");
            }

            if (empty((int) $dados['idDetalhaPlanoDistribuicao'])) {
                unset($dados['idDetalhaPlanoDistribuicao']);
            }

            $dados['dsProduto'] = utf8_decode($dados['dsProduto']);

            $mdlDetalhaPlanoDistribuicao = new Proposta_Model_TbDetalhaPlanoDistribuicao($dados);

            $tbDetalhamentoMapper = new Proposta_Model_TbDetalhaPlanoDistribuicaoMapper();
            $id = $tbDetalhamentoMapper->salvar($mdlDetalhaPlanoDistribuicao, $this->idPreProjeto);
            if (!empty($id)) {
                $dados['idDetalhaPlanoDistribuicao'] = $id;
            }

            $this->_helper->json([
                'data' => TratarArray::utf8EncodeArray($dados),
                'success' => 'true',
                'msg' => 'Detalhamento salvo com sucesso!'
            ]);
        } catch (Exception $e) {
            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->setHttpResponseCode(412);
            $this->_helper->json(array('data' => $dados, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }

    public function obterDetalhamentosAction()
    {
        $dados = $this->getRequest()->getParams();
        $detalhamento = new Proposta_Model_DbTable_TbDetalhaPlanoDistribuicao();
        $dados = $detalhamento->listarPorMunicicipioUF($dados);

        $this->_helper->json(array('data' => TratarArray::utf8EncodeArray($dados->toArray()), 'success' => 'true'));
    }

    public function detalharExcluirAction()
    {
        try {
            $id = (int)$this->getRequest()->getParam('idDetalhaPlanoDistribuicao');
            $idPlanoDistribuicao = (int)$this->getRequest()->getParam('idPlanoDistribuicao');

            $tbDetalhaMapper = new Proposta_Model_TbDetalhaPlanoDistribuicaoMapper();
            $retorno = $tbDetalhaMapper->excluirDetalhamento($id, $idPlanoDistribuicao, $this->idPreProjeto);

            $this->_helper->json(array('data' => $retorno, 'success' => 'true', 'msg' => 'Detalhamento exclu&iacute;do com sucesso'));

        } catch (Exception $e) {
            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->setHttpResponseCode(412);

            $this->_helper->json(array('data' => $retorno, 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }

    private function carregarScriptsVue()
    {
        $gitTag = '?v=' . $this->view->gitTag();
        $this->view->headScript()->offsetSetFile(99, '/public/dist/js/manifest.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(100, '/public/dist/js/vendor.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(101, '/public/dist/js/proposta.js'. $gitTag, 'text/javascript', array('charset' => 'utf-8'));
    }
}
