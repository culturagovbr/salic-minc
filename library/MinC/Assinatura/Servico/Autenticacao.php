<?php

namespace MinC\Assinatura\Servico;

class Autenticacao implements IServico
{
    private $post;
    private $identidadeUsuarioLogado;
    private $configuracoesAplicacao;

    public function __construct($post, $identidadeUsuarioLogado) {
        $this->post = $post;
        $this->identidadeUsuarioLogado = $identidadeUsuarioLogado;
        $this->configuracoesAplicacao = \Zend_Registry::get("config")->toArray();

        $this->validarDefinicaoDePropriedades();
    }

    /**
     * @return bool
     */
    protected function validarDefinicaoDePropriedades() {

        if(!is_array($this->configuracoesAplicacao['Assinatura'])) {
            throw new \Exception("&Eacute; necess&aacute;rio informar a propriedade 'Assinatura' no arquivo de configuracao do sistema.");
        }

        if($this->configuracoesAplicacao['Assinatura']['isServicoHabilitado'] != true) {
            throw new \Exception("&Eacute; necess&aacute;rio habilitar o serviço de assinatura para acessar esse recurso, informando a propriedade 'Assinatura.isServicoHabilitado' no arquivo de configuracao do sistema.");
        }

        if(!is_array($this->configuracoesAplicacao['Assinatura']['Autenticacao'])
            || count($this->configuracoesAplicacao['Assinatura']['Autenticacao']) < 1
            || !$this->configuracoesAplicacao['Assinatura']['Autenticacao']['Metodo']) {
            throw new \Exception("&Eacute; necess&aacute;rio informar a propriedade 'Assinatura.Autenticacao.Metodo' no arquivo de configuracao do sistema.");
        }

        $metodoAutenticacao =  ucfirst($this->configuracoesAplicacao['Assinatura']['Autenticacao']['Metodo']);

        if(!class_exists("\MinC\Assinatura\Autenticacao\\{$metodoAutenticacao}")) {
            throw new \Exception("Classe definida no arquivo de configura&ccedil;&atilde;o do sistema inexistente.");
        }

    }

    /**
     * @return \MinC\Assinatura\Autenticacao\IAdapter
     */
    public function obterMetodoAutenticacao() {
        $metodoAutenticacao =  ucfirst($this->configuracoesAplicacao['Assinatura']['Autenticacao']['Metodo']);
        $classeDeAutenticacao = "\MinC\Assinatura\Autenticacao\\{$metodoAutenticacao}";

        return new $classeDeAutenticacao($this->post, $this->identidadeUsuarioLogado);
    }
}