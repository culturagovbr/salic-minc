<?php

class Proposta_Model_TbCustosVinculadosMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Proposta_Model_DbTable_TbCustosVinculados');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    /**
     * Os custos vinculados são valores calculados automaticamente baseados no valor do projeto ou sobre o custo do projeto
     *
     * @param $idPreProjeto
     * @return array
     */
    public function obterCustosVinculados($idPreProjeto, $valorDoProjeto = 0)
    {
        if (empty($idPreProjeto)) {
            return [];
        }

        $modelCustosVinculados = new Proposta_Model_TbCustosVinculados();

        $whereCustosVinculados = [
            'a.idPlanilhaItens not in (?)' => array(
                $modelCustosVinculados::ID_DIREITOS_AUTORAIS,
                $modelCustosVinculados::ID_CONTROLE_E_AUDITORIA
            ),
            'a.idPlanilhaEtapa in (?)' => array(
                Proposta_Model_TbPlanilhaEtapa::CUSTOS_VINCULADOS,
                Proposta_Model_TbPlanilhaEtapa::CAPTACAO_DE_RECURSOS
            ),
        ];

        $tbItensPlanilhaProduto = new tbItensPlanilhaProduto();
        $itensCustosVinculadosERemuneracao = $tbItensPlanilhaProduto->buscarItens(
            $whereCustosVinculados,
            null,
            null,
            Zend_DB::FETCH_ASSOC
        );

        $percentualRemuneracaoCaptacao = $modelCustosVinculados::PERCENTUAL_PADRAO_REMUNERACAO_CAPTACAO_DE_RECURSOS;
        $limiteRemuneracaoCaptacao = $modelCustosVinculados::LIMITE_PADRAO_CAPTACAO_DE_RECURSOS_IN_2019;

        $tbProjetoFase = new Projeto_Model_DbTable_TbProjetoFase();
        if ($tbProjetoFase->isNormativo2019ByIdPreProjeto($idPreProjeto)) {
            $localizacao = $this->obterMunicipioUF($idPreProjeto);
            $idUFLocalizacao = $localizacao['idUFLocalizacao'];
            $idMunicipioLocalizacao = $localizacao['idMunicipioLocalizacao'];
        } else {
            $limites = $this->obterLimitesDeRemuneracaoCaptacao2017($idPreProjeto);
            $percentualRemuneracaoCaptacao = $limites['percentualRemuneracaoCaptacao'];
            $limiteRemuneracaoCaptacao = $limites['limiteRemuneracaoCaptacao'];
            $idUFLocalizacao = $limites['idUFLocalizacao'];
            $idMunicipioLocalizacao = $limites['idMunicipioLocalizacao'];
        }

        $percentualDivulgacao = $modelCustosVinculados::PERCENTUAL_DIVULGACAO_ATE_VALOR_LIMITE;
        if ($valorDoProjeto > $modelCustosVinculados::VALOR_LIMITE_DIVULGACAO) {
            $percentualDivulgacao = $modelCustosVinculados::PERCENTUAL_DIVULGACAO_MAIOR_QUE_VALOR_LIMITE;
        }

        $custosVinculados = array();
        foreach ($itensCustosVinculadosERemuneracao as $item) {
            switch ($item['idPlanilhaItens']) {
                case $modelCustosVinculados::ID_CUSTO_ADMINISTRATIVO:
                    $item['percentualPadrao'] = $modelCustosVinculados::PERCENTUAL_CUSTO_ADMINISTRATIVO;
                    break;
                case $modelCustosVinculados::ID_DIVULGACAO:
                    $item['percentualPadrao'] = $percentualDivulgacao;
                    break;
                case $modelCustosVinculados::ID_REMUNERACAO_CAPTACAO:
                    $item['percentualPadrao'] = $percentualRemuneracaoCaptacao;
                    $item['limitePadrao'] = $limiteRemuneracaoCaptacao;
                    break;
            }

            $custoVinculadoProponente = $this->findBy(
                array(
                    'idProjeto' => $idPreProjeto,
                    'idPlanilhaItem' => $item['idPlanilhaItens']
                )
            );

            if ($custoVinculadoProponente) {
                $item['percentualProponente'] = (float) $custoVinculadoProponente['pcCalculo'];
                $item['idCustosVinculados'] = $custoVinculadoProponente['idCustosVinculados'];
            }

            if (!isset($item['percentualProponente']) || $item['percentualProponente'] > $item['percentualPadrao']) {
                $item['percentualProponente'] = $item['percentualPadrao'];
            }

            $item['idUF'] = !empty($idUFLocalizacao) ? $idUFLocalizacao : 1;
            $item['idMunicipio'] = !empty($idMunicipioLocalizacao) ? $idMunicipioLocalizacao : 1;

            $custosVinculados[$item['idPlanilhaItens']] = $item;
        }

        $custosVinculados = $this->obterValoresDosItens($custosVinculados, $valorDoProjeto);

        return $custosVinculados;
    }

    private function obterLimitesDeRemuneracaoCaptacao2017($idPreProjeto)
    {
        $modelCustosVinculados = new Proposta_Model_TbCustosVinculados();

        $estadosPercentualRemuneracao10 = ['RJ', 'SP'];

        $estadosPercentualRemuneracao12 = [
            'SC', 'RS', 'PR', 'ES', 'MG'
        ];

        $estadosPercentualRemuneracao15 = [
            'AC', 'AP', 'AM', 'PA', 'RO', 'RR', 'TO',
            'AL', 'BA', 'CE', 'MA', 'PB', 'PE', 'PI', 'RN', 'SE',
            'DF', 'GO', 'MT', 'MS'
        ];

        $tbPlanoDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $localizacoesProposta = $tbPlanoDistribuicao->obterUfsMunicipiosDoDetalhamento($idPreProjeto);

        $percentualRemuneracaoCaptacao = $modelCustosVinculados::PERCENTUAL_PADRAO_REMUNERACAO_CAPTACAO_DE_RECURSOS;
        $limiteRemuneracaoCaptacao = $modelCustosVinculados::LIMITE_PADRAO_CAPTACAO_DE_RECURSOS;

        $idUFLocalizacao = null;
        $idMunicipioLocalizacao = null;
        foreach ($localizacoesProposta as $localizacao) {
            if (in_array($localizacao->UF, $estadosPercentualRemuneracao12)) {
                $percentualRemuneracaoCaptacao = $modelCustosVinculados::PERCENTUAL_UFS_RS_PR_SC_MG_ES_REMUNERACAO_CAPTACAO_DE_RECURSOS;
                $limiteRemuneracaoCaptacao = $modelCustosVinculados::LIMITE_UFS_RS_PR_SC_MG_ES;
                $idUFLocalizacao = $localizacao->idUF;
                $idMunicipioLocalizacao = $localizacao->idMunicipio;
            }

            if (in_array($localizacao->UF, $estadosPercentualRemuneracao10)) {
                $percentualRemuneracaoCaptacao = $modelCustosVinculados::PERCENTUAL_PADRAO_REMUNERACAO_CAPTACAO_DE_RECURSOS;
                $limiteRemuneracaoCaptacao = $modelCustosVinculados::LIMITE_PADRAO_CAPTACAO_DE_RECURSOS;
                $idUFLocalizacao = $localizacao->idUF;
                $idMunicipioLocalizacao = $localizacao->idMunicipio;
                break;
            }

            if (in_array($localizacao->UF, $estadosPercentualRemuneracao15)
                && $percentualRemuneracaoCaptacao != $modelCustosVinculados::PERCENTUAL_UFS_RS_PR_SC_MG_ES_REMUNERACAO_CAPTACAO_DE_RECURSOS) {
                $percentualRemuneracaoCaptacao = $modelCustosVinculados::PERCENTUAL_REGIOES_N_NE_CO_REMUNERACAO_CAPTACAO_DE_RECURSOS;
                $limiteRemuneracaoCaptacao = $modelCustosVinculados::LIMITE_REGIOES_N_NE_CO;
                $idUFLocalizacao = $localizacao->idUF;
                $idMunicipioLocalizacao = $localizacao->idMunicipio;
            }
        }

        return [
            'limiteRemuneracaoCaptacao' => $limiteRemuneracaoCaptacao,
            'percentualRemuneracaoCaptacao' => $percentualRemuneracaoCaptacao,
            'idUFLocalizacao' => $idUFLocalizacao,
            'idMunicipioLocalizacao' => $idMunicipioLocalizacao
        ];
    }

    private function obterMunicipioUF($idPreProjeto)
    {
        $tbPlanoDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $localizacao = $tbPlanoDistribuicao->obterUfsMunicipiosDoDetalhamento($idPreProjeto)->current();

        return [
            'idUFLocalizacao' => $localizacao->idUF,
            'idMunicipioLocalizacao' => $localizacao->idMunicipio
        ];
    }

    public function obterCustosVinculadosReadequacao($idPronac, $idReadequacao = '')
    {
        if (!$idPronac) {
            return;
        }

        $projetos = new Projetos();
        $projeto = $projetos->buscar(['idPronac = ?' => $idPronac])->current();
        $idPreProjeto = $projeto['idProjeto'];

        $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
        $custosVinculados = $tbCustosVinculadosMapper->obterCustosVinculadosPlanilhaProposta($idPreProjeto);

        $readequacaoModelDbTable = new Readequacao_Model_DbTable_TbReadequacao();
        if ($idReadequacao == '') {
            $idReadequacao = $readequacaoModelDbTable->buscarIdReadequacaoAtiva(
                $idPronac,
                Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA
            );
        }
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $itensEmReadequacao = $tbPlanilhaAprovacao->obterPlanilhaReadequacao($idReadequacao);

        $totalParaDivulgacaoAdministracao = 0;

        $etapasSomaDivulgacaoAdministracao = [
            PlanilhaEtapa::ETAPA_PRE_PRODUCAO_PREPARACAO,
            PlanilhaEtapa::ETAPA_PRODUCAO_EXECUCAO,
            PlanilhaEtapa::ETAPA_POS_PRODUCAO,
            PlanilhaEtapa::ETAPA_ASSESORIA_CONTABIL_JURIDICA,
            PlanilhaEtapa::ETAPA_RECOLHIMENTOS
        ];

        foreach ($itensEmReadequacao as $item) {
            if (in_array($item->idEtapa, $etapasSomaDivulgacaoAdministracao)
                && $item->tpAcao != 'E'
                && $item->nrFonteRecurso == Mecanismo::INCENTIVO_FISCAL_FEDERAL
            ) {
                $totalParaDivulgacaoAdministracao += $item->vlUnitario * $item->qtItem * $item->nrOcorrencia;
            }
        }

        return $this->obterCustosVinculados($idPreProjeto, $totalParaDivulgacaoAdministracao);
    }

    public function obterValoresDosItens($custosVinculados, $valorDoProjeto)
    {
        $custoAdicional = 0;
        foreach ($custosVinculados as &$item) {
            $item['valorUnitario'] = 0;

            if (!empty($valorDoProjeto)) {
                $item['valorDoProjeto'] = $valorDoProjeto;
                $item['valorUnitario'] = ($valorDoProjeto * ($item['percentualProponente'] / 100));

                if (isset($item['limitePadrao']) && $item['valorUnitario'] > $item['limitePadrao']) {
                    $item['valorUnitario'] = $item['limitePadrao'];
                }

                if ($item['idPlanilhaItens'] == Proposta_Model_TbCustosVinculados::ID_CUSTO_ADMINISTRATIVO
                    || ($item['idPlanilhaItens'] == Proposta_Model_TbCustosVinculados::ID_DIVULGACAO)
                ) {
                    $custoAdicional += $item['valorUnitario'];
                }
            }
        }

        $idRemuneracao = Proposta_Model_TbCustosVinculados::ID_REMUNERACAO_CAPTACAO;
        $limitePadrao = $custosVinculados[$idRemuneracao]['limitePadrao'];
        $valorRemuneracaoCaptacao = (($valorDoProjeto + $custoAdicional) * ($item['percentualProponente'] / 100));

        if (!empty($limitePadrao) && $valorRemuneracaoCaptacao > $limitePadrao) {
            $valorRemuneracaoCaptacao = $limitePadrao;
        }
        $custosVinculados[$idRemuneracao]['valorUnitario'] = $valorRemuneracaoCaptacao;

        return $custosVinculados;
    }

    public function obterCustosVinculadosPlanilhaProposta($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return [];
        }

        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $valorDoProjeto = $tbPlanilhaProposta->somarPlanilhaPropostaPorEtapa(
            $idPreProjeto,
            Mecanismo::INCENTIVO_FISCAL_FEDERAL,
            null,
            [
                'idPlanilhaEtapa in (?)' => [
                    Proposta_Model_TbPlanilhaEtapa::PRE_PRODUCAO,
                    Proposta_Model_TbPlanilhaEtapa::PRODUCAO,
                    Proposta_Model_TbPlanilhaEtapa::POS_PRODUCAO,
                    Proposta_Model_TbPlanilhaEtapa::ASSESSORIA_CONTABIL_E_JURIDICA,
                    Proposta_Model_TbPlanilhaEtapa::RECOLHIMENTOS
                ]
            ]
        );

        $custosVinculados = $this->obterCustosVinculados($idPreProjeto, $valorDoProjeto);

        return $custosVinculados;
    }

    public function salvarCustosVinculadosDaTbPlanilhaProposta($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            throw new Exception('idPreProjeto &eacute; obrigat&oacute;rio');
        }

        $this->removerCustosVinculadosPropostaLegada($idPreProjeto);

        $modelPlanilhaProposta = new Proposta_Model_TbPlanilhaProposta();
        $tbPlanilhaPropostaMapper = new Proposta_Model_TbPlanilhaPropostaMapper();

        $itens = $this->montarItensCustosVinculadosParaTbPlanilhaProposta($idPreProjeto);
        foreach ($itens as $item) {

            $modelPlanilhaProposta->setOptions($item);
            $itemPlanilhaProposta = $tbPlanilhaPropostaMapper->findBy(
                [
                    'idProjeto' => $idPreProjeto,
                    'idPlanilhaItem' => $item['idPlanilhaItem']
                ]
            );

            if (!empty($itemPlanilhaProposta)) {
                $modelPlanilhaProposta->setIdPlanilhaProposta($itemPlanilhaProposta['idPlanilhaProposta']);
            }

            $tbPlanilhaPropostaMapper->save($modelPlanilhaProposta);
        }

        $this->atualizarCustosVinculados($idPreProjeto);
    }

    private function montarItensCustosVinculadosParaTbPlanilhaProposta($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return [];
        }

        $itensCustosVinculados = $this->obterCustosVinculadosPlanilhaProposta($idPreProjeto);

        if (empty($itensCustosVinculados)) {
            return [];
        }

        $dados = [];
        foreach ($itensCustosVinculados as $item) {

            $dados[] = array(
                'idProjeto' => $idPreProjeto,
                'idProduto' => 0,
                'idEtapa' => $item['idPlanilhaEtapa'],
                'idPlanilhaItem' => $item['idPlanilhaItens'],
                'Descricao' => '',
                'Unidade' => '15',
                'Quantidade' => '1',
                'Ocorrencia' => '1',
                'ValorUnitario' => $item['valorUnitario'],
                'QtdeDias' => '1',
                'TipoDespesa' => '0',
                'TipoPessoa' => '0',
                'contraPartida' => '0',
                'FonteRecurso' => Mecanismo::INCENTIVO_FISCAL_FEDERAL,
                'UfDespesa' => $item['idUF'],
                'MunicipioDespesa' => $item['idMunicipio'],
                'dsJustificativa' => 'Item or&ccedil;ament&aacute;rio recalculado automaticamente conforme o percentual solicitado pelo proponente',
                'stCustoPraticado' => 0,
                'idUsuario' => 462
            );
        }

        return $dados;
    }

    public function atualizarCustosVinculados($idPreProjeto)
    {
        $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
        $custosVinculados = $tbCustosVinculadosMapper->obterCustosVinculadosPlanilhaProposta($idPreProjeto);

        $auth = Zend_Auth::getInstance();
        $idUsuario = $auth->getIdentity()->IdUsuario;

        foreach ($custosVinculados as $key => $item) {

            $dados = array(
                'idCustosVinculados' => $item['idCustosVinculados'],
                'idProjeto' => $idPreProjeto,
                'idPlanilhaItem' => $item['idPlanilhaItens'],
                'dtCadastro' => new Zend_Db_Expr('getdate()'),
                'pcCalculo' => $item['percentualProponente'],
                'idUsuario' => $idUsuario
            );

            $this->save(new Proposta_Model_TbCustosVinculados($dados));
        }
    }

    /**
     * Essa pesquisa e exclus�o dos custos vinculados removidos poder� ser retirada futuramente.
     * Foi feita apenas para propostas com custos vinculados que j� existiam antes da nova IN
     */
    private function removerCustosVinculadosPropostaLegada($idPreProjeto)
    {
        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

        $whereCustosVinculadosRemovidos = [
            'idProjeto = ?' => $idPreProjeto,
            'idProduto = ?' => 0,
            'idPlanilhaItem in (?)' => [
                Proposta_Model_TbCustosVinculados::ID_CONTROLE_E_AUDITORIA,
                Proposta_Model_TbCustosVinculados::ID_DIREITOS_AUTORAIS
            ]
        ];

        $custosVinculadosRemovidos = $tbPlanilhaProposta->findBy($whereCustosVinculadosRemovidos);

        if (!empty($custosVinculadosRemovidos)) {
            $tbPlanilhaProposta->delete($whereCustosVinculadosRemovidos);
        }

    }

}
