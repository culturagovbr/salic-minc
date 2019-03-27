<?php

class ComprovacaoObjeto_Model_DbTable_TbComprovanteTrimestral extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbComprovanteTrimestral";

    /**
     * Metodo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    }

    /**
     * Metodo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idComprovanteTrimestral = " . $where;
        return $this->update($dados, $where);
    }


    public function buscarComprovantes($where, $all = false, $order = array())
    {
        // criando objeto do tipo select
        $slct = $this->select();
        $slct->from(
            $this->_name,
            array('idComprovanteTrimestral', 'IdPRONAC', 'dtComprovante', 'dtInicioPeriodo', 'dtFimPeriodo',
                new Zend_Db_Expr('CAST(dsEtapasExecutadas AS TEXT) AS dsEtapasExecutadas'),
                new Zend_Db_Expr('CAST(dsAcessibilidade AS TEXT) AS dsAcessibilidade'),
                new Zend_Db_Expr('CAST(dsDemocratizacaoAcesso AS TEXT) AS dsDemocratizacaoAcesso'),
                new Zend_Db_Expr('CAST(dsImpactoAmbiental AS TEXT) AS dsImpactoAmbiental'),
                'siComprovanteTrimestral', 'nrComprovanteTrimestral', 'idCadastrador',
                new Zend_Db_Expr('CAST(dsParecerTecnico AS TEXT) AS dsParecerTecnico'),
                new Zend_Db_Expr('CAST(dsRecomendacao AS TEXT) AS dsRecomendacao'), 'idTecnicoAvaliador')
        );

        // adicionando clausulas where
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        // retornando os registros
        if ($all) {
            return $this->fetchAll($slct);
        } else {
            return $this->fetchRow($slct);
        }
    }
}
