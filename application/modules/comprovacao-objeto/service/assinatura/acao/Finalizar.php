<?php

namespace Application\Modules\ComprovacaoObjeto\Service\Assinatura\Acao;

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
        }

        $tbProjetos = new \Projetos();
        $tbProjetos->alterarSituacao($modeloTbAssinatura->getIdPronac(), '', $situacao, $providenciaTomada);
    }

}
