<?php
/**
 * DAO vwMemoriaDeCalculo
 * @since 01/03/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class vwMemoriaDeCalculo extends MinC_Db_Table_Abstract
{

    /* dados da tabela */
    protected $_banco  = 'SAC';
    protected $_schema = 'SAC';
    protected $_name   = 'vwMemoriaDeCalculo';

    public function busca($idPronac)
    {
        $select =  new Zend_Db_Expr("
                SELECT idPronac,PRONAC,ValorProposta,OutrasFontes,ValorSolicitado,
                       Elaboracao,ValorParecer,ValorSugerido
                FROM SAC.dbo.vwMemoriaDeCalculo
                WHERE idPronac = $idPronac ");
        try {
            $db= Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_OBJ);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = $e->getMessage();
        }
        return $db->fetchAll($select);
    }
}
