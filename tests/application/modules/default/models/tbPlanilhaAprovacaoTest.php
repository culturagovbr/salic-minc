<?php
/**
 * Default_TbPlanilhaAprovacao
 *
 * @package
 */
class TbPlanilhaAprovacaoTest extends MinC_Test_ModelTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Marcado para refatoração futura
        // * fixture do banco de dados com dados controlados
        $this->idPronac = '206025';
    }

    public function testValorTotalPlanilha()
    {
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        
        $where = array();
        $where['a.IdPRONAC = ?'] = $this->idPronac;
        $where['a.stAtivo = ?'] = 'S';
        
        $result = $tbPlanilhaAprovacao->valorTotalPlanilha($where);
        
        $this->assertNotEmpty($result->current()['Total']);
    }

    public function testItemJaAdicionado() {
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        // Marcado para refatoração futura
        // * fixture do banco de dados com dados controlados
        $nrFonteRecurso = 109;
        $idProduto = 51;
        $idEtapa = 1;
        $idMunicipioDespesa = 431490;
        $idPlanilhaItem = 35;
        
        $this->assertTrue(
            $tbPlanilhaAprovacao->itemJaAdicionado(
                $this->idPronac,
                $nrFonteRecurso,
                $idProduto,
                $idEtapa,
                $idMunicipioDespesa,
                $idPlanilhaItem
            )
        );
    }
    
    public function testValorTotalPlanilhaAtiva() {
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $valorTotalPlanilhaAtiva = $tbPlanilhaAprovacao->valorTotalPlanilhaAtiva($this->idPronac);
                
        $this->assertNotEmpty($valorTotalPlanilhaAtiva);
    }

    public function testValorTotalPlanilhaReadequada() {
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $idReadequacao = $Readequacao_Model_tbReadequacao->buscarIdReadequacaoAtiva($this->idPronac, Readequacao_Model_tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        
        $valorTotalPlanilhaReadequada = $tbPlanilhaAprovacao->valorTotalPlanilhaReadequada($this->idPronac, $idReadequacao);
                
        $this->assertNotEmpty($valorTotalPlanilhaReadequada);
    }
    
    public function testBuscarPlanilhaAtiva() {
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $planilhaAtiva = $tbPlanilhaAprovacao->buscarPlanilhaAtiva($this->idPronac);

        $this->assertNotEmpty($planilhaAtiva);
    }

    public function testBuscarPlanilhaAtivaNaoExcluidos() {
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $planilhaAtiva = $tbPlanilhaAprovacao->buscarPlanilhaAtivaNaoExcluidos($this->idPronac);
        
        $this->assertNotEmpty($planilhaAtiva);
    }

    public function testBuscarPlanilhaReadequadaEmEdicao() {
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        
        $idReadequacao = $Readequacao_Model_tbReadequacao->buscarIdReadequacaoAtiva($this->idPronac, Readequacao_Model_tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        $planilhaReadequadaEmEdicao = $tbPlanilhaAprovacao->buscarPlanilhaReadequadaEmEdicao($this->idPronac, $idReadequacao);
        
        $this->assertEmpty($planilhaReadequadaEmEdicao);
    }

    public function testBuscarItemPlanilhaOriginal()
    {
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        // Marcado para refatoração futura
        // * fixture do banco de dados com dados controlados
        $idPlanilhaAprovacao = 2224030;  // buscar um idPlanilhaAprovacao de planilha em remanejamento
        $itemPlanilha = $tbPlanilhaAprovacao->buscarItemPlanilhaOriginal($idPlanilhaAprovacao);
        
        $this->assertNotEmpty($itemPlanilha);
    }
    
    public function testBuscarItemAtivoId()
    {
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();

        $idPlanilhaAprovacao = 2224030;
        $itemPlanilha = $tbPlanilhaAprovacao->buscarItemPlanilhaOriginal($idPlanilhaAprovacao);
        
        $this->assertNotEmpty($itemPlanilha);
    }

    public function testValorTotalPlanilhaAtivaNaoExcluidosPorEtapa()
    {
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        
        $idPlanilhaAprovacao = 2224030;
        $valorTotal = $tbPlanilhaAprovacao->valorTotalPlanilhaAtivaNaoExcluidosPorEtapa($this->idPronac, 2);
        
        $this->assertNotEmpty($valorTotal);
    }

    public function testBuscarValoresItem()
    {
        $item = [];
        $item['qtItem'] = 10;
        $item['nrOcorrencia'] = 3;
        $item['vlUnitario'] = 1000;
        $valorComprovado = 30000;
        
        $tbPlanilhaAprovacao = new tbPlanilhaAprovacao();
        $valores = $tbPlanilhaAprovacao->buscarValoresItem($item, $valorComprovado);

        $this->assertEquals(30000, $valores['vlTotalItem']);
        $this->assertEquals(3000000, $valores['vlAtual']);
        $this->assertEquals(3000000, $valores['vlAtualMin']);
    }
}