<?php

namespace Application\Modules\ComprovacaoObjeto\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoAssinar;

class Assinar implements IAcaoAssinar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;
        $tbCumprimentoObjeto = new \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $objeto = $tbCumprimentoObjeto->findBy(['idPronac = ?' => $modeloTbAssinatura->getIdPronac()]);

        switch ($objeto['siCumprimentoObjeto']) {
            case \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_EM_AVALIACAO_TECNICO:
                $siCumprimentoObjeto = \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_PARA_AVALIACAO_COORDENADOR;
                break;
            case \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_PARA_AVALIACAO_COORDENADOR:
                $siCumprimentoObjeto = \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_FINALIZADO_PELO_COORDENADOR;
                break;
            default:
                $siCumprimentoObjeto = null;
        }

        if (!empty($siCumprimentoObjeto)) {
            $dados = [];
            $dados['siCumprimentoObjeto'] = $siCumprimentoObjeto;
            $where = sprintf("idCumprimentoObjeto = %d", $objeto['idCumprimentoObjeto']);
            $tbCumprimentoObjeto->update($dados, $where);
        }
    }
}
