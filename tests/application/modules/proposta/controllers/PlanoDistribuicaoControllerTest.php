<?php
/**
 * Proposta_PlanoDistribuicaoController
 *
 * @package
 * @author wouerner <wouerner@gmail.com>
 */
class PlanoDistribuicaoControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->perfilParaProponente();

        $this->resetRequest()
            ->resetResponse();

        $this->idPreProjeto = $this->getIdPreProjeto();
        $this->idPlanoDistribuicao = $this->getIdPlanoDistribuicao();
    }

    private function getIdPreProjeto()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );

        $projetos = new Projetos();
        $select = $projetos->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => 'PreProjeto'),
            'p.idPreProjeto AS idPreProjeto',
            'sac.dbo'
        );

        $select->joinInner(
            array('a' => 'Agentes'),
            'a.idAgente = p.idAgente',
            array(''),
            'agentes.dbo'
        );

        $select->where('p.stEstado = ?', 1);
        $select->where('a.cnpjcpf = ?', $config->test->params->login);
        $select->limit(1);

        $result = $projetos->fetchAll($select);
        if (count($result) > 0)
        {
            return $result[0]['idPreProjeto'];
        } else {
            return false;
        }
    }

    private function getIdPlanoDistribuicao()
    {
        $tblPlanoDistribuicao = new PlanoDistribuicao();
        $select = $tblPlanoDistribuicao->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('p' => 'PlanoDistribuicaoProduto'),
            'p.idPlanoDistribuicao AS idPlanoDistribuicao',
            'sac.dbo'
        );

        $select->where('p.stPlanoDistribuicaoProduto = ?', 1);
        $select->where('p.idProjeto = ?', $this->idPreProjeto);
        
        $result = $tblPlanoDistribuicao->fetchAll($select);
        if (count($result) > 0)
        {
            return $result[0]['idPlanoDistribuicao'];
        } else {
            return false;
        }
    }


    /**
     * Proposta_PlanoDistribuicaoController
     *
     * @package
     * @author wouerner <wouerner@gmail.com>
     */
    public function testIndexAction()
    {

        // Acessando local de realizacao
        $url = '/proposta/plano-distribuicao?idPreProjeto=' . $this->idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('plano-distribuicao');
        $this->assertAction('index');
        $this->assertQuery('div.container-fluid div');
        //$this->assertQueryContentContains('html body div#titulo', 'Plano de Distribuição');
    }

    public function testDetalharPlanoDistribuicaoAction()
	{
        $url = '/proposta/plano-distribuicao/detalhar-plano-distribuicao/idPreProjeto/' . $this->idPreProjeto . '/idPlanoDistribuicao/' . $this->idPlanoDistribuicao;
        $this->request->setMethod('GET');
        $this->dispatch($url);
        $this->assertModule('proposta');
        $this->assertController('plano-distribuicao');
        $this->assertAction('detalhar-plano-distribuicao');
    }

    public function testSalvarAction()
	{

        $url = '/proposta/plano-distribuicao/salvar?idPreProjeto=' . $this->idPreProjeto;

        $this->request->setMethod('POST')
            ->setPost([
            'areaCultural' => 1,
            'idPlanoDistribuicao' => '',
            'idProjeto' => '',
            'prodprincipal' => 0,
            'produto' => 81,
            'segmentoCultural' => 17
        ]);

        $this->dispatch($url);
        $this->assertRedirectTo('/proposta/plano-distribuicao/index?idPreProjeto=' . $this->idPreProjeto);
    }
}
