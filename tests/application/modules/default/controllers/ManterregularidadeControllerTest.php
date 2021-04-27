<?php

class ManterregularidadeControllerTest extends MinC_Test_ControllerActionTestCase
{
     public function setUp()
     {
        parent::setUp();

        $this->idPreProjeto = 276034;
        
        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        
        $this->resetRequest()
            ->resetResponse();
     }
    
    
    /**
     * TestManterregularidadeproponenteAction
     *
     * @access public
     * @return void
     */
    public function testManterregularidadeproponenteAction()
    {
        $this->dispatch('/manterregularidadeproponente');
        $this->assertUrl('default','manterregularidadeproponente', 'index');
        
        $this->assertQuery('input#cpfCnpj');
    }
}
