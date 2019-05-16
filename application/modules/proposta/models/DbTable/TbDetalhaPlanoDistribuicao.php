<?php

class Proposta_Model_DbTable_TbDetalhaPlanoDistribuicao extends MinC_Db_Table_Abstract
{

    protected $_schema = 'sac';
    protected $_name = 'tbDetalhaPlanoDistribuicao';
    protected $_primary = 'idDetalhaPlanoDistribuicao';

    public function salvar($dados)
    {
        return $this->insert($dados);
    }

    public function listarPorMunicicipioUF($dados)
    {
        $cols = array(
            '*'
        );

        $sql = $this->select()
            ->from($this->_name, $cols, $this->_schema)
            ->where('idPlanoDistribuicao = ?', $dados['idPlanoDistribuicao']);

        if ($dados['idUF']) {
            $sql->where(' idUF= ?', $dados['idUF']);
        }

        if ($dados['idMunicipio']) {
            $sql->where(' idMunicipio= ?', $dados['idMunicipio']);

        }

        return $this->fetchAll($sql);
    }

    public function excluir($id)
    {
        return $this->delete("idDetalhaPlanoDistribuicao =  $id");
    }

    public function excluirByIdPreProjeto($idPreProjeto, $where = array(), $order = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("d" => $this->_name), array('d.idDetalhaPlanoDistribuicao'), $this->_schema);

        $slct->joinInner(
            array("p" => 'planodistribuicaoproduto'),
            "p.idPlanoDistribuicao = d.idPlanoDistribuicao",
            array(),
            $this->_schema
        );

        $slct->where('p.idProjeto = ?', $idPreProjeto);

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        $this->delete(new Zend_Db_Expr('idDetalhaPlanoDistribuicao IN (' . $slct .')'));
    }

    public function obterDetalhamentosDaProposta($idPreProjeto, $where = array(), $order = null)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("d" => $this->_name), $this->_getCols(), $this->_schema);

        $slct->joinInner(
            array("p" => 'PlanoDistribuicaoProduto'),
            "p.idPlanoDistribuicao = d.idPlanoDistribuicao",
            'p.idProjeto',
            $this->_schema
        );

        $slct->where('p.idProjeto = ?', $idPreProjeto);

        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        $slct->order($order);

        try {
            return $this->fetchAll($slct)->toArray();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateLocalizacaoDetalhamento($novoIdUF, $novoIdMunicipio, $idProjeto, $idUf, $idMunicipio)
    {
        $query = "UPDATE tbDetalhaPlanoDistribuicao 
            SET tbDetalhaPlanoDistribuicao.idUF = ?, tbDetalhaPlanoDistribuicao.idMunicipio = ?
            FROM sac.dbo.tbDetalhaPlanoDistribuicao AS tbDetalhaPlanoDistribuicao 
            INNER JOIN  sac.dbo.PlanoDistribuicaoProduto AS PlanoDistribuicaoProduto 
            ON PlanoDistribuicaoProduto.idPlanoDistribuicao = tbDetalhaPlanoDistribuicao.idPlanoDistribuicao
            WHERE PlanoDistribuicaoProduto.idProjeto = ? 
            AND tbDetalhaPlanoDistribuicao.idUF = ? 
            AND tbDetalhaPlanoDistribuicao.idMunicipio = ?";

        $bind = [
            $novoIdUF,
            $novoIdMunicipio,
            $idProjeto,
            $idUf,
            $idMunicipio
        ];

        $db = Zend_Db_Table::getDefaultAdapter();
        $stmt = $db->query($query, $bind);
        return $stmt->fetch();
    }

    public function deleteDetalhamentoByLocalizacao($idProjeto, $idUf, $idMunicipio)
    {
        $query = "DELETE tbDetalhaPlanoDistribuicao
            FROM sac.dbo.tbDetalhaPlanoDistribuicao AS tbDetalhaPlanoDistribuicao
            INNER JOIN sac.dbo.PlanoDistribuicaoProduto AS PlanoDistribuicaoProduto
            ON PlanoDistribuicaoProduto.idPlanoDistribuicao = tbDetalhaPlanoDistribuicao.idPlanoDistribuicao
            WHERE PlanoDistribuicaoProduto.idProjeto = ? 
            AND tbDetalhaPlanoDistribuicao.idUF = ?
            AND tbDetalhaPlanoDistribuicao.idMunicipio = ?";

        $bind = [
            $idProjeto,
            $idUf,
            $idMunicipio
        ];

        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query($query, $bind);
    }
}
