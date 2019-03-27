<?php

class tbHistoricoEmail extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_name = "tbHistoricoEmail";

    const SITUACAO_ESTADO_ENVIADO = 1;
    const SITUACAO_ESTADO_NAO_ENVIADO = 0;
}
