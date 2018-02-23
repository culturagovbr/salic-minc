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
        $preProjeto = $tblPreProjeto->buscar(array('idPreProjeto = ?' => $idPreProjeto))->current()->toArray();

        /**
         * devido ao tamanho da tabela preprojeto separamos em partes
         */
        # responsabilidade social (preprojeto)
        $proposta['responsabilidadesocial'] = array(
            'Acessibilidade' => $preProjeto['Acessibilidade'],
            'DemocratizacaoDeAcesso' => $preProjeto['DemocratizacaoDeAcesso'],
            'ImpactoAmbiental' => $preProjeto['ImpactoAmbiental']
        );

        # detalhes t�cnicos (preprojeto)
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

        # Local de realizacao (abrangencia)
        $tbAbrangencia = new Proposta_Model_DbTable_Abrangencia();
        $proposta['abrangencia'] = $tbAbrangencia->buscar(['idProjeto' => $idPreProjeto]);

        # Deslocamento
        $tbDeslocamento = new Proposta_Model_DbTable_TbDeslocamento();
        $proposta['deslocamento'] = $tbDeslocamento->buscarDeslocamentosGeral(['idProjeto' => $idPreProjeto]);

        # Plano distribuicao
        $tbPlanoDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $proposta['planodistribuicaoproduto'] = $tbPlanoDistribuicao->buscar(['idProjeto = ?' => $idPreProjeto])->toArray();

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
        $proposta = TratarArray::prepararArrayMultiParaJson($proposta);

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
                $planilha[$row['FonteRecurso']][$produto][$row['Etapa']][$row['UF'] . ' - '
                . $row['Municipio']][$count][$cel] = $val;
            }
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
            $arrayDetalhamentos[$item['idPlanoDistribuicao']][$item['DescricaoUf'] . ' - '. $item['DescricaoMunicipio']][] = $item;
        }

        return $arrayDetalhamentos;
    }

}
