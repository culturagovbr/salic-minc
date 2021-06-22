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

        $tbDistribuirParecer = new \Parecer_Model_DbTable_TbDistribuirParecer();
        $dadosDistribuirParecer = $tbDistribuirParecer->findAll([
            'idPRONAC = ?' => $modeloTbAssinatura->getIdPronac(),
            'stEstado = ?' => 0,
        ]);

        $auth = \Zend_Auth::getInstance();
        $tbDistribuirParecerMapper = new \Parecer_Model_TbDistribuirParecerMapper();
        foreach ($dadosDistribuirParecer as $distribuirParecer) {
            $modelDistribuicao = new \Parecer_Model_TbDistribuirParecer($distribuirParecer);
            $modelDistribuicao->setFecharAnalise(1);
            $modelDistribuicao->setIdUsuario($auth->getIdentity()->usu_codigo);
            $modelDistribuicao->setDtRetorno($tbDistribuirParecer->getExpressionDate());
            $modelDistribuicao->setSiEncaminhamento(\TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_MINC_PELA_UNIDADE);
            $tbDistribuirParecerMapper->save($modelDistribuicao);
        }
    }
}
