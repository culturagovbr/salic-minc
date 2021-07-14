<?php
/**
 * Projeto_AlterarprojetoControllerTest
 *
 * @package
 */
class AreadetrabalhoControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->idPronac = '209649';

        $this->hashIdPronac = "501eac548e7d4fa987034573abc6e179MjAxNzEzZUA3NWVmUiEzNDUwb3RT";

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COMPONENTE_COMISSAO, Orgaos::ORGAO_SUPERIOR_SAV);

        $this->resetRequest()
            ->resetResponse();
    }

    public function testindexAction()
    {

        $this->dispatch('/areadetrabalho');
        $this->assertUrl('default', 'areadetrabalho', 'index');
    }
}