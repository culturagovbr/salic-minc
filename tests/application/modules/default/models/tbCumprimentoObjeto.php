<?php
/**
 * Default_TbCumprimentoObjeto
 *
 * @package
 */
class TbCumprimentoObjetoTest extends MinC_Test_ModelTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Marcado para refatoração futura
        // * fixture do banco de dados com dados controlados
        $this->idPronac = '206025';
    }

    public function testNaoPossuiRelatorioDeCumprimento()
    {
        $tbCumprimentoObjeto = new ComprovacaoObjeto_Model_DbTable_TbCumprimentoObjeto();
        
        $possuiRelatorioDeCumprimento = $tbCumprimentoObjeto->possuiRelatorioDeCumprimento($this->idPronac);
        
        $this->assertFalse($possuiRelatorioDeCumprimento);
    }
}
