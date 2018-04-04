<?php

class ProponenteDAO extends Zend_Db_Table
{
    protected $_name = 'Projetos';
    protected $_schema = 'SAC';
    protected $_primary = 'IdPRONAC';

    public function execPaProponente($idPronac)
    {
        $idPronac = preg_replace("/[^0-9]/", "", $idPronac); //REMOVE injections
        $sql = "exec SAC.dbo.paAgente $idPronac";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);


        return $resultado;
    }

    public function verificarHabilitado($CgcCPf)
    {
        $sql = "SELECT i.Habilitado FROM SAC.dbo.Inabilitado i
                WHERE i.CgcCpf = '$CgcCPf' AND i.Habilitado='N'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public function buscarDadosProponente($idpronac)
    {
        $table = Zend_Db_Table::getDefaultAdapter();

        $select = $table->select()
            ->from(
                array('pr' => 'Projetos'),
                array(new Zend_Db_Expr('
                    case
                        when ag.tipoPessoa = 0 then \'Pessoa F&iacute;sica\'
                        when ag.tipoPessoa = 1 then \'Pessoa Jur&iacute;dica\'
                    end as tipoPessoa')),
                'SAC.dbo'
            )
            ->joinInner(
                array('itn' => 'Interessado'),
                'pr.CgcCpf = itn.CgcCpf',
                array('Nome', 'Endereco', 'CgcCpf', 'Uf', 'Cidade', 'Esfera', 'Responsavel', 'Cep', 'Administracao', 'Utilidade'),
                'SAC.dbo'
            )
            ->joinLeft(
                array('ag' => 'Agentes'),
                'ag.CNPJCPF = pr.CgcCpf',
                array(''),
                'Agentes.dbo'
            )
            ->joinLeft(
                array('nat' => 'Natureza'),
                'nat.idAgente = ag.idAgente',
                array('Direito'),
                'AGENTES.dbo'
            )
            ->where('pr.IdPRONAC = ?', $idpronac);

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($select);
        return $resultado;
    }

    public function buscarEmail($idpronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $select = $db->select()
            ->from(
                'Internet',
                array(new Zend_db_Expr('*'),new Zend_db_Expr("
                    CASE
                    WHEN Internet.TipoInternet = 28
                        THEN 'Email Particular'
                    WHEN Internet.TipoInternet = 29
                        THEN 'Email Institucional'
                    End as TipoInternet,
                    Internet.Descricao as Email    
                ")),
                'AGENTES.dbo'
            )
            ->where('Projetos.IdPRONAC = ?', $idpronac)
            ->joinLeft(
                'Agentes',
                'Agentes.IdAgente = Internet.IdAgente',
                array(''),
                'AGENTES.dbo'
            )
            ->joinLeft(
                'Projetos',
                'Agentes.CNPJCPF = Projetos.CgcCpf',
                array(''),
                'SAC.dbo'
            );

        $resultado = $db->fetchAll($select);

        return $resultado;
    }

    public function buscarTelefone($idpronac)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        $select = $db->select()
            ->from(
                'Telefones',
                array(new Zend_db_Expr("
                    CASE
                        WHEN Telefones.TipoTelefone = 22 or Telefones.TipoTelefone = 24
                            THEN 'Residencial'
                        WHEN Telefones.TipoTelefone = 23 or Telefones.TipoTelefone = 25
                            THEN 'Comercial'
                        WHEN Telefones.TipoTelefone = 26
                            THEN 'Celular'
                        WHEN Telefones.TipoTelefone = 27
                            THEN 'Fax'
                        END as TipoTelefone,
                        Uf.Descricao as UF,
                        Telefones.DDD as DDDTelefone,
                        Telefones.Numero as NumeroTelefone,
                        CASE
                        WHEN Telefones.Divulgar = 1
                            THEN 'Sim'
                        WHEN Telefones.Divulgar = 0
                            THEN 'N&atilde;o'
                    end as Divulgar
                ")),
                'AGENTES.dbo'
            )
            ->where('Projetos.IdPRONAC = ?', $idpronac)
            ->joinInner(
                'Uf',
                'Uf.idUF = Telefones.UF',
                array(''),
                'AGENTES.dbo'
            )
            ->joinInner(
                'Agentes',
                'Agentes.IdAgente = Telefones.IdAgente',
                array(''),
                'AGENTES.dbo'
            )
            ->joinInner(
                'Projetos',
                'Agentes.CNPJCPF = Projetos.CgcCpf',
                array(''),
                'SAC.dbo'
            );

        $resultado = $db->fetchAll($select);

        return $resultado;
    }

    public function dadospronacs($idpronac)
    {
        $sql = "select
            pr.AnoProjeto+pr.Sequencial as pronac,
            pr.NomeProjeto as nomeprojeto,
            ar.Descricao as area,
            seg.Descricao as seg,
            ap.AprovadoReal,
            SAC.dbo.fnTotalCaptacao(pr.AnoProjeto, pr.Sequencial) as captado,
            SAC.dbo.fnTotalAprovadoProjeto(pr.AnoProjeto, pr.Sequencial) as aprovado
            from SAC.dbo.Projetos pr
            join SAC.dbo.Aprovacao ap on ap.IdPRONAC = pr.IdPRONAC
            join SAC.dbo.Area ar on ar.Codigo = pr.Area
            join SAC.dbo.Segmento seg on seg.Codigo = pr.Segmento
            where pr.IdPRONAC = " . $idpronac;
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql);

        return $resultado1;
    }

    public function buscarArquivados($idpronac)
    {
        $sql3 = "SELECT
							Pr.IdPRONAC,
                                                        Pr.AnoProjeto+Pr.Sequencial as pronac,
							Pr.NomeProjeto,
							Ar.descricao dsArea,
							Sg.descricao dsSegmento,
							Pr.SolicitadoReal,
							CASE WHEN Pr.Mecanismo IN ('2','6')
							THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial) 
							ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial)
							END AS ValorAprovado,
							SAC.dbo.fnCustoProjeto (Pr.AnoProjeto,Pr.Sequencial) as ValorCaptado
							FROM SAC.dbo.Projetos Pr 
							INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
							INNER JOIN SAC.dbo.Area Ar ON  Ar.Codigo = Pr.Area
							INNER JOIN SAC.dbo.Segmento Sg ON Sg.Codigo = Pr.Segmento
							INNER JOIN SAC.dbo.Mecanismo Mc ON Mc.Codigo = Pr.Mecanismo
							INNER JOIN SAC.dbo.Enquadramento En ON En.idPRONAC =  Pr.idPRONAC
							LEFT JOIN AGENTES.dbo.Agentes A ON A.CNPJCPF = Pr.CgcCpf
							LEFT JOIN SAC.dbo.PreProjeto PP ON PP.idPreProjeto = Pr.idProjeto
							LEFT JOIN AGENTES.dbo.Nomes N ON N.idAgente = A.idAgente 
							LEFT JOIN SAC.dbo.tbArquivamento Ta ON Ta.idPronac = Pr.idPRONAC and Ta.stEstado = '1'
							LEFT JOIN SAC.dbo.Interessado I ON Pr.CgcCpf = I.CgcCpf
					  		WHERE Pr.idPRONAC = " . $idpronac . " and Ta.stEstado = '1'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql3);

        return $resultado1;
    }

    public function buscarInativos($cpfCnpj)
    {
        $sql4 = "SELECT
                    Pr.IdPRONAC,
                    Pr.NomeProjeto,
                    Pr.AnoProjeto+Pr.Sequencial as pronac,
                    Ar.descricao dsArea,
                    Sg.descricao dsSegmento,
                    Pr.SolicitadoReal,
                    CASE WHEN Pr.Mecanismo IN ('2','6')
                    THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial)
                    ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial)
                    END AS ValorAprovado,
                    SAC.dbo.fnCustoProjeto (Pr.AnoProjeto,Pr.Sequencial) as ValorCaptado
                    FROM SAC.dbo.Projetos Pr
                    INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
                    INNER JOIN SAC.dbo.Area Ar ON  Ar.Codigo = Pr.Area
                    INNER JOIN SAC.dbo.Segmento Sg ON Sg.Codigo = Pr.Segmento
                    WHERE Pr.CgcCpf='$cpfCnpj' and St.StatusProjeto = '0' order by 2 asc ";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql4);

        return $resultado1;
    }

    public function buscarAtivos($cpfCnpj)
    {
        $sql5 = "SELECT
                    Pr.IdPRONAC,
                    Pr.AnoProjeto+Pr.Sequencial as pronac,
                    Pr.NomeProjeto,
                    Ar.descricao dsArea,
                    Sg.descricao dsSegmento,
                    Pr.SolicitadoReal,
                    CASE WHEN Pr.Mecanismo IN ('2','6')
                    THEN SAC.dbo.fnValorAprovadoConvenio(Pr.AnoProjeto,Pr.Sequencial)
                    ELSE SAC.dbo.fnValorAprovado(Pr.AnoProjeto,Pr.Sequencial)
                    END AS ValorAprovado,
                    SAC.dbo.fnCustoProjeto (Pr.AnoProjeto,Pr.Sequencial) as ValorCaptado
                    FROM SAC.dbo.Projetos Pr
                    INNER JOIN SAC.dbo.Situacao St ON St.Codigo = Pr.Situacao
                    INNER JOIN SAC.dbo.Area Ar ON  Ar.Codigo = Pr.Area
                    INNER JOIN SAC.dbo.Segmento Sg ON Sg.Codigo = Pr.Segmento
                    WHERE Pr.CgcCpf='$cpfCnpj' and St.StatusProjeto = '1' order by 2 asc";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql5);

        return $resultado1;
    }

    public function buscarDirigentes($idPronac)
    {
        $sql = "SELECT A.CNPJCPF, N.Descricao
				FROM SAC.dbo.Projetos P
					,SAC.dbo.Interessado I
					,AGENTES.dbo.Agentes A
					,AGENTES.dbo.Nomes N
				
				WHERE P.CgcCpf = I.CgcCpf
					AND A.idAgente = N.idAgente
					AND I.CgcCpf = A.CNPJCPF
					AND P.IdPRONAC = $idPronac";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }
}
