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

        $tbReadequacao = new tbReadequacao();
        $idReadequacao = $tbReadequacao->buscarIdReadequacaoAtiva($this->idPronac, tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        
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
        $tbReadequacao = new tbReadequacao();
        
        $idReadequacao = $tbReadequacao->buscarIdReadequacaoAtiva($this->idPronac, tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);
        $planilhaReadequadaEmEdicao = $tbPlanilhaAprovacao->buscarPlanilhaReadequadaEmEdicao($this->idPronac, $idReadequacao);
        
        $this->assertEmpty($planilhaReadequadaEmEdicao);
    }        
}