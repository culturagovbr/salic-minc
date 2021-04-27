<?php
/**
 * Projeto_mantersecretarioorgao
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class MantersecretarioorgoaControllerTest extends MinC_Test_ControllerActionTestCase
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

    public function testIndexAction()
    {
        $this->dispatch('/mantersecretarioorgao');
        $this->assertUrl('default', 'mantersecretarioorgao', 'index');
    }

}