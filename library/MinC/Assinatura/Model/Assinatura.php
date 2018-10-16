<?php

namespace MinC\Assinatura\Model;
use Mockery\Exception;

/**
 * Class Assinatura
 * @package MinC\Assinatura\Model
 *
 * @var \Assinatura_Model_TbAssinatura $modeloTbAssinatura
 * @var \Assinatura_Model_TbAtoAdministrativo $modeloTbAtoAdministrativo
 * @var \Proposta_Model_TbDespacho $modeloTbDespacho
 * @var \Assinatura_Model_DbTable_TbAssinatura $dbTableTbAssinatura
 */
class Assinatura implements IModel
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    public $request;
    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    public $response;

    public $modeloTbAssinatura;
    public $modeloTbAtoAdministrativo;
    public $modeloTbDespacho;
    public $modeloTbDocumentoAssinatura;
    public $dbTableTbAssinatura;

    public function __construct(array $dadosModelo = [])
    {
        $this->modeloTbAssinatura = new \Assinatura_Model_TbAssinatura($dadosModelo);
        $this->modeloTbAtoAdministrativo = new \Assinatura_Model_TbAtoAdministrativo($dadosModelo);

        $dadosModelo['Tipo'] = $dadosModelo['idTipoDoAto'];
        $this->modeloTbDespacho = new \Proposta_Model_TbDespacho($dadosModelo);

        $this->modeloTbDocumentoAssinatura = new \Assinatura_Model_TbDocumentoAssinatura($dadosModelo);
        if($this->modeloTbAssinatura->getIdPronac() && $this->modeloTbAtoAdministrativo->getIdTipoDoAto()) {
            $this->carregarDocumentoDeAssinatura();
        }
        $this->dbTableTbAssinatura = new \Assinatura_Model_DbTable_TbAssinatura();
    }

    public function definirModeloTbDocumentoAssinatura(array $dados)
    {
        $this->modeloTbDocumentoAssinatura = new \Assinatura_Model_TbDocumentoAssinatura($dados);
        return $this;
    }

    public function carregarDocumentoDeAssinatura()
    {
        if(!$this->modeloTbAssinatura->getIdPronac()) {
            throw new Exception("Identificador do Projeto n&atilde;o informado.");
        }
        if(!$this->modeloTbAtoAdministrativo->getIdTipoDoAto()) {
            throw new Exception("Identificador do Tipo do Ato Administrativo n&atilde;o informado.");
        }

        $documentoAssinaturaDbTable = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $documentoAssinatura = $documentoAssinaturaDbTable->findBy(
            array(
                'IdPRONAC' => $this->modeloTbAssinatura->getIdPronac(),
                'idTipoDoAtoAdministrativo' => $this->modeloTbAtoAdministrativo->getIdTipoDoAto(),
                'cdSituacao' => \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_DISPONIVEL_PARA_ASSINATURA,
                'stEstado' => \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO
            )
        );
        $this->definirModeloTbDocumentoAssinatura($documentoAssinatura);
    }
}