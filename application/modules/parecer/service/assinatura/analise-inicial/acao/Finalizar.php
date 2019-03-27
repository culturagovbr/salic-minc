<?php

namespace Application\Modules\Parecer\Service\Assinatura\AnaliseInicial\Acao;

use MinC\Assinatura\Acao\IAcaoFinalizar;

class Finalizar implements IAcaoFinalizar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {

        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;

        $objProjetos = new \Projetos();
        $objProjetos->alterarSituacao(
            $modeloTbAssinatura->getIdPronac(),
            null,
            \Projeto_Model_Situacao::PARECER_TECNICO_EMITIDO,
            'An&aacute;lise t&eacute;cnica conclu&iacute;da'
        );

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $dadosProjeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $modeloTbAssinatura->getIdPronac()
        ));

        $objTbProjetos->alterarOrgao(
            $dadosProjeto['OrgaoOrigem'],
            $modeloTbAssinatura->getIdPronac()
        );

        $tbDistribuirParecer = new \tbDistribuirParecer();
        $dadosDistribuirParecer = $tbDistribuirParecer->findBy([
            'idPRONAC = ?' => $modeloTbAssinatura->getIdPronac(),
            'stEstado = ?' => 0,
            'stPrincipal = ?' => 1
        ]);

        if (count($dadosDistribuirParecer) > 0) {

            $distribuirParecer = $tbDistribuirParecer->find(
                $dadosDistribuirParecer['idDistribuirParecer']
            )->current();

            $auth = \Zend_Auth::getInstance();
            $distribuirParecer->FecharAnalise = 1;
            $distribuirParecer->idUsuario = $auth->getIdentity()->usu_codigo;
            $distribuirParecer->DtRetorno = $tbDistribuirParecer->getExpressionDate();

            $distribuirParecer->save();
        }
    }
}
