<?php
/**
 * Default_TbReadequacao
 *
 * @package
 */
class TbReadequacaoModelTest extends MinC_Test_ModelTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    private function getIdPronacRemanejamento()
    {
        // Fixture
        // Marcado para refatoração futura
        
        $idPronac = 169144;
        return $idPronac;
    }

    private function getIdPronacReadequacao()
    {
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        return $Readequacao_Model_DbTable_TbReadequacao->buscarIdPronacReadequacaoEmAndamento(Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
    }
    
    public function testExisteReadequacaoEmAndamento()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $existeReadequacaoEmAndamento = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmAndamento($this->idPronac);
        $this->assertTrue($existeReadequacaoEmAndamento);
    }

    public function testExisteReadequacaoParcialEmAndamento()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $existeReadequacaoEmAndamento = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmAndamento(
            $this->idPronac,
               Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_REMANEJAMENTO_PARCIAL
        );
        
        $this->assertFalse($existeReadequacaoEmAndamento);
    }
    
    public function testExisteReadequacaoPlanilhaEmEdicao()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $existeReadequacaoPlanilhaEmEdicao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoPlanilhaEmEdicao($this->idPronac);
        
        $this->assertTrue($existeReadequacaoPlanilhaEmEdicao);
    }

    public function testExisteReadequacaoParcialEmEdicao()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $existeReadequacaoParcialEmEdicao = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoParcialEmEdicao($this->idPronac);
        
        $this->assertFalse($existeReadequacaoParcialEmEdicao);
    }
    
    public function testExisteReadequacaoPlanilhaEmAndamento()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $existeReadequacaoEmAndamento = $Readequacao_Model_DbTable_TbReadequacao->existeReadequacaoEmAndamento(
            $this->idPronac,
               Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA
        );
        
        $this->assertTrue($existeReadequacaoEmAndamento);
    }
    
    public function testDisponivelParaEdicaoReadequacaoPlanilha()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $disponivelParaEdicaoReadequacaoPlanilha = $Readequacao_Model_DbTable_TbReadequacao->disponivelParaEdicaoReadequacaoPlanilha($this->idPronac);
        
        $this->assertTrue($disponivelParaEdicaoReadequacaoPlanilha);
    }

    public function testDisponivelParaAdicaoItensReadequacaoPlanilha()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $disponivelParaAdicaoItensReadequacaoPlanilha = $Readequacao_Model_DbTable_TbReadequacao->disponivelParaAdicaoItensReadequacaoPlanilha($this->idPronac);
        
        $this->assertTrue($disponivelParaAdicaoItensReadequacaoPlanilha);
    }

    public function testBuscarIdReadequacaoAtiva()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $result = $Readequacao_Model_DbTable_TbReadequacao->buscarIdReadequacaoAtiva($this->idPronac, Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        
        $this->assertNotEmpty($result);
    }

    public function testBuscarIdPronacReadequacaoEmAndamento()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $idPronac = $Readequacao_Model_DbTable_TbReadequacao->buscarIdPronacReadequacaoEmAndamento(Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        
        $this->assertNotEmpty($idPronac);
    }

    public function testExisteRemanejamento50EmAndamento()
    {
        $this->idPronac = $this->getIdPronacRemanejamento();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $existeRemanejamento50EmAndamento = $Readequacao_Model_DbTable_TbReadequacao->existeRemanejamento50EmAndamento($this->idPronac);
        $this->assertFalse($existeRemanejamento50EmAndamento);
    }

    public function testDisponivelParaEdicaoRemanejamentoPlanilha()
    {
        $this->idPronac = $this->getIdPronacRemanejamento();
        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $existeRemanejamento50EmAndamento = $Readequacao_Model_DbTable_TbReadequacao->disponivelParaEdicaoRemanejamentoPlanilha($this->idPronac);
        $this->assertFalse($existeRemanejamento50EmAndamento);
    }
}