<?php

class Arquivo_Model_DbTable_TbArquivo extends MinC_Db_Table_Abstract
{
    protected $_schema = "bdcorporativo.scCorp";
    protected $_name = "tbArquivo";
    protected $_primary = "idArquivo";

    /**
     * Metodo para buscar um arquivo pelo seu id
     * @access public
     * @param integer $idArquivo
     * @return array
     */
    public function buscarDados($idArquivo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array("a" => $this->_name),
            array("a.idArquivo"
            , "a.nmArquivo"
            , "a.sgExtensao"
            , "a.nrTamanho"
            , "a.dtEnvio"
            , "a.dsHash"
            , "a.stAtivo"
            , "a.dsTipoPadronizado"
            , "a.idUsuario"), 'BDCORPORATIVO.scCorp'
        );
        $select->joinInner(
            array("i" => "tbArquivoImagem"), "a.idArquivo = i.idArquivo",
            array("i.biArquivo"), 'BDCORPORATIVO.scCorp'
        );

        $select->where("a.idArquivo = ?", $idArquivo);

        return $this->fetchRow($select);
    } // fecha metodo buscarDados()

    public static function buscarArquivo($id)
    {
        $sql = "SELECT A.nmArquivo, AI.biArquivo 
				FROM BDCORPORATIVO.scCorp.tbArquivo A 
				INNER JOIN BDCORPORATIVO.scCorp.tbArquivoImagem AI ON AI.idArquivo = A.idArquivo
				WHERE A.idArquivo = " . $id;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 10485760");
        $resultado = $db->fetchAll($sql);
        return $resultado;

    }

    /**
     * Metodo para buscar o ultimo registro
     * @access public
     * @return int
     */
    public function buscarUltimo()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array($this->_name),
            array('*'), 'BDCORPORATIVO.scCorp');


        $select->order('idArquivo desc');
        $select->limit('1', '0');

        return $this->fetchRow($select)->toArray();
    } // fecha metodo buscarDados()


    /**
     * Metodo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o ?ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha metodo cadastrarDados()


    /**
     * Metodo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idArquivo = " . $where;
        return $this->update($dados, $where);
    }

    /**
     * Metodo para excluir
     * @access public
     * @param integer $where
     * @return integer (quantidade de registros excluidos)
     */
    public function excluirDados($where)
    {
        $where = "idArquivo = " . $where;
        return $this->delete($where);
    }


    /**
     * Metodo para verificar se o arquivo existe (pelo hash)
     * @access public
     * @param string $dsHash
     * @return array || bool
     */
    public function verificarHash($dsHash)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from($this, "idArquivo");

        $select->where("dsHash = ?", $dsHash);

        return $this->fetchRow($select);
    }


    public function excluir($where)
    {
        return $this->delete($where);
    }

}