<?php

class AvaliacaoResultados_Model_DbTable_FluxosProjeto extends MinC_Db_Table_Abstract
{
    protected $_name = "FluxosProjeto";
    protected $_schema = "SAC";
    protected $_primary = "id";

    public function projetos($estadoId, $idAgente = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('e' => $this->_name),
            array('*'),
            $this->_schema
        )
        ->join(
            ['p' => 'projetos'],
            'p.idpronac = e.idpronac',
            ['*', new Zend_Db_Expr('anoprojeto+sequencial as PRONAC')],
            $this->_schema
        )
        //inner join Tabelas.dbo.Usuarios as a ON a.usu_codigo = fp.idAgente
        ->joinLeft(
            ['u' => 'Usuarios'],
            'u.usu_codigo = e.idAgente',
            ['u.usu_nome'],
            'Tabelas.dbo'
        )
            ->joinLeft(
                ['doc' => new Zend_Db_Expr(
                    '(SELECT idDocumentoAssinatura,
                                       IdPRONAC,
                                       idTipoDoAtoAdministrativo,
                                       stEstado,
                                       cdSituacao
                                FROM "SAC"."dbo"."tbDocumentoAssinatura"
                                WHERE idTipoDoAtoAdministrativo = 622
                                  AND cdSituacao = 1
                                  AND stEstado = 1)')],
                'doc.IdPRONAC = p.idpronac',
                ['idDocumentoAssinatura',
                    'idTipoDoAtoAdministrativo',
                    'stEstado',
                    'cdSituacao']
            )
        ->joinLeft(
            ['dil' => new Zend_Db_Expr('(SELECT a.idPronac, 
                                                          a.idDiligencia, 
                                                          a.DtSolicitacao, 
                                                          a.DtResposta, a.stEnviado 
                                                   FROM "sac"."dbo"."tbDiligencia" as a 
                                                   where a.DtSolicitacao = (select max(DtSolicitacao) 
                                                                            from sac..tbDiligencia 
                                                                            where a.idPronac = idPronac))')],
            'dil.idPronac = p.IdPRONAC',
            ['idDiligencia',
             'DtSolicitacao',
             'DtResposta',
             'stEnviado']
            )
        ->where('estadoId = ? ', $estadoId);

        if($idAgente) {
            $select->where('idAgente = ? ', $idAgente);
        }

        return $this->fetchAll($select);
    }

    public function estado($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('e' => $this->_name),
            array('*'),
            $this->_schema
        )
        ->where('idpronac = ? ', $idPronac);

        return $this->fetchRow($select);
    }

    public function quantidadeProjetos($estadosId = [], $idAgente = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('fp' => $this->_name),
            [new Zend_Db_Expr('count(*) as quantidade')],
            $this->_schema
        );

        if($estadosId) {
            $select->where('estadoId in (?) ', $estadosId);
        }

        if($idAgente) {
            $select->where('idAgente = ? ', $idAgente);
        }

        return $this->fetchRow($select);
    }
}
