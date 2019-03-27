<?php

class Proposta_Model_PreProjetoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Proposta_Model_DbTable_PreProjeto');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function obterPropostaCulturalCompleta($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        $proposta = [];

        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $preProjeto = $tblPreProjeto->buscarIdentificacaoProposta(array('idPreProjeto = ?' => $idPreProjeto))->current()->toArray();

        /**
         * devido ao tamanho da tabela preprojeto separamos em partes
         */
        # responsabilidade social (preprojeto)
        $proposta['responsabilidadesocial'] = array(
            'Acessibilidade' => $preProjeto['Acessibilidade'],
            'DemocratizacaoDeAcesso' => $preProjeto['DemocratizacaoDeAcesso'],
            'ImpactoAmbiental' => $preProjeto['ImpactoAmbiental']
        );

        # detalhes tecnicos (preprojeto)
        $proposta['detalhestecnicos'] = array(
            'EtapaDeTrabalho' => $preProjeto['EtapaDeTrabalho'],
            'FichaTecnica' => $preProjeto['FichaTecnica'],
            'Sinopse' => $preProjeto['Sinopse'],
            'EspecificacaoTecnica' => $preProjeto['EspecificacaoTecnica'],
            'DescricaoAtividade' => $preProjeto['DescricaoAtividade']
        );

        # outras informacoes (preprojeto)
        $proposta['outrasinformacoes'] = array(
            'EstrategiadeExecucao' => $preProjeto['EstrategiadeExecucao']
        );

        # identificacao preprojeto - campos que ainda nao foram salvos
        $proposta['identificacaoproposta'] = (
        array_diff(
            $preProjeto,
            $proposta['responsabilidadesocial'],
            $proposta['detalhestecnicos'],
            $proposta['outrasinformacoes'])
        );

        # Planilha orcamentaria
        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $proposta['tbplanilhaproposta'] = $tbPlanilhaProposta->buscarPlanilhaCompleta($idPreProjeto);

        # Custos Vinculados
        $tbCustosVinculados = new Proposta_Model_DbTable_TbCustosVinculados();
        $proposta['tbcustosvinculados'] = $tbCustosVinculados->buscarCustosVinculados(['idProjeto = ?' => $idPreProjeto])->toArray();

        # Local de realizacao (abrangencia)
        $tbAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $proposta['abrangencia'] = $tbAbrangencia->buscar(['idProjeto' => $idPreProjeto]);

        # Deslocamento
        $tbDeslocamento = new Proposta_Model_DbTable_TbDeslocamento();
        $proposta['deslocamento'] = $tbDeslocamento->buscarDeslocamentosGeral(['idProjeto' => $idPreProjeto]);

        # Plano distribuicao
        $tbPlanoDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $proposta['planodistribuicaoproduto'] = $tbPlanoDistribuicao->buscar(['idProjeto = ?' => $idPreProjeto], ['Produto DESC'])->toArray();

        # Plano de distribuicao Detalhado
        $proposta['tbdetalhaplanodistribuicao'] = $tbPlanoDistribuicao->buscarPlanoDistribuicaoDetalhadoByIdProjeto($idPreProjeto);

        # Documentos Proposta
        $tbDocumentosPreProjeto = new Proposta_Model_DbTable_TbDocumentosPreProjeto();
        $proposta['documentos_proposta'] = $tbDocumentosPreProjeto->buscarDadosDocumentos(["idProjeto = ?" => $idPreProjeto]);

        # Documentos do proponente
        $tbDocumentosAgentes = new Proposta_Model_DbTable_TbDocumentosAgentes();
        $proposta['documentos_proponente'] = $tbDocumentosAgentes->buscarDadosDocumentos(["idAgente = ?" => $preProjeto['idAgente']])->toArray();

        return $proposta;
    }

    public function obterArrayPropostaCompleta($idPreProjeto)
    {
        $proposta = $this->obterPropostaCulturalCompleta($idPreProjeto);

        if (empty($proposta)) {
            return false;
        }

        return $this->prepararPropostaParaJson($proposta);
    }

    public function obterArrayVersaoPropostaCompleta($idPreProjeto, $tipo)
    {
        $tbPreProjetoMetaMapper = new Proposta_Model_TbPreProjetoMetaMapper();
        $proposta = $tbPreProjetoMetaMapper->unserializarPropostaCulturalCompleta($idPreProjeto, $tipo);

        if (empty($proposta)) {
            return false;
        }

        return $this->prepararPropostaParaJson($proposta);
    }

    public function prepararPropostaParaJson($proposta)
    {
        $proposta = TratarArray::utf8EncodeArray($proposta);

        if (!isset($proposta['tbplanilhaproposta'][0]['OrdemEtapa'])) {
            $proposta['tbplanilhaproposta'] = TratarArray::ordenarArrayMultiPorColuna(
                $proposta['tbplanilhaproposta'],
                'DescricaoRecurso', SORT_DESC,
                'DescricaoProduto', SORT_DESC,
                'DescricaoEtapa', SORT_DESC,
                'DescricaoMunicipio', SORT_ASC,
                'DescricaoItem', SORT_ASC
            );
        }

        if ($proposta['tbcustosvinculados']) {
            $newArray = [];

            foreach ($proposta['tbcustosvinculados'] as $key => $dado) {
                $objDateTime = new DateTime($dado['dtCadastro']);
                $newArray[$key]['item'] = $dado['item'];
                $newArray[$key]['dtCadastro'] = $objDateTime->format('d/m/Y');
                $newArray[$key]['pcCalculo'] = $dado['pcCalculo'] . '%';
            }

            $custosVinculados = [];
            $custosVinculados['class'] = 'bordered striped';
            $custosVinculados['lines'] = $newArray;
            $custosVinculados['cols'] = [
                'item' => ['name' => 'Item'],
                'dtCadastro' => ['name' => 'Data', 'class' => 'valig'],
                'pcCalculo' => ['name' => 'Percentual']
            ];

            $proposta['tbcustosvinculados'] = $custosVinculados;
        }

        $proposta['tbplanilhaproposta'] = $this->montarPlanilhaProposta(
            $proposta['tbplanilhaproposta']
        );

        $proposta['tbdetalhaplanodistribuicao'] = $this->montarArrayDetalhamentoPlanoDistribuicao(
            $proposta['tbdetalhaplanodistribuicao']
        );

        $arrayPreProjeto = array_merge(
            $proposta['responsabilidadesocial'],
            $proposta['detalhestecnicos'],
            $proposta['outrasinformacoes'],
            $proposta['identificacaoproposta']
        );

        return array_merge($arrayPreProjeto, $proposta);
    }

    public function montarPlanilhaProposta($planilhaOrcamentaria)
    {
        $planilha = array();
        $count = 0;
        $i = 1;

        foreach ($planilhaOrcamentaria as $item) {
            $row = [];

            $produto = !empty($item['idProduto']) ? $item['DescricaoProduto'] : html_entity_decode('Administra&ccedil;&atilde;o do Projeto');
            $fonte = $item['DescricaoRecurso'];
            $etapa = $item['DescricaoEtapa'];
            $regiao = $item['DescricaoUf'] . ' - ' . $item['DescricaoMunicipio'];

            $row["Seq"] = $i;
            $row["idPlanilhaProposta"] = $item['idPlanilhaProposta'];
            $row["Item"] = $item['DescricaoItem'];
            $row["Unidade"] = $item['DescricaoUnidade'];
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

            $planilha[$fonte]['total'] += $row["vlSolicitado"];
            $planilha[$fonte][$produto]['total'] += $row["vlSolicitado"];
            $planilha[$fonte][$produto][$etapa]['total'] += $row["vlSolicitado"];
            $planilha[$fonte][$produto][$etapa][$regiao]['total'] += $row["vlSolicitado"];
            $planilha[$fonte][$produto][$etapa][$regiao]['itens'][] = $row;

            $planilha['total'] += $row["vlSolicitado"];

            $count++;
            $i++;
        }

        return $planilha;
    }

    public function montarArrayDetalhamentoPlanoDistribuicao($detalhamentos)
    {
        return $detalhamentos;

        $arrayDetalhamentos = [];

        foreach ($detalhamentos as $key => $item) {
            $arrayDetalhamentos[$item['idPlanoDistribuicao']][$item['DescricaoUf'] . ' - ' . $item['DescricaoMunicipio']][] = $item;
        }

        return $arrayDetalhamentos;
    }

    public function obterPlanilhaPropostaCongelada($idPreProjeto, $meta = 'alterarprojeto')
    {
        if (empty($idPreProjeto) || empty($meta)) {
            return false;
        }

        $TbPreProjetoMeta = new Proposta_Model_DbTable_TbPreProjetoMeta();
        $planilha = unserialize($TbPreProjetoMeta->buscarMeta($idPreProjeto, $meta . '_tbplanilhaproposta'));

        if (empty($planilha)) {
            return false;
        }

        $planilha = TratarArray::utf8EncodeArray($planilha);
        $planilha = $this->montarPlanilhaProposta($planilha);

        return $planilha;

    }

    public function obterValorTotalPlanilhaPropostaCongelada($idPreProjeto, $meta = 'alterarprojeto')
    {
        if (empty($idPreProjeto) || empty($meta)) {
            return false;
        }

        $TbPreProjetoMeta = new Proposta_Model_DbTable_TbPreProjetoMeta();
        $planilha = unserialize($TbPreProjetoMeta->buscarMeta($idPreProjeto, $meta . '_tbplanilhaproposta'));

        if (empty($planilha)) {
            return 0;
        }

        $arrSoma = [];
        $arrSoma['vlSolicitadoOriginal'] = 0;
        $arrSoma['vlOutrasFontesPropostaOriginal'] = 0;
        $arrSoma['vlTotalPropostaOriginal'] = 0;

        foreach ($planilha as $item) {

            if ($item['FonteRecurso'] == 109) {
                $arrSoma['vlSolicitadoOriginal'] += ($item['ValorUnitario'] * $item['Quantidade'] * $item['Ocorrencia']);
            } else {
                $arrSoma['vlOutrasFontesPropostaOriginal'] += ($item['ValorUnitario'] * $item['Quantidade'] * $item['Ocorrencia']);
            }
        }
        $arrSoma['vlTotalPropostaOriginal'] = $arrSoma['vlSolicitadoOriginal'] + $arrSoma['vlOutrasFontesPropostaOriginal'];

        return $arrSoma;
    }

    public function obterPlanilhaPropostaAtual($idPreProjeto)
    {

        if (empty($idPreProjeto)) {
            return false;
        }

        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $planilha = $tbPlanilhaProposta->buscarPlanilhaCompleta($idPreProjeto);

        if (empty($planilha)) {
            return false;
        }

        $planilha = TratarArray::utf8EncodeArray($planilha);
        $planilha = $this->montarPlanilhaProposta($planilha);

        return $planilha;
    }

    public function obterPlanilhaAdequacao($idPreProjeto, $idPronac = null)
    {

        if (empty($idPreProjeto)) {
            return false;
        }

        if (empty($idPronac)) {
            $dbTableProjetos = new Projeto_Model_DbTable_Projetos();
            $projeto = $dbTableProjetos->findBy(array(
                'idProjeto' => $idPreProjeto
            ));

            $idPronac = $projeto->IdPRONAC;
        }


        $tbAvaliacao = new Analise_Model_DbTable_TbAvaliarAdequacaoProjeto();
        $avaliacao = $tbAvaliacao->buscarUltimaAvaliacao($idPronac);

        $planilha = [];
        if (!empty($avaliacao)) {
            $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
            $planilha = $preProjetoMapper->obterPlanilhaPropostaAtual($idPreProjeto);
        }

        return $planilha;
    }

    public function obterPlanilhaOriginal($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
        $planilha = $preProjetoMapper->obterPlanilhaPropostaCongelada($idPreProjeto);

        if (empty($planilha)) {
            $preProjetoMapper = new Proposta_Model_PreProjetoMapper();
            $planilha = $preProjetoMapper->obterPlanilhaPropostaAtual($idPreProjeto);
        }

        return $planilha;
    }
}
