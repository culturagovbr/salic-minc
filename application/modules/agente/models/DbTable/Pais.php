<?php

/**
 * Class Agente_Model_DbTable_Pais
 *
 * @name Agente_Model_DbTable_Pais
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 19/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_Pais extends MinC_Db_Table_Abstract
{
    protected $_banco = "agentes";
    protected $_name = 'pais';
    protected $_schema = 'agentes';
}
