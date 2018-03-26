<?php

class HistoricoDocumento extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'tbHistoricoDocumento';

    public function pesquisarOrgaosPorAcao($acaoA=null, $acaoB=null, $usu_codigo=null, $orgao=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('h' => $this->_name),
                array(
                    'h.idUnidade as idDestino',
                    'h.idLote as lote',
                    'h.idOrigem',
                    new Zend_Db_Expr('(select Sigla from SAC.dbo.Orgaos where Codigo = '.$orgao.') as nomeDestino')
                )
        );
        $select->where('h.stEstado = ?', 1);
        $select->where('h.idDocumento = ?', 0);

        if (($acaoA) and !($acaoB)) {
            $select->where("h.Acao = ?", $acaoA);
        }
        if (($acaoA) and ($acaoB)) {
            $select->where("(h.Acao = ? or h.Acao = $acaoB)", $acaoA);
        }
        if ($usu_codigo) {
            $select->where("h.idUsuarioEmissor = ?", $usu_codigo);
        }
        if ($orgao) {
            $select->where("h.idOrigem = ?", $orgao);
        }
        $select->group(array('h.idLote','h.idUnidade','h.idOrigem'));
        
        return $this->fetchAll($select);
    }
    
    public function pesquisarOrgaosPorDestino($acaoA = null, $acaoB = null, $usu_codigo=null, $orgao = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('h' => $this->_name),
                array(
                    'h.idUnidade as idDestino',
                    'h.idLote as lote',
                    'h.idOrigem',
                    new Zend_Db_Expr('(select Sigla from SAC.dbo.Orgaos where Codigo = '.$orgao.') as nomeDestino')
                )
        );
        $select->where('h.stEstado = ?', 1);
        $select->where('h.idDocumento = ?', 0);

        if (($acaoA) and !($acaoB)) {
            $select->where("h.Acao = ?", $acaoA);
        }
        if (($acaoA) and ($acaoB)) {
            $select->where("(h.Acao = ? or h.Acao = $acaoB)", $acaoA);
        }
        if ($orgao) {
            $select->where("h.idUnidade = ?", $orgao);
        }
        $select->where("h.idOrigem is not null");
        $select->group(array('h.idLote','h.idUnidade','h.idOrigem'));

        return $this->fetchAll($select);
    }
    
    public function pesquisarOrgaosPorDestinoRecebimento($acaoA = null, $acaoB = null, $usu_codigo=null, $orgao = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('h' => $this->_name),
                array(
                    'h.idUnidade as idDestino',
                    'h.idLote as lote',
                    'h.idOrigem',
                    new Zend_Db_Expr('(select Sigla from SAC.dbo.Orgaos where Codigo = '.$orgao.') as nomeDestino')
                )
        );
        $select->where('h.stEstado = ?', 1);
        $select->where('h.idDocumento = ?', 0);

        if (($acaoA) and !($acaoB)) {
            $select->where("h.Acao = ?", $acaoA);
        }
        if (($acaoA) and ($acaoB)) {
            $select->where("(h.Acao = ? or h.Acao = $acaoB)", $acaoA);
        }

        if ($orgao) {
            $select->where("h.idUnidade = ?", $orgao);
        }
        $select->group(array('h.idLote','h.idUnidade','h.idOrigem'));

        return $this->fetchAll($select);
    }

    public function projetosDespachados($acao = array(), $idDestino = null, $lote = null, $idpronac=null, $orgaologado=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
                array('h' => $this->_name),
                array(
                    "h.idHistorico",
                    "h.meDespacho as despacho",
                    "h.idUnidade AS idDestino",
                    "h.idOrigem",
                    "h.idUsuarioReceptor",
                    "h.idUsuarioEmissor",
                    "h.idLote as idLote",
                    "h.Acao as Acao",
                    "p.Orgao as Orgao",
                    "dtEnvio" => new Zend_Db_Expr("(CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) + ' ' + CONVERT(CHAR(8), h.dtTramitacaoEnvio,108))"),
                    "dtRecebida" =>new Zend_Db_Expr("(CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) + ' ' + CONVERT(CHAR(8), h.dtTramitacaoRecebida,108))"),
                    "dtSituacao" => new Zend_Db_Expr("(CONVERT(CHAR(10), p.DtSituacao,103) + ' ' + CONVERT(CHAR(8), p.DtSituacao,108))"),
                    "Situacao" => new Zend_Db_Expr(
                            "CASE
                              WHEN h.Acao = 0 THEN 'Bloqueado'
                              WHEN h.Acao = 1 THEN 'Cadastrado'
                              WHEN h.Acao = 2 THEN 'Enviado'
                              WHEN h.Acao = 3 THEN 'Recebido'
                              WHEN h.Acao = 4 THEN 'Recusado'
                              WHEN h.Acao = 6 THEN 'Anexado'
                              END"
                    ),
                    "h.stEstado"
                )
        );
        $select->joinInner(
                array('p' => 'projetos'),
                'h.idPronac = p.IdPRONAC',
                array(
                    'p.IdPRONAC as idPronac',
                    '(p.AnoProjeto + p.Sequencial) AS Pronac',
                    'p.NomeProjeto',
                    'p.Processo'
                )
        );
        $select->joinInner(
                array('org' => 'Orgaos'),
                'org.org_codigo = p.orgao',
                array('org.org_sigla as Origem'),
                'Tabelas.dbo'
        );

        $select->joinLeft(
                array('usue' => 'Usuarios'),
                'usue.usu_codigo = h.idUsuarioEmissor',
                array('usue.usu_nome as Emissor'),
                'Tabelas.dbo'
        );
        $select->joinLeft(
                array('usud' => 'Usuarios'),
                'usud.usu_codigo = h.idUsuarioReceptor',
                array('usud.usu_nome as Receptor'),
                'Tabelas.dbo'
        );

        $select->where('h.idDocumento = ?', 0);
        $select->where(' h.stEstado = ?', 1);

        if ($acao) {
            if (count($acao) == 1) {
                $select->where(' h.Acao = ? ', $acao[0]);
            }
            if (count($acao) == 2) {
                $select->where('( h.Acao = ? ', $acao[0]);
                $select->orWhere(' h.Acao = ? )', $acao[1]);
            }
        }
        if ($idDestino) {
            $select->where(' h.idUnidade = ? ', $idDestino);
        }
        if ($lote) {
            $select->where(' h.idLote = ? ', $lote);
        }
        if ($idpronac) {
            $select->where(' h.idPronac = ? ', $idpronac);
        }
        if ($orgaologado) {
            $select->where(' p.Orgao = ? ', $orgaologado);
        }
        $select->order('h.idHistorico');
        
        return $this->fetchAll($select);
    }
    
    public function projetosDespachadosListagem($acao = array(), $idDestino = null, $lote = null, $idpronac=null, $idUsuario=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(
                array('h' => $this->_name),
                array(
                    "h.idHistorico",
                        "h.meDespacho as despacho",
                        "h.idUnidade AS idDestino",
                    "h.idOrigem",
                        "h.idUsuarioReceptor",
                        "h.idUsuarioEmissor",
                        "h.idLote as idLote",
                        "h.Acao as Acao",
                        "h.dsJustificativa",
                    "p.Orgao as Orgao",
                        "dtEnvio" =>  new Zend_Db_Expr("(CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) + ' ' + CONVERT(CHAR(8), h.dtTramitacaoEnvio,108))"),
                    "dtRecebida" => new Zend_Db_Expr("(CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) + ' ' + CONVERT(CHAR(8), h.dtTramitacaoRecebida,108))"),
                        "dtSituacao" =>  new Zend_Db_Expr("(CONVERT(CHAR(10), p.DtSituacao,103) + ' ' + CONVERT(CHAR(8), p.DtSituacao,108))"),
                        "Situacao" => new Zend_Db_Expr(
                            "CASE
                              WHEN h.Acao = 0 THEN 'Bloqueado'
                              WHEN h.Acao = 1 THEN 'Cadastrado'
                              WHEN h.Acao = 2 THEN 'Enviado'
                              WHEN h.Acao = 3 THEN 'Recebido'
                              WHEN h.Acao = 4 THEN 'Recusado'
                              WHEN h.Acao = 6 THEN 'Anexado'
                              END"
                        ),
                        "h.stEstado"
                )
        );
        $select->joinInner(
                array('p' => 'projetos'),
                'h.idPronac = p.IdPRONAC',
                array(
                    'p.IdPRONAC as idPronac',
                    '(p.AnoProjeto + p.Sequencial) AS Pronac',
                    'p.NomeProjeto',
                    new Zend_Db_Expr('SAC.dbo.fnFormataProcesso(p.IdPRONAC) AS Processo')
                )
        );
        $select->joinInner(
                array('org' => 'Orgaos'),
                'org.org_codigo = p.orgao',
                array('org.org_sigla as Origem'),
                'Tabelas.dbo'
        );

        $select->joinLeft(
                array('usue' => 'Usuarios'),
                'usue.usu_codigo = h.idUsuarioEmissor',
                array('usue.usu_nome as Emissor'),
                'Tabelas.dbo'
        );
        $select->joinLeft(
                array('usud' => 'Usuarios'),
                'usud.usu_codigo = h.idUsuarioReceptor',
                array('usud.usu_nome as Receptor'),
                'Tabelas.dbo'
        );

        $select->where('h.idDocumento = ?', 0);
        $select->where('h.stEstado = ?', 1);

        if ($acao) {
            if (count($acao) == 1) {
                $select->where(' h.Acao = ? ', $acao[0]);
            }
            if (count($acao) == 2) {
                $select->where('( h.Acao = ? ', $acao[0]);
                $select->orWhere(' h.Acao = ? )', $acao[1]);
            }
        }
        if ($idDestino) {
            $select->where(' h.idUnidade = ? ', $idDestino);
        }
        if ($lote) {
            $select->where(' h.idLote = ? ', $lote);
        }
        if ($idpronac) {
            $select->where(' h.idPronac = ? ', $idpronac);
        }
        if ($idUsuario) {
            $select->where(' h.idUsuarioEmissor = ? ', $idUsuario);
        }
        $select->order('h.idHistorico');
        
        return $this->fetchAll($select);
    }

    public function alterarHistoricoDocumento($dados, $where)
    {
        $update = $this->update($dados, $where);
        return $update;
    }

    public function inserirHistoricoDocumento($dados)
    {
        $inserir = $this->insert($dados);
        return $inserir;
    }
}
