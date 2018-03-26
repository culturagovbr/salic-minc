<?php
/**
 * LocalDeRealizacaoControllerTest
 * @author wouerner <wouerner@gmail.com>
 * @link http://www.cultura.gov.br
 */

class LocalderealizacaoControllerTest extends MinC_Test_ControllerActionTestCase
{
     public function setUp()
     {
        parent::setUp();

        $this->idPreProjeto = '276031';
        $this->autenticar();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        // trocar para perfil Proponente
        $this->perfilParaProponente();

        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();
     }

    public function testIndex()
    {
        $auth = Zend_Auth::getInstance();
        $usuarioCpf = $auth->getIdentity()->usu_identificacao;
        /* var_dump($usuarioCpf);die; */

        /* // Busca na SGCAcesso */
        $sgcAcesso = new Autenticacao_Model_Sgcacesso();
        $acessos = $sgcAcesso->porCPF($usuarioCpf);
        /* $acessos = $sgcAcesso->findBy(['cpf' => $usuarioCpf]); */
        /* var_dump($acessos->toArray());die; */

        // Buscar projetos do Usuario Logado.
        $where['stestado = ?'] = 1;
        $where['idusuario = ?'] = $acessos['IdUsuario'];

        /* var_dump($where);die; */
        $tblPreProjeto = new Proposta_Model_DbTable_PreProjeto();
        $rsPreProjeto = $tblPreProjeto->buscar($where, array("idpreprojeto ASC"));
        /* var_dump($rsPreProjeto->toArray());die; */

        //id do Pre Projeto, necessario usuario ter um pre projeto
        $idPreProjeto = $rsPreProjeto[0]->idPreProjeto;

        // Acessando local de realizacao
        $this->dispatch('proposta/localderealizacao?idPreProjeto='. $idPreProjeto);

        $this->assertModule('proposta', 'proposta');
        $this->assertController('localderealizacao');
        $this->assertAction('index');
    }

    public function testLocalderealizacaoAction()
    {
        $this->dispatch('/proposta/localderealizacao/index/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','localderealizacao', 'index');
    }

    public function testLocalderealizacaoNovoAction()
    {
        $this->dispatch('/proposta/localderealizacao/form-inserir/idPreProjeto/' . $this->idPreProjeto);
        $this->assertUrl('proposta','localderealizacao', 'form-inserir');
    }

    public function testLocalderealizacaoEditarAction()
    {
        $this->dispatch('/proposta/localderealizacao/form-local-de-realizacao/idPreProjeto/' . $this->idPreProjeto . '?cod=540779');
        $this->assertUrl('proposta','localderealizacao', 'form-local-de-realizacao');
    }        

    
}
