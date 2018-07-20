<?php

namespace Application\Modules\Readequacao\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoEncaminhar;

class Encaminhar implements IAcaoEncaminhar
{
    /**
     * @var \MinC\Assinatura\Model\Assinatura $assinatura
     */
    private $assinatura;

    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $this->assinatura = $assinatura;

        $tbReadequacaoXParecerDbTable = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
        $tbReadequacaoXParecer = $tbReadequacaoXParecerDbTable->findBy([
            'idParecer' => $assinatura->modeloTbDocumentoAssinatura->getIdAtoDeGestao()
        ]);

        $idTipoDoAtoAdministrativo = $assinatura->modeloTbDocumentoAssinatura->getIdTipoDoAtoAdministrativo();
        if ((int)$idTipoDoAtoAdministrativo == (int)\Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_TECNICO_READEQUACAO_VINCULADAS) {
            $this->tratarEncaminhamentoVinculadas($tbReadequacaoXParecer['idReadequacao']);
        } elseif ((int)$idTipoDoAtoAdministrativo == (int)\Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_TECNICO_AJUSTE_DE_PROJETO) {
            $this->tratarEncaminhamentoDeAjustesDoProjeto($tbReadequacaoXParecer['idReadequacao']);
        } elseif ((int)$idTipoDoAtoAdministrativo == (int)\Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_TECNICO_READEQUACAO_PROJETOS_MINC) {
            $this->tratarEncaminhamentoProjetosMinc($tbReadequacaoXParecer['idReadequacao']);
        }
    }

    private function tratarEncaminhamentoVinculadas($idReadequacao)
    {
        $atoAdministrativo = $this->assinatura->modeloTbAtoAdministrativo;
        $objTbProjetos = new Projeto_Model_DbTable_Projetos();
        $objOrgaos = new \Orgaos();
        $dadosProjeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $this->assinatura->modeloTbAssinatura->getIdPronac()
        ));
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);

        switch ((string)$atoAdministrativo->getIdPerfilDoAssinante()) {
            case (string)\Autenticacao_Model_Grupos::PARECERISTA:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA;
                break;
            case (string)\Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_PRESIDENTE_DA_VINCULADA;
                break;
            case (string)\Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL;

                $orgaoDestino = \Orgaos::ORGAO_SEFIC_DIC;
                if ($dadosOrgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SAV) {
                    $orgaoDestino = 682;
                }

                break;
            case (string)\Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_SECRETARIO;

                $orgaoDestino = \Orgaos::ORGAO_SUPERIOR_SEFIC;
                if ($dadosOrgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SAV) {
                    $orgaoDestino = \Orgaos::ORGAO_SUPERIOR_SAV;
                }
                break;
        }

        if (isset($orgaoDestino)) {
            $objTbProjetos->alterarOrgao($orgaoDestino, $this->assinatura->modeloTbAssinatura->getIdPronac());
        }

        if ($siEncaminhamento) {
            $dados = ['siEncaminhamento' => $siEncaminhamento];
            $where = "idReadequacao = {$idReadequacao}";
            $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $tbReadequacao->update($dados, $where);
        }

    }

    private function tratarEncaminhamentoDeAjustesDoProjeto($idReadequacao)
    {
        $dados = [];
        $atoAdministrativo = $this->assinatura->modeloTbAtoAdministrativo;

        switch ((string)$atoAdministrativo->getIdPerfilDoAssinante()) {
            case (string)\Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO;
                break;
            case (string)\Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL;

                break;
        }

        $dados['siEncaminhamento'] = $siEncaminhamento;
        $where = "idReadequacao = {$idReadequacao}";

        $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
        $tbReadequacao->update($dados, $where);
    }

    private function tratarEncaminhamentoProjetosMinc($idReadequacao)
    {
        $atoAdministrativo = $this->assinatura->modeloTbAtoAdministrativo;
        $objTbProjetos = new Projeto_Model_DbTable_Projetos();
        $objOrgaos = new \Orgaos();
        $dadosProjeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $this->assinatura->modeloTbAssinatura->getIdPronac()
        ));
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);

        switch ((string)$atoAdministrativo->getIdPerfilDoAssinante()) {
            case (string)\Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO;
                break;
            case (string)\Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL;
                break;
            case (string)\Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_DIRETOR;
                $orgaoDestino = \Orgaos::SEFIC_DEIPC;
                if ($dadosOrgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SAV) {
                    $orgaoDestino = \Orgaos::SAV_DPAV;
                }
                break;
            case (string)\Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_SECRETARIO;
                $orgaoDestino = \Orgaos::ORGAO_SUPERIOR_SEFIC;
                if ($dadosOrgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SAV) {
                    $orgaoDestino = \Orgaos::ORGAO_SUPERIOR_SAV;
                }
                break;
        }

        if (isset($orgaoDestino)) {
            $objTbProjetos->alterarOrgao(
                $orgaoDestino,
                $this->assinatura->modeloTbAssinatura->getIdPronac()
            );
        }

        if ($siEncaminhamento) {
            $dados = ['siEncaminhamento' => $siEncaminhamento];
            $where = "idReadequacao = {$idReadequacao}";
            $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $tbReadequacao->update($dados, $where);
        }
    }
}