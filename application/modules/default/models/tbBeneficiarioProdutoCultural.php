<?php
/**
 * DAO tbBeneficiarioProdutoCultural
 * @since 16/03/2011
 * @version 1.0
 * @link http://www.cultura.gov.br
 */

class tbBeneficiarioProdutoCultural extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbBeneficiarioProdutoCultural";

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
        $where = "idBeneficiarioProdutoCultural = " . $where;
        return $this->update($dados, $where);
    } // fecha m�todo alterarDados()


    public function buscarPlanosCadastrados($idPronac)
    {
        $a = $this->select();
        $a->setIntegrityCheck(false);
        $a->from(
                array('a' => $this->_name),
                array('idBeneficiarioProdutoCultural','idTipoBeneficiario','qtRecebida','idPlanoDistribuicao')
        );
        $a->joinInner(
                array('b' => 'PlanoDistribuicaoProduto'),
            "a.idPlanoDistribuicao = b.idPlanoDistribuicao",
                array(''),
            'SAC.dbo'
        );
        $a->joinInner(
                array('c' => 'Produto'),
            "b.idProduto = c.Codigo",
                array('Descricao as Produto'),
            'SAC.dbo'
        );
        $a->joinInner(
                array('d' => 'Nomes'),
            "a.idAgente = d.idAgente",
                array('Descricao as Beneficiario'),
            'AGENTES.dbo'
        );
        $a->joinInner(
                array('e' => 'tbDocumento'),
            "a.idDocumento = e.idDocumento",
                array(''),
            'BDCORPORATIVO.scCorp'
        );
        $a->joinInner(
                array('f' => 'tbArquivo'),
            "e.idArquivo = f.idArquivo",
                array('idArquivo','nmArquivo','dtEnvio'),
            'BDCORPORATIVO.scCorp'
        );
        $a->joinInner(
                array('g' => 'Agentes'),
            "d.idAgente = g.idAgente",
                array('CNPJCPF'),
            'AGENTES.dbo'
        );
        $a->where('a.IdPRONAC = ?', $idPronac);
        $a->order('c.Descricao');
        return $this->fetchAll($a);
    }
}
