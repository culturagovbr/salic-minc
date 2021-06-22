<?php

namespace Application\Modules\AvaliacaoResultados\Service\Assinatura\Laudo\Acao;

use MinC\Assinatura\Acao\IAcaoFinalizar;

class Finalizar implements IAcaoFinalizar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;

        $laudo = new \AvaliacaoResultados_Model_DbTable_LaudoFinal();
        $where = [
            'idPronac' => $modeloTbAssinatura->getIdPronac()
        ];
        $laudoFinal = $laudo->findBy($where);

        $situacao = \Projeto_Model_Situacao::PC_AVALIADA_SEFIC;
        $providenciaTomada = 'Projeto encaminhado para o setor de elabora&ccedil;&atilde;o de portaria';

        $tbProjetos = new \Projetos();
        $tbProjetos->alterarSituacao($modeloTbAssinatura->getIdPronac(), '', $situacao, $providenciaTomada);

        $dadosProjeto = $tbProjetos->findBy(array(
            'IdPRONAC' => $modeloTbAssinatura->getIdPronac()
        ));

        $objOrgaos = new \Orgaos();
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);
        if ((int)$dadosOrgaoSuperior['Codigo'] == (int)\Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $orgaoDestino = (int)\Orgaos::ORGAO_GEAR_SACAV;
        } elseif ((int)$dadosOrgaoSuperior['Codigo'] == (int)\Orgaos::ORGAO_SUPERIOR_SAV) {
            $orgaoDestino = (int)\Orgaos::ORGAO_SAV_CEP;
        }
        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $objTbProjetos->alterarOrgao($orgaoDestino, $modeloTbAssinatura->getIdPronac());
    }
}
