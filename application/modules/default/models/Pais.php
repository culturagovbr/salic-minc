<?php
/**
 * DAO Pais
 * @author emanuel.sampaio <emanuelonline@gmail.com>
 * @since 18/04/2012
 * @package application
 * @subpackage application.model
 * @link http://salic.cultura.gov.br
 */

class Pais extends MinC_Db_Table_Abstract
{
    protected $_banco   = "AGENTES";
    protected $_schema  = "AGENTES";
    protected $_name    = "Pais";
}
