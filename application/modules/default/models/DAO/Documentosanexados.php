<?php
class Documentosanexados extends Zend_Db_Table
{
    protected $_name    = 'SAC.dbo.Projetos';

    public static function buscar($pronac)
    {
        $sql = "select 
       				CONVERT(CHAR(10),Do.dtDocumento,103) as Data, 
       				Td.dsTipoDocumento, Do.NoArquivo, Pr.IdPRONAC, Pr.NomeProjeto
				from 
					SAC.dbo.Projetos Pr
					LEFT JOIN SAC.dbo.tbDocumento Do ON Do.IdPRONAC = Pr.IdPRONAC
					LEFT JOIN BDCORPORATIVO.scCorp.tbArquivo Ar on Ar.idArquivo = Do.idDocumento
					LEFT JOIN SAC.dbo.tbTipoDocumento Td on Do.idTipoDocumento = Do.idTipoDocumento
					where Pr.IdPRONAC= " . $pronac . "";
               
               
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
           
    public function inserirArquivo($name, $fileType)
    {
        $name     = $_FILES['arquivo']['name']; // nome
            $arquivoTemp     = $_FILES['arquivo']['tmp_name']; // nome tempor�rio
            $fileType     = $_FILES['arquivo']['type']; // tipo
            $arquivoTamanho  = $_FILES['arquivo']['size']; // tamanho
            if (!empty($name)) {
                $arquivoExtensao = Upload::getExtensao($name); // extens�o
            }
        if (!empty($arquivoTemp)) {
            $arquivoBinario  = Upload::setBinario($arquivoTemp); // bin�rio
                $arquivoHash     = Upload::setHash($arquivoTemp); // hash
        }
            
        if (empty($arquivoTemp)) { // nome do arquivo
            throw new Exception("Por favor, informe o arquivo!");
        } elseif ($arquivoExtensao == 'exe' || $arquivoExtensao == 'bat' ||
                $fileType == 'application/exe' || $fileType == 'application/x-exe' ||
                $fileType == 'application/dos-exe') { // extens�o do arquivo
            throw new Exception("A extens�o do arquivo � inv�lida!");
        } elseif ($arquivoTamanho > 10485760) { // tamanho do arquivo: 10MB
            throw new Exception("O arquivo n�o pode ser maior do que 10MB!");
        }
        // faz o cadastro no banco de dados
        else {
        }
        $tbArquivo = "INSERT INTO BDCORPORATIVO.scCorp.tbArquivo " .
                                "(nmArquivo, sgExtensao, dsTipo, dtEnvio ,stAtivo)  " .
                            "VALUES ('$name', '$fileType', 'application/pdf', GETDATE(),'A')";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->query();
        return $resultado;
    }
        
    public function inserirArquivoImagem($idGeradoArquivo, $data)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $tbArquivoImagem = "INSERT INTO BDCORPORATIVO.scCorp.tbArquivoImagem " .
                                "(idArquivo,biArquivo) " .
                            "VALUES ($idGeradoArquivo,$data)";
        $resultado = $db->query();
        return $resultado;
    }
        
    public function ultimoIdArquivo()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $idGerado = $db->fetchOne("SELECT MAX(idArquivo) as id from BDCORPORATIVO.scCorp.tbArquivo");
        return $idGerado;
    }
}
