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
                        THEN 'Técnico / Parecerista'
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

    public function buscarParecerReadequacao($idReadequacao)
    {
        $result = $this->buscarPareceresReadequacao([
            'a.idReadequacao = ?' => $idReadequacao,
            'b.idTipoAgente = ?' => 1
        ]);

        if (count($result) > 0) {
            $result = $result[0];
        } else {
            $result = [];
        }
        return $result;
    }

    public function possuiDocumentoAssinatura($idReadequacao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            ['tbReadequacaoXParecer' => $this->_name],
            [],
            $this->_schema
        );

        $select->joinInner(
            ['tbDocumentoAssinatura' => 'tbDocumentoAssinatura'],
            'tbDocumentoAssinatura.idAtoDeGestao = tbReadequacaoXParecer.idParecer',
            ['tbDocumentoAssinatura.idDocumentoAssinatura'],
            $this->_schema
        );

        $select->where('tbDocumentoAssinatura.stEstado = ?', Assinatura_Model_TbDocumentoAssinatura::ST_ESTADO_DOCUMENTO_ATIVO);
        $select->where('tbReadequacaoXParecer.idReadequacao = ?', $idReadequacao);

        return $this->_db->fetchAll($select);
    }
}
