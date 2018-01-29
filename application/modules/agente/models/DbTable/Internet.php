<?php

class Agente_Model_DbTable_Internet extends MinC_Db_Table_Abstract
{
    /**
     * @var string
     */
    protected $_schema = 'agentes';

    /**
     * @var bool
     */
    protected $_name = 'internet';

    /**
     * @var bool
     */
    protected $_primary = 'idInternet';

    /**
     * @param string $email
     * @param string $assunto
     * @param string $texto
     * @param string $perfil
     * @param string $formato
     * @return void
     * @todo retirar SP, n�o foi encontrada uso do metodo no sistema, proposta de remo��o.
     */
    public function enviarEmail($email, $assunto, $texto, $perfil = "PerfilGrupoPRONAC", $formato = "HTML")
    {
        $sql = "EXEC msdb.dbo.sp_send_dbmail
                    @profile_name          = '" . $perfil . "'
                    ,@recipients           = '" . $email . "'
                    ,@body                 = '" . $texto . "'
                    ,@body_format          = '" . $formato . "'
                    ,@subject              = '" . $assunto . "'
                    ,@exclude_query_output = 1;";

        return $this->getAdapter()->query($sql);
    }

    /**
     * Metodo para buscar o(s) e-mail(s) do agente
     * @access public
     * @param string $cpfcnpj
     * @param integer $idAgente
     * @param integer $statusEmail (1 = ATIVADO, 0 = DESATIVADO)
     * @param integer $statusDivulgacao (1 = ATIVADO, 0 = DESATIVADO)
     * @param boolean $buscarTodos (informa se busca todos ou somente um)
     * @return array || object
     */
    public function buscarEmailAgente($cpfcnpj = null, $idAgente = null, $statusEmail = null, $statusDivulgacao = null, $buscarTodos = true)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("e" => $this->_name),
            array("e.idinternet as idemail"
            ,"e.tipointernet as tipoemail"
            ,"e.descricao as email"
            ,"e.status"
            ,"e.divulgar"),
            $this->_schema
        );

        $select->join(
            array("a" => "agentes"),
            "a.idAgente = e.idagente",
            array(),
            $this->_schema
        );

        // busca pelo cnpj ou cpf
        if (!empty($cpfcnpj)) {
            $select->where("a.cnpjcpf = ?", $cpfcnpj);
        }

        // busca pelo id do agente
        if (!empty($idAgente)) {
            $select->where("a.idagente = ?", $idAgente);
        }

        // busca pelo email ativado/desativado
        if (!empty($statusEmail)) {
            $select->where("e.status = ?", $statusEmail);
        }

        // busca pelo email de divulgacao
        if (!empty($statusDivulgacao)) {
            $select->where("e.divulgar = ?", $statusDivulgacao);
        }

        return $buscarTodos ? $this->fetchAll($select) : $this->fetchRow($select);
    }

    /**
     * Metodo para buscar os e-mails do agente
     *
     * @access public
     * @static
     * @param integer $idAgente
     * @return object
     */
    public function buscarEmails($idAgente = null)
    {
        $tblAgentes = new Agente_Model_DbTable_Agentes();

        $i = array(
            'i.idinternet',
            'i.idagente',
            'i.tipointernet',
            'i.descricao',
            'i.status',
            'i.divulgar'
        );

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('i' => 'internet'), $i, $this->_schema)
            ->join(array('v' => 'verificacao'), 'i.tipointernet = v.idverificacao', 'v.descricao as tipo', $this->_schema)
            ->join(array('t' => 'tipo'), 't.idtipo = v.idtipo', null, $this->_schema);

        if (!empty($idAgente)) {// busca de acordo com o id do agente

            $sql->where('i.idagente = ?', $idAgente);
        }

        return $this->fetchAll($sql);
    }

    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o ultimo id cadastrado)
     */
    public function cadastrarEmailAgente($dados)
    {
        return $this->insert($dados);
    }

    /**
     * Metodo para excluir
     * @access public
     * @param integer $idAgente (excluir todos os e-mails de um agente)
     * @param integer $idInternet (excluir um determinado e-mail)
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirEmailAgente($idAgente = null, $idInternet = null)
    {
        // exclui todos os e-mails de um agente
        if (!empty($idAgente)) {
            $where['idAgente = ?'] = $idAgente;
        }

        // exclui um determinado e-mail
        elseif (!empty($idInternet)) {
            $where['idInternet = ?'] = $idInternet;
        }

        return $this->delete($where);
    }

    public function obterEmailProponentesPorPreProjeto($idPreProjeto)
    {
        $select = $this->select();
        $this->_db->setFetchMode(Zend_DB::FETCH_OBJ);

        $select->setIntegrityCheck(false);
        $select->from(
            array("Internet"),
            array('Internet.Descricao'),
            $this->_schema
        );

        $select->joinInner(array("PreProjeto" => "PreProjeto"), 'PreProjeto.idAgente = Internet.idAgente', array(), $this->getSchema("sac"));
        $select->where("PreProjeto.idPreProjeto = ?", array($idPreProjeto));
        $select->where("Internet.Status = ?", array(1));

        return $this->_db->fetchAll($select);
    }
}
