<?php

/**
 * AgentesControllerTest
 *
 * @author wouerner <woeurner@gmail.como>
 * @author anderson <anderson.asp.si@gmail.com>
 */
class MensagemControllerTest extends MinC_Test_ControllerActionTestCase
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


        $this->mudarPerfil();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/admissibilidade/mensagem?idPronac=204022');
        $this->assertModule('admissibilidade');
        $this->assertController('mensagem');
        $this->assertAction('index');
    }
}
