<?php
class Proponente extends MinC_Db_Table_Abstract
{
    protected $_banco = 'SAC';
    protected $_name = 'Projetos';
    protected $_schema = 'SAC';
    protected $_primary = 'idProjeto';

    public function buscarProponenteProjetoDeUsuario($idUsuario)
    {
        $consulta = $this->select();
        $consulta->setIntegrityCheck(false);
        $consulta->from(array('a' => 'vwAgentesSeusProjetos'), array(
            'a.idAgente',
            'a.NomeProponente'
        ), 'SAC.dbo')
        ->where('IdUsuario = ?', $idUsuario)
        ->group(array('a.idAgente', 'a.NomeProponente'))
        ->order(array('a.NomeProponente'))
        ;

//        $consulta->setFetchMode(Zend_DB::FETCH_OBJ);

        $listaResultado = $this->fetchAll($consulta);

        return $listaResultado;
    }

    public function buscarDados($pronac)
    {
        $sql = "SELECT a.idAgente, a.CNPJCPF,n.Descricao AS Proponente,
						   CASE
						     WHEN LEN(a.CNPJCPF) = 11
						       THEN 'F�sica'
						     ELSE 'Jur�dica'
						   END AS TipoPessoa,
						   v1.Descricao + ' - ' + v2.Descricao + ' - ' + e.Logradouro + ' - ' + e.Numero + ' - ' + e.Bairro + ' - ' +
						   e.Complemento Logradouro,
						   u.Municipio,
						   u.UF,
						   e.Cep,
						   CASE
						      WHEN Direito = 1
						           THEN 'Direito P�blico'
						      WHEN Direito = 2 or Direito = 35
						           THEN 'Direito Privado'
						      end as Direito,
						   CASE
						      WHEN Esfera = 3
						           THEN 'Municipal'
						      WHEN Esfera = 4
						           THEN 'Estadual'
						      WHEN Esfera = 5
						           THEN 'Federal'
						      end as Esfera,
						   CASE
						      WHEN Administracao = 11
						           THEN 'Direta'
						      WHEN Administracao = 12
						           THEN 'Indireta'
						      END AS Administracao,
						   CASE
						      WHEN Direito = 2
						           THEN 'Fins Lucrativos'
						      WHEN Direito = 35
						           THEN 'Sem fins lucrativos'
						      END AS Utilidade,
						   SAC.dbo.fnNomeResponsavel(a.Usuario) as Responsavel,
						   CASE
						      WHEN e.Divulgar = 0
						        THEN 'N�o'
						        ELSE 'Sim'
						      END AS DivulgarEndereco,
						   CASE
						      WHEN e.Status = 0
						        THEN 'N�o'
						        ELSE 'Sim'
						      END AS Correspondencia,
						   a.idAgente,e.idEndereco
						   FROM Agentes.dbo.Agentes as a
						   INNER JOIN Agentes.dbo.Visao v on (a.idAgente = v.idAgente and Visao = 144)
						   INNER JOIN Agentes.dbo.Nomes as n on (a.idAgente = n.idAgente and (n.TipoNome =18  or n.TipoNome = 19))
						   INNER JOIN Agentes.dbo.EnderecoNacional as e on (a.idAgente = e.idAgente and e.Status = 1)
						   INNER JOIN Agentes.dbo.Verificacao v1 on (e.TipoEndereco = v1.idVerificacao)
						   INNER JOIN Agentes.dbo.Verificacao v2 on (e.TipoLogradouro = v2.idVerificacao)
						   INNER JOIN Agentes.dbo.vUFMunicipio u on (e.UF = u.idUF and e.Cidade = u.idMunicipio)
						   LEFT JOIN  SAC.dbo.vwNatureza nt on (a.idAgente = nt.idAgente)
						   LEFT JOIN SAC.dbo.Projetos Pr on a.CNPJCPF = Pr.CgcCpf
						  WHERE Pr.IdPronac = " . $pronac . "";




        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public function buscarEmail($pronac)
    {
        $sql1 = "SELECT
CASE
WHEN It.TipoInternet = 28
THEN 'Email Particular'
WHEN It.TipoInternet = 29
THEN 'Email Institucional'
End as TipoInternet,
It.Descricao as Email
FROM AGENTES.dbo.Internet as It
LEFT JOIN AGENTES.dbo.Agentes Ag on Ag.IdAgente = It.IdAgente
LEFT JOIN SAC.dbo.Projetos Pr ON Ag.CNPJCPF = Pr.CgcCpf
where Pr.IdPRONAC = " . $pronac . "";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql1);

        return $resultado1;
    }


    public function buscarTelefone($pronac)
    {
        $sql2 =  "SELECT
CASE
WHEN Tl.TipoTelefone = 22 or Tl.TipoTelefone = 24
THEN 'Residencial'
WHEN Tl.TipoTelefone = 23 or Tl.TipoTelefone = 25
THEN 'Comercial'
WHEN Tl.TipoTelefone = 26
THEN 'Celular'
WHEN Tl.TipoTelefone = 27
THEN 'Fax'
END as TipoTelefone,
Uf.Descricao as UF,
Tl.DDD as DDDTelefone,
Tl.Numero as NumeroTelefone,
CASE
WHEN Tl.Divulgar = 1
THEN 'Sim'
WHEN Tl.Divulgar = 0
THEN 'N&atilde;o'
end as Divulgar
FROM AGENTES.dbo.Telefones Tl
LEFT JOIN AGENTES.dbo.Uf as Uf on Uf.idUF = Tl.UF
LEFT JOIN AGENTES.dbo.Agentes Ag on Ag.IdAgente = Tl.IdAgente
LEFT JOIN SAC.dbo.Projetos Pr On Ag.CNPJCPF = Pr.CgcCpf
where Pr.IdPRONAC = " . $pronac . "";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado2 = $db->fetchAll($sql2);

        return $resultado2;
    }


    public function buscarArquivados($pronac)
    {
        $sql3 = "SELECT
                                                Pr.IdPRONAC,
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
                                                WHERE Pr.idPRONAC = " . $pronac . " and Ta.stEstado = '1'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql3);

        return $resultado1;
    }


    public function buscarInativos($pronac)
    {
        $sql4 = "SELECT
                                                Pr.IdPRONAC,
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
                                                LEFT JOIN SAC.dbo.tbArquivamento Ta ON Ta.idPronac = Pr.idPRONAC
                                                LEFT JOIN SAC.dbo.Interessado I ON Pr.CgcCpf = I.CgcCpf
                                                WHERE Pr.idPRONAC = " . $pronac . " and St.StatusProjeto = '0'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql4);

        return $resultado1;
    }

    public function buscarAtivos($pronac)
    {
        $sql5 = "SELECT
							Pr.IdPRONAC,
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
							LEFT JOIN SAC.dbo.tbArquivamento Ta ON Ta.idPronac = Pr.idPRONAC
							LEFT JOIN SAC.dbo.Interessado I ON Pr.CgcCpf = I.CgcCpf
					  		WHERE Pr.idPRONAC = " . $pronac . " and St.StatusProjeto = '1'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado1 = $db->fetchAll($sql5);

        return $resultado1;
    }

    public function mostrar()
    {
    }
}
