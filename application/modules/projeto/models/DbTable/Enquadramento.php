<?php

class Projeto_Model_DbTable_Enquadramento extends MinC_Db_Table_Abstract
{
    protected $_schema = 'sac';
    protected $_name = 'Enquadramento';
    protected $_primary = 'IdEnquadramento';

    public function obterProjetoAreaSegmento($where, $order = array())
    {
        if (empty($where) || !is_array($where)) {
            return [];
        }

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                ['a' => 'Projetos'],
                [
                    'a.IdPRONAC',
                    'a.NomeProjeto',
                    'a.CgcCpf AS CNPJCPF',
                    'a.UfProjeto',
                    'a.DtInicioExecucao',
                    'a.DtFimExecucao',
                    'a.ResumoProjeto',
                    'a.OrgaoOrigem',
                    'a.SolicitadoReal AS VlSolicitado',
                    'a.SolicitadoReal AS CustoProjeto',
                    new Zend_Db_Expr('a.AnoProjeto + a.Sequencial AS Pronac'),
                    new Zend_Db_Expr('dbo.fnNomeDoProponente(a.IdPRONAC) AS Proponente'),
                    new Zend_Db_Expr('sac.dbo.fnVlAdequadoIncentivo(a.IdPRONAC) AS VlAdequadoIncentivo'),
                    new Zend_Db_Expr('sac.dbo.fnVlAdequadoOutrasFontes(a.IdPRONAC) AS VlAdequadoOutrasFontes'),
                    new Zend_Db_Expr('sac.dbo.fnVlTotalAdequado(a.IdPRONAC) AS VlTotalAdequado'),
                    new Zend_Db_Expr('sac.dbo.fnVlHomologadoIncentivo(a.IdPRONAC) AS VlHomologadoIncentivo'),
                    new Zend_Db_Expr('sac.dbo.fnVlHomologadoOutrasFontes(a.IdPRONAC) AS VlHomologadoOutrasFontes'),
                    new Zend_Db_Expr('sac.dbo.fnVlTotalHomologado(a.IdPRONAC) AS VlTotalHomologado'),
                ],
                $this->_schema
            );

        $sql->joinLeft(
            ['b' => $this->_name],
            'a.AnoProjeto = b.AnoProjeto AND a.Sequencial = b.Sequencial',
            [
                new Zend_Db_Expr("
                        CASE WHEN b.Enquadramento = '1' THEN 'Artigo 26' 
                        WHEN b.Enquadramento = '2' THEN 'Artigo 18' 
                        ELSE 'N&atilde;o enquadrado' END AS Enquadramento
                "),
                'b.DtEnquadramento',
                'b.Observacao AS AvaliacaoTecnica',
                'b.IdEnquadramento'
            ],
            $this->_schema
        );

        $sql->join(
            ['c' => 'Segmento'],
            'a.Segmento = c.Codigo',
            ['c.Descricao AS Segmento'],
            $this->_schema
        );

        $sql->join(
            ['d' => 'Area'],
            'a.Area = d.Codigo',
            ['d.Descricao AS Area'],
            $this->_schema
        );

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna, $valor);
        }

        if ($order) {
            $sql->order($order);
        }
        return $this->fetchAll($sql);
    }

    public function obterProjetosApreciadosCnic(
        $where,
        $order = null,
        $start = 0,
        $limit = 20,
        $search = null)
    {
        if (empty($where)) {
            return [];
        }

        $sql = $this->select()
            ->setIntegrityCheck(false)
            ->from(
                ['a' => 'Projetos'],
                [
                    new Zend_Db_Expr('a.AnoProjeto+a.Sequencial as Pronac'),
                    'a.IdPRONAC',
                    'ResumoProjeto',
                    'a.NomeProjeto',
                    'a.DtInicioExecucao',
                    'a.DtFimExecucao',
                    'a.Orgao as idUnidade',
                    new Zend_Db_Expr('sac.dbo.fnVlHomologadoIncentivo(a.IdPRONAC) as VlHomologadoIncentivo')
                ],
                $this->_schema
            );

        $sql->join(
            ['b' => $this->_name],
            'a.IdPRONAC = b.IdPRONAC',
            [
                new Zend_Db_Expr("
                        CASE WHEN b.Enquadramento = '1' THEN 'Artigo 26' 
                        WHEN b.Enquadramento = '2' THEN 'Artigo 18' 
                        ELSE 'N&atilde;o enquadrado' END AS Enquadramento
                "),
                'b.DtEnquadramento'
            ],
            $this->_schema
        );

        $sql->joinLeft(
            ['c' => 'tbPauta'],
            'a.IdPRONAC = c.IdPRONAC',
            [],
            'BDCORPORATIVO.scSAC'
        );

        $sql->joinLeft(
            ['d' => 'tbReuniao'],
            'c.idNrReuniao = d.idNrReuniao',
            ['d.NrReuniao'],
            $this->_schema
        );
        $sql->joinInner(
            ['e' => 'Orgaos'],
            'a.OrgaoOrigem = e.Codigo',
            ['e.idSecretaria AS idOrgaoSuperior'],
            $this->_schema
        );
        $sql->joinInner(
            ['f' => 'Area'],
            'f.Codigo = a.Area',
            [new Zend_Db_Expr('f.Descricao as Area')],
            $this->_schema
        );
        $sql->joinInner(
            ['g' => 'Segmento'],
            'g.Codigo = a.Segmento',
            [new Zend_Db_Expr('g.Descricao as Segmento')],
            $this->_schema
        );

        $sql->joinInner(
          ['h'=> 'tbVerificaProjeto'],
          'h.IdPRONAC = a.IdPRONAC',
          ['stAnaliseProjeto'],
          $this->_schema
        );

        $sql->joinInner(
            ['i' => 'Usuarios'],
            'h.idUsuario = i.usu_codigo',
            [
                'usu_nome AS Tecnico',
                new Zend_Db_Expr("DATEDIFF(day, h.dtrecebido, GETDATE()) AS tempoAnalise")
            ],
            'TABELAS.dbo'
        );
        $sql->joinInner(
            ['j' => 'tbProjetoFase'],
            'a.IdPRONAC = J.idPronac',
            [],
            $this->_schema
        );

        if (!empty($search['value'])) {
            $sql->where('a.NomeProjeto like ? OR a.AnoProjeto+a.Sequencial like ? OR d.NrReuniao like ?', '%'.$search['value'].'%');
        }

        $sql->where('j.idNormativo > 6');
        $sql->where('j.stEstado = 1');
        $sql->where('sac.dbo.fnDtPortariaAprovacao(a.AnoProjeto,a.Sequencial) IS NOT NULL');

        foreach ($where as $coluna => $valor) {
            $sql->where($coluna, $valor);
        }

        if (!empty($order)) {
            $sql->order($order);
        }

        if (!is_null($start) && $limit) {
            $start = (int) $start;
            $limit = (int) $limit;
            $sql->limit($limit, $start);
        }

        return $this->fetchAll($sql);
    }
}
