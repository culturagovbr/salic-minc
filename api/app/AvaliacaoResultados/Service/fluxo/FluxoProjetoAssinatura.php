<?php

namespace Application\Modules\AvaliacaoResultados\Service\Fluxo;

class FluxoProjetoAssinatura
{
    /**
     * @var \Zend_Controller_Request_Abstract $request
     */
    private $request;

    /**
     * @var \Zend_Controller_Response_Abstract $response
     */
    private $response;

    function __construct(\Zend_Controller_Request_Abstract $request, \Zend_Controller_Response_Abstract $response)
    {

        $GrupoAtivo = new \Zend_Session_Namespace('GrupoAtivo');
        foreach($GrupoAtivo as $chave => $valor){
            $this->{$chave} = $valor;
        }

        $auth = \Zend_Auth::getInstance()->getIdentity();
        foreach($auth as $chave => $valor){
            $this->{$chave} = $valor;
        }

        $this->request = $request;
        $this->response = $response;
    }

    public function obterProjetosAguardandoAssinaturaTecnico()
    {
        $tbAssinaturaDbTable = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $select = $tbAssinaturaDbTable->select();
        $select->setIntegrityCheck(false);
        $select->from(
            ['a' => $tbAssinaturaDbTable->getTableName()],
            '',
            $tbAssinaturaDbTable->getSchema()
        );
        $select->join(
            ['p' => 'projetos'],
            'p.idpronac = a.idpronac',
            ['*', new \Zend_Db_Expr('p.anoprojeto+p.sequencial as PRONAC')],
            $tbAssinaturaDbTable->getSchema()
        );
        $select->where('a.idTipoDoAtoAdministrativo = ?', 622);
        $select->where('a.stEstado = ?', 1);
        $select->where('a.cdSituacao = ?', 1);
        $select->where("
            (SELECT
              count(*)
            FROM
              TbAssinatura
            WHERE
              IdPRONAC = p.IdPRONAC
            AND idDocumentoAssinatura = a.idDocumentoAssinatura) = 0
        ");
        
        return $tbAssinaturaDbTable->fetchAll($select);
    }

    public function obterProjetosAguardandoAssinaturasSuperiores()
    {
        $tbAssinaturaDbTable = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $select = $tbAssinaturaDbTable->select();
        $select->setIntegrityCheck(false);
        $select->from(
            ['a' => $tbAssinaturaDbTable->getTableName()],
            '',
            $tbAssinaturaDbTable->getSchema()
        );
        $select->join(
            ['p' => 'projetos'],
            'p.idpronac = a.idpronac',
            ['*', new \Zend_Db_Expr('p.anoprojeto+p.sequencial as PRONAC')],
            $tbAssinaturaDbTable->getSchema()
        );
        $select->where('a.idTipoDoAtoAdministrativo = ?', 622);
        $select->where('a.stEstado = ?', 1);
        $select->where('a.cdSituacao = ?', 1);
        $select->where("
            (SELECT
              count(*)
            FROM
              TbAssinatura
            WHERE
              IdPRONAC = p.IdPRONAC
            AND idDocumentoAssinatura = a.idDocumentoAssinatura) BETWEEN 1 AND 2
        ");

        return $tbAssinaturaDbTable->fetchAll($select);
    }

    public function obterProjetosComAssinaturasFinalizada($dql = false)
    {
        $tbAssinaturaDbTable = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $select = $tbAssinaturaDbTable->select();
        $select->setIntegrityCheck(false);
        $select->from(
            ['a' => $tbAssinaturaDbTable->getTableName()],
            '',
            $tbAssinaturaDbTable->getSchema()
        );
        $select->join(
            ['p' => 'projetos'],
            'p.idpronac = a.idpronac',
            ['*', new \Zend_Db_Expr('p.anoprojeto+p.sequencial as PRONAC')],
            $tbAssinaturaDbTable->getSchema()
        );
        $select->where('a.idTipoDoAtoAdministrativo = ?', \Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_LAUDO_PRESTACAO_CONTAS);
        $select->where('a.stEstado = ?', \Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO);
        $select->where('a.cdSituacao = ?', \Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA);

        return ($dql) ?
            $select: $tbAssinaturaDbTable->fetchAll($select);
    }

    public function obterProjetosComAssinaturasFinalizadaPorTecnico(){
        $select = $this->obterProjetosComAssinaturasFinalizada(true);
        $select->where('a.idCriadorDocumento = ?', $this->usu_codigo);

        return (new \Assinatura_Model_DbTable_TbDocumentoAssinatura())->fetchAll($select);
    }

}