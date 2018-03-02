<?php

class EnquadramentoAssinaturaControllerTest extends MinC_Test_ControllerActionTestCase
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
     * TestEncaminharAssinaturaAction Listagem de assinaturas disponíveis
     *
     * @access public
     * @return void
     */
    public function testEnquadramentoAssinaturaRedirectAction()
    {
        $this->dispatch('/admissibilidade/enquadramento-assinatura');
        $this->assertRedirect('/admissibilidade/enquadramento-assinatura/gerenciar-assinaturas');
    }
    
    /**
     * TestEncaminharAssinaturaAction Listagem de assinaturas disponíveis
     *
     * @access public
     * @return void
     */
    public function testAssinaturaAction()
    {
        $this->dispatch('/admissibilidade/enquadramento-assinatura/gerenciar-assinaturas');
        $this->assertUrl('admissibilidade','enquadramento-assinatura', 'gerenciar-assinaturas');
    }
}
