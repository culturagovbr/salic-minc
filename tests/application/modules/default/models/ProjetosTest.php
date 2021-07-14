<?php
/**
 * Default_Projetos
 *
 * @package
 */
class ProjetosModelTest extends MinC_Test_ModelTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->idPronac = $this->retornaProjetoVigente();
    }

    private function retornaProjetoVigente()
    {
        $projetos = new Projetos();

        $where = array();
        $where['DtInicioExecucao <= ?'] = new Zend_Db_Expr('GETDATE()');
        $where['DtFimExecucao >= ?'] = new Zend_Db_Expr('GETDATE()');
        
        $result = $projetos->buscar(
            $where,
            array('IdPRONAC DESC'),
            1
        )->current();
        
        return $result['IdPRONAC'];        
    }

    public function testPeriodoExecucao()
    {
        $projetos = new Projetos();
        
        $periodoExecucao = $projetos->buscarPeriodoExecucao($this->idPronac);
        
        $this->assertNotEmpty($periodoExecucao->qtdDias);
        $this->assertNotEmpty($periodoExecucao->DtInicioExecucao);
        $this->assertNotEmpty($periodoExecucao->DtFimExecucao);
    }

    public function testPeriodoExecucaoVigente()
    {
        $projetos = new Projetos();
        
        $periodoExecucaoVigente = $projetos->verificarPeriodoExecucaoVigente($this->idPronac);
        $this->assertTrue($periodoExecucaoVigente);
    }    
}