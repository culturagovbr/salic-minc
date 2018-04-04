<?php
/**
 * ReadequacoesControllerTest
 *
 * @package
 * @author
 */
class ReadequacoesControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $tbReadequacao = new tbReadequacao();
        $this->idPronac = $tbReadequacao->buscarIdPronacReadequacaoEmAndamento(tbReadequacao::TIPO_READEQUACAO_PLANILHA_ORCAMENTARIA);

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