<?php

class Parecer_Model_TbDistribuirParecerMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Parecer_Model_DbTable_TbDistribuirParecer');
    }

    public function devolverProduto($distribuicao)
    {
        return $this->inserirDistribuicaoProduto($distribuicao);
    }

    public function distribuirProdutoParaParecerista($distribuicao)
    {
        $dados = array_merge($distribuicao, [
            'DtDistribuicao' => MinC_Db_Expr::date(),
            'siEncaminhamento' => TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
            'siAnalise' => Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE
        ]);

        return $this->inserirDistribuicaoProduto($dados);
    }

    public function encaminharProdutoParaVinculada($distribuicao)
    {
        $dados = array_merge($distribuicao, [
            'idAgenteParecerista' => null,
            'DtEnvio' => MinC_Db_Expr::date(),
            'DtDistribuicao' => null,
            'siEncaminhamento' => TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_PARA_ANALISE_PELO_MINC,
            'siAnalise' => Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE
        ]);

        $this->inserirDistribuicaoProduto($dados);
    }

    private function inserirDistribuicaoProduto($distribuicao)
    {
        try {
            $dados = array_merge($distribuicao, [
                'stEstado' => Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
            ]);

            $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
            $tbDistribuirParecer->getAdapter()->beginTransaction();
            $tbDistribuirParecer->alterar(
                ['stEstado' => Parecer_Model_TbDistribuirParecer::ST_ESTADO_INATIVO],
                ['idDistribuirParecer = ?' => $distribuicao['idDistribuirParecer']]
            );

            $response = $tbDistribuirParecer->inserir($dados);
            $tbDistribuirParecer->getAdapter()->commit();
            return $response;
        } catch (\Exception $e) {
            $tbDistribuirParecer->getAdapter()->rollBack();
            throw $e;
        }
    }
}
