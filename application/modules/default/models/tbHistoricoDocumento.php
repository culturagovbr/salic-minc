<?php
class tbHistoricoDocumento extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_name = "tbHistoricoDocumento";

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarCompleto($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("hd"=>$this->_name),
                array(
                    "idHistorico",
                    "dtTramitacaoEnvio" => new Zend_Db_Expr("CONVERT(CHAR(20),dtTramitacaoEnvio, 120)"),
                    "dtTramitacaoRecebida" =>  new Zend_Db_Expr("CONVERT(CHAR(20),dtTramitacaoRecebida, 120)"),
                    "idLote",
                    "Acao"
                )
        );
        $slct->joinInner(
                array("pr"=>"Projetos"),
                "pr.IdPRONAC = hd.idPronac",
                array("IdPRONAC", "Pronac"=>new Zend_Db_Expr("pr.AnoProjeto + pr.Sequencial"), "NomeProjeto"),
                "SAC.dbo"
        );
        $slct->joinLeft(
                array("oo"=>"Orgaos"),
                "oo.Codigo = hd.idOrigem",
                array("Origem"=>"Sigla"),
                "SAC.dbo"
        );
        $slct->joinLeft(
                array("od"=>"Orgaos"),
                "od.Codigo = hd.idUnidade",
                array("Destino"=>"Sigla"),
                "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }


        return $this->fetchAll($slct);
    }

    public function pegaTotalCompleto($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("hd"=>$this->_name),
                array("total"=>new Zend_Db_Expr("count(*)"))
        );
        $slct->joinInner(
                array("pr"=>"Projetos"),
                "pr.IdPRONAC = hd.idPronac",
                array(),
                "SAC.dbo"
        );
        $slct->joinLeft(
                array("oo"=>"Orgaos"),
                "oo.Codigo = hd.idOrigem",
                array(),
                "SAC.dbo"
        );
        $slct->joinLeft(
                array("od"=>"Orgaos"),
                "od.Codigo = hd.idUnidade",
                array(),
                "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    /**
     * Retorna registros do banco de dados
     * @param array $where - array com dados where no formato "nome_coluna_1"=>"valor_1","nome_coluna_2"=>"valor_2"
     * @param array $order - array com orders no formado "coluna_1 desc","coluna_2"...
     * @param int $tamanho - numero de registros que deve retornar
     * @param int $inicio - offset
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function buscarTramitacaoDocumento($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("hd"=>$this->_name),
                array(
                    "idHistorico",
                    "dtTramitacaoEnvio"=> new Zend_Db_Expr("CONVERT(CHAR(20),dtTramitacaoEnvio, 120)"),
                    "dtTramitacaoRecebida"=> new Zend_Db_Expr("CONVERT(CHAR(20),dtTramitacaoRecebida, 120)"),
                    "idLote",
                    "Acao"
                )
        );
        $slct->joinInner(
                array("d"=>"tbDocumento"),
                "d.idDocumento = hd.idDocumento",
                array(
                    "NoArquivo", 
                    "dtDocumento"=> new Zend_Db_Expr("CONVERT(CHAR(20),dtDocumento, 120)"), 
                    "CodigoCorreio", 
                    "idDocumento"
                ),
                "SAC.dbo"
        );
        $slct->joinInner(
                array("pr"=>"Projetos"),
                "pr.IdPRONAC = hd.idPronac",
                array("IdPRONAC", "Pronac"=>new Zend_Db_Expr("pr.AnoProjeto + pr.Sequencial"), "NomeProjeto"),
                "SAC.dbo"
        );
        $slct->joinLeft(
                array("oo"=>"Orgaos"),
                "oo.Codigo = hd.idOrigem",
                array("Origem"=>"Sigla"),
                "SAC.dbo"
        );
        $slct->joinLeft(
                array("od"=>"Orgaos"),
                "od.Codigo = hd.idUnidade",
                array("Destino"=>"Sigla"),
                "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }


        return $this->fetchAll($slct);
    }

    public function pegaTotalTramitacaoDocumento($where=array())
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("hd"=>$this->_name),
                array("total"=>new Zend_Db_Expr("count(*)"))
        );
        $slct->joinInner(
                array("d"=>"tbDocumento"),
                "d.idDocumento = hd.idDocumento",
                array(),
                "SAC.dbo"
        );
        $slct->joinInner(
                array("pr"=>"Projetos"),
                "pr.IdPRONAC = hd.idPronac",
                array(),
                "SAC.dbo"
        );
        $slct->joinLeft(
                array("oo"=>"Orgaos"),
                "oo.Codigo = hd.idOrigem",
                array(),
                "SAC.dbo"
        );
        $slct->joinLeft(
                array("od"=>"Orgaos"),
                "od.Codigo = hd.idUnidade",
                array(),
                "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchAll($slct);
    }

    public function buscarHistoricoTramitacaoProjeto($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("h"=>$this->_name),
                array("h.idHistorico",
                "dtTramitacaoEnvio"=>new Zend_Db_Expr("CONVERT(CHAR(20),h.dtTramitacaoEnvio, 120)"),
                "dtTramitacaoRecebida"=>new Zend_Db_Expr("CONVERT(CHAR(20),h.dtTramitacaoRecebida, 120)"),
                "idDestino"=>"h.idUnidade",
                "h.meDespacho",
                "Destino"=>new Zend_Db_Expr("TABELAS.dbo.fnEstruturaOrgao(h.idunidade,0)"),
                "h.idLote",
                "h.idUsuarioEmissor",
                "Emissor"=>new Zend_Db_Expr("SAC.dbo.fnNomeUsuario(h.idUsuarioEmissor)"),
                "h.idUsuarioReceptor",
                "Receptor"=>new Zend_Db_Expr("SAC.dbo.fnNomeUsuario(h.idUsuarioReceptor)"),
                "h.Acao",
                "Situacao" => new Zend_Db_Expr("CASE WHEN h.Acao = 1 THEN 'Cadastrado'
                                                               WHEN h.Acao = 2 THEN 'Enviado'
                                                               WHEN h.Acao = 3 THEN 'Recebido'
                                                               WHEN h.Acao = 4 THEN 'Recusado'
                                                               WHEN h.Acao = 6 THEN 'Anexado' END"),
                ),
            "SAC.dbo"
        );
        $slct->joinInner(
                array("p"=>"Projetos"),
                "p.IdPRONAC = h.idPronac",
                array("p.IdPRONAC",
                "Pronac"=>new Zend_Db_Expr("p.AnoProjeto + p.Sequencial"),
                "p.NomeProjeto",
                "idOrigem"=>"p.Orgao",
                "Origem"=>new Zend_Db_Expr("TABELAS.dbo.fnEstruturaOrgao(h.idorigem,0)"),
                "Processo"=>new Zend_Db_Expr("SAC.dbo.fnFormataProcesso(p.idPronac)"),
                ),
                "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slctCount = $this->select();
            $slctCount->setIntegrityCheck(false);
            $slctCount->from(
                    array("h"=>$this->_name),
                    array('total'=>"count(*)"),
                "SAC.dbo"
            );
            $slctCount->joinInner(
                    array("p"=>"Projetos"),
                    "p.IdPRONAC = h.idPronac",
                    array(),
                    "SAC.dbo"
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctCount->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctCount)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }


        return $this->fetchAll($slct);
    }

    public function buscarHistoricoTramitacaoDocumento($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("h"=>$this->_name),
                array("h.idHistorico",
                "h.idLote",
                "h.Acao",
                "Situacao" => new Zend_Db_Expr("CASE WHEN h.Acao = 1 THEN 'Cadastrado'
                                                           WHEN h.Acao = 2 THEN 'Enviado'
                                                           WHEN h.Acao = 3 THEN 'Recebido'
                                                           WHEN h.Acao = 4 THEN 'Recusado'
                                                           WHEN h.Acao = 6 THEN 'Anexado' END"),
                ),
            "SAC.dbo"
        );
        $slct->joinInner(
                array("p"=>"Projetos"),
                "p.IdPRONAC = h.idPronac",
                array("p.IdPRONAC",
                "Pronac"=>new Zend_Db_Expr("p.AnoProjeto + p.Sequencial"),
                "p.NomeProjeto"
                ),
                "SAC.dbo"
        );
        $slct->joinInner(
                array("d"=>"tbDocumento"),
                "d.idPronac = p.idPronac",
                array("d.idDocumento",
                "dtDocumento" => new Zend_Db_Expr("CONVERT(CHAR(20),d.dtDocumento, 120)"),
                "dtJuntada" => new Zend_Db_Expr("CONVERT(CHAR(20),d.dtJuntada, 120)"),
                "d.imDocumento",
                "d.noArquivo",
                "Usuario"=>new Zend_Db_Expr("SAC.dbo.fnNomeUsuario(d.idUsuarioJuntada)")
                ),
                "SAC.dbo"
        );
        $slct->joinInner(
                array("t"=>"tbTipoDocumento"),
                "d.idTipoDocumento = t.idTipoDocumento",
                array("t.dsTipoDocumento"),
                "SAC.dbo"
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slctCount = $this->select();
            $slctCount->setIntegrityCheck(false);
            $slctCount->from(
                    array("h"=>$this->_name),
                    array('total'=>"count(*)"),
                "SAC.dbo"
            );
            $slctCount->joinInner(
                    array("p"=>"Projetos"),
                    "p.IdPRONAC = h.idPronac",
                    array(),
                    "SAC.dbo"
            );
            $slctCount->joinInner(
                    array("d"=>"tbDocumento"),
                    "d.idPronac = p.idPronac",
                    array(),
                    "SAC.dbo"
            );
            $slctCount->joinInner(
                    array("t"=>"tbTipoDocumento"),
                    "d.idTipoDocumento = t.idTipoDocumento",
                    array(),
                    "SAC.dbo"
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctCount->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctCount)->current();
            if ($rs) {
                return $rs->total;
            } else {
                return 0;
            }
        }

        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }

        return $this->fetchAll($slct);
    }

    public function consultarTramitacoes($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('h' => $this->_name),
                array('idHistorico','idOrigem AS idOrigem','idPronac','meDespacho as despacho','idUnidade AS idDestino',
                    'idUsuarioEmissor','idUsuarioReceptor','idLote','stEstado','Acao',
                    new Zend_Db_Expr("TABELAS.dbo.fnEstruturaOrgao(h.idOrigem, 0) AS Origem"),
                    new Zend_Db_Expr("TABELAS.dbo.fnEstruturaOrgao(h.idUnidade, 0) AS Destino"),
                    new Zend_Db_Expr("SAC.dbo.fnFormataProcesso(p.IdPRONAC) AS Processo"),
                    new Zend_Db_Expr("SAC.dbo.fnNomeUsuario(h.idUsuarioEmissor) AS Emissor"),
                    new Zend_Db_Expr("CONVERT(CHAR(10), h.dtTramitacaoEnvio,103) as dtEnvio"),
                    new Zend_Db_Expr("CONVERT(CHAR(10), h.dtTramitacaoRecebida,103) as dtRecebida"),
                    new Zend_Db_Expr("SAC.dbo.fnNomeUsuario(h.idUsuarioReceptor) AS Receptor"),
                    new Zend_Db_Expr("CASE
                                         WHEN h.Acao = 1 THEN 'Cadastrado'
                                         WHEN h.Acao = 2 THEN 'Enviado'
                                         WHEN h.Acao = 3 THEN 'Recebido'
                                         WHEN h.Acao = 4 THEN 'Recusado'
                                         WHEN h.Acao = 6 THEN 'Anexado'
                                       END AS Estado"),
                )
        );
        $select->joinInner(
            array('p' => 'Projetos'),
            'h.idPronac = p.IdPRONAC',
            array(new Zend_Db_Expr('AnoProjeto+Sequencial AS Pronac'),'NomeProjeto',new Zend_Db_Expr('h.idDocumento')),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('doc' => 'tbDocumento'),
            'h.idDocumento = doc.idDocumento',
            array('NoArquivo', 'CodigoCorreio'),
            'SAC.dbo'
        );
        $select->joinLeft(
            array('td' => 'tbTipoDocumento'),
            'doc.idTipoDocumento = td.idTipoDocumento',
            array('dsTipoDocumento'),
            'SAC.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        if ($qtdeTotal) {
            return $this->fetchAll($select)->count();
        }
        //adicionando linha order ao select
        $select->order($order);

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
}
