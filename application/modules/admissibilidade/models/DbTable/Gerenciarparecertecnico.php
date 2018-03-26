<?php
/**
 * Class Admissibilidade_Model_DbTable_Gerenciarparecertecnico
 * @author Vinícius Feitosa da Silva <viniciusfesil@mail.com>
 * @since 02/12/2016 15:05
 */
class Admissibilidade_Model_DbTable_Gerenciarparecertecnico extends MinC_Db_Table_Abstract
{
    protected $_banco   = 'SAC';
    protected $_schema  = 'SAC';
    protected $_name    = 'Projetos';


    public static function BuscaProjeto($pronac)
    {
        $sql = "select
					AnoProjeto+Sequencial as pronac,
					Situacao
				from SAC.dbo.Projetos" ;

        /*$sql .= "where AnoProjeto+Sequencial = '$pronac' and
        (Situacao = 'E12' or Situacao = 'C16' or Situacao = 'D11' or Situacao = 'A14' or Situacao = 'D25' or Situacao = 'E23')";*/

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function dadosEtiqueta($pronac)
    {
        $sql = "SELECT
				    p.AnoProjeto + p.Sequencial AS NrProjeto,
					p.NomeProjeto,
					p.UfProjeto,
					a.Descricao AS Area,
					s.Descricao AS Segmento,
					m.Descricao AS Mecanismo,
					si.Descricao AS Situacao,
					CONVERT(varchar(10), p.DtSituacao, 103) AS DtSituacao,
					p.ProvidenciaTomada,
					p.DtProtocolo,
					p.DtAnalise,
					p.Analista,
					p.DtSaida,
					p.UnidadeAnalise,
					p.DtRetorno,
					c.Banco,
					c.Agencia,
					c.ContaBloqueada,
					c.DtLoteRemessaCB,
					c.OcorrenciaCB,
					c.ContaLivre,
				    c.DtLoteRemessaCL,
				    sac.dbo.fnValorSolicitado(p.AnoProjeto, p.Sequencial) AS ValorSolicitado,
				    sac.dbo.fnValorAprovado(p.AnoProjeto, p.Sequencial) AS ValorAprovado,
					sac.dbo.fnCustoProjeto(p.AnoProjeto, p.Sequencial) AS ValorCaptado,
					p.CgcCpf, i.Nome AS Proponente,
				    sac.dbo.fnFormataProcesso(p.IdPRONAC) AS Processo,
				    l.DtLiberacao,
				    l.NumeroDocumento,
				    TABELAS.dbo.fnEstruturaOrgao(p.Orgao, 0) AS Unidade,
				    p.ResumoProjeto
				FROM
				    sac.dbo.Projetos AS p INNER JOIN
				    sac.dbo.Interessado AS i ON p.CgcCpf = i.CgcCpf LEFT OUTER JOIN
				    sac.dbo.ContaBancaria AS c ON p.AnoProjeto = c.AnoProjeto AND p.Sequencial = c.Sequencial LEFT OUTER JOIN
				    sac.dbo.Liberacao AS l ON p.AnoProjeto = l.AnoProjeto AND p.Sequencial = l.Sequencial INNER JOIN
				    sac.dbo.Area AS a ON p.Area = a.Codigo INNER JOIN
				    sac.dbo.Segmento AS s ON p.Segmento = s.Codigo INNER JOIN
				    sac.dbo.Mecanismo AS m ON p.Mecanismo = m.Codigo INNER JOIN
				    sac.dbo.Situacao AS si ON p.Situacao = si.Codigo
				WHERE p.AnoProjeto + p.Sequencial = '$pronac'";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function ParecerTecnico($pronac)
    {
        $sql = "SELECT
				   idPRONAC,
				   AnoProjeto+Sequencial as Pronac,
				   p.NomeProjeto as NomeProjeto,
				   UfProjeto as UfProjeto, a.Descricao as Area,
				   s.Descricao as Segmento,
				   m.Descricao as Mecanismo,
				   p.CgcCPf, Nome as Proponente,
				   sac.dbo.fnFormataProcesso(p.idPronac) as Processo,
				   p.ResumoProjeto as ResumoProjeto,
				   p.DtSituacao,
				   i.Nome as Proponente
				FROM sac.dbo.Projetos p
				     INNER JOIN sac.dbo.Interessado i          on (p.CgcCPf = i.CgcCPf)
				     INNER JOIN sac.dbo.Area a                 on (p.Area = a.Codigo)
				     INNER JOIN sac.dbo.Segmento s             on (p.Segmento = s.Codigo)
				     INNER JOIN sac.dbo.Mecanismo m            on (p.Mecanismo = m.Codigo)
				WHERE p.Anoprojeto+p.Sequencial='$pronac'";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function AnaliseConteudo($pronac)
    {
        $sql = "SELECT
					p.IdPRONAC,
					p.AnoProjeto + p.Sequencial AS Pronac,
					p.NomeProjeto,
					i.Nome AS Proponente,
					pr.Descricao AS Produto,
					a.idProduto,
					CASE
						WHEN Lei8313 = 1 THEN 'Sim' ELSE 'N�o'
					END AS Lei8313,
					CASE
						WHEN Artigo3 = 1 THEN 'Sim' ELSE 'N�o'
					END AS Artigo3,
					CASE
						WHEN IncisoArtigo3 = 1 THEN 'I'
						WHEN IncisoArtigo3 = 2 THEN 'II'
						WHEN IncisoArtigo3 = 3 THEN 'III'
						WHEN IncisoArtigo3 = 4 THEN 'IV'
						WHEN IncisoArtigo3 = 5 THEN 'V'
					END AS IncisoArtigo3,
					a.AlineaArtigo3,
					CASE
						WHEN Artigo18 = 1 THEN 'Sim' ELSE 'N�o'
					END AS Artigo18,
					a.AlineaArtigo18,
					CASE
						WHEN Artigo26 = 1 THEN 'Sim' ELSE 'N�o'
					END AS Artigo26,
					CASE
						WHEN Lei5761 = 1 THEN 'Sim' ELSE 'N�o'
					END AS Lei5761,
					CASE
						WHEN Artigo27 = 1 THEN 'Sim' ELSE 'N�o'
					END AS Artigo27,
					CASE
						WHEN IncisoArtigo27_I = 1 THEN 'X' ELSE ''
					END AS IncisoArtigo27_I,
					CASE
						WHEN IncisoArtigo27_II = 1 THEN 'X' ELSE ''
					END AS IncisoArtigo27_II,
					CASE
						WHEN IncisoArtigo27_III = 1 THEN 'X' ELSE ''
					END AS IncisoArtigo27_III,
					CASE
						WHEN IncisoArtigo27_IV = 1 THEN 'X' ELSE ''
					END AS IncisoArtigo27_IV,
					CASE
						WHEN TipoParecer = 1 THEN 'Aprova��o'
						WHEN TipoParecer = 2 THEN 'Complementa��o'
						WHEN TipoParecer = 4 THEN 'Redu��o'
					END AS TipoParecer,
					CASE
						WHEN ParecerFavoravel = 1 THEN 'Sim' ELSE 'N�o'
					END AS ParecerFavoravel,
					a.ParecerDeConteudo,
					sac.dbo.fnNomeParecerista(a.idUsuario) AS Parecerista
				FROM
					sac.dbo.Projetos AS p INNER JOIN
					sac.dbo.Interessado AS i ON p.CgcCpf = i.CgcCpf INNER JOIN
					sac.dbo.tbAnaliseDeConteudo AS a ON p.IdPRONAC = a.idPronac INNER JOIN
					sac.dbo.Produto AS pr ON a.idProduto = pr.Codigo
				WHERE (a.idUsuario IS NOT NULL) and p.AnoProjeto + p.Sequencial='$pronac'";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function Deslocamento($pronac)
    {
        $sql = "select
					idDeslocamento,
					d.idProjeto,
					d.idPaisOrigem,
					pa.Descricao as PaisOrigem,
					d.idUFOrigem,
					uf.Descricao as UfOrigem,
					d.idMunicipioOrigem,
					m.Descricao as MunicipioOrigem,
					d.idPaisDestino,
					pa.Descricao as PaisDestino,
					d.idUFDestino,
					uf.Descricao as UfDestino,
					d.idMunicipioDestino,
					m.Descricao as MunicipioDestino
					from SAC.dbo.tbDeslocamento as d
					INNER JOIN sac.dbo.Projetos p on (d.idProjeto = p.idProjeto)
					inner join SAC.dbo.Uf as uf on d.idUFOrigem = uf.CodUfIbge
					inner join AGENTES.dbo.Pais as pa on d.idPaisOrigem = pa.idPais
					inner join AGENTES.dbo.Municipios m on d.idMunicipioOrigem = m.idMunicipioIBGE
					WHERE p.AnoProjeto+p.Sequencial = '$pronac' order by d.idDeslocamento";


        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function InformacoesProjeto($pronac)
    {
        $sql = "SELECT
					p.idPronac,
					p.idProjeto,
					p.AnoProjeto+p.Sequencial as Pronac,
					ResumoProjeto,
					Objetivos,
					Justificativa,
					Acessibilidade,
					DemocratizacaoDeAcesso,
					EtapaDeTrabalho,
					FichaTecnica,
					Sinopse,
					EstrategiaDeExecucao,
					ImpactoAmbiental,
					EspecificacaoTecnica,
					NrAtoTombamento,
					DtAtoTombamento,
					EsferaTombamento
				FROM  sac.dbo.PreProjeto pr
				       INNER JOIN sac.dbo.Projetos p on (pr.idPreProjeto = p.idProjeto)
				WHERE p.AnoProjeto+p.Sequencial ='$pronac'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function Divulgacao($pronac)
    {
        $sql = "SELECT v1.Descricao as Peca,v2.Descricao as Veiculo
					FROM sac.dbo.PlanoDeDivulgacao d
					INNEr JOIN sac.dbo.Projetos p on (d.idProjeto = p.idProjeto)
					INNER JOIN sac.dbo.Verificacao v1 on (d.idPeca = v1.idVerificacao)
					INNER JOIN sac.dbo.Verificacao v2 on (d.idVeiculo = v2.idVerificacao)
					WHERE p.AnoProjeto+p.Sequencial='$pronac'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function LocalRealizacao($pronac)
    {
        $sql = "SELECT CASE a.idPais
			            WHEN 0 THEN 'N�o � poss�vel informar o local de realiza��o do projeto'
			            ELSE p.Descricao
			            END as Pais,u.Descricao as UF,m.Descricao as Cidade,x.DtInicioDeExecucao,x.DtFinalDeExecucao
			FROM  sac.dbo.Abrangencia a
			INNER JOIN sac.dbo.PreProjeto x on (a.idProjeto = x.idPreProjeto)
			INNER JOIN sac.dbo.Projetos y on (x.idPreProjeto = y.idProjeto)
			LEFT JOIN agentes.dbo.Pais p on (a.idPais=p.idPais)
			LEFT JOIN agentes.dbo.Uf u on (a.idUF=u.idUF)
			LEFT JOIN agentes.dbo.Municipios m on (a.idMunicipioIBGE=m.idMunicipioIBGE)
			WHERE y.AnoProjeto+y.Sequencial = '$pronac' AND a.stAbrangencia = 1";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function PlanoDistribuicao($pronac)
    {
        $sql = "SELECT
					idPlanoDistribuicao,
					x.idProjeto,
					idProduto,
					pd.Descricao as Produto,
					idPosicaoDaLogo,
					QtdeProduzida,
					QtdeProponente,
					QtdePatrocinador,
				    QtdeOutros,
				    QtdeVendaNormal,
				    QtdeVendaPromocional,
				    PrecoUnitarioNormal,
				    PrecoUnitarioPromocional,
				    QtdeVendaNormal*PrecoUnitarioNormal as ReceitaNormal,
				    QtdeVendaPromocional*PrecoUnitarioPromocional as ReceitaPro,
				    (QtdeVendaNormal*PrecoUnitarioNormal) +(QtdeVendaPromocional*PrecoUnitarioPromocional) as ReceitaPrevista,
				    Usuario
				FROM sac.dbo.PlanoDistribuicaoProduto x
				INNER JOIN sac.dbo.Projetos y on (x.idProjeto = y.idProjeto)
				INNER JOIN SAC.dbo.Produto pd on x.idProduto = pd.Codigo

				WHERE y.AnoProjeto+y.Sequencial='$pronac' AND x.stPlanoDistribuicaoProduto = 1";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function FonteRecurso($pronac)
    {
        $sql = "SELECT distinct
					x.idVerificacao,
					x.idTipo,
				    x.Descricao AS FonteRecurso
				FROM sac.dbo.Projetos AS a INNER JOIN
				     sac.dbo.tbPlanilhaProposta AS b ON a.idProjeto = b.idProjeto LEFT OUTER JOIN
				     sac.dbo.Verificacao AS x ON b.FonteRecurso = x.idVerificacao INNER JOIN
				     AGENTES.dbo.vUFMunicipio AS f ON b.UfDespesa = f.idUF AND b.MunicipioDespesa = f.idMunicipio
				WHERE AnoProjeto+Sequencial = '$pronac'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function Produto($pronac)
    {
        $sql = "SELECT  distinct
					x.idTipo,
					b.idProduto,
					CASE
						WHEN idProduto = 0 THEN 'Administra��o do Projeto'
						ELSE c.Descricao END AS Produto
				FROM sac.dbo.Projetos AS a
				     INNER JOIN sac.dbo.tbPlanilhaProposta AS b ON a.idProjeto = b.idProjeto
				     LEFT OUTER JOIN sac.dbo.Produto AS c ON b.idProduto = c.Codigo
				     INNER JOIN sac.dbo.Verificacao AS x ON b.FonteRecurso = x.idVerificacao
				WHERE a.AnoProjeto+a.Sequencial = '$pronac' ORDER BY b.idProduto";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function Etapa($pronac)
    {
        $sql = "SELECT distinct
					b.idProduto,
					b.idEtapa,
					CONVERT(varchar(8), d.idPlanilhaEtapa)+ ' - ' + d.Descricao AS Etapa
				FROM sac.dbo.Projetos AS a
				     INNER JOIN sac.dbo.tbPlanilhaProposta AS b ON a.idProjeto = b.idProjeto
				     LEFT OUTER JOIN sac.dbo.tbPlanilhaEtapa AS d ON b.idEtapa = d.idPlanilhaEtapa
				WHERE a.AnoProjeto+a.Sequencial = '$pronac' ORDER BY idProduto";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    /**
     * @name Uf
     * @param $pronac
     * @return array
     *
     * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
     * @since  ${DATE}
     */
    public static function Uf($pronac)
    {
        $sql = "SELECT distinct
					b.idProduto,
					b.idEtapa,
					f.idUF,
				    f.UF,
				    f.Municipio
				FROM sac.dbo.Projetos AS a INNER JOIN
				     sac.dbo.tbPlanilhaProposta AS b ON a.idProjeto = b.idProjeto LEFT OUTER JOIN
				     AGENTES.dbo.vUFMunicipio AS f ON b.UfDespesa = f.idUF AND b.MunicipioDespesa = f.idMunicipio
				WHERE a.AnoProjeto+a.Sequencial = '$pronac' ORDER BY idProduto";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }


    public static function Item($pronac)
    {
        $sql = "SELECT distinct
					b.idProduto,
					b.idEtapa,
    				b.UfDespesa,
					i.idPlanilhaItens,
					i.Descricao AS Item
				FROM sac.dbo.Projetos AS a
				     INNER JOIN sac.dbo.tbPlanilhaProposta AS b ON a.idProjeto = b.idProjeto
				     INNER JOIN sac.dbo.tbPlanilhaItens AS i ON b.idPlanilhaItem = i.idPlanilhaItens
				WHERE a.AnoProjeto+a.Sequencial = '$pronac' ORDER BY idProduto";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function Unidade($pronac)
    {
        $sql = "SELECT distinct
    				b.idProduto,
    				b.idEtapa,
    				b.UfDespesa,
    				b.idPlanilhaItem,
    				e.idUnidade,
					e.Descricao AS Unidade,
					b.Quantidade,
					b.Ocorrencia,
					b.ValorUnitario,
				    b.Quantidade * b.Ocorrencia * b.ValorUnitario AS VlTotal,
				    b.QtdeDias
				FROM sac.dbo.Projetos AS a
				     INNER JOIN sac.dbo.tbPlanilhaProposta AS b ON a.idProjeto = b.idProjeto
				     INNER JOIN sac.dbo.tbPlanilhaUnidade AS e ON b.Unidade = e.idUnidade
				     INNER JOIN AGENTES.dbo.vUFMunicipio AS f ON b.UfDespesa = f.idUF AND b.MunicipioDespesa = f.idMunicipio
				WHERE a.AnoProjeto+a.Sequencial = '$pronac' ORDER BY idProduto";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }


    public function inserirparecer($dados)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->insert("SAC.dbo.Parecer", $dados);
    }


    public function listar_parecer($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('tbr' => $this->_name));

        $slct->joinInner(
                array('v2' => 'Parecer'),
                'v2.idPronac = tbr.idPronac',
                array('v2.TipoParecer','v2.Atendimento','v2.idParecer','v2.ParecerFavoravel','v2.DtParecer','v2.ResumoParecer','v2.SugeridoReal','v2.SugeridoCusteioReal','v2.SugeridoCapitalReal','v2.idEnquadramento','v2.Parecerista','v2.SugeridoUfir','v2.idPronac','v2.NumeroReuniao')
        );

        $slct->joinInner(
                array('v3' => 'Area'),
                'v3.Codigo = tbr.Area',
                array('v3.Descricao as AreaDescricao')
        );

        $slct->joinInner(
                array('v4' => 'Segmento'),
                'v4.Codigo = tbr.Segmento',
                array('v3.Descricao as SegmentoDescricao')
        );

        $slct->joinInner(
                array('v5' => 'Mecanismo'),
                'v5.Codigo = tbr.Mecanismo',
                array('v5.descricao as MecanismoDescricao')
           );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }


        //adicionando linha order ao select
        $slct->order($order);
        //$this->_totalRegistros = $this->pegaTotal($where);
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


    public function VerificaPronac($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(array('tbr' => $this->_name));



        // adicionando clausulas where
        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }



        $rows = $this->fetchAll($slct);
        return $rows->count();
    }

    public function VerificaParecer($where=array(), $order=array(), $tamanho=-1, $inicio=-1)
    {
        // criando objeto do tipo select
        $slct = $this->select();

        $slct->setIntegrityCheck(false);

        $slct->from(array('v2' => 'Parecer'));


        // adicionando clausulas where
        foreach ($where as $coluna=>$valor) {
            $slct->where($coluna, $valor);
        }



        $rows = $this->fetchAll($slct);
        return $rows->count();
    }
}
