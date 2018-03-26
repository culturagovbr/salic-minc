<?php

/**
 * AgentesControllerTest
 *
 * @author wouerner <woeurner@gmail.como>
 * @author anderson <anderson.asp.si@gmail.com>
 */
class RecursoTecnicoControllerTest extends MinC_Test_ControllerActionTestCase
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


        $this->mudarPerfilTecnicoADM();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->dispatch('/recurso/avaliar-recurso-enquadramento?recurso=7069');
        $this->assertModule('default');
        $this->assertController('recurso');
        $this->assertAction('avaliar-recurso-enquadramento');
    }
}
