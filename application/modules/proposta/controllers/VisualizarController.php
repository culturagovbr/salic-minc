<?php

class Proposta_VisualizarController extends Proposta_GenericController
{
    public function init()
    {
        parent::init();

        $this->verificarPermissaoAcesso(true, false, false);

    }

    public function indexAction()
    {
        $this->view->idPreProjeto = $this->_request->getParam('idPreProjeto');

        if (empty($this->view->idPreProjeto)) {
            $this->redirect("/proposta/manterpropostaincentivofiscal/listarproposta");
        }

        $gitTag = '?v=' . $this->view->gitTag();
        $this->view->headScript()->offsetSetFile(99, '/public/dist/js/manifest.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(100, '/public/dist/js/vendor.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));
        $this->view->headScript()->offsetSetFile(101, '/public/dist/js/proposta.js' . $gitTag, 'text/javascript', array('charset' => 'utf-8'));

    }

    public function obterPropostaCulturalCompletaAction()
    {
        $this->_helper->layout->disableLayout();
        try {

            $idPreProjeto = $this->_request->getParam('idPreProjeto');

            if (empty($idPreProjeto)) {
                throw new Exception("N&uacute;mero da proposta &eacute; obrigat&oacute;rio");
            }

            $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
            $dados = $preProjetoMapper->obterArrayPropostaCompleta($idPreProjeto);
            $dados['fase_proposta'] = $this->obterFaseProposta($idPreProjeto, $dados);

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $dados));
        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }


    /** @todo migrar para service ou modelo */
    public function obterFaseProposta($idPreProjeto, $proposta)
    {
        $tbMovimentacao = new Proposta_Model_DbTable_TbMovimentacao();
        $movimentacao = $tbMovimentacao->buscarMovimentacaoProposta($idPreProjeto);

        switch ($movimentacao['idMovimentacao']) {
            case Proposta_Model_TbMovimentacao::PROPOSTA_COM_PROPONENTE:
                $fase = 'proposta_com_proponente';
                break;
            case Proposta_Model_TbMovimentacao::PROPOSTA_PARA_ANALISE_INICIAL:
                $fase = 'proposta_analise_inicial';
                break;
            case Proposta_Model_TbMovimentacao::PROPOSTA_PARA_ANALISE_FINAL:
                $fase = 'proposta_em_enquadramento';
                if (count($this->view->recursoEnquadramentoVisaoProponente) > 0) {
                    $fase = 'recurso_enquadramento';
                }

                $tbProjetos = new Projeto_Model_DbTable_Projetos();
                $projeto = $tbProjetos->findBy(['idProjeto' => $idPreProjeto] );
                if (!empty($projeto)) {
                    $fase = 'projeto_cultural';
                }

                break;
            default:
                $fase = 'invalido';
                break;
        }

        if ($proposta['stEstado'] == 0) {
            $fase = 'proposta_arquivada';
        }

        return $fase;
    }

    public function obterIdentificacaoAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $idPreProjeto = $this->_request->getParam('idPreProjeto');

            $tbProposta = new Proposta_Model_DbTable_PreProjeto();
            $dados = $tbProposta->buscarIdentificacaoProposta(['pp.idPreProjeto = ?' => $idPreProjeto])->current()->toArray();

            $dados = array_map('utf8_encode', $dados);
            $dados = array_map('html_entity_decode', $dados);

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $dados));
        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }

    public function obterPropostaCulturalVersionamentoAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $idPreProjeto = $this->_request->getParam('idPreProjeto');
            $tipo = $this->_request->getParam('tipo', 'alterarprojeto');


            if (empty($idPreProjeto)) {
                throw new Exception("N&uacute;mero da proposta &eacute; obrigat&oacute;rio");
            }

            $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
            $propostaAtual = $preProjetoMapper->obterArrayPropostaCompleta($idPreProjeto);
            $propostaHistorico = $preProjetoMapper->obterArrayVersaoPropostaCompleta($idPreProjeto, $tipo);

            $tbProjeto = new Projeto_Model_DbTable_Projetos();
            $projeto = $tbProjeto->findBy(['idProjeto' => $idPreProjeto]);

            if (!empty($projeto)) {

                $pronac = $projeto['AnoProjeto'] . $projeto['Sequencial'];
                $propostaAtual = array_merge($propostaAtual, ['PRONAC' => $pronac, 'idPronac' => $projeto['IdPRONAC']]);

            }

            $dados = [];
            $dados['atual'] = $propostaAtual;
            $dados['historico'] = $propostaHistorico;

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $dados));
        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }


    public function obterHistoricoAvaliacoesAction()
    {
        $this->_helper->layout->disableLayout();

        try {
            $dados = Proposta_Model_AnalisarPropostaDAO::buscarHistorico($this->idPreProjeto);
            $json = [];
            $newArray = [];

            foreach ($dados as $key => $dado) {
                $objDateTime = new DateTime($dado->DtAvaliacao);
                $newArray[$key]['Tipo'] = $dado->tipo;
                $newArray[$key]['DtAvaliacao'] = $objDateTime->format('d/m/Y H:i:s');
                $newArray[$key]['Avaliacao'] = str_replace('<p>&nbsp;</p>', '', $dado->Avaliacao);
            }

            $json['class'] = 'bordered striped';
            $json['lines'] = $newArray;
            $json['cols'] = [
                'Tipo' => ['name' => 'Tipo'],
                'DtAvaliacao' => ['name' => 'Data', 'class' => 'valig'],
                'Avaliacao' => [
                    'name' => html_entity_decode('Avalia&ccedil;&atilde;o')]
            ];

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $json));
        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }

    public function obterDocumentosAnexadosAction()
    {
        $this->_helper->layout->disableLayout();

        $idAgente = $this->_request->getParam('idAgente');
        $idPreProjeto = $this->_request->getParam('idPreProjeto');

        try {

            if (empty($idAgente) || empty($idPreProjeto)) {
                throw new Exception("IdAgente e IdPreProjeto s&atilde;o obrigat&oacute;rios");
            }

            $documentos = [];

            $tbDocumentosPreProjeto = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
            $documentos['documentos_proposta'] = $tbDocumentosPreProjeto->buscarDadosDocumentos(array("idProjeto = ?" => $idPreProjeto));

            $tbAgentes = new Proposta_Model_DbTable_TbDocumentosAgentes();
            $documentos['documentos_proponente'] = $tbAgentes->buscarDadosDocumentos(array("idAgente = ?" => $idAgente))->toArray();

            $arrayTipos = array(1, 2, 3);

            foreach ($documentos as $key => $array) {
                foreach ($array as $key2 => $dado) {

                    $id = isset($dado['idDocumentosPreProjetos']) ? $dado['idDocumentosPreProjetos'] : $dado['idDocumentosAgentes'];

                    $dado['url'] = '';

                    if (in_array($dado['tpDoc'], $arrayTipos)) {

                        $dado['url'] = $this->_helper->url->url(
                                [
                                    'module' => 'admissibilidade',
                                    'controller' => 'admissibilidade',
                                    'action' => 'abrir-documentos-anexados-admissibilisdade'
                                ],
                                false,
                                true
                            ) . "?id=" . $id . "&tipo=" . $dado['tpDoc'];
                    }

                    $documentos[$key][$key2] = array_map('utf8_encode', $dado);
                }
            }

            $this->_helper->json(array('data' => $documentos, 'success' => 'true'));
        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }

    public function obterLocalRealizacaoDeslocamentoAction()
    {
        $this->_helper->layout->disableLayout();

        $idPreProjeto = $this->_request->getParam('idPreProjeto');

        $arrBusca = array();
        $arrBusca['idprojeto'] = $idPreProjeto;
        $arrBusca['stabrangencia'] = 1;
        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $dados['abrangencia'] = $tblAbrangencia->buscar($arrBusca);

        $tblDeslocamento = new Proposta_Model_DbTable_TbDeslocamento();
        $dados['deslocamento'] = $tblDeslocamento->buscarDeslocamentosGeral(array('idProjeto' => $idPreProjeto));

        foreach ($dados as $key => $array) {
            foreach ($array as $key2 => $dado) {
                $dados[$key][$key2] = array_map('utf8_encode', $dado);
            }
        }

        $this->_helper->json(array('data' => $dados, 'success' => 'true'));
    }

    public function obterPlanilhaOrcamentariaPropostaAction()
    {
        $this->_helper->layout->disableLayout();

        $idPreProjeto = $this->_request->getParam('idPreProjeto');

        try {

            if (empty($idPreProjeto)) {
                throw new Exception("N&uacute;mero da proposta &eacute; obrigat&oacute;ria");
            }
            $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
            $planilhaOrcamentaria = $spPlanilhaOrcamentaria->exec($idPreProjeto, 0);
            $planilha = $this->montarPlanilhaOrcamentaria($planilhaOrcamentaria, 0);

            $planilha = TratarArray::utf8EncodeArray($planilha);

            $this->_helper->json(array('data' => $planilha, 'success' => 'true'));
        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }

    public function obterPlanoDeDivulgacaoAction($idPreProjeto)
    {
        $this->_helper->layout->disableLayout();

        $dados = [];

        $this->_helper->json(array('data' => $dados, 'success' => 'true'));
    }

    public function obterPlanoDistribuicacaoAction()
    {
        $dados = [];

        $this->_helper->layout->disableLayout();

        $idPreProjeto = $this->_request->getParam('idPreProjeto');

        try {

            if (empty($idPreProjeto)) {
                throw new Exception("Proposta inválida");
            }

            $tbPlanoDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
            $dados['planodistribuicaoproduto'] = $tbPlanoDistribuicao->buscar(array('idProjeto = ?' => $idPreProjeto))->toArray();
            $dados['tbdetalhaplanodistribuicao'] = $tbPlanoDistribuicao->buscarPlanoDistribuicaoDetalhadoByIdProjeto($idPreProjeto);
            $dados = TratarArray::utf8EncodeArray($dados);

            $this->_helper->json(array('data' => $dados, 'success' => 'true'));
        } catch (Exception $e) {
            $this->_helper->json(array('msg' => utf8_encode($e->getMessage()), 'data' => $dados, 'success' => 'false'));
        }
    }

    public function obterDetalhamentoPlanoDistribuicao($idPlanoDistribuicacao)
    {
        $this->_helper->layout->disableLayout();

        $dados = [];

        $this->_helper->json(array('data' => $dados, 'success' => 'true'));
    }

    public function obterFonteDeRecursoAction()
    {
        $this->_helper->layout->disableLayout();
        $idPreProjeto = $this->_request->getParam('idPreProjeto');

        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $dados = $tbPlanilhaProposta->buscarFontesDeRecursos($idPreProjeto);

        $json = [];
        $newArray = [];

        foreach ($dados as $key => $dado) {
            $newArray[$key]['Descricao'] = utf8_encode($dado->Descricao);
            $newArray[$key]['Valor'] = number_format($dado->Valor, 2, ',', '.');
        }

        $json['lines'] = $newArray;
        $json['cols'] = [
            'Descricao' => ['name' => 'Fonte Recurso'],
            'Valor' => ['name' => 'Valor (R$)']
        ];

        $this->_helper->json(array('data' => $json, 'success' => 'true'));
    }

    public function obterCustosVinculadosAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $tbCustosVinculados = new Proposta_Model_DbTable_TbCustosVinculados();
            $dados = $tbCustosVinculados->buscarCustosVinculados(['idProjeto = ?' => $this->idPreProjeto])->toArray();

            $data = [];
            $newArray = [];

            foreach ($dados as $key => $dado) {
                $objDateTime = new DateTime($dado['dtCadastro']);
                $newArray[$key]['item'] = utf8_encode($dado['item']);
                $newArray[$key]['dtCadastro'] = $objDateTime->format('d/m/Y');
                $newArray[$key]['pcCalculo'] = $dado['pcCalculo'] . '%';
            }

            $data['class'] = 'bordered striped';
            $data['lines'] = $newArray;
            $data['cols'] = [
                'item' => ['name' => 'Item'],
                'dtCadastro' => ['name' => 'Data', 'class' => 'valig'],
                'pcCalculo' => ['name' => 'Percentual']
            ];

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $data));
        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }

    public function obterPlanilhaPropostaOriginalAjaxAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $idPreProjeto = $this->_request->getParam('idPreProjeto');

            if (empty($idPreProjeto)) {
                throw new Exception("N&uacute;mero da proposta &eacute; obrigat&oacute;rio");
            }

            $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
            $planilha = $preProjetoMapper->obterPlanilhaOriginal($idPreProjeto);

            if (empty($planilha)) {
                throw new Exception("Nenhuma planilha encontrada... ;(");
            }

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $planilha));
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(412);
            $this->_helper->json(array('data' => [], 'success' => 'false', 'msg' => $e->getMessage()));

        }
    }

    public function obterPlanilhaPropostaAdequadaAjaxAction()
    {
        $this->_helper->layout->disableLayout();

        try {

            $idPreProjeto = $this->_request->getParam('idPreProjeto');

            if (empty($idPreProjeto)) {
                throw new Exception("N&uacute;mero da proposta &eacute; obrigat&oacute;rio");
            }

            $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
            $planilha = $preProjetoMapper->obterPlanilhaAdequacao($idPreProjeto);

            if (empty($planilha)) {
                throw new Exception("Nenhuma planilha encontrada... ;(");
            }

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $planilha));
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(412);
            $this->_helper->json(array('data' => [], 'success' => 'false', 'msg' => $e->getMessage()));
        }
    }
}
