<?php

namespace Application\Modules\ComprovacaoObjeto\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoDevolver;

class Devolver implements IAcaoDevolver
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;
        $tbCumprimentoObjeto = new \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        $objeto = $tbCumprimentoObjeto->findBy(['idPronac = ?' => $modeloTbAssinatura->getIdPronac()]);

        $dados = [];
        $dados['siCumprimentoObjeto'] = \ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto::SI_PARA_AVALIACAO_TECNICO;
        $where = sprintf("idCumprimentoObjeto = %d", $objeto['idCumprimentoObjeto']);
        $tbCumprimentoObjeto->update($dados, $where);
    }
}
