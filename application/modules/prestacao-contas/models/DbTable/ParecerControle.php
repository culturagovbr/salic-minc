<?php

class PrestacaoContas_Model_DbTable_ParecerControle extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'ParecerControle';
    protected $_primary = 'Contador';

    public function obterPrioridadesOrgaosDeControle($idPronac)
    {
        $sql = $this->select();
        $sql->setIntegrityCheck(false);
        $sql->from(
            ['a' => $this->_name],
           [
               new Zend_Db_Expr('a.Contador as id'),
               new Zend_Db_Expr("
                   CASE
                       WHEN Controladores = 0 THEN 'Unidades do MinC'
                       WHEN Controladores = 1 THEN 'Entidades Vinculadas'
                       WHEN Controladores = 2 THEN 'Tribunal de Contas da Uni&atilde;o'
                       WHEN Controladores = 3 THEN 'AECI'
                       WHEN Controladores = 4 THEN 'Minist&eacute;rio P&uacute;blico Federal'
                       WHEN Controladores = 5 THEN 'Outros &oacute;rg&atilde;os de Controle'
                       WHEN Controladores = 6 THEN 'Controle Social'
                       ELSE 'Sem informa&ccedil;&atilde;o'
                   END Controladores
               "),
               new Zend_Db_Expr("
                   CASE
                       WHEN DocumentoSolicitacao = 0 THEN 'Of&iacute;cio'
                       WHEN DocumentoSolicitacao = 1 THEN 'Relat&oacute;rio'
                       WHEN DocumentoSolicitacao = 2 THEN 'Dilig&ecirc;ncia'
                       WHEN DocumentoSolicitacao = 3 THEN 'Outros'
                       WHEN DocumentoSolicitacao = 4 THEN 'Memorando'
                       ELSE 'Sem informa&ccedil;&atilde;o'
                   END as DocumentoSolicitacao
               "),
               new Zend_Db_Expr("
                   CASE
                       WHEN DocumentoResposta = 0 THEN 'Of&iacute;cio'
                       WHEN DocumentoResposta = 1 THEN 'Relat&oacute;rio'
                       WHEN DocumentoResposta = 2 THEN 'Dilig&ecirc;ncia'
                       WHEN DocumentoResposta = 3 THEN 'Outros'
                       WHEN DocumentoResposta = 4 THEN 'Memorando'
                       ELSE 'Sem informa&ccedil;&atilde;o'
                   END as DocumentoResposta
               "),
               'DtSolicitacao',
               'Constatacao',
               'DtResposta',
               'Resposta',
               'NrDocumentoSolicitacao'
           ], $this->_schema
        );

        $sql->joinInner(
            ['b' => 'Projetos'],
            'a.AnoProjeto = b.AnoProjeto AND a.Sequencial = b.Sequencial',
            [
                'idPronac',
                new Zend_Db_Expr('a.AnoProjeto + a.Sequencial as PRONAC'),
                'NomeProjeto',
                'DtSituacao',
                'ProvidenciaTomada',
                'Orgao'
            ],
            $this->_schema
        );

        $sql->where('b.idPronac = ?', $idPronac);

        return $this->fetchAll($sql);
    }

}
