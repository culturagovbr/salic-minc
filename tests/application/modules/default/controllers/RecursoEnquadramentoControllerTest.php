<?php

class RecursoControllerTest extends MinC_Test_ControllerActionTestCase
{
     public function setUp()
     {
        parent::setUp();
        
        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        
        $this->resetRequest()
            ->resetResponse();
     }
    
    
    /**
     * TestRecursoEnquadramentoAction
     *
     * @access public
     * @return void
     */
    public function testRecursoEnquadramentoAction()
    {
        $this->dispatch('/recurso/recurso-enquadramento');
        $this->assertUrl('default', 'recurso','recurso-enquadramento');
    }
}
