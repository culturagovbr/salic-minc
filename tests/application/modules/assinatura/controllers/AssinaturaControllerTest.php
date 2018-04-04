<?php

class AssinaturaControllerTest extends MinC_Test_ControllerActionTestCase
{
     public function setUp()
     {
         parent::setUp();

         $this->idPronac = '209649';
         
         $this->autenticar();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE, Orgaos::ORGAO_SEFIC_DIC);

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();
     }
    
    
    /**
     * TestGerenciarAssinatura
     *
     * @access public
     * @return void
     */
    public function testGerenciarAssinaturas()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/assinatura/index/gerenciar-assinaturas');
        $this->assertUrl('assinatura','index', 'gerenciar-assinaturas');
    }

    /**
     * TestVisualizarAssinatura
     *
     * @access public
     * @return void
     */
    public function testVisualizarAssinaturas()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/assinatura/index/visualizar-assinaturas');
        $this->assertUrl('assinatura','index', 'visualizar-assinaturas');
    }

    /**
     * TestVisualizarProjeto
     *
     * @access public
     * @return void
     */
    public function testVisualizarProjeto()
    {
        $this->dispatch('/assinatura/index/visualizar-projeto?idDocumentoAssinatura=5842');
        $this->assertUrl('assinatura','index', 'visualizar-projeto');
    }

    /**
     * TestVisualizarAssinatura
     *
     * @access public
     * @return void
     */
    public function testVisualizarDocumentosAssinaturaAjax()
    {
        $this->dispatch('/assinatura/index/visualizar-documentos-assinatura-ajax/idPronac/' . $this->idPronac);
        $this->assertUrl('assinatura','index', 'visualizar-documentos-assinatura-ajax');
    }

    /**
     * TestVisualizarDocumentoAssinaod
     *
     * @access public
     * @return void
     */
    public function testVisualizarDocumentoAssinado()
    {
        $this->dispatch('/assinatura/index/visualizar-documento-assinado/idPronac/' . $this->idPronac . '?idDocumentoAssinatura=32');
        $this->assertUrl('assinatura','index', 'visualizar-documento-assinado');
    }    
}
