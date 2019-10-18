<?php

class AvaliacaoResultados_Model_DbTable_FluxosProjeto extends MinC_Db_Table_Abstract
{
    protected $_name = "FluxosProjeto";
    protected $_schema = "SAC";
    protected $_primary = "id";


    private function obterQueryProjetos()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            array('a' => $this->_name),
            array('*'),
            $this->_schema
        );

        $select->join(
            ['b' => 'projetos'],
            'b.IdPRONAC = a.idPronac',
            [
                new Zend_Db_Expr('AnoProjeto + Sequencial as PRONAC'),
                'b.IdPRONAC',
                'b.Orgao as orgao',
                'b.Situacao',
                'b.NomeProjeto',
                'b.UfProjeto'
            ],
            $this->_schema
        );

        $select->joinLeft(
            ['c' => 'Usuarios'],
            'c.usu_codigo = a.idAgente',
            ['c.usu_nome'],
            $this->getSchema('Tabelas')
        );

        $select->joinLeft(
            ['d' => 'tbDocumentoAssinatura'],
            '(b.IdPRONAC = d.IdPRONAC AND d.idTipoDoAtoAdministrativo = 622 AND d.cdSituacao = 1)',
            [
                'd.idDocumentoAssinatura',
                'd.idTipoDoAtoAdministrativo',
                'd.stEstado',
                'd.cdSituacao'
            ],
            $this->_schema
        );

        $select->joinLeft(
            ['e' => 'tbDiligencia'],
            'b.IdPRONAC = e.idPronac AND e.idDiligencia = 
                (select MAX(idDiligencia) 
                    from sac..tbDiligencia as dili 
                    where b.IdPRONAC = dili.idPronac AND dili.idTipoDiligencia IN (174, 645))',
            [
                'e.idDiligencia',
                'e.DtSolicitacao',
                'e.DtResposta',
                'e.stEnviado'
            ],
            $this->_schema
        );

        $select->join(
            ['f' => 'Orgaos'],
            'b.Orgao = f.Codigo',
            [],
            $this->_schema
        );

        return $select;
    }

    public function projetos($estadoId, $idSecretaria, $idAgente = null)
    {
        $select  = $this->obterQueryProjetos();

        $select->where('estadoId = ? ', $estadoId);
        $select->where('f.idSecretaria = ? ', $idSecretaria);

        if ($idAgente) {
            $select->where('idAgente = ? ', $idAgente);
        }

        return $this->fetchAll($select);
    }

    public function projeto($idPronac)
    {
        $select = $this->obterQueryProjetos();

        $select->where('idPronac = ? ', $idPronac);

        return $this->fetchRow($select);
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

        if ($estadosId) {
            $select->where('estadoId in (?) ', $estadosId);
        }

        if ($idAgente) {
            $select->where('idAgente = ? ', $idAgente);
        }

        return $this->fetchRow($select);
    }
}
