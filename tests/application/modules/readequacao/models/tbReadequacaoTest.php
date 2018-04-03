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
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        return $Readequacao_Model_tbReadequacao->buscarIdPronacReadequacaoEmAndamento(Readequacao_Model_tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
    }
    
    public function testExisteReadequacaoEmAndamento()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $existeReadequacaoEmAndamento = $Readequacao_Model_tbReadequacao->existeReadequacaoEmAndamento($this->idPronac);
        $this->assertTrue($existeReadequacaoEmAndamento);
    }

    public function testExisteReadequacaoParcialEmAndamento()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $existeReadequacaoEmAndamento = $Readequacao_Model_tbReadequacao->existeReadequacaoEmAndamento(
            $this->idPronac,
               Readequacao_Model_tbReadequacao::TIPO_READEQUACAO_REMANEJAMENTO_PARCIAL
        );
        
        $this->assertFalse($existeReadequacaoEmAndamento);
    }
    
    public function testExisteReadequacaoPlanilhaEmEdicao()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $existeReadequacaoPlanilhaEmEdicao = $Readequacao_Model_tbReadequacao->existeReadequacaoPlanilhaEmEdicao($this->idPronac);
        
        $this->assertTrue($existeReadequacaoPlanilhaEmEdicao);
    }

    public function testExisteReadequacaoParcialEmEdicao()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $existeReadequacaoParcialEmEdicao = $Readequacao_Model_tbReadequacao->existeReadequacaoParcialEmEdicao($this->idPronac);
        
        $this->assertFalse($existeReadequacaoParcialEmEdicao);
    }
    
    public function testExisteReadequacaoPlanilhaEmAndamento()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $existeReadequacaoEmAndamento = $Readequacao_Model_tbReadequacao->existeReadequacaoEmAndamento(
            $this->idPronac,
               Readequacao_Model_tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA
        );
        
        $this->assertTrue($existeReadequacaoEmAndamento);
    }
    
    public function testDisponivelParaEdicaoReadequacaoPlanilha()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $disponivelParaEdicaoReadequacaoPlanilha = $Readequacao_Model_tbReadequacao->disponivelParaEdicaoReadequacaoPlanilha($this->idPronac);
        
        $this->assertTrue($disponivelParaEdicaoReadequacaoPlanilha);
    }

    public function testDisponivelParaAdicaoItensReadequacaoPlanilha()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $disponivelParaAdicaoItensReadequacaoPlanilha = $Readequacao_Model_tbReadequacao->disponivelParaAdicaoItensReadequacaoPlanilha($this->idPronac);
        
        $this->assertTrue($disponivelParaAdicaoItensReadequacaoPlanilha);
    }

    public function testBuscarIdReadequacaoAtiva()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $result = $Readequacao_Model_tbReadequacao->buscarIdReadequacaoAtiva($this->idPronac, Readequacao_Model_tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        
        $this->assertNotEmpty($result);
    }

    public function testBuscarIdPronacReadequacaoEmAndamento()
    {
        $this->idPronac = $this->getIdPronacReadequacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $idPronac = $Readequacao_Model_tbReadequacao->buscarIdPronacReadequacaoEmAndamento(Readequacao_Model_tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        
        $this->assertNotEmpty($idPronac);
    }

    public function testExisteRemanejamento50EmAndamento()
    {
        $this->idPronac = $this->getIdPronacRemanejamento();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $existeRemanejamento50EmAndamento = $Readequacao_Model_tbReadequacao->existeRemanejamento50EmAndamento($this->idPronac);
        $this->assertFalse($existeRemanejamento50EmAndamento);
    }

    public function testDisponivelParaEdicaoRemanejamentoPlanilha()
    {
        $this->idPronac = $this->getIdPronacRemanejamento();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $existeRemanejamento50EmAndamento = $Readequacao_Model_tbReadequacao->disponivelParaEdicaoRemanejamentoPlanilha($this->idPronac);
        $this->assertFalse($existeRemanejamento50EmAndamento);
    }
}