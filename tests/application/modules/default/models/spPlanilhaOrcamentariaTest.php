<?php
/**
 * Default_SpPlanilhaOrcamentaria
 *
 * @package
 */
class SpPlanilhaOrcamentariaModelTest extends MinC_Test_ModelTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Marcado para refatoração futura
        // * fixture do banco de dados com dados controlados
        $this->idPronac = '206025';
    }


    /*
     *  tipoPlanilha = 0 : Planilha Orcamentaria da Proposta
     *  tipoPlanilha = 1 : Planilha Orcamentaria do Proponente
     *  tipoPlanilha = 2 : Planilha Orcamentaria do Parecerista
     *  tipoPlanilha = 3 : Planilha Orcamentaria Aprovada Ativa
     *  tipoPlanilha = 4 : Cortes Orcamentarios Aprovados
     *  tipoPlanilha = 5 : Remanejamento menor que 20%
     *  tipoPlanilha = 6 : Readequacao
     */
    
    public function testExecPlanilhaReadequacao()
    {
        $tipoPlanilha = 6;
        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
        $result = $spPlanilhaOrcamentaria->exec($this->idPronac, $tipoPlanilha);
        
        $qtdLinhas = 68;
        $this->assertEquals($qtdLinhas, count($result));
    }

    public function testExecPlanilhaReadequacaoSPigualCodigo()
    {
        $tipoPlanilha = 6;
        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();

        $resultFuncao = $spPlanilhaOrcamentaria->readequacao($this->idPronac);
        $resultSP = $spPlanilhaOrcamentaria->execSpPlanilhaOrcamentaria($this->idPronac, $tipoPlanilha);
        
        $this->assertEquals(count($resultFuncao), count($resultSP));
    }
    
    public function testExecPlanilhaReadequacaoNomesDasColunasSp()
    {
        
        $colunasEsperadas = ['idPronac','PRONAC','NomeProjeto','idProduto','idPlanilhaAprovacao','idPlanilhaAprovacaoPai','Produto','idEtapa','Etapa','tpGrupo','Item','idFonte','FonteRecurso','Unidade','Quantidade','Ocorrencia','vlUnitario','vlAprovado','vlComprovado','QtdeDias','UF','Municipio','dsJustificativa','idAgente','tpAcao'];
        
        $tipoPlanilha = 6;
        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();

        $resultSP = $spPlanilhaOrcamentaria->execSpPlanilhaOrcamentaria($this->idPronac, $tipoPlanilha);

        $nomesColunasSP = (array)$resultSP[0];
        $colunasSP = array_keys($nomesColunasSP);
        
        $this->assertEmpty(
            array_diff(
                $colunasSP,
                $colunasEsperadas
            )
        );
    }

    public function testExecPlanilhaReadequacaoNomesDasColunasFuncao()
    {
        
        $colunasEsperadas = ['vlComprovado','Produto','vlAprovado','PRONAC','NomeProjeto','idPronac','Etapa','tpGrupo','Unidade','Municipio','UF','Item','QtdeDias','Quantidade','dsJustificativa','idAgente','idEtapa','idPlanilhaAprovacao','idPlanilhaAprovacaoPai','idProduto','idMunicipio','idUF','idFonte','Ocorrencia','tpAcao','vlUnitario','FonteRecurso'];
        
        $tipoPlanilha = 6;
        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();

        $resultFuncao = $spPlanilhaOrcamentaria->readequacao($this->idPronac);
        
        $nomesColunas = (array)$resultFuncao[0];
        $colunas = array_keys($nomesColunas);
        
        $this->assertEmpty(
            array_diff(
                $colunas,
                $colunasEsperadas
            )
        );
    } 

    
    public function testExecPlanilhaReadequacaoCompararVerificarAdicaoDeColunasNaFuncao()
    {
        $diferencaEsperada = ['idMunicipio', 'idUF'];

        $tipoPlanilha = 6;
        $spPlanilhaOrcamentaria = new spPlanilhaOrcamentaria();
        
        $resultFuncao = $spPlanilhaOrcamentaria->readequacao($this->idPronac);
        $resultSP = $spPlanilhaOrcamentaria->execSpPlanilhaOrcamentaria($this->idPronac, $tipoPlanilha);
        
        $nomesColunasFuncao = (array)$resultFuncao[0];
        $nomesColunasSP = (array)$resultSP[0];
        
        $diferenca = array_keys(
            array_diff_key(
                $nomesColunasFuncao,
                $nomesColunasSP
            )
        );
        
        $this->assertEquals($diferenca, $diferencaEsperada);
    }

}