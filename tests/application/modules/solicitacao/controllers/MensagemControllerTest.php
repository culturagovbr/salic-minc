<?php

/**
 * MensagemControllerTest
 *
 */
class Solicitacao_MensagemControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->idPreProjeto = 276034;

        $this->autenticar();
        $this->resetRequest()->resetResponse();
        $this->alterarPerfil(
            Autenticacao_Model_Grupos::COORDENADOR_ADMISSIBILIDADE,
            Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI
        );
        $this->resetRequest()->resetResponse();
    }

    public function testListarAction()
    {
        $this->dispatch("/solicitacao/mensagem/index");
        $this->assertUrl('solicitacao', 'mensagem', 'index');

        $this->assertQuery('div.container-fluid div');
    }
}
