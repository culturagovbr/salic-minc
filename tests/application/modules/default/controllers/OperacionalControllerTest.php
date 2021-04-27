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


    public function testdiagnosticoAction()
    {

        $this->dispatch('/operacional?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'diagnostico');
    }

    public function testeditaisMincAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/editais-minc?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'editais-minc');
    }

    public function testTramitacaoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/tramitacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'tramitacao');
    }

    public function testAgenciaBancariaAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/agencia-bancaria?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'agencia-bancaria');
    }

    public function testPedidoProrrogacaoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/pedido-prorrogacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'pedido-prorrogacao');
    }

    public function testContaBancariaAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
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
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/tabelas?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'tabelas');
    }

    public function testRegularidadeProponenteAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/regularidade-proponente?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'regularidade-proponente');
    }

    public function testprojetosEmPautaReuniaoCnicAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/projetos-em-pauta-reuniao-cnic-sem-quebra?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'projetos-em-pauta-reuniao-cnic-sem-quebra');
    }

    public function testprojetosEmPautaReuniaoCnicSemQuebraAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/projetos-em-pauta-reuniao-cnic?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'projetos-em-pauta-reuniao-cnic');
    }

    public function testprojetosAvaliadosCnicAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/projetos-avaliados-cnic?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'projetos-avaliados-cnic');
    }

    public function testprojetosVotoAlteradoAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/operacional/projetos-voto-alterado?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'operacional', 'projetos-voto-alterado');
    }
}
