<?php

class Solicitacao_Model_DbTable_TbSolicitacao extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'tbSolicitacao';
    protected $_primary = 'idSolicitacao';


    public function obterSolicitacoes($where=array(), $order=array('a.dtSolicitacao DESC'), $tamanho= 300, $inicio=-1)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                ['a' => $this->_name],
                [
                    'idSolicitacao',
                    'idPronac',
                    'idProjeto',
                    'stLeitura',
                    'stEstado',
                    'idOrgao',
                    'idSolicitante',
                    'dtSolicitacao',
                    'CAST(dsSolicitacao AS TEXT) AS dsSolicitacao',
                    'dtResposta',
                    'CAST(dsResposta AS TEXT) AS dsResposta',
                    'idDocumento',
                    'siEncaminhamento',
                    'idTecnico',
                    'dtEncaminhamento',
                    'idDocumentoResposta',
                    new Zend_Db_Expr("
                        CASE
                            WHEN a.dtSolicitacao IS NOT NULL AND a.dtResposta IS NULL
                                THEN datediff(day,a.dtSolicitacao,GETDATE())
                            WHEN a.dtSolicitacao IS NULL OR a.dtResposta IS NOT NULL
                                THEN 0
                            END as diasSemResposta
                    "),
                    new Zend_Db_Expr(
                        "CASE
                            WHEN e.NomeProjeto IS NOT NULL
                                THEN e.NomeProjeto
                            ELSE f.NomeProjeto
                        END as NomeProjeto"
                    )
                ],
                $this->_schema
            )
        ;

        $select->joinInner(
            ['b' => 'Orgaos'],
            'a.idOrgao = b.Codigo',      
            ['Sigla'],
            $this->_schema
        );
        
        $select->joinInner(
            ['c' => 'Nomes'],
            'a.idSolicitante = c.idAgente',
            ['idAgente', 'Descricao as Solicitante'],
            $this->getSchema('Agentes')
        );

        $select->joinLeft(
            ['d' => 'Usuarios'],
            'a.idTecnico = d.usu_codigo',
            ['usu_nome as Tecnico'],
            $this->getSchema('Tabelas')
        );

        $select->joinLeft(
            ['e' => 'Projetos'],
            'a.idPronac = e.IdPRONAC',
            [new Zend_Db_Expr('AnoProjeto+e.Sequencial as Pronac')],
            $this->_schema
        );
        
        $select->joinLeft(
            ['f' => 'PreProjeto'],
            'a.idProjeto = f.idPreProjeto',
            [''],
            $this->_schema
        );
        
        $select->joinInner(
            ['g' => 'tbTipoEncaminhamento'],
            'a.siEncaminhamento = g.idTipoEncaminhamento',
            ['g.dsEncaminhamento'],
            $this->_schema
        );

        foreach ($where as $coluna=>$valor)
        {
            $select->where($coluna, $valor);
        }

        $select->order($order);

        if ($tamanho > -1)
        {
            $tmpInicio = 0;
            if ($inicio > -1)
            {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($select);
    }

    public function contarSolicitacoesNaoLidasUsuario($idUsuario, $idAgente)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('count(idSolicitacao)')
            ),
            $this->_schema
        );

        $select->where('a.stLeitura = ?', 0);
        $select->where('a.dtResposta IS NOT NULL');
        $select->where("a.idAgente = {$idAgente} OR a.idSolicitante = {$idUsuario}");

        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->fetchOne($select);
    }

    public function contarSolicitacoesNaoRespondidasTecnico($idTecnico, $idOrgao)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array(
                new Zend_Db_Expr('count(a.idSolicitacao)')
            ),
            $this->_schema
        );

        $select->where('a.idTecnico = ?', $idTecnico);
        $select->where('a.idOrgao = ?', $idOrgao);
        $select->where('a.dtResposta is null', '');
        $select->where('a.siEncaminhamento = ?', Solicitacao_Model_TbSolicitacao::SITUACAO_ENCAMINHAMENTO_ENCAMINHADA_AO_MINC);

        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->fetchOne($select);
    }
}