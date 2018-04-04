<?php
/**
 * Projeto_RecursoControllerTest
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class RecursoControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->idPronac = '209649';

        $this->hashIdPronac = "501eac548e7d4fa987034573abc6e179MjAxNzEzZUA3NWVmUiEzNDUwb3RT";

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);

        $this->resetRequest()
            ->resetResponse();
    }
    public function testIndexAction()
    {
        $this->dispatch('/recurso?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'recurso', 'index');
    }
}