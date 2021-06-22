<?php

namespace Application\Modules\ComprovacaoObjeto\Service\Assinatura\Acao;

use Application\Modules\AvaliacaoResultados\Service\Fluxo\Estado as EstadoService;

use MinC\Assinatura\Acao\IAcaoFinalizar;

class Finalizar implements IAcaoFinalizar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;
        $tbCumprimentoObjeto = new \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $objeto = $tbCumprimentoObjeto->findBy([
            'idPronac = ?' => $modeloTbAssinatura->getIdPronac(),
            'siCumprimentoObjeto = ?' => \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_FINALIZADO_PELO_COORDENADOR
        ]);

        $situacao = \Projeto_Model_Situacao::AGUARDA_ANALISE_FINANCEIRA;
        $providenciaTomada = 'Projeto encaminhado para avalia&ccedil;&atilde;o financeira';

        if ($objeto['stResultadoAvaliacao'] == \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::OBJETO_REPROVADO) {
            $situacao = \Projeto_Model_Situacao::AGUARDANDO_REVISAO_DE_RESULTADOS;
            $providenciaTomada = 'Projeto encaminhado para revis&atilde;o da avalia&ccedil;&atilde;o de  resultados';
            $idPronac = $objeto['idPronac'];
            $proximoEstado = 10;

            $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
            $dadosProjeto = $objTbProjetos->findBy(array(
                'IdPRONAC' => $modeloTbAssinatura->getIdPronac()
            ));

            $objOrgaos = new \Orgaos();
            $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);

            if ((int)$dadosOrgaoSuperior['Codigo'] == (int)\Orgaos::ORGAO_SUPERIOR_SEFIC) {
                $idOrgaoDestino = (int)\Orgaos::ORGAO_SEFIC_ARQ_CGEPC;
            } elseif ((int)$dadosOrgaoSuperior['Codigo'] == (int)\Orgaos::ORGAO_SUPERIOR_SAV) {
                $idOrgaoDestino = (int)\Orgaos::ORGAO_SAV_CEP;
            }

            if (isset($idPronac)) {
                $estadoService = new EstadoService();
                $estadoService->alterarEstado(
                    [
                        'idPronac' => $idPronac,
                        'proximo' => $proximoEstado,
                        'idOrgaoDestino' => $idOrgaoDestino,
                        'cdGruposDestino' => \Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
                        'idAgenteDestino' => $objeto['idTecnicoAvaliador']

                    ]
                );
            }
        }

        $tbProjetos = new \Projetos();
        $tbProjetos->alterarSituacao($modeloTbAssinatura->getIdPronac(), '', $situacao, $providenciaTomada);
    }

}
