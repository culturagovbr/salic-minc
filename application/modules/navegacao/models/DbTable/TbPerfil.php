<?php

class Navegacao_Model_DbTable_TbPerfil extends MinC_Db_Table_Abstract
{
    protected $_banco = "tabelas";
    protected $_name = 'usuarios';
    protected $_schema = 'tabelas';
    protected $_primary = 'usu_codigo';
    protected $_sequence = false;

    public function buscarPerfisDisponiveis(
        $usu_codigo,
        $sis_codigo = null,
        $gru_codigo = null,
        $uog_orgao = null
    ){
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            'vwusuariosorgaosgrupos',
            array(
                'usu_orgao'
            , 'usu_orgaolotacao'
            , 'uog_orgao'
            , 'org_siglaautorizado'
            , 'org_nomeautorizado'
            , 'gru_codigo'
            , 'gru_nome'
            , 'org_superior'
            , 'uog_status'
            , 'id_unico'
            ),
            $this->_schema
        );
        $sql->where('usu_codigo = ?', $usu_codigo);
        $sql->where('uog_status = ?', 1);

        if (!empty($sis_codigo)) {
            $sql->where('sis_codigo = ?', $sis_codigo);
        }
        if (!empty($gru_codigo)) {
            $sql->where('gru_codigo = ?', $gru_codigo);
        }
        if (!empty($uog_orgao)) {
            $sql->where('uog_orgao = ?', $uog_orgao);
        }
        $sql->where('gru_codigo <> ?', 129);
        $sql->order('org_siglaautorizado ASC');
        $sql->order('gru_nome ASC');

        return $this->fetchAll($sql);
    }

    public function getIdUsuario($usu_codigo = null, $usu_identificacao = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('u' => $this->_name),
            array(
                'u.usu_codigo',
                'u.usu_identificacao'
            ),
            $this->_schema
        );

        $select->joinInner(
            array('a' => 'agentes'),
            'u.usu_identificacao = a.cnpjcpf',
            array('a.idagente'),
            parent::getSchema('agentes')
        );

        if (!empty($usu_codigo)) {
            $select->where("usu_codigo = ? ", $usu_codigo);
        }
        if (!empty($usu_identificacao)) {
            $select->where("usu_identificacao = ? ", $usu_identificacao);
        }
        try {
            $result = $this->fetchRow($select);
            return ($result) ? $result->toArray() : $result;
        } catch (Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
    }
}
