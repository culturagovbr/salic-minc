<?php
/**
 * Projeto_OperacionalControllerTest
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class OperacionalControllerTest extends MinC_Test_ControllerActionTestCase
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
    public function testDiagnosticoAction()
    {
        $this->dispatch('/operacional?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'diagnostico');
    }
    public function testeditaisMincAction()
    {
        $this->dispatch('/operacional/editais-minc?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'editais-minc');
    }
    public function testTramitacaoAction()
    {
        $this->dispatch('/operacional/tramitacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'tramitacao');
    }
    public function testAgenciaBancariaAction()
    {
        $this->dispatch('/operacional/agencia-bancaria?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'agencia-bancaria');
    }
    public function testPedidoProrrogacaoAction()
    {
        $this->dispatch('/operacional/pedido-prorrogacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'pedido-prorrogacao');
    }
    public function testContaBancariaAction()
    {
        $this->dispatch('/operacional/conta-bancaria?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'conta-bancaria');
    }
    public function testResultadoContaBancariaAction()
    {
        $this->dispatch('/operacional/resultado-conta-bancaria?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'resultado-conta-bancaria');
    }
    public function testTabelasAction()
    {
        $this->dispatch('/operacional/tabelas?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'tabelas');
    }
    public function testRegularidadeProponenteAction()
    {
        $this->dispatch('/operacional/regularidade-proponente?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'regularidade-proponente');
    }
}