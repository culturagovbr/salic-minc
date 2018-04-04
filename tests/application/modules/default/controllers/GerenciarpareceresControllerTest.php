<?php
/**
 * Projeto_GerenciarpareceresControllerTest
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class GerenciarpareceresControllerTest extends MinC_Test_ControllerActionTestCase
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
        $this->dispatch('/gerenciarpareceres?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'gerenciarpareceres', 'index');
    }
    public function testImprimirParecerTecnicoAction()
    {
        $this->dispatch('/gerenciarpareceres/imprimir-parecer-tecnico?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'gerenciarpareceres', 'imprimir-parecer-tecnico');
    }
}