<?php
class Proposta_Model_AnalisarPropostaDAO extends MinC_Db_Model
{
    /**
     * @todo deletar depois
     */
    public function buscarDocumentoOpcao($idOpcao)
    {
        die('enviado para o dbtable documentosexigidos');
        $sql = "select codigo, descricao from SAC.dbo.vwDocumentosExigidosApresentacaoProposta where opcao = {$idOpcao} order by descricao ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarGeral($idPreProjeto)
    {
        $sql = "SELECT
                    p.idPreProjeto,
                    p.idAgente,
                    p.NomeProjeto,
                    p.AreaAbrangencia,
                    p.Mecanismo,---usar regra do Scrip Case.
                    p.AgenciaBancaria,---label scrip case
                    convert(TEXT, p.ResumoDoProjeto) as ResumoDoProjeto,
                    p.Objetivos,
                    p.Justificativa,
                    convert(varchar(30),p.DtInicioDeExecucao, 103 )+' '+convert(varchar(30),p.DtInicioDeExecucao, 108) as DtInicioDeExecucao,
                    convert(varchar(30),p.DtFinalDeExecucao, 103 )+' '+convert(varchar(30),p.DtFinalDeExecucao, 108 )  as DtFinalDeExecucao,
                    p.NrAtoTombamento,
                    convert(varchar(30),p.DtAtoTombamento, 103 ) as DtAtoTombamento,
                    p.EsferaTombamento,
                    p.Acessibilidade,
                    p.DemocratizacaoDeAcesso,
                    p.EtapaDeTrabalho,
                    p.FichaTecnica,
                    p.Sinopse,
                    p.ImpactoAmbiental,
                    p.EspecificacaoTecnica,
                    p.EstrategiaDeExecucao,
                    p.stDataFixa,
                    p.stPlanoAnual,
                    p.stProposta,
                    p.tpProrrogacao,
                    a.CNPJCPF,
                    agentes.dbo.fnNome(p.idAgente) as NomeAgente,
                    SAC.dbo.fnNomeTecnicoMinc(tbap.idTecnico) as tecnico,
                    en.TipoEndereco,
                    en.TipoLogradouro,
                    en.Logradouro,
                    en.Bairro,
                    en.Numero,
                    en.Complemento,
                    en.Cidade,
                    en.UF,
                    en.CEP,
                    en.Divulgar,
                    ve.Descricao as endereco,
                    ver.Descricao as logradouro,
                    vemail.Descricao as email,
                    vci.Descricao  as agente,
                    vci.Divulgar as divulgarEmail,
                    vdireito.Descricao as direito,
                    vesfera.Descricao as esfera,
                    vpoder.Descricao as poder,
                    vadm.Descricao as Admins,
                    vcd.CNPJCPF as CNPJCPFdigirente,
                    vcd.Nome as nomeAgente,
                    c.Cpf,
                    c.Nome as nomeUsuario,
                    uf.Sigla as SiglaUf,
                    mun.Descricao as NomeCidade,
                    p.idEdital,
                    p.DtArquivamento,
                    p.stEstado,
                    versac.Descricao as ExecucaoImediata
                FROM sac.dbo.PreProjeto p
                    left JOIN agentes.dbo.Agentes a			on p.idAgente = a.idAgente
                    left join agentes.dbo.endereconacional en		on p.idAgente = en.idAgente
                    left join AGENTES.dbo.Verificacao ve		on en.TipoEndereco = ve.idVerificacao
                    left join AGENTES.dbo.Verificacao ver		on en.TipoLogradouro = ver.idVerificacao
                    left join sac.dbo.vCadastrarInternet vci		on p.idAgente = vci.idAgente
                    left join AGENTES.dbo.Verificacao vemail		on vci.TipoInternet = vemail.idVerificacao
                    left join sac.dbo.vwNatureza vna			on p.idAgente = vna.idAgente
                    left join AGENTES.dbo.Verificacao vdireito		on vna.Direito = vdireito.idVerificacao
                    left join AGENTES.dbo.Verificacao vesfera		on vna.Esfera = vesfera.idVerificacao
                    left join AGENTES.dbo.Verificacao vpoder		on vna.Poder = vpoder.idVerificacao
                    left join AGENTES.dbo.Verificacao vadm		on vna.Administracao = vadm.idVerificacao
                    left join sac.dbo.vCadastrarDirigente vcd		on p.idAgente = vcd.idVinculoPrincipal
                    left JOIN ControleDeAcesso.dbo.SGCAcesso c		on p.IdUsuario = c.idUsuario
                    left JOIN SAC.dbo.tbHistoricoEmail tbhe		on p.idPreProjeto = tbhe.idProjeto
                    left JOIN SAC.dbo.tbAvaliacaoProposta tbap		on tbhe.idAvaliacaoProposta = tbap.idAvaliacaoProposta
                    left join AGENTES.dbo.Uf uf                         on uf.idUf = en.UF
                    left join AGENTES.dbo.Municipios mun                on mun.idMunicipioIBGE = en.Cidade
                    left join SAC.dbo.Verificacao versac    on  p.stProposta = versac.idVerificacao
                WHERE idPreProjeto = {$idPreProjeto}";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $db->query('SET TEXTSIZE 2147483647');
        return $db->fetchAll($sql);
    }

    public static function buscarTelefone($idAgente)
    {
        $sql = "SELECT
                    t.TipoTelefone,
                    t.UF,
                    t.DDD,
                    t.Numero,
                    t.Divulgar,
                    v.Descricao,
                    uf.Sigla

                FROM
                    sac.dbo.vCadastrarTelefones t
                        join AGENTES.dbo.Verificacao v on t.TipoTelefone = v.idVerificacao
                     join AGENTES.dbo.UF uf on t.UF = uf.idUF
                WHERE t.idAgente= $idAgente
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarPlanoDeDistribucaoProduto($idPreProjeto)
    {
        $sql = "SELECT
                            idPlanoDistribuicao,
                            idProjeto,
                            idProduto,
                            idPosicaoDaLogo,
                            QtdeProduzida,
                            QtdePatrocinador,QtdeProponente,
                            QtdeOutros,
                            QtdeVendaNormal,
                            QtdeVendaPromocional,
                            PrecoUnitarioNormal,
                            PrecoUnitarioPromocional,
                            QtdeVendaNormal*PrecoUnitarioNormal as ReceitaNormal,
                            QtdeVendaPromocional*PrecoUnitarioPromocional as ReceitaPro,
                            (QtdeVendaNormal*PrecoUnitarioNormal) +
                            (QtdeVendaPromocional*PrecoUnitarioPromocional) as ReceitaPrevista,
                            Usuario,
                            pp.Area,
                            Segmento,
                            stPrincipal,
                            p.Descricao as produto,
                            a.Descricao as area,
                            s.Descricao as segmento,
                            v.Descricao as posicaoLogo
                        FROM
                            sac.dbo.PlanoDistribuicaoProduto pp
                            left join SAC.dbo.Produto p on pp.idProduto = p.Codigo
                            left join SAC.dbo.Area a on pp.Area = a.Codigo
                            left join SAC.dbo.Segmento s on pp.Segmento = s.Codigo
                            left join SAC.dbo.Verificacao v on pp.idPosicaoDaLogo = v.idVerificacao
                        WHERE idProjeto=$idPreProjeto AND pp.stPlanoDistribuicaoProduto = 1
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarPlanilhaOrcamentaria($idPreProjeto)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $sql = "SELECT
                        idPlanilhaProposta,
                        idProjeto,
                        idProduto,
                        idEtapa,
                        idPlanilhaItem,
                        Descricao,
                        Unidade,
                        Quantidade,
                        Ocorrencia,
                        ValorUnitario,
                        QtdeDias,
                         FonteRecurso,
                        UfDespesa,
                        MunicipioDespesa,
                        idUsuario
                    FROM
                        sac.dbo.tbPlanilhaProposta
                    WHERE idProjeto = $idPreProjeto
                    ORDER BY idEtapa,Descricao
                ";
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarFonteDeRecurso($idPreProjeto)
    {
        $sql = "select v.Descricao,
                sum(Quantidade*Ocorrencia*ValorUnitario) as valor

                from sac.dbo.tbPlanilhaProposta p
                inner join sac.dbo.Verificacao v on (p.FonteRecurso = v.idVerificacao)
                where idProjeto=$idPreProjeto
                group by v.Descricao
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarLocalDeRealizacao($idPreProjeto)
    {
        $sql = "SELECT
                CASE a.idPais
                WHEN 0 THEN 'N&atilde;o&eacute; poss&iacute;vel informar o local de realiza&ccedil;&atilde;o do projeto'
                ELSE p.Descricao
                END as Pais,
                u.Descricao as UF,
                m.Descricao as Cidade,
                convert(varchar(30),x.DtInicioDeExecucao, 103 )+' '+convert(varchar(30),x.DtInicioDeExecucao, 108 ) as DtInicioDeExecucao,
                convert(varchar(30),x.DtFinalDeExecucao, 103 ) +' '+convert(varchar(30),x.DtFinalDeExecucao, 108 ) as DtFinalDeExecucao

                FROM  sac.dbo.Abrangencia a
                INNER JOIN sac.dbo.PreProjeto x on (a.idProjeto = x.idPreProjeto AND a.stAbrangencia = 1)
                LEFT JOIN agentes.dbo.Pais p on (a.idPais=p.idPais)
                LEFT JOIN agentes.dbo.Uf u on (a.idUF=u.idUF)
                LEFT JOIN agentes.dbo.Municipios m on (a.idMunicipioIBGE=m.idMunicipioIBGE)
                WHERE idProjeto=$idPreProjeto
                ORDER BY p.Descricao,u.Descricao,m.Descricao
                ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarDeslocamento($idPreProjeto)
    {
        $sql = "SELECT

                    Qtde,
                    pao.Descricao as paisOrigem,
                    pad.Descricao as paisDestino,
                    ufo.Sigla as ufOrigem,
                    ufd.Sigla as ufDestino,
                    muo.Descricao as municipioOrigem,
                    mud.Descricao as municipioDestino
                FROM
                    sac.dbo.tbDeslocamento tdes
                    left join agentes.dbo.Pais pao on tdes.idPaisOrigem = pao.idPais
                    left join agentes.dbo.Pais pad on tdes.idMunicipioDestino = pad.idPais
                    left join agentes.dbo.uf ufo on tdes.idUFOrigem = ufo.idUF
                    left join agentes.dbo.uf ufd on tdes.idUFDestino = ufd.idUF
                    left join agentes.dbo.Municipios muo on tdes.idMunicipioOrigem = muo.idMunicipioIBGE
                    left join agentes.dbo.Municipios mud on tdes.idMunicipioDestino = mud.idMunicipioIBGE
                WHERE
                    idProjeto=$idPreProjeto
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarPlanoDeDivulgacao($idPreProjeto)
    {
        $sql = "SELECT
                    idPlanoDivulgacao,
                    idProjeto,
                    idPeca,
                    idVeiculo,
                    Usuario,
                    v.Descricao as Peca,
                    ve.Descricao as Veiculo
                FROM
                    sac.dbo.PlanoDeDivulgacao p
                    left join SAC.dbo.Verificacao v on p.idPeca = v.idVerificacao
                    left join SAC.dbo.Verificacao ve on p.idVeiculo = ve.idVerificacao
                WHERE idProjeto=$idPreProjeto
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarDocumentoPreProjeto($idPreProjeto)
    {
        $sql = "SELECT
                        convert(varchar(30),Data, 103 ) as Data,
                        NoArquivo,
                        dex.Descricao
                    FROM
                        sac.dbo.tbDocumentosPreProjeto p
                        left join SAC.dbo.DocumentosExigidos dex on p.CodigoDocumento = dex.Codigo
                    WHERE
                        idprojeto=$idPreProjeto
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarDocumentoAgente($idAgente)
    {
        $sql = "SELECT
                            tbd.NoArquivo as NomeArqui,
                            convert(varchar(30),tbd.Data, 103 ) as Data,
                            dex.Descricao as TipoArqui,
                            idDocumentosAgentes

                    FROM

                            sac.dbo.tbDocumentosAgentes tbd
                            join SAC.dbo.DocumentosExigidos dex on (tbd.CodigoDocumento = dex.Codigo)
                    WHERE
                            tbd.idAgente= $idAgente
                ";
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function buscarHistorico($idPreProjeto)
    {
        $sql = "
        SELECT 'Proposta' as tipo,idProjeto,idTecnico,usu_Nome, convert(varchar(30),DtAvaliacao, 120 ) as DtAvaliacao, Avaliacao
            FROM       SAC.dbo.tbAvaliacaoProposta p
              INNER JOIN tabelas.dbo.Usuarios        u on (p.idTecnico = u.usu_codigo)
            WHERE    ConformidadeOK < 9
                     AND idProjeto = {$idPreProjeto}
            UNION ALL
            SELECT 'Proposta' as tipo,idProjeto,0,'Proponente' as Tecnico, convert(varchar(30),DtMovimentacao, 120 ) as DtMovimentacao,
                                          'Proposta Cultural ENVIADA ao Minist&eacute;rio da Cultura para Conformidade Visual' as Avaliacao
            FROM SAC.dbo.tbMovimentacao
            WHERE Movimentacao=96
                  AND idProjeto = {$idPreProjeto}
            UNION ALL
            SELECT 'Projeto' as tipo,a.idPronac,idTecnico,d.usu_Nome, convert(varchar(30),DtAvaliacao, 120 ) as DtAvaliacao, dsAvaliacao
            FROM       SAC.dbo.tbAvaliarAdequacaoProjeto a
              INNER JOIN sac.dbo.Projetos                  b on (a.idPronac  = b.IdPRONAC)
              INNER JOIN sac.dbo.PreProjeto                c on (b.idProjeto = c.idPreProjeto)
              INNER JOIN tabelas.dbo.Usuarios              d on (a.idTecnico = d.usu_codigo)
            WHERE idProjeto = {$idPreProjeto}
            ORDER BY 5 ASC
        ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function inserirAvaliacao($dado)
    {
        try {

            //insert da avalia��o
            $sql = "INSERT INTO sac.dbo.tbAvaliacaoProposta
                   (idProjeto,idTecnico, DtEnvio, DtAvaliacao, Avaliacao, ConformidadeOK, stEstado, stEnviado)
                    values (" . $dado['idPreProjeto'] . "," . $dado['idTecnico'] . "," . $dado['dtEnvio'] . "," . $dado['dtAvaliacao'] . ",'" . $dado['avaliacao'] . "'," . $dado['conformidade'] . "," . $dado['estado'] . ",'N');";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);

            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function verificarAvaliacao($idPreProjeto)
    {
        $sql = "SELECT * FROM sac.dbo.tbAvaliacaoProposta WHERE idProjeto = $idPreProjeto order by idAvaliacaoProposta DESC";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function updateEstadoAvaliacao($IdProjeto)
    {
        try {

            //update do estado das outras avaliacoes
            $sql = "UPDATE sac.dbo.tbAvaliacaoProposta
                    SET stEstado = 1
                    WHERE idProjeto = $IdProjeto AND stEstado <> 1";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);

            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }


    public static function updateEstadoMovimentacao($IdProjeto)
    {
        try {

            //update do estado das outras avaliacoes
            $sql = "UPDATE sac.dbo.tbMovimentacao
                    SET stEstado = 1
                    WHERE idProjeto = $IdProjeto AND stEstado <> 1";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function inserirMovimentacao($dado)
    {
        try {
            $sql = "INSERT INTO sac.dbo.tbMovimentacao
                (idProjeto,Movimentacao,DtMovimentacao,stEstado,Usuario)
                VALUES (" . $dado['idPreProjeto'] . "," . $dado['movimentacao'] . ",getdate(),0," . $dado['idTecnico'] . ");";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function buscarDespacho($idPreProjeto)
    {
        $sql = "select
                        idProposta,
                        NomeProjeto,
                        convert(varchar(30),Data, 120 ) as Data,
                        Despacho,
                        sac.dbo.fnNomeTecnicoMinc(d.idUsuario) as Usuario,d.idUsuario

                        from sac.dbo.tbDespacho d
                        inner join sac.dbo.PreProjeto p on (d.idProposta = p.idPreProjeto)

                         where idProposta = $idPreProjeto
                ";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function inserirDespacho($dado)
    {
        try {
            $sql = "INSERT INTO sac.dbo.tbDespacho
                (idProposta,Tipo,Data,Despacho,stEstado,idUsuario)
                VALUES (" . $dado['idPreProjeto'] . ",129,getdate(),'" . $dado['despacho'] . "',0," . $dado['idTecnico'] . ");";
            ;
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function verificarDespacho($idPreProjeto)
    {
        $sql = "SELECT * FROM sac.dbo.tbDespacho WHERE idProposta = $idPreProjeto";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function updateEstadoDespacho($IdProjeto)
    {
        try {
            $sql = "UPDATE SAC.dbo.tbDespacho
                     SET stEstado = 1
                     WHERE idProposta = $IdProjeto AND stEstado <> 1;";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function verificarMovimentcaoDespacho($idPreProjeto)
    {
        //$sql = "SELECT TOP 1 * FROM sac.dbo.tbMovimentacao WHERE idProjeto = $idPreProjeto AND stEstado = 0 and Movimentacao = 128 ORDER BY idMovimentacao DESC";
        $sql = "SELECT TOP 1 * FROM sac.dbo.tbMovimentacao WHERE idProjeto = $idPreProjeto AND stEstado = 0 ORDER BY idMovimentacao DESC";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    public static function inserirDocumentoProponente($dado)
    {
        try {
            $sql = "INSERT INTO SAC.dbo.DocumentosProponente
                (idProjeto,CodigoDocumento)
                VALUES (" . $dado['idPreProjeto'] . "," . $dado['CodigoDocumento'] . ");";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function inserirDocumentoProjeto($dado)
    {
        try {
            $sql = "INSERT INTO sac.dbo.DocumentosProjeto
                (idProjeto,CodigoDocumento)
                VALUES (" . $dado['idPreProjeto'] . "," . $dado['CodigoDocumento'] . ");
                ";
            ;
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function updateDocumentoProponente($dado)
    {
        try {
            $sql = "UPDATE sac.dbo.DocumentosProponente
        SET CodigoDocumento = " . $dado['CodigoDocumento'] . "
        WHERE idProjeto=" . $dado['idPreProjeto'] . " and CodigoDocumento=" . $dado['iddocantigo'] . "";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function updateDocumentoProjeto($dado)
    {
        try {
            $sql = "UPDATE sac.dbo.DocumentosProjeto
        SET CodigoDocumento = " . $dado['CodigoDocumento'] . "
        WHERE idProjeto=" . $dado['idPreProjeto'] . " and CodigoDocumento=" . $dado['iddocantigo'] . "";


            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function deleteDocumentoProponente($idcontador)
    {
        try {
            $sql = "DELETE FROM sac.dbo.DocumentosProponente WHERE Contador = $idcontador";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function deleteDocumentoProponentePeloProjeto($idProjeto)
    {
        try {
            $sql = "DELETE FROM sac.dbo.DocumentosProponente WHERE IdProjeto = $idProjeto";

            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function deleteDocumentoProjeto($idcontador)
    {
        try {
            $sql = "DELETE FROM sac.dbo.DocumentosProjeto WHERE Contador = $idcontador";


            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function deleteDocumentoProjetoPeloProjeto($idProjeto)
    {
        try {
            $sql = "DELETE FROM sac.dbo.DocumentosProjeto WHERE idProjeto = $idProjeto";


            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public static function buscarFonte($idfonte)
    {
        $sql = "select idVerificacao,descricao from SAC.dbo.Verificacao where idVerificacao=$idfonte order by descricao
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarProduto($idproduto)
    {
        $sql = " select Codigo,Descricao from SAC.dbo.Produto where Codigo = $idproduto order by descricao
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarEtapa($idetapa)
    {
        $sql = "  select idPlanilhaEtapa,Descricao from sac.dbo.tbPlanilhaEtapa where idPlanilhaEtapa = $idetapa order by descricao
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function buscarItem($iditem)
    {
        $sql = "    select i.idPlanilhaItens,i.Descricao as nomeitem,pp.Quantidade,pp.Ocorrencia,pp.ValorUnitario,pp.QtdeDias,pu.Descricao as nomeUni,uf.Uf
                        from sac.dbo.tbPlanilhaItens i
                        left join sac.dbo.tbPlanilhaProposta pp on i.idPlanilhaItens = pp.idPlanilhaItem
                        left join SAC.dbo.tbPlanilhaUnidade pu on pp.Unidade = pu.idUnidade
                        left join SAC.dbo.Uf uf on pp.UfDespesa = uf.CodUfIbge
                        where idPlanilhaItens = $iditem
                        order by i.descricao
                ";


        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        $resultado = $db->fetchAll($sql);

        return $resultado;
    }

    public static function deletePreProjeto($idPreProjeto)
    {
        try {
            $sql = "DELETE FROM sac.dbo.PreProjeto WHERE idPreProjeto = $idPreProjeto";


            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB::FETCH_ASSOC);
            $db->fetchRow($sql);
        } catch (Exception $e) {
            die("ERRO" . $e->getMessage());
        }
    }

    public function recuperarQtdePropostaTecnicoOrgao($idTecnico, $idOrgao)
    {
        $sql = "
                SELECT count(*) as qtdePropostas
                FROM tbAvaliacaoProposta a
                INNER JOIN tabelas.dbo.vwUsuariosOrgaosGrupos  u ON (a.idTecnico = u.usu_Codigo)
                WHERE uog_orgao={$idOrgao} AND idTecnico={$idTecnico} and sis_codigo=21 and gru_codigo=92 and
                stEstado = 0 and year(DtAvaliacao)=year(Getdate()) and month(DtAvaliacao)=month(Getdate())";

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
}
