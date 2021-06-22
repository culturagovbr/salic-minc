<?php
/**
 * Default_Liberacao
 *
 * @package
 */
class TbLiberacaoModelTest extends MinC_Test_ModelTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Marcado para refatoração futura
        // * fixture do banco de dados com dados controlados
        $this->idPronac = '206025';
    }

    public function testContaLiberada()
    {
        $idPronac = 206025;
        $liberacao = new Liberacao();
        $contaLiberada = $liberacao->contaLiberada($this->idPronac);
        
        $this->assertTrue($contaLiberada);
    }
}