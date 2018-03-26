<?php

/**
 * Class Proposta_Model_TbDocumentosPreProjetoMapper
 *
 * @name Proposta_Model_TbDocumentosPreProjetoMapper
 * @package Modules/Agente
 * @subpackage Models
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 02/10/2016
 *
 * @link http://salic.cultura.gov.br
 */
class Proposta_Model_TbDocumentosPreProjetoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Proposta_Model_DbTable_TbDocumentosPreProjeto');
    }

    public function save(Proposta_Model_TbDocumentosPreProjeto $model)
    {
        return parent::save($model);
    }
}
