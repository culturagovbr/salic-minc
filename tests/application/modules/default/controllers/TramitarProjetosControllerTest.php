<?php
/**
 * Projeto_MantertermodecisaoConttrollerTest
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class TramitarProjetosControllerTest extends MinC_Test_ControllerActionTestCase
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

    public function testdespacharprojetosAction()
    {
        $this->dispatch('/tramitarprojetos/despacharprojetos');
        $this->assertUrl('default', 'tramitarprojetos', 'despacharprojetos');
    }

    public function testconsultarprojetosarquivadosAction()
    {
        $this->dispatch('/tramitarprojetos/consultarprojetosarquivados');
        $this->assertUrl('default', 'tramitarprojetos', 'consultarprojetosarquivados');
    }

    public function testguiasAction()
    {
        $this->dispatch('/tramitarprojetos/guias');
        $this->assertUrl('default', 'tramitarprojetos', 'guias');
    }

    public function testconsultarprojetosAction()
    {
        $this->dispatch('/tramitarprojetos/consultarprojetos');
        $this->assertUrl('default', 'tramitarprojetos', 'consultarprojetos');
    }
}