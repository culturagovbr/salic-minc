<?php

/**
 * DAO tbReadequacaoXParecer
 * @since 13/03/2014
 */
class Readequacao_Model_DbTable_TbReadequacaoXParecer extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbReadequacaoXParecer";

    public function buscarPareceresReadequacao($where = array(), $order = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr("b.IdParecer, b.ParecerFavoravel, b.ResumoParecer, b.DtParecer, c.usu_nome AS Avaliador"),
                new Zend_Db_Expr("
                    CASE WHEN b.idTipoAgente = 1
                        THEN 'T&eacute;cnico / Parecerista'
                    WHEN b.idTipoAgente = 6
                        THEN 'Componente da Comiss&atilde;o'
                    END AS tpAvaliador
                ")
            )
        );
        $select->joinInner(
            array('b' => 'Parecer'),
            'b.IdParecer = a.idParecer',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
            array('c' => 'Usuarios'),
            'c.usu_codigo = b.Logon',
            array(''),
            'TABELAS.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $select->order($order);

        return $this->fetchAll($select);
    }
}
