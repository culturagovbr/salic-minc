<?php
/**
 * Projeto_MantertermodecisaoConttrollerTest
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class TramitardocumentosTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->idPronac = '209649';

        $this->hashIdPronac = "501eac548e7d4fa987034573abc6e179MjAxNzEzZUA3NWVmUiEzNDUwb3RT";

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::GESTOR_SALIC, Orgaos::ORGAO_SUPERIOR_SEFIC);

        $this->resetRequest()
            ->resetResponse();
    }

    public function testreceberAction()
    {
        $this->dispatch('/tramitardocumentos/receber');
        $this->assertUrl('default', 'tramitardocumentos', 'receber');
    }

    public function testanexarAction()
    {
        $this->dispatch('/tramitardocumentos/anexar');
        $this->assertUrl('default', 'tramitardocumentos', 'anexar');
    }

    public function testguiasAction()
    {
        $this->dispatch('/tramitardocumentos/guias');
        $this->assertUrl('default', 'tramitardocumentos', 'guias');
    }

    public function testconsultarAction()
    {
        $this->dispatch('/tramitardocumentos/consultar');
        $this->assertUrl('default', 'tramitardocumentos', 'consultar');
    }
}