<?php
/**
 * Modelo Telefone
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.model.DAO
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class TitulacaoConselheiroDAO extends Zend_Db_Table
{
    protected $_name = 'AGENTES.dbo.tbTitulacaoConselheiro'; // nome da tabela



    #--verifica se tem algum componente na area e segmento selecionado--
    public static function buscaAreaSegmento($area, $segmento = null)
    {
        $sql = "SELECT A.idAgente
					,N.Descricao Nome
					,A.cdArea
					,AC.Descricao Area
					,A.cdSegmento
					,SC.Descricao Segmento
					,A.stTitular

				FROM AGENTES.dbo.tbTitulacaoConselheiro A 
					INNER JOIN AGENTES.dbo.Nomes N ON A.idAgente = N.idAgente 
					INNER JOIN SAC.dbo.Area AC ON A.cdArea = AC.Codigo 
					LEFT JOIN SAC.dbo.Segmento SC ON A.cdSegmento = SC.Codigo 

				WHERE A.cdArea = " . $area . " AND stConselheiro = 'A' ";

        if (!empty($segmento)) {
            $sql.= " AND A.cdSegmento = " . $segmento;
        }

        $sql.= " ORDER BY A.stTitular DESC, N.Descricao";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->fetchAll($sql);
    }



    #--Buscando quanto titulares tem na area x
    public static function buscaTitularArea($area)
    {
        $sql = "Select COUNT(*) as QTD FROM AGENTES.dbo.tbTitulacaoConselheiro where cdArea = ".$area." AND stTitular = 1 AND stConselheiro = 'A'";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->fetchAll($sql);
    }



    #--Buscando quantos suplentes tem na area x--
    public static function buscaSuplentesArea($area)
    {
        $sql = "Select COUNT(*) as QTD FROM AGENTES.dbo.tbTitulacaoConselheiro where cdArea = ".$area." AND stTitular = 0 AND stConselheiro = 'A'";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->fetchAll($sql);
    }



    public static function buscarComponente($idAgente)
    {
        $sql = "Select * From AGENTES.dbo.tbTitulacaoConselheiro where idAgente = ".$idAgente;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        return $db->fetchAll($sql);
    }



    public static function atualizaComponente($idAgente, $dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $where = "idAgente =".$idAgente;
        $i = $db->update('AGENTES.dbo.tbTitulacaoConselheiro', $dados, $where);
    }



    public static function gravarComponente($dados)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $i = $db->insert('AGENTES.dbo.tbTitulacaoConselheiro', $dados);
    }
} // fecha class
