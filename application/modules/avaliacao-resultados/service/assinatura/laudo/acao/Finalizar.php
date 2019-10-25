<?php

namespace Application\Modules\AvaliacaoResultados\Service\Assinatura\Laudo\Acao;

use MinC\Assinatura\Acao\IAcaoFinalizar;

class Finalizar implements IAcaoFinalizar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;

        $situacao = \Projeto_Model_Situacao::PC_AVALIADA_SEFIC;
        $providenciaTomada = 'Projeto encaminhado para o setor de elabora&ccedil;&atilde;o de portaria';

        $tbProjetos = new \Projetos();
        $tbProjetos->alterarSituacao($modeloTbAssinatura->getIdPronac(), '', $situacao, $providenciaTomada);

        $dadosProjeto = $tbProjetos->findBy(array(
            'IdPRONAC' => $modeloTbAssinatura->getIdPronac()
        ));

        $objOrgaos = new \Orgaos();
        $orgaoDestino = $objOrgaos->obterUnidadeDestinoLaudoDaFinal($dadosProjeto['Orgao']);

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $objTbProjetos->alterarOrgao($orgaoDestino, $modeloTbAssinatura->getIdPronac());
    }
}
