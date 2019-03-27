<?php

class Proposta_Model_TbDetalhaPlanoDistribuicaoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Proposta_Model_DbTable_TbDetalhaPlanoDistribuicao');
    }

    public function salvar(Proposta_Model_TbDetalhaPlanoDistribuicao $model, $idPreProjeto)
    {
        $idPlanoDistribuicao = $model->getIdPlanoDistribuicao();

        if (empty($idPlanoDistribuicao) || empty($idPreProjeto)) {
            throw new Exception("Dados obrigat&oacute;rios n&atilde;o informados");
        }

        $id = parent::save($model);

        if ($id) {
            $tblPlanoDistribuicao = new PlanoDistribuicao();
            $tblPlanoDistribuicao->updateConsolidacaoPlanoDeDistribuicao($idPlanoDistribuicao);

            $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
            $tbCustosVinculadosMapper->salvarCustosVinculadosDaTbPlanilhaProposta($idPreProjeto);
        }

        return $id;
    }

    public function excluirDetalhamento($idDetalhaPlanoDistribuicao, $idPlanoDistribuicao, $idPreProjeto)
    {
        if (empty($idDetalhaPlanoDistribuicao)) {
            throw new Exception("ID do detalhamento &eacute; obrigat&oacute;rio");
        }

        if (empty($idPlanoDistribuicao)) {
            throw new Exception("ID do Produto &eacute; obrigat&oacute;rio");
        }

        if (empty($idPreProjeto)) {
            throw new Exception("ID do projeto &eacute; obrigat&oacute;rio");
        }

        $detalhamento = new Proposta_Model_DbTable_TbDetalhaPlanoDistribuicao();
        $response = $detalhamento->excluir($idDetalhaPlanoDistribuicao);

        if ($response) {
            $tblPlanoDistribuicao = new PlanoDistribuicao();
            $tblPlanoDistribuicao->updateConsolidacaoPlanoDeDistribuicao($idPlanoDistribuicao);

            $tbCustosVinculadosMapper = new Proposta_Model_TbCustosVinculadosMapper();
            $tbCustosVinculadosMapper->salvarCustosVinculadosDaTbPlanilhaProposta($idPreProjeto);
        }

        return $response;
    }

    public function excluirDetalhamentosPorLocalizacao($idPreProjeto, $idUf, $idMunicipio)
    {
        if (empty($idPreProjeto) || empty($idUf) || empty($idMunicipio)) {
            throw new Exception("Dados obrigat&oacute;rios n&atilde;o informados!");
        }

        $detalhamento = new Proposta_Model_DbTable_TbDetalhaPlanoDistribuicao();
        $response = $detalhamento->deleteDetalhamentoByLocalizacao($idPreProjeto,  $idUf, $idMunicipio);

        $tblPlanoDistribuicao = new PlanoDistribuicao();
        $distribuicoes = $tblPlanoDistribuicao->findAll(['idProjeto = ?' => $idPreProjeto]);
        foreach ($distribuicoes as $distribuicao) {
            $tblPlanoDistribuicao->updateConsolidacaoPlanoDeDistribuicao($distribuicao['idPlanoDistribuicao']);
        }

        return $response;
    }
}
