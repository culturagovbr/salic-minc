<?php

class ListarprojetosControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->idPronac = '209649';

        $this->hashIdPronac = "501eac548e7d4fa987034573abc6e179MjAxNzEzZUA3NWVmUiEzNDUwb3RT";

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->resetRequest()
            ->resetResponse();
    }
    public function testlistarprojetosAction()
    {
        $this->dispatch('/Listarprojetos/listarprojetos?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'Listarprojetos', 'listarprojetos');
    }

}