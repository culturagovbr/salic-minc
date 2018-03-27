<?php

class Analise_Model_DbTable_TbAvaliarAdequacaoProjeto extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbAvaliarAdequacaoProjeto';
    protected $_primary = 'idAvaliarAdequacaoProjeto';


    public function buscarUltimaAvaliacao($idPronac)
    {
        $avaliacao = $this->buscar(
            array('idPronac = ?' => $idPronac),
            array('idAvaliarAdequacaoProjeto DESC'),
            1
        )->toArray();

        return $avaliacao[0];
    }

    public function inserirAvaliacao($idPronac, $orgaoUsuario, $idTecnico = null)
    {
        if (empty($idTecnico)) {
            $idTecnico = new Zend_Db_Expr('sac.dbo.fnPegarTecnico(110, ' . $orgaoUsuario . ' ,1)');
        }

        $dados = array(
            'idPronac' => $idPronac,
            'dtEncaminhamento' => new Zend_Db_Expr('GETDATE()'),
            'idTecnico' => $idTecnico,
            'dtAvaliacao' => null,
            'dsAvaliacao' => null,
            'siEncaminhamento' => TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_MINC,
            'stAvaliacao' => 0,
            'stEstado' => 1
        );

        return $this->insert($dados);
    }

    public function atualizarAvaliacaoNegativa($idPronac, $idTecnico, $avaliacao)
    {
        $dados = array(
            'dtAvaliacao' => new Zend_Db_Expr('GETDATE()'),
            'dsAvaliacao' => $avaliacao,
            'siEncaminhamento' => TbTipoEncaminhamento::SOLICITACAO_DEVOLVIDA_AO_PROPONENTE_PARA_AJUSTES,
            'stAvaliacao' => 2,
            'stEstado' => 0
        );

        $where = array(
            'idPronac = ?' => $idPronac,
            'stEstado = ?' => 1,
            'siEncaminhamento = ?' => TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_MINC,
            'idTecnico = ?' => $idTecnico
        );

        return $this->update($dados, $where);
    }


    public function atualizarAvaliacaoPositiva($idPronac, $idTecnico, $avaliacao)
    {
        $dados = array(
            'dtAvaliacao' => new Zend_Db_Expr('GETDATE()'),
            'dsAvaliacao' => $avaliacao,
            'siEncaminhamento' => TbTipoEncaminhamento::SOLICITACAO_FINALIZADA_PELO_MINC,
            'stAvaliacao' => 1,
            'stEstado' => 0
        );

        $where = array(
            'idPronac = ?' => $idPronac,
            'stEstado = ?' => 1,
            'siEncaminhamento = ?' => TbTipoEncaminhamento::SOLICITACAO_ENCAMINHADA_AO_MINC,
            'idTecnico = ?' => $idTecnico
        );

        return $this->update($dados, $where);
    }

    public function obterAvaliacoes($where = array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                'a.idAvaliarAdequacaoProjeto',
                'a.idTecnico',
                'a.idPronac',
                new Zend_Db_Expr('convert(varchar(30), dtAvaliacao, 120) as dtAvaliacao'),
                new Zend_Db_Expr("CAST(A.dsAvaliacao AS TEXT) AS dsAvaliacao"),
            ),
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        $select->where('a.stEstado = ?', 0);
//        $select->where('a.stAvaliacao = ?', 2);
//        $select->where('a.siEncaminhamento = ?', 0);

        $select->order('a.DtAvaliacao DESC');

        return $this->fetchAll($select);
    }
}
