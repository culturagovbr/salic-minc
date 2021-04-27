<?php
/**
 * Proposta_PlanoDistribuicaoController
 *
 * @package
 */
class VisualizarPlanoDistribuicaoControllerTest extends MinC_Test_ControllerActionTestCase
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
     * TestDetalharAction
     *
     * @access public
     * @return void
     */
    public function testDetalharAction()
    {
        $this->dispatch('/proposta/visualizar-plano-distribuicao/detalhar/idPreProjeto/' . $this->idPreProjeto . '?idPlanoDistribuicao=205762&idMunicipio=420165&idUF=42');
        
        $this->assertUrl('proposta','visualizar-plano-distribuicao', 'detalhar');
    }    
}