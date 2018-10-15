<?php

namespace Application\Modules\Projeto\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoFinalizar;

class Finalizar implements IAcaoFinalizar
{
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $modeloTbAssinatura = $assinatura->modeloTbAssinatura;

        $dbTableEnquadramento = new \Projeto_Model_DbTable_Enquadramento();
        $enquadramentoProjeto = $dbTableEnquadramento->obterProjetoAreaSegmento(
            ['a.IdPRONAC = ?' => $modeloTbAssinatura->getIdPronac()]
        )->current();

        $orgaoDestinoSAV = (int)\Orgaos::ORGAO_SAV_DAP;
        $tipoAprovacao = \Aprovacao::TIPO_APROVACAO_REDUCAO;
        $situacao = [
            'codigo' => \Projeto_Model_Situacao::ENCAMINHADO_PARA_INCLUSAO_EM_PORTARIA_REDUCAO,
            'providencia' => "Projeto encaminhado para elabora&ccedil;&atilde;o de portaria de redu&ccedil;&atilde;o or&ccedil;ament&aacute;ria"
        ];

        if ($enquadramentoProjeto['VlHomologadoIncentivo'] == $enquadramentoProjeto['VlAdequadoIncentivo']) {
            $orgaoDestinoSAV = (int)\Orgaos::ORGAO_SAV_CAP;
            $tipoAprovacao = null;
            $situacao = [
                'codigo' => \Projeto_Model_Situacao::AUTORIZADA_CAPTACAO_RESIDUAL_DOS_RECURSOS,
                'providencia' => "Homologado a execu&ccedil;&atilde;o do projeto cultural"
            ];

        } else if ($enquadramentoProjeto['VlHomologadoIncentivo'] > $enquadramentoProjeto['VlAdequadoIncentivo']) {
            $tipoAprovacao = \Aprovacao::TIPO_APROVACAO_COMPLEMENTACAO;
            $situacao = [
                'codigo' => \Projeto_Model_Situacao::ENCAMINHADO_PARA_INCLUSAO_EM_PORTARIA_COMPLEMENTACAO,
                'providencia' => "Projeto encaminhado para elabora&ccedil;&atilde;o de portaria de complementa&ccedil;&atilde;o or&ccedil;ament&aacute;ria"
            ];
        }

        $objProjetos = new \Projetos();
        $objProjetos->alterarSituacao(
            $modeloTbAssinatura->getIdPronac(),
            null,
            $situacao['codigo'],
            $situacao['providencia']
        );

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $dadosProjeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $modeloTbAssinatura->getIdPronac()
        ));

        $objOrgaos = new \Orgaos();
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);

        if ((int)$dadosOrgaoSuperior['Codigo'] == (int)\Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $orgaoDestino = (int)\Orgaos::ORGAO_GEAR_SACAV;
        } elseif ((int)$dadosOrgaoSuperior['Codigo'] == (int)\Orgaos::ORGAO_SUPERIOR_SAV) {
            $orgaoDestino = $orgaoDestinoSAV;
        }

        if (isset($orgaoDestino)) {
            $objTbProjetos->alterarOrgao($orgaoDestino, $modeloTbAssinatura->getIdPronac());
        }

        if (!empty($tipoAprovacao)) {
            $auth = \Zend_Auth::getInstance();

            $dbTableHomologacao = new \Projeto_Model_DbTable_TbHomologacao();
            $parecerHomolog = $dbTableHomologacao->getBy(['idPronac' => $modeloTbAssinatura->getIdPronac(), 'tpHomologacao' => '1']);

            if (empty($parecerHomolog['dsHomologacao'])) {
                throw new \Exception('Parecer de homologa&ccedil;&atilde;o n&atilde;o encontrado.');
            }

            $dadosInclusaoAprovacao = array(
                'IdPRONAC' => $modeloTbAssinatura->getIdPronac(),
                'AnoProjeto' => $dadosProjeto['AnoProjeto'],
                'Sequencial' => $dadosProjeto['Sequencial'],
                'TipoAprovacao' => $tipoAprovacao,
                'dtAprovacao' => $objTbProjetos->getExpressionDate(),
                'ResumoAprovacao' => $parecerHomolog['dsHomologacao'],
                'AprovadoReal' => $enquadramentoProjeto['VlHomologadoIncentivo'],
                'Logon' => $auth->getIdentity()->usu_codigo,
            );
            $objAprovacao = new \Aprovacao();
            $objAprovacao->inserir($dadosInclusaoAprovacao);
        }

        return true;
    }
}
