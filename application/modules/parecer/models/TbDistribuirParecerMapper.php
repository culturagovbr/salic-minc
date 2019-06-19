<?php

class Parecer_Model_TbDistribuirParecerMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Parecer_Model_DbTable_TbDistribuirParecer');
    }

    public function devolverProduto($distribuicao)
    {
        $whereDistribuicaoAtual = [];
        $whereDistribuicaoAtual["idDistribuirParecer = ?"] = $distribuicao['idDistribuirParecer'];
        $whereDistribuicaoAtual["idPRONAC = ?"] = $distribuicao['idPRONAC'];
        $whereDistribuicaoAtual["idProduto = ?"] = $distribuicao['idProduto'];
        $whereDistribuicaoAtual["idAgenteParecerista = ?"] = $distribuicao['idAgenteParecerista'];
        $whereDistribuicaoAtual["stEstado = ?"] = Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO;
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicaoAtual = $tbDistribuirParecer->findBy($whereDistribuicaoAtual);

        if (empty($distribuicaoAtual)) {
            throw new Exception("Distribui&ccedil;&atilde;o n&atilde;o encontrada para o produto informado");
        }

        $dados = [
            'idAgenteParecerista' => $distribuicaoAtual['idAgenteParecerista'],
            'DtDistribuicao' => $distribuicaoAtual['DtDistribuicao'],
            'TipoAnalise' => $distribuicaoAtual['TipoAnalise'],
            'stPrincipal' => $distribuicaoAtual['stPrincipal'],
            'idProduto' => $distribuicaoAtual['idProduto'],
            'idPRONAC' => $distribuicaoAtual['idPRONAC'],
            'idOrgao' => $distribuicaoAtual['idOrgao'],
            'idOrgaoOrigem' => $distribuicaoAtual['idOrgaoOrigem'],
            'DtEnvio' => $distribuicaoAtual['DtEnvio'],
            'Observacao' => $distribuicao['Observacao'],
            'idUsuario' => $distribuicao['idUsuario'],
            'DtDevolucao' => \MinC_Db_Expr::date(),
            'stDiligenciado' => null,
            'DtRetorno' => null
        ];

        return $this->inserirDistribuicaoProduto(
            $dados,
            TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_PARA_ANALISE_PELO_MINC,
            Parecer_Model_TbDistribuirParecer::SI_ANALISE_AGUARDANDO_ANALISE
        );
    }

    public function distribuirProdutoParaParecerista($distribuicao)
    {
        $dados = [
            'idAgenteParecerista' => $distribuicao['idAgenteParecerista'],
            'DtEnvio' => $distribuicao['DtEnvioMincVinculada'],
            'stPrincipal' => $distribuicao['stPrincipal'],
            'Observacao' => $distribuicao['observacao'],
            'idUsuario' => $distribuicao['idUsuario'],
            'idProduto' => $distribuicao['idProduto'],
            'idPRONAC' => $distribuicao['IdPRONAC'],
            'idOrgao' => $distribuicao['idOrgao'],
            'TipoAnalise' => $distribuicao['TipoAnalise'],
            'DtDistribuicao' => MinC_Db_Expr::date()
        ];

        return $this->inserirDistribuicaoProduto(
            $dados,
            TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
            Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE
        );
    }

    public function encaminharProdutoParaVinculada($distribuicao)
    {
        $dados = [
            'idAgenteParecerista' => null,
            'DtEnvio' => MinC_Db_Expr::date(),
            'stPrincipal' => $distribuicao['stPrincipal'],
            'Observacao' => $distribuicao['observacao'],
            'idOrgao' => $distribuicao['idOrgaoDestino'],
            'idUsuario' => $distribuicao['idUsuario'],
            'idPRONAC' => $distribuicao['idPronac'],
            'idProduto' => $distribuicao['idProduto'],
            'TipoAnalise' => $distribuicao['TipoAnalise'],
            'DtDistribuicao' => null
        ];

        $this->inserirDistribuicaoProduto(
            $dados,
            TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_PARA_ANALISE_PELO_MINC,
            Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_ANALISE
        );
    }

    private function inserirDistribuicaoProduto($distribuicao, $siEncaminhamento, $siAnalise)
    {
        try {

            $dados = array_merge([
                'siEncaminhamento' => $siEncaminhamento,
                'stEstado' => Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
                'siAnalise' => $siAnalise
            ], $distribuicao);

            xd('dadosss', $dados);

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
