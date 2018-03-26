<?php
/**
 * DAO vwAnexarDocumentoAgente
 * @since 11/10/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class vwAnexarDocumentoAgente extends MinC_Db_Table_Abstract
{

    /* dados da tabela */
    protected $_banco  = 'SAC';
    protected $_schema = 'SAC';
    protected $_name   = 'vwAnexarDocumentoAgente';
    protected $_primary = 'idArquivo';


    public function excluirArquivo($arquivo)
    {
        $where = "idArquivo = " . $arquivo;
        return $this->delete($where);
    }

    public function inserirUploads($dados)
    {
        $name                   = $dados['nmArquivo'];
        $fileType               = $dados['sgExtensao'];
        $nrTamanho              = $dados['nrTamanho']; // Null
        $stAtivo                = $dados['stAtivo'];
        $biArquivo              = $dados['biArquivo'];
        $idTipoDocumento        = $dados['idTipoDocumento']; // 36 RPA
        $dsDocumento            = $dados['dsDocumento']; // Null
        $idAgente               = $dados['idAgente'];
        $stAtivoDocumentoAgente = $dados['stAtivoDocumentoAgente'];

        $sql = "INSERT INTO ".$this->_schema.".".$this->_name.
               "(nmArquivo, sgExtensao, nrTamanho, dtEnvio, stAtivo, biArquivo, idTipoDocumento, dsDocumento, idAgente, stAtivoDocumentoAgente) " .
               "VALUES ('$name', '$fileType', '$nrTamanho', GETDATE(), '$stAtivo', $biArquivo, $idTipoDocumento, '$dsDocumento', $idAgente, $stAtivoDocumentoAgente)";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->query($sql);
    }
}
