<?php
/**
 * Projeto_ChecklistpublicacaoController
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class CaptacaoControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->idPronac = '209649';

        $this->hashIdPronac = "501eac548e7d4fa987034573abc6e179MjAxNzEzZUA3NWVmUiEzNDUwb3RT";

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);

        $this->resetRequest()
            ->resetResponse();
    }

    /**
     * TestListasAction
     *
     * @access public
     * @return void
     */
    public function testlistaCaptacao()
    {
        $this->dispatch('/captacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default','captacao', 'index');
    }
}