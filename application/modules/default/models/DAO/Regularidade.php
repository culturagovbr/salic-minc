<?php
class Regularidade extends Zend_Db_Table
{
    public static function buscarSalic($CgcCpf)
    {
        $sql = "select Habilitado from SAC.dbo.Inabilitado where CgcCpf = '$CgcCpf' AND Habilitado='N' ";
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarCQTE($CgcCpf)
    {
        $sql = "select DtValidade from SAC.dbo.CertidoesNegativas where CgcCpf = '$CgcCpf' and CodigoCertidao = 70 ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);



        return $db->fetchAll($sql);
    }

    public static function buscarCQTF($CgcCpf)
    {
        $sql = "select DtValidade from SAC.dbo.CertidoesNegativas where CgcCpf = '$CgcCpf' and CodigoCertidao = 49 ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarFGTS($CgcCpf)
    {
        $sql = "select DtValidade from SAC.dbo.CertidoesNegativas where CgcCpf = '$CgcCpf 'and CodigoCertidao = 51 ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarINSS($CgcCpf)
    {
        $sql = "select DtValidade,cdSituacaoCertidao from SAC.dbo.CertidoesNegativas where CgcCpf = '$CgcCpf' and CodigoCertidao = 52 ";
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscarCADIN($CgcCpf)
    {
        $sql = "select DtEmissao,  cdSituacaoCertidao from SAC.dbo.CertidoesNegativas where CgcCpf = '$CgcCpf' and CodigoCertidao = 244";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
}
