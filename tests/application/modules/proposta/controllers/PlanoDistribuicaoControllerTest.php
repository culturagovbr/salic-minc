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
    }

    /**
     * Proposta_PlanoDistribuicaoController
     *
     * @package
     * @author wouerner <wouerner@gmail.com>
     */
    public function testIndexAction()
    {
        $this->autenticar();
        $this->perfilParaProponente();

        $auth = Zend_Auth::getInstance();
        $usuarioCpf = $auth->getIdentity()->cpf;

        // Busca na SGCAcesso
        $sgcAcesso = new Autenticacao_Model_Sgcacesso();
        /* var_dump($sgcAcesso);die; */
        $acessos = $sgcAcesso->findBy(['cpf' => $usuarioCpf]);
        var_dump($acessos);

        // Buscar projetos do Usuario Logado.
        $where['stestado = ?'] = 1;
        $where['idusuario = ?'] = $acessos['idusuario'];

        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $rsPreProjeto = $tblPreProjeto->buscar($where, array("idpreprojeto DESC"));

        //id do Pre Projeto, necessario usuario ter um pre projeto
        $idPreProjeto = $rsPreProjeto[0]->idPreProjeto;

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        // Acessando local de realizacao
        $url = '/proposta/plano-distribuicao?idPreProjeto=' . $idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('plano-distribuicao');
        $this->assertAction('index');
        $this->assertQueryContentContains('html body div#titulo', 'Plano de Distribuição');
    }
}
