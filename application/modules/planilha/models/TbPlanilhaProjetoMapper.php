<?php

class Planilha_Model_TbPlanilhaProjetoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Planilha_Model_DbTable_TbPlanilhaProjeto');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function atualizarCustosVinculadosDaTbPlanilhaProjeto($idPronac)
    {
        if (empty($idPronac)) {
            throw new Exception('idPronac é obrigatório');
        }

        $itens = $this->obterItensCustosVinculadosParaTbPlanilhaProjeto($idPronac);
        foreach ($itens as $item) {

            $modelTbPlanilhaProjeto = new Planilha_Model_TbPlanilhaProjeto($item);
            $itemPlanilhaProjeto = $this->findBy(
                [
                    'idPRONAC' => $idPronac,
                    'idPlanilhaItem' => $item['idPlanilhaItem']
                ]
            );

            if (!empty($itemPlanilhaProjeto)) {
                $modelTbPlanilhaProjeto->setIdPlanilhaProjeto($itemPlanilhaProjeto['idPlanilhaProjeto']);
                $modelTbPlanilhaProjeto->setIdPlanilhaProposta($itemPlanilhaProjeto['idPlanilhaProposta']);
                $modelTbPlanilhaProjeto->setIdParecer($itemPlanilhaProjeto['idParecer']);
            }

            $this->save($modelTbPlanilhaProjeto);
        }

        return true;
    }

    private function obterItensCustosVinculadosParaTbPlanilhaProjeto($idPronac)
    {
        if (empty($idPronac)) {
            return [];
        }

        $itensCustosVinculados = $this->obterValoresCustosVinculadosPlanilhaProjeto($idPronac);
        if (empty($itensCustosVinculados)) {
            return [];
        }

        $dados = [];
        foreach ($itensCustosVinculados as $item) {
            $dados[] = array(
                'idPRONAC' => $idPronac,
                'idProduto' => 0,
                'idEtapa' => $item['idPlanilhaEtapa'],
                'idPlanilhaItem' => $item['idPlanilhaItens'],
                'Descricao' => '',
                'idUnidade' => 1,
                'Quantidade' => '1',
                'Ocorrencia' => '1',
                'ValorUnitario' => $item['valorUnitario'],
                'QtdeDias' => '1',
                'TipoDespesa' => '0',
                'TipoPessoa' => '0',
                'Contrapartida' => '0',
                'FonteRecurso' => Mecanismo::INCENTIVO_FISCAL_FEDERAL,
                'UfDespesa' => $item['idUF'],
                'MunicipioDespesa' => $item['idMunicipio'],
                'Justificativa' => 'Item ajustado automaticamente pelo sistema',
                'stCustoPraticado' => 0,
                'idUsuario' => 462
            );
        }

        return $dados;
    }

    public function obterValoresCustosVinculadosPlanilhaProjeto($idPronac)
    {
        if (empty($idPronac)) {
            return [];
        }

        $projetos = new Projetos();
        $projeto = $projetos->findBy(['idPronac = ?' => $idPronac]);
        $idPreProjeto = $projeto['idProjeto'];

        $tbPlanilhaProjeto = new PlanilhaProjeto();
        $valorDoProjeto = $tbPlanilhaProjeto->somarPlanilhaProjeto(
            $idPronac,
            Mecanismo::INCENTIVO_FISCAL_FEDERAL,
            null,
            [
                'idEtapa in (?)' => [
                    Proposta_Model_TbPlanilhaEtapa::PRE_PRODUCAO,
                    Proposta_Model_TbPlanilhaEtapa::PRODUCAO,
                    Proposta_Model_TbPlanilhaEtapa::POS_PRODUCAO,
                    Proposta_Model_TbPlanilhaEtapa::ASSESSORIA_CONTABIL_E_JURIDICA,
                    Proposta_Model_TbPlanilhaEtapa::RECOLHIMENTOS
                ]
            ]
        );

        $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
        return $tbCustosVinculadosMapper->obterCustosVinculados($idPreProjeto, $valorDoProjeto['soma']);
    }
}
