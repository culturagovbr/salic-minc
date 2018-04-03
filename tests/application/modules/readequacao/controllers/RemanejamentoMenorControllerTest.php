<?php
/**
 * Readequacao_RemanejamentoMenorControllerTest
 *
 * @package
 * @author
 */
class Readequacao_RemanejamentoMenorControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        
        $this->autenticar();
        
        $this->resetRequest()
            ->resetResponse();
        
        $this->perfilParaProponente();
        
        $this->resetRequest()
            ->resetResponse();

        //$this->idPronac = $this->getProjetoPodeRemanejar();
        $this->idPronac = 169114;
        $this->hashPronac = Seguranca::encrypt($this->idPronac);
    }

    private function getProjetoPodeRemanejar()
    {
        return false;
    }
    
    /**
     * TestIndexAction
     *
     * @access public
     * @return void
     */    
    public function testIndexAction()
    {
        $this->dispatch('/readequacao/remanejamento-menor?idPronac=' . $this->hashPronac);
        $this->assertUrl('readequacao','remanejamento-menor', 'index');
    }
    
}