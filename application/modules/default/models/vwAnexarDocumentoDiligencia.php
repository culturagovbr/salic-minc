<?php
/**
 * DAO vwAnexarDocumentoDiligencia
 * @since 06/03/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class vwAnexarDocumentoDiligencia extends MinC_Db_Table_Abstract
{

    /* dados da tabela */
    protected $_banco  = 'SAC';
    protected $_schema = 'SAC';
    protected $_name   = 'vwAnexarDocumentoDiligencia';
    protected $_primary = 'idDiligencia';


    public function excluirArquivo($arquivo, $diligencia)
    {
        $where = "idArquivo = " . $arquivo. " AND idDiligencia = " . $diligencia;
        return $this->delete($where);
    }

    public function inserirUploads($dados)
    {
        $name = $dados['nmArquivo'];
        $fileType = $dados['sgExtensao'];
        $data = $dados['biArquivo'];
        $dsDocumento = $dados['dsDocumento'];
        $IdPRONAC = $dados['idPronac'];
        $idTipoDocumento = $dados['idTipoDocumento'];
        $idDiligencia = $dados['idDiligencia'];

        $sql = "INSERT INTO ".$this->_schema.".".$this->_name.
               "(nmArquivo,sgExtensao,dtEnvio,stAtivo,biArquivo,idTipoDocumento,dsDocumento,idPronac,stAtivoDocumentoProjeto,idDiligencia) " .
               "VALUES ('$name', '$fileType', GETDATE(), 'I', $data, $idTipoDocumento, '$dsDocumento', $IdPRONAC, 'E', $idDiligencia)";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
} // fecha class
