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
                'dil.idProduto',
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

    public function listarDiligenciasReadequacao($where = [])
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            ['tbDiligencia' => $this->_name],
            ['tbDiligencia.stProrrogacao',
             'idDiligencia' => 'tbDiligencia.idDiligencia',
             'dataSolicitacao' => 'tbDiligencia.DtSolicitacao',
             'dataResposta' => 'tbDiligencia.DtResposta',
             'Solicitacao' => 'tbDiligencia.Solicitacao',
             'Resposta' => new Zend_Db_Expr('CAST(tbDiligencia.Resposta AS TEXT)'),
             'tbDiligencia.idCodigoDocumentosExigidos',
             'tbDiligencia.idTipoDiligencia',
             'tbDiligencia.stEnviado',
             'idPronac' => 'tbDiligencia.idPronac',
            ]);

        $select->joinInner(
            ['tbReadequacaoXtbDiligencia' => 'tbReadequacaoXtbDiligencia'],
            'tbReadequacaoXtbDiligencia.idDiligencia = tbDiligencia.idDiligencia',
            [],
            $this->_schema
        );

        $select->joinInner(
            ['tbReadequacao' => 'tbReadequacao'],
            'tbReadequacao.idReadequacao = tbReadequacaoXtbDiligencia.idReadequacao',
            [],
            $this->_schema
        );

        $select->joinInner(
            ['projetos' => 'projetos'],
            'projetos.idPronac = tbDiligencia.idPronac',
            [
                'IdPRONAC',
                'nomeProjeto' => 'projetos.NomeProjeto',
                'pronac' => new Zend_Db_Expr('projetos.AnoProjeto+projetos.Sequencial')
            ],
            $this->_schema
        );

        $select->joinInner(
            ['verificacao' => 'Verificacao'],
            'verificacao.idVerificacao = tbDiligencia.idTipoDiligencia',
            ['tipoDiligencia' => 'verificacao.Descricao'],
            $this->_schema
        );

        $select->joinInner(
            ['tbTipoReadequacao'],
            'tbTipoReadequacao.idTipoReadequacao = tbReadequacao.idTipoReadequacao',
            ['produto' => new Zend_Db_Expr('CAST(tbTipoReadequacao.dsReadequacao AS TEXT)')],
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        return $this->fetchAll($select);
    }
}
