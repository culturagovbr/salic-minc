<?php
class MantertabelaitensController  extends MinC_Test_ControllerActionTestCase
{
    public function testIndexAction()
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

        //id do Pre Projeto, necessario usuario ter um pre projeto para testar
        $idPreProjeto = $rsPreProjeto[0]->idPreProjeto;

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $url = '/proposta/mantertabelaitens?idPreProjeto='. $idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);

        $this->assertModule('proposta');
        $this->assertController('mantertabelaitens');
        $this->assertAction('consultartabelaitens');
        $this->assertQuery('html body div#titulo div', 'Tabelas de Itens ');
    }

    public function testMinhasSolicitacoesAction()
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

        //id do Pre Projeto, necessario usuario ter um pre projeto para testar
        $idPreProjeto = $rsPreProjeto[0]->idPreProjeto;

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $url = '/proposta/mantertabelaitens/minhas-solicitacoes?idPreProjeto='. $idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);

        $this->assertModule('proposta');
        $this->assertController('mantertabelaitens');
        $this->assertAction('minhas-solicitacoes');
        $this->assertQuery('html body div#titulo div', 'Minhas Solicitações');
    }
}
