<?php

class EnquadramentoControllerTest extends MinC_Test_ControllerActionTestCase
{
     public function setUp()
     {
        parent::setUp();

        $this->idPronac = 215141;
        
        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        
        $this->resetRequest()
            ->resetResponse();
     }
    
    /**
     * TestEncaminharAssinaturaAction Listagem de projetos disponíveis para encaminhar para assinatura
     *
     * @access public
     * @return void
     */
    public function testEncaminharAssinaturaAction()
    {
        $this->dispatch('/admissibilidade/enquadramento/encaminhar-assinatura');
        $this->assertUrl('admissibilidade','enquadramento', 'encaminhar-assinatura');
    }

    /**
     * TestGerenciarEnquadramentoAction
     *
     * @access public
     * @return void
     */
    public function testGerenciarEnquadramentoAction()
    {
        $this->dispatch('/admissibilidade/enquadramento/gerenciar-enquadramento');
        $this->assertUrl('admissibilidade','enquadramento', 'gerenciar-enquadramento');
       
        $this->assertQuery('table#enquadramento');
    }

    /**
     * TestEnquadrarprojetoAction
     *
     * @access public
     * @return void
     */
    public function testEnquadrarprojetoAction()
    {
        $idPronac = 204085;
        $this->dispatch('/admissibilidade/enquadramento/enquadrarprojeto' . '?IdPRONAC=' . $idPronac);
        $this->assertUrl('admissibilidade','enquadramento', 'enquadrarprojeto');
       
        $this->assertQuery('form#formEnquadramentoProjeto');
    }
}
