<?php

/**
 * Class Agente_Model_DDDMapper
 *
 * @name Agente_Model_DDDMapper
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
class Agente_Model_DDDMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Agente_Model_DbTable_DDD');
    }

    /**
     *
     * @name fetchPairs
     * @param string $key
     * @param string $value
     * @param array $where
     * @param string $order
     * @return array
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  02/09/2016
     *
     * @todo retirar futuramente, metodo feito para utilizar no xml.
     */
    public function fetchPairs($key, $value, array $where = array(), $order = '')
    {
        $result = parent::fetchPairs($key, $value, $where, $order);
        $resultNew = array();
        foreach ($result as $key => $value) {
            $stdClass = new stdClass();
            $stdClass->id = $key;
            $stdClass->descricao = $value;
            $resultNew[] = $stdClass;
        }

        return $resultNew;
    }
}
