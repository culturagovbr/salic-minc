<?php


class ArquivoDAO extends Zend_Db_Table
{
    protected $_schema = "";
    protected $_name = "BDCORPORATIVO.scCorp.tbArquivo";
    protected $_primary = "idArquivo";

    /**
     * @static
     * @param array $dados
     * @return bool
     */
    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $cadastrar = $db->insert("BDCORPORATIVO.scCorp.tbArquivo", $dados);

        return $cadastrar ? true : false;
    }

    /**
     * @param array $dados
     * @param integer $id
     * @return bool
     */
    public static function alterar($dados, $id)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $where = "idArquivo = $id";
        $alterar = $db->update("BDCORPORATIVO.scCorp.tbArquivo", $dados, $where);

        return $alterar ? true : false;
    }

    public static function deletar($idArquivo)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->exec("delete from BDCORPORATIVO.scCorp.tbArquivoImagem WHERE idArquivo = {$idArquivo}");
        $db->exec("delete from BDCORPORATIVO.scCorp.tbArquivo WHERE idArquivo = {$idArquivo}");
    }

    public static function verificarHash($dsHash)
    {
        $sql = "SELECT * FROM BDCORPORATIVO.scCorp.tbArquivo WHERE dsHash = '$dsHash'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function buscarIdArquivo()
    {
        $sql = "SELECT MAX(idArquivo) AS id FROM BDCORPORATIVO.scCorp.tbArquivo";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}

/**
 * @todo migrar para o arquivo certo
 */
class ArquivoImagemDAO extends Zend_Db_Table
{
    protected $_schema = "";
    protected $_name = "BDCORPORATIVO.scCorp.tbArquivoImagem";
    protected $_primary = "idArquivo";

    public static function cadastrar($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $dados['biArquivo'] = new Zend_Db_Expr("CONVERT(varbinary(MAX),{$dados['biArquivo']})");
        return $db->insert('BDCORPORATIVO.scCorp.tbArquivoImagem', $dados);
    }

    public static function alterar($dados, $id)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $sql = "UPDATE BDCORPORATIVO.scCorp.tbArquivoImagem SET 
            biArquivo = {$dados['biArquivo']} WHERE idArquivo = $id";

        return $db->fetchAll($sql);
    }
}
