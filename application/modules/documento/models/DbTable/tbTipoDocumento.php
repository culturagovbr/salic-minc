<?php

/**
 * Class Documento_Model_DbTable_tbTipoDocumento
 */
class Documento_Model_DbTable_tbTipoDocumento extends MinC_Db_Table_Abstract
{
    protected $_schema = 'bdcorporativo.scCorp';
    protected $_name = 'tbTipoDocumento';
    protected $_primary = 'idTipoDocumento';

    const TIPO_DOCUMENTO_READEQUACAO = 38;
}
