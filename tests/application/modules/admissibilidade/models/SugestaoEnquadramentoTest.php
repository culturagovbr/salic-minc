<?php

class SugestaoEnquadramentoTest extends MinC_Test_ModelActionTestCase
{
    public function testIsPropostaEnquadrada()
    {

        $sugestaoEnquadramentoFase1 = new Admissibilidade_Model_SugestaoEnquadramento();
        $sugestaoEnquadramentoFase1->setIdPreprojeto(237487);
        $sugestaoEnquadramentoFase1->setIdOrgao(171);
        $sugestaoEnquadramentoFase1->setIdPerfilUsuario(92);

        $sugestaoEnquadramentoDbTable = new Admissibilidade_Model_DbTable_SugestaoEnquadramento();
        $isPropostaEnquadradaFase1 = $sugestaoEnquadramentoDbTable->isPropostaEnquadrada($sugestaoEnquadramentoFase1);
        $this->assertNotNull($isPropostaEnquadradaFase1);

        $sugestaoEnquadramentoFase2 = new Admissibilidade_Model_SugestaoEnquadramento();
        $sugestaoEnquadramentoFase2->setIdDistribuicaoAvaliacaoProposta(28);

        $isPropostaEnquadradaFase2 = $sugestaoEnquadramentoDbTable->isPropostaEnquadrada($sugestaoEnquadramentoFase2);
        $this->assertNotNull($isPropostaEnquadradaFase2);

    }
}