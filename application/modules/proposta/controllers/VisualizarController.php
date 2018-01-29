<?php

class Proposta_VisualizarController extends Proposta_GenericController
{
    public function init()
    {
        parent::init();

    }

    public function indexAction()
    {
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

            $tbPreProjetoMapper = new Proposta_Model_TbPreProjetoMetaMapper();
            $propostaCulturalAtual = $tbPreProjetoMapper->obterPropostaCulturalCompleta($idPreProjeto);

            $propostaCulturalAtual = $this->prepararArrayParaJson($propostaCulturalAtual);
            $propostaCulturalAtual['tbplanilhaproposta'] = $this->montarPlanilhaProposta(
                $propostaCulturalAtual['tbplanilhaproposta']
            );

            $propostaAtual = array_merge(
                $propostaCulturalAtual['responsabilidadesocial'],
                $propostaCulturalAtual['detalhestecnicos'],
                $propostaCulturalAtual['outrasinformacoes'],
                $propostaCulturalAtual['identificacaoproposta']
            );

            $propostaAtual = array_merge($propostaAtual, $propostaCulturalAtual);

            $propostaCulturalHistorico = $tbPreProjetoMapper->unserializarPropostaCulturalCompleta($idPreProjeto, $tipo);

            if (empty($propostaCulturalHistorico)) {
                throw new Exception("Historico n&atilde;o encontrado!");
            }

            $propostaCulturalHistorico = $this->prepararArrayParaJson($propostaCulturalHistorico);
            $propostaCulturalHistorico['tbplanilhaproposta'] = $this->montarPlanilhaProposta(
                $propostaCulturalHistorico['tbplanilhaproposta']
            );
            $propostaHistorico = array_merge(
                $propostaCulturalHistorico['responsabilidadesocial'],
                $propostaCulturalHistorico['detalhestecnicos'],
                $propostaCulturalHistorico['outrasinformacoes'],
                $propostaCulturalHistorico['identificacaoproposta']
            );

            $propostaHistorico = array_merge($propostaHistorico, $propostaCulturalHistorico);

            $dados = [];
            $dados['atual'] = $propostaAtual;
            $dados['historico'] = $propostaHistorico;

            $this->_helper->json(array('success' => 'true', 'msg' => '', 'data' => $dados));
        } catch (Exception $e) {
            $this->_helper->json(array('success' => 'false', 'msg' => $e->getMessage(), 'data' => []));
        }
    }

    public function montarPlanilhaProposta($planilhaOrcamentaria)
    {
        $planilha = array();
        $count = 0;
        $i = 1;

        foreach ($planilhaOrcamentaria as $item) {
            $row = [];

            $produto = !empty($item['idProduto']) ? $item['DescricaoProduto'] : utf8_encode('Administra&ccedil;&atilde;o do Projeto');

            $row["Seq"] = $i;
            $row["idPlanilhaProposta"] = $item['idPlanilhaProposta'];
            $row["Item"] = $item['DescricaoItem'];
            $row['FonteRecurso'] = $item['DescricaoRecurso'];
            $row['Municipio'] = $item['DescricaoMunicipio'];
            $row['UF'] = $item['DescricaoUf'];
            $row['idEtapa'] = $item['idEtapa'];
            $row['Etapa'] = $item['DescricaoEtapa'];
            $row['Ocorrencia'] = $item['Ocorrencia'];
            $row['Quantidade'] = $item['Quantidade'];
            $row['QtdeDias'] = $item['QtdeDias'];
            $row['vlUnitario'] = $item['ValorUnitario'];
            $row["vlSolicitado"] = $item['Quantidade'] * $item['Ocorrencia'] * $item['ValorUnitario'];
            $row['JustProponente'] = $item['dsJustificativa'];
            $row['stCustoPraticado'] = $item['stCustoPraticado'];

            foreach ($row as $cel => $val) {
                $planilha[$row['FonteRecurso']][$produto][$row['idEtapa'] . ' - '
                . $row['Etapa']][$row['UF'] . ' - '
                . $row['Municipio']][$count][$cel] = $val;
            }
            $count++;
            $i++;
        }

        return $planilha;
    }

    public function prepararArrayParaJson($dados)
    {
        foreach ($dados as $key => $array) {
            foreach ($array as $key2 => $dado) {
                if (is_array($dado)) {
                    $dado = array_map('utf8_encode', $dado);
                    $dados[$key][$key2] = array_map('html_entity_decode', $dado);

                    foreach ($dado as $key3 => $dado2) {
                        if (is_array($dado2)) {
                            $dado2 = array_map('utf8_encode', $dado2);
                            $dados[$key][$key2][$key3] = array_map('html_entity_decode', $dado2);
                        }
                    }
                } else {
                    $dado = utf8_encode($dado);
                    $dados[$key][$key2] = html_entity_decode($dado);
                }
            }
        }

        return $dados;
    }

    public function obterHistoricoAvaliacoesAction()
    {
        $this->_helper->layout->disableLayout();

        try {
            $dados = Proposta_Model_AnalisarPropostaDAO::buscarHistorico($this->idPreProjeto);
            $json = [];
            $newArray = [];

            foreach ($dados as $key => $dado) {
                $newArray[$key]['Tipo'] = $dado->tipo;
                $objDateTime = new DateTime($dado->DtAvaliacao);
                $newArray[$key]['DtAvaliacao'] = $objDateTime->format('d/m/Y H:i:s');
                $newArray[$key]['Avaliacao'] = $dado->Avaliacao;
            }

            $json['lines'] = $newArray;
            $json['cols'] = [
                'Tipo' => ['name' => 'Tipo'],
                'DtAvaliacao' => ['name' => 'Data'],
                'Avaliacao' => ['name' => 'Avalia&ccedil;&atilde;o']
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

            $tbl = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
            $documentos['proposta'] = $tbl->buscarDadosDocumentos(array("idProjeto = ?" => $idPreProjeto));

            $tbA = new Proposta_Model_DbTable_TbDocumentosAgentes();
            $documentos['proponente'] = $tbA->buscarDadosDocumentos(array("idAgente = ?" => $idAgente))->toArray();

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
        $dados['localizacoes'] = $tblAbrangencia->buscar($arrBusca);

        $tblDeslocamento = new Proposta_Model_DbTable_TbDeslocamento();
        $dados['deslocamentos'] = $tblDeslocamento->buscarDeslocamentosGeral(array('idProjeto' => $idPreProjeto));

        foreach ($dados as $key => $array) {
            foreach ($array as $key2 => $dado) {
                $dados[$key][$key2] = array_map('utf8_encode', $dado);
            }
        }

        $this->_helper->json(array('data' => $dados, 'success' => 'true'));
    }

    public function obterDeslocamentoAction($idPreProjeto)
    {
        $this->_helper->layout->disableLayout();

        $deslocamentos = new Proposta_Model_TbDeslocamentoMapper();
        $dados = $deslocamentos->getDbTable()->buscarDeslocamento($idPreProjeto, $id);

        $dados = [];

        $this->_helper->json(array('data' => $dados, 'success' => 'true'));
    }

    public function obterPlanilhaOrcamentariaPropostaAction($idPreProjeto)
    {
        $this->_helper->layout->disableLayout();

        $dados = [];

        $this->_helper->json(array('data' => $dados, 'success' => 'true'));
    }

    public function obterPlanoDeDivulgacaoAction($idPreProjeto)
    {
        $this->_helper->layout->disableLayout();

        $dados = [];

        $this->_helper->json(array('data' => $dados, 'success' => 'true'));
    }

    public function obterPlanoDistribuicacaoAction($idPreProjeto)
    {
        $dados = [];

        $this->_helper->layout->disableLayout();

        $arrBusca = array();
        $arrBusca['idprojeto'] = $this->idPreProjeto;

        $tblAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $rsAbrangencia = $tblAbrangencia->buscar($arrBusca);
        $this->view->abrangencias = $rsAbrangencia;

        $tblPlanoDistribuicao = new PlanoDistribuicao();

        $rsPlanoDistribuicao = $tblPlanoDistribuicao->buscar(
            array("a.idprojeto = ?" => $this->idPreProjeto, "a.stplanodistribuicaoproduto = ?" => 1),
            array("idplanodistribuicao DESC")
        );

        $this->view->planosDistribuicao = $rsPlanoDistribuicao;
        $this->abrangencias = $rsAbrangencia;

        $this->_helper->json(array('data' => $dados, 'success' => 'true'));
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
}
