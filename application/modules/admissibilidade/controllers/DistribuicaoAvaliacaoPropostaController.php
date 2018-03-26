<?php

class Admissibilidade_DistribuicaoAvaliacaoPropostaController extends MinC_Controller_Action_Abstract
{
    protected $grupoAtivo;

    public function init()
    {
        parent::perfil();
        parent::init();
        $this->auth = Zend_Auth::getInstance();
        $this->grupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
    }

    public function indexAction()
    {
        throw new Exception("M&eacute;todo n&atilde;o implementado.");
    }

    public function encaminharPropostaAjaxAction()
    {
        $resposta = false;
        try {
            $post = $this->getRequest()->getPost();
            $id_preprojeto = trim($post['id_preprojeto']);
            if (empty($id_preprojeto)) {
                throw new Exception("Identificador da Proposta nao informado.");
            }

            $id_perfil = trim($post['id_perfil']);
            if (empty($id_perfil)) {
                throw new Exception("Identificador do Perfil nao informado.");
            }

            $orgaoDbTable = new Orgaos();
            $resultadoOrgaoSuperior = $orgaoDbTable->codigoOrgaoSuperior($this->grupoAtivo->codOrgao);
            $orgaoSuperior = $resultadoOrgaoSuperior[0]['Superior'];

            $dadosEncaminhamentoProposta = array(
                'id_preprojeto' => $post['id_preprojeto'],
                'id_orgao_superior' => $orgaoSuperior,
                'id_perfil' => $id_perfil,
                'data_distribuicao' => $orgaoDbTable->getExpressionDate(),
                'avaliacao_atual' => Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta::AVALIACAO_ATUAL_ATIVA
            );

            $distribuicaoAvaliacaoPropostaDbTable = new Admissibilidade_Model_DbTable_DistribuicaoAvaliacaoProposta();
            $distribuicaoAvaliacaoPropostaDbTable->inativarAvaliacoesProposta($post['id_preprojeto']);
            $distribuicaoAvaliacaoPropostaDbTable->inserir($dadosEncaminhamentoProposta);

            $msg = 'Proposta encaminhada com sucesso!';
            $resposta = true;
        } catch (Exception $objException) {
            $msg = $objException->getMessage();
        }

        $this->_helper->json(
            [
                'resposta' => $resposta,
                'mensagem' => $msg
            ]
        );
    }
}