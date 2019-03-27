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
        $this->autenticar();
        $this->resetRequest()->resetResponse();

        $this->alterarPerfil(
            Autenticacao_Model_Grupos::COMPONENTE_COMISSAO,
            Orgaos::ORGAO_SUPERIOR_SAV
        );
        $this->resetRequest()->resetResponse();
    }
    public function testIndexAction()
    {
        $this->perfilParaProponente();
        $this->dispatch("/solicitacao/mensagem/index");
        $this->assertUrl("solicitacao", "mensagem", "index");
    }
    public function testListarAction()
    {
        $this->dispatch("/solicitacao/mensagem/listar");
        $this->assertUrl('solicitacao', 'mensagem', 'listar');
    }
}
