<?php
/**
 * Projeto_ConsultardadosprojetoController
 *
 * @package
 * @author ShinNin-Chan <ananji.costa@gmail.com>
 */
class ConsultardadosprojetoControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);

        $this->resetRequest()
            ->resetResponse();

        $this->idPronac = $this->getIdPronacProjeto();
        $this->hashIdPronac = Seguranca::encrypt($this->idPronac);
    }
    private function getIdPronacProjeto()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );

        $projetos = new Projetos();
        $select = $projetos->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => 'projetos'),
            'p.IdPRONAC AS idPronac',
            'sac.dbo'
        );

        $select->where('p.AnoProjeto > ?', 15);
        $select->where('p.AnoProjeto < ?', date('y'));
        $select->where('p.cgccpf = ?', $config->test->params->login);
        $select->limit(1);


        $result = $projetos->fetchAll($select);

        if (count($result) > 0)
        {
            return $result[0]['idPronac'];
        } else {
            return false;
        }
    }

    /**
     * TestIndexAction
     *
     * @access public
     * @return void
     */
    public function testIndexAction()
    {
        $this->dispatch('/consultardadosprojeto?idPronac=' . $this->idPronac);
        $this->assertUrl('default','consultardadosprojeto', 'index');
    }

    /**
     * TestIndexAction
     *
     * @access public
     * @return void
     */
    public function testIndexActionHash()
    {
        
        $this->dispatch('/consultardadosprojeto?idPronac=' . $this->hashIdPronac);
        $this->assertUrl('default','consultardadosprojeto', 'index');
    }
    
    
    /**
     * TestCertidoesNegativasAction
     *
     * @access public
     * @return void
     */
    public function testCertidoesNegativasAction()
    {
        $this->dispatch('/consultardadosprojeto/certidoes-negativas?idPronac=' . $this->idPronac);
        $this->assertUrl('default','consultardadosprojeto', 'certidoes-negativas');
    }

    /**
     * TestCertidoesNegativasAction
     *
     * @access public
     * @return void
     */
    public function testCertidoesNegativasActionHash()
    {
        $this->dispatch('/consultardadosprojeto/certidoes-negativas?idPronac=' . $this->hashIdPronac);
        $this->assertUrl('default','consultardadosprojeto', 'certidoes-negativas');
    }    

    /**
     * TestDadosComplementaresAction
     *
     * @access public
     * @return void
     */
    public function testDadosComplementaresAction()
    {
        $this->dispatch('/consultardadosprojeto/dados-complementares?idPronac=' . $this->idPronac);
        $this->assertUrl('default','consultardadosprojeto', 'dados-complementares');
    }

    /**
     * TestDadosComplementaresAction
     *
     * @access public
     * @return void
     */
    public function testDadosComplementaresActionHash()
    {
        $this->dispatch('/consultardadosprojeto/dados-complementares?idPronac=' . $this->hashIdPronac);
        $this->assertUrl('default','consultardadosprojeto', 'dados-complementares');
    }    
    
    /**
     * TestDocumentosAnexadosAction
     *
     * @access public
     * @return void
     */
    public function testDocumentosAnexadosAction()
    {
        $this->dispatch('/consultardadosprojeto/documentos-anexados?idPronac=' . $this->idPronac);
        $this->assertUrl('default','consultardadosprojeto', 'documentos-anexados');
    }

    /**
     * TestDiligenciasAction
     *
     * @access public
     * @return void
     */
    public function testDiligenciasAction()
    {
        $this->dispatch('/consultardadosprojeto/diligencias?idPronac=' . $this->idPronac);
        $this->assertUrl('default','consultardadosprojeto', 'diligencias');
    }

    /**
     * TestLocalRealizacaoDeslocamentoAction
     *
     * @access public
     * @return void
     */
    public function testLocalRealizacaoDeslocamentoAction()
    {
        $this->dispatch('/consultardadosprojeto/local-realizacao-deslocamento?idPronac=' . $this->idPronac);
        $this->assertUrl('default','consultardadosprojeto', 'local-realizacao-deslocamento');
    }

    /**
     * TestPlanoDeDivulgacaoAction
     *
     * @access public
     * @return void
     */
    public function testPlanoDeDivulgacaoAction()
    {
        $this->dispatch('/consultardadosprojeto/plano-de-divulgacao?idPronac=' . $this->idPronac);
        $this->assertUrl('default','consultardadosprojeto', 'plano-de-divulgacao');
    }

    public function testDadosBancariosAction()
    {
        $this->dispatch('/consultardadosprojeto/dados-bancarios?idPronac=' . $this->idPronac);
        $this->assertUrl('default','consultardadosprojeto', 'dados-bancarios');
    }
}