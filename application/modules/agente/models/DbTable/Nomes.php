<?php

/**
 * Class Agente_Model_DbTable_Nomes
 *
 * @name Agente_Model_DbTable_Nomes
 * @package Modules/Agente
 * @subpackage Models/DbTable
 * @version $Id$
 *
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 05/09/2016
 *
 * @copyright © 2012 - Ministerio da Cultura - Todos os direitos reservados.
 * @link http://salic.cultura.gov.br
 */
class Agente_Model_DbTable_Nomes extends MinC_Db_Table_Abstract
{
    /**
     * _schema
     *
     * @var string
     * @access protected
     */
    protected $_schema = 'agentes';

    /**
     * _name
     *
     * @var bool
     * @access protected
     */
    protected $_name = 'nomes';

    /**
     * _primary
     *
     * @var bool
     * @access protected
     */
    protected $_primary = 'idNome';

    /**
     * Se a tabela possui sequence
     * @var bool
     */
    protected $_sequence = true;
}
