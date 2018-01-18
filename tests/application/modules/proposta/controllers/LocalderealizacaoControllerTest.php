<?php
/**
 * LocalDeRealizacaoControllerTest
 * @author wouerner <wouerner@gmail.com>
 * @link http://www.cultura.gov.br
 */

class LocalderealizacaoControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function testIndex()
    {
        $this->autenticar();
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
}
