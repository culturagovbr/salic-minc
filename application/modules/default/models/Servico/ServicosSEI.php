<?php

/**
 * Classe do componente de SEI que gerencia a
 * comunicacao via webservice entre o NovoSalic e o sistema SEI
 * Este Servi�o � do tipo SOAP
 *
 * @copyright Minist�rio da Cultura
 * @author Hepta/Minc - Alysson Vicu�a de Oliveira
 * @since 18/04/2016
 * @version 1.0
 */
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

        //Sigla do Sistema
        #$txSiglaSistema = "INTRANET";

        //Identifica��o do Procedimento
        #$txIdentificacaoServico = "SALIC";

        //Numero da Unidade
        #$nrIdUnidade = '110000142'; //DINPC

        #Inicio dos Dados do Procedimento
        //Procedimento
        $Procedimento = array();
        #$Procedimento['IdTipoProcedimento'] = '100000316';
        #$Procedimento['Especificacao'] = utf8_encode('Gest�o de Contrato: Acompanhamento da Execu��o'); //Verificar se tem que usar UTF-8 Em Produ��o
        #$Procedimento['IdTipoProcedimento'] = '1'; #Alysson - C�digo do Tipo de Processo "Projetos Culturais em Homologa��o";
        $Procedimento['IdTipoProcedimento'] = '100000520'; #Alysson - C�digo do Tipo de Processo "Apoio Cultural: Projeto Cultural" em Produ��o;
        $Procedimento['Especificacao'] = utf8_encode('Apoio Cultural: Projeto Cultural'); //Verificar se tem que usar UTF-8 Em Produ��o

        //Assuntos
        $arrAssuntos = array();
        $arrAssuntos[] = array('CodigoEstruturado' => '067.1');
        #$arrAssuntos[] = array('CodigoEstruturado'=>'930.b');
        #$arrAssuntos[] = array('CodigoEstruturado'=>'930.c');
        $Procedimento['Assuntos'] = $arrAssuntos; //Atribui os Assuntos aos Dados do Procedimento

        //Interessados
        $arrInteressados = array();
        #$arrInteressados[] = array('Sigla'=>'dgx', 'Nome'=>'Alberto');
        #$arrInteressados[] = array('Sigla'=>'utv', 'Nome'=>'Maria');
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
        #$DocumentoGerado['Tipo'] = 'G';

        //Se incluindo em um processo existente, informar o ID neste campo
        //Se incluindo o documento no momento da gera��o do processo, passar NULL
        #$DocumentoGerado['IdProcedimento'] = null;

        #$DocumentoGerado['IdSerie'] = '3'; //Portaria
        #$DocumentoGerado['Numero'] = null;
        #$DocumentoGerado['Data'] = null;
        #$DocumentoGerado['Descricao'] = 'Descri��o de Teste do Documento';
        #$DocumentoGerado['Remetente'] = null;

        //Interessados no Documento Gerado
        #$arrInteressadosDocumentoGerado = array();
        #$arrInteressadosDocumentoGerado[] = array('Sigla'=>'dgx', 'Nome'=>'Alberto');
        #$arrInteressadosDocumentoGerado[] = array('Sigla'=>'utv', 'Nome'=>'Maria');
        #$DocumentoGerado['Interessados'] = $arrInteressadosDocumentoGerado;

        //Destinat�rios para Documento Gerado
        #$arrDestinatarios = array();
        #$arrDestinatarios[] = array('Sigla'=>'dgx', 'Nome'=>'Alberto');
        #$arrDestinatarios[] = array('Sigla'=>'dgx', 'Nome'=>'Alberto');
        #$DocumentoGerado['Destinatarios'] = $arrDestinatarios;

        #$DocumentoGerado['Observacao'] = 'Observa��o de Teste';
        #$DocumentoGerado['NomeArquivo'] = 'null';
        #$DocumentoGerado['Conteudo'] = base64_encode('Conteudo do Documento. xxxxx');
        #$DocumentoGerado['NivelAcesso'] = 'null';
        #Fim dos dados para Documentos Gerados

        #Inicio dos Dados de Documentos Recebidos
        //Documentos Recebidos
        $DocumentoRecebido = array();
        #$DocumentoRecebido['Tipo'] = 'R';

        //Se incluindo em um processo existente, informar o ID neste campo
        //Se incluindo o documento no momento da gera��o do processo, passar NULL
        #$DocumentoRecebido['IdProcedimento'] = null;

        #$DocumentoRecebido['IdSerie'] = '301'; //Oficio
        #$DocumentoRecebido['Numero'] = '1000';
        #$DocumentoRecebido['Data'] = '10/09/2011';
        #$DocumentoRecebido['Descricao'] = 'Descri��o de Teste do Documento';
        #$DocumentoRecebido['Remetente'] = array('Sigla'=>'dgx', 'Nome'=>'Alberto');

        //Interessados no Documento Recebido
        #$arrInteressadosDocumentoRecebido = array();
        #$arrInteressadosDocumentoRecebido[] = array('Sigla'=>'dgx', 'Nome'=>'Alberto');
        #$arrInteressadosDocumentoRecebido[] = array('Sigla'=>'utv', 'Nome'=>'Maria');
        #$DocumentoRecebido['Interessados'] = $arrInteressadosDocumentoRecebido;

        //Destinat�rios para Documento Rebido
        #$arrDestinatarios = array();
        #$arrDestinatarios[] = array('Sigla'=>'dgx', 'Nome'=>'Alberto');
        #$arrDestinatarios[] = array('Sigla'=>'dgx', 'Nome'=>'Alberto');
        #$DocumentoRecebido['Destinatarios'] = null;

        #$DocumentoRecebido['Observacao'] = 'Observacoes de Teste';
        #$DocumentoRecebido['NomeArquivo'] = 'Oficio.txt';
        #$DocumentoRecebido['Conteudo'] = base64_encode('Conteudo do Documento txt');

        //Para MTOM
        #$DocumentoRecebido['Conteudo'] = '';
        #$DocumentoRecebido['ConteudoMTOM'] = file_get_contents(dirname(__FILE__).'/OFIC832014CEF.pdf');

        #$DocumentoRecebido['NivelAcesso'] = 'null';
        #Fim dos dados para Documentos Recebido

        ###################################################################################################################################################################
        //1 - gera processo
        //$ret = $objWS->gerarProcedimento('Corregedoria','Suspei��o/Impedimento', $numIdUnidade, $Procedimento, array(),$ProcedimentosRelacionados,  $UnidadesEnvio);

        //2 - gera processo + documento gerado
        //$ret = $objWS->gerarProcedimento('Corregedoria','Suspei��o/Impedimento', $numIdUnidade, $Procedimento, array($DocumentoGerado),  array(),$UnidadesEnvio);

        //3 - gera processo + documento gerado + documento externo
        //$ret = $objWS->gerarProcedimento('Corregedoria','Suspei��o/Impedimento', $numIdUnidade, $Procedimento, array($DocumentoGerado,  $DocumentoRecebido));

        //4 - inclui documento gerado em processo existente
        //$DocumentoGerado['IdProcedimento'] deve estar com o id preenchido
        //$ret = $objWS->incluirDocumento('Corregedoria','Suspei��o/Impedimento', $numIdUnidade, $DocumentoGerado);

        //5 -inclui documento externo em processo existente
        //$DocumentoRecebido['IdProcedimento'] deve estar com o id preenchido
        //$ret = $objWS->incluirDocumento('Corregedoria','Suspei��o/Impedimento', $numIdUnidade, $DocumentoRecebido);
        ###################################################################################################################################################################

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

    /*
     ###Exemplo de Instancia do WebService
        $wsWebServiceSEI = new ServicosSEI();
        $arrRetornoGerarProcedimento = $wsWebServiceSEI->wsGerarProcedimento();
        $chars = array(".","/","-");
        $nrProcessoSemFormatacao = str_replace($chars,"",$arrRetornoGerarProcedimento->ProcedimentoFormatado);
        $nrProcesso = $nrProcessoSemFormatacao;
     */
} // end Utils_Wsdne
