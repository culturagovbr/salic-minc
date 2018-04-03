<?php

/**
 * DAO tbArquivo
 * @author emanuel.sampaio - Politec
 * @since 19/02/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright  2011 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class tbArquivo extends MinC_Db_Table_Abstract
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
            , "a.idUsuario"),
            'BDCORPORATIVO.scCorp'
        );
        $select->joinInner(
            array("i" => "tbArquivoImagem"),
            "a.idArquivo = i.idArquivo",
            array("i.biArquivo"),
            'BDCORPORATIVO.scCorp'
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

    /**
     * Apagar todos os Arquivos e relacionamentos das tabelas
     * @access public
     * @param integer $idArquivo
     * @param integer $idDocumento
     * @return bool
     */
    public function excluirArquivosDocumentais($idArquivo, $idDocumento)
    {
        if (empty($idArquivo) || empty($idArquivo)) {
            return false;
        }

        try{
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            $db->beginTransaction();

            $db->delete("BDCORPORATIVO.scCorp.tbDocumento","idArquivo = {$idArquivo} and idDocumento= {$idDocumento} ");

            $db->delete("BDCORPORATIVO.scCorp.tbArquivoImagem","idArquivo =  {$idArquivo} ");

            $db->delete("{$this->_schema}.{$this->_name}", "idArquivo = {$idArquivo} ");

            $db->commit();
            
            return true;
        }catch (Exception $e) {
            $db->rollBack();

            return false;
        }
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

    public function abrirdocumentosanexadosbinarioAction()
    {
        // recebe o id do arquivo via get
//        $get = Zend_Registry::get('get');
//        $id = (int)$get->id;
//        $busca = $this->_request->getParam('busca'); //$get->busca;
//        // Configuracao o php.ini para 10MB
//        @ini_set("mssql.textsize", 10485760);
//        @ini_set("mssql.textlimit", 10485760);
//        @ini_set("upload_max_filesize", "10M");
//
//        $response = new Zend_Controller_Response_Http;
//
//        // busca o arquivo
//        $resultado = UploadDAO::abrirdocumentosanexados($id, $busca);
//        if (!$resultado) {
//            if ($busca == "documentosanexadosminc") {
//                $resultado = UploadDAO::abrirdocumentosanexados($id, "documentosanexadosminc");
//            } else {
//                $resultado = UploadDAO::abrirdocumentosanexados($id, "documentosanexados");
//            }
//        }
//
//        // erro ao abrir o arquivo
//        if (!$resultado) {
//            $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
//            $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
//            die("N&atilde;o existe o arquivo especificado");
//            $this->view->message = 'N&atilde;o foi poss&iacute;vel abrir o arquivo!';
//            $this->view->message_type = 'ERROR';
//        } else {
//            // os cabecalhos formatado
//            foreach ($resultado as $r) {
//                $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
//                $this->_helper->viewRenderer->setNoRender();    // Desabilita o Zend Render
//                Zend_Layout::getMvcInstance()->disableLayout(); // Desabilita o Zend MVC
//                $this->_response->clearBody();                  // Limpa o corpo html
//                $this->_response->clearHeaders();               // Limpa os headers do Zend
//
//                $hashArquivo = ($r->biArquivo) ? $r->biArquivo : $r->biArquivo2;
//
//                $this->getResponse()
//                    ->setHeader('Content-Type', 'application/pdf')
//                    ->setHeader('Content-Disposition', 'attachment; filename="' . $r->nmArquivo . '"')
//                    ->setHeader("Connection", "close")
//                    ->setHeader("Content-transfer-encoding", "binary")
//                    ->setHeader("Cache-control", "private");
//
//                if ($r->biArquivo2 == 1) {
//                    if (strtolower(substr($r->biArquivo, 0, 4)) == '%pdf') {
//                        $this->getResponse()->setBody($hashArquivo);
//                    } else {
//                        $this->getResponse()->setBody(base64_decode($hashArquivo));
//                    }
//                } else {
//                    $this->getResponse()->setBody($hashArquivo);
//                }
//                        $this->getResponse()->setBody($hashArquivo);
                //->setBody(base64_decode($hashArquivo));
//            } // fecha foreach
//        }
    }
}
