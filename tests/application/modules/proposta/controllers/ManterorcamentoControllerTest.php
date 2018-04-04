<?php

class ManterorcamentoControllerTest extends MinC_Test_ControllerActionTestCase {

    public function setUp()
    {
        parent::setUp();

        $this->idPreProjeto = '240102';
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

    public function testProdutoscadastrados()
    {
        $this->idPreProjeto = '240102';

        $url = '/proposta/manterorcamento/produtoscadastrados/idPreProjeto/'. $this->idPreProjeto;
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('manterorcamento');
        $this->assertAction('produtoscadastrados');

        $this->assertQuery('div.container-fluid div');
    }


    public function testPlanilhaorcamentariageralAction()
    {
        $this->idPreProjeto = '240102';

        $url = '/proposta/manterorcamento/planilhaorcamentariageral/idPreProjeto/'. $this->idPreProjeto;
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('manterorcamento');
        $this->assertAction('planilhaorcamentariageral');
        $this->assertQueryContentContains('html body div#titulo div', 'Planilha Orçamentária ');
    }

    public function testCustosvinculadosAction()
    {
        $this->idPreProjeto = '240102';

        $url = '/proposta/manterorcamento/custosvinculados/idPreProjeto/'. $this->idPreProjeto;
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('manterorcamento');
        $this->assertAction('custosvinculados');
        $this->assertQuery('div.container-fluid div');
    }


}
