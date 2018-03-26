<?php
class Execucaofisicaprojeto extends Zend_Db_Table
{
    /**
     * Método para buscar documentos de um PRONAC
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function buscardocumentos($idPronac)
    {
        $sql = "SELECT doc.idComprovante AS id, 
				doc.idTipoDocumento, 
				tipodoc.dsTipoDocumento, 
				doc.nmComprovante AS Nome,
				doc.idArquivo, 
				arq.nmArquivo, 
				arq.dsTipo, 
				arq.nrTamanho,
				arqimg.biArquivo,
				CONVERT(CHAR(10), doc.dtEnvioComprovante,103) + ' ' + CONVERT(CHAR(8), doc.dtEnvioComprovante,108) 
					AS dtEnvioComprovante, 
				doc.stParecerComprovante, 
				doc.idComprovanteAnterior 
			FROM BDCORPORATIVO.scSAC.tbComprovanteExecucao doc, 
				BDCORPORATIVO.scSAC.tbTipoDocumento tipodoc, 
				BDCORPORATIVO.scCorp.tbArquivo arq, 
				BDCORPORATIVO.scCorp.tbArquivoImagem arqimg,
				SAC.dbo.Projetos proj
			WHERE doc.idTipoDocumento = tipodoc.idTipoDocumento 
				AND doc.idArquivo = arq.idArquivo 
				AND arq.idArquivo = arqimg.idArquivo 
				AND doc.idPRONAC  = proj.IdPRONAC
				AND proj.AnoProjeto+proj.Sequencial  = '" . $idPronac . "' 
			ORDER BY doc.dtEnvioComprovante DESC;";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Comprovantes: " . $e->getMessage();
        }
    }

    /**
     * Método para cadastrar documentos do PRONAC
     * @access public
     * @param void
     * @return object
     */
    public static function cadastrar($dados)
    {
    }

    /**
     * Método para alterar documentos do PRONAC
     * @access public
     * @param void
     * @return object
     */
    public static function alterar()
    {
    } 

    /**
     * Método para excluir documentos do PRONAC
     * @access public
     * @param void
     * @return object
     */
    public static function excluir()
    {
    } // fecha método excluir()
}
