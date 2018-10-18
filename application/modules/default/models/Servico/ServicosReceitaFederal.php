<?php

/**
 * Classe do componente de SEI que gerencia a
 * comunicacao via webservice entre o NovoSalic e o servico do Corporativo
 * que consome o Webservice da Receita Federal
 * Este Servico eh do tipo REST
 *
 */
class ServicosReceitaFederal
{
    const urlPessoaFisica = "pessoa_fisica/consultar/";
    const urlPessoaJuridica = "pessoa_juridica/consultar/";
    const urlForcar = "?forcarBuscaNaReceita=true";

    # Constante usada na classe para conexao com o WS
    const urlServico = "/minc-pessoa/servicos/"; #Homologacao

    # Atributos da classe
    #private static $objSoapCliente;

    /**
     * Endereco do Webservice.
     *
     * @var type
     */
    protected $baseUrl;

    /**
     * Nome usado para se conectar com o WebService da Receita Federal.
     *
     * @var string
     */
    protected $user;

    /**
     * Senha de acesso ao Webservice.
     *
     * @var string
     */
    protected $password;

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setBaseUrl(type $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @author Alysson Vicuña de Oliveira
     *
     * @param $cnpj - CNPJ a ser consultado
     * @param bool $returnJSON - Define se o retorno sera um JSON ou Array de Objetos
     * @param bool $forcarBuscaReceita - Define se deve ir na Base da receita federal, mesmo ja existindo o CPF na base do MINC
     * @return ArrayObject|mixed - Resultado da consulta em Json ou ArrayObject
     */
    public function consultarPessoaJuridicaReceitaFederal($cnpj, $forcarBuscaReceita = false, $returnJSON = false)
    {
        $chars = array(".", "/", "-");
        $cnpj = str_replace($chars, "", $cnpj);

        if (15 == strlen($cnpj) && !isCnpjValid($cnpj)) {
            throw new InvalidArgumentException("CPF/CNPJ inv&aacute;lido");
        }

        $url = $this->baseUrl . self::urlServico . self::urlPessoaJuridica . $cnpj;
        if ($forcarBuscaReceita) {
            $url .= self::urlForcar;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "$this->user:$this->password");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $resultCurl = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($httpCode != '200') {
            throw new Zend_Exception("Erro ao consultar CNPJ. Status: " . $httpCode);
        }

        $resultArray = json_decode($resultCurl, true);

        if (!is_array($resultArray)) {
            throw new Zend_Exception("Servi&ccedil;o receita: dados da empresa n&atilde;o encontrado.");
        }

        $retornoResultado =  new ArrayObject($resultArray);

        if ($returnJSON) {
            $retornoResultado = $resultCurl;
        }

        return $retornoResultado;
    }

    /**
     *
     * @param $cpf - CPF a ser consultado
     * @param bool $returnJSON - Define se o retorno sera um JSON ou Array de Objetos
     * @param bool $forcarBuscaReceita - Define se deve ir na Base da receita federal, mesmo ja existindo o CPF na base do MINC
     * @return ArrayObject|mixed - Resultado da consulta em Json ou ArrayObject
     */
    public function consultarPessoaFisicaReceitaFederal($cpf, $forcarBuscaReceita = false, $returnJSON = false)
    {
        if (11 == strlen($cpf) && !validaCPF($cpf)) {
            throw new InvalidArgumentException("CPF/CNPJ inv&aacute;lido");
        }
        
        $url = $this->baseUrl . self::urlServico . self::urlPessoaFisica . $cpf;
        if ($forcarBuscaReceita) {
            $url .= self::urlForcar;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "$this->user:$this->password");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        $resultCurl = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($httpCode != '200') {
            throw new Zend_Exception("Erro ao consultar CPF. Status: " . $httpCode);
        }

        $resultArray = json_decode($resultCurl, true);

        if (!is_array($resultArray)) {
            throw new Zend_Exception("Servi&ccedil;o receita: dados da empresa n&atilde;o encontrado.");
        }

        $retornoResultado =  new ArrayObject($resultArray);

        if ($returnJSON) {
            $retornoResultado = $resultCurl;
        }

        return $retornoResultado;
    }

    /**
     * Metodo chamado quando o objeto da classe e instanciado
     *
     * @return VOID
     */
    public function __construct()
    {
        # Carrega configuracoes do Webservice
        $config = Zend_Registry::get('config');
        $this->baseUrl = $config->service->receita->url;
        $this->user = $config->service->receita->user;
        $this->password = $config->service->receita->password;
    }

    /**
     * Metodo chamado quando o objeto da classe e serializado
     *
     * @return VOID
     */
    public function __sleep()
    {
        return;
    }

    /**
     * Metodo chamado quando o objeto da classe e unserializado
     *
     * @return VOID
     */
    public function __wakeup()
    {
        return;
    }

    /**
     * Caso o metodo nao seja encontrado
     *
     * @param STRING $strMethod
     * @param ARRAY $arrParameters
     * @return VOID
     */
    public function __call($strMethod, $arrParameters)
    {
        debug("O m&eacute;todo " . $strMethod . " n&atilde;o foi encontrado na classe " . get_class($this) . ".<br />" . __FILE__ . "(linha " . __LINE__ . ")", 1);
    }
}
