<?php

class Parecer_Model_TbDistribuirParecerMapper extends MinC_Db_Mapper
{

    public function __construct()
    {
        $this->setDbTable('Parecer_Model_DbTable_TbDistribuirParecer');
    }

    public function devolverProduto($distribuicao)
    {
        $whereDistribuicaoAtual  = [];
        $whereDistribuicaoAtual["idDistribuirParecer = ?"] = $distribuicao['idDistribuirParecer'];
        $whereDistribuicaoAtual["idPRONAC = ?"] = $distribuicao['idPRONAC'];
        $whereDistribuicaoAtual["idProduto = ?"] = $distribuicao['idProduto'];
        $whereDistribuicaoAtual["idAgenteParecerista = ?"] = $distribuicao['idAgenteParecerista'];
        $whereDistribuicaoAtual["stEstado = ?"] = Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO;
        $tbDistribuirParecer = new Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicaoAtual = $tbDistribuirParecer->findBy($whereDistribuicaoAtual);

        if (empty($distribuicaoAtual)) {
            throw new Exception("Não encontramos a distribuição para o produto informado");
        }

        try {
            $tbDistribuirParecer->getAdapter()->beginTransaction();
            $dados = array(
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
                'DtRetorno' => null,
                'FecharAnalise' => Parecer_Model_TbDistribuirParecer::FECHAR_ANALISE_EM_VALIDACAO, // @todo confirmar com o Romulo
                'stEstado' => Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
                'siEncaminhamento' => TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_PARA_ANALISE_PELO_MINC,
                'siAnalise' => Parecer_Model_TbDistribuirParecer::SI_ANALISE_AGUARDANDO_ANALISE
            );

            $tbDistribuirParecer->alterar(
                ['stEstado' => Parecer_Model_TbDistribuirParecer::ST_ESTADO_INATIVO],
                ['idDistribuirParecer = ?' => $distribuicao['idDistribuirParecer']]
            );

            $response = $tbDistribuirParecer->inserir($dados);
            $tbDistribuirParecer->getAdapter()->commit();
            return $response;
        } catch (Zend_Db_Exception $e) {
            $tbDistribuirParecer->getAdapter()->rollBack();
            throw $e;
        }
    }

    public function distribuirProduto($distribuicao)
    {

    }



}
