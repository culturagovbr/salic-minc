<?php

/*
 * Created on 27/04/2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * @deprecated Utilize 'Projetos' no Module Default em Model
 */

class Projetos extends Zend_Db_Table
{
    protected $_name = 'SAC.dbo.Projetos';

    public function buscarCpf($sql)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public function abrirArquivo()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $sql = "select top 1 * 
        from BDCORPORATIVO.scCorp.tbArquivo a inner join
        BDCORPORATIVO.scCorp.tbArquivoImagem b
        on a.idArquivo = b.idArquivo where a.idArquivo = 406";
        $resultado = $db->fetchAssoc($sql);
        return $resultado;
    }

    public function buscarArquivo($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $busca = "select * FROM scCorp.tbArquivo a inner join
                                  scCorp.tbArquivoImagem b on
                                  a.idArquivo = b.idArquivo
                          where
                                  a.idArquivo =" . $id;
        $resultado = $db->fetchAll($busca);
        return $resultado;
    }

    public function buscaTelaProjeto($cpf)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $sql = "select 
                        a.idPronac,
                        a.NomeProjeto, 
                        a.CgcCpf, 
                        b.Descricao    
                    from SAC.dbo.Projetos a inner join 
                        SAC.dbo.Situacao b on
                        a.Situacao = b.Codigo
                        where
                         b.Codigo in ( 
                                    'E13','E10','E11','E12','E15','E16'
                        ) and CgcCpf = '" . $cpf . "'";
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public function buscaTelaProponente($cpf)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $sql = "select * from SAC.dbo.Interessado where CgcCpf = '" . $cpf . "'";
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public function telaBuscaDetalheProjeto($cpf, $pronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $sql = "select 
                            a.idPronac,
                            a.NomeProjeto, 
                            a.CgcCpf, 
                            b.Nome
                    from scSAC.Projetos a inner join
                            scSAC.Interessado b on
                            a.CgcCpf = b.CgcCpf
                    where
                            b.CgcCpf = '" . $cpf . "' and a.idPronac = '" . $pronac . "'";
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public function buscarGeral($sql)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function respostaTela($cpf, $pronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $sql = "select top 1
                            a.idPRONAC,
                            CONVERT(CHAR(10), a.dtSolicitacao,103) + ' ' + CONVERT(CHAR(8), a.dtSolicitacao,108) AS maior 
                            from scSAC.tbPedidoAlteracaoProjeto a inner join
                            SAC.dbo.Projetos b on a.idPRONAC = b.idPRONAC 
                            where b.CgcCpf = '" . $cpf . "' and a.idPRONAC = '" . $pronac . "' order by a.dtSolicitacao desc";
        $resultado = $db->fetchAll($sql);
        if ($resultado) {
            echo $resultado[0]->maior;
        } else {
            echo "---";
        }
        return $resultado;
    }

    public function inserirDados($dados)
    {
        //echo $dados;die();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->query($dados);
        return $resultado;
    }

    public function inserirArquivo($name, $fileType)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $tbArquivo = "INSERT INTO scCorp.tbArquivo " .
                "(nmArquivo, sgExtensao, dsTipo, dtEnvio ,stAtivo) values  ('$name', '$fileType', 'application/pdf', GETDATE(),'A')";
        $resultado = $db->query($tbArquivo);
        return $resultado;
    }

    public function inserirArquivoImagem($idGeradoArquivo, $data)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $tbArquivoImagem = "INSERT INTO scCorp.tbArquivoImagem " .
                "(idArquivo,biArquivo) values  ($idGeradoArquivo,$data)";
        $resultado = $db->query($tbArquivoImagem);
        return $resultado;
    }

    public function ultimoIdPedidoAlteracaoProjeto()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $idGerado = $db->fetchOne("SELECT MAX(idPedidoAlteracao) as maior from scSAC.tbPedidoAlteracaoProjeto");
        return $idGerado;
    }

    public function ultimoIdArquivo()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $idGerado = $db->fetchOne("SELECT MAX(idArquivo) as id from scCorp.tbArquivo");
        return $idGerado;
    }
}
