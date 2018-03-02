<?php
class ManterpropostaincentivofiscalControllerTest extends MinC_Test_ControllerActionTestCase
{
     public function setUp()
     {
        parent::setUp();

        $this->idPreProjeto = '276031';
        $this->autenticar();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        // trocar para perfil Proponente
        $this->perfilParaProponente();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();
     }
    /**
     * TestListarpropostaAction Verifica acesso a tela.
     *
     * @access public
     * @return void
     */
    public function testListarpropostaAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/listarproposta');
        $this->assertModule('proposta');
        $this->assertController('manterpropostaincentivofiscal');
        $this->assertAction('listarproposta');

        //verifica se tela carregou corretamente
        $this->assertQuery('div.container-fluid div');
    }

    public function testIdentificacaodapropostaAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/identificacaodaproposta' . '/idPreProjeto/' . $this->idPreProjeto);
        $this->assertResponseCode('200');
        
        $this->assertQuery('#identificacaodiv');
    }

    public function testIdentificacaodapropostaActionRedirect()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/identificacaodaproposta');
        $this->assertResponseCode('302');
    }    

    public function testResponsabilidadesocialAction() 
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/responsabilidadesocial' . '/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'responsabilidadesocial');
    }

    public function testDetalhestecnicosAction() 
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/detalhestecnicos' . '/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'detalhestecnicos');
    }

    public function testVerificaPermissaoAcessoProposta(){}

    public function testIndexAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/index' . '/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'index');

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/proposta/manterpropostaincentivofiscal/index');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'index');
    }

    public function testDeclaracaonovapropostaAction(){}
    public function testBuscaproponenteAction(){}

    public function testValidaagenciaAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/validaagencia');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'validaagencia');

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/proposta/manterpropostaincentivofiscal/validaagencia/agencia/123');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'validaagencia');

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/proposta/manterpropostaincentivofiscal/validaagencia/agencia/27278');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'validaagencia');
    }

    public function testSalvarAction(){}

    /**
     * conferir se esse metodo é usado pelo sistema
     *
     */
    public function testCarregaProposta(){
    }

    public function testEditarAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/editar' . '/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'editar');
    }

    public function testOutrasinformacoesAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/outrasinformacoes' . '/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'outrasinformacoes');
    }

    public function testEncaminharprojetoaomincAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/encaminharprojetoaominc' . '/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'encaminharprojetoaominc');
    }

    public function testExcluirAction(){}
    public function testEnviarPropostaAction(){}
    public function testConfirmarEnvioPropostaAoMincAction(){}
    public function testValidaDatasAction(){}
    public function testListarPropostasAjaxAction(){}

    public function testConsultarresponsaveisAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/consultarresponsaveis');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'consultarresponsaveis');
    }

    public function testVincularpropostasAction()
    {
        $this->dispatch('/proposta/manterpropostaincentivofiscal/vincularpropostas');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'vincularpropostas');
    }

    public function testVincularprojetosAction(){
        $this->dispatch('/proposta/manterpropostaincentivofiscal/vincularprojetos');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'vincularprojetos');
    }
    public function testNovoresponsavelAction(){
        $this->dispatch('/proposta/manterpropostaincentivofiscal/novoresponsavel');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'novoresponsavel');
    }

    public function testRespnovoresponsavelAction(){
    }

    public function testListarPropostasArquivadasAction(){
        $this->dispatch('/proposta/manterpropostaincentivofiscal/listar-propostas-arquivadas');
        $this->assertUrl('proposta','manterpropostaincentivofiscal', 'listar-propostas-arquivadas');
    }
}
