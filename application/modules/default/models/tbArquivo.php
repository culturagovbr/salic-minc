<?php

class tbArquivo extends MinC_Db_Table_Abstract
{
    protected $_schema = "bdcorporativo.scCorp";
    protected $_name = "tbArquivo";
    protected $_primary = "idArquivo";

    public function buscarArquivoComBinario($idArquivo)
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
            , "a.idUsuario"),
            $this->getSchema('BDCORPORATIVO.scCorp')
        );
        $select->joinInner(
            array("i" => "tbArquivoImagem"),
            "a.idArquivo = i.idArquivo",
            array("i.biArquivo"),
            $this->getSchema('BDCORPORATIVO.scCorp')
        );

        $select->where("a.idArquivo = ?", $idArquivo);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->fetchAll("SET TEXTSIZE 10485760");
        return $this->fetchRow($select);
    }

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

        $select->from(
            array($this->_name),
            array('*'),
            'BDCORPORATIVO.scCorp'
        );


        $select->order('idArquivo desc');
        $select->limit('1', '0');

        return $this->fetchRow($select)->toArray();
    } // fecha metodo buscarDados()


    /**
     * Metodo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
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
     * @return integer (quantidade de registros exclu�dos)
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

    public function removerAnexoDoRecursoDaPropostaVisaoProponente(array $recursoProposta, $idProponente)
    {
        try {
            $tbArquivoDbTable = new tbArquivo();

            if (!$recursoProposta['idPreProjeto']) {
                throw new Exception("Identificador da Proposta n&atilde;o foi localizado.");
            }

            if (!$recursoProposta['idArquivo']) {
                throw new Exception("Identificador do Arquivo n&atilde;o informado.");
            }

            if (is_null($idProponente) || empty($idProponente)) {
                throw new Exception("Identificador do Proponente n&atilde;o localizado.");
            }

            $tbArquivoDbTable->findBy([
                'idArquivo' => $recursoProposta['idArquivo'],
                'idUsuario' => $idProponente
            ]);

            $tbArquivoImagemDAO = new tbArquivoImagem();
            $tbArquivoImagemDAO->delete("idArquivo = {$recursoProposta['idArquivo']}");
            $tbArquivoDbTable->delete("idArquivo = {$recursoProposta['idArquivo']}");
            $recursoPropostaDbTable = new Recurso_Model_DbTable_TbRecursoProposta();
            $recursoPropostaDbTable->update([
                'idArquivo' => new Zend_Db_Expr('NULL')
            ], [
                'idRecursoProposta = ?' => $recursoProposta['idRecursoProposta'],
                'idPreProjeto = ?' => $recursoProposta['idPreProjeto']
            ]);
            return true;
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param string $nomeArquivoUpload
     * @param int $tamanhoMaximoUpload
     * @return int|null $idArquivo
     */
    public function uploadAnexoSqlServer(
        $idUsuario, 
        $nomeArquivoUpload = 'arquivo',
        $tamanhoMaximoUpload = 10485760
    )
    {
        $idArquivo = null;
        $file = new Zend_File_Transfer();
        if ($file->isUploaded() && !empty($file->getFileInfo())) {
            $fileInformation = $file->getFileInfo();
            
            $arquivoNome = $fileInformation[$nomeArquivoUpload]['name'];
            $arquivoTemp = $fileInformation[$nomeArquivoUpload]['tmp_name'];
            $arquivoTamanho = $fileInformation[$nomeArquivoUpload]['size'];
            $arquivoHash = '';
 
            if (!empty($arquivoNome) && !empty($arquivoTemp)) {
                $arquivoExtensao = Upload::getExtensao($arquivoNome);
                $arquivoBinario = Upload::setBinario($arquivoTemp);
                $arquivoHash = Upload::setHash($arquivoTemp);
            }

            if ($arquivoTamanho > $tamanhoMaximoUpload) {
                throw new Exception("O arquivo n&atilde;o pode ser maior do que 10MB!");
            }
            
            if(strtolower(trim($arquivoExtensao)) != 'pdf') {
                throw new Exception("O arquivo deve possuir o formato PDF.");
            }

            $tbArquivoDbTable = new tbArquivo();
            $dadosArquivo = [];
            $dadosArquivo['nmArquivo'] = $arquivoNome;
            $dadosArquivo['sgExtensao'] = $arquivoExtensao;
            $dadosArquivo['nrTamanho'] = $arquivoTamanho;
            $dadosArquivo['dtEnvio'] = $tbArquivoDbTable->getExpressionDate();
            $dadosArquivo['stAtivo'] = 'A';
            $dadosArquivo['dsHash'] = $arquivoHash;
            $dadosArquivo['idUsuario'] = $idUsuario;

            $idArquivo = $tbArquivoDbTable->insert($dadosArquivo);

            $tbArquivoImagemDAO = new tbArquivoImagem();
            $dadosBinario = array(
                'idArquivo' => $idArquivo,
                'biArquivo' => new Zend_Db_Expr("CONVERT(varbinary(MAX), {$arquivoBinario})")
            );
            $idArquivo = $tbArquivoImagemDAO->inserir($dadosBinario);
        }
        return $idArquivo;
    }

}
