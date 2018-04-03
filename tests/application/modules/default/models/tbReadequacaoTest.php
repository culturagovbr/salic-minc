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

        $tbReadequacao = new tbReadequacao();
        $this->idPronac = $tbReadequacao->buscarIdPronacReadequacaoEmAndamento(tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
    }

    public function testExisteReadequacaoEmAndamento()
    {
        $tbReadequacao = new tbReadequacao();
        
        $existeReadequacaoEmAndamento = $tbReadequacao->existeReadequacaoEmAndamento($this->idPronac);
        $this->assertTrue($existeReadequacaoEmAndamento);
    }

    public function testExisteReadequacaoParcialEmAndamento()
    {
        $tbReadequacao = new tbReadequacao();
        
        $existeReadequacaoEmAndamento = $tbReadequacao->existeReadequacaoEmAndamento(
            $this->idPronac,
               tbReadequacao::TIPO_READEQUACAO_REMANEJAMENTO_PARCIAL
        );
        
        $this->assertFalse($existeReadequacaoEmAndamento);
    }
    
    public function testExisteReadequacaoPlanilhaEmEdicao()
    {
        $tbReadequacao = new tbReadequacao();
        
        $existeReadequacaoPlanilhaEmEdicao = $tbReadequacao->existeReadequacaoPlanilhaEmEdicao($this->idPronac);
        
        $this->assertTrue($existeReadequacaoPlanilhaEmEdicao);
    }

    public function testExisteReadequacaoParcialEmEdicao()
    {
        $tbReadequacao = new tbReadequacao();
        
        $existeReadequacaoParcialEmEdicao = $tbReadequacao->existeReadequacaoParcialEmEdicao($this->idPronac);
        
        $this->assertFalse($existeReadequacaoParcialEmEdicao);
    }
    
    public function testExisteReadequacaoPlanilhaEmAndamento()
    {
        $tbReadequacao = new tbReadequacao();
        
        $existeReadequacaoEmAndamento = $tbReadequacao->existeReadequacaoEmAndamento(
            $this->idPronac,
               tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA
        );
        
        $this->assertTrue($existeReadequacaoEmAndamento);
    }
    
    public function testDisponivelParaEdicaoReadequacaoPlanilha()
    {
        $tbReadequacao = new tbReadequacao();
        
        $disponivelParaEdicaoReadequacaoPlanilha = $tbReadequacao->disponivelParaEdicaoReadequacaoPlanilha($this->idPronac);
        $this->assertTrue($disponivelParaEdicaoReadequacaoPlanilha);
    }

    public function testDisponivelParaAdicaoItensReadequacaoPlanilha()
    {
        $tbReadequacao = new tbReadequacao();
        
        $disponivelParaAdicaoItensReadequacaoPlanilha = $tbReadequacao->disponivelParaAdicaoItensReadequacaoPlanilha($this->idPronac);
        $this->assertTrue($disponivelParaAdicaoItensReadequacaoPlanilha);
    }

    public function testBuscarIdReadequacaoAtiva()
    {
        $tbReadequacao = new tbReadequacao();
        $result = $tbReadequacao->buscarIdReadequacaoAtiva($this->idPronac, tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        
        $this->assertNotEmpty($result);
    }

    public function testBuscarIdPronacReadequacaoEmAndamento()
    {
        $tbReadequacao = new tbReadequacao();
        $idPronac = $tbReadequacao->buscarIdPronacReadequacaoEmAndamento(tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        
        $this->assertNotEmpty($idPronac);
    }
}