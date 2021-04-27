<?php

class AdmissibilidadeControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->idPreProjeto = $this->getIdPreProjeto();

        $this->autenticar();
        $this->resetRequest()->resetResponse();
        $this->alterarPerfil(
            Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE,
            Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI
        );
        $this->resetRequest()->resetResponse();
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
        $select->limit(1);

        $result = $projetos->fetchAll($select);
        if (count($result) > 0)
        {
            return $result[0]['idPreProjeto'];
        } else {
            return false;
        }
    }

    public function testAdmissibilidadeAvaliacaoAction()
    {
        $this->dispatch('/admissibilidade/admissibilidade/listar-propostas');
        $this->assertUrl('admissibilidade', 'admissibilidade', 'listar-propostas');

        $this->assertQuery('div.container-fluid div');
    }

    public function testExibirpropostaculturalAction()
    {
        $this->alterarPerfil(
            Autenticacao_Model_Grupos::COORDENADOR_GERAL_ADMISSIBILIDADE,
            171
        );
        $this->resetRequest()->resetResponse();
        $this->request->setMethod('GET');


        $this->dispatch('/admissibilidade/admissibilidade/exibirpropostacultural/?idPreProjeto=' . $this->idPreProjeto);
        $this->assertUrl(
            'admissibilidade',
            'admissibilidade',
            'exibirpropostacultural'
        );
        $this->assertQuery('div .exibir-proposta-cultural');
        $this->assertResponseCode('200');
    }


    public function testAlterarunidadedeanalisepropostaAction()
    {
        $this->dispatch('/admissibilidade/admissibilidade/alterarunianalisepropostaconsulta');
        $this->assertUrl('admissibilidade', 'admissibilidade', 'alterarunianalisepropostaconsulta');

        $this->assertQuery('form table.tabela');
    }

    public function testMensagemAction()
    {
        $this->dispatch('/admissibilidade/mensagem' . '?idPronac=' . $this->idPronac);
        $this->assertUrl('admissibilidade', 'mensagem', 'index');
    }

    public function testDesarquivarPropostaAction()
    {
        $this->dispatch('/admissibilidade/admissibilidade/desarquivarpropostas');
        $this->assertUrl('admissibilidade', 'admissibilidade', 'desarquivarpropostas');
    }

    public function testRedistribuiranaliseAction()
    {
        $this->dispatch('/admissibilidade/admissibilidade/redistribuiranalise');
        $this->assertUrl('admissibilidade', 'admissibilidade', 'redistribuiranalise');
    }

    public function testGerenciaranalistasAction()
    {
        $this->dispatch('/admissibilidade/admissibilidade/gerenciaranalistas');
        $this->assertUrl('admissibilidade', 'admissibilidade', 'gerenciaranalistas');
    }

    public function testGerenciaranalistaAction()
    {
        $this->dispatch('/admissibilidade/admissibilidade/gerenciaranalista?usu_cod=6927&usu_orgao=262&gru_codigo=92');
        $this->assertUrl('admissibilidade', 'admissibilidade', 'gerenciaranalista');
    }

    public function testlistarPropostasAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::GESTOR_SALIC, Orgaos::ORGAO_SUPERIOR_SEFIC);
        $this->dispatch('/admissibilidade/admissibilidade/listar-propostas');
        $this->assertUrl('admissibilidade', 'admissibilidade', 'listar-propostas');
    }

    public function testlistarSolicitacoesDesarquivamentoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::GESTOR_SALIC, Orgaos::ORGAO_SUPERIOR_SEFIC);
        $this->dispatch('/admissibilidade/admissibilidade/listar-solicitacoes-desarquivamento');
        $this->assertUrl('admissibilidade', 'admissibilidade', 'listar-solicitacoes-desarquivamento');
    }
}
