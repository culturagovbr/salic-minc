<?php
/**
 * Projeto_ChecklistpublicacaoController
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class ControlarmovimentacaobancariaControllerTest extends MinC_Test_ControllerActionTestCase
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
    /**
     * testuploadAction
     *
     * @access public
     * @return void
     */

    public function testindexAction()
    {
        $this->dispatch('/controlarmovimentacaobancaria?idPronac=' . $this->idPronac);
        $this->assertUrl('default','controlarmovimentacaobancaria', 'form');
    }

    public function testuploadAction()
    {
        $this->dispatch('/controlarmovimentacaobancaria/upload?idPronac=' . $this->idPronac);
        $this->assertUrl('default','controlarmovimentacaobancaria', 'upload');
    }

//    public function testindexAction()
//    {
//        $this->dispatch('/controlarmovimentacaobancaria/index/inconsistencia/true?idPronac=' . $this->idPronac);
//        $this->assertUrl('default','controlarmovimentacaobancaria', 'form');
//    }
}