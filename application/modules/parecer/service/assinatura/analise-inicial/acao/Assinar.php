<?php

namespace Application\Modules\Parecer\Service\Assinatura\AnaliseInicial\Acao;

use MinC\Assinatura\Acao\IAcaoAssinar;

class Assinar implements IAcaoAssinar
{
    private $numeroDeAssinaturas;
    private $idProximoOrgao;

    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $this->numeroDeAssinaturas = $assinatura->dbTableTbAssinatura->obterQuantidadeAssinaturasRealizadas();

        $this->idProximoOrgao = $this->obterProximoOrgao($assinatura);

        if ($this->numeroDeAssinaturas == 1) {
           return $this->finalizarAnaliseInicial($assinatura);
        }

        return $this->assinarAnaliseCoordenador($assinatura);
    }

    private function finalizarAnaliseInicial(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;
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
            'siEncaminhamento' => \TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_PARECERISTA,
            'idOrgao' => $this->idProximoOrgao,
        ];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicao = $tbDistribuirParecer->findBy($where);

        $tbDistribuirParecer->alterar($dados, $where);

        $this->finalizarOutrosProdutosDoParecerista(
            $modeloTbAssinatura->getIdPronac(),
            $distribuicao['idAgenteParecerista']
        );
    }

    private function finalizarOutrosProdutosDoParecerista($idPronac, $idAgenteParecerista)
    {
        $where = [
            "idPronac = ?" => $idPronac,
            "idAgenteParecerista = ?" => $idAgenteParecerista,
            "stEstado = ?" => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
            "siEncaminhamento = ?" => \TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_PARECERISTA,
            "siAnalise = ?" => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_ANALISADO,
            "stPrincipal = ?" => 0
        ];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $produtos = $tbDistribuirParecer->findAll($where);

        foreach ($produtos as $produto) {
            $where = ['idDistribuirParecer = ?' => $produto['idDistribuirParecer']];
            $dados = [
                'siEncaminhamento' => \TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_PARECERISTA,
                'siAnalise' => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_VALIDACAO,
                'DtDevolucao' => \MinC_Db_Expr::date(),
                'idOrgao' => $this->idProximoOrgao
            ];

            $tbDistribuirParecer->alterar($dados, $where);
        }
    }

    private function assinarAnaliseCoordenador(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;
        $where = [
            'idPronac = ?' => $modeloTbAssinatura->getIdPronac(),
            'stEstado = ?' => \Parecer_Model_TbDistribuirParecer::ST_ESTADO_ATIVO,
            'siEncaminhamento = ?' => \TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_PARECERISTA,
        ];

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $distribuicaoAtual = $tbDistribuirParecer->findBy($where);

        $dados = [
            'siAnalise' => \Parecer_Model_TbDistribuirParecer::SI_ANALISE_EM_VALIDACAO,
            'idOrgao' => $this->idProximoOrgao
        ];

        if ($distribuicaoAtual['idOrgao'] == $this->idProximoOrgao) {
            $dados['siEncaminhamento'] =  \TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_MINC_PELA_UNIDADE;
        }

        $tbDistribuirParecer->alterar($dados, $where);
    }

    private function obterProximoOrgao(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $tbAtoAdministrativo = new \Assinatura_Model_DbTable_TbAtoAdministrativo();
        $grupoAtoAdministrativo = $tbAtoAdministrativo->obterGrupoPorIdDocumentoAssinatura(
            $assinatura->modeloTbAssinatura->getIdDocumentoAssinatura()
        );

        return $tbAtoAdministrativo->obterProximoOrgaoDeDestino(
            $assinatura->modeloTbAtoAdministrativo->getIdTipoDoAto(),
            $assinatura->modeloTbAtoAdministrativo->getIdOrdemDaAssinatura(),
            $assinatura->modeloTbAtoAdministrativo->getIdOrgaoSuperiorDoAssinante(),
            $grupoAtoAdministrativo
        );
    }
}
