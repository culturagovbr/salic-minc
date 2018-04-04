<?php

/**
 * AgentesControllerTest
 *
 * @author wouerner <woeurner@gmail.como>
 * @author anderson <anderson.asp.si@gmail.com>
 */
class RecursoProponenteControllerTest extends MinC_Test_ControllerActionTestCase
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


        $this->perfilParaProponente();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/solicitarrecursodecisao/recurso-enquadramento?idPronac=501eac548e7d4fa987034573abc6e179MjA0MDUyZUA3NWVmUiEzNDUwb3RT');
        $this->assertModule('default');
        $this->assertController('solicitarrecursodecisao');
        $this->assertAction('recurso-enquadramento');
    }
}
