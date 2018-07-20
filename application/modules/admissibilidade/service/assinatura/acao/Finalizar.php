<?php

namespace Application\Modules\Admissibilidade\Service\Assinatura\Acao;

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
            \Projeto_Model_Situacao::PROJETO_APROVADO_AGUARDANDO_ANALISE_DOCUMENTAL,
            'Projeto aprovado - aguardando an&aacute;lise documental'
        );

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $dadosProjeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $modeloTbAssinatura->getIdPronac()
        ));

        $orgaoDestino = \Orgaos::ORGAO_SAV_DAP;
        $objOrgaos = new \Orgaos();
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);

        if ($dadosOrgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SEFIC) {
            $orgaoDestino = \Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI;
        }
        $objTbProjetos->alterarOrgao($orgaoDestino, $modeloTbAssinatura->getIdPronac());

        $enquadramento = new \Admissibilidade_Model_Enquadramento();
        $dadosEnquadramento = $enquadramento->obterEnquadramentoPorProjeto(
            $modeloTbAssinatura->getIdPronac(),
            $dadosProjeto['AnoProjeto'],
            $dadosProjeto['Sequencial']
        );

        $auth = \Zend_Auth::getInstance();

        $valoresProjeto = $objTbProjetos->obterValoresProjeto($modeloTbAssinatura->getIdPronac());

        $dadosInclusaoAprovacao = array(
            'IdPRONAC' => $modeloTbAssinatura->getIdPronac(),
            'AnoProjeto' => $dadosProjeto['AnoProjeto'],
            'Sequencial' => $dadosProjeto['Sequencial'],
            'TipoAprovacao' => 1,
            'dtAprovacao' => $objTbProjetos->getExpressionDate(),
            'ResumoAprovacao' => $dadosEnquadramento['Observacao'],
            'AprovadoReal' => $valoresProjeto['ValorProposta'],
            'Logon' => $auth->getIdentity()->usu_codigo,
        );
        $objAprovacao = new \Aprovacao();
        $idAprovacao = $objAprovacao->inserir($dadosInclusaoAprovacao);

        $idTecnico = new \Zend_Db_Expr("sac.dbo.fnPegarTecnico(110, {$orgaoDestino}, 3)");

        $tblVerificaProjeto = new \tbVerificaProjeto();
        $dadosVP['idPronac'] = $modeloTbAssinatura->getIdPronac();
        $dadosVP['idOrgao'] = $orgaoDestino;
        $dadosVP['idAprovacao'] = $idAprovacao;
        $dadosVP['idUsuario'] = $idTecnico;
        $dadosVP['stAnaliseProjeto'] = 1;
        $dadosVP['dtRecebido'] = $tblVerificaProjeto->getExpressionDate();
        $dadosVP['stAtivo'] = 1;
        $tblVerificaProjeto->inserir($dadosVP);
    }

}