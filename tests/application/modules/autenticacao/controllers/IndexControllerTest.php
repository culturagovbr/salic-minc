<?php
class IndexControllerTest  extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCadastrarusuarioAction()
    {
        $this->dispatch('/autenticacao/index/cadastrarusuario');
        $this->assertModule('autenticacao');
        $this->assertController('index');
        $this->assertAction('cadastrarusuario');
    }

    public function testSolicitarsenhaAction()
    {
        $this->dispatch('/autenticacao/index/solicitarsenha');
        $this->assertModule('autenticacao');
        $this->assertController('index');
        $this->assertAction('solicitarsenha');
    }

    /**
     * TestAlterarsenhaAction
     *
     * @access public
     * @return void
     */
    public function testAlterarsenhaAction()
    {
        $this->autenticar();
        $this->perfilParaProponente();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/autenticacao/index/alterarsenha');

        $this->assertModule('autenticacao');
        $this->assertController('index');
        $this->assertAction('alterarsenha');
        $this->assertNotRedirect();
        $this->assertQueryContentContains('html body div#titulo div', 'Alterar Senha');
    }

    /**
     * TestAlterardadosAction
     *
     * @access public
     * @return void
     */
    public function testAlterardadosAction()
    {
        $this->autenticar();
        $this->perfilParaProponente();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/autenticacao/index/alterardados');

        $this->assertModule('autenticacao');
        $this->assertController('index');
        $this->assertAction('alterardados');
        $this->assertNotRedirect();
        $this->assertQueryContentContains('html body div#titulo div', 'Alterar Dados');
    }
}


