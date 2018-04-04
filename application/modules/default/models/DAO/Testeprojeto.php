<?php

class Testeprojeto extends Zend_Db_Table
{

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
				BDCORPORATIVO.scCorp.tbArquivoImagem arqimg 
			WHERE doc.idTipoDocumento = tipodoc.idTipoDocumento 
				AND doc.idArquivo = arq.idArquivo 
				AND arq.idArquivo = arqimg.idArquivo 
				AND doc.idPRONAC = " . $idPronac . " 
			ORDER BY doc.dtEnvioComprovante DESC;";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            return $db->fetchAll($sql);
        } catch (Zend_Exception_Db $e) {
            xd("Erro ao buscar Comprovantes: " . $e->getMessage());
        }
    }

    public static function cadastrar($dados)
    {

    }
}
