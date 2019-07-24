<?php

class Diligencia_Model_DbTable_TbDiligencia extends MinC_Db_Table_Abstract
{
    public $modeloTbDiligencia;

    protected $_schema = 'sac';
    protected $_name = 'tbDiligencia';
    protected $_primary = 'idDiligencia';

    public function listarDiligencias($where = [], $retornaSelect = false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('pro' => 'projetos'),
            array(
                'IdPRONAC',
                'nomeProjeto' => 'pro.NomeProjeto',
                'pronac' => new Zend_Db_Expr('pro.AnoProjeto+pro.Sequencial'))
        );

        $select->joinInner(
            array('dil' => $this->_name),
            'dil.idPronac = pro.IdPRONAC',
            array(
                'dil.stProrrogacao',
                'idDiligencia' => 'dil.idDiligencia',
                'dataSolicitacao' => 'dil.DtSolicitacao',
                'dataResposta' => 'dil.DtResposta',
                'Solicitacao' => 'dil.Solicitacao',
                'Resposta' => new Zend_Db_Expr('CAST(dil.Resposta AS TEXT)'),
                'dil.idCodigoDocumentosExigidos',
                'dil.idTipoDiligencia',
                'dil.stEnviado'
            )
        );

        $select->joinInner(
            array('ver' => 'Verificacao'),
            'ver.idVerificacao = dil.idTipoDiligencia',
            array('tipoDiligencia' => 'ver.Descricao')
        );

        $select->joinLeft(
            array('prod' => 'Produto'),
            'prod.Codigo = dil.idProduto',
            array('produto' => 'prod.Descricao')
        );

        $select->joinInner(
            array('usu' => 'Usuarios'),
            'dil.idSolicitante = usu.usu_codigo',
            array('nomeParecerista' => 'usu_nome', 'idParecerista' => 'usu_codigo'),
            $this->getSchema('tabelas')
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($retornaSelect) {
            return $select;
        } else {
            return $this->fetchAll($select);
        }
    }

}
