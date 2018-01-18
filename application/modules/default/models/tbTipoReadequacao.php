<?php
/**
 * DAO tbTipoReadequacao
 * @author jeffersonassilva@gmail.com - XTI
 * @since 28/02/2014
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbTipoReadequacao extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbTipoReadequacao";

    public function buscarTiposReadequacoesPermitidos($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("idTipoReadequacao, dsReadequacao"),
            )
        );

        $select->where('stReadequacao = ?', 0);
        $select->orWhere("stReadequacao = 1 and idTipoReadequacao not in (
            select idTipoReadequacao from SAC.dbo.tbReadequacao where idPronac = $idPronac AND siEncaminhamento != 12
        )");

        $select->order('2');

        
        return $this->fetchAll($select);
    }
}
