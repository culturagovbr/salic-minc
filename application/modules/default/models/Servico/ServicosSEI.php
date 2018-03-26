<?php
class ServicosSEI
{
    private $caminhoWSDLSEI;

    private static $objSoapCliente;

    public function __construct()
    {
        // @TODO: mover configuração para application.ini
        //$this->caminhoWSDLSEI = "http://seihomolog.cultura.gov.br/sei/controlador_ws.php?servico=sei";
        $this->caminhoWSDLSEI = "http://seitreinamento.cultura.gov.br/sei/controlador_ws.php?servico=sei";
        if (APPLICATION_ENV == "production") {
            $this->caminhoWSDLSEI = "http://sei.cultura.gov.br/sei/controlador_ws.php?servico=sei";
        }
    }

    private function getSoapClient()
    {
        if (!@file_get_contents($this->caminhoWSDLSEI)) {
            throw new Exception("Arquivo WSDL n&atilde;o encontrado! Verifique: {$this->caminhoWSDLSEI}");
        }

        if (is_null(self::$objSoapCliente)) {
            try {
                # Instanciando a classe que conecta ao WebService
                $objSoapCliente = new Zend_Soap_Client($this->caminhoWSDLSEI, array('encoding' => 'UTF-8'));
            } catch (Exception $objException) {
                # Retorna mensagem de erro
                return ($objException->getMessage());
            }
            self::$objSoapCliente = $objSoapCliente;
        }
        return (self::$objSoapCliente);
    }

    /**
     * @author Alysson Vicu�a de Oliveira
     * Carrega o dados das Unidades Cadastradas no SEI
     * @param $txSiglaSistema
     * @param $txIdentificacaoServico
     * @param null $nrIdTipoProcedimento
     * @param null $nrIdSerie
     * @return mixed
     */
    public function wsListarUnidades($txSiglaSistema, $txIdentificacaoServico, $nrIdTipoProcedimento = null, $nrIdSerie = null)
    {
        $objSoapCliente = self::getSoapClient();
        //Nome do M�todo Remoto a ser Chamado
        $mixResult = $objSoapCliente->listarUnidades($txSiglaSistema, $txIdentificacaoServico, $nrIdTipoProcedimento, $nrIdSerie);
        return $mixResult;
    }

    /**
     * @author Alysson Vicu�a de Oliveira
     * Procedimento para criar um processo dentro do SEI.
     * @param $txSiglaSistema
     * @param $txIdentificacaoServico
     * @return mixed
     * @throws Exception
     */
    public function wsGerarProcedimento($txSiglaSistema = "INTRANET", $txIdentificacaoServico = "SALIC", $nrIdUnidade = '110000151')
    {
        $objSoapCliente = self::getSoapClient();

        #Inicio dos Dados do Procedimento
        //Procedimento
        $Procedimento = array();
        $Procedimento['IdTipoProcedimento'] = '100000520'; #Alysson - C�digo do Tipo de Processo "Apoio Cultural: Projeto Cultural" em Produ��o;
        $Procedimento['Especificacao'] = utf8_encode('Apoio Cultural: Projeto Cultural'); //Verificar se tem que usar UTF-8 Em Produ��o

        //Assuntos
        $arrAssuntos = array();
        $arrAssuntos[] = array('CodigoEstruturado' => '067.1');
        $Procedimento['Assuntos'] = $arrAssuntos; //Atribui os Assuntos aos Dados do Procedimento

        //Interessados
        $arrInteressados = array();
        $Procedimento['Interessados'] = $arrInteressados;

        //Observa��es
        #$Procedimento['Observacao'] = 'Observa��es para Teste de Inser��o de Dados';
        $Procedimento['Observacao'] = null;

        //Nivel de Acesso [0-Publico, 1-Restrito, 2-Sigiloso, NULL-Nivel de acesso do tipo de projeto cadastrado no SEI]
        $Procedimento['NivelAcesso'] = 0;
        #Fim dos Dados do Procedimento

        //Procedimentos Relacionados
        #$ProcedimentosRelacionados = array('1210000004770');
        $ProcedimentosRelacionados = array();

        //Unidades de Envio
        #$UnidadesEnvio = array('110000015', '100000983');
        $UnidadesEnvio = array();

        #Inicio dos Dados dos Documentos Gerados
        //Documentos Gerados
        $DocumentoGerado = array();
        //Documentos Recebidos
        $DocumentoRecebido = array();


        //Nome do M�todo Remoto a ser Chamado
        $mixResult = $objSoapCliente->gerarProcedimento($txSiglaSistema, $txIdentificacaoServico, $nrIdUnidade, $Procedimento, array(), $ProcedimentosRelacionados, $UnidadesEnvio);

        return $mixResult;
    }


    /**
     * Metodo chamado quando o objeto da classe e serializado
     *
     * @return VOID
     */
    public function __sleep()
    {
    }

    /**
     * Metodo chamado quando o objeto da classe e unserializado
     *
     * @return VOID
     */
    public function __wakeup()
    {
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
        debug("O metodo " . $strMethod . " nao foi encontrado na classe " . get_class($this) . ".<br />" . __FILE__ . "(linha " . __LINE__ . ")", 1);
    }
}
