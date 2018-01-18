<?php
class Lembretes extends Zend_Db_Table
{
    protected $_name    = 'SAC.dbo.Projetos';

    public function buscar($sql)
    {
        //echo $sql . "<br>";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }
    
    public function inserirLembrete($anoprojeto, $sequencial, $lembrete)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = "INSERT INTO SAC.dbo.Lembrete (Logon, AnoProjeto, Sequencial, DtLembrete, Lembrete)
				VALUES (75, '$anoprojeto', '$sequencial', GETDATE(), '$lembrete')";
           
        $resultado = $db->query($sql);
        return $resultado;
    }
    
    public function buscaLembrete($pronac)
    {
        $sql = "select 
   
        lm.Lembrete as lembrete, lm.Contador as contador,
		CONVERT(CHAR(10),lm.DtLembrete,103) as dtlembrete,
		Pr.IdPRONAC, 
		Pr.NomeProjeto 
from SAC.dbo.Lembrete lm
left join SAC.dbo.Projetos Pr on Pr.AnoProjeto = lm.AnoProjeto
 where Pr.IdPRONAC = " . $pronac . "  and lm.Sequencial = Pr.Sequencial";
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public function buscaProjeto($pronac)
    {
        $sql1 = "select  IdPRONAC, NomeProjeto, AnoProjeto, Sequencial 
		 		 from     SAC.dbo.Projetos 
 				 where 	 IdPRONAC = ". $pronac;
    
            
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql1);
        return $resultado1;
    }

    
    public function alterarLembrete($lembrete)
    {
        $sql2 = "update SAC.dbo.Lembrete
				SET 
					Lembrete       		= '".$lembrete."', 
				where Contador     	= '".$contador."'";
        //echo $sql;die();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->query($sql2);
    }
    
    
    public function exluirLembrete($dtlembrete, $lembrete)
    {
    }
}

// fecha class
