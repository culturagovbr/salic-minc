<?php

class AvaliacaoResultados_Model_DbTable_LaudoFinal extends MinC_Db_Table_Abstract
{
    protected $_name = "tbLaudoFinal";
    protected $_schema = "SAC";
    protected $_primary = "idLaudoFinal";

    public function all() {
        return $this->fetchAll();
    }

    public function laudoFinal($id)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select();
        $select->from(
            ['a' => $this->_name],
            [
                'a.idLaudoFinal',
                'a.siManifestacao',
                'a.dsLaudoFinal'
            ],
            $this->_schema
        )
        ->where('idPronac = ? ', $id);

        return $db->fetchRow($select);
    }

    public function projetosLaudoFinal($estadoId)
    {
        $auth = \Zend_Auth::getInstance();
        $orgao = $auth->getIdentity()->usu_org_max_superior;

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
            ['p' => 'Projetos'],
            ['p.IdPronac',
                'p.NomeProjeto',
                'p.Orgao',
                new Zend_Db_Expr('p.AnoProjeto+p.Sequencial AS PRONAC')
            ],
            'sac.dbo'
        )
            ->join(['fp'=>'FluxosProjeto'],
                'fp.idPronac=p.IdPRONAC',
                null,
                'sac.dbo'
            )

            ->joinLeft(['parecer'=>'tbAvaliacaoFinanceira'],
                'parecer.IdPronac=p.IdPRONAC',
                ['parecer.*','parecer.siManifestacao as dsResutaldoAvaliacaoObjeto'],
                'sac.dbo'
            )

            ->joinLeft(['o' => 'Orgaos'],
                'p.Orgao=o.Codigo',
                ['Codigo','Sigla','idSecretaria'],
                'sac.dbo'
            )
            ->joinLeft(['u' => 'Usuarios'],
                'u.usu_codigo = fp.idAgente',
                ['u.usu_nome','u.usu_codigo'],
                'Tabelas.dbo'
            )
        ->where('fp.estadoId = ? ', $estadoId)
        ->where('o.idSecretaria = ?', $orgao);

        return $this->fetchAll($select);
    }
}
