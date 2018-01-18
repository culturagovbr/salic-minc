<?php

/**
 * Class Proposta_Model_TbCustosVinculadosMapper
 *
 * @name Proposta_Model_TbCustosVinculadosMapper
 * @package Modules/Proposta
 * @subpackage Models
 *
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_TbCustosVinculadosMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Proposta_Model_DbTable_TbCustosVinculados');
    }

    public function save($model)
    {
        return parent::save($model);
    }
}
