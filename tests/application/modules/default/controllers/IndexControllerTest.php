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

        $Readequacao_Model_tbReadequacao = new Readequacao_Model_tbReadequacao();
        $this->idPronac = $Readequacao_Model_tbReadequacao->buscarIdPronacReadequacaoEmAndamento(Readequacao_Model_tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);

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