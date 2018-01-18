<?php
class LembretesDAO extends Zend_Db_Table
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
    
    public static function buscaLembrete($pronac)
    {
        $sql = "select 
   		Pr.AnoProjeto+Pr.Sequencial as nrpronac,
        lm.Lembrete as lembrete, lm.Contador,
		CONVERT(CHAR(10),lm.DtLembrete,103) as dtlembrete,
		Pr.IdPRONAC, 
		Pr.NomeProjeto 
from SAC.dbo.Lembrete lm
inner join SAC.dbo.Projetos Pr on Pr.AnoProjeto = lm.AnoProjeto and lm.Sequencial = Pr.Sequencial
 where Pr.IdPRONAC = " . $pronac . " ";
        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    
    
    
    
    
    
    
    public static function pesquisaLembrete($pronac, $dtlembrete = null)
    {
        $sql = "select Pr.AnoProjeto+Pr.Sequencial as nrpronac, lm.Lembrete as lembrete, lm.Contador,
		CONVERT(CHAR(10),lm.DtLembrete,103) as dtlembrete,
		Pr.IdPRONAC, 
		Pr.NomeProjeto,
		Pr.AnoProjeto,
		Pr.Sequencial 
from SAC.dbo.Projetos Pr 
INNER join SAC.dbo.Lembrete lm on Pr.AnoProjeto = lm.AnoProjeto and lm.Sequencial = Pr.Sequencial
 where Pr.IdPRONAC = " . $pronac . " and CONVERT(CHAR(10),lm.DtLembrete,103) = '" . $dtlembrete . "'";
        

        
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        
        return $resultado;
    }

    public static function buscaProjeto($pronac, $data=null)
    {
        $sql = "select AnoProjeto+Sequencial as nrpronac, 
				IdPRONAC,
				NomeProjeto,
				AnoProjeto,
				Sequencial 
				from     SAC.dbo.Projetos 
 				where 	 IdPRONAC = ".$pronac;
    
            
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    
    public function alterarlembrete($contador, $lembrete)
    {
        $sql2 = "update SAC.dbo.Lembrete
				SET 
					Lembrete       		= '" . $lembrete . "' 
				where Contador = '" . $contador . "'";
        //echo $sql;die();
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado2 = $db->query($sql2);
    }
    
    
    public function exluirlembrete($contador)
    {
        $sql3 = "DELETE 
 	               				FROM SAC.dbo.Lembrete 
 	            				WHERE Contador = '" . $contador . "'";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $resultado3 = $db->fetchAll($sql3);
    }
}

// fecha class
