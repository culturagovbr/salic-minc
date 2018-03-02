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
    
    
    /**
     * TestAnaliseListarprojetosAction
     *
     * @access public
     * @return void
     */
    public function testAnaliseListarprojetosAction()
    {
        $this->dispatch('/analise/analise/listarprojetos');
        $this->assertUrl('analise','analise', 'listarprojetos');
    }
}