<?php

namespace Application\Modules\Parecer\Service\Assinatura\AnaliseInicial\Acao;

use MinC\Assinatura\Acao\IAcaoAssinar;

class Assinar implements IAcaoAssinar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;
        $numeroDeAssinaturas = $assinatura->dbTableTbAssinatura->obterQuantidadeAssinaturasRealizadas();
        if ($numeroDeAssinaturas == 1) {
            $where = [
                'idPronac = ?' => $modeloTbAssinatura->getIdPronac(),
                'stEstado = ?' => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
                'siEncaminhamento = ?' => \TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
                'siAnalise = ?' => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_ANALISADO,
                'stPrincipal = ?' => 1,
            ];

            $dados = [
                'DtDevolucao' => \MinC_Db_Expr::date(),
                'siAnalise' => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_VALIDACAO,
                'siEncaminhamento' => \TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_PARECERISTA
            ];

            $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
            $tbDistribuirParecer->alterar($dados, $where);

            $distribuicao  = $tbDistribuirParecer->findBy($where);

            $this->finalizarOutrosProdutosDoParecerista(
                $modeloTbAssinatura->getIdPronac(),
                $distribuicao['idAgenteParecerista']
            );
        }
    }

    private function finalizarOutrosProdutosDoParecerista($idPronac, $idAgenteParecerista)
    {
        $where = [
            "t.idPronac = ?" => $idPronac,
            "t.idAgenteParecerista = ?" => $idAgenteParecerista,
            "t.stEstado = ?" => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
            "t.siEncaminhamento = ?" => \TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
            "t.siAnalise = ?" => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_FINALIZADO,
            "t.stPrincipal = ?" => 0
        ];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $produtos = $tbDistribuirParecer->dadosParaDistribuir($where);

        foreach ($produtos as $produto) {
            $where = ['idDistribuirParecer = ?' => $produto->idDistribuirParecer];
            $dados = [
                'siEncaminhamento' => \TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_PARECERISTA,
                'siAnalise' => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_VALIDACAO,
                'DtDevolucao' => \MinC_Db_Expr::date()
            ];

            $tbDistribuirParecer->alterar($dados, $where);
        }
    }
}
