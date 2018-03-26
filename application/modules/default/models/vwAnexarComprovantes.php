<?php
/**
 * DAO vwAnexarComprovantes
 * @since 17/12/2012
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class vwAnexarComprovantes extends MinC_Db_Table_Abstract
{

    /* dados da tabela */
    protected $_banco  = 'SAC';
    protected $_schema = 'SAC';
    protected $_name   = 'vwAnexarComprovantes';

    public function excluirArquivo($idArquivo)
    {
        $where = "idArquivo = " . $idArquivo;
        return $this->delete($where);
    }
}
