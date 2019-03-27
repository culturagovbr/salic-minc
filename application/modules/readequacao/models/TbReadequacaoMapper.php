<?php

class Readequacao_Model_TbReadequacaoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Readequacao_Model_DbTable_TbReadequacao');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function salvarSolicitacaoReadequacao($arrData)
    {
        try {

            $auth = Zend_Auth::getInstance();
            $tblAgente = new Agente_Model_DbTable_Agentes();
            $rsAgente = $tblAgente->buscar(array('CNPJCPF=?' => $auth->getIdentity()->Cpf))->current();

            $objReadequacao = new Readequacao_Model_TbReadequacao();
            $objReadequacao->setIdReadequacao($arrData['idReadequacao']);
            $objReadequacao->setIdPronac($arrData['idPronac']);
            $objReadequacao->setDtSolicitacao(new Zend_Db_Expr('GETDATE()'));
            $objReadequacao->setIdSolicitante($rsAgente->idAgente);
            $objReadequacao->setStAtendimento('N');
            $objReadequacao->setSiEncaminhamento(Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE);
            $objReadequacao->setStEstado(0);

            if (isset($arrData['stAtendimento'])) {
                $objReadequacao->setStAtendimento($arrData['stAtendimento']);
            }
            
            if (isset($arrData['idTipoReadequacao'])) {
                $objReadequacao->setIdTipoReadequacao($arrData['idTipoReadequacao']);
            }

            if (isset($arrData['dsJustificativa'])) {
                $objReadequacao->setDsJustificativa($arrData['dsJustificativa']);
            }

            if (isset($arrData['dsSolicitacao'])) {
                $objReadequacao->setDsSolicitacao($arrData['dsSolicitacao']);
            }

            if ($arrData['idDocumento']) {
                $objReadequacao->setIdDocumento($arrData['idDocumento']);
            }

            $id = $this->save($objReadequacao);
            if ($this->getMessage()) {
                throw new Exception($this->getMessage());
            }

            return $id;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function finalizarSolicitacaoReadequacao($idPronac, $idTipoReadequacao = null, $idReadequacao = null)
    {
        try {

            if (empty($idPronac)) {
                throw new Exception("Pronac &eacute; obrigat&oacute;rio");
            }

            $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();

            $where = [];
            if ($idTipoReadequacao) {
                $where[] = $tbReadequacao->getAdapter()->quoteInto('idTipoReadequacao = ?', (int)$idTipoReadequacao);
            }

            if ($idReadequacao) {
                $where[] = $tbReadequacao->getAdapter()->quoteInto('idReadequacao = ?', (int)$idReadequacao);
            }

            $where[] = $tbReadequacao->getAdapter()->quoteInto('idPronac = ?', (int)$idPronac);
            $where[] = $tbReadequacao->getAdapter()->quoteInto('siEncaminhamento = ?', Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE);
            $where[] = $tbReadequacao->getAdapter()->quoteInto('stEstado = ?', 0);

            $dados = array();
            $dados['siEncaminhamento'] = Readequacao_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_ENVIADO_MINC;
            $dados['dtEnvio'] = new Zend_Db_Expr('GETDATE()');

            return $tbReadequacao->update($dados, $where);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function existeSolicitacaoEmAnalise($idPronac, $idTipoReadequacao = null) {

        $whereSolicitacaoEmAndamento = [];
        $whereSolicitacaoEmAndamento['idPronac = ?'] = $idPronac;
        $whereSolicitacaoEmAndamento['siEncaminhamento <> ?'] = TbTipoEncaminhamento::SOLICITACAO_CADASTRADA_PELO_PROPONENTE;
        $whereSolicitacaoEmAndamento['stEstado = ?'] = 0;

        if ($idTipoReadequacao) {
            $whereSolicitacaoEmAndamento['idTipoReadequacao = ?'] = $idTipoReadequacao;
        }

        $tbReadequacao = new Readequacao_Model_DbTable_TbReadequacao();
        return (count($tbReadequacao->buscar($whereSolicitacaoEmAndamento)->toArray()) > 0);
    }
}
