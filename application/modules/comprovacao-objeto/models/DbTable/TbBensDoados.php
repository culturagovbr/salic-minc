<?php

class ComprovacaoObjeto_Model_DbTable_TbBensDoados extends MinC_Db_Table_Abstract
{
    protected $_banco  = "SAC";
    protected $_schema = "SAC";
    protected $_name   = "tbBensDoados";

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
        $where = "idBensDoados = " . $where;
        return $this->update($dados, $where);
    }


    public function buscarBensCadastrados($where, $order = array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);

        $slct->from(
                array('a' => $this->_name),
                array('idBensDoados','idPronac','tpBem','qtBensDoados', new Zend_Db_Expr('CAST(dsObservacao AS TEXT) AS dsObservacao'))
        );
        $slct->joinLeft(
                array('b' => 'tbPlanilhaItens'),
            "a.idItemOrcamentario = b.idPlanilhaItens",
                array('Descricao as ItemOrcamentario'),
            'SAC.dbo'
        );
        $slct->joinLeft(
                array('c' => 'Agentes'),
            "a.idAgente = c.idAgente",
                array('CNPJCPF'),
            'AGENTES.dbo'
        );
        $slct->joinLeft(
                array('d' => 'Nomes'),
            "a.idAgente = d.idAgente",
                array('Descricao as NomeAgente'),
            'AGENTES.dbo'
        );
        $slct->joinLeft(
                array('e' => 'tbDocumento'),
            "a.idDocumentoDoacao = e.idDocumento",
                array('idArquivo as idArquivoDoacao'),
            'BDCORPORATIVO.scCorp'
        );
        $slct->joinLeft(
                array('f' => 'tbArquivo'),
            "e.idArquivo = f.idArquivo",
                array('nmArquivo as nmArquivoDoacao'),
            'BDCORPORATIVO.scCorp'
        );
        $slct->joinLeft(
                array('g' => 'tbDocumento'),
            "a.idDocumentoAceite = g.idDocumento",
                array('idArquivo as idArquivoAceite'),
            'BDCORPORATIVO.scCorp'
        );
        $slct->joinLeft(
                array('h' => 'tbArquivo'),
            "g.idArquivo = h.idArquivo",
                array('nmArquivo as nmArquivoAceite'),
            'BDCORPORATIVO.scCorp'
        );

        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        return $this->fetchAll($slct);
    }
}
