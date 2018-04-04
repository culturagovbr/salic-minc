<?php
class SolicitarAlteracaoProjetoDAO extends Zend_Db_Table
{
    public static function buscaProjetos($cpfCnpj = null)
    {
        $sql = "SELECT
                AGENTES.dbo.Agentes.idAgente, (SAC.dbo.Projetos.AnoProjeto +
                SAC.dbo.Projetos.Sequencial) AS nrPronac, SAC.dbo.Projetos.NomeProjeto,
                SAC.dbo.Projetos.Situacao, SAC.dbo.Projetos.DtSaida,
                      SAC.dbo.Projetos.DtRetorno, AGENTES.dbo.Agentes.CNPJCPF
                FROM         SAC.dbo.Projetos INNER JOIN
                      SAC.dbo.Aprovacao ON SAC.dbo.Projetos.IdPRONAC = SAC.dbo.Aprovacao.IdPRONAC INNER JOIN
                      AGENTES.dbo.Agentes ON SAC.dbo.Projetos.CgcCpf = AGENTES.dbo.Agentes.CNPJCPF
                      WHERE AGENTES.dbo.Agentes.CNPJCPF = $cpfCnpj";


        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function detalhesProjetos($idPronac)
    {
        $sql = "select projetos.idProjeto,

                    projetos.IdPRONAC,
                    projetos.CgcCpf,
                    projetos.AnoProjeto+projetos.Sequencial as nrpronac,
                    projetos.NomeProjeto,
                    agentes.Descricao,
                    areaCultura.Codigo as 'codigoArea',
                    areaCultura.Descricao as 'areaCultura',
                    segmentoCultura.Codigo as 'codigoDescricao',
                    segmentoCultura.Descricao as 'segmentoCultura' from
                    sac.dbo.Projetos as projetos
                    inner join SAC.dbo.Area as areaCultura
                    on projetos.Area = areaCultura.Codigo
                    left join SAC.dbo.Segmento as segmentoCultura
                    on projetos.Segmento = segmentoCultura.Codigo
                    inner join SAC.dbo.PreProjeto as pre
                    on projetos.idProjeto = pre.idPreProjeto
                    inner join AGENTES.dbo.Nomes as agentes
                    on pre.idAgente = agentes.idAgente

                where projetos.IdPRONAC = $idPronac";
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }

    public function detalhesLocalizacao($idPronac)
    {
        $sql = "SELECT
            Abrangencia.idUF, Abrangencia.idPais,
                            Abrangencia.idMunicipioIBGE, AGENTES.dbo.Uf.idUF AS UfLocal,
                             AGENTES.dbo.Uf.Descricao AS UfDescricao,
                      AGENTES.dbo.Municipios.idMunicipioIBGE AS idMunicipio,
            AGENTES.dbo.Municipios.Descricao AS DescicaoMunicipio, AGENTES.dbo.Pais.idPais AS idPais,
                      AGENTES.dbo.Pais.Descricao AS DescricaoPais, Projetos.IdPRONAC
            FROM         SAC.dbo.Abrangencia INNER JOIN
                                  SAC.dbo.PreProjeto ON Abrangencia.idProjeto = PreProjeto.idPreProjeto INNER JOIN
                                  SAC.dbo.Projetos ON PreProjeto.idPreProjeto = Projetos.idProjeto INNER JOIN
                                  AGENTES.dbo.Pais ON Abrangencia.idPais = AGENTES.dbo.Pais.idPais INNER JOIN
                                   AGENTES.dbo.Uf ON Abrangencia.idUF = AGENTES.dbo.Uf.idUF INNER JOIN
                                  AGENTES.dbo.Municipios ON Abrangencia.idMunicipioIBGE = AGENTES.dbo.Municipios.idMunicipioIBGE
            WHERE     (Projetos.IdPRONAC = $idPronac) AND SAC.dbo.Abrangencia.stAbrangencia = 1";
        $db = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        return $db->fetchAll($sql);
    }
}
