<?php

/**
 * @author  wouerner <wouerner@gmail.com>
 */
class DiligenciarControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function testListardiligenciaproponenteAction()
    {
        $this->autenticar();

        $this->perfilParaProponente();

        $auth = Zend_Auth::getInstance();
        $usuarioCpf = $auth->getIdentity()->cpf;

        // Busca na SGCAcesso
        $sgcAcesso = new Autenticacao_Model_Sgcacesso();
        $acessos = $sgcAcesso->porCPF($usuarioCpf);

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

        $url = '/proposta/diligenciar/listardiligenciaproponente?idPreProjeto='. $idPreProjeto;
        $this->request->setMethod('GET');
        $this->dispatch($url);

        $this->assertModule('proposta');
        $this->assertController('diligenciar');
        $this->assertAction('listardiligenciaproponente');
        $this->assertQuery('html body div#titulo div', 'Listagem das Diligências');
    }
}
