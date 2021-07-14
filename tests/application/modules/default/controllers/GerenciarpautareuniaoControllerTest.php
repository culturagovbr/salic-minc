<?php
/**
 * Projeto_AlterarprojetoControllerTest
 *
 * @package
 */
class GerenciarpautareuniaoControllerTest extends MinC_Test_ControllerActionTestCase
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

    public function testgerenciarpautareuniaoAction()
    {

        $this->dispatch('/gerenciarpautareuniao/gerenciarpautareuniao');
        $this->assertUrl('default', 'gerenciarpautareuniao', 'gerenciarpautareuniao');
    }

    public function testgerenciarpautareuniaoreadequecaoAction()
    {

        $this->dispatch('gerenciarpautareuniao/gerenciarpautareuniao/readequacao/false/plenaria/false');
        $this->assertUrl('default', 'gerenciarpautareuniao', 'gerenciarpautareuniao');
    }

    public function testrecursosNaoSubmetidosAction()
    {

        $this->dispatch('gerenciarpautareuniao/recursos-nao-submetidos');
        $this->assertUrl('default', 'gerenciarpautareuniao', 'recursos-nao-submetidos');
    }

    public function testreadequacoesNaoSubmetidasAction()
    {

        $this->dispatch('gerenciarpautareuniao/readequacoes-nao-submetidas');
        $this->assertUrl('default', 'gerenciarpautareuniao', 'readequacoes-nao-submetidas');
    }

    public function testpaineisdareuniaoAction()
    {

        $this->dispatch('gerenciarpautareuniao/paineisdareuniao');
        $this->assertUrl('default', 'gerenciarpautareuniao', 'paineisdareuniao');
    }

    public function testprojetosvotadosAction()
    {

        $this->dispatch('gerenciarpautareuniao/projetosvotados');
        $this->assertUrl('default', 'gerenciarpautareuniao', 'projetosvotados');
    }
}