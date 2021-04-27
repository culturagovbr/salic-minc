<?php
/**
 * Projeto_MovimentacaodecontaControllerTest
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class MovimentacaodecontaControllerTest extends MinC_Test_ControllerActionTestCase
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

    public function testindexAction()
    {
        $this->dispatch('/movimentacaodeconta?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'movimentacaodeconta', 'index');
    }

    public function testresultadoExtratoDeContaCaptacaoAction()
    {
        $this->dispatch('/movimentacaodeconta/resultado-extrato-de-conta-captacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'movimentacaodeconta', 'resultado-extrato-de-conta-captacao');
    }

    public function testuploadAction()
    {
        $this->dispatch('/movimentacaodeconta/upload?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'movimentacaodeconta', 'upload');
    }

    public function testlistarInconsistenciasAction()
    {
        $this->dispatch('/movimentacaodeconta/listar-inconsistencias?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'movimentacaodeconta', 'listar-inconsistencias');
    }

    public function testformRelatorioReciboCaptacaoAction()
    {
        $this->dispatch('movimentacaodeconta/form-relatorio-recibo-captacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'movimentacaodeconta', 'form-relatorio-recibo-captacao');
    }


}





