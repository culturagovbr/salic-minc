<?php
/**
 * DAO tbComprovanteTrimestral
 * @since 16/03/2011
 * @version 1.0
 * @package application
 * @subpackage application.model
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class tbComprovanteTrimestral extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbComprovanteTrimestral";

    /**
     * M�todo para cadastrar
     * @access public
     * @param array $dados
     * @return integer (retorna o �ltimo id cadastrado)
     */
    public function cadastrarDados($dados)
    {
        return $this->insert($dados);
    } // fecha m�todo cadastrarDados()


    /**
     * M�todo para alterar
     * @access public
     * @param array $dados
     * @param integer $where
     * @return integer (quantidade de registros alterados)
     */
    public function alterarDados($dados, $where)
    {
        $where = "idComprovanteTrimestral = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()


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
    } // fecha m�todo alterarDados()
}
