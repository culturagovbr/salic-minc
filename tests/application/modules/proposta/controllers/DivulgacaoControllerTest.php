<?php
class DivulgacaoControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function testPlanodivulgacaoAction()
    {
        $this->autenticar();
        $this->perfilParaProponente();

        $auth = Zend_Auth::getInstance();
        $usuarioCpf = $auth->getIdentity()->cpf;

        // Busca na SGCAcesso
        $sgcAcesso = new Autenticacao_Model_Sgcacesso();
        $acessos = $sgcAcesso->findBy(['cpf' => $usuarioCpf]);

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
        $url = '/proposta/divulgacao/planodivulgacao?idPreProjeto='. $idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);
        $this->assertNotRedirect();

        $this->assertModule('proposta');
        $this->assertController('divulgacao');
        $this->assertAction('planodivulgacao');
        $this->assertQueryContentContains('html body div#titulo', 'Divulgação');
    }

}
