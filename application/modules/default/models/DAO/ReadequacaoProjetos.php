<?php 
class ReadequacaoProjetos extends Zend_Db_Table
{
    public function buscarProjetos($idPronac)
    {
        $sql0 = " select  projetos.idProjeto,

                    projetos.IdPRONAC,
                    projetos.CgcCpf,
                    projetos.AnoProjeto+projetos.Sequencial as pronac,
                    projetos.NomeProjeto,
                    nomes.Descricao,
                    agentes.idAgente,
                    areaCultura.Codigo as 'codigoArea',
                    areaCultura.Descricao as 'areaCultura',
                    segmentoCultura.Codigo as 'codigoDescricao',
                    segmentoCultura.Descricao as 'segmentoCultura' from
                    sac.dbo.Projetos as projetos
                    inner join SAC.dbo.Area as areaCultura
                    on projetos.Area = areaCultura.Codigo
                    left join SAC.dbo.Segmento as segmentoCultura
                    on projetos.Segmento = segmentoCultura.Codigo
                    inner join AGENTES.dbo.Agentes as agentes
                    on projetos.CgcCpf = agentes.CNPJCPF
                    inner join AGENTES.dbo.Nomes as nomes
                    on agentes.idAgente = nomes.idAgente
                    where
                    projetos.IdPRONAC = $idPronac";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarLocais($idProjeto)
    {
        $sql0 = "   select pais.idPais, uf.idUF, mp.idMunicipioIBGE as idMunicipioIBGE ,idAbrangencia,idProjeto,pais.Descricao,uf.Sigla as sigla,mp.Descricao as cidade from SAC.dbo.Abrangencia as ab
                    left join AGENTES.dbo.Municipios as mp
                    on ab.idMunicipioIBGE = mp.idMunicipioIBGE
                    left join AGENTES.dbo.UF as uf
                    on ab.idUF = uf.idUF
                    inner join Agentes.dbo.Pais as pais
                    on pais.idPais = ab.idPais
                    where ab.idProjeto = $idProjeto AND ab.stAbrangencia = 1 
                    ORDER BY pais.Descricao, uf.Sigla, mp.Descricao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarLocaisExterior($idPedidoAlteracao)
    {
        $sql0 = " select pais.idPais, idAbrangencia,idAbrangenciaAntiga,pais.Descricao,'-' as sigla,'-'as cidade from SAC.dbo.tbAbrangencia as ab
                    inner join Agentes.dbo.Pais as pais
                    on pais.idPais = ab.idPais
                    where ab.idPedidoAlteracao = $idPedidoAlteracao
                    and ab.tpAcao!='E'and pais.Descricao!= 'Brasil'";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarLocais2($idPedidoAlteracao)
    {
        $sql0 = " select pais.idPais, uf.idUF, mp.idMunicipioIBGE as idMunicipioIBGE ,idAbrangencia,idAbrangenciaAntiga,pais.Descricao,uf.Sigla as sigla,mp.Descricao as cidade, ab.tpAcao from SAC.dbo.tbAbrangencia as ab
                    left join AGENTES.dbo.Municipios as mp
                    on ab.idMunicipioIBGE = mp.idMunicipioIBGE
                    left join AGENTES.dbo.UF as uf
                    on ab.idUF = uf.idUF
                    inner join Agentes.dbo.Pais as pais
                    on pais.idPais = ab.idPais
                    where ab.idPedidoAlteracao = $idPedidoAlteracao
                    and ab.tpAcao!='E'
                    ORDER BY pais.Descricao, uf.Sigla, mp.Descricao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function updateLocais($idPais, $idUF, $idMunicipioIBGE, $tpAcao, $idPedidoAlteracao, $idAbrangencia)
    {
        $sql0 = "  update SAC.dbo.tbAbrangencia set idPais = $idPais, idUF= $idUF,idMunicipioIBGE = $idMunicipioIBGE,tpAcao = '$tpAcao',dtRegistro = GETDATE()
                    where idPedidoAlteracao = $idPedidoAlteracao and idAbrangencia= $idAbrangencia";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function excluirLocais($idAbrangencia, $dsJustificativaExclusao)
    {
        $sql0 = "  update SAC.dbo.tbAbrangencia set tpAcao = 'E', dtRegistro = GETDATE(), dsExclusao='".$dsJustificativaExclusao."'
                    where idAbrangencia= $idAbrangencia";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function insertLocais($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao, $tpAcao = 'I')
    {
        $sql0 = " insert into SAC.dbo.tbAbrangencia (idPais,idUF,idMunicipioIBGE,tpAbrangencia,tpAcao,idPedidoAlteracao,dtRegistro)
                    values ($idPais,$idUF,$idMunicipioIBGE,'SA','$tpAcao',$idPedidoAlteracao,GETDATE())";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarLocaisCadastrados($idPais, $idUF, $idMunicipioIBGE, $idPedidoAlteracao)
    {
        $sql0 = " select * from SAC.dbo.tbAbrangencia where idPais = $idPais and idUF = $idUF and  idMunicipioIBGE = $idMunicipioIBGE and idPedidoAlteracao = $idPedidoAlteracao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }
    public function buscarLocaisCadastradosFinal($idPedidoAlteracao)
    {
        $sql0 = " select * from BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao where tpAlteracaoProjeto  = 4 and idPedidoAlteracao = $idPedidoAlteracao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscaridPedidoAlteracao($idPedidoAlteracao)
    {
        $sql0 = "  select * from BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
                    where idPedidoAlteracao =  $idPedidoAlteracao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarTipoAlteracaoInserido($idPedidoAlteracao)
    {
        $sql0 = " select * from   BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao WHERE tpAlteracaoProjeto = 7 AND idPedidoAlteracao = $idPedidoAlteracao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarProdutobd($idPedidoAlteracao, $idProduto)
    {
        $sql0 = "SELECT *, plano.stPrincipal, CAST(plano.dsjustificativa AS TEXT) AS JustificativaProponente
					FROM SAC.dbo.tbPlanoDistribuicao AS plano 
						INNER JOIN SAC.dbo.Segmento AS segmento ON plano.cdSegmento = segmento.Codigo
						LEFT JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao AS x ON plano.idPedidoAlteracao = x.idPedidoAlteracao
					WHERE plano.idPedidoAlteracao = $idPedidoAlteracao and plano.idProduto = $idProduto ";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarID($idPronac)
    {
        $sql0 = " select p.idProjeto from SAC.dbo.Projetos as p where p.IdPRONAC = $idPronac";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarProdutosAtual($idProjeto)
    {
        $sql0 = "   select  produto.Descricao,plano.idProduto, plano.stPrincipal from SAC.dbo.PlanoDistribuicaoProduto as plano
                    inner join SAC.dbo.Produto as produto
                    on plano.idProduto = produto.Codigo
                    inner join SAC.dbo.Segmento as segmento
                    on plano.Segmento = segmento.Codigo
                    where idProjeto = $idProjeto AND plano.stPlanoDistribuicaoProduto = 1 
                    ORDER BY plano.stPrincipal DESC, produto.Descricao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarProdutosOpcao($idProjeto, $idProduto)
    {
        $sql0 = "SELECT *, plano.stPrincipal, CAST(pla.dsjustificativa AS TEXT) AS JustificativaProponente FROM SAC.dbo.PlanoDistribuicaoProduto AS plano 
					INNER JOIN SAC.dbo.Produto AS produto ON plano.idProduto = produto.Codigo 
					INNER JOIN SAC.dbo.Segmento AS segmento ON plano.Segmento = segmento.Codigo 
					LEFT JOIN SAC.dbo.tbPlanoDistribuicao AS pla ON plano.idPlanoDistribuicao = pla.idPlanoDistribuicao 
					LEFT JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao AS x ON pla.idPedidoAlteracao = x.idPedidoAlteracao
				WHERE plano.idProjeto = $idProjeto and plano.idProduto = $idProduto AND plano.stPlanoDistribuicaoProduto = 1";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarProdutosAtiva($idProjeto, $idProduto)
    {
        $sql0 = "   select  produto.Descricao,plano.idProduto as idProduto, plano.stPrincipal from SAC.dbo.PlanoDistribuicaoProduto as plano
                    inner join SAC.dbo.Produto as produto
                    on plano.idProduto = produto.Codigo
                    where idProjeto = $idProjeto and idProduto = $idProduto AND plano.stPlanoDistribuicaoProduto = 1";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarProdutostabelaAtiva($idProjeto)
    {
        $sql0 = "   select  produto.Descricao,plano.idProduto as idProduto, plano.stPrincipal from SAC.dbo.PlanoDistribuicaoProduto as plano
                    inner join SAC.dbo.Produto as produto
                    on plano.idProduto = produto.Codigo
                    where idProjeto = $idProjeto  AND plano.stPlanoDistribuicaoProduto = 1";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function buscarSolicitacao($idPronac)
    {
        $sql1 = "select MAX(idPedidoAlteracao)as idPedidoAlteracao   from BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto where IdPRONAC = $idPronac";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql1);
    }

    public function buscarprodutoSolicitado($idPedidoAlteracao)
    {
        $sql1 = "select plano.idProduto,
                produto.Descricao as Descricao,
                plano.stPrincipal 
                from SAC.dbo.tbPlanoDistribuicao as plano
                inner join SAC.dbo.Produto as produto
                on plano.idProduto = produto.Codigo
                where idPedidoAlteracao = $idPedidoAlteracao and plano.tpAcao!='E' 
                order by plano.stPrincipal DESC, produto.Descricao";
//        die($sql1);
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql1);
    }

    public static function inserirProdutoPlano($idProjeto, $idPedidoAlteracao, $idProduto)
    {
        $sql = "insert into SAC.dbo.tbPlanoDistribuicao
                (idPlanoDistribuicao, cdArea, cdSegmento, idPedidoAlteracao,idProduto,idPosicaoLogo,qtPatrocinador,qtProduzida,qtOutros,qtVendaNormal,qtVendaPromocional,vlUnitarioNormal,vlUnitarioPromocional,stPrincipal,tpAcao,tpPlanoDistribuicao,dtPlanoDistribuicao)
                select
                plano.idPlanoDistribuicao, plano.Area, plano.Segmento, pedido.idPedidoAlteracao,plano.idProduto,plano.idPosicaoDaLogo,plano.QtdePatrocinador,plano.QtdeProduzida,plano.QtdeOutros,plano.QtdeVendaNormal,plano.QtdeVendaPromocional,plano.PrecoUnitarioNormal,plano.PrecoUnitarioPromocional,stPrincipal,'N','S',GETDATE()   from SAC.dbo.PlanoDistribuicaoProduto as plano,
                BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto as pedido
                inner join SAC.dbo.Projetos as projetos
                on projetos.IdPRONAC = pedido.IdPRONAC
                where plano.idProjeto = $idProjeto and pedido.idPedidoAlteracao = $idPedidoAlteracao and plano.idProduto = $idProduto AND plano.stPlanoDistribuicaoProduto = 1";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function inserirSolicitacao($idPronac, $idSolicitante, $stPedido, $siVerificacao = 0)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto (IdPRONAC, idSolicitante,dtSolicitacao,stPedidoAlteracao, siVerificacao)
                VALUES ('$idPronac','$idSolicitante',GETDATE(),'$stPedido', '$siVerificacao')";


        
        $resultado = $db->query($sql);
        return $resultado;
    }

    public function buscarProdutos($idPronac)
    {
        $sql1 = " select
                    projetos.IdPRONAC,
                    projetos.AnoProjeto,
                    projetos.Sequencial,
                    projetos.NomeProjeto,
                    produto.Codigo,
                    produto.Descricao
                    from SAC.dbo.Projetos as projetos
                    inner join SAC.dbo.ProjetoProduto as  ProjetoProduto
                    on projetos.AnoProjeto = ProjetoProduto.AnoProjeto
                    and projetos.Sequencial = ProjetoProduto.Sequencial
                    inner join SAC.dbo.Produto as produto
                    on ProjetoProduto.CodigoProduto = produto.Codigo
                    where projetos.IdPRONAC = $idPronac";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql1);
    }

    public function buscarDescricao()
    {
        $sql1 = "Select
                produto.Codigo,
                produto.Descricao 
                from SAC.dbo.Produto as produto 
                ORDER BY produto.Descricao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql1);
    }

    public function buscarprodutoAcao($idProduto, $idPedidoAlteracao)
    {
        $sql1 = "select * from SAC.dbo.tbPlanoDistribuicao where idPedidoAlteracao = $idPedidoAlteracao and idProduto = $idProduto";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql1);
    }

    public function buscarprodutoPlano($idProjeto, $idProduto)
    {
        $sql1 = "SELECT p.idPlanoDistribuicao
					,p.idProjeto
					,p.idProduto
					,p.Area
					,p.Segmento
					,p.idPosicaoDaLogo
					,p.QtdeProduzida
					,p.QtdePatrocinador
					,p.QtdeProponente
					,p.QtdeOutros
					,p.QtdeVendaNormal
					,p.QtdeVendaNormal
					,p.QtdeVendaPromocional
					,p.PrecoUnitarioNormal
					,p.PrecoUnitarioPromocional
					,p.stPrincipal
					,p.Usuario
					,p.dsJustificativaPosicaoLogo
					,a.Descricao AS dsArea
					,s.Descricao AS dsSegmento
					,s.Descricao AS Descricao
					,pla.dsjustificativa AS Justificativa
					,CAST(pla.dsjustificativa AS TEXT) AS JustificativaProponente
				 FROM SAC.dbo.PlanoDistribuicaoProduto AS p
					LEFT JOIN SAC.dbo.Area AS a ON a.Codigo = p.Area
					LEFT JOIN SAC.dbo.Segmento AS s ON s.Codigo = p.Segmento 
					LEFT JOIN SAC.dbo.tbPlanoDistribuicao AS pla ON p.idPlanoDistribuicao = pla.idPlanoDistribuicao
				 WHERE p.idProjeto = $idProjeto AND p.idProduto = $idProduto AND p.stPlanoDistribuicaoProduto = 1";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql1);
    }

    public function buscarPosicao()
    {
        $sql1 = "select Verificacao.idVerificacao as idVerificacao, ltrim(Verificacao.Descricao)as Descricao from SAC.dbo.Verificacao as Verificacao
                inner join SAC.dbo.Tipo as Tipo
                on Verificacao.idTipo = Tipo.idTipo
                where Tipo.idTipo = 3 
                ORDER BY Verificacao.Descricao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql1);
    }

    public function acaoProduto($idPronac, $idProduto)
    {
        $sql1 = "select
                projetos.IdPRONAC,
                projetos.AnoProjeto,
                projetos.Sequencial,
                projetos.NomeProjeto,
                produto.Codigo,
                produto.Descricao

                from SAC.dbo.Projetos as projetos
                inner join SAC.dbo.ProjetoProduto as  ProjetoProduto
                on projetos.AnoProjeto = ProjetoProduto.AnoProjeto
                and projetos.Sequencial = ProjetoProduto.Sequencial
                inner join SAC.dbo.Produto as produto
                on ProjetoProduto.CodigoProduto = produto.Codigo
                where projetos.IdPRONAC = $idPronac
                and produto.Codigo = $idProduto";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql1);
    }

    public function buscarProdutosPlano($idProjeto)
    {
        $sql0 = "select idProduto from SAC.dbo.PlanoDistribuicaoProduto  where idProjeto = $idProjeto AND stPlanoDistribuicaoProduto = 1";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function compararProdutos($idPedidoAlteracao, $idProjeto, $idProduto)
    {
        $sql0 = "select * from SAC.dbo.PlanoDistribuicaoProduto as plano
                inner join SAC.dbo.tbPlanoDistribuicao as plano2
                on plano.idProduto = plano2.idProduto
                where idPedidoAlteracao = $idPedidoAlteracao and idProjeto = $idProjeto and plano.idProduto = $idProduto AND plano.stPlanoDistribuicaoProduto = 1";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql0);
    }

    public function solicitarAlteracao($idPronac)
    {
        $sql0 = "";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql0);
    }

    public function inserirProduto($idPedidoAlteracao, $idProdutoNovo, $areaCultural, $segmentoCultural, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural, $dsJustificativa = null)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        $sql = "insert into sac.dbo.tbPlanoDistribuicao
                (idPedidoAlteracao,idProduto,cdArea,cdSegmento,idPosicaoLogo,qtProduzida,qtPatrocinador,qtOutros,qtVendaNormal,qtVendaPromocional,vlUnitarioNormal,vlUnitarioPromocional,stPrincipal,tpAcao,tpPlanoDistribuicao,dtPlanoDistribuicao, dsjustificativa)
                values
                ('$idPedidoAlteracao','$idProdutoNovo','$areaCultural','$segmentoCultural','$idPosicaoLogo','$qtProduzida','$qtPatrocinador','$qtOutros','$qtVendaNormal','$qtVendaPromocional','$vlUnitarioNormal','$vlUnitarioPromocional',0,'I','S','" . date('Y-m-d H:i:s') . "', '".$dsJustificativa."')";
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function inserirPedidoTipo($idPedidoAlteracao, $justificativa)
    {
        $sql = "INSERT INTO
                BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao(idPedidoAlteracao, tpAlteracaoProjeto, dsJustificativa)
                VALUES     ($idPedidoAlteracao,7,'$justificativa')";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function atualizaPedidoTipoAlteracao($idPedidoAlteracao, $justificativa)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
                SET     idPedidoAlteracao = $idPedidoAlteracao, tpAlteracaoProjeto = 7 , dsJustificativa = '$justificativa' WHERE idPedidoAlteracao = $idPedidoAlteracao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificaPedidoTipoAlteracao($idPedidoAlteracao)
    {
        $sql = "select TOP 1 idPedidoAlteracao
                from BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao WHERE idPedidoAlteracao = $idPedidoAlteracao order by idPedidoAlteracao desc";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function verificarBotao($idPedidoAlteracao)
    {
        $sql = "select * from BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto where idPedidoAlteracao=$idPedidoAlteracao and stPedidoAlteracao = 'A'";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function verificarMenu($idPronac)
    {
        $sql = "select stPedidoAlteracao from BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto where idPronac = $idPronac";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }

    public static function verificarProposta($idPedidoAlteracao)
    {
        $sql = "select * from SAC.dbo.tbProposta where idPedidoAlteracao = $idPedidoAlteracao";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function inserirProposta($dsEspecificacaotecnica, $idPedidoAlteracao)
    {
        $sql = "insert into SAC.dbo.tbProposta (tpProposta,dtProposta,dsEspecificacaoTecnica,idPedidoAlteracao)
                values ('SA',GETDATE(),'$dsEspecificacaotecnica',$idPedidoAlteracao);";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function alterarPedido($idPedidoAlteracao, $status)
    {
        $sql = "update BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto
                set dtSolicitacao = GETDATE(),stPedidoAlteracao= '$status'
                where idPedidoAlteracao = $idPedidoAlteracao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function inserirJustificativa($idPedidoAlteracao, $dsJustificativa, $status)
    {
        $sql = "insert into BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao(idPedidoAlteracao,tpAlteracaoProjeto,dsJustificativa)
                values ($idPedidoAlteracao,$status,'$dsJustificativa')";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public static function alterarJustificativa($idPedidoAlteracao, $dsJustificativa)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao SET dsJustificativa = '".$dsJustificativa."' WHERE idPedidoAlteracao = '".$idPedidoAlteracao."';";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function alterarProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional, $areaCultural, $segmentoCultural, $dsJustificativa = null)
    {
        $sql = "update SAC.dbo.tbPlanoDistribuicao set idPedidoAlteracao = $idPedidoAlteracao,idPosicaoLogo = $idPosicaoLogo, qtProduzida=$qtProduzida, qtPatrocinador=$qtPatrocinador, qtOutros=$qtOutros, qtVendaNormal=$qtVendaNormal, qtVendaPromocional = $qtVendaPromocional  , vlUnitarioNormal=$vlUnitarioNormal, vlUnitarioPromocional=$vlUnitarioPromocional
                ,cdArea = $areaCultural,cdSegmento = $segmentoCultural,tpAcao = 'A', dsjustificativa = '".$dsJustificativa."' 
                where idPedidoAlteracao = $idPedidoAlteracao and idProduto = $idProdutoNovo";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function updateProduto($idPedidoAlteracao, $idProdutoNovo, $idPosicaoLogo, $qtProduzida, $qtPatrocinador, $qtOutros, $qtVendaNormal, $qtVendaPromocional, $vlUnitarioNormal, $vlUnitarioPromocional)
    {
        $sql = "update SAC.dbo.tbPlanoDistribuicao set idPedidoAlteracao = $idPedidoAlteracao,idPosicaoLogo = $idPosicaoLogo, qtProduzida=$qtProduzida, qtPatrocinador=$qtPatrocinador, qtOutros=$qtOutros, qtVendaNormal=$qtVendaNormal, qtVendaPromocional = $qtVendaPromocional  , vlUnitarioNormal=$vlUnitarioNormal, vlUnitarioPromocional=$vlUnitarioPromocional,tpAcao = 'A'
                where idPedidoAlteracao = $idPedidoAlteracao and idProduto = $idProdutoNovo";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function alterarSolicitacao($idPedidoAlteracao, $stPedido)
    {
        $sql = "update BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto set stPedidoAlteracao = '$stPedido'
                where idPedidoAlteracao = $idPedidoAlteracao";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function excluirProduto($idPedidoAlteracao, $idProduto, $dsJustificativa = null)
    {
        $sql = "update SAC.dbo.tbPlanoDistribuicao  set tpAcao = 'E',dtPlanoDistribuicao = GETDATE(), dsjustificativa = '".$dsJustificativa."' 
                where idPedidoAlteracao = $idPedidoAlteracao and idProduto = $idProduto ";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function BuscarPrazo($idPedidoAlteracao, $tpProrrogacao)
    {
        $sql = "SELECT idPedidoAlteracao
					,tpProrrogacao
					,CONVERT(CHAR(10), dtInicioNovoPrazo, 103) AS dtInicioNovoPrazo
					,CONVERT(CHAR(10), dtFimNovoPrazo, 103) AS dtFimNovoPrazo
				FROM BDCORPORATIVO.scSAC.tbProrrogacaoPrazo 
				WHERE idPedidoAlteracao = $idPedidoAlteracao AND tpProrrogacao = '$tpProrrogacao'";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }


    public function BuscarPrazoProjetos($idPronac)
    {
        $sql = "select CONVERT(CHAR(10), DtInicioExecucao,103) AS DtInicioExecucao
                    ,CONVERT(CHAR(10), DtFimExecucao,103) AS DtFimExecucao from Sac.dbo.Projetos WHERE IdPRONAC = $idPronac";

        

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }



    public function BuscarPrazoProjetosCaptacao($pronac)
    {
        $sql = "SELECT TOP 1 CONVERT(CHAR(10), DtInicioCaptacao,103) AS DtInicioCaptacao
                    ,CONVERT(CHAR(10), DtFimCaptacao,103) AS DtFimCaptacao
            FROM SAC.dbo.Aprovacao
            WHERE AnoProjeto+Sequencial = $pronac
            AND TipoAprovacao in (1,3)
            ORDER BY idAprovacao DESC";

        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }



    public function insertPrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao)
    {
        $sql = "insert into BDCORPORATIVO.scSAC.tbProrrogacaoPrazo (idPedidoAlteracao,dtInicioNovoPrazo,dtFimNovoPrazo,tpProrrogacao)
                values ($idPedidoAlteracao,'$dtInicioNovoPrazo','$dtFimNovoPrazo','$tpProrrogacao')";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function updatePrazo($idPedidoAlteracao, $dtInicioNovoPrazo, $dtFimNovoPrazo, $tpProrrogacao)
    {
        $sql = "update BDCORPORATIVO.scSAC.tbProrrogacaoPrazo set dtInicioNovoPrazo = '$dtInicioNovoPrazo',dtFimNovoPrazo ='$dtFimNovoPrazo'
                where idPedidoAlteracao = $idPedidoAlteracao and tpProrrogacao = '$tpProrrogacao'";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    /*     * ************************************************************************************************************************
     * Fun��o que retorna os dados da pesquisa de acordo com o perfil
     * *********************************************************************************************************************** */

    //SQL DO COORDENADOR DE ACOMPANHAMENTO
    public static function retornaSQL($sqlDesejado, $tpAlteracao, $unidade_autorizada = null)
    {
        $sql = '';

        if ($sqlDesejado == "sqlCoordAcomp") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        a.dtSolicitacao AS DataEnvio,
                        b.Orgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem

                        FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto               AS a
                        INNER JOIN SAC.dbo.Projetos                                     AS b ON a.IdPRONAC = b.IdPRONAC
                        INNER JOIN SAC.dbo.Area                                         AS c ON b.Area = c.Codigo
                        LEFT JOIN SAC.dbo.Segmento					AS d ON b.Segmento = d.Codigo
                        INNER JOIN SAC.dbo.Abrangencia					AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                        INNER JOIN AGENTES.dbo.Municipios				AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao	AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                        INNER JOIN AGENTES.dbo.UF					AS h ON e.idUF = h.idUF

                        WHERE a.stPedidoAlteracao = 'I'
                        AND a.siVerificacao in (0,1)
                        AND (g.stVerificacao = 0 or g.stVerificacao is null)
                        AND g.tpAlteracaoProjeto = $tpAlteracao ";

            if ($unidade_autorizada == 166) :
                $sql.= " AND b.Area = 2 ";  // quando for SAV/CGAV/CAP pega somente os projetos da �rea de Audiovisual
            elseif ($unidade_autorizada == 272) :
                $sql.= " AND b.Area <> 2 "; // quando for SEFIC/GEAR/SACAV pega somente os projetos das �reas que n�o sejam de Audiovisual
            else :
                $sql.= " AND b.Area = 0 ";  // quando for diferente de SAV/CGAV/CAP e SAV/CGAV/CAP pega somente os projetos da �rea de Audiovisual
            endif;

            $sql.= " ORDER BY a.dtSolicitacao";
        } elseif ($sqlDesejado == "sqlCoordAcompProdutos") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        a.dtSolicitacao AS DataEnvio,
                        b.Orgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        CASE
						WHEN (g.tpAlteracaoProjeto) = 7
							THEN 'Produtos'
							ELSE 'Item de Custo'
						END AS Situacao

                        FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto               AS a
                        INNER JOIN SAC.dbo.Projetos                                     AS b ON a.IdPRONAC = b.IdPRONAC
                        INNER JOIN SAC.dbo.Area                                         AS c ON b.Area = c.Codigo
                        LEFT JOIN SAC.dbo.Segmento					AS d ON b.Segmento = d.Codigo
                        INNER JOIN SAC.dbo.Abrangencia					AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                        INNER JOIN AGENTES.dbo.Municipios				AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao	AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                        INNER JOIN AGENTES.dbo.UF					AS h ON e.idUF = h.idUF
                        ,(SELECT MAX(tpAlteracaoProjeto) AS tpAlteracaoProjeto, idPedidoAlteracao
							  FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
							  WHERE tpAlteracaoProjeto IN (7, 10)
							  GROUP BY idPedidoAlteracao) AS tmp
                        WHERE a.stPedidoAlteracao = 'I'
                        AND a.siVerificacao in (0,1)
                        AND (g.stVerificacao = 0 or g.stVerificacao is null)
                        AND g.tpAlteracaoProjeto = $tpAlteracao
                        AND tmp.idPedidoAlteracao = g.idPedidoAlteracao
                        AND tmp.tpAlteracaoProjeto = g.tpAlteracaoProjeto ";

            if ($unidade_autorizada == 166) :
                $sql.= " AND b.Area = 2 ";  // quando for SAV/CGAV/CAP pega somente os projetos da �rea de Audiovisual
            elseif ($unidade_autorizada == 272) :
                $sql.= " AND b.Area <> 2 "; // quando for SEFIC/GEAR/SACAV pega somente os projetos das �reas que n�o sejam de Audiovisual
            else :
                $sql.= " AND b.Area = 0 ";  // quando for diferente de SAV/CGAV/CAP e SAV/CGAV/CAP pega somente os projetos da �rea de Audiovisual
            endif;

            $sql.= " ORDER BY a.dtSolicitacao ";
        } elseif ($sqlDesejado == "sqlUFs") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        h.Sigla

                        FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto		AS a
                        INNER JOIN SAC.dbo.Projetos					AS b ON a.IdPRONAC = b.IdPRONAC
                        INNER JOIN SAC.dbo.Area						AS c ON b.Area = c.Codigo
                        LEFT JOIN SAC.dbo.Segmento					AS d ON b.Segmento = d.Codigo
                        INNER JOIN SAC.dbo.Abrangencia					AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                        INNER JOIN AGENTES.dbo.Municipios				AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao	AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                        INNER JOIN AGENTES.dbo.UF					AS h ON e.idUF = h.idUF

                        WHERE a.stPedidoAlteracao = 'I' ";
        } elseif ($sqlDesejado == "sqlCoordAcompDev") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        i.dtInicioAvaliacao AS DataEnvio,
                        i.dtFimAvaliacao AS DataFim,
                        j.idOrgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        j.stVerificacao AS stAcao

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                   AS a
                    INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                    INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                    INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                    INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao and g.tpAlteracaoProjeto = i.tpAlteracaoProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao

                    WHERE a.stPedidoAlteracao = 'I'
                    AND a.siVerificacao in (0,1)
                    AND g.stVerificacao in (2,3)
                    AND g.tpAlteracaoProjeto = $tpAlteracao
                    AND j.stAtivo = 0
                    AND j.idTipoAgente = 3
                    AND i.stAvaliacaoItemPedidoAlteracao in ('AP','IN')";

            if ($unidade_autorizada == 166) :
                $sql.= " AND b.Area = 2 ";  // quando for SAV/CGAV/CAP pega somente os projetos da �rea de Audiovisual
            elseif ($unidade_autorizada == 272) :
                $sql.= " AND b.Area <> 2 "; // quando for SEFIC/GEAR/SACAV pega somente os projetos das �reas que n�o sejam de Audiovisual
            else :
                $sql.= " AND b.Area = 0 ";  // quando for diferente de SAV/CGAV/CAP e SAV/CGAV/CAP pega somente os projetos da �rea de Audiovisual
            endif;
        } elseif ($sqlDesejado == "sqlAnaliseGeral") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        a.dtSolicitacao AS DataEnvio,
                        b.Orgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem

                        FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto		AS a
                        INNER JOIN SAC.dbo.Projetos					AS b ON a.IdPRONAC = b.IdPRONAC
                        INNER JOIN SAC.dbo.Area						AS c ON b.Area = c.Codigo
                        LEFT JOIN SAC.dbo.Segmento					AS d ON b.Segmento = d.Codigo
                        INNER JOIN SAC.dbo.Abrangencia					AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                        INNER JOIN AGENTES.dbo.Municipios				AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao	AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                        INNER JOIN AGENTES.dbo.UF					AS h ON e.idUF = h.idUF

                        WHERE a.stPedidoAlteracao = 'I'
                        AND a.siVerificacao in (0,1)
                        AND (g.stVerificacao = 0 or g.stVerificacao is null) ";
        } elseif ($sqlDesejado == "sqlAnaliseGeralDev") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        j.dtEncaminhamento AS DataEnvio,
                        j.idOrgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        j.stVerificacao AS stAcao

                        FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                   AS a
                        INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                        INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                        LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                        INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                        INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                        INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                        INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao and g.tpAlteracaoProjeto = i.tpAlteracaoProjeto
                        INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao

                        WHERE a.stPedidoAlteracao = 'I'
                        AND a.siVerificacao in (0,1)
                        AND g.stVerificacao in (3,2)
                        AND j.stAtivo = 0
                        AND j.idTipoAgente = 3
                        AND i.stAvaliacaoItemPedidoAlteracao in ('AP','IN') ";
        }

        return $sql;
    }

    /*     * ***********************************************************************************************************************
     * SQL DO COORDENADOR DE PARECERISTA
     * *********************************************************************************************************************** */

    public static function retornaSQLCP($sqlDesejado, $tpAlteracao, $AgenteAcionado, $idOrgao = null)
    {
        if ($sqlDesejado == "sqlCoordParecerista") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        j.dtEncaminhamento AS DataEnvio,
                        j.idOrgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        j.stVerificacao AS stAcao,
                        j.idAgenteAcionado

                        FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                   AS a
                        INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                        INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                        LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                        INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                        INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                        INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                        INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao
                        INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao

                        WHERE a.stPedidoAlteracao = 'I'
                        AND a.siVerificacao in (0,1)
                        AND g.stVerificacao = 1
                        AND g.tpAlteracaoProjeto = $tpAlteracao
                        AND i.tpAlteracaoProjeto = $tpAlteracao
                        AND j.stAtivo = 0
                        AND j.idTipoAgente = 2
                        AND i.stAvaliacaoItemPedidoAlteracao = 'AG'
                        AND j.idAgenteAcionado = $AgenteAcionado ";

            if (!empty($idOrgao)) :
                        $sql.= " AND j.idOrgao = $idOrgao ";
            endif;

            $sql .= " ORDER BY j.dtEncaminhamento";
        } elseif ($sqlDesejado == "sqlCoordPareceristaGeral") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        j.dtEncaminhamento AS DataEnvio,
                        j.idOrgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        j.stVerificacao AS stAcao

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                   AS a
                    INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                    INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                    INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                    INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao
                    ,(SELECT MAX(tpAlteracaoProjeto) AS tpAlteracaoProjeto, idPedidoAlteracao
							  FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
							  WHERE tpAlteracaoProjeto IN (7, 10)
							  GROUP BY idPedidoAlteracao) AS tmp

                    WHERE a.stPedidoAlteracao = 'I'
                    AND a.siVerificacao in (0,1)
                    AND g.stVerificacao in (1,2)
                    AND j.stAtivo = 0
                    AND j.idTipoAgente = 2
                    AND g.idPedidoAlteracao = tmp.idPedidoAlteracao
                    AND g.tpAlteracaoProjeto = tmp.tpAlteracaoProjeto ";
        } elseif ($sqlDesejado == "sqlCoordPareceristaDev") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        j.dtEncaminhamento AS DataEnvio,
                        j.idOrgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        j.stVerificacao AS stAcao,
                        i.stAvaliacaoItemPedidoAlteracao AS situacao

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                   AS a
                    INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                    INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                    INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                    INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao and g.tpAlteracaoProjeto = i.tpAlteracaoProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao

                    WHERE a.stPedidoAlteracao = 'I'
                    AND a.siVerificacao in (0,1)
                    AND g.stVerificacao in (1,2)
                    AND g.tpAlteracaoProjeto = $tpAlteracao
                    AND j.stAtivo = 0
                    AND j.idTipoAgente = 2
                    AND i.stAvaliacaoItemPedidoAlteracao != 'AG' 
                    AND j.idAgenteAcionado = $AgenteAcionado  ";

            if (!empty($idOrgao)) :
                        $sql.= " AND j.idOrgao = $idOrgao ";
            endif;
        }


        return $sql;
    }

    /*     * ***********************************************************************************************************************
     * SQL DO PARECERISTA
     * *********************************************************************************************************************** */

    public static function retornaSQLPar($sqlDesejado, $tpAlteracao, $idOrgao = null, $idAgente = null)
    {
        if ($sqlDesejado == "sqlParecerista") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        j.dtEncaminhamento AS DataEnvio,
                        b.Orgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao,
                        j.stVerificacao AS stAcao

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                   AS a
                    INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                    INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                    INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                    INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao and g.tpAlteracaoProjeto = i.tpAlteracaoProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao

                    WHERE a.stPedidoAlteracao = 'I'
                    AND a.siVerificacao = 1
                    AND g.stVerificacao = 1
                    AND g.tpAlteracaoProjeto = $tpAlteracao
                    AND j.stAtivo = 0
                    AND j.idTipoAgente = 1 ";

            if (!empty($idOrgao)) :
                        $sql.= " AND j.idOrgao = $idOrgao ";
            endif;

            if (!empty($idAgente)) :
                        $sql.= " AND j.idAgenteAcionado = $idAgente ";
            endif;

            $sql.= " ORDER BY j.dtEncaminhamento";
        } elseif ($sqlDesejado == "sqlPareceristaGeral") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        j.dtEncaminhamento AS DataEnvio,
                        b.Orgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        j.stVerificacao AS stAcao

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                   AS a
                    INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                    INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                    INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                    INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao

                    WHERE a.stPedidoAlteracao = 'I'
                    AND a.siVerificacao = 1
                    AND g.stVerificacao = 1
                    AND j.stAtivo = 0
                    AND j.idTipoAgente = 1 ";
        }

        return $sql;
    }

    /*     * ***********************************************************************************************************************
     * SQL DO T�CNICO
     * *********************************************************************************************************************** */

    public static function retornaSQLTec($sqlDesejado, $tpAlteracao, $agenteAcionado, $orgaoAcionado)
    {
        if ($sqlDesejado == "sqlTecnico") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        j.dtEncaminhamento AS DataEnvio,
                        j.idOrgao,
                        b.Orgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        j.stVerificacao AS stAcao

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                           AS a
                    INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                    INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                    INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON A.idPedidoAlteracao = g.idPedidoAlteracao
                    INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao and g.tpAlteracaoProjeto = i.tpAlteracaoProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao

                    WHERE a.stPedidoAlteracao = 'I'
                    AND a.siVerificacao = 1
                    AND g.stVerificacao = 1
                    AND g.tpAlteracaoProjeto = $tpAlteracao
                    AND j.idOrgao = $orgaoAcionado
                    AND j.stAtivo = 0
                    AND j.idTipoAgente = 5
                    AND j.idAgenteAcionado = $agenteAcionado
                    ORDER BY j.dtEncaminhamento";
        } elseif ($sqlDesejado == "sqlTecnicoGeral") {
            $sql = "SELECT DISTINCT a.IdPRONAC AS ID_PRONAC,
                        b.AnoProjeto+b.Sequencial AS PRONAC,
                        b.NomeProjeto AS NomeProjeto,
                        c.Descricao AS areaDesc,
                        d.Descricao AS segmentoDesc,
                        j.dtEncaminhamento AS DataEnvio,
                        j.idOrgao,
                        b.Orgao,
                        a.stPedidoAlteracao,
                        g.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        a.siVerificacao,
                        g.stVerificacao AS stItem,
                        i.idAvaliacaoItemPedidoAlteracao,
                        j.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        j.stVerificacao AS stAcao

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto                           AS a
                    INNER JOIN SAC.dbo.Projetos                                         AS b ON a.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.Area                                             AS c ON b.Area = c.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                         AS d ON b.Segmento = d.Codigo
                    INNER JOIN SAC.dbo.Abrangencia                                      AS e ON b.idProjeto = e.idProjeto AND e.stAbrangencia = 1 
                    INNER JOIN AGENTES.dbo.Municipios                                   AS f ON e.idMunicipioIBGE = f.idMunicipioIBGE
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON a.idPedidoAlteracao = g.idPedidoAlteracao
                    INNER JOIN AGENTES.dbo.UF                                           AS h ON e.idUF = h.idUF
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS i ON g.idPedidoAlteracao = i.idPedidoAlteracao and g.tpAlteracaoProjeto = i.tpAlteracaoProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS j ON i.idAvaliacaoItemPedidoAlteracao = j.idAvaliacaoItemPedidoAlteracao

                    WHERE a.stPedidoAlteracao = 'I'
                    AND a.siVerificacao = 1
                    AND j.idOrgao = $orgaoAcionado
                    AND g.stVerificacao = 1
                    AND j.stAtivo = 0
                    AND j.idTipoAgente = 5 ";
        };
        
        return $sql;
    }

    /*     * *********************************************************************
      SQL - PROPOSTA PEDAG�GICA
     * ********************************************************************* */

    public static function retornaSQLproposta($sqlDesejado, $id_Pronac, $tipoAlteracao=null, $planoDistribuicaoObrigatorio = null, $idPedidoAlteracao = null)
    {
        $sql = '';

        if ($sqlDesejado == "sqlproposta") {
            $sql = "SELECT *,
                        CAST(Solicitacao AS text) as Solicitacao,
                        CAST(Justificativa AS text) as Justificativa ,
                        CAST(dsEstrategiaExecucao AS text) as dsEstrategiaExecucao,
                        CAST(dsEspecificacaoSolicitacao AS text) as dsEspecificacaoSolicitacao,
                        CAST(dsJustificativaSolicitacao AS text) as dsJustificativaSolicitacao
                        FROM(
                        SELECT DISTINCT b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto AS NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        f.Nome AS proponente,
                        f.CgcCpf,
                        d.EstrategiadeExecucao as EspecificacaoTecnica,
                        d.EspecificacaoTecnica as EstrategiadeExecucao,
                        a.dsObjetivos AS Solicitacao,
                        a.dsJustificativa AS Justificativa,
                        a.dsEstrategiaExecucao,
                        a.dsEspecificacaoTecnica as dsEspecificacaoSolicitacao,
                        g.dsJustificativa as dsJustificativaSolicitacao

                    FROM SAC.dbo.tbProposta AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto             AS b ON b.idPedidoAlteracao = a.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON c.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.PreProjeto                                       AS d ON d.idPreProjeto = c.idProjeto
                    INNER JOIN AGENTES.dbo.Agentes                                      AS e ON e.idAgente = d.idAgente
                    INNER JOIN SAC.dbo.vProponenteProjetos                              AS f ON c.CgcCpf = f.CgcCpf
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON g.idPedidoAlteracao = a.idPedidoAlteracao

                    WHERE b.IdPRONAC = $id_Pronac AND g.tpAlteracaoProjeto = 6) as tabela";
        }

        if ($sqlDesejado == "sqlpropostadev") {
            $sql = "SELECT *,
                        CAST(Justificativa AS text) as Justificativa ,
                        CAST(dsJustificativa AS text) as dsJustificativa,
                        CAST(dsEstrategiaExecucao AS text) as dsEstrategiaExecucao,
                        CAST(dsEspecificacaoTecnica AS text) as dsEspecificacaoTecnica,
                        CAST(Solicitacao AS text) as Solicitacao
                        FROM(
            SELECT DISTINCT b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto AS NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Nome AS proponente,
                        d.EstrategiadeExecucao as EspecificacaoTecnica,
                        d.EspecificacaoTecnica as EstrategiadeExecucao,
                        a.dsObjetivos AS Solicitacao,
                        a.dsJustificativa AS Justificativa,
                        a.dsEstrategiaExecucao,
                        a.dsEspecificacaoTecnica,
                        f.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        h.idAvaliacaoItemPedidoAlteracao AS idAvaliacao,
                        i.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        a.IdProposta,
                        i.idOrgao,
                        h.stAvaliacaoItemPedidoAlteracao,
                        g.dsJustificativa
                    FROM SAC.dbo.tbProposta AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto             AS b ON b.idPedidoAlteracao = a.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON c.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.PreProjeto                                       AS d ON d.idPreProjeto = c.idProjeto
                    INNER JOIN AGENTES.dbo.Agentes                                      AS e ON e.idAgente = d.idAgente
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto             AS f ON f.IdPRONAC = c.IdPRONAC
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON g.idPedidoAlteracao = f.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS h ON h.idPedidoAlteracao = g.idPedidoAlteracao and h.tpAlteracaoProjeto = g.tpAlteracaoProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS i ON i.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao
                    INNER JOIN SAC.dbo.vProponenteProjetos                              AS j ON c.CgcCpf = j.CgcCpf


                    WHERE b.IdPRONAC = $id_Pronac 
                    AND b.idPedidoAlteracao = $idPedidoAlteracao 
                    AND i.stAtivo = 0
                    AND H.tpAlteracaoProjeto = 6 ) as tabela";
        }

        if ($sqlDesejado == "sqlpropostaeditar") {
            $sql = "SELECT *,
            CAST(dsJustificativa AS text) as dsJustificativa,
            CAST(dsEstrategiaExecucao AS text) as dsEstrategiaExecucao,
            CAST(dsEspecificacaoTecnica AS text) as dsEspecificacaoTecnica,
            CAST(dsJustificativa AS text) as Justificativa  FROM
            (
            SELECT DISTINCT b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto AS NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Nome AS proponente,
                        d.EstrategiadeExecucao as EspecificacaoTecnica,
                        d.EspecificacaoTecnica as EstrategiadeExecucao,
                        a.dsObjetivos AS Solicitacao,
                        a.dsJustificativa AS Justificativa,
                        a.dsEstrategiaExecucao,
                        a.dsEspecificacaoTecnica,
                        f.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        h.idAvaliacaoItemPedidoAlteracao AS idAvaliacao,
                        i.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao,
                        a.IdProposta,
                        i.idOrgao,
                        h.stAvaliacaoItemPedidoAlteracao,
                        g.dsJustificativa
                    FROM SAC.dbo.tbProposta AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto             AS b ON b.idPedidoAlteracao = a.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON c.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.PreProjeto                                       AS d ON d.idPreProjeto = c.idProjeto
                    INNER JOIN AGENTES.dbo.Agentes                                      AS e ON e.idAgente = d.idAgente
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto             AS f ON f.IdPRONAC = c.IdPRONAC
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao      AS g ON g.idPedidoAlteracao = f.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS h ON h.idPedidoAlteracao = g.idPedidoAlteracao and h.tpAlteracaoProjeto = g.tpAlteracaoProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS i ON i.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao
                    INNER JOIN SAC.dbo.vProponenteProjetos                              AS j ON c.CgcCpf = j.CgcCpf
                    WHERE b.IdPRONAC = $id_Pronac 
                    AND b.idPedidoAlteracao = $idPedidoAlteracao 
                    AND g.stVerificacao = 1
                    AND i.idTipoAgente in (1,5)
                    AND i.stAtivo = 0
                    AND H.tpAlteracaoProjeto = 6 ) as tabela";
        }

        if ($sqlDesejado == "sqlConsultaReadequacaoInicial") {
            $sql = "SELECT a.idPedidoAlteracao,
                        a.idPlano,
                        b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Descricao AS proponente,
                        d.Descricao AS Produto,
                        e.Descricao AS Area,
                        f.Descricao AS Segmento,
                        g.Descricao AS Posicao,
                        a.cdSegmento,
                        a.qtPatrocinador AS Patrocinador,
                        a.qtProduzida AS Beneficiarios,
                        a.qtOutros AS Divulgacao,
                        a.qtVendaNormal AS NormalTV,
                        a.qtVendaPromocional AS PromocionalTV,
                        a.vlUnitarioNormal AS NormalPU,
                        a.vlUnitarioPromocional AS PromocionalPU,
                        a.stPrincipal AS PrdotudoPrincpal,
                        a.tpAcao,
                        CAST(a.dsjustificativa AS TEXT) AS JustificativaProponente,
                        h.idAvaliacaoItemPedidoAlteracao

                    FROM SAC.dbo.tbPlanoDistribuicao					AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto             AS b ON a.idPedidoAlteracao = b.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON b.IdPRONAC = c.IdPRONAC
                    INNER JOIN SAC.dbo.Produto                                          AS d ON a.idProduto = d.Codigo
                    INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                          AS f ON a.cdSegmento = f.Codigo
                    INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                    LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto in (7,10)
                    INNER JOIN AGENTES.dbo.Agentes                                      AS i ON c.CgcCpf = i.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes                                        AS j ON i.idAgente = j.idAgente
                    WHERE c.IdPRONAC = $id_Pronac ";
                    
            if (!empty($idPedidoAlteracao)) :
                $sql.= "AND b.idPedidoAlteracao = $idPedidoAlteracao";
            endif;
        }

        if ($sqlDesejado == "sqlConsultaReadequacao") {
            $sql = "SELECT distinct a.idPedidoAlteracao,
                        a.idPlano,
                        b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        i.Nome AS proponente,
                        d.Descricao AS Produto,
                        e.Descricao AS Area,
                        f.Descricao AS Segmento,
                        g.Descricao AS Posicao,
                        a.cdSegmento,
                        a.qtPatrocinador AS Patrocinador,
                        a.qtProduzida AS Beneficiarios,
                        a.qtOutros AS Divulgacao,
                        a.qtVendaNormal AS NormalTV,
                        a.qtVendaPromocional AS PromocionalTV,
                        a.vlUnitarioNormal AS NormalPU,
                        a.vlUnitarioPromocional AS PromocionalPU,
                        a.stPrincipal AS PrdotudoPrincpal,
                        a.tpAcao,
                        h.idAvaliacaoItemPedidoAlteracao

                    FROM SAC.dbo.tbPlanoDistribuicao					AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto             AS b ON a.idPedidoAlteracao = b.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON b.IdPRONAC = c.IdPRONAC
                    INNER JOIN SAC.dbo.Produto                                          AS d ON a.idProduto = d.Codigo
                    INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                         AS f ON a.cdSegmento = f.Codigo
                    INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                    LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto = 10
                    INNER JOIN SAC.dbo.vProponenteProjetos				AS i ON c.CgcCpf = i.CgcCpf

                    WHERE c.IdPRONAC = $id_Pronac
                    AND h.idAvaliacaoItemPedidoAlteracao = (

                        SELECT TOP 1 h.idAvaliacaoItemPedidoAlteracao
                        FROM SAC.dbo.tbPlanoDistribuicao				AS a
                        INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto		AS b ON a.idPedidoAlteracao = b.idPedidoAlteracao
                        INNER JOIN SAC.dbo.Projetos					AS c ON b.IdPRONAC = c.IdPRONAC
                        INNER JOIN SAC.dbo.Produto					AS d ON a.idProduto = d.Codigo
                        INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                        LEFT JOIN SAC.dbo.Segmento					AS f ON a.cdSegmento = f.Codigo
                        INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                        LEFT JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto = 10
                        INNER JOIN SAC.dbo.vProponenteProjetos				AS i ON c.CgcCpf = i.CgcCpf
                        WHERE c.IdPRONAC = $id_Pronac
                        ORDER BY h.idAvaliacaoItemPedidoAlteracao DESC)";
        }

        if ($sqlDesejado == "sqlConsultaReadequacaoEditar") {
            $sql = "SELECT a.idPedidoAlteracao,
                        a.idPlano,
                        b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Descricao AS proponente,
                        d.Codigo AS idProduto,
                        d.Descricao AS Produto,
                        e.Descricao AS Area,
                        f.Descricao AS Segmento,
                        g.Descricao AS Posicao,
                        a.cdSegmento,
                        a.qtPatrocinador AS Patrocinador,
                        a.qtProduzida AS Beneficiarios,
                        a.qtOutros AS Divulgacao,
                        a.qtVendaNormal AS NormalTV,
                        a.qtVendaPromocional AS PromocionalTV,
                        a.vlUnitarioNormal AS NormalPU,
                        a.vlUnitarioPromocional AS PromocionalPU,
                        a.stPrincipal AS PrdotudoPrincpal,
                        a.tpAcao,
                        c.idProjeto,
                        h.idAvaliacaoItemPedidoAlteracao,
                        e.Codigo AS cdArea
                        ,(select TOP 1 bb.stAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano and aa.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS avaliacao
                        ,CAST((select TOP 1 bb.dsAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano and aa.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS TEXT) AS dsJustificativa


                    FROM SAC.dbo.tbPlanoDistribuicao					AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto     	AS b ON a.idPedidoAlteracao = b.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON b.IdPRONAC = c.IdPRONAC
                    INNER JOIN SAC.dbo.Produto                                          AS d ON a.idProduto = d.Codigo
                    INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                          AS f ON a.cdSegmento = f.Codigo
                    INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto in (7,10)
                    INNER JOIN AGENTES.dbo.Agentes                                      AS i ON c.CgcCpf = i.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes                                        AS j ON i.idAgente = j.idAgente
                    ,(SELECT MAX(idPlano) AS idPlano, idProduto
                            FROM SAC.dbo.tbPlanoDistribuicao
                            GROUP BY idProduto
                            HAVING MAX(idPlano) > 0) AS tmp
                    ,(SELECT MAX(idAvaliacaoItemPedidoAlteracao) AS idAvaliacaoItemPedidoAlteracao
                            FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                            WHERE stAvaliacaoItemPedidoAlteracao != 'AP'
                            AND stAvaliacaoItemPedidoAlteracao != 'IN'
                            GROUP BY idAvaliacaoItemPedidoAlteracao) AS tmp2

                    WHERE c.IdPRONAC = $id_Pronac
                    AND a.idProduto = tmp.idProduto
                    AND a.idPlano   = tmp.idPlano
                    AND h.idAvaliacaoItemPedidoAlteracao = tmp2.idAvaliacaoItemPedidoAlteracao
                    ORDER BY d.Descricao ";
        }


        if ($sqlDesejado == "sqlConsultaReadequacaoEditarParecerista") {
            $sql = "SELECT a.idPedidoAlteracao,
                        a.idPlano,
                        b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Descricao AS proponente,
                        d.Codigo AS idProduto,
                        d.Descricao AS Produto,
                        e.Descricao AS Area,
                        f.Descricao AS Segmento,
                        g.Descricao AS Posicao,
                        a.cdSegmento,
                        a.qtPatrocinador AS Patrocinador,
                        a.qtProduzida AS Beneficiarios,
                        a.qtOutros AS Divulgacao,
                        a.qtVendaNormal AS NormalTV,
                        a.qtVendaPromocional AS PromocionalTV,
                        a.vlUnitarioNormal AS NormalPU,
                        a.vlUnitarioPromocional AS PromocionalPU,
                        a.stPrincipal AS PrdotudoPrincpal,
                        a.tpAcao,
                        c.idProjeto,
                        h.idAvaliacaoItemPedidoAlteracao,
                        e.Codigo AS cdArea
                        ,(select TOP 1 bb.stAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano and aa.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS avaliacao
                        ,CAST((select TOP 1 bb.dsAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano and aa.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS TEXT) AS dsJustificativa


                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto     	AS b
                    LEFT JOIN SAC.dbo.tbPlanoDistribuicao					AS a ON b.idPedidoAlteracao = a.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON b.IdPRONAC = c.IdPRONAC
                    INNER JOIN SAC.dbo.Produto                                          AS d ON a.idProduto = d.Codigo
                    INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                          AS f ON a.cdSegmento = f.Codigo
                    INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto in (7,10)
                    INNER JOIN AGENTES.dbo.Agentes                                      AS i ON c.CgcCpf = i.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes                                        AS j ON i.idAgente = j.idAgente
                    ,(SELECT MAX(idPlano) AS idPlano, idProduto
                            FROM SAC.dbo.tbPlanoDistribuicao
                            GROUP BY idProduto
                            HAVING MAX(idPlano) > 0) AS tmp
                    ,(SELECT MAX(idAvaliacaoItemPedidoAlteracao) AS idAvaliacaoItemPedidoAlteracao
                            FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                            WHERE stAvaliacaoItemPedidoAlteracao != 'AP'
                            AND stAvaliacaoItemPedidoAlteracao != 'IN'
                            GROUP BY idAvaliacaoItemPedidoAlteracao) AS tmp2

                    WHERE c.IdPRONAC = $id_Pronac
                    AND d.Codigo = tmp.idProduto";

            if ($planoDistribuicaoObrigatorio) :
                   $sql.= " AND a.idPlano   = tmp.idPlano";
            endif;
                    
            $sql.= " AND h.idAvaliacaoItemPedidoAlteracao = tmp2.idAvaliacaoItemPedidoAlteracao ORDER BY d.Descricao ";
        }


        if ($sqlDesejado == "sqlConsultaReadequacaoProponente") {
            $sql = "SELECT a.idPedidoAlteracao,
                        a.idPlano,
                        b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Descricao AS proponente,
                        d.Codigo AS idProduto,
                        d.Descricao AS Produto,
                        e.Descricao AS Area,
                        f.Descricao AS Segmento,
                        g.Descricao AS Posicao,
                        a.cdSegmento,
                        a.qtPatrocinador AS Patrocinador,
                        a.qtProduzida AS Beneficiarios,
                        a.qtOutros AS Divulgacao,
                        a.qtVendaNormal AS NormalTV,
                        a.qtVendaPromocional AS PromocionalTV,
                        a.vlUnitarioNormal AS NormalPU,
                        a.vlUnitarioPromocional AS PromocionalPU,
                        a.stPrincipal AS PrdotudoPrincpal,
                        a.tpAcao,
                        CAST(a.dsjustificativa AS TEXT) AS JustificativaProponente,
                        c.idProjeto,
                        h.idAvaliacaoItemPedidoAlteracao,
                        e.Codigo AS cdArea
                        ,(select TOP 1 bb.stAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS avaliacao
                        ,(select TOP 1 bb.dsAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS dsJustificativa


                    FROM SAC.dbo.tbPlanoDistribuicao					AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto     	AS b ON a.idPedidoAlteracao = b.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON b.IdPRONAC = c.IdPRONAC
                    INNER JOIN SAC.dbo.Produto                                          AS d ON a.idProduto = d.Codigo
                    INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                 	AS f ON a.cdSegmento = f.Codigo
                    INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto in (7,10)
                    INNER JOIN AGENTES.dbo.Agentes                                      AS i ON c.CgcCpf = i.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes                                        AS j ON i.idAgente = j.idAgente
                    ,(SELECT MAX(idPlano) AS idPlano, idProduto
                            FROM SAC.dbo.tbPlanoDistribuicao
                            GROUP BY idProduto
                            HAVING MAX(idPlano) > 0) AS tmp
                    ,(SELECT MAX(idAvaliacaoItemPedidoAlteracao) AS idAvaliacaoItemPedidoAlteracao
                            FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                            WHERE stAvaliacaoItemPedidoAlteracao != 'AP'
                            AND stAvaliacaoItemPedidoAlteracao != 'IN'
                            GROUP BY idAvaliacaoItemPedidoAlteracao) AS tmp2

                    WHERE c.IdPRONAC = $id_Pronac
                    AND a.idProduto = tmp.idProduto
                    AND a.idPlano   = tmp.idPlano
                    AND h.idAvaliacaoItemPedidoAlteracao = tmp2.idAvaliacaoItemPedidoAlteracao
                    ORDER BY d.Descricao ";
        }


        if ($sqlDesejado == "sqlConsultaReadequacaoParecerista") {
            $sql = "SELECT a.idPedidoAlteracao,
                        a.idPlano,
                        b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Descricao AS proponente,
                        d.Codigo AS idProduto,
                        d.Descricao AS Produto,
                        e.Descricao AS Area,
                        f.Descricao AS Segmento,
                        g.Descricao AS Posicao,
                        a.cdSegmento,
                        a.qtPatrocinador AS Patrocinador,
                        a.qtProduzida AS Beneficiarios,
                        a.qtOutros AS Divulgacao,
                        a.qtVendaNormal AS NormalTV,
                        a.qtVendaPromocional AS PromocionalTV,
                        a.vlUnitarioNormal AS NormalPU,
                        a.vlUnitarioPromocional AS PromocionalPU,
                        a.stPrincipal AS PrdotudoPrincpal,
                        a.tpAcao,
                        CAST(a.dsjustificativa AS TEXT) AS JustificativaProponente,
                        c.idProjeto,
                        h.idAvaliacaoItemPedidoAlteracao,
                        e.Codigo AS cdArea
                        ,(select TOP 1 bb.stAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS avaliacao
                        ,(select TOP 1 bb.dsAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS dsJustificativa


                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto     	AS b
                    LEFT JOIN SAC.dbo.tbPlanoDistribuicao					AS a ON b.idPedidoAlteracao = a.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON b.IdPRONAC = c.IdPRONAC
                    INNER JOIN SAC.dbo.Produto                                          AS d ON a.idProduto = d.Codigo
                    INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                 	AS f ON a.cdSegmento = f.Codigo
                    INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto in (7,10)
                    INNER JOIN AGENTES.dbo.Agentes                                      AS i ON c.CgcCpf = i.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes                                        AS j ON i.idAgente = j.idAgente
                    ,(SELECT MAX(idPlano) AS idPlano, idProduto
                            FROM SAC.dbo.tbPlanoDistribuicao
                            GROUP BY idProduto
                            HAVING MAX(idPlano) > 0) AS tmp
                    ,(SELECT MAX(idAvaliacaoItemPedidoAlteracao) AS idAvaliacaoItemPedidoAlteracao
                            FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                            WHERE stAvaliacaoItemPedidoAlteracao != 'AP'
                            AND stAvaliacaoItemPedidoAlteracao != 'IN'
                            GROUP BY idAvaliacaoItemPedidoAlteracao) AS tmp2

                    WHERE c.IdPRONAC = $id_Pronac
                    AND d.Codigo = tmp.idProduto";
                   
            if ($planoDistribuicaoObrigatorio) :
                   $sql.= " AND a.idPlano   = tmp.idPlano";
            endif;

            $sql.= " AND h.idAvaliacaoItemPedidoAlteracao = tmp2.idAvaliacaoItemPedidoAlteracao
                    ORDER BY d.Descricao ";
        }


        if ($sqlDesejado == "sqlConsultaReadequacaoDev") {
            $sql = "SELECT distinct a.idPedidoAlteracao,
                        a.idPlano,
                        b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Descricao AS proponente,
                        d.Descricao AS Produto,
                        e.Descricao AS Area,
                        f.Descricao AS Segmento,
                        g.Descricao AS Posicao,
                        a.cdSegmento,
                        a.qtPatrocinador AS Patrocinador,
                        a.qtProduzida AS Beneficiarios,
                        a.qtOutros AS Divulgacao,
                        a.qtVendaNormal AS NormalTV,
                        a.qtVendaPromocional AS PromocionalTV,
                        a.vlUnitarioNormal AS NormalPU,
                        a.vlUnitarioPromocional AS PromocionalPU,
                        a.stPrincipal AS PrdotudoPrincpal,
                        a.tpAcao,
                        h.idAvaliacaoItemPedidoAlteracao,
                        h.dsAvaliacao
                        ,(select TOP 1 bb.stAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS avaliacao
                        ,(select TOP 1 bb.dsAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS dsJustificativa

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto     	AS b 
                    LEFT JOIN SAC.dbo.tbPlanoDistribuicao					AS a  ON a.idPedidoAlteracao = a.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON b.IdPRONAC = c.IdPRONAC
                    INNER JOIN SAC.dbo.Produto                                          AS d ON a.idProduto = d.Codigo
                    INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                          AS f ON a.cdSegmento = f.Codigo
                    INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto in (7,10)
                    INNER JOIN AGENTES.dbo.Agentes                                      AS i ON c.CgcCpf = i.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes                                        AS j ON i.idAgente = j.idAgente


                    ,(SELECT MIN(idPlano) AS idPlano, idProduto
                            FROM SAC.dbo.tbPlanoDistribuicao
                            GROUP BY idProduto
                            HAVING MIN(idPlano) > 0) AS tmp
                    ,(SELECT MAX(idAvaliacaoItemPedidoAlteracao) AS idAvaliacaoItemPedidoAlteracao
                            FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                            WHERE stAvaliacaoItemPedidoAlteracao in ('AP','IN')  AND tpAlteracaoProjeto = 7) AS tmp2

                    WHERE c.IdPRONAC = $id_Pronac
                    AND a.idProduto = tmp.idProduto
                    --AND a.idPlano   = tmp.idPlano
                    --AND tmp2.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao
                    ";
        }

        if ($sqlDesejado == "sqlConsultaReadequacaoDevParecerista") {
            $sql = "SELECT distinct a.idPedidoAlteracao,
                        a.idPlano,
                        b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Descricao AS proponente,
                        d.Descricao AS Produto,
                        e.Descricao AS Area,
                        f.Descricao AS Segmento,
                        g.Descricao AS Posicao,
                        a.cdSegmento,
                        a.qtPatrocinador AS Patrocinador,
                        a.qtProduzida AS Beneficiarios,
                        a.qtOutros AS Divulgacao,
                        a.qtVendaNormal AS NormalTV,
                        a.qtVendaPromocional AS PromocionalTV,
                        a.vlUnitarioNormal AS NormalPU,
                        a.vlUnitarioPromocional AS PromocionalPU,
                        a.stPrincipal AS PrdotudoPrincpal,
                        a.tpAcao,
                        h.idAvaliacaoItemPedidoAlteracao,
                        h.dsAvaliacao
                        ,(select TOP 1 bb.stAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS avaliacao
                        ,(select TOP 1 bb.dsAvaliacaoSubItemPedidoAlteracao
                                from BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPlanoDistribuicao as aa
                                inner join BDCORPORATIVO.scSAC.tbAvaliacaoSubItemPedidoAlteracao as bb on aa.idAvaliacaoSubItemPedidoAlteracao = bb.idAvaliacaoSubItemPedidoAlteracao
                                where aa.idPlano = a.idPlano ORDER BY aa.idAvaliacaoSubItemPedidoAlteracao desc) AS dsJustificativa

                    FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto     	AS b 
                    LEFT JOIN SAC.dbo.tbPlanoDistribuicao					AS a  ON a.idPedidoAlteracao = a.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON b.IdPRONAC = c.IdPRONAC
                    INNER JOIN SAC.dbo.Produto                                          AS d ON a.idProduto = d.Codigo
                    INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                    LEFT JOIN SAC.dbo.Segmento                                          AS f ON a.cdSegmento = f.Codigo
                    INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto in (7,10)
                    INNER JOIN AGENTES.dbo.Agentes                                      AS i ON c.CgcCpf = i.CNPJCPF
                    INNER JOIN AGENTES.dbo.Nomes                                        AS j ON i.idAgente = j.idAgente


                    ,(SELECT MIN(idPlano) AS idPlano, idProduto
                            FROM SAC.dbo.tbPlanoDistribuicao
                            GROUP BY idProduto
                            HAVING MIN(idPlano) > 0) AS tmp
                    ,(SELECT MAX(idAvaliacaoItemPedidoAlteracao) AS idAvaliacaoItemPedidoAlteracao
                            FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                            WHERE stAvaliacaoItemPedidoAlteracao in ('AP','IN')  AND tpAlteracaoProjeto = 10) AS tmp2

                    WHERE c.IdPRONAC = $id_Pronac
                    AND a.idProduto = tmp.idProduto
                    AND a.idPlano   = tmp.idPlano
                    AND tmp2.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao
                    ";
        }

        if ($sqlDesejado == "sqlListaArea") {
            $sql = "SELECT * FROM SAC.dbo.Area ORDER BY Descricao";
        }
        if ($sqlDesejado == "sqlListaSegmento") {
            $sql = "SELECT * FROM SAC.dbo.Segmento ORDER BY Descricao";
        }
        if ($sqlDesejado == "sqlConsultaNomeProjEditar") {
            $sql = "SELECT distinct b.IdPRONAC,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto AS NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        j.Nome AS proponente,
                        d.EstrategiadeExecucao,
                        d.EspecificacaoTecnica,
                        a.dsObjetivos AS Solicitacao,
                        g.dsJustificativa AS Justificativa,
                        a.dsEstrategiaExecucao,
                        a.dsEspecificacaoTecnica,
                        f.idPedidoAlteracao,
                        g.tpAlteracaoProjeto,
                        h.idAvaliacaoItemPedidoAlteracao,
                        i.idAcaoAvaliacaoItemPedidoAlteracao,
                        a.IdProposta,
                        i.idOrgao,
                        h.stAvaliacaoItemPedidoAlteracao
                FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto AS b
                LEFT JOIN SAC.dbo.tbProposta                                            AS a ON a.idPedidoAlteracao = b.idPedidoAlteracao
                INNER JOIN SAC.dbo.Projetos 						AS c ON c.IdPRONAC = b.IdPRONAC
                INNER JOIN SAC.dbo.PreProjeto 						AS d ON d.idPreProjeto = c.idProjeto
                INNER JOIN AGENTES.dbo.Agentes 						AS e ON e.idAgente = d.idAgente
                INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto 		AS f ON f.IdPRONAC = c.IdPRONAC
                INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao 		AS g ON g.idPedidoAlteracao = f.idPedidoAlteracao
                INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 		AS h ON h.idPedidoAlteracao = g.idPedidoAlteracao and h.tpAlteracaoProjeto = g.tpAlteracaoProjeto
                INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao 	AS i ON i.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao
                INNER JOIN SAC.dbo.vProponenteProjetos 					AS j ON c.CgcCpf = j.CgcCpf
                WHERE b.IdPRONAC = $id_Pronac 
                AND b.idPedidoAlteracao = $idPedidoAlteracao 
                AND g.stVerificacao in (1,2)
                AND i.idTipoAgente in (3,5)
                AND i.stAtivo = 0
                AND H.tpAlteracaoProjeto = $tipoAlteracao AND b.idPedidoAlteracao = $idPedidoAlteracao ";
        }

        return $sql;
    }

    //finalizar
    public static function retornaSQLfinalprop($estrategia, $especificacao, $IdProposta)
    {
        $sql = "UPDATE SAC.dbo.tbProposta
                SET dsEstrategiaExecucao = '" . $estrategia . "', dsJustificativa = '" . $especificacao . "'
                WHERE IdProposta = $IdProposta ";
        return $sql;
    }

    public static function retornaSQLfinalprop1($idPedidoAlteracao, $tpAlteracaoProjeto)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
                SET stVerificacao = 2
                WHERE idPedidoAlteracao = $idPedidoAlteracao
                AND tpAlteracaoProjeto = $tpAlteracaoProjeto
                AND stVerificacao = 1 ";
        return $sql;
    }

    public static function consultarIdAvaliacao($idPedidoAlteracao)
    {
        $sql = "SELECT * FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                WHERE idPedidoAlteracao = $idPedidoAlteracao
                AND tpAlteracaoProjeto = 7";
        //AND stAvaliacaoItemPedidoAlteracao = 'EA' ";
        return $sql;
    }

    public static function consultarIdAcaoAvaliacao($idAvaliacaoPedidoAlteracao)
    {
        $sql = "SELECT idAcaoAvaliacaoItemPedidoAlteracao AS idAcaoAvaliacao, idOrgao FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacaoPedidoAlteracao
                AND stAtivo = 0 ";
        return $sql;
    }

    public static function retornaSQLfinalprop2($idAvaliacao, $especificacao='.', $status, $tpAlteracaoProjeto = null)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                SET dtFimAvaliacao = GETDATE(), stAvaliacaoItemPedidoAlteracao = '$status', dsAvaliacao = '$especificacao'
                WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacao ";

        if (!empty($tpAlteracaoProjeto)) :
            $sql.= " AND tpAlteracaoProjeto = $tpAlteracaoProjeto ";
        endif;

        return $sql;
    }

    public static function retornaSQLfinalprop3($idAcao)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                SET stAtivo = 1
                WHERE idAcaoAvaliacaoItemPedidoAlteracao = $idAcao ";
        return $sql;
    }

    public static function retornaSQLfinalprop4($idAvaliacao, $idOrgao, $idAgenteRemetente, $idPerfilRemetente)
    {
        $sql = "INSERT BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                VALUES ('$idAvaliacao','','','3','$idOrgao','0','2',GETDATE(),'$idAgenteRemetente','$idPerfilRemetente')";
        return $sql;
    }

    /*     * *****************************************************************************
      SQL PARA INICIAR A SOLICITA��O DE PROPOSTA PEDAG�GICA
     * ***************************************************************************** */

    public static function stPropostaInicio($sqlDesejado, $idAvaliacao, $AgenteLogin)
    {
        if ($sqlDesejado == "readequacaoEA") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                    SET idAgenteAvaliador = $AgenteLogin, dtInicioAvaliacao = GETDATE(), stAvaliacaoItemPedidoAlteracao = 'EA'
                    WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacao ";
        } elseif ($sqlDesejado == "readequacaoAP") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                    SET idAgenteAvaliador = $AgenteLogin, dtInicioAvaliacao = GETDATE(), stAvaliacaoItemPedidoAlteracao = 'AP'
                    WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacao ";
        } elseif ($sqlDesejado == "readequacaoIN") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                    SET idAgenteAvaliador = $AgenteLogin, dtInicioAvaliacao = GETDATE(), stAvaliacaoItemPedidoAlteracao = 'IN'
                    WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacao ";
        }

        return $sql;
    }

    public static function PropostaAltCampo($idAvaliacao)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                SET stVerificacao = 1
                WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacao
                AND stAtivo = 0 ";
        return $sql;
    }

    public static function diligenciarProposta($IdPronac, $solicitacao, $AgenteLogin)
    {
        $sql = "INSERT into SAC.dbo.tbDiligencia
                VALUES ('$IdPronac','124',GETDATE(),'" . $solicitacao . "','$AgenteLogin','','','','0')";
        return $sql;
    }

    /***********************************************************************
      SQL PARA LISTAR OS IDs PLANOS PARA ALTERA��O DOS DADOS INDIVIDUALMENTE
     ********************************************************************** */

    public static function listaSQLidPlano($idPronac)
    {
        $sql = "SELECT distinct a.idPedidoAlteracao,
                    a.idPlano,
                    b.IdPRONAC,
                    c.AnoProjeto+c.Sequencial AS PRONAC,
                    c.NomeProjeto,
                    c.CgcCpf AS CNPJCPF,
                    i.Nome AS proponente,
                    d.Descricao AS Produto,
                    e.Descricao AS Area,
                    f.Descricao AS Segmento,
                    g.Descricao AS Posicao,
                    a.cdSegmento,
                    a.qtPatrocinador AS Patrocinador,
                    a.qtProduzida AS Beneficiarios,
                    a.qtOutros AS Divulgacao,
                    a.qtVendaNormal AS NormalTV,
                    a.qtVendaPromocional AS PromocionalTV,
                    a.vlUnitarioNormal AS NormalPU,
                    a.vlUnitarioPromocional AS PromocionalPU,
                    a.stPrincipal AS PrdotudoPrincpal,
                    a.tpAcao,
                    h.idAvaliacaoItemPedidoAlteracao

                FROM SAC.dbo.tbPlanoDistribuicao				AS a
                INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto     	AS b ON a.idPedidoAlteracao = b.idPedidoAlteracao
                INNER JOIN SAC.dbo.Projetos                                     AS c ON b.IdPRONAC = c.IdPRONAC
                INNER JOIN SAC.dbo.Produto                                      AS d ON a.idProduto = d.Codigo
                INNER JOIN SAC.dbo.Area						AS e ON a.cdArea = e.Codigo
                INNER JOIN SAC.dbo.Segmento                                 	AS f ON a.cdSegmento = f.Codigo
                INNER JOIN SAC.dbo.Verificacao					AS g ON a.idPosicaoLogo = g.idVerificacao
                INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao 	AS h ON h.idPedidoAlteracao = a.idPedidoAlteracao and h.tpAlteracaoProjeto in (7,10)
                INNER JOIN SAC.dbo.vProponenteProjetos				AS i ON c.CgcCpf = i.CgcCpf

                WHERE c.IdPRONAC = $idPronac
                AND h.dsAvaliacao = '' or h.dsAvaliacao is NULL ";
        return $sql;
    }


    /*     * *********************************************************************
      SQL SALVAR OS DADOS NA TABELA tbPlanoDistribuicao
     * ********************************************************************* */

    public static function sqlsalvareadequacao($updateFrom, $sqldados, $where, $and1)
    {
        $sql = $updateFrom . " " . $sqldados . " " . $where . " " . $and1;
        return $sql;
    }

    /*     * *********************************************************************
      SQL
     * ********************************************************************* */

    public static function alteraStatusReadequacao($idPedidoAlt)
    {
        $sql = "SELECT *
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                WHERE idAvaliacaoItemPedidoAlteracao = $idPedidoAlt ";

        return $sql;
    }

    public static function alteraStatusProposta($idAvaliacao)
    {
        $sql = "SELECT *
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacao ";
        
        return $sql;
    }

    /*     * *****************************************************************************
      SQL PARA INICIAR A SOLICITA��O DE READEQUA��O DE PRODUTOS
     * ***************************************************************************** */

    public static function stReadequacaoInicio($sqlDesejado, $idPedidoAlteracao, $idAgente)
    {
        if ($sqlDesejado == "readequacaoEA") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                    SET idAgenteAvaliador = $idAgente, dtInicioAvaliacao = GETDATE(), stAvaliacaoItemPedidoAlteracao = 'EA'
                    WHERE idAvaliacaoItemPedidoAlteracao = $idPedidoAlteracao ";
        } elseif ($sqlDesejado == "readequacaoAP") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                    SET idAgenteAvaliador = $idAgente, dtInicioAvaliacao = GETDATE(), stAvaliacaoItemPedidoAlteracao = 'AP'
                    WHERE idAvaliacaoItemPedidoAlteracao = $idPedidoAlteracao ";
        } elseif ($sqlDesejado == "readequacaoIN") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                    SET idAgenteAvaliador = $idAgente, dtInicioAvaliacao = GETDATE(), stAvaliacaoItemPedidoAlteracao = 'IN'
                    WHERE idAvaliacaoItemPedidoAlteracao = $idPedidoAlteracao ";
        }

        return $sql;
    }

    /*     * **************************************************************************************
      SQL ALTERAR O STATUS DO CAMPO stVerificacao DA TABELA tbPedidoAlteracaoXTipoAlteracao
     * ************************************************************************************** */

    public static function readequacaoAltCampo($idPedido)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                SET stVerificacao = 1
                WHERE idAvaliacaoItemPedidoAlteracao = $idPedido
                AND stAtivo = 0 ";
        return $sql;
    }

    public static function dadosAgentesOrgaoA($idorgao)
    {
        //sis_codigo = 21 (Trata-se do c�digo do SalicWeb)
        //gru_codigo = 129 (C�digo de T�cnico de acompanhamento)

        $sql = "SELECT a.usu_codigo, a.usu_nome, a.gru_nome AS Perfil, b.idAgente, a.gru_codigo AS idVerificacao
                FROM Tabelas..vwUsuariosOrgaosGrupos a
                INNER JOIN AGENTES.dbo.Agentes AS b ON a.usu_identificacao = b.CNPJCPF
                WHERE sis_codigo = 21 and uog_orgao = $idorgao
                 AND gru_codigo in (121) AND a.uog_status = 1 
                ORDER BY usu_nome ";

        return $sql;
    }

    public static function dadosAgentesOrgaoB($idorgao)
    {
        //sis_codigo = 21 (Trata-se do c�digo do SalicWeb)
        //gru_codigo = 93 (C�digo de Coordenador de Parecerista)

        $sql = "SELECT a.usu_codigo, a.usu_nome, a.gru_nome AS Perfil, b.idAgente, a.gru_codigo AS idVerificacao
                FROM Tabelas..vwUsuariosOrgaosGrupos a
                INNER JOIN AGENTES.dbo.Agentes AS b ON a.usu_identificacao = b.CNPJCPF
                WHERE sis_codigo = 21 and uog_orgao = $idorgao
                AND gru_codigo IN (121, 93) AND a.uog_status = 1 
                ORDER BY gru_codigo ";


        return $sql;
    }

    public static function dadosAgentesPerfil($idagente)
    {
        $sql = "SELECT DISTINCT
                    vuog.usu_codigo,
                    vuog.usu_nome,
                    d.idVerificacao,
                    d.Descricao AS Perfil,
                    ag.idAgente
                FROM TABELAS.dbo.vwUsuariosOrgaosGrupos vuog
                LEFT JOIN AGENTES.dbo.Agentes ag on (ag.CNPJCPF = vuog.usu_identificacao)
                LEFT JOIN AGENTES.dbo.Nomes AS b ON ag.idAgente = b.idAgente  and b.TipoNome = 18
                LEFT JOIN AGENTES.dbo.Visao AS c ON ag.idAgente = c.idAgente
                LEFT JOIN AGENTES.dbo.Verificacao AS d ON c.Visao = d.idVerificacao AND d.IdTipo = 16
                WHERE ag.idAgente = $idagente ";

        return $sql;
    }

    public static function retornaSQLlista($sqlDesejado, $idOrgao)
    {
        if ($sqlDesejado == "listasDeEncaminhamento") {
            $sql = "SELECT a.usu_codigo, a.usu_nome, a.gru_nome AS Perfil, b.idAgente, a.gru_codigo AS idVerificacao
                    FROM Tabelas..vwUsuariosOrgaosGrupos a
                    INNER JOIN AGENTES.dbo.Agentes AS b ON a.usu_identificacao = b.CNPJCPF
                    WHERE sis_codigo = 21 and uog_orgao = $idOrgao
                    AND gru_codigo = 94 AND a.uog_status = 1 
                    ORDER BY usu_nome ";
        }

        if ($sqlDesejado == "listasDeEntidadesVinculadas") {
            $sql = "SELECT * FROM SAC.dbo.Orgaos
                    WHERE Status = 0 ";

            if (!empty($idOrgao)) :
                $sql.= " AND Codigo = '$idOrgao'";
            endif;

            $sql.= "ORDER BY Sigla";
                   
            //$sql = "select org_codigo as Codigo,org_sigla as Sigla from Orgaos ORDER BY org_sigla";
            //$sql = "SELECT DISTINCT uog_orgao as Codigo,org_siglaautorizado as Sigla FROM vwUsuariosOrgaosGrupos ORDER BY org_siglaautorizado";
        }
        if ($sqlDesejado == "listasDeEntidadesVinculadasPar") {
            $sql = "SELECT * FROM SAC.dbo.Orgaos 
                    WHERE Vinculo = 1 AND Status = 0 AND idSecretaria IS NOT NULL 
                    ORDER BY Sigla";
        }
        if ($sqlDesejado == "listasDeEntidadesVinculadasEspecificas") {
            $sql = "SELECT * FROM SAC.dbo.Orgaos
                    WHERE Vinculo = 1 AND Status = 0 AND idSecretaria IS NOT NULL 
                          AND Codigo in ({$idOrgao})
                    ORDER BY Sigla DESC";
            //$sql = "select org_codigo as Codigo,org_sigla as Sigla from Orgaos ORDER BY org_sigla";
            //$sql = "SELECT DISTINCT uog_orgao as Codigo,org_siglaautorizado as Sigla FROM vwUsuariosOrgaosGrupos ORDER BY org_siglaautorizado";
        }

        return $sql;
    }

    /*     * ***********************************************************
     * VERIFICA O PERFIL DO AGENTE ACIONADO
     * ************************************************************ */

    public static function retornaSQLPerfilAgente()
    {
        $sql = "";

        return $sql;
    }

    /*     * *********************************************************** */

    //SQL PARA ENCAMINHAR DE COORDENADOR DE ACOMPANHAMENTO PARA COORDENADOR DE PARECERISTA
    public static function retornaSQLencaminhar($sqlDesejado, $ID_PRONAC, $idPedidoAlteracao, $tpAlteracaoProjeto, $justificativa, $Orgao, $idAgenteReceber)
    {
        if ($sqlDesejado == "sqlAlteraVariavelAltProj") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto
                    SET siVerificacao = 1
                    WHERE idPedidoAlteracao = $idPedidoAlteracao
                    AND IdPRONAC = $ID_PRONAC ";
        }

        if ($sqlDesejado == "sqlAlteraVariavelTipoAlt") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
                    SET stVerificacao = 1
                    WHERE idPedidoAlteracao = $idPedidoAlteracao
                    AND tpAlteracaoProjeto = $tpAlteracaoProjeto ";
        }

        if ($sqlDesejado == "sqlCoordAcompEncaminhar") {
            $sql = "INSERT BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                    VALUES ('$idPedidoAlteracao','$tpAlteracaoProjeto','','','','AG','')";
        }

        if ($sqlDesejado == "sqlRecuperarRegistro") {
            $sql = "SELECT TOP 1 idAvaliacaoItemPedidoAlteracao
                    FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                    WHERE idPedidoAlteracao = '$idPedidoAlteracao'
                    AND tpAlteracaoProjeto = '$tpAlteracaoProjeto'
                    ORDER BY 1 DESC ";
        }
        return $sql;
    }

    //SQL PARA GERAR UMA A��O NA TABELA tbAcaoAvaliacaoItemPedidoAlteracao
    public static function retornaSQLtbAcao($idAvaliacaoItemPedidoAlteracao, $justificativa, $tipoAg, $Orgao, $idAgenteReceber, $idAgenteRemente, $idPerfilRemetente)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                VALUES ('$idAvaliacaoItemPedidoAlteracao','$idAgenteReceber','" . $justificativa . "','$tipoAg','$Orgao','0','0',GETDATE(),'$idAgenteRemente','$idPerfilRemetente')";
        return $sql;
    }

    /*     * ***********************************************************************
     * //SQL PARA ENCAMINHAR DE COORDENADOR PARECERISTA PARA PARECERISTA
     * *********************************************************************** */

    public static function retornaSQLencaminharParecerista($sqlDesejado, $idAvaliacaoItemPedidoAlteracao, $idAcao, $stAcao, $justificativa, $agenteNovo, $Orgao, $idAgenteRemetente, $idPerfilRemetente)
    {
        if ($sqlDesejado == "sqlAlteraVariavel") {
            $sql = "UPDATE BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                    SET stAtivo = 1
                    WHERE idAcaoAvaliacaoItemPedidoAlteracao = $idAcao
                    AND idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao ";
        }


        if ($sqlDesejado == "sqlCoordPareceristaEncaminhar") {
            $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                    VALUES ('$idAvaliacaoItemPedidoAlteracao','$agenteNovo','" . $justificativa . "','1','$Orgao','0','0',GETDATE(), '$idAgenteRemetente', '$idPerfilRemetente')";
        }

        return $sql;
    }

    public static function retornaSQLReencaminharPar($idPedidoAlteracao, $tpAlteracaoProjeto)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
                SET stVerificacao = 1
                WHERE idPedidoAlteracao = $idPedidoAlteracao
                AND tpAlteracaoProjeto = $tpAlteracaoProjeto ";
        return $sql;
    }

    public static function reencaminharPar($idPedidoAlteracao, $tpAlteracaoProjeto)
    {
        $sql = "INSERT BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                VALUES ('$idPedidoAlteracao','$tpAlteracaoProjeto','','','','AG','')";
        return $sql;
    }

    public static function reencaminharPar1($idAcao)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                SET stAtivo = 1
                WHERE idAcaoAvaliacaoItemPedidoAlteracao = $idAcao ";
        return $sql;
    }

    public static function reencaminharPar2($idPedidoAlteracao, $tpAlteracaoProjeto)
    {
        $sql = "SELECT TOP 1 idAvaliacaoItemPedidoAlteracao
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                WHERE idPedidoAlteracao = $idPedidoAlteracao
                AND tpAlteracaoProjeto = $tpAlteracaoProjeto
                ORDER BY 1 DESC";
        return $sql;
    }

    public static function verificaProdutos($idPedidoAlteracao)
    {
        $sql = "SELECT * FROM SAC.dbo.tbplanodistribuicao WHERE idPedidoAlteracao = $idPedidoAlteracao ";
        return $sql;
    }

    public static function verificaPedidoAlteracaoProjetoProduto($idPronac)
    {
        $sql = "select * from BDCORPORATIVO.scSac.tbPedidoAlteracaoProjeto tpa inner join
        BDCORPORATIVO.scSac.tbPedidoAlteracaoXTipoAlteracao paxta on tpa.idPedidoAlteracao = paxta.idPedidoAlteracao WHERE idPronac = $idPronac AND tpAlteracaoProjeto = 7";
        return $sql;
    }


    public static function reencaminharPar3($idAvaliacaoItemPedidoAlteracao, $idAgente, $justificativa, $Orgao, $idAgenteRemetente, $idPerfilRemetente)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                VALUES ('$idAvaliacaoItemPedidoAlteracao','$idAgente','" . $justificativa . "','1','$Orgao','0','0',GETDATE(), $idAgenteRemetente, $idPerfilRemetente)";
        return $sql;
    }

    public static function reencaminharPar4()
    {
        $sql = "SELECT TOP 1 idAvaliacaoItemPedidoAlteracao
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                WHERE idPedidoAlteracao = $idPedidoAlteracao
                AND tpAlteracaoProjeto = $tpAlteracaoProjeto
                ORDER BY 1 DESC ";
        return $sql;
    }

    public static function reencaminharPar5($idAvaliacaoItemPedidoAlteracao, $idAgenteLogado, $justificativa, $Orgao, $idPerfil, $idAgente, $idGrupo)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                VALUES ('$idAvaliacaoItemPedidoAlteracao','$idAgenteLogado','" . $justificativa . "','$idPerfil','$Orgao','0','0',GETDATE(), '$idAgente', '$idGrupo')";
        return $sql;
    }

    /*     * ***********************************************************************
      //SQLs PARA FINALIZA��O DA READEQUA��O DE PRODUTO
     * *********************************************************************** */

    public static function retornaSQLfinalizarPar($idPedidoAlteracao, $situacao, $justificativa)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                SET stAvaliacaoItemPedidoAlteracao = '".$situacao."', dtFimAvaliacao = GETDATE(), dsAvaliacao = '".$justificativa."'
                WHERE idPedidoAlteracao = $idPedidoAlteracao
                AND dtFimAvaliacao = '1900-01-01 00:00:00.000' ";
        return $sql;
    }

    public static function retornaSQLfinalizarPar2($idPedidoAlteracao)
    {
        $sql = "SELECT a.idAvaliacaoItemPedidoAlteracao, a.idAgenteAvaliador, idOrgao
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao AS a
                INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao AS b ON a.idAvaliacaoItemPedidoAlteracao = b.idAvaliacaoItemPedidoAlteracao
                WHERE idPedidoAlteracao = $idPedidoAlteracao
                AND stAtivo = 0 ";
        return $sql;
    }

    public static function retornaSQLfinalizarPar3($idAvaliacaoItemPedidoAlteracao)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                SET stAtivo = 1
                WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao
                AND stAtivo = 0 ";
        return $sql;
    }

    public static function retornaSQLfinalizarPar4($idAvaliacaoItemPedidoAlteracao, $idAgenteAvaliador, $idOrgao, $idAgenteRemetente, $idGrupoRemetente)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                VALUES ('$idAvaliacaoItemPedidoAlteracao','$idAgenteAvaliador','','3','$idOrgao','0','2',GETDATE(), $idAgenteRemetente, $idGrupoRemetente) ";
        return $sql;
    }

    //serve somente para o item de custo (IC)
    public static function retornaSQLfinalizarPar4IC($idAvaliacaoItemPedidoAlteracao, $idAgenteAvaliador, $idOrgao, $idAgenteRemetente, $idGrupoRemetente)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                VALUES ('$idAvaliacaoItemPedidoAlteracao','$idAgenteAvaliador','','2','$idOrgao','0','2',GETDATE(), $idAgenteRemetente, $idGrupoRemetente) ";
        return $sql;
    }

    public static function retornaSQLfinalizarParST($idAvaliacaoItemPedidoAlteracao)
    {
        $sql = "select * from BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao where idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao ";
        return $sql;
    }

    public static function retornaSQLfinalizarParST2($idPedidoAlteracao, $tpAlteracaoProjeto)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
                SET stVerificacao = 2
                WHERE idPedidoAlteracao = $idPedidoAlteracao
                AND tpAlteracaoProjeto = $tpAlteracaoProjeto ";
        return $sql;
    }

    /*     * ***********************************************************************
     * SQL PARA LISTAR O HIST�RICO
     * *********************************************************************** */

    public static function retornaSQLHistorico($sqlDesejado)
    {
        if ($sqlDesejado == "sqlListarHistorico") {
            $sql = "SELECT distinct e.IdPRONAC, e.NomeProjeto, b.idPedidoAlteracao, a.dtEncaminhamento, a.idOrgao, f.Sigla, a.idTipoAgente, a.dsObservacao, stAtivo, a.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao, b.tpAlteracaoProjeto
                    FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao AS b ON a.idAvaliacaoItemPedidoAlteracao = b.idAvaliacaoItemPedidoAlteracaO
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao AS c ON b.idPedidoAlteracao = c.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto AS d ON c.idPedidoAlteracao = d.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos AS e ON d.IdPRONAC = e.IdPRONAC
                    INNER JOIN SAC.dbo.Orgaos AS f ON a.idOrgao = f.Codigo ";
        }

        if ($sqlDesejado == "sqlListarHistoricoUnico") {
            $sql = "SELECT distinct e.IdPRONAC, e.NomeProjeto, b.idPedidoAlteracao, a.dtEncaminhamento, a.idOrgao, f.Sigla, a.idTipoAgente, a.dsObservacao, c.tpAlteracaoProjeto, stAtivo, a.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao, b.tpAlteracaoProjeto, b.idAvaliacaoItemPedidoAlteracao
                    FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao AS b ON a.idAvaliacaoItemPedidoAlteracao = b.idAvaliacaoItemPedidoAlteracaO
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao AS c ON b.idPedidoAlteracao = c.idPedidoAlteracao and c.tpAlteracaoProjeto = b.tpAlteracaoProjeto
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto AS d ON c.idPedidoAlteracao = d.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos AS e ON d.IdPRONAC = e.IdPRONAC
                    INNER JOIN SAC.dbo.Orgaos AS f ON a.idOrgao = f.Codigo
                    WHERE stAtivo = 0 ";
        }
        return $sql;
    }

    public static function retornaSQLHistoricoLista($idavaliacao)
    {
        $sql = "
        SELECT *, CAST(dsObservacao AS text) as dsObservacao FROM
            (
        SELECT distinct e.IdPRONAC, e.NomeProjeto, b.idPedidoAlteracao, a.dtEncaminhamento, a.idOrgao, f.Sigla, a.idTipoAgente, a.dsObservacao, stAtivo, a.idAcaoAvaliacaoItemPedidoAlteracao AS idAcao, b.tpAlteracaoProjeto, i.usu_nome AS Remetente, g.gru_nome AS perfilRemetente, k.usu_nome AS Destinatario, l.dsTipoAgente AS perfilDestinatario
                FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao AS a
                INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao AS b ON a.idAvaliacaoItemPedidoAlteracao = b.idAvaliacaoItemPedidoAlteracaO
                INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao AS c ON b.idPedidoAlteracao = c.idPedidoAlteracao
                INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto AS d ON c.idPedidoAlteracao = d.idPedidoAlteracao
                INNER JOIN SAC.dbo.Projetos AS e ON d.IdPRONAC = e.IdPRONAC
                INNER JOIN SAC.dbo.Orgaos AS f ON a.idOrgao = f.Codigo
                INNER JOIN TABELAS.dbo.Grupos AS g ON g.gru_codigo = a.idPerfilRemetente
                INNER JOIN AGENTES.dbo.Agentes AS h ON h.idAgente = a.idAgenteRemetente
                INNER JOIN TABELAS.dbo.Usuarios AS i ON i.usu_identificacao = h.CNPJCPF
                LEFT JOIN AGENTES.dbo.Agentes AS j ON j.idAgente = a.idAgenteAcionado
                LEFT JOIN TABELAS.dbo.Usuarios AS k ON k.usu_identificacao = j.CNPJCPF
                INNER JOIN BDCORPORATIVO.scSAC.tbTipoAgente AS l ON l.idTipoAgente = a.idTipoAgente
                where b.idAvaliacaoItemPedidoAlteracao = $idavaliacao ) as minhaTabela";
        return $sql;
    }

    /*     * ***********************************************************************
     * SQL PARA DEVOLVER MINC (TELA DE COORDENADOR DE PARECERISTA)
     * *********************************************************************** */

    public static function retornaSQLdevolverMinc($idAcao)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                SET stAtivo = 1
                WHERE idAcaoAvaliacaoItemPedidoAlteracao = $idAcao ";
        return $sql;
    }

    public static function retornaSQLdevolverMinc2($idAcao)
    {
        $sql = "SELECT idAvaliacaoItemPedidoAlteracao, idOrgao
                FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                WHERE idAcaoAvaliacaoItemPedidoAlteracao = $idAcao ";
        return $sql;
    }

    public static function retornaSQLdevolverMinc3($id)
    {
        $sql = "SELECT idPedidoAlteracao, tpAlteracaoProjeto
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                WHERE idAvaliacaoItemPedidoAlteracao = $id ";
        return $sql;
    }

    public static function retornaSQLdevolverMinc4($idPedidoAlt, $tpAlt)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
                SET stVerificacao = 3
                WHERE idPedidoAlteracao = $idPedidoAlt
                AND tpAlteracaoProjeto = $tpAlt ";
        return $sql;
    }

    public static function retornaSQLdevolverMinc5($id, $idOrgao, $idAgenteRemetente, $idPerfilRemetente)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                VALUES ('$id','','','3','$idOrgao','0','3',GETDATE(),'$idAgenteRemetente','$idPerfilRemetente') ";
        return $sql;
    }

    /*     * *****************************************************************
     * SQL PARA FINALIZAR GERAL (TELA DE COORDENADOR DE ACOMPANHAMENTO)
     * ****************************************************************** */

    public static function retornaSQLfinalizaGeral($idAcao)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                SET stAtivo = 1
                WHERE idAcaoAvaliacaoItemPedidoAlteracao = $idAcao ";
        return $sql;
    }

    public static function retornaSQLfinalizaGeral2($idAcao)
    {
        $sql = "SELECT idAvaliacaoItemPedidoAlteracao, idOrgao, dsObservacao
                FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                WHERE idAcaoAvaliacaoItemPedidoAlteracao = $idAcao ";
        return $sql;
    }

    public static function retornaSQLfinalizaGeral3($id)
    {
        $sql = "SELECT idPedidoAlteracao, tpAlteracaoProjeto, stAvaliacaoItemPedidoAlteracao, idAgenteAvaliador 
                FROM BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao
                WHERE idAvaliacaoItemPedidoAlteracao = $id ";
        return $sql;
    }

    public static function retornaSQLfinalizaGeral4($idPedidoAlt, $tpAlt)
    {
        $sql = "UPDATE BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
                SET stVerificacao = 4
                WHERE idPedidoAlteracao = $idPedidoAlt
                AND tpAlteracaoProjeto = $tpAlt ";
        return $sql;
    }

    public static function retornaSQLfinalizaGeral5($id, $idOrgao, $idAgenteRemetente, $idPerfilRemetente)
    {
        $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao
                VALUES ('$id','','','4','$idOrgao','1','4',GETDATE(),'$idAgenteRemetente','$idPerfilRemetente') ";
        return $sql;
    }

    public static function retornaSQLfinalizaGeral6($idPedidoAlt)
    {
        $sql = "SELECT IdPRONAC
                FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto
                WHERE idPedidoAlteracao = $idPedidoAlt ";
        return $sql;
    }

    //Alterar o TipoParecer da Tabela SAC.dbo.Parecer
    public static function AlteraTipoParecer($idPronac)
    {
        $sql = " update SAC.dbo.Parecer set TipoParecer = 2 where idPRONAC = $idPronac";
        return $sql;
    }




    // Atualiza os dados originais ap�s a finaliza��o do coordenador de acompanhamento
    public static function finalizacaoCoordAcomp($tabela, $campo, $dados, $where, $id)
    {
        $sql = "UPDATE ".$tabela."
                SET ".$campo." = '".$dados."'
                WHERE ".$where." = $id ";
        return $sql;
    }

    public static function ConsultaFinalPropPedag($id_Pronac)
    {
        $sql = "SELECT DISTINCT b.IdPRONAC,
                        d.idPreProjeto,
                        c.AnoProjeto+c.Sequencial AS PRONAC,
                        c.NomeProjeto AS NomeProjeto,
                        c.CgcCpf AS CNPJCPF,
                        f.Nome AS proponente,
                        f.CgcCpf,
                        d.EstrategiadeExecucao,
                        d.EspecificacaoTecnica,
                        a.dsObjetivos AS Solicitacao,
                        a.dsJustificativa AS Justificativa,
                        a.dsEstrategiaExecucao,
                        a.dsEspecificacaoTecnica,
                        g.stAvaliacaoItemPedidoAlteracao,
                        i.dsJustificativa

                    FROM SAC.dbo.tbProposta AS a
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoProjeto             AS b ON b.idPedidoAlteracao = a.idPedidoAlteracao
                    INNER JOIN SAC.dbo.Projetos                                         AS c ON c.IdPRONAC = b.IdPRONAC
                    INNER JOIN SAC.dbo.PreProjeto                                       AS d ON d.idPreProjeto = c.idProjeto
                    INNER JOIN AGENTES.dbo.Agentes                                      AS e ON e.idAgente = d.idAgente
                    INNER JOIN SAC.dbo.vProponenteProjetos                              AS f ON c.CgcCpf = f.CgcCpf
                    INNER JOIN BDCORPORATIVO.scSAC.tbAvaliacaoItemPedidoAlteracao       AS g ON g.idPedidoAlteracao = b.idPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao   AS h ON g.idAvaliacaoItemPedidoAlteracao = h.idAvaliacaoItemPedidoAlteracao
                    INNER JOIN BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao	AS i ON i.idPedidoAlteracao = g.idPedidoAlteracao

                    WHERE b.IdPRONAC = ".$id_Pronac."
                    AND h.stAtivo = 1
                    AND g.tpAlteracaoProjeto = 6 ";
        return $sql;
    }
   

    public static function buscarJustificativaFinalParecerista($idAvaliacaoItemPedidoAlteracao)
    {
        $sql = "SELECT CAST(dsObservacao AS TEXT) AS dsObservacao, idAgenteRemetente  
				FROM BDCORPORATIVO.scSAC.tbAcaoAvaliacaoItemPedidoAlteracao 
				WHERE idAvaliacaoItemPedidoAlteracao = $idAvaliacaoItemPedidoAlteracao AND idTipoAgente = 2 AND stVerificacao = 2";
        
        return $sql;
    }
}
