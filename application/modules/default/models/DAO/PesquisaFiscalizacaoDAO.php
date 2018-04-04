<?php
/**
 * Modelo Cidade
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class PesquisaFiscalizacaoDAO extends Zend_Db_Table
{
    protected $_name = 'AGENTES.dbo.UF'; // nome da tabela



    /**
     * M�todo para buscar as cidades de um determinado estado
     * @access public
     * @param integer $idUF
     * @return object $db->fetchAll($sql)
     */
    public static function buscarregiao()
    {
        $sql = "SELECT Distinct Regiao
				FROM AGENTES.dbo.UF";

    
    
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarRecursoReenquadramento()
    
    
    
    
    public static function artescenicas()
    {
        $sql ="SELECT   Projetos.AnoProjeto+Sequencial as pronac,Projetos.IdPRONAC,Projetos.NomeProjeto,Projetos.UfProjeto,Area.Descricao,Segmento.Descricao as Segmento
FROM  SAC.dbo.Projetos  as Projetos INNER JOIN
SAC.dbo.Segmento as Segmento ON Projetos.Segmento = Segmento.Codigo  INNER JOIN
SAC.dbo.Area  as Area ON Projetos.Area = Area.Codigo
where Projetos.Area = 4 and Projetos.UfProjeto = 'RJ' and Projetos.Area = 4 and Projetos.Segmento = 47";
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
    
    
    
    
    public static function buscaprojeto()
    {
        $sql ="SELECT   Projetos.AnoProjeto+Sequencial as pronac,Projetos.IdPRONAC,Projetos.NomeProjeto,Projetos.UfProjeto,Area.Descricao,Segmento.Descricao as Segmento
FROM  SAC.dbo.Projetos  as Projetos INNER JOIN
SAC.dbo.Segmento as Segmento ON Projetos.Segmento = Segmento.Codigo  INNER JOIN
SAC.dbo.Area  as Area ON Projetos.Area = Area.Codigo
where Projetos.Area = 4 and Projetos.UfProjeto = 'RJ' and Projetos.Area = 4 and Projetos.Segmento = 47";
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
