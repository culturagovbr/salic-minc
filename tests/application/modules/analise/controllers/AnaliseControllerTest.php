<?php

class AnaliseControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);

        $this->resetRequest()
            ->resetResponse();
    }


    public function testAnaliseListarprojetosAction()
    {
        $this->dispatch('/analise/analise/listarprojetos');
        $this->assertUrl('analise', 'analise', 'listarprojetos');
    }

    public function testAnaliseListarProjetosAjaxAction()
    {
        $this->dispatch('/analise/analise/listar-projetos-ajax');
        $this->assertUrl('analise', 'analise', 'listar-projetos-ajax');
    }

    public function testAnaliseVisualizarprojetoAction()
    {
        $this->dispatch('/analise/analise/visualizarprojeto');
        $this->assertUrl('analise', 'analise', 'visualizarprojeto');
        $this->assertRedirectTo('/analise/analise/listarprojetos');

    }

    public function testAnaliseRedistribuiranaliseitemAction()
    {
        $this->dispatch('/analise/analise/redistribuiranaliseitem');
        $this->assertUrl('analise', 'analise', 'redistribuiranaliseitem');
    }

//    public function testAnaliseRedistribuiranaliseitemComboTecnicoAction()
//    {
//        $this->dispatch('/analise/analise/redistribuiranaliseitem?idpronac=209649');
//        $this->assertUrl('analise', 'analise', 'redistribuiranaliseitem');
//        $this->assertQueryCountMin('form#frmRedistAnalise select.select_simples option', 2);
//    }

}