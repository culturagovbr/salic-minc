<?php
/**
 * Model tbTmpDepositoIdentificado
 * @author jailton.landim - Politec
 * @since 13/09/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbTmpDepositoIdentificado extends MinC_Db_Table_Abstract
{
    
    /* dados da tabela */
    protected $_schema  = "sac";
    protected $_name    = "tbTmpDepositoIdentificado";
        
        
    public function deletar($where)
    {
        $delete = $this->delete($where);
    }
}
