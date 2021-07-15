<?php

/**
 * Tem como responsabilidade abstrair regras negociais da entidade SGCAcesso.
 * @class Agente_Model_AgentesMapper
 * @author Vinícius Feitosa da Silva <viniciusfesil@gmail.com>
 * @since 31/10/16 15:17
 * @link http://salic.cultura.gov.br
 */
class Autenticacao_Model_SgcacessoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Autenticacao_Model_DbTable_Sgcacesso');
    }
}
