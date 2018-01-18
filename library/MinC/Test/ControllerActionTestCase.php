<?php

abstract class MinC_Test_ControllerActionTestCase extends MinC_Test_Abstract
{
    private $autenticado = false;

    /**
     * @return boolean
     */
    public function isAutenticado()
    {
        return $this->autenticado;
    }

    /**
     * @param boolean $autenticado
     * @return MinC_Test_ControllerActionTestCase
     */
    public function setAutenticado($autenticado)
    {
        $this->autenticado = $autenticado;
        return $this;
    }

    public function tearDown()
    {
        if ($this->isAutenticado())
            $this->logout();
        parent::tearDown();
    }

    public function autenticar($login = null, $senha = null)
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_ENV
        );

        $this->resetRequest()
            ->resetResponse();

        if (empty($config->test->params->login) || empty($config->test->params->password)) {
            throw new exception('test.username e test.password');
        }

        $this->request->setMethod('POST')
            ->setPost(array(
                'Login' => ($login) ? $login : $config->test->params->login,
                'Senha' => ($senha) ? $senha : $config->test->params->password
            ));

        $this->dispatch('/autenticacao/index/login');

        $this->setAutenticado(true);
    }

    public function logout()
    {
        $this->resetRequest()->resetResponse();
        $this->dispatch('index/logout');
        $this->setAutenticado(false);
    }

    /**
     * PerfAlterarilParaProponente Troca o perfil atual do usuário para perfil de Proponente
     *
     * @access protected
     * @return void
     */
    protected function perfilParaProponente()
    {
        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->request->setMethod('GET');
        $this->dispatch('/autenticacao/perfil/alterarperfil?codGrupo=1111&codOrgao=2222');
        $this->assertRedirectTo('/principalproponente');
    }

    protected function mudarPerfil()
    {
        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->request->setMethod('GET');
        $this->dispatch('/autenticacao/perfil/alterarperfil?codGrupo=131&codOrgao=171');
        $this->assertRedirectTo('/principal');
    }

     protected function mudarPerfil1()
    {
        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->request->setMethod('GET');
        $this->dispatch('/autenticacao/perfil/alterarperfil?codGrupo=93&codOrgao=93');
        $this->assertRedirectTo('/principal');
    }
    protected function mudarPerfil2()
    {
        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->request->setMethod('GET');
        $this->dispatch('/autenticacao/perfil/alterarperfil?codGrupo=131&codOrgao=171');
        $this->assertRedirectTo('/principal');
    }
    protected function mudarPerfilTecnicoADM()
    {
        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->request->setMethod('GET');
        $this->dispatch('/autenticacao/perfil/alterarperfil?codGrupo=92&codOrgao=262');
        $this->assertRedirectTo('/principal');
    }
    protected function mudarPerfilCoordenadorADM()
    {
        //reset para garantir respostas.
        $this->resetRequest()
            ->resetResponse();

        $this->request->setMethod('GET');
        $this->dispatch('/autenticacao/perfil/alterarperfil?codGrupo=131&codOrgao=262');
        $this->assertRedirectTo('/principal');
    }

    protected function assertUrl($module, $controller, $action)
    {
        $this->assertModule($module);
        $this->assertController($controller);
        $this->assertAction($action);
    }
}




