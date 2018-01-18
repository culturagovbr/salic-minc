<?php
/**
 * DAO tbReadequacaoXParecer
 * @author jeffersonassilva@gmail.com - XTI
 * @since 13/03/2014
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbReadequacaoXParecer extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbReadequacaoXParecer";

    /*
     * Criada em 13/03/2014
     * @author: Jefferson Alessandro - jeffersonassilva@gmail.com
     */
    public function buscarPareceresReadequacao($where=array(), $order=array())
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
