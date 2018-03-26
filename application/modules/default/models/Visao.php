<?php

/**
 * DAO Visao
 * @author emanuel.sampaio - Politec
 * @since 23/08/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class Visao extends MinC_Db_Table_Abstract
{
    /* dados da tabela */

    protected $_banco = 'Agentes';
    protected $_name = 'visao';
    protected $_schema = 'agentes';
    protected $_primary = 'idVisao';
}
