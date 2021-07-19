<?php

class ManterorcamentoControllerTest extends MinC_Test_ControllerActionTestCase {

    public function setUp()
    {
        parent::setUp();

        $this->idPreProjeto = $this->getIdPreProjeto();
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

    private function getIdPreProjeto()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );

        $projetos = new Projetos();
        $select = $projetos->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => 'PreProjeto'),
            'p.idPreProjeto AS idPreProjeto',
            'sac.dbo'
        );

        $select->joinInner(
            array('a' => 'Agentes'),
            'a.idAgente = p.idAgente',
            array(''),
            'agentes.dbo'
        );

        $select->where('p.stEstado = ?', 1);
        $select->where('a.cnpjcpf = ?', $config->test->params->login);
        $select->limit(1);

        $result = $projetos->fetchAll($select);
        if (count($result) > 0)
        {
            return $result[0]['idPreProjeto'];
        } else {
            return false;
        }
    }

    public function testProdutoscadastrados()
    {

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

        $url = '/proposta/manterorcamento/planilhaorcamentariageral/idPreProjeto/'. $this->idPreProjeto;
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('manterorcamento');
        $this->assertAction('planilhaorcamentariageral');
//        $this->assertQueryContentContains('html body div#titulo div', 'Planilha Orçament&aacute;ria ');
//        $this->assertQueryContentContains('div.content div#conteudo div', 'planilhaOrcamentariaMontada');
        $this->assertQuery('div.container-fluid div');

    }

    public function testCustosvinculadosAction()
    {

        $url = '/proposta/manterorcamento/custosvinculados/idPreProjeto/'. $this->idPreProjeto;
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('manterorcamento');
        $this->assertAction('custosvinculados');
        $this->assertQuery('div.container-fluid div');
    }


}
