<?php

namespace MinC\Assinatura\Autenticacao;

class Padrao implements IAdapter
{
    /**
     * @var \Autenticacao_Model_DbTable_Usuario $usuario
     */
    private $usuario;

    public function __construct($post, $identidadeUsuarioLogado)
    {
        $this->usuario = new \Autenticacao_Model_DbTable_Usuario();
        $this->usuario->setUsuIdentificacao($identidadeUsuarioLogado->usu_identificacao);
        $this->usuario->setUsuSenha($post['password']);
    }

    public function autenticar() : bool
    {
        $isUsuarioESenhaValidos = $this->usuario->isUsuarioESenhaValidos();
        if ($isUsuarioESenhaValidos) {
            return true;
        }
        return false;
    }

    public function obterInformacoesAssinante() : array
    {
        $usuariosBuscar = $this->usuario->buscar(
            ['usu_identificacao = ?' => $this->usuario->getUsuIdentificacao()]
        )->current();
        return $usuariosBuscar->toArray();
    }

    /**
     * @return string
     * @todo melhorar meneira de obtenção dos templates de tipos de documento passando como parametro
     * pelo application.ini
     */
    public function obterTemplateAutenticacao()
    {
        $view = new \Zend_View();
        $view->setScriptPath(__DIR__ . '/templates');
        return $view->render('padrao.phtml');
    }
}