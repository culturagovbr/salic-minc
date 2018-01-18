<?php

/**
 * Class Agente_Model_AgentesMapper
 *
 * @name Agente_Model_AgentesMapper
 * @package Modules/Agente
 * @subpackage Models
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 01/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_AgentesMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        $this->setDbTable('Agente_Model_DbTable_Agentes');
    }

    public function fetchAll()
    {
        $resultSet = $this->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Agente_Model_Agentes($row->toArray());
            $entries[] = $entry->toArray();
        }
        return $entries;
    }

    public function isUniqueCpfCnpj($value)
    {
        return ($this->findBy(array("cnpjcpf" => $value))) ? true : false;
    }

    public function save($model)
    {
        if (self::isUniqueCpfCnpj($model->getCnpjcpf())) {
            throw new Exception('CNPJ ou CPF j&aacute; cadastrado.');
        } else {
            return parent::save($model);
        }
    }
}
