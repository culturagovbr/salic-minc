<?php
class SolicitarAlteracaoDAO extends Zend_Db_Table
{
    public function buscarProjetos($CgcCpf)
    {
        $sql = "SELECT Projetos.IdPRONAC,
					Projetos.idProjeto, 
					Projetos.NomeProjeto,
					CONVERT(CHAR(10), Projetos.DtProtocolo,103) + ' ' + CONVERT(CHAR(8), Projetos.DtProtocolo,108) AS DtProtocolo,
					CONVERT(CHAR(10), Projetos.DtAnalise,103) + ' ' + CONVERT(CHAR(8), Projetos.DtAnalise,108) AS DtAnalise,
					Situacao.Descricao
				FROM Sac.dbo.Projetos as  Projetos INNER JOIN
					Sac.dbo.Situacao  as Situacao ON Projetos.Situacao = Situacao.Codigo
				WHERE (Situacao.Codigo='E10'OR Situacao.Codigo='E11'OR Situacao.Codigo='E12'OR Situacao.Codigo='E13'OR Situacao.Codigo='E14'OR Situacao.Codigo='E16')
					AND Projetos.idProjeto is not NULL
					AND Projetos.CgcCpf = '$CgcCpf'";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarProjetos()



    public function compararInserirAbrangencia($idProjeto, $idPedidoAlteracao)
    {
        /*$sql = "insert into sac.dbo.tbAbrangencia(tpAbrangencia,idPais,idUF,idMunicipioIBGE,tpAcao,idAbrangenciaAntiga,idPedidoAlteracao,dtRegistro)
             select 'SA',a.idPais,a.idUF,a.idMunicipioIBGE,'N',a.idAbrangencia,'$idPedidoAlteracao',GETDATE()
             from SAC.dbo.Abrangencia a
             inner join SAC.dbo.Projetos pr on pr.idProjeto = a.idProjeto
             where
             (select COUNT(*) from SAC.dbo.tbAbrangencia ab where a.idAbrangencia = ab.idAbrangenciaAntiga) = 0
             and pr.idProjeto = $idProjeto";*/
        $sql = "insert into sac.dbo.tbAbrangencia(tpAbrangencia,idPais,idUF,idMunicipioIBGE,tpAcao,idAbrangenciaAntiga,idPedidoAlteracao,dtRegistro)
        		SELECT 'SA',a.idPais,a.idUF,a.idMunicipioIBGE,'N',a.idAbrangencia,'$idPedidoAlteracao',GETDATE()
            FROM SAC.dbo.Abrangencia a
            where a.idProjeto=$idProjeto AND a.stAbrangencia = 1";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    } // fecha m�todo compararInserirAbrangencia()



    public function buscartbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $status)
    {
        $sql = "SELECT idPedidoAlteracaoXTipoAlteracao
				,idPedidoAlteracao
				,tpAlteracaoProjeto
				,CAST(dsJustificativa AS TEXT) AS dsJustificativa
				,stVerificacao
			FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
			WHERE idPedidoAlteracao    = $idPedidoAlteracao 
				AND tpAlteracaoProjeto = $status";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscartbPedidoAlteracaoXTipoAlteracao()

    public function buscartbPedidoAlteracaoXTipoAlteracaoGeral($idPedidoAlteracao)
    {
        $sql = "SELECT idPedidoAlteracaoXTipoAlteracao
				,idPedidoAlteracao
				,tpAlteracaoProjeto
				,CAST(dsJustificativa AS TEXT) AS dsJustificativa
				,stVerificacao
			FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
			WHERE idPedidoAlteracao    = $idPedidoAlteracao ORDER BY idPedidoAlteracaoXTipoAlteracao desc
				";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscartbPedidoAlteracaoXTipoAlteracao()


    public function buscartbPedidoAlteracaoXTipoAlteracaoGeralJustificativa($idPedidoAlteracao)
    {
        $sql = "SELECT idPedidoAlteracaoXTipoAlteracao
				,idPedidoAlteracao
				,tpAlteracaoProjeto
				,CAST(dsJustificativa AS TEXT) AS dsJustificativa
				,stVerificacao
			FROM BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao
			WHERE idPedidoAlteracao    = $idPedidoAlteracao AND tpAlteracaoProjeto in (1,2) ORDER BY idPedidoAlteracaoXTipoAlteracao desc
				";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscartbPedidoAlteracaoXTipoAlteracao()


    public function buscarTrocaProponente($idPedidoAlteracao)
    {
        $sql = "select * from  BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente WHERE idPedidoAlteracao = $idPedidoAlteracao";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscartbPedidoAlteracaoXTipoAlteracao()



    public function updatetbPedidoAlteracaoXTipoAlteracao($idPedidoAlteracao, $dsJustificativa, $status)
    {
        $sql = "update BDCORPORATIVO.scSAC.tbPedidoAlteracaoXTipoAlteracao set dsJustificativa = '$dsJustificativa'
            where idPedidoAlteracao = $idPedidoAlteracao and tpAlteracaoProjeto = $status";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo updatetbPedidoAlteracaoXTipoAlteracao()



    public function buscatbProposta($idPedidoAlteracao)
    {
        $sql = "SELECT IdProposta
					,tpProposta
					,dtProposta
					,CAST(nmProjeto AS TEXT) AS nmProjeto
					,cdMecanismo
					,nrAgenciaBancaria
					,stAreaAbrangencia
					,dtInicioExecucao
					,dtFimExecucao
					,nrAtoTombamento
					,dtAtoTombamento
					,cdEsferaTombamento
					,CAST(dsResumoProjeto AS TEXT) AS dsResumoProjeto
					,CAST(dsObjetivos AS TEXT) AS dsObjetivos
					,CAST(dsJustificativa AS TEXT) AS dsJustificativa
					,CAST(dsAcessibilidade AS TEXT) AS dsAcessibilidade
					,CAST(dsDemocratizacaoAcesso AS TEXT) AS dsDemocratizacaoAcesso
					,CAST(dsEtapaTrabalho AS TEXT) AS dsEtapaTrabalho
					,CAST(dsFichaTecnica AS TEXT) AS dsFichaTecnica
					,CAST(dsSinopse AS TEXT) AS dsSinopse
					,CAST(dsImpactoAmbiental AS TEXT) AS dsImpactoAmbiental
					,CAST(dsEspecificacaoTecnica AS TEXT) AS dsEspecificacaoTecnica
					,CAST(dsEstrategiaExecucao AS TEXT) AS dsEstrategiaExecucao
					,dtAceite
					,dtArquivamento
					,stEstado
					,stDataFixa
					,stPlanoAnual
					,stTipoDemanda
					,idPedidoAlteracao
				FROM SAC.dbo.tbProposta 
				WHERE idPedidoAlteracao = $idPedidoAlteracao";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscatbProposta()



    public function inserttbProposta($idPedidoAlteracao, $coluna, $value)
    {
        $sql = "insert into Sac.dbo.tbProposta (tpProposta,dtProposta,$coluna,idPedidoAlteracao)
              values ('SA',GETDATE(),'$value',$idPedidoAlteracao)";
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo inserttbProposta()



    public function updatetbProposta($idPedidoAlteracao, $coluna, $value)
    {
        $sql = "update Sac.dbo.tbProposta set $coluna = '$value'
        where idPedidoAlteracao = $idPedidoAlteracao";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo updatetbProposta()



    public function buscarProposta2($idPedidoAlteracao)
    {
        $sql = "SELECT CAST(dsEspecificacaoTecnica AS TEXT) AS dsEspecificacaoTecnica 
				FROM SAC.dbo.tbProposta 
				WHERE idPedidoAlteracao = $idPedidoAlteracao";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarProposta2()



    public function buscarNomeProposta($idPedidoAlteracao)
    {
        $sql = "select nmProjeto from SAC.dbo.tbProposta where idPedidoAlteracao = $idPedidoAlteracao and nmProjeto IS not NULL";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarNomeProposta()



    public function buscarRazaoSocial($idPedidoAlteracao)
    {
        $sql = "select * from BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente where idPedidoAlteracao = $idPedidoAlteracao";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscarRazaoSocial()



    public function insertRazaoSocial($idPedidoAlteracao, $CNPJCPF=null, $razaosocial)
    {
        $sql = "insert into BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente (idPedidoAlteracao, nrCNPJCPF, nmProponente)
        values ('$idPedidoAlteracao', '$CNPJCPF', '$razaosocial');";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo insertRazaoSocial()



    public function updateRazaoSocial($idPedidoAlteracao, $CNPJCPF=null, $razaosocial)
    {
        $sql = "update BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente set nmProponente = '$razaosocial', nrCNPJCPF = '$CNPJCPF' 
        where idPedidoAlteracao = $idPedidoAlteracao";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo updateRazaoSocial()



    public function buscaNomeProponente($idPedidoAlteracao)
    {
        $sql = "select * from BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente
                where idPedidoAlteracao = $idPedidoAlteracao";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo buscaNomeProponente()



    public function updateNomeProponente($idPedidoAlteracao, $CNPJCPF, $Proponente)
    {
        $sql = "update BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente set nrCNPJCPF = '$CNPJCPF',nmProponente = '$Proponente'
        where idPedidoAlteracao = $idPedidoAlteracao";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo updateNomeProponente()



    public function insertNomeProponente($idPedidoAlteracao, $CNPJCPF, $Proponente)
    {
        $sql = "insert into BDCORPORATIVO.scSAC.tbAlteracaoNomeProponente (idPedidoAlteracao,nrCNPJCPF,nmProponente)
                values ($idPedidoAlteracao,'$CNPJCPF','$Proponente')";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo insertNomeProponente()



    public function insertArquivo($idArquivo, $idPedidoAlteracao, $status)
    {
        $sql = "insert into BDCORPORATIVO.scSAC.tbPedidoAltProjetoXArquivo (idArquivo,idPedidoAlteracao,tpAlteracaoProjeto)
                values ($idArquivo,$idPedidoAlteracao,$status)";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo insertArquivo()



    public function buscarArquivo($idPedidoAlteracao, $status)
    {
        $sql = "select arquivo.nmArquivo,arquivo.idArquivo from BDCORPORATIVO.scSAC.tbPedidoAltProjetoXArquivo as pedido
                inner join BDCORPORATIVO.scCorp.tbArquivo as arquivo
                on pedido.idArquivo = arquivo.idArquivo
                where idPedidoAlteracao = $idPedidoAlteracao  and pedido.tpAlteracaoProjeto = $status ";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo insertArquivo()

    public function buscarArquivoTrocaNomeProponente($idPedidoAlteracao)
    {
        $sql = "select arquivo.nmArquivo,arquivo.idArquivo from BDCORPORATIVO.scSAC.tbPedidoAltProjetoXArquivo as pedido
                inner join BDCORPORATIVO.scCorp.tbArquivo as arquivo
                on pedido.idArquivo = arquivo.idArquivo
                where idPedidoAlteracao = $idPedidoAlteracao  and pedido.tpAlteracaoProjeto in (1,2)";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    } // fecha m�todo insertArquivo()



    /**
     * M�todo para excluir o arquivo fisicamente
     * @access public
     * @static
     * @param integer $idPedidoAlteracao
     * @param integer $idArquivo
     * @return bool
     */
    public static function excluirArquivo($idPedidoAlteracao, $idArquivo)
    {
        $sql = "DELETE FROM BDCORPORATIVO.scSAC.tbPedidoAltProjetoXArquivo WHERE idPedidoAlteracao = $idPedidoAlteracao AND idArquivo = $idArquivo";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $excluir = $db->fetchAll($sql);

        $sql = "DELETE FROM BDCORPORATIVO.scCorp.tbArquivoImagem WHERE idArquivo = $idArquivo";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $excluir = $db->fetchAll($sql);

        $sql = "DELETE FROM BDCORPORATIVO.scCorp.tbArquivo WHERE idArquivo = $idArquivo";
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $excluir = $db->fetchAll($sql);
    } // fecha m�todo excluirArquivo()



    /**
     * M�todo para verificar se o usuario existe na tabela Interessados e na tabela Agentes
     * @access public
     * @static
     * @param string $CNPJCPF
     * @return bool
     */
    public static function verificarInteressadosAgentes($CNPJCPF)
    {
        $sqlInteressados = "SELECT CgcCpf FROM SAC.dbo.Interessado WHERE CgcCpf = '$CNPJCPF'";
        $sqlAgentes      = "SELECT CNPJCPF FROM AGENTES.dbo.Agentes WHERE CNPJCPF = '$CNPJCPF'";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        if ($db->fetchAll($sqlInteressados) || $db->fetchAll($sqlAgentes)) {
            return true;
        } else {
            return false;
        }
    } // fecha m�todo verificarInteressadosAgentes()



    public static function excluirArquivoDuplicado($idPedidoAlteracao, $Pais, $UF, $Cidade)
    {
        $sql = " DELETE FROM SAC.dbo.tbAbrangencia WHERE idPedidoAlteracao = $idPedidoAlteracao AND (tpAcao = 'I' OR tpAcao = 'N') AND idPais = $Pais AND idUF = $UF AND idMunicipioIBGE = $Cidade";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        if ($db->fetchAll($sql)) {
            return true;
        } else {
            return false;
        }
    } // fecha fun��o excluirArquivoDuplicado()



    public static function alterarJustificativaPrimeiroArquivo($idPedidoAlteracao, $Pais, $UF, $Cidade, $dsJustificativaExclusao)
    {
        $sql = "UPDATE SAC.dbo.tbAbrangencia SET dsExclusao='".$dsJustificativaExclusao."' WHERE idPedidoAlteracao = $idPedidoAlteracao AND tpAcao = 'E' AND idPais = $Pais AND idUF = $UF AND idMunicipioIBGE = $Cidade";

        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        if ($db->fetchAll($sql)) {
            return true;
        } else {
            return false;
        }
    } // fecha fun��o excluirArquivoDuplicado()
} // fecha class
