<?php
class ComprovarexecucaofinanceiraDao extends Zend_Db_Table
{
    public function selectTable($tabela, $atributos, $where = array(), $order = array())
    {
        $atributosAux = implode(",", $atributos);

        $whereAux = '';
        foreach ($where as $key=>$atributo) {
            $whereAux .=  $key.$atributo;
        }
        $orderAux = '';
        if (count($order)>0) {
            $orderAux= 'order by '.implode(',', $order);
        }
        
        $sql = "Select {$atributosAux} From {$tabela} Where {$whereAux} {$orderAux}";
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $dados =  $db->fetchAll($sql);

        return $dados;
    }

    public function insertTable($tabela, $cadastro)
    {
        $coluna = '';
        $value  = '';
        foreach ($cadastro as $key=>$atributo) {
            if ($coluna!='') {
                $coluna = ',';
            }
            $coluna .=  $key;
            if ($value!='') {
                $value = ',';
            }
            $value .=  $atributo;
        }
        

        $sql = "insert into $tabela ($coluna)
                values ($value)";
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
        //return true;
    }
    public function updateTable($tabela, $update, $where)
    {
        $whereAux = '';
        $value    = '';
        foreach ($cadastro as $key=>$atributo) {
            if ($value!='') {
                $value = ',';
            }
            $value .=  $key.' = '.$atributo;
        }
        foreach ($where as $key=>$atributo) {
            $whereAux .=  $key.' = '.$atributo;
        }

        $sql = "update $tabela set $value where $whereAux";
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
        //return true;
    }
    public function deleteTable($tabela, $where)
    {
        /*$whereAux = '';
        foreach ($where as $key=>$atributo){
            $whereAux .=  $key.' = '.$atributo;
        }

        $sql = "delete from $tabela  where $whereAux";
        $db = Zend_Registry::get ( 'db' );
        $db->setFetchMode ( Zend_DB::FETCH_OBJ );
        return $db->fetchAll ($sql);*/
        return true;
    }

    public function retornaUltimoIdentity()
    {
        /*$sql = "select  @@IDENTITY";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $dados =  $db->fetchAll($sql);

        return $dados;*/
        return 1;
    }
    public function anexarArquivo(Arquivo $dados)
    {
        try {
            // ver se vai ser assim no banco

            // cadastra dados do arquivo

            $sql = "";
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
            $db->query($sql);

                        

            // insere o bin�rio do arquivo select insert
            $sql = "";
            $db->query($sql);

            // insere informa��es do documento select insert
            $sql = "";
            $db->query($sql);
            return true;
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao inserir o arquivo: " . $e->getMessage();
            return false;
        }
    }

    public function inserirNomeFornecedor($array = array())
    {
        $sql = "insert into tbFornecedorPagamento (idCotacao,
                                                   idDispensa,
                                                   idContrato,
                                                   idLicitacao,
                                                   idComprovantePagamento)
                                           values ({$array['idCotacao']},
                                                   {$array['idDispensa']},
                                                   {$array['idContrato']},
                                                   {$array['idLicitacao']},
                                                   {$array['idComprovantePagamento']})";
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
