<?php

/**
 * Modelo AnexarDocumentos
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.models
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */
class AnexardocumentosDAO extends Zend_Db_Table
{

    /**
     * M�todo para buscar pronac para os documentos
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function buscarprojeto($idpronac)
    {
        $sql = "SELECT idProjeto FROM sac.dbo.Projetos WHERE idPronac = $idpronac";
        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_ASSOC);
            return $db->fetchRow($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Documentos anexados: " . $e->getMessage();
        }
    }

    /**
     * M�todo para buscar documentos de um PRONAC
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
    public static function buscaragente($projeto)
    {
        $sql = "SELECT idAgente FROM sac.dbo.PreProjeto WHERE idPreProjeto = $projeto";

        try {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_DB :: FETCH_ASSOC);
            return $db->fetchRow($sql);
        } catch (Zend_Exception_Db $e) {
            $this->view->message = "Erro ao buscar Documentos anexados: " . $e->getMessage();
        }
    }

    // fecha m�todo buscar()

    /**
     * M�todo para buscar documentos de um PRONAC
     * @access public
     * @static
     * @param integer $idPronac
     * @return object
     */
//    public static function buscarArquivos($idagente, $idprojeto, $idpronac)
//    {
//        $sql = "SELECT CodigoDocumento,
//                       Descricao,
//                       'Anexado pelo Proponente' as Classificacao,
//                       idAgente as Codigo,
//                       Data,
//                       NoArquivo,
//                       TaArquivo,
//                       idDocumentosAgentes,
//                       'agentes' as tipo
//                        FROM sac.dbo.tbDocumentosAgentes d
//                        INNER JOIN sac.dbo.DocumentosExigidos e on (d.CodigoDocumento = e.Codigo)
//                        WHERE idagente = $idagente
//                        UNION
//                        SELECT CodigoDocumento,
//                        Descricao,
//                        'Anexado pelo Proponente' as Classificacao,
//                        d.idProjeto as Codigo,
//                        Data,
//                        NoArquivo,
//                        TaArquivo,
//                        idDocumentosPreprojetos,
//                        'preprojeto' as tipo
//                        FROM sac.dbo.tbDocumentosPreProjeto d
//                        INNER JOIN sac.dbo.DocumentosExigidos e on (d.CodigoDocumento = e.Codigo)
//                        WHERE idProjeto = $idprojeto
//                        UNION
//                        SELECT d.idTipoDocumento,
//                        e.dsTipoDocumento as Descricao,
//                        'Anexado no MinC' as Classificacao,
//                        idPronac as Codigo,
//                        dtDocumento,
//                        NoArquivo,
//                        TaArquivo,
//                        d.idDocumento,
//                       'documento' as tipo
//                        FROM sac.dbo.tbDocumento d
//                        INNER JOIN sac.dbo.tbTipoDocumento e on (d.idTipoDocumento = e.idTipoDocumento)
//                        WHERE idPronac = $idpronac and NoArquivo not in ('')
//                        ORDER BY Data";
    ////        die("<pre>".$sql);
//
//
//        try
//        {
//            $db = Zend_Db_Table::getDefaultAdapter();
//            $db->setFetchMode(Zend_DB :: FETCH_OBJ);
//            return $db->fetchAll($sql);
//        }
//        catch (Zend_Exception_Db $e)
//        {
//            $this->view->message = "Erro ao buscar Documentos anexados: " . $e->getMessage();
//        }
//    }

    public static function buscarArquivos($idpronac)
    {
        $sql = "select Classificacao, idDocumento, nome, tipoDocumento from (
        select
              a1.idDocumentosAgentes as idDocumento,
              a1.NoArquivo as nome,
              pr.IdPRONAC as idpronac,
              'antigo-agente' as tipoDocumento,
              'Anexado pelo Proponente' as Classificacao
              from sac.dbo.tbDocumentosAgentes a1
              inner join Agentes.dbo.Agentes ag on ag.idAgente = a1.idAgente
              inner join sac.dbo.projetos pr on pr.CgcCpf = ag.CNPJCPF
        union
        select
              a2.idDocumentosPreprojetos as idDocumento,
              a2.NoArquivo as nome,
              pr.IdPRONAC as idpronac,
              'antigo-preprojeto' as tipoDocumento,
              'Anexado pelo Proponente' as Classificacao
              from sac.dbo.tbDocumentosPreProjeto a2
              inner join sac.dbo.projetos pr on pr.idProjeto = a2.idProjeto
        union
        select
              a3.idDocumento as idDocumento,
              a3.NoArquivo as nome,
              a3.idPronac as idpronac,
              'antigo-documento' as tipoDocumento,
              'Anexado pelo MINC' as Classificacao
              from sac.dbo.tbDocumento a3
        union
        select
              a4.idDocumento as idDocumento,
              a5.nmArquivo as nome,
              tap.idpronac as idpronac,
              'docProp' as  tipoDocumento,
              'Anexado pelo Proponente' as Classificacao
              from BDCORPORATIVO.scCorp.tbDocumento a4
              inner join BDCORPORATIVO.scCorp.tbArquivo a5 on (a4.idArquivo = a5.idArquivo)
              inner join BDCORPORATIVO.scCorp.tbDocumentoProjeto tap on tap.idDocumento = a4.idDocumento
        ) tb where idpronac = $idpronac and nome <> '' order by tb.tipoDocumento, tb.idDocumento";
        
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
//        die('<pre>'.$sql);
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    
    public static function uploadDocumento($id, $tipo)
    {
        $db= Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_DB::FETCH_OBJ);

        // busca o arquivo

        if ($tipo == 'antigo-agente') {
            $sql = " SELECT
                       imdocumento,
                       noarquivo
                FROM sac.dbo.tbDocumentosAgentes
                where idDocumentosAgentes=$id
        ";
        }
        if ($tipo == 'antigo-preprojeto') {
            $sql = "SELECT
                       imdocumento,
                       noarquivo
                FROM sac.dbo.tbDocumentosPreProjeto
                where idDocumentosPreprojetos=$id";
        }
        if ($tipo == 'antigo-documento') {
            $sql = " SELECT
                       imdocumento,
                       noarquivo
                FROM sac.dbo.tbDocumento
                where idDocumento=$id";
        }
        if ($tipo == 'docProp') {
            $sql = "SELECT
                      biArquivo
                FROM BDCORPORATIVO.scCorp.tbArquivoImagem
                where idDocumento=$id";
        }

        $resultadxo = $db->fetchAll("SET TEXTSIZE 10485760");
        $resultado = $db->fetchAll($sql);
        return $resultado;
    }

    // fecha m�todo abrir()
    // fecha m�todo buscar()

    /**
     * M�todo para cadastrar documentos do PRONAC
     * @access public
     * @param void
     * @return object
     */
    public static function cadastrar($dados)
    {
        /* $sql = "INSERT INTO BDCORPORATIVO.scSAC.tbComprovanteExecucao ";
          $sql.= "VALUES ($dados['idPRONAC'], $dados['idTipoDocumento'], $dados['nmComprovante'], $dados['dsComprovante'], $dados['idArquivo'], $dados['idSolicitante'], $dados['dtEnvioComprovante'], $dados['stComprovante'], $dados['stComprovante'], $dados['idComprovanteAnterior'])"; */
    }

    // fecha m�todo cadastrar()
}

// fecha class
