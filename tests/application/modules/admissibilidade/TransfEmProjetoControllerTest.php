<?php

class TransfEmProjetoControllerTest extends MinC_Test_ControllerActionTestCase
{

    /**
     * TestIncluiragente Acesso a tela de incluir agente
     *
     * @access public
     * @return void
     */
    public function testIndex()
    {
        $this->autenticar();

        $this->mudarPerfil2();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/admissibilidade/admissibilidade/exibirpropostacultural?idPreProjeto=237778');
        $this->assertModule('admissibilidade');
        $this->assertController('admissibilidade');
        $this->assertAction('exibirpropostacultural');
    }
}
