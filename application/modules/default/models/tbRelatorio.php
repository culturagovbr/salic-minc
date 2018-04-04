<?php

class tbRelatorio extends MinC_Db_Table_Abstract
{
    protected $_banco = "SAC";
    protected $_schema = "SAC";
    protected $_name = "tbRelatorio";

    public function buscarDadosRelatorioPronac($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('p' => $this->_name),
            array('*')
        );
        if ($idpronac) {
            $select->where('p.idPRONAC = ?', $idpronac);
        }

        return $this->fetchAll($select);
    }

    public function buscarDadosRelatorio($where=array())
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->distinct();
        $select->from(
                array('r' => $this->_name),
            array()
        );
        $select->joinInner(
                array('pr' => 'Projetos'),
            'pr.IdPRONAC = r.idPRONAC',
            array(
            new Zend_Db_Expr('SAC.dbo.fnchecarDiligencia(pr.IdPRONAC) AS Diligencia'),
            'pr.IdPRONAC',
            'pr.AnoProjeto',
            'pr.Sequencial',
            'pr.NomeProjeto',
            'pr.UfProjeto',
            'pr.CgcCpf',
            'dtInicioExecucao' => new Zend_Db_Expr('(convert(varchar(30),pr.dtInicioExecucao, 103 ))'),
            'dtFimExecucao' => new Zend_Db_Expr('(convert(varchar(30),pr.dtFimExecucao, 103 ))')
                )
        );
        $select->joinLeft(
                array('dl' => 'tbDiligencia'),
            'dl.IdPRONAC = r.idPRONAC',
            array()
        );
        $select->joinInner(
                array('rt' => 'tbRelatorioTrimestral'),
            'rt.idRelatorio = r.idRelatorio',
            array()
        );
        $select->joinLeft(
                array('ab' => 'Abrangencia'),
            'ab.idProjeto = pr.idProjeto AND ab.stAbrangencia = 1',
            array()
        );
        $select->joinLeft(
                array('mc'=>'Mecanismo'),
                "mc.Codigo = pr.Mecanismo",
                array('mc.Descricao as dsMecanismo'),
                'SAC.dbo'
        );
        $select->joinInner(
                array("org"=>'Orgaos'),
                "org.org_codigo = pr.Orgao",
                array('org.org_superior', 'org.org_codigo'),
                'TABELAS.dbo'
        );
        foreach ($where as $key => $valor) {
            if (!is_array($valor) and (!$valor == '' or !$valor == 0)) {
                $select->where($key, $valor);
            } elseif (is_array($valor) and (!in_array(0, $valor))) {
                if (!in_array('', $valor)) {
                    $select->where($key, $valor);
                }
            }
        }

        return $this->fetchAll($select);
    }

    public function buscarDistribuicaoProduto($idpronac, $idProduto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('r' => $this->_name),
            array('*')
        );
        if ($idpronac) {
            $select->where('r.idPRONAC = ?', $idpronac);
        }
        if ($idProduto) {
            $select->where('r.idDistribuicaoProduto = ?', $idProduto);
        }
        return $this->fetchAll($select);
    }

    public function dadosGerais($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => $this->_name),
            array('a.idRelatorio')
        );
        $select->joinInner(
                array('b' => 'tbRelatorioTrimestral'),
            'b.idRelatorio = a.idRelatorio',
            array(
                new Zend_Db_Expr("CAST(b.dsObjetivosMetas AS TEXT) AS dsObjetivosMetas", 'b.stRelatorioTrimestral')),
            'SAC.dbo'
        );
        $select->joinLeft(
                array('c' => 'tbBeneficiario'),
            'c.idRelatorio = a.idRelatorio',
            array(
                'c.dsBeneficiario',
                'c.tpBeneficiario',
                'c.nrCNPJ',
                'c.nrCPF',
                new Zend_Db_Expr('CAST(c.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo'),
                new Zend_Db_Expr('CAST(c.dsEntrega AS TEXT) AS dsEntrega')),
            'SAC.dbo'
        );
        $select->where("a.idPRONAC = '" . $idpronac . "'");
        $select->where("b.stRelatorioTrimestral = 1");


        return $this->fetchAll($select);
    }

    public function dadosGerais2($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => $this->_name),
            array('a.idRelatorio')
        );
        $select->joinInner(
                array('b' => 'tbRelatorioTrimestral'),
            'b.idRelatorio = a.idRelatorio',
            array('b.dsObjetivosMetas'),
            'SAC.dbo'
        );
        $select->joinInner(
                array('c' => 'tbBeneficiario'),
            'c.idRelatorio = a.idRelatorio',
            array('c.dsBeneficiario', 'c.tpBeneficiario', 'c.nrCNPJ', 'c.nrCPF', 'c.dsPublicoAlvo', 'c.dsEntrega'),
            'SAC.dbo'
        );
        $select->where("a.idPRONAC = '" . $idpronac . "'");
        $select->where("a.idAgenteAvaliador IS NOT NULL ");
        $select->order('a.idRelatorio DESC');

        return $this->fetchAll($select);
    }

    public function DadosAcesso($idpronac, $tpAcesso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('a' => $this->_name),
            array('a.idRelatorio')
        );
        $select->joinInner(
                array('b' => 'tbRelatorioTrimestral'),
            'b.idRelatorio = a.idRelatorio',
            array(''),
            'SAC.dbo'
        );
        $select->joinInner(
                array('c' => 'tbAcesso'),
            'c.idRelatorio = a.idRelatorio',
            array(
                'c.idAcesso',
                'c.idRelatorio',
                new Zend_Db_Expr('CAST(c.dsAcesso AS TEXT) AS dsAcesso'),
                new Zend_Db_Expr('CAST(c.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo'),
                'c.qtPessoa',
                new Zend_Db_Expr('CAST(c.dsLocal AS TEXT) AS dsLocal'),
                new Zend_Db_Expr('CAST(c.dsEstruturaSolucao AS TEXT) AS dsEstruturaSolucao'),
                'c.tpAcesso',
                'c.stAcesso',
                'c.stQtPessoa',
                'c.stPublicoAlvo',
                'c.stLocal',
                'c.stEstrutura',
                'c.dsJustificativaAcesso'
            ),
            'SAC.dbo'
        );
        $select->where("a.idPRONAC = '" . $idpronac . "'");
        $select->where("a.tpRelatorio = 'T'");
        $select->where("b.stRelatorioTrimestral = 1");
        $select->where("c.tpAcesso = $tpAcesso ");

        return $this->fetchAll($select);
    }

    public function buscarRelatorioTrimestral($idpronac, $nrrelatorio)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('r' => $this->_name),
            array('r.idRelatorio')
        );
        $select->joinInner(
                array('rt' => 'tbRelatorioTrimestral'),
            "rt.idRelatorio = r.idRelatorio",
            array()
        );
        $select->where('r.IdPRONAC = ?', $idpronac);
        $select->where('r.tpRelatorio = ?', 'T');
        $select->order('rt.idRelatorio DESC');
        return $this->fetchAll($select);
    }

    public function buscarRelatorioTrimestrais($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('r' => $this->_name),
            array('*')
        );
        $select->joinInner(
                array('rt' => 'tbRelatorioTrimestral'),
            "rt.idRelatorio = r.idRelatorio",
            array('*')
        );
        $select->where('r.IdPRONAC = ?', $idpronac);
        $select->where('r.tpRelatorio = ?', 'T');
        $select->where('rt.stRelatorioTrimestral != ?', 1);
        return $this->fetchAll($select);
    }

    public function buscarRelatorioFinal($idpronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                array('r' => $this->_name),
            array('*')
        );
        $select->where('r.IdPRONAC = ?', $idpronac);
        $select->where('r.tpRelatorio = ?', 'C');
        return $this->fetchAll($select);
    }

    public function buscarTecnicoAcompanhamento($idPronac, $idusuario=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('rel'=>$this->_name),
                        array()
                      );

        $select->joinInner(
                            array('age'=>'Agentes'),
                            'age.idAgente = rel.idAgenteAvaliador',
                            array('age.idAgente'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('nm'=>'Nomes'),
                            'age.idAgente = nm.idAgente',
                            array('Nome'=>'nm.Descricao'),
                            'AGENTES.dbo'
                           );
        $select->joinInner(
                            array('usu'=>'Usuarios'),
                            'usu.usu_identificacao = age.CNPJCPF',
                            array(),
                            'TABELAS.dbo'
                           );
        $select->joinInner(
                            array('uog'=>'UsuariosXOrgaosXGrupos'),
                            'uog.uog_usuario = usu.usu_codigo',
                            array(),
                            'TABELAS.dbo'
                           );
        $select->joinInner(
                            array('gru'=>'Grupos'),
                            'gru.gru_codigo = uog.uog_grupo',
                            array('Perfil'=>'gru.gru_nome','cdPerfil'=>'gru.gru_codigo'),
                            'TABELAS.dbo'
                           );

        $select->joinInner(
                            array('org'=>'Orgaos'),
                            'org.Codigo = uog.uog_orgao',
                            array('Orgao'=>'org.Sigla'),
                            'SAC.dbo'
                           );
        $select->where('gru.gru_codigo = 121');
        $select->where('rel.idPRONAC = ?', $idPronac);
        //$select->where('usu.usu_codigo <> ?', $idusuario);

        return $this->fetchAll($select);
    }

    public function dadosRelatoriosAnteriores($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array()
                      );

        $select->joinInner(
                            array('b'=>'Projetos'),
                            'b.IdPRONAC = a.idPRONAC',
                            array('*'),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('c'=>'tbRelatorioTrimestral'),
                            'a.idRelatorio = c.idRelatorio',
                            array(
                                'c.idRelatorioTrimestral',
                                'c.idRelatorio',
                                new Zend_Db_Expr('CAST(c.dsParecer AS TEXT) AS dsParecer'),
                                new Zend_Db_Expr('CAST(c.dsObjetivosMetas AS TEXT) AS dsObjetivosMetas'),
                                'c.dtCadastro',
                                'c.stRelatorioTrimestral',
                                'c.nrRelatorioTrimestral'),
                            'SAC.dbo'
                           );
        $select->where('a.IdPRONAC = ?', $idPronac);
        $select->where('c.stRelatorioTrimestral != 1');

        return $this->fetchAll($select);
    }

    public function dadosAcessoAnteriores($idPronac, $tpAcesso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array()
                      );

        $select->joinInner(
                            array('b'=>'tbAcesso'),
                            'a.idRelatorio = b.idRelatorio',
                             array(
                                 'b.idAcesso',
                                 'b.idRelatorio',
                                 new Zend_Db_Expr('CAST(b.dsAcesso AS TEXT) AS dsAcesso'),
                                 new Zend_Db_Expr('CAST(b.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo'),
                                 new Zend_Db_Expr('convert(varchar(10),b.qtPessoa) as qtPessoa'),
                                 new Zend_Db_Expr('CAST(b.dsLocal AS TEXT) AS dsLocal'),
                                 new Zend_Db_Expr('CAST(b.dsEstruturaSolucao AS TEXT) AS dsEstruturaSolucao'),
                                 'b.tpAcesso',
                                 'b.stAcesso',
                                 'b.stQtPessoa',
                                 'b.stPublicoAlvo',
                                 'b.stLocal',
                                 'b.stEstrutura',
                                 'b.dsJustificativaAcesso'
                             ),
                            'SAC.dbo'
                           );
        $select->where('a.idPRONAC = ?', $idPronac);
        $select->where('b.tpAcesso = ?', $tpAcesso);
        return $this->fetchAll($select);
    }

    public function dadosBeneficiarioAnteriores($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array()
                      );

        $select->joinInner(
                            array('b'=>'tbBeneficiario'),
                            'a.idRelatorio = b.idRelatorio',
                            array(
                                'b.idRelatorio',
                                'b.dsBeneficiario',
                                'b.tpBeneficiario',
                                'b.nrCNPJ',
                                'b.nrCPF',
                                new Zend_Db_Expr('CAST(b.dsPublicoAlvo AS TEXT) AS dsPublicoAlvo'),
                                new Zend_Db_Expr('CAST(b.dsEntrega AS TEXT) AS dsEntrega')),
                            'SAC.dbo'
                           );
        $select->where('a.idPRONAC = ?', $idPronac);

        return $this->fetchAll($select);
    }

    public function dadosRelatorioLiberacao($idPronac)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(
                        array('a'=>$this->_name),
                        array()
                      );

        $select->joinInner(
                            array('b'=>'Projetos'),
                            'a.idPRONAC = b.IdPRONAC',
                            array(),
                            'SAC.dbo'
                           );
        $select->joinInner(
                            array('c'=>'Liberacao'),
                            'c.AnoProjeto = b.AnoProjeto and c.Sequencial = b.Sequencial',
                            array('*'),
                            'SAC.dbo'
                           );
        $select->where('a.idPRONAC = ?', $idPronac);

        return $this->fetchAll($select);
    }
}
