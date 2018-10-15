<?php

namespace Application\Modules\Readequacao\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoDevolver;

class Devolver implements IAcaoDevolver
{
    public function init()
    {
        parent::init();

        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
    }
    
    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $idTipoDoAtoAdministrativo = $assinatura->modeloTbDocumentoAssinatura->getIdTipoDoAtoAdministrativo();
        $documentoAssinaturaDbTable = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        
        $documentoAssinatura = $documentoAssinaturaDbTable->findBy(
            [
                'IdPRONAC' => $assinatura->modeloTbAssinatura->getIdPronac(),
                'idTipoDoAtoAdministrativo' => (int)$assinatura->modeloTbAtoAdministrativo->getIdTipoDoAto(),
                'cdSituacao' => (int)\Assinatura_Model_TbDocumentoAssinatura::CD_SITUACAO_FECHADO_PARA_ASSINATURA,
                'stEstado = ?' => (int)\Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_INATIVO
            ]
        );
        
        $tbReadequacaoXParecerDbTable = new \Readequacao_Model_DbTable_TbReadequacaoXParecer();
        $tbReadequacaoXParecer = $tbReadequacaoXParecerDbTable->findBy([
            'idParecer' => $documentoAssinatura['idAtoDeGestao']
        ]);
        $idReadequacao = $tbReadequacaoXParecer['idReadequacao'];
        
        $atoAdministrativo = $assinatura->modeloTbAtoAdministrativo;
        
        switch ($atoAdministrativo->getIdPerfilDoAssinante()) {
            case \Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_AO_MINC;
                break;            
            case \Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO;
                break;
            case \Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_COORDENADOR_GERAL;
                break;               
            case \Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_DIRETOR;
            case \Autenticacao_Model_Grupos::SECRETARIO:
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_SECRETARIO;
                break;
            case \Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA:                
                $siEncaminhamento = \Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_DE_PARECER_PELO_PRESIDENTE;
                break;
        }

        if($siEncaminhamento) {
            $dados = ['siEncaminhamento' => $siEncaminhamento];
            $where = "idReadequacao = {$idReadequacao}";
            $tbReadequacao = new \Readequacao_Model_DbTable_TbReadequacao();
            $tbReadequacao->update($dados, $where);
        }
    }
}
