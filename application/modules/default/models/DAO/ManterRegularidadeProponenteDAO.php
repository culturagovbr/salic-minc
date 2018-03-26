<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ManterRegularidadeProponenteDAO
 *
 * @author 01373930160
 */
class ManterRegularidadeProponenteDAO extends Zend_Db_Table
{
    public static function buscaPronac($idPronac)
    {
        $sql = "SELECT idPRONAC FROM Sac.dbo.Projetos WHERE AnoProjeto+Sequencial = '{$idPronac}'";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo buscaAgentes()


    public static function buscaAgentes($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $sql = "SELECT TipoPessoa FROM Agentes.dbo.Agentes WHERE CNPJCPF = '$cnpjcpf'";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo buscaAgentes()


    public static function buscaInteressados($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $sql = "SELECT CgcCpf FROM SAC.dbo.Interessado WHERE CgcCpf = '$cnpjcpf'";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo buscaAgentes()

    public static function buscaCertidoesQF($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $sql = "SELECT     Sequencial, Logon, CgcCpf, CodigoCertidao, DtEmissao, DtValidade, AnoProjeto, idCertidoesnegativas,
                      AnoProjeto + Sequencial AS Pronac, CONVERT(VARCHAR(10), DtEmissao, 103)
                      AS DtEmissaoFormatada, CONVERT(VARCHAR(10), DtValidade, 103) AS DtValidadeFormatada,
                      cdProtocoloNegativa 
						FROM         SAC.dbo.CertidoesNegativas
						WHERE     (CgcCpf = '$cnpjcpf') AND (CodigoCertidao = 49)
						ORDER BY idCertidoesnegativas DESC";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaCertidoesQE($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $sql = "SELECT     Sequencial, Logon, CgcCpf, CodigoCertidao, DtEmissao, DtValidade, AnoProjeto, idCertidoesnegativas,
                      AnoProjeto + Sequencial AS Pronac, CONVERT(VARCHAR(10), DtEmissao, 103)
                      AS DtEmissaoFormatada, CONVERT(VARCHAR(10), DtValidade, 103) AS DtValidadeFormatada,
                      cdProtocoloNegativa
						FROM         SAC.dbo.CertidoesNegativas
						WHERE     (CgcCpf = '$cnpjcpf') AND (CodigoCertidao = 70)
						ORDER BY idCertidoesnegativas DESC";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaCertidoesFGTS($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $sql = "SELECT     Sequencial, Logon, CgcCpf, CodigoCertidao, DtEmissao, DtValidade, AnoProjeto, idCertidoesnegativas,
                      AnoProjeto + Sequencial AS Pronac, CONVERT(VARCHAR(10), DtEmissao, 103)
                      AS DtEmissaoFormatada, CONVERT(VARCHAR(10), DtValidade, 103) AS DtValidadeFormatada,
                      cdProtocoloNegativa
						FROM         SAC.dbo.CertidoesNegativas
						WHERE     (CgcCpf = '$cnpjcpf') AND (CodigoCertidao = 51)
						ORDER BY idCertidoesnegativas DESC";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
    public static function buscaCertidoesCADIN($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $sql = "SELECT     Sequencial, Logon, CgcCpf, CodigoCertidao, DtEmissao, DtValidade, AnoProjeto, idCertidoesnegativas,
                      AnoProjeto + Sequencial AS Pronac, CONVERT(VARCHAR(10), DtEmissao, 103)
                      AS DtEmissaoFormatada, CONVERT(VARCHAR(10), DtValidade, 103) AS DtValidadeFormatada,
                      cdProtocoloNegativa, cdSituacaoCertidao
						FROM         SAC.dbo.CertidoesNegativas
						WHERE     (CgcCpf = '$cnpjcpf') AND (CodigoCertidao = 244)
						ORDER BY idCertidoesnegativas DESC ";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaCertidoesINSS($cnpjcpf = null, $nome = null, $idAgente = null)
    {
        $sql = "SELECT     Sequencial, Logon, CgcCpf, CodigoCertidao, DtEmissao, DtValidade, AnoProjeto, idCertidoesnegativas,
                      AnoProjeto + Sequencial AS Pronac, CONVERT(VARCHAR(10), DtEmissao, 103)
                      AS DtEmissaoFormatada, CONVERT(VARCHAR(10), DtValidade, 103) AS DtValidadeFormatada,
                      cdProtocoloNegativa
						FROM         SAC.dbo.CertidoesNegativas
						WHERE     (CgcCpf = '$cnpjcpf') AND (CodigoCertidao = 52)
						ORDER BY idCertidoesnegativas DESC";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function buscaCertidao($post)
    {
        $cpfCnpj = $_POST['cpfCnpj'];
        $codigoCertidao = $_POST['codigoCertidao'];

        $cpfCnpj = str_replace(".", "", $_POST["cpfCnpj"]);
        $cpfCnpj = str_replace(",", "", $cpfCnpj);
        $cpfCnpj = str_replace("/", "", $cpfCnpj);
        $cpfCnpj = str_replace("-", "", $cpfCnpj);
            
        $sql = "select  CgcCpf,CodigoCertidao  from SAC.dbo.CertidoesNegativas where CgcCpf = '$cpfCnpj' and CodigoCertidao = $codigoCertidao";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function insereCertidao($post)
    {

               //print_r($_POST);die;

        $cpfCnpj = str_replace("/", "", str_replace("-", "", str_replace(".", "", $_POST['cpfCnpj'])));
        $codigoCertidao = $_POST['codigoCertidao'];
        if (isset($_POST['situacao'])) {
            $codigoSituacao = $_POST['situacao'];
        }
                

        $dataEmissao = $_POST['dataEmissao'];
        $hora = $_POST['hora'];
        $dataEmissaoHora = Data::dataAmericana($dataEmissao) . " " . $hora;
        $dataEmissaoFormatada = str_replace("/", "-", $dataEmissaoHora);

        if (isset($_POST['validade'])) {
            $validade = $_POST['validade'];
            $dataValidadeHora = Data::dataAmericana($validade) . " " .  date('h:i:s');
        } else {
            $validade = date("Y-m-d");
            $dataValidadeHora = $validade . " " .  date('h:i:s');
        }
                
        $dataValidadeFormatada = str_replace("//", "-", $dataValidadeHora);
        $anoProjeto = substr($_POST['projeto'], 0, 2);
        $sequencial = substr($_POST['projeto'], 2);
        $protocolo = trim($_POST['protocolo']);




        if (isset($_POST['codigoSituacao'])) {
            echo $sql = "INSERT INTO SAC.dbo.CertidoesNegativas
                          (CgcCpf, CodigoCertidao, DtEmissao, DtValidade, AnoProjeto, Sequencial, cdProtocoloNegativa, cdSituacaoCertidao, Logon)
                    VALUES     ('$cpfCnpj',$codigoCertidao,'$dataEmissaoFormatada','$dataValidadeFormatada',$anoProjeto,$sequencial,$protocolo,$codigoSituacao, ' )";
        } else {
            echo $sql = "INSERT INTO SAC.dbo.CertidoesNegativas
                          (CgcCpf, CodigoCertidao, DtEmissao, DtValidade, AnoProjeto, Sequencial, cdProtocoloNegativa, Logon)
                    VALUES     ('$cpfCnpj',$codigoCertidao,'$dataEmissaoFormatada','$dataValidadeFormatada',$anoProjeto,$sequencial,$protocolo, 1)";
        }


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function atualizaCertidao($post, $cpfCgc, $codigoCertidao)
    {
        //print_r($post);die;
                
                
        if (isset($_POST['situacao'])) {
            $codigoSituacao = $_POST['situacao'];
        }
        $dataEmissao = $_POST['dataEmissao'];
        $hora = $_POST['hora'];
                
                
                
        $dataEmissaoHora = Data::dataAmericana($dataEmissao) . " " . $hora;
        $dataEmissaoFormatada = str_replace("/", "-", $dataEmissaoHora);

        if (!isset($_POST['situacao'])) {
            $validade = $_POST['validade'];
            $dataValidadeHora = Data::dataAmericana($validade) . " " .  date('h:i:s');
            $dataValidadeFormatada = str_replace("/", "-", $dataValidadeHora);
        }
        $pronac = $_POST['projeto'];
        $protocolo = trim($_POST['protocolo']);


        if (isset($_POST['situacao'])) {
            echo $sql = "UPDATE    SAC.dbo.CertidoesNegativas
                                    SET    DtEmissao = '$dataEmissaoFormatada', 
                                    cdProtocoloNegativa = $protocolo, 
                                    cdSituacaoCertidao = $codigoSituacao , 
                                    AnoProjeto = '".  substr($pronac, 0, 2)."',
                                    Sequencial = '".  substr($pronac, 2, strlen($pronac)-2)."'
                        where CgcCpf = '$cpfCgc' and CodigoCertidao = $codigoCertidao";
        } else {
            echo $sql = "UPDATE    SAC.dbo.CertidoesNegativas
                        SET    DtEmissao = '$dataEmissaoFormatada', 
                        DtValidade = '$dataValidadeFormatada',
                        cdProtocoloNegativa = $protocolo  ,
                        AnoProjeto = '".  substr($pronac, 0, 2)."',
                        Sequencial = '".  substr($pronac, 2, strlen($pronac)-2)."'
                    where CgcCpf = '$cpfCgc' and CodigoCertidao = $codigoCertidao";
        }

        



        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
}
