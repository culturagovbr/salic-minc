<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * Description of Liberacao
 * @author 01610881125
 */
class Liberacao extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_schema = 'SAC';
    protected $_name = 'Liberacao';
    protected $_base = 'SAC.dbo.Liberacao';

    public function liberacaoPorProjeto($idPronac)
    {
        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(
                array("l"=>$this->_name),
                array(
                        "l.DtLiberacao","l.vlLiberado"
                    )
                );
        $slct->joinInner(
                array("p"=>"projetos"),
                "l.AnoProjeto=p.AnoProjeto and l.Sequencial=p.Sequencial",
                array(
                        'NrProjeto'=>new Zend_Db_Expr('p.AnoProjeto+p.Sequencial'),
                        'p.NomeProjeto'
                    )
                );
        $slct->joinInner(
                array("u"=>"Usuarios"),
                "l.Logon=u.usu_Codigo",
                array(
                        'u.usu_Nome'
                    ),
                'tabelas.dbo'
                );


        $slct->where('p.IdPRONAC = ?', $idPronac);

        
        return $this->fetchAll($slct)->current();
    }

    public function dadosRelatorioLib($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array('*')
                      );

        $select->joinInner(
                            array('b'=>'Projetos'),
                            'a.AnoProjeto = b.AnoProjeto and a.Sequencial = b.Sequencial',
                            array(),
                            'SAC.dbo'
                           );
        $select->where('b.IdPRONAC = ?', $idPronac);

        return $this->fetchAll($select);
    }

    public function buscarProjetosLiberados($pronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('l'=>$this->_name),
                        array('Pronac'=>new Zend_Db_Expr('l.AnoProjeto+l.Sequencial'))
                      );

        $select->where('l.AnoProjeto+l.Sequencial = ?', $pronac);
        
        return $this->fetchAll($select);
    }

    public function buscaProjetosInabilitados($orgao = null, $cpf = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
                array('p' => 'Projetos'),
            array(
            'Pronac' => new Zend_Db_Expr('p.AnoProjeto+p.Sequencial'), "p.IdPRONAC", "p.NomeProjeto", "p.Situacao",
                new Zend_Db_Expr("sac.dbo.fnPercentualCaptado(p.AnoProjeto, p.Sequencial) as captacao")
                )
        );

        $select->joinInner(
                array('i' => 'Inabilitado'),
            'i.AnoProjeto+i.Sequencial = p.AnoProjeto+p.Sequencial',
            array(
            "i.Habilitado as Status", "i.Orgao as idOrgao", new Zend_Db_Expr("TABELAS.dbo.fnEstruturaOrgao(i.Orgao, 0) AS Orgao"), "i.CgcCpf"
                ),
            array(),
            'SAC.dbo'
        );

        /*$select->joinInner(
                array('c' => 'CertidoesNegativas'), 'c.AnoProjeto+c.Sequencial = p.AnoProjeto+p.Sequencial', array(), 'SAC.dbo'
        );*/

        if ($orgao) {
            $select->where('p.Orgao = ?', $orgao);
        }
        if ($cpf) {
            $select->where('i.CgcCpf = ?', $cpf);
            $select->where('i.Habilitado = ?', 'N');
        }
        //$select->Where("p.Situacao = 'E12' or p.Situacao = 'E13'");

        return $this->fetchAll($select);
    }

    /*public function buscaProjetoLiberacao($orgao = null, $cpf = null, $inicio = null, $fim = null, $count = false) {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('p' => 'Projetos'), array('p.AnoProjeto',
                      'p.Sequencial',
                      'Pronac' => new Zend_Db_Expr('p.AnoProjeto+p.Sequencial'),
                      'p.IdPRONAC',
                      'p.NomeProjeto',
                      'p.Situacao')
                    );

            $select->joinLeft(
                array('i' => 'Inabilitado'), 'i.AnoProjeto = p.AnoProjeto and i.Sequencial = p.Sequencial and i.CgcCpf = p.CgcCpf', array(
                        'CgcCpf' => new Zend_Db_Expr('CASE
                                                                WHEN i.CgcCpf Is NOT NULL
                                                                THEN i.CgcCpf
                                                                ELSE p.CgcCpf
                                                                END
                                                                '
            ),
            'Status' => new Zend_Db_Expr("CASE
                                                                WHEN i.Habilitado = 'N'
                                                                THEN 'N'
                                                                ELSE 'S'
                                                                END
                                                                "
            )
                )
        );


        $select->joinInner(
                array('cn' => 'CertidoesNegativas'), 'cn.AnoProjeto = p.AnoProjeto and cn.Sequencial = p.Sequencial', array('c' => new Zend_db_Expr("CASE
                                                         WHEN datediff(dy,Getdate(),cn.DtValidade)>=0
                                                         THEN 'Ok'
                                                         ELSE 'Vencida'
                                                         END
                                                         "
            )
                )
        );

        $select->where("p.Orgao ={$orgao}");
        $select->where("p.Situacao = 'E12' or p.Situacao = 'E13'");
        $select->where("dbo.fnPercentualCaptado(p.AnoProjeto,p.Sequencial) >= 20");
        $select->where("NOT EXISTS(SELECT * FROM Sac.dbo.Liberacao l WHERE p.AnoProjeto=l.AnoProjeto and p.Sequencial=l.Sequencial)");

        if ($cpf) {
            $select->where("CgcCpf = '{$cpf}'");
        }


        if ($count) {
            $slct2 = $this->select();
            $slct2->setIntegrityCheck(false);
            $slct2->from(
                    array('pr' => $slct), array("total" => "count(*)")
            );
            $rs = $this->fetchAll($slct2)->current();
            return $rs->total;
        }


        return $this->fetchAll($select);
    }*/

    //public function buscaProjetoLiberacao($orgao = null, $cpf = null, $inicio = null, $fim = null, $count = false) {
    public function buscaProjetoLiberacao($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count = false)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(
              array('p' => 'Projetos'),
              array('p.AnoProjeto',
                    'p.Sequencial',
                    'Pronac' => new Zend_Db_Expr('p.AnoProjeto+p.Sequencial'),
                    'p.IdPRONAC',
                    'p.NomeProjeto',
                    'p.Situacao',
                    'p.CgcCpf',
                    'Inabilitado' => new Zend_Db_Expr("CASE
                                                            WHEN (SELECT TOP 1 Habilitado FROM Inabilitado i WHERE p.CgcCpf = i.CgcCpf) = 'N'
                                                            THEN 'SIM'
                                                            ELSE 'N�O'
                                                            END "),
                    'Certidao' => new Zend_Db_Expr("CASE
                                                            WHEN EXISTS(SELECT top 1 CONVERT(CHAR(8),DtValidade,112) FROM CertidoesNegativas c WHERE p.CgcCpf = c.CgcCpf AND CodigoCertidao <> 244 AND CodigoCertidao <> 70 AND CONVERT(CHAR(8),DtValidade,112) <  GETDATE())
                                                            THEN 'VENCIDA'
                                                            ELSE 'VALIDA'
                                                            END "),
                    'Cadin' => new Zend_Db_Expr("CASE
                                                            WHEN (SELECT TOP 1 cdSituacaoCertidao FROM CertidoesNegativas c WHERE p.CgcCpf = c.CgcCpf) = '1'
                                                            THEN 'CADIN REGULAR'
                                                            ELSE 'CADIN PENDENTE'
                                                            END ")
                )
        );


        /*$slct->where("p.Orgao ={$orgao}");
        $slct->where("p.Situacao = 'E12' or p.Situacao = 'E13'");
        $slct->where("dbo.fnPercentualCaptado(p.AnoProjeto,p.Sequencial) >= 20");
        $slct->where("NOT EXISTS(SELECT * FROM Sac.dbo.Liberacao l WHERE p.AnoProjeto=l.AnoProjeto and p.Sequencial=l.Sequencial)");

        if ($cpf) {
            $slct->where("CgcCpf = '{$cpf}'");
        }*/

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if ($count) {
            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(
                            array('p'=>'Projetos'),
                            array('total'=>"count(*)")
                         );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }
            $rs = $this->fetchAll($slctContador)->current();
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

    public function buscarCaptacao($pronac)
    {
        $sql = "SELECT sac.dbo.fnPercentualCaptado(p.AnoProjeto, p.Sequencial) AS captacao
				FROM Sac.dbo.Projetos AS p where p.AnoProjeto+p.Sequencial = '$pronac'";
//
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 104857600");
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function buscarCertidoesVencidas($cpf)
    {
        $sql = "select CONVERT(CHAR(10), DtValidade,103) as DtValidade from SAC.dbo.CertidoesNegativas where CgcCpf = '$cpf'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $resultado = $db->fetchAll("SET TEXTSIZE 104857600");
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function liberarProjeto($dados)
    {
        $sql = "insert into SAC.dbo.Liberacao
				values
				('$dados[AnoProjeto]', '$dados[Sequencial]', 1, '$dados[DtLiberacao]', '$dados[DtDocumento]', '$dados[NumeroDocumento]', '$dados[VlOutrasFontes]', '$dados[Observacao]', '$dados[CgcCpf]', '$dados[Permissao]', $dados[Logon])";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query($sql);

        return $db->lastInsertId();
    }

    public function consultarLiberacoes($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('l' => $this->_name),
                array( new Zend_Db_Expr("CONVERT(CHAR(10), l.DtLiberacao,103) as DtLiberacao"), 'vlLiberado' )
        );
        $select->joinInner(
            array('p' => 'Projetos'),
            'l.AnoProjeto=p.AnoProjeto AND l.Sequencial=p.Sequencial',
            array( 'IdPRONAC', new Zend_Db_Expr('p.AnoProjeto+p.Sequencial AS Pronac'), 'NomeProjeto' ),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Agentes'),
            'p.CgcCpf = a.CNPJCPF',
            array( 'CNPJCPF' ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array( 'Descricao as Proponente' ),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('u' => 'Usuarios'),
            'l.Logon=u.usu_Codigo',
            array(),
            'TABELAS.dbo'
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

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $select->limit($tamanho, $tmpInicio);
        }

        
        return $this->fetchAll($select);
    }

    public function consultarLiberacoesTotalValorGrid($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $qtdeTotal=false)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('l' => $this->_name),
                array('vlLiberado' => new Zend_Db_Expr("SUM(l.vlLiberado)"))
        );
        $select->joinInner(
            array('p' => 'Projetos'),
            'l.AnoProjeto=p.AnoProjeto AND l.Sequencial=p.Sequencial',
            array(),
            'SAC.dbo'
        );
        $select->joinInner(
            array('a' => 'Agentes'),
            'p.CgcCpf = a.CNPJCPF',
            array(),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('n' => 'Nomes'),
            'a.idAgente = n.idAgente',
            array(),
            'AGENTES.dbo'
        );
        $select->joinInner(
            array('u' => 'Usuarios'),
            'l.Logon=u.usu_Codigo',
            array(),
            'TABELAS.dbo'
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $select->where($coluna, $valor);
        }

        
        return $this->fetchRow($select);
    }

    /**
     * Método que diz se a conta para um projeto buscado está liberada
     * 
     * @param integer $idPronac
     * @return bool
     */
    public function contaLiberada($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);        
        $select->from(
            array('l' => $this->_name),
            array('Permissao')
        );
        $select->joinInner(
            array('p' => 'projetos'),
            new Zend_Db_Expr('p.AnoProjeto = l.AnoProjeto AND p.Sequencial = l.Sequencial'),
            array(''),
            $this->_schema
        );
        $select->where('p.idPronac = ?', $idPronac);
        
        $contaLiberada = $this->fetchAll($select);

        return ($contaLiberada) ? true: false;
    }   
}
