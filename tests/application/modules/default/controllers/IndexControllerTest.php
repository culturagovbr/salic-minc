<?php
/**
 * Default_IndexControllerTest
 *
 * @package
 * @author
 */
class Default_IndexControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $Readequacao_Model_DbTable_TbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        $this->idPronac = $Readequacao_Model_DbTable_TbReadequacao->buscarIdPronacReadequacaoEmAndamento(Readequacao_Model_DbTable_TbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);

        $this->perfilParaProponente();
    }

    public function testMontarPlanilhaOrcamentariaAction()
    {
        $tipoPlanilha = 6;
        
        $this->dispatch('/index/montar-planilha-orcamentaria?idPronac=' . $this->idPronac . '&tipoPlanilha=' . $tipoPlanilha);
        $this->assertUrl('default', 'index','montar-planilha-orcamentaria');
    }

    public function testMontarPlanilhaOrcamentariaViewEdicaoAction()
    {
        $tipoPlanilha = 6;
        
        $this->dispatch('/index/montar-planilha-orcamentaria?idPronac=' . $this->idPronac . '&tipoPlanilha=' . $tipoPlanilha . '&view_edicao=true');
        $this->assertUrl('default', 'index','montar-planilha-orcamentaria');
    }
}