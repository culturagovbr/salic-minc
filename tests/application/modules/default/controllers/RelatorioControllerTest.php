<?php
/**
 * Projeto_RelatorioControllerTest
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class RelatorioControllerTest extends MinC_Test_ControllerActionTestCase
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
    public function testpropostaAction()
    {
        $this->dispatch('/relatorio/proposta?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'relatorio', 'proposta');
    }
    public function testprojetoAction()
    {
        $this->dispatch('/relatorio/projeto?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'relatorio', 'projeto');
    }

    public function testgerencialAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_AVALIACAO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/relatorio/gerencial?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'relatorio', 'gerencial');
    }
    public function testdesembolsoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_AVALIACAO, Orgaos::ORGAO_GEAR_SACAV);
        $this->dispatch('/relatorio/desembolso?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'relatorio', 'desembolso');
    }
}