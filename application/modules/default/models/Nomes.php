<?php
/**
 * DAO Nomes
 * @author emanuel.sampaio - Politec
 * @since 19/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Nomes extends MinC_Db_Table_Abstract
{
    protected $_banco  = "AGENTES";
    protected $_schema = "AGENTES";
    protected $_name   = "Nomes";

    /**
     * M�todo para buscar o(s) nome(s) do agente
     * @access public
     * @param string $cpfcnpj
     * @param integer $idAgente
     * @param integer $statusAgente (0 = ATIVADO, 1 = DESATIVADO)
     * @param integer $statusNome (0 = ATIVADO, 1 = DESATIVADO)
     * @param boolean $buscarTodos (informa se busca todos ou somente um)
     * @return array || object
     */
    public function buscarNomePorCPFCNPJ($cpfcnpj = null, $idAgente = null, $statusAgente = null, $statusNome = null, $buscarTodos = true)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("n" => $this->_name),
            array("n.idNome"
                ,"n.Descricao AS Nome")
        );
        $select->joinInner(
            array("a" => "Agentes"),
            "a.idAgente = n.idAgente",
            array()
        );

        // busca pelo cnpj ou cpf
        if (!empty($cpfcnpj)) {
            $select->where("a.CNPJCPF = ?", $cpfcnpj);
        }

        // busca pelo id do agente
        if (!empty($idAgente)) {
            $select->where("a.idAgente = ?", $idAgente);
        }

        // busca pelo agente ativado/desativado
        if (!empty($statusAgente)) {
            $select->where("a.Status = ?", $statusAgente);
        }

        // busca pelo nome ativado/desativado
        if (!empty($statusNome)) {
            $select->where("n.Status = ?", $statusNome);
        }

        return $buscarTodos ? $this->fetchAll($select) : $this->fetchRow($select);
    } // fecha m�todo buscarNomePorCPFCNPJ()



    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarNomeAgente($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarNomeAgente()



    /**
     * M�todo para excluir
     * @access public
     * @param integer $idAgente (excluir todos os nomes de um agente)
     * @param integer $idNome (excluir um determinado nome)
     * @return integer (quantidade de registros exclu�dos)
     */
    public function excluirNomeAgente($idAgente = null, $idNome = null)
    {
        // exclui todos os nomes de um agente
        if (!empty($idAgente)) {
            $where = "idAgente = " . $idAgente;
        }

        // exclui um determinado nome
        elseif (!empty($idNome)) {
            $where = "idNome = " . $idNome;
        }

        return $this->delete($where);
    } // fecha m�todo excluirNomeAgente()


    public function buscarPareceristas($idOrgao = '')
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("nm" => $this->_name),
            array("nm.Descricao AS Nome")
        );
        $select->joinInner(
            array("age" => "Agentes"),
            "age.idAgente = nm.idAgente",
            array('age.idAgente AS id','age.idAgente')
        );
        $select->joinInner(
            array("usu" => "Usuarios"),
                    "age.CNPJCPF = usu.usu_identificacao",
                    array(),
                    'TABELAS.dbo'
        );
        $select->joinInner(
            array("uxoxg" => "UsuariosXOrgaosXGrupos"),
                    "usu.usu_codigo = uxoxg.uog_usuario",
                    array(),
                    'TABELAS.dbo'
        );
        $select->where('uxoxg.uog_grupo = 94');
        if ($idOrgao) {
            $select->where('uxoxg.uog_orgao = ?', $idOrgao);
        }
        $select->group(array('nm.Descricao','age.idAgente'));

        return $this->fetchAll($select);
    }
} // fecha class
