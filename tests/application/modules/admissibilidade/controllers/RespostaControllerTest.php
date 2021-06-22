<?php

class RespostaControllerTest extends MinC_Test_ControllerActionTestCase
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


        $this->mudarPerfil1();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/admissibilidade/mensagem/responder/id/76/idPronac//actionBack/perguntas-usuario');
        $this->assertModule('admissibilidade');
        $this->assertController('mensagem');
        $this->assertAction('responder');
    }
}
