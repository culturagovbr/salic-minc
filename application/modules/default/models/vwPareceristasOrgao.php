<?php
/**
 * DAO vwPareceristasOrgao
 * @since 17/09/2013
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class vwPareceristasOrgao extends MinC_Db_Table_Abstract
{
    protected $_banco  = 'SAC';
    protected $_schema = 'SAC';
    protected $_name   = 'vwPareceristasOrgao';
    protected $_primary = 'idParecerista';
}
