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

    public function obterCustosVinculados($idPreProjeto)
    {
        if (empty($idPreProjeto)) {
            return [];
        }

        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $valorDoProjeto = $tbPlanilhaProposta->somarPlanilhaPropostaPorEtapa(
            $idPreProjeto,
            Mecanismo::INCENTIVO_FISCAL,
            null,
            [
                'e.tpCusto = ?' => 'P',
                'e.tpGrupo = ?' => 'A',
            ]
        );

        $ModelCustosVinculados = new Proposta_Model_TbCustosVinculados();

        $whereCustosVinculados = [
            'a.idPlanilhaItens not in (?)' => array(
                $ModelCustosVinculados::ID_DIREITOS_AUTORAIS,
                $ModelCustosVinculados::ID_CONTROLE_E_AUDITORIA
            ),
            'a.idPlanilhaEtapa in (?)' => array(
                Proposta_Model_TbPlanilhaEtapa::CUSTOS_VINCULADOS,
                Proposta_Model_TbPlanilhaEtapa::REMUNERACAO_CAPTACAO
            ),
        ];

        $tbItensPlanilhaProduto = new tbItensPlanilhaProduto();
        $itensCustosVinculadosERemuneracao = $tbItensPlanilhaProduto->buscarItens(
            $whereCustosVinculados,
            null,
            null,
            Zend_DB::FETCH_ASSOC
        );

        $wherePercentualRemuneracao10 = array('RJ', 'SP');

        $wherePercentualRemuneracao12 = array(
            'SC', 'RS', 'PR', 'ES', 'MG'
        );

        $wherePercentualRemuneracao15 = array(
            'AC', 'AP', 'AM', 'PA','RO', 'RR', 'TO',
            'AL', 'BA', 'CE', 'MA','PB', 'PE', 'PI', 'RN', 'SE',
            'DF', 'GO', 'MT', 'MS'
        );

        $tbPlanoDistribuicao = new Proposta_Model_DbTable_PlanoDistribuicaoProduto();
        $localizacoesProposta = $tbPlanoDistribuicao->obterUfsMunicipiosDoDetalhamento($idPreProjeto);

        $percentualRemuneracaoCaptacao = $ModelCustosVinculados::PERCENTUAL_PADRAO_REMUNERACAO_CAPTACAO_DE_RECURSOS;

        $idUFLocalizacao = null;
        $idMunicipioLocalizacao = null;
        foreach ($localizacoesProposta as $localizacao) {
            if (in_array($localizacao->UF, $wherePercentualRemuneracao12)) {
                $percentualRemuneracaoCaptacao = $ModelCustosVinculados::PERCENTUAL_UFS_RS_PR_SC_MG_ES_REMUNERACAO_CAPTACAO_DE_RECURSOS;
                $idUFLocalizacao = $localizacao->idUF;
                $idMunicipioLocalizacao = $localizacao->idMunicipio;
            }

            if (in_array($localizacao->UF, $wherePercentualRemuneracao10)) {
                $percentualRemuneracaoCaptacao = $ModelCustosVinculados::PERCENTUAL_PADRAO_REMUNERACAO_CAPTACAO_DE_RECURSOS;
                $idUFLocalizacao = $localizacao->idUF;
                $idMunicipioLocalizacao = $localizacao->idMunicipio;
                break;
            }

            if(in_array($localizacao->UF, $wherePercentualRemuneracao15)
                && $percentualRemuneracaoCaptacao != $ModelCustosVinculados::PERCENTUAL_UFS_RS_PR_SC_MG_ES_REMUNERACAO_CAPTACAO_DE_RECURSOS) {
                $percentualRemuneracaoCaptacao = $ModelCustosVinculados::PERCENTUAL_REGIOES_N_NE_CO_REMUNERACAO_CAPTACAO_DE_RECURSOS;
                $idUFLocalizacao = $localizacao->idUF;
                $idMunicipioLocalizacao = $localizacao->idMunicipio;
            }
        }

        $percentualDivulgacao = $ModelCustosVinculados::PERCENTUAL_DIVULGACAO_ATE_VALOR_LIMITE;
        if ($valorDoProjeto > $ModelCustosVinculados::VALOR_LIMITE_DIVULGACAO) {
            $percentualDivulgacao = $ModelCustosVinculados::PERCENTUAL_DIVULGACAO_MAIOR_QUE_VALOR_LIMITE;
        }

        $custosVinculados = array();
        foreach ($itensCustosVinculadosERemuneracao as $item) {
            switch ($item['idPlanilhaItens']) {
                case $ModelCustosVinculados::ID_CUSTO_ADMINISTRATIVO:
                    $item['percentualPadrao'] = $ModelCustosVinculados::PERCENTUAL_CUSTO_ADMINISTRATIVO;
                    break;
                case $ModelCustosVinculados::ID_DIVULGACAO:
                    $item['percentualPadrao'] = $percentualDivulgacao;
                    break;
                case $ModelCustosVinculados::ID_REMUNERACAO_CAPTACAO:
                    $item['percentualPadrao'] = $percentualRemuneracaoCaptacao;
                    $item['limitePadrao'] = $ModelCustosVinculados::LIMITE_PADRAO_CAPTACAO_DE_RECURSOS;
                    break;
            }

            $custoVinculadoProponente = $this->findBy(
                array(
                    'idProjeto' => $idPreProjeto,
                    'idPlanilhaItem' => $item['idPlanilhaItens']
                )
            );

            if ($custoVinculadoProponente) {
                $item['percentualProponente'] = $custoVinculadoProponente['pcCalculo'];
                $item['idCustosVinculados'] = $custoVinculadoProponente['idCustosVinculados'];
            }

            if (!empty($valorDoProjeto)) {
                $item['valorDoProjeto'] = $valorDoProjeto;
            }

            $item['idUF'] = !empty($idUFLocalizacao) ? $idUFLocalizacao : 1;
            $item['idMunicipio'] = !empty($idMunicipioLocalizacao) ? $idMunicipioLocalizacao : 1;

            $custosVinculados[] = $item;
        }

        return $custosVinculados;
    }

    public function salvarCustosVinculadosDaTbPlanilhaProposta($idPreProjeto)
    {

        if (empty($idPreProjeto)) {
            throw new Exception('idPreProjeto &eacute; obrigat&oacute;rio');
        }

        $itens = $this->calcularCustosVinculadosERemuneracaoPlanilhaProposta($idPreProjeto);

//        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();

//        if (array_sum(array_column($itens, 'ValorUnitario')) == 0) {
//            $tbPlanilhaProposta->excluirCustosVinculadosERemuneracaoDaPlanilha($idPreProjeto);
//            return true;
//        }

        $this->removerCustosVinculadosPropostaLegada($idPreProjeto);

        $modelPlanilhaProposta = new Proposta_Model_TbPlanilhaProposta();
        $tbPlanilhaPropostaMapper = new Proposta_Model_TbPlanilhaPropostaMapper();

        foreach ($itens as $item) {

            $modelPlanilhaProposta->setOptions($item);
            $itemPlanilhaProposta = $tbPlanilhaPropostaMapper->findBy(
                [
                    'idProjeto' => $idPreProjeto,
                    'idPlanilhaItem' => $item['idPlanilhaItem']
                ]
            );

            if(!empty($itemPlanilhaProposta)) {
                $modelPlanilhaProposta->setIdPlanilhaProposta($itemPlanilhaProposta['idPlanilhaProposta']);
            }


            $tbPlanilhaPropostaMapper->save($modelPlanilhaProposta);
        }

        $this->atualizarCustosVinculados($idPreProjeto);
    }

    public function calcularCustosVinculadosERemuneracaoPlanilhaProposta(
        $idPreProjeto,
        $valorDoProjeto = null,
        $valorCustoDoProjeto = null
    )
    {
        if (empty($idPreProjeto)) {
            return false;
        }

        $dados = array();
        $tbPlanilhaProposta = new Proposta_Model_DbTable_TbPlanilhaProposta();
        $itensCustosVinculados = $this->obterCustosVinculados($idPreProjeto);

        if (empty($itensCustosVinculados)) {
            return [];
        }

        if (empty($valorCustoDoProjeto )) {
            $valorCustoDoProjeto = $tbPlanilhaProposta->somarPlanilhaPropostaPorEtapa(
                $idPreProjeto,
                Mecanismo::INCENTIVO_FISCAL,
                null,
                ['e.idPlanilhaEtapa in (?)' => [1, 2, 7, 8]]
            );
        }

        foreach ($itensCustosVinculados as $item) {

            $percentual = $item['percentualPadrao'];

            if ($percentual['percentualProponente'] <= $item['percentualPadrao']) {
                $percentual = $item['percentualProponente'];
            }

            if(empty($valorDoProjeto)) {
                $valorDoProjeto =  $item['valorDoProjeto'];
            }

            $valorTotal = $valorDoProjeto;
            if ($item['idPlanilhaEtapa'] != Proposta_Model_TbPlanilhaEtapa::CUSTOS_VINCULADOS) {
                $valorTotal = $valorCustoDoProjeto;
            }

            $valorUnitario = ($valorTotal * ($percentual / 100));

            if (isset($item['limitePadrao']) && $valorUnitario > $item['limitePadrao']) {
                $valorUnitario = $item['limitePadrao'];
            }

            $dados[] = array(
                'idProjeto' => $idPreProjeto,
                'idProduto' => 0,
                'idEtapa' => $item['idPlanilhaEtapa'],
                'idPlanilhaItem' => $item['idPlanilhaItens'],
                'Descricao' => '',
                'Unidade' => '1',
                'Quantidade' => '1',
                'Ocorrencia' => '1',
                'ValorUnitario' => $valorUnitario,
                'QtdeDias' => '1',
                'TipoDespesa' => '0',
                'TipoPessoa' => '0',
                'contraPartida' => '0',
                'FonteRecurso' => Mecanismo::INCENTIVO_FISCAL,
                'UfDespesa' => $item['idUF'],
                'MunicipioDespesa' => $item['idMunicipio'],
                'dsJustificativa' => '',
                'stCustoPraticado' => 0,
                'idUsuario' => 462
            );
        }

        return $dados;
    }

    public function somarTotalCustosVinculados($idPreProjeto, $valorDoProjeto = null, $valorCustoDoProjeto = null)
    {
        $itens = $this->calcularCustosVinculadosERemuneracaoPlanilhaProposta($idPreProjeto, $valorDoProjeto, $valorCustoDoProjeto);

        if ($itens == 0 || empty($itens)) {
            return 0;
        }

        $soma = 0;
        if ($itens) {
            foreach ($itens as $item) {
                $soma = $item['ValorUnitario'] + $soma;
            }
        }
        return $soma;
    }

    public function atualizarCustosVinculados($idPreProjeto)
    {
        $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
        $custosVinculados = $tbCustosVinculadosMapper->obterCustosVinculados($idPreProjeto);

        $auth = Zend_Auth::getInstance();
        $idUsuario = $auth->getIdentity()->IdUsuario;

        foreach ($custosVinculados as $key => $item) {

            if($item['percentualPadrao'] < $item['percentualProponente'] || !isset($item['percentualProponente'])) {
                $item['percentualProponente'] = $item['percentualPadrao'];
            }
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
